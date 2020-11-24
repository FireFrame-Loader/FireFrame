<?php
namespace general;

$connection = new mysqli_wrapper('localhost','root','','fireframe');

function rnd_string_secure($length){
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}

function sign_message($message) {
    putenv('GNUPGHOME=/var/www/.gnupg');
    $gpg = new gnupg();
    $gpg->addsignkey('E7024416A6B8C327A0D75188C6BBA999B2D601AD');

    return $gpg->sign($message);
}

function contains($needle, $haystack)
{
    return strpos($haystack, $needle) !== false;
}


?>