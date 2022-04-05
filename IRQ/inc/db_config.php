<?php
include_once ('config.php');
//$servername = "localhost";
//$username = "root";
//$password = "";
$path = 'IRQ/';
$p = dirname(htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8'));
// Create connection
$conn = mysqli_connect($host, $DBUser, $DBPassword);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_select_db($conn,$_SESSION['DatabaseName']);
?> 