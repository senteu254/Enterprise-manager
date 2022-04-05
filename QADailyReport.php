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
if (isset($_POST['Submit'])) {
$InputError = 0;

if (isset($SelectedUser)) {
$sql = "UPDATE qadailyreport SET calibre='" . $_POST['calibre'] ."',
		cartlotno='" . $_POST['cartlotno'] ."',
		detcharge='" . $_POST['detcharge'] ."',
		powderlotno='" . $_POST['powderlotno'] ."',
		bulletmass='" . $_POST['bulletmass'] ."',
		velocity='" . $_POST['velocity'] ."',
		mouthpressure='" . $_POST['mouthpressure'] ."',
		portpressure='" . $_POST['portpressure'] ."',
		mercurous='" . $_POST['mercurous'] ."',
		watertightness='" . $_POST['watertightness'] ."',
		bulletproduction='" . $_POST['bproduction'] ."',
		bulletproductionlot='" . $_POST['bproductionlot'] ."',
		loading='" . $_POST['loading'] ."',
		loadinglot='" . $_POST['loadinglot'] ."',
		bextraction='" . $_POST['bextraction'] ."',
		bextractionlot='" . $_POST['bextractionlot'] ."',
		ratefire='" . $_POST['ratefire'] ."',
		ratefirelot='" . $_POST['ratefirelot'] ."',
		sensitivity='" . $_POST['sensitivity'] ."',
		sensitivityat3='" . $_POST['sensitivity3'] ."',
		sensitivitylot='" . $_POST['sensitivitylot'] ."'
					WHERE id = '". $SelectedUser . "'";
					
$sql2 = "UPDATE qadailyreportremarks SET remarks='" . $_POST['remarks'] . "',
						approvername='" . $_SESSION['UsersRealName'] ."'
					WHERE refid = '". $SelectedUser . "' and approver=1";
					
	prnMsg( _('The selected report has been updated successfully'), 'success' );
		
	} elseif ($InputError !=1) {
	//initialise no input errors assumed initially before we test
		$RequestNo = GetNextTransNo(82, $db);
		$sql = "INSERT INTO `qadailyreport`(`id`,`calibre`, `cartlotno`, `detcharge`, `powderlotno`, `bulletmass`, `velocity`, `mouthpressure`, `portpressure`, `mercurous`, `watertightness`, `bulletproduction`, `bulletproductionlot`, `loading`, `loadinglot`, `bextraction`, `bextractionlot`, `ratefire`, `ratefirelot`, `sensitivity`, `sensitivityat3`, `sensitivitylot`) 
		VALUES ('" . $RequestNo ."', 
		'" . $_POST['calibre'] ."',
		'" . $_POST['cartlotno'] ."',
		'" . $_POST['detcharge'] ."',
		'" . $_POST['powderlotno'] ."',
		'" . $_POST['bulletmass'] ."',
		'" . $_POST['velocity'] ."',
		'" . $_POST['mouthpressure'] ."',
		'" . $_POST['portpressure'] ."',
		'" . $_POST['mercurous'] .' '. $_POST['mcheck'] ."',
		'" . $_POST['watertightness'] .' '. $_POST['wcheck'] ."',
		'" . $_POST['bproduction'] .' '. $_POST['bcheck'] ."',
		'" . $_POST['bproductionlot'] ."',
		'" . $_POST['loading'] .' '. $_POST['lcheck'] ."',
		'" . $_POST['loadinglot'] ."',
		'" . $_POST['bextraction'] ."',
		'" . $_POST['bextractionlot'] ."',
		'" . $_POST['ratefire'] ."',
		'" . $_POST['ratefirelot'] ."',
		'" . $_POST['sensitivity'] ."',
		'" . $_POST['sensitivity3'] ."',
		'" . $_POST['sensitivitylot'] ."')";
		
		$sql2 = "INSERT INTO qadailyreportremarks (`refid`, `remarks`,approver, approvertitle, `approvername`)
					VALUES (" . $RequestNo . ",
						'" . $_POST['remarks'] ."',
						1,
						'Final Remarks',
						'" . $_SESSION['UsersRealName'] ."')";
		prnMsg( _('A new record has been inserted Successfully'), 'success' );

	}

	if ($InputError!=1){
		//run the SQL from either of the above possibilites
		$ErrMsg = _('The user alterations could not be processed because');
		$DbgMsg = _('The SQL that was used to update the user and failed was');
		$result = DB_query($sql,$ErrMsg,$DbgMsg);
		$result = DB_query($sql2,$ErrMsg,$DbgMsg);

		unset( $_POST['calibre']);
		unset( $_POST['cartlotno']);
		unset($_POST['detcharge']);
		unset( $_POST['powderlotno'] );
		unset($_POST['bulletmass']);
		unset( $_POST['velocity']);
		unset($_POST['mouthpressure'] );
		unset($_POST['portpressure'] );
		unset($_POST['mercurous'] );
		unset($_POST['watertightness']);
		unset($_POST['bproduction']);
		unset($_POST['bproductionlot']);
		unset( $_POST['loading']);
		unset($_POST['loadinglot']);
		unset($_POST['bextraction']);
		unset( $_POST['bextractionlot']);
		unset($_POST['ratefire']);
		unset($_POST['ratefirelot'] );
		unset($_POST['sensitivity']);
		unset($_POST['sensitivity3']);
		unset($_POST['sensitivitylot']);
		unset($_POST['remarks']);
		unset($SelectedUser);
	}
	
}elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button		

			$sql="DELETE FROM qadailyreport WHERE id='" . $SelectedUser . "'";
			$ErrMsg = _('The Record could not be deleted because');
			$result = DB_query($sql,$ErrMsg);
			$sql2="DELETE FROM qadailyreportremarks WHERE refid='" . $SelectedUser . "'";
			$result = DB_query($sql2,$ErrMsg);
			prnMsg(_('Report Deleted Successfully'),'info');

		unset($SelectedUser);
	}
	if (isset($_GET['process'])) {
$sql = "UPDATE qadailyreport SET process_level='" . $_GET['process'] . "'
					WHERE id = '". $SelectedUser . "'";

		$result = DB_query($sql);
		prnMsg( _('The selected Report has been forwaded for processing'), 'info' );
		
	unset($SelectedUser);
	}
######################################################################################
if (isset($_GET['view'])) {

	$sql = "SELECT *
		FROM qadailyreport
		WHERE id='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['id'] = $myrow['id'];
	
echo '<a href="' . $RootPath . '/QADailyReport.php">' . _('Back to Main Menu') . '</a>';

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
		FROM qadailyreport a
		INNER JOIN qadailyreportremarks b ON a.id=b.refid
		WHERE a.id='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['id'] = $myrow['id'];
	$_POST['calibre']  = $myrow['calibre'];
	$_POST['cartlotno']  = $myrow['cartlotno'];
	$_POST['detcharge']  = $myrow['detcharge'];
	$_POST['powderlotno']   = $myrow['powderlotno'];
	$_POST['bulletmass']  = $myrow['bulletmass'];
	$_POST['velocity'] = $myrow['velocity'];
	$_POST['mouthpressure'] = $myrow['mouthpressure'];
	$_POST['portpressure'] = $myrow['portpressure'];
	$_POST['mercurous'] = $myrow['mercurous'];
	$_POST['watertightness'] = $myrow['watertightness'];
	$_POST['bproduction'] = $myrow['bulletproduction'];
	$_POST['bproductionlot'] = $myrow['bulletproductionlot'];
	$_POST['loading'] = $myrow['loading'];
	$_POST['loadinglot'] = $myrow['loadinglot'];
	$_POST['bextraction'] = $myrow['bextraction'];
	$_POST['bextractionlot'] = $myrow['bextractionlot'];
	$_POST['ratefire'] = $myrow['ratefire'];
	$_POST['ratefirelot'] = $myrow['ratefirelot'];
	$_POST['sensitivity'] = $myrow['sensitivity'];
	$_POST['sensitivity3'] = $myrow['sensitivityat3'];
	$_POST['sensitivitylot'] = $myrow['sensitivitylot'];
	$_POST['remarks'] = $myrow['remarks'];


	echo '<input type="hidden" name="SelectedUser" value="' . $SelectedUser . '" />';
	echo '<input type="hidden" name="UserID" value="' . $_POST['id'] . '" />';

	echo '<table class="selection">
			<tr>
				<td>' . _('Record ID') . ':</td>
				<td>' . $_POST['id'] . '</td>
			</tr>';
echo '<a href="' . $RootPath . '/QADailyReport.php">' . _('Back to Main Menu') . '</a>';
}
#############################################################################################

	echo '<td>' . _('Calibre') . '</td>
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
		</tr><tr>
		<td>' . _('Cart. Lot No') . '</td>
			<td><input type="text" autofocus="autofocus" id="first" required="required" name="cartlotno" value="'.$_POST['cartlotno'].'" /></td>
		</tr>
		<td>' . _('Det Charge') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" name="detcharge" value="'.$_POST['detcharge'].'" /></td>
		</tr>
		<td>' . _('Powder Lot No') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" name="powderlotno" value="'.$_POST['powderlotno'].'" /></td>
		</tr>
		<tr>
		<td>' .  _('Bullet Mass') . '</td>';
	echo '<td><input type="text" autofocus="autofocus" name="bulletmass" value="'.$_POST['bulletmass'].'" /></td>';
		

echo '</td>
	</tr>
	</table>
	<br/>';

echo '</div>';

echo '<table id="dataTable" class="selection">';

echo '<tr>
	<td>' . _('Mean Velocity ') . '</td>
	<td><input type="text" autofocus="autofocus" required="required" name="velocity" value="'.$_POST['velocity'].'" /></td>
		</tr>';
echo '<tr>
	<td>' . _('Mean Mouth Pressure ') . '</td>
	<td><input type="text" autofocus="autofocus" required="required" name="mouthpressure" value="'.$_POST['mouthpressure'].'" /></td>
		</tr>';
echo '<tr>
	<td>' . _('Mean Port Pressure') . '</td>
	<td><input type="text" autofocus="autofocus" required="required" name="portpressure" value="'.$_POST['portpressure'].'" /></td>
		</tr>';
echo '<tr>
	<th colspan="4">' . _('Mean Accuracy (H+L)') . '</th>
		</tr>';
echo '<tr>
	<td>' . _('Bullet Production') . '</td>
	<td>
	'.(isset($SelectedUser) ? '<input type="text" autofocus="autofocus" required="required" name="bproduction" value="'.$_POST['bproduction'].'" />' :
	'<input type="text" autofocus="autofocus" size="8" required="required" name="bproduction" value="'.$_POST['bproduction'].'" />
	<select name="bcheck">
	<option value="Pass">Pass</option>
	<option value="Fail">Fail</option>
	</select>' ).
	'</td>
	<td>' . _('Lot No') . '</td>
	<td><input type="text" autofocus="autofocus" id="second" name="bproductionlot" value="'.$_POST['bproductionlot'].'" /></td>
		</tr>';
echo '<tr>
	<td>' . _('Loading (PC 530)') . '</td>
	<td>'
	.(isset($SelectedUser) ? '<input type="text" autofocus="autofocus" required="required" name="loading" value="'.$_POST['loading'].'" />' :
	'<input type="text" autofocus="autofocus" size="8" required="required" name="loading" value="'.$_POST['loading'].'" />
	<select name="lcheck">
	<option value="Pass">Pass</option>
	<option value="Fail">Fail</option>
	</select>'
	).
	'</td>
	<td>' . _('Lot No') . '</td>
	<td><input type="text" autofocus="autofocus" id="second1" name="loadinglot" value="'.$_POST['loadinglot'].'" /></td>
		</tr>';
echo '<tr>
	<td>' . _('Bullet Extraction Force') . '</td>
	<td><input type="text" autofocus="autofocus" required="required" name="bextraction" value="'.$_POST['bextraction'].'" /></td>
	<td>' . _('Lot No') . '</td>
	<td><input type="text" autofocus="autofocus" id="second2" name="bextractionlot" value="'.$_POST['bextractionlot'].'" /></td>
		</tr>';
echo '<tr>
	<td>' . _('Mercurous Nitrate Test') . '</td>
	<td>'
	.(isset($SelectedUser) ? '<input type="text" autofocus="autofocus" required="required" name="mercurous" value="'.$_POST['mercurous'].'" />' :
	'<input type="text" autofocus="autofocus" size="8" required="required" name="mercurous" value="'.$_POST['mercurous'].'" />
	<select name="mcheck">
	<option value="Pass">Pass</option>
	<option value="Fail">Fail</option>
	</select>'
	).
	'</td>
		</tr>';
echo '<tr>
	<td>' . _('Water Tightness Test') . '</td>
	<td>'
	.(isset($SelectedUser) ? '<input type="text" autofocus="autofocus" required="required" name="watertightness" value="'.$_POST['watertightness'].'" />' :
	'<input type="text" autofocus="autofocus" size="8" required="required" name="watertightness" value="'.$_POST['watertightness'].'" />
	<select name="wcheck">
	<option value="Pass">Pass</option>
	<option value="Fail">Fail</option>
	</select>'
	).
	'</td>
		</tr>';
echo '<tr>
	<td>' . _('Rate of Fire') . '</td>
	<td><input type="text" autofocus="autofocus" required="required" name="ratefire" value="'.$_POST['ratefire'].'" /></td>
	<td>' . _('Lot No') . '</td>
	<td><input type="text" autofocus="autofocus" id="second3" name="ratefirelot" value="'.$_POST['ratefirelot'].'" /></td>
		</tr>';
echo '<tr>
	<td>' . _('Primer Sensitivity') . ' At 3"</td>
	<td><input type="text" autofocus="autofocus" required="required" name="sensitivity3" value="'.$_POST['sensitivity3'].'" /></td>
	<td rowspan="2">' . _('Primer Lot No') . '</td>
	<td rowspan="2"><input type="text" autofocus="autofocus" name="sensitivitylot" value="'.$_POST['sensitivitylot'].'" /></td>
		</tr>';
echo '<tr>
	<td>' . _('Primer Sensitivity At 16"/14"/12"') . '</td>
	<td><input type="text" autofocus="autofocus" required="required" name="sensitivity" value="'.$_POST['sensitivity'].'" /></td>
		</tr>';
echo '<tr>
	<td>' . _('Final Remarks') . '</td>
	<td colspan="3"><textarea name="remarks" required="required" cols="43" rows="3">'.$_POST['remarks'].'</textarea></td>
		</tr>';
echo '</table>';

echo '<input type="submit" name="Submit" value="' . _('Save Draft') . '" />';

echo '</form><BR />';

######################################################################################
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" id="form">';
		
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
if (!isset($SelectedUser)) {
	$sql = "SELECT *
				FROM qadailyreport ORDER BY id DESC";
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
		if ($myrow['process_level'] !=0) {
		$view='<td><a href="%s&amp;SelectedUser=%s&amp;view=1">' . _('View') . '</a></td>';
		$edit='';
		$del='';
		$process='';
		}else {
		$edit='<td><a href="%s&amp;SelectedUser=%s">' . _('Edit') . '</a></td>';
		$del= '<td><a href="%s&amp;SelectedUser=%s&amp;delete=1" onclick="return confirm(\'' . _('Are you sure you wish to delete this Report?') . '\');">' . _('Delete') . '</a></td>';
		$process='<td><a href="%s&amp;SelectedUser=%s&amp;process=1" onclick="return confirm(\'' . _('Are you sure you wish to Forward this Report?') . '\');">' . _('Process') . '</a></td>';
		$view='<td><a href="%s&amp;SelectedUser=%s&amp;view=1">' . _('View') . '</a></td>';
		}
		if($myrow['process_level'] >=1 && $myrow['process_level'] < 10){
		$status = '<td><b class="jj" style="background: #3399FF; font-size:12px;" id="">Forwarded</b></td>';
		}elseif($myrow['process_level'] >=10){
		$status = '<td><b class="jj" style="background: #FF3300; font-size:12px;" id="">Rejected</b></td>';
		}else{
		$status = '<td><b class="jj" style="background: #FFCC33; font-size:12px;" id="">Pending</b></td>';
		}
		
		if($myrow['process_level'] >=10){
		if($myrow['process_level']==10){
		$alevel='Foreman';
		$prog = 1;
		}elseif($myrow['process_level']==11){
		$alevel='SQAO';
		$prog = 2;
		}elseif($myrow['process_level']==12){
		$alevel='QA Manager';
		$prog = 3;
		}
		$edit='<td><a href="%s&amp;SelectedUser=%s">' . _('Edit') . '</a></td>';
		$del= '<td><a href="%s&amp;SelectedUser=%s&amp;delete=1" onclick="return confirm(\'' . _('Are you sure you wish to delete this Report?') . '\');">' . _('Delete') . '</a></td>';
		$process='<td><a href="%s&amp;SelectedUser=%s&amp;process='.$prog.'" onclick="return confirm(\'' . _('Are you sure you wish to Resend this Report to '.$alevel.'?') . '\');">' . _('Resend') . '</a></td>';
		$view='<td><a href="%s&amp;SelectedUser=%s&amp;view=1">' . _('View') . '</a></td>';
		echo '<input type="hidden" name="Reprocess" value="' . $_SESSION['FormID'] . '" />';
		}


		printf('<td>%s</td>
					<td>%s</td>
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
					$myrow['calibre'],
					$myrow['cartlotno'],
					$LastVisitDate,
					$myrow['detcharge'],
					$myrow['powderlotno'],
					$myrow['bulletmass'],
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
		
		<script type="text/javascript">
		$('#first').keyup(function(){
    $('#second').val(this.value);
	$('#second1').val(this.value);
	$('#second2').val(this.value);
	$('#second3').val(this.value);
});
$('#first').blur(function(){
    $('#second').val(this.value);
	$('#second1').val(this.value);
	$('#second2').val(this.value);
	$('#second3').val(this.value);
});
		</script>