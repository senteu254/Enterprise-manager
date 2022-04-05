
<?php
session_start();
if(!isset($_SESSION['UserID']) && !isset($_SESSION['AccessLevel']) && !isset($_SESSION['UsersRealName']) && !isset($_SESSION['DatabaseName'])){
header('location:../index.php');
}
include_once('Menu_Links.php');
include 'inc/db_config.php';
if(is_numeric($_GET['id'])){
$id=$_GET['id'];
}else{
die ('Invalid Request Content Please Try Again!');
}
$qry="DELETE FROM irq_request WHERE requestid='".$id."'";
	mysqli_query($conn,$qry) or die('Could not run Query: ' . mysqli_error($conn));
	$_SESSION['msg']='<ul class="states"><li class="succes">Success: Draft Request Deleted Successfully</li></ul>';
header('location:../'. $link .'?Ref=Draft');
?>