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
$pdf->addInfo('Title', _('Vacation Leave Report') );
$pdf->addInfo('Subject', _('Vacation Leave Report') );
$FontSize = 12;
$line_height = 12;// Recommended: $line_height = $x * $FontSize.

/* Now ... Has the order got any line items still outstanding to be invoiced */

$ErrMsg = _('There was a problem retrieving the Vacation Leave Report line details for Vacation Leave Report Number') . ' ' .
	$_GET['id'] . ' ' . _('from the database');

$sql = "(SELECT *,employee.emp_id as id FROM employee, vacation_log, departments,general_manager 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.emp_id = vacation_log.emp_id
									AND vacation_log.eddate < '".date('Y-m-d')."'
									AND general_manager.gm = vacation_log.general_manager
									AND general_manager.gm ='4'
									order by employee.emp_id )
									UNION
									(SELECT *,employee.emp_id as id FROM employee, vacation_log, departments,program_head 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.emp_id = vacation_log.emp_id
									AND vacation_log.eddate < '".date('Y-m-d')."'
									AND program_head.prog_head = vacation_log.prog_head
									AND program_head.prog_head ='4'
									order by employee.emp_id )
									UNION
									(SELECT *,employee.emp_id as id FROM employee, vacation_log, departments,managing_director 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.emp_id = vacation_log.emp_id
									AND vacation_log.eddate < '".date('Y-m-d')."'
									AND managing_director.md = vacation_log.managing_director
									AND managing_director.md ='2'
									order by employee.emp_id )";

$result=DB_query($sql, $ErrMsg);

$ListCount = 0;

if (DB_num_rows($result)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('PDFVLBackReportHeader.php');

	while ($myrow2=DB_fetch_array($result)){

        $ListCount ++;

		$YPos -= $line_height;// Increment a line down for the next line item.

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFVLBackReportHeader.php');
		} //end if need a new page headed up

		$FontSize = 10;// Font size for the line item.

		$LeftOvers = $pdf->addText($Left_Margin, $YPos+$FontSize, $FontSize, $myrow2['id']);
		$LeftOvers = $pdf->addText(100, $YPos+$FontSize, $FontSize, $myrow2['emp_fname'].' '.$myrow2['emp_lname']);
		$LeftOvers = $pdf->addText(200, $YPos+$FontSize, $FontSize, $myrow2['description']);
		$LeftOvers = $pdf->addText(360, $YPos+$FontSize, $FontSize, $myrow2['sdate']);
		$LeftOvers = $pdf->addText(420, $YPos+$FontSize, $FontSize, $myrow2['eddate']);
		$LeftOvers = $pdf->addText(480, $YPos+$FontSize, $FontSize, $myrow2['nodays']);



	}// Ends while there are line items to print out.

	if ($YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('PDFVLBackReportHeader.php');
	} //end if need a new page headed up

} /*end if there are line details to show on the quotation*/


if ($ListCount == 0){
        $Title = _('Print Vacation Leave Report Error');
        include('includes/header.inc');
        echo '<p>' .  _('There were no Vacation Leave Reports') . '. ' . _('The Vacation Leave Report cannot be printed').
                '<br /><a href="index.php?Application=HR&Ref=SLReports">' .  _('Print Another Vacation Leave Report');
        include('includes/footer.inc');
	exit;
} else {
    $pdf->OutputI($_SESSION['DatabaseName'] . '_Vacation Leave Report_'  . '_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
}
ob_end_flush();
?>
