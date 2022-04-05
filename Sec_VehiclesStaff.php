<?php
/* $Id: SelectSupplier.php 6941 2014-10-26 23:18:08Z daintree $*/

include ('includes/session.inc');
$Title = _('Search Staff Vehicles');

/* webERP manual links before header.inc */
$ViewTopic= 'Security';
$BookMark = 'SelectStaffVehicle';

include ('includes/header.inc');
include ('includes/SQL_CommonFunctions.inc');
if (!isset($_SESSION['VehicleStaffID'])) {
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/user.png" title="' . _('Search') . '" alt="" />' . ' ' . _('Staff Vehicles Register') . '</p>';
}
if (isset($_GET['VehicleID'])) {
	$_SESSION['VehicleStaffID']=$_GET['VehicleID'];
}

if (!isset($_POST['PageOffset'])) {
	$_POST['PageOffset'] = 1;
} else {
	if ($_POST['PageOffset'] == 0) {
		$_POST['PageOffset'] = 1;
	}
}
if (isset($_POST['Select'])) { /*User has hit the button selecting a supplier */
	$_SESSION['VehicleStaffID'] = $_POST['Select'];
	unset($_POST['Select']);
	unset($_POST['Keywords']);
	unset($_POST['idno']);
	unset($_POST['Search']);
	unset($_POST['Go']);
	unset($_POST['Next']);
	unset($_POST['Previous']);
}

if (isset($_POST['Search'])
	OR isset($_POST['Go'])
	OR isset($_POST['Next'])
	OR isset($_POST['Previous'])) {

	if (mb_strlen($_POST['Keywords']) > 0 AND mb_strlen($_POST['idno']) > 0) {
		prnMsg( _('Vehicle Reg Number keywords have been used in preference to the Driver ID Number extract entered'), 'info' );
	}
	if ($_POST['Keywords'] == '' AND $_POST['idno'] == '') {
		$SQL = "SELECT VehicleNo,
					RegNo,
					Make,
					Name,
					IdNo,
					Type,
					Color,
					Remarks,
					Date
				FROM vehicle_register_staff
				ORDER BY RegNo";
	} else {
		if (mb_strlen($_POST['Keywords']) > 0) {
			$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
			//insert wildcard characters in spaces
			$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';
			$SQL = "SELECT VehicleNo,
							RegNo,
							Make,
							Name,
							IdNo,
							Type,
							Color,
							Remarks,
							Date
						FROM vehicle_register_staff
						WHERE RegNo " . LIKE . " '" . $SearchString . "'
						ORDER BY RegNo";
		} elseif (mb_strlen($_POST['idno']) > 0) {
			$_POST['idno'] = mb_strtoupper($_POST['idno']);
			$SQL = "SELECT VehicleNo,
							RegNo,
							Make,
							Name,
							IdNo,
							Type,
							Color,
							Remarks,
							Date
						FROM vehicle_register_staff
						WHERE IdNo " . LIKE . " '%" . $_POST['idno'] . "%'
						ORDER BY IdNo";
		}
	} //one of keywords or SupplierCode was more than a zero length string
	$result = DB_query($SQL);
	if (DB_num_rows($result) == 1) {
		$myrow = DB_fetch_row($result);
		$SingleVisitorReturned = $myrow[0];
	}
	if (isset($SingleVisitorReturned)) { /*there was only one supplier returned */
 	   $_SESSION['VehicleStaffID'] = $SingleVisitorReturned;
	   unset($_POST['Keywords']);
	   unset($_POST['idno']);
	   unset($_POST['Search']);
        } else {
               unset($_SESSION['VehicleStaffID']);
        }
} //end of if search

if (isset($_SESSION['VehicleStaffID'])) {
	$VehicleName = '';
	$SQL = "SELECT RegNo,Make,Type,Name,IdNo,Color,Remarks,description,Date,Rank
			FROM vehicle_register_staff
			INNER JOIN departments ON vehicle_register_staff.departmentid=departments.departmentid
			WHERE VehicleNo ='" . $_SESSION['VehicleStaffID'] . "'";
	$SupplierNameResult = DB_query($SQL);
	if (DB_num_rows($SupplierNameResult) == 1) {
		$myrow = DB_fetch_row($SupplierNameResult);
		$VehicleName = $myrow[0];
	}
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/user.png" title="' . _('Vehicle') . '" alt="" />' . ' ' . _('Vehicle') . ' : <b>' . $_SESSION['VehicleStaffID'] . ' - ' . $VehicleName . '</b> ' . _('has been selected') . '.</p>';
	echo '<div class="page_help_text">' . _('Select a menu option to operate using this Vehicle.') . '</div>';
	echo '<br />
		<table width="90%" cellpadding="4">
		<tr>
			<th style="width:70%">' . _('Vehicle Information') . '</th>
			<th style="width:30%">' . _('Vehicle Maintenance') . '</th>
		</tr>';
	echo '<tr><td valign="top" class="select">'; /* Inquiry Options */
		echo '<table  style="background:#FFFFFF">
			<tr height="30px"><th width="100px">Make :</th><td width="200px" align="left">'.$myrow[1].'</td>
			<th width="100px">Type :</th><td width="200px" align="left">'.$myrow[2].'</td></tr>
			<tr height="30px"><th>Owner Name :</th><td align="left">'.$myrow[3].'</td>
			<th width="100px">ID/SVC No :</th><td width="200px" align="left">'.$myrow[4].'</td></tr>
			<tr height="30px"><th>Rank/Title :</th><td align="left">'.$myrow[9].'</td>
			<th width="100px">Color :</th><td width="200px" align="left">'.$myrow[5].'</td></tr>
			<tr height="30px"><th>Department :</th><td align="left">'.$myrow[7].'</td>
			<th width="120px">Registered Date :</th><td width="200px" align="left">'.ConvertSQLDate($myrow[8]).'</td></tr>
			<tr height="30px"><th>Remarks :</th><td colspan="3" align="left">'.$myrow[6].'</td></tr>
			</table>';
			
	echo '</td><td valign="top" class="select">'; /* Supplier Maintenance */
	echo '<a href="' . $RootPath . '/Sec_VehicleRegisterStaffs.php">' . _('Add a New Vehicle') . '</a>
		<br /><a href="' . $RootPath . '/Sec_VehicleRegisterStaffs.php?VehicleID=' . $_SESSION['VehicleStaffID'] . '">' . _('Modify Or Delete Vehicle Details') . '</a>
		<br />';
	
	echo '</div>';
	echo '</from>';
		echo '</td>
		</tr>
		</table>';
} else {
	// Visitor is not selected yet
	echo '<br />';
	echo '<table width="90%" cellpadding="4">
		<tr>
			<th style="width:33%">' . _('Vehicles Information') . '</th>
			<th style="width:33%">' . _('Vehicles Maintenance') . '</th>
		</tr>';
	echo '<tr>
			<td valign="top" class="select"></td>
			<td valign="top" class="select">'; /* Visitor Maintenance */
	echo '<a href="' . $RootPath . '/Sec_VehicleRegisterStaffs.php">' . _('Add a New Vehicle') . '</a><br />';
	echo '</td>
		</tr>
		</table>';
}
echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/magnifier.png" title="' . _('Search') . '" alt="" />' . ' ' . _('Search for Vehicle') . '</p>
	<table cellpadding="3" class="selection">
	<tr>
		<td>' . _('Enter a partial Reg No') . ':</td>
		<td>';
if (isset($_POST['Keywords'])) {
	echo '<input type="text" name="Keywords" value="' . $_POST['Keywords'] . '" size="20" maxlength="25" />';
} else {
	echo '<input type="text" name="Keywords" size="20" maxlength="25" />';
}
echo '</td>
		<td><b>' . _('OR') . '</b></td>
		<td>' . _('Enter a partial Owner ID/SVC No.') . ':</td>
		<td>';
if (isset($_POST['idno'])) {
	echo '<input type="text" autofocus="autofocus" name="idno" value="' . $_POST['idno'] . '" size="15" maxlength="18" />';
} else {
	echo '<input type="text" autofocus="autofocus" name="idno" size="15" maxlength="18" />';
}
echo '</td></tr>
		</table>
		<br /><div class="centre"><input type="submit" name="Search" value="' . _('Search Now') . '" /></div>';
//if (isset($result) AND !isset($SingleSupplierReturned)) {
if (isset($_POST['Search'])) {
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
	if ($ListPageMax > 1) {
		echo '<p>&nbsp;&nbsp;' . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': </p>';
		echo '<select name="PageOffset">';
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
			<input type="submit" name="Go" value="' . _('Go') . '" />
			<input type="submit" name="Previous" value="' . _('Previous') . '" />
			<input type="submit" name="Next" value="' . _('Next') . '" />';
		echo '<br />';
	}
	echo '<input type="hidden" name="Search" value="' . _('Search Now') . '" />';
	echo '<br />
		<br />
		<br />
		<table cellpadding="2">';
	echo '<tr>
	  		<th class="ascending">' . _('Vehicle No') . '</th>
			<th class="ascending">' . _('Reg Number') . '</th>
			<th class="ascending">' . _('Make') . '</th>
			<th class="ascending">' . _('Owner Name') . '</th>
			<th class="ascending">' . _('ID/SVC Number') . '</th>
			<th class="ascending">' . _('Type') . '</th>
			<th class="ascending">' . _('Reg. Date') . '</th>
		</tr>';
	$k = 0; //row counter to determine background colour
	$RowIndex = 0;
	if (DB_num_rows($result) <> 0) {
		DB_data_seek($result, ($_POST['PageOffset'] - 1) * $_SESSION['DisplayRecordsMax']);
	}
	while (($myrow = DB_fetch_array($result)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {
		if ($k == 1) {
			echo '<tr class="EvenTableRows">';
			$k = 0;
		} else {
			echo '<tr class="OddTableRows">';
			$k = 1;
		}
		echo '<td><input type="submit" name="Select" value="'.$myrow['VehicleNo'].'" /></td>
				<td>' . $myrow['RegNo'] . '</td>
				<td>' . $myrow['Make'] . '</td>
				<td>' . $myrow['Name'] . '</td>
				<td>' . $myrow['IdNo'] . '</td>
				<td>' . $myrow['Type'] . '</td>
				<td>' . ConvertSQLDate($myrow['Date']) . '</td>
			</tr>';
		$RowIndex = $RowIndex + 1;
		//end of page full new headings if
	}
	//end of while loop
	echo '</table>';
}
//end if results to show
if (isset($ListPageMax) and $ListPageMax > 1) {
	echo '<p>&nbsp;&nbsp;' . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': </p>';
	echo '<select name="PageOffset">';
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
		<input type="submit" name="Go" value="' . _('Go') . '" />
		<input type="submit" name="Previous" value="' . _('Previous') . '" />
		<input type="submit" name="Next" value="' . _('Next') . '" />';
	echo '<br />';
}
echo '</div>
      </form>';
include ('includes/footer.inc');
?>