<?php
namespace auth;

function generate_session($connection, $aes_key) {
    $session_id= \rnd_string_secure(32);
    $session_expiration = time() + 300; // 5 minutes 

    $connection->query('INSERT INTO loader_sessions(session_id,enc_key,expiration) VALUES(?,?)',[$session_id,$aes_key,$session_expiration]);

    return ['session_id' => $session_id,
            'expiration' => $session_expiration
           ];
}

/* 

0 - expired session

*/

function get_session_from_id($connection, $session_id,$fetch_all) {
    $query = $connection->query('SELECT * FROM loader_sessions WHERE session_id=? LIMIT 1',[$session_id]);

    $row_data = $query->fetch_assoc();

    if (time() > $row_data['expiration']) {
        destroy_session($connection,$session_id);
        return 0;
    }

    if ($fetch_all)
        return $row_data;
        
    return $row_data['enc_key'];
}

function add_session_db_identifiers($connection, $session_id,$username,$loader_key) {
    $connection->query('UPDATE loader_sessions SET username=? WHERE session_id=?',[$username,$session_id]);
    $connection->query('UPDATE loader_sessions SET loader_key=? WHERE session_id=?',[$loader_key,$session_id]);
}

function destroy_session($connection, $session_id,$loader_key = "",$owner = "") {
    if ($loader_key !== "" && $owner !== "")
        $connection->query('DELETE FROM loader_sessions WHERE session_id=? AND loader_key=? AND owner=?',[$session_id,$loader_key,$owner]);
        
    $connection->query('DELETE FROM loader_sessions WHERE session_id=?'[$session_id]);
}

?>