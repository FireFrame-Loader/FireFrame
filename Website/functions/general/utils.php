<?php

$connection = new mysqli_wrapper('localhost','root','','fireframe');

function is_onion() {
    return $_SERVER['HTTP_HOST'] === 'etqz5veooa2zlcftxzkbxs6k4kvcbyqfuiq7uesxspwikcwzxamnzsyd.onion';
}

function process_link($add, $dash) {
    $to_add = ($dash ? '/dash/' : '/') . $add;

    $out = '://' . $_SERVER['HTTP_HOST'];

    return (
        (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] === 'on')
            ? 'https'.$out : 'http'.$out) .
        $to_add;
}

function validate_password($password) {
    $upper_case = preg_match('@[A-Z]@', $password);
    $lower_case = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $special_chars = preg_match('@[^\w]@', $password);

    return ($upper_case && $lower_case && $number && $special_chars && strlen($password) >= 8);
}

function rnd_string_secure($length){
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}

function check_expiry($expiry){
    if($expiry === null) {
        return false;
    }

    return ($expiry > time() || $expiry === -1);
}

function get_account_type($expiry, $username = null){
    if($username !== null && $username === 'pest03'  || $username === "denny" || $username === "21dogs" || $username === "FinGu") {
        return 'Admin'; // TODO : create admin row in the user's table
    }

    if(!check_expiry($expiry)) {
        return 'Normal';
    }

    return 'Premium';
}

function contains($needle, $haystack){
    return strpos($haystack, $needle) !== false;
}


