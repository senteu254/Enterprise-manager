<?php
/*	$Id: PDFQuotationPortrait.php 4491 2011-02-15 06:31:08Z daintree $ */

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/
ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

//Get Out if we have no order number to work with
If (!isset($_GET['id']) || $_GET['id']==""){
        $Title = _('Select Employee medical Report To Print');
        include('includes/header.inc');
        echo '<div class="centre">
				<br />
				<br />
				<br />';
        prnMsg( _('Select a Employee medical Report to Print before calling this page') , 'error');
        echo '<br />
				<br />
				<br />
				<table class="table_index">
				<tr>
					<td class="menu_group_item">
						<ul><li><a href="kofcerp/IndividualReport.php">' . _('Medical Report') . '</a></li>
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
$ErrMsg = _('There was a problem retrieving the Employee medical  header details for Request Number') . ' ' . $_GET['id'] . ' ' . _('from the database');

$sql = "SELECT  *,employee.emp_id as id,departments.description as dep FROM employee,medical,departments,prescription
WHERE  employee.emp_id=medical.personal_no
AND departments.departmentid=employee.id_dept
AND medical.id=prescription.med_id
AND employee.emp_id='".$_GET['id']."' ";
$result = DB_query($sql,$ErrMsg);
		
//If there are no rows, there's a problem.
if (DB_num_rows($result)==0){
	$Title = _('Print Employee medical Report Error');
	include('includes/header.inc');
	 echo '<div class="centre">
			<br />
			<br />
			<br />';
	prnMsg( _('Unable to Locate Employee medical Report Number') . ' : ' . $_GET['id'] . ' ', 'error');
	echo '<br />
			<br />
			<br />
			<table class="table_index">
			<tr>
				<td class="menu_group_item">
					<ul><li><a href="kofcerp/IndividualReport.php">' . _('Medical Report') . '</a></li></ul>
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
$pdf->addInfo('Title', _('Employee medical Report') );
$pdf->addInfo('Subject', _('Employee medical Report') . ' ' . $_GET['id']);
$FontSize = 12;
$line_height = 12;// Recommended: $line_height = $x * $FontSize.

/* Now ... Has the order got any line items still outstanding to be invoiced */

$ErrMsg = _('There was a problem retrieving the Employee medical Report line details for Employee medical Report Number') . ' ' .
	$_GET['id'] . ' ' . _('from the database');
	
$sql = "SELECT  *,employee.emp_id as id FROM employee,medical,departments
WHERE  employee.emp_id=medical.personal_no
AND departments.departmentid=employee.id_dept
AND employee.emp_id='".$_GET['id']."' ";

$sql1 = "SELECT  *,employee.emp_id as id FROM employee,medical,prescription
WHERE  employee.emp_id=medical.personal_no
AND medical.id=prescription.med_id
AND employee.emp_id='".$_GET['id']."' ";

$result=DB_query($sql, $ErrMsg);
$result1=DB_query($sql1, $ErrMsg);
$ListCount = 0;

if (DB_num_rows($result)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('PDFMEDReportHeader.php');
while ($myrow2=DB_fetch_array($result)){
        $ListCount ++;

		$YPos -= $line_height;// Increment a line down for the next line item.

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFMEDReportHeader.php');
} //end if need a new page headed up
		$FontSize = 10;// Font size for the line item.

		$LeftOvers = $pdf->addText($Left_Margin, $YPos+$FontSize, $FontSize, $myrow2['doctor']);
		$LeftOvers = $pdf->addText(120, $YPos+$FontSize, $FontSize, $myrow2['date']);
		$LeftOvers = $pdf->addText(200, $YPos+$FontSize, $FontSize, $myrow2['diagnosis']);
		$row=0;
		while ($myrow2=DB_fetch_array($result1)){
        $LeftOvers = $pdf->addText(400, $YPos+$FontSize-$row, $FontSize, $myrow2['qty'].' '.$myrow2['description'].'-'.$myrow2['dosage']);
		$row=+10;
	}


	}// Ends while there are line items to print out.

	if ($YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('PDFMEDReportHeader.php');
} //end if need a new page headed up

} /*end if there are line details to show on the quotation*/


if ($ListCount == 0){
        $Title = _('Print Employee medical Report Error');
        include('includes/header.inc');
        echo '<p>' .  _('There were no Employee medical Reports') . '. ' . _('The Employee medical Report cannot be printed').
                '<br /><a href="kofcerp/IndividualReport.php">' .  _('Print Another Employee medical Report');
        include('includes/footer.inc');
	exit;
} else {
    $pdf->OutputI($_SESSION['DatabaseName'] . '_Employee medical Report_' . $_GET['id'] . '_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
}
ob_end_flush();
?>
