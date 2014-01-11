<?php
$sitename = "MorfMedia";

// Mysql settings
$mysql_server = "localhost";
$mysql_user = "morfmedia";
$mysql_password = "OlwHhVJezEa8otRfjSwf";
$mysql_database = "morfmedia";

$conn = mysql_connect($mysql_server, $mysql_user, $mysql_password);
mysql_select_db($mysql_database, $conn) or die("<br><span style=\"color:red\">Could not connect to database and/or table. Please check \"config.php\"</span><br><br<b>A fatal MySQL error occurred</b>.\n<br>\nError: (" . mysql_errno() . ") " . mysql_error());
unset($mysql_server,$mysql_user,$mysql_password);
?>