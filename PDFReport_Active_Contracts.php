<?php

ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$PaperSize = 'A4';
include('includes/PDFStarter.php');
/*$PageNumber = 1;// RChacon: PDFStarter.php sets $PageNumber = 0.*/
$pdf->addInfo('Title', _('Active Contract Report') );
$pdf->addInfo('Subject', _('Active Contract Report'));
$FontSize = 8;
$line_height = 13;// Recommended: $line_height = $x * $FontSize.
//$_POST['PV_Level']=$_GET['level'];
//$_POST['Yearend']=$_GET['year'];
//$_POST['supname']=$_GET['supplier'];
//$_POST['ref']=$_GET['Ref'];
// $sort =" AND authorityref " . LIKE . " '%". $_POST['Yearend'] ."%' AND authorityref " . LIKE . " '%". $_POST['ref'] ."%'";
 //$sort =" AND payeename " . LIKE . " '%". $_POST['supname'] ."%' AND authorityref " . LIKE . " '%". $_POST['Yearend'] ."%' AND authorityref " . LIKE . " '%". $_POST['ref'] ."%'";
$ErrMsg = _('There was a problem retrieving the Leave Report line details for Active Contract Report Header Report Number') . ' ' . _('from the database');

		$sql = "SELECT  * FROM contract_details a
				inner join contract_assignment b on a.ContractID = b.ContractID
				inner join suppliers c on b.SupplierID = c.SupplierID";
			$rest=DB_query($sql);

$ListCount = 0;
if (DB_num_rows($rest)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('PDFActiveContractReportHeader.php');
$FontSize = 6;

	while ($myrow=DB_fetch_array($rest)){
        $ListCount ++;
		$YPos -= $line_height;// Increment a line down for the next line item.		
		
		$LeftOvers = $pdf->addTextWrap($Left_Margin-20,$YPos,100,$FontSize, $myrow['Contract_Number']);
		$LeftOvers = $pdf->addTextWrap(73,$YPos,320,$FontSize, $myrow['Contract_Name']);
		$LeftOvers = $pdf->addTextWrap(310,$YPos,200,$FontSize, $myrow['suppname']);
		$LeftOvers = $pdf->addTextWrap(500,$YPos,40,$FontSize, $myrow['Currency']);
		$LeftOvers = $pdf->addTextWrap(535,$YPos,100,$FontSize, locale_number_format($myrow['Amount'],2));


		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFActiveContractReportHeader.php');
		} //end if need a new page headed up
	}// Ends while there are line items to print out.

	if ($YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('PDFActiveContractReportHeader.php');
	} //end if need a new page headed up

} /*end if there are line details to show on the quotation*/

    $pdf->OutputI($_SESSION['DatabaseName'] . '_Active_Contract_Report_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
	
ob_end_flush();
?>
