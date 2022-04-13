<?php
require "../class/User.php";
$user=new User();
if ($user->loginreally()) {
    echo $user->countnotify($_SESSION['userid']);
} else {
    echo "SERVER INTERNEL ERROR";
}

