<?php
/* $Id: Z_ChangeSupplierCode.php 4466 2011-01-13 09:33:59Z daintree $*/
/* This script is an utility to change a supplier code. */

include ('includes/session.inc');
$Title = _('UTILITY PAGE To Changes A Employee Service Number In All Tables');// Screen identificator.
$ViewTopic = 'SpecialUtilities'; // Filename's id in ManualContents.php's TOC.
$BookMark = 'Z_ChangeEmployeeCode'; // Anchor's id in the manual's html document
include('includes/header.inc');
echo '<p class="page_title_text"><img alt="" src="'.$RootPath.'/css/'.$Theme.
	'/images/supplier.png" title="' . 
	_('Change A Employee Service Number') . '" /> ' .// Icon title.
	_('Change A Employee Service Number') . '</p>';// Page title.

if (isset($_POST['ProcessSupplierChange']))
	ProcessSupplier($_POST['OldSupplierNo'], $_POST['NewSupplierNo']);

echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '" method="post">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

echo '
	<div class="centre">
	<table>
	<tr><td>' . _('Existing Employee Service Number') . ':</td>
		<td><input type="text" name="OldSupplierNo" size="20" maxlength="20" /></td>
	</tr>
		<tr><td> ' . _('New Employee Service Number') . ':</td>
	<td><input type="text" name="NewSupplierNo" size="20" maxlength="20" /></td>
	</tr>
	</table>
	<button type="submit" name="ProcessSupplierChange">' . _('Process') . '</button>
	<div>
	</form>';

include('includes/footer.inc');
exit();


function ProcessSupplier($oldCode, $newCode) {
	global $db;

	// First check the Supplier code exists
	if (!checkSupplierExist($oldCode)) {
		prnMsg ('<br /><br />' . _('The Employee Service Number') . ': ' . $oldCode . ' ' .
				_('does not currently exist as a Employee Service Number in the system'),'error');
		return;
	}
	if (checkUserExist($oldCode)) {
		prnMsg ('<br /><br />' . _('The Employee Service Number') . ': ' . $oldCode . ' ' .
				_('has already an active user account in the system'),'error');
		return;
	}
	$newCode = trim($newCode);
	if (checkNewCode($newCode)) {
		// Now check that the new code doesn't already exist
		if (checkSupplierExist($newCode)) {
				prnMsg(_('The replacement Employee Service Number') .': ' .
						$newCode . ' ' . _('already exists as a Employee Service Number in the system') . ' - ' . _('a unique Employee Service Number must be entered for the new code'),'error');
				return;
		}
	} else {
		return;
	}

	$result = DB_Txn_Begin();

	prnMsg(_('Inserting the new employee record'),'info');
	$sql = "INSERT INTO employee (`emp_id`, `emp_fname`, `emp_lname`, `emp_mname`, `emp_bday`, `emp_gen`, `emp_add`, `emp_stat`, `emp_cont`, `bank_name`, `branch`, `account_no`, `email`, `band`, `appointment_name`, `datecurrentapp`, `grade`, `emp_pos`, `id_pos`, `id_dept`, `id_sec`, `addedby`, `stat`, `id_number`, `pin`, `dlicence_no`, `personnel`, `exitdate`, `reasonforexit`, `nhif`, `nssf`, `ethnicity`, `pwd`, `disability`, `imagepath`, `appointment_category`)
	SELECT '" . $newCode . "',
		`emp_fname`, `emp_lname`, `emp_mname`, `emp_bday`, `emp_gen`, `emp_add`, `emp_stat`, `emp_cont`, `bank_name`, `branch`, `account_no`, `email`, `band`, `appointment_name`, `datecurrentapp`, `grade`, `emp_pos`, `id_pos`, `id_dept`, `id_sec`, `addedby`, `stat`, `id_number`, `pin`, `dlicence_no`, `personnel`, `exitdate`, `reasonforexit`, `nhif`, `nssf`, `ethnicity`, `pwd`, `disability`, `imagepath`, `appointment_category` FROM `employee` WHERE emp_id='" . $oldCode . "'";

	$DbgMsg =_('The SQL that failed was');
	$ErrMsg = _('The SQL to insert the new employee master record failed') . ', ' . _('the SQL statement was');
	$result = DB_query($sql,$ErrMsg,$DbgMsg,true);

	prnMsg(_('Deleting the employee number from the employee master table'),'info');
	$sql = "DELETE FROM employee WHERE emp_id='" . $oldCode . "'";

	$ErrMsg = _('The SQL to delete the old employee record failed');
	$result = DB_query($sql,$ErrMsg,$DbgMsg,true);

	$result = DB_Txn_Commit();
}

function checkSupplierExist($code) {
	global $db;
	$result=DB_query("SELECT emp_id FROM employee WHERE emp_id='" . $code . "'");
	if (DB_num_rows($result)==0) return false;
	return true;
}

function checkUserExist($code) {
	global $db;
	$result=DB_query("SELECT emp_id FROM www_users WHERE emp_id='" . $code . "'");
	if (DB_num_rows($result)==0) return false;
	return true;
}

function checkNewCode($code) {
	$tmp = str_replace(' ','',$code);
	if ($tmp != $code) {
		prnMsg ('<br /><br />' . _('The New employee number') . ': ' . $code . ' ' .
				_('must be not empty nor with spaces'),'error');
		return false;
	}
	return true;
}
?>
