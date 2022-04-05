<?php

/* $Id: SecurityTokens.php 4424 2010-12-22 16:27:45Z tim_schofield $*/

include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$Title = _('Primer Sensitivity Curve Data Sheet');

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
$sql = "UPDATE qaprimersensitivity SET depthrange='" . $_POST['depthrange'] . "',
						test='" . $_POST['test'] ."',
						calibre='" . $_POST['calibre'] ."',
						primerlot='" . $_POST['primerlot'] ."',
						date='" . FormatDateForSQL($_POST['date']) ."'
					WHERE testno = '". $SelectedUser . "'";
					
$sqldel = "DELETE FROM qaprimersensitivitydata WHERE testno = '". $SelectedUser . "'";
$ErrMsg = _('The user alterations could not be processed because');
$DbgMsg = _('The SQL that was used to update the user and failed was');
$Rest = DB_query($sqldel,$ErrMsg,$DbgMsg,true);

	for($i=0;$i<count($_POST['height']);$i++){
	$sno= $_POST['height'][$i];
	$qty = $_POST['fired'][$i];
	$desc = $_POST['misfired'][$i];
	$no = $i+1;	
	$SQL="INSERT INTO qaprimersensitivitydata (num,
									testno,
									height,
									fired,
									misfired) 
							VALUES('". $no ."',
									'". $SelectedUser ."',
									'". $sno ."',
									'". $qty ."',
									'". $desc ."')";
					$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		}
					
	prnMsg( _('The selected record has been updated successfully'), 'success' );
		
	} elseif ($InputError !=1) {
	//initialise no input errors assumed initially before we test
		$RequestNo = GetNextTransNo(81, $db);
		$sql = "INSERT INTO qaprimersensitivity (`testno`, 
										`test`,
										`depthrange`,
										`calibre`, 
										`primerlot`,
										`date`)
					VALUES (" . $RequestNo . ",
						'" . $_POST['test'] ."',
						'" . $_POST['depthrange'] ."',
						'" . $_POST['calibre'] ."',
						'" . $_POST['primerlot'] ."',
						'" . FormatDateForSQL($_POST['date']) ."')";
	
	for($i=0;$i<count($_POST['height']);$i++){
	$sno= $_POST['height'][$i];
	$qty = $_POST['fired'][$i];
	$desc = $_POST['misfired'][$i];
	$no = $i+1;	
	$SQL="INSERT INTO qaprimersensitivitydata (num,
									testno,
									height,
									fired,
									misfired) 
							VALUES('". $no ."',
									'". $RequestNo ."',
									'". $sno ."',
									'". $qty ."',
									'". $desc ."')";
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
  

		unset($_POST['depthrange']);
		unset($_POST['calibre']);
		unset($_POST['primerlot']);
		unset($_POST['date']);
		unset($_POST['test']);
		unset($SelectedUser);
	}
	
}elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button		

			$sql="DELETE FROM qaprimersensitivity WHERE testno='" . $SelectedUser . "'";
			$ErrMsg = _('The Record could not be deleted because');
			$result = DB_query($sql,$ErrMsg);
			$sql2="DELETE FROM qaprimersensitivitydata WHERE testno='" . $SelectedUser . "'";
			$result = DB_query($sql2,$ErrMsg);
			prnMsg(_('Record Deleted Successfully'),'info');

		unset($SelectedUser);
	}

######################################################################################
if (isset($_GET['view'])) {

	$sql = "SELECT *
		FROM qaprimersensitivity
		WHERE testno='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['id'] = $myrow['testno'];
	$_POST['machine'] = $myrow['depthrange'];
	$_POST['date'] = ConvertSQLDate($myrow['date']);
	$_POST['lot'] = $myrow['primerlot'];
	$_POST['calibre']	= $myrow['calibre'];
	$_POST['test'] = $myrow['test'];
	
echo '<a href="' . $RootPath . '/QAPrimerDataSheet.php">' . _('Back to Main Menu') . '</a>';

echo '<table class="selection">
      <tr><td>';

	echo '<table class="selection">
			<tr height="30px">
				<td width="150px">' . _('Test No') . ':</td>
				<th width="200px">' . $_POST['test'] . '</th>
			</tr>';

	echo '<tr height="30px"><td>' . _('Primer Depth Range.') . '</td>
			<td>'.$_POST['machine'].'</td>
		</tr><tr height="30px">
		<td>' . _('Date') . '</td>
			<td>'.$_POST['date'].'</td>
		</tr><tr height="30px">
		<td>' . _('Calibre') . '</td>
			<td>'.$_POST['calibre'].'</td>
		</tr>
		<tr height="30px">
		<td>' .  _('Primer Lot No') . '</td>
		<td>'.$_POST['lot'] .'</td>
		<td>';

echo '</td>
	</tr>
	</table>';
	echo '</td>	<td>';
	
	echo '</td>
	</tr>
	<tr><td colspan="4">';
		
$sql = "SELECT *
		FROM qaprimersensitivitydata
		WHERE testno='" . $SelectedUser . "' ORDER BY num ASC";
	$results = DB_query($sql);
echo '<table class="selection">';
echo '<tr>
		<th width="100">Height</td>
		<th width="100">Fired</td>
		<th width="100">Misfired</td>
	</tr>';
while($myro = DB_fetch_array($results)){

echo '<tr>
		<td >'.$myro['height'].'</td>
		<td >'.$myro['fired'].'</td>
		<td >'.$myro['misfired'].'</td>
	</tr>';
	}

echo '</table>';
	
echo '</td></tr><tr><td>';
echo '<a href="' . $RootPath . '/QAPrimerCurve.php?SelectedUser='.$SelectedUser.'">' . _('Generate Primer Sensitivity Curve') . '</a>';
echo '</td></tr></table>';
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
		FROM qaprimersensitivity
		WHERE testno='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['id'] = $myrow['testno'];
	$_POST['date'] = $myrow['date'];
	$_POST['calibre'] = $myrow['calibre'];
	$_POST['primerlot'] = $myrow['primerlot'];
	$_POST['depthrange'] = $myrow['depthrange'];
	$_POST['test'] = $myrow['test'];


	echo '<input type="hidden" name="SelectedUser" value="' . $SelectedUser . '" />';
	echo '<input type="hidden" name="UserID" value="' . $_POST['id'] . '" />';

	echo '<table class="selection">
			<tr>
				<td>' . _('Record No') . ':</td>
				<td>' . $_POST['id'] . '</td>
			</tr>';
echo '<a href="' . $RootPath . '/QAPrimerDataSheet.php">' . _('Back to Main Menu') . '</a>';
}
#############################################################################################

	echo '<td>' . _('Test No') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" name="test" value="'.$_POST['test'].'" /></td>
		</tr>
	<tr>
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
		<td>' .  _('Primer Lot No.') . '</td>';
	echo '<td><input type="text" autofocus="autofocus" name="primerlot" value="'.$_POST['primerlot'].'" /></td>';

echo '</td></tr><tr>
<td>' . _('Primer Depth Range') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" name="depthrange" value="'.$_POST['depthrange'].'" /></td>
		</tr>
		<tr>
<td>' . _('Date') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" class="date" name="date" value="'.Date($_SESSION['DefaultDateFormat']).'" /></td>
		</tr>
	</tr>
	</table>
	<br/>';

echo '</div>';

if (isset($SelectedUser)) {
	//editing an existing User

	$sql = "SELECT *
		FROM qaprimersensitivitydata
		WHERE testno='" . $SelectedUser . "'";

	$result3 = DB_query($sql);
echo '<table id="dataTable" class="selection">';
echo '<tr>
		<th>' .  _('Height')  . '</th>
		<th>' .  _('Fired'). '</th>
		<th>' .  _('MisFired'). '</th>
	</tr>';
while($myro = DB_fetch_array($result3)){
	echo '<tr>
			
			<td><input type="text" size="10" maxlength="20" autofocus="autofocus" required name="height[]" class="integer" value="'.$myro['height'].'" /></td>
			<td><input type="text" size="10" maxlength="10" autofocus="autofocus" required  class="integer" name="fired[]" value="'.$myro['fired'].'" /></td>
			<td><input type="text" name="misfired[]" size="10" maxlength="10" required autofocus="autofocus" class="integer" value="'.$myro['misfired'].'" /></td>
			<td><a href="#" onClick="Javacsript:deleteRow(this)"><img src="'.$RootPath.'/css/trash.png" title="' ._('Delete Row') . '" alt="" /></a></a></td>
		</tr>';
	}
echo '</table>';
echo '<input name="" type="button" onclick=addRow("dataTable") title="' ._('Add New Row') . '" value="Add New Row" /></br></br>';	
}else{
echo '<table id="dataTable" class="selection">';
echo '<tr>
		<th>' .  _('Height')  . '</th>
		<th>' .  _('Fired'). '</th>
		<th>' .  _('MisFired'). '</th>
	</tr>';

	echo '<tr>
			
			<td><input type="text" size="10" maxlength="20" required autofocus="autofocus" name="height[]" autocomplete="off" class="integer" value="" /></td>
			<td><input type="text" size="10" maxlength="10" required autofocus="autofocus"  class="integer" autocomplete="off" name="fired[]" value="" /></td>
			<td><input type="text" name="misfired[]" size="10" required maxlength="10" autofocus="autofocus" autocomplete="off" class="integer" /></td>
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
	$sql = "SELECT *
				FROM qaprimersensitivity ORDER BY testno DESC";
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
				<th>' . _('Test No') . '</th>
				<th>' . _('Calibre') . '</th>
				<th>' . _('Primer Lot No.') . '</th>
				<th>' . _('Date') . '</th>
				<th>' . _('Primer Depth Range') . '</th>
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
					$myrow['testno'],
					$myrow['test'],
					$myrow['calibre'],
					$myrow['primerlot'],
					$LastVisitDate,
					$myrow['depthrange'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?',
					$myrow['testno'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?',
					$myrow['testno'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?',
					$myrow['testno']);
	
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
            element2.name = "height[]";
            cell2.appendChild(element2);

            var cell3 = row.insertCell(1);
            var element3 = document.createElement("input");
            element3.type = "text";
			element3.required = "required";
			element3.autocomplete = "off";
			element3.size = "10";
            element3.name = "fired[]";
            cell3.appendChild(element3);
			
			var cell4 = row.insertCell(2);
            var element4 = document.createElement("input");
			element4.type = "text";
			element4.required = "required";
			element4.autocomplete="off";
			element4.size = "10";
            element4.name = "misfired[]";
            cell4.appendChild(element4);
			
			row.insertCell(3).innerHTML= '<a href="#" onClick="Javacsript:deleteRow(this)"><?php echo '<img src="'.$RootPath.'/css/trash.png" title="' ._('Delete Row') . '" alt="" /></a>';?></a>';


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