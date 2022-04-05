<?php
/* $Id: SelectSupplier.php 6941 2014-10-26 23:18:08Z daintree $*/

include ('includes/session.inc');
$Title = _('Search Visitors');

/* webERP manual links before header.inc */
$ViewTopic= 'Security';
$BookMark = 'SelectVisitor';

include ('includes/header.inc');
include ('includes/SQL_CommonFunctions.inc');
if (!isset($_SESSION['StaffID'])) {
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/visitor.png" title="' . _('Search') . '" alt="" />' . ' ' . _('Staff Booking Register') . '</p>';
}
if (isset($_GET['StaffID'])) {
	$_SESSION['StaffID']=$_GET['StaffID'];
}

if (!isset($_POST['PageOffset'])) {
	$_POST['PageOffset'] = 1;
} else {
	if ($_POST['PageOffset'] == 0) {
		$_POST['PageOffset'] = 1;
	}
}
if (isset($_POST['Select'])) { /*User has hit the button selecting a supplier */
	$_SESSION['StaffID'] = $_POST['Select'];
	unset($_POST['Select']);
	unset($_POST['Keywords']);
	unset($_POST['idno']);
	unset($_POST['Search']);
	unset($_POST['Go']);
	unset($_POST['Next']);
	unset($_POST['Previous']);
}
if (isset($_POST['CheckIn']) AND $_POST['CheckIn'] != '') {

//the link to delete a selected record was clicked instead of the submit button

	$Cancel = 0;
	$StaffID=$_SESSION['StaffID'];
// PREVENT DELETES IF DEPENDENT RECORDS IN 'SuppTrans' , PurchOrders, SupplierContacts
if(isset($_POST['gate']) && $_POST['gate']==""){
$Cancel = 1;
prnMsg(_('Please Select the gate you want to check in the visitor.'),'warn');
}

	$sql= "SELECT COUNT(*),MAX(CheckID) FROM visitor_timein WHERE VisitorNo='" . $StaffID . "' AND GateID='". $_POST['gate'] ."'";
	$result = DB_query($sql);
	$myrow = DB_fetch_row($result);
	if ($myrow[0] > 0) {
	$sql= "SELECT COUNT(*) FROM visitor_timeout WHERE CheckID='". $myrow[1] ."' AND VisitorNo='" . $StaffID . "' AND GateID='". $_POST['gate'] ."'";
	$result = DB_query($sql);
	$row = DB_fetch_row($result);
	if ($row[0] > 0) {
	$Cancel = 0;
	}else{
		$Cancel = 1;
		prnMsg(_('Cannot Check In this visitor because he/she has already been checked in through this gate.'),'warn');
	}
	}
	if ($Cancel == 0) {
	$checkid = GetNextTransNo(53, $db);
		$sql = "INSERT INTO visitor_timein (CheckID,
										VisitorNo,
										remarks,
										sec_officer,
										GateID)
								 VALUES ('" . $checkid . "',
								 	'" . $StaffID . "',
									'". $_POST['remarks'] ."',
									'". $_SESSION['UserID'] ."',
									'" . $_POST['gate'] . "')";
		$result = DB_query($sql);
		prnMsg(_('Visitor record Number') . ' ' . $StaffID . ' ' . _('has been Checked In'),'success');
	} //end if Delete supplier
}
if (isset($_POST['CheckOut']) AND $_POST['CheckOut'] != '') {

//the link to delete a selected record was clicked instead of the submit button

	$Cancel = 0;
	$StaffID=$_SESSION['StaffID'];
// PREVENT DELETES IF DEPENDENT RECORDS IN 'SuppTrans' , PurchOrders, SupplierContacts
if(isset($_POST['gate']) && $_POST['gate']==""){
$Cancel = 1;
prnMsg(_('Please Select the gate you want to check out the visitor.'),'warn');
}
	$sql= "SELECT COUNT(*),MAX(CheckID) FROM visitor_timein WHERE VisitorNo='" . $StaffID . "' AND GateID='". $_POST['gate'] ."'";
	$result = DB_query($sql);
	$myrow = DB_fetch_row($result);
	if ($myrow[0] > 0) {
	$sql= "SELECT COUNT(*) FROM visitor_timeout WHERE CheckID='". $myrow[1] ."' AND VisitorNo='" . $StaffID . "' AND GateID='". $_POST['gate'] ."'";
	$result = DB_query($sql);
	$row = DB_fetch_row($result);
	if ($row[0] > 0) {
		$Cancel = 1;
		prnMsg(_('Cannot Check Out this visitor because he/she has already been checked Out through this gate.'),'warn');
	}else{
	$Cancel = 0;
	$checkid = $myrow[1];
	}
	}else{
		$Cancel = 1;
		prnMsg(_('Cannot Check Out this visitor because he/she has not been checked in through this gate.'),'warn');
	}
	if ($Cancel == 0) {
		$sql = "INSERT INTO visitor_timeout (CheckID,
										VisitorNo,
										remarks,
										sec_officer,
										GateID)
								 VALUES ('" . $checkid . "',
								 	'" . $StaffID . "',
									'". $_POST['remarks'] ."',
									'". $_SESSION['UserID'] ."',
									'" . $_POST['gate'] . "')";
		$result = DB_query($sql);
		prnMsg(_('Visitor record Number') . ' ' . $StaffID . ' ' . _('has been Checked Out'),'success');
	} //end if Delete supplier
}

if (isset($_POST['Search'])
	OR isset($_POST['Go'])
	OR isset($_POST['Next'])
	OR isset($_POST['Previous'])) {

	if (mb_strlen($_POST['Keywords']) > 0 AND mb_strlen($_POST['idno']) > 0) {
		prnMsg( _('Visitor name keywords have been used in preference to the Visitor ID Number extract entered'), 'info' );
	}
	if ($_POST['Keywords'] == '' AND $_POST['idno'] == '') {
		$SQL = "SELECT *
				FROM employee
				ORDER BY emp_fname";
	} else {
		if (mb_strlen($_POST['Keywords']) > 0) {
			$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
			//insert wildcard characters in spaces
			$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';
			$SQL = "SELECT *
						FROM employee
						WHERE emp_fname " . LIKE . " '" . $SearchString . "'
						ORDER BY emp_fname";
		} elseif (mb_strlen($_POST['idno']) > 0) {
			$_POST['idno'] = mb_strtoupper($_POST['idno']);
			$SQL = "SELECT *
						FROM employee
						WHERE emp_id " . LIKE . " '%" . $_POST['idno'] . "%'
						ORDER BY emp_id";
		}
	} //one of keywords or SupplierCode was more than a zero length string
	$result = DB_query($SQL);
	if (DB_num_rows($result) == 1) {
		$myrow = DB_fetch_row($result);
		$SingleVisitorReturned = $myrow[0];
	}
	if (isset($SingleVisitorReturned)) { /*there was only one supplier returned */
 	   $_SESSION['StaffID'] = $SingleVisitorReturned;
	   unset($_POST['Keywords']);
	   unset($_POST['idno']);
	   unset($_POST['Search']);
        } else {
               unset($_SESSION['StaffID']);
        }
} //end of if search

if (isset($_SESSION['StaffID'])) {
	$VisitorName = '';
	$SQL = "SELECT * FROM employee WHERE employee.emp_id ='" . $_SESSION['StaffID'] . "'";
	$SupplierNameResult = DB_query($SQL);
	if (DB_num_rows($SupplierNameResult) == 1) {
		$myrow = DB_fetch_array($SupplierNameResult);
		$VisitorName = $myrow['emp_fname'].' '.$myrow['emp_lname'];
	}
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/visitor.png" title="' . _('Staff') . '" alt="" />' . ' ' . _('Staff') . ' : <b>' . $_SESSION['StaffID'] . ' - ' . $VisitorName . '</b> ' . _('has been selected') . '.</p>';
	echo '<div class="page_help_text">' . _('Select a menu option to operate using this Staff.') . '</div>';
	echo '<br />
		<table width="90%" cellpadding="4">
		<tr>
			<th style="width:30%">' . _('Staff Information') . '</th>
			<th style="width:30%">' . _('Transaction Information') . '</th>
			<th style="width:40%">' . _('Staff Maintenance') . '</th>
		</tr>';
	echo '<tr><td valign="top" class="select">'; /* Inquiry Options */
		echo '<table style="background:#FFFFFF">
			<tr height="30px"><th width="100px">ID Number :</th><td width="200px" align="left">'.$myrow['id_number'].'</td></tr>
			</table>';
	echo '</td><td valign="top" class="select">'; /* Supplier Maintenance */
	echo '</td><td valign="top" class="select">'; /* Supplier Maintenance */
echo '<a href="' . $RootPath . '/Sec_StaffMaterialRegister.php?StaffID=' . $_SESSION['StaffID'] . '">' . _('Staff Material Register') . '</a>
		<br /><br /><a href="' . $RootPath . '/Sec_VehiclesStaff.php?StaffID=' . $_SESSION['StaffID'] . '">' . _('Staff Vehicles Register') . '</a>
		<br />';
		echo '</td>
		</tr>
		</table>';
} else {
	// Visitor is not selected yet
	echo '<br />';
	echo '<table width="90%" cellpadding="4">
		<tr>
			<th style="width:33%">' . _('Staff Information') . '</th>
			<th style="width:33%">' . _('Staff Maintenance') . '</th>
		</tr>';
	echo '<tr>
			<td valign="top" class="select"></td>
			<td valign="top" class="select">'; /* Visitor Maintenance */
	//echo '<a href="' . $RootPath . '/Sec_VisitorRegister.php">' . _('Add a New Visitor') . '</a><br />';
	echo '</td>
		</tr>
		</table>';
}
echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/magnifier.png" title="' . _('Search') . '" alt="" />' . ' ' . _('Search for Staff') . '</p>
	<table cellpadding="3" class="selection">
	<tr>
		<td>' . _('Enter a partial Name') . ':</td>
		<td>';
if (isset($_POST['Keywords'])) {
	echo '<input type="text" name="Keywords" value="' . $_POST['Keywords'] . '" size="20" maxlength="25" />';
} else {
	echo '<input type="text" name="Keywords" size="20" maxlength="25" />';
}
echo '</td>
		<td><b>' . _('OR') . '</b></td>
		<td>' . _('Enter a partial Staff ID No.') . ':</td>
		<td>';
if (isset($_POST['idno'])) {
	echo '<input type="text" autofocus="autofocus" name="idno" value="' . $_POST['idno'] . '" size="15" maxlength="18" />';
} else {
	echo '<input type="text" autofocus="autofocus" name="idno" size="15" maxlength="18" />';
}
echo '</td></tr>
		</table>
		<br /><div class="centre"><input type="submit" name="Search" value="' . _('Search Now') . '" /></div>';
//if (isset($result) AND !isset($SingleSupplierReturned)) {
if (isset($_POST['Search'])) {
	$ListCount = DB_num_rows($result);
	$ListPageMax = ceil($ListCount / $_SESSION['DisplayRecordsMax']);
	if (isset($_POST['Next'])) {
		if ($_POST['PageOffset'] < $ListPageMax) {
			$_POST['PageOffset'] = $_POST['PageOffset'] + 1;
		}
	}
	if (isset($_POST['Previous'])) {
		if ($_POST['PageOffset'] > 1) {
			$_POST['PageOffset'] = $_POST['PageOffset'] - 1;
		}
	}
	if ($ListPageMax > 1) {
		echo '<p>&nbsp;&nbsp;' . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': </p>';
		echo '<select name="PageOffset">';
		$ListPage = 1;
		while ($ListPage <= $ListPageMax) {
			if ($ListPage == $_POST['PageOffset']) {
				echo '<option value="' . $ListPage . '" selected="selected">' . $ListPage . '</option>';
			} else {
				echo '<option value="' . $ListPage . '">' . $ListPage . '</option>';
			}
			$ListPage++;
		}
		echo '</select>
			<input type="submit" name="Go" value="' . _('Go') . '" />
			<input type="submit" name="Previous" value="' . _('Previous') . '" />
			<input type="submit" name="Next" value="' . _('Next') . '" />';
		echo '<br />';
	}
	echo '<input type="hidden" name="Search" value="' . _('Search Now') . '" />';
	echo '<br />
		<br />
		<br />
		<table cellpadding="2">';
	echo '<tr>
	  		<th class="ascending">' . _('Staff No') . '</th>
			<th class="ascending">' . _('Staff Name') . '</th>
			<th class="ascending">' . _('ID Number') . '</th>
		</tr>';
	$k = 0; //row counter to determine background colour
	$RowIndex = 0;
	if (DB_num_rows($result) <> 0) {
		DB_data_seek($result, ($_POST['PageOffset'] - 1) * $_SESSION['DisplayRecordsMax']);
	}
	while (($myrow = DB_fetch_array($result)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {
		if ($k == 1) {
			echo '<tr class="EvenTableRows">';
			$k = 0;
		} else {
			echo '<tr class="OddTableRows">';
			$k = 1;
		}
		echo '<td><input type="submit" name="Select" value="'.$myrow['emp_id'].'" /></td>
				<td>' . $myrow['emp_fname'] .' '. $myrow['emp_lname'] . '</td>
				<td>' . $myrow['id_number'] . '</td>
			</tr>';
		$RowIndex = $RowIndex + 1;
		//end of page full new headings if
	}
	//end of while loop
	echo '</table>';
}
//end if results to show
if (isset($ListPageMax) and $ListPageMax > 1) {
	echo '<p>&nbsp;&nbsp;' . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': </p>';
	echo '<select name="PageOffset">';
	$ListPage = 1;
	while ($ListPage <= $ListPageMax) {
		if ($ListPage == $_POST['PageOffset']) {
			echo '<option value="' . $ListPage . '" selected="selected">' . $ListPage . '</option>';
		} else {
			echo '<option value="' . $ListPage . '">' . $ListPage . '</option>';
		}
		$ListPage++;
	}
	echo '</select>
		<input type="submit" name="Go" value="' . _('Go') . '" />
		<input type="submit" name="Previous" value="' . _('Previous') . '" />
		<input type="submit" name="Next" value="' . _('Next') . '" />';
	echo '<br />';
}
echo '</div>
      </form>';
include ('includes/footer.inc');
?>

		<script type="text/javascript">
			function pop(div) {
				document.getElementById(div).style.display = 'block';
			}
			function hide(div) {
				document.getElementById(div).style.display = 'none';
			}
			//To detect escape button
			document.onkeydown = function(evt) {
				evt = evt || window.event;
				if (evt.keyCode == 27) {
					hide('popDiv');
				}
			};
		</script>
	<style type="text/css">
	input[type='button']{
	 background-color:#34a7e8;
    border:thin outset #1992DA;
    padding:6px 24px;
    vertical-align:middle;
    font-weight:bold;
    color:#FFFFFF;
    cursor: pointer;
    
    -webkit-border-radius:  4px;
    -moz-border-radius:     4px;
    border-radius:          4px;
    -moz-box-shadow:    1px 1px 1px #64BEF1 inset;
	-webkit-box-shadow: 1px 1px 1px #64BEF1 inset;
	box-shadow:         1px 1px 1px #64BEF1 inset;
	}
	</style>