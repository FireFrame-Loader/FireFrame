<?php

/* 

0 - loader doesn't exist
1 - loader expired

*/

function is_loader_expired($connection, $owner) {
    $query = $connection->query('SELECT expires FROM users WHERE username=? LIMIT 1', [$owner]);

    $expires = $query->fetch_assoc()['expires'];

    return time() > $expires;
}

function get_loader_owner($connection, $loader_key) {
    $query = $connection->query('SELECT owner FROM loaders WHERE loader_key=? LIMIT 1',[$loader_key]);
    
    if ($query->num_rows === 0)
        return 0;

    $owner = $query->fetch_assoc()['owner'];

    if (is_loader_expired($connection, $owner))
        return 1;
    
    return $owner;
}

?>