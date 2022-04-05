<?php
/* $Id: LocationUsers.php 6806 2013-09-28 05:10:46Z daintree $*/

include('includes/session.inc');
$Title = _('Prequalified Suppliers');
$ViewTopic = 'Prequalified Suppliers';// Filename in ManualContents.php's TOC.
$BookMark = 'Prequalified Suppliers';// Anchor's id in the manual's html document.
include('includes/header.inc');

echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/money_add.png" title="' . _('Prequalified Suppliers') . '" alt="" />' . ' ' . $Title . '</p>';

if (isset($_POST['SelectedUser'])) {
	$SelectedUser = mb_strtoupper($_POST['SelectedUser']);
} elseif (isset($_GET['SelectedUser'])) {
	$SelectedUser = mb_strtoupper($_GET['SelectedUser']);
} else {
	$SelectedUser = '';
}

if (isset($_POST['SelectedLocation'])) {
	$SelectedLocation = mb_strtoupper($_POST['SelectedLocation']);
} elseif (isset($_GET['SelectedLocation'])) {
	$SelectedLocation = mb_strtoupper($_GET['SelectedLocation']);
}

if (isset($_POST['Cancel'])) {
	unset($SelectedLocation);
	unset($SelectedUser);
}

if (isset($_POST['Process'])) {
	if ($_POST['SelectedLocation'] == '') {
		prnMsg(_('You have not selected any Category'), 'error');
		echo '<br />';
		unset($SelectedLocation);
		unset($_POST['SelectedLocation']);
	}
}

if (isset($_POST['submit'])) {

	$InputError = 0;

	if ($_POST['SelectedUser'] == '') {
		$InputError = 1;
		prnMsg(_('You have not selected Supplier to be categorised'), 'error');
		echo '<br />';
		unset($SelectedLocation);
	}

	if ($InputError != 1) {

		// First check the user is not being duplicated

		$CheckSql = "SELECT count(*)
			     FROM prequalifiedsuppliers
			     WHERE CatID= '" . $_POST['SelectedLocation'] . "'
				 AND supprid = '" . $_POST['SelectedUser'] . "'";

		$CheckResult = DB_query($CheckSql);
		$CheckRow = DB_fetch_row($CheckResult);

		if ($CheckRow[0] > 0) {
			$InputError = 1;
			prnMsg(_('The user') . ' ' . $_POST['SelectedUser'] . ' ' . _('is already categorised Under this category'), 'error');
		} else {
			$Checkname = "SELECT suppname
			     FROM suppliers
			     WHERE supplierid = '" . $_POST['SelectedUser'] . "'";

		$CheckResultname = DB_query($Checkname);
		while ($SRow  = DB_fetch_array($CheckResultname)){	  
		$Name =$SRow['suppname'];
		}
		
			// Add new record on submit
			$SQL = "INSERT INTO prequalifiedsuppliers (CatID,
												supprid,
												name,
												FY,
												dateAdded,
												canview,
												canupd)
										VALUES ('" . $_POST['SelectedLocation'] . "',
												'" . $_POST['SelectedUser'] . "',
												'" . $Name . "',
												'".  Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],0)) . "',
												'" .date('Y.m.d'). "',
												'1',
												'1')";

			$msg = _('Supplier') . ': ' . $_POST['SelectedUser'] . ' ' . _('has been categorised under') . ' ' . $_POST['SelectedLocation'] . ' ' . _('.');
			$Result = DB_query($SQL);
			prnMsg($msg, 'success');
			unset($_POST['SelectedUser']);
		}
	}
} elseif (isset($_GET['delete'])) {
	$SQL = "DELETE FROM prequalifiedsuppliers
		WHERE CatID='" . $SelectedLocation . "'
		AND supprid='" . $SelectedUser . "'";

	$ErrMsg = _('The Supplier record could not be deleted because');
	$Result = DB_query($SQL, $ErrMsg);
	prnMsg(_('Supplier') . ' ' . $SelectedUser . ' ' . _('has had their authority to use the') . ' ' . $SelectedLocation . ' ' . _(''), 'success');
	unset($_GET['delete']);
} elseif (isset($_GET['ToggleUpdate'])) {
	$SQL = "UPDATE prequalifiedsuppliers
			SET canupd='" . $_GET['ToggleUpdate'] . "'
			WHERE CatID='" . $SelectedLocation . "'
			AND supprid='" . $SelectedUser . "'";

	$ErrMsg = _('The Supplier user record could not be deleted because');
	$Result = DB_query($SQL, $ErrMsg);
	prnMsg(_('Supplier') . ' ' . $SelectedUser . ' ' . _('has had their authority to update') . ' ' . $SelectedLocation . ' ' . _('Supplier removed'), 'success');
	unset($_GET['ToggleUpdate']);
}

if (!isset($SelectedLocation)) {

	/* It could still be the second time the page has been run and a record has been selected for modification - SelectedUser will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
	then none of the above are true. These will call the same page again and allow update/input or deletion of the records*/
	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
			<table class="selection">
			<tr>
				<td>' . _('Select Category') . ':</td>
				<td><select name="SelectedLocation">';

	$Result = DB_query("SELECT typeid, typename FROM suppliertype");

	echo '<option value="">' . _('Not Yet Selected') . '</option>';
	while ($MyRow = DB_fetch_array($Result)) {
		if (isset($SelectedLocation) and $MyRow['typeid'] == $SelectedLocation) {
			echo '<option selected="selected" value="';
		} else {
			echo '<option value="';
		}
		echo $MyRow['typeid'] . '">' . $MyRow['typeid'] . ' - ' . $MyRow['typename'] . '</option>';

	} //end while loop

	echo '</select></td></tr>';

	echo '</table>'; // close main table
	DB_free_result($Result);

	echo '<div class="centre">
			<input type="submit" name="Process" value="' . _('Accept') . '" />
			<input type="submit" name="Cancel" value="' . _('Cancel') . '" />
		</div>';

	echo '</form>';

}

//end of ifs and buts!
if (isset($_POST['process']) or isset($SelectedLocation)) {
	$SQLName = "SELECT typeid, typename FROM suppliertype
			      WHERE typeid='" . $SelectedLocation . "'";
	$Result = DB_query($SQLName);
	$MyRow = DB_fetch_array($Result);
	$SelectedLocationName = $MyRow['typename'];

	echo '<div class="centre"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">' . _('Categorised Suppliers for') . ' ' . $SelectedLocationName . ' ' . _('') . '</a></div>
		<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
		<input type="hidden" name="SelectedLocation" value="' . $SelectedLocation . '" />';

	$SQL = "SELECT * FROM prequalifiedsuppliers 
	        INNER JOIN suppliers ON prequalifiedsuppliers.supprid=suppliers.supplierid
			WHERE prequalifiedsuppliers.CatID='" . $SelectedLocation . "'
			ORDER BY prequalifiedsuppliers.supprid ASC";

	$Result = DB_query($SQL);

	echo '<table class="selection">';
	echo '<tr>
			<th colspan="6"><h3>' . _('Select Suppliers for') . ': ' . $SelectedLocationName . '</h3></th>
		</tr>';
	echo '<tr>
			<th>' . _('Supplier Code') . '</th>
			<th>' . _('Supplier Name') . '</th>
			<th>' . _('Address No.') . '</th>
			<th>' . _('Telephone No.') . '</th>
			<th>' . _('Email Address') . '</th>
			<th>' . _('Update') . '</th>
		</tr>';

	$k = 0; //row colour counter

	while ($MyRow = DB_fetch_array($Result)) {
		if ($k == 1) {
			echo '<tr class="EvenTableRows">';
			$k = 0;
		} else {
			echo '<tr class="OddTableRows">';
			$k = 1;
		}

		printf('<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td><a href="%s?SelectedUser=%s&amp;delete=yes&amp;SelectedLocation=' . $SelectedLocation . '" onclick="return confirm(\'' . _('Are you sure you wish to un-authorise this user?') . '\');">' . _('Un-authorise') . '</a></td>
				</tr>',
				$MyRow['supplierid'],
				$MyRow['suppname'],
				$MyRow['address1'],
				$MyRow['telephone'],
				$MyRow['email'],
				htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'),
				$MyRow['supprid']);
	}
	//END WHILE LIST LOOP
	echo '</table>';

	if (!isset($_GET['delete'])) {


		echo '<table  class="selection">'; //Main table

		echo '<tr>
				<td>' . _('Select Supplier') . ':</td>
				<td><select name="SelectedUser">';

		$Result = DB_query("SELECT * FROM suppliers");

		if (!isset($_POST['SelectedUser'])) {
			echo '<option selected="selected" value="">' . _('Not Yet Selected') . '</option>';
		}
		while ($MyRow = DB_fetch_array($Result)) {
			if (isset($_POST['SelectedUser']) and $MyRow['supplierid'] == $_POST['SelectedUser']) {
				echo '<option selected="selected" value="';
			} else {
				echo '<option value="';
			}
			echo $MyRow['supplierid'] . '">' . $MyRow['supplierid'] . ' - ' . $MyRow['suppname'] . '</option>';

		} //end while loop

		echo '</select>
					</td>
				</tr>
			</table>'; // close main table
		DB_free_result($Result);

		echo '<div class="centre">
				<input type="submit" name="submit" value="' . _('Accept') . '" />
				<input type="submit" name="Cancel" value="' . _('Cancel') . '" />
			</div>
			</form>';

	} // end if user wish to delete
}

include('includes/footer.inc');
?>
