<?php
/* $Id: LocationUsers.php 6806 2013-09-28 05:10:46Z daintree $*/

include('includes/session.inc');
$Title = _('Procurement Cells Maintenance');
$ViewTopic = 'Procurement Cells Maintenance';// Filename in ManualContents.php's TOC.
$BookMark = 'Procurement Cells Maintenance';// Anchor's id in the manual's html document.
include('includes/header.inc');

echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/money_add.png" title="' . _('Procurement Cells Maintenance') . '" alt="" />' . ' ' . $Title . '</p>';

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
		prnMsg(_('You have not selected any Location'), 'error');
		echo '<br />';
		unset($SelectedLocation);
		unset($_POST['SelectedLocation']);
	}
}

if (isset($_POST['submit'])) {

	$InputError = 0;

	if ($_POST['SelectedUser'] == '') {
		$InputError = 1;
		prnMsg(_('You have not selected an user to be authorised to use this Location'), 'error');
		echo '<br />';
		unset($SelectedLocation);
	}

	if ($InputError != 1) {

		// First check the user is not being duplicated

		$CheckSql = "SELECT count(*)
			     FROM data_cell
			     WHERE cell= '" . $_POST['SelectedLocation'] . "'
				 AND c_name = '" . $_POST['SelectedUser'] . "'";

		$CheckResult = DB_query($CheckSql);
		$CheckRow = DB_fetch_row($CheckResult);

		if ($CheckRow[0] > 0) {
			$InputError = 1;
			prnMsg(_('The user') . ' ' . $_POST['SelectedUser'] . ' ' . _('is already authorised to use this location'), 'error');
		} else {
			// Add new record on submit
			$SQL = "INSERT INTO data_cell (cell,
										   c_name)
								VALUES ('" . $_POST['SelectedLocation'] . "',
										'" . $_POST['SelectedUser'] . "')";

			$msg = _('cell') . ': ' . $_POST['SelectedUser'] . ' ' . _('authority to use the') . ' ' . $_POST['SelectedLocation'] . ' ' . _('cell has been changed');
			$Result = DB_query($SQL);
			prnMsg($msg, 'success');
			unset($_POST['SelectedUser']);
		}
	}
} elseif (isset($_GET['delete'])) {
	$SQL = "DELETE FROM data_cell
		WHERE cell='" . $SelectedLocation . "'
		AND c_name='" . $SelectedUser . "'";

	$ErrMsg = _('The cell record could not be deleted because');
	$Result = DB_query($SQL, $ErrMsg);
	prnMsg(_('cell') . ' ' . $SelectedUser . ' ' . _('has had their authority to use the') . ' ' . $SelectedLocation . ' ' . _('cell removed'), 'success');
	unset($_GET['delete']);
} 

if (!isset($SelectedLocation)) {

	/* It could still be the second time the page has been run and a record has been selected for modification - SelectedUser will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
	then none of the above are true. These will call the same page again and allow update/input or deletion of the records*/
	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
			<table class="selection">
			<tr>
				<td>' . _('Select Cell') . ':</td>
				<td><select name="SelectedLocation">';

	$Result = DB_query("SELECT cell_code,
								cell_name
						FROM cell_maintenance");

	echo '<option value="">' . _('Not Yet Selected') . '</option>';
	while ($MyRow = DB_fetch_array($Result)) {
		if (isset($SelectedLocation) and $MyRow['cell_code'] == $SelectedLocation) {
			echo '<option selected="selected" value="';
		} else {
			echo '<option value="';
		}
		echo $MyRow['cell_code'] . '">' . $MyRow['cell_code'] . ' - ' . $MyRow['cell_name'] . '</option>';

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
	$SQLName = "SELECT cell_name
			FROM cell_maintenance
			WHERE cell_code='" . $SelectedLocation . "'";
	$Result = DB_query($SQLName);
	$MyRow = DB_fetch_array($Result);
	$SelectedLocationName = $MyRow['cell_name'];

	echo '<div class="centre"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">' . _('Authorised users for') . ' ' . $SelectedLocationName . ' ' . _('Location') . '</a></div>
		<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
		<input type="hidden" name="SelectedLocation" value="' . $SelectedLocation . '" />';

	$SQL = "SELECT  * ,a.cell,b.loccode,b.locationname FROM data_cell a
	                   INNER JOIN locations b ON a.c_name=b.loccode
					   WHERE cell='" . $SelectedLocation . "'
					   ORDER BY a.cell ASC";

	$Result = DB_query($SQL);

	echo '<table class="selection">';
	echo '<tr>
			<th colspan="6"><h3>' . _('Location for Cell') . ': ' . $SelectedLocationName . '</h3></th>
		</tr>';
	echo '<tr>
			<th>' . _('Location Code') . '</th>
			<th>' . _('Location Name') . '</th>
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
				<td><a href="%s?SelectedUser=%s&amp;delete=yes&amp;SelectedLocation=' . $SelectedLocation . '" onclick="return confirm(\'' . _('Are you sure you wish to un-authorise this user?') . '\');">' . _('Un-authorise') . '</a></td>
				</tr>',
				$MyRow['loccode'],
				$MyRow['locationname'],
				htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'),
				$MyRow['cell']);
	}
	//END WHILE LIST LOOP
	echo '</table>';

	if (!isset($_GET['delete'])) {


		echo '<table  class="selection">'; //Main table

		echo '<tr>
				<td>' . _('Select location') . ':</td>
				<td><select name="SelectedUser">';

		$Result = DB_query("SELECT * FROM locations");

		if (!isset($_POST['SelectedUser'])) {
			echo '<option selected="selected" value="">' . _('Not Yet Selected') . '</option>';
		}
		while ($MyRow = DB_fetch_array($Result)) {
			if (isset($_POST['SelectedUser']) and $MyRow['loccode'] == $_POST['SelectedUser']) {
				echo '<option selected="selected" value="';
			} else {
				echo '<option value="';
			}
			echo $MyRow['loccode'] . '">' . $MyRow['loccode'] . ' - ' . $MyRow['locationname'] . '</option>';

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
