<?php
include('includes/session.inc');
if (isset($_GET['TenderID'])) {
$SQLI  = DB_query("SELECT * FROM tenders a
				INNER JOIN locationusers b ON a.location= b.loccode
				INNER JOIN tenderitems c ON a.tenderid =c.tenderid	
				INNER JOIN tendersuppliers d ON c.tenderid=d.tenderid
				INNER JOIN suppliers e ON d.supplierid=e.supplierid
				INNER JOIN stockmaster f ON c.stockid=f.stockid
				WHERE a.tenderid=" .$_GET['TenderID']. "
				GROUP BY e.supplierid ");
while ($myrow4 = DB_fetch_array($SQLI)) {
	$Title = _('View Quotation Details: Number').' ' .$myrow4['quotation'];
	$date = _('Required by').' ' .$myrow4['requiredbydate'];
}
}else {
	$Title = _('View Quotation');
}
include('includes/header.inc');

$sql = DB_query("SELECT * FROM tenders a
				INNER JOIN tenderitems c ON a.tenderid =c.tenderid	
				INNER JOIN stockmaster f ON c.stockid=f.stockid
				WHERE a.tenderid=" .$_GET['TenderID']. " ");

echo '<table class="selection">';
	echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/supplier.png" title="' .
		_('Viewing Quotation Dscription and Deatails.') . '" alt="" />' . ' ' . $Title . '</p>';
		echo '<th colspan="8"><b>' .$date. '</b></th>';
		echo'</br>';
	echo '<a href="'. $RootPath . '/SupplierTendersReport.php">' .  _('Back to Supplier Tenders Inquiry'). '</a><br />
		</tr>
		<tr>
		<th>Stock Code</th>
		<th>Stock Discription</th>
		<th>UOM</th>
		<th>Quantity</th>
		</tr>';
	while ($myrow = DB_fetch_array($sql)) {
     echo'<tr>
		<td>' . ($myrow['stockid']) . '</td>
		<td>' . ($myrow['description']) . '</td>
		<td>' . ($myrow['units']) . '</td>
		<td>' . ($myrow['quantity']) . '</td>		
		</tr>';	
		}	
	echo'</table>';	
	echo'</table>';
include('includes/footer.inc');
?>