<?php
require "../class/User.php";
$user=new User();
if ($user->loginreally()) {
    echo $user->readnotify($_SESSION['userid']);
} else {
    echo "SERVER INTERNEL ERROR";
}

header('Content-Type:application/json');
