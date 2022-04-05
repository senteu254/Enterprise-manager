<?php
/* $Id: Departments.php 4567 2011-05-15 04:34:49Z daintree $*/

include('includes/session.inc');

$Title = _('Cheque Control');

include('includes/header.inc');
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/magnifier.png" title="' .
		_('Cheque Control') . '" alt="" />' . ' ' . $Title . '</p>';

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
		$sql = "SELECT count(*) FROM cheque_control
				WHERE chequeid <> '" . $SelectedID ."'
				AND fy " . LIKE . " '" . FormatDateForSQL($_POST['fy']) . "' 
				AND bank " . LIKE . " '" . $_POST['bank'] . "'";
		$result = DB_query($sql);
		$myrow = DB_fetch_row($result);
		if ( $myrow[0] > 0 ) {
			$InputError = 1;
			prnMsg( _('This control name already exists.'),'error');
		} else {
			// Get the old name and check that the record still exist neet to be very careful here

			$sql = "SELECT fy
					FROM cheque_control
					WHERE chequeid = '" . $SelectedID . "'";
			$result = DB_query($sql);
			if ( DB_num_rows($result) != 0 ) {
				// This is probably the safest way there is
				$myrow = DB_fetch_array($result);
				$OldDepartmentName = $myrow['description'];
				$sql = array();
				$sql[] = "UPDATE cheque_control
							SET fy='" . FormatDateForSQL($_POST['fy']) . "',
								bank='" . $_POST['bank'] . "',
								cheque_from='" . $_POST['from'] . "',
								cheque_to='" . $_POST['to'] . "'
							WHERE chequeid = '" . $SelectedID . "'";
			} else {
				$InputError = 1;
				prnMsg( _('The control does not exist.'),'error');
			}
		}
		$msg = _('The Control has been modified');
	}else {
			$sql = "INSERT INTO cheque_control (fy,
											 bank,
											 cheque_from,
											 cheque_to )
					VALUES ('" . FormatDateForSQL($_POST['fy']) . "',
							'" . $_POST['bank'] . "',
							'" . $_POST['from'] . "',
							'" . $_POST['to'] . "')";
		}
		$msg = _('The new control has been created');
	

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
	unset ($_POST['bank']);
	unset ($_POST['from']);
	unset ($_POST['to']);

} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

			$sql="DELETE FROM cheque_control WHERE chequeid = '" . $SelectedID. "'";
			$result = DB_query($sql);
			prnMsg( $SelectedID . ' ' . _('The Control has been removed') . '!','success');
	unset ($SelectedID);
	unset ($_GET['SelectedID']);
	unset($_GET['delete']);
	unset ($_POST['SelectedID']);
	unset ($_POST['ControlID']);
	unset ($_POST['fy']);
	unset ($_POST['bank']);
	unset ($_POST['from']);
	unset ($_POST['to']);
}elseif (isset($_GET['close'])) {
//the link to delete a selected record was clicked instead of the submit button
			$sql="UPDATE cheque_control SET close = 1 WHERE chequeid = '" . $SelectedID. "'";
			$result = DB_query($sql);
			prnMsg( $SelectedID . ' ' . _('The Control range has been closed') . '!','success');
}

 if (!isset($SelectedID)) {

	$sql = "SELECT chequeid,
						fy,
						bank,
						cheque_from,
						cheque_to,
						close
				FROM cheque_control
				ORDER BY chequeid";

	$ErrMsg = _('There are no controls created');
	$result = DB_query($sql,$ErrMsg);

	echo '<table class="selection">
			<tr>
				<th>' . _('Financial Year End') . '</th>
				<th>' . _('Bank Account Name') . '</th>
				<th>' . _('Range From') . '</th>
				<th>' . _('Range To') . '</th>
				<th>' . _('Status') . '</th>
			</tr>';

	$k=0; //row colour counter
	while ($myrow = DB_fetch_array($result)) {
	/****************************************************************************************/
$SQL1 = "SELECT bankaccountname,
				bankaccounts.accountcode,
				bankaccounts.currcode
		FROM bankaccounts
		INNER JOIN chartmaster
			ON bankaccounts.accountcode=chartmaster.accountcode
		INNER JOIN bankaccountusers
			ON bankaccounts.accountcode=bankaccountusers.accountcode
		WHERE bankaccountusers.userid = '" . $_SESSION['UserID'] ."'
		AND bankaccounts.accountcode = ".$myrow['bank']."
		ORDER BY bankaccountname";

$ErrMsg = _('The bank accounts could not be retrieved because');
$DbgMsg = _('The SQL used to retrieve the bank accounts was');
$Accounts = DB_query($SQL1,$ErrMsg,$DbgMsg);
while ($row=DB_fetch_array($Accounts)){
$bankname=$row['bankaccountname'];
}
/***************************************************************************************/

		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k++;
		}

		echo '<td>' . ConvertSQLDate($myrow['fy']) . '</td>
				<td>' . $bankname . '</td>
				<td>' . $myrow['cheque_from'] . '</td>
				<td>' . $myrow['cheque_to'] . '</td>';
				if($myrow['close']==1){
			    $status="Closed";
				}elseif($myrow['close']==0){
				 $status='Open';
				}
				echo'<td>'.$status.'</td>';
				if($myrow['close']==1){
				echo'<td></td>';
				}elseif($myrow['close']==0){
				echo'<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?SelectedID=' . $myrow['chequeid'] . '">' . _('Edit') . '</a></td>
				<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?SelectedID=' . $myrow['chequeid'] . '&amp;delete=1" onclick="return confirm(\'' . _('Are you sure you wish to delete this Control?') . '\');">'  . _('Delete')  . '</a></td>				
				<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?SelectedID=' . $myrow['chequeid'] . '&amp;close=1" onclick="return confirm(\'' . _('Are you sure you wish to close this Control?') . '\');">'  . _('Close Cheque')  . '</a></td>';
				}
			echo'</tr>';

	} //END WHILE LIST LOOP
	echo '</table>';
} //end of ifs and buts!


if (isset($SelectedID)) {
	echo '<div class="centre">
			<a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">' . _('View all Cheques Controls') . '</a>
		</div>';
}

echo '<br />';

if (! isset($_GET['delete'])) {

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') .  '">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	if (isset($SelectedID)) {
		//editing an existing section

		$sql = "SELECT chequeid,
						fy,
						bank,
						cheque_from,
						cheque_to,
						close
				FROM cheque_control
				WHERE chequeid='" . $SelectedID . "'
				AND close=0 ";

		$result = DB_query($sql);
		if ( DB_num_rows($result) == 0 ) {
			prnMsg( _('The selected  cheque control could not be found.'),'warn');
			unset($SelectedDepartmentID);
		} else {
			$myrow = DB_fetch_array($result);


			$_POST['ControlID'] = $myrow['chequeid'];
			$_POST['fy']  = $myrow['fy'];
			$_POST['bank']  = $myrow['bank'];
			$_POST['from']	= $myrow['cheque_from'];
			$_POST['to']	= $myrow['cheque_to'];

			echo '<input type="hidden" name="SelectedID" value="' . $_POST['ControlID'] . '" />';
			echo '<table class="selection">';
		}

	}  else {
		$_POST['fy']='';
		echo '<table class="selection">';
	}
	echo '<tr>
			<td>' . _('Financial Year') . ':' . '</td>
			<td><select name="fy">';					   
echo'<option selected value="'.  Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],0)) . '">'.Date('Y',YearEndDate($_SESSION['YearEnd'],-1)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd'],0)).'</option>';

echo'<option value="'.  Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],+1)) . '">'.Date('Y',YearEndDate($_SESSION['YearEnd'],0)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd'],+1)).'</option>';
	echo '</select></td>
		</tr>';
##########################################################################################################################################
		$SQL = "SELECT bankaccountname,
				bankaccounts.accountcode,
				bankaccounts.currcode
		FROM bankaccounts
		INNER JOIN chartmaster
			ON bankaccounts.accountcode=chartmaster.accountcode
		INNER JOIN bankaccountusers
			ON bankaccounts.accountcode=bankaccountusers.accountcode
		WHERE bankaccountusers.userid = '" . $_SESSION['UserID'] ."'
		ORDER BY bankaccountname";

$ErrMsg = _('The bank accounts could not be retrieved because');
$DbgMsg = _('The SQL used to retrieve the bank accounts was');
$AccountsResults = DB_query($SQL,$ErrMsg,$DbgMsg);
##########################################################################################################################################
		echo '<tr>
		<td>' . _('Bank Account') . ':</td>
		<td><select name="bank" autofocus="autofocus" required="required" title="' . _('Select the bank account that the payment has been made from') . '" onchange="ReloadForm(UpdateHeader)">';

if (DB_num_rows($AccountsResults)==0){
	echo '</select></td>
		</tr>
		</table>
		<p />';
	prnMsg( _('Bank Accounts have not yet been defined. You must first') . ' <a href="' . $RootPath . '/BankAccounts.php">' . _('define the bank accounts') . '</a> ' . _('and general ledger accounts to be affected'),'warn');
	include('includes/footer.inc');
	exit;
} else {
	echo '<option value=""></option>';
	while ($myrow=DB_fetch_array($AccountsResults)){
	/*list the bank account names */
		if (isset($_POST['BankAccount']) AND $_POST['BankAccount']==$myrow['accountcode']){
			echo '<option selected="selected" value="' . $myrow['accountcode'] . '">' . $myrow['bankaccountname'] . ' - ' . $myrow['currcode'] . '</option>';
		} else {
			echo '<option value="' . $myrow['accountcode'] . '">' . $myrow['bankaccountname'] . ' - ' . $myrow['currcode'] . '</option>';
		}
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