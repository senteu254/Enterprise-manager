<?php
/* $Id: Departments.php 4567 2011-05-15 04:34:49Z daintree $*/

include('includes/session.inc');

$Title = _('Order Number Control');

include('includes/header.inc');
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/magnifier.png" title="' .
		_('Order Number Control') . '" alt="" />' . ' ' . $Title . '</p>';

if ( isset($_GET['SelectedID']) )
	$SelectedID = $_GET['SelectedID'];
elseif (isset($_POST['SelectedID']))
	$SelectedID = $_POST['SelectedID'];

if (isset($_POST['Submit'])) {

	//initialise no input errors assumed initially before we test

	$InputError = 0;


	if (trim($_POST['from']) == '') {
		$InputError = 1;
		prnMsg( _('The Range From should not be empty'), 'error');
	}
	
	if (trim($_POST['to']) == '') {
		$InputError = 1;
		prnMsg( _('The Range To should not be empty'), 'error');
	}
	
	if ($_POST['from'] > $_POST['to']) {
		$InputError = 1;
		prnMsg( _('The Range From should not be grater than Range To'), 'error');
	}

	if (isset($_POST['SelectedID'])
		AND $_POST['SelectedID']!=''
		AND $InputError !=1) {


		/*SelectedDepartmentID could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/
		// Check the name does not clash
		$sql = "SELECT count(*) FROM purchorder_control
				WHERE controlid <> '" . $SelectedID ."'
				AND fy " . LIKE . " '" . FormatDateForSQL($_POST['fy']) . "' 
				AND type " . LIKE . " '" . $_POST['type'] . "'";
		$result = DB_query($sql);
		$myrow = DB_fetch_row($result);
		if ( $myrow[0] > 0 ) {
			$InputError = 1;
			prnMsg( _('This control name already exists.'),'error');
		} else {
			// Get the old name and check that the record still exist neet to be very careful here

			$sql = "SELECT fy
					FROM purchorder_control
					WHERE controlid = '" . $SelectedID . "'";
			$result = DB_query($sql);
			if ( DB_num_rows($result) != 0 ) {
				// This is probably the safest way there is
				$myrow = DB_fetch_array($result);
				$OldDepartmentName = $myrow['description'];
				$sql = array();
				$sql[] = "UPDATE purchorder_control
							SET fy='" . FormatDateForSQL($_POST['fy']) . "',
								type='" . $_POST['type'] . "',
								order_from='" . $_POST['from'] . "',
								order_to='" . $_POST['to'] . "'
							WHERE controlid = '" . $SelectedID . "'";
			} else {
				$InputError = 1;
				prnMsg( _('The control does not exist.'),'error');
			}
		}
		$msg = _('The Control has been modified');
	} elseif ($InputError !=1) {
		/*SelectedDepartmentID is null cos no item selected on first time round so must be adding a record*/
		$sql = "SELECT count(*) FROM purchorder_control
				WHERE fy " . LIKE . " '" . FormatDateForSQL($_POST['fy']) . "' and type " .LIKE. " '".  $_POST['type'] ."'";
		$result = DB_query($sql);
		$myrow = DB_fetch_row($result);
		if ( $myrow[0] > 0 ) {
			$InputError = 1;
			prnMsg( _('There is already a control with the specified information.'),'error');
		} else {
			$sql = "INSERT INTO purchorder_control (fy,
											 type,
											 order_from,
											 order_to )
					VALUES ('" . FormatDateForSQL($_POST['fy']) . "',
							'" . $_POST['type'] . "',
							'" . $_POST['from'] . "',
							'" . $_POST['to'] . "')";
		}
		$msg = _('The new control has been created');
	}

	if ($InputError!=1){
		//run the SQL from either of the above possibilites
		if (is_array($sql)) {
			$result = DB_Txn_Begin();
			$ErrMsg = _('The control could not be inserted');
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
	unset ($SelectedID);
	unset ($_POST['SelectedID']);
	unset ($_POST['fy']);
	unset ($_POST['type']);
	unset ($_POST['from']);
	unset ($_POST['to']);

} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

			$sql="DELETE FROM purchorder_control WHERE controlid = '" . $SelectedID. "'";
			$result = DB_query($sql);
			prnMsg( $SelectedID . ' ' . _('The Control has been removed') . '!','success');
	unset ($SelectedID);
	unset ($_GET['SelectedID']);
	unset($_GET['delete']);
	unset ($_POST['SelectedID']);
	unset ($_POST['ControlID']);
	unset ($_POST['fy']);
	unset ($_POST['type']);
	unset ($_POST['from']);
	unset ($_POST['to']);
}

 if (!isset($SelectedID)) {

	$sql = "SELECT controlid,
						fy,
						type,
						order_from,
						order_to
				FROM purchorder_control
				ORDER BY controlid";

	$ErrMsg = _('There are no controls created');
	$result = DB_query($sql,$ErrMsg);

	echo '<table class="selection">
			<tr>
				<th>' . _('Financial Year End') . '</th>
				<th>' . _('Order Type') . '</th>
				<th>' . _('Range From') . '</th>
				<th>' . _('Range To') . '</th>
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

		echo '<td>' . ConvertSQLDate($myrow['fy']) . '</td>
				<td>' . $myrow['type'] . '</td>
				<td>' . $myrow['order_from'] . '</td>
				<td>' . $myrow['order_to'] . '</td>
				<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?SelectedID=' . $myrow['controlid'] . '">' . _('Edit') . '</a></td>
				<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?SelectedID=' . $myrow['controlid'] . '&amp;delete=1" onclick="return confirm(\'' . _('Are you sure you wish to delete this Control?') . '\');">'  . _('Delete')  . '</a></td>
			</tr>';

	} //END WHILE LIST LOOP
	echo '</table>';
} //end of ifs and buts!


if (isset($SelectedID)) {
	echo '<div class="centre">
			<a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">' . _('View all Controls') . '</a>
		</div>';
}

echo '<br />';

if (! isset($_GET['delete'])) {

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') .  '">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	if (isset($SelectedID)) {
		//editing an existing section

		$sql = "SELECT controlid,
						fy,
						type,
						order_from,
						order_to
				FROM purchorder_control
				WHERE controlid='" . $SelectedID . "'";

		$result = DB_query($sql);
		if ( DB_num_rows($result) == 0 ) {
			prnMsg( _('The selected control could not be found.'),'warn');
			unset($SelectedDepartmentID);
		} else {
			$myrow = DB_fetch_array($result);

			$_POST['ControlID'] = $myrow['controlid'];
			$_POST['fy']  = $myrow['fy'];
			$_POST['Type']  = $myrow['type'];
			$_POST['from']	= $myrow['order_from'];
			$_POST['to']	= $myrow['order_to'];

			echo '<input type="hidden" name="SelectedID" value="' . $_POST['ControlID'] . '" />';
			echo '<table class="selection">';
		}

	}  else {
		$_POST['fy']='';
		echo '<table class="selection">';
	}
	echo '<tr>
			<td>' . _('Financial Year') . ':' . '</td>
			<td><select name="fy">';echo'<option value="' .  Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],-1)) . '">'.Date('Y',YearEndDate($_SESSION['YearEnd'],-2)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd'],-1)).'</option>';			   
echo'<option selected value="'.  Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],0)) . '">'.Date('Y',YearEndDate($_SESSION['YearEnd'],-1)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd'],0)).'</option>';

echo'<option value="'.  Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],+1)) . '">'.Date('Y',YearEndDate($_SESSION['YearEnd'],0)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd'],+1)).'</option>';
	echo '</select></td>
		</tr>
		<tr>
			<td>' . _('Order Type') . '</td>
			<td><select name="type">';
	$arr=array('LPO','LSO');
	foreach ($arr as $data) {
		if ($data==$_POST['type']) {
			echo '<option selected="True" value="'.$data.'">' . $data . '</option>';
		} else {
			echo '<option value="'.$data.'">' . $data . '</option>';
		}
	}
	echo '</select></td>
		</tr>
		<tr>
			<td>' . _('Range From') . '</td>
			<td><input name="from" type="text" autocomplete="off" class="number" value="'.$_POST['from'].'"/></td>
		</tr>
		<tr>
			<td>' . _('Range To') . '</td>
			<td><input name="to" type="text" autocomplete="off" class="number" value="'.$_POST['to'].'"/></td>
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