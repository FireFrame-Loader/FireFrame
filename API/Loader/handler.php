<?php

//TODO : include functions

if($_SERVER['HTTP_USER_AGENT'] !== '#F1R3FR4M3L04D3R#'){
    die(sign_message(json_encode([
        'error' => true,
        'type' => 'wrong_user_agent'
    ])));
}

if (!isset($_POST['command'])) {
    die(sign_message(json_encode([
        'error' => true,
        'type' => 'no_command_requested'
    ])));
}

$command = $_POST['command'];

if ($command === 'request_public_key') {
    die(sign_message(json_encode([
        'error' => false,
        'data' => file_get_contents('/var/www/html/api.firefra.me/public_key.crt') //change extension if needed when we generate the actual key.
    ]))); 
}

if (!isset($_POST['data'])) {
    die(sign_message(json_encode([
        'error' => true,
        'type' => 'no_command_data_sent'
    ])));
}
$data = json_decode($_POST['data']);

switch ($command) {
    case 'create_session':
        $aes_key = $data->aes_key; //TODO: Decrypt the aes key with RSA private key
        $session = auth\generate_session($connection, $aes_key);
        die(sign_message(json_encode([ //TODO: Encrypt with $aes_key
            'error' => false,
            'data' => json_encode($session)
        ])));
    break;
    case 'login': 
        $session_key = auth\get_session_from_id($connection, $data->session_id, false);

        if ($session_key === 0) {
            die(sign_message(json_encode([
                'error' => true,
                'type' => 'session_expired'
            ])));
        }

        $request_data = json_decode($data->data); //TODO: Decrypt $data->data with $session_key

        $auth_data = auth\is_valid_user($connection, $request_data->username, $request_data->password, $request_data->hwid, $request_data->loader_key);

        switch ($auth_data) {
            case 1:
                die(sign_message(json_encode([
                    'error' => true,
                    'type' => 'invalid_login_details'
                ])));
             break; //TODO : remove break
            case 2: 
                die(sign_message(json_encode([
                    'error' => true,
                    'type' => 'invalid_hwid'
                ])));
            break;
            case 3: 
                die(sign_message(json_encode([
                    'error' => true,
                    'type' => 'loader_doesnt_exist'
                ])));
            break;
            case 4: 
                die(sign_message(json_encode([
                    'error' => true,
                    'type' => 'loader_expired'
                ])));
            break;
            default:
                auth\add_session_db_identifiers($connection, $data->session_id,$request_data->username,$request_data->loader_key);
                die(sign_message(json_encode([ //Encrypt with $session_key
                    'error' => false,
                    'data' => json_encode($auth_data)
                ])));
             break;
        }

    break;
    case 'register':
        $session_key = auth\get_session_from_id($connection, $data->session_id,false);
        
        if ($session_key === 0) {
            die(sign_message(json_encode([
                'error' => true,
                'type' => 'session_expired'
            ])));
        }

        $request_data = json_decode($data->data); //TODO: Decrypt $data->data with $session_key

        $register_data = auth\insert_new_user($connection, $request_data->username,$request_data->password,$request_data->hwid,$request_data->license,$request_data->loader_key);

        switch ($register_data) {
            case 0:
                die(sign_message(json_encode([
                    'error' => true,
                    'type' => 'loader_doesnt_exist'
                ])));
             break;
            case 1:
                die(sign_message(json_encode([
                    'error' => true,
                    'type' => 'loader_expired'
                ])));
             break;
            case 2:
                die(sign_message(json_encode([
                    'error' => true,
                    'type' => 'user_already_exists'
                ])));
             break;
            case 3:
                die(sign_message(json_encode([
                    'error' => true,
                    'type' => 'invalid_license'
                ])));
             break;
            default:
                auth\add_session_db_identifiers($connection, $data->session_id,$request_data->username,$request_data->loader_key);
                die(sign_message(json_encode([
                    'error' => false,
                    'data' => json_encode($register_data)
                ])));
             break;
        }

    break;
    default:
    die(sign_message(json_encode([
        'error' => true,
        'type' => 'unknown_command'
    ])));
    break;
}

?>


