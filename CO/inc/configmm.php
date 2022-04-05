<?php
date_default_timezone_set('Africa/Nairobi');
define('COOKIE_EXPIRE', 60*60*24*100);      //100 days by default
define('COOKIE_PATH', '/');                 //Avaible in whole domain
	$conn = mysqli_connect('localhost', 'root', '')or die('cannot connect'); 
     mysqli_select_db($conn,'contract')or die('cannot select DB');
     ?>