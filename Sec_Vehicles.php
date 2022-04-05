<?php
/* $Id: SelectSupplier.php 6941 2014-10-26 23:18:08Z daintree $*/

include ('includes/session.inc');
$Title = _('Search Vehicles');

/* webERP manual links before header.inc */
$ViewTopic= 'Security';
$BookMark = 'SelectVehicle';

include ('includes/header.inc');
include ('includes/SQL_CommonFunctions.inc');
if (!isset($_SESSION['VehicleID'])) {
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/visitor.png" title="' . _('Search') . '" alt="" />' . ' ' . _('Vehicle Booking Register') . '</p>';
}
if (isset($_GET['VehicleID'])) {
	$_SESSION['VehicleID']=$_GET['VehicleID'];
}

if (!isset($_POST['PageOffset'])) {
	$_POST['PageOffset'] = 1;
} else {
	if ($_POST['PageOffset'] == 0) {
		$_POST['PageOffset'] = 1;
	}
}
if (isset($_POST['Select'])) { /*User has hit the button selecting a supplier */
	$_SESSION['VehicleID'] = $_POST['Select'];
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
	$VehicleID=$_SESSION['VehicleID'];
// PREVENT DELETES IF DEPENDENT RECORDS IN 'SuppTrans' , PurchOrders, SupplierContacts
if(isset($_POST['gate']) && $_POST['gate']==""){
$Cancel = 1;
prnMsg(_('Please Select the gate you want to check in the Vehicle.'),'warn');
}

	$sql= "SELECT COUNT(*),MAX(CheckID) FROM vehicle_timein WHERE VehicleNo='" . $VehicleID . "' AND GateID='". $_POST['gate'] ."'";
	$result = DB_query($sql);
	$myrow = DB_fetch_row($result);
	if ($myrow[0] > 0) {
	$sql= "SELECT COUNT(*) FROM vehicle_timeout WHERE CheckID='". $myrow[1] ."' AND VehicleNo='" . $VehicleID . "' AND GateID='". $_POST['gate'] ."'";
	$result = DB_query($sql);
	$row = DB_fetch_row($result);
	if ($row[0] > 0) {
	$Cancel = 0;
	}else{
		$Cancel = 1;
		prnMsg(_('Cannot Check In this Vehicle because he/she has already been checked in through this gate.'),'warn');
	}
	}
	if ($Cancel == 0) {
	$checkid = GetNextTransNo(55, $db);
		$sql = "INSERT INTO vehicle_timein (CheckID,
										VehicleNo,
										remarks,
										sec_officer,
										GateID)
								 VALUES ('" . $checkid . "',
								 	'" . $VehicleID . "',
									'". $_POST['remarks'] ."',
									'". $_SESSION['UserID'] ."',
									'" . $_POST['gate'] . "')";
		$result = DB_query($sql);
		prnMsg(_('Vehicle record Number') . ' ' . $VehicleID . ' ' . _('has been Checked In'),'success');
	} //end if Delete supplier
}
if (isset($_POST['CheckOut']) AND $_POST['CheckOut'] != '') {

//the link to delete a selected record was clicked instead of the submit button

	$Cancel = 0;
	$VehicleID=$_SESSION['VehicleID'];
// PREVENT DELETES IF DEPENDENT RECORDS IN 'SuppTrans' , PurchOrders, SupplierContacts
if(isset($_POST['gate']) && $_POST['gate']==""){
$Cancel = 1;
prnMsg(_('Please Select the gate you want to check out the Vehicle.'),'warn');
}
	$sql= "SELECT COUNT(*),MAX(CheckID) FROM vehicle_timein WHERE VehicleNo='" . $VehicleID . "' AND GateID='". $_POST['gate'] ."'";
	$result = DB_query($sql);
	$myrow = DB_fetch_row($result);
	if ($myrow[0] > 0) {
	$sql= "SELECT COUNT(*) FROM vehicle_timeout WHERE CheckID='". $myrow[1] ."' AND VehicleNo='" . $VehicleID . "' AND GateID='". $_POST['gate'] ."'";
	$result = DB_query($sql);
	$row = DB_fetch_row($result);
	if ($row[0] > 0) {
		$Cancel = 1;
		prnMsg(_('Cannot Check Out this Vehicle because he/she has already been checked Out through this gate.'),'warn');
	}else{
	$Cancel = 0;
	$checkid = $myrow[1];
	}
	}else{
		$Cancel = 1;
		prnMsg(_('Cannot Check Out this Vehicle because he/she has not been checked in through this gate.'),'warn');
	}
	if ($Cancel == 0) {
		$sql = "INSERT INTO vehicle_timeout (CheckID,
										VehicleNo,
										remarks,
										sec_officer,
										GateID)
								 VALUES ('" . $checkid . "',
								 	'" . $VehicleID . "',
									'". $_POST['remarks'] ."',
									'". $_SESSION['UserID'] ."',
									'" . $_POST['gate'] . "')";
		$result = DB_query($sql);
		prnMsg(_('Vehicle record Number') . ' ' . $VehicleID . ' ' . _('has been Checked Out'),'success');
	} //end if Delete supplier
}

if (isset($_POST['Search'])
	OR isset($_POST['Go'])
	OR isset($_POST['Next'])
	OR isset($_POST['Previous'])) {

	if (mb_strlen($_POST['Keywords']) > 0 AND mb_strlen($_POST['idno']) > 0) {
		prnMsg( _('Vehicle Reg Number keywords have been used in preference to the Driver ID Number extract entered'), 'info' );
	}
	if ($_POST['Keywords'] == '' AND $_POST['idno'] == '') {
		$SQL = "SELECT VehicleNo,
					RegNo,
					Make,
					DriverName,
					IdNo,
					Org,
					Destination,
					Purpose,
					Date,
					phoneno
				FROM vehicle_register
				ORDER BY RegNo";
	} else {
		if (mb_strlen($_POST['Keywords']) > 0) {
			$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
			//insert wildcard characters in spaces
			$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';
			$SQL = "SELECT VehicleNo,
							RegNo,
							Make,
							DriverName,
							IdNo,
							Org,
							Destination,
							Purpose,
							Date,
							phoneno
						FROM vehicle_register
						WHERE RegNo " . LIKE . " '" . $SearchString . "'
						ORDER BY RegNo";
		} elseif (mb_strlen($_POST['idno']) > 0) {
			$_POST['idno'] = mb_strtoupper($_POST['idno']);
			$SQL = "SELECT VehicleNo,
							RegNo,
							Make,
							DriverName,
							IdNo,
							Org,
							Destination,
							Purpose,
							Date,
							phoneno
						FROM vehicle_register
						WHERE IdNo " . LIKE . " '%" . $_POST['idno'] . "%'
						ORDER BY IdNo";
		}
	} //one of keywords or SupplierCode was more than a zero length string
	$result = DB_query($SQL);
	if (DB_num_rows($result) == 1) {
		$myrow = DB_fetch_row($result);
		$SingleVisitorReturned = $myrow[0];
	}
	if (isset($SingleVisitorReturned)) { /*there was only one supplier returned */
 	   $_SESSION['VehicleID'] = $SingleVisitorReturned;
	   unset($_POST['Keywords']);
	   unset($_POST['idno']);
	   unset($_POST['Search']);
        } else {
               unset($_SESSION['VehicleID']);
        }
} //end of if search

if (isset($_SESSION['VehicleID'])) {
	$VehicleName = '';
	$SQL = "SELECT RegNo,Make,Org,DriverName,IdNo,Destination,Purpose,Date,description,phoneno
			FROM vehicle_register
			INNER JOIN departments ON vehicle_register.departmentid=departments.departmentid
			WHERE VehicleNo ='" . $_SESSION['VehicleID'] . "'";
	$SupplierNameResult = DB_query($SQL);
	if (DB_num_rows($SupplierNameResult) == 1) {
		$myrow = DB_fetch_row($SupplierNameResult);
		$VehicleName = $myrow[0];
	}
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/visitor.png" title="' . _('Vehicle') . '" alt="" />' . ' ' . _('Vehicle') . ' : <b>' . $_SESSION['VehicleID'] . ' - ' . $VehicleName . '</b> ' . _('has been selected') . '.</p>';
	echo '<div class="page_help_text">' . _('Select a menu option to operate using this Vehicle.') . '</div>';
	echo '<br />
		<table width="90%" cellpadding="4">
		<tr>
			<th style="width:50%">' . _('Vehicle Information') . '</th>
			<th style="width:50%">' . _('Vehicle Maintenance') . '</th>
		</tr>';
	echo '<tr><td valign="top" class="select">'; /* Inquiry Options */
		echo '<table  style="background:#FFFFFF">
			<tr height="30px"><th width="150px">Make :</th><td width="300px" align="left">'.$myrow[1].'</td></tr>
			<tr height="30px"><th>Organization :</th><td align="left">'.$myrow[2].'</td></tr>
			<tr height="30px"><th>Driver Name :</th><td align="left">'.$myrow[3].'</td></tr>
			<tr height="30px"><th>ID Number :</th><td align="left">'.$myrow[4].'</td></tr>
			<tr height="30px"><th>Phone No :</th><td align="left">'.$myrow[9].'</td></tr>
			<tr height="30px"><th>Destination :</th><td align="left">'.$myrow[5].'</td></tr>
			<tr height="30px"><th>Department :</th><td align="left">'.$myrow[8].'</td></tr>
			<tr height="30px"><th>Date :</th><td align="left">'.ConvertSQLDate($myrow[7]).'</td></tr>
			<tr height="30px"><th>Purpose :</th><td align="left">'.$myrow[6].'</td></tr>
			</table>';
			echo '<a href="' . $RootPath . '/Sec_VehicleRegister.php">' . _('Add a New Vehicle') . '</a>
		<br /><a href="' . $RootPath . '/Sec_VehicleRegister.php?VehicleID=' . $_SESSION['VehicleID'] . '">' . _('Modify Or Delete Vehicle Details') . '</a>
		<br />';
	echo '</td><td valign="top" class="select">'; /* Supplier Maintenance */
	$sql = "SELECT gates.GateID,
				gates.description
			FROM gates";
			$result = DB_query($sql);

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
		echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background:#FFFFFF">
			<tr height="30px"><th colspan="3"><div align="left">
			<select name="gate">';
			echo '<option value="">--Please Select Gate--</option>';
			while ($myrow = DB_fetch_array($result)) {
			echo '<option value="'. $myrow[0] .'">'. $myrow[0] .'-'. $myrow[1] .'</option>';
			}
			echo '</select>';
			echo "&nbsp;&nbsp;&nbsp;<input type='button' onclick=pop('popDiv') name='' value='" . _('Submit') . "' /></div></th>";
			echo '<tr height="30px"><th width="110px">Gate</th><th width="175px">Time In</th><th width="175px">Time Out</th></tr>';
			
			$sql= "SELECT gates.description, vehicle_timein.time_in, vehicle_timeout.time_out, vehicle_timein.sec_officer, vehicle_timeout.sec_officer, vehicle_timein.remarks, vehicle_timeout.remarks FROM vehicle_timein INNER JOIN gates ON gates.GateID=vehicle_timein.GateID LEFT JOIN vehicle_timeout ON vehicle_timein.CheckID=vehicle_timeout.CheckID WHERE vehicle_timein.VehicleNo='" . $_SESSION['VehicleID'] . "' GROUP BY vehicle_timein.CheckID, vehicle_timein.GateID ORDER BY vehicle_timein.time_in DESC";
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
			<th style="width:33%">' . _('Vehicles Information') . '</th>
			<th style="width:33%">' . _('Owner Information') . '</th>
			<th style="width:33%">' . _('Vehicles Maintenance') . '</th>
		</tr>';
	echo '<tr>
			<td valign="top" class="select"></td>
			<td valign="top" class="select"></td>
			<td valign="top" class="select">'; /* Visitor Maintenance */
	echo '<a href="' . $RootPath . '/Sec_VehicleRegister.php">' . _('Add a New Vehicle') . '</a><br />';
	echo '</td>
		</tr>
		</table>';
}
echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/magnifier.png" title="' . _('Search') . '" alt="" />' . ' ' . _('Search for Vehicle') . '</p>
	<table cellpadding="3" class="selection">
	<tr>
		<td>' . _('Enter a partial Reg No') . ':</td>
		<td>';
if (isset($_POST['Keywords'])) {
	echo '<input type="text" name="Keywords" value="' . $_POST['Keywords'] . '" size="20" maxlength="25" />';
} else {
	echo '<input type="text" name="Keywords" size="20" maxlength="25" />';
}
echo '</td>
		<td><b>' . _('OR') . '</b></td>
		<td>' . _('Enter a partial Driver ID No.') . ':</td>
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
	  		<th class="ascending">' . _('Vehicle No') . '</th>
			<th class="ascending">' . _('Reg Number') . '</th>
			<th class="ascending">' . _('Make') . '</th>
			<th class="ascending">' . _('Driver Name') . '</th>
			<th class="ascending">' . _('ID Number') . '</th>
			<th class="ascending">' . _('Organization') . '</th>
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
		echo '<td><input type="submit" name="Select" value="'.$myrow['VehicleNo'].'" /></td>
				<td>' . $myrow['RegNo'] . '</td>
				<td>' . $myrow['Make'] . '</td>
				<td>' . $myrow['DriverName'] . '</td>
				<td>' . $myrow['IdNo'] . '</td>
				<td>' . $myrow['Org'] . '</td>
				<td>' . $myrow['Date'] . '</td>
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