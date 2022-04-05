<?php

/* $Id: SecurityTokens.php 4424 2010-12-22 16:27:45Z tim_schofield $*/

include('includes/session.inc');
include('includes/phplot/phplot.php');
include('includes/SQL_CommonFunctions.inc');
$Title = _('Annealing Hardness Data Sheet');

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
if (isset($_POST['Go1']) OR isset($_POST['Go2'])) {
	$_POST['PageOffset'] = (isset($_POST['Go1']) ? $_POST['PageOffset1'] : $_POST['PageOffset2']);
	$_POST['Go'] = '';
}
if (!isset($_POST['PageOffset'])) {
	$_POST['PageOffset'] = 1;
} else {
	if ($_POST['PageOffset'] == 0) {
		$_POST['PageOffset'] = 1;
	}
}

if (isset($_GET['SelectedUser'])){
	$SelectedUser = $_GET['SelectedUser'];
} elseif (isset($_POST['SelectedUser'])){
	$SelectedUser = $_POST['SelectedUser'];
}
if (isset($_POST['Submit'])) {
$InputError = 0;

if (isset($SelectedUser)) {
$sql = "UPDATE qaannealinghardness SET `sheetid`='" . $_POST['sheet'] ."', 
										`brasslot`='" . $_POST['brasslot'] ."', 
										`machineno`='" . $_POST['machine'] ."', 
										`shift`='" . $_POST['shift'] ."',
										date='" . FormatDateForSQL($_POST['date']) ."',
										technician='".$_SESSION['UsersRealName']."'
					WHERE id = '". $SelectedUser . "'";
					
$sqldel = "DELETE FROM qaannealinghardnessdata WHERE testno = '". $SelectedUser . "'";
$ErrMsg = _('The user alterations could not be processed because');
$DbgMsg = _('The SQL that was used to update the user and failed was');
$Rest = DB_query($sqldel,$ErrMsg,$DbgMsg,true);

	for($i=0;$i<count($_POST['sample']);$i++){
	$sno= $_POST['sample'][$i];
	$qty = $_POST['result'][$i];
	$no = $i+1;	
	$SQL="INSERT INTO qaannealinghardnessdata (num,
									testno,
									sample,
									result) 
							VALUES('". $no ."',
									'". $SelectedUser ."',
									'". $sno ."',
									'". $qty ."')";
					$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		}
					
	prnMsg( _('The selected record has been updated successfully'), 'success' );
		
	} elseif ($InputError !=1) {
	//initialise no input errors assumed initially before we test
		$RequestNo = GetNextTransNo(83, $db);
		$sql = "INSERT INTO qaannealinghardness (`id`, 
												`sheetid`, 
												`brasslot`, 
												`machineno`, 
												`shift`, 
												`date`,
												technician)
											VALUES (" . $RequestNo . ",
												'" . $_POST['sheet'] ."',
												'" . $_POST['brasslot'] ."',
												'" . $_POST['machine'] ."',
												'" . $_POST['shift'] ."',
												'" . FormatDateForSQL($_POST['date']) ."',
												'".$_SESSION['UsersRealName']."')";
	
	for($i=0;$i<count($_POST['sample']);$i++){
	$sno= $_POST['sample'][$i];
	$qty = $_POST['result'][$i];
	$no = $i+1;	
	$SQL="INSERT INTO qaannealinghardnessdata (num,
									testno,
									sample,
									result) 
							VALUES('". $no ."',
									'". $RequestNo ."',
									'". $sno ."',
									'". $qty ."')";
					$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		}
prnMsg( _('A new record has been inserted Successfully'), 'success' );
	}

	if ($InputError!=1){
		//run the SQL from either of the above possibilites
		$ErrMsg = _('The user alterations could not be processed because');
		$DbgMsg = _('The SQL that was used to update the user and failed was');
		$result = DB_query($sql,$ErrMsg,$DbgMsg);
  

		unset($_POST['sheet']);
		unset($_POST['brasslot']);
		unset($_POST['machine']);
		unset($_POST['date']);
		unset($_POST['shift']);
		unset($_POST['type']);
		unset($_POST['operation']);
		unset($SelectedUser);
	}
	
}elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button		

			$sql="DELETE FROM qaannealinghardness WHERE id='" . $SelectedUser . "'";
			$ErrMsg = _('The Record could not be deleted because');
			$result = DB_query($sql,$ErrMsg);
			$sql2="DELETE FROM qaannealinghardnessdata WHERE testno='" . $SelectedUser . "'";
			$result = DB_query($sql2,$ErrMsg);
			prnMsg(_('Record Deleted Successfully'),'info');

		unset($SelectedUser);
	}

######################################################################################
if (isset($_GET['view'])) {

	$sql = "
	SELECT a.id,
			a.sheetname,
			b.typename,
			c.operation,
			a.max_limit,
			a.min_limit,
			a.description,
			d.`brasslot`,
			d.`machineno`,
			d.`shift`,
			d.`date`,
			d.`technician`
			FROM qarecordingsheet a
			INNER JOIN qaoperationtype b ON b.id=a.typeid
			INNER JOIN qaoperation c ON c.id=a.operationid
			INNER JOIN qaannealinghardness d ON d.sheetid=a.id
			WHERE d.id='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);
	
echo '<a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">' . _('Back to Main Menu') . '</a>';

echo '<table class="selection">
      <tr><td>';

	echo '<table class="selection">
			<tr height="30px">
				<td width="150px">' . _('Operation') . ':</td>
				<td width="300px">' . $myrow['operation'] . '</td>
			</tr>
			<tr height="30px">
				<td width="150px">' . _('Operation Type') . ':</td>
				<td>' . $myrow['typename'] . '</td>
			</tr>
			<tr height="30px">
				<td width="150px">' . _('Sheet') . ':</td>
				<td width="200px">' . $myrow['sheetname'] . '</td>
			</tr>';

	echo '<tr height="30px">
			<td>' . _('Machine No.') . '</td>
			<td>'.$myrow['machineno'].'</td>
		</tr>
		<tr height="30px">
			<td>' .  _('Brass Lot No') . '</td>
			<td>'.$myrow['brasslot'] .'</td>
		</tr>
		<tr height="30px">
			<td>' . _('Date') . '</td>
			<td>'.ConvertSQLDate($myrow['date']).'</td>
		</tr>
		<tr height="30px">
			<td>' . _('Shift') . '</td>
			<td>'.$myrow['shift'].'</td>
		</tr>
		<tr height="30px">
			<td>' . _('Technician') . '</td>
			<td>'.$myrow['technician'].'</td>
		</tr>';

echo '</table>';
	echo '</td>	<td>';
			
$sql = "SELECT *
		FROM qaannealinghardnessdata
		WHERE testno='" . $SelectedUser . "' ORDER BY num ASC";
	$results = DB_query($sql);
echo '<table class="selection">';
echo '<tr>
		<th width="100">Test</td>
		<th width="100">Result</td>
	</tr>';
while($myro = DB_fetch_array($results)){

echo '<tr>
		<td >'.$myro['sample'].'</td>
		<td >'.$myro['result'].'</td>
	</tr>';
	}

echo '</table>';
	echo '</td>
	</tr>
	<tr><td colspan="4">';

$graph = new PHPlot(950,450);

	$GraphTitle = ' ' . $myrow['description'] . "\n\r";

	$SQL = "SELECT sample,
				result
		FROM qaannealinghardnessdata
		WHERE testno='" . $SelectedUser . "' ORDER BY num ASC";


	$graph->SetTitle($GraphTitle);
	$graph->SetTitleColor('blue');
	$graph->SetOutputFile('companies/' .$_SESSION['DatabaseName'] .  '/reports/hardnessgraph.png');
	$graph->SetXTitle(_('Sample'));
	$graph->SetYTitle(_('Hardness'));
	$graph->SetXTickPos('none');
	$graph->SetXTickLabelPos('none');
	$graph->SetXLabelAngle(0);
	$graph->SetBackgroundColor('white');
	$graph->SetTitleColor('blue');
	$graph->SetFileFormat('png');
	$graph->SetPlotType('linepoints');
	$graph->SetIsInline('1');
	$graph->SetShading(5);
	$graph->SetDrawYGrid(TRUE);
	$graph->SetDataType('text-data');
	//$graph->SetNumberFormat($DecimalPoint, $ThousandsSeparator);
	//$graph->SetPrecisionY($_SESSION['CompanyRecord']['decimalplaces']);

	$SalesResult = DB_query($SQL);
	if (DB_error_no() !=0) {

		prnMsg(_('The sales graph data for the selected criteria could not be retrieved because') . ' - ' . DB_error_msg(),'error');
		include('includes/footer.inc');
		exit;
	}
	if (DB_num_rows($SalesResult)==0){
		prnMsg(_('There is not sales data for the criteria entered to graph'),'info');
		include('includes/footer.inc');
		exit;
	}

	$GraphArray = array();
	$i = 0;
	while ($myro = DB_fetch_array($SalesResult)){
		$GraphArray[$i] = array($myro['sample'],$myro['result'], $myrow['min_limit'],'',$myrow['max_limit']);
		$i++;
	}

	$graph->SetDataValues($GraphArray);
	$graph->SetDataColors(
		array('grey','orange','red','red'),  //Data Colors
		array('black')	//Border Colors
	);
	$graph->SetLegend(array(_('Actual'),_('Lower Limit'),_('Upper Limit')));

	//Draw it
	$graph->DrawGraph();
	echo '<table class="selection">
			<tr>
				<td><p><img src="companies/' .$_SESSION['DatabaseName'] .  '/reports/hardnessgraph.png" alt="Hardness Report Graph"></img></p></td>
			</tr>
		  </table>';
		  
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
echo '</td></tr><tr><td>';
echo '<a href="' . $RootPath . '/PDFQAHardnessAnnealingGraph.php?id='.$SelectedUser.'">' . _('Generate PDF Report') . '</a>';
echo '</td></tr></table>';
} else{

echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" id="form" name="form">
	<div>
	<br />
	<table>
		<tr>';
		
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

#############################################################################################

if (isset($SelectedUser)) {
	//editing an existing User

	$sql = "SELECT *,a.id
		FROM qaannealinghardness a
		INNER JOIN qarecordingsheet b ON a.sheetid=b.id
		WHERE a.id='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['id'] = $myrow['id'];
	$_POST['date'] = $myrow['date'];
	$_POST['shift'] = $myrow['shift'];
	$_POST['brasslot'] = $myrow['brasslot'];
	$_POST['machine'] = $myrow['machineno'];
	$_POST['sheet'] = $myrow['sheetid'];
	$_POST['operation'] = $myrow['operationid'];
	$_POST['type'] = $myrow['typeid'];
	
echo '<a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">' . _('Back to Main Menu') . '</a>';

	echo '<input type="hidden" name="SelectedUser" value="' . $SelectedUser . '" />';
	echo '<input type="hidden" name="UserID" value="' . $_POST['id'] . '" />';

	echo '<td>' . _('Record No') . ':</td>
			<td>' . $_POST['id'] . '</td>
		</tr>';
}
#############################################################################################

	echo '<tr>
		<td>' . _('Operation') . '</td>
		<td><select name="operation" required="required" onchange="document.form.submit();">';
     $SQL = "SELECT * FROM qaoperation";
				 $result=DB_query($SQL);
  echo '<option selected="selected" value="">--Select Sheet--</option>';
  while ($myrow4=DB_fetch_array($result)){
	if (isset($_POST['operation']) AND  $myrow4['id']==$_POST['operation']){
		echo '<option selected="selected" value="'. $myrow4['id'] . '">' . $myrow4['operation'] . '</option>';
	} else {
		echo '<option value="'. $myrow4['id'] . '">' . $myrow4['operation'] . '</option>';
	}
}
  echo '</select></td>
		</tr>';
		
  echo '<tr>
  			<td>' . _('Operation Type') . '</td>
			<td><select name="type" required="required" onchange="document.form.submit();">';
     $SQL = "SELECT * FROM qaoperationtype";
				 $result=DB_query($SQL);
  echo '<option selected="selected" value="">--Select Sheet--</option>';
  while ($myrow4=DB_fetch_array($result)){
	if (isset($_POST['type']) AND  $myrow4['id']==$_POST['type']){
		echo '<option selected="selected" value="'. $myrow4['id'] . '">' . $myrow4['typename'] . '</option>';
	} else {
		echo '<option value="'. $myrow4['id'] . '">' . $myrow4['typename'] . '</option>';
	}
}
  echo '</select></td>
		</tr>';

  echo '<tr><td>' . _('Data Sheet') . '</td>
			<td><select name="sheet" required="required">';
  echo '<option selected="selected" value="">--Select Sheet--</option>';
  if(isset($_POST['operation']) && $_POST['operation'] !="" && isset($_POST['type']) && $_POST['type'] !=""){
  $SQL = "SELECT * FROM qarecordingsheet WHERE operationid=".$_POST['operation']." AND typeid=".$_POST['type']."";
	$result=DB_query($SQL);
  while ($myrow4=DB_fetch_array($result)){
	if (isset($_POST['sheet']) AND  $myrow4['id']==$_POST['sheet']){
		echo '<option selected="selected" value="'. $myrow4['id'] . '">' . $myrow4['sheetname'] . '</option>';
	} else {
		echo '<option value="'. $myrow4['id'] . '">' . $myrow4['sheetname'] . '</option>';
	}
	}
}
  echo '</select></td>
		</tr>';

  echo '<tr>
		<td>' .  _('Brass Lot No.') . '</td>';
	echo '<td><input type="text" autofocus="autofocus" name="brasslot" value="'.$_POST['brasslot'].'" /></td></tr>';
 echo '<tr>
		<td>' .  _('Machine No.') . '</td>';
	echo '<td><input type="text" autofocus="autofocus" name="machine" value="'.$_POST['machine'].'" /></td></tr>';
	echo '<tr>
			<td>' . _('Shift') . '</td>
			<td><input type="text" autofocus="autofocus" name="shift" value="'.$_POST['shift'].'" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Date') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" class="date" name="date" value="'.Date($_SESSION['DefaultDateFormat']).'" /></td>
		</tr>
	</table>
	';

echo '</div>';

if (isset($SelectedUser)) {
	//editing an existing User

	$sql = "SELECT *
		FROM qaannealinghardnessdata
		WHERE testno='" . $SelectedUser . "'";

	$result3 = DB_query($sql);
echo '<table id="dataTable" class="selection">';
echo '<tr>
		<th>' .  _('Test')  . '</th>
		<th>' .  _('Result'). '</th>
	</tr>';
while($myro = DB_fetch_array($result3)){
	echo '<tr>
			
			<td><input type="text" size="10" maxlength="20" autofocus="autofocus" required name="sample[]" class="integer" value="'.$myro['sample'].'" /></td>
			<td><input type="text" size="10" maxlength="10" autofocus="autofocus" required  class="integer" name="result[]" value="'.$myro['result'].'" /></td>
			<td><a href="#" onClick="Javacsript:deleteRow(this)"><img src="'.$RootPath.'/css/trash.png" title="' ._('Delete Row') . '" alt="" /></a></a></td>
		</tr>';
	}
echo '</table>';
echo '<input name="" type="button" onclick=addRow("dataTable") title="' ._('Add New Row') . '" value="Add New Row" /></br></br>';	
}else{
echo '<table id="dataTable" class="selection">';
echo '<tr>
		<th>' .  _('Test')  . '</th>
		<th>' .  _('Result'). '</th>
	</tr>';

	echo '<tr>
			
			<td><input type="text" size="10" maxlength="20" required autofocus="autofocus" name="sample[]" autocomplete="off" class="integer" value="" /></td>
			<td><input type="text" size="10" maxlength="10" required autofocus="autofocus"  class="integer" autocomplete="off" name="result[]" value="" /></td>
			<td><a href="#" onClick="Javacsript:deleteRow(this)"><img src="'.$RootPath.'/css/trash.png" title="' ._('Delete Row') . '" alt="" /></a></a></td>
		</tr>';

echo '</table>';	
	echo '<input name="" type="button" onclick=addRow("dataTable") title="' ._('Add New Row') . '" value="Add New Row" /></br></br>';		
}

echo '<input type="submit" name="Submit" value="' . _('Submit') . '" />';

echo '</form>';
echo '<br />';
######################################################################################
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" id="form">';
		
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
if (!isset($SelectedUser)) {
	$sql = "SELECT *, a.id
				FROM qaannealinghardness a
				INNER JOIN qarecordingsheet b ON a.sheetid=b.id
				ORDER BY a.id DESC";
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
				<th>' . _('Data Sheet') . '</th>
				<th>' . _('Brass Lot No.') . '</th>
				<th>' . _('Machine No.') . '</th>
				<th>' . _('Date') . '</th>
				<th>' . _('Shift') . '</th>
				<th colspan="3">&nbsp;</th>
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
		$edit='<td><a href="%s&amp;SelectedUser=%s">' . _('Edit') . '</a></td>';
		$del= '<td><a href="%s&amp;SelectedUser=%s&amp;delete=1" onclick="return confirm(\'' . _('Are you sure you wish to delete this Record?') . '\');">' . _('Delete') . '</a></td>';

		printf('<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					' .$edit. '
					'.$del.'
					'.$view.'
					</tr>',
					$myrow['id'],
					$myrow['sheetname'],
					$myrow['brasslot'],
					$myrow['machineno'],
					$LastVisitDate,
					$myrow['shift'],
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
############################################################################################
echo '</form>';

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

input[type='button'], button {
    background-color:#34a7e8;
    border:thin outset #1992DA;
    padding:6px 24px;
    vertical-align:middle;
    font-weight:bold;
    color:#FFFFFF;
    cursor: pointer;
    
    -webkit-border-radius:  4px;
    -moz-border-radius:     4px;
    border-radius:          4px;
    -moz-box-shadow:    1px 1px 1px #64BEF1 inset;
	-webkit-box-shadow: 1px 1px 1px #64BEF1 inset;
	box-shadow:         1px 1px 1px #64BEF1 inset;
}
</style>
		
		<SCRIPT language="javascript">
        function addRow(tableID) {

            var table = document.getElementById(tableID);

            var rowCount = table.rows.length;
            var row = table.insertRow(rowCount);

			var cell2 = row.insertCell(0);
            var element2 = document.createElement("input");
            element2.type = "text";
			element2.required = "required";
			element2.autocomplete="off";
			element2.size = "10";
            element2.name = "sample[]";
            cell2.appendChild(element2);

            var cell3 = row.insertCell(1);
            var element3 = document.createElement("input");
            element3.type = "text";
			element3.required = "required";
			element3.autocomplete = "off";
			element3.size = "10";
            element3.name = "result[]";
            cell3.appendChild(element3);
			
			row.insertCell(2).innerHTML= '<a href="#" onClick="Javacsript:deleteRow(this)"><?php echo '<img src="'.$RootPath.'/css/trash.png" title="' ._('Delete Row') . '" alt="" /></a>';?></a>';


        }

        function deleteRow(obj) {
      
    var index = obj.parentNode.parentNode.rowIndex;
    var table = document.getElementById("dataTable");
    table.deleteRow(index);
    
}

var specialKeys = new Array();
        specialKeys.push(8); //Backspace
        function IsNumeric(e) {
            var keyCode = e.which ? e.which : e.keyCode
            var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
            document.getElementById("error").style.display = ret ? "none" : "inline";
            return ret;
        }

function findTotal(){
    var arr = document.getElementsByName('amnt[]');
    var tot=0;
    for(var i=0;i<arr.length;i++){
        if(parseInt(arr[i].value))
            tot += parseInt(arr[i].value);
			total = tot.toString().replace(/([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,');
    }
    document.getElementById('total').value = total;
}

    </SCRIPT>