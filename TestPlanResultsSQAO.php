<?php
/* $Id: TestPlanResults.php 1 2014-09-08 10:42:50Z agaluski $*/

include('includes/session.inc');
$Title = _('Test Plan Results');
include('includes/header.inc');

if (isset($_GET['SelectedSampleID'])){
	$SelectedSampleID =mb_strtoupper($_GET['SelectedSampleID']);
} elseif(isset($_POST['SelectedSampleID'])){
	$SelectedSampleID =mb_strtoupper($_POST['SelectedSampleID']);
}

if (!isset($_POST['FromDate'])){
	$_POST['FromDate']=Date(($_SESSION['DefaultDateFormat']), strtotime($UpcomingDate . ' - 15 days'));
}
if (!isset($_POST['ToDate'])){
	$_POST['ToDate'] = Date($_SESSION['DefaultDateFormat']);
}

if (isset($Errors)) {
	unset($Errors);
}

if (isset($_GET['LotNumber'])) {
	$LotNumber = $_GET['LotNumber'];
} elseif (isset($_POST['LotNumber'])) {
	$LotNumber = $_POST['LotNumber'];
}
if (isset($_GET['SampleID'])) {
	$SampleID = $_GET['SampleID'];
} elseif (isset($_POST['SampleID'])) {
	$SampleID = $_POST['SampleID'];
}
if (!isset($_POST['FromDate'])){
	$_POST['FromDate']=Date(($_SESSION['DefaultDateFormat']), Mktime(0, 0, 0, Date('m'), Date('d')-15, Date('Y')));
}
if (!isset($_POST['ToDate'])){
	$_POST['ToDate'] = Date($_SESSION['DefaultDateFormat']);
}

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

$Errors = array();

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/maintenance.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p>';

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;
	
	if(!isset($_POST['remark1'])){
	$_POST['remark1'] = "";
	}
	
	$result = DB_query("SELECT count(sampleid) FROM qrsampleremarks
						WHERE sampleid = '".$SelectedSampleID."' AND approver_level=2",	$db);
	$myrow = DB_fetch_row($result);
	if($myrow[0]>0) {
		$sql = "UPDATE qrsampleremarks SET remark='" . $_POST['remark1'] . "'
				WHERE sampleid = '".$SelectedSampleID."' AND approver_level=2";
		$ErrMsg = _('The update of the QA Sample Remarks failed because');
		$DbgMsg = _('The SQL that was used and failed was');
		$result = DB_query($sql,$db,$ErrMsg, $DbgMsg);
	}else{
	$sql = "INSERT INTO qrsampleremarks (sampleid,remark,approver_level,approver_title,approver_name)
							 VALUES('".$SelectedSampleID."',
							 		'" . $_POST['remark1'] . "',
									'2',
									'SQAO REMARKS',
									'" . $_SESSION['UsersRealName'] . "')";
		$ErrMsg = _('The update of the QA Sample Remarks failed because');
		$DbgMsg = _('The SQL that was used and failed was');
		$result = DB_query($sql,$db,$ErrMsg, $DbgMsg);
	}
	
}
if (!isset($SelectedSampleID)) {
echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<table class="selection"><tr><td>';
		if (!isset($LotNumber)){
			$LotNumber ='';
		}
		if (!isset($SampleID)){
			$SampleID='';
		}
		
		echo _('Lot Number') . ': <input name="LotNumber" autofocus="autofocus" maxlength="20" size="12" value="' . $LotNumber . '"/> ' . _('Sample ID') . ': <input name="SampleID" maxlength="10" size="10" value="' . $SampleID . '"/> ';
		echo _('From Sample Date') . ': <input name="FromDate" size="10" class="date" value="' . $_POST['FromDate'] . '"/> ' . _('To Sample Date') . ': <input name="ToDate" size="10" class="date" value="' . $_POST['ToDate'] . '"/> ';
		echo '<input type="submit" name="SearchSamples" value="' . _('Search Samples') . '" /></td>
			</tr>
			</table>';

$FromDate = FormatDateForSQL($_POST['FromDate']);
		$ToDate = FormatDateForSQL($_POST['ToDate']);
		if (isset($LotNumber) AND $LotNumber != '') {
			$SQL = "SELECT qasamples.sampleid,
							prodspeckey,
							description,
							lotkey,
							identifier,
							createdby,
							sampledate,
							cert
						FROM qasamples
						LEFT OUTER JOIN stockmaster on stockmaster.stockid=qasamples.prodspeckey
						WHERE lotkey='" . filter_number_format($LotNumber) . "'";
		} elseif (isset($SampleID) AND $SampleID != '') {
			$SQL = "SELECT qasamples.sampleid,
							prodspeckey,
							description,
							lotkey,
							identifier,
							createdby,
							sampledate,
							cert
						FROM qasamples
						LEFT OUTER JOIN stockmaster on stockmaster.stockid=qasamples.prodspeckey
						WHERE sampleid='" . filter_number_format($SampleID) . "'";
		} else {
				$SQL = "SELECT qasamples.sampleid,
							prodspeckey,
							description,
							lotkey,
							identifier,
							createdby,
							sampledate,
							comments,
							cert
						FROM qasamples
						LEFT OUTER JOIN stockmaster on stockmaster.stockid=qasamples.prodspeckey
						INNER JOIN qrsampleremarks ON qrsampleremarks.sampleid=qasamples.sampleid
						WHERE sampledate>='".$FromDate."'
						AND sampledate <='".$ToDate."' AND approver_level=1";
		
		} //end no sample id selected
		$ErrMsg = _('No QA samples were returned by the SQL because');
		$SampleResult = DB_query($SQL, $ErrMsg);
		if (DB_num_rows($SampleResult) > 0) {

			echo '<table cellpadding="2" width="90%" class="selection">';
			$TableHeader = '<tr>
								<th class="ascending">' . _('Enter Results') . '</th>
								<th class="ascending">' . _('Specification') . '</th>
								<th class="ascending">' . _('Description') . '</th>
								<th class="ascending">' . _('Lot / Serial') . '</th>
								<th class="ascending">' . _('Identifier') . '</th>
								<th class="ascending">' . _('Created By') . '</th>
								<th class="ascending">' . _('Sample Date') . '</th>
								<th class="ascending">' . _('Comments') . '</th>
								<th class="ascending">' . _('Cert Allowed') . '</th>
							</tr>';
			echo $TableHeader;
			$j = 1;
			$k = 0; //row colour counter
			while ($myrow = DB_fetch_array($SampleResult)) {
				if ($k == 1) { /*alternate bgcolour of row for highlighting */
					echo '<tr class="EvenTableRows">';
					$k = 0;
				} else {
					echo '<tr class="OddTableRows">';
					$k++;
				}
				$ModifySampleID = $RootPath . '/TestPlanResultsSQAO.php?SelectedSampleID=' . $myrow['sampleid'];
				
				$FormatedSampleDate = ConvertSQLDate($myrow['sampledate']);

				//echo '<div class="centre"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?CopyTest=yes&amp;SelectedSampleID=' .$SelectedSampleID .'">' . _('Copy These Results') . '</a></div>';
				//echo '<div class="centre"><a target="_blank" href="'. $RootPath . '/PDFTestPlan.php?SelectedSampleID=' .$SelectedSampleID .'">' . _('Print Testing Worksheet') . '</a></div>';
				if ($myrow['cert']==1) {
					$CertAllowed='<a target="_blank" href="'. $RootPath . '/PDFCOA.php?LotKey=' .$myrow['lotkey'] .'&ProdSpec=' .$myrow['prodspeckey'] .'">' . _('Yes') . '</a>';
				} else {
					$CertAllowed=_('No');
				}

				echo '<td><a href="' . $ModifySampleID . '">' . str_pad($myrow['sampleid'],10,'0',STR_PAD_LEFT) . '</a></td>
						<td>' . $myrow['prodspeckey'] . '</td>
						<td>' . $myrow['description'] . '</td>
						<td>' . $myrow['lotkey'] . '</td>
						<td>' .  $myrow['identifier']  . '</td>
						<td>' .  $myrow['createdby']  . '</td>
						<td>' . $FormatedSampleDate . '</td>
						<td>' . $myrow['comments'] . '</td>
						<td>' . $CertAllowed . '</td>
						<td><a href="' . $ModifySampleID . '">View</a></td>
						</tr>';
				$j++;
				if ($j == 12) {
					$j = 1;
					//echo $TableHeader;
				}
				//end of page full new headings if
			} //end of while loop
			echo '</table>';
		} // end if Pick Lists to show
echo '</form>';
	include ('includes/footer.inc');
	exit;
} 

echo '<div class="centre"><a href="' . $RootPath . '/TestPlanResultsSQAO.php">' . _('Back to Samples') . '</a></div>';


echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';


$sql = "SELECT prodspeckey,
				description,
				lotkey,
				identifier,
				sampledate,
				comments,
				cert,
				Batch,
				SampleSize
		FROM qasamples 
		LEFT OUTER JOIN stockmaster on stockmaster.stockid=qasamples.prodspeckey
		WHERE sampleid='".$SelectedSampleID."'";

$result = DB_query($sql, $db);
$myrow = DB_fetch_array($result);

if ($myrow['cert']==1){
	$Cert=_('Yes');
} else {
	$Cert=_('No');
}

echo '<input type="hidden" name="SelectedSampleID" value="' . $SelectedSampleID . '" />';
echo '<table class="selection">
		<tr>
			<th>' . _('Sample ID') . '</th>
			<th>' . _('Specification') . '</th>
			<th>' . _('Lot / Serial') . '</th>
			<th>' . _('Batch Size') . '</th>
			<th>' . _('Sample Date') . '</th>
			<th>' . _('Sample Size') . '</th>
			<th>' . _('Comments') . '</th>
			<th>' . _('Used for Cert') . '</th>
		</tr>';
		
echo '<tr class="EvenTableRows"><td>' . str_pad($SelectedSampleID,10,'0',STR_PAD_LEFT)  . '</td>
	<td>' . $myrow['prodspeckey'] . ' - ' . $myrow['description'] . '</td>
	<td>' . $myrow['lotkey'] . '</td>
	<td>' . number_format($myrow['Batch']) . '</td>
	<td>' . ConvertSQLDate($myrow['sampledate']) . '</td>
	<td>' . $myrow['SampleSize'] . '</td>
	<td>' . $myrow['comments'] . '</td>
	<td>' . $Cert . '</td>
	</tr>	</table><br />';
$LotKey=$myrow['lotkey'];
$ProdSpec=$myrow['prodspeckey'];
$CanCert=$myrow['cert'];
$sql = "SELECT sampleid,
				resultid,
				sampleresults.testid,
				qatests.name,
				qatests.method,
				qatests.units,
				qatests.type,
				qatests.numericvalue,
				sampleresults.defaultvalue,
				sampleresults.targetvalue,
				sampleresults.rangemin,
				sampleresults.rangemax,
				sampleresults.testvalue,
				sampleresults.testdate,
				sampleresults.testedby,
				sampleresults.showoncert,
				isinspec,
				sampleresults.manuallyadded,
				groupby
		FROM sampleresults 
		INNER JOIN qatests ON qatests.testid=sampleresults.testid
		WHERE sampleresults.sampleid='".$SelectedSampleID."'
		AND sampleresults.showontestplan='1'
		ORDER BY groupby, name";

$result = DB_query($sql, $db);

echo '<table cellpadding="2" width="90%" class="selection">';
$TableHeader = '<tr>
					<th class="ascending">' . _('Test Name') . '</th>
					<th class="ascending">' . _('Test Method') . '</th>
					<th class="ascending">' . _('Range') . '</th>
					<th class="ascending">' . _('Target Value') . '</th>
										
					<th class="ascending">' . _('Test Result') . '</th>					
					
				</tr>';
	//<th class="ascending">' . _('Test Date') . '</th>
	//<th class="ascending">' . _('Tested By') . '</th>
	//<th class="ascending">' . _('On Cert') . '</th>
echo $TableHeader;
$x = 0;
$k = 0; //row colour counter
$techsql = "SELECT userid,
						realname
					FROM www_users
					INNER JOIN securityroles ON securityroles.secroleid=www_users.fullaccess
					INNER JOIN securitygroups on securitygroups.secroleid=securityroles.secroleid
					WHERE blocked='0'
					AND tokenid='16'";

$techresult = DB_query($techsql, $db);

$groupby = "";
while ($myrow = DB_fetch_array($result)) {

if($groupby != $myrow['groupby']){
echo '<tr><th colspan="9">'.strtoupper($myrow['groupby']).'</th></tr>';
$groupby = $myrow['groupby'];
}
	if ($k == 1) { /*alternate bgcolour of row for highlighting */
		echo '<tr class="EvenTableRows">';
		$k = 0;
	} else {
		echo '<tr class="OddTableRows">';
		$k++;
	}
	$x++;
	$CompareVal='yes';
	$CompareRange='no';
	if ($myrow['targetvalue']=='') {
		$CompareVal='no';
	}
	if ($myrow['type']==4) {
		//$RangeDisplay=$myrow['rangemin'] . '-'  . $myrow['rangemax'] . ' ' . $myrow['units'];
		$RangeDisplay='';
		if ($myrow['rangemin'] > '' OR $myrow['rangemax'] > '') {
			//var_dump($myrow['rangemin']); var_dump($myrow['rangemax']);
			if ($myrow['rangemin'] > '' AND $myrow['rangemax'] == '') {
				$RangeDisplay='> ' . $myrow['rangemin'] . ' ' . $myrow['units'];
			} elseif ($myrow['rangemin']== '' AND $myrow['rangemax'] > '') {
				$RangeDisplay='< ' . $myrow['rangemax'] . ' ' . $myrow['units'];
			} else {
				$RangeDisplay=$myrow['rangemin'] . ' - ' . $myrow['rangemax'] . ' ' . $myrow['units'];
			}
			$CompareRange='yes';
		}
		//$CompareRange='yes';
		$CompareVal='yes';
	} else {
		$RangeDisplay='&nbsp;';
		$CompareRange='no';
	}
	if ($myrow['type']==3) {
		$CompareVal='no';
	}
	if ($myrow['showoncert'] == 1) {
		$ShowOnCertText = _('Yes');
	} else {
		$ShowOnCertText = _('No');
	}
	if ($myrow['testdate']=='0000-00-00'){
		$TestDate=ConvertSQLDate(date('Y-m-d'));
	} else {
		$TestDate=ConvertSQLDate($myrow['testdate']);
	}

	$BGColor='';
	if ($myrow['testvalue']=='') {
		$BGColor=' style="background-color:yellow;" ';
	} else {
		if ($myrow['isinspec']==0) {
		$BGColor=' style="background-color:orange;" ';		
		}
	}
	
	$Class='';
	if ($myrow['numericvalue'] == 1) {
		$Class="number";
	}	
	switch ($myrow['type']) {
		case 0; //textbox
			$TypeDisp='Text Box';
			$TestResult='<input type="text" disabled="true" size="50" maxlength="80" class="' . $Class . '" name="TestValue' .$x .'" value="' . $myrow['testvalue'] . '"' . $BGColor . '/>';
			break;
		case 1; //select box
			$TypeDisp='Select Box';
			$OptionValues = explode(',',$myrow['defaultvalue']);
			$TestResult='<select disabled="true" name="TestValue' .$x .'"' . $BGColor . '/>';
			foreach ($OptionValues as $PropertyOptionValue){
				if ($PropertyOptionValue == $myrow['testvalue']){
					$TestResult.='<option selected="selected" value="' . $PropertyOptionValue . '">' . $PropertyOptionValue . '</option>';
				} else {
					$TestResult.='<option value="' . $PropertyOptionValue . '">' . $PropertyOptionValue . '</option>';
				}
			}
			$TestResult.='</select>';
			break;
		case 2; //checkbox
			$TypeDisp='Check Box';
			break;
		case 3; //datebox
			$TypeDisp='Date Box';
			$Class="date";
			$TestResult='<input type="text" disabled="true" size="10" maxlength="20" class="' . $Class . '" name="TestValue' .$x .'" value="' . $myrow['testvalue'] . '"' . $BGColor . '/>';
			break;
		case 4; //range
			$TypeDisp='Range';
			//$Class="number";
			$TestResult='<input type="text" disabled="true" size="10" maxlength="20" class="' . $Class . '" name="TestValue' .$x .'" value="' . $myrow['testvalue'] . '"' . $BGColor . '/>';
			break;
	} //end switch
	if ($myrow['manuallyadded']==1) {
		$Delete = '<a href="' .htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  .'?Delete=yes&amp;SelectedSampleID=' . $myrow['sampleid'].'&amp;ResultID=' . $myrow['resultid']. '" onclick="return confirm(\'' . _('Are you sure you wish to delete this Test from this Sample ?') . '\');">' . _('Delete').'</a>';
		//echo $myrow['showoncert'];
		$ShowOnCert='<select name="ShowOnCert' .$x .'">';
		if ($myrow['showoncert']==1) {
			$ShowOnCert.= '<option value="1" selected="selected">' . _('Yes') . '</option>';
			$ShowOnCert.= '<option value="0">' . _('No') . '</option>';
		} else {
			$ShowOnCert.= '<option value="0" selected="selected">' . _('No') . '</option>';
			$ShowOnCert.= '<option value="1">' . _('Yes') . '</option>';
		}
		$ShowOnCert.='</select>';
	} else {
		$Delete ='';
		$ShowOnCert='<input type="hidden" name="ShowOnCert' .$x .'" value="' . $myrow['showoncert'] . '" />' .$ShowOnCertText;
	}
	if ($myrow['testedby']=='') {
		$myrow['testedby']=$_SESSION['UserID'];
	}
	echo '<td><input type="hidden" name="ResultID' .$x. '" value="' . $myrow['resultid'] . '" /> ' . $myrow['name'] . '
			<input type="hidden" name="ExpectedValue' .$x. '" value="' . $myrow['targetvalue'] . '" /> 
			<input type="hidden" name="MinVal' .$x. '" value="' . $myrow['rangemin'] . '" /> 
			<input type="hidden" name="MaxVal' .$x. '" value="' . $myrow['rangemax'] . '" /> 
			<input type="hidden" name="CompareRange' .$x. '" value="' . $CompareRange . '" /> 
			<input type="hidden" name="CompareVal' .$x. '" value="' . $CompareVal . '" /> 
			</td>
			<td>' . $myrow['method'] . '</td>
			<td>' . $RangeDisplay . '</td>
			<td>' . $myrow['targetvalue'] . ' ' . $myrow['units'] . '</td>';
	echo '<td>' . $TestResult . '</td>
		</tr>';
}
	
echo '</table>';
$result = DB_query("SELECT remark,approver_title,approver_name,remarkdate FROM qrsampleremarks
						WHERE sampleid = '".$SelectedSampleID."' ORDER BY approver_level ASC",	$db);
echo '<table>';
$tech = DB_query("SELECT technician,realname FROM qasampletechnicians
						INNER JOIN www_users ON qasampletechnicians.technician = www_users.userid
						WHERE sampleidno = '".$SelectedSampleID."'",	$db);
if(DB_num_rows($tech) >0){
echo '<tr><td colspan="3"><table style="background-color:#fff"><tr><th colspan="2">Quality Assurance Technicians</th></tr>';
$no=1;
while($mya = DB_fetch_array($tech)){
echo '<tr><td>'.$no.'</td><td>'.$mya['technician'].' - '.$mya['realname'].'</td></tr>';
$no++;
}
echo '</table></td></tr>';
}
while($my = DB_fetch_array($result)){
echo '<tr><td>'.$my['approver_title'].'</td><td colspan="2"><textarea name="remark1" disabled="true" cols="80" rows="1">'.$my['remark'].'</textarea></td></tr>';
echo '<tr><td></td>
		<td><strong style="color: #999999; font-size:12px;">Remarks From :</strong> <a style="text-decoration:none" href="#">' . $my['approver_name'] . '</a></td>
		<td class="number"><span style="color: #999999; font-size:12px;">' .  calculate_time_span($my['remarkdate']) . '</span></td>
	</tr>';
}
$result = DB_query("SELECT remark,approver_title FROM qrsampleremarks
						WHERE sampleid = '".$SelectedSampleID."' AND approver_level=2",	$db);
						$mys = DB_fetch_row($result);
echo '<tr><td>Remarks</td><td  colspan="2">'.($mys[0] !="" && !isset($_GET['Result1']) ? '<textarea name="remark1" hidden="true" cols="80" rows="1">'.$mys[0].'</textarea> <a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Result1=yes&amp;SelectedSampleID=' .$SelectedSampleID .'">Edit Remarks</a>' : '<textarea name="remark1" cols="80" rows="2">'.$mys[0].'</textarea>').'</td></tr>
</table>';
echo '<div class="centre">
		<input type="hidden" name="TestResultsCounter" value="' . $x . '" />
		<input type="submit" name="submit" value="' . _('Enter Information') . '" />
	</div>
	</div>
	</form>';

if ($CanCert==1){
	echo '<div class="centre"><a target="_blank" href="'. $RootPath . '/PDFCOA.php?LotKey=' .$LotKey .'&ProdSpec=' . $ProdSpec. '">' . _('Print COA') . '</a></div>';
}

include('includes/footer.inc');
?>