<?php
//$connection = mysql_connect("$db_host" , "$db_user" , "$db_pass") or die ("Can't connect to MySQL");
//$db = mysql_select_db($db_name , $connection) or die ("Can't select database.");

$db = new PDO("mysql:host=$db_host;dbname=$db_name", "$db_user", "$db_pass");
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>