<?php
/*😎*/
/*
Server env setting 
$servername is you server's name with protocol and port(if needed). If protocol is not set, it might go to the http website
*/
$servername="";
/*
database config
*/
$db_hostname = '';
$db_username = '';
$db_password ='';
$db_dbname = '';
/* Please do NOT change this line*/
$conn=pg_connect("host=$db_hostname user=$db_username password=$db_password dbname=$db_dbname sslmode=require");
/*
reCAPTCHA config
Get it from https://developers.google.com/recaptcha/docs/v3
and paste the key here
$set_recaptchapublickey is the public key
$set_recaptchakey is the secret
*/
$set_recaptchapublickey='';
$set_recaptchakey='';
/*
SMTP server config
SMTP is require when signup, forgot password
Please find a smtp service provider (like outlook).
$smtp_hostname is required.
$smtp_port default to 587, if you have other encrypt method please read github/phpmailer docs. Set to the smtp port you server have.
$smtp_auth should be true unless you don't need to be auth to send mail on your server.
$smtp_username, $smtp_password if $smtp_auth is true then you need to fill it.
$smtp_sendername the name you want :-)
*/
$smtp_hostname="";
$smtp_port=587;
$smtp_auth=true;
$smtp_username="";
$smtp_password="";
$smtp_sendername="Anytalkonym system notification helper";

/*DO NOT CHANGE THE CONTENT HERE
unless you know what you are doing
changing these content might caught a big problem
PLEASE THINK BEOFRE YOU TYPE*/
$key = "";
//$key is a server key that can encrpyt password if you use save password option, this must be a random string.