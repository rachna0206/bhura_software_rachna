<?php
error_reporting(E_ALL);
ob_start();
$dbhost = '5.9.96.241:3306';
$dbuser = 'bhuracon_test';
$dbname = 'bhuracon_test';
$dbpass = 'TestBhura@123';

 $mysqli = new mysqli($dbhost, $dbuser, $dbpass,$dbname);
 echo "resp=". $qr=mysqli_query($mysqli, "INSERT INTO `tbl_user_role`( `user_id`, `role`, `role_id`) VALUES (1,'test',1)")
 
 ?>