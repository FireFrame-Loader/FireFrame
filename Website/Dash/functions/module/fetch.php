<?php
namespace module;

/*
 * 0 - invalid query
 */

function fetch_all($connection, $loader){
    $query = $connection->query('SELECT * FROM loader_modules WHERE loader_key=?', [$loader['key']]);

    return $query->fetch_all(1);
}

function fetch($connection, $loader, $uid){
    $query = $connection->query('SELECT * FROM loader_modules WHERE loader_key=? AND uid=?', [$loader['key'], $uid]);

    if($query->num_rows === 0)
        return 0;

    return $query->fetch_assoc();
}
