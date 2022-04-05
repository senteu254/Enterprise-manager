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
				GROUP BY e.supplierid");
while ($myrow4 = DB_fetch_array($SQLI)) {
	$Title = _('View Quotation Details: No.').' ' .$myrow4['quotation'];
		$date = _('Required by').' ' .$myrow4['requiredbydate'];
}
}else {
	$Title = _('View Quotation');
}
include('includes/header.inc');

$sql9 = DB_query("SELECT a.supplierid,
                         c.quotation,
                         b.suppname,
						 b.email,
						 b.telephone,
						 c.tenderid
						 FROM tendersuppliers a
						INNER JOIN suppliers b ON a.supplierid=b.supplierid
						INNER JOIN tenders c ON a.tenderid=c.tenderid
						WHERE a.tenderid=" .$_GET['TenderID']. "
						ORDER BY a.supplierid");

echo '<table class="selection">';
	echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/supplier.png" title="' .
		_('Viewing Quatation Dscription .') . '" alt="" />' . ' ' . $Title . '</p>';
	echo '<a href="'. $RootPath . '/SupplierTendersReport.php">' .  _('Back to Supplier Tenders Inquiry'). '</a><br />';
	echo '<th colspan="8"><b>' .  $date. '</b></th>
		</tr>
		<tr>
		<th>Code</th>
		<th>Supplier Name</th>
		<th>Telephone No.</th>
		<th>Email Address</th>
		<th>Print</th>
		</tr>';
	while ($myrow = DB_fetch_array($sql9)) {
	 $Print  = $RootPath . '/PDFsuppliersquotationReport.php?SupplierID=' . $myrow['supplierid'].'&TenderID=' . $myrow['tenderid'];
     echo'<tr>
		<td>' . ($myrow['supplierid']) . '</td>
		<td>' . ($myrow['suppname']) . '</td>
		<td>' . ($myrow['telephone']) . '</td>	
		<td>' . ($myrow['email']) . '</td>
		<td><a href="' . $Print . '">' . _('Print Quotation') . '</a></td>
		</tr>';	
		}	
	echo'</table>';	
	echo'</table>';
include('includes/footer.inc');
?>