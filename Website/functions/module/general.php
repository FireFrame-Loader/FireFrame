<?php
namespace module;

use encryption;
use auth;

function get_upload_path($name){
    return '/var/www/html/dash.firefra.me/modules/'.$name; // create a folder named modules 'htdocs/modules'
}

function module_of_owner($connection, $username, $module_uid){
    $query = $connection->query('SELECT owner FROM loader_modules WHERE owner=? AND uid=?', [$username, $module_uid]);

    return $query->num_rows > 0;
}

function upload($connection, $file, $loader, $module_name, $target_process, $groups){
    $server_name = rnd_string_secure(32).'.dll';

    $server_key = rnd_string_secure(64);

    $uid = rnd_string_secure(32);

    if (!contains('.exe', $target_process))
        $target_process .= '.exe';

    $file_path = get_upload_path($server_name);

    if(empty($file))
        return 15; //error occurred

    $file_name = basename($file['name']);

    $file_extension = substr($file_name, strrpos($file_name,'.') + 1);

    if($file_extension !== 'dll' || $file['size'] > 8388608) //8mb?
        return 16;

    if(@!move_uploaded_file($file['tmp_name'], $file_path)) // no perms smh or invalid path?
        return 17; //error occurred

    file_put_contents($file_path, encryption\encrypt_file($file_path, $server_key));

    $connection->query('INSERT INTO loader_modules(`name`, process, `groups`, `server_name`, server_key, uid, paused, loader_key, owner) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
        $module_name, $target_process, $groups, $server_name, $server_key, $uid, 0, (string) $loader['key'], $loader['owner']
    ));

    return 18;
}

function update($connection, $loader, $uid, array $update_data) {
    if (!contains('.exe', $update_data['process'])) {
        $update_data['process'] .= '.exe';
    }

    if(!module_of_owner($connection, $loader['owner'], $uid)){
        return 19;
    }

    $connection->query('UPDATE loader_modules SET `name`=?, process=? WHERE loader_key=? AND uid=?', [$update_data['name'], $update_data['process'], $loader['key'], $uid]);

    $module_data = fetch($connection, $loader, $uid);

    $file_path = get_upload_path($module_data['server_name']);

    $file = $update_data['file'];

    if (empty($file))
        return 15;

    unlink($file_path);

    $file_name = basename($file['name']);

    $file_extension = substr($file_name, strrpos($file_name, '.') + 1);

    if ($file_extension !== 'dll' || $_FILES['file']['size'] > 8388608)
        return 16;

    if(@!move_uploaded_file($file['tmp_name'], $file_path))
        return 17;

    $encrypted_data = encryption\encrypt_file($file_path, $module_data['server_key']);

    file_put_contents($file_path, $encrypted_data);

    return 18;
}

function delete($connection, $loader, $uid){
    $module_data = fetch($connection, $loader, $uid);

    if($module_data === 0)
        return 20;

    $server_path = get_upload_path($module_data['server_name']);

    $connection->query('DELETE FROM loader_modules WHERE loader_key=? AND uid=?', [$loader['key'], $uid]);

    unlink($server_path);

    return 21;
}

function pause($connection, $loader, $uid, $pause = true){
    $query = $connection->query('UPDATE loader_modules SET paused=? WHERE loader_key=? AND uid=?', [$pause ? '1' : '0', $loader['key'], $uid]);

    return 22;
}

/* 

0 - no modules available

*/

function fetch_available_modules($connection, $loader, $user){
    $modules = fetch_all($connection, $loader); //module\fetch_all => Website/functions/module/fetch.php 

    $user_data = auth\user\fetch($connection, $loader, $user); //auth\user\fetch => Website/functions/auth/user/fetch.php

    if($user_data === 0){
        return 0;
    }

    $user_expiry = $user_data['expirygroup']; // supposing 'expirygroup' = [['Default', 1245], ['Admin', 1245]]

    $module_array = [];

    foreach($modules as $module){
        $module_groups = explode(',', $module['groups']); //Default, Admin

        foreach($module_groups as $module_group){ //Default
            foreach($user_expiry as $expiry){ //['Default', 1245]
                if($expiry[0] === $module_group){ //if Default equals to Default
                    $module_array[] = $module; // add
                    continue 3; //skip module as it was added already
                }           
            }
        }
    }
    
    return $module_array;
}

function get_available_modules_list($usergroups,$loader_key) {
    $query = $connection->query('SELECT * FROM loader_modules WHERE paused=? AND loader_key=?',[0,$loader_key]); //nigga, loader_key['owner']

    if ($query->num_rows === 0)
        return 0;

    $modules = $query->fetch_all(1);
    
    $allowed_modules = array();

    //$usergroups is a json_encoded string if called from redeem_license & validate_user (if groups matches, expiration check if needed) and a static string if called from insert_new_user (no expiration check needed, user just registered with valid license)

    foreach($modules as $module) {
        $module_groups = explode(',',$module['groups']);
    }

    if (empty($allowed_modules))
        return 0;

    return $allowed_modules;
}

