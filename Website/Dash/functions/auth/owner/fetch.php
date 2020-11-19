<?php
namespace auth;

function owner_fetch($connection, $username){
    $query = $connection->query('SELECT * FROM users WHERE username=? LIMIT 1', [$username]);

    if($query->num_rows === 0) {
        return 0;
    }

    return $query->fetch_assoc();
}