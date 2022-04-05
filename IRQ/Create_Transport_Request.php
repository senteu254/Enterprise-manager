<?php 
if(!isset($_SESSION['UserID']) && !isset($_SESSION['AccessLevel']) && !isset($_SESSION['UsersRealName']) && !isset($_SESSION['DatabaseName'])){
header('location:../index.php');
}
require_once('includes/session.inc');
include('includes/DefineStockRequestClass.php');
include('includes/SQL_CommonFunctions.inc');
echo '<link href="' . $RootPath . '/css/'. $_SESSION['Theme'] .'/default.css" rel="stylesheet" type="text/css" />';

if($_POST['Submit']){

if($_POST['document'] ==''){
$error = '<br />Document Type cannt be blank';
}
if(empty($_POST['Department'])){
$error .= '<br />Department cannt be blank';
}
if(empty($_POST['type'])){
$error .= '<br />Transport Type cannt be blank';
}
if(empty($_POST['destination'])){
$error .= '<br />Destination cannt be blank';
}
if(empty($_POST['nature'])){
$error .= '<br />Nature of Duty cannt be blank';
}
if(empty($_POST['from'])){
$error .= '<br />From Date and time cannt be blank';
}
if(empty($_POST['to'])){
$error .= '<br />To Date and Time cannt be blank';
}
$from = FormatDateForSQL($_POST['from']).' '.$_POST['timefrom'];
$to = FormatDateForSQL($_POST['to']).' '.$_POST['timeto'];
$now = FormatDateForSQL(date($_SESSION['DefaultDateFormat'])).' '.date('H:i');
if($from < $now){
$error .= '<br />From Date can not be Earlier than Now';
}
if($from >$to){
$error .= '<br />From Date can not be higher than To Date';
}

if(isset($error)){
echo prnMsg ( _($error), 'warn' );
}else{
if(isset($_POST['draft']) && $_POST['draft']==1){
$draft=1;
}else{
$draft=0;
}
$from = FormatDateForSQL($_POST['from']).' '.$_POST['timefrom'];
$to = FormatDateForSQL($_POST['to']).' '.$_POST['timeto'];
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
		
			$SQL="INSERT INTO irq_transport (TransportID,
									departmentid,
									transport_type,
									nature_of_duty,
									destination,
									required_from,
									required_to,
									requesting_officer) 
							VALUES('". $RequestNo ."',
									'". $_POST['Department'] ."',
									'". $_POST['type'] ."',
									'". $_POST['nature'] ."',
									'". $_POST['destination'] ."',
									'". $from ."',
									'". $to ."',
									'". $_SESSION['UserID'] ."')";
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
														'" . $_SESSION['UsersRealName']. "',
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
		 	//header('location:IRQ_PDFTransportPortrait.php?id='. $RequestNo .'&Email='. $myEmail['email'] .'&User='. $myEmail['realname'] .'&Ref=Create-Request&New=Yes');
			
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
		echo '<br />';
		 prnMsg( _('Success: Requisition No. '). $RequestNo . ' ' . _('has been forwarded to'). ' ' . $myEmail['realname'] . ' ' . _('and emailed to') . ' ' . $myEmail['email'], 'success');
		 echo '<br />';
		
		}else{
			echo '<br />';
			prnMsg('Transport Request No. '.$RequestNo.' has been created and forwarded for authoritation', 'success');
			echo '<br />';
			}
		/*--------------------------------------------------------------------------------------------------*/
		
		}else{
		echo '<br />';
		prnMsg( _('Transport Request No. '.$RequestNo.' has been saved as a Draft.'), 'success');
		echo '<br />';
		}
}
}
		
			
?>

<div align="center" style="width:40%">
<form action="" method="post" enctype="multipart/form-data" target="_parent">
<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
<table>
  <tr>
    <th colspan="4"><h4>Transport Requisition Proformae</h4></th>
  </tr>
  <tr>
    <th colspan="4">
	<div align="center">
	<div style="width:80%;">
	  <table>
	    <tr>
		<td>Document</td>
	      <td><label>
	        <input type="radio" name="document" value="3" />
	        Within Eldoret</label></td>
	      </tr>
	    <tr>
		<td></td>
	      <td><label>
	        <input type="radio" name="document" value="2" />
	        Outside Eldoret</label></td>
	      </tr>
		  <tr>
		  <td>Department</td>
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
echo '<td><select name="Department">';
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
	    </table>
		</div>
		</div>
		</th>
  </tr>
  <tr>
    <th width="30%">Type of Transport</th>
	<td><input style="width:260px;" name="type" type="text"></td>
  </tr>
  <tr>
    <th>Nature Of Duty</th>
	<td><input  style="width:260px;" name="nature" type="text"></td>
  </tr>
  <tr>
    <th>Destination</th>
	<td><input  style="width:260px;" name="destination" type="text"></td>
  </tr>
  <tr>
    <th>Required From</th>
	<td><?php echo '<input type="text" class="date" alt="'.$_SESSION['DefaultDateFormat'].'" name="from" maxlength="10" size="11" value="" />'; ?>
	    <?php
	 echo '<select name="timefrom">';
	 for($hours=0; $hours<24; $hours++) // the interval for hours is '1'
    for($mins=0; $mins<60; $mins+=30) // the interval for mins is '30'
        echo '<option>'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
                       .str_pad($mins,2,'0',STR_PAD_LEFT).'</option>';
	echo '</select>';
					   ?>
	   </td>
  </tr>
   <tr>
	<th>Required To</th>
	<td><?php echo '<input type="text" class="date" alt="'.$_SESSION['DefaultDateFormat'].'" name="to" maxlength="10" size="11" value="" />'; ?>
		 <?php
	 echo '<select name="timeto">';
	 for($hours=0; $hours<24; $hours++) // the interval for hours is '1'
    for($mins=0; $mins<60; $mins+=30) // the interval for mins is '30'
        echo '<option>'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
                       .str_pad($mins,2,'0',STR_PAD_LEFT).'</option>';
	echo '</select>';
					   ?>
	  </td>
  </tr>
     <tr>
	 <td>
	 </td>
	<td><input name="Submit" type="submit" value="Submit" /> <strong><input name="draft" type="checkbox" value="1" />
	Save as a Draft</strong></td>
  </tr>
</table>
</form>
</div>
