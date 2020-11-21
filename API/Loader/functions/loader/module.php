<?php
namespace loader;

function contains($needle, $haystack)
{
    return strpos($haystack, $needle) !== false;
}

function get_user_groups($usergroup) {
    if (contains(',',$usergroup))
        $usergroup = explode(',',$usergroup);
    return $usergroup;
}

/* 

0 - no modules available

*/

function get_available_modules_list($user_groups,$loader_key,$owner) {
    $query = $connection->query('SELECT * FROM loader_modules WHERE paused=? AND loader_key=? AND owner=?',[0,$loader_key,$owner]);

    if ($query->num_rows === 0)
        return 0;

    $modules = $query->fetch_all(1);
    $allowed_modules = array();

    $user_groups = get_user_groups($user_groups);

    foreach($modules as $module) { //this definitely needs some cleaning!
        $module_groups = get_user_groups($module['groups']);
        if (is_array($module_groups) && is_array($user_groups)) {
            foreach($module_groups as $module_group) {
                foreach($user_groups as $user_group) {
                    if ($module_group !== $user_group)
                        continue;
                    $allowed_modules[] = $module;
                }
            }
        } else if (is_array($module_groups) && !is_array($user_groups)) {
            foreach($module_groups as $module_group) {
                if ($module_group !== $user_groups)
                    continue;
                $allowed_modules[] = $module;
            }
        } else if (!is_array($module_groups) && is_array($user_groups)) {
            foreach($user_groups as $user_group) {
                if ($user_group !== $module_groups)
                    continue;
                $allowed_modules[] = $module;
            }
        } else {
            if ($user_groups === $module_groups)
                $allowed_modules[] = $module;
        }
    }

    if (empty($allowed_modules))
        return 0;

    return $allowed_modules;
}

?>