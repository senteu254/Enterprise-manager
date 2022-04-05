<?php

/* $Id SupplierTypes.php 4183 2010-12-14 09:30:20Z daintree $ */

include('includes/session.inc');
$Title = _('Supplier Group Types') . ' / ' . _('Maintenance');
include('includes/header.inc');

if (isset($_POST['SelectedType'])){
	$SelectedType = mb_strtoupper($_POST['SelectedType']);
} elseif (isset($_GET['SelectedType'])){
	$SelectedType = mb_strtoupper($_GET['SelectedType']);
}

if (isset($Errors)) {
	unset($Errors);
}

$Errors = array();

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/maintenance.png" title="' . _('Supplier group Types')
	. '" alt="" />' . _('Supplier  group Type Setup') . '</p>
	<div class="page_help_text">' . _('Add/edit/delete Supplier group Types') . '</div>
	<br />';

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible
	$i=1;
	if (mb_strlen($_POST['groupname']) >100) {
		$InputError = 1;
		echo prnMsg(_('The supplier type name description must be 100 characters or less long'),'error');
		$Errors[$i] = 'SupplierType';
		$i++;
	}

	if (mb_strlen(trim($_POST['groupname']))==0) {
		$InputError = 1;
		echo prnMsg(_('The supplier Group  type name description must contain at least one character'),'error');
		$Errors[$i] = 'SupplierType';
		$i++;
	}

	$CheckSQL = "SELECT count(*)
		     FROM suppliergrouptype
		     WHERE groupname = '" . $_POST['groupname'] . "'";
	$CheckResult=DB_query($CheckSQL);
	$CheckRow=DB_fetch_row($CheckResult);
	if ($CheckRow[0]>0) {
		$InputError = 1;
		echo prnMsg(_('You already have a supplier type called').' '.$_POST['groupname'],'error');
		$Errors[$i] = 'SupplierName';
		$i++;
	}

	if (isset($SelectedType) AND $InputError !=1) {

		$sql = "UPDATE suppliergrouptype
			SET groupname = '" . $_POST['groupname'] . "'
			WHERE groupid = '" . $SelectedType . "'";

		prnMsg(_('The supplier type') . ' ' . $SelectedType . ' ' .  _('has been updated'),'success');
	} elseif ($InputError !=1){
		// Add new record on submit

		$sql = "INSERT INTO suppliergrouptype
					(groupname)
				VALUES ('" . $_POST['groupname'] . "')";


		$msg = _('Supplier type') . ' ' . $_POST['groupname'] .  ' ' . _('has been created');
		$CheckSQL = "SELECT count(groupid) FROM suppliergrouptype";
		$result = DB_query($CheckSQL);
		$row = DB_fetch_row($result);
	}

	if ( $InputError !=1) {
	//run the SQL from either of the above possibilites
		$result = DB_query($sql);


	// Fetch the default supplier type
		$sql = "SELECT confvalue
					FROM config
					WHERE confname='DefaultSupplierType'";
		$result = DB_query($sql);
		$SupplierTypeRow = DB_fetch_row($result);
		$DefaultSupplierType = $SupplierTypeRow[0];

	// Does it exist
		$CheckSQL = "SELECT count(*)
			     FROM suppliergrouptype
			     WHERE groupid = '" . $DefaultSupplierType . "'";
		$CheckResult = DB_query($CheckSQL);
		$CheckRow = DB_fetch_row($CheckResult);

	// If it doesnt then update config with newly created one.
		if ($CheckRow[0] == 0) {
			$sql = "UPDATE config
					SET confvalue='" . $_POST['TypeID'] . "'
					WHERE confname='DefaultSupplierType'";
			$result = DB_query($sql);
			$_SESSION['DefaultSupplierType'] = $_POST['TypeID'];
		}

		unset($SelectedType);
		unset($_POST['TypeID']);
		unset($_POST['groupname']);
	}

} elseif ( isset($_GET['delete']) ) {

	$sql = "SELECT COUNT(*) FROM suppliers WHERE suppliergroup='" . $SelectedType . "'";

	$ErrMsg = _('The number of suppliers using this Type record could not be retrieved because');
	$result = DB_query($sql,$ErrMsg);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		prnMsg (_('Cannot delete this type because suppliers are currently set up to use this type') . '<br />' .
			_('There are') . ' ' . $myrow[0] . ' ' . _('suppliers with this type code'));
	} else {

		$sql="DELETE FROM suppliergrouptype WHERE groupid='" . $SelectedType . "'";
		$ErrMsg = _('The Type record could not be deleted because');
		$result = DB_query($sql,$ErrMsg);
		prnMsg(_('Supplier type') . $SelectedType  . ' ' . _('has been deleted') ,'success');

		unset ($SelectedType);
		unset($_GET['delete']);

	}
}

if (!isset($SelectedType)){

/* It could still be the second time the page has been run and a record has been selected for modification - SelectedType will
 *  exist because it was sent with the new call. If its the first time the page has been displayed with no parameters then
 * none of the above are true and the list of sales types will be displayed with links to delete or edit each. These will call
 * the same page again and allow update/input or deletion of the records
 */

	$sql = "SELECT groupid, groupname FROM suppliergrouptype";
	$result = DB_query($sql);

	echo '<table class="selection">';
	echo '<tr>
		<th class="ascending" >' . _('Group ID') . '</th>
		<th class="ascending" >' . _('Group Name') . '</th>
		</tr>';

$k=0; //row colour counter

while ($myrow = DB_fetch_row($result)) {
	if ($k==1){
		echo '<tr class="EvenTableRows">';
		$k=0;
	} else {
		echo '<tr class="OddTableRows">';
		$k=1;
	}

	printf('<td>%s</td>
			<td>%s</td>
			<td><a href="%sSelectedType=%s">' . _('Edit') . '</a></td>
			<td><a href="%sSelectedType=%s&amp;delete=yes" onclick="return confirm(\'' .
				_('Are you sure you wish to delete this Supplier Type?') . '\');">' . _('Delete') . '</a></td>
		</tr>',
		$myrow[0],
		$myrow[1],
		htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?',
		$myrow[0],
		htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?',
		$myrow[0]);
	}
	//END WHILE LIST LOOP
	echo '</table>';
}

//end of ifs and buts!
if (isset($SelectedType)) {

	echo '<div class="centre">
			<p><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">' . _('Show All Types Defined') . '</a></p>
		</div>';
}
if (! isset($_GET['delete'])) {

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<br />
		<table class="selection">'; //Main table

	// The user wish to EDIT an existing type
	if ( isset($SelectedType) AND $SelectedType!='' ) {

		$sql = "SELECT groupid,
			       groupname
		        FROM suppliergrouptype
		        WHERE groupid='" . $SelectedType . "'";

		$result = DB_query($sql);
		$myrow = DB_fetch_array($result);

		$_POST['TypeID'] = $myrow['groupid'];
		$_POST['groupname']  = $myrow['groupname'];

		echo '<input type="hidden" name="SelectedType" value="' . $SelectedType . '" />';
		echo '<input type="hidden" name="TypeID" value="' . $_POST['TypeID'] . '" />';

		// We dont allow the user to change an existing type code

		echo '<tr>
				<td>' ._('Type ID') . ': </td>
				<td>' . $_POST['TypeID'] . '</td>
			</tr>';
	}

	if (!isset($_POST['groupname'])) {
		$_POST['groupname']='';
	}
	echo '<tr>
			<td>' . _('Type Name') . ':</td>
			<td><input type="text"  required="true" pattern="(?!^\s+$)[^<>+-]{1,100}" title="'._('The input should not be over 100 characters and contains illegal characters').'" name="groupname" placeholder="'._('less than 100 characters').'" value="' . $_POST['groupname'] . '" /></td>
		</tr>';

	echo '<tr>
			<td colspan="2">
				<div class="centre">
					<input type="submit" name="submit" value="' . _('Accept') . '" />
				</div>
			</td>
		</tr>
		</table>
		</div>
		</form>';

} // end if user wish to delete

include('includes/footer.inc');
?>
