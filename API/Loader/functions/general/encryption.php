<?php

/* 

Include phpseclib (http://phpseclib.sourceforge.net/rsa/2.0/examples.html), i had problems doing it on live server
see /api.firefra.me/error.log thanks

*/
namespace general;

use phpseclib\Crypt\RSA;

define('CRYPT_RSA_PKCS15_COMPAT', true);

function encrypt($public_key, $plain_text){
    $rsa = new RSA();

    $rsa->loadKey($public_key);

    $rsa->setEncryptionMode(RSA::PUBLIC_FORMAT_PKCS1);

    return $rsa->encrypt($plain_text);
}

function decrypt($private_key, $cipher_text){
    $rsa = new RSA();

    $rsa->loadKey($private_key);

    return $rsa->decrypt($cipher_text);
}

function create_key_pair(){
    $rsa = new RSA();

    $keys = $rsa->createKey(); // ['privatekey' => '?', 'publickey' => '?']

    return $keys;
}

?>