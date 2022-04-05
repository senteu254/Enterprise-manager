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
						$results = "SELECT *, z.doc_id, a.departmentid FROM irq_request z 
							INNER JOIN irq_transport a on a.TransportID = z.requestid 
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN www_users y ON z.initiator = y.userid
							WHERE z.requestid='" . $LID . "' GROUP BY requestid";
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
		$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
		$redirect = htmlspecialchars($protocol.''.$_SERVER['HTTP_HOST'].''.$_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8').'?Application=IRQ2&Link=Draft';
if(isset($_POST['Cancel'])){
$insert = "DELETE FROM irq_request WHERE requestid=".$LID."";	
DB_query($insert);
$_SESSION['msg'] = '<ul class="states"><li class="warning">' . _('Requisition No. '). $LID . ' ' . _('has been deleted.'). '</li></ul>';
?>
<script>
        window.location.href = "<?php echo $redirect; ?>";
	</script>
<?php
}
if(isset($_POST['Forward'])){
$insert = "UPDATE irq_request SET draft='0', Requesteddate='" . date('Y-m-d H:i:s') . "' WHERE requestid=".$LID."";
DB_query($insert);
		
		$levelid=1 .$doc;
		$comment=($row['narrative'] =="" ? "Process Initiator" : $row['narrative']);
$HSQL="INSERT INTO irq_authorize_state (requisitionid,
											level,
											approvaldate,
											approver,
											approver_comment)
										VALUES(
											'" . $LID . "',
											'" . $levelid . "',
											'" . date('Y-m-d H:i:s') . "',
											'" . $_SESSION['UsersRealName']. "',
											'". $comment ."')";
	$insert=DB_query($HSQL);
	/******************************************************************************************/
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
		
		include ('includes/htmlMimeMail.php');
		$mail = new htmlMimeMail();
		$mail->setText(_('Dear '.$myEmail['realname'].', Requisition Number '.$LID.' has been created and is waiting for your authoritation. Please Login to the System for details.'));
		$mail->SetSubject('REQUISITION NEEDS YOUR AUTHORITATION');

		if($_SESSION['SmtpSetting'] == 0){
			$mail->setFrom($_SESSION['CompanyRecord']['coyname'] . ' <' . $_SESSION['CompanyRecord']['email'] . '>');
			$result = $mail->send(array($myEmail['email']));
		}else{
			$result = SendmailBySmtp($mail,array($myEmail['email']));
		}

		 $_SESSION['msg'] = '' . _('Success: Requisition No. '). $LID . ' ' . _('has been forwarded to'). ' ' . $myEmail['realname'] . ' ' . _('and emailed to') . ' ' . $myEmail['email']. '';
			
		}else{
			$_SESSION['msg'] = '' . _('Success: Requisition No. '). $LID . ' ' . _('has been forwarded for authoritation'). '';
			}
?>
		<script>
        window.location.href = "<?php echo $redirect; ?>";
		</script>
		<?php
/*---------------------------------------------------------------------------------------*/
	
}//end of post submit
				
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
				
				<?php	
		echo '<br />';			
		echo '<table class="table" style="width:100%; ">';
		echo '<tr>
		<td >Request No</td>
		<td colspan="3"><strong>'.$rows['requestid'].'</strong></td>
		</tr><tr>
		<td>Required From</td>
		<td>'.date("d, M Y",strtotime($rows['required_from'])).'</td>
		<td>Required Time</td><td>'. date("h:i A",strtotime($rows['required_from'])) .'</td>
		</tr><tr>
		<td>Required To</td>
		<td>'.date("d, M Y",strtotime($rows['required_to'])).'</td>
		<td>Required Time</td><td>'. date("h:i A",strtotime($rows['required_to'])) .'</td>
		</tr><tr>
		<td>Transport Type</td>
		<td>'.$rows['transport_type'].'</td>
		<td>Destination</td>
		<td>'.$rows['destination'].'</td>
		</tr><tr>
		<td>Nature of Duty</td>
		<td colspan="3">'.$rows['nature_of_duty'].'</td>
		</tr><tr>
		<td>Requesting Officer</td>
		<td>'.$rows['requesting_officer'].'</td>
		<td>Department</td><td>'.$rows['description'].'</td>
		</tr><tr>
		<td>Vehicle Available</td>
		<td colspan="3">'.$rows['vregno'].'</td>
		</tr>';
		echo '</table>';
			?>
			<br />
                <p>Kind Regards,<br><?php echo ucwords(strtolower($rows['realname'])); ?></p>
              </div>
			 <form enctype="multipart/form-data" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
            <!-- /.box-footer -->
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
            <div class="box-footer">
			<div class="pull-right">
			   <button type="submit" name="Cancel" onclick="return confirm('Are you sure you want to Delete this Request?')" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</button>
                <button type="submit" name="Forward" onclick="return confirm('Are you sure you want to Forward this Request?')" class="btn btn-default"><i class="fa fa-share"></i> Forward</button>
              </div>
			  <a href="#" target="_blank"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>
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