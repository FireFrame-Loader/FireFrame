<?php

if($_SERVER['HTTP_USER_AGENT'] !== '#F1R3FR4M3D45H#'){
    die(json_encode([
        'error' => true,
        'type' => 'wrong_user_agent'
    ]));
}

if (!isset($_POST['api_key'])) {
    die(json_encode([
        'error' => true,
        'type' => 'no_api_key_sent'
    ]));
}

$api_key = $_POST['api_key']; 

//TODO: Validate API Key

if (!isset($_POST['command'])) {
    die(json_encode([
        'error' => true,
        'type' => 'no_command_requested'
    ]));
}

if (!isset($_POST['data'])) {
    die(json_encode([
        'error' => true,
        'type' => 'no_command_data_sent'
    ]));
}

$data = json_decode($_POST['data']); 

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