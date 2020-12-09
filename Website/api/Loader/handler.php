<?php

//TODO : include functions

use function general\sign_message;


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

$data = $_POST['data'];

$cleartext = '';

if (general\verify_message($data,$cleartext)) {
    $data = json_decode($cleartext);
} else {
    die(sign_message(json_encode([
        'error' => true,
        'type' => 'invalid_request_signature'
    ])));
}

switch ($command) {
    case 'create_session':
        $aes_key = general\decrypt_rsa($data->aes_key,true); //4096 or 2048 bit private key encryption
        $session = auth\generate_session($connection, $aes_key);
        die(sign_message(json_encode([
            'error' => false,
            'data' => $session
        ])));
    break;
    case 'login': 
        $session_key = auth\get_session_from_id($connection, $data->session_id, false);

        if ($session_key === 0) {
            die(sign_message(general\encrypt_aes(json_encode([
                'error' => true,
                'type' => 'session_expired'
                ]),$session_key)));
        }

        $request_data = json_decode(general\aes_decrypt($data->data,$session_key)); 

        $auth_data = auth\is_valid_user($connection, $request_data->username, $request_data->password, $request_data->hwid, $request_data->loader_key);

        switch ($auth_data) {
            case 1:
                die(sign_message(general\encrypt_aes(json_encode([
                    'error' => true,
                    'type' => 'invalid_login_details'
                    ]),$session_key)));
             break; //TODO : remove break
            case 2: 
                die(sign_message(general\encrypt_aes(json_encode([
                    'error' => true,
                    'type' => 'invalid_hwid'
                    ]),$session_key)));
            break;
            case 3: 
                die(sign_message(general\encrypt_aes(json_encode([
                    'error' => true,
                    'type' => 'loader_doesnt_exist'
                    ]),$session_key)));
            break;
            case 4: 
                die(sign_message(general\encrypt_aes(json_encode([
                    'error' => true,
                    'type' => 'loader_expired'
                    ]),$session_key)));
            break;
            default:
                auth\add_session_db_identifiers($connection, $data->session_id,$request_data->username,$request_data->loader_key);
                die(sign_message(general\encrypt_aes(json_encode([
                    'error' => false,
                    'data' => $auth_data
                    ]),$session_key)));
             break;
        }

    break;
    case 'register':
        $session_key = auth\get_session_from_id($connection, $data->session_id,false);

        if ($session_key === 0) {
            die(sign_message(general\encrypt_aes(json_encode([
                'error' => true,
                'type' => 'session_expired'
                ]),$session_key)));
        }

        $request_data = json_decode(general\aes_decrypt($data->data,$session_key)); 

        $register_data = auth\insert_new_user($connection, $request_data->username,$request_data->password,$request_data->hwid,$request_data->license,$request_data->loader_key);

        switch ($register_data) {
            case 0:
                die(sign_message(general\encrypt_aes(json_encode([
                    'error' => true,
                    'type' => 'loader_doesnt_exist'
                    ]),$session_key)));
             break;
            case 1:
                die(sign_message(general\encrypt_aes(json_encode([
                    'error' => true,
                    'type' => 'loader_expired'
                    ]),$session_key)));
             break;
            case 2:
                die(sign_message(general\encrypt_aes(json_encode([
                    'error' => true,
                    'type' => 'user_already_exists'
                    ]),$session_key)));
             break;
            case 3:
                die(sign_message(general\encrypt_aes(json_encode([
                    'error' => true,
                    'type' => 'invalid_license'
                    ]),$session_key)));
             break;
            default:
                auth\add_session_db_identifiers($connection, $data->session_id,$request_data->username,$request_data->loader_key);
                die(sign_message(general\encrypt_aes(json_encode([
                    'error' => false,
                    'data' => $register_data
                    ]),$session_key)));
             break;
        }
    break;
    case 'activate_license':
        $session = auth\get_session_from_id($connection,$data->session_id,true);

        if ($session === 0) {
            die(sign_message(general\encrypt_aes(json_encode([
                'error' => true,
                'type' => 'session_expired'
                ]),$session['enc_key'])));
        }

        $request_data = json_decode(general\aes_decrypt($data->data,$session['enc_key'])); 

        $license_reedem_data = auth\redeem_license($connection,$session['username'],$request_data->license,$session['loader_key']);

        if ($license_reedem_data === 0) {
            die(sign_message(general\encrypt_aes(json_encode([
                'error' => true,
                'type' => 'invalid_license'
            ]),$session['enc_key'])));
        }

        die(sign_message(general\encrypt_aes(json_encode([
            'error' => false,
            'data' => $license_reedem_data
            ]),$session['enc_key'])));
    break;
    default:
    die(sign_message(json_encode([
        'error' => true,
        'type' => 'unknown_command'
    ])));
    break;
}

?>


