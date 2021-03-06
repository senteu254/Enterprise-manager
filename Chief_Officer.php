<?php
/* $Id: Departments.php 4567 2011-05-15 04:34:49Z daintree $*/

include('includes/session.inc');

$Title = _('Chief Officer');

include('includes/header.inc');
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/magnifier.png" title="' .
		_('Departments') . '" alt="" />' . ' ' . $Title . '</p>';

if ( isset($_GET['SelectedSectionID']) )
	$SelectedDepartmentID = $_GET['SelectedSectionID'];
elseif (isset($_POST['SelectedSectionID']))
	$SelectedDepartmentID = $_POST['SelectedSectionID'];

if (isset($_POST['Submit'])) {

	//initialise no input errors assumed initially before we test

	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible

	if (ContainsIllegalCharacters($_POST['DepartmentName'])) {
		$InputError = 1;
		prnMsg( _('The description of the Chief officer must not contain the character') . " '&amp;' " . _('or the character') ." '",'error');
	}
	if (trim($_POST['DepartmentName']) == '') {
		$InputError = 1;
		prnMsg( _('The Name of the Chief Officer should not be empty'), 'error');
	}

	if (isset($_POST['SelectedSectionID'])
		AND $_POST['SelectedSectionID']!=''
		AND $InputError !=1) {


		/*SelectedDepartmentID could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/
		// Check the name does not clash
		$sql = "SELECT count(*) FROM section
				WHERE id_sec <> '" . $SelectedDepartmentID ."'
				AND section_name " . LIKE . " '" . $_POST['DepartmentName'] . "'";
		$result = DB_query($sql);
		$myrow = DB_fetch_row($result);
		if ( $myrow[0] > 0 ) {
			$InputError = 1;
			prnMsg( _('This Chief Officer name already exists.'),'error');
		} else {
			// Get the old name and check that the record still exist neet to be very careful here

			$sql = "SELECT co_name
					FROM chiefofficer
					WHERE id_co= '" . $SelectedDepartmentID . "'";
			$result = DB_query($sql);
			if ( DB_num_rows($result) != 0 ) {
				// This is probably the safest way there is
				$myrow = DB_fetch_array($result);
				$OldDepartmentName = $myrow['co_name'];
				$sql = array();
				$sql[] = "UPDATE chiefofficer
							SET co_name='" . $_POST['DepartmentName'] . "',
								id_dept='" . $_POST['Department'] . "',
								emp_id='" . $_POST['Authoriser'] . "'
							WHERE co_name " . LIKE . " '" . $OldDepartmentName . "'";
			} else {
				$InputError = 1;
				prnMsg( _('The Chief Officer does not exist.'),'error');
			}
		}
		$msg = _('The SChief Officer has been modified');
	} elseif ($InputError !=1) {
		/*SelectedDepartmentID is null cos no item selected on first time round so must be adding a record*/
		$sql = "SELECT count(*) FROM chiefofficer
				WHERE co_name " . LIKE . " '" . $_POST['DepartmentName'] . "'";
		$result = DB_query($sql);
		$myrow = DB_fetch_row($result);
		if ( $myrow[0] > 0 ) {
			$InputError = 1;
			prnMsg( _('There is already a Chief officer with the specified name.'),'error');
		} else {
			$sql = "INSERT INTO chiefofficer (id_dept,co_name,
											 emp_id )
					VALUES ('" . $_POST['Department'] . "','" . $_POST['DepartmentName'] . "',
							'" . $_POST['Authoriser'] . "')";
		}
		$msg = _('The new Chief Officer has been created');
	}

	if ($InputError!=1){
		//run the SQL from either of the above possibilites
		if (is_array($sql)) {
			$result = DB_Txn_Begin();
			$ErrMsg = _('The Chief Officer could not be inserted');
			$DbgMsg = _('The sql that failed was') . ':';
			foreach ($sql as $SQLStatement ) {
				$result = DB_query($SQLStatement, $ErrMsg,$DbgMsg,true);
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
        echo '<br />';
	}
	unset ($SelectedDepartmentID);
	unset ($_POST['SelectedSectionID']);
	unset ($_POST['DepartmentName']);

} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button


	$sql = "SELECT id_co
			FROM chiefofficer
			WHERE id_co = '" . $SelectedDepartmentID . "'";
	$result = DB_query($sql);
	if ( DB_num_rows($result) == 0 ) {
		prnMsg( _('You cannot delete this Chief Officer'),'warn');
	} else {
			$sql="DELETE FROM chiefofficer WHERE id_co='" . $SelectedDepartmentID . "'";
			$result = DB_query($sql);
			prnMsg( _('The Chief Officer has been removed') . '!','success');
		}

	unset ($SelectedDepartmentID);
	unset ($_GET['SelectedSectionID']);
	unset($_GET['delete']);
	unset ($_POST['SelectedSectionID']);
	unset ($_POST['DepartmentID']);
	unset ($_POST['DepartmentName']);
}

 if (!isset($SelectedDepartmentID)) {

	$sql = "SELECT *
			FROM chiefofficer,departments
			WHERE chiefofficer.id_dept = departments.departmentid 
			ORDER BY id_co";

	$ErrMsg = _('There are no Chief officer created');
	$result = DB_query($sql,$ErrMsg);

	echo '<table class="selection">
			<tr>
				<th>' . _('Chief Officer Name') . '</th>
				<th>' . _('Department Name') . '</th>
				<th>' . _('Authoriser') . '</th>
			</tr>';

	$k=0; //row colour counter
	while ($myrow = DB_fetch_array($result)) {

		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k++;
		}

		echo '<td>' . $myrow['co_name'] . '</td>
				<td>' . $myrow['description'] . '</td>
				<td>' . $myrow['emp_id'] . '</td>
				<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?SelectedSectionID=' . $myrow['id_co'] . '">' . _('Edit') . '</a></td>
				<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?SelectedSectionID=' . $myrow['id_co'] . '&amp;delete=1" onclick="return confirm(\'' . _('Are you sure you wish to delete this Chief Officer?') . '\');">'  . _('Delete')  . '</a></td>
			</tr>';

	} //END WHILE LIST LOOP
	echo '</table>';
} //end of ifs and buts!


if (isset($SelectedDepartmentID)) {
	echo '<div class="centre">
			<a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">' . _('View all Chief Officers') . '</a>
		</div>';
}

echo '<br />';

if (! isset($_GET['delete'])) {

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') .  '">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	if (isset($SelectedDepartmentID)) {
		//editing an existing section

		$sql = "SELECT *
				FROM chiefofficer,departments
				WHERE chiefofficer.id_dept=departments.departmentid 
				AND id_co='" . $SelectedDepartmentID . "'";

		$result = DB_query($sql);
		if ( DB_num_rows($result) == 0 ) {
			prnMsg( _('The selected Chief Officer could not be found.'),'warn');
			unset($SelectedDepartmentID);
		} else {
			$myrow = DB_fetch_array($result);

			$_POST['DepartmentID'] = $myrow['id_co'];
			$_POST['DepartmentName']  = $myrow['co_name'];
			$AuthoriserID	= $myrow['emp_id'];
			$DeptID			= $myrow['id_dept'];

			echo '<input type="hidden" name="SelectedSectionID" value="' . $_POST['DepartmentID'] . '" />';
			echo '<table class="selection">';
		}

	}  else {
		$_POST['DepartmentName']='';
		echo '<table class="selection">';
	}
	echo '<tr>
			<td>' . _('Chief Officer Name') . ':' . '</td>
			<td><input type="text" name="DepartmentName" size="50" required="required" title="' ._('The Section name is required') . '" maxlength="100" value="' . $_POST['DepartmentName'] . '" /></td>
		</tr>
		<tr>
			<td>' . _('Department') . '</td>
			<td><select name="Department">';
	$usersql="SELECT * FROM departments";
	$userresult=DB_query($usersql);
	while ($myrow=DB_fetch_array($userresult)) {
		if ($myrow['departmentid']==$DeptID) {
			echo '<option selected="True" value="'.$myrow['departmentid'].'">' . $myrow['description'] . '</option>';
		} else {
			echo '<option value="'.$myrow['departmentid'].'">' . $myrow['description'] . '</option>';
		}
	}
	echo '</select></td>
		</tr>
		<tr>
			<td>' . _('Authoriser') . '</td>
			<td><select name="Authoriser">';
	$usersql="SELECT userid FROM www_users";
	$userresult=DB_query($usersql);
	while ($myrow=DB_fetch_array($userresult)) {
		if ($myrow['userid']==$AuthoriserID) {
			echo '<option selected="True" value="'.$myrow['userid'].'">' . $myrow['userid'] . '</option>';
		} else {
			echo '<option value="'.$myrow['userid'].'">' . $myrow['userid'] . '</option>';
		}
	}
	echo '</select></td>
		</tr>
		</table>
		<br />
		<div class="centre">
			<input type="submit" name="Submit" value="' . _('Enter Information') . '" />
		</div>
        </div>
		</form>';

} //end if record deleted no point displaying form to add record

include('includes/footer.inc');
?>