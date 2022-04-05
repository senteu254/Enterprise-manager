<?php

/* $Id: SecurityTokens.php 4424 2010-12-22 16:27:45Z tim_schofield $*/

include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$Title = _('Non-Conforming Products');

include('includes/header.inc');
echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/maintenance.png" title="' .
		_('Print') . '" alt="" />' . ' ' . $Title . '</p>';
		
function calculate_time_span($date){
    $seconds  = strtotime(date('Y-m-d H:i:s')) - strtotime($date);

        $months = floor($seconds / (3600*24*30));
        $day = floor($seconds / (3600*24));
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds - ($hours*3600)) / 60);
        $secs = floor($seconds % 60);

        if($seconds < 60)
            $time = $secs." seconds ago";
        else if($seconds < 60*60 )
            $time = $mins." min ago";
        else if($seconds < 24*60*60)
            $time = $hours." hours ago";
        else if($seconds < 24*60*60)
            $time = $day." day ago";
        else
            //$time = $months." month ago";
			$time = date("d, M Y",strtotime($date)).' '. date("h:i:s A",strtotime($date));

        return $time;
}
		
############################################################################################

if (isset($_GET['SelectedUser'])){
	$SelectedUser = $_GET['SelectedUser'];
} elseif (isset($_POST['SelectedUser'])){
	$SelectedUser = $_POST['SelectedUser'];
}
if (isset($_POST['Submit'])) {
$InputError = 0;

if (isset($SelectedUser)) {
$sql = "UPDATE qanonconformingproducts SET machine='" . $_POST['machine'] . "',
						calibre='" . $_POST['calibre'] ."',
						lot='" . $_POST['lot'] ."',
						mc_setter='" . $_POST['setter'] ."',
						date='" . FormatDateForSQL($_POST['date']) ."'
					WHERE id = '". $SelectedUser . "'";
					
$sql2 = "UPDATE qanonconformingremarks SET remarks='" . $_POST['remarks'] . "',
						approvername='" . $_SESSION['UsersRealName'] ."'
					WHERE refid = '". $SelectedUser . "'";
					
	prnMsg( _('The selected record has been updated successfully'), 'success' );
		
	} elseif ($InputError !=1) {
	//initialise no input errors assumed initially before we test
		$RequestNo = GetNextTransNo(80, $db);
		$sql = "INSERT INTO qanonconformingproducts (`id`, 
										`machine`,
										`calibre`, 
										`lot`, 
										`mc_setter`,
										`date`)
					VALUES (" . $RequestNo . ",
						'" . $_POST['machine'] ."',
						'" . $_POST['calibre'] ."',
						'" . $_POST['lot'] ."',
						'" . $_POST['setter'] ."',
						'" . FormatDateForSQL($_POST['date']) ."')";
		$sql2 = "INSERT INTO qanonconformingremarks (`refid`, `remarks`,approver, approvertitle, `approvername`)
					VALUES (" . $RequestNo . ",
						'" . $_POST['remarks'] ."',
						1,
						'QAT OBSERVATION(S)/DETAILS OF NON-CONFORMANCE',
						'" . $_SESSION['UsersRealName'] ."')";
		prnMsg( _('A new record has been inserted Successfully'), 'success' );

	}

	if ($InputError!=1){
		//run the SQL from either of the above possibilites
		$ErrMsg = _('The user alterations could not be processed because');
		$DbgMsg = _('The SQL that was used to update the user and failed was');
		$result = DB_query($sql,$ErrMsg,$DbgMsg);
		$result = DB_query($sql2,$ErrMsg,$DbgMsg);


		unset($_POST['machine']);
		unset($_POST['calibre']);
		unset($_POST['lot']);
		unset($_POST['date']);
		unset($_POST['remarks']);
		unset($_POST['setter']);
		unset($SelectedUser);
	}
	
}elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button		

			$sql="DELETE FROM qanonconformingproducts WHERE id='" . $SelectedUser . "'";
			$ErrMsg = _('The Record could not be deleted because');
			$result = DB_query($sql,$ErrMsg);
			$sql2="DELETE FROM qanonconformingremarks WHERE refid='" . $SelectedUser . "'";
			$result = DB_query($sql2,$ErrMsg);
			prnMsg(_('Record Deleted Successfully'),'info');

		unset($SelectedUser);
	}
	if (isset($_GET['process'])) {
$sql = "UPDATE qanonconformingproducts SET process_level='" . 1 . "'
					WHERE id = '". $SelectedUser . "'";

		$result = DB_query($sql);
		prnMsg( _('The selected Record has been forwaded for processing'), 'info' );
		
	unset($SelectedUser);
	}
######################################################################################
if (isset($_GET['view'])) {

	$sql = "SELECT *
		FROM qanonconformingproducts
		WHERE id='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['id'] = $myrow['id'];
	$_POST['machine'] = $myrow['machine'];
	$_POST['date'] = ConvertSQLDate($myrow['date']);
	$_POST['lot'] = $myrow['lot'];
	$_POST['calibre']	= $myrow['calibre'];
	
echo '<a href="' . $RootPath . '/QACreateNon_ConformanceForm.php">' . _('Back to Main Menu') . '</a>';

echo '<table class="selection">
      <tr><td>';

	echo '<table class="selection">
			<tr height="30px">
				<td width="100px">' . _('Record ID') . ':</td>
				<th width="200px">' . $_POST['id'] . '</th>
			</tr>';

	echo '<tr height="30px"><td>' . _('Machine.') . '</td>
			<td>'.$_POST['machine'].'</td>
		</tr><tr height="30px">
		<td>' . _('Date') . '</td>
			<td>'.$_POST['date'].'</td>
		</tr><tr height="30px">
		<td>' . _('Calibre') . '</td>
			<td>'.$_POST['calibre'].'</td>
		</tr>
		<tr height="30px">
		<td>' .  _('Lot No') . '</td>
		<td>'.$_POST['lot'] .'</td>
		<td>';

echo '</td>
	</tr>
	</table>';
include('includes/Level_Tracking_QA.php');
	echo '</td>	<td>';
	
	
	echo '</td><td>
	<td valign="top" class="status">
	<div style="background:url(css/status1.png) left top no-repeat; height:150px; width:150px;">
	<div style="padding-left:35px; padding-top:2px; font-weight:bold; font-size:12px;">'.$label.' &nbsp;QAT</div>
	<div style="padding-left:35px; padding-top:4px; font-weight:bold; font-size:12px;">'.$label2.' &nbsp;M/C Setter</div>
	<div style="padding-left:35px; padding-top:4px; font-weight:bold; font-size:12px;">'.$label3.' &nbsp;CAPO</div>
	<div style="padding-left:35px; padding-top:4px; font-weight:bold; font-size:12px;">'.$label4.' &nbsp;PM</div>
	<div style="padding-left:35px; padding-top:4px; font-weight:bold; font-size:12px;">'.$label5.' &nbsp;CQAO</div>
	<div style="padding-left:35px; padding-top:4px; font-weight:bold; font-size:12px;">'.$label6.' &nbsp;QARDM</div>
	
	
	</div>
	</td>
	</tr>
	<tr><td colspan="4">';
		
$sql = "SELECT *
		FROM qanonconformingremarks
		WHERE refid='" . $SelectedUser . "' ORDER BY approver ASC";
	$results = DB_query($sql);
echo '<table class="selection">';

$TITLE = "";
while($myro = DB_fetch_array($results)){
	if($TITLE != $myro['approvertitle']){
	echo '<tr>
		<th colspan="2" width="300px">' .  $myro['approvertitle']  . '</th>
	</tr>';
	//$TITLE = $myro['approvertitle'];
	}
echo '<tr>
		<td colspan="2"><textarea disabled="true" cols="60" rows="1">'.$myro['remarks'].'</textarea></td>
	</tr>';
echo '<tr>
		<td colspan="2">'.($myro['action'] !="" ? 'ACTION : '.$myro['action'] : '').'</td>
	</tr>';
echo '<tr>
		<td><strong style="color: #999999; font-size:12px;">Remarks From :</strong> <a style="text-decoration:none" href="#">' .  $myro['approvername'] . '</a></td>
		<td class="number"><span style="color: #999999; font-size:12px;">' .  calculate_time_span($myro['remarkdate']) . '</span></td>
	</tr>';
	}

echo '</table>';
	
echo '</td></tr>
	</table>';
} else{


echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" id="form">
	<div>
	<br />
	<table>
		<tr>';
		
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

#############################################################################################
if (isset($SelectedUser)) {
	//editing an existing User

	$sql = "SELECT *
		FROM qanonconformingproducts a
		INNER JOIN qanonconformingremarks b ON a.id=b.refid
		WHERE id='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['id'] = $myrow['id'];
	$_POST['machine'] = $myrow['machine'];
	$_POST['date'] = $myrow['date'];
	$_POST['calibre'] = $myrow['calibre'];
	$_POST['lot'] = $myrow['lot'];
	$_POST['remarks'] = $myrow['remarks'];
	$_POST['setter'] = $myrow['mc_setter'];


	echo '<input type="hidden" name="SelectedUser" value="' . $SelectedUser . '" />';
	echo '<input type="hidden" name="UserID" value="' . $_POST['id'] . '" />';

	echo '<table class="selection">
			<tr>
				<td>' . _('Record ID') . ':</td>
				<td>' . $_POST['id'] . '</td>
			</tr>';
echo '<a href="' . $RootPath . '/QACreateNon_ConformanceForm.php">' . _('Back to Main Menu') . '</a>';
}
#############################################################################################

	echo '<td>' . _('Machine.') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" name="machine" value="'.$_POST['machine'].'" /></td>
		</tr>
		<tr>
		<td>' . _('Machine Setter') . '</td>
			<td><select name="setter" required="required">';
     $SQL = "SELECT userid,
						realname
					FROM www_users
					WHERE blocked='0'";
				 $result=DB_query($SQL);
  echo '<option selected="selected" value="">--Select Machine Setter--</option>';
  while ($myrow4=DB_fetch_array($result)){
	if (isset($_POST['setter']) AND  $myrow4['userid']==$_POST['setter']){
		echo '<option selected="selected" value="'. $myrow4['userid'] . '">' . $myrow4['realname'] . '</option>';
	} else {
		echo '<option value="'. $myrow4['userid'] . '">' . $myrow4['realname'] . '</option>';
	}
}
  echo '</select></td>
		</tr>
		<td>' . _('Date') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" class="date" name="date" value="'.Date($_SESSION['DefaultDateFormat']).'" /></td>
		</tr>
		<td>' . _('Calibre') . '</td>
			<td><select name="calibre" required="required">';
     $SQL = "SELECT calibre	FROM wocalibre";
				 $result=DB_query($SQL);
  echo '<option selected="selected" value="">--Select Calibre--</option>';
  while ($myrow4=DB_fetch_array($result)){
	if (isset($_POST['calibre']) AND  $myrow4['calibre']==$_POST['calibre']){
		echo '<option selected="selected" value="'. $myrow4['calibre'] . '">' . $myrow4['calibre'] . '</option>';
	} else {
		echo '<option value="'. $myrow4['calibre'] . '">' . $myrow4['calibre'] . '</option>';
	}
}
  echo '</select></td>
		</tr>
		<tr>
		<td>' .  _('Lot No.') . '</td>';
	echo '<td><input type="text" autofocus="autofocus" name="lot" value="'.$_POST['lot'].'" /></td>';
		

echo '</td>
	</tr>
	</table>
	<br/>';

echo '</div>';

echo '<table id="dataTable" class="selection">';
echo '<tr>
		<th>' .  _('QAT Observation(s)/Details of Non-Conformance')  . '</th>
	</tr>';

echo '<tr>
	<td><textarea name="remarks" required="required" cols="70" rows="5">'.$_POST['remarks'].'</textarea></td>
		</tr>';
echo '</table>';

echo '<input type="submit" name="Submit" value="' . _('Save Draft') . '" />';

echo '</form><BR />';

######################################################################################
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" id="form">';
		
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
if (!isset($SelectedUser)) {
	$sql = "SELECT *
				FROM qanonconformingproducts ORDER BY id DESC";
	$result = DB_query($sql);
	
	$ListCount = DB_num_rows($result);
	$ListPageMax = ceil($ListCount / $_SESSION['DisplayRecordsMax']);
	if (isset($_POST['Next'])) {
			if ($_POST['PageOffset'] < $ListPageMax) {
				$_POST['PageOffset'] = $_POST['PageOffset'] + 1;
			}
		}
		if (isset($_POST['Previous'])) {
			if ($_POST['PageOffset'] > 1) {
				$_POST['PageOffset'] = $_POST['PageOffset'] - 1;
			}
		}
		echo '<input type="hidden" name="PageOffset" value="' . $_POST['PageOffset'] . '" />';
		if ($ListPageMax > 1) {
			echo '<br /><div class="centre">&nbsp;&nbsp;' . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';
			echo '<select name="PageOffset1">';
			$ListPage = 1;
			while ($ListPage <= $ListPageMax) {
				if ($ListPage == $_POST['PageOffset']) {
					echo '<option value="' . $ListPage . '" selected="selected">' . $ListPage . '</option>';
				} else {
					echo '<option value="' . $ListPage . '">' . $ListPage . '</option>';
				}
				$ListPage++;
			}
			echo '</select>
				<input type="submit" name="Go1" value="' . _('Go') . '" />
				<input type="submit" name="Previous" value="' . _('Previous') . '" />
				<input type="submit" name="Next" value="' . _('Next') . '" />';
			echo '</div>';
		}
	
	echo '<table class="selection">';
	echo '<tr><th>' . _('Record No') . '</th>
				<th>' . _('Machine') . '</th>
				<th>' . _('Machine Setter') . '</th>
				<th>' . _('Date') . '</th>
				<th>' . _('Calibre') . '</th>
				<th>' . _('Lot No') . '</th>
				<th>' . _('Status') . '</th>
				<th colspan="4">&nbsp;</th>
			</tr>';

	$k=0; //row colour counter

	DB_data_seek($result, ($_POST['PageOffset'] - 1) * $_SESSION['DisplayRecordsMax']);
	while (($myrow = DB_fetch_array($result)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}

	if ($myrow['date']=='') {
		$LastVisitDate = Date($_SESSION['DefaultDateFormat']);
	} else {
		$LastVisitDate = ConvertSQLDate($myrow['date']);
	}

		/*The SecurityHeadings array is defined in config.php */
		if ($myrow['process_level'] !=0) {
		$view='<td><a href="%s&amp;SelectedUser=%s&amp;view=1">' . _('View') . '</a></td>';
		$edit='';
		$del='';
		$process='';
		}else {
		$edit='<td><a href="%s&amp;SelectedUser=%s">' . _('Edit') . '</a></td>';
		$del= '<td><a href="%s&amp;SelectedUser=%s&amp;delete=1" onclick="return confirm(\'' . _('Are you sure you wish to delete this Record?') . '\');">' . _('Delete') . '</a></td>';
		$process='<td><a href="%s&amp;SelectedUser=%s&amp;process=1" onclick="return confirm(\'' . _('Are you sure you wish to Forward this Record?') . '\');">' . _('Process') . '</a></td>';
		$view='<td><a href="%s&amp;SelectedUser=%s&amp;view=1">' . _('View') . '</a></td>';
		}
		if($myrow['process_level'] >=1){
		$status = '<td><b class="jj" style="background: #3399FF; font-size:12px;" id="">Forwarded</b></td>';
		}else{
		$status = '<td><b class="jj" style="background: #FFCC33; font-size:12px;" id="">Pending</b></td>';
		}


		printf('<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					' .$status. '
					' .$edit. '
					' .$del. '
					'.$process.'
					'.$view.'
					</tr>',
					$myrow['id'],
					$myrow['machine'],
					$myrow['mc_setter'],
					$LastVisitDate,
					$myrow['calibre'],
					$myrow['lot'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?',
					$myrow['id'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?',
					$myrow['id'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?',
					$myrow['id'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?',
					$myrow['id']);
	$RowIndex++;
	} //END WHILE LIST LOOP

	echo '</table>';
	if (isset($ListPageMax) AND $ListPageMax > 1) {
		echo '<br /><div class="centre">&nbsp;&nbsp;' . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';
		echo '<select name="PageOffset2">';
		$ListPage = 1;
		while ($ListPage <= $ListPageMax) {
			if ($ListPage == $_POST['PageOffset']) {
				echo '<option value="' . $ListPage . '" selected="selected">' . $ListPage . '</option>';
			} //$ListPage == $_POST['PageOffset']
			else {
				echo '<option value="' . $ListPage . '">' . $ListPage . '</option>';
			}
			$ListPage++;
		} //$ListPage <= $ListPageMax
		echo '</select>
			<input type="submit" name="Go2" value="' . _('Go') . '" />
			<input type="submit" name="Previous" value="' . _('Previous') . '" />
			<input type="submit" name="Next" value="' . _('Next') . '" />';
		echo '</div>';
	}//end if results to show
}
echo '</form>';
############################################################################################
} //close else function
include('includes/footer.inc');
?>

		<style type="text/css">
		.jj {
    border-radius: 2px;
	padding-right:5px;
	padding-left:5px;
	padding-bottom:2px;
	color:#FFFFFF;
	font-weight:bold;
    width: 35px;
	font-family:"Times New Roman", Times, serif;
}
		</style>