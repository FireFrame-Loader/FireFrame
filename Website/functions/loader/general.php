<?php
namespace loader;

/*
 * 0 = success
 * 1 = loader creation failed
 * 2 = loader name is invalid or user already has a loader
 * 3 = error occurred?
 * 4 = 8mb max
 */

use auth;

function loader_key_structure(){
   return rnd_string_secure(3) . '-' . rnd_string_secure(3) . '-' . rnd_string_secure(3);
}

function create($connection, $loader_name, $username){

    $loader_name = htmlentities($loader_name);

    $user_data = auth\owner\fetch($connection, $username);

    if($user_data === 0 || !check_expiry($user_data['expires']) ){
        return 12;
    }

    $loader_exists = static function($username, $loader_name) use ($connection){
        $first_query = $connection->query('SELECT owner FROM loaders WHERE owner=?', [$username]);

        if($first_query->num_rows > 0)
            return true;

        $second_query = $connection->query('SELECT `name` FROM loaders WHERE `name`=?', [$loader_name]);

        if($second_query->num_rows > 0)
            return true;

        return false;
    };

    if($loader_exists($username, $loader_name)) {
        return 13;
    }

    $loader_key = loader_key_structure();

    $connection->query('INSERT INTO loaders (`name`, loader_key, owner) VALUES(?,?,?)', [$loader_name, $loader_key, $username]);

    return 14;
}
