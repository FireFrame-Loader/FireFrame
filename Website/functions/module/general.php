<?php
namespace module;

use encryption;

function get_upload_path($name){
    return '/var/www/html/dash.firefra.me/modules/'.$name; // create a folder named modules 'htdocs/modules'
}

function module_of_owner($connection, $username, $module_uid){
    $query = $connection->query('SELECT owner FROM loader_modules WHERE owner=? AND uid=?', [$username, $module_uid]);

    return $query->num_rows > 0; // check if the user isn't trying to update another module other than his own
}

function upload($connection, $file, $loader, $module_name, $target_process, $groups){
    $server_name = rnd_string_secure(32).'.dll';

    $server_key = rnd_string_secure(64);

    $uid = rnd_string_secure(32);

    if (!contains('.exe', $target_process))
        $target_process .= '.exe';

    $file_path = get_upload_path($server_name);

    if(empty($file))
        return 3; //error occurred

    $file_name = basename($file['name']);

    $file_extension = substr($file_name, strrpos($file_name,'.') + 1);

    if($file_extension !== 'dll' || $file['size'] > 8388608) //8mb?
        return 4;

    if(!move_uploaded_file($file['tmp_name'], $file_path)) // no perms smh or invalid path?
        return 3; //error occurred

    file_put_contents($file_path, encryption\encrypt_file($file_path, $server_key));

    $connection->query('INSERT INTO loader_modules(`name`, process, `groups`, `server_name`, server_key, uid, paused, loader_key, owner) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
        $module_name, $target_process, $groups, $server_name, $server_key, $uid, 0, (string) $loader['key'], $loader['owner']
    ));

    return 5;
}

function update($connection, $loader, $uid, array $update_data) {
    if (!contains('.exe', $update_data['process'])) {
        $update_data['process'] .= '.exe';
    }

    if(!module_of_owner($connection, $loader['owner'], $uid)){
        return 7;
    }

    $connection->query('UPDATE loader_modules SET `name`=?, process=? WHERE loader_key=? AND uid=?', [$update_data['name'], $update_data['process'], $loader['key'], $uid]);

    $module_data = fetch($connection, $loader, $uid);

    $file_path = get_upload_path($module_data['server_name']);

    $file = $update_data['file'];

    if (empty($file))
        return 3;

    unlink($file_path);

    $file_name = basename($file['name']);

    $file_extension = substr($file_name, strrpos($file_name, '.') + 1);

    if ($file_extension !== 'dll' || $_FILES['file']['size'] > 8388608)
        return 4;

    if(@!move_uploaded_file($file['tmp_name'], $file_path))
        return 3;

    $encrypted_data = encryption\encrypt_file($file_path, $module_data['server_key']);

    file_put_contents($file_path, $encrypted_data);

    return 5;
}

function delete($connection, $loader, $uid){
    $module_data = fetch($connection, $loader, $uid);

    if($module_data === 0)
        return 0;

    $server_path = get_upload_path($module_data['server_name']);

    $connection->query('DELETE FROM loader_modules WHERE loader_key=? AND uid=?', [$loader['key'], $uid]);

    unlink($server_path);

    return 5;
}

function pause($connection, $loader, $uid, $pause = true){
    $query = $connection->query('UPDATE loader_modules SET paused=? WHERE loader_key=? AND uid=?', [$pause ? '1' : '0', $loader['key'], $uid]);

    return 5;
}