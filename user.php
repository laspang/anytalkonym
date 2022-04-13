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
<title><?php echo $name;?>
</title>
<?php include "include/container.php"; ?>
<div class="container contact">
    <h2 style="padding: 0.7em 0px;"><?php echo $name;?>
    </h2>
    <?php include "menu.php"; ?>
    <?php
    //php init program
    if (!isset($_GET["id"])) {
        $mes='<div><i style="color:red" class="fa-solid fa-skull"></i>Not Found or deleted</div>';
    } else {
        $uid=$_GET['id'];
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
        <?php echo $mes;?>
    </h2>
</div>
<?php echo $biotext;?>
<script>
    function onClick(id) {
        grecaptcha.ready(function() {
            grecaptcha.execute(
                '<?php echo $set_recaptchapublickey;?>', {
                    action: 'submit'
                }).then(function(token) {
                var xhttp = new XMLHttpRequest();
                xhttp.open("POST", "answer.php");
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                answer = document.getElementById(id + "i").value;
                gtoken = token;
                xhttp.send("qid=" + id + "&answer=" + answer + "&g-recaptcha-response=" + gtoken);
                xhttp.onload = function() {
                    document.getElementById(id + "i").value = '';
                    console.log(this.responseText);
                    const relust = JSON.parse(this.responseText);
                    if (relust.status == 0) {
                        document.getElementById("alertarea").innerHTML = '<div class="alert success"\>\
            <span class="closeebtn" >&times;</span\>\
                <?php echo $success;?>\
</div>';
                    } else {
                        document.getElementById("alertarea").innerHTML = '<div class="alert"\>\
            <span class="closeebtn" >&times;</span\>\
               <?php echo $error;?>.' + relust.message + '\
</div>';
                    }

                    var close = document.getElementsByClassName("closeebtn");
                    var i;

                    for (i = 0; i < close.length; i++) {
                        close[i].onclick = function() {
                            var div = this.parentElement;
                            div.style.opacity = "0";
                            setTimeout(function() {
                                div.style.display = "none";
                            }, 600);
                        }
                    }

                }
            });
        });
    }

    function post() {
        grecaptcha.ready(function() {
            grecaptcha.execute(
                '<?php echo $set_recaptchapublickey;?>', {
                    action: 'submit'
                }).then(function(token) {
                var xhttp = new XMLHttpRequest();
                xhttp.open("POST", "personalsubmit.php");
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                answer = document.getElementById("question").value;
                gtoken = token; //anon need a specific
                anon = document.getElementById("anon").checked;
                xhttp.send("anon=" + anon +
                    "&nid=<?php echo $_GET["id"];?>&question=" +
                    answer + "&g-recaptcha-response=" + gtoken);
                xhttp.onload = function() {

                    console.log(this.responseText);
                    const relust = JSON.parse(this.responseText);
                    if (relust.status == "success") {
                        document.getElementById("alertarea").innerHTML = '<div class="alert success"\>\
            <span class="closeebtn" >&times;</span\>\
                <?php echo $success;?>\
</div>';
                    } else {
                        document.getElementById("alertarea").innerHTML = '<div class="alert"\>\
            <span class="closeebtn" >&times;</span\>\
               <?php echo $error;?>.' + relust.reason + '\
</div>';
                    }
                    location.reload();
                    document.getElementById("question").value = '';
                    var close = document.getElementsByClassName("closeebtn");
                    var i;

                    for (i = 0; i < close.length; i++) {
                        close[i].onclick = function() {
                            var div = this.parentElement;
                            div.style.opacity = "0";
                            setTimeout(function() {
                                div.style.display = "none";
                            }, 600);
                        }
                    }

                }
            });
        });
    }

    function AddToFavorites(siteTitle, siteURL) {
        if (window.sidebar && window.sidebar.addPanel) { // Mozilla Firefox Bookmark
            window.sidebar.addPanel(siteTitle, siteURL, '');
        } else if (window.external && ('AddFavorite' in window.external)) { // IE Favorite
            window.external.AddFavorite(siteURL, siteTitle);
        } else if (window.opera && window.print) { // Opera Hotlist
            this.title = siteTitle;
            return true;
        } else { // webkit - safari/chrome
            alert('Press ' + (navigator.userAgent.toLowerCase().indexOf('mac') != -1 ? 'Command/Cmd' : 'CTRL') +
                ' + D to bookmark this page.');
        }
    }

    function osshare(t, tt, ttt) {
        const shareData = {
            title: t,
            text: tt,
            url: ttt
        }
        navigator.share(shareData);

    }

    function toggle(id) {
        var x = document.getElementById("sharelist" + id);
        if (x.style.display === "none") {
            x.style.display = "block";
            document.getElementById("share").innerHTML = "&times;Close";
        } else {
            x.style.display = "none";
            document.getElementById("share").innerHTML = "+Share";
        }
    }
</script>
<p>This will only be sent to the user. No one else will see.</p>
<span id="alertarea"></span>
<div id="asking" action="personalsubmit.php" method="POST">
    <div class="first-row">
        <textarea maxlength="1000" id="question" name="question" required></textarea>
        <div class="columinrow">
            <span>&nbsp;&nbsp;&nbsp;<?php echo $anon;?>&nbsp;&nbsp;&nbsp;</span>
            <div class="checkarea">
                <i class="fas fa-times"></i>
                <label class="switch" for="anon">
                    <input type="checkbox" id="anon" name="anon" checked>
                    <span class="slider round"></span>
                </label>
                <i class="fas fa-check"></i>
                <input
                    value="<?php echo $_GET["id"];?>"
                    name="nid" disable style="display:none">
            </div>
        </div>
    </div>
    <button class="nobutton" onclick="post();"></i> &nbsp;<?php echo $submit;?></button>
    <button class="nobutton" type="reset"><?php echo $clear;?></button>
</div>
</div>
<?php include "include/footer.php";
