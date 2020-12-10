<?php
namespace auth\owner;

//TODO : maybe use oop?

/*
 * 0 = invalid username
 * 1 = invalid password
 * 2 = success
 * 4 = invalid_license
 */

function login( $connection, $username, $password){
    $row_data = fetch($connection, $username);

    if($row_data === 0){
        return 1;
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

function register($connection, $username, $password){
    $owner_already_exists = static function($username) use ($connection) {
        $query = $connection->query('SELECT username FROM users WHERE username=? LIMIT 1', [$username]);

        return $query->num_rows >= 1;
    };

    if($owner_already_exists($username)) {
        return 2;
    }

    if(!validate_password($password)) {
        return 3;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $connection->query('INSERT INTO users(username, password) VALUES(?, ?)', [$username, $hashed_password]);

    return 4;
}

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

#region account
function change_password($connection, $username, $old_password, $new_password){
    $login_output = login($connection, $username, $old_password);

    if(!is_array($login_output)) {
        return $login_output;
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $connection->query('UPDATE users SET password=? WHERE username=?', [$hashed_password, $username]);

    return 5;
}

function delete_account($connection, $username, $confirm_password){
    $login_output = login($connection, $username, $confirm_password);

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

    return 6;
}

function activate_license($connection, $username, $license){
    $query = $connection->query('SELECT `type` FROM licenses WHERE code=?', [$license]);

    if($query->num_rows === 0) {
        return 7;
    }

    $owner_data = fetch($connection, $username);

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

    return 8;
}
#endregion
