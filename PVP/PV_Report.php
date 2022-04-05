	
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
	
echo '<a href="' . $RootPath . '/index.php?Application=PVM&Ref=default&Link=PV_Report"><button type="button" name="" class="button"><i class="fa fa-reply"></i> ' . _('Go Back') . '</button></a>';
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
		//echo '<tr align="right"><td></td><td></td><td>' .  _('Total Amount'). '</td><th>' .$_POST['total'].'</th></tr>';
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
		

		################################################################6% witholding##################################################
		//$total = str_replace(',','',$_POST['total']);
		//$TAX = (6/116*$total);
		
		
		while ($myrowq = DB_fetch_array($results)) {
		
			echo '<tr>';
				echo'<td>' . $myrowq['tax_name'] . '</td>';
				if($myrowq['percentage']==6){
		$TaxTotals6 = (round(($total*(16/116) * (6/16)),2));
				echo'<td>' . locale_number_format($TaxTotals6,2) . '</td>';
				}
				if($myrowq['percentage']==5){
		$TaxTotals5 = (round(($total*(5/100)),2));
				echo'<td>' . locale_number_format($TaxTotals5,2) . '</td>';
				}
				if($myrowq['percentage']==10){
		$TaxTotals10 = (round(($total*(10/100)),2));
				echo'<td>' .locale_number_format($TaxTotals10 ,2). '</td>';
				}
				if($myrowq['percentage']==3){
		$TaxTotals3 = (round(($total*(3/100)),2));
				echo'<td>' . locale_number_format($TaxTotals3,2) . '</td>';
				}
				if($myrowq['percentage']==8){
		 $TaxTotals8 = (round(($total*(8/108) * (6/8)),2));
				echo'<td>' . locale_number_format($TaxTotals38,2). '</td>';
				}
				
			echo'</tr>';
			$TOT_TAX=$TaxTotals6+$TaxTotals5+$TaxTotals10+$TaxTotals3+$TaxTotals8;
	
		//echo '<tr align="right"><td colspan="3" style="text-align:right">' .  _('PAYEE 2 ['.$myrow['tax'].'%]'). '</td><th>' .number_format($TAX,2).'</th></tr>';
		}
		echo '<tr align="right"><td colspan="3" style="text-align:right">' .  _('PAYEE 1 ['.$_POST['name'].']'). '</td><th>' .number_format($total-$TOT_TAX,2).'</th></tr>';
		echo '<tr align="right"><td colspan="3" style="text-align:right">' .  _('PAYEE 2 TOTAL'). '</td><th>' .locale_number_format($TOT_TAX,2).'</th></tr>';
		echo '<tr align="right"><td colspan="3" style="text-align:right">' .  _('TOTAL AMOUNT PAYABLE'). '</td><th>' .locale_number_format($total,2).'</th></tr>';
        /*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
echo '</table>';
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=inbox" id="form">';
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

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=PV_Report" id="form">';
	
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
	echo'<td>' . _('PV Stage') . ':</td>';
echo '<td><select name="PV_Level">';
if ($_POST['PV_Level'] == 'All') {
	echo '<option selected="selected" value="All">' . _('All') . '</option>';
} else {
	echo '<option value="All">' . _('All') . '</option>';
}
while ($myrow5 = DB_fetch_array($result5)) {
	if ($myrow5['levelcode'] == $_POST['PV_Level']) {
		echo '<option selected="selected" value="' . $myrow5['levelcode'] . '">' . $myrow5['pvrole'] . '</option>';
	} else {
		echo '<option value="' . $myrow5['levelcode'] . '">' . $myrow5['pvrole'] . '</option>';
	}
}
echo '</select></td>
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
		 $sort=" AND authorityref " . LIKE . " '%". $_POST['ref'] ."%' AND level='".$_POST['PV_Level']."' AND authorityref " . LIKE . " '%". $_POST['Yearend'] ."%'";
		 }elseif(isset($_POST['supname']) && $_POST['supname'] !=""){
		 $sort=" AND payeename " . LIKE . " '%".$_POST['supname']."%' AND authorityref " . LIKE . " '%". $_POST['Yearend'] ."%'";
		 }else{
		 $sort =" AND authorityref " . LIKE . " '%". $_POST['Yearend'] ."%'";
		 }
				
		 }
	
		  
######################################################################################
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=PV_Report" id="form">';

/*		$sql = "SELECT *,a.process_level FROM payment_voucher a
		        INNER JOIN  pvlevel b ON a.process_level=b.levelcode
				WHERE a.process_level >0
				".$sort."
				ORDER BY a.process_level ASC, a.voucherid DESC LIMIT {$startpoint} , {$per_page}";
				
	$sqlforPages = " a.process_level FROM payment_voucher a
		         INNER JOIN  pvlevel b ON a.process_level=b.levelcode
				 WHERE a.process_level >0
				".$sort."";*/
				
	$sql = "SELECT a.voucherid,
	                a.uniqueid,
					a.authorityref,
					a.datereq,
					a.label,
					a.payeename,
					a.particulars,
					a.lpo_no,
					a.invoice_no,
					a.amount,
					a.process_level,
					a.total,
					b.level
				FROM payment_voucher a 
				INNER JOIN pvroles b ON a.process_level=b.level
			    WHERE a.process_level >0
				".$sort."
	GROUP BY a.uniqueid
	ORDER BY a.process_level ASC, a.voucherid DESC LIMIT {$startpoint} , {$per_page}";
	
	
	$sqlforPages = " payment_voucher a 
				INNER JOIN pvroles b ON a.process_level=b.level
			    WHERE a.process_level >0
				".$sort."";
				
				
	$result = DB_query($sql);
	
	echo pagination($sqlforPages,$per_page,$page,$url='?Application=PVM&Ref=default&Link=PV_Report&');

		?>
	<table style="width:100%;font-size:11px;" class="selection table table-hover">
	<tr style="height:30px;">
				<th>Date Raised</th>
				<th>Priority</th>
				<th>Book No.</th>
				<th>Payee Name</th>
				<th>Tax</th>
				<th>Amount</th>
			</tr>
<?php
	$k=0; //row colour counter
	while ($myrow = DB_fetch_array($result)) {
	  $total = str_replace(',','',$myrow['total']); 
  $tax = $myrow['tax'];

 ###########################################################TAX CALCULATIONS###################################################################################33
$sqls = "SELECT *,b.tax_name,a.tax_percentage FROM  tax_deducted a 
	         INNER JOIN pv_tax b ON a.tax_percentage=b.ptid WHERE a.Vid=" .  $myrow['voucherid'] . "";
	$results = DB_query($sqls);
	echo '<tr>';	
		while ($myrowq = DB_fetch_array($results)) {
		
			echo '<tr>';
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
				
			echo'</tr>';
			$TOT_TAX=$TaxTotals6+$TaxTotals5+$TaxTotals10+$TaxTotals3+$TaxTotals8;
	}
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

		/*The SecurityHeadings array is defined in config.php */
	     	printf('<td>%s</td>
					<td>%s</td>
					<td><a href="%s&amp;SelectedUser=%s&amp;view=1">' . $myrow['authorityref']. '</a></td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					</tr>',
					$myrow['datereq'],
					'<span class="label2 label-'.(($myrow['label']=="Urgent" or $myrow['label']=="Emergency")? 'danger':'success').'">'.$myrow['label'].'</span>',
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '??Application=PVM&Ref=default&Link=PV_Report',
					$myrow['voucherid'],
					$myrow['payeename'],
					locale_number_format($TOT_TAX,2),
					($total-$TOT_TAX),
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?Application=PVM&Ref=default&Link=PV_Report',
					$myrow['voucherid'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '??Application=PVM&Ref=default&Link=PV_Report',
					$myrow['voucherid']);
	
	$RowIndex++;
	
	} //END WHILE LIST LOOP
	echo '</table>';
	}
	$print ='<a href="PDFPaymentVoucherReport.php?level='.$_POST['PV_Level'].'&year='.$_POST['Yearend'].'&supplier='.$_POST['supname'].'&Ref='.$_POST['ref'].'"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print PV Report</button></a>';
	 echo $print; 
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
