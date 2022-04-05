<?php
 session_start();
    if(!isset($_SESSION['Username'])){
         header("Location: login.php");
    }
if(empty($_GET['id'])){
session_start();
	$_SESSION['err_msg']='
					<p><strong>No Type was selected!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php?Page=Type");
					exit;
}
if(!is_numeric($_GET['id'])){
session_start();
	$_SESSION['err_msg']='
					<p><strong>Invalid ID!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php?Page=Type");
					exit;
}
require_once('inc/config.php');
$rowCount = count($_GET["id"]);
for($i=0;$i<$rowCount;$i++) {
$result=mysqli_query($conn,"DELETE FROM contract_type WHERE TypeID='" .$_GET["id"][$i]. "'");
}
if($result==NULL){
session_start();
	$_SESSION['err_msg']='
					<p><strong>Type cannot be Deleted because this type have been assign to a contract </strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php?Page=Type");
					exit;
}else{
session_start();
	$_SESSION['msg']='
					<p><strong>Type Deleted Successfully!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php?Page=Type");
					exit;
	}
?>


