<?php
require "class/User.php";
require "settings.php";
$user=new User();
$user->loginStatus();
include "include/header.php";
?>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.1.1/css/all.css"
	integrity="sha384-/frq1SRXYH/bSyou/HUp/hib7RVN1TawQYja658FEOodR/FQBKVqT9Ol+Oz3Olq5" crossorigin="anonymous">
<link rel="stylesheet" href="/main.css">
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $set_recaptchapublickey;?>"></script>

<title><?php echo $name;?>
</title>
<?php include "include/container.php"; ?>
<div class="container contact">
    <h2 style="padding: 0.7em 0px;"><?php echo $name;?>
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
                    grecaptcha.execute('<?php echo $set_recaptchapublickey;?>', {
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
?>

<!DOCTYPE html>

<head>
    <link rel="icon" type="image/svg" href="/3an-fire-fit.svg">
    <!--seo-->
    <meta name="title" content="Anonymous asking and answering network">
    <meta name="description"
        content="Anyone can asking and answering using 3AN network. It's completely opensource and free. Build your own asking network now.">
    <meta name="keywords" content="anonymous, Q&A, social">
    <meta name="robots" content="index, nofollow">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="language" content="English">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>

<body>
    <div class="col-sm-6 col-sm-offset-3 text-center align-middle" style="margin-top:10%;">
        <div class="panel panel-primary">
            <header class="panel-heading">2FA is recommanded.</header>
            <div class="panel-body">
                <main>
                    <p>It's recommanded to add 2FA to your account. 2FA can protect you againist the hacker. (But 2FA is
                        not
                        panacea, a
                        good habbit is important.</p>
                    <ul class="pager">
                        <li><a href="/2fa/setup.php">Setup now</a></li>
                        <li><a href="/index.php">Later</a></li>
                    </ul>

                </main>

            </div>
            <div class="panel-footer">
                <footer>Made By lapsangsouchong</footer>
            </div>
        </div>
    </div>
</body>