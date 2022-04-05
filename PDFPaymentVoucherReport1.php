<?php

ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$PaperSize = 'A4';
include('includes/PDFStarter.php');
/*$PageNumber = 1;// RChacon: PDFStarter.php sets $PageNumber = 0.*/
$pdf->addInfo('Title', _('PV Report Header Report') );
$pdf->addInfo('Subject', _('Pv ReportHeader Report'));
$FontSize = 13;
$line_height = 13;// Recommended: $line_height = $x * $FontSize.
$_POST['PV_Level']=$_GET['level'];
$_POST['Yearend']=$_GET['year'];
 $sort =" AND authorityref " . LIKE . " '%". $_POST['Yearend'] ."%'";

$ErrMsg = _('There was a problem retrieving the Leave Report line details forPV Report Header Report Number') . ' ' . _('from the database');

		if ($_POST['PV_Level'] == 'All') {
		$sql = "SELECT a.voucherid,
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
					b.levelcode,
					b.pvrole
				FROM payment_voucher a 
				INNER JOIN pvlevel b ON a.process_level=b.levelcode
			    WHERE a.process_level >0
				".$sort."
				ORDER BY a.process_level ASC, a.voucherid ASC";
       }else{		
		 $sql = "SELECT a.voucherid,
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
					b.levelcode,
					b.pvrole
				FROM payment_voucher a 
				INNER JOIN pvlevel b ON a.process_level=b.levelcode
			    WHERE a.process_level >0
				AND b.levelcode='" . $_POST['PV_Level'] . "'
				".$sort."
				ORDER BY a.process_level ASC, a.voucherid ASC";
				}
			$rest=DB_query($sql);

$ListCount = 0;
if (DB_num_rows($rest)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('PDFPVReportHeader.php');
$FontSize = 10;
$pdf->SetFillColor(206, 219, 226);
$dept ="";
$user ="";
	while ($myrow=DB_fetch_array($rest)){
        $ListCount ++;
		$YPos -= $line_height;// Increment a line down for the next line item.		
		
		$LeftOvers = $pdf->addTextWrap($Left_Margin-20,$YPos,100,$FontSize, $myrow['datereq']);
		$LeftOvers = $pdf->addTextWrap(73,$YPos,110,$FontSize, $myrow['label']);
		$LeftOvers = $pdf->addTextWrap(110,$YPos,200,$FontSize, $myrow['authorityref']);
		$LeftOvers = $pdf->addTextWrap(210,$YPos,200,$FontSize, $myrow['payeename']);
		$LeftOvers = $pdf->addTextWrap(410,$YPos,40,$FontSize, $myrow['total']);
		$LeftOvers = $pdf->addTextWrap(475,$YPos,100,$FontSize, $myrow['pvrole']);


		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFPVReportHeader.php');
		} //end if need a new page headed up
	}// Ends while there are line items to print out.

	if ($YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('PDFPVReportHeader.php');
	} //end if need a new page headed up

} /*end if there are line details to show on the quotation*/

    $pdf->OutputD($_SESSION['DatabaseName'] . '_Payment_Voucher_Report_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
	
ob_end_flush();
?>
