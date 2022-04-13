<?php
require "../class/User.php";
$user= new User();
$user->vertotp($_SESSION["userid"], $_POST['code']);
