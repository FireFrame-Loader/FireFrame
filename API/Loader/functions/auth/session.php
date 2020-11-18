<?php
namespace auth;

function generate_session($aes_key) {
    $session_id= \rnd_string_secure(32);
    $session_expiration = time() + 300;
    $connection->query('INSERT INTO loader_sessions(session_id,enc_key,expiration) VALUES(?,?)',[$session_id,$aes_key,$session_expiration]);
    return ['session_id' => $session_id,
            'expiration' => $session_expiration
           ];
}


?>