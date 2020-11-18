<?php
//session_start();

session_start(['use_strict_mode' => 1,
        'use_only_cookies' => 1,
        'use_trans_sid' => 0,
        'cookie_lifetime' => 1800,
        'cookie_secure' => 1,
        'cookie_httponly' => 1]);

function session_valid($session = null) {
    if($session === null) {
        $session = $_SESSION;
    }
    return (isset($session['username']) && $session['user_agent'] === $_SERVER['HTTP_USER_AGENT']);
}
