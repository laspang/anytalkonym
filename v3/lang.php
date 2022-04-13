<?php
if ($_POST['lang']=='zh') {
    setcookie('lang', 'zh');
} elseif ($_POST['lang']=='en') {
    setcookie('lang', 'en');
} else {
    http_response_code(403);
}
