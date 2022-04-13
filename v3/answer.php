<?php
include('class/User.php');
$user = new User();
$user->loginStatus();
//this is a backend process page input with POST:
//qid, answer
if (!empty($_POST['qid'])&&!empty($_POST['answer'])) {
    if (empty($_SESSION["userid"])) {
        http_response_code('401');
        $code='1';
        $message = 'Not logged in.';
    } else {
        if ($_SERVER['REQUEST_METHOD']=='POST') {
            require_once 'settings.php';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("response"=>$_POST['g-recaptcha-response'], "secret"=>$set_recpatchakey)));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $output = curl_exec($ch);

            $resonse = json_decode($output, true);
            curl_close($ch);
            if ($resonse['success']=="true") {//config handline
                $db_handle = $conn;
                if ($db_handle) {
                    //next user function

                    // if (!isset($_POST['anon'])) {
                    //     $uquery="SELECT first_name FROM \"user\" WHERE id=$uid";
                    //     $uresult=pg_exec($db_handle, $uquery);
                    //     $unreslut = pg_fetch_row($uresult);
                    //     $uname = $unreslut[0];
                    // } elseif ($_POST['anon']=='on') {
                    //     $uname="The anonymous";
                    // } else {
                    //     http_response_code('422');
                    //     //echo "Error query problem.";
                    //     $status="failed";
                    //     $reason="Spam problem";
                    //     $reasoncode="3";
                    // }

                    $time= date("Y-m-d h:i:sa");
                    $uid=$_SESSION['userid'];
                    $qid=$_POST['qid'];
                    $answer=$_POST['answer'];
                    $question = str_replace('"', '&quot;', $answer);
                    $question = str_replace("'", '&#39;', $answer);
                    $question =str_replace("<", "&lsaquo;", $answer);
                    $question =str_replace(">", "&rsaquo;", $answer);
                    //to perform a absoulte safe env maybe use if to check again?
                    $query = "INSERT INTO \"answer\" (answer, time, uid, qid) VALUES ('$answer', '$time', '$uid','$qid'); ";
                    $result = pg_exec($db_handle, $query);
                    pg_close($db_handle);
                    if ($db_handle==true) {
                        $code='0';
                        $message='Good';
                    } else {
                        http_response_code('500');
                        $code='12';
                        $message='Try again later';
                    }
                } else {
                    http_response_code('500');
                    $code='11';
                    $message='Wring database';
                }
            } else {
                http_response_code('429');
                $code='3';
                $message='Captcha incomplete';
            }
        } else {
            http_response_code('422');
            $code='2';
            $message='Incorect method';
        }
    }
} else {
    http_response_code('422');
    $code='4';
    $message='Not complete yet.';
}
header('Content-Type: application/json');

echo '{"status":'.$code.',"message":"'.$message.'"}';
