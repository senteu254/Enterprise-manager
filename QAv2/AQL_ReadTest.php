<?php
/* $Id: TestPlanResults.php 1 2014-09-08 10:42:50Z agaluski $*/

if (isset($_GET['SelectedSampleID'])){
	$SelectedSampleID =mb_strtoupper($_GET['SelectedSampleID']);
} elseif(isset($_POST['SelectedSampleID'])){
	$SelectedSampleID =mb_strtoupper($_POST['SelectedSampleID']);
}


if (isset($Errors)) {
	unset($Errors);
}

$Errors = array();

if (isset($_GET['ListTests'])) {
	$sql = "SELECT qatests.testid,
				name,
				method,
				units,
				type,
				numericvalue,
				qatests.defaultvalue
			FROM qatests
			LEFT JOIN sampleresults 
			ON sampleresults.testid=qatests.testid
			AND sampleresults.sampleid='".$SelectedSampleID."'
			WHERE qatests.active='1'
			AND sampleresults.sampleid IS NULL";
	$result = DB_query($sql,$db);
	echo '<form method="post" action="" onsubmit="return document.getElementById(\'loadingbackground\').style.display = \'block\';">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table class="table">';
	echo '<tr>
			<th>' . _('Add') . '</th>
			<th>' . _('Name') . '</th>
			<th>' . _('Method') . '</th>
			<th>' . _('Target Value') . '</th>
			<th>' . _('Range Min') . '</th>
			<th>' . _('Range Max') . '</th>
		</tr>';
	$k=0;
	$x=0;
	while ($myrow=DB_fetch_array($result)) {

	if ($k==1){
		echo '<tr>';
		$k=0;
	} else {
		echo '<tr>';
		$k++;
	}
	$x++;
	$Class='';
	$RangeMin='';
	$RangeMax='';
	if ($myrow['numericvalue'] == 1) {
		$IsNumeric = _('Yes');
		$Class="number";
	} else {
		$IsNumeric = _('No');
	}
	
	switch ($myrow['type']) {
	 	case 0; //textbox
	 		$TypeDisp='Text Box';
	 		break;
	 	case 1; //select box
	 		$TypeDisp='Select Box';
			break;
		case 2; //checkbox
			$TypeDisp='Check Box';
			break;
		case 3; //datebox
			$TypeDisp='Date Box';
			$Class="date";
			break;
		case 4; //range
			$TypeDisp='Range';
			$RangeMin='<input  class="' .$Class. '" type="text" name="AddRangeMin' .$x.'" />';
			$RangeMax='<input  class="' .$Class. '" type="text" name="AddRangeMax' .$x.'" />';
			break;
	} //end switch
	printf('<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			</tr>',
			'<input type="checkbox" name="AddRow' .$x.'"><input type="hidden" name="AddTestID' .$x.'" value="' .$myrow['testid']. '">',
			$myrow['name'],
			$myrow['method'],
			'<input  class="' .$Class. '" type="text" name="AddTargetValue' .$x.'" />',
			$RangeMin,
			$RangeMax);

	} //END WHILE LIST LOOP
	
	echo '</table><br /></div>	
			<div class="centre">
				<input type="hidden" name="SelectedSampleID" value="' . $SelectedSampleID . '" />
				<input type="hidden" name="AddTestsCounter" value="' . $x . '" />
				<input type="submit" name="AddTests" value="' . _('Add') . '" />
		</div></form>';
	include('includes/footer.inc');
	exit;
}  //ListTests
if (isset($_POST['AddTests'])) {
	for ($i=0;$i<=$_POST['AddTestsCounter'];$i++){
		if ($_POST['AddRow' .$i]=='on') {
				if ($_POST['AddRangeMin' .$i]=='') {
				$AddRangeMin="NULL";
			} else {
				$AddRangeMin="'" . $_POST['AddRangeMin' .$i] . "'";
			}
			if ($_POST['AddRangeMax' .$i]=='') {
				$AddRangeMax="NULL";
			} else {
				$AddRangeMax="'" . $_POST['AddRangeMax' .$i] . "'";
			}
			$sql = "INSERT INTO sampleresults 
							(sampleid,
							testid,
							defaultvalue,
							targetvalue,
							rangemin,
							rangemax,
							showoncert,
							showontestplan,
							manuallyadded)
						SELECT '"  . $SelectedSampleID . "',
								testid, 
								defaultvalue, 
								'"  .  $_POST['AddTargetValue' .$i] . "',
								"  . $AddRangeMin . ",
								"  . $AddRangeMax . ",
								showoncert, 
								'1',
								'1'
						FROM qatests WHERE testid='" .$_POST['AddTestID' .$i]. "'";
			$msg = _('A Sample Result record has been added for Test ID') . ' ' . $_POST['AddTestID' .$i]  . ' for ' . ' ' . $KeyValue ;
			$ErrMsg = _('The insert of the Sample Result failed because');
			$DbgMsg = _('The SQL that was used and failed was');
			$result = DB_query($sql,$db,$ErrMsg, $DbgMsg);
			prnMsg($msg , 'success');
		} //if on
	} //for
} //AddTests

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;
	$msg ='';
	for ($i=1;$i<=$_POST['TestResultsCounter'];$i++){
		$IsInSpec=1;
		//var_dump($_POST['CompareVal' .$i]); var_dump($_POST['CompareRange' .$i]);
		if ($_POST['CompareVal' .$i]=='yes'){
			if ($_POST['CompareRange' .$i]=='yes'){
				//if (($_POST['TestValue' .$i]<>$_POST['ExpectedValue' .$i]) AND ($_POST['TestValue' .$i]<$_POST['MinVal' .$i] OR $_POST['TestValue' .$i] > $_POST['MaxVal' .$i])) {
				//	$IsInSpec=0;
				//}
				if ($_POST['MinVal' .$i] > '' AND $_POST['MaxVal' .$i] > '') {
					if (($_POST['TestValue' .$i]<>$_POST['ExpectedValue' .$i]) AND ($_POST['TestValue' .$i]<$_POST['MinVal' .$i] OR $_POST['TestValue' .$i] > $_POST['MaxVal' .$i])) {
						//echo "one";
						$IsInSpec=0;
					}
				} elseif ($_POST['MinVal' .$i] > '' AND $_POST['MaxVal' .$i] == '') {
					if (($_POST['TestValue' .$i]<>$_POST['ExpectedValue' .$i]) AND ($_POST['TestValue' .$i] <= $_POST['MinVal' .$i])) {
						//echo "two";
						$IsInSpec=0;
					}
				} elseif ($_POST['MinVal' .$i] == '' AND $_POST['MaxVal' .$i] > '') {
					if (($_POST['TestValue' .$i]<>$_POST['ExpectedValue' .$i]) AND ($_POST['TestValue' .$i] >= $_POST['MaxVal' .$i])) {
						//echo "three";
						$IsInSpec=0;
					}
				}	
				//echo "four";
				//var_dump($_POST['TestValue' .$i]); var_dump($_POST['ExpectedValue' .$i]); var_dump($_POST['MinVal' .$i]); var_dump($_POST['MaxVal' .$i]); var_dump($IsInSpec);	
			} else {
				if (($_POST['TestValue' .$i]<>$_POST['ExpectedValue' .$i])) {
					$IsInSpec=0;
				}
			}
		}
		
		if(is_array($_POST['TestValue' .$i])==1){
		$TestVal ='';
		foreach($_POST['TestValue' .$i] as $val){
		$TestVal .= $val.',';
		}
		$TestValue = rtrim($TestVal,',');
		}else{
		$TestValue = $_POST['TestValue' .$i];
		}
		
		$sql = "UPDATE sampleresults SET testedby='".  $_SESSION['UserID'] . "',
										testdate='". date('Y-m-d') . "',
										testvalue='".  $TestValue . "',
										showoncert='1',
										isinspec='".  $IsInSpec . "'
						WHERE resultid='".  $_POST['ResultID' .$i] . "'";
		
		$ErrMsg = _('The updated of the sampleresults failed because');
		$DbgMsg = _('The SQL that was used and failed was');
		$result = DB_query($sql,$db,$ErrMsg, $DbgMsg);
		//prnMsg($msg , 'success');
	} //for
$_SESSION['msg'] = _('Sample Results were updated Successfully') . '';
	if(!isset($_POST['remark1'])){
	$_POST['remark1'] = "";
	}
	
	if(isset($_POST['agree']) && $_POST['agree'] !=""){
	$sqli = "INSERT INTO qasampletechnicians (sampleidno,technician)
							 VALUES('".$SelectedSampleID."',
							 		'" . $_POST['agree'] . "')";
		$ErrMsg = _('The update of the QA Sample Remarks failed because');
		$DbgMsg = _('The SQL that was used and failed was');
		$res = DB_query($sqli,$db,$ErrMsg, $DbgMsg);
	}
	$resl = DB_query("SELECT accept_msg,reject_msg FROM qa_approval_levels
						WHERE type = 1 AND levelcheck=0",	$db);
	$myl = DB_fetch_row($resl);	
	$result = DB_query("SELECT count(sampleid) FROM qrsampleremarks
						WHERE sampleid = '".$SelectedSampleID."' AND approver_level=1",	$db);
	$myrow = DB_fetch_row($result);
	if(isset($_POST['remark1']) && $_POST['remark1']!=""){
/*	if($_POST['EditRemarks']=='Yes') {
		$sql = "UPDATE qrsampleremarks SET remark='" . $_POST['remark1'] . "'
				WHERE sampleid = '".$SelectedSampleID."' AND approver_level=1 AND approver_name='" . $_SESSION['UsersRealName'] . "'";
		$ErrMsg = _('The update of the QA Sample Remarks failed because');
		$DbgMsg = _('The SQL that was used and failed was');
		$result = DB_query($sql,$db,$ErrMsg, $DbgMsg);
	}else{*/
	$sql = "INSERT INTO qrsampleremarks (sampleid,remark,approver_level,approver_title,approver_name)
							 VALUES('".$SelectedSampleID."',
							 		'" . $_POST['remark1'] . "',
									'1',
									'".$myl[0]."',
									'" . $_SESSION['UsersRealName'] . "')";
		$ErrMsg = _('The update of the QA Sample Remarks failed because');
		$DbgMsg = _('The SQL that was used and failed was');
		$result = DB_query($sql,$db,$ErrMsg, $DbgMsg);
	//}
	}

}
if (isset($_GET['Delete'])) {
	$sql= "SELECT COUNT(*) FROM sampleresults WHERE sampleresults.resultid='".$_GET['ResultID']."'
											AND sampleresults.manuallyadded='1'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]==0) {
		$_SESSION['msg'] = _('Cannot delete this Result ID because it is a part of the Product Specification');
	} else {
		$sql="DELETE FROM sampleresults WHERE resultid='". $_GET['ResultID']."'";
		$ErrMsg = _('The sample results could not be deleted because');
		$result = DB_query($sql,$db,$ErrMsg);
		
		$_SESSION['msg'] = _('Result QA Sample') . ' ' . $_GET['ResultID'] . _('has been deleted from the database');
		unset($_GET['ResultID']);
		unset($delete);
		unset ($_GET['delete']);
	}
}

if(isset($_POST['AddTech']) && $_POST['AddTech'] !=""){
	$sqli = "INSERT INTO qasampletechnicians (sampleidno,technician)
							 VALUES('".$SelectedSampleID."',
							 		'" . $_POST['userx'] . "')";
		$ErrMsg = _('The update of the QA Sample Remarks failed because');
		$DbgMsg = _('The SQL that was used and failed was');
		$res = DB_query($sqli,$db,$ErrMsg, $DbgMsg);
		$_SESSION['msg'] = _('Technician') . ' ' . $_GET['DeleteTech'] . _('has been Added from the List');
	}
	
if(isset($_GET['DeleteTech']) && $_GET['DeleteTech'] !=""){
	$sqli = "DELETE FROM qasampletechnicians WHERE technician ='".$_GET['DeleteTech']."' AND sampleidno='".$SelectedSampleID."'";
		$ErrMsg = _('The update of the QA Sample Remarks failed because');
		$DbgMsg = _('The SQL that was used and failed was');
		$res = DB_query($sqli,$db,$ErrMsg, $DbgMsg);
	$_SESSION['msg'] = _('Technician') . ' ' . $_GET['DeleteTech'] . _('has been deleted from the list');
	unset($_GET['DeleteTech']);	
	}
	
if (isset($_POST['ForwardRecord'])) {
if(isset($_POST['agree']) && $_POST['agree'] !=""){
	$sqli = "INSERT INTO qasampletechnicians (sampleidno,technician)
							 VALUES('".$SelectedSampleID."',
							 		'" . $_POST['agree'] . "')";
		$ErrMsg = _('The update of the QA Sample Remarks failed because');
		$DbgMsg = _('The SQL that was used and failed was');
		$res = DB_query($sqli,$db,$ErrMsg, $DbgMsg);
	}
	if(isset($_POST['remark1']) && $_POST['remark1']!=""){
	$resl = DB_query("SELECT accept_msg,reject_msg FROM qa_approval_levels
						WHERE type = 1 AND levelcheck=0",	$db);
	$myl = DB_fetch_row($resl);	
	$sql = "INSERT INTO qrsampleremarks (sampleid,remark,approver_level,approver_title,approver_name)
							 VALUES('".$SelectedSampleID."',
							 		'" . $_POST['remark1'] . "',
									'1',
									'".$myl[0]."',
									'" . $_SESSION['UsersRealName'] . "')";
		$ErrMsg = _('The update of the QA Sample Remarks failed because');
		$DbgMsg = _('The SQL that was used and failed was');
		$result = DB_query($sql,$db,$ErrMsg, $DbgMsg);
	}
	$reslx = DB_query("SELECT rejected FROM qasamples
						WHERE sampleid= '". $SelectedSampleID . "'",	$db);
	$mylx = DB_fetch_row($reslx);	
	if($mylx[0]==1){
	$sql = "UPDATE qasamples SET process_level=level_rejected, rejected=0
					WHERE sampleid = '". $SelectedSampleID . "'";
		$result = DB_query($sql);
	}else{	
	$sql = "UPDATE qasamples SET process_level='1', rejected=0
					WHERE sampleid = '". $SelectedSampleID . "'";
		$result = DB_query($sql);
		}
		$_SESSION['msg'] = '' . _('Success: AQL No. '). $SelectedSampleID . ' ' . _('has been forwarded for authoritation'). '';
	
}

echo '<div id="loadingbackground">';
		echo '<div id="progressBar" ><br />
			 <i class="fa fa-spinner fa-pulse fa-4x fa-fw"></i><br>PROCESSING. PLEASE WAIT...
			</div>';
		echo '</div>';

error_reporting( error_reporting() & ~E_NOTICE ); if(!empty($_SESSION['msg'])) echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success</h4>
                ' . ucwords($_SESSION['msg']). '
              </div>'; unset($_SESSION['msg']); 
			 if(!empty($_SESSION['errmsg'])) echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>
                ' . ucwords($_SESSION['errmsg']). '
              </div>'; unset($_SESSION['errmsg']); 

if (!isset($SelectedSampleID)) {
	echo '<div class="centre">
			<a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&Link=AQL">' .  _('Select a sample to enter results against') . '</a>
		</div>';
	prnMsg(_('This page can only be opened if a QA Sample has been selected. Please select a sample first'),'info');
	include ('includes/footer.inc');
	exit;
} 

echo '<div class="centre"><a class="btn btn-default" href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&Link=AQL">' . _('Back to Samples') . '</a></div>';

echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&Link=AQLReadTest&amp;SelectedSampleID='.$SelectedSampleID.'" onsubmit="return document.getElementById(\'loadingbackground\').style.display = \'block\';">';
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
				SampleSize,
				process_level,
				sampleid
		FROM qasamples 
		LEFT OUTER JOIN stockmaster on stockmaster.stockid=qasamples.prodspeckey
		WHERE sampleid='".$SelectedSampleID."'";

$result = DB_query($sql, $db);
$myrow = $myx = DB_fetch_array($result);

if ($myrow['cert']==1){
	$Cert=_('Yes');
} else {
	$Cert=_('No');
}

$userx = DB_query("SELECT userid,realname FROM www_users",	$db);
$tech = DB_query("SELECT technician,realname FROM qasampletechnicians
						INNER JOIN www_users ON qasampletechnicians.technician = www_users.userid
						WHERE sampleidno = '".$SelectedSampleID."'",	$db);

echo '<input type="hidden" name="SelectedSampleID" value="' . $SelectedSampleID . '" />';
echo '<table class="table">
		<tr>
			<th>' . _('Sample ID') . '</th>
			<th>' . _('Specification') . '</th>
			<th>' . _('Lot / Serial') . '</th>
			<th>' . _('Batch Size') . '</th>
			<th>' . _('Sample Date') . '</th>
			<th>' . _('Sample Size') . '</th>
		</tr>';
		
echo '<tr class="EvenTableRows"><td>' . str_pad($SelectedSampleID,10,'0',STR_PAD_LEFT)  . '</td>
	<td>' . $myrow['prodspeckey'] . ' - ' . $myrow['description'] . '</td>
	<td>' . $myrow['lotkey'] . '</td>
	<td>' . $myrow['Batch'] . '</td>
	<td>' . ConvertSQLDate($myrow['sampledate']) . '</td>
	<td>' . $myrow['SampleSize'] . '</td>
	</tr>';
	if(DB_num_rows($tech) >0){
echo '<tr> ';
/*echo '<td colspan="2" width="50%"><table class="table" style="width:100%"><tr><td><select name="userx" >';
while($myus = DB_fetch_array($userx)){
echo '<option value="'.$myus['userid'].'">'.$myus['userid'].' - '.$myus['realname'].'</option>';
}
echo '</select></td></tr>';
echo '<tr><td><input name="AddTech" type="submit" value="Add Technician" /></td></tr></table></td>';*/
echo '<td colspan="6" width="50%"><table class="table"  style="width:100%"><tr><th colspan="2">Quality Assurance Technicians</th></tr>';
$no=1;
while($mya = DB_fetch_array($tech)){
echo '<tr><td>'.$no.'</td><td>'.$mya['technician'].' - '.$mya['realname'].'</td>';
/*echo '<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&Link=AQLReadTest&amp;SelectedSampleID='.$SelectedSampleID.'&amp;DeleteTech='.$mya['technician'].'" onclick="return confirm(\'Are you absolutely sure you want to Delete this User?\')" title="Delete" style="color:red;"><i class="fa fa-trash"></i></a></td>';*/
echo '</tr>';
$no++;
}
echo '</table></td></tr>';
}	
echo '</table><br />';
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

echo '<table cellpadding="2" width="90%" class="table">';
$TableHeader = '<tr>
					<th>' . _('Test Name') . '</th>
					<th>' . _('Method') . '</th>
					<th>' . _('Test Result') . '</th>	
				</tr>';
	//<th class="ascending">' . _('Test Date') . '</th>
	//<th class="ascending">' . _('Tested By') . '</th>
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
$ForwardAction = 0;
while ($myrow = DB_fetch_array($result)) {

if($groupby != $myrow['groupby']){
echo '<tr><th colspan="3">'.strtoupper($myrow['groupby']).'</th></tr>';
$groupby = $myrow['groupby'];
$title =1;
}
	if ($k == 1) { /*alternate bgcolour of row for highlighting */
		echo '<tr>';
		$k = 0;
	} else {
		echo '<tr>';
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
		$ForwardAction = 1;
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
			$TestResult='<input type="text" size="30" maxlength="50" class="' . $Class . '" name="TestValue' .$x .'" value="' . $myrow['testvalue'] . '"' . $BGColor . '/>';
			break;
		case 1; //select box
			$TypeDisp='Select Box';
			$OptionValues = explode(',',$myrow['defaultvalue']);
			$TestResult='<select name="TestValue' .$x .'"' . $BGColor . '/>';
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
			$TestResult='<input type="text" size="10" maxlength="20" class="' . $Class . '" name="TestValue' .$x .'" value="' . $myrow['testvalue'] . '"' . $BGColor . '/>';
			break;
		case 4; //range
			$TypeDisp='Range';
			//$Class="number";
			$TestResult='<input type="text" size="10" maxlength="20" class="' . $Class . '" name="TestValue' .$x .'" value="' . $myrow['testvalue'] . '"' . $BGColor . '/>';
			break;
	} //end switch
	
	if ($myrow['testedby']=='') {
		$myrow['testedby']=$_SESSION['UserID'];
	}
	echo '<input type="hidden" name="ResultID' .$x. '" value="' . $myrow['resultid'] . '" /> 
			<input type="hidden" name="ExpectedValue' .$x. '" value="' . $myrow['targetvalue'] . '" /> 
			<input type="hidden" name="MinVal' .$x. '" value="' . $myrow['rangemin'] . '" /> 
			<input type="hidden" name="MaxVal' .$x. '" value="' . $myrow['rangemax'] . '" /> 
			<input type="hidden" name="CompareRange' .$x. '" value="' . $CompareRange . '" /> 
			<input type="hidden" name="CompareVal' .$x. '" value="' . $CompareVal . '" /> ';
	if($ProdSpec=="KOFC54030601" && $groupby =='Hardness Test'){
$hardness = array(_('SAMPLE'),_('38mm'),_('47mm'),_('57mm'),_('67mm'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="12" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
for($columns=0; $columns< 4; $columns++){
echo '<input size="12" maxlength="80" name="TestValue' .$x .'[]" value="' . $hard[$columns] . '" ' . $BGColor . '/>';
}
echo '</td>';

}elseif($ProdSpec=="KOFC54030601" && $groupby =='Wall Thickness Test'){
$hardness = array(_('SAMPLE'),_('6.7mm'),_('37.7mm'),_('Neck'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="12" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
for($columns=0; $columns< 3; $columns++){
echo '<input size="12" maxlength="80" name="TestValue' .$x .'[]" value="' . $hard[$columns] . '" ' . $BGColor . '/>';
}
echo '</td>';

}elseif($ProdSpec=="KOFC54030602" && $groupby =='Hardness Test'){
$hardness = array(_('SAMPLE'),_('8mm'),_('29mm'),_('35mm'),_('38mm'),_('47mm'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="12" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
for($columns=0; $columns< 5; $columns++){
echo '<input size="12" maxlength="80" name="TestValue' .$x .'[]" value="' . $hard[$columns] . '" ' . $BGColor . '/>';
}
echo '</td>';

}elseif($ProdSpec=="KOFC54030602" && $groupby =='Wall Thickness Test'){
$hardness = array(_('SAMPLE'),_('6.7mm'),_('12.7mm'),_('20.7mm'),_('37.7mm'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="12" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
for($columns=0; $columns< 4; $columns++){
echo '<input size="12" maxlength="80" name="TestValue' .$x .'[]" value="' . $hard[$columns] . '" ' . $BGColor . '/>';
}
echo '</td>';

}elseif($ProdSpec=="KOFC54030603" && $groupby =='Hardness Test'){
$hardness = array(_('SAMPLE'),_('6mm'),_('10mm'),_('14mm'),_('18mm'),_('22mm'),_('27mm'),_('35mm'),_('42mm'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="6" value="' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="6" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
for($columns=0; $columns< 8; $columns++){
echo '<input size="6" maxlength="80" name="TestValue' .$x .'[]" value="' . $hard[$columns] . '" ' . $BGColor . '/>';
}
echo '</td>';

}elseif($ProdSpec=="KOFC54030603" && $groupby =='Wall Thickness Test'){
$hardness = array(_('SAMPLE'),_('7mm'),_('14mm'),_('25mm'),_('35mm'),_('Mouth'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="12" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
for($columns=0; $columns< 5; $columns++){
echo '<input size="12" maxlength="80" name="TestValue' .$x .'[]" value="' . $hard[$columns] . '" ' . $BGColor . '/>';
}
echo '</td>';

}elseif($ProdSpec=="KOFC54030604" && $groupby =='Hardness Test'){
$hardness = array(_('SAMPLE'),_('6mm'),_('10mm'),_('14mm'),_('18mm'),_('22mm'),_('27mm'),_('35mm'),_('42mm'),_('46mm'),_('50mm'),_('54mm'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="5" value="' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="5" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
for($columns=0; $columns< 11; $columns++){
echo '<input size="5" maxlength="80" name="TestValue' .$x .'[]" value="' . $hard[$columns] . '" ' . $BGColor . '/>';
}
echo '</td>';

}elseif($ProdSpec=="KOFC54030604" && $groupby =='Wall Thickness Test'){
$hardness = array(_('SAMPLE'),_('7mm'),_('14mm'),_('25mm'),_('35mm'),_('Mouth'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="12" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
for($columns=0; $columns< 5; $columns++){
echo '<input size="12" maxlength="80" name="TestValue' .$x .'[]" value="' . $hard[$columns] . '" ' . $BGColor . '/>';
}
echo '</td>';

}elseif($ProdSpec=="KOFC54030605" && $groupby =='Hardness Test'){
$hardness = array(_('SAMPLE'),_('6mm'),_('10mm'),_('17mm'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="12" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
for($columns=0; $columns< 3; $columns++){
echo '<input size="12" maxlength="80" name="TestValue' .$x .'[]" value="' . $hard[$columns] . '" ' . $BGColor . '/>';
}
echo '</td>';

}elseif($ProdSpec=="KOFC54030605" && $groupby =='Wall Thickness Test'){
$hardness = array(_('SAMPLE'),_('10mm'),_('18.64mm'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="12" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
for($columns=0; $columns< 2; $columns++){
echo '<input size="12" maxlength="80" name="TestValue' .$x .'[]" value="' . $hard[$columns] . '" ' . $BGColor . '/>';
}
echo '</td>';

}else{
	echo '<td>' . $myrow['name'] . '</td>';
	echo '<td>' . $myrow['method'] . '</td>';
	echo '<td>' . $TestResult . '</td>';
	}
	echo '</tr>';
}
	
echo '</table>';
$result = DB_query("SELECT remark,approver_title,approver_name,remarkdate FROM qrsampleremarks
						WHERE sampleid = '".$SelectedSampleID."' ORDER BY approver_level ASC",	$db);
						
$tech1 = DB_query("SELECT technician FROM qasampletechnicians
						WHERE sampleidno = '".$SelectedSampleID."' AND technician='".$_SESSION['UserID']."'",	$db);

echo '<table class="table" style="width:100%;">';
while($my = DB_fetch_array($result)){
echo' <tr>
				<td align="center" width="70"><img class="image" src="images/image.jpg"  /></td>
			<td><div class="bubble"><span class="time">' .  strtoupper($my['approver_title'])  . '<br />From: <a href="#">'.$my['approver_name'].'</a></span> 
			<br /> '.$my['remark'].'<br /><span class="time">'. calculate_time_span($my['remarkdate']) .'</span>
				</div>
			</td>
			  </tr>';
}
DB_data_seek($result,0);
$result2 = DB_query("SELECT remark,approver_title FROM qrsampleremarks
						WHERE sampleid = '".$SelectedSampleID."' AND approver_level=1 AND approver_name='".$_SESSION['UsersRealName']."'",	$db);
						$mys = DB_fetch_row($result2);
echo '<tr><td>Remarks</td><td>'.($mys[0] !="" && !isset($_GET['Result1']) ? '' : '<input name="EditRemarks" type="hidden" value="'.(isset($_GET['Result1'])? 'Yes':'No').'" /><textarea required name="remark1" cols="80" style="width:90%" rows="2">'.$mys[0].'</textarea>').'</td></tr>';
if(DB_num_rows($tech1) ==0){
echo '<tr><td></td><td ><input name="agree" required type="checkbox" value="'.$_SESSION['UserID'].'" /> I <b>'.$_SESSION['UsersRealName'].'</b> Agree that I was Part of this Quality Assurance Technicians perform this tests.</td></tr>';
}
echo '</table>';
echo '<div class="centre">
		<input type="hidden" name="TestResultsCounter" value="' . $x . '" />
		<input type="submit" name="submit" value="' . _('Enter Information') . '" />
	</div>
	</div>';
	
            echo '<div class="box-footer">
			 <div class="pull-right">';
			  if($myx['process_level']==0){
			  if($ForwardAction ==0){
			echo '<button type="submit" name="ForwardRecord" onclick="return confirm(\'Are you sure you want to Forward this Request?\')" class="btn btn-success"><i class="fa fa-share"></i> Forward</button>';
				}
			 } 
			echo '</div>
			  <a href="'. $RootPath . '/PDFCOA.php?LotKey=' .$myx['lotkey'] .'&ProdSpec=' .$myx['prodspeckey'] .'&QASampleID=' .$myx['sampleid'].'" target="_blank"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>
            </div>';
	
	echo '</form>';

//echo '<div class="centre"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&amp;Link=AQLReadTest&amp;ListTests=yes&amp;SelectedSampleID=' .$SelectedSampleID .'">' . _('Add More Tests') . '</a></div>';

?>
<style type="text/css">
#results {height:135px; width:120px; border-radius:8px 8px 8px 8px; background:#ccc;}
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