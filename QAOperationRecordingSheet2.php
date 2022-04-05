<?php
/* $Id: UnitsOfMeasure.php 6945 2014-10-27 07:20:48Z daintree $*/

include('includes/session.inc');

$Title = _('Hardness Annealing Operation Recording Sheet');

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
		prnMsg( _('The Sheet may not be empty'), 'error');
	}

	if (isset($_POST['SelectedMeasureID']) AND $_POST['SelectedMeasureID']!='' AND $InputError !=1) {


		/*SelectedMeasureID could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/
		// Check the name does not clash
		$sql = "SELECT count(*) FROM qarecordingsheet
				WHERE id <> '" . $SelectedMeasureID ."'
				AND sheetname ".LIKE." '" . $_POST['MeasureName'] . "'
				AND operationid=".$_POST['opid']." 
				AND typeid=".$_POST['typeid']."";
		$result = DB_query($sql);
		$myrow = DB_fetch_row($result);
		if ( $myrow[0] > 0 ) {
			$InputError = 1;
			prnMsg( _('The Sheet can not be renamed because another with the same name already exist.'),'error');
		} else {
			// Get the old name and check that the record still exist neet to be very carefull here
			// idealy this is one of those sets that should be in a stored procedure simce even the checks are
			// relavant
			$sql = "SELECT sheetname FROM qarecordingsheet
				WHERE id = '" . $SelectedMeasureID . "'";
			$result = DB_query($sql);
			if ( DB_num_rows($result) != 0 ) {
				// This is probably the safest way there is
				$myrow = DB_fetch_row($result);
				$OldMeasureName = $myrow[0];
				$sql = array();
				$sql[] = "UPDATE qarecordingsheet
					SET operationid='" . $_POST['opid'] . "', typeid='" . $_POST['typeid'] . "', sheetname='" . $_POST['MeasureName'] . "', max_limit='" . $_POST['max'] . "', min_limit='" . $_POST['min'] . "'
					WHERE id = '" . $SelectedMeasureID . "'";
				
			} else {
				$InputError = 1;
				prnMsg( _('The Sheet no longer exist.'),'error');
			}
		}
		$msg = _('Sheet changed successfully');
	} elseif ($InputError !=1) {
		/*SelectedMeasureID is null cos no item selected on first time round so must be adding a record*/
		$sql = "SELECT count(*) FROM qarecordingsheet
				WHERE sheetname " .LIKE. " '".$_POST['MeasureName'] ."' AND operationid=".$_POST['opid']." AND typeid=".$_POST['typeid'];
		$result = DB_query($sql);
		$myrow = DB_fetch_row($result);
		if ( $myrow[0] > 0 ) {
			$InputError = 1;
			prnMsg( _('The Sheet can not be created because it already exists.'),'error');
		} else {
			$sql = "INSERT INTO qarecordingsheet (operationid, typeid,sheetname,max_limit,min_limit,description)
					VALUES ('" . $_POST['opid'] ."','" . $_POST['typeid'] ."','" . $_POST['MeasureName'] ."','" . $_POST['max'] ."','" . $_POST['min'] ."','" . $_POST['description'] ."')";
		}
		$msg = _('New Sheet Added successfully');
	}

	if ($InputError!=1){
		//run the SQL from either of the above possibilites
		if (is_array($sql)) {
			$result = DB_Txn_Begin();
			$tmpErr = _('Could not update Operation type');
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
	unset ($_POST['opid']);
	unset ($_POST['typeid']);
	unset ($_POST['max']);
	unset ($_POST['min']);
	unset ($_POST['description']);

} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button
// PREVENT DELETES IF DEPENDENT RECORDS IN 'stockmaster'
	// Get the original name of the unit of measure the ID is just a secure way to find the unit of measure
	$sql = "SELECT sheetname FROM qarecordingsheet
		WHERE id = '" . $SelectedMeasureID . "'";
	$result = DB_query($sql);
	if ( DB_num_rows($result) == 0 ) {
		// This is probably the safest way there is
		prnMsg( _('Cannot delete this Recording Sheet because it no longer exist'),'warn');
	} else {
		$myrow = DB_fetch_row($result);
		$OldMeasureName = $myrow[0];
			$sql="DELETE FROM qarecordingsheet WHERE id = '" . $SelectedMeasureID . "'";
			$result = DB_query($sql);
			prnMsg( $OldMeasureName . ' ' . _('Recording Sheet has been deleted successfully') . '!','success');
		
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

	$sql = "SELECT a.id,
			a.sheetname,
			b.typename,
			c.operation,
			a.max_limit,
			a.min_limit,
			a.description
			FROM qarecordingsheet a
			INNER JOIN qaoperationtype b ON b.id=a.typeid
			INNER JOIN qaoperation c ON c.id=a.operationid
			ORDER BY a.id";

	$ErrMsg = _('Could not get Operation because');
	$result = DB_query($sql,$ErrMsg);

	echo '<table class="selection">
			<tr>
				<th class="ascending">' . _('Sheet Name') . '</th>
				<th class="ascending">' . _('Operation Type') . '</th>
				<th class="ascending">' . _('Operation') . '</th>
				<th class="ascending">' . _('Max Limit') . '</th>
				<th class="ascending">' . _('Min Limit') . '</th>
				<th class="ascending">' . _('Description') . '</th>
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
		echo '<td>' . $myrow[2] . '</td>';
		echo '<td>' . $myrow[3] . '</td>';
		echo '<td>' . $myrow[4] . '</td>';
		echo '<td>' . $myrow[5] . '</td>';
		echo '<td>' . $myrow[6] . '</td>';
		echo '<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?SelectedMeasureID=' . $myrow[0] . '">' . _('Edit') . '</a></td>';
		echo '<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?SelectedMeasureID=' . $myrow[0] . '&amp;delete=1" onclick="return confirm(\'' . _('Are you sure you wish to delete this Sheet?') . '\');">' . _('Delete')  . '</a></td>';
		echo '</tr>';

	} //END WHILE LIST LOOP
	echo '</table><br />';
} //end of ifs and buts!


if (isset($SelectedMeasureID)) {
	echo '<div class="centre">
			<a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">' . _('Review Recording Sheet') . '</a>
		</div>';
}

echo '<br />';

if (! isset($_GET['delete'])) {

	echo '<form method="post" name="form" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') .  '">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	if (isset($SelectedMeasureID)) {
		//editing an existing section

		$sql = "SELECT id,
				operationid,
				typeid,
				sheetname,
				max_limit,
				min_limit,
				description
				FROM qarecordingsheet
				WHERE id='" . $SelectedMeasureID . "'";

		$result = DB_query($sql);
		if ( DB_num_rows($result) == 0 ) {
			prnMsg( _('Could not retrieve the requested Operation, please try again.'),'warn');
			unset($SelectedMeasureID);
		} else {
			$myrow = DB_fetch_array($result);

			$_POST['MeasureID'] = $myrow['id'];
			$_POST['opid']  = $myrow['operationid'];
			$_POST['MeasureName']  = $myrow['sheetname'];
			$_POST['typeid']  = $myrow['typeid'];
			$_POST['max']  = $myrow['max_limit'];
			$_POST['min']  = $myrow['min_limit'];
			$_POST['description']  = $myrow['description'];

			echo '<input type="hidden" name="SelectedMeasureID" value="' . $_POST['MeasureID'] . '" />';
			echo '<table class="selection">';
		}

	}  else {
		$_POST['MeasureName']='';
		echo '<table>';
	}
	echo '<tr>
		<td>' . _('Operation') . ':' . '</td>
		<td>';
		$sql1 = "SELECT id, operation
				FROM qaoperation";
		$result1 = DB_query($sql1);
	echo '<select name="opid" required>';
	echo '<option value="">--Please Select Operation--</option>';
		while($myr = DB_fetch_row($result1)){
		echo '<option '.((isset($_POST['opid']) && $_POST['opid']==$myr[0])? 'selected':'').' value="'.$myr[0].'">'.$myr[1].'</option>';
		}
	echo '</select>';
	echo '</td>
		</tr>';
	echo '<tr>
		<td>' . _('Type') . ':' . '</td>
		<td>';
	echo '<select name="typeid" required>';
		$sql = "SELECT id, typename
				FROM qaoperationtype";
		$result1 = DB_query($sql);
	echo '<option value="">--Please Select Operation Type--</option>';
		while($myr = DB_fetch_row($result1)){
		echo '<option '.((isset($_POST['typeid']) && $_POST['typeid']==$myr[0])? 'selected':'').' value="'.$myr[0].'">'.$myr[1].'</option>';
		}
	echo '</select>';
	echo '</td>
		</tr>';
	echo '<tr>
		<td>' . _('Sheet Name') . ':' . '</td>
		<td><input required="required" type="text" name="MeasureName" title="'._('Cannot be blank or contains illegal characters').'" placeholder="'._('More than one character').'" size="60" maxlength="100" value="' . $_POST['MeasureName'] . '" /></td>
		</tr>';
	echo '<tr>
		<td>' . _('Max Limit') . ':' . '</td>
		<td><input required="required" type="text" name="max" title="'._('Cannot be blank or contains illegal characters').'" placeholder="'._('More than one character').'" size="30" maxlength="30" value="' . $_POST['max'] . '" /></td>
		</tr>';
	echo '<tr>
		<td>' . _('Min Limit') . ':' . '</td>
		<td><input required="required" type="text" name="min" title="'._('Cannot be blank or contains illegal characters').'" placeholder="'._('More than one character').'" size="30" maxlength="30" value="' . $_POST['min'] . '" /></td>
		</tr>';
	echo '<tr>
		<td>' . _('Description') . ':' . '</td>
		<td><input required="required" type="text" name="description" title="'._('Cannot be blank or contains illegal characters').'" placeholder="'._('More than one character').'" size="60" maxlength="100" value="' . $_POST['description'] . '" /></td>
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
