<?php
include "class/User.php";
$user = new User();
$user->loginStatus();
require "settings.php";
include "include/header.php";
if (isset($_POST["query"])) {
    $searchquery=$_POST["query"];
} else {
    header('Location: /');
}

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
        </script>
        <img src="3an-fit.svg" class="mainimg">
        <p><?php echo $notice;?>
        </p>
        <div id="asking" action="submit.php" method="POST">
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
                    </div>
                </div>
            </div>
            <button class="nobutton" onclick="post();"></i> &nbsp;<?php echo $submit;?></button>
            <button class="nobutton" type="reset"><?php echo $clear;?></button>
        </div>
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

            function post() {
                grecaptcha.ready(function() {
                    grecaptcha.execute(
                        '<?php echo $set_recaptchapublickey;?>', {
                            action: 'submit'
                        }).then(function(token) {
                        var xhttp = new XMLHttpRequest();
                        xhttp.open("POST", "submit.php");
                        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        answer = document.getElementById("question").value;
                        gtoken = token; //anon need a specific
                        anon = document.getElementById("anon").checked;
                        xhttp.send("anon=" + anon + "&question=" + answer + "&g-recaptcha-response=" +
                            gtoken);
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
        <?php
  require "settings.php";
  if (isset($_GET['page'])&&$_GET['page']>0) {
      $offset=($_GET['page']-1)*5;
      $page=$_GET['page'];
  //if page is not set it's should be 1-1 *5=0*5 and 2 is 2-1 *5 5
  } else {
      $offset=0;
      $page=1;
  }
  $npage=$page+1;
  $ppage=$page-1;
  
  $query =
      "SELECT question, time,uname, id FROM content WHERE question LIKE '%$searchquery%' ORDER BY id DESC LIMIT 5 OFFSET $offset";

  ($rs = pg_query($conn, $query)) or die("Cannot execute query: $query\n");

  while ($row = pg_fetch_row($rs)) {
      $sqlanswerquery = "SELECT answer, time FROM answer WHERE qid=$row[3] ORDER BY aid DESC ";
      ($rss = pg_query($conn, $sqlanswerquery)) or die("Cannot execute query: $sqlanswerquery\n");
      $cpdid = htmlentities($row[3]);
      echo'<br><div class="tellsandshare"><div class="tells" id="' .
           htmlentities($row[3]) .
          '">
    <div class="header"><span class="header-conetnt"><i class="fas fa-user"></i> ' .
           htmlentities($row[2]) .
          '</span> <span class="header-conetnt"><i class="fas fa-clock"></i> ' .
           htmlentities($row[1]) .
          '</span>
		  <span class="header-conetnt">  <button id="share" onclick=\'toggle();\'>+Share</button>
</div>
    <div class="contenttells">
<p>' .
           htmlentities($row[0]) .
          '</p>
</div><span class="reply-box"><label for="name" class="reply-label">Reply: </label><input id="'.htmlentities($row[3]).'i" name="answer"><button class="reply" onclick="onClick('.htmlentities($row[3]).');"><i class="fas fa-reply"></i></button></span>
<div class="answerscroll">';
      while ($row = pg_fetch_row($rss)) {
          echo '<span class="answerpart"><i class="fas fa-angle-double-right"></i>'.htmlentities($row[0]).'</span><span class="answertime">--'.htmlentities($row[1]).'</span><br>';
      }
      echo '</div>
</div>
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
    </ul></div></div><br>';
  }
  ?>
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item<?php if ($ppage<=0) {
      echo " disabled";
  }?>">
                    <a class="page-link"
                        href="/search.php?page=<?php echo $ppage;?>"
                        aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>
                <li class="page-item"><a class="page-link" href="#"><?php echo $page;?></a></li>
                <li class="page-item">
                    <a class="page-link"
                        href="/search.php?page=<?php echo $npage;?>"
                        aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>
<?php include "include/footer.php";
