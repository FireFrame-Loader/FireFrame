<?php

if($_SERVER['HTTP_USER_AGENT'] !== '#F1R3FR4M3L04D3R#'){
    die(json_encode([
        'error' => true,
        'type' => 'wrong_user_agent'
    ]));
}


