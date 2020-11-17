<?php
namespace auth;

use mysqli_wrapper;

/*
 * 0 = invalid username
 * 1 = invalid password
 * 2 = success
 */

function owner_login(mysqli_wrapper $connection, $username, $password){
    $row_data = owner_fetch($connection, $username);

    if($row_data === 0){
        return 0;
    }

    if(!password_verify($password, $row_data['password'])) {
        return 1;
    }

    return array(
        'username' => htmlentities($username),
        'expires' => $row_data['expires'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT']
    );
}

function owner_register($connection, $username, $password){
    $owner_already_exists = static function($username) use ($connection) {
        $query = $connection->query('SELECT username FROM users WHERE username=? LIMIT 1', [$username]);

        return $query->num_rows >= 1;
    };

    if($owner_already_exists($username)) {
        return 0;
    }

    if(!validate_password($password)) {
        return 1;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $connection->query('INSERT INTO users(username, password) VALUES(?, ?)', [$username, $hashed_password]);

    return 2;
}