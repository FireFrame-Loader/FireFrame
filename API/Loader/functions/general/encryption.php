<?php

/* 

Include phpseclib (http://phpseclib.sourceforge.net/rsa/2.0/examples.html), i had problems doing it on live server
see /api.firefra.me/error.log thanks

*/
namespace general;

use phpseclib\Crypt\RSA;

define('CRYPT_RSA_PKCS15_COMPAT', true);

$private_key = '';

function decrypt_rsa($cipher_text){
    $rsa = new RSA();

    $rsa->loadKey($private_key);

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