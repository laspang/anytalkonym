<?php
require "../vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$dbhostname=$_POST["dbhostname"];
$dbusername=$_POST["dbusername"];
$dbpassword=$_POST["dbpassword"];
$dbname=$_POST["dbname"];
$smtpport=$_POST["port"];
$smtphostname=$_POST["smtphostname"];
$smtpauth=$_POST["encrypt"];
$smtpusername=$_POST["smtpusername"];
$smtppassword=$_POST["smtppassword"];
$db_bundle=pg_connect("host=$dbhostname port=5432 dbname=$dbname user=$dbusername password=$dbpassword sslmode=require");
function breakline()
{
    echo "===========================================<br>";
    flush();
    ob_flush();
}
breakline();
echo "Database check<br>";
breakline();
if ($db_bundle) {
    echo "Database connect............... success<br>";
} else {
    echo "Database connect............... failed<br>";
    exit(1);
}
flush();
ob_flush();

breakline();
echo "SMTP check<br>";
flush();
ob_flush();

breakline();
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host       = $smtphostname;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpusername;
    $mail->Password   = $smtppassword;
    switch ($smtpauth) {
      case "SMTPS":    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;break;
      case "STARTTLS":    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;break;
      case "none":    $mail->SMTPSecure = false;break;
      
    }
    $mail->Port       = $smtpport;

    $mail->setFrom($_POST["smtpemail"], 'Anytalkonym mailer');
    $mail->addAddress($_POST["smtpemail"], 'Anytalkonym mailer');
    $mail->isHTML(false);
    $mail->Subject = 'Test message form anytalkonym';
    $mail->Body    = 'This is the test message form your own anytalkonym server. If you don\'t own your own server, this means that your password might be leaked to the internet, please change it.';

    $mail->send();
    echo 'SMTP connect................success<br>';
} catch (Exception $e) {
    echo "SMTP connect................false Mailer Error: {$mail->ErrorInfo}<br>";
    exit(1);
}
flush();
ob_flush();

breakline();
echo "Start config database<br>";
flush();
ob_flush();

breakline();
$sqlquery="
DROP TABLE IF EXISTS \"user\";
DROP SEQUENCE IF EXISTS user_id_seq;
CREATE SEQUENCE user_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE \"public\".\"user\" (
    \"id\" integer DEFAULT nextval('user_id_seq') NOT NULL,
    \"first_name\" character varying(50) NOT NULL,
    \"last_name\" character varying(50) NOT NULL,
    \"email\" character varying(50) NOT NULL,
    \"password\" character varying(255) NOT NULL,
    \"gender\" character varying(50),
    \"mobile\" character varying(50),
    \"designation\" character varying(50),
    \"type\" character varying(250) DEFAULT 'general' NOT NULL,
    \"status\" character varying(30) DEFAULT 'pending' NOT NULL,
    \"authtoken\" character varying(255) NOT NULL,
    \"TOTP\" text,
    \"TOTPenable\" boolean DEFAULT false NOT NULL,
    \"username\" text NOT NULL,
    \"bios\" text,
    CONSTRAINT \"user_pkey\" PRIMARY KEY (\"id\")
) WITH (oids = false);
";//create user table
$result1=pg_exec($db_bundle, $sqlquery);
if ($result1) {
    echo "Create user table.......success<br>";
} else {
    echo "Error when creating user table...failed<br>";
    exit(1);
}
flush();
ob_flush();

$sqlquery='DROP TABLE IF EXISTS "answer";
DROP SEQUENCE IF EXISTS answer_aid_seq;
CREATE SEQUENCE answer_aid_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."answer" (
    "aid" integer DEFAULT nextval(\'answer_aid_seq\') NOT NULL,
    "qid" integer NOT NULL,
    "uid" integer NOT NULL,
    "answer" text NOT NULL,
    "time" text NOT NULL,
    CONSTRAINT "answer_pkey" PRIMARY KEY ("aid")
) WITH (oids = false);';//create table
$result2=pg_exec($db_bundle, $sqlquery);
if ($result2) {
    echo "Create answer table.......success<br>";
} else {
    echo "Error when creating answer table...failed<br>";
    exit(1);
}
flush();
ob_flush();

$sqlquery='DROP TABLE IF EXISTS "content";
DROP SEQUENCE IF EXISTS content_id_seq;
CREATE SEQUENCE content_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."content" (
    "id" integer DEFAULT nextval(\'content_id_seq\') NOT NULL,
    "uid" integer NOT NULL,
    "question" text,
    "time" character varying(255),
    "uname" text,
    "touid" integer DEFAULT \'0\',
    CONSTRAINT "content_pkey" PRIMARY KEY ("id")
) WITH (oids = false);';//create table
$result3=pg_exec($db_bundle, $sqlquery);
if ($result3) {
    echo "Create content table.......success<br>";
} else {
    echo "Error when creating content table...failed<br>";
    exit(1);
}
flush();
    ob_flush();

$sqlquery='DROP TABLE IF EXISTS "notification";
DROP SEQUENCE IF EXISTS notification_nid_seq;
CREATE SEQUENCE notification_nid_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."notification" (
    "nid" integer DEFAULT nextval(\'notification_nid_seq\') NOT NULL,
    "uid" integer NOT NULL,
    "type" text NOT NULL,
    "content" text NOT NULL,
    "time" text NOT NULL,
    "from" text NOT NULL,
    "status" boolean DEFAULT false
) WITH (oids = false);
';//create table
$result4=pg_exec($db_bundle, $sqlquery);
if ($result4) {
    echo "Create notification table.......success<br>";
} else {
    echo "Error when creating notification table...failed<br>";
    exit(1);
}
flush();
    ob_flush();

breakline();
echo "Generate config file<br>";
flush();
    ob_flush();

breakline();
//gen random words
$str=("1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM");
        $i=1;
        $word="";
        while ($i<=64) {
            $word .=substr($str, rand(0, 62), 1);
            $i+=1;
        }

$myfile = fopen("../settings.php", "w") or die("Unable to open file!");
flush();
    ob_flush();

$txt = '<?php
/*😎*/
/*
Server env setting
$servername is you server\'s name with protocol and port(if needed). If protocol is not set, it might go to the http website
*/
$servername="https://anytalkonym.herokuapp.com";
/*
database config
*/
$db_hostname = "'.$dbhostname.'";
$db_username = "'.$dbusername.'";
$db_password ="'.$dbpassword.'";
$db_dbname = "'.$dbname.'";
/* Please do NOT change this line*/
$conn=pg_connect("host=$db_hostname user=$db_username password=$db_password dbname=$db_dbname sslmode=require");
/*
reCAPTCHA config
Get it from https://developers.google.com/recaptcha/docs/v3
and paste the key here
$set_recaptchapublickey is the public key
$set_recaptchakey is the secret
*/
$set_recaptchapublickey="'.$_POST["cappub"].'";
$set_recaptchakey="'.$_POST["cappri"].'";
/*
SMTP server config
SMTP is require when signup, forgot password
Please find a smtp service provider (like outlook).
$smtp_hostname is required.
$smtp_port default to 587, if you have other encrypt method please read github/phpmailer docs. Set to the smtp port you server have.
$smtp_auth should be true unless you don\'t need to be auth to send mail on your server.
$smtp_username, $smtp_password if $smtp_auth is true then you need to fill it.
$smtp_sendername the name you want :-)
*/
$smtp_hostname="'.$smtphostname.'";
$smtp_auth="'.$smtpauth.'";
$smtp_port='.$smtpport.';
$smtp_authme=true;
$smtp_username="'.$smtpusername.'";
$smtp_password="'.$smtppassword.'";
$smtp_senderaddress="'.$_POST["smtpemail"].'";
$smtp_sendername="Anytalkonym system notification helper";

/*DO NOT CHANGE THE CONTENT HERE
unless you know what you are doing
changing these content might caught a big problem
PLEASE THINK BEFORE YOU TYPE*/
$key = "'.$word.'";
//$key is a server key that can encrypt password if you use save password option, this must be a random string.
';//syntax here
fwrite($myfile, $txt);
fclose($myfile);
echo "ALL GOOD";
