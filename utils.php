<?php

require 'mysqli_wrapper.php';

$connection = new mysqli_wrapper('localhost','admin','Osmakdegu123456789#','fireframe');

function is_onion() {
    $http_host = $_SERVER['HTTP_HOST'];
    if ($http_host == "etqz5veooa2zlcftxzkbxs6k4kvcbyqfuiq7uesxspwikcwzxamnzsyd.onion")
        return true;
    return false;
}

function process_link($add,$dash) {
    if(is_onion())
        return 'http://etqz5veooa2zlcftxzkbxs6k4kvcbyqfuiq7uesxspwikcwzxamnzsyd.onion/' . ($dash ? 'dash/' : '') . $add;

    return 'https://' . ($dash ? 'dash.' : '') . 'firefra.me/' . $add;
}

function start_session() {
    session_start(['use_strict_mode' => 1,
               'use_only_cookies' => 1,
               'use_trans_sid' => 0,
               'cookie_lifetime' => 1800,
               'cookie_secure' => 1,
               'cookie_httponly' => 1]);
}

function check_login() {
    if (isset($_SESSION['logged_in']) && (bool)$_SESSION['logged_in'] && $_SESSION['user_agent'] === $_SERVER['HTTP_USER_AGENT']) {

    } else {
        header("Location: " . process_link("login.php",true));
        die('invalid login');
    }
}

function get_expiration($exp) {
    if ($exp == -1)
        return "Never";

    if ($exp <= time())
        return "Expired";

    if ($exp > time())
        return date("F j, Y, g:i a", $exp);
}

function get_acc_type($username,$exp) {
    if ($username === "pest03" || $username === "denny" || $username === "21dogs" || $username === "FinGu")
        return "Admin";

    if ($exp === "Never")
        return "Lifetime";

    if ($exp === "Expired")
        return "Normal";

    return "Premium";
}

function validate_password($password) {
    $upper_case = preg_match('@[A-Z]@', $password);
    $lower_case = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $special_chars = preg_match('@[^\w]@', $password);

    if ($upper_case && $lower_case && $number && $special_chars && strlen($password) >= 8)
        return true;

    return false;
}

function delete_account($username,$connection) {
    $queries[] = 'DELETE FROM users WHERE username=?';

    $queries[] = 'DELETE FROM loaders WHERE owner=?';

    $queries[] = 'DELETE FROM loader_licenses WHERE owner=?';

    $queries[] = 'DELETE FROM loader_users WHERE owner=?';

    $queries[] = 'DELETE FROM loader_modules WHERE owner=?';

    foreach($queries as $query)
        $connection->query($query, [$username]);
}

function get_expiry($key_type, $expires){
    if($key_type !== 0)
        return -1;

    if($expires >= time())
        return $expires + 2592000;

    return time() + 2592000;
}

function activate_license($username,$sub_key,$key_type,$connection,$expires) {
    $new_expire = get_expiry($key_type, $expires);

    $connection->query('UPDATE users SET expires=? WHERE username=?', [$new_expire, $username]);

    $connection->query('DELETE FROM licenses WHERE code=?', [$sub_key]);

    $_SESSION['expires'] = $new_expire;
    $_SESSION['expired'] = false;
}

function has_loader($username,$connection) {
    $query = $connection->query('SELECT * FROM loaders WHERE owner=?', [$username]);

    if($query->num_rows === 0)
        return "";

    $row_data = $query->fetch_all(1);

    $array = [
        'name' => $row_data[0]['name'],
        'key' => $row_data[0]['loader_key']
    ];

    return json_encode($array);
}

function rnd_string_secure(int $length): string {
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}
function loader_check() {
    if (isset($_SESSION['loader'])) {
        return $_SESSION['loader'];
    } else {
     header("Location: " . process_link("index.php",true));
     die('No loader!');   
    }
}

function fetch_users($loader_key, $owner, $connection) {
    $query = $connection->query('SELECT * FROM loader_users WHERE loader_key=? AND owner=?', [$loader_key, $owner]);

    if($query->num_rows === 0)
        return [];
    
    return $query->fetch_all(1);
}

function fetch_licenses($loader_key,$owner,$connection) {
    $query = $connection->query('SELECT * FROM loader_licenses WHERE loader_key=? AND owner=?', [$loader_key, $owner]);

    if($query->num_rows === 0)
        return [];
    
    return $query->fetch_all(1); 
}

function fetch_modules($loader_key,$owner,$connection) {
    $query = $connection->query('SELECT * FROM loader_modules WHERE loader_key=? AND owner=?', [$loader_key, $owner]);

    if($query->num_rows === 0)
        return [];
    
    return $query->fetch_all(1); 
}

function contains($needle, $haystack)
{
    return strpos($haystack, $needle) !== false;
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

?>
