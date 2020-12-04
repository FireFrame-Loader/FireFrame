<?php
namespace auth\user;

function fetch_all($connection, $loader){
    $query = $connection->query('SELECT * FROM loader_users WHERE loader_key=?', [$loader['key']]);

    return $query->fetch_all(1);
}

function fetch($connection, $loader, $username){
    $query = $connection->query('SELECT * FROM loader_users WHERE loader_key=? AND username=?', [$loader['key'], $username]);

    if($query->num_rows === 0){
        return 0;
    }

    return $query->fetch_assoc();
}