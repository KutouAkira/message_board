<?php
$db_username="";
$db_password="";
$database="";
$host="";
$port="";
$cnt = mysqli_connect($host, $db_username, $db_password, $database, $port);
$root_pwd=md5('****************');