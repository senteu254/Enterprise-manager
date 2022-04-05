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
							INNER JOIN irq_maintenance a on a.maintenanceid = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN www_users y ON z.initiator = y.userid
							WHERE draft=0 AND closed=0 AND b.Sent=0 AND
							z.requestid='" . $LID . "' AND level='".$level."' AND
							CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' 
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
			$insert = "UPDATE irq_authorize_state SET Unread='1'
					WHERE requisitionid=".$LID." AND level=".$rows['level']."";
					DB_query($insert);
						
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
if($final==TRUE){
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
		$insert = "UPDATE irq_request SET closed='1' WHERE requestid=".$LID."";	
		DB_query($insert);
		$_SESSION['msg'] = _('Maintenance Requisition No. '). $LID . ' ' . _('has been appoved');
		}else{
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
		} //if final end
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
		if($doc==5){
		$message='Problem Observed';
		$date ='Breakdown Date';
		}else{
		$message='Service Due';
		$date ='Service Date';
		}
		echo '<br />';
		echo '<table class="table" style="width:100%; ">';
		echo '<tr>
		<td>Request No.</td>
		<td colspan="3"><strong>'.$rows['requestid'].'</strong></td>
		</tr><tr>
		<td>Department</td>
		<td colspan="3">'.strtoupper($rows['description']).'</td>		
		</tr><tr>
		<td>Section</td>
		<td align="left">'.$rows['section'].'</td>
		<td >'.$date.'</td>
		<td>'. date("d, M Y h:i A",strtotime($rows['breakdowndate'])) .'</td>
		</tr><tr>
		<td>M/C Type</td>
		<td>'.$rows['mctype'].'</td>
		<td>M/C No.</td>
		<td>'.$rows['mcno'].'</td>
		</tr><tr>
		<td>Requesting Officer</td>
		<td>'.$rows['requesting_officer'].'</td>
		<td>Urgency</td>
		<td>'.$rows['urgency'].'</td>
		</tr><tr>
		<td>'.$message.'</td>
		<td colspan="3"><textarea name="" disabled="true" style="width:100%;" rows="2">'.$rows['problem'].'</textarea></td>
		</tr>';
		echo '</table>';
			?>
			<br />
                <p>Kind Regards,<br><?php echo ucwords(strtolower($rows['realname'])); ?></p>
              </div>
			<form enctype="multipart/form-data" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php
			echo '<textarea required="required" name="comment" placeholder="Write your Comment here..." cols="125" rows="2"></textarea>';
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			  ?>
            <!-- /.box-footer -->
            <div class="box-footer">
              <div class="pull-right">
                <button type="submit" name="Reject" onclick="return confirm('Are you sure you want to Reject this Request?')" class="btn btn-warning"><i class="fa fa-reply"></i> Reject</button>			
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