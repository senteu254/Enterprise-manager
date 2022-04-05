<?php
	//$PageSecurity=0;
	include('includes/session.inc');
	$Title=_('Payment Voucher Report');
	include('includes/header.inc');
if (isset($_GET['SelectedUser'])){
	$SelectedUser = $_GET['SelectedUser'];
} elseif (isset($_POST['SelectedUser'])){
	$SelectedUser = $_POST['SelectedUser'];
}

    //echo '<tr><center><td colspan="1"><input name="Back" type="hidden" value=""/><a href="Votebook_Commitment.php">Back to Commitments</a></center></tr>';
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Payment Voucher Report') . '" alt="" />' . ' ' . _(     'Payment Voucher Report') . '</p>';
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
	$_POST['payeename']	= $myrow['payeename'];
	$pat  = unserialize($myrow['particulars']);
	$lpono = unserialize($myrow['lpo_no']);
	$invoiceno = unserialize($myrow['invoice_no']);
	$amount = unserialize($myrow['amount']);
	$_POST['total'] = $myrow['total'];
	$_POST['reason'] = $myrow['comment'];
	
echo '<a href="' . $RootPath . '/PaymentVoucherReportsPC.php">' . _('Back to Payment Voucher Report') . '</a>';
echo '<table style="background:none repeat scroll 0% 0% #F1F1F1; border:none; box-shadow:none;">
      <tr><td>';
echo '<table class="selection">
      <tr><td>';

	echo '<table class="selection">
			<tr>
				<td>' . _('Voucher ID') . ':</td>
				<th>' . $_POST['voucherid'] . '</th>
			</tr>';


	echo '<td>' . _('Authority Ref No.') . '</td>
			<td>'.$_POST['arefno'].'</td>
		</tr>
		<td>' . _('Date requested') . '</td>
			<td>'.$_POST['datereq'].'</td>
		</tr>
		<td>' . _('Label') . '</td>
			<td>'.$_POST['label'].'</td>
		</tr>
		<tr>
		<td>' .  _('Payee Name') . '</td>
		<td>'.$_POST['payeename'] .'</td>
		<td>';

echo '</td>
	</tr>
	</table>

	<br />';

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
	<div style="background:url(css/status.png) left top no-repeat; height:240px; width:220px;">
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
}else{
	echo '<form action="" method="post">';
	echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';
	
	echo '<table><tr>
		<td>' . _('Book Ref No.') . ':</td>
		<td><input name="ref" value="'. $_POST['ref'] .'" type="text" /></td>
		<td>' . _('Date') . ':</td>
		<td><input type="text" class="date" alt="'.$_SESSION['DefaultDateFormat'].'" name="date" maxlength="11" size="12" value="' . $_POST['date'] . '" /></td>';

		echo'	<td></td><td>
			<input name="Search" type="submit" value="Search" />
			<td>';
	   
	    echo' </tr></table>'; 
		//////////////////////////////////////
		 echo '</form>';
}
		 if(isset($_POST['Search'])){
		if(isset($_POST['ref']) && $_POST['ref'] !="" && isset($_POST['date']) && $_POST['date'] !=""){
		 $sort=" AND authorityref " . LIKE . " '%". $_POST['ref'] ."%' AND DATE_FORMAT(approvaldate,'%Y-%m-%d') " . LIKE . " '%". FormatDateForSQL($_POST['date']) ."%'";
		 }elseif(isset($_POST['ref']) && $_POST['ref'] !=""){
		 $sort=" AND authorityref " . LIKE . " '%". $_POST['ref'] ."%'";
		 }elseif(isset($_POST['date']) && $_POST['date'] !=""){
		 $sort=" AND DATE_FORMAT(approvaldate,'%Y-%m-%d') " . LIKE . " '%". FormatDateForSQL($_POST['date']) ."%'";
		 }else{
		 $sort ="";
		 }
		 echo'<table cellpadding="2" class="selection">';
	echo '<tr><th>' . _('Voucher ID') . '</th>
				<th>' . _('Reg No') . '</th>
				<th>' . _('Label') . '</th>
				<th>' . _('Requested Date') . '</th>
				<th>' . _('Payee Name') . '</th>
				<th>' . _('Amount') . '</th>
			</tr>';
			?>
	<?php
	$i=0;   
	$sql = "SELECT voucherid,
					authorityref,
					datereq,
					label,
					payeename,
					particulars,
					lpo_no,
					invoice_no,
					amount,
					payment_voucher.process_level,
					total
				FROM payment_voucher
				LEFT JOIN payment_voucher_approval ON payment_voucher.voucherid=payment_voucher_approval.voucherno 
													AND payment_voucher.process_level = payment_voucher_approval.process_level
				WHERE payment_voucher.process_level >=2
				". $sort ."
				 ORDER BY payment_voucher.process_level ASC";
	$result = DB_query($sql);
  
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
$view='<td><a href="%s&amp;SelectedUser=%s&amp;view=1">' . _('View') . '</a></td>';

		printf('<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					'.$view.'
					</tr>',
					$myrow['voucherid'],
					$myrow['authorityref'],
					$myrow['label'],
					$LastVisitDate,
					$myrow['payeename'],
					$myrow['total'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?',
					$myrow['voucherid']);

	} //END WHILE LIST LOOP
	 echo '</table>';
	  }
	
include('includes/footer.inc');
	?>
		