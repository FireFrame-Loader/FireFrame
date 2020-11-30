<?php
namespace auth\user;

function add($connection, $loader, $username, $password, $group){
    if(empty($group))
        $group = 'Default';

    if(!validate_password($password))
        return 3;

    $query = $connection->query('SELECT username FROM loader_users WHERE username=? AND loader_key=?', [$username, $loader['key']]);

    if($query->num_rows !== 0)
        return 2;

    $connection->query('INSERT INTO loader_users(username, password, usergroup, loader_key, owner) VALUES(?, ?, ?, ?, ?)', [$username, $password, $group, $loader['key'], $loader['owner']]);

    return 0;
}

function delete($connection, $loader, $username){
    $connection->query('DELETE FROM loader_users WHERE username=? AND loader_key=?', [$username, $loader['key']]);

    return 28;
}

function reset_hwid($connection, $loader, $username){
    $connection->query('UPDATE loader_users SET hwid=NULL WHERE loader_key=? AND username=?', [$loader['key'], $username]);
}
