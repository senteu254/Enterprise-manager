	<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
	<script type="text/javascript" src="js/qsearch.js"></script>									
<?php
/* $Id: Suppliers.php 7088 2015-01-20 08:02:37Z exsonqu $ */

include('includes/session.inc');
$Title = _('Visitor Maintenance');
/* webERP manual links before header.inc */
$ViewTopic= 'Security';
$BookMark = 'NewVisitor';
include('includes/header.inc');

include('includes/SQL_CommonFunctions.inc');

if (isset($_GET['VisitorID'])) {
	$VisitorID = mb_strtoupper($_GET['VisitorID']);
} elseif (isset($_POST['VisitorID'])) {
	$VisitorID = mb_strtoupper($_POST['VisitorID']);
} else {
	unset($VisitorID);
}
if (isset($_GET['MaterialID'])) {
	$MaterialID = mb_strtoupper($_GET['MaterialID']);
} elseif (isset($_POST['MaterialID'])) {
	$MaterialID = mb_strtoupper($_POST['MaterialID']);
} else {
	unset($MaterialID);
}
if(!isset($MaterialID) and !isset($VisitorID)){
	echo '<a href="' . $RootPath . '/Sec_Visitors.php">Back to Search Visitor</a>';
	include('includes/footer.inc');
	exit;
}
echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/visitor.png" title="' . _('Search') . '" alt="" />' . ' ' . _('Visitor Materials Booking Register') . '</p>';
$InputError = 0;

if (isset($Errors)) {
	unset($Errors);
}
$Errors=Array();

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$i=1;
	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	if (mb_strlen(trim($_POST['name'])) > 40
		OR mb_strlen(trim($_POST['name'])) == 0
		OR trim($_POST['name']) == '') {

		$InputError = 1;
		prnMsg(_('The material name must be entered and be forty characters or less long'),'error');
		$Errors[$i]='Name';
		$i++;
	}


	if ($InputError != 1) {

		if (!isset($_POST['New'])) {
				$sql = "UPDATE visitor_material_register SET description='" . $_POST['name'] . "',
							destination='" . $_POST['dest'] . "',
							gate='" . $_POST['gate'] . "',
							purpose='" . $_POST['purpose'] . "'
						WHERE id = '".$MaterialID."'";

			$ErrMsg = _('The material could not be updated because');
			$DbgMsg = _('The SQL that was used to update the material but failed was');
			// echo $sql;
			$result = DB_query($sql, $ErrMsg, $DbgMsg);

			prnMsg(_('The material master record for') . ' ' . $MaterialID . ' ' . _('has been updated'),'success');
			
			unset($MaterialID);

		} else { //its a new visitor
				/* system assigned, sequential, numeric */
				//$VisitorID = GetNextTransNo(52, $db);
			
			$sql = "INSERT INTO visitor_material_register (visitorid,
										description,
										gate,
										destination,
										purpose,
										security_in)
								 VALUES ('" . $_POST['VisitorID'] . "',
								 	'" . $_POST['name'] . "',
									'" . $_POST['gate'] . "',
									'" . $_POST['dest'] . "',
									'" . $_POST['purpose'] . "',
									'".$_SESSION['UsersRealName']."')";

			$ErrMsg = _('The material') . ' ' . $_POST['name'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the material but failed was');

			$result = DB_query($sql, $ErrMsg, $DbgMsg);

			prnMsg(_('A new material for') . ' ' . $_POST['name'] . ' ' . _('has been added to the database'),'success');

			//unset($VisitorID);
			unset($_POST['gate']);
			unset($_POST['name']);
			unset($_POST['dest']);
			unset($_POST['purpose']);

		}

	} else {

		prnMsg(_('Validation failed') . _('no updates or deletes took place'),'warn');

	}

} elseif (isset($_POST['delete']) AND $_POST['delete'] != '') {

//the link to delete a selected record was clicked instead of the submit button

	$CancelDelete = 0;

// PREVENT DELETES IF DEPENDENT RECORDS IN 'SuppTrans' , PurchOrders, SupplierContacts

	if ($CancelDelete == 0) {
		$sql="DELETE FROM visitor_material_register WHERE id='" . $MaterialID . "'";
		$result = DB_query($sql);
		prnMsg(_('Material record for') . ' ' . $MaterialID . ' ' . _('has been deleted'),'success');
		unset($MaterialID);
		//unset($_SESSION['VisitorID']);
	} //end if Delete supplier
}
if(isset($_GET['BookedOut']) && $_GET['BookedOut']=='True'){
$sql = "UPDATE visitor_material_register SET booked_out=1,
										booked_out_time='". date('Y-m-d H:m:s') ."',
										security_out='".$_SESSION['UsersRealName']."'
						WHERE id = '".$MaterialID."'";

			$ErrMsg = _('The material could not be updated because');
			$DbgMsg = _('The SQL that was used to update the material but failed was');
			// echo $sql;
			$result = DB_query($sql, $ErrMsg, $DbgMsg);

			prnMsg(_('The material number') . ' ' . $MaterialID . ' ' . _('has been booked out.'),'success');
			
			unset($MaterialID);
}

$link ='' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '';
$sql = "SELECT *, b.description as gatename, a.description as name
			FROM visitor_material_register a
			INNER JOIN gates b ON b.GateID = a.gate
			WHERE visitorid=".$VisitorID." and booked_out=0";

		$result = DB_query($sql);
			echo '<table class="selection">
					<tr><th>Description</th><th>Destination</th><th>Gate</th><th>Date</th><th>Purpose</th><th></th></tr>';
					while($row = DB_fetch_array($result)){
			echo '<tr><td>'.$row['name'].'</td><td>'.$row['destination'].'</td><td>'.$row['gatename'].'</td><td>'.ConvertSQLDateTime($row['booked_in_time']).'</td><td>'.$row['purpose'].'</td><td><a href="'.$link.'?MaterialID=' . $row['id'] .'&VisitorID='.$VisitorID.'">Edit</a> ||'; ?> <a onclick="return confirm('Are you sure you want to book out this item?');" href="<?php echo $link; ?>?MaterialID=<?php echo $row['id']; ?>&VisitorID=<?php echo $VisitorID; ?>&BookedOut=True">Book Out</a></td></tr><?php
			}
			echo '</table>';

if (!isset($MaterialID)) {

/*If the page was called without $SupplierID passed to page then assume a new supplier is to be entered show a form with a Supplier Code field other wise the form showing the fields with the existing entries against the supplier will show for editing with only a hidden SupplierID field*/

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?VisitorID=' . $VisitorID . '">';
	echo '<div>';
	echo '<a href="' . $RootPath . '/Sec_Visitors.php">Back to Search Visitor</a>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	echo '<input type="hidden" name="New" value="Yes" />';
	echo '<input type="hidden" name="VisitorID" value="' . $VisitorID . '" />';

	echo '<table class="selection">';

	/* if $AutoSupplierNo is off (not 0) then provide an input box for the SupplierID to manually assigned */
	echo '<tr><td>';
		/* if $AutoSupplierNo is off (i.e. 0) then provide an input box for the SupplierID to manually assigned */
			echo _('Visitor Number') . ':</td>
					<td>' . $VisitorID . '</td></tr>';
	echo '<tr>
			<td>' . _('Item Description') . ':</td>
			<td><input type="text" pattern="(?!^\s+$)[^<>+]{1,40}" required="required" title="'._('The Item Description should not be blank and should be less than 40 legal characters').'" name="name" size="42" placeholder="'._('Within 40 legal characters').'" maxlength="40" /></td>
		</tr>';
		$sql = "SELECT gates.GateID,
				gates.description
			FROM gates";
			$result = DB_query($sql);
	echo '<tr><td>' . _('Gate') . ':</td><td colspan="3">
			<select name="gate">';
			echo '<option value="">--Please Select Gate--</option>';
			while ($myrow = DB_fetch_array($result)) {
			echo '<option value="'. $myrow[0] .'">'. $myrow[0] .'-'. $myrow[1] .'</option>';
			}
			echo '</select></td></tr>';
	echo '<tr>
			<td>' . _('Destination') . ':</td>
			<td><input type="text" title="'._('The input should be less than 40 characters').'" placeholder="'._('Less than 40 characters').'" name="dest" size="42" maxlength="40" /></td>
		</tr>
		<tr>';
	echo '
		<tr>
			<td>' . _('Purpose') . ':</td>
			<td><textarea name="purpose" cols="40" rows="5"></textarea></td>
		</tr>
	
		</table>
		<br />
		<div class="centre"><input type="submit" name="submit" value="' . _('Insert New Material') . '" /></div>';
	echo '</div>
		</form>';
}else {


//VisitorID exists - either passed when calling the form or from the form itself

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
	echo '<div>';
	echo '<a href="' . $RootPath . '/Sec_Visitors.php">Back to Search Visitor</a>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table class="selection">';

	if (!isset($_POST['New'])) {
		$sql = "SELECT *
			FROM visitor_material_register
			WHERE id = '" . $MaterialID . "'";

		$result = DB_query($sql);
		$myrow = DB_fetch_array($result);

		$_POST['name'] = stripcslashes($myrow['description']);
		$_POST['dest'] = $myrow['destination'];
		$_POST['gate'] = $myrow['gate'];
		$_POST['purpose'] = stripcslashes($myrow['purpose']);

		echo '<tr><td><input type="hidden" name="MaterialID" value="' . $MaterialID . '" /></td></tr>';
		echo '<input type="hidden" name="VisitorID" value="' . $myrow['visitorid'] . '" />';

	}
	// its a new supplier being added
		echo '<tr><td>';
		/* if $AutoSupplierNo is off (i.e. 0) then provide an input box for the SupplierID to manually assigned */
			echo _('Material Number') . ':</td>
					<td>' . $MaterialID . '</td></tr>';
	

	echo '<tr>
			<td>' . _('Item Description') . ':</td>
			<td><input '.(in_array('Name',$Errors) ? 'class="inputerror"' : '').' type="text" name="name" value="' . $_POST['name'] . '" size="42" maxlength="40" /></td>
		</tr>';
		$sql = "SELECT gates.GateID,
				gates.description
			FROM gates";
			$result = DB_query($sql);
	echo '<tr><td>' . _('Gate') . ':</td><td colspan="3">
			<select name="gate">';
		echo '<option value="">--Please Select Gate--</option>';
			while ($myrow = DB_fetch_array($result)) {
			echo '<option '.($_POST['gate']==$myrow[0] ? 'selected':'').' value="'. $myrow[0] .'">'. $myrow[0] .'-'. $myrow[1] .'</option>';
			}
		echo '</select></td></tr>';
		echo '<tr>
			<td>' . _('Destination') . ':</td>
			<td><input type="text" name="dest" value="' . $_POST['dest'] . '" size="42" maxlength="15" /></td>
		</tr>';

	echo '<tr>
			<td>' . _('Purpose') . ':</td>
			<td><textarea name="purpose" cols="40" rows="5">'. $_POST['purpose'] .'</textarea></td>
		</tr>
	
		</table>
		</table>';

	if (isset($_POST['New'])) {
		echo '<br />
				<div class="centre">
					<input type="submit" name="submit" value="' . _('Add These New Material Details') . '" />
				</div>';
	} else {
		echo '<br />
				<div class="centre">
					<input type="submit" name="submit" value="' . _('Update Material') . '" />
				</div>
			<br />';
//		echo '<p><font color=red><b>' . _('WARNING') . ': ' . _('There is no second warning if you hit the delete button below') . '. ' . _('However checks will be made to ensure there are no outstanding purchase orders or existing accounts payable transactions before the deletion is processed') . '<br /></font></b>';
		prnMsg(_('WARNING') . ': ' . _('There is no second warning if you hit the delete button below') . '. ', 'Warn');
		echo '<br />
			<div class="centre">
				<input type="submit" name="delete" value="' . _('Delete Material') . '" onclick="return confirm(\'' . _('Are you sure you wish to delete this Material?') . '\');" />
			</div>';
	}
	echo '</div>
		</form>';} // end of main ifs

include('includes/footer.inc');
?>
