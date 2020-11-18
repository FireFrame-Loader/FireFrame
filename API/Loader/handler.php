<?php

if ($_SERVER['HTTP_USER_AGENT'] === "#F1R3FR4M3L04D3R#") {

} else {
    $array = [
        "error" => true,
        "type" => "wrong_user_agent"
    ];
    die(json_encode($array));
}

?>