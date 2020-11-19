<?php
namespace auth;

//TODO : maybe use oop?

/*
 * 0 = invalid username
 * 1 = invalid password
 * 2 = success
 * 4 = invalid_license
 */

function owner_login( $connection, $username, $password){
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

#region account
function owner_change_password($connection, $username, $old_password, $new_password){
    $login_output = owner_login($connection, $username, $old_password);

    if(!is_array($login_output)) {
        return $login_output;
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $connection->query('UPDATE users SET password=? WHERE username=?', [$hashed_password, $username]);

    return 2;
}

function owner_delete_account($connection, $username, $confirm_password){
    $login_output = owner_login($connection, $username, $confirm_password);

    if(!is_array($login_output)) {
        return $login_output;
    }

    $queries[] = 'DELETE FROM users WHERE username=?';

    $queries[] = 'DELETE FROM loaders WHERE owner=?';

    $queries[] = 'DELETE FROM loader_licenses WHERE owner=?';

    $queries[] = 'DELETE FROM loader_users WHERE owner=?';

    $queries[] = 'DELETE FROM loader_modules WHERE owner=?';

    foreach($queries as $query) {
        $connection->query($query, [$username]);
    }

    return 2;
}

function owner_activate_license($connection, $username, $license){
    $query = $connection->query('SELECT `type` FROM licenses WHERE code=?', [$license]);

    if($query->num_rows === 0) {
        return 4;
    }

    $owner_data = owner_fetch($connection, $username);

    $type = $query->fetch_assoc()['type'];

    $expiry = static function() use($owner_data, $type) { //TODO : -
        $month_str = '+1 month';

        if($type !== 0)
            return -1;

        $user_expiry = $owner_data['expires'];

        if($owner_data['expires'] == null)
            return strtotime($month_str);

        return strtotime($month_str, $user_expiry);
    };

    $connection->query('UPDATE users SET expires=? WHERE username=?', [$expiry(), $username]);

    $connection->query('DELETE FROM licenses WHERE code=?', [$license]);

    return 2;
}
#endregion