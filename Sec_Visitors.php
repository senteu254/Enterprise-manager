<?php
/* $Id: SelectSupplier.php 6941 2014-10-26 23:18:08Z daintree $*/

include ('includes/session.inc');
$Title = _('Search Visitors');

/* webERP manual links before header.inc */
$ViewTopic= 'Security';
$BookMark = 'SelectVisitor';

include ('includes/header.inc');
include ('includes/SQL_CommonFunctions.inc');
if (!isset($_SESSION['VisitorID'])) {
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/visitor.png" title="' . _('Search') . '" alt="" />' . ' ' . _('Visitor Booking Register') . '</p>';
}
if (isset($_GET['VisitorID'])) {
	$_SESSION['VisitorID']=$_GET['VisitorID'];
}

if (!isset($_POST['PageOffset'])) {
	$_POST['PageOffset'] = 1;
} else {
	if ($_POST['PageOffset'] == 0) {
		$_POST['PageOffset'] = 1;
	}
}
if (isset($_POST['Select'])) { /*User has hit the button selecting a supplier */
	$_SESSION['VisitorID'] = $_POST['Select'];
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
	$VisitorID=$_SESSION['VisitorID'];
// PREVENT DELETES IF DEPENDENT RECORDS IN 'SuppTrans' , PurchOrders, SupplierContacts
if(isset($_POST['gate']) && $_POST['gate']==""){
$Cancel = 1;
prnMsg(_('Please Select the gate you want to check in the visitor.'),'warn');
}

	$sql= "SELECT COUNT(*),MAX(CheckID) FROM visitor_timein WHERE VisitorNo='" . $VisitorID . "' AND GateID='". $_POST['gate'] ."'";
	$result = DB_query($sql);
	$myrow = DB_fetch_row($result);
	if ($myrow[0] > 0) {
	$sql= "SELECT COUNT(*) FROM visitor_timeout WHERE CheckID='". $myrow[1] ."' AND VisitorNo='" . $VisitorID . "' AND GateID='". $_POST['gate'] ."'";
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
								 	'" . $VisitorID . "',
									'". $_POST['remarks'] ."',
									'". $_SESSION['UserID'] ."',
									'" . $_POST['gate'] . "')";
		$result = DB_query($sql);
		prnMsg(_('Visitor record Number') . ' ' . $VisitorID . ' ' . _('has been Checked In'),'success');
	} //end if Delete supplier
}
if (isset($_POST['CheckOut']) AND $_POST['CheckOut'] != '') {

//the link to delete a selected record was clicked instead of the submit button

	$Cancel = 0;
	$VisitorID=$_SESSION['VisitorID'];
// PREVENT DELETES IF DEPENDENT RECORDS IN 'SuppTrans' , PurchOrders, SupplierContacts
if(isset($_POST['gate']) && $_POST['gate']==""){
$Cancel = 1;
prnMsg(_('Please Select the gate you want to check out the visitor.'),'warn');
}
	$sql= "SELECT COUNT(*),MAX(CheckID) FROM visitor_timein WHERE VisitorNo='" . $VisitorID . "' AND GateID='". $_POST['gate'] ."'";
	$result = DB_query($sql);
	$myrow = DB_fetch_row($result);
	if ($myrow[0] > 0) {
	$sql= "SELECT COUNT(*) FROM visitor_timeout WHERE CheckID='". $myrow[1] ."' AND VisitorNo='" . $VisitorID . "' AND GateID='". $_POST['gate'] ."'";
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
								 	'" . $VisitorID . "',
									'". $_POST['remarks'] ."',
									'". $_SESSION['UserID'] ."',
									'" . $_POST['gate'] . "')";
		$result = DB_query($sql);
		prnMsg(_('Visitor record Number') . ' ' . $VisitorID . ' ' . _('has been Checked Out'),'success');
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
		$SQL = "SELECT VisitorNo,
					v_name,
					v_idno,
					v_phoneno,
					v_from,
					host,
					departmentid,
					purpose,
					date
				FROM visitor_register
				ORDER BY v_name";
	} else {
		if (mb_strlen($_POST['Keywords']) > 0) {
			$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
			//insert wildcard characters in spaces
			$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';
			$SQL = "SELECT VisitorNo,
							v_name,
							v_idno,
							v_phoneno,
							v_from,
							host,
							departmentid,
							purpose,
							date
						FROM visitor_register
						WHERE v_name " . LIKE . " '" . $SearchString . "'
						ORDER BY v_name";
		} elseif (mb_strlen($_POST['idno']) > 0) {
			$_POST['idno'] = mb_strtoupper($_POST['idno']);
			$SQL = "SELECT VisitorNo,
							v_name,
							v_idno,
							v_phoneno,
							v_from,
							host,
							departmentid,
							purpose,
							date
						FROM visitor_register
						WHERE v_idno " . LIKE . " '%" . $_POST['idno'] . "%'
						ORDER BY v_idno";
		}
	} //one of keywords or SupplierCode was more than a zero length string
	$result = DB_query($SQL);
	if (DB_num_rows($result) == 1) {
		$myrow = DB_fetch_row($result);
		$SingleVisitorReturned = $myrow[0];
	}
	if (isset($SingleVisitorReturned)) { /*there was only one supplier returned */
 	   $_SESSION['VisitorID'] = $SingleVisitorReturned;
	   unset($_POST['Keywords']);
	   unset($_POST['idno']);
	   unset($_POST['Search']);
        } else {
               unset($_SESSION['VisitorID']);
        }
} //end of if search

if (isset($_SESSION['VisitorID'])) {
	$VisitorName = '';
	$SQL = "SELECT v_name,v_idno,v_phoneno,v_from,date,purpose,emp_id,host,description,appointment_name,emp_cont
			FROM visitor_register
			INNER JOIN departments ON visitor_register.departmentid=departments.departmentid
			LEFT JOIN employee ON CONCAT_WS(' ', employee.emp_fname, employee.emp_lname)= visitor_register.host
			WHERE visitor_register.VisitorNo ='" . $_SESSION['VisitorID'] . "'";
	$SupplierNameResult = DB_query($SQL);
	if (DB_num_rows($SupplierNameResult) == 1) {
		$myrow = DB_fetch_row($SupplierNameResult);
		$VisitorName = $myrow[0];
	}
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/visitor.png" title="' . _('Visitor') . '" alt="" />' . ' ' . _('Visitor') . ' : <b>' . $_SESSION['VisitorID'] . ' - ' . $VisitorName . '</b> ' . _('has been selected') . '.</p>';
	echo '<div class="page_help_text">' . _('Select a menu option to operate using this Visitor.') . '</div>';
	echo '<br />
		<table width="90%" cellpadding="4">
		<tr>
			<th style="width:30%">' . _('Visitor Information') . '</th>
			<th style="width:30%">' . _('Host Information') . '</th>
			<th style="width:40%">' . _('Visitor Maintenance') . '</th>
		</tr>';
	echo '<tr><td valign="top" class="select">'; /* Inquiry Options */
		echo '<table style="background:#FFFFFF">
			<tr height="30px"><th width="100px">ID Number :</th><td width="200px" align="left">'.$myrow[1].'</td></tr>
			<tr height="30px"><th>Phone No.:</th><td align="left">'.$myrow[2].'</td></tr>
			<tr height="30px"><th>Residence :</th><td align="left">'.$myrow[3].'</td></tr>
			<tr height="30px"><th>Date :</th><td align="left">'.ConvertSQLDate($myrow[4]).'</td></tr>
			<tr height="30px"><th>Purpose :</th><td align="left">'.$myrow[5].'</td></tr>
			</table>';
			echo '<a href="' . $RootPath . '/Sec_VisitorRegister.php">' . _('Add a New Visitor') . '</a>
		<br /><a href="' . $RootPath . '/Sec_VisitorRegister.php?VisitorID=' . $_SESSION['VisitorID'] . '">' . _('Modify Or Delete Visitor Details') . '</a>
		<br /><a href="' . $RootPath . '/Sec_VisitorMaterialRegister.php?VisitorID=' . $_SESSION['VisitorID'] . '">' . _('Visitor Material Register') . '</a>
		<br />';
	echo '</td><td valign="top" class="select">'; /* Supplier Transactions */
		echo '<table style="background:#FFFFFF">
			<tr height="30px"><th width="100px">Host Svc No. :</th><td width="200px" align="left">'.$myrow[6].'</td></tr>
			<tr height="30px"><th>Host Name :</th><td align="left">'.$myrow[7].'</td></tr>
			<tr height="30px"><th>Department :</th><td align="left">'.$myrow[8].'</td></tr>
			<tr height="30px"><th>Position :</th><td align="left">'.$myrow[9].'</td></tr>
			<tr height="30px"><th>Phone No. :</th><td align="left">'.$myrow[10].'</td></tr>
			</table>';
	echo '</td><td valign="top" class="select">'; /* Supplier Maintenance */
	$sql = "SELECT gates.GateID,
				gates.description
			FROM gates";
			$result = DB_query($sql);

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
		echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background:#FFFFFF">
			<tr height="30px"><th colspan="3">
			<select name="gate">';
			echo '<option value="">--Please Select Gate--</option>';
			while ($myrow = DB_fetch_array($result)) {
			echo '<option value="'. $myrow[0] .'">'. $myrow[0] .'-'. $myrow[1] .'</option>';
			}
			echo '</select>';
			echo "&nbsp;&nbsp;&nbsp;<input type='button' onclick=pop('popDiv') name='' value='" . _('Submit') . "' /></th>";
			echo '<tr height="30px"><th width="110px">Gate</th><th width="150px">Time In</th><th width="150px">Time Out</th></tr>';
			
			$sql= "SELECT gates.description, visitor_timein.time_in, visitor_timeout.time_out, visitor_timein.sec_officer, visitor_timeout.sec_officer, visitor_timein.remarks, visitor_timeout.remarks FROM visitor_timein INNER JOIN gates ON gates.GateID=visitor_timein.GateID LEFT JOIN visitor_timeout ON visitor_timein.CheckID=visitor_timeout.CheckID WHERE visitor_timein.VisitorNo='" . $_SESSION['VisitorID'] . "' GROUP BY visitor_timein.CheckID, visitor_timein.GateID ORDER BY visitor_timein.time_in DESC";
			$result = DB_query($sql);
			$i=0;
			while($myro = DB_fetch_row($result)){
			echo '<tr height="30px"><td><div class="grabPromo'.$i.'">'.$myro[0].'</div></td><td>'.$myro[1].'</td><td>'.($myro[2]==""? '<strong>Pending CheckOut</strong>': ''.$myro[2].'').'</td></tr>';
			echo '<tr  class="slideDown'.$i.'"><td><strong>Sec Officer:</strong> </td><td>'.$myro[3].'</td><td>'.$myro[4].'</td></tr>';
			echo '<tr class="slideDown'.$i.'"><td><strong>Remarks :</strong> </td><td>'.$myro[5].'</td><td>'.$myro[6].'</td></tr>';
			?>
			<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
			<script type="text/javascript">
			$('.grabPromo<?php echo $i; ?>').click(function(e){
				$('.slideDown<?php echo $i; ?>').slideToggle("slow");
			});
			</script>
			<style type="text/css">
			.slideDown<?php echo $i; ?> { background:#ace; display: none; }
			.grabPromo<?php echo $i; ?> { cursor:pointer; color:#0066CC; }
			</style>
			<?php
			$i++;
			}
			echo '</table>';
				echo '<div id="popDiv" style="z-index: 999;
									width: 100%;
									height: 100%;
									top: 0;
									left: 0;
									display: none;
									position: absolute;				
									background-color: #fff;
									background-color: rgba(255,255,255,0.7);
									filter: alpha(opacity = 50);">';
	
	
	echo '<table style="width: 300px;
						background:#FFFFFF;
						height: 150px;
						position: absolute;
						color: #000000;
						/* To align popup window at the center of screen*/
						top: 35%;
						left: 73%;
						margin-top: -100px;
						margin-left: -150px;">
			<tr>';
				echo "<th>" . _('Please Give Us Any Remarks') . ": </th><th><a href=# onclick=hide('popDiv')><div align='right'><img src='".$RootPath."/css/close.png'/></div></th></tr>";
			echo '<tr>
				<td colspan="2">
				<textarea name="remarks" cols="31" rows="2"></textarea>
				</td>			
			</tr>
			<tr>
				<td colspan="2"><input type="submit" name="CheckIn" value="' . _('CheckIn') . '" /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" style="background:#FF3300;" name="CheckOut" value="' . _('CheckOut') . '" />';
	echo '</td>
			</tr>
			</table>';
	
	echo '</div>';
	echo '</from>';
		echo '</td>
		</tr>
		</table>';
} else {
	// Visitor is not selected yet
	echo '<br />';
	echo '<table width="90%" cellpadding="4">
		<tr>
			<th style="width:33%">' . _('Visitor Information') . '</th>
			<th style="width:33%">' . _('Host Information') . '</th>
			<th style="width:33%">' . _('Visitor Maintenance') . '</th>
		</tr>';
	echo '<tr>
			<td valign="top" class="select"></td>
			<td valign="top" class="select"></td>
			<td valign="top" class="select">'; /* Visitor Maintenance */
	echo '<a href="' . $RootPath . '/Sec_VisitorRegister.php">' . _('Add a New Visitor') . '</a><br />';
	echo '</td>
		</tr>
		</table>';
}
echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/magnifier.png" title="' . _('Search') . '" alt="" />' . ' ' . _('Search for Visitors') . '</p>
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
		<td>' . _('Enter a partial ID No.') . ':</td>
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
	  		<th class="ascending">' . _('Visitor No') . '</th>
			<th class="ascending">' . _('Visitor Name') . '</th>
			<th class="ascending">' . _('ID Number') . '</th>
			<th class="ascending">' . _('Phone No.') . '</th>
			<th class="ascending">' . _('Place of Residence') . '</th>
			<th class="ascending">' . _('Host') . '</th>
			<th class="ascending">' . _('Date') . '</th>
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
		echo '<td><input type="submit" name="Select" value="'.$myrow['VisitorNo'].'" /></td>
				<td>' . $myrow['v_name'] . '</td>
				<td>' . $myrow['v_idno'] . '</td>
				<td>' . $myrow['v_phoneno'] . '</td>
				<td>' . $myrow['v_from'] . '</td>
				<td>' . $myrow['host'] . '</td>
				<td>' . $myrow['date'] . '</td>
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