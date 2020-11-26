<?php
namespace licenses;

//LOADER LICENSES

/*
 * 0 - success
 * 1 - query failed cuz do not exist
 * 2 - 2 much licenses
 */

function fetch($connection, $loader, $license) {
    $query = $connection->query('SELECT * FROM loader_licenses WHERE loader_key=? AND code=?', [$loader['key'], $license]);

    if($query->num_rows === 0)
        return 1;

    return $query->fetch_assoc();
}

function fetch_all($connection, $loader){
    $query = $connection->query('SELECT * FROM loader_licenses WHERE loader_key=?', [$loader['key']]);

    return $query->fetch_all(1);
}