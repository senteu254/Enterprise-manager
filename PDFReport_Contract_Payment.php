<?php

ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$PaperSize = 'A4';
include('includes/PDFStarter.php');
/*$PageNumber = 1;// RChacon: PDFStarter.php sets $PageNumber = 0.*/
$id=$_GET['id'];
$pdf->addInfo('Title', _('Payment Contract Report') );
$pdf->addInfo('Subject', _('Payment Contract Report'));
$FontSize = 8;
$line_height = 13;// Recommended: $line_height = $x * $FontSize.

$ErrMsg = _('There was a problem retrieving the Leave Report line details for Active Contract Report Header Report Number') . ' ' . _('from the database');

		$sql = "SELECT *, a.Description as P_Description FROM contract_payment a 
		       INNER JOIN contract_details b ON a.ContractID=b.ContractID
			   WHERE a.ContractID=".$id."";
		$rest=DB_query($sql);
$ListCount = 0;
if (DB_num_rows($rest)>0){
//$myrow22=DB_fetch_array($rest);
//$payid=$myrow22['PaymentID'];

	/*Yes there are line items to start the ball rolling with a page header */
	include('PDFPaymentContractReportHeader.php');
$FontSize = 10;
$pdf->SetFillColor(206, 219, 226);
	while ($myrow=DB_fetch_array($rest)){
	    $Total_Amount+=$myrow['Amount_Paid'];
        $ListCount ++;
		$YPos -= $line_height;// Increment a line down for the next line item.		
		$LeftOvers = $pdf->addTextWrap($Left_Margin-20,$YPos-48,100,$FontSize, $myrow['PaymentID']);
		$LeftOvers = $pdf->addTextWrap(73,$YPos-48,300,$FontSize, $myrow['P_Description']);
		$xx =0;
		while(mb_strlen($LeftOvers)>0){
		$YPos -=12;
		$xx +=12;
		$LeftOvers = $pdf->addTextWrap(73,$YPos-48,300,$FontSize,$LeftOvers,'left');
		}
		$YPos += $xx;
		$line_height = $xx;
		$LeftOvers = $pdf->addTextWrap(410,$YPos-48,100,$FontSize, $myrow['Date_Paid']);
		$LeftOvers = $pdf->addTextWrap(525,$YPos-48,100,$FontSize, locale_number_format($myrow['Amount_Paid'],2));


		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFPaymentContractReportHeader.php');
		} //end if need a new page headed up
	}// Ends while there are line items to print out.

	if ($YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('PDFPaymentContractReportHeader.php');
	} //end if need a new page headed up

} /*end if there are line details to show on the quotation*/
$pdf->line($Page_Width-$Right_Margin+10, $YPos=$line_height+20, $Left_Margin-20, $YPos=$line_height+20);
$original ='Total Amount Paid ';
$status =locale_number_format($Total_Amount,2);

$LeftOvers = $pdf->addTextWrap($Left_Margin+470,$$YPos=$line_height,400,$FontSize,' ' . _($status));
$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos=$line_height,400,$FontSize,' ' . _($original));				
    $pdf->OutputI($_SESSION['DatabaseName'] . '_Payment_Contract_Report_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
	
ob_end_flush();
?>
