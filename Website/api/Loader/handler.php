<?php

//TODO : include functions

use function general\sign_message;

$public_key = "-----BEGIN CERTIFICATE-----
MIIDvzCCAqegAwIBAgIUD7vk8q5vdeJe54QihZTjXK1QB7AwDQYJKoZIhvcNAQEF
BQAwbzELMAkGA1UEBhMCU0MxEzARBgNVBAgMClNleWNoZWxsZXMxEjAQBgNVBAcM
CUN5YmVyRERvUzEQMA4GA1UECgwHRmx1eENETjEUMBIGA1UECwwLQmxhemluZ0Zh
c3QxDzANBgNVBAMMBk1lZHVzYTAeFw0yMDEyMTAxMzIyMzdaFw0yMTAxMDkxMzIy
MzdaMG8xCzAJBgNVBAYTAlNDMRMwEQYDVQQIDApTZXljaGVsbGVzMRIwEAYDVQQH
DAlDeWJlckREb1MxEDAOBgNVBAoMB0ZsdXhDRE4xFDASBgNVBAsMC0JsYXppbmdG
YXN0MQ8wDQYDVQQDDAZNZWR1c2EwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEK
AoIBAQDUvOgmmqGeIdk26XgfMacjrQrcv3wKomqAyqKgZ1SmwBjq3rzfQ+vECOyH
KXM+J2Hy86zfc7/qJ+my5ytsj0fc8mt6nMxDzesYvS52wcbMXmNy5Giz7fYGNr/M
4SR2aH1tw+zb+ToWxrctZFya2AkWvF9McZisDM5OSt3t72cMfQ4/ruKKCVV6flXG
zKvG13dUCYX5dOIDB2B26c/xp9yYNxl4bhWaoUjkbEjDcqSqUuCqX4W/rTEcRTQM
8lLj8txPklj6t9kb+jz2dyiiBDIroRr/leIVZCskLsyL5+GFmo8RX/2pn8Rf6fbS
Lceb+GCiz/SBRWAkQM4SmaP/yx/xAgMBAAGjUzBRMB0GA1UdDgQWBBQkdlciIVB7
dfKwip2pHKYQoWLnEjAfBgNVHSMEGDAWgBQkdlciIVB7dfKwip2pHKYQoWLnEjAP
BgNVHRMBAf8EBTADAQH/MA0GCSqGSIb3DQEBBQUAA4IBAQCbmIUqzPrA5Y+6mml3
/OVbrVRXYfH/KMjR+nJN70bXXTTycPb7I8ujoMTHfRYAY1UZ+bWPAFe/zksp55cf
qEFl1CadYjljg3NXMX8HFxL6/lHwiaORw1j1Qa24PjRE/kxnKXu7wTFSG9bT3B3v
JM+IQtXy/cBgSK7GiAUTtPNBGmQ63/W5Crj90qo1aBU5ND+i6kSZ8f2HPCaTnyUR
df0rpZaUJERPG8FP4xWguw05G+GoKsIH6Llny5CyuGd82/+emctuUivC0Gy4ji2H
SvnfKkuPTzqM99dvgRjyovetJj1NGmQtfbs/9aByOJyiyjEktoI7UIo3bF48F3Iz
dkDm
-----END CERTIFICATE-----";


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
        'data' => $public_key
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
        $aes_key = general\decrypt_rsa($data->aes_key);
        $session = auth\generate_session($connection, $aes_key);
        die(sign_message(json_encode([
            'error' => false,
            'data' => $session
        ])));
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
            case 2: 
                die(sign_message(general\encrypt_aes(json_encode([
                    'error' => true,
                    'type' => 'invalid_hwid'
                    ]),$session_key)));
            case 3: 
                die(sign_message(general\encrypt_aes(json_encode([
                    'error' => true,
                    'type' => 'loader_doesnt_exist'
                    ]),$session_key)));
            case 4: 
                die(sign_message(general\encrypt_aes(json_encode([
                    'error' => true,
                    'type' => 'loader_expired'
                    ]),$session_key)));
            default:
                auth\add_session_db_identifiers($connection, $data->session_id,$request_data->username,$request_data->loader_key);
                die(sign_message(general\encrypt_aes(json_encode([
                    'error' => false,
                    'data' => $auth_data
                    ]),$session_key)));
        }
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
            case 1:
                die(sign_message(general\encrypt_aes(json_encode([
                    'error' => true,
                    'type' => 'loader_expired'
                    ]),$session_key)));
            case 2:
                die(sign_message(general\encrypt_aes(json_encode([
                    'error' => true,
                    'type' => 'user_already_exists'
                    ]),$session_key)));
            case 3:
                die(sign_message(general\encrypt_aes(json_encode([
                    'error' => true,
                    'type' => 'invalid_license'
                    ]),$session_key)));
            default:
                auth\add_session_db_identifiers($connection, $data->session_id,$request_data->username,$request_data->loader_key);
                die(sign_message(general\encrypt_aes(json_encode([
                    'error' => false,
                    'data' => $register_data
                    ]),$session_key)));
        }
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
    default:
    die(sign_message(json_encode([
        'error' => true,
        'type' => 'unknown_command'
    ])));
}

?>


