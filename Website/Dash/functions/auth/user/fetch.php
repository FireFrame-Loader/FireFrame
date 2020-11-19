<?php
namespace auth\user;

function fetch_all($connection, $loader_key){
    $query = $connection->query('SELECT * FROM loader_users WHERE loader_key=?', [$loader_key]);

    if($query->num_rows === 0)
        return 0;

    return $query->fetch_all(1);
}