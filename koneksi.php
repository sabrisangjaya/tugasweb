<?php
if(!isset($_SESSION)) session_start(); 
$host = "localhost";
$user = "root";
$password = "";
$db = "myweb";
// $host = "sql307.byethost17.com";
// $user = "b17_20991436";
// $password = "sabri123";
// $db = "b17_20991436_web";
$con = mysqli_connect($host, $user, $password,$db);
if (!$con) die("Connection error: " . mysqli_connect_error());
?>