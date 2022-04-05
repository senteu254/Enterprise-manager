
<?php

/* $Id: InternalStockRequestFulfill.php  $*/

include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

if(isset($_POST['Decline'])){
$comment= $_POST['comment'];
$id = $_POST['DeclineRequestID'];
$doc = 4;
		$sql="SELECT level FROM irq_authorize_state WHERE requisitionid='" . $id . "'";
		$result=DB_query($sql);
		$rowcount=DB_num_rows($result);
		if($rowcount >0){
		$new = ($rowcount);
		$levelid = $new .$doc;
		}else{
		$levelid =1 .$doc;
		}
$HSQL="INSERT INTO irq_authorize_state (requisitionid,
											level,
											approvaldate,
											approver,
											approver_comment)
										VALUES(
											'" . $id . "',
											'" . $levelid . "',
											'" . date('Y-m-d H:i:s') . "',
											'" . $_SESSION['UsersRealName']. "',
											'" . $comment . "')";
	$insert=DB_query($HSQL);
	
$qry="UPDATE irq_stockrequestitems SET cancelled=1, completed=1, cancelled_by='". $_SESSION['UserID'] ."' WHERE dispatchid='". $id ."'";
	DB_query($qry);
$insert = "UPDATE irq_request SET closed='2' WHERE requestid=".$id."";	
DB_query($insert);
$_SESSION['msg'] = '<ul class="states"><li class="warning">' . _('Warning: Requisition No. '). $id . ' ' . _('has been closed because all the items for this request has been cancelled.'). '</li></ul>';
header('location:index.php?Application=IRQ2&Ref=Inbox');
exit;
}
//------------------------------------------------------------------------------------------------------------------
if(isset($_POST['Request']) or isset($_GET['StockIT'])){
if(isset($_POST['StockIT']) or isset($_GET['StockIT'])){
if(isset($_POST['StockIT'])){
$cheks = implode("','", $_POST['StockIT']);
$id = $_POST['dispatch'];
}else{
$cheks = $_GET['StockIT'];
$id = $_GET['dispatch'];
}
$RequestNo = GetNextTransNo(51, $db);

$query1 = "SELECT * FROM irq_request A
		INNER JOIN irq_stockrequest B ON A.requestid=B.dispatchid WHERE B.dispatchid='". $id ."'";	
		$Qresult1=DB_query($query1);
		$rows=DB_fetch_array($Qresult1);

$query = "SELECT * FROM irq_stockrequest INNER JOIN irq_stockrequestitems
							ON irq_stockrequest.dispatchid = irq_stockrequestitems.dispatchid
							WHERE irq_stockrequest.dispatchid='". $id ."' AND irq_stockrequestitems.stockid in ('$cheks')";					
		$Qresult=DB_query($query);
		
		$HeaderSQL="INSERT INTO irq_request (requestid,
											doc_id,
											Requesteddate,
											draft,
											initiator)
										VALUES(
											'" . $RequestNo . "',
											'1',
											'" . date('Y-m-d H:m:s') . "',
											'0',
											'".$rows['initiator']."')";
		$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($HeaderSQL,$ErrMsg,$DbgMsg,true);
		
		$HeaderSQL="INSERT INTO irq_stockrequest (dispatchid,
											loccode,
											departmentid,
											despatchdate,
											narrative)
										VALUES(
											'" . $RequestNo . "',
											'" . $rows['loccode'] . "',
											'" . $rows['departmentid'] . "',
											'" . $rows['despatchdate'] . "',
											'" . addslashes($rows['narrative']) . "')";
		$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($HeaderSQL,$ErrMsg,$DbgMsg,true);
		
			while($row=DB_fetch_array($Qresult)){
			$LineSQL="INSERT INTO irq_stockrequestitems (dispatchitemsid,
													dispatchid,
													stockid,
													quantity,
													decimalplaces,
													uom)
												VALUES(
													'".$row['dispatchitemsid']."',
													'".$RequestNo."',
													'".$row['stockid']."',
													'".$row['quantity']."',
													'".$row['decimalplaces']."',
													'".$row['uom']."')";
			$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request line record could not be inserted because');
			$DbgMsg = _('The following SQL to insert the request header record was used');
			$Result = DB_query($LineSQL,$ErrMsg,$DbgMsg,true);
			}
			$HSQL="INSERT INTO irq_authorize_state (requisitionid,
											level,
											approvaldate,
											approver,
											approver_comment)
										VALUES(
											'" . $RequestNo . "',
											'11',
											'" . date('Y-m-d H:i:s') . "',
											'" .$_SESSION['UsersRealName'] . "',
											'".$_POST['comment']."')";
	$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($HSQL,$ErrMsg,$DbgMsg,true);
		
DB_query("UPDATE irq_stockrequestitems SET on_order='" . $RequestNo . "', cancelled=2, completed=1, cancelled_by='". $_SESSION['UserID'] ."' WHERE irq_stockrequestitems.dispatchid='". $id ."' AND irq_stockrequestitems.stockid in ('$cheks')");
		
	$SQL="SELECT dispatchid
						FROM irq_stockrequestitems
						WHERE dispatchid='".$id."'
							AND completed=0";
				$Result=DB_query($SQL);
				if (DB_num_rows($Result)==0) {
					$SQL="UPDATE irq_request
						SET closed=1
					WHERE requestid='".$id."'";
					$Result=DB_query($SQL);
				$sql="SELECT level FROM irq_authorize_state WHERE requisitionid='" . $id . "'";
				$result=DB_query($sql);
				$rowcount=DB_num_rows($result);
				$doc =4;
					$HSQL="INSERT INTO irq_authorize_state (requisitionid,
											level,
											approvaldate,
											approver,
											approver_comment)
										VALUES(
											'" . $id . "',
											'" . $rowcount.$doc . "',
											'" . date('Y-m-d H:i:s') . "',
											'" . $_SESSION['UsersRealName']. "',
											'" . $_POST['comment'] . "')";
		$insert=DB_query($HSQL);
		$insert = "UPDATE irq_request SET closed='1' WHERE requestid=".$id."";	
		DB_query($conn,$insert);
				}
		$Text='Request for Purchase for the selected Items Has been Created and forwarded for Autoritation';
		$_SESSION['msg'] = '<ul class="states"><li class="succes">' . $Text . '</li></ul>';	
		unset($_POST);	
		unset($_GET);
/******************************************************************************************/
	header('location:IRQ_SentMail.php?doc=1&level=11&dept='.$rows['departmentid'].'&loc='.$rows['loccode'].'');
	$EmailSQL="SELECT www_users.email, www_users.realname
					FROM www_users, irq_approvers, irq_levels, departments, locations
					WHERE irq_approvers.approver_id = irq_levels.approver_id AND 
						irq_levels.level_id = 11 AND
						irq_levels.doc_id = 1 AND
						CASE WHEN irq_approvers.userid ='HOD' THEN departments.authoriser = www_users.userid and departments.departmentid ='". $rows['departmentid'] ."' WHEN irq_approvers.userid ='ISSUE' THEN locations.authoriser=www_users.userid and locations.loccode ='".$rows['loccode']."' WHEN irq_approvers.userid ='PROCURE' THEN locations.purchasing_officer=www_users.userid and locations.loccode ='".$rows['loccode']."' ELSE irq_approvers.userid = www_users.userid END LIMIT 1";
		$EmailResult =DB_query($EmailSQL);
		$nums = DB_num_rows($EmailResult);
		if ($nums>0){
		$myEmail=DB_fetch_array($EmailResult);
		
		include ('includes/htmlMimeMail.php');
		$mail = new htmlMimeMail();
		$mail->setText(_('Dear '.$myEmail['realname'].', Requisition Number '.$RequestNo.' has been created and is waiting for your authoritation. Please Login to the System for details.'));
		$mail->SetSubject('REQUISITION NEEDS YOUR AUTHORITATION');
		if($_SESSION['SmtpSetting'] == 0){
			$mail->setFrom($_SESSION['CompanyRecord']['coyname'] . ' <' . $_SESSION['CompanyRecord']['email'] . '>');
			$result = $mail->send(array($myEmail['email']));
		}else{
			$result = SendmailBySmtp($mail,array($myEmail['email']));
		}

		 $_SESSION['msg'] = '' . _('Success: Requisition No. '). $RequestNo. ' ' . _('has been created and forwarded to'). ' ' . $myEmail['realname'] . ' ' . _('and emailed to') . ' ' . $myEmail['email']. '';
			
		}else{
			$_SESSION['msg'] = '' . _('Success: Requisition No. '). $RequestNo . ' ' . _('has been created and forwarded for authoritation'). '';
			}
/*---------------------------------------------------------------------------------------*/		

}else{
$Text='No Items selected! You cannot submit an empty request please try again or seek assistance from System Administrator.';
		$_SESSION['errmsg'] =  $Text;
		ob_start() ;	
			header('location:index.php?Application=IRQ2&Ref=Inbox');
			ob_end_flush(); 
			exit;
}
}elseif(isset($_POST['CancelRequest'])){
$LID = $_POST['RequestID'];
$qry="UPDATE irq_stockrequestitems SET cancelled=1, completed=1, cancelled_by='" . addslashes($_SESSION['UsersRealName']). "' WHERE dispatchid=".$LID;
$insert = "UPDATE irq_request SET closed='1' WHERE requestid=".$LID."";	
DB_query($insert);
$_SESSION['msg'] = 'Requisition No. '.$LID.' has been Cancelled successfully.';
header('location:index.php?Application=IRQ2&Ref=Inbox');
exit;	
}else{
if( !isset($_POST['fulfil']) or $_POST['fulfil'] == ""){
$_SESSION['errmsg'] = 'No Transaction Type selected!. Please select one option you want to transact.';
header('location:index.php?Application=IRQ2&Ref=Inbox');
exit;
}
$Title = _('Fulfill Stock Requests');
if(isset($_POST['fulfil']) && $_POST['fulfil'] == "Transfer"){
			foreach ($_POST as $key => $value) {
		if (mb_strpos($key,'Qty')) {
			$RequestID = mb_substr($key,0, mb_strpos($key,'Qty'));
			$LineID = mb_substr($key,mb_strpos($key,'Qty')+3);
			$_SESSION['RequestID'] = $RequestID;
			$_SESSION['Qty'][] = filter_number_format($_POST[$RequestID.'Qty'.$LineID]);
			$_SESSION['StockID'][] = ' \''.$_POST[$RequestID.'StockID'.$LineID].'\'';
			$_SESSION['Locode'] = $_POST[$RequestID.'Location'.$LineID];
			$_SESSION['comment'] = $_POST['comment'];
			ob_start() ;
			header('location:StockLocTransfer.php');
			ob_end_flush(); 
			//exit;
			}
			}
			}else{

	foreach ($_POST['LineID'] as $key => $value) {
			$RequestID = $_POST['RequestID'];
			$LineID = $value;
			$Quantity = filter_number_format($_POST[$RequestID.'Qty'.$LineID]);
			$StockID = $_POST[$RequestID.'StockID'.$LineID];
			$Location = $_POST[$RequestID.'Location'.$LineID];
			$Department = $_POST[$RequestID.'Department'.$LineID];
			$Tag = $_POST[$RequestID.'Tag'.$LineID];
			$RequestedQuantity = filter_number_format($_POST[$RequestID.'RequestedQuantity'.$LineID]);
			if (isset($_POST[$RequestID.'Completed'.$LineID])) {
				$Completed=True;
			} else {
				$Completed=False;
			}
			
			$sql="SELECT materialcost, labourcost, overheadcost, decimalplaces FROM stockmaster WHERE stockid='".$StockID."'";
			$result=DB_query($sql);
			$myrow=DB_fetch_array($result);
			$StandardCost=$myrow['materialcost']+$myrow['labourcost']+$myrow['overheadcost'];
			$DecimalPlaces = $myrow['decimalplaces'];

			$Narrative = _('Issue') . ' ' . $Quantity . ' ' . _('of') . ' '. $StockID . ' ' . _('to department') . ' ' . $Department . ' ' . _('from') . ' ' . $Location ;

			$AdjustmentNumber = GetNextTransNo(17,$db);
			$PeriodNo = GetPeriod (Date($_SESSION['DefaultDateFormat']), $db);
			$SQLAdjustmentDate = FormatDateForSQL(Date($_SESSION['DefaultDateFormat']));

			$Result = DB_Txn_Begin();

			// Need to get the current location quantity will need it later for the stock movement
			$SQL="SELECT locstock.quantity
					FROM locstock
					WHERE locstock.stockid='" . $StockID . "'
						AND loccode= '" . $Location . "'";
			$Result = DB_query($SQL);
			if (DB_num_rows($Result)==1){
				$LocQtyRow = DB_fetch_row($Result);
				$QtyOnHandPrior = $LocQtyRow[0];
			} else {
				// There must actually be some error this should never happen
				$QtyOnHandPrior = 0;
			}

			if ($_SESSION['ProhibitNegativeStock']==0 OR ($_SESSION['ProhibitNegativeStock']==1 AND $QtyOnHandPrior >= $Quantity)) {

				$SQL = "INSERT INTO stockmoves (
									stockid,
									type,
									transno,
									loccode,
									trandate,
									userid,
									prd,
									reference,
									qty,
									newqoh)
								VALUES (
									'" . $StockID . "',
									17,
									'" . $AdjustmentNumber . "',
									'" . $Location . "',
									'" . $SQLAdjustmentDate . "',
									'" . $_SESSION['UserID'] . "',
									'" . $PeriodNo . "',
									'" . $Narrative ."',
									'" . -$Quantity . "',
									'" . ($QtyOnHandPrior - $Quantity) . "'
								)";


				$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock movement record cannot be inserted because');
				$DbgMsg =  _('The following SQL to insert the stock movement record was used');
				$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);


				/*Get the ID of the StockMove... */
				$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');

				$SQL="UPDATE irq_stockrequestitems
						SET qtydelivered=qtydelivered+" . $Quantity . "
						WHERE dispatchid='" . $RequestID . "'
							AND dispatchitemsid='" . $LineID . "'";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' ._('The location stock record could not be updated because');
				$DbgMsg = _('The following SQL to update the stock record was used');
				$Result = DB_query($SQL, $ErrMsg, $DbgMsg,true);

				$SQL = "UPDATE locstock SET quantity = quantity - '" . $Quantity . "'
									WHERE stockid='" . $StockID . "'
										AND loccode='" . $Location . "'";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' ._('The location stock record could not be updated because');
				$DbgMsg = _('The following SQL to update the stock record was used');

				$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

				if ($_SESSION['CompanyRecord']['gllink_stock']==1 AND $StandardCost > 0){

					$StockGLCodes = GetStockGLCode($StockID,$db);

					$SQL = "INSERT INTO gltrans (type,
												typeno,
												trandate,
												periodno,
												account,
												amount,
												narrative,
												tag)
											VALUES (17,
												'"  .$AdjustmentNumber . "',
												'" . $SQLAdjustmentDate . "',
												'" . $PeriodNo . "',
												'" . $StockGLCodes['issueglact'] . "',
												'" . $StandardCost * ($Quantity) . "',
												'" . $Narrative . "',
												'" . $Tag . "'
											)";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The general ledger transaction entries could not be added because');
					$DbgMsg = _('The following SQL to insert the GL entries was used');
					$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

					$SQL = "INSERT INTO gltrans (type,
												typeno,
												trandate,
												periodno,
												account,
												amount,
												narrative,
												tag)
											VALUES (17,
												'" . $AdjustmentNumber . "',
												'" . $SQLAdjustmentDate . "',
												'" . $PeriodNo . "',
												'" . $StockGLCodes['stockact'] . "',
												'" . $StandardCost * -$Quantity . "',
												'" . $Narrative . "',
												'" . $Tag . "'
											)";

					$Errmsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The general ledger transaction entries could not be added because');
					$DbgMsg = _('The following SQL to insert the GL entries was used');
					$Result = DB_query($SQL, $ErrMsg, $DbgMsg,true);
				}

				if (($Quantity >= $RequestedQuantity) OR $Completed==True) {
					$SQL="UPDATE irq_stockrequestitems
								SET completed=1
							WHERE dispatchid='".$RequestID."'
								AND dispatchitemsid='".$LineID."'";
					$Result = DB_query($SQL, $ErrMsg, $DbgMsg,true);
				}

				$Result = DB_Txn_Commit();
				
				// Check if request can be closed and close if done.
			if (isset($RequestID)) {
				$SQL="SELECT dispatchid
						FROM irq_stockrequestitems
						WHERE dispatchid='".$RequestID."'
							AND completed=0";
				$Result=DB_query($SQL);
				if (DB_num_rows($Result)==0) {
					$SQL="UPDATE irq_request
						SET closed=1
					WHERE requestid='".$RequestID."'";
					$Result=DB_query($SQL);
				$sql="SELECT level FROM irq_authorize_state WHERE requisitionid='" . $RequestID . "'";
				$result=DB_query($sql);
				$rowcount=DB_num_rows($result);
				$doc =4;
					$HSQL="INSERT INTO irq_authorize_state (requisitionid,
											level,
											approvaldate,
											approver,
											approver_comment)
										VALUES(
											'" . $RequestID . "',
											'" . $rowcount.$doc . "',
											'" . date('Y-m-d H:i:s') . "',
											'" . $_SESSION['UsersRealName']. "',
											'" . $_POST['comment'] . "')";
		$insert=DB_query($HSQL);
		$insert = "UPDATE irq_request SET closed='1' WHERE requestid=".$RequestID."";	
		DB_query($conn,$insert);
				}
			}

				$ConfirmationText = _('An internal stock request for'). ' ' . $StockID . ' ' . _('has been fulfilled from location').' ' . $Location .' '. _('for a quantity of') . ' ' . locale_number_format($Quantity, $DecimalPlaces ) ;
				
				$_SESSION['msg'] = '<ul class="states"><li class="succes">' . $ConfirmationText . '</li></ul>';

				/*if ($_SESSION['InventoryManagerEmail']!=''){
					$ConfirmationText = $ConfirmationText . ' ' . _('by user') . ' ' . $_SESSION['UserID'] . ' ' . _('at') . ' ' . Date('Y-m-d H:i:s');
					$EmailSubject = _('Internal Stock Request Fulfillment for'). ' ' . $StockID;
					if($_SESSION['SmtpSetting']==0){
						      mail($_SESSION['InventoryManagerEmail'],$EmailSubject,$ConfirmationText);
					}else{
						include('includes/htmlMimeMail.php');
						$mail = new htmlMimeMail();
						$mail->setSubject($EmailSubject);
						$mail->setText($ConfirmationText);
						$result = SendmailBySmtp($mail,array($_SESSION['InventoryManagerEmail']));
					}

				
				}*/
			} else {
				$ConfirmationText = _('An internal stock request for'). ' ' . $StockID . ' ' . _('has been fulfilled from location').' ' . $Location .' '. _('for a quantity of') . ' ' . locale_number_format($Quantity, $DecimalPlaces) . ' ' . _('cannot be created as there is insufficient stock and your system is configured to not allow negative stocks');
				
				$_SESSION['msg'] = '<ul class="states"><li class="warning">' . $ConfirmationText . '</li></ul>';
				
			}

			
			ob_start() ;	
			header('location:index.php?Application=IRQ2&Ref=Inbox');
			ob_end_flush(); 	
	}
	}
}//end of else if Request
?>
