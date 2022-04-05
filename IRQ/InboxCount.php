<?php
session_start();
if(!isset($_SESSION['UserID']) && !isset($_SESSION['AccessLevel']) && !isset($_SESSION['UsersRealName']) && !isset($_SESSION['DatabaseName'])){
header('location:../IRQ.php');
}

include 'inc/db_config.php';
if($_SESSION['Document_id']==1){  //request for purchase 1; 4 stores requisition
if(isset($_SESSION['Doc_Store'])){
$query="SELECT * FROM irq_request z 
							INNER JOIN irq_stockrequest a on z.requestid = a.dispatchid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN locations g ON a.loccode = g.loccode
							WHERE draft=0 AND closed=0 AND Unread=0 AND z.doc_id='". $_SESSION['Doc_Store'] ."' AND CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' WHEN d.userid ='ISSUE' THEN g.authoriser='".$_SESSION['UserID']."' WHEN d.userid ='PROCURE' THEN g.purchasing_officer='".$_SESSION['UserID']."' ELSE d.userid='".$_SESSION['UserID']."' END";
}else{
$query="SELECT * FROM irq_request z
							INNER JOIN irq_stockrequest a on z.requestid = a.dispatchid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN locations g ON a.loccode = g.loccode
							WHERE draft=0 AND closed=0 AND Unread=0 AND z.doc_id='". $_SESSION['Document_id'] ."' AND CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' WHEN d.userid ='ISSUE' THEN g.authoriser='".$_SESSION['UserID']."' WHEN d.userid ='PROCURE' THEN g.purchasing_officer='".$_SESSION['UserID']."' ELSE d.userid='".$_SESSION['UserID']."' END";
}//end of if doc_store
}elseif($_SESSION['Document_id']==2){//transport request 2 within eldoret 3 ouside eldoret
$query="SELECT * FROM irq_request z 
							INNER JOIN irq_transport a on a.TransportID = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							WHERE draft=0 AND closed=0 AND Unread=0 AND CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' ELSE d.userid='".$_SESSION['UserID']."' END";
}elseif($_SESSION['Document_id']==5){//maintenance request 5 breakdown 6 preventive
$query="SELECT * FROM irq_request z 
							INNER JOIN irq_maintenance a on a.maintenanceid = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							WHERE draft=0 AND closed=0 AND Unread=0 AND CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' ELSE d.userid='".$_SESSION['UserID']."' END";
}elseif($_SESSION['Document_id']==7){//Gatepass request 5 breakdown 6 preventive
$query="SELECT * FROM irq_request z 
							INNER JOIN irq_gatepass a on a.gatepassid = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							WHERE draft=0 AND closed=0 AND Unread=0 AND CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' ELSE d.userid='".$_SESSION['UserID']."' END";
}

$results= mysqli_query($conn,$query) or die (mysqli_error($conn));
$inum=mysqli_num_rows($results);
if($inum>0){echo '<span class="num">'. $inum .'</span>';}

?>
