<?php
if(!is_numeric($_GET['LID'])){
ob_start();
die('<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                Invalid Request, Please try Again
              </div>');
}
$LID = $_GET['LID'];
$level=$_GET['LV'];
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$results = "SELECT *, a.loccode, z.doc_id, a.departmentid FROM irq_request z 
							INNER JOIN irq_stockrequest a on z.requestid = a.dispatchid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN locations g ON a.loccode = g.loccode
							INNER JOIN www_users y ON z.initiator = y.userid
							WHERE draft=0 AND closed=0 AND b.Sent=0 AND 
							z.requestid='" . $LID . "' AND level='".$level."' AND
							CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' 
							WHEN d.userid ='ISSUE' THEN g.authoriser='".$_SESSION['UserID']."' 
							WHEN d.userid ='PROCURE' THEN g.purchasing_officer='".$_SESSION['UserID']."' 
							ELSE d.userid='".$_SESSION['UserID']."' END
							GROUP BY requestid";
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
						$rows = DB_fetch_array($welcome_viewed);
						$doc = $rows['doc_id'];
						$loccode = $rows['loccode'];
						$dept = $rows['departmentid'];
						if(isset($rows['final_approver']) && $rows['final_approver']==1){
						$final=TRUE;
						}else{
						$final=FALSE;
						}
						if(isset($rows['decision']) && $rows['decision']==1){
						$decision=TRUE;
						}else{
						$decision=FALSE;
						}
if($rows['Unread']==0){
$insert = "UPDATE irq_authorize_state SET Unread='1' WHERE requisitionid=".$LID." AND level=".$level."";
DB_query($insert);
}
		
		$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
		$redirect = htmlspecialchars($protocol.''.$_SERVER['HTTP_HOST'].''.$_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8').'?Application=IRQ2&Link=Inbox';

if(isset($_POST['Forward'])){
$comment= $_POST['comment'];
		$sql="SELECT MAX(level) AS currentlevel FROM irq_authorize_state WHERE requisitionid='" . $LID . "'";
		$result=DB_query($sql);
		$row=DB_fetch_array($result);
		if($final==TRUE){
		$levelid = $row['currentlevel'];
		}else{
		$levelid = ($row['currentlevel']+10);
		}
$HSQL="INSERT INTO irq_authorize_state (requisitionid,
											level,
											approvaldate,
											approver,
											approver_comment)
										VALUES(
											'" . $LID . "',
											'" . $levelid . "',
											'" . date('Y-m-d H:i:s') . "',
											'" . addslashes($_SESSION['UsersRealName']). "',
											'" . $comment . "')";
	$insert=DB_query($HSQL);
	
$insert = "UPDATE irq_authorize_state SET sent='1' WHERE requisitionid=".$LID." AND level=".$rows['level']."";
DB_query($insert);

		$EmailSQL="SELECT www_users.email, www_users.realname
					FROM www_users, irq_approvers, irq_levels, departments, locations
					WHERE irq_approvers.approver_id = irq_levels.approver_id AND 
						irq_levels.level_id = '" . $levelid ."' AND
						irq_levels.doc_id = '" . $doc ."' AND
						CASE WHEN irq_approvers.userid ='HOD' THEN departments.authoriser = www_users.userid and departments.departmentid ='". $dept ."' WHEN irq_approvers.userid ='ISSUE' THEN locations.authoriser=www_users.userid and locations.loccode ='".$loccode."' WHEN irq_approvers.userid ='PROCURE' THEN locations.purchasing_officer=www_users.userid and locations.loccode ='".$loccode."' ELSE irq_approvers.userid = www_users.userid END LIMIT 1";
		$EmailResult =DB_query($EmailSQL);
		$nums = DB_num_rows($EmailResult);
		if ($nums>0){
		$myEmail=DB_fetch_array($EmailResult);
		
		/*include ('includes/htmlMimeMail.php');
		$mail = new htmlMimeMail();
		$mail->setText(_('Dear '.$myEmail['realname'].', Requisition Number '.$LID.' has been created and is waiting for your authoritation. Please Login to the System for details.'));
		$mail->SetSubject('REQUISITION NEEDS YOUR AUTHORITATION');
		if($_SESSION['SmtpSetting'] == 0){
			$mail->setFrom($_SESSION['CompanyRecord']['coyname'] . ' <' . $_SESSION['CompanyRecord']['email'] . '>');
			$result = $mail->send(array($myEmail['email']));
		}else{
			$result = SendmailBySmtp($mail,array($myEmail['email']));
		}*/

		 $_SESSION['msg'] = '' . _('Success: Requisition No. '). $LID . ' ' . _('has been forwarded to'). ' ' . $myEmail['realname'] . ' ' . _('and emailed to') . ' ' . $myEmail['email']. '';
			
		}else{
			$_SESSION['msg'] = '' . _('Success: Requisition No. '). $LID . ' ' . _('has been forwarded for authoritation'). '';
			}
?>
		<script>
        window.location.href = "<?php echo $redirect; ?>";
		</script>
		<?php
}//end of post submit

if(isset($_POST['Approved'])){
$comment= $_POST['comment'];
$comment2= "(for Managing Director)";
		$sql="SELECT MAX(level) AS currentlevel FROM irq_authorize_state WHERE requisitionid='" . $LID . "'";
		$result=DB_query($sql);
		$row=DB_fetch_array($result);
		if($final==TRUE){
		$levelid = $row['currentlevel'];
		}else{
		$levelid = ($row['currentlevel']+10);
		$levelid2 = ($row['currentlevel']+20);
		}
$HSQL="INSERT INTO irq_authorize_state (requisitionid,
											level,
											approvaldate,
											approver,
											approver_comment)
										VALUES('" . $LID . "',
											'" . $levelid . "',
											'" . date('Y-m-d H:i:s') . "',
											'" . addslashes($_SESSION['UsersRealName']). "',
											'" . $comment . "'),
											('" . $LID . "',
											'" . $levelid2 . "',
											'" . date('Y-m-d H:i:s') . "',
											'" . addslashes($_SESSION['UsersRealName']). "',
											'" . $comment2 . "')";
	$insert=DB_query($HSQL);
	
$insert = "UPDATE irq_authorize_state SET sent='1' WHERE requisitionid=".$LID." AND level=".$rows['level']."";
DB_query($insert);
$insert = "UPDATE irq_authorize_state SET sent='1' WHERE requisitionid=".$LID." AND level=".$levelid."";
DB_query($insert);

		$EmailSQL="SELECT www_users.email, www_users.realname
					FROM www_users, irq_approvers, irq_levels, departments, locations
					WHERE irq_approvers.approver_id = irq_levels.approver_id AND 
						irq_levels.level_id = '" . $levelid2 ."' AND
						irq_levels.doc_id = '" . $doc ."' AND
						CASE WHEN irq_approvers.userid ='HOD' THEN departments.authoriser = www_users.userid and departments.departmentid ='". $dept ."' WHEN irq_approvers.userid ='ISSUE' THEN locations.authoriser=www_users.userid and locations.loccode ='".$loccode."' WHEN irq_approvers.userid ='PROCURE' THEN locations.purchasing_officer=www_users.userid and locations.loccode ='".$loccode."' ELSE irq_approvers.userid = www_users.userid END LIMIT 1";
		$EmailResult =DB_query($EmailSQL);
		$nums = DB_num_rows($EmailResult);
		if ($nums>0){
		$myEmail=DB_fetch_array($EmailResult);
		
		/*include ('includes/htmlMimeMail.php');
		$mail = new htmlMimeMail();
		$mail->setText(_('Dear '.$myEmail['realname'].', Requisition Number '.$LID.' has been created and is waiting for your authoritation. Please Login to the System for details.'));
		$mail->SetSubject('REQUISITION NEEDS YOUR AUTHORITATION');
		if($_SESSION['SmtpSetting'] == 0){
			$mail->setFrom($_SESSION['CompanyRecord']['coyname'] . ' <' . $_SESSION['CompanyRecord']['email'] . '>');
			$result = $mail->send(array($myEmail['email']));
		}else{
			$result = SendmailBySmtp($mail,array($myEmail['email']));
		}*/

		 $_SESSION['msg'] = '' . _('Success: Requisition No. '). $LID . ' ' . _('has been forwarded to'). ' ' . $myEmail['realname'] . ' for authoritation'; //. _('and emailed to') . ' ' . $myEmail['email']. '';
			
		}else{
			$_SESSION['msg'] = '' . _('Success: Requisition No. '). $LID . ' ' . _('has been forwarded for authoritation'). '';
			}
?>
		<script>
        window.location.href = "<?php echo $redirect; ?>";
		</script>
		<?php
}//end of post submit

if(isset($_POST['Reject'])){
$comment= $_POST['comment'];
		$sql="SELECT MAX(level) AS currentlevel FROM irq_authorize_state WHERE requisitionid='" . $LID . "'";
		$result=DB_query($sql);
		$row=DB_fetch_array($result);
		$levelid = $row['currentlevel'];
$HSQL="INSERT INTO irq_authorize_state (requisitionid,
											level,
											approvaldate,
											approver,
											approver_comment)
										VALUES(
											'" . $LID . "',
											'" . $levelid . "',
											'" . date('Y-m-d H:i:s') . "',
											'" . addslashes($_SESSION['UsersRealName']). "',
											'" . $comment . "')";
	$insert=DB_query($HSQL);
	
//$qry="UPDATE irq_stockrequestitems SET cancelled=1, completed=1, cancelled_by='". $_SESSION['UserID'] ."' WHERE dispatchid='". $LID ."'";
//	DB_query($qry);
$insert = "UPDATE irq_request SET closed='2' WHERE requestid=".$LID."";	
DB_query($insert);
$_SESSION['msg'] =  _('Requisition No. '). $LID . ' ' . _('has been rejected. Reason: '.$comment.'.');

	?>
		<script>
        window.location.href = "<?php echo $redirect; ?>";
		</script>
		<?php
}
if(isset($_GET['Cancel']) && $_GET['id'] !=""){
$qry="UPDATE irq_stockrequestitems SET cancelled=1, completed=1, cancelled_by='" . addslashes($_SESSION['UsersRealName']). "' WHERE itemid=".$_GET['id'];
DB_query($qry);
	$q='SELECT * FROM irq_stockrequestitems WHERE cancelled=0 AND completed=0 AND dispatchid='.$LID;
	$re= DB_query($q);
	$count = DB_num_rows($re);
	if($count ==0){
$insert = "UPDATE irq_request SET closed='1' WHERE requestid=".$LID."";	
DB_query($insert);
$_SESSION['msg'] = '<ul class="states"><li class="warning">' . _('Requisition No. '). $LID . ' ' . _('has been deleted because all the items for this request has been cancelled.'). '</li></ul>';
?>
<script>
        window.location.href = "<?php echo $redirect; ?>";
	</script>
<?php
	}
 prnMsg( _('Success: Item No. '). $_GET['id'] . ' ' . _('has been Cancelled successfully.'), 'success');
}				
echo '<div id="loadingbackground">';
		echo '<div id="progressBar" ><br />
			 <i class="fa fa-spinner fa-pulse fa-4x fa-fw"></i><br>PROCESSING. PLEASE WAIT...
			</div>';
		echo '</div>';
?>
              
			  <div class="mailbox-read-info">
                <h4><?php echo strtoupper($rows['doc_name']); ?></h4>
                <h5>From: <?php echo ucwords(strtolower($rows['realname'])); ?>
                  <span class="mailbox-read-time pull-right"><?php echo  date("d M. Y",strtotime($rows['approvaldate'])).' '. date("h:i A",strtotime($rows['approvaldate'])); ?></span></h5>
              </div>
			  <legend></legend>
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message">
 <span class="pull-right">
<?php  
		$comments="SELECT approver_name, approver, approvaldate, approver_comment,Unread,Sent FROM irq_levels a
	 			INNER JOIN irq_approvers b ON a.approver_id=b.approver_id
				LEFT JOIN irq_authorize_state c ON a.level_id  = c.level and requisitionid='".$LID."'
				WHERE a.doc_id=".$doc." GROUP BY a.level_id
				ORDER BY a.level_id ASC";
				
		$titles="SELECT approver_name FROM irq_levels a
	 			INNER JOIN irq_approvers b ON a.approver_id=b.approver_id
				WHERE a.doc_id=".$doc."
				GROUP BY a.level_id ORDER BY a.level_id ASC";
$first=1;
$tit = array();
$comm = array();
$commentresults= DB_query($comments);
$titleresults= DB_query($titles);
while($comment= DB_fetch_array($commentresults)){
$comm[] = $comment;
}
$tit[] ='USER';
while($title=DB_fetch_array($titleresults)){
$tit[] = $title['approver_name'];
}

for($i=0; $i < count($comm)+1 and $i < count($tit); $i++) {
if($comm[$i]['approver_comment'] !="" && $closed !=2){
$color = 'color:green;';
$check ='<i class="fa fa-check-circle-o fa-2x" style="color:green;" title="'.($i ==0 ? 'Created':'Recommended').' By '.$comm[$i]['approver'].'" aria-hidden="true"></i>';
}elseif($comm[$i]['approver_comment'] !="" && $closed==2){
$color = 'color:#FF0000;';
$check ='<i class="fa fa-times-circle-o fa-2x" style="color:#FF0000;" title="Rejected By '.$comm[$i]['approver'].'" saria-hidden="true"></i>';
}elseif($closed ==1){
$color = 'color:green;';
$check ='<i class="fa fa-check-circle-o fa-2x" style="color:green;" title="" aria-hidden="true"></i>';
}else{
$color = 'color:#CCCCCC;';
$check ='<i class="fa fa-circle-o fa-2x" style="color:#CCCCCC;" aria-hidden="true"></i>';
}
if($i !=0){
echo '&nbsp;<i class="fa fa-arrow-right" style="'.$color.'"></i>';
}
echo '<span class="fa-stack fa-2x" style="font-size:17px;">';
echo $check; 
echo '</span>';
echo '<span style="font-size:10px;'.$color.'">'.strtoupper($tit[$i]).'</span>';; 
}
?>
</span>
                <p>Hello Sir/Madam,</p>
				
				<table align="left" style="width:100%">
				<tr><td height="25">Request No.:</td><td><?php echo '<b>'.$rows['requestid'].'</b>'; ?></td>
				<td width="120" height="25">Department:</td><td><?php echo ' <b>'.strtoupper($rows['description']).'</b>'; ?></td></tr>
				<tr><td height="25">Store Location: </td><td><?php echo '<b>'.strtoupper($rows['locationname']).'</b>'; ?></td>
				<td height="25">Required Date: </td><td><?php echo '<b>'.date("d, M Y",strtotime($rows['despatchdate'])).'</b>'; ?></td></tr>
				<tr><td height="25">Reason: </td><td colspan="3"><?php echo '<b>'.$rows['narrative'].'</b>'; ?></td></tr>
				</table><br>
				<?php
				echo '<table style="width:90%;" class="table table-hover table-striped"><tbody>';
				if($final == TRUE && $doc==4){
		echo '<tr>
						<th>' . _('Product') . '</th>
						<th>' . _('Stock Bal') . '</th>
						<th>' . _('Qty Req') . '</th>
						<th>' . _('Qty Delvd') . '</th>
						<th>' . _('Issue Qty') . '</th>
						<th>' . _('Units') . '</th>
						<th>' . _('RFP') . '</th>
						<th width="5%">' . _('Cancel') . '</th>
					</tr>';
					}else{
				echo '<tr>
						<th width="15%">' . _('Item Code') . '</th>
						<th width="50%">' . _('Product') . '</th>
						<th width="15%">' . _('Stock Bal') . '</th>
						<th width="10%">' . _('Qty Req') . '</th>
						<th width="10%">' . _('UOM') . '</th>
					</tr>';
					}
			$query = "SELECT *, locstock.quantity as stkbal, irq_stockrequestitems.quantity as qty FROM irq_stockrequestitems,stockmaster,locstock
							WHERE irq_stockrequestitems.stockid = stockmaster.stockid AND locstock.stockid = stockmaster.stockid AND irq_stockrequestitems.dispatchid='".$LID."' AND locstock.loccode='". $loccode ."' AND irq_stockrequestitems.completed=0";
					$results= DB_query($query);
	if($final == TRUE && $doc==4){
		?>
		<form action="IRQ_InternalStockRequestFulfill.php" method="post" enctype="multipart/form-data" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" target="_top">
		<?php
		}
					while($row=DB_fetch_array($results)){
		
		if($final == TRUE && $doc==4){
		echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
		echo '<input name="DeclineRequestID" type="hidden" value="'.$LID.'" />';
		echo '<input type="hidden" name="LineID[]" value="'.$row['dispatchitemsid'].'" />';
		echo '<input type="hidden" name="RequestID" value="'.$row['dispatchid'].'" />';
		echo '<tr>
					<td>' . $row['longdescription'] . '</td>
					<td><center>' . $row['stkbal'] . '</center></td>
					<td align="center"><center>' . $row['qty']. '</center></td>
					<td align="center"><center>' . $row['qtydelivered']. '</center></td>
					<td align="center">';
			if( $row['stkbal']>0){		
		echo '<input type="text" class="number" id="numberbox_'.$row['dispatchitemsid'].'" style="width:80px" name="'. $row['dispatchid'] . 'Qty'. $row['dispatchitemsid'] . '" value="0" />';
			}else{
		echo '<input type="text" disabled="true" class="number" style="width:80px" name="'. $row['dispatchid'] . 'Qty'. $row['dispatchitemsid'] . '" value="0" />
		<input type="hidden" class="number" style="width:80px" name="'. $row['dispatchid'] . 'Qty'. $row['dispatchitemsid'] . '" value="0" />';
			}
		?>
		<script language="javascript">
		$('#numberbox_<?php echo $row['dispatchitemsid']; ?>').keyup(function(){
		  if ($(this).val() > <?php echo ($row['qty']-$row['qtydelivered']); ?>){
			alert("Quantity Issued is greater than the Requested Amount");
			$(this).val('0');
		  }
		});
		</script>
		<?php
		echo '</td>
					<td><center>' . $row['uom'] . '</center></td>';

			echo '<td align="center">';
			if($row['on_order']==1){
			echo 'On Order';
			}else{
			echo '<center><input type="checkbox" name="StockIT[]" value="'.$row['stockid'].'" /></center>';
			}
			echo '</td>';
			?>
				<td><center><a style="color:#FF0000;" onclick="return confirm('Are you sure you want to Cancel this Item?')" href=<?php echo "index.php?Application=IRQ2&Link=InboxRead&LID=".$LID."&id=".$row['itemid']."&LV=".$level."&Cancel=1 title='Cancel'>"; ?><i class='fa fa-trash'></i></a></center></td>
		<?php
			echo '</tr>';
			echo '<input type="hidden" class="number" name="'. $row['dispatchid'] . 'Tag'. $row['dispatchitemsid'] . '" value="0" />';
			echo '<input type="hidden" class="number" name="dispatch" value="'.$row['dispatchid'].'" />';
			echo '<input type="hidden" class="number" name="'. $row['dispatchid'] . 'StockID'. $row['dispatchitemsid'] . '" value="'.$row['stockid'].'" />';
			echo '<input type="hidden" class="number" name="'. $row['dispatchid'] . 'Location'. $row['dispatchitemsid'] . '" value="'.$loccode.'" />';
			echo '<input type="hidden" class="number" name="'. $row['dispatchid'] . 'RequestedQuantity'. $row['dispatchitemsid'] . '" value="'.($row['qty']-$row['qtydelivered']).'" />';
			echo '<input type="hidden" class="number" name="'. $row['dispatchid'] . 'Department'. $row['dispatchitemsid'] . '" value="'.$dept.'" />';
			echo '<input type="hidden" class="number" name="'. $row['dispatchid'] . 'StockBal'. $row['dispatchitemsid'] . '" value="'.$row['stkbal'].'" />';
		}else{
				echo '<tr>
					<tr><td><center>'.$row['stockid'].'</center></td>
					<td>' . $row['longdescription'] . '</td>
					<td><center>' . $row['stkbal'] . '</center></td>
					<td><center>' . $row['qty']. '</center></td>
					<td><center>' . $row['uom'] . '</center></td>';
			echo '</tr>';
			}
			}
			echo '<tbody></table>';
			if($final == TRUE && $doc==4){
			echo '<div style="width:95%" align="right"><input name="Request" type="submit" value="RFP Request" /></div>';
			}
			?>
			<br /> 
                <p>Kind Regards,<br><?php echo ucwords(strtolower($rows['realname'])); ?></p>
              </div>
			<?php
			if($final == TRUE && $doc==1){
			?>
			<form enctype="multipart/form-data" action="IRQ_SentMail.php" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php
			echo '<textarea required="required" name="comment" placeholder="Write your Comment here..." cols="125" rows="2"></textarea>';
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			echo '<input type="hidden" name="LID" value="' . $LID . '" />';
		echo '<table style="width:100%;">
				<tr><th><strong>Select Request Type</strong></th></tr>
				<tr><td> <label>
					<input type="radio" name="RequestType" value="LPO" /> Create a Purchase Order</label>
				  </td></tr>
				  <tr><td>
				  <label><input type="radio" name="RequestType" value="RFQ" /> Request For Quotation</label>
				  </td></tr></table>
		';
		echo '<br />';
		echo '<input type="hidden" name="PurchaseItems" value="PurchaseItems" />';
		}elseif($final == TRUE && $doc==4){
			echo '<textarea required="required" name="comment" id="comment" placeholder="Write your Comment here..." cols="125" rows="2"></textarea>';
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			echo '<input type="hidden" name="LID" value="' . $LID . '" />';
		echo '<table style="width:100%;">
		<tr><th><strong>Select Fulfil Type</strong></th></tr>
		<tr><td>';
		echo '<label>
		    <input type="radio" name="fulfil" value="Issue" />
		    Issue Stock Request?</label>';
		echo '</td></tr><tr><td>';
		echo ' <label>
		    <input type="radio" name="fulfil" value="Transfer" />
		    Make Location Transfer?</label>';
		echo '</td></tr>';
		echo '</table>';
		echo '<br />';
		}else{
			?>
			<form enctype="multipart/form-data" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php
			echo '<textarea required="required" name="comment" placeholder="Write your Comment here..." style="width:100%" rows="2"></textarea>';
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
		}
			  ?>
			 
            <!-- /.box-footer -->
            <div class="box-footer">
              <div class="pull-right">
			  <?php if($final == TRUE){  ?>
			   <button type="submit" name="CancelRequest" onclick="document.getElementById('comment').value='OK';return confirm('Are you sure you want to Cancel this Request?');" class="btn btn-danger"><i class="fa fa-ban"></i> Cancel</button>
			   <?php } ?>
                <button type="submit" name="Reject" onclick="return confirm('Are you sure you want to Reject this Request?')" class="btn btn-warning"><i class="fa fa-reply"></i> Reject</button>
				<?php if($decision == TRUE && $doc==1){  ?>
				<button type="submit" name="Approved" onclick="return confirm('Are you sure you want to Approve this Request? This action will bypass Managing Director approval and forward straight for procurement!')" title="This action will bypass Managing Director approval and forward straight for procurement" class="btn btn-success"><i class="fa fa-thumbs-o-up"></i> Approve</button>
				 <?php } ?>			
                <button type="submit" name="Forward" onclick="return confirm('Are you sure you want to Forward this Request?')" class="btn btn-default"><i class="fa fa-share"></i> Forward</button>
              </div>
			  <a href="PrintReq_Item_Service.php?<?php echo 'id='.$LID; ?>" target="_blank"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>
            </div>
			</form>
			<br></br>
			<?php
			$comment="SELECT * FROM irq_request a 
							INNER JOIN irq_authorize_state b on a.requestid = b.requisitionid 
							WHERE a.requestid='" . $LID . "' ORDER BY Requesteddate DESC";

$commentresults= DB_query($comment);
	
		echo '<table border="0" style="border:none; width:100%;">';
		while($myrows=DB_fetch_array($commentresults)){
		echo' <tr>
				<td align="center" width="70"><img class="image" src="images/image.jpg"  /></td>
				<td><div class="bubble"><span class="time">From: <a href="#">'.$myrows['approver'].'</a></span> <br /> '.$myrows['approver_comment'].'
				<br /><span class="time">'. calculate_time_span($myrows['approvaldate']) .'</span>
				
				</div>
			</td>
			  </tr>';
			  }
		echo '</table>';
			?>
            <!-- /.box-footer -->
          </div>

<style type="text/css">
<!--
.title {
	font-size: x-large;
	font-family: "Times New Roman", Times, serif;
	font-weight: bold;
	padding-bottom:2px;
}
.bg{
	background-color:#00CCFF;
	font-family:"Times New Roman", Times, serif;
	font-size:16px;
	border-radius:4px 4px 1px 1px;
	padding-bottom:3px;
	padding:2px;
	color:#FFFFFF;
	font-weight:bold;
}
.line{
	border-bottom:inset;
	width:90%;
	border-bottom-color:#00CCFF;
}
.content {
    background-color:white;
    margin:0 auto;
    width:100%;
    /*border-collapse: collapse;*/
    border:thin outset #B3B3B3;
    
    -webkit-border-radius:  4px;
    -moz-border-radius:     4px;
    border-radius:          4px;
    -moz-box-shadow:    1px 1px 2px #C3C3C3;
	-webkit-box-shadow: 1px 1px 2px #C3C3C3;
	box-shadow:         1px 1px 2px #C3C3C3;
	-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')";
	filter: progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')
}


a{
text-decoration:none;
}
.time {color: #999999; font-size:10px;}
.image{
		 border-radius:25px;
		 width:50px;
		 height:50px;
		 padding:20px,20px,20px,20px;
}
/*bubble*/
.bubble
{
position: relative;
width: 90%;

min-height: 10px;
padding-left:18px;
background: #DEFFFF;
-webkit-border-radius:  4px;
    -moz-border-radius:     4px;
    border-radius:          4px;
    -moz-box-shadow:    1px 1px 2px #C3C3C3;
	-webkit-box-shadow: 1px 1px 2px #C3C3C3;
	box-shadow:         1px 1px 2px #C3C3C3;
	-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')";
	filter: progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3');

}

.bubble:after
{
content: '';
position: absolute;
border-style: solid;
border-width: 9px 15px 9px 0;
border-color: transparent #DEFFFF;
display: block;
width: 0;
z-index: 1;
left: -15px;
top: 7px;
}

.link{font-size:9px;}
-->
</style>