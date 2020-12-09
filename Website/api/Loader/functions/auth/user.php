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
        'usergroup' => $row_data['usergroup'], //is already json_encoded in db
    ];

    if ($modules !== 0) {
        foreach($modules as $module) {
            $return_modules[] = [
                'name' => $module['name'],
                'uid' => $module['uid']
            ];
        }
        $return_array['modules'] = $return_modules;
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
    
    $expires = time() + ($license_info['duration'] * 86400);
    $password = password_hash($password,PASSWORD_DEFAULT);

    $group_array = array(
        array($license_info['usergroup'],$expires)
    );

    $group_array = json_encode($group_array);

    $connection->query('INSERT INTO loader_users(username,password,hwid,usergroup,loader_key,owner) VALUES(?,?,?,?,?,?,?)',[$username,$password,$hwid,$group_array,$loader_key,$owner]);

    $modules = get_available_modules_list($license_info['usergroup'],$loader_key,$owner);

    $return_modules = array();

    $return_array = [
        'usergroup' => $group_array,
    ];

    if ($modules !== 0) {
        foreach($modules as $module) {
            $return_modules[] = [
                'name' => $module['name'],
                'uid' => $module['uid']
            ];
        }
        $return_array['modules'] = $return_modules;
    }
    
    return $return_array;

}

/* 

0 - invalid license

*/

function redeem_license($connection,$username,$license,$loader_key) {
    $owner = \get_loader_owner($connection,$loader_key);

    $license_info = get_license_info($connection,$license,$loader_key,$owner);

    if ($license_info === 0)
        return 0;

    $new_expires = $license_info['duration'] * 86400;

    $query = $connection->query('SELECT usergroup FROM loader_users WHERE username=? AND loader_key=? AND owner=?',[$username,$loader_key,$owner]);

    $row_data = $query->fetch_assoc();

    $groups = $row_data['usergroup'];

    if (strlen($group) > 0) { 
        $groups = json_decode($groups);
        foreach($groups as &$group) {
            if ($group[0] === $license_info['usergroup']) {
                if ($group[1] > (time() + $new_expires))
                    $group[1] += $new_expires;
                else
                    $group[1] = time() + $new_expires;
            } else {
                $groups[] = array($license_info['usergroup'],time() + $new_expires);
            }
        }

    } else {
        $groups = array(
            array($license_info,time() + $new_expires)
        );   
    }

    $return_array = [
        'usergroup' => $groups,
    ];

    $groups = json_encode($groups);

    $modules = get_available_modules_list($groups,$loader_key,$owner);

    $return_modules = array();

    if ($modules !== 0) {
        foreach($modules as $module) {
            $return_modules[] = [
                'name' => $module['name'],
                'uid' => $module['uid']
            ];
        }
        $return_array['modules'] = $return_modules;
    }

    $connection->query('UPDATE loader_users SET usergroup=? WHERE username=? AND loader_key=? AND owner=?',[$groups,$username,$loader_key,$owner]);

    return $return_array;
}


?>