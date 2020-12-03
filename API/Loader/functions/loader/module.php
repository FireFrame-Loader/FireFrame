<?php
namespace loader;

/* 

0 - no modules available

*/

function get_available_modules_list($usergroups,$loader_key,$owner) {
    $query = $connection->query('SELECT * FROM loader_modules WHERE paused=? AND loader_key=? AND owner=?',[0,$loader_key,$owner]);

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