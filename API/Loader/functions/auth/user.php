<?php
namespace auth;

/* 

1 - invalid credentials
2 - invalid hwid
3 - loader doesnt exist
4 - loader expired
*/

use function loader\get_license_info;

function is_valid_user($connection, $username, $password, $hwid, $loader_key) {
    $owner = \get_loader_owner($connection, $loader_key);

    if ($owner === 0)
        return 3;
//NIGGA WHAT - nigga i added these checks after the func was done, hence the fcked up codes XD
    if ($owner === 1)
        return 4;

    $query = $connection->query('SELECT * FROM loader_users WHERE username=? AND loader_key=? AND owner=? LIMIT 1', [$username,$loader_key,$owner]);

    if ($query->num_rows === 0)
        return 1;

    $row_data = $query->fetch_assoc();

    if (!password_verify($password,$row_data['password']))
        return 1;

    if (strlen($row_data['hwid']) > 0) {
        if ($hwid !== $row_data['hwid'])
            return 2;
    } else
        $connection->query('UPDATE loader_users SET hwid=? WHERE username=? AND loader_key=? AND owner=?',[$hwid,$username,$loader_key,$owner]);

    $modules = get_available_modules_list($row_data['usergroup'],$loader_key,$owner);

    $return_modules = array();

    $return_array = [
        'usergroup' => $row_data['usergroup'],
        'expires' => $row_data['expires']
    ];

    if ($modules !== 0) {
        foreach($modules as $module) {
            //TODO: Add $module['name'] and $module['uid'] into $return_modules
        }
        $return_array['modules'] = json_encode($return_modules);
    }

    return $return_array;
}

/*

0 - loader doesnt exist
1 - loader expired
2 - username already exists
3 - invalid license
*/

function insert_new_user($connection, $username,$password,$hwid,$license,$loader_key) {
    $owner = \get_loader_owner($loader_key);

    if ($owner === 0)
        return 0;

    if ($owner === 1)
        return 1;

    $query = $connection->query('SELECT * FROM loader_users WHERE username=? AND owner=? AND loader_key=? LIMIT 1',[$username,$owner,$loader_key]);
    
    if ($query->num_rows > 0)
        return 2;

    $license_info = get_license_info($connection, $license,$loader_key,$owner);

    if ($license_info === 0)
        return 3;
    
    $expires = $license_info['duration'] * 86400;
    $password = password_hash($password,PASSWORD_DEFAULT);

    $connection->query('INSERT INTO loader_users(username,password,hwid,usergroup,expires,loader_key,owner) VALUES(?,?,?,?,?,?,?)',[$username,$password,$hwid,$license_info['usergroup'],$expires,$loader_key,$owner]);

    $modules = get_available_modules_list($license_info['usergroup'],$loader_key,$owner);

    $return_modules = array();

    $return_array = [
        'usergroup' => $license_info['usergroup'],
        'expires' => $expires
    ];

    if ($modules !== 0) {
        foreach($modules as $module) {
            //TODO: Add $module['name'] and $module['uid'] into $return_modules
        }
        $return_array['modules'] = json_encode($return_modules);
    }
    
    return $return_array;

}

?>