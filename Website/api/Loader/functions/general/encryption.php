<?php

namespace general;

use phpseclib\Crypt\RSA;

define('CRYPT_RSA_PKCS15_COMPAT', true);

/* 2 varieties, just cuz i can x) */

$private_key_2048 = '';

$private_key_4096 = '';


function decrypt_rsa($cipher_text, $use_4096){
    $rsa = new RSA();

    $rsa->loadKey(($use_4096 ? $private_key_4096 : $private_key_2048));

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
  

?>