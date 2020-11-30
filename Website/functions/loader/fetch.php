<?php
namespace loader;

/*
 * function fetch_loader_with_key($connection, $loader_key){
 *      $query = $connection->query('SELECT * FROM loaders WHERE loader_key=? LIMIT 1', [$loader_key]);
 *
 *      if($query->num_rows === 0)
 *          return 0;
 *
 *      return $query->fetch_assoc();
 * }
 *
 * function fetch_loader_with_name($connection, $loader_name){
 *      $query = $connection->query('SELECT * FROM loaders WHERE name=? LIMIT 1', [$loader_name]);
 *
 *      if($query->num_rows === 0)
 *          return 0;
 *
 *      return $query->fetch_assoc();
 * }
 *
 * function fetch_all_loaders($connection, $owner){
 *      $query = $connection->query('SELECT * FROM loaders WHERE owner=?', [$owner]);
 *
 *      return $query->fetch_all(1);
 * }
 *
 */


function fetch($connection, $username){
    $query = $connection->query('SELECT * FROM loaders WHERE owner=?', [$username]);

    if($query->num_rows === 0) {
        return 11;
    }

    $row_data = $query->fetch_all(1)[0];

    return [
        'name' => $row_data['name'],
        'key' => $row_data['loader_key'],
        'owner' => $row_data['owner']
    ];
}