	<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
	<script type="text/javascript" src="js/qsearch.js"></script>									
<?php
/* $Id: Suppliers.php 7088 2015-01-20 08:02:37Z exsonqu $ */

include('includes/session.inc');
$Title = _('Visitor Maintenance');
/* webERP manual links before header.inc */
$ViewTopic= 'Security';
$BookMark = 'NewVisitor';
include('includes/header.inc');

include('includes/SQL_CommonFunctions.inc');

if (isset($_GET['VisitorID'])) {
	$VisitorID = mb_strtoupper($_GET['VisitorID']);
} elseif (isset($_POST['VisitorID'])) {
	$VisitorID = mb_strtoupper($_POST['VisitorID']);
} else {
	unset($VisitorID);
}

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/visitor.png" title="' . _('Search') . '" alt="" />' . ' ' . _('Visitor Booking Register') . '</p>';
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
	$sql="SELECT COUNT(VisitorNo) FROM visitor_register WHERE VisitorNo='".$VisitorID."'";
	$result=DB_query($sql);
	$myrow=DB_fetch_row($result);
	if ($myrow[0]>0 and isset($_POST['New'])) {
		$InputError = 1;
		prnMsg( _('The Visitor number already exists in the database'),'error');
		$Errors[$i] = 'ID';
		$i++;
	}
	if (mb_strlen(trim($_POST['name'])) > 40
		OR mb_strlen(trim($_POST['name'])) == 0
		OR trim($_POST['name']) == '') {

		$InputError = 1;
		prnMsg(_('The visitor name must be entered and be forty characters or less long'),'error');
		$Errors[$i]='Name';
		$i++;
	}
	if (ContainsIllegalCharacters($VisitorID)) {
		$InputError = 1;
		prnMsg(_('The visitor code cannot contain any of the illegal characters') ,'error');
		$Errors[$i]='ID';
		$i++;
	}
	if (ContainsIllegalCharacters($_POST['idno']) && is_numeric($_POST['idno'])) {
		$InputError = 1;
		prnMsg(_('The ID number cannot contain any of the illegal characters') ,'error');
		$Errors[$i]='ID';
		$i++;
	}
	if (mb_strlen($_POST['phoneno']) >25) {
		$InputError = 1;
		prnMsg(_('The telephone number must be 25 characters or less long'),'error');
		$Errors[$i] = 'Telephone';
		$i++;
	}
	if (!Is_Date($_POST['date'])) {
		$InputError = 1;
		prnMsg(_('The date field must be a date in the format') . ' ' . $_SESSION['DefaultDateFormat'],'error');
		$Errors[$i]='Date';
		$i++;
	}


	if ($InputError != 1) {

		$SQL_date = FormatDateForSQL($_POST['date']);

		if (!isset($_POST['New'])) {
				$sql = "UPDATE visitor_register SET v_name='" . $_POST['name'] . "',
							v_idno='" . $_POST['idno'] . "',
							v_phoneno='" . $_POST['phoneno'] . "',
							v_from='" . $_POST['from'] . "',
							host='" . $_POST['host'] . "',
							date='".$SQL_date . "',
							purpose='" . $_POST['purpose'] . "',
							departmentid='". $_POST['departmentid'] ."'
						WHERE VisitorNo = '".$VisitorID."'";

			$ErrMsg = _('The visitor could not be updated because');
			$DbgMsg = _('The SQL that was used to update the visitor but failed was');
			// echo $sql;
			$result = DB_query($sql, $ErrMsg, $DbgMsg);

			prnMsg(_('The visitor master record for') . ' ' . $VisitorID . ' ' . _('has been updated'),'success');

		} else { //its a new visitor
				/* system assigned, sequential, numeric */
				$VisitorID = GetNextTransNo(52, $db);
			
			$sql = "INSERT INTO visitor_register (VisitorNo,
										v_name,
										v_idno,
										v_phoneno,
										v_from,
										host,
										date,
										purpose,
										departmentid)
								 VALUES ('" . $VisitorID . "',
								 	'" . $_POST['name'] . "',
									'" . $_POST['idno'] . "',
									'" . $_POST['phoneno'] . "',
									'" . $_POST['from'] . "',
									'" . $_POST['host'] . "',
									'" . $SQL_date . "',
									'" . $_POST['purpose'] . "',
									'" . $_POST['departmentid'] . "')";

			$ErrMsg = _('The visitor') . ' ' . $_POST['name'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the visitor but failed was');

			$result = DB_query($sql, $ErrMsg, $DbgMsg);

			prnMsg(_('A new visitor for') . ' ' . $_POST['name'] . ' ' . _('has been added to the database'),'success');

			echo '<p>
				<a href="' . $RootPath . '/Sec_Visitors.php?VisitorID=' . $VisitorID . '">' . _('Review Visitor Check In Details') . '</a>
				</p>';

			unset($VisitorID);
			unset($_POST['name']);
			unset($_POST['idno']);
			unset($_POST['phoneno']);
			unset($_POST['from']);
			unset($_POST['host']);
			unset($_POST['purpose']);
			unset($_POST['departmentid']);
			unset($SQL_date);

		}

	} else {

		prnMsg(_('Validation failed') . _('no updates or deletes took place'),'warn');

	}

} elseif (isset($_POST['delete']) AND $_POST['delete'] != '') {

//the link to delete a selected record was clicked instead of the submit button

	$CancelDelete = 0;

// PREVENT DELETES IF DEPENDENT RECORDS IN 'SuppTrans' , PurchOrders, SupplierContacts

	$sql= "SELECT COUNT(*) FROM visitor_timein WHERE VisitorNo='" . $VisitorID . "'";
	$result = DB_query($sql);
	$myrow = DB_fetch_row($result);
	if ($myrow[0] > 0) {
		$CancelDelete = 1;
		prnMsg(_('Cannot delete this visitor because he/she have been checked in'),'warn');
		echo '<br />' . _('There are') . ' ' . $myrow[0] . ' ' . _('transactions against this visitor');

	}
	if ($CancelDelete == 0) {
		$sql="DELETE FROM visitor_register WHERE VisitorNo='" . $VisitorID . "'";
		$result = DB_query($sql);
		prnMsg(_('Visitor record for') . ' ' . $VisitorID . ' ' . _('has been deleted'),'success');
		unset($VisitorID);
		unset($_SESSION['VisitorID']);
	} //end if Delete supplier
}


if (!isset($VisitorID)) {

/*If the page was called without $SupplierID passed to page then assume a new supplier is to be entered show a form with a Supplier Code field other wise the form showing the fields with the existing entries against the supplier will show for editing with only a hidden SupplierID field*/

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
	echo '<div>';
	echo '<a href="' . $RootPath . '/Sec_Visitors.php">Back to Search Visitor</a>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	echo '<input type="hidden" name="New" value="Yes" />';

	echo '<table class="selection">';

	/* if $AutoSupplierNo is off (not 0) then provide an input box for the SupplierID to manually assigned */
	echo '<tr>
			<td>' . _('Visitor Name') . ':</td>
			<td><input type="text" pattern="(?!^\s+$)[^<>+]{1,40}" required="required" title="'._('The Visitor name should not be blank and should be less than 40 legal characters').'" name="name" size="42" placeholder="'._('Within 40 legal characters').'" maxlength="40" /></td>
		</tr>
		<tr>
			<td>' . _('ID Number') . ':</td>
			<td><input type="text" pattern=".{1,40}" required="required" title="'._('The input should be less than 15 characters').'" placeholder="'._('Less than 15 characters').'" name="idno" size="42" maxlength="15" /></td>
		</tr>
		<tr>
			<td>' . _('Telephone No') . ':</td>
			<td><input type="tel" pattern="[\s\d+)(-]{1,40}" title="'._('The input should be phone number').'" placeholder="'._('only number + - ( and ) allowed').'" name="phoneno" size="30" maxlength="40" /></td>
		</tr>
		<tr>
			<td>' . _('Place of Residence') . ':</td>
			<td><input type="text" title="'._('The input should be less than 40 characters').'" placeholder="'._('Less than 40 characters').'" name="from" size="42" maxlength="40" /></td>
		</tr>
		<tr>
			<td>' . _('Host') . ':</td>
			<td><input type="text" pattern="(?!^\s+$)[^<>+]{1,40}" required="required" title="'._('The Host name should not be blank and should be less than 40 legal characters').'" name="host" size="42" placeholder="'._('Within 40 legal characters').'" maxlength="40" id="key" /><br />
		<span id="result" class="result"><span class="loading"></span></span>
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

	$DateString = Date($_SESSION['DefaultDateFormat']);
	echo '<tr>
			<td>' . _('Date') . ' (' . $_SESSION['DefaultDateFormat'] . '):</td>
			<td><input type="text" class="date" alt="' .$_SESSION['DefaultDateFormat'] .'" name="date" value="' . $DateString . '" size="12" maxlength="10" /></td>
		</tr>
		<tr>
			<td>' . _('Purpose') . ':</td>
			<td><textarea name="purpose" cols="40" rows="5"></textarea></td>
		</tr>
	
		</table>
		<br />
		<div class="centre"><input type="submit" name="submit" value="' . _('Insert New Visitor') . '" /></div>';
	echo '</div>
		</form>';
}
else {

//VisitorID exists - either passed when calling the form or from the form itself

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
	echo '<div>';
	echo '<a href="' . $RootPath . '/Sec_Visitors.php">Back to Search Visitor</a>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table class="selection">';

	if (!isset($_POST['New'])) {
		$sql = "SELECT *
			FROM visitor_register
			WHERE VisitorNo = '" . $VisitorID . "'";

		$result = DB_query($sql);
		$myrow = DB_fetch_array($result);

		$_POST['name'] = stripcslashes($myrow['v_name']);
		$_POST['phoneno'] = $myrow['v_phoneno'];
		$_POST['idno'] = $myrow['v_idno'];
		$_POST['from'] = $myrow['v_from'];
		$_POST['host'] = $myrow['host'];
		$_POST['date'] = ConvertSQLDate($myrow['date']);
		$_POST['purpose'] = stripcslashes($myrow['purpose']);
		$_POST['departmentid'] = $myrow['departmentid'];

		echo '<tr><td><input type="hidden" name="VisitorID" value="' . $VisitorID . '" /></td></tr>';

	}
	// its a new supplier being added

		echo '<tr><td>';
		/* if $AutoSupplierNo is off (i.e. 0) then provide an input box for the SupplierID to manually assigned */
			echo _('Visitor Number') . ':</td>
					<td>' . $VisitorID . '</td></tr>';
	

	echo '<tr>
			<td>' . _('Visitor Name') . ':</td>
			<td><input '.(in_array('Name',$Errors) ? 'class="inputerror"' : '').' type="text" name="name" value="' . $_POST['name'] . '" size="42" maxlength="40" /></td>
		</tr>
		<tr>
			<td>' . _('ID Number') . ':</td>
			<td><input type="text" name="idno" value="' . $_POST['idno'] . '" size="42" maxlength="15" /></td>
		</tr>';
		
	echo '<tr>
			<td>' . _('Telephone No') . ':</td>
			<td><input '.(in_array('Name',$Errors) ? 'class="inputerror"' : '').' type="tel" pattern="[\s\d+()-]{1,40}" placeholder="'._('Only digit blank ( ) and - allowed').'" name="phoneno" value="' . $_POST['phoneno'] . '" size="42" maxlength="40" /></td>
		</tr>
		<tr>
			<td>' . _('Place of Residence') . ':</td>
			<td><input type="text" name="from" size="42" maxlength="40" value="' . $_POST['from'] . '" /></td>
		</tr>
		<tr>
			<td>' . _('Host') . ':</td>
			<td><input '.(in_array('BankRef',$Errors) ? 'class="inputerror"' : '').' type="text" name="host" size="42" maxlength="40" value="' . $_POST['host'] . '" /></td>
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
			<td>' . _('Date') . ' (' . $_SESSION['DefaultDateFormat'] . '):</td>
			<td><input type="text" class="date" alt="' .$_SESSION['DefaultDateFormat'] .'" name="date" value="' . $_POST['date'] . '" size="12" maxlength="10" /></td>
		</tr>
		<tr>
			<td>' . _('Purpose') . ':</td>
			<td><textarea name="purpose" cols="40" rows="5">'. $_POST['purpose'] .'</textarea></td>
		</tr>
	
		</table>
		</table>';

	if (isset($_POST['New'])) {
		echo '<br />
				<div class="centre">
					<input type="submit" name="submit" value="' . _('Add These New Visitor Details') . '" />
				</div>';
	} else {
		echo '<br />
				<div class="centre">
					<input type="submit" name="submit" value="' . _('Update Visitor') . '" />
				</div>
			<br />';
//		echo '<p><font color=red><b>' . _('WARNING') . ': ' . _('There is no second warning if you hit the delete button below') . '. ' . _('However checks will be made to ensure there are no outstanding purchase orders or existing accounts payable transactions before the deletion is processed') . '<br /></font></b>';
		prnMsg(_('WARNING') . ': ' . _('There is no second warning if you hit the delete button below') . '. ', 'Warn');
		echo '<br />
			<div class="centre">
				<input type="submit" name="delete" value="' . _('Delete Visitor') . '" onclick="return confirm(\'' . _('Are you sure you wish to delete this Visitor?') . '\');" />
			</div>';
	}
	echo '</div>
		</form>';
} // end of main ifs

include('includes/footer.inc');
?>
