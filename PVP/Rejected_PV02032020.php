<link rel="stylesheet" href="PVP/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="PVP/iCheck/flat/blue.css">
<?php

/* $Id: SecurityTokens.php 4424 2010-12-22 16:27:45Z tim_schofield $*/
		
############################################################################################

if (isset($_GET['SelectedUser'])){
	$SelectedUser = $_GET['SelectedUser'];
} elseif (isset($_POST['SelectedUser'])){
	$SelectedUser = $_POST['SelectedUser'];
}
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
$sql = "UPDATE payment_voucher SET department='" . $_POST['departmentid'] . "', 
                        votehead='" . $_POST['votehead'] . "',
                        authorityref='" . $_POST['arefno'] . "',
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

		$sql = "INSERT INTO payment_voucher (voucherid,
						authorityref,
						datereq,
						label,
						payeename,
						supplierid,
						particulars,
						lpo_no,
						invoice_no,
						amount,
						total)
					VALUES ('" . $VoucherID . "',
						'" . $_POST['arefno'] ."',
						'" . $_POST['reqdate'] ."',
						'" . $_POST['label'] ."',
						'" . $_POST['supplier'] ."',
						'" . $_POST['supplierid'] ."',
						'" . serialize($_POST['particulars']) . "',
						'" . serialize($_POST['lpono']) . "',
						'" . serialize($_POST['invoiceno']) ."',
						'" . serialize($_POST['amnt']) ."',
						'" . number_format(array_sum($_POST['amnt']),2) . "')";
		prnMsg( _('A new payment voucher record has been inserted'), 'success' );
		
	}

	if ($InputError!=1){
		//run the SQL from either of the above possibilites
		$ErrMsg = _('The user alterations could not be processed because');
		$DbgMsg = _('The SQL that was used to update the user and failed was');
		$result = DB_query($sql,$ErrMsg,$DbgMsg);

		unset($_POST['voucherid']);
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
	if (isset($_GET['process'])) {
$sql = "UPDATE payment_voucher SET process_level='" . $_GET['process'] . "',
								   comment=''
					WHERE voucherid = '". $SelectedUser . "'";

		$result = DB_query($sql);
		
$SQL = "SELECT voucherno FROM  payment_voucher_approval where voucherno='".$SelectedUser."' and process_level='". $_GET['process'] ."'";
$resu=DB_query($SQL);
  if(DB_num_rows($resu) ==0){
$sqlq = "INSERT INTO payment_voucher_approval (voucherno,
						process_level,
						approver)
					VALUES ('" . $SelectedUser . "',
						'" . $_GET['process'] ."',
						'" . $_SESSION['UserID'] ."')";
		$resulta = DB_query($sqlq);
	}
	
		prnMsg( _('The selected Payment Voucher has been forwaded for processing'), 'info' );
		
	unset($SelectedUser);
	}
######################################################################################
if (isset($_GET['view'])) {
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
					process_level,
					comment
		FROM payment_voucher
		WHERE voucherid='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['voucherid'] = $myrow['voucherid'];
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
	

echo '<a href="' . $RootPath . '/index.php?Application=PVM&Ref=default&Link=inbox_Accountant"><button type="button" name="" class="button"><i class="fa fa-reply"></i> ' . _('Go Back') . '</button></a>';
echo '<table style="width:100%; border:none; box-shadow:none;">
      <tr><td>';
echo '<table class="selection">
      <tr><td>';

echo '<table style="width:100%;background-color: white;">
			<tr>
				<td>' . _('Voucher ID') . ':</td>
				<th>' . $_POST['voucherid'] . '</th>
			</tr>';
	echo '<tr>
			<td>' . _('Authority Ref No.') . '</td>
			<td>'.$_POST['arefno'].'</td>
		</tr>
		<tr>
			<td>' . _('Date Raised') . '</td>
			<td>'.$_POST['datereq'].'</td>
		</tr>
		<tr>
			<td>' . _('Label') . '</td>
			<td>'.$_POST['label'].'</td>
		</tr>
		<tr>
			<td>' .  _('Payee Name') . '</td>
			<td>'.$_POST['name'] .'</td>
		</tr>';

echo '</table><br />';

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
	echo '<tr><center><th>' .  _('Reason for Decline'). '</th></center></tr>';
	echo '<tr><td><center><textarea style="font-weight:bold" disabled="true" cols="35" rows="2">' .$_POST['reason'].'</textarea></center></td></tr>';
	}else{
	echo '';
	}
	echo '</table>';
	echo '</td>
	<td valign="top" class="status">
	<td valign="top" class="status">
	<div style="background:url(css/statuspv.png) left top no-repeat; height:320px; width:220px;">
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label.' &nbsp;VBC Certificate</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label2.' &nbsp;Procurement &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Certificate</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label3.' &nbsp;AIE Holder</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label4.' &nbsp;Internal Audit</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label5.' &nbsp;Examination</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label6.' &nbsp;Finance</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label7.' &nbsp;Cash Payment</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label8.' &nbsp;MD</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label9.' &nbsp;Payment</div>';
	//echo'<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label9.' &nbsp;FM</div>
	
	
	echo'</div>
	</td>
	</tr>
	</table>';
} else{
######################################################################################
if (!isset($SelectedUser)) {
	$sql = "SELECT voucherid,
	               department,
	               votehead,
					authorityref,
					datereq,
					label,
					payeename,
					particulars,
					lpo_no,
					invoice_no,
					amount,
					process_level,
					total
				FROM payment_voucher
				WHERE process_level =10 OR process_level =11 OR process_level =12 OR process_level =13 OR process_level =14 OR process_level =15 OR process_level =16 OR process_level =17 OR process_level =18 OR process_level =19";
	$result = DB_query($sql);

	echo '<table style="width:100%; font-size:12px;" class="selection table table-hover">';
	echo '<tr><th>' . _('VID') . '</th>
				<th>' . _('Priority') . '</th>
				<th style="width:120px;">' . _('Book No') . '</th>
				<th>' . _('Date Raised') . '</th>
				<th>' . _('Payee Name') . '</th>
				<th>' . _('Amount') . '</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>';

	$k=0; //row colour counter
if (DB_num_rows($result) == 0) {
echo '<tr><td colspan="9"><center style="color:#FF0000"><strong>No Records Found</strong></center></td></tr>';
}else{
	while ($myrow = DB_fetch_array($result)) {
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}

	if ($myrow['datereq']=='') {
		$LastVisitDate = Date($_SESSION['DefaultDateFormat']);
	} else {
		$LastVisitDate = ConvertSQLDate($myrow['datereq']);
	}

		/*The SecurityHeadings array is defined in config.php */

if($myrow['process_level'] >=10){
		if($myrow['process_level']==11){
		$alevel='AIE Holder Certificate';
		$prog = 1;
		}elseif($myrow['process_level']==12){
		$alevel='Voucher Examination';
		$prog = 2;
		}elseif($myrow['process_level']==13){
		$alevel='Internal Audit';
		$prog = 3;
		}elseif($myrow['process_level']==14){
		$alevel='VBC Certificate';
		$prog = 4;
		}elseif($myrow['process_level']==15){
		$alevel='Voucher Authorization';
		$prog = 5;
		}elseif($myrow['process_level']==16){
		$alevel='Voucher Payment';
		$prog = 6;
		}elseif($myrow['process_level']==17){
		$alevel='MD Authorozation';
		$prog = 7;
		}elseif($myrow['process_level']==18){
		$alevel='Finance Cash ';
		$prog = 8;
		}elseif($myrow['process_level']==19){
		$alevel='Cash Payment';
		$prog = 9;
		}
		$edit='<td><a title="Edit" href="%s&amp;SelectedUser=%s"><span class="glyphicon glyphicon-edit"></span></a></td>';
		$del= '<td><a href="%s&amp;SelectedUser=%s&amp;delete=1" title="Delete" onclick="return confirm(\'' . _('Are you sure you wish to delete this Voucher?') . '\');"><span class="glyphicon glyphicon-trash"></span></a></td>';
		$process='<td><a href="%s&amp;SelectedUser=%s&amp;process='.$prog.'" title="Process Resend" onclick="return confirm(\'' . _('Are you sure you wish to Resend this Voucher to '.$alevel.'?') . '\');"><span class="glyphicon glyphicon-play-circle"></span></a></td>';
		$view='<td><a title="View" href="%s&amp;SelectedUser=%s&amp;view=1">'.$myrow['authorityref'].'</a></td>';
		
		echo '<input type="hidden" name="Reprocess" value="' . $_SESSION['FormID'] . '" />';
		}
       $print='<td><a target="_blank" href="PDFprintPV.php?voucher='.$SelectedUser.'">Print PV</a></td>';
		printf('<td>%s</td>
				<td>%s</td>
					'.$view.'
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					' .$edit. '
					' .$del. '
					'.$process.'
					
					</tr>',
					$myrow['votehead'],
					'<span class="label2 label-'.(($myrow['label']=="Urgent" or $myrow['label']=="Emergency")? 'danger':'success').'">'.$myrow['label'].'</span>',
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?Application=PVM&Ref=default&Link=inbox_Accountant',
					$myrow['voucherid'],
					$LastVisitDate,
					$myrow['payeename'],
					$myrow['total'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?Application=PVM&Ref=default&Link=inbox_Accountant',
					$myrow['voucherid'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?Application=PVM&Ref=default&Link=inbox_Accountant',
					$myrow['voucherid'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?Application=PVM&Ref=default&Link=inbox_Accountant',
					$myrow['voucherid']);

	} //END WHILE LIST LOOP
	$sql = "SELECT voucherid FROM payment_voucher";
	$results = DB_query($sql);
	$num = DB_num_rows($results);
	$id= $num+1;
	$voucherid= sprintf("%03d", $id);
echo '</table><br />';	
}
############################################################################################

echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=inbox_Accountant" id="form">
	<br />
	<table>
		<tr>';
		
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<input type="hidden" name="voucherid" value="' . $voucherid . '" />';

	

echo '</table>';
	





 //close else function
} //close else function
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

echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=inbox_Accountant" id="form">';
		
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	
	echo '<input type="hidden" name="SelectedUser" value="' . $SelectedUser . '" />';
	echo '<input type="hidden" name="UserID" value="' . $_POST['voucherid'] . '" />';

	echo '<table class="selection table table-hover">
			<tr>
				<td>' . _('Voucher ID') . ':</td>
				<td>' . $_POST['voucherid'] . '</td>
			</tr>';
echo '<a href="' . $RootPath . '/index.php?Application=PVM&Ref=default&Link=inbox_Accountant"><button type="button" name="" class="button"><i class="fa fa-reply"></i> ' . _('Go Back') . '</button></a>';
echo'<tr>
<td>' .  _('Department') . '</td>';
	echo '<td>
	<select required="required" name="departmentid">';
      $SQL2 = "SELECT * FROM  departments
			  WHERE departmentid=7 OR departmentid=17";
				 $result2=DB_query($SQL2);
  echo '<option selected="selected" value="">--Select department--</option>';
  while ($myrow5=DB_fetch_array($result2)){
	if (isset($_SESSION['PV' . $identifier]->Dept) &&  $myrow5['departmentid']==$_SESSION['PV' . $identifier]->Dept){
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
	if (isset($_SESSION['PV' . $identifier]->Vote) &&  $myrow6['Votecode']==$_SESSION['PV' . $identifier]->Vote){
		echo '<option selected="selected" value="'. $myrow6['Votecode'] . '">' . $myrow6['Votehead'] . '</option>';
	} else {
		echo '<option value="'. $myrow6['Votecode'] . '">' . $myrow6['Votehead'] . '</option>';
	}
}

  echo '</select></div>
  </td>';	
	echo'</tr>';
echo '<td>' . _('Book No.') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" " name="arefno" value="'.$_POST['arefno'].'" /></td>
		</tr>
		<td>' . _('Date PV Raised') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" class="integer" name="reqdate" value="'.date('d/m/Y').'" /></td>
		</tr>
		<td>' . _('Label') . '</td>';
		$label = array('Normal','Urgent','Not Urgent');
		echo '<td><select required="required" name="label">';
	foreach($label as $rob){
		if($rob == $_POST['label']){
		echo'<option selected="selected" value="'.$rob.'">'.$rob.'</option>';
		}else{
	  echo'<option value="'.$rob.'">'.$rob.'</option>';
	  }
	  }
	echo '</select></td>';
	//--------------------------------------------------------------------
	echo'<tr>';
	echo'<td>' .  _('Payee Name') . '</td>';
	if(isset($_POST['supid']) && $_POST['supid'] !=""){
echo '<td><input name="supplierid" style="width:350px" value="'.$_POST['supplierid'].'" disabled type="text" /></td>';
	
	}else{
	echo '<input class="user" type="hidden" value="user" name="user" />';
	echo '<td><input name="Username" style="width:350px" value="'.$_POST['supplierid'].'" type="text" /></td>';
	}
	echo'</tr>';	
	//--------------------------------------------------------------------

echo '</td>
	</tr>
	</table>
	<br/>';


echo '<table id="dataTable" class="selection table table-hover">';
echo '<tr>
		<th>' .  _('Particulars')  . '</th>
		<th>' .  _('LPO/LSO No'). '</th>
		<th>' .  _('Invoice No'). '</th>
		<th>' .  _('Amount'). '</th>
	</tr>';
	
for($i=0;$i<=(count($lpono)-1);$i++)
{
	$particulars = $pat[$i];
	$lpo = $lpono[$i];
	$invoice = $invoiceno[$i];
	$amnt = $amount[$i];
	
	if(isset($_POST['supid']) && $_POST['supid'] !=""){
	echo '<tr>
			
			<td><input type="text" size="35" maxlength="55" autofocus="autofocus" name="particulars[]" value="'.$particulars.'" /></td>
			<td><input type="hidden" name="lpono[]" value="'.$lpo.'" /><input type="text" size="10" maxlength="30" autofocus="autofocus" disabled="true" value="'.$lpo.'" /></td>
			<td><input type="hidden"  name="invoiceno[]" value="'.$invoice.'" /><input type="text" size="10" maxlength="30" autofocus="autofocus" disabled="true" value="'.$invoice.'" /></td>
			<td><input type="hidden" name="amnt[]" value="'.$amnt.'" /><input type="text" size="15" maxlength="15" autofocus="autofocus"  disabled="true" class="number" value="'.number_format($amnt,2).'" /></td>';
		echo '</tr>';
	}else{
		echo '<tr>
			
			<td><input type="text" size="35" maxlength="55" autofocus="autofocus" required="required"name="particulars[]" value="'.$particulars.'" /></td>
			<td><input type="text" size="10" maxlength="30" autofocus="autofocus"   name="lpono[]" value="'.$lpo.'" /></td>
			<td><input type="text" size="10" maxlength="30" autofocus="autofocus"   name="invoiceno[]" value="'.$invoice.'" /></td>
			<td><input type="text" size="15" maxlength="15" autofocus="autofocus" required="required" class="number" name="amnt[]" value="'.$amnt.'" /></td>
		</tr>';
		}
		}

//echo '</table>';
	//echo '<table style="width:667px;">';	
		echo '<tr><td style="width:270px;"></td><td></td><td>' .  _('Total Amount:')  . '&nbsp;&nbsp;&nbsp;<span onClick="findTotal()" style="cursor:pointer" title="' ._('Update Total') . '" class="glyphicon glyphicon-refresh"></span></td><td><input type="text" size="15" disabled="true" class="number"  onmousemove="findTotal()" maxlength="15" id="total" name="tot" value'.$_POST['total'].'" /></td></tr>';
echo '</table>';
echo '<center><input type="submit" name="Submit" value="' . _('Save Draft') . '" /></center>';

}
#############################################################################################
}
echo '</form>';
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
	$("#outputcustomer").html( '<div style="background:#FFFFFF;position: absolute; z-index: 99;min-width:300px;">No Customer to display</div>' );
	}
}

function supplier(id){
		document.form.supplierid.value= id;
		//document.form.customer.value= name;
		document.form.action='<?php echo htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=Initiate_PV&identifier='.$identifier ; ?>';
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
	