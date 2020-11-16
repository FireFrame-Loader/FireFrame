<?php
include_once('utils.php');
start_session();
session_regenerate_id();
session_unset();
session_destroy();
header("Location: ". process_link("login.php",true));
?>