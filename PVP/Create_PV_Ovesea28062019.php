<link rel="stylesheet" href="PVP/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="PVP/iCheck/flat/blue.css">

<?php

if (empty($_GET['identifier'])) {
	$identifier = date('U');
} else {
	$identifier = $_GET['identifier'];
}
if (isset($_GET['SelectedUser'])){
	$SelectedUser = $_GET['SelectedUser'];
} elseif (isset($_POST['SelectedUser'])){
	$SelectedUser = $_POST['SelectedUser'];
}
echo '<form action="'. htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Application=PVM&Ref=default&Link=Initiate_PV_Ovesea" method="post">';


if (isset($_POST['Submit'])) {
$InputError = 0;
$SQL = "SELECT COUNT(*) FROM  payment_voucher where authorityref='" . $_POST['arefno'] . "'";
				 $result=DB_query($SQL);
  $myro=DB_fetch_row($result);
if($myro[0] >0 && !isset($SelectedUser)){
$InputError = 1;
prnMsg( _('Can not Allow Duplicate Entry'), 'error' );
}
if((!isset($_POST['Username']) or $_POST['Username'] =="") && (!isset($_POST['supplierid']) or $_POST['supplierid'] =="")){
$InputError = 1;
prnMsg( _('Payee Name can not be Empty'), 'error' );
}
if(isset($_POST['user']) && $_POST['user'] !=""){
$_POST['supplier'] = $_POST['Username'];
$_POST['supplierid'] = "";
}else{

$SQL = "SELECT supplierid,
					suppname			
				    FROM  suppliers
				    where supplierid='".$_POST['supplierid']."'";
				 $result=DB_query($SQL);
  $myrowb=DB_fetch_array($result);
  $_POST['supplier']=$myrowb['suppname'];
  
  }

if (isset($SelectedUser)) {
$sql = "UPDATE payment_voucher SET authorityref='" . $_POST['arefno'] . "',
                        votehead='" . $_POST['votehead'] ."',
						label='" . $_POST['label'] ."',
						payeename='" . $_POST['supplier'] ."',
						supplierid='" . $_POST['supplierid'] ."',
						particulars='" . serialize($_POST['particulars']) ."',
						lpo_no='" . serialize($_POST['lpono']) . "',
						invoice_no='" . serialize($_POST['invoiceno']) . "',
						amount='" . serialize($_POST['amnt']) . "',
						total='" . number_format(array_sum($_POST['amnt']),2) . "'
					WHERE voucherid = '". $SelectedUser . "'";

		prnMsg( _('The selected payment voucher has been updated'), 'success' );
		
	} elseif ($InputError !=1) {
	
	//initialise no input errors assumed initially before we test
	$VoucherID = GetNextTransNo(201,$db);
   if(isset($_POST['draft']) && $_POST['draft']==1){
   
			$process_level=0;
			}else{
			$process_level=process_level+1;
			}
		$sql88 = "INSERT INTO payment_voucher (voucherid,
		                votehead,
						authorityref,
						datereq,
						label,
						payeename,
						supplierid,
						particulars,
						lpo_no,
						invoice_no,
						amount,
						total,
						process_level,
						department,
						tax)
					VALUES ('" . $VoucherID . "',
					    '" . $_POST['votehead'] ."',
						'" . $_POST['arefno'] ."',
						'" . $_POST['reqdate'] ."',
						'" . $_POST['label'] ."',
						'" . $_POST['supplier'] ."',
						'" . $_POST['supplierid'] ."',
						'" . serialize($_POST['particulars']) . "',
						'" . serialize($_POST['lpono']) . "',
						'" . serialize($_POST['invoiceno']) ."',
						'" . serialize($_POST['amnt']) ."',
						'" . number_format(array_sum($_POST['amnt']),2) . "',
						'".$process_level."',
						'" . $_POST['departmentid'] ."',
						'".$_POST['tax']."')";
		echo  '<div class="alert alert-success alert-dismissible"><h4><i class="icon fa fa-check"></i> Success</h4>'._('A new payment voucher record has been inserted').'</div>';
		
	}

	if ($InputError!=1){
		//run the SQL from either of the above possibilites
		$ErrMsg = _('The user alterations could not be processed because');
		$DbgMsg = _('The SQL that was used to update the user and failed was');
		$result = DB_query($sql88,$ErrMsg,$DbgMsg);

		unset($_POST['voucherid']);
		unset($_POST['votehead']);
		unset($_POST['arefno']);
		unset($_POST['label']);
		unset($_POST['supplierid']);
		unset($_POST['particulars']);
		unset($_POST['lpono']);
		unset($_POST['invoiceno']);
		unset($_POST['amnt']);
		unset($_POST['tot']);
		unset($SelectedUser);
	}
	
}elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button		

			$sql="DELETE FROM payment_voucher WHERE voucherid='" . $SelectedUser . "'";
			$ErrMsg = _('The Voucher could not be deleted because');
			$result = DB_query($sql,$ErrMsg);
			prnMsg(_('Voucher Deleted'),'info');

		unset($SelectedUser);
	}
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	#########################################################################################################################################################
 if(isset($_POST['supplierid'])){
 $SQL = "SELECT  supplierid, suppname FROM suppliers
				WHERE supplierid='".$_POST['supplierid']."'";

	$ErrMsg = _('The searched supplier records requested cannot be retrieved because');
	$result = DB_query($SQL,$ErrMsg);
	$rowb=DB_fetch_row($result);
	
$_POST['supplierid'] = $rowb[0];
$_POST['customer'] = $rowb[1];
 }
 if(isset($_POST['SearchSupplier'])){
	if (!isset($_POST['PageOffset'])) {
	$_POST['PageOffset'] = 1;
} else {
	if ($_POST['PageOffset'] == 0) {
		$_POST['PageOffset'] = 1;
	}
}
if (isset($_POST['SearchSupplier'])
	OR isset($_POST['Go'])
	OR isset($_POST['Next'])
	OR isset($_POST['Previous'])) {

	if (mb_strlen($_POST['Keywords']) > 0 AND mb_strlen($_POST['SupplierCode']) > 0) {
		prnMsg( _('Supplier name keywords have been used in preference to the Supplier code extract entered'), 'info' );
	}
	if ($_POST['Keywords'] == '' AND $_POST['SupplierCode'] == '') {
		$SQLi = "SELECT supplierid,
					suppname,
					currcode,
					address1,
					address2,
					address3,
					address4,
					telephone,
					email,
					url
				FROM suppliers
				ORDER BY suppname";
	} else {
		if (mb_strlen($_POST['Keywords']) > 0) {
			$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
			//insert wildcard characters in spaces
			$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';
			$SQLi = "SELECT supplierid,
							suppname,
							currcode,
							address1,
							address2,
							address3,
							address4,
							telephone,
							email,
							url
						FROM suppliers
						WHERE suppname " . LIKE . " '" . $SearchString . "'
						ORDER BY suppname";
		} elseif (mb_strlen($_POST['SupplierCode']) > 0) {
			$_POST['SupplierCode'] = mb_strtoupper($_POST['SupplierCode']);
			$SQLi = "SELECT supplierid,
							suppname,
							currcode,
							address1,
							address2,
							address3,
							address4,
							telephone,
							email,
							url
						FROM suppliers
						WHERE supplierid " . LIKE . " '%" . $_POST['SupplierCode'] . "%'
						ORDER BY supplierid";
		}
	} //one of keywords or SupplierCode was more than a zero length string
	$result = DB_query($SQLi);
	if (DB_num_rows($result) == 1) {
		$row = DB_fetch_row($result);
		$SingleSupplierReturned = $row[0];
	}
	if (isset($SingleSupplierReturned)) { /*there was only one supplier returned */
 	   $_SESSION['SupplierID'] = $SingleSupplierReturned;
	   unset($_POST['Keywords']);
	   unset($_POST['SupplierCode']);
	   unset($_POST['Search']);
        } else {
         unset($_SESSION['SupplierID']);
        }
}
  echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Select Supplier for Commitment') . '" alt="" />' . '  ' .    _('Select Supplier for Commitment'). '</p>';
  echo'<table>';
	echo'<table cellpadding="3" class="selection">
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
		<td>' . _('Enter a partial Code') . ':</td>
		<td>';
if (isset($_POST['SupplierCode'])) {
	echo '<input type="text" autofocus="autofocus" name="SupplierCode" value="' . $_POST['SupplierCode'] . '" size="15" maxlength="18" />';
} else {
	echo '<input type="text" autofocus="autofocus" name="SupplierCode" size="15" maxlength="18" />';
}
echo '</td></tr>
		</table>
		<br /><div class="centre"><input type="submit" name="SearchSupplier" value="' . _('Search Now') . '" /></div>';
if (isset($_POST['SearchSupplier'])) {
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
		echo '<div class="centre"><p>&nbsp;&nbsp;' . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': </p>';
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
		echo'</div><br>';
		echo '</select>
		<div class="centre">
			<input type="submit" name="Go" value="' . _('Go') . '" />
			<input type="submit" name="Previous" value="' . _('Previous') . '" />
			<input type="submit" name="Next" value="' . _('Next') . '" />';
		echo '<br />
		</div>';
	}
	//echo '<input type="hidden" name="Search" value="' . _('Search Now') . '" />';
	echo '<br />
		<br />
		<br />';
 echo' </table>';
	echo'<table cellpadding="2">';
	echo '<tr>
	  		<th class="ascending">' . _('Code') . '</th>
			<th class="ascending">' .'Supplier'. '</th>
			<th class="ascending">' .'Address'. '</th>
			<th class="ascending">' .'Telephone No'. '</th>
			<th class="ascending">' .'Email'. '</th>
			<th></th>
		   </tr>';
	   $i=0;
  
 $k = 0; //row counter to determine background colour
	$RowIndex = 0;
	if (DB_num_rows($result) <> 0) {
		DB_data_seek($result, ($_POST['PageOffset'] - 1) * $_SESSION['DisplayRecordsMax']);
	}
	while (($row = DB_fetch_array($result)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {
		if ($k == 1) {
			echo '<tr class="EvenTableRows">';
			$k = 0;
		} else {
			echo '<tr class="OddTableRows">';
			$k = 1;
		}
  echo'<td><input name="SupplierSelected" type="submit" value="'. $row['supplierid'] .'" /></td>
  <td>'.$row['suppname'] .'</td>
  <td>'. $row['address1'] .'</td>
  <td>'. $row['telephone'] .'</td>
  <td>'. $row['email'] .'</td>
	<input name="'. $row['supplierid'] .'SuppSelected" type="hidden" value="'. $row['suppname'] .'" />
  </tr>';
  }
  }
  echo '</table>';
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
	<div class="centre">
		<input type="submit" name="Go" value="' . _('Go') . '" />
		<input type="submit" name="Previous" value="' . _('Previous') . '" />
		<input type="submit" name="Next" value="' . _('Next') . '" /></div>';
	echo '<br />';
}
include('includes/footer.inc');
  exit;
	 
	
	 }
	 echo'</form>';
	

######################################################################################
if (isset($_GET['view'])) {
	//editing an existing User

	$sql = "SELECT voucherid,
	                votehead,
					authorityref,
					datereq,
					label,
					payeename,
					particulars,
					lpo_no,
					invoice_no,
					amount,
					total,
					process_level,
					comment
		FROM payment_voucher
		WHERE voucherid='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['voucherid'] = $myrow['voucherid'];
	$_POST['votehead'] = $myrow['votehead'];
	$_POST['arefno'] = $myrow['authorityref'];
	$_POST['datereq'] = $myrow['datereq'];
	$_POST['label'] = $myrow['label'];
	$_POST['supplierid']	= $myrow['supplierid'];
	$_POST['name']	= $myrow['payeename'];
	$pat  = unserialize($myrow['particulars']);
	$lpono = unserialize($myrow['lpo_no']);
	$invoiceno = unserialize($myrow['invoice_no']);
	$amount = unserialize($myrow['amount']);
	$_POST['total'] = $myrow['total'];
	$_POST['reason'] = $myrow['comment'];
	
echo '<a href="' . $RootPath . '/index.php?Application=PVM&Ref=default&Link=Initiate_PV_Ovesea">' . _('Back to Payment Voucher') . '</a>';
echo'<br>';
echo'<br>';
echo '<table style="background:none repeat scroll 0% 0% #F1F1F1; border:none; box-shadow:none;">
      <tr><td>';
echo '<table align="left" style="width:600%">
      <tr><td>';

	echo '<table class="selection">
			<tr>
				<td>' . _('Voucher ID') . ':</td>
				<th>' . $_POST['voucherid'] . '</th>
			</tr>
			<tr>
				<td>' . _('Vote Head') . ':</td>
				<th>' . $_POST['votehead'] . '</th>
			</tr>';


	echo '<td>' . _('Book  No.') . '</td>
			<td>'.$_POST['arefno'].'</td>
		</tr>
		<td>' . _('Date PV Raised') . '</td>
			<td>'.$_POST['datereq'].'</td>
		</tr>
		<td>' . _('Label') . '</td>
			<td>'.$_POST['label'].'</td>
		</tr>
		<tr>
		<td>' .  _('Payee Name') . '</td>
		<td>'.$_POST['name'] .'</td>
		<td>';

echo '</td>
	</tr>
	</table>

	<br />';

echo '</div>';

echo '<table id="dataTable" class="selection">';
echo '<tr>
		<th>' .  _('Particulars')  . '</th>
		<th>' .  _('LPO No'). '</th>
		<th>' .  _('Invoice No'). '</th>
		<th>' .  _('Amount'). '</th>
	</tr>';
	
for($i=0;$i<=count($lpono);$i++)
{
	$particulars = $pat[$i];
	$lpo = $lpono[$i];
	$invoice = $invoiceno[$i];
	$amnt = $amount[$i];

	echo '<tr>
			
			<td>'.$particulars.'</td>
			<td>'.$lpo.'</td>
			<td>'.$invoice.'</td>
			<td>'.$amnt.'</td>
		 
		 </tr>';
		}
		echo '<tr align="right"><td></td><td></td><td>' .  _('Total Amount'). '</td><th>' .$_POST['total'].'</th></tr>';

echo '</table>';
include('includes/Level_Tracking.php');
	echo '</td>	</tr>';
	if($_POST['reason'] !=''){
	echo '<tr><th>' .  _('Reason for Decline'). '</th></tr>';
	echo '<tr><td><textarea style="font-weight:bold" disabled="true" cols="35" rows="2">' .$_POST['reason'].'</textarea></td></tr>';
	}else{
	echo '';
	}
	echo '</table>';
	echo '</td>
	<td valign="top" class="status">
	<div style="background:url(css/status.png) left top no-repeat; height:243px; width:220px;">
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label.' &nbsp;Bills/Accountant</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label2.' &nbsp;AIE Holder</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label3.' &nbsp;Examination</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label4.' &nbsp;Internal Audit</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label5.' &nbsp;VBC Certificate</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label6.' &nbsp;Authorization</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label7.' &nbsp;Payment</div>
	
	
	</div>
	</td>
	</tr>
	</table>';
} else{
######################################################################################
if (!isset($SelectedUser)) {}
############################################################################################
echo '<form method="post" name="form" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=Initiate_PV_Ovesea" id="form">
	<div>
	<br />
	<table class="table" style="width: 550px;>
		<tr>';
		
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<input type="hidden" name="voucherid" value="' . $voucherid . '" />';

#############################################################################################
if (isset($SelectedUser)) {
	//editing an existing User

	$sql = "SELECT voucherid,
					authorityref,
					datereq,
					label,
					payeename,
					particulars,
					lpo_no,
					invoice_no,
					amount,
					total,
					supplierid
		FROM payment_voucher
		WHERE voucherid='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['voucherid'] = $myrow['voucherid'];
	$_POST['arefno'] = $myrow['authorityref'];
	$_POST['datereq'] = $myrow['datereq'];
	$_POST['label'] = $myrow['label'];
	$_POST['supplierid']	= $myrow['payeename'];
	$pat  = unserialize($myrow['particulars']);
	$lpono = unserialize($myrow['lpo_no']);
	$invoiceno = unserialize($myrow['invoice_no']);
	$amount = unserialize($myrow['amount']);
	$_POST['total'] = $myrow['total'];
	$_POST['supid']	= $myrow['supplierid'];
	
	echo '<input type="hidden" name="SelectedUser" value="' . $SelectedUser . '" />';
	echo '<input type="hidden" name="UserID" value="' . $_POST['voucherid'] . '" />';
#######################################################################################################3
	
###########################################################################################################
	echo '<table style="width:950px;background-color: white;" >
	<div">
			<tr>
				<td>' . _('Voucher ID') . ':</td>
				<td>' . $_POST['voucherid'] . '</td>
			</tr>';
echo '<a href="' . $RootPath . '/index.php?Application=PVM&Ref=default&Link=Initiate_PV_Ovesea">' . _('Back to Payment Voucher2') . '</a>';
echo'<br>';
}
#############################################################################################
echo'<tr>
<td>' .  _('Department') . '</td>';
	echo '<td>
	<select required="required" name="departmentid">';
     $SQL2 = "SELECT * FROM  departments
			  WHERE departmentid=7 OR departmentid=17";
				 $result2=DB_query($SQL2);
  echo '<option selected="selected" value="">--Select department--</option>';
  while ($myrow5=DB_fetch_array($result2)){
	if (isset($_POST['departmentid']) &&  $myrow5['departmentid']==$_POST['departmentid']){
		echo '<option selected="selected" value="'. $myrow5['departmentid'] . '">' . $myrow5['description'] . '</option>';
	} else {
		echo '<option value="'. $myrow5['departmentid'] . '">' . $myrow5['description'] . '</option>';
	}
}

  echo '</select></div>
  </td>';	
	echo'</tr>';
	echo'<tr>
<td>' .  _('Vote Head') . '</td>';
	echo '<td>
	<select required="required" name="votehead">';
    $sqli = "SELECT a.Votecode,
					a.Votehead,
					a.Vbook,
					b.Financial_Year,
					b.votecode,
					b.allocated_Fund,
					b.suppliementary	
				FROM voteheadmaintenance a
				INNER JOIN funds_allocations b ON a.Votecode=b.votecode
				WHERE b.Financial_Year='".Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],+0))."'";
	$resulti = DB_query($sqli);
  echo '<option selected="selected" value="">--Select Votehead--</option>';
  while ($myrow6=DB_fetch_array($resulti)){
	if (isset($_POST['Votecode' . $identifier]) &&  $myrow6['Votecode']==$_POST['Votecode']){
		echo '<option selected="selected" value="'. $myrow6['Votecode'] . '">' . $myrow6['Votehead'] . '</option>';
	} else {
		echo '<option value="'. $myrow6['Votecode'] . '">' . $myrow6['Votehead'] . '</option>';
	}
}

  echo '</select></div>
  </td>';	
	echo'</tr>';
	
##############################################################################################
	echo '<td>' . _('Book No.') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" " name="arefno" value="'.$_POST['arefno'].'" /></td>
		</tr>
		<td>' . _('Date PV Raised') . '</td>
			<td><input type="text" required="required" autofocus="autofocus" class="date" alt="' .  Date($_SESSION['DefaultDateFormat']) . '" name="reqdate" size="11" value="' . Date($_SESSION['DefaultDateFormat']) . '" /></td>
		</tr>
		<td>' . _('Label') . '</td>';
		$label = array('Normal','Urgent','Not Urgent','Emergercy');
		echo '<td><select required="required" name="label">';
	foreach($label as $rob){
		if($rob == $_POST['label']){
		echo'<option selected="selected" value="'.$rob.'">'.$rob.'</option>';
		}else{
	  echo'<option value="'.$rob.'">'.$rob.'</option>';
	  }
	  }
	echo '</select></td>';
	
	echo'<tr><td>Witholding Tax(6%)</td>
	<td>
	<input type="checkbox" id="1" name="tax" value="1">
	</td></tr>';
	if (isset($SelectedUser)){
	echo'<tr>';
	echo'<td>' .  _('Payee Name') . '</td>';
	if(isset($_POST['supid']) && $_POST['supid'] !=""){
	echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';
		echo '<td>';
		?>
			<div class="form-group" id="select_customer">
				
				<?php echo '<input type="text" name="FormID" value="' . $_SESSION['FormID'] . '" />'; 
					echo '<input type="text" size="30" maxlength="40" name="supplierid" value=""/>';
				?> 
					<input type="text" autocomplete="off" name="customer" value="" placeholder="Start typing supplier Name..." onKeyUp="AutoCompleteCust();" id="customer" class="form-control input-sm" size="50" tabindex="1"  />
					<span class="ui-helper-hidden-accessible" role="status"></span><div id="outputcustomer"></div>					
				</div>	
</td> 
  <?php	
	}else{
	echo '<input class="user" type="hidden" value="user" name="user" />';
	echo '<td><input name="Username" style="width:350px" value="'.$_POST['supplierid'].'" type="text" /></td>';
	}
	echo'</tr>';	
	}else{
	echo '<tr>
			<td>' . _('User') . ':</td>
			<td>';
			echo '<input class="user" type="checkbox" name="user" />';
	echo '</td>
		</tr>';
		echo'<tr>
			<td>' .  _('Payee Name') . '</td>';
	echo '<td>
	<div class="supplierfield">';
	?>
	<div class="form-group" id="select_customer">				
				<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; 
				echo '<input type="hidden" size="30" maxlength="40" id-="supplierid" name="supplierid" value="'.(isset($_POST['supplierid']) ? $_POST['supplierid'] : '').'"/>';?> 
					<input type="text" autocomplete="off" name="customer" value="<?php echo (isset($_POST['customer']) ? $_POST['customer'] : ''); ?>" placeholder="Start typing Supplier  Name..." onKeyUp="AutoCompleteCust();" id="customer" class="form-control input-sm" size="50" tabindex="1"  />
					<span class="ui-helper-hidden-accessible" style="" role="status"></span><div id="outputcustomer"></div>					

				</div>	
	
	</div>
  <div class="userfield" style="display:none"><input name="Username" style="width:350px" type="text" /></div>
  <?php
 echo'</td>';
	}	
	//--------------------------------------------------------------------

echo '</td>
	</tr>
	</table>
	<br/>';

echo '</div>';

echo '<table style="width:670px;" id="dataTable" class="selection table table-hover">';
echo '<tr>
		<th>' .  _('Particulars')  . '</th>
		<th>' .  _('LPO/LSO No'). '</th>
		<th>' .  _('Invoice No'). '</th>
		<th>' .  _('Amount'). '</th>
	</tr>';
	
for($i=0;$i<=count($lpono);$i++)
{
	$particulars = $pat[$i];
	$lpo = $lpono[$i];
	$invoice = $invoiceno[$i];
	$amnt = $amount[$i];

	echo '<tr>
			
			<td><input type="text" size="35" maxlength="55" autofocus="autofocus" required="required"name="particulars[]" value="'.$particulars.'" /></td>
			<td><input type="text" size="10" maxlength="30" autofocus="autofocus"   name="lpono[]" value="'.$lpo.'" /></td>
			<td><input type="text" size="10" maxlength="30" autofocus="autofocus"   name="invoiceno[]" value="'.$invoice.'" /></td>
			<td><input type="text" size="15" maxlength="15" autofocus="autofocus" required="required" class="number" name="amnt[]" value="'.$amnt.'" /></td>
			<td><a href="#" onClick="Javacsript:deleteRow(this)"><img src="'.$RootPath.'/css/trash.png" title="' ._('Delete Row') . '" alt="" /></a></a></td>
		</tr>';
		
		}

echo '</table>';
	echo '<table style="width:667px;">';	
		echo '<tr><td style="width:270px;"><img src="'.$RootPath.'/css/newrow.jpg" onclick=addRow("dataTable") title="' ._('Add New Row') . '" alt="" /></td><td></td><td></td><td><img src="'.$RootPath.'/css/updtamnt.jpg" onClick="findTotal()" title="' ._('Update Total') . '" alt="" />' .  _('Total Amount:')  . '<input type="text" size="15" disabled="true" onmousemove="findTotal()" maxlength="15" id="total" name="tot" value'.$_POST['total'].'" /></td></tr>';
	echo'</div>';
echo '</table>';
echo '<br />
	<div class="centre">
		<strong>Save as Draft: 
		<input name="draft" type="checkbox" value="1" />
	    </strong><br />
	<br />';
echo '<center><input type="submit" name="Submit" value="' . _('Process') . '" /></center>';


echo '</form>';

} //close else function
?>
</div>
<script type="text/javascript">

function AutoComplete() {
	var state_id = document.getElementsByName('item')[0].value;;  
	if (state_id.length > 0 ) { 
	 $.ajax({
			type: "GET",
			url: "Ajax_CounterSale.php",
			data: "Keywords="+state_id,
			cache: false,
			beforeSend: function () { 
				$('#output').html('<i class="fa fa-spinner fa-pulse fa-2x fa-fw">');
			},
			success: function(html) {    
				$("#output").html( html );
			}
		});
	}else{
	$("#output").html( '<div style="background:#FFFFFF;position: absolute; z-index: 99;min-width:376px;">No Items to display</div>' );
	}
}

function AutoCompleteCust() {
	var state_id = document.getElementsByName('customer')[0].value;;  
	if (state_id.length > 0 ) { 
	 $.ajax({
			type: "GET",
			url: "Ajax_Suppliers.php",
			data: "Keywords="+state_id,
			cache: false,
			beforeSend: function () { 
				$('#outputcustomer').html('<i class="fa fa-spinner fa-pulse fa-2x fa-fw">');
			},
			success: function(html) {    
				$("#outputcustomer").html( html );
			}
		});
	}else{
	$("#outputcustomer").html( '<div style="background:#FFFFFF;position: absolute; z-index: 99;min-width:300px;">No Suplier to display</div>' );
	}
}

function supplier(id){
		document.form.supplierid.value= id;
		//document.form.customer.value= name;
		document.form.action='<?php echo htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=Initiate_PV_Ovesea&identifier='.$identifier ; ?>';
		document.form.submit();
		//$(".edithide").hide();
	}


</script>
<script>
        function addRow(tableID) {

            var table = document.getElementById(tableID);

            var rowCount = table.rows.length;
            var row = table.insertRow(rowCount);

			var cell2 = row.insertCell(0);
            var element2 = document.createElement("input");
            element2.type = "text";
			element2.required = "required";
			element2.size = "35";
            element2.name = "particulars[]";
            cell2.appendChild(element2);

            var cell3 = row.insertCell(1);
            var element3 = document.createElement("input");
            element3.type = "text";
			element3.size = "10";
            element3.name = "lpono[]";
            cell3.appendChild(element3);
			
			var cell4 = row.insertCell(2);
            var element4 = document.createElement("input");
            element4.type = "text";
			element4.size = "10";
            element4.name = "invoiceno[]";
            cell4.appendChild(element4);
			
			var cell5 = row.insertCell(3);
            var element5 = document.createElement("input");
            element5.type = "text";
			element5.required = "required";
			element5.size = "15";
            element5.name = "amnt[]";
            cell5.appendChild(element5);
			
			row.insertCell(4).innerHTML= '<a href="#" onClick="Javacsript:deleteRow(this)"><?php echo '<img src="'.$RootPath.'/css/trash.png" title="' ._('Delete Row') . '" alt="" /></a>';?></a>';


        }

        function deleteRow(obj) {
      
    var index = obj.parentNode.parentNode.rowIndex;
    var table = document.getElementById("dataTable");
    table.deleteRow(index);
    
}

function findTotal(){
    var arr = document.getElementsByName('amnt[]');
    var tot=0;
    for(var i=0;i<arr.length;i++){
        if(parseInt(arr[i].value))
            tot += parseInt(arr[i].value);
			total = tot.toString().replace(/([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,');
    }
    document.getElementById('total').value = total;
}
$(".user").click(function() {
    if($(this).is(":checked")) {
		 $(".supplierfield").hide(200);
		 $(".userfield").show(200);
    } else {
        $(".supplierfield").show(300);
		$(".userfield").hide(200);
    }
});

    </script>
	