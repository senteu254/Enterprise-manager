<?php
/* $Id: WorkCentres.php 6941 2014-10-26 23:18:08Z daintree $*/


include('includes/session.inc');
$Title = _('Gates Maintenance');
include('includes/header.inc');

if (isset($_POST['SelectedG'])){
	$SelectedG =$_POST['SelectedG'];
} elseif (isset($_GET['SelectedG'])){
	$SelectedG =$_GET['SelectedG'];
}

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible

	if (mb_strlen($_POST['GateID']) < 2) {
		$InputError = 1;
		prnMsg(_('The Gate Number must be at least 2 characters long'),'error');
	}
	if (mb_strlen($_POST['Description'])<3) {
		$InputError = 1;
		prnMsg(_('The Gate description must be at least 3 characters long'),'error');
	}
	if (mb_strstr($_POST['GateID'],' ') OR ContainsIllegalCharacters($_POST['GateID']) ) {
		$InputError = 1;
		prnMsg(_('The Gate Number cannot contain any of the following characters') . " - ' &amp; + \" \\ " . _('or a space'),'error');
	}

	if (isset($SelectedG) AND $InputError !=1) {

		/*SelectedWC could also exist if submit had not been clicked this code
		would not run in this case cos submit is false of course  see the
		delete code below*/

		$sql = "UPDATE gates SET description = '" . $_POST['Description'] . "'
				WHERE GateID = '" . $SelectedG . "'";
		$msg = _('The gate record has been updated');
	} elseif ($InputError !=1) {

	/*Selected work centre is null cos no item selected on first time round so must be adding a	record must be submitting new entries in the new work centre form */

		$sql = "INSERT INTO gates (GateID,description)
					VALUES ('" . $_POST['GateID'] . "',
						'" . $_POST['Description'] . "'
						)";
		$msg = _('The new gate has been added to the database');
	}
	//run the SQL from either of the above possibilites

	if ($InputError !=1){
		$result = DB_query($sql,_('The update/addition of the gate failed because'));
		prnMsg($msg,'success');
		unset ($_POST['Description']);
		unset ($_POST['GateID']);
		unset ($SelectedG);
	}

} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

// PREVENT DELETES IF DEPENDENT RECORDS IN 'BOM'

	$sql= "SELECT COUNT(*) FROM visitor_timein WHERE visitor_timein.GateID='" . $SelectedG . "'";
	$result = DB_query($sql);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		prnMsg(_('Cannot delete this gate because visitors have been checked in through this gate') . '<br />' . _('There are') . ' ' . $myrow[0] . ' ' ._('Visitors referring to this gate'),'warn');
	}  else {
		$sql= "SELECT COUNT(*) FROM visitor_timeout WHERE visitor_timeout.GateID='" . $SelectedG . "'";
	$result = DB_query($sql);
	$myrow = DB_fetch_row($result);
		if ($myrow[0]>0) {
			prnMsg(_('Cannot delete this gate because visitors have been checked out through this gate') . '<br />' . _('There are') . ' ' . $myrow[0] . ' ' ._('Visitors referring to this gate'),'warn');
		} else {
			$sql="DELETE FROM gates WHERE gateID='" . $SelectedG . "'";
			$result = DB_query($sql);
			prnMsg(_('The selected gate record has been deleted'),'succes');
		} // end of Contract BOM test
	} // end of BOM test
}

if (!isset($SelectedG)) {

	echo '<p class="page_title_text">
			<img src="'.$RootPath.'/css/'.$Theme.'/images/maintenance.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '
		</p>';

	$sql = "SELECT gates.GateID,
				gates.description
			FROM gates";

	$result = DB_query($sql);
	echo '<table class="selection">
			<tr>
				<th class="ascending">' . _('Gate No') . '</th>
				<th class="ascending">' . _('Description') . '</th>
			</tr>';

	while ($myrow = DB_fetch_array($result)) {

		printf('<tr>
					<td>%s</td>
					<td>%s</td>
					<td><a href="%s&amp;SelectedG=%s">' . _('Edit') . '</a></td>
					<td><a href="%s&amp;SelectedG=%s&amp;delete=yes" onclick="return confirm(\'' . _('Are you sure you wish to delete this Gate?') . '\');">' . _('Delete')  . '</a></td>
				</tr>',
				$myrow['GateID'],
				$myrow['description'],
				htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?',
				$myrow['GateID'],
				htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?',
				$myrow['GateID']);
	}

	//END WHILE LIST LOOP
	echo '</table>';
}

//end of ifs and buts!

if (isset($SelectedG)) {
	echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/maintenance.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p>';
	echo '<div class="centre"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">' . _('Show all Gates') . '</a></div>';
}

echo '<br />
	<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

if (isset($SelectedG)) {
	//editing an existing work centre

	$sql = "SELECT GateID,
					description
			FROM gates
			WHERE GateID='" . $SelectedG . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['GateID'] = $myrow['GateID'];
	$_POST['Description'] = $myrow['description'];
	echo '<input type="hidden" name="SelectedG" value="' . $SelectedG . '" />
		<input type="hidden" name="GateID" value="' . $_POST['GateID'] . '" />
		<table class="selection">
			<tr>
				<td>' ._('Gate Number') . ':</td>
				<td>' . $_POST['GateID'] . '</td>
			</tr>';

} else { //end of if $SelectedWC only do the else when a new record is being entered
	if (!isset($_POST['GateID'])) {
		$_POST['GateID'] = '';
	}
	echo '<table class="selection">
			<tr>
				<td>' . _('Gate Number') . ':</td>
				<td><input type="text" name="GateID" pattern="[^&+-]{2,}" required="required" autofocus="autofocus" title="'._('The code should be at least 2 characters and no illegal characters allowed').'"  size="6" maxlength="5" value="' . $_POST['GateID'] . '" placeholder="'._('More than 2 legal characters').'" /></td>
			</tr>';
}

if (!isset($_POST['Description'])) {
	$_POST['Description'] = '';
}
echo '<tr>
		<td>' . _('Gate Description') . ':</td>
		<td><input type="text" pattern="[^&+-]{3,}" required="required" title="'._('The Work Center should be more than 3 characters and no illegal characters allowed').'" name="Description" ' . (isset($SelectedG)? 'autofocus="autofocus"': '') . ' size="21" maxlength="20" value="' . $_POST['Description'] . '" placeholder="'._('More than 3 legal characters').'" /></td>
	</tr>
	</table>
	<br />
	<div class="centre">
		<input type="submit" name="submit" value="' . _('Enter Information') . '" />
	</div>
	</div>
      </form>';
include('includes/footer.inc');
?>
