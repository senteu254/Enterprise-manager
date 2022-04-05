<?php
/* $Id: LocationUsers.php 6806 2013-09-28 05:10:46Z daintree $*/

include('includes/session.inc');
$Title = _('Work Orders Authorised Users Maintenance');
$ViewTopic = 'Production';// Filename in ManualContents.php's TOC.
$BookMark = 'WorkOrderUsers';// Anchor's id in the manual's html document.
include('includes/header.inc');

echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/money_add.png" title="' . _('Location Authorised Users') . '" alt="" />' . ' ' . $Title . '</p>';

if (isset($_POST['SelectedUser'])) {
	$SelectedUser = mb_strtoupper($_POST['SelectedUser']);
} elseif (isset($_GET['SelectedUser'])) {
	$SelectedUser = mb_strtoupper($_GET['SelectedUser']);
} else {
	$SelectedUser = '';
}



if (isset($_POST['submit'])) {

	$InputError = 0;

	if ($_POST['SelectedUser'] == '') {
		$InputError = 1;
		prnMsg(_('You have not selected an user to be authorised to view work orders'), 'error');
		echo '<br />';
		unset($SelectedLocation);
	}

	if ($InputError != 1) {

		// First check the user is not being duplicated

		$CheckSql = "SELECT count(*)
			     FROM www_users
			     WHERE canviewworkorder= 1
				 AND userid = '" . $_POST['SelectedUser'] . "'";

		$CheckResult = DB_query($CheckSql);
		$CheckRow = DB_fetch_row($CheckResult);

		if ($CheckRow[0] > 0) {
			$InputError = 1;
			prnMsg(_('The user') . ' ' . $_POST['SelectedUser'] . ' ' . _('is already authorised to view work orders'), 'error');
		} else {
			// Add new record on submit
			$SQL = "UPDATE www_users SET canviewworkorder=1 WHERE userid='" . $_POST['SelectedUser'] . "'";

			$msg = _('User') . ': ' . $_POST['SelectedUser'] . ' ' . _('authority to view work orders has been changed');
			$Result = DB_query($SQL);
			prnMsg($msg, 'success');
			unset($_POST['SelectedUser']);
		}
	}
} elseif (isset($_GET['delete'])) {
$SQL = "UPDATE www_users SET canviewworkorder=0 WHERE userid='" . $SelectedUser . "'";
	$ErrMsg = _('The Work Order User record could not be deleted because');
	$Result = DB_query($SQL, $ErrMsg);
	prnMsg(_('User') . ' ' . $SelectedUser . ' ' . _('has had their authority to view work orders Removed'), 'success');
	unset($_GET['delete']);
}


//end of ifs and buts!

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	$SQL = "SELECT userid,realname
			FROM www_users
			WHERE canviewworkorder=1
			ORDER BY userid ASC";

	$Result = DB_query($SQL);

	echo '<table class="selection">';
	echo '<tr>
			<th colspan="6"><h3>' . _('Authorised users to Access Workorders') . '</h3></th>
		</tr>';
	echo '<tr>
			<th>' . _('User Code') . '</th>
			<th>' . _('User Name') . '</th>
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
				<td><a href="%s?SelectedUser=%s&amp;delete=yes" onclick="return confirm(\'' . _('Are you sure you wish to un-authorise this user?') . '\');">' . _('Un-authorise') . '</a></td>
				</tr>',
				$MyRow['userid'],
				$MyRow['realname'],
				htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'),
				$MyRow['userid']);
	}
	//END WHILE LIST LOOP
	echo '</table>';

	if (!isset($_GET['delete'])) {


		echo '<table  class="selection">'; //Main table

		echo '<tr>
				<td>' . _('Select User') . ':</td>
				<td><select name="SelectedUser">';

		$Result = DB_query("SELECT userid,
									realname
							FROM www_users
							WHERE canviewworkorder=0");

		if (!isset($_POST['SelectedUser'])) {
			echo '<option selected="selected" value="">' . _('Not Yet Selected') . '</option>';
		}
		while ($MyRow = DB_fetch_array($Result)) {
			if (isset($_POST['SelectedUser']) and $MyRow['userid'] == $_POST['SelectedUser']) {
				echo '<option selected="selected" value="';
			} else {
				echo '<option value="';
			}
			echo $MyRow['userid'] . '">' . $MyRow['userid'] . ' - ' . $MyRow['realname'] . '</option>';

		} //end while loop

		echo '</select>
					</td>
				</tr>
			</table>'; // close main table
		DB_free_result($Result);

		echo '<div class="centre">
				<input type="submit" name="submit" value="' . _('Accept') . '" />
			</div>
			</form>';

	} // end if user wish to delete

include('includes/footer.inc');
?>
