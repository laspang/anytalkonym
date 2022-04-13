<?php
require "../class/User.php";
$user=new User();
$user->loginStatus();
if ($user->readednotify($_SESSION['userid'])) {
    echo "true";
} else {
    echo "false";
}
