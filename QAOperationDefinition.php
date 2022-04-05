<?php
/* $Id: UnitsOfMeasure.php 6945 2014-10-27 07:20:48Z daintree $*/

include('includes/session.inc');

$Title = _('Hardness Annealing OP Definition');

include('includes/header.inc');
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/magnifier.png" title="' .
		_('Search') . '" alt="" />' . ' ' . $Title . '</p>';

if ( isset($_GET['SelectedMeasureID']) )
	$SelectedMeasureID = $_GET['SelectedMeasureID'];
elseif (isset($_POST['SelectedMeasureID']))
	$SelectedMeasureID = $_POST['SelectedMeasureID'];

if (isset($_POST['Submit'])) {

	//initialise no input errors assumed initially before we test

	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible

	if (trim($_POST['MeasureName']) == '') {
		$InputError = 1;
		prnMsg( _('The Operation may not be empty'), 'error');
	}

	if (isset($_POST['SelectedMeasureID']) AND $_POST['SelectedMeasureID']!='' AND $InputError !=1) {


		/*SelectedMeasureID could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/
		// Check the name does not clash
		$sql = "SELECT count(*) FROM qaoperation
				WHERE id <> '" . $SelectedMeasureID ."'
				AND operation ".LIKE." '" . $_POST['MeasureName'] . "'";
		$result = DB_query($sql);
		$myrow = DB_fetch_row($result);
		if ( $myrow[0] > 0 ) {
			$InputError = 1;
			prnMsg( _('The Operation can not be renamed because another with the same name already exist.'),'error');
		} else {
			// Get the old name and check that the record still exist neet to be very carefull here
			// idealy this is one of those sets that should be in a stored procedure simce even the checks are
			// relavant
			$sql = "SELECT operation FROM qaoperation
				WHERE id = '" . $SelectedMeasureID . "'";
			$result = DB_query($sql);
			if ( DB_num_rows($result) != 0 ) {
				// This is probably the safest way there is
				$myrow = DB_fetch_row($result);
				$OldMeasureName = $myrow[0];
				$sql = array();
				$sql[] = "UPDATE qaoperation
					SET operation='" . $_POST['MeasureName'] . "'
					WHERE operation ".LIKE." '".$OldMeasureName."'";
				
			} else {
				$InputError = 1;
				prnMsg( _('The operation no longer exist.'),'error');
			}
		}
		$msg = _('Operation changed successfully');
	} elseif ($InputError !=1) {
		/*SelectedMeasureID is null cos no item selected on first time round so must be adding a record*/
		$sql = "SELECT count(*) FROM qaoperation
				WHERE operation " .LIKE. " '".$_POST['MeasureName'] ."'";
		$result = DB_query($sql);
		$myrow = DB_fetch_row($result);
		if ( $myrow[0] > 0 ) {
			$InputError = 1;
			prnMsg( _('The Operation can not be created because it already exists.'),'error');
		} else {
			$sql = "INSERT INTO qaoperation (operation)
					VALUES ('" . $_POST['MeasureName'] ."')";
		}
		$msg = _('New Operation Added successfully');
	}

	if ($InputError!=1){
		//run the SQL from either of the above possibilites
		if (is_array($sql)) {
			$result = DB_Txn_Begin();
			$tmpErr = _('Could not update Operation');
			$tmpDbg = _('The sql that failed was') . ':';
			foreach ($sql as $stmt ) {
				$result = DB_query($stmt, $tmpErr,$tmpDbg,true);
				if(!$result) {
					$InputError = 1;
					break;
				}
			}
			if ($InputError!=1){
				$result = DB_Txn_Commit();
			} else {
				$result = DB_Txn_Rollback();
			}
		} else {
			$result = DB_query($sql);
		}
		prnMsg($msg,'success');
	}
	unset ($SelectedMeasureID);
	unset ($_POST['SelectedMeasureID']);
	unset ($_POST['MeasureName']);

} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button
// PREVENT DELETES IF DEPENDENT RECORDS IN 'stockmaster'
	// Get the original name of the unit of measure the ID is just a secure way to find the unit of measure
	$sql = "SELECT operation FROM qaoperation
		WHERE id = '" . $SelectedMeasureID . "'";
	$result = DB_query($sql);
	if ( DB_num_rows($result) == 0 ) {
		// This is probably the safest way there is
		prnMsg( _('Cannot delete this Operation because it no longer exist'),'warn');
	} else {
		$myrow = DB_fetch_row($result);
		$OldMeasureName = $myrow[0];
		$sql= "SELECT COUNT(*) FROM qaoperationtype WHERE operationid ".LIKE." '" . $OldMeasureName . "'";
		$result = DB_query($sql);
		$myrow = DB_fetch_row($result);
		if ($myrow[0]>0) {
			prnMsg( _('Cannot delete this Operation because it has been used in production'),'warn');
		} else {
			$sql="DELETE FROM qaoperation WHERE operation ".LIKE."'" . $OldMeasureName . "'";
			$result = DB_query($sql);
			prnMsg( $OldMeasureName . ' ' . _('Operation has been deleted successfully') . '!','success');
		}
	} //end if account group used in GL accounts
	unset ($SelectedMeasureID);
	unset ($_GET['SelectedMeasureID']);
	unset($_GET['delete']);
	unset ($_POST['SelectedMeasureID']);
	unset ($_POST['MeasureID']);
	unset ($_POST['MeasureName']);
}

 if (!isset($SelectedMeasureID)) {

/* An unit of measure could be posted when one has been edited and is being updated
  or GOT when selected for modification
  SelectedMeasureID will exist because it was sent with the page in a GET .
  If its the first time the page has been displayed with no parameters
  then none of the above are true and the list of account groups will be displayed with
  links to delete or edit each. These will call the same page again and allow update/input
  or deletion of the records*/

	$sql = "SELECT id,
			operation
			FROM qaoperation
			ORDER BY id";

	$ErrMsg = _('Could not get Operation because');
	$result = DB_query($sql,$ErrMsg);

	echo '<table class="selection">
			<tr>
				<th class="ascending">' . _('Operation') . '</th>
			</tr>';

	$k=0; //row colour counter
	while ($myrow = DB_fetch_row($result)) {

		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k++;
		}

		echo '<td>' . $myrow[1] . '</td>';
		echo '<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?SelectedMeasureID=' . $myrow[0] . '">' . _('Edit') . '</a></td>';
		echo '<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?SelectedMeasureID=' . $myrow[0] . '&amp;delete=1" onclick="return confirm(\'' . _('Are you sure you wish to delete this Operation?') . '\');">' . _('Delete')  . '</a></td>';
		echo '</tr>';

	} //END WHILE LIST LOOP
	echo '</table><br />';
} //end of ifs and buts!


if (isset($SelectedMeasureID)) {
	echo '<div class="centre">
			<a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">' . _('Review Operation') . '</a>
		</div>';
}

echo '<br />';

if (! isset($_GET['delete'])) {

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') .  '">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	if (isset($SelectedMeasureID)) {
		//editing an existing section

		$sql = "SELECT id,
				operation
				FROM qaoperation
				WHERE id='" . $SelectedMeasureID . "'";

		$result = DB_query($sql);
		if ( DB_num_rows($result) == 0 ) {
			prnMsg( _('Could not retrieve the requested Operation, please try again.'),'warn');
			unset($SelectedMeasureID);
		} else {
			$myrow = DB_fetch_array($result);

			$_POST['MeasureID'] = $myrow['id'];
			$_POST['MeasureName']  = $myrow['operation'];

			echo '<input type="hidden" name="SelectedMeasureID" value="' . $_POST['MeasureID'] . '" />';
			echo '<table class="selection">';
		}

	}  else {
		$_POST['MeasureName']='';
		echo '<table>';
	}
	echo '<tr>
		<td>' . _('Operation') . ':' . '</td>
		<td><input required="required" type="text" name="MeasureName" title="'._('Cannot be blank or contains illegal characters').'" placeholder="'._('More than one character').'" size="60" maxlength="100" value="' . $_POST['MeasureName'] . '" /></td>
		</tr>';
	echo '</table>';

	echo '<div class="centre">
			<input type="submit" name="Submit" value="' . _('Enter Information') . '" />
		</div>';

	echo '</div>
          </form>';

} //end if record deleted no point displaying form to add record

include('includes/footer.inc');
?>
