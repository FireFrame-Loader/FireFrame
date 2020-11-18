<?php
namespace auth;

/* 

1 - invalid credentials
2 - invalid hwid

*/

function is_valid_user($username,$password,$hwid,$loader_key) {
    $query = $connection->query('SELECT * FROM loader_users WHERE username=? AND loader_key=? LIMIT 1', [$username,$loader_key]);

    if ($query->num_rows === 0)
        return 1;

    $row_data = $query->fetch_assoc();

    if (!password_verify($password,$row_data['password']))
        return 1;

    if (strlen($row_data['hwid']) > 0) {
        if ($hwid !== $row_data['hwid'])
            return 2;
    } else
        $connection->query('UPDATE loader_users SET hwid=? WHERE username=? AND loader_key=?',[$hwid,$username,$loader_key]);

    return [
        'usergroup' => $row_data['usergroup'],
        'expires' => $row_data['expires']
    ];
}

?>