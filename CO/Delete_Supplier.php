<?php
 session_start();
    if(!isset($_SESSION['Username'])){
         header("Location: login.php");
    }
if(empty($_GET['id'])){
session_start();
	$_SESSION['err_msg']='
					<p><strong>No Supplier was selected!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php?Page=Suppliers");
					exit;
}
if(!is_numeric($_GET['id'])){
session_start();
	$_SESSION['err_msg']='
					<p><strong>Invalid ID!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php?Page=Suppliers");
					exit;
}
require_once('inc/config.php');
$rowCount = count($_GET["id"]);
for($i=0;$i<$rowCount;$i++) {
$result=mysqli_query($conn,"DELETE FROM suppliers WHERE SupplierID='" .$_GET["id"][$i]. "'");
}
if($result==NULL){
session_start();
	$_SESSION['err_msg']='
					<p><strong>Supplier Cannot deleted because Contract have been assigned to this supplier </strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php?Page=Suppliers");
					exit;
}else{
session_start();
	$_SESSION['msg']='
					<p><strong>Supplier Deleted Successfully!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php?Page=Suppliers");
					exit;
	}
?>


