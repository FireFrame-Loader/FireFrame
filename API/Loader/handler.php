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
    case 'request_public_key':
        die(json_encode([
            'error' => false,
            'data' => file_get_contents('/var/www/html/api.firefra.me/public_key.crt') //change extension if needed when we generate the actual key.
        ])); 
    break;
    case 'handshake_init':
        
    break;
    default:
    die(json_encode([
        'error' => true,
        'type' => 'unknown_command'
    ]));
    break;
}

?>


