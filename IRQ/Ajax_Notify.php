<?php
session_start();
unset($_SESSION['appr']);
if(!isset($_SESSION['UserID']) && !isset($_SESSION['AccessLevel']) && !isset($_SESSION['UsersRealName']) && !isset($_SESSION['DatabaseName'])){
header('location:../index.php');
}
include 'inc/db_config.php';
$query="SELECT  TIMESTAMPDIFF(SECOND, approvaldate, now()) as sec,approver FROM irq_request z 
							INNER JOIN irq_stockrequest a on a.dispatchid = z.requestid 
							INNER JOIN irq_authorize_state b on a.dispatchid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							WHERE TIMESTAMPDIFF(SECOND, approvaldate, now()) <40 AND draft=0 AND closed=0 AND Unread=0 AND d.userid='".$_SESSION['UserID']."'";

$results= mysqli_query($conn,$query) or die (mysqli_error($conn));
$inum=mysqli_num_rows($results);
if($inum >0){
echo $inum;
}
?>
