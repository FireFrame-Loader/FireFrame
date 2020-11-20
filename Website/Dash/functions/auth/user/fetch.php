<?php
namespace auth\user;

function fetch_all($connection, $loader){
    $query = $connection->query('SELECT * FROM loader_users WHERE loader_key=?', [$loader['key']]);

    return $query->fetch_all(1);
}