<?php
namespace licenses;

function delete($connection, $loader, $license){
    $connection->query('DELETE FROM loader_licenses WHERE loader_key=? AND code=?', [$loader['key'], $license]);

    return 0;
}

function generate($connection, $loader, /* int */$amount, /* int */$duration, $group = null){
    $amount = filter_var($amount, FILTER_SANITIZE_NUMBER_INT);

    $duration = filter_var($duration, FILTER_SANITIZE_NUMBER_INT);

    if($group === null)
        $group = 'Default';

    if($amount > 100 || $duration > 1825) //not sure if the clientside limit is enough :)
        return 2;

    $out_arr = [];

    $license_structure = static function() {
        return rnd_string_secure(4) . '-' . rnd_string_secure(4) . '-' . rnd_string_secure(4) . '-' . rnd_string_secure(4);
    };

    for($i = 0; $i < $amount; $i++){
        $code = $license_structure();

        $connection->query('INSERT INTO loader_licenses(code, usergroup, duration, loader_key, owner) VALUES(?, ?, ?, ?, ?)', [$code, $group, $duration, $loader['key'], $loader['owner']]);

        $out_arr[] = $code;
    }

    return $out_arr;
}