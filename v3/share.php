<?php
require "class/User.php";
require "settings.php";
$user=new User();
$user->loginStatus();
include "include/header.php";
?>
<script src="https://kit.fontawesome.com/571f31f395.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="/main.css">
<script src="https://www.google.com/recaptcha/api.js?render=6Lfn1jUeAAAAAP3XzYsFsirZ_vQghxITdwFQQ35P"></script>

<title><?php echo $name;?>
</title>
<?php include "include/container.php"; ?>
<div class="container contact">
    <h2><?php echo $name;?>
    </h2>
    <?php include "menu.php"; ?>
    <span id="alertarea"></span>
    <div class="table-responsive">
        <script>
            function onSubmit(token) {
                document.getElementById("asking").submit();
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

            function toggle() {
                var x = document.getElementById("sharelist");
                if (x.style.display === "none") {
                    x.style.display = "block";
                    document.getElementById("share").innerHTML = "&times;Close";
                } else {
                    x.style.display = "none";
                    document.getElementById("share").innerHTML = "+Share";
                }
            }
        </script>
        <img src="3an-fit.svg" class="mainimg" href="index.php">
        <p><?php echo $notice;?>
        </p>
        <script>
            function onClick(id) {
                grecaptcha.ready(function() {
                    grecaptcha.execute('6Lfn1jUeAAAAAP3XzYsFsirZ_vQghxITdwFQQ35P', {
                        action: 'submit'
                    }).then(function(token) {
                        var xhttp = new XMLHttpRequest();
                        xhttp.open("POST", "answer.php");
                        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        answer = document.getElementById(id + "i").value;
                        gtoken = token;
                        xhttp.send("qid=" + id + "&answer=" + answer + "&g-recaptcha-response=" +
                            gtoken);
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
        </script><?php
if (isset($_GET["qid"])) {
    $qid=$_GET["qid"];
    $query =
      "SELECT question, time,uname, id FROM content WHERE id=$qid";

    ($rs = pg_query($conn, $query)) or die("Cannot execute query: $query\n");

    while ($row = pg_fetch_row($rs)) {
        $sqlanswerquery = "SELECT answer, time FROM answer WHERE qid=$row[3] ORDER BY aid DESC ";
        ($rss = pg_query($conn, $sqlanswerquery)) or die("Cannot execute query: $sqlanswerquery\n");
        echo'<br><div class="tellsandshare"><div class="tells" id="' .
           htmlentities($row[3]) .
          '">
    <div class="header"><span><i class="fas fa-user"></i> ' .
           htmlentities($row[2]) .
          '</span> <span><i class="fas fa-clock"></i> ' .
           htmlentities($row[1]) .
          '</span><span class="header-conetnt">  <button id="share" onclick=\'toggle();\'>+Share</button></div>
    <div class="contenttells">
<p>' .
           htmlentities($row[0]) .
          '</p>
</div><span class="reply-box"><label for="name" class="reply-label">Reply: </label><input id="'.htmlentities($row[3]).'i" name="answer"><button class="reply" onclick="onClick('.htmlentities($row[3]).');"><i class="fas fa-reply"></i></button></span>
<div class="answerscroll">';
        while ($row = pg_fetch_row($rss)) {
            echo '<span class="answerpart"><i class="fas fa-angle-double-right"></i>'.htmlentities($row[0]).'</span><span class="answertime">--'.htmlentities($row[1]).'</span><br>';
        }
        echo '</div></div>
<div class="sharefatherpart">
   <ul class="cpt" id="sharelist">
      <li class="hfap" style="border-top: 0px solid #000;padding-top: 0.4em;">
        <a class="cjoafs"
          href="https://social-plugins.line.me/lineit/share?url=https://anytalkonym.herokuapp.com/share.php?qid='.$cpdid.'"><i
            class="fa-brands fa-line line"></i><span class="des">Send to line</span></a>
      </li>
      <hr class="biu">
      <li class="hfap"><a class="cjoafs" href="https://t.me/share/url?url=https%3A%2F%2Fanytalkonym.herokuapp.com%2Fshare.php%3Fqid%3D'.$cpdid.'&text=No.%20'.$cpdid.'%20Anytalkonym%20%7C%203AN%20network%20tells"><i
            class="fa-brands fa-telegram telegram"></i><span class="des">Send to telegram</span></a>
      </li>
      <hr class="biu">
      <li class="hfap"><a class="cjoafs"
          href=" mailto:?subject=No.%20'.$cpdid.'%20Anytalkonym%20%7C%203AN%20network%20tells&body=view%20more%20at%20https%3A%2F%2Fanytalkonym.herokuapp.com%2Fshare.php%3Fqid%3D'.$cpdid.' "><i
            class="fa-solid fa-envelope mail"></i><span class="des">Send via email</span></a></li>
      <hr class="biu">
      <li class="hfap">
        <a class="cjoafs"
          href="sms:&body=view%20more%20at%20https%3A%2F%2Fanytalkonym.herokuapp.com%2Fshare.php%3Fqid%3D'.$cpdid.'"><i
            class="fa-solid fa-comment-sms sms"></i><span class="des">Send via SMS</span></a>
      </li>
      <hr class="biu">
      <li class="hfap">
        <a class="cjoafs" href="#"
          onmousedown="AddToFavorites(\'No.'.$cpdid.' tells Anytalkonym | 3AN network\',\'https:\/\/anytalkonym.herokuapp.com/share.php?qid='.$cpdid.'\')"><i
            class="fa-solid fa-book-bookmark bookmark"></i><span class="des">Bookmark it</span></a>
      </li>
      <hr class="biu">
      <li class="hfap">
        <a class="cjoafs"
          href="https://twitter.com/intent/tweet?text=https%3A%2F%2Fanytalkonym.herokuapp.com%2Fshare.php%3Fqid%3D'.$cpdid.'"><i
            class="fa-brands fa-twitter twitter"></i><span class="des">Share on twitter</span></a>
      </li>
      <hr class="biu">
      <li class="hfap">
        <a class="cjoafs" href="#"
          onclick="osshare(\'No. '.$cpdid.' tells Anytalkonym | 3AN network\',\'See what\'s new here.\',\'https:\/\/anytalkonym.herokuapp.com/share.php?qid='.$cpdid.'\')"><i
            class="fa-solid fa-share-nodes system"></i><span class="des">Share</span></a>
      </li>
      <hr class="biu">
      <li class="hfap" style="padding-bottom: 0.4em;">
        <a class="cjoafs" href="#" onclick="windows.print()"><i class="fa-solid fa-print print"></i><span
            class="des">Print</span></a>
      </li>
    </ul>
</div></div><br>';
    }
} else {
    header("Location: /index.php");
}require "settings.php";
