<link rel="stylesheet" type="text/css" href="datepickr.css" />
			
	 <link rel="stylesheet" href="js/smoothness/jquery-ui.css" />
    
    <!-- Load jQuery JS -->
    <script src="js/jquery-1.9.1.js"></script>
    <!-- Load jQuery UI Main JS  -->
    <script src="js/jquery-ui.js"></script>
    
    <!-- Load SCRIPT.JS which will create datepicker for input field  -->
    <script src="script.js"></script>
    
    <link rel="stylesheet" href="runnable.css" />
<?php

/* $Id: MaintenanceTasks.php 5231 2012-04-07 18:10:09Z daitnree $*/

include('includes/session.inc');

$Title = _('Preventive Maintenance Plan');

$ViewTopic = 'Preventive Maintenance Plan';
$BookMark = 'Preventive Maintenance Plan';

include('includes/header.inc');

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/group_add.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p><br />';


if (isset($_POST['Submit'])) {

	$dat=strtotime($_POST['Date']);
	$date=date('Y/m/d',$dat);
	
		$sql="INSERT INTO maintenance_plan (assetid,
											activity,
											maintenance_date,
											user)
						VALUES( '" . $_POST['AssetID'] . "',
								'" . $_POST['Activity'] . "',
								'" . $date . "' ,
								'" . $_POST['user'] . "') ";
		$ErrMsg = _('The maintenance plan details cannot be inserted because');
		$Result=DB_query($sql,$ErrMsg);
		
		unset($_POST['AssetID']);
		unset($_POST['Activity']);
		unset($_POST['Date']);
		unset($_POST['user']);
	}

if (isset($_POST['Update'])) {
$dat=strtotime($_POST['Date']);
	$date=date('Y/m/d',$dat);
		$sql="UPDATE maintenance_plan SET
				assetid = '" . $_POST['AssetID'] . "',
				activity='".$_POST['Activity'] ."',
				user='".$_POST['user'] ."',
				maintenance_date='".$date ."'
				WHERE planid='".$_POST['PlanID']."'";

		$ErrMsg = _('The task details cannot be updated because');
		$Result=DB_query($sql,$ErrMsg);
		unset($_POST['AssetID']);
		unset($_POST['Activity']);
		unset($_POST['Date']);
		unset($_POST['user']);
	}


if (isset($_GET['Delete'])) {
	$sql="DELETE FROM maintenance_plan
		WHERE planid='".$_GET['PlanID']."'";

	$ErrMsg = _('The maintenance plan cannot be deleted because');
	$Result=DB_query($sql,$ErrMsg);
}
$sql="SELECT * FROM fixedassets,maintenance_plan,www_users
		WHERE maintenance_plan.assetid=fixedassets.assetid
		AND www_users.userid=maintenance_plan.user";

$ErrMsg = _('The maintenance plan details cannot be retrieved because');
$Result=DB_query($sql,$ErrMsg);

echo '<table class="selection">
     <tr>
		<th>' . _('Plan ID') . '</th>
		<th>' . _('Asset') . '</th>
		<th>' . _('Activity') . '</th>
		<th>' . _('Maintenance Date') . '</th>
		<th>' . _('User') . '</th>
			<th>' . _('Edit') . '</th>
			<th>' . _('Delete') . '</th>
		<th>' . _('Requisition') . '</th>
		
    </tr>';

while ($myrow=DB_fetch_array($Result)) {

	echo '<tr>
			<td>' . $myrow['planid'] . '</td>
			<td>' . $myrow['description'] . '</td>
			<td>' . $myrow['activity'] . '</td>
			<td>' . $myrow['maintenance_date'] . '</td>
			<td>' . $myrow['user'] . '</td>
			<td><a href="'.$RootPath.'/CreatePreventiveMaintenancePlan.php?Edit=Yes&amp;PlanID=' . $myrow['planid'] .'">' . _('Edit') . '</a></td>
			<td><a href="'.$RootPath.'/CreatePreventiveMaintenancePlan?Delete=Yes&amp;PlanID=' . $myrow['planid'] .'" onclick="return confirm(\'' . _('Are you sure you wish to delete this maintenance task?') . '\');">' . _('Delete') . '</a></td>
			<td><a href="'.$RootPath.'/IRQ_MaintenanceRequest.php?New=Document&PlanID=' . $myrow['planid'] .'" >' . _('Create') . '</a></td>
		</tr>';

}
echo '</table><br /><br />';


echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post" id="form1">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<table class="selection">';

if (isset($_GET['Edit'])) {
	echo '<tr>
			<td>' . _('Plan ID') . '</td>
                        <td>' . $_GET['PlanID'] . '</td>
		</tr>';
	echo '<input type="hidden" name="PlanID" value="'.$_GET['PlanID'].'" />';
	$sql="SELECT planid,
	fixedassets.assetid,
	            description,
				activity,
				user,
				maintenance_date
		FROM  fixedassets,maintenance_plan
		WHERE maintenance_plan.assetid=fixedassets.assetid 
		AND planid='".$_GET['PlanID']."'";
	$ErrMsg = _('The maintenance plan details cannot be retrieved because');
	$result=DB_query($sql,$ErrMsg);
	$myrow=DB_fetch_array($result);
	$_POST['Activity'] = $myrow['activity'];
	$_POST['AssetID'] = $myrow['assetid'];
	$_POST['Date'] = $myrow['maintenance_date'];
	$_POST['user'] = $myrow['user'];
}
if (!isset($_POST['Activity'])){
	$_POST['Activity']='';
}
if (!isset($_POST['Date'])){
	$_POST['Date']='';
}
if (!isset($_POST['AssetID'])){
	$_POST['AssetID']='';
}
if (!isset($_POST['user'])){
	$_POST['user']='';
}


echo '<tr>
		<td>' . _('Asset.').':</td>
		<td><select required="required" name="AssetID" id="AssetID">';
$SQL="SELECT * FROM  fixedassets";
$Result=DB_query($SQL);

while ($myrow=DB_fetch_array($Result)) {
	if ($myrow['assetid']==$_POST['AssetID']) {
		echo '<option selected="selected" value="'.$myrow['assetid'].'">' . $myrow['assetid'] . ' - ' . $myrow['description']  . '</option>';
	} else {
		echo '<option value="'.$myrow['assetid'].'">' . $myrow['assetid'] . ' - ' . $myrow['description']  . '</option>';
	}
}		echo'</select></td>
		</tr>';
			

echo '<tr>
		<td>' . _('Activity').':</td>
		<td><textarea name="Activity" required="required" cols="40" rows="3">' . $_POST['Activity'] . '</textarea></td>
	</tr>';

echo '<tr>
		<td>' . _('Maintenance Date').':</td>
		<td><input type="text" class="integer" required="required" name="Date" id="date"  value="' . $_POST['Date'] . '" /></td>
	</tr>';


echo '<tr>
		<td>' . _('Planning Officer') . ':</td>
		<td><select required="required" name="user">';
$UserSQL="SELECT userid FROM www_users";
$UserResult=DB_query($UserSQL);
while ($myrow=DB_fetch_array($UserResult)) {
	if ($myrow['userid']==$_POST['user']) {
		echo '<option selected="selected" value="'.$myrow['userid'].'">' . $myrow['userid'] . '</option>';
	} else {
		echo '<option value="'.$myrow['userid'].'">' . $myrow['userid'] . '</option>';
	}
}
echo '</select></td>
	</tr>';
	echo'</table>';


if (isset($_GET['Edit'])) {
	echo '<br />
			<div class="centre">
				<input type="submit" name="Update" value="'._('Update Task').'" />
			</div>';
} else {
	echo '<br />
		<div class="centre">
			<input type="submit" name="Submit" value="'._('Enter New Plan').'" />
		</div>';

}
echo '</div>
        </form>';
		
include('includes/footer.inc');

?>
<?php
include('includes/htmlMimeMail.php');

$sql="SELECT description,
				activity,
				maintenance_date,
				user,
				email
		FROM maintenance_plan
		INNER JOIN fixedassets
		ON maintenance_plan.assetid=fixedassets.assetid
		INNER JOIN www_users
		ON maintenance_plan.user=www_users.userid
		";

$res = DB_query($sql);

while ($row = DB_fetch_array($res)){
$maintenance=$row['maintenance_date'];
}
$maintenancedat=strtotime($maintenance);
$maintenancedate=date('Y/m/d',$maintenancedat);

$now=strtotime('now');
$nowdate=date('Y/m/d',$now);

$diffdate=$nowdate-$maintenancedate;


$sql="SELECT 	description,
				activity,
				maintenance_date,
				user,
				email
		FROM maintenance_plan
		INNER JOIN fixedassets
		ON maintenance_plan.assetid=fixedassets.assetid
		INNER JOIN www_users
		ON maintenance_plan.user=www_users.userid";

$res = DB_query($sql);
$myrow=DB_fetch_array($res);
$email=$myrow['email'];
if($diffdate == 7){
while($myrow=DB_fetch_array($res)){
$EmailText= _('This email has been automatically generated by webERP') . "\n";
		$EmailText.= _('You are notified that the following asset is supposed to be maintained in less than a week  ') . ' ' . $_SESSION['CompanyRecord']['coyname'] . "\n";
		$EmailText.= _('Asset : '.$myrow['description'].'') . "\n";
		$EmailText.= _('Activity : '. $myrow['activity'] .'') . "\n";
		$EmailText.= _('Maintenance Date : '. $myrow['maintenance_date'] .'') . "\n";
		include ('includes/htmlMimeMail.php');
		$mail = new htmlMimeMail();
		$mail->setText($EmailText);
		$mail->SetSubject(_('MAINTENANCE REMINDER '));
		}
		if($_SESSION['SmtpSetting'] == 0){
			$mail->setFrom($_SESSION['CompanyRecord']['coyname'] . ' <' . $_SESSION['CompanyRecord']['email'] . '>');
			$result = $mail->send(array($email));
		}else{
			$result = SendmailBySmtp($mail,array($email));
		}
}


	

?>
