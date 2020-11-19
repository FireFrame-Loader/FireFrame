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
        die(json_encode([ //TODO: Encrypt with $aes_key
            'error' => false,
            'data' => json_encode($session)
        ]));
    break;
    case 'login': 
        $session_key = get_session_from_id($data->session_id);
        $request_data = json_decode($data->data); //TODO: Decrypt $data->data with $session_key

        $auth_data = is_valid_user($request_data->username,$request_data->password,$request_data->hwid,$request_data->loader_key);

        switch ($auth_data) {
            case 1:
                die(json_encode([
                    'error' => true,
                    'type' => 'invalid_login_details'
                ]));
             break;
            case 2: 
                die(json_encode([
                    'error' => true,
                    'type' => 'invalid_hwid'
                ]));
            break;
            default:
                add_session_db_identifiers($data->session_id,$request_data->username,$request_data->loader_key);
                die(json_encode([ //Encrypt with $session_key
                    'error' => false,
                    'data' => json_encode($auth_data)
                ]));
             break;
        }

    break;
    case 'register':
        $session_key = get_session_from_id($data->session_id,false);
        $request_data = json_decode($data->data); //TODO: Decrypt $data->data with $session_key

    break;
    default:
    die(json_encode([
        'error' => true,
        'type' => 'unknown_command'
    ]));
    break;
}

?>


