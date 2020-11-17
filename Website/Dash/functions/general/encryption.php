<?php
namespace encryption;

function encrypt_file($file_location, $password){
    $file_content = file_get_contents($file_location);

    $context_iv = random_bytes(16);

    $encrypted_file = openssl_encrypt($file_content, 'aes-256-cbc', md5($password), 0, $context_iv);

    $encrypted_file .= '__FireFrame__'.$context_iv;

    return $encrypted_file;
}

function decrypt_file($file_location, $password){
    $file_content = file_get_contents($file_location);

    $file_content = explode('__FireFrame__', $file_content);

    $context_iv = $file_content[1];

    $file_content = $file_content[0];

    $decrypted_file = openssl_decrypt($file_content, 'aes-256-cbc', md5($password), 0, $context_iv);

    return $decrypted_file;
}
