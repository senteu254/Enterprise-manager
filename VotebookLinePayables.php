<?php
include('includes/session.inc');
$_POST['Yearend']=$_GET['fyear'];
if (isset($_GET['VoteCode'])) {
$SQLI  = DB_query("SELECT Votehead FROM voteheadmaintenance WHERE Votecode=" .$_GET['VoteCode']. "");
while ($myrow4 = DB_fetch_array($SQLI)) {
	$Title = _('View Daily Payment Voucher Expenses').' ' .$myrow4['Votehead'];
}
}else {
	$Title = _('View Voteheads');
}
include('includes/header.inc');
/*$sql = DB_query("SELECT * ,
                  SUM(a.amt) AS amount,
				  a.invoice,
                  b.suppreference,
				  c.lpo_No,
				  c.date,
				  c.commitments,
				  c.decommitment,
				  c.Fyear				  
                  FROM suppallocs a
                  RIGHT JOIN supptrans b ON a.invoice=b.suppreference
				  INNER JOIN commitment c ON b.OrderNo=c.lpo_No
                  WHERE c.voted_Item = '" .$_GET['VoteCode']. "'
				  AND c.Fyear='".$_POST['Yearend']."'
				  GROUP BY c.lpo_No");*/
$sql = DB_query("SELECT *,b.total FROM pvpaymenttrans a
                 LEFT JOIN payment_voucher b ON a.Voucherid=b.voucherid		  
                 WHERE a.VoteCode = '" .$_GET['VoteCode']. "'
				 AND a.Fy='".$_POST['Yearend']."'");

echo '<table class="selection">';
	echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/supplier.png" title="' .
		_('Viewing LPO AND LSO.') . '" alt="" />' . ' ' . $Title . '</p>';
	echo '<a href="'. $RootPath . '/daily_payment_voucher_Expenses.php">' .  _('Back to Daily Votebook Status Inquiry'). '</a><br />';
	echo '<th colspan="8"><b>' .  _('Votehead Detail'). '</b></th>
		</tr>
		<tr>
		<th>Date Paid</th>
		<th>Voucherid</th>
		<th>Book No.</th>
		<th>Payee Name</th>
		<th>Amount Paid</th>
		<th>Financial Year</th>
		</tr>';
	$k=0;
	while ($myrow = DB_fetch_array($sql)) {
	$sumcommitments+=$myrow['commitments'];
	$payables+=str_replace(',','',$myrow['Amount']);//$myrow['Amount'];
    if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k++;
		}
	echo'<td>'.($myrow['date_paid']).'</td>
		<td>' . ($myrow['Voucherid']) . '</td>
		<td>' . ($myrow['authorityref']) . '</td>
		<td>' . ($myrow['SuppNo']) . '</td>
		<td>' .$myrow['Amount'] . '</td>
		<td>' . $myrow['Fy'] . '</td>
		</tr>';	
		}
	echo'<tr>
	    <td></td>
		<td></td>
		<td></td>
		<td><b>TOTAL AMOUNT</b></td>
		<td><b>' . locale_number_format($payables,2) . '<b></td>
		</tr>';
	//echo '<tr><td colspan="3"></td><th class="number">'.locale_number_format($sumcommitments, 2).'</th>';
	//echo '<td colspan="2"></td><th class="number">'.locale_number_format($payables, 2).'</th></tr>';
	echo'</table>';	
	echo'</table>';
include('includes/footer.inc');
?>