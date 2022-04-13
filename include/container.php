<link rel="icon" type="image/svg" href="/3an-fire-fit.svg">
<!--seo-->
<meta name="title" content="Anonymous asking and answering network">
<meta name="description"
  content="Anyone can asking and answering using 3AN network. It's completely opensource and free. Build your own asking network now.">
<meta name="keywords" content="anonymous, Q&A, social">
<meta name="robots" content="index, nofollow">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="language" content="English">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
</head>

<body class="">
  <!--new navbar-->
  <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
    <div class="container-fluid" style="justify-content:space-around;">
      <div class="navbar-header">
        <a class="navbar-brand" href="https://anytalkonym.herokuapp.com">3AN network</a>
      </div>
      <ul class="nav navbar-nav">
        <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="/account.php">Account</a></li>
        <li class="nav-item"><a class="nav-link" href="#" id="notifyopen">Notice</a></li>
        <li class="nav-item">
          <!-- <div id="count"></div>--><button type="button"
            onclick=' var modal = document.getElementById("notice");modal.style.display = "block";'
            class="btn btn-dark position-relative">
            Inbox
            <span id="count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
              0+
              <span class="visually-hidden">unread messages</span>
            </span>
          </button>
        </li>
        <!-- The Modal -->
        <div id="notice" class="modal">
          <!-- Modal content -->
          <div class="modal-content">
            <div class="modal-header">
              <span class="mclose">&times;</span>
              <h2 style="padding: 0.7em 0px;">Your letters</h2>
            </div>
            <div class="modal-body" id="Noticearea">
            </div>
            <div class="modal-footer">
              <h3>It should be all up to date. Refresh to see the most recent notice.</h3>
            </div>
          </div>

        </div>

        <li class="languageselect">
          <form id="langform" action="/lang.php" target="_self" method="php"><label style="color:white"
              for="lang">language:</label>
            <select name="lang" id="lang" form="langform">

              <option value="zh" <?php switch ($_COOKIE['lang']) {
    case "en":
        break;
    case "zh":
        echo "selected=\"selected\"";
        // no break
    default:
  }?>>🇹🇼🇸🇬🇲🇾🇨🇳🇭🇰🇲🇴Chinese中文
              </option>
              <option value="en" <?php switch ($_COOKIE['lang']) {
    case "en":
        echo "selected=\"selected\"";
        break;
    case "zh":
        break;
    default:
        echo "selected=\"selected\"";
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
?>>🇺🇳🇬🇧🇺🇸English
              </option>
            </select>
          </form>
        </li>
      </ul>
    </div>
  </nav>

  <style>
    /* The Modal (background) */
    .modal {
      display: none;
      /* Hidden by default */
      position: fixed;
      /* Stay in place */
      z-index: 1;
      /* Sit on top */
      left: 0;
      top: 0;
      width: 100%;
      /* Full width */
      height: 100%;
      /* Full height */
      overflow: auto;
      /* Enable scroll if needed */
      background-color: rgb(0, 0, 0);
      /* Fallback color */
      background-color: rgba(0, 0, 0, 0.4);
      /* Black w/ opacity */
      -webkit-animation-name: fadeIn;
      /* Fade in the background */
      -webkit-animation-duration: 0.4s;
      animation-name: fadeIn;
      animation-duration: 0.4s
    }

    /* Modal Content */
    .modal-content {
      position: fixed;
      bottom: 0;
      background-color: #fefefe;
      width: 100%;
      -webkit-animation-name: slideIn;
      -webkit-animation-duration: 0.4s;
      animation-name: slideIn;
      animation-duration: 0.4s
    }

    /* The Close Button */
    .mclose {
      color: white;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }

    .mclose:hover,
    .mclose:focus {
      color: #000;
      text-decoration: none;
      cursor: pointer;
    }

    .modal-header {
      padding: 2px 16px;
      background-color: #5cb85c;
      color: white;
    }

    .modal-body {
      padding: 2px 16px;
    }

    .modal-footer {
      padding: 2px 16px;
      background-color: #5cb85c;
      color: white;
    }

    /* Add Animation */
    @-webkit-keyframes slideIn {
      from {
        bottom: -300px;
        opacity: 0
      }

      to {
        bottom: 0;
        opacity: 1
      }
    }

    @keyframes slideIn {
      from {
        bottom: -300px;
        opacity: 0
      }

      to {
        bottom: 0;
        opacity: 1
      }
    }

    @-webkit-keyframes fadeIn {
      from {
        opacity: 0
      }

      to {
        opacity: 1
      }
    }

    @keyframes fadeIn {
      from {
        opacity: 0
      }

      to {
        opacity: 1
      }
    }
  </style>
  <Script>
    $(function() {
      $('#notifyopen').click(function() {
        $.ajax({
          type: 'GET',
          data: '',
          success: function(response) {

          },
          error: function() {
            console.log("problem when request");
          },
          url: '/notify/read.php',
          cache: false
        });
      });
    });
    $(function() {
      setInterval(function() {
        $.ajax({
          type: 'GET',
          data: '',
          success: function(response) {
            var noticearea = document.getElementById("Noticearea");
            noticearea.innerHTML = "<hr>";
            var ii = Object.keys(response).length - 1;
            i = 0;
            while (i <= ii) {
              noticearea.innerHTML += "From: " + response[i][0] + " - " + response[i][1] +
                "<hr>";
              i++;
            }
          },
          error: function() {
            console.log("REQUEST problem");
          },
          url: '/notify/fetch.php',
          cache: false
        });
        //finish the content now start with the count
        $.ajax({
          type: 'GET',
          data: '',
          success: function(response) {
            var count = document.getElementById("count");
            if (response > 0) {
              count.innerHTML = response;
            } else {
              count.innerHTML = "";
            }

          },
          error: function() {
            console.log("REQUEST problem");
          },
          url: '/notify/fetch.php',
          cache: false
        });
      }, 120000); //update every two minute
    });
    $.ajax({
      type: 'GET',
      data: '',
      success: function(response) {
        var count = document.getElementById("count");
        if (response > 0) {
          count.innerHTML = response;
        } else {
          count.innerHTML = "";
        }

      },
      error: function() {
        console.log("REQUEST problem");
      },
      url: '/notify/fetch.php',
      cache: false
    });
    $.ajax({
      type: 'GET',
      data: '',
      success: function(response) {
        var noticearea = document.getElementById("Noticearea");
        noticearea.innerHTML = "<hr>";
        var ii = Object.keys(response).length - 1;
        i = 0;
        while (i <= ii) {
          noticearea.innerHTML += "From: " + response[i][0] + " - " + response[i][1] + "<hr>";
          i++;
        }
      },
      error: function() {
        console.log("REQUEST problem");
      },
      url: '/notify/fetch.php',
      cache: false
    });
    //start alert
    // Get the modal
    var modal = document.getElementById("notice");

    // Get the button that opens the modal
    var btn = document.getElementById("notifyopen");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("mclose")[0];

    // When the user clicks the button, open the modal
    btn.onclick = function() {
      modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
      modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
    //end alert

    function postans(langcode) {
      var xhttp = new XMLHttpRequest();
      xhttp.open("POST", "lang.php");
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      answer = document.getElementById('lang').value;
      xhttp.send("lang=" + langcode);
      xhttp.onload = function() {
        window.location.reload();
      }
    }
  </script>
  </form>
  </li>
  <script>
    document.addEventListener('input', function(event) {

      // Only run for #lang select
      if (event.target.id !== 'lang') return;

      if (event.target.value === 'zh') {
        postans('zh');
      }

      if (event.target.value === 'en') {
        postans('en');
      }

    }, false);
  </script>
  </ul>

  </div>
  <!--/.nav-collapse -->
  </div>
  </div>

  <div class="container" style="min-height:500px;">
    <div class=''>
    </div>