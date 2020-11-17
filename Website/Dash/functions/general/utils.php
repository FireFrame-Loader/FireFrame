<?php

$connection = new mysqli_wrapper('localhost','root','','fireframe');

function is_onion() {
    return $_SERVER['HTTP_HOST'] === 'etqz5veooa2zlcftxzkbxs6k4kvcbyqfuiq7uesxspwikcwzxamnzsyd.onion';
}

function process_link($add, $dash) {
    if(is_onion()) {
        return 'http://etqz5veooa2zlcftxzkbxs6k4kvcbyqfuiq7uesxspwikcwzxamnzsyd.onion/' . ($dash ? 'dash/' : '') . $add;
    }

    //return 'http://localhost/' . ($dash ? 'dash/' : '') . $add;
    return 'https://firefra.me/' . ($dash ? 'dash/' : '') . $add;
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

function contains($needle, $haystack){
    return strpos($haystack, $needle) !== false;
}
