<?php
$db_hostname = '';
$db_username = '';
$db_password ='';
$db_dbname = '';
$set_recpatchakey='';
$conn=pg_connect("host=$db_hostname user=$db_username password=$db_password dbname=$db_dbname sslmode=require");
