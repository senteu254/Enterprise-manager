<?php 
if($_POST['Submit']){

if($_POST['document'] ==''){
$error = '<br />Document Type can not be blank';
}
if(empty($_POST['Department'])){
$error .= '<br />Department can not be blank';
}
if(empty($_POST['section'])){
$error .= '<br />Section can not be blank';
}
if(empty($_POST['mctype'])){
$error .= '<br />Machine Type can not be blank';
}
if(empty($_POST['mcno'])){
$error .= '<br />Machine Number can not be blank';
}
if(empty($_POST['problem'])){
$error .= '<br />Problem Observed can not be blank';
}
if(empty($_POST['date'])){
$error .= '<br />Date and Time can not be blank';
}
$from = FormatDateForSQL($_POST['date']).' '.$_POST['time'];
$now = FormatDateForSQL(date($_SESSION['DefaultDateFormat'])).' '.date('H:i');
if($_POST['document'] ==6 && $from < $now){
$error .= '<br />Service Date Can not be Earlier than Now';
}
if(isset($error)){
echo prnMsg ( _($error), 'warn' );
}else{
if(isset($_POST['draft']) && $_POST['draft']==1){
$draft=1;
}else{
$draft=0;
}
$from = FormatDateForSQL($_POST['date']).' '.$_POST['time'];
$RequestNo = GetNextTransNo(51, $db);
		/* $q='SELECT MAX(requestid) as id FROM irq_request';
			$Result = DB_query($q);
			$num= DB_num_rows($Result);
			if($num >0){
			$myrow = DB_fetch_array($Result);
			$RequestNo = $myrow['id']+1;
			}else{
			$RequestNo =1;
			} */
	$SQL="INSERT INTO irq_request (requestid,
									doc_id,
									draft,
									closed,
									initiator,
									Requesteddate) 
							VALUES('". $RequestNo ."',
									'". $_POST['document'] ."',
									'". $draft ."',
									'0',
									'". $_SESSION['UserID'] ."',
									'". date('Y-m-d H:i:s') ."')";
					$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		
			$SQL="INSERT INTO irq_maintenance (maintenanceid,
									departmentid,
									section,
									mctype,
									mcno,
									problem,
									breakdowndate,
									urgency,
									requesting_officer) 
							VALUES('". $RequestNo ."',
									'". $_POST['Department'] ."',
									'". $_POST['section'] ."',
									'". $_POST['mctype'] ."',
									'". $_POST['mcno'] ."',
									'". $_POST['problem'] ."',
									'". $from ."',
									'". $_POST['urgency'] ."',
									'". addslashes($_SESSION['UsersRealName']) ."')";
					$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);	
		
		if($draft==0){
				$levelid=1 .$_POST['document'];
			$HSQL="INSERT INTO irq_authorize_state (requisitionid,
														level,
														approvaldate,
														approver,
														approver_comment)
													VALUES(
														'" . $RequestNo . "',
														'" . $levelid . "',
														'" . date('Y-m-d H:i:s') . "',
														'" . addslashes($_SESSION['UsersRealName']). "',
														'Process initiator')";
		$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($HSQL,$ErrMsg,$DbgMsg,true);
		
		/******************************************************************************************/
		$EmailSQL="SELECT www_users.email, www_users.realname
					FROM www_users, irq_approvers, irq_levels, departments, locations
					WHERE irq_approvers.approver_id = irq_levels.approver_id AND 
						irq_levels.level_id = '" . $levelid ."' AND
						irq_levels.doc_id = '" . $_POST['document'] ."' AND
						CASE WHEN irq_approvers.userid ='HOD' THEN departments.authoriser = www_users.userid and departments.departmentid ='". $_POST['Department'] ."' WHEN irq_approvers.userid ='ISSUE' THEN locations.authoriser=www_users.userid ELSE irq_approvers.userid = www_users.userid END
						LIMIT 1";
		$EmailResult =DB_query($EmailSQL);
		$nums = DB_num_rows($EmailResult);
		if ($nums>0){
		$myEmail=DB_fetch_array($EmailResult);
		 	//header('location:IRQ_MaintenanceRequest.php?id='. $RequestNo .'&Email='. $myEmail['email'] .'&User='. $myEmail['realname'] .'&Ref=Create-Request&New=Yes');
			
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
		 echo _('<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success</h4>Success: Requisition No. '). $RequestNo . ' ' . _('has been forwarded to'). ' ' . $myEmail['realname'] . ' ' . _('and emailed to') . ' ' . $myEmail['email'].'</div>';
		
		}else{
			echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success</h4>Maintenance Request No. '.$RequestNo.' has been created and forwarded for authoritation</div>';
			}
		/*--------------------------------------------------------------------------------------------------*/
		}else{
		echo _('<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success</h4>Maintenance Request No. '.$RequestNo.' has been saved as a Draft.</div>');
		}
}
}
		
			
?>
<span class="pull-right">
<?php  
$doc =5;
			
		$titles="SELECT approver_name FROM irq_levels a
	 			INNER JOIN irq_approvers b ON a.approver_id=b.approver_id
				WHERE a.doc_id=".$doc."
				GROUP BY a.level_id ORDER BY a.level_id ASC";
$first=1;
$tit = array();
$titleresults= DB_query($titles);
$tit[] ='USER';
while($title=DB_fetch_array($titleresults)){
$tit[] = $title['approver_name'];
}

for($i=0; $i < count($tit); $i++) {
$color = 'color:#CCCCCC;';
$check ='<i class="fa fa-circle-o fa-2x" style="color:#CCCCCC;" aria-hidden="true"></i>';
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
<style>
.required {color: #FF0000;}
</style>
<script src="js/jquery-1.9.1.js"></script>
<div align="center" style="width:100%">
<form action="" method="post" enctype="multipart/form-data" target="_parent">
<?php 
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<input type="hidden" name="document" value="5" />';
 ?>
<table class="table">
  <tr>
    <th colspan="2"><center><h4>Breakdown Maintenance Service Requisition</h4></center></th>
  </tr>
  <tr>
  <td>Department <span class="required">*</span></td>
  <?php
						
	// any internal department allowed
	if($_SESSION['AllowedDepartment'] == 0){
	// any internal department allowed
	$sql="SELECT departmentid,
				description
			FROM departments
			ORDER BY description";
}else{
	// just 1 internal department allowed
	$sql="SELECT departmentid,
				description
			FROM departments
			WHERE departmentid = '". $_SESSION['AllowedDepartment'] ."'
			ORDER BY description";
}
$result=DB_query($sql);
echo '<td><select style="width:380px;" name="Department">';
echo '<option selected="selected" value="">--Please Select Requesting Department--</option>';
while ($myrow=DB_fetch_array($result)){
	if (isset($_SESSION['Request']->Department) AND $_SESSION['Request']->Department==$myrow['departmentid']){
		echo '<option selected="True" value="' . $myrow['departmentid'] . '">' . htmlspecialchars($myrow['description'], ENT_QUOTES,'UTF-8') . '</option>';
	} else {
		echo '<option value="' . $myrow['departmentid'] . '">' . htmlspecialchars($myrow['description'], ENT_QUOTES,'UTF-8') . '</option>';
	}
}
echo '</select>';
echo '</td>';
?>
  </tr>
  <tr>
    <td width="170px">Section</td>
	<?php
	$sql="SELECT id_sec,
				section_name
			FROM section";
$result=DB_query($sql);
echo '<td><select style="width:380px;" name="section">';
echo '<option selected="selected" value="">-- Please Select Requesting Section --</option>';
while ($myrow=DB_fetch_array($result)){
	if (isset($_POST['section']) AND $_POST['section']==$myrow['section_name']){
		echo '<option selected="True" value="' . $myrow['section_name'] . '">' . htmlspecialchars($myrow['section_name'], ENT_QUOTES,'UTF-8') . '</option>';
	} else {
		echo '<option value="' . $myrow['section_name'] . '">' . htmlspecialchars($myrow['section_name'], ENT_QUOTES,'UTF-8') . '</option>';
	}
}
echo '</select>';
echo '</td>';
?>
  </tr>
   <tr><td>Machine Type <span class="required">*</span></td>
	<td><input  style="width:380px;" name="mctype" type="text"></td>
   <tr><td> Machine No. <span class="required">*</span></th>
	  <?php

	$sql="SELECT mcno,
				description
			FROM fixedassets";
$result=DB_query($sql);
echo '<td><select style="width:380px;" name="mcno">';
echo '<option selected="selected" value="">-- Please Select Machine to be Mainteined --</option>';
while ($myrow=DB_fetch_array($result)){
	if (isset($_POST['mcno']) AND $_POST['mcno']==$myrow['mcno']){
		echo '<option selected="True" value="' . $myrow['mcno'] . '">' . htmlspecialchars($myrow['mcno'].'-'.$myrow['description'], ENT_QUOTES,'UTF-8') . '</option>';
	} else {
		echo '<option value="' . $myrow['mcno'] . '">' . htmlspecialchars($myrow['mcno'].'-'.$myrow['description'], ENT_QUOTES,'UTF-8') . '</option>';
	}
}
echo '</select>';
echo '</td></tr>';
?>
	
  <tr>
    <td>Problem Observed</td>
	<td><textarea name="problem" style="width:380px;" rows="2"></textarea></td>
  </tr>
  <tr>
    <td>Breakdown Date &amp; Time <span class="required">*</span></td>
	<td>
	<?php 
	echo '<input type="text" class="date" alt="'.$_SESSION['DefaultDateFormat'].'" name="date" maxlength="10" value="'.date($_SESSION['DefaultDateFormat']).'" />';
	 echo '<select name="time">';
	 for($hours=0; $hours<24; $hours++) // the interval for hours is '1'
    for($mins=0; $mins<60; $mins+=15) // the interval for mins is '30'
        echo '<option>'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
                       .str_pad($mins,2,'0',STR_PAD_LEFT).'</option>';
	echo '</select>';
	?>
	</td>
  </tr>
   <tr>
	<td>Urgency</td>
	<td>
	      <strong><input type="radio" name="urgency" value="Low" />
	      Low
	      <input type="radio" name="urgency" value="Normal" />
	      Normal
	      <input type="radio" name="urgency" value="High" />
	      High</strong></td>
  </tr>
     <tr>
	 <td>	 </td>
	<td><input name="Submit" type="submit" value="Submit" /> <strong><input name="draft" type="checkbox" value="1" />Save as a Draft</strong></td>
  </tr>
</table>
</form>
</div>

<script>
$( "input" ).on( "click", function() {
if($( "input:checked" ).val() ==5){
  $( "#date" ).html( " Breakdown Date &amp; Time" );
   $( "#message" ).html( " Problem Observed" );
  }else{
  $( "#date" ).html( " Service Date &amp; Time" );
   $( "#message" ).html( " Job Description" );
  }
});
</script>
