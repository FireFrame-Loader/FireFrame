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
        return 23;

    return $query->fetch_assoc();
}

function fetch_all($connection, $loader){
    $query = $connection->query('SELECT * FROM loader_licenses WHERE loader_key=?', [$loader['key']]);

    return $query->fetch_all(1);
}

/* 

0 - license doesnt exist

*/

function get_license_info($connection, $license,$loader_key,$owner) {
    $query = $connection->query('SELECT * loader_licenses WHERE code=? AND owner=? AND loader_key=?',[$license,$owner,$loader_key]);

    if ($query->num_rows === 0)
        return 0;

    $row_data = $query->fetch_assoc();

    $connection->query('DELETE FROM loader_licenses WHERE code=? AND owner=? AND loader_key=?',[$license,$owner,$loader_key]);

    return [
        'usergroup' => $row_data['usergroup'],
        'duration' => $row_data['duration']
    ];
}

