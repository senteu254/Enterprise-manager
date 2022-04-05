<?php

/* $Id: SecurityTokens.php 4424 2010-12-22 16:27:45Z tim_schofield $*/

include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$Title = _('Payment Voucher Processing');

include('includes/header.inc');
echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/maintenance.png" title="' .
		_('Print') . '" alt="" />' . ' ' . $Title . '</p>';
		
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
if($myro[0] >0){
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
	
echo '<a href="' . $RootPath . '/Create_PV.php">' . _('Back to Payment Voucher') . '</a>';
echo '<table style="background:none repeat scroll 0% 0% #F1F1F1; border:none; box-shadow:none;">
      <tr><td>';
echo '<table class="selection">
      <tr><td>';

	echo '<table class="selection">
			<tr>
				<td>' . _('Voucher ID') . ':</td>
				<th>' . $_POST['voucherid'] . '</th>
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
	<div style="background:url(css/status.png) left top no-repeat; height:230px; width:220px;">
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label.' &nbsp;Bills</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label2.' &nbsp;Procurement &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Certificate</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label3.' &nbsp;AIE Holder</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label4.' &nbsp;Internal Audit</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label5.' &nbsp;Examination</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label6.' &nbsp;Cash Payment</div>
	
	
	</div>
	</td>
	</tr>
	</table>';
} else{
######################################################################################
if (!isset($SelectedUser)) {
	$sql = "SELECT voucherid,
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
				WHERE process_level =0 OR process_level =10 OR process_level =11 OR process_level =12 OR process_level =13 OR process_level =14";
	$result = DB_query($sql);

	echo '<table class="selection">';
	echo '<tr><th>' . _('Voucher ID') . '</th>
				<th>' . _('Control Book No') . '</th>
				<th>' . _('Label') . '</th>
				<th>' . _('Date Raised') . '</th>
				<th>' . _('Payee Name') . '</th>
				<th>' . _('Amount') . '</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>';

	$k=0; //row colour counter

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
		if ($myrow['process_level'] !=0) {
		$view='<td><a href="%s&amp;SelectedUser=%s&amp;view=1">' . _('View') . '</a></td>';
		$edit='';
		$del='';
		$process='';
		}else {
		$edit='<td><a href="%s&amp;SelectedUser=%s">' . _('Edit') . '</a></td>';
		$del= '<td><a href="%s&amp;SelectedUser=%s&amp;delete=1" onclick="return confirm(\'' . _('Are you sure you wish to delete this Voucher?') . '\');">' . _('Delete') . '</a></td>';
		$process='<td><a href="%s&amp;SelectedUser=%s&amp;process=1" onclick="return confirm(\'' . _('Are you sure you wish to Process this Voucher?') . '\');">' . _('Process') . '</a></td>';
		$view='';
		}
if($myrow['process_level'] >=10){
		if($myrow['process_level']==10){
		$alevel='Procurement Certificate';
		$prog = 1;
		}elseif($myrow['process_level']==11){
		$alevel='AIE Holder';
		$prog = 2;
		}elseif($myrow['process_level']==12){
		$alevel='Internal Audit';
		$prog = 3;
		}elseif($myrow['process_level']==13){
		$alevel='Examination';
		$prog = 4;
		}elseif($myrow['process_level']==14){
		$alevel='Cash Payment';
		$prog = 5;
		}
		$edit='<td><a href="%s&amp;SelectedUser=%s">' . _('Edit') . '</a></td>';
		$del= '<td><a href="%s&amp;SelectedUser=%s&amp;delete=1" onclick="return confirm(\'' . _('Are you sure you wish to delete this Voucher?') . '\');">' . _('Delete') . '</a></td>';
		$process='<td><a href="%s&amp;SelectedUser=%s&amp;process='.$prog.'" onclick="return confirm(\'' . _('Are you sure you wish to Resend this Voucher to '.$alevel.'?') . '\');">' . _('Resend') . '</a></td>';
		$view='<td><a href="%s&amp;SelectedUser=%s&amp;view=1">' . _('View') . '</a></td>';
		echo '<input type="hidden" name="Reprocess" value="' . $_SESSION['FormID'] . '" />';
		}


		printf('<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					' .$edit. '
					' .$del. '
					'.$process.'
					'.$view.'
					</tr>',
					$myrow['voucherid'],
					$myrow['authorityref'],
					$myrow['label'],
					$LastVisitDate,
					$myrow['payeename'],
					$myrow['total'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?',
					$myrow['voucherid'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?',
					$myrow['voucherid'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?',
					$myrow['voucherid'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?',
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

echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" id="form">
	<div>
	<br />
	<table>
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
					total
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
	


	echo '<input type="hidden" name="SelectedUser" value="' . $SelectedUser . '" />';
	echo '<input type="hidden" name="UserID" value="' . $_POST['voucherid'] . '" />';

	echo '<table class="selection">
			<tr>
				<td>' . _('Voucher ID') . ':</td>
				<td>' . $_POST['voucherid'] . '</td>
			</tr>';
echo '<a href="' . $RootPath . '/Create_PV.php">' . _('Back to Payment Voucher') . '</a>';
}
#############################################################################################

	echo '<td>' . _('Book No.') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" " name="arefno" value="'.$_POST['arefno'].'" /></td>
		</tr>
		<td>' . _('Date PV Raised') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" class="integer" name="reqdate" value="'.date('d/m/Y').'" /></td>
		</tr>
		<td>' . _('Label') . '</td>
			<td><select required="required" name="label">';
	if ($_POST['label']=='Urgent'){
	  echo'<option selected="selected" value="Urgent">Urgent</option>
	  <option value="Normal">Normal</option>
	  <option value="Not Urgent">Not Urgent</option>
	  <option selected="selected">--Select Label--</option>';
	  }elseif ($_POST['label']=='Normal'){
	  echo'<option value="Urgent">Urgent</option>
	  <option selected="selected" value="Normal">Normal</option>
	  <option value="Not Urgent">Not Urgent</option>
	  <option >--Select Label--</option>';
	  }elseif ($_POST['label']=='Not Urgent'){
	  echo '<option value="Urgent">Urgent</option>
	  <option selected="selected" value="Normal">Normal</option>
	  <option value="Not Urgent">Not Urgent</option>
	  <option >--Select Label--</option>';
	  }else{
	  echo '<option value="Urgent">Urgent</option>
	  <option value="Normal">Normal</option>
	  <option value="Not Urgent">Not Urgent</option>
	  <option selected="selected" value="">--Select Label--</option>';
	  }
	echo '</select></td>';
	echo '<tr>
			<td>' . _('User') . ':</td>
			<td>';
			echo '<input class="user" type="checkbox" name="user" />';
	echo '</td>
		</tr>';
		echo'<tr>
		<td>' .  _('Payee Name') . '</td>';
	echo '<td>
	<div class="supplierfield"><select name="supplierid"  onchange="this.myform.submit">';
     $SQL = "SELECT supplierid,
					suppname			
				    FROM  suppliers
				    ORDER BY supplierid";
				 $result=DB_query($SQL);
  echo '<option selected="selected" value="">--Select Service Supplier--</option>';
  while ($myrow4=DB_fetch_array($result)){
	if (isset($_POST['supplierid']) &&  $myrow4['supplierid']==$_POST['supplierid']){
		echo '<option selected="selected" value="'. $myrow4['supplierid'] . '">' . $myrow4['suppname'] . '</option>';
	} else {
		echo '<option value="'. $myrow4['supplierid'] . '">' . $myrow4['suppname'] . '</option>';
	}
}

  echo '</select></div>
  <div class="userfield" style="display:none"><input name="Username" style="width:350px" type="text" /></div>
  </td>';
		

echo '</td>
	</tr>
	</table>
	<br/>';

echo '</div>';

echo '<table id="dataTable" class="selection">';
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
echo '</table>';

echo '<input type="submit" name="Submit" value="' . _('Save Draft') . '" />';

echo '</form>';

} //close else function
include('includes/footer.inc');
?>

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