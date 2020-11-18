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

if ($command === 'request_public_key') {
    die(json_encode([
        'error' => false,
        'data' => file_get_contents('/var/www/html/api.firefra.me/public_key.crt') //change extension if needed when we generate the actual key.
    ])); 
}

if (!isset($_POST['data'])) {
    die(json_encode([
        'error' => true,
        'type' => 'no_command_data_sent'
    ]));
}

$data = json_decode($_POST['data']);

switch ($command) {
    case 'create_session':
        $aes_key = $data->aes_key; //TODO: Decrypt the aes key with RSA private key
        $session = create_session($aes_key); 
        die(json_encode([
            'error' => true,
            'data' => json_encode($session)
        ]));
    break;
    default:
    die(json_encode([
        'error' => true,
        'type' => 'unknown_command'
    ]));
    break;
}

?>


