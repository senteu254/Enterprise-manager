<?php
/* $Id: StockCategories.php 7054 2015-01-01 11:36:36Z exsonqu $*/

include('includes/session.inc');

$Title = _('Farm fields');
$ViewTopic= 'Farm fields';
$BookMark = 'Farm fields';
include('includes/header.inc');

// BEGIN: Stock Type Name array.
//$StockTypeName = array();
///$StockTypeName['D'] = _('Dummy Item - (No Movements)');
//$StockTypeName['F'] = _('Finished Goods');
//$StockTypeName['L'] = _('Labour');
//$StockTypeName['M'] = _('Raw Materials');
//($StockTypeName);
// END: Stock Type Name array.

// BEGIN: Tax Category Name array.
//$TaxCategoryName = array();
//$Query = "SELECT taxcatid, taxcatname FROM taxcategories ORDER BY taxcatname";
//$Result = DB_query($Query);
//while ($Row = DB_fetch_array($Result)) {
//	$TaxCategoryName[$Row['taxcatid']] = $Row['taxcatname'];
//}
// END: Tax Category Name array.

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/supplier.png" title="' . _('Inventory Adjustment') . '" alt="" />' . ' ' . $Title . '</p>';

if (isset($_GET['SelectedField'])){
	$SelectedField = mb_strtoupper($_GET['SelectedField']);
} else if (isset($_POST['SelectedField'])){
	$SelectedField = mb_strtoupper($_POST['SelectedField']);
}
/*
if (isset($_GET['DeleteProperty'])){

	$ErrMsg = _('Could not delete the property') . ' ' . $_GET['DeleteProperty'] . ' ' . _('because');
	$sql = "DELETE FROM stockitemproperties WHERE stkcatpropid='" . $_GET['DeleteProperty'] . "'";
	$result = DB_query($sql,$ErrMsg);
	$sql = "DELETE FROM stockcatproperties WHERE stkcatpropid='" . $_GET['DeleteProperty'] . "'";
	$result = DB_query($sql,$ErrMsg);
	prnMsg(_('Deleted the property') . ' ' . $_GET['DeleteProperty'],'success');
}
*/
if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible

	$_POST['code'] = mb_strtoupper($_POST['code']);

	if (mb_strlen($_POST['code']) > 6) {
		$InputError = 1;
		prnMsg(_('The Inventory Category code must be six characters or less long'),'error');
	} elseif (mb_strlen($_POST['code'])==0) {
		$InputError = 1;
		prnMsg(_('The Inventory category code must be at least 1 character but less than six characters long'),'error');
	} elseif (mb_strlen($_POST['Field_Name']) >20 or mb_strlen($_POST['Field_Name'])==0) {
		$InputError = 1;
		prnMsg(_('The Sales category description must be twenty characters or less long and cannot be zero'),'error');
	} 
	for ($i=0;$i<=$_POST['PropertyCounter'];$i++){
		if (isset($_POST['PropNumeric' .$i]) and $_POST['PropNumeric' .$i] == true){
			if (!is_numeric(filter_number_format($_POST['PropMinimum' .$i]))){
				$InputError = 1;
				prnMsg(_('The minimum value is expected to be a numeric value'),'error');
			}
			if (!is_numeric(filter_number_format($_POST['PropMaximum' .$i]))){
				$InputError = 1;
				prnMsg(_('The maximum value is expected to be a numeric value'),'error');
			}
		}
	} //check the properties are sensible

	if (isset($SelectedField) AND $InputError !=1) {

		/*SelectedField could also exist if submit had not been clicked this code
		would not run in this case cos submit is false of course  see the
		delete code below*/

		$sql = "UPDATE farmfield SET Field_Name = '" . $_POST['Field_Name'] . "',
									 acres = '" . $_POST['acres'] . "'
									 WHERE
									 code = '" . $SelectedField. "'";
		$ErrMsg = _('Could not update the Farm Field') . $_POST['Field_Name'] . _('because');
		$result = DB_query($sql,$ErrMsg);

		if ($_POST['PropertyCounter']==0 and $_POST['PropLabel0']!='') {
			$_POST['PropertyCounter']=0;
		}

		for ($i=0;$i<=$_POST['PropertyCounter'];$i++){} //end of loop round properties

		prnMsg(_('Updated the Field Name record for') . ' ' . $_POST['Field_Name'],'success');

	} elseif ($InputError !=1) {

	/*Selected category is null cos no item selected on first time round so must be adding a	record must be submitting new entries in the new stock category form */

		$sql = "INSERT INTO farmfield (code,
											Field_Name,
											acres)
										VALUES ('" .
											$_POST['code'] . "','" .
											$_POST['Field_Name'] . "','" .
											$_POST['acres'] . "')";
		$ErrMsg = _('Could not insert the new stock category') . $_POST['Field_Name'] . _('because');
		$result = DB_query($sql,$ErrMsg);
		prnMsg(_('A new Field record has been added for') . ' ' . $_POST['Field_Name'],'success');

	}
	//run the SQL from either of the above possibilites

	
	unset($_POST['Field_Name']);
	unset($_POST['acres']);


} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

// PREVENT DELETES IF DEPENDENT RECORDS IN 'StockMaster'

	$sql= "SELECT code FROM farmfield WHERE farmfield.code='" . $SelectedField . "'";
	$result = DB_query($sql);

	if (DB_num_rows($result)>1) {
		prnMsg(_('Cannot delete this stock category because stock items have been created using this stock category') .
			'<br /> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('items referring to this stock category code'),'warn');

	} else {
				$sql="DELETE FROM farmfield WHERE code='" . $SelectedField . "'";
				$result = DB_query($sql);
				prnMsg(_('The Farm Field') . ' ' . $SelectedField . ' ' . _('has been deleted') . ' !','success');
				unset ($SelectedField);
			}
		}
	//end if stock category used in debtor transactions


if (!isset($SelectedField)) {

/* It could still be the second time the page has been run and a record has been selected for modification - SelectedField will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
then none of the above are true and the list of stock categorys will be displayed with
links to delete or edit each. These will call the same page again and allow update/input
or deletion of the records*/

	$sql = "SELECT code,
					Field_Name,
					acres					
				    FROM farmfield
				    ORDER BY code";
	$result = DB_query($sql);

	echo '<br />
		<table class="selection">
			<tr>
				<th class="ascending">' . _('Field Code') . '</th>
				<th class="ascending">' . _('Field Name') . '</th>' . '
				<th class="ascending">' . _('Number of Acres') . '</th>
				<th class="ascending">' . _('Action') . '</th>
			</tr>';

	$k=0; //row colour counter

	while ($myrow = DB_fetch_array($result)) {
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}
		printf('<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td><a href="%sSelectedField=%s">' . _('Edit') . '</a></td>
				<td><a href="%sSelectedField=%s&amp;delete=yes" onclick="return confirm(\'' . _('Are you sure you wish to delete this Field Name? Additional checks will be performed before actual deletion to ensure data integrity is not compromised.') . '\');">' . _('Delete') . '</a></td>
			</tr>',
				$myrow['code'],
				$myrow['Field_Name'],
				$myrow['acres'],
				htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?',
				$myrow['code'],
				htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?',
				$myrow['code']);
	}
	//END WHILE LIST LOOP
	echo '</table>';
}

//end of ifs and buts!

echo '<br />';

if (isset($SelectedField)) {
	echo '<a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '" >' . _('Show All Farm Fields') . '</a>';
}

echo '<form id="CategoryForm" method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">';
echo '<div>';
echo '<br />';

echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

if (isset($SelectedField)) {
	//editing an existing stock category
	if (!isset($_POST['UpdateTypes'])) {
		$sql = "SELECT code,
						Field_Name,
						acres
					FROM farmfield
					WHERE code='" . $SelectedField . "'";

		$result = DB_query($sql);
		$myrow = DB_fetch_array($result);

		$_POST['code'] = $myrow['code'];
		$_POST['Field_Name']  = $myrow['Field_Name'];
		$_POST['acres']  = $myrow['acres'];
	}
	echo '<input type="hidden" name="SelectedField" value="' . $SelectedField . '" />';
	echo '<input type="hidden" name="code" value="' . $_POST['code'] . '" />';
	echo '<table class="selection">
			<tr>
				<td>' . _('Field Code') . ':</td>
				<td>' . $_POST['code'] . '</td>
			</tr>';

} else { //end of if $SelectedField only do the else when a new record is being entered
	if (!isset($_POST['code'])) {
		$_POST['code'] = '';
	}
	echo '<table class="selection">
			<tr>
				<td>' . _('Field Code') . ':</td>
				<td><input type="text" name="code" required="required" autofocus="autofocus" data-type="no-illegal-chars" title="' . _('Enter up to six alphanumeric characters or underscore as a code for this Field') . '" size="7" maxlength="6" value="' . $_POST['code'] . '" /></td>
			</tr>';
} 

//SQL to poulate account selection boxes
$sql = "SELECT accountcode,
				accountname
			FROM chartmaster
			LEFT JOIN accountgroups
				ON chartmaster.group_=accountgroups.groupname
			WHERE accountgroups.pandl=0
			ORDER BY accountcode";

$BSAccountsResult = DB_query($sql);

$sql = "SELECT accountcode,
				accountname
			FROM chartmaster
			LEFT JOIN accountgroups
				ON chartmaster.group_=accountgroups.groupname
			WHERE accountgroups.pandl=1
			ORDER BY accountcode";

$PnLAccountsResult = DB_query($sql);

// Category Description input.
if (!isset($_POST['CategoryDescription'])) {
	$_POST['CategoryDescription'] = '';
}
echo '<tr><td><label for="Field_Name">' . _('Field Name') .
	':</label></td><td><input id="Field_Name" maxlength="20" name="Field_Name" required="required" size="22" title="' .
	_('A Field Name of the farm is required') .
	'" type="text" value="' . $_POST['Field_Name'] .
	'" /></td></tr>';
if (!isset($_POST['acres'])) {
	$_POST['acres'] = '';
}
echo '<tr><td><label for="acres">' . _('Number of Acres') .
	':</label></td><td><input id="acres" maxlength="20" name="acres" required="required" size="22" title="' .
	_('A Number of Acres for the field is required') .
	'" type="text" value="' . $_POST['acres'] .
	'" /></td></tr>';

// Stock Type input.///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


// Default Tax Category input.


// Recovery or Stock GL Code input.



// WIP GL Code input.






		echo'</table>';

if (!isset($SelectedField)) {
	$SelectedField='';
}
if (isset($SelectedField)) {
	//editing an existing stock category

	$sql = "SELECT stkcatpropid,
					label,
					controltype,
					defaultvalue,
					numericvalue,
					reqatsalesorder,
					minimumvalue,
					maximumvalue
			   FROM stockcatproperties
			   WHERE categoryid='" . $SelectedField . "'
			   ORDER BY stkcatpropid";

	$result = DB_query($sql);

/*		echo '<br />Number of rows returned by the sql = ' . DB_num_rows($result) .
			'<br />The SQL was:<br />' . $sql;
*/
 //end loop around defined properties for this category
	

} /* end if there is a category selected */

echo '<br />
		<div class="centre">
			<input type="submit" name="submit" value="' . _('Enter Information') . '" />
		</div>
    </div>
	</form>';

include('includes/footer.inc');
?>
