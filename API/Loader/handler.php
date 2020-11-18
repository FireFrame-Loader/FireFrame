<?php

if($_SERVER['HTTP_USER_AGENT'] !== '#F1R3FR4M3L04D3R#'){
    die(json_encode([
        'error' => true,
        'type' => 'wrong_user_agent'
    ]));
}

if (!isset($_POST['command'])) {
    die(json_encode([
        'error' => true,
        'type' => 'no_command_requested'
    ]));
}

$command = $_POST['command'];

switch ($command) {
    default:
    die(json_encode([
        'error' => true,
        'type' => 'unknown_command'
    ]));
    break;
}

?>


