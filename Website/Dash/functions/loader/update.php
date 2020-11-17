<?php
namespace loader;

/*
 * 0 = success
 * 1 = loader creation failed
 * 2 = loader name is invalid or user already has a loader
 * 3 = error occurred?
 * 4 = 8mb max
 */

use function auth\owner_fetch;

function loader_key_structure(){
   return rnd_string_secure(3) . '-' . rnd_string_secure(3) . '-' . rnd_string_secure(3);
}

function create_loader($connection, $loader_name, $username){

    //TODO : restrict loader amount

    $loader_name = htmlentities($loader_name);

    $user_data = owner_fetch($connection, $username);

    if($user_data === 0 || !check_expiry($user_data['expires']) ){
        return 1;
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
        return 2;
    }

    $loader_key = loader_key_structure();

    $connection->query('INSERT INTO loaders (`name`, loader_key, owner) VALUES(?,?,?)', [$loader_name, $loader_key, $username]);

    return 0;
}
