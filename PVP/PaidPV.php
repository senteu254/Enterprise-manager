	
<?php	
############################################################################################
$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
 
$per_page = 30; // Set how many records do you want to display per page.
$startpoint = ($page * $per_page) - $per_page;

if (isset($_GET['SelectedUser'])){
	$SelectedUser = $_GET['SelectedUser'];
} elseif (isset($_POST['SelectedUser'])){
	$SelectedUser = $_POST['SelectedUser'];
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
					process_level
		FROM payment_voucher
		WHERE voucherid='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['voucherid'] = $myrow['voucherid'];
	$_POST['arefno'] = $myrow['authorityref'];
	$_POST['datereq'] = $myrow['datereq'];
	$_POST['label'] = $myrow['label'];
	$_POST['name']	= $myrow['payeename'];
	$pat  = unserialize($myrow['particulars']);
	$lpono = unserialize($myrow['lpo_no']);
	$invoiceno = unserialize($myrow['invoice_no']);
	$amount = unserialize($myrow['amount']);
	$_POST['total'] = $myrow['total'];
	
echo '<a href="' . $RootPath . '/index.php?Application=PVM&Ref=default&Link=Paid_PV"><button type="button" name="" class="button"><i class="fa fa-reply"></i> ' . _('Go Back') . '</button></a>';
echo'<br>';
echo'<br>';
echo '<table style="border:none; box-shadow:none;">
      <tr><td>';
echo '<table class="selection">
      <tr><td>';

	echo '<table style="width:100%;background-color: white;">
			<tr>
				<td>' . _('Voucher ID') . ':</td>
				<td>' . $_POST['voucherid'] . '</td>
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
		################################################################6% witholding##################################################
/* if ($myrow['Voucherid']!='') {
  $total = str_replace(',','',$Amount);
  }else{
  $total = str_replace(',','',$myrow['total']);
  }*/
   $total = str_replace(',','',$myrow['total']);
		//$TAX = round(($myrow['tax']/(100+$myrow['tax'])*$total),2);
 ###########################################################TAX CALCULATIONS###################################################################################33
$sqls = "SELECT *,b.tax_name,a.tax_percentage FROM  tax_deducted a 
	         INNER JOIN pv_tax b ON a.tax_percentage=b.ptid WHERE a.Vid=" .  $myrow['voucherid'] . "";
	$results = DB_query($sqls);	
				
		while ($myrowq = DB_fetch_array($results)) {
		
			echo '<tr>';
				echo'<td>' . $myrowq['tax_name'] . ',' . $myrowq['percentage'] . '</td>';
				if($myrowq['percentage']==6){
		$TaxTotals6 = (round(($total*(16/116) * (6/16)),2));
				echo'<td>' . $TaxTotals6 . '</td>';
				}
				if($myrowq['percentage']==5){
		$TaxTotals5 = (round(($total*(5/100)),2));
				echo'<td>' . $TaxTotals5 . '</td>';
				}
				if($myrowq['percentage']==10){
		$TaxTotals10 = (round(($total*(10/100)),2));
				echo'<td>' . $TaxTotals10 . '</td>';
				}
				if($myrowq['percentage']==3){
		$TaxTotals3 = (round(($total*(3/100)),2));
				echo'<td>' . $TaxTotals3 . '</td>';
				}
				if($myrowq['percentage']==8){
		 $TaxTotals8 = (round(($total*(8/108) * (6/8)),2));
				echo'<td>' . $TaxTotals38. '</td>';
				}
				
			echo'</tr>';
			$TOT_TAX=$TaxTotals6+$TaxTotals5+$TaxTotals10+$TaxTotals3+$TaxTotals8;
	
		//echo '<tr align="right"><td colspan="3" style="text-align:right">' .  _('PAYEE 2 ['.$myrow['tax'].'%]'). '</td><th>' .number_format($TAX,2).'</th></tr>';
		}
		echo '<tr align="right"><td colspan="3" style="text-align:right">' .  _('PAYEE 1 ['.$_POST['name'].']'). '</td><th>' .number_format($total-$TOT_TAX,2).'</th></tr>';
		echo '<tr align="right"><td colspan="3" style="text-align:right">' .  _('PAYEE 2 TOTAL'). '</td><th>' .number_format($TOT_TAX,2).'</th></tr>';
		echo '<tr align="right"><td colspan="3" style="text-align:right">' .  _('TOTAL AMOUNT PAYABLE'). '</td><th>' .number_format($total,2).'</th></tr>';
        /*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

echo '</table>';
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=Paid_PV" id="form">';
			//<input type="submit" name="Submit" value="' . _('Approve') . '" />';
		echo'<input type="hidden" name="pv[]" value="' . $_GET['SelectedUser'] . '" />
			<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
		//echo "<a href=# onclick=pop('popDiv')><img src='".$RootPath."/css/decline.png'/> </a>";
		echo '</form>';
		

    include('includes/Level_Tracking.php');

	echo '</td>	</tr></table>';
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
	}else{
######################################################################################

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=Paid_PV" id="form">';
	
	echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';
		$SQL2 = "SELECT *,a.process_level FROM payment_voucher a
		         INNER JOIN  pvlevel b ON a.process_level=b.levelcode
				 GROUP BY b.levelcode
				 ORDER BY b.levelcode";
$result5 = DB_query($SQL2);
if (DB_num_rows($result5) == 0) {
	echo '<p class="bad">' . _('Problem Report') . ':<br />' . _('There are no pv levels  currently defined please use the link below to set them up,visit system admin') . '</p>';
	exit;
}
	echo '<table>
	<tr>';
	echo'<td>' . _('Finance Voucher No.') . ':</td>';
echo '<td><input name="FVNO" value="'. $_POST['FVNO'] .'" type="text" /></td>
<td>' . _('Supplier Name') . ':</td>
<td colspan="2"><input name="supname" value="'. $_POST['supname'] .'" type="text" /></td>
	</tr>
	<tr>
		<td>' . _('Book Ref No.') . ':</td>
		<td><input name="ref" value="'. $_POST['ref'] .'" type="text" /></td>
         <td>Financial Year End</td><td><select name="Yearend">';
		 if(isset($_POST['Yearend']) && $_POST['Yearend']==Date('y',YearEndDate($_SESSION['YearEnd'],-2)).'-'.Date('y',YearEndDate($_SESSION['YearEnd'],-1))){
		echo'<option selected value="' .  Date('y',YearEndDate($_SESSION['YearEnd'],-2)).'-'.Date('y',YearEndDate($_SESSION['YearEnd'],-1)) . '">' .  Date('y',YearEndDate($_SESSION['YearEnd'],-2)).'-'.Date('y',YearEndDate($_SESSION['YearEnd'],-1)) . '</option>'; 
		echo'<option value="'.  Date('y',YearEndDate($_SESSION['YearEnd'],-1)).'-'.Date('y',YearEndDate($_SESSION['YearEnd'],0)) . '">' .Date('y',YearEndDate($_SESSION['YearEnd'],-1)).'-'.Date('y',YearEndDate($_SESSION['YearEnd'],0)) . '</option>';
		 }else{
		  echo'<option value="' .  Date('y',YearEndDate($_SESSION['YearEnd'],-2)).'-'.Date('y',YearEndDate($_SESSION['YearEnd'],-1)) . '">' .  Date('y',YearEndDate($_SESSION['YearEnd'],-2)).'-'.Date('y',YearEndDate($_SESSION['YearEnd'],-1)) . '</option>';
		  echo'<option selected value="'.  Date('y',YearEndDate($_SESSION['YearEnd'],-1)).'-'.Date('y',YearEndDate($_SESSION['YearEnd'],0)) . '">' .Date('y',YearEndDate($_SESSION['YearEnd'],-1)).'-'.Date('y',YearEndDate($_SESSION['YearEnd'],0)) . '</option>';
			}
echo '</select></td>';
		echo'<td>
			<input name="Search" type="submit" value="Search" />
			</td>';
	   
	    echo' </tr></table>'; 
		//////////////////////////////////////
		 echo '</form>';
		$sort ='';
		 if(isset($_POST['Search'])){
		 
		if(isset($_POST['ref']) && $_POST['ref'] !=""){
		 $sort=" AND authorityref " . LIKE . " '%". $_POST['ref'] ."%' AND authorityref " . LIKE . " '%". $_POST['Yearend'] ."%'";
		 }elseif(isset($_POST['PV_Level']) && $_POST['PV_Level'] !="" && $_POST['PV_Level'] !="All"){
		 $sort=" AND authorityref " . LIKE . " '%". $_POST['ref'] ."%' AND levelcode='".$_POST['PV_Level']."' AND authorityref " . LIKE . " '%". $_POST['Yearend'] ."%'";
		 }elseif(isset($_POST['supname']) && $_POST['supname'] !=""){
		 $sort=" AND payeename " . LIKE . " '%".$_POST['supname']."%'";
		 }elseif(isset($_POST['FVNO']) && $_POST['FVNO'] !=""){
		 $sort=" AND FVoucherNo " . LIKE . " '%".$_POST['FVNO']."%'";
		 }else{
		 $sort =" AND authorityref " . LIKE . " '%". $_POST['Yearend'] ."%'";
		 }
				
		 }
	
		  
######################################################################################
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=Paid_PV" id="form">';

		$sql = "SELECT * FROM payment_voucher a
		        RIGHT JOIN  cash b ON b.VoucherID=a.voucherid
				".$sort."
				GROUP BY b.FVoucherNo
				ORDER BY b.VoucherID ASC, b.VoucherID DESC LIMIT {$startpoint} , {$per_page}";
				
	$sqlforPages = "FROM payment_voucher a
		        RIGHT JOIN  cash b ON b.VoucherID=a.voucherid	
				".$sort."
				GROUP BY b.FVoucherNo";
	$result = DB_query($sql);
	
	echo pagination($sqlforPages,$per_page,$page,$url='?Application=PVM&Ref=default&Link=Paid_PV&');

		?>
	<table style="width:100%;" class="selection table table-hover">
	<tr style="height:30px;">
				<th>Date Paid</th>
				<th>F.Voucher No.</th>
				<th>Book No.</th>
				<th>Payee Name</th>
				<th>Tax</th>
				<th>Amount</th>
			</tr>
<?php
	$k=0; //row colour counter

	while ($myrow = DB_fetch_array($result)) {
  $total = str_replace(',','',$myrow['total']);
 ###########################################################TAX CALCULATIONS###################################################################################33
$sqlsm = "SELECT * FROM  tax_deducted a
                   INNER JOIN pv_tax b ON a.tax_percentage=b.ptid
				   WHERE a.Vid='" .  $myrow['voucherid'] . "'";
	$results7 = DB_query($sqlsm);
if (DB_num_rows($result7) == 0) {
	$TaxTotals6=0;	
	$TaxTotals5=0;	
	$TaxTotals10=0;	
	$TaxTotals3=0;	
	$TaxTotals8=0;
}
/*	
$sqlsm = "SELECT *,b.tax_name,c.tax_percentage FROM  cash a 
                  INNER JOIN tax_deducted c ON a.VoucherID=c.Vid
	              INNER JOIN pv_tax b ON c.tax_percentage=b.ptid
			      WHERE a.VoucherID='" .  $myrow['voucherid'] . "'";
	$results7 = DB_query($sqlsm);*/		
		while ($myrowq = DB_fetch_array($results7)) {		
				//<td>' . $myrowq['tax_name'] . ',' . $myrowq['percentage'] . '</td>';
				if($myrowq['percentage']==6){
		$TaxTotals6 = (round(($total*(16/116) * (6/16)),2));
				//echo'<td>' . $TaxTotals6 . '</td>';
				}
				if($myrowq['percentage']==5){
		$TaxTotals5 = (round(($total*(5/100)),2));
				//echo'<td>' . $TaxTotals5 . '</td>';
				}
				if($myrowq['percentage']==10){
		$TaxTotals10 = (round(($total*(10/100)),2));
				//echo'<td>' . $TaxTotals10 . '</td>';
				}
				if($myrowq['percentage']==3){
		$TaxTotals3 = (round(($total*(3/100)),2));
				//echo'<td>' . $TaxTotals3 . '</td>';
				}
				if($myrowq['percentage']==8){
		 $TaxTotals8 = (round(($total*(8/108) * (6/8)),2));
				//echo'<td>' . $TaxTotals38. '</td>';
				}
				
			}
			$TOT_TAX=$TaxTotals6+$TaxTotals5+$TaxTotals10+$TaxTotals3+$TaxTotals8;
			
			

###########################################################TAX CALCULATIONS###############################################################################################33
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
		$LastVisitDate = $myrow['datereq'];
	}

$URL_to_FV_Details = $RootPath . '/GLTransInquiry.php?TypeID=1&TransNo=' . $myrow['TransNo'];
		/*The SecurityHeadings array is defined in config.php */
	     	printf('<td>%s</td>
					<td>%s</td>
					<td><a href="%s&amp;SelectedUser=%s&amp;view=1">' . $myrow['authorityref']. '</a></td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					</tr>',
					$myrow['date'],
					'<a href="' . $URL_to_FV_Details . '">' .$myrow['FVoucherNo'] . '</a>',
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?Application=PVM&Ref=default&Link=Paid_PV',
					$myrow['voucherid'],
					$myrow['payeename'],
					$TOT_TAX,
					($total-$TOT_TAX),
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?Application=PVM&Ref=default&Link=Paid_PV',
					$myrow['voucherid'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=Paid_PV',
					$myrow['voucherid']);
	
	$RowIndex++;
	
		
	} //END WHILE LIST LOOP
	echo '</table>';
	}
	//$print ='<a href="PDFPaymentVoucherReport.php?level='.$_POST['PV_Level'].'&year='.$_POST['Yearend'].'"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print PV Report</button></a>';
	// echo $print; 
?>

		<script type="text/javascript">
			function pop(div,id) {
				document.getElementById(div).style.display = 'block';
				document.getElementById('pvs').value = id;
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
