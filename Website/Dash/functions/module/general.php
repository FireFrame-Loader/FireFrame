<?php
namespace module;

use encryption;

function upload($connection, $file, $loader, $module_name, $target_process, $groups){
    $server_name = rnd_string_secure(32).'.dll';

    $server_key = rnd_string_secure(64);

    $uid = rnd_string_secure(32);

    if (!contains('.exe', $target_process))
        $target_process .= '.exe';

    $file_path = dirname(__FILE__).'/../../modules/'.$server_name;

    if(empty($file))
        return 3;

    $file_name = basename($file['name']);

    $file_extension = substr($file_name, strrpos($file_name,'.') + 1);

    if($file_extension !== 'dll' || $file['size'] > 8388608) //8mb?
        return 4;

    if(@!move_uploaded_file($file['tmp_name'], $file_path))
        return 3;

    file_put_contents($file_path, encryption\encrypt_file($file_path, $server_key));

    $connection->query('INSERT INTO loader_modules(`name`, process, `groups`, `server_name`, server_key, uid, paused, loader_key, owner) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
        $module_name, $target_process, $groups, $server_name, $server_key, $uid, 0, (string) $loader['key'], $loader['owner']
    ));

    return 5;
}