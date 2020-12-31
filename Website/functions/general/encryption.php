<?php
namespace encryption;

use phpseclib\Crypt\RSA;

define('CRYPT_RSA_PKCS15_COMPAT', true);

define('PRIVATE_KEY', ""); //RSA Private key  

function decrypt_rsa($cipher_text){
    $rsa = new RSA();

    $rsa->loadKey(PRIVATE_KEY);

    return $rsa->decrypt($cipher_text);
}

function encrypt_aes($plaintext, $password,$iv = "_FireFrame_") {
    $method = "AES-256-CBC";
    $ciphertext = openssl_encrypt($plaintext, $method, $password, OPENSSL_RAW_DATA, $iv);
   return base64_encode($ciphertext);
}
  
function decrypt_aes($ciphertext, $password, $iv = "_FireFrame_") {
      $method = "AES-256-CBC";
      return openssl_decrypt(base64_decode($ciphertext), $method, $password, OPENSSL_RAW_DATA,  $iv);	
}

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

