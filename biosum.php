<?php

include "class/User.php";
$user = new User();
$user->loginStatus();
if ($user->updatebio($_POST["text"], $_SESSION["userid"])) {
    $var="Location: user.php?id=".$_SESSION["userid"];
    header($var);
} else {
    echo "SERVER INTERNEL ERROR";
}
