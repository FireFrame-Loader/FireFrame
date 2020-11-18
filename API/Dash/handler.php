<?php

if($_SERVER['HTTP_USER_AGENT'] !== '#F1R3FR4M3D45H#'){
    die(json_encode([
        'error' => true,
        'type' => 'wrong_user_agent'
    ]));
}

?>