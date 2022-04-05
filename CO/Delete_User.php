<?php
 session_start();
    if(!isset($_SESSION['Username'])){
         header("Location: login.php");
    }
if(empty($_GET['id'])){
session_start();
	$_SESSION['err_msg']='
					<p><strong>No User was selected!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php?Page=Users");
					exit;
}
require_once('inc/config.php');
$rowCount = count($_GET["id"]);
for($i=0;$i<$rowCount;$i++) {
$result=mysqli_query($conn,"DELETE FROM user_details WHERE Login_ID='" .$_GET["id"][$i]. "'");
}
if($result==NULL){
session_start();
	$_SESSION['err_msg']='
					<p><strong>User was not Deleted!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php?Page=Users");
					exit;
}else{
session_start();
	$_SESSION['msg']='
					<p><strong>User Deleted Successfully!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php?Page=Users");
					exit;
	}
?>


