<?php
include('class/User.php');
$user = new User();
$user->loginStatus();

//builder need to test the performance
if (empty($_SESSION["userid"])) {
    http_response_code('401');
    $status='failed';
    $reason = 'Not logged in.';
    $reasoncode='0';
} else {
    if ($_SERVER['REQUEST_METHOD']=='POST') {
        $question=$_POST['question'];
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
                $uid=$_SESSION['userid'];
                if (!isset($_POST['anon']) || $_POST['anon']=='false') {
                    $uquery="SELECT first_name FROM \"user\" WHERE id=$uid";
                    $uresult=pg_exec($db_handle, $uquery);
                    $unreslut = pg_fetch_row($uresult);
                    $uname = $unreslut[0];
                } elseif ($_POST['anon']=='true') {
                    $uname="The anonymous";
                } else {
                    http_response_code('422');
                    //echo "Error query problem.";
                    $status="failed";
                    $reason="Spam problem";
                    $reasoncode="3";
                }
                $time= date("Y-m-d h:i:sa");
                $uid=$_SESSION['userid'];
                //startping
                $prefix = "@";
                $index = strpos($question, $prefix) + strlen($prefix);
                $result = substr($question, $index);
                $strpos=strpos($result, " ");
                if (substr_count($result, "@")<=5) {
                    if ($user->notify($user->getuserid(substr($result, 0, $strpos)), "ping", "Someone just mention you:)", "System currently")) {
                        //good
                    } else {
                        //bad
                    }
                    while (str_contains($result, $prefix)) {
                        $index = strpos($result, $prefix) + strlen($prefix);
                        $result = substr($result, $index);
                        $strpos=strpos($result, " ");
                        if ($user->notify($user->getuserid(substr($result, 0, $strpos)), "ping", "Someone just mention you:)", "System currently")) {
                            //good
                        } else {
                            //bad
                        }
                    }
                } else {
                    $status='failed';
                    $reason = 'Pinging too much people, preventing flood.';
                    $reasoncode = '3';//please test
                }
               
                //end ping
                $question = str_replace('"', '&quot;', $question);
                $question = str_replace("'", '&#39;', $question);
                $question =str_replace("<", "&lsaquo;", $question);
                $question =str_replace(">", "&rsaquo;", $question);
                //to perform a absoulte safe env maybe use if to check again?
                $query = "INSERT INTO \"content\" (question, time, uid, uname) VALUES ('$question', '$time', '$uid','$uname'); ";
                $result = pg_exec($db_handle, $query);
                pg_close($db_handle);
                if ($db_handle==true) {
                    //echo "Your message have been send";
                    $status='success';
                } else {
                    //echo "Failed try again";
                    $status='failed';
                    $reason='Server-side error  please try again later or report to the admin';
                    $reasoncode='6';
                }
            } else {
                //echo 'Connection attempt failed.';
                $status='failed';
                $reason='Please contact admin about the setting problem';
                $reasoncode = '5';
            }
        } else {
            //echo"Robot? Sorry, you might need to solve the captcha first.";
            $status='failed';
            $reason='Captcha failed please try again';
            $reasoncode='2';
        }
    } else {
        http_response_code('422');
        //echo 'Incorrect method.<br>Error!';
        $status='failed';
        $reason = 'Wrong method.';
        $reasoncode = '1';
    }
}
header('Content-Type: application/json');

if ($status=='failed') {
    $json_result = '{"Status":"'.$status.'","reason":"'.$reason.'","errorcode":'.$reasoncode.'}';
    echo $json_result;
} else {
    $json_result = '{"status":"'.$status.'"}';
    echo $json_result;
}
