								
<?php
/* $Id: Suppliers.php 7088 2015-01-20 08:02:37Z exsonqu $ */

include('includes/session.inc');
$Title = _('Vehicle Maintenance');
/* webERP manual links before header.inc */
$ViewTopic= 'Security';
$BookMark = 'NewStaffVehicle';
include('includes/header.inc');

include('includes/SQL_CommonFunctions.inc');

if (isset($_GET['VehicleID'])) {
	$VehicleID = mb_strtoupper($_GET['VehicleID']);
} elseif (isset($_POST['VehicleID'])) {
	$VehicleID = mb_strtoupper($_POST['VehicleID']);
} else {
	unset($VehicleID);
}

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/visitor.png" title="' . _('Search') . '" alt="" />' . ' ' . _('Staff Vehicle Register') . '</p>';
$InputError = 0;

if (isset($Errors)) {
	unset($Errors);
}
$Errors=Array();
if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$i=1;
	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible
	if (mb_strlen(trim($_POST['regno'])) > 10
		OR mb_strlen(trim($_POST['regno'])) == 0
		OR trim($_POST['regno']) == '') {

		$InputError = 1;
		prnMsg(_('The vehicle Registratiron Number must be entered and be ten characters or less long'),'error');
		$Errors[$i]='regno';
		$i++;
	}
	$sql="SELECT COUNT(VehicleNo) FROM vehicle_register_staff WHERE RegNo='".$_POST['regno']."'";
	$result=DB_query($sql);
	$myrow=DB_fetch_row($result);
	if ($myrow[0]>0 and isset($_POST['New'])) {
		$InputError = 1;
		prnMsg( _('The Vehicle Records for '.$_POST['regno'].' already exists in the database'),'error');
		$Errors[$i] = 'ID';
		$i++;
	}
	
	if (ContainsIllegalCharacters($VehicleID)) {
		$InputError = 1;
		prnMsg(_('The vehicle code cannot contain any of the illegal characters') ,'error');
		$Errors[$i]='ID';
		$i++;
	}
	if (ContainsIllegalCharacters($_POST['idno']) && is_numeric($_POST['idno'])) {
		$InputError = 1;
		prnMsg(_('The ID number cannot contain any of the illegal characters') ,'error');
		$Errors[$i]='ID';
		$i++;
	}
	if (mb_strlen($_POST['make']) >15) {
		$InputError = 1;
		prnMsg(_('The Make must be 15 characters or less long'),'error');
		$Errors[$i] = 'make';
		$i++;
	}


	if ($InputError != 1) {

		if (!isset($_POST['New'])) {
				$sql = "UPDATE vehicle_register_staff SET RegNo='" . $_POST['regno'] . "',
							Make='" . $_POST['make'] . "',
							Type='" . $_POST['org'] . "',
							Name='" . $_POST['driver'] . "',
							IdNo='" . $_POST['idno'] . "',
							Color='" . $_POST['color'] . "',
							Rank='" . $_POST['rank'] . "',
							Remarks='" . $_POST['remarks'] . "',
							departmentid='". $_POST['departmentid'] ."'
						WHERE VehicleNo = '".$VehicleID."'";

			$ErrMsg = _('The vehicle could not be updated because');
			$DbgMsg = _('The SQL that was used to update the visitor but failed was');
			// echo $sql;
			$result = DB_query($sql, $ErrMsg, $DbgMsg);

			prnMsg(_('The vehicle master record for') . ' ' . $VehicleID . ' ' . _('has been updated'),'success');

		} else { //its a new visitor
				/* system assigned, sequential, numeric */
				$VehicleID = GetNextTransNo(54, $db);
			
			$sql = "INSERT INTO vehicle_register_staff (VehicleNo,
										RegNo,
										Make,
										Type,
										Name,
										IdNo,
										Color,
										Rank,
										Remarks,
										departmentid)
								 VALUES ('" . $VehicleID . "',
								 	'" . $_POST['regno'] . "',
									'" . $_POST['make'] . "',
									'" . $_POST['org'] . "',
									'" . $_POST['driver'] . "',
									'" . $_POST['idno'] . "',
									'" . $_POST['color'] . "',
									'" . $_POST['rank'] . "',
									'" . $_POST['remarks'] . "',
									'" . $_POST['departmentid'] . "')";

			$ErrMsg = _('The vehicle') . ' ' . $_POST['regno'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the visitor but failed was');

			$result = DB_query($sql, $ErrMsg, $DbgMsg);

			prnMsg(_('A new vehicle for') . ' ' . $_POST['regno'] . ' ' . _('has been added to the database'),'success');

			echo '<p>
				<a href="' . $RootPath . '/Sec_VehiclesStaff.php?VehicleID=' . $VehicleID . '">' . _('Review Vehicle Details') . '</a>
				</p>';

			unset($VehicleID);
			unset($_POST['regno']);
			unset($_POST['make']);
			unset($_POST['org']);
			unset($_POST['driver']);
			unset($_POST['idno']);
			unset($_POST['color']);
			unset($_POST['remarks']);
			unset($_POST['rank']);
			unset($_POST['departmentid']);

		}

	} else {

		prnMsg(_('Validation failed ') . _('no updates or deletes took place'),'warn');

	}

} elseif (isset($_POST['delete']) AND $_POST['delete'] != '') {

//the link to delete a selected record was clicked instead of the submit button

	$CancelDelete = 0;

// PREVENT DELETES IF DEPENDENT RECORDS IN 'SuppTrans' , PurchOrders, SupplierContacts

	if ($CancelDelete == 0) {
		$sql="DELETE FROM vehicle_register_staff WHERE VehicleNo='" . $VehicleID . "'";
		$result = DB_query($sql);
		prnMsg(_('Vehicle record for') . ' ' . $VehicleID . ' ' . _('has been deleted'),'success');
		unset($VehicleID);
		unset($_SESSION['VehicleStaffID']);
	} //end if Delete supplier
}


if (!isset($VehicleID)) {

/*If the page was called without $SupplierID passed to page then assume a new supplier is to be entered show a form with a Supplier Code field other wise the form showing the fields with the existing entries against the supplier will show for editing with only a hidden SupplierID field*/

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
	echo '<div>';
	echo '<a href="' . $RootPath . '/Sec_VehiclesStaff.php">Back to Search Vehicles</a>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	echo '<input type="hidden" name="New" value="Yes" />';

	echo '<table class="selection">';

	/* if $AutoSupplierNo is off (not 0) then provide an input box for the SupplierID to manually assigned */
	echo '<tr>
			<td>' . _('Vehicle Reg No.') . ':</td>
			<td><input type="text" pattern="(?!^\s+$)[^<>+]{1,10}" required="required" title="'._('The Vehicle RegNo should not be blank and should be less than 10 legal characters').'" name="regno" size="42" placeholder="'._('Within 10 legal characters').'" maxlength="10" /></td>
		</tr>
		<tr>
			<td>' . _('Make') . ':</td>
			<td><input type="text" required="required" title="'._('The input should be less than 15 characters').'" placeholder="'._('Less than 15 characters').'" name="make" size="42" maxlength="15" /></td>
		</tr>
		<tr>
			<td>' . _('Type') . ':</td>
			<td><input type="text" required="required" title="'._('The input should be less than 40 characters').'" placeholder="'._('Less than 40 characters').'" name="org" size="42" maxlength="40" /></td>
		</tr>
		<tr>
			<td>' . _('Color') . ':</td>
			<td><input type="text" required="required" title="'._('The input should be less than 20 characters').'" placeholder="'._('Less than 20 characters').'" name="color" size="22" maxlength="20" /></td>
		</tr>
		<tr>
			<td>' . _('Owner Name') . ':</td>
			<td><input type="text" required="required" title="'._('The input should be less than 40 characters').'" placeholder="'._('Less than 40 characters').'" name="driver" size="42" maxlength="40" /></td>
		</tr>
		<tr>
			<td>' . _('ID/ SVC No.') . ':</td>
			<td><input type="text" pattern=".{1,40}" required="required" title="'._('The input should be less than 15 characters').'" placeholder="'._('Less than 15 characters').'" name="idno" size="42" maxlength="15" /></td>
		</tr>
		<tr>
			<td>' . _('Rank/Title') . ':</td>
			<td><input type="text" pattern="(?!^\s+$)[^<>+]{1,40}" required="required" name="rank" size="42" placeholder="'._('Within 20 legal characters').'" maxlength="20" />
		</td>
		</tr>
		<tr>
			<td>' . _('Department') . ':</td>
			<td><select required="required" name="departmentid">';
			echo '<option selected="selected" value="">--Please Select Department--</option>';
	$result=DB_query("SELECT departmentid, description FROM departments");
	while ($myrow = DB_fetch_array($result)) {
		echo '<option value="' . $myrow['departmentid'] . '">' . $myrow['description'] . '</option>';
	} //end while loop
	echo '</select></td>
		</tr>';
	echo '
		<tr>
			<td>' . _('Remarks') . ':</td>
			<td><textarea name="remarks" cols="40" rows="5"></textarea></td>
		</tr>
	
		</table>
		<br />
		<div class="centre"><input type="submit" name="submit" value="' . _('Insert New Vehicle') . '" /></div>';
	echo '</div>
		</form>';
}
else {

//VisitorID exists - either passed when calling the form or from the form itself

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
	echo '<div>';
	echo '<a href="' . $RootPath . '/Sec_VehiclesStaff.php">Back to Search Vehicles</a>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table class="selection">';

	if (!isset($_POST['New'])) {
		$sql = "SELECT *
			FROM vehicle_register_staff
			WHERE VehicleNo = '" . $VehicleID . "'";

		$result = DB_query($sql);
		$myrow = DB_fetch_array($result);

		$_POST['regno'] = stripcslashes($myrow['RegNo']);
		$_POST['make'] = $myrow['Make'];
		$_POST['org'] = $myrow['Type'];
		$_POST['driver'] = $myrow['Name'];
		$_POST['idno'] = $myrow['IdNo'];
		$_POST['rank'] = $myrow['Rank'];
		$_POST['color'] = $myrow['Color'];
		$_POST['remarks'] = stripcslashes($myrow['Remarks']);
		$_POST['departmentid'] = $myrow['departmentid'];

		echo '<tr><td><input type="hidden" name="VehicleID" value="' . $VehicleID . '" /></td></tr>';

	}
	// its a new supplier being added

		echo '<tr><td>';
		/* if $AutoSupplierNo is off (i.e. 0) then provide an input box for the SupplierID to manually assigned */
			echo _('Vehicle Number') . ':</td>
					<td>' . $VehicleID . '</td></tr>';
	

	echo '<tr>
			<td>' . _('Vehicle Reg No.') . ':</td>
			<td><input '.(in_array('regno',$Errors) ? 'class="inputerror"' : '').' type="text" name="regno" value="' . $_POST['regno'] . '" size="42" maxlength="40" /></td>
		</tr>
		<tr>
			<td>' . _('Make') . ':</td>
			<td><input type="text" name="make" value="' . $_POST['make'] . '" size="42" maxlength="40" /></td>
		</tr>';
		
	echo '<tr>
			<td>' . _('Type') . ':</td>
			<td><input '.(in_array('org',$Errors) ? 'class="inputerror"' : '').' type="text" name="org" value="' . $_POST['org'] . '" size="42" maxlength="40" /></td>
		</tr>
		<tr>
			<td>' . _('Color') . ':</td>
			<td><input '.(in_array('color',$Errors) ? 'class="inputerror"' : '').' type="text" name="color" value="' . $_POST['color'] . '" size="42" maxlength="40" /></td>
		</tr>
		<tr>
			<td>' . _('Owner Name') . ':</td>
			<td><input type="text" name="driver" size="42" maxlength="40" value="' . $_POST['driver'] . '" /></td>
		</tr>
		<tr>
			<td>' . _('ID/SVC No.') . ':</td>
			<td><input type="text" name="idno" value="' . $_POST['idno'] . '" size="42" maxlength="15" /></td>
		</tr>
		<tr>
			<td>' . _('Rank/Title') . ':</td>
			<td><input '.(in_array('rank',$Errors) ? 'class="inputerror"' : '').' type="text" name="rank" size="42" maxlength="40" value="' . $_POST['rank'] . '" /></td>
		</tr>';
	echo '<tr>
			<td>' . _('Department') . ':</td>
			<td><select required="required" name="departmentid">';
			echo '<option selected="selected" value="">--Please Select Department--</option>';
	$result=DB_query("SELECT departmentid, description FROM departments");
	while ($myrow = DB_fetch_array($result)) {
	if ($_POST['departmentid'] == $myrow['departmentid']) {
		echo '<option selected="selected" value="' . $myrow['departmentid'] . '">' . $myrow['description'] . '</option>';
		} else {
		echo '<option value="' . $myrow['departmentid'] . '">' . $myrow['description'] . '</option>';
		}
		
	} //end while loop
	echo '</select></td>
		</tr>';
	echo '<tr>
			<td>' . _('Remarks') . ':</td>
			<td><textarea name="remarks" cols="40" rows="5">'. $_POST['remarks'] .'</textarea></td>
		</tr>
	
		</table>
		</table>';

	if (isset($_POST['New'])) {
		echo '<br />
				<div class="centre">
					<input type="submit" name="submit" value="' . _('Add These New Vehicle Details') . '" />
				</div>';
	} else {
		echo '<br />
				<div class="centre">
					<input type="submit" name="submit" value="' . _('Update Vehicle') . '" />
				</div>
			<br />';
//		echo '<p><font color=red><b>' . _('WARNING') . ': ' . _('There is no second warning if you hit the delete button below') . '. ' . _('However checks will be made to ensure there are no outstanding purchase orders or existing accounts payable transactions before the deletion is processed') . '<br /></font></b>';
		prnMsg(_('WARNING') . ': ' . _('There is no second warning if you hit the delete button below') . '. ', 'Warn');
		echo '<br />
			<div class="centre">
				<input type="submit" name="delete" value="' . _('Delete Vehicle') . '" onclick="return confirm(\'' . _('Are you sure you wish to delete this Vehicle?') . '\');" />
			</div>';
	}
	echo '</div>
		</form>';
} // end of main ifs

include('includes/footer.inc');
?>
