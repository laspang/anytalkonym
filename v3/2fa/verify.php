<?php
require "../class/User.php";
$user=new User();
if ($user->verifytotp($_SESSION["userid"], $_POST["code"])) {
    echo"true";
    header("/index.php");
} else {
    echo"false";
    header("/2fa/verift.html");
}
