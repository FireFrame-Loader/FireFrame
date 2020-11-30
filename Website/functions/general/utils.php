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
        return 'Admin';
    }

    if(!check_expiry($expiry)) {
        return 'Normal';
    }

    return 'Premium';
}

function contains($needle, $haystack){
    return strpos($haystack, $needle) !== false;
}

/* 
1 - invalid credentials
2 - already registered 
3 - password isnt strong 
4 - register success 
5 - password change success 
6 - acc delete success
7 - invalid license
8 - license activation successfull
9 - passwords dont match
10 - passwords dont match
11 - you dont have a loader
12 - expired account
13 - you cannot have more than 1 loader
14 - loader create success
15 - no file uploaded
16 - .dll and 8MB
17 - trouble handling the file
18 - upload success
19 - not updating own module!
20 - module doesnt exist
21 - delete module success
22 - pause module success
23 - no licenses
24 - license delete success
25 - amount bigger than 100, duration 1825
26 - sub update success
27 - sub updated user doesnt exist
28 - delete user success
*/

$code_switcher = static function($code){
    switch ($code) {
        case 1:
            return '<div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Invalid credentials!</strong>
          </div>';
        case 2:
            return '<div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Username is already registered!</strong>
          </div>';
        break;
        case 3:
            return '<div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Password isnt strong enough (8 chars, 1 lowercase, 1 uppercase, 1 number and 1 special char)</strong>
          </div>';
        break;
        case 4:
            return '<div class="alert alert-dismissible alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Successfully registered!</strong>
          </div>';
        break;
        case 5:
            return '<div class="alert alert-dismissible alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Password successfully changed!</strong>
          </div>';
        break;
        case 6:
            return '<div class="alert alert-dismissible alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Account deleted!</strong>
          </div>';
        break;
        case 7:
            return '<div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Invalid license code!</strong>
          </div>';
        break;
        case 8:
            return '<div class="alert alert-dismissible alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>License activated!</strong>
          </div>';
        break;
        case 9:
            return '<div class="alert alert-dismissible alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Passwords do not match!</strong>
          </div>';
        break;
        case 10:
            return '<div class="alert alert-dismissible alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Passwords do not match!</strong>
          </div>';
        break;
        case 11:
            return '<div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>You do not have a loader created!</strong>
          </div>';
        break;
        case 12:
            return '<div class="alert alert-dismissible alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>You cannot create a loader if your account is expired!</strong>
          </div>';
        break;
        case 13:
            return '<div class="alert alert-dismissible alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>You cannot have more than 1 loader!</strong>
          </div>';
        break;
        case 14:
            return '<div class="alert alert-dismissible alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Created loader successfully!</strong>
          </div>';
        break;
        case 15:
            return '<div class="alert alert-dismissible alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>No file uploaded!</strong>
          </div>';
        break;
        case 16:
            return '<div class="alert alert-dismissible alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Uploaded files can only be .dll and under 8MB!</strong>
          </div>';
        break;
        case 17:
            return '<div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>We had trouble handling the file on the server side!</strong>
          </div>';
        break;
        case 18:
            return '<div class="alert alert-dismissible alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Your module has been uploaded successfully!</strong>
          </div>';
        break;
        case 19:
            return '<div class="alert alert-dismissible alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>You can only update your own module!</strong>
          </div>';
        break;
        case 20:
            return '<div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Module does not exist..</strong>
          </div>';
        break;
        case 21:
            return '<div class="alert alert-dismissible alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Module delete successfully!</strong>
          </div>';
        break;
        case 22:
            return '<div class="alert alert-dismissible alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Module (un)paused successfully!</strong>
          </div>';
        break;
        case 23:
            return '<div class="alert alert-dismissible alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>You dont have any unused licenses!</strong>
          </div>';
        break;
        case 24:
            return '<div class="alert alert-dismissible alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>License deleted successfully!</strong>
          </div>';
        break;
        case 25:
            return '<div class="alert alert-dismissible alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Amount cannot be bigger than 100, expiration limit is 1825!</strong>
          </div>';
        break;
        case 26: //deprecated
            return '<div class="alert alert-dismissible alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Subscription updated successfully!</strong>
          </div>';
        break;
        case 27: //deprecated
            return '<div class="alert alert-dismissible alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>User you tried to update, doesnt exist!</strong>
          </div>';
        break;
        case 28:
            return '<div class="alert alert-dismissible alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>User delete successfully!</strong>
          </div>';
        break;
    }
};
