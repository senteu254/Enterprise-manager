<?php
ob_start();
$mainlink = "index.php?Application=IRQ2&Link=";
require_once('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$approver=$_POST['state'];
$doc=$_POST['doc'];
$Appid=$_POST['Appid'];
if(isset($_POST['end'])){
$end=1;
}else{
$end=0;
}
if(isset($_POST['decision'])){
$decision=1;
}else{
$decision=0;
}
if(isset($_POST['Delete'])){
$result=DB_query("DELETE FROM irq_levels WHERE level_id='" .$Appid. "' AND doc_id='" . $doc . "'");
if($result==true){
$_SESSION['msg']='<ul class="states"><li class="succes">Success: Level Deleted Successfully from the Workflow</li></ul>';
	header('location:'. $mainlink .'ManageTasks&Tab=2&Doc='. $doc .'');
	exit;
	}else{
	$_SESSION['msg']='<ul class="states"><li class="error">Error: '.DB_error().'</li></ul>';
	header('location:'. $mainlink .'ManageTasks&Tab=2&Doc='. $doc .'');
	exit;
	}
}
if(isset($_POST['submit'])){
if(isset($Appid) && $Appid !=''){
$insert = "UPDATE irq_levels SET approver_id='$approver', decision='$decision' WHERE level_id='" . $Appid . "' AND doc_id='" . $doc . "'";
DB_query($insert);
$_SESSION['msg']='<ul class="states"><li class="succes">Success: Level Update Successfully</li></ul>';
header('location:'. $mainlink .'ManageTasks&Tab=2&Doc='. $doc .'');
exit;
}

$sql="SELECT level_id FROM irq_levels WHERE doc_id='" . $doc . "' ";
	$result=DB_query($sql);
	$rowcount=DB_num_rows($result);
	if($rowcount >0){
	$new = ($rowcount+1);
	$newappid = $new .$doc;
	}else{
	$newappid =1 .$doc;
	}
	$HeaderSQL="INSERT INTO irq_levels (level_id,doc_id,approver_id,final_approver,decision)
										VALUES('" . $newappid. "','" . $doc . "','" . $approver . "','" . $end . "','" . $decision . "')";
	$Result = DB_query($HeaderSQL);
	$_SESSION['msg']='<ul class="states"><li class="succes">Success: Level Added Successfully to the Workflow</li></ul>';
	header('location:'. $mainlink .'ManageTasks&Tab=2&Doc='. $doc .'');
	exit;
}
ob_end_clean();
?>
