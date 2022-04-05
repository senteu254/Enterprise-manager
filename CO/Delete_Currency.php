<?php
 
    if(!isset($_SESSION['Username'])){
         header("Location: login.php");
    }
if(empty($_GET['id'])){
session_start();
	$_SESSION['err_msg']='
					<p><strong>No Currency was selected!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php?Page=Currency");
					exit;
}
if(!is_numeric($_GET['id'])){
session_start();
	$_SESSION['err_msg']='
					<p><strong>Invalid ID!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php?Page=Currency");
					exit;
}
require_once('inc/config.php');
$result=mysqli_query($conn,"DELETE FROM currencies WHERE CurrencyID='" .$_GET["id"]. "'");

if($result==NULL){
session_start();
	$_SESSION['err_msg']='
					<p><strong>Currency Cannot deleted because this Currency have been assigned to Contract or Contract Payment </strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php?Page=Currency");
					exit;
}else{
session_start();
	$_SESSION['msg']='
					<p><strong>Currency Deleted Successfully!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php?Page=Currency");
					exit;
	}
?>



