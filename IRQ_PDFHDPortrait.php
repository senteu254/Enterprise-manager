<?php
/*	$Id: PDFQuotationPortrait.php 4491 2011-02-15 06:31:08Z daintree $ */

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/
ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

//Get Out if we have no order number to work with
If (!isset($_GET['id']) || $_GET['id']==""){
        $Title = _('Select Half-Day Permission To Print');
        include('includes/header.inc');
        echo '<div class="centre">
				<br />
				<br />
				<br />';
        prnMsg( _('Select a Half-Day Permission to Print before calling this page') , 'error');
        echo '<br />
				<br />
				<br />
				<table class="table_index">
				<tr>
					<td class="menu_group_item">
						<ul><li><a href="index.php?Application=HR&Ref=HdAppAll' . _(' Half-Day All Approve') . '</a></li>
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
$ErrMsg = _('There was a problem retrieving the Vacation header details for this Half-Day Permission Number') . ' ' . $_GET['id'] . ' ' . _('from the database');
$sql = "SELECT *,b.emp_id as id FROM hd_log a
							INNER JOIN employee b on a.emp_id = b.emp_id
							INNER JOIN departments c on b.id_dept = c.departmentid
							INNER JOIN section d ON c.departmentid = d.id_dept
							WHERE a.hd_id='" . $_GET['id'] . "' ";

$result=DB_query($sql, $ErrMsg);

	
//If there are no rows, there's a problem.
if (DB_num_rows($result)==0){
	$Title = _('Print Half-Day Permission Request Error');
	include('includes/header.inc');
	 echo '<div class="centre">
			<br />
			<br />
			<br />';
	prnMsg( _('Unable to Locate Half-Day Permission Number') . ' : ' . $_GET['id'] . ' ', 'error');
	echo '<br />
			<br />
			<br />
			<table class="table_index">
			<tr>
				<td class="menu_group_item">
					<ul><li><a href="index.php?Application=HR&Ref=HdAppAll">' . _(' Half-Day All Approve') . '</a></li></ul>
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
$pdf->addInfo('Title', _('Half-Day Permission') );
$pdf->addInfo('Subject', _('Half-Day Permission') . ' ' . $_GET['id']);
$FontSize = 12;
$line_height = 12;// Recommended: $line_height = $x * $FontSize.

	/*Yes there are line items to start the ball rolling with a page header */
	include ('PDFHDHeader.php');

		$FontSize = 10;// Font size for the line item.
        $LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize, _('Name.'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-20,100,$FontSize, _('Personal No.'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-40,100,$FontSize, _('Date of Filing.'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-60,100,$FontSize, _('Leave Applied For.'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-80,100,$FontSize, _('Department'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-100,100,$FontSize, _('Section'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-120,100,$FontSize, _('Start Time:'));
		$LeftOvers = $pdf->addTextWrap(300,$YPos-120,100,$FontSize, _('End Time.'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-140,300,$FontSize, _('Contact Address while on leave'));
		
		$LeftOvers = $pdf->addTextWrap(180,$YPos,300,$FontSize, $myrow['emp_fname'].'&nbsp;'.$myrow['emp_mname'].'&nbsp;'.$myrow['emp_lname']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-20,300,$FontSize, $myrow['id']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-40,300,$FontSize, $myrow['date']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-60,300,$FontSize, $myrow['leavetype']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-80,300,$FontSize, $myrow['description']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-100,300,$FontSize, $myrow['section_name']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-120,100,$FontSize, $myrow['stime']);
		$LeftOvers = $pdf->addTextWrap(350,$YPos-120,100,$FontSize, $myrow['etime']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-140,300,$FontSize, $myrow['emp_add']);
		$YPos = $YPos-130;
	$pdf->addText($XPos, $YPos, $FontSize, $message.':');
	$Width2 = $Page_Width-$Right_Margin-200;// Width to print salesorders.comments.
	$LeftOver = trim($myrow['problem']);
	while(mb_strlen($LeftOver) > 1) {
		$YPos -= $FontSize;
		if ($YPos < ($Bottom_Margin)) {// Begins new page.
			include ('PDFHDHeader.php');
		}
		$LeftOver = $pdf->addTextWrap(180, $YPos, $Width2, $FontSize, $LeftOver);
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Prints company logo:
$XPos = $Page_Width/2 - 140;
$YPos=330;
$pdf->addTextWrap(0, $YPos+80, $Page_Width, 18,'--------------------------------------------------------------------------------------------------', 'center');
$pdf->addJpegFromFile($_SESSION['LogoFile'],$XPos+60,$YPos-20,0,60);

// Prints 'Quotation' title:

$pdf->addTextWrap(0, $YPos+60, $Page_Width, 18,$tt, 'center');
$pdf->addTextWrap(0, $YPos+60, $Page_Width-80, 18,'(Copy)', 'right');

$YPos +=40;
// Draws a box with round corners around 'Delivery To' info:
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $YPos-$FontSize*1, 200, $FontSize, $_SESSION['CompanyRecord']['coyname'], 'left');
$FontSize=10;
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $YPos-$FontSize*2, 200, $FontSize, $_SESSION['CompanyRecord']['regoffice1'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $YPos-$FontSize*3, 200, $FontSize,  _('Ph') . ': ' . $_SESSION['CompanyRecord']['telephone'] .' ' . _('Fax'). ': ' . $_SESSION['CompanyRecord']['fax'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $YPos-$FontSize*4, 200, $FontSize, $_SESSION['CompanyRecord']['email'], 'left');

// Prints quotation info:
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $YPos-$FontSize*1, 200, $FontSize,  _('Printed On'). ': '.date("d, M Y"), 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $YPos-$FontSize*2, 200, $FontSize, _('Ref No.'). ': '.$_GET['id'], 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $YPos-$FontSize*3, 200, $FontSize, _('Page').': '.$PageNumber, 'right');

$FontSize=10;

// Prints table header:
$YPos -= 100;
$XPos = 40;

// Draws a box with round corners around line items:
$pdf->RoundRectangle(
	$Left_Margin,// RoundRectangle $XPos.
	$YPos+$FontSize+5,// RoundRectangle $YPos.
	$Page_Width-$Left_Margin-$Right_Margin,// RoundRectangle $Width.
	$YPos+$FontSize-$Bottom_Margin+10,// RoundRectangle $Height.
	10,// RoundRectangle $RadiusX.
	10);// RoundRectangle $RadiusY.

$YPos -= $FontSize;// This is to use addTextWrap's $YPos instead of normal $YPos.

		$FontSize = 10;// Font size for the line item.
        $LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize, _('Name.'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-20,100,$FontSize, _('Personal No.'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-40,100,$FontSize, _('Date of Filing.'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-60,100,$FontSize, _('Leave Applied For.'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-80,100,$FontSize, _('Department'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-100,100,$FontSize, _('Section'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-120,100,$FontSize, _('Start Time:'));
		$LeftOvers = $pdf->addTextWrap(300,$YPos-120,100,$FontSize, _('End Time.'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-140,300,$FontSize, _('Contact Address while on leave'));
		
		$LeftOvers = $pdf->addTextWrap(180,$YPos,300,$FontSize, $myrow['emp_fname'].'&nbsp;'.$myrow['emp_mname'].'&nbsp;'.$myrow['emp_lname']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-20,300,$FontSize, $myrow['id']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-40,300,$FontSize, $myrow['date']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-60,300,$FontSize, $myrow['leavetype']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-80,300,$FontSize, $myrow['description']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-100,300,$FontSize, $myrow['section_name']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-120,100,$FontSize, $myrow['stime']);
		$LeftOvers = $pdf->addTextWrap(350,$YPos-120,100,$FontSize, $myrow['etime']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-140,300,$FontSize, $myrow['emp_add']);
		$YPos = $YPos-130;
	$pdf->addText($XPos, $YPos, $FontSize, $message.':');
	$Width2 = $Page_Width-$Right_Margin-200;// Width to print salesorders.comments.
	$LeftOver = trim($myrow['problem']);
	while(mb_strlen($LeftOver) > 1) {
		$YPos -= $FontSize;
		if ($YPos < ($Bottom_Margin)) {// Begins new page.
			include ('PDFHDHeader.php');
		}
		$LeftOver = $pdf->addTextWrap(180, $YPos, $Width2, $FontSize, $LeftOver);
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $pdf->OutputI($_SESSION['DatabaseName'] . '_Transport_' . $_GET['id'] . '_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();

ob_end_flush();
?>
