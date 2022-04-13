<?php
include "class/User.php";
$user = new User();
$user->loginStatus();
require "settings.php";    require "vendor/autoload.php";
include "include/header.php";
switch ($_COOKIE['lang']) {
      case "en":
          include "lang/en.php";
          break;
      case "zh":
          include "lang/zh.php";
          break;
      default:
          include "lang/en.php";
  }

?>
<link rel="stylesheet" href="/main.css">
<script
    src="https://www.google.com/recaptcha/api.js?render=<?php echo $set_recaptchapublickey;?>">
</script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.1.1/css/all.css"
    integrity="sha384-/frq1SRXYH/bSyou/HUp/hib7RVN1TawQYja658FEOodR/FQBKVqT9Ol+Oz3Olq5" crossorigin="anonymous">
<title><?php echo $name;?>
</title>
<?php include "include/container.php"; ?>
<div class="container contact">
    <h2 style="padding: 0.7em 0px;"><?php echo $name;?>
    </h2>
    <?php include "menu.php"; ?>
    <?php
    //php init program
    if (!isset($_SESSION["userid"])) {
        echo "No user found.";
    } else {
        $uid=$_SESSION["userid"];
        $getbiosqlquery="SELECT bios, username,status,type FROM\"user\" WHERE id=$uid";

        $result=pg_query($conn, $getbiosqlquery);
        $bios=pg_fetch_row($result);
        $bio=$bios[0];
        $Parsedown = new Parsedown();
        $Parsedown->setSafeMode(true);
        $biotext= $Parsedown->text($bio);
        $username=$bios[1];
        if ($bios[2]!="active") {
            $mes='<div><i style="color:red" class="fa-solid fa-skull"></i>Not Found or deleted</div>';
        } elseif ($bios[3]=="administrator") {
            $mes='<div><i style="color:rgb(216, 216, 0)" class="fa-solid fa-star"></i>Admin - '.$username.'</div>';
        } elseif ($bios[3]=="general") {
            $mes='<div><i style="color:rgb(0, 161, 0)" class="fa-solid fa-user"></i>User - '.$username.'</div>';
        }
    }
?>
    <h2 style="padding: 0.7em 0px;">
    </h2>
</div>

<form action="biosum.php" method="post">
    <textarea id="bio" name="text" title="Bio" maxlength="100"
        onkeyup="countChar(this)"><?php echo $biotext;?></textarea>
    <div><i class="fa-brands fa-markdown"></i>Markdown supported</div>
    <div id="charNum"></div>
    <script>
        setInterval(function() {
            countChar(document.getElementById("bio"))
        }, 1000)

        function countChar(val) {
            var len = val.value.length;
            if (len >= 101) {
                val.value = val.value.substring(0, 101);
            } else {
                $('#charNum').text((100 - len) + " words left");
            }
        };
    </script>
    <input class="btn btn-primary" type="submit" value="Submit">
</form>
</div>
<?php include "include/footer.php";
