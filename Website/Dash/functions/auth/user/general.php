<?php
namespace auth\user;

//TODO : fix this broken code system

function add($connection, $loader, $username, $password, $group){
    if(empty($group))
        $user_group = 'Default';

    if(!validate_password($password))
        return 3;

    $query = $connection->query('SELECT username FROM loader_users WHERE username=? AND loader_key=?', [$username, $loader['key']]);

    if($query->num_rows !== 0)
        return 2;

    $connection->query('INSERT INTO loader_users(username, password, usergroup, loader_key, owner) VALUES(?, ?, ?, ?, ?)', [$username, $password, $group, $loader['key'], $loader['owner']]);

    return 0;
}