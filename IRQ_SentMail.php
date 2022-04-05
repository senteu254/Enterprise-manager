<?php
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
unset($_SESSION['RequestStockID']);
unset($_SESSION['Requisition_No']);
unset($_SESSION['RFQStockID']);
unset($_SESSION['RFQReq_No']);
if (isset($_POST['RequestType']) && $_POST['RequestType']=='LPO') {
$_SESSION['Requisition_No'] = $_POST['LID'];
$query='SELECT stockid,quantity FROM irq_stockrequestitems
							WHERE dispatchid='.$_POST['LID'].'';
					$RequestStockID = array();
					$results= DB_query($query);
					while($row=DB_fetch_array($results)){
					$RequestStockID = array(
											 'SID'=> $row['stockid'],
											 'QTY'=>  $row['quantity']
											);
					$_SESSION['RequestStockID'][]=$RequestStockID;
					}
 header('location:PO_Header.php?NewOrder=Yes');
}elseif(isset($_POST['RequestType']) && $_POST['RequestType']=='RFQ'){
	$_SESSION['RFQReq_No'] = $_POST['LID'];
	$query='SELECT stockid,quantity FROM irq_stockrequestitems
							WHERE dispatchid='.$_POST['LID'].'';
					$RequestStockID = array();
					$results= DB_query($query);
					while($row=DB_fetch_array($results)){
					$RequestStockID = array(
											 'SID'=> $row['stockid'],
											 'QTY'=>  $row['quantity']
											);
					$_SESSION['RFQStockID'][]=$RequestStockID;
					}
	header('location:SupplierTenderCreate.php?New=Yes');
	}else{

$doc =$_GET['doc'];
$levelid = $_GET['level'];
if(isset($_GET['Re'])){
$ref= $_GET['Re'];
}else{
$ref = 'Ref=Inbox';
}
if(isset($_GET['dept'])){
$dept= "and departments.departmentid ='".$_GET['dept']."'";
}else{
$dept = '';
}
if(isset($_GET['loc'])){
$loc= "and locations.loccode ='".$_GET['loc']."'";
}else{
$loc = "";
}
/******************************************************************************************/
		$EmailSQL="SELECT www_users.email, www_users.realname
					FROM www_users, irq_approvers, irq_levels, departments, locations
					WHERE irq_approvers.approver_id = irq_levels.approver_id AND 
						irq_levels.level_id = '" . $levelid ."' AND
						irq_levels.doc_id = '" . $doc ."' AND
						CASE WHEN irq_approvers.userid ='HOD' THEN departments.authoriser = www_users.userid ". $dept ." WHEN irq_approvers.userid ='ISSUE' THEN locations.authoriser=www_users.userid ".$loc." WHEN irq_approvers.userid ='PROCURE' THEN locations.purchasing_officer=www_users.userid ".$loc." ELSE irq_approvers.userid = www_users.userid END
						LIMIT 1";
		$EmailResult =DB_query($EmailSQL);
		$nums = DB_num_rows($EmailResult);
		if ($nums>0){
		$myEmail=DB_fetch_array($EmailResult);
		 	//header('location:../IRQ_PDFGatepassPortrait.php?id='. $id .'&Email='. $myEmail['email'] .'&User='. $myEmail['realname'] .'&Ref=Inbox');
			
		include ('includes/htmlMimeMail.php');
		$mail = new htmlMimeMail();
		$mail->setText(_('Dear '.$myEmail['realname'].', Requisition Number '.$id.' has been created and is waiting for your authoritation. Please Login to the System for details.'));
		$mail->SetSubject('REQUISITION NEEDS YOUR AUTHORITATION');
		if($_SESSION['SmtpSetting'] == 0){
			$mail->setFrom($_SESSION['CompanyRecord']['coyname'] . ' <' . $_SESSION['CompanyRecord']['email'] . '>');
			$result = $mail->send(array($myEmail['email']));
		}else{
			$result = SendmailBySmtp($mail,array($myEmail['email']));
		}

		$_SESSION['msg'] = _('Success: Requisition No. '). $id . ' ' . _('has been forwarded to'). ' ' . $myEmail['realname'] . ' ' . _('and emailed to') . ' ' . $myEmail['email'];
		 
		}else{
			$_SESSION['msg'] = _('Success: Requisition No. '). $id . ' ' . _('has been forwarded for authoritation');
			}
		header('location:index.php?Application=IRQ2&Link=Inbox');
}
	?>