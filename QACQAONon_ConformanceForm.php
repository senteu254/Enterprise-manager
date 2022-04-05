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
$sql = "UPDATE qanonconformingproducts SET process_level='" . 5 . "'
					WHERE id = '". $SelectedUser . "'";

		$result = DB_query($sql);
$sql = "INSERT INTO qanonconformingremarks (refid,remarks,action,approver,approvertitle,approvername)
					VALUE('". $SelectedUser ."','". $_POST['remarks'] ."','". $_POST['action'] ."',5,'CQAO FOLLOW-UP ACTION','". $_SESSION['UsersRealName'] ."')";

		$result = DB_query($sql);
		prnMsg( _('The selected Record has been forwaded for processing'), 'info' );
		
	unset($SelectedUser);

}
	echo '<div id="popDiv" style="z-index: 999;
									width: 100%;
									height: 70%;
									top: 0;
									left: 0;
									display: none;
									position: absolute;				
									background-color: #fff;
									background-color: rgba(255,255,255,0.7);
									filter: alpha(opacity = 50);">';
	
	
	echo '<table style="width: 350px;
						height: 200px;
						position: absolute;
						color: #000000;
						/* To align popup window at the center of screen*/
						top: 50%;
						left: 50%;
						margin-top: -100px;
						margin-left: -150px;">
			<tr>
				<th colspan="2">' . _('Please Leave your Remarks') . ':</th>
			</tr>';
			echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" id="form">';
			echo '<tr>
				<td colspan="2">
				<textarea required name="remarks" cols="40" rows="4"></textarea>
				<input type="hidden" name="SelectedUser" value="' . $_GET['SelectedUser'] . '" />
				<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
				</td>
			</tr>
			<tr>
			<td>ACTION FULLY COMPLETED </td><td><input name="action" type="radio" value="FULLY COMPLETED" /></td>
			</tr>
			<tr>
			<td>ACTION PARTIALLY COMPLETED </td><td><input name="action" type="radio" value="PARTIALLY COMPLETED" /></td>
			</tr>
			<tr>
			<td>NO ACTION TAKEN</td><td> <input name="action" type="radio" value="NO ACTION TAKEN" /></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" name="Submit" value="' . _('Submit') . '" />
				</form>';
	echo "<input type=submit onclick=hide('popDiv') value=" . _('Cancel') . " />";
	echo '</td>
			</tr>
			</table>';
	
	echo '</div>';	
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
	
echo '<a href="' . $RootPath . '/QACQAONon_ConformanceForm.php">' . _('Back to Main Menu') . '</a>';

echo '<table class="selection">
      <tr><td>';

	echo '<table class="selection">
			<tr >
				<td width="100px">' . _('Record ID') . ':</td>
				<th width="200px">' . $_POST['id'] . '</th>
			</tr>';

	echo '<tr><td>' . _('Machine.') . '</td>
			<td>'.$_POST['machine'].'</td>
		</tr><tr height="30px">
		<td>' . _('Date') . '</td>
			<td>'.$_POST['date'].'</td>
		</tr><tr>
		<td>' . _('Calibre') . '</td>
			<td>'.$_POST['calibre'].'</td>
		</tr>
		<tr>
		<td>' .  _('Lot No') . '</td>
		<td>'.$_POST['lot'] .'</td>
		<td>';

echo '</td>
	</tr>
	</table>';
	if($myrow['process_level']==4){
	echo "<a href=# onclick=pop('popDiv')><input type=submit name=nam value=Submit /></a>";
	}
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

######################################################################################
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" id="form">';
		
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
if (!isset($SelectedUser)) {
	$sql = "SELECT *
				FROM qanonconformingproducts where process_level >=4 ORDER BY id DESC";
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
		$view='<td><a href="%s&amp;SelectedUser=%s&amp;view=1">' . _('View') . '</a></td>';
		if($myrow['process_level'] >=5){
		$status = '<td><b class="jj" style="background: #3399FF; font-size:12px;" id="">Forwarded</b></td>';
		}else{
		$status = '<td><b class="jj" style="background: #FFCC33; font-size:12px;" id="">Pending</b></td>';
		}


		printf('<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					' .$status. '
					'.$view.'
					</tr>',
					$myrow['id'],
					$myrow['machine'],
					$LastVisitDate,
					$myrow['calibre'],
					$myrow['lot'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?',
					$myrow['id'],
					htmlspecialchars('PDFConformanceReportPortrait.php',ENT_QUOTES,'UTF-8')  . '?',
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
		<script type="text/javascript">
			function pop(div) {
				document.getElementById(div).style.display = 'block';
			}
			function hide(div) {
				document.getElementById(div).style.display = 'none';
			}
			//To detect escape button
			document.onkeydown = function(evt) {
				evt = evt || window.event;
				if (evt.keyCode == 27) {
					hide('popDiv');
				}
			};
		</script>
		
		<style type="text/css">
		.jj {
    border-radius: 2px;
	padding-right:5px;
	padding-left:5px;
	padding-bottom:2px;
	font-weight:bold;
	color:#FFFFFF;
	font-weight:bold;
    width: 35px;
	font-family:"Times New Roman", Times, serif;
}
		</style>
