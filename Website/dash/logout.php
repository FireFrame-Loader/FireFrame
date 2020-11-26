<?php 
require '../firefra.me/functions/session.php';

session_destroy();

header("Location: ../firefra.me/login.php");

