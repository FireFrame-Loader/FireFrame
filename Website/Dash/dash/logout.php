<?php 
require '../functions/session.php';

session_destroy();

header("Location: ../login.php");

