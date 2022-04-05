<?php
/*	$Id: PDFQuotationPortrait.php 4491 2011-02-15 06:31:08Z daintree $ */

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/
ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

//Get Out if we have no order number to work with
If (!isset($_GET['id']) || $_GET['id']==""){
        $Title = _('Select Report To Print');
        include('includes/header.inc');
        echo '<div class="centre">
				<br />
				<br />
				<br />';
        prnMsg( _('Select a Report to Print before calling this page') , 'error');
        echo '<br />
				<br />
				<br />
				<table class="table_index">
				<tr>
					<td class="menu_group_item">
						<ul><li><a href="index.php">' . _('Main Menu') . '</a></li>
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
$ErrMsg = _('There was a problem retrieving the  header details for Request Number') . ' ' . $_GET['id'] . ' ' . _('from the database');

$sql = "SELECT *
		FROM qanonconformingproducts
		WHERE id='" . $_GET['id'] . "'";

$result=DB_query($sql, $ErrMsg);

//If there are no rows, there's a problem.
if (DB_num_rows($result)==0){
	$Title = _('Print Report Error');
	include('includes/header.inc');
	 echo '<div class="centre">
			<br />
			<br />
			<br />';
	prnMsg( _('Unable to Locate Report Number') . ' : ' . $_GET['id'] . ' ', 'error');
	echo '<br />
			<br />
			<br />
			<table class="table_index">
			<tr>
				<td class="menu_group_item">
					<ul><li><a href="index.php">' . _('Main Menu') . '</a></li></ul>
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
$pdf->addInfo('Title', _('Non-conforming Products Report') );
$pdf->addInfo('Subject', _('Non-conforming Products Report') . ' ' . $_GET['id']);
$FontSize = 12;
$line_height = 12;// Recommended: $line_height = $x * $FontSize.

/* Now ... Has the order got any line items still outstanding to be invoiced */

$ErrMsg = _('There was a problem retrieving the Report line details for Report Number') . ' ' .
	$_GET['id'] . ' ' . _('from the database');

$sql = "SELECT *
		FROM qanonconformingremarks
		WHERE refid='" . $_GET['id'] . "' ORDER BY approver ASC";

$result=DB_query($sql, $ErrMsg);

$ListCount = 0;
$Title = "";

if (DB_num_rows($result)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('includes/PDFConformanceReportHeader.php');

	while ($myrow2=DB_fetch_array($result)){

        $ListCount ++;
		$YPos -= $line_height;// Increment a line down for the next line item.

		$FontSize = 10;// Font size for the line item.
		$Space = 40;
		if($Title !=$myrow2['approvertitle']){
		//$LeftOvers = $pdf->addText($Left_Margin, $YPos+$FontSize, $FontSize, $myrow2['approvertitle'],'','', 'center');
		$pdf->SetFont('','B');
		$pdf->line($Page_Width-$Right_Margin, $YPos+$FontSize+10, $Left_Margin, $YPos+$FontSize+10);
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $myrow2['approvertitle'], 'left');
		//$Title =$myrow2['approvertitle'];
		$pdf->line($Page_Width-$Right_Margin, $YPos+$FontSize-3, $Left_Margin, $YPos+$FontSize-3);
		$YPos -= $line_height;// Increment a line down for the next line item.
		$pdf->SetFont('');
		//$Space = 30;
		}
		$inline =0;
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $myrow2['remarks']);
		if (mb_strlen($LeftOvers)>0) {
				$YPos -= $line_height;// Increment a line down for the next line item.
				$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $LeftOvers);
				$inline = $line_height;
				if (mb_strlen($LeftOvers)>0) {
					$YPos -= $line_height;// Increment a line down for the next line item.
					$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $LeftOvers);
					$inline +=$line_height;
						if (mb_strlen($LeftOvers)>0) {
						$YPos -= $line_height;// Increment a line down for the next line item.
						$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $LeftOvers);
						$inline +=$line_height;
							if (mb_strlen($LeftOvers)>0) {
							$YPos -= $line_height;// Increment a line down for the next line item.
							$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $LeftOvers);
							$inline +=$line_height;
								if (mb_strlen($LeftOvers)>0) {
								$YPos -= $line_height;// Increment a line down for the next line item.
								$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $LeftOvers);
								$inline +=$line_height;
								/*If there is some of the InvText leftover after 3 lines 200 wide then it is not printed :( */
							}
						}
					}
				}
			}
		$YPos -= $Space -$inline;
		if($myrow2['action'] !=""){
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, 'ACTION: ');
		$pdf->SetFont('');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+50,$YPos+$FontSize,520,$FontSize, $myrow2['action']);
		$YPos -= $line_height;// Increment a line down for the next line item.
		}
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,8, ucwords(strtolower($myrow2['approvername'])));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,8, ConvertSQLDateTime($myrow2['remarkdate']),'right');
		$pdf->SetFont('','');
		$YPos -= 3;
		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('includes/PDFConformanceReportHeader.php');
		} //end if need a new page headed up

	}// Ends while there are line items to print out.

	if ($YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('includes/PDFConformanceReportHeader.php');
	} //end if need a new page headed up

} /*end if there are line details to show on the quotation*/


if ($ListCount == 0){
        $Title = _('Print Report Error');
        include('includes/header.inc');
        echo '<p>' .  _('There were no Reports') . '. ' . _('The Report cannot be printed').
                '<br /><a href="index.php">' .  _('Print Another Report');
        include('includes/footer.inc');
	exit;
} else {
    $pdf->OutputI($_SESSION['DatabaseName'] . '_NonConformanceReport_' . $_GET['id'] . '_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
}
ob_end_flush();
?>
