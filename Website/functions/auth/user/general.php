<?php
namespace auth\user;

function add($connection, $loader, $username, $password, $group){
    if(empty($group))
        $group = 'Default';

    if(!validate_password($password))
        return 3;

    $query = $connection->query('SELECT username FROM loader_users WHERE username=? AND loader_key=?', [$username, $loader['key']]);

    if($query->num_rows !== 0)
        return 2;

    $connection->query('INSERT INTO loader_users(username, password, usergroup, loader_key, owner) VALUES(?, ?, ?, ?, ?)', [$username, $password, $group, $loader['key'], $loader['owner']]);

    return 0;
}

function update_subscription($connection, $loader, $username, $life = false){
    if($life){
        $connection->query('UPDATE loader_users SET expires=\'-1\' WHERE loader_key=? AND username=?', [$loader['key'], $username]);

        return 26;
    }

    $query = $connection->query('SELECT expires FROM loader_users WHERE loader_key=? AND username=?', [$loader['key'], $username]);

    if($query->num_rows === 0) {
        return 27;
    }

    $expiry_ts = $query->fetch_assoc()['expires'];

    $exp_calc = static function($expiry){
        $tm = '+1 month';

        if($expiry === -1)
            return $expiry;

        if($expiry > time())
            return strtotime($tm, $expiry);

        return strtotime($tm);
    };

    $new_expiry_ts = $exp_calc($expiry_ts);

    $connection->query('UPDATE loader_users SET expires=? WHERE username=? AND loader_key=?', [$new_expiry_ts, $username, $loader['key']]);

    return 26;
}

function delete($connection, $loader, $username){
    $connection->query('DELETE FROM loader_users WHERE username=? AND loader_key=?', [$username, $loader['key']]);

    return 28;
}

function reset_hwid($connection, $loader, $username){
    $connection->query('UPDATE loader_users SET hwid=NULL WHERE loader_key=? AND username=?', [$loader['key'], $username]);
}
