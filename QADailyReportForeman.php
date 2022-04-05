<?php

/* $Id: SecurityTokens.php 4424 2010-12-22 16:27:45Z tim_schofield $*/

include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$Title = _('BLD. 54 QA DAILY REPORT');

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

############################################################################################

if (isset($_GET['SelectedUser'])){
	$SelectedUser = $_GET['SelectedUser'];
} elseif (isset($_POST['SelectedUser'])){
	$SelectedUser = $_POST['SelectedUser'];
}
if (isset($_POST['Submit'])) {
$sql = "UPDATE qadailyreport SET process_level='" . 2 . "'
					WHERE id = '". $SelectedUser . "'";

		$result = DB_query($sql);
DB_query("DELETE FROM qadailyreportremarks WHERE approver=2 AND refid='". $SelectedUser ."'");
$sql = "INSERT INTO qadailyreportremarks (refid,remarks,approver,approvertitle,approvername)
					VALUE('". $SelectedUser ."','". $_POST['remarks'] ."',2,'Foremen Remarks','". $_SESSION['UsersRealName'] ."')";

		$result = DB_query($sql);
		prnMsg( _('The selected Report has been forwaded for processing'), 'info' );
		
	unset($SelectedUser);

}

if (isset($_POST['Reject'])) {
$sql = "UPDATE qadailyreport SET process_level='" . 10 . "'
					WHERE id = '". $SelectedUser . "'";

		$result = DB_query($sql);
DB_query("DELETE FROM qadailyreportremarks WHERE approver=2 AND refid='". $SelectedUser ."'");
$sql = "INSERT INTO qadailyreportremarks (refid,remarks,approver,approvertitle,approvername)
					VALUE('". $SelectedUser ."','". $_POST['remarks'] ."',2,'Foremen Reason For Rejection','". $_SESSION['UsersRealName'] ."')";

		$result = DB_query($sql);
		prnMsg( _('The selected Report has been Rejected back to the Technician'), 'info' );
		
	unset($SelectedUser);

}
	######################################################################################
	echo '<div id="popDiv" style="z-index: 999;
									width: 100%;
									height: 100%;
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
				<td colspan="2"><input type="submit" name="Submit" value="' . _('Submit') . '" />
				</form>';
	echo "<input type=submit onclick=hide('popDiv') value=" . _('Cancel') . " />";
	echo '</td>
			</tr>
			</table>';
	
	echo '</div>';	
######################################################################################
	echo '<div id="popDivReject" style="z-index: 999;
									width: 100%;
									height: 100%;
									top: 0;
									left: 0;
									display: none;
									position: absolute;				
									background-color: #fff;
									background-color: rgba(255,255,255,0.7);
									filter: alpha(opacity = 50);">';
	
	
	echo '<table style="width: 350px;
						height: 200px;
						background: #FF3300;
						position: absolute;
						color: #000000;
						/* To align popup window at the center of screen*/
						top: 50%;
						left: 50%;
						margin-top: -100px;
						margin-left: -150px;">
			<tr>
				<th colspan="2">' . _('Please Leave your Reason') . ':</th>
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
				<td colspan="2"><input type="submit" name="Reject" value="' . _('Reject') . '" />
				</form>';
	echo "<input type=submit onclick=hide('popDivReject') value=" . _('Cancel') . " />";
	echo '</td>
			</tr>
			</table>';
	
	echo '</div>';	
######################################################################################
if (isset($_GET['view'])) {

	$sql = "SELECT *
		FROM qadailyreport
		WHERE id='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['id'] = $myrow['id'];
	
echo '<a href="' . $RootPath . '/QADailyReportForeman.php">' . _('Back to Main Menu') . '</a>';

echo '<table class="selection">
      <tr><td colspan="4">';

	echo '<table class="selection">
			<tr height="30px">
				<th width="100px">' . _('Calibre Tested') . '</th>
				<th width="100px">' . _('Cart. Lot No.') . '</th>
				<th width="100px">' . _('Det. Charge') . '</th>
				<th width="100px">' . _('Powder Lot No.') . '</th>
				<th width="100px">' . _('Bullet Mass') . '</th>
			</tr>';

	echo '<tr height="30px">
			<td>'.$myrow['calibre'].'</td>
			<td>'.$myrow['cartlotno'].'</td>
			<td>'.$myrow['detcharge'].'</td>
			<td>'.$myrow['powderlotno'].'</td>
			<td>'.$myrow['bulletmass'].'</td>
		</tr>';
echo '</td>
	</tr>
	</table>';
	if($myrow['process_level']==1){
	echo "<a href=# onclick=pop('popDivReject')><input type=submit style='background:#FF3300; width:130px;' name=reject value='<<Reject' /></a>&nbsp;&nbsp;&nbsp;";
	echo " <a href=# onclick=pop('popDiv')><input type=submit name=nam value='Forward>>' /></a>"; 
	}
	echo '</td>	<td>';	
	echo '<tr><th colspan="4">EPVAT TEST</th></tr>';
	if($myrow['calibre']=='7.62x51mm Ball'){
	$velocity = '833.5 &plusmn; 15 m/s';
	$pressure = '< 3600 Bars';
	$pressure1 = '< 2300 Bars';
	$accuracy = '< 150 mm';
	$force = '&ge; 27.21 Kgf';
	$rate = '650 to 750 Rds/Min';
	$at = 'At 3" 100% Misfire<br />At 16" 100% Fire';
	$v = 'V25m';
	}elseif($myrow['calibre']=='5.56x45mm Ball'){
	$velocity = '915 &plusmn; 12 m/s';
	$pressure = '< 3800 Bars';
	$pressure1 = '< 550 Bars';
	$accuracy = '< 221 mm';
	$force = '&ge; 20.4 Kgf';
	$rate = '700 to 1000 Rds/Min';
	$at = 'At 3" 100% Misfire<br />At 14" 100% Fire';
	$v = 'V25m';
	}elseif($myrow['calibre']=='9x19mm Para'){
	$velocity = '370 &plusmn; 10 m/s';
	$pressure = '< 2300 Bars';
	$pressure1 = '< 880 Bars';
	$accuracy = '< 200 mm';
	$force = '&ge; 20.4 Kgf';
	$rate = '550 to 650 Rds/Min';
	$at = 'At 3" 100% Misfire<br />At 12" 100% Fire';
	$v = 'V15m';
	}
	echo '<tr><td colspan="2"></td><th>Required Velocity</th><th>Obtained Velocity</th></tr>';
	echo '<tr><td>Mean Velocity</td><td>'.$v.'</td><td><center>'.$velocity.'</center></td><td><center>'.$myrow['velocity'].'</center></td></tr>';
	echo '<tr><td colspan="2"></td><th>Required Pressure</th><th>Obtained Pressure</th></tr>';
	echo '<tr><td>Mean Mouth Pressure</td><td></td><td><center>'.$pressure.'</center></td><td><center>'.$myrow['mouthpressure'].'</center></td></tr>';
	echo '<tr><td>Mean Port Pressurre</td><td></td><td><center>'.$pressure1.'</center></td><td><center>'.$myrow['portpressure'].'</center></td></tr>';
	echo '<tr><th colspan="4">Mean Accuracy (H + L)</th></tr>';
	echo '<tr><td colspan="2"></td><th>Required Accuracy</th><th>Obtained Accuracy</th></tr>';
	echo '<tr><td>Bullet Production</td><td>Lot No : '.$myrow['bulletproductionlot'].'</td><td><center>'.$accuracy.'</center></td><td><center>'.$myrow['bulletproduction'].'</center></td></tr>';
	echo '<tr><td colspan="2"></td><th>Required Accuracy</th><th>Obtained Accuracy</th></tr>';
	echo '<tr><td>Loading (PC 530)</td><td>Lot No : '.$myrow['loadinglot'].'</td><td><center>'.$accuracy.'</center></td><td><center>'.$myrow['loading'].'</center></td></tr>';
	echo '<tr><td colspan="2"></td><th>Required Force</th><th>Obtained Force</th></tr>';
	echo '<tr><td>Bullet Extraction Force</td><td>Lot No : '.$myrow['bextractionlot'].'</td><td><center>'.$force.'</center></td><td><center>'.$myrow['bextraction'].'</center></td></tr>';
	echo '<tr><td colspan="2"></td><th>Required Standard</th><th>Remarks</th></tr>';
	echo '<tr><td>Mercurous Nitrate Test</td><td></td><td><center>No Cracks(0)</center></td><td><center>'.$myrow['mercurous'].'</center></td></tr>';
	echo '<tr><td>Water Tightness Test</td><td></td><td><center>&le; 3 Out of 20 Leaks</center></td><td><center>'.$myrow['watertightness'].'</center></td></tr>';
	echo '<tr><td colspan="2"></td><th>Required Rate/Minute</th><th>Remarks</th></tr>';
	echo '<tr><td>Rate of Fire</td><td>Lot No : '.$myrow['ratefirelot'].'</td><td><center>'.$rate.'</center></td><td><center>'.$myrow['ratefire'].'</center></td></tr>';
	echo '<tr><td colspan="2"></td><th>Required Rate/Minute</th><th>Remarks</th></tr>';
	echo '<tr><td>Primer Sensitivity</td><td>Lot No : '.$myrow['sensitivitylot'].'</td><td><center>'.$at.'</center></td><td><center>'.$myrow['sensitivityat3'].'</br />'.$myrow['sensitivity'].'</center></td></tr>';
	
	echo '<tr><td colspan="4">';
		
$sql = "SELECT *
		FROM qadailyreportremarks
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
				FROM qadailyreport where process_level>=1 ORDER BY id DESC";
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
				<th>' . _('Calibre') . '</th>
				<th>' . _('Cart. Lot No') . '</th>
				<th>' . _('Created Date') . '</th>
				<th>' . _('Det. Charge') . '</th>
				<th>' . _('Powder Lot No') . '</th>
				<th>' . _('Bullet Mass') . '</th>
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
		if($myrow['process_level'] >=2 && $myrow['process_level'] != 10){
		$status = '<td><b class="jj" style="background: #3399FF; font-size:12px;" id="">Forwarded</b></td>';
		}elseif($myrow['process_level'] ==10 ){
		$status = '<td><b class="jj" style="background: #FF3300; font-size:12px;" id="">Rejected</b></td>';
		}else{
		$status = '<td><b class="jj" style="background: #FFCC33; font-size:12px;" id="">Pending</b></td>';
		}


		printf('<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					' .$status. '
					'.$view.'
					</tr>',
					$myrow['id'],
					$myrow['calibre'],
					$myrow['cartlotno'],
					$LastVisitDate,
					$myrow['detcharge'],
					$myrow['powderlotno'],
					$myrow['bulletmass'],
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
}
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