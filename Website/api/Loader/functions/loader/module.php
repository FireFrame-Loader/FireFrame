<?php
namespace loader;

/* 

0 - no modules available

*/

function fetch_available_modules($connection, $loader, $user){
    $modules = module\fetch_all($connection, $loader); //module\fetch_all => Website/functions/module/fetch.php 

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

?>