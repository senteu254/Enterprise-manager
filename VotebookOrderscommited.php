<?php
include('includes/session.inc');
$_POST['Yearend']=$_GET['fyear'];
if (isset($_GET['VoteCode'])) {
$SQLI  = DB_query("SELECT Votehead FROM voteheadmaintenance WHERE Votecode=" .$_GET['VoteCode']. "");
while ($myrow4 = DB_fetch_array($SQLI)) {
	$Title = _('View lPO/lSO Orders From Votehead').' ' .$myrow4['Votehead'];
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
$sql = DB_query("SELECT * ,
				  a.lpo_No,
				  a.date,
				  a.commitments,
				  a.decommitment,
				  a.Fyear				  
                  FROM commitment a			  
                  WHERE a.voted_Item = '" .$_GET['VoteCode']. "'
				  AND a.Fyear='".$_POST['Yearend']."'
				  GROUP BY a.lpo_No");

echo '<table class="selection">';
	echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/supplier.png" title="' .
		_('Viewing LPO AND LSO.') . '" alt="" />' . ' ' . $Title . '</p>';
	echo '<a href="'. $RootPath . '/Votebook_Expenses.php">' .  _('Back to Votehead Inquiry'). '</a><br />';
	echo '<th colspan="8"><b>' .  _('Votehead Detail'). '</b></th>
		</tr>
		<tr>
		<th>Date Committed</th>
		<th>LPO/LSO No.</th>
		<th>Supplier Name</th>
		<th>Commitments</th>
		<th>Decommitment</th>
		</tr>';
	
	while ($myrow = DB_fetch_array($sql)) {
	$sumcommitments+=$myrow['commitments'];
	$payables+=$myrow['amount'];
     echo'<tr>
		<td>' . ($myrow['date']) . '</td>
		<td>' . ($myrow['lpo_No']) . '</td>
		<td>' . ($myrow['payee_Name']) . '</td>
		<td>' . locale_number_format($myrow['commitments'],2) . '</td>
		<td>' . locale_number_format($myrow['decommitment'],2) . '</td>	
		</tr>';	
		}
	//echo '<tr><td colspan="3"></td><th class="number">'.locale_number_format($sumcommitments, 2).'</th>';
	//echo '<td colspan="2"></td><th class="number">'.locale_number_format($payables, 2).'</th></tr>';
	echo'</table>';	
	echo'</table>';
include('includes/footer.inc');
?>