<?php
namespace loader;

/* 

0 - no modules available

*/

function get_available_modules_list($user_groups,$loader_key,$owner) {
    $query = $connection->query('SELECT * FROM loader_modules WHERE paused=? AND loader_key=? AND owner=?',[0,$loader_key,$owner]);

    if ($query->num_rows === 0)
        return 0;

    $modules = $query->fetch_all(1);
    $allowed_modules = array();

    foreach($modules as $module) {
        //check if users groups contains module allowed group and add the module into allowed_modules array
    }

    if (empty($allowed_modules))
        return 0;

    return $allowed_modules;
}

?>