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

$Errors = array();

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/maintenance.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p>';
if (isset($_GET['CopyResults']) OR isset($_POST['CopyResults'])) {
	if (!isset($_POST['CopyToSampleID']) OR $_POST['CopyToSampleID']=='' OR !isset($_POST['Copy'])) {
		echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">
			<div>
			<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
			<input type="hidden" name="SelectedSampleID" value="' . $SelectedSampleID . '" />
			<input type="hidden" name="CopyResults" value="CopyResults" />';
		if (isset($_POST['ResetPart'])) {
			unset($SelectedStockItem);
		}
		
		if (isset($SampleID) AND $SampleID != '') {
			if (!is_numeric($SampleID)) {
				prnMsg(_('The Sample ID entered') . ' <U>' . _('MUST') . '</U> ' . _('be numeric'), 'error');
				unset($SampleID);
			} else {
				echo _('Sample ID') . ' - ' . $SampleID;
			}
		}
		if (!Is_Date($_POST['FromDate'])) {
			$InputError = 1;
			prnMsg(_('Invalid From Date'),'error');
			$_POST['FromDate']=Date(($_SESSION['DefaultDateFormat']), strtotime($UpcomingDate . ' - 15 days'));
		} 
		if (!Is_Date($_POST['ToDate'])) {
			$InputError = 1;
			prnMsg(_('Invalid To Date'),'error');
			$_POST['ToDate'] = Date($_SESSION['DefaultDateFormat']);
		} 
		if (isset($_POST['SearchParts'])) {
			if ($_POST['Keywords'] AND $_POST['StockCode']) {
				prnMsg(_('Stock description keywords have been used in preference to the Stock code extract entered'), 'info');
			}
			if ($_POST['Keywords']) {
				//insert wildcard characters in spaces
				$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.decimalplaces,
						SUM(locstock.quantity) as qoh,
						stockmaster.units,
					FROM stockmaster INNER JOIN locstock
					ON stockmaster.stockid = locstock.stockid 
					INNER JOIN locationusers ON locationusers.loccode = locstock.loccode
							AND locationusers.userid='" .  $_SESSION['UserID'] . "'
							AND locationusers.canview=1
					WHERE stockmaster.description " . LIKE  . " '" . $SearchString ."'
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					GROUP BY stockmaster.stockid,
						stockmaster.description,
						stockmaster.decimalplaces,
						stockmaster.units
					ORDER BY stockmaster.stockid";
			} elseif ($_POST['StockCode']) {
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.decimalplaces,
						SUM(locstock.quantity) AS qoh,
						stockmaster.units
					FROM stockmaster INNER JOIN locstock
						ON stockmaster.stockid = locstock.stockid
					INNER JOIN locationusers ON locationusers.loccode = locstock.loccode
							AND locationusers.userid='" .  $_SESSION['UserID'] . "'
							AND locationusers.canview=1
					WHERE stockmaster.stockid " . LIKE  . " '%" . $_POST['StockCode'] . "%'
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					GROUP BY stockmaster.stockid,
						stockmaster.description,
						stockmaster.decimalplaces,
						stockmaster.units
					ORDER BY stockmaster.stockid";
			} elseif (!$_POST['StockCode'] AND !$_POST['Keywords']) {
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.decimalplaces,
						SUM(locstock.quantity) AS qoh,
						stockmaster.units
					FROM stockmaster INNER JOIN locstock ON stockmaster.stockid = locstock.stockid
					INNER JOIN locationusers ON locationusers.loccode = locstock.loccode
							AND locationusers.userid='" .  $_SESSION['UserID'] . "'
							AND locationusers.canview =1
					WHERE stockmaster.categoryid='" . $_POST['StockCat'] . "'
					GROUP BY stockmaster.stockid,
						stockmaster.description,
						stockmaster.decimalplaces,
						stockmaster.units
					ORDER BY stockmaster.stockid";
			}
			
			$ErrMsg = _('No stock items were returned by the SQL because');
			$DbgMsg = _('The SQL used to retrieve the searched parts was');
			$StockItemsResult = DB_query($SQL, $db, $ErrMsg, $DbgMsg);
		}

		if (true or !isset($LotNumber) or $LotNumber == "") { //revisit later, right now always show all inputs
			echo '<table class="selection"><tr><td>';
			if (isset($SelectedStockItem)) {
				echo _('For the part') . ':<b>' . $SelectedStockItem . '</b> ' . _('and') . ' <input type="hidden" name="SelectedStockItem" value="' . $SelectedStockItem . '" />';
			}
			echo _('Lot Number') . ': <input name="LotNumber" autofocus="autofocus" maxlength="20" size="12" value="' . $LotNumber . '"/> ' . _('Sample ID') . ': <input name="SampleID" maxlength="10" size="10" value="' . $SampleID . '"/> ';
			echo _('From Sample Date') . ': <input name="FromDate" size="10" class="date" value="' . $_POST['FromDate'] . '"/> ' . _('To Sample Date') . ': <input name="ToDate" size="10" class="date" value="' . $_POST['ToDate'] . '"/> ';
			echo '<input type="submit" name="SearchSamples" value="' . _('Search Samples') . '" /></td>
				</tr>
				</table>';
		}
		$SQL = "SELECT categoryid,
					categorydescription
				FROM stockcategory
				ORDER BY categorydescription";
		$result1 = DB_query($SQL, $db);
		echo '
				<table class="selection">
				<tr>
					<td>';
		echo _('To search for Pick Lists for a specific part use the part selection facilities below') . '</td></tr>';
		echo '<tr>
				<td>' . _('Select a stock category') . ':<select name="StockCat">';
		while ($myrow1 = DB_fetch_array($result1)) {
			if (isset($_POST['StockCat']) and $myrow1['categoryid'] == $_POST['StockCat']) {
				echo '<option selected="selected" value="' . $myrow1['categoryid'] . '">' . $myrow1['categorydescription'] . '</option>';
			} else {
				echo '<option value="' . $myrow1['categoryid'] . '">' . $myrow1['categorydescription'] . '</option>';
			}
		}
		echo '</select></td>
				<td>' . _('Enter text extracts in the') . ' <b>' . _('description') . '</b>:</td>
				<td><input type="text" name="Keywords" size="20" maxlength="25" /></td>
			</tr>
			<tr>
				<td></td>
				<td><b>' . _('OR') . ' </b>' . _('Enter extract of the') . '<b> ' . _('Stock Code') . '</b>:</td>
				<td><input type="text" name="StockCode" size="15" maxlength="18" /></td>
			</tr>
			<tr>
				<td colspan="3">
					<div class="centre">
						<input type="submit" name="SearchParts" value="' . _('Search Parts Now') . '" />
						<input type="submit" name="ResetPart" value="' . _('Show All') . '" />
					</div>
				</td>
			</tr>
			</table>
			<br />
			<br />';

		if (isset($StockItemsResult)) {
			echo '<table class="selection">';
			$TableHeader = '<tr>
								<th class="ascending">' . _('Code') . '</th>
								<th class="ascending">' . _('Description') . '</th>
								<th class="ascending">' . _('On Hand') . '</th>
								<th class="ascending">' . _('Units') . '</th>
							</tr>';
			echo $TableHeader;
			$j = 1;
			$k = 0; //row colour counter
			while ($myrow = DB_fetch_array($StockItemsResult)) {
				if ($k == 1) {
					echo '<tr class="EvenTableRows">';
					$k = 0;
				} else {
					echo '<tr class="OddTableRows">';
					$k = 1;
				}
				echo '<td><input type="submit" name="SelectedStockItem" value="' . $myrow['stockid'] . '"</td>
					<td>' . $myrow['description'] . '</td>
					<td class="number">' . locale_number_format($myrow['qoh'],$myrow['decimalplaces']) . '</td>
					<td>' . $myrow['units'] . '</td>
					</tr>';
				$j++;
				if ($j == 12) {
					$j = 1;
					echo $TableHeader;
				}
				//end of page full new headings if

			}
			//end of while loop
			echo '</table>';
		}
		//end if stock search results to show
		else {
			$FromDate = FormatDateForSQL($_POST['FromDate']);
			$ToDate = FormatDateForSQL($_POST['ToDate']);
			if (isset($LotNumber) AND $LotNumber != '') {
				$SQL = "SELECT sampleid,
								prodspeckey,
								description,
								lotkey,
								identifier,
								createdby,
								sampledate,
								cert
							FROM qasamples
							LEFT OUTER JOIN stockmaster on stockmaster.stockid=qasamples.prodspeckey
							WHERE lotkey='" . filter_number_format($LotNumber) . "'
							AND sampleid<>'" . $SelectedSampleID . "'";
			} elseif (isset($SampleID) AND $SampleID != '') {
				$SQL = "SELECT sampleid,
								prodspeckey,
								description,
								lotkey,
								identifier,
								createdby,
								sampledate,
								cert
							FROM qasamples
							LEFT OUTER JOIN stockmaster on stockmaster.stockid=qasamples.prodspeckey
							WHERE sampleid='" . filter_number_format($SampleID) . "'
							AND sampleid<>'" . $SelectedSampleID . "'";
			} else {
				if (isset($SelectedStockItem)) {
					$SQL = "SELECT sampleid,
								prodspeckey,
								description,
								lotkey,
								identifier,
								createdby,
								sampledate,
								cert
							FROM qasamples
							INNER JOIN stockmaster on stockmaster.stockid=qasamples.prodspeckey
							WHERE stockid='" . $SelectedStockItem . "'
							AND sampledate>='$FromDate'
							AND sampledate <='$ToDate'
							AND sampleid<>'" . $SelectedSampleID . "'";
				} else {
					$SQL = "SELECT sampleid,
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
							WHERE sampledate>='$FromDate'
							AND sampledate <='$ToDate'
							AND sampleid<>'" . $SelectedSampleID . "'";
				} //no stock item selected
			} //end no sample id selected
			$ErrMsg = _('No QA samples were returned by the SQL because');
			$SampleResult = DB_query($SQL, $db, $ErrMsg);
			if (DB_num_rows($SampleResult) > 0) {

				echo '<table cellpadding="2" width="90%" class="selection">';
				$TableHeader = '<tr>
									<th class="ascending">' . _('Copy Results') . '</th>
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
					$ModifySampleID = $RootPath . '/TestPlanResults.php?SelectedSampleID=' . $myrow['sampleid'];
					$Copy = '<a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?SelectedSampleID=' . $SelectedSampleID .'&CopyToSampleID=' . $myrow['sampleid'] .'">' . _('Copy to This Sample') .'</a>';
					$FormatedSampleDate = ConvertSQLDate($myrow['sampledate']);
					
					if ($myrow['cert']==1) {
						$CertAllowed='<a target="_blank" href="'. $RootPath . '/PDFCOA.php?LotKey=' .$myrow['lotkey'] .'&ProdSpec=' .$myrow['prodspeckey'] .'">' . _('Yes') . '</a>';
					} else {
						$CertAllowed=_('No');
					}
					
					echo '<td><input type="radio" name="CopyToSampleID" value="' . $myrow['sampleid'] .'">
							<td><a target="blank" href="' . $ModifySampleID . '">' . str_pad($myrow['sampleid'],10,'0',STR_PAD_LEFT) . '</a></td>
							<td>' . $myrow['prodspeckey'] . '</td>
							<td>' . $myrow['description'] . '</td>
							<td>' . $myrow['lotkey'] . '</td>
							<td>' .  $myrow['identifier']  . '</td>
							<td>' .  $myrow['createdby']  . '</td>
							<td>' . $FormatedSampleDate . '</td>
							<td>' . $myrow['comments'] . '</td>
							<td>' . $CertAllowed . '</td>
							</tr>';
					$j++;
					if ($j == 12) {
						$j = 1;
						echo $TableHeader;
					}
					//end of page full new headings if
				} //end of while loop
				echo '</table>';
			} // end if Pick Lists to show
		}
		echo '</div>' . _('Override existing Test values?') .
			 '<input type="checkbox" name="OverRide"><input type="submit" name="Copy" value="' . _('Copy') . '" />
			  </form>';
		include('includes/footer.inc');
		exit;
	} else {
		$sql = "SELECT sampleresults.testid,
						sampleresults.defaultvalue,
						sampleresults.targetvalue,
						sampleresults.rangemin,
						sampleresults.rangemax,
						sampleresults.testvalue,
						sampleresults.testdate,
						sampleresults.testedby,
						sampleresults.comments,
						sampleresults.isinspec,
						sampleresults.showoncert,
						sampleresults.showontestplan,
						prodspeckey,
						type
					FROM sampleresults 
					INNER JOIN qasamples ON qasamples.sampleid=sampleresults.sampleid 
					INNER JOIN qatests ON qatests.testid=sampleresults.testid
					WHERE sampleresults.sampleid='" .$SelectedSampleID. "'";
		$msg = _('Test Results have been copied to sample') . ' ' . $_POST['CopyToSampleID']  . ' from sample' . ' ' . $SelectedSampleID ;
		$ErrMsg = _('The insert of the test results failed because');
		$DbgMsg = _('The SQL that was used and failed was');
		$result = DB_query($sql,$db,$ErrMsg, $DbgMsg);

		while ($myrow = DB_fetch_array($result)) {
			$result2 = DB_query("SELECT count(testid) FROM prodspecs
						WHERE testid = '".$myrow['testid']."'
						AND keyval='".$myrow['prodspeckey']."'",	$db);
			$myrow2 = DB_fetch_row($result2);;
			if($myrow2[0]>0) {
				$ManuallyAdded=0;
			} else {
				$ManuallyAdded=1;
			}
			$result2 = DB_query("SELECT resultid, targetvalue,rangemin, rangemax FROM sampleresults
						WHERE testid = '".$myrow['testid']."'
						AND sampleid='".$_POST['CopyToSampleID']."'",	$db);
			$myrow2 = DB_fetch_array($result2);;
			$IsInSpec=1;
			$CompareVal='yes';
			$CompareRange='no';
			if ($myrow['targetvalue']=='') {
				$CompareVal='no';
			}
			if ($myrow['type']==4) {
				//$RangeDisplay=$myrow['rangemin'] . '-'  . $myrow['rangemax'] . ' ' . $myrow['units'];
				$RangeDisplay='';
				if ($myrow['rangemin'] > '' OR $myrow['rangemax'] > '') {
					if ($myrow['rangemin'] > '' AND $myrow['rangemax'] == '') {
						$RangeDisplay='> ' . $myrow['rangemin'] . ' ' . $myrow['units'];
					} elseif ($myrow['rangemin']== '' AND $myrow['rangemax'] > '') {
						$RangeDisplay='< ' . $myrow['rangemax'] . ' ' . $myrow['units'];
					} else {
						$RangeDisplay=$myrow['rangemin'] . ' - ' . $myrow['rangemax'] . ' ' . $myrow['units'];
					}
					$CompareRange='yes';
				}
				
			} else {
				$RangeDisplay='&nbsp;';
				$CompareRange='no';
			}
			if ($myrow['type']==3) {
				$CompareVal='no';
			}
			//var_dump($CompareVal); var_dump($CompareRange); 
			if ($CompareVal=='yes'){
				if ($CompareRange=='yes'){
					if ($myrow2['rangemin'] > '' AND $myrow2['rangemax'] > '') {
						if (($myrow['testvalue']<>$myrow2['targetvalue']) AND ($myrow['testvalue']<$myrow2['rangemin'] OR $myrow['testvalue'] > $myrow2['rangemax'])) {
							$IsInSpec=0;
						}
					} elseif ($myrow2['rangemin'] > '' AND $myrow2['rangemax'] == '') {
						if (($myrow['testvalue']<>$myrow2['targetvalue']) AND ($myrow['testvalue']<=$myrow2['rangemin'])) {
							$IsInSpec=0;
						}
					} elseif ($myrow2['rangemin'] == '' AND $myrow2['rangemax'] > '') {
						if (($myrow['testvalue']<>$myrow2['targetvalue']) AND ($myrow['testvalue'] >= $myrow2['rangemax'])) {
							$IsInSpec=0;
						}
					}
					//var_dump($myrow['testvalue']); var_dump($myrow2['targetvalue']); var_dump($myrow2['rangemin']); var_dump($myrow2['rangemax']);
				} else {
					if (($myrow['testvalue']<>$myrow2['targetvalue'])) {
						$IsInSpec=0;
					}
				}
			}
			if($myrow2[0]>'') {
				//test already exists on CopyToSample
				if ($_POST['OverRide']=='on') {
					$updsql = "UPDATE sampleresults
								SET	testvalue='" .$myrow['testvalue']. "',
									testdate='" .$myrow['testdate']. "',
									testedby='" .$myrow['testedby']. "',
									isinspec='" .$IsInSpec. "'
								WHERE sampleid='" . $_POST['CopyToSampleID'] ."'
								AND resultid='".$myrow2[0]."'";
					$msg = _('Test Results have been overwritten to sample') . ' ' . $_POST['CopyToSampleID']  . _(' from sample') . ' ' . $SelectedSampleID  . _(' for test ') . $myrow['testid'];
					$ErrMsg = _('The insert of the test results failed because');
					$DbgMsg = _('The SQL that was used and failed was');
					$updresult = DB_query($updsql,$db,$ErrMsg, $DbgMsg);
					prnMsg($msg , 'success');
				} else {
					$msg = _('Test Results have NOT BEEN overwritten for Result ID ') . $myrow2[0];
					prnMsg($msg , 'warning');
				}
			} else {
				//Need to insert the test and results
				$inssql = "INSERT INTO sampleresults 
							(sampleid,
							testid,
							defaultvalue,
							targetvalue,
							testvalue,
							rangemin,
							rangemax,
							showoncert,
							showontestplan,
							comments,
							manuallyadded,
							testedby,
							testdate,
							isinspec)
						VALUES ( '"  . $_POST['CopyToSampleID'] . "',
								'"  . $myrow['testid'] . "',
								'"  . $myrow['defaultvalue'] . "', 
								'"  . $myrow['targetvalue']. "',
								'"  . $myrow['testvalue']. "',
								'"  . $myrow['rangemin'] . "',
								'"  . $myrow['rangemax'] . "',
								'"  . $myrow['showoncert'] . "',
								'"  . $myrow['showontestplan'] . "',
								'"  . $myrow['comments'] . "',
								'"  . $ManuallyAdded . "',
								'"  . $myrow['testedby'] . "',
								'"  . $myrow['testdate'] . "',
								'"  . $IsInSpec . "'
								)";
				$msg = _('Test Results have been copied to') . ' ' . $_POST['CopyToSampleID']  . _(' from ') . ' ' . $SelectedSampleID  . _(' for ') . $myrow['testid'];
				$ErrMsg = _('The insert of the test results failed because');
				$DbgMsg = _('The SQL that was used and failed was');
				$insresult = DB_query($inssql,$db,$ErrMsg, $DbgMsg);
				prnMsg($msg , 'success');
			}
		} //while loop on myrow
		$SelectedSampleID=$_POST['CopyToSampleID'];
		unset($_GET['CopyResults']);
		unset($_POST['CopyResults']);
	} //else
} //CopySpec

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
	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table class="selection">';
	echo '<tr>
			<th class="ascending">' . _('Add') . '</th>
			<th class="ascending">' . _('Name') . '</th>
			<th class="ascending">' . _('Method') . '</th>
			<th class="ascending">' . _('Units') . '</th>
			<th>' . _('Possible Values') . '</th>
			<th>' . _('Target Value') . '</th>
			<th>' . _('Range Min') . '</th>
			<th>' . _('Range Max') . '</th>
		</tr>';
	$k=0;
	$x=0;
	while ($myrow=DB_fetch_array($result)) {

	if ($k==1){
		echo '<tr class="EvenTableRows">';
		$k=0;
	} else {
		echo '<tr class="OddTableRows">';
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
			<td>%s</td>
			<td>%s</td>
			</tr>',
			'<input type="checkbox" name="AddRow' .$x.'"><input type="hidden" name="AddTestID' .$x.'" value="' .$myrow['testid']. '">',
			$myrow['name'],
			$myrow['method'],
			$myrow['units'],
			$myrow['defaultvalue'],
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
		$sql = "UPDATE sampleresults SET testedby='".  $_POST['TestedBy' .$i] . "',
										testdate='". FormatDateForSQL($_POST['TestDate' .$i]) . "',
										testvalue='".  $_POST['TestValue' .$i] . "',
										showoncert='".  $_POST['ShowOnCert' .$i] . "',
										isinspec='".  $IsInSpec . "'
						WHERE resultid='".  $_POST['ResultID' .$i] . "'";
		
		$msg = _('Sample Results were updated for Result ID') . ' ' . $_POST['ResultID' .$i] ;
		$ErrMsg = _('The updated of the sampleresults failed because');
		$DbgMsg = _('The SQL that was used and failed was');
		$result = DB_query($sql,$db,$ErrMsg, $DbgMsg);
		prnMsg($msg , 'success');
	} //for
	//check to see all values are in spec or at least entered
	$result = DB_query("SELECT count(sampleid) FROM sampleresults
						WHERE sampleid = '".$SelectedSampleID."'
						AND showoncert='1'
						AND testvalue=''",	$db);
	$myrow = DB_fetch_row($result);
	if($myrow[0]>0) {
		$sql = "UPDATE qasamples SET identifier='" . $_POST['Identifier'] . "',
									comments='" . $_POST['Comments'] . "',
									cert='0'
				WHERE sampleid = '".$SelectedSampleID."'";
		$msg = _('Test Results have not all been entered.  This Lot is not able to be used for a a Certificate of Analysis');
		$ErrMsg = _('The update of the QA Sample failed because');
		$DbgMsg = _('The SQL that was used and failed was');
		$result = DB_query($sql,$db,$ErrMsg, $DbgMsg);
		prnMsg($msg , 'error');
	}
	
	if(!isset($_POST['remark1'])){
	$_POST['remark1'] = "";
	}
	if(!isset($_POST['remark2'])){
	$_POST['remark2'] = "";
	}
	if(!isset($_POST['remark3'])){
	$_POST['remark3'] = "";
	}
	if(!isset($_POST['remark4'])){
	$_POST['remark4'] = "";
	}
	
	$result = DB_query("SELECT count(sampleid) FROM qrsampleremarks
						WHERE sampleid = '".$SelectedSampleID."'",	$db);
	$myrow = DB_fetch_row($result);
	if($myrow[0]>0) {
		$sql = "UPDATE qrsampleremarks SET remark1='" . $_POST['remark1'] . "',
									remark2='" . $_POST['remark2'] . "',
									remark3='" . $_POST['remark3'] . "',
									remark4='" . $_POST['remark4'] . "'
				WHERE sampleid = '".$SelectedSampleID."'";
		$ErrMsg = _('The update of the QA Sample Remarks failed because');
		$DbgMsg = _('The SQL that was used and failed was');
		$result = DB_query($sql,$db,$ErrMsg, $DbgMsg);
	}else{
	$sql = "INSERT INTO qrsampleremarks (sampleid,remark1,remark2,remark3,remark4)
							 VALUES('".$SelectedSampleID."',
							 		'" . $_POST['remark1'] . "',
									'" . $_POST['remark2'] . "',
									'" . $_POST['remark3'] . "',
									'" . $_POST['remark4'] . "')";
		$ErrMsg = _('The update of the QA Sample Remarks failed because');
		$DbgMsg = _('The SQL that was used and failed was');
		$result = DB_query($sql,$db,$ErrMsg, $DbgMsg);
	}
	
}
if (isset($_GET['Delete'])) {
	$sql= "SELECT COUNT(*) FROM sampleresults WHERE sampleresults.resultid='".$_GET['ResultID']."'
											AND sampleresults.manuallyadded='1'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]==0) {
		prnMsg(_('Cannot delete this Result ID because it is a part of the Product Specification'),'error');
	} else {
		$sql="DELETE FROM sampleresults WHERE resultid='". $_GET['ResultID']."'";
		$ErrMsg = _('The sample results could not be deleted because');
		$result = DB_query($sql,$db,$ErrMsg);
		
		prnMsg(_('Result QA Sample') . ' ' . $_GET['ResultID'] . _('has been deleted from the database'),'success');
		unset($_GET['ResultID']);
		unset($delete);
		unset ($_GET['delete']);
	}
}
if (!isset($SelectedSampleID)) {
	echo '<div class="centre">
			<a href="' . $RootPath . '/SelectQASamples.php">' .  _('Select a sample to enter results against') . '</a>
		</div>';
	prnMsg(_('This page can only be opened if a QA Sample has been selected. Please select a sample first'),'info');
	include ('includes/footer.inc');
	exit;
} 

echo '<div class="centre"><a href="' . $RootPath . '/SelectQASamples.php">' . _('Back to Samples') . '</a></div>';


echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';


$sql = "SELECT prodspeckey,
				description,
				lotkey,
				identifier,
				sampledate,
				comments,
				cert
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
			<th>' . _('Identifier') . '</th>
			<th>' . _('Sample Date') . '</th>
			<th>' . _('Comments') . '</th>
			<th>' . _('Used for Cert') . '</th>
		</tr>';
		
echo '<tr class="EvenTableRows"><td>' . str_pad($SelectedSampleID,10,'0',STR_PAD_LEFT)  . '</td>
	<td>' . $myrow['prodspeckey'] . ' - ' . $myrow['description'] . '</td>
	<td>' . $myrow['lotkey'] . '</td>
	<td>' . $myrow['identifier'] . '</td>
	<td>' . ConvertSQLDate($myrow['sampledate']) . '</td>
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
					<th class="ascending">' . _('Test Date') . '</th>
					<th class="ascending">' . _('Tested By') . '</th>					
					<th class="ascending">' . _('Test Result') . '</th>					
					<th class="ascending">' . _('On Cert') . '</th>
				</tr>';
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
			$TestResult='<input type="text" size="10" maxlength="20" class="' . $Class . '" name="TestValue' .$x .'" value="' . $myrow['testvalue'] . '"' . $BGColor . '/>';
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
			<td>' . $myrow['targetvalue'] . ' ' . $myrow['units'] . '</td>
			<td><input type="text" class="date" name="TestDate' .$x. '" size="10" maxlength="10" value="' . $TestDate . '" /> </td>
			<td><select name="TestedBy' .$x .'"/>';
	while ($techrow = DB_fetch_array($techresult)) {
		if ($techrow['userid'] == $myrow['testedby']){
			echo '<option selected="selected" value="' . $techrow['userid'] . '">' .$techrow['realname'] . '</option>';
		} else {
			echo '<option value="' .$techrow['userid'] . '">' . $techrow['realname'] . '</option>';
		}
	}
	echo '</select>';
	DB_data_seek($techresult,0);
	echo '<td>' . $TestResult . '</td>
			<td>' . $ShowOnCert . '</td>
			<td>' . $Delete . '</td>
		</tr>';
}
	
echo '</table>';
$result = DB_query("SELECT remark1,remark2,remark3,remark4 FROM qrsampleremarks
						WHERE sampleid = '".$SelectedSampleID."'",	$db);
	$my = DB_fetch_row($result);
echo '<table>
<tr><td>Remarks</td><td>'.($my[0] !="" && !isset($_GET['Result1']) ? '<textarea name="remark1" disabled="true" cols="90" rows="2">'.$my[0].'</textarea><textarea name="remark1" hidden="true" cols="90" rows="2">'.$my[0].'</textarea> <a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Result1=yes&amp;SelectedSampleID=' .$SelectedSampleID .'">Edit</a>' : '<textarea name="remark1" cols="90" rows="2">'.$my[0].'</textarea>').'</td></tr>

<tr><td>SQAO Remarks</td><td>'.($my[1] !="" && !isset($_GET['Result2']) ? '<textarea name="remark2" disabled="true" cols="90" rows="2">'.$my[1].'</textarea><textarea name="remark2" hidden="true" cols="90" rows="2">'.$my[1].'</textarea> <a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Result2=yes&amp;SelectedSampleID=' .$SelectedSampleID .'">Edit</a>' : '<textarea name="remark2" cols="90" rows="2">'.$my[1].'</textarea>').'</td></tr>

<tr><td>CQAO Remarks</td><td>'.($my[2] !="" && !isset($_GET['Result3']) ? '<textarea name="remark3" disabled="true" cols="90" rows="2">'.$my[2].'</textarea><textarea name="remark3" hidden="true" cols="90" rows="2">'.$my[2].'</textarea> <a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Result3=yes&amp;SelectedSampleID=' .$SelectedSampleID .'">Edit</a>' : '<textarea name="remark3" cols="90" rows="2">'.$my[2].'</textarea>').'</td></tr>

<tr><td>QARD Manager Remarks</td><td>'.($my[3] !="" && !isset($_GET['Result4']) ? '<textarea name="remark4" disabled="true" cols="90" rows="2">'.$my[3].'</textarea><textarea name="remark4" hidden="true" cols="90" rows="2">'.$my[3].'</textarea> <a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Result4=yes&amp;SelectedSampleID=' .$SelectedSampleID .'">Edit</a>' : '<textarea name="remark4" cols="90" rows="2">'.$my[3].'</textarea>').'</td></tr>
</table>';
echo '<div class="centre">
		<input type="hidden" name="TestResultsCounter" value="' . $x . '" />
		<input type="submit" name="submit" value="' . _('Enter Information') . '" />
	</div>
	</div>
	</form>';

echo '<div class="centre"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?ListTests=yes&amp;SelectedSampleID=' .$SelectedSampleID .'">' . _('Add More Tests') . '</a></div>';
echo '<div class="centre"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?CopyResults=yes&amp;SelectedSampleID=' .$SelectedSampleID .'">' . _('Copy These Results') . '</a></div>';

if ($CanCert==1){
	echo '<div class="centre"><a target="_blank" href="'. $RootPath . '/PDFCOA.php?LotKey=' .$LotKey .'&ProdSpec=' . $ProdSpec. '">' . _('Print COA') . '</a></div>';
}

include('includes/footer.inc');
?>