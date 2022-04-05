<?php
/*	$Id: PDFQuotationPortrait.php 4491 2011-02-15 06:31:08Z daintree $ */

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/
ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');


/*retrieve the order details from the database to print */

/* Then there's an order to print and its not been printed already (or its been flagged for reprinting/ge_Width=807;
)
LETS GO */
$PaperSize = 'A4';
include('includes/PDFStarter.php');
/*$PageNumber = 1;// RChacon: PDFStarter.php sets $PageNumber = 0.*/
$pdf->addInfo('Title', _('Company Property Report') );
$pdf->addInfo('Subject', _('Company Property Report'));
$FontSize = 12;
$line_height = 12;// Recommended: $line_height = $x * $FontSize.

/* Now ... Has the order got any line items still outstanding to be invoiced */

$ErrMsg = _('There was a problem retrieving the Company Property Report line details ') .  ' ' . _('from the database').' '. _('The query that failed is');

$sql = "SELECT emp_lname,emp_fname,departments.description,personal_no,type,cost,issue_date,return_date FROM company_property,employee,departments 
WHERE company_property.personal_no = employee.emp_id
AND departments.departmentid = employee.id_dept";

$result=DB_query($sql, $ErrMsg);

$ListCount = 0;

if (DB_num_rows($result)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('PDFCPALLReportHeader.php');

	while ($myrow2=DB_fetch_array($result)){

        $ListCount ++;

		$YPos -= $line_height;// Increment a line down for the next line item.

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFCPALLReportHeader.php');
		} //end if need a new page headed up

		$FontSize = 10;// Font size for the line item.

		$LeftOvers = $pdf->addText($Left_Margin, $YPos+$FontSize, $FontSize, $myrow2['personal_no']);
		$LeftOvers = $pdf->addText(90, $YPos+$FontSize, $FontSize, $myrow2['emp_fname']);
		$LeftOvers = $pdf->addText(130, $YPos+$FontSize, $FontSize, $myrow2['description']);
		$LeftOvers = $pdf->addText(280, $YPos+$FontSize, $FontSize, $myrow2['type']);
		$LeftOvers = $pdf->addText(380, $YPos+$FontSize, $FontSize, $myrow2['issue_date']);
		$LeftOvers = $pdf->addText(440, $YPos+$FontSize, $FontSize, $myrow2['return_date']);
		$LeftOvers = $pdf->addText(500, $YPos+$FontSize, $FontSize, $myrow2['cost']);



	}// Ends while there are line items to print out.

	if ($YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('PDFCPALLReportHeader.php');
	} //end if need a new page headed up

} /*end if there are line details to show on the quotation*/


if ($ListCount == 0){
        $Title = _('Print Company Property Report Error');
        include('includes/header.inc');
        echo '<p>' .  _('There were no Company Property Reports') . '. ' . _('The Company Property Report cannot be printed').
                '<br /><a href="index.php?Application=HR&Ref=CPALLReports">' .  _('Print Another Company Property Report');
        include('includes/footer.inc');
	exit;
} else {
    $pdf->OutputI($_SESSION['DatabaseName'] . '_Company Property Report_'  . '_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
}
ob_end_flush();
?>
