<?php
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
		prnMsg(_('You have not selected any Role'), 'error');
		echo '<br />';
		unset($SelectedLocation);
		unset($_POST['SelectedLocation']);
	}
}

if (isset($_POST['submit'])) {

	$InputError = 0;

	if ($_POST['SelectedUser'] == '') {
		$InputError = 1;
		prnMsg(_('You have not selected an user to be authorised to use this Role'), 'error');
		echo '<br />';
		unset($SelectedLocation);
	}

	if ($InputError != 1) {

		// First check the user is not being duplicated

		$CheckSql = "SELECT count(*)
			     FROM pvroles
			     WHERE level= '" . $_POST['SelectedLocation'] . "'
				 AND authoriser = '" . $_POST['SelectedUser'] . "'";

		$CheckResult = DB_query($CheckSql);
		$CheckRow = DB_fetch_row($CheckResult);

		if ($CheckRow[0] > 0) {
			$InputError = 1;
			prnMsg(_('The user') . ' ' . $_POST['SelectedUser'] . ' ' . _('is already authorised to use this role'), 'error');
		} else {
			// Add new record on submit
			$SQL = "INSERT INTO pvroles (level,
										authoriser)
								VALUES ('" . $_POST['SelectedLocation'] . "',
										'" . $_POST['SelectedUser'] . "')";

			$msg = _('User') . ': ' . $_POST['SelectedUser'] . ' ' . _('authority to use the') . ' ' . $_POST['SelectedLocation'] . ' ' . _('role has been changed');
			$Result = DB_query($SQL);
			prnMsg($msg, 'success');
			unset($_POST['SelectedUser']);
		}
	}
} elseif (isset($_GET['delete'])) {
	$SQL = "DELETE FROM pvroles
		WHERE level='" . $SelectedLocation . "'
		AND authoriser='" . $SelectedUser . "'";

	$ErrMsg = _('The role user record could not be deleted because');
	$Result = DB_query($SQL, $ErrMsg);
	prnMsg(_('User') . ' ' . $SelectedUser . ' ' . _('has had their authority to use the') . ' ' . $SelectedLocation . ' ' . _('role removed'), 'success');
	unset($_GET['delete']);
} 

if (!isset($SelectedLocation)) {

	/* It could still be the second time the page has been run and a record has been selected for modification - SelectedUser will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
	then none of the above are true. These will call the same page again and allow update/input or deletion of the records*/
	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Application=PVM&Ref=Assignroles">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
			<table class="selection table table-hover">
			<tr>
				<td>' . _('Select Pv role') . ':</td>
				<td><select name="SelectedLocation">';

	$Result = DB_query("SELECT levelcode,
								pvrole
						FROM pvlevel");

	echo '<option value="">' . _('Not Yet Selected') . '</option>';
	while ($MyRow = DB_fetch_array($Result)) {
		if (isset($SelectedLocation) and $MyRow['levelcode'] == $SelectedLocation) {
			echo '<option selected="selected" value="';
		} else {
			echo '<option value="';
		}
		echo $MyRow['levelcode'] . '">' . $MyRow['levelcode'] . ' - ' . $MyRow['pvrole'] . '</option>';

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
	$SQLName = "SELECT pvrole
			FROM pvlevel
			WHERE levelcode='" . $SelectedLocation . "'";
	$Result = DB_query($SQLName);
	$MyRow = DB_fetch_array($Result);
	$SelectedLocationName = $MyRow['pvrole'];

	echo '<div class="centre"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Application=PVM&Ref=Assignroles">' . _('Authorised users for') . ' ' . $SelectedLocationName . ' ' . _('role') . '</a></div>
		<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Application=PVM&Ref=Assignroles">
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
		<input type="hidden" name="SelectedLocation" value="' . $SelectedLocation . '" />';

	$SQL = "SELECT pvroles.authoriser,
					www_users.realname
			FROM pvroles INNER JOIN www_users
			ON pvroles.authoriser=www_users.userid
			WHERE pvroles.level='" . $SelectedLocation . "'
			ORDER BY pvroles.authoriser ASC";

	$Result = DB_query($SQL);

	echo '<table class="selection table table-hover">';
	echo '<tr>
			<th colspan="6"><h4>' . _('Authorised users for role') . ': ' . $SelectedLocationName . '</h4></th>
		</tr>';
	echo '<tr>
			<th>' . _('User Code') . '</th>
			<th>' . _('User Name') . '</th>
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
				<td><a href="%s?Application=PVM&Ref=Assignroles&SelectedUser=%s&amp;delete=yes&amp;SelectedLocation=' . $SelectedLocation . '" onclick="return confirm(\'' . _('Are you sure you wish to un-authorise this user?') . '\');">' . _('Un-authorise') . '</a></td>
				</tr>',
				$MyRow['authoriser'],
				$MyRow['realname'],
				htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'),
				$MyRow['authoriser']);
	}
	//END WHILE LIST LOOP
	echo '</table>';

	if (!isset($_GET['delete'])) {


		echo '<table class="selection table table-hover">'; //Main table

		echo '<tr>
				<td>' . _('Select User') . ':</td>
				<td><select name="SelectedUser">';

		$Result = DB_query("SELECT userid,
									realname
							FROM www_users
							WHERE NOT EXISTS (SELECT locationusers.userid
											FROM locationusers
											WHERE locationusers.loccode='" . $SelectedLocation . "'
												AND locationusers.userid=www_users.userid)");

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
				<input type="submit" name="Cancel" value="' . _('Cancel') . '" />
			</div>
			</form>';

	} // end if user wish to delete
}

?>
