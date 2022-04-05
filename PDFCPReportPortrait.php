<?php
/*	$Id: PDFQuotationPortrait.php 4491 2011-02-15 06:31:08Z daintree $ */

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/
ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

//Get Out if we have no order number to work with
If (!isset($_GET['id']) || $_GET['id']==""){
        $Title = _('Select Company Property Report To Print');
        include('includes/header.inc');
        echo '<div class="centre">
				<br />
				<br />
				<br />';
        prnMsg( _('Select a Company Property Report to Print before calling this page') , 'error');
        echo '<br />
				<br />
				<br />
				<table class="table_index">
				<tr>
					<td class="menu_group_item">
						<ul><li><a href="index.php?Application=HR&Ref=CPReports">' . _('Company Property') . '</a></li>
						</ul>
					</td>
				</tr>
				</table>
				</div>
				<br />
				<br />
				<br />';
        include('includes/footer.inc');
        exit();
}

/*retrieve the order details from the database to print */
$ErrMsg = _('There was a problem retrieving the Company Property header details for Request Number') . ' ' . $_GET['id'] . ' ' . _('from the database');

$sql = "SELECT *,COUNT(personal_no) AS total FROM employee a
        INNER JOIN company_property b ON a.emp_id = b.personal_no WHERE a.emp_id='".$_GET[id]."'";

$result=DB_query($sql, $ErrMsg);

//If there are no rows, there's a problem.
if (DB_num_rows($result)==0){
	$Title = _('Print Company Property Report Error');
	include('includes/header.inc');
	 echo '<div class="centre">
			<br />
			<br />
			<br />';
	prnMsg( _('Unable to Locate Company Property Report Number') . ' : ' . $_GET['id'] . ' ', 'error');
	echo '<br />
			<br />
			<br />
			<table class="table_index">
			<tr>
				<td class="menu_group_item">
					<ul><li><a href="index.php?Application=HR&Ref=CPReports">' . _('Company Property') . '</a></li></ul>
				</td>
			</tr>
			</table>
			</div>
			<br />
			<br />
			<br />';
	include('includes/footer.inc');
	exit;
} else{ /*There is only one order header returned - thats good! */
	$myrow = DB_fetch_array($result);
}
/*retrieve the order details from the database to print */

/* Then there's an order to print and its not been printed already (or its been flagged for reprinting/ge_Width=807;
)
LETS GO */
$PaperSize = 'A4';
include('includes/PDFStarter.php');
/*$PageNumber = 1;// RChacon: PDFStarter.php sets $PageNumber = 0.*/
$pdf->addInfo('Title', _('Company Property Report') );
$pdf->addInfo('Subject', _('Company Property Report') . ' ' . $_GET['id']);
$FontSize = 12;
$line_height = 12;// Recommended: $line_height = $x * $FontSize.

/* Now ... Has the order got any line items still outstanding to be invoiced */

$ErrMsg = _('There was a problem retrieving the Company Property Report line details for Company Property Report Number') . ' ' .
	$_GET['id'] . ' ' . _('from the database');

$sql = "SELECT * FROM company_property a 
INNER JOIN employee b ON a.personal_no = b.emp_id
WHERE  b.emp_id='" . $_GET['id'] . "'";

$result=DB_query($sql, $ErrMsg);

$ListCount = 0;

if (DB_num_rows($result)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('PDFCPReportHeader.php');

	while ($myrow2=DB_fetch_array($result)){

        $ListCount ++;

		$YPos -= $line_height;// Increment a line down for the next line item.

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFCPReportHeader.php');
		} //end if need a new page headed up

		$FontSize = 10;// Font size for the line item.

		$LeftOvers = $pdf->addText($Left_Margin, $YPos+$FontSize, $FontSize, $myrow2['type']);
		$LeftOvers = $pdf->addText(150, $YPos+$FontSize, $FontSize, $myrow2['description']);
		$LeftOvers = $pdf->addText(300, $YPos+$FontSize, $FontSize, $myrow2['issue_date']);
		$LeftOvers = $pdf->addText(380, $YPos+$FontSize, $FontSize, $myrow2['return_date']);
		$LeftOvers = $pdf->addText(460, $YPos+$FontSize, $FontSize, $myrow2['cost']);


	}// Ends while there are line items to print out.

	if ($YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('PDFCPReportHeader.php');
	} //end if need a new page headed up

} /*end if there are line details to show on the quotation*/


if ($ListCount == 0){
        $Title = _('Print Company Property Report Error');
        include('includes/header.inc');
        echo '<p>' .  _('There were no Company Property Reports') . '. ' . _('The Company Property Report cannot be printed').
                '<br /><a href="index.php?Application=HR&Ref=CPReports">' .  _('Print Another Company Property Report');
        include('includes/footer.inc');
	exit;
} else {
    $pdf->OutputI($_SESSION['DatabaseName'] . '_Company Property Report_' . $_GET['id'] . '_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
}
ob_end_flush();
?>
