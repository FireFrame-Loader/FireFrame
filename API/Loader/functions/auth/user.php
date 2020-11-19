<?php
namespace auth;

/* 

1 - invalid credentials
2 - invalid hwid
3 - loader doesnt exist
4 - loader expired
*/

function is_valid_user($username,$password,$hwid,$loader_key) {
    $owner = \get_loader_owner($loader_key);

    if ($owner === 0)
        return 3;

    if ($owner === 1)
        return 4;

    $query = $connection->query('SELECT * FROM loader_users WHERE username=? AND loader_key=? AND owner=? LIMIT 1', [$username,$loader_key,$owner]);

    if ($query->num_rows === 0)
        return 1;

    $row_data = $query->fetch_assoc();

    if (!password_verify($password,$row_data['password']))
        return 1;

    if (strlen($row_data['hwid']) > 0) {
        if ($hwid !== $row_data['hwid'])
            return 2;
    } else
        $connection->query('UPDATE loader_users SET hwid=? WHERE username=? AND loader_key=? AND owner=?',[$hwid,$username,$loader_key,$owner]);

    return [
        'usergroup' => $row_data['usergroup'],
        'expires' => $row_data['expires']
    ];
}

/*

0 - loader doesnt exist
1 - loader expired

*/

function insert_new_user($username,$password,$hwid,$license,$loader_key) {
    $owner = \get_loader_owner($loader_key);
}

?>