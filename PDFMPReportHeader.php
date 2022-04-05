<?php
/*	$Id: PDFQuotationPortraitPageHeader.inc 6822 2014-08-15 20:24:57Z rchacon $*/

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/

// $PageNumber is initialised in 0 by includes/PDFStarter.php.
$PageNumber ++;// Increments $PageNumber before printing.
if ($PageNumber>1) {// Inserts a page break if it is not the first page.
	$pdf->newPage();
}

// Prints company logo:
$XPos = $Page_Width/2 - 140;
$pdf->addJpegFromFile($_SESSION['LogoFile'],$XPos+60,720,0,60);

// Prints 'Quotation' title:
$tt = _('Maternity/Paternity Leave  Report');

$pdf->addTextWrap(0, $Page_Height-$Top_Margin-18, $Page_Width, 18,$tt, 'center');

// Prints 'Delivery To' info:
$XPos = 46;
$YPos = 770;
$FontSize=12;
// Prints 'Quotation For' info:
$YPos -= 80;

$pdf->addText($XPos-10, $YPos+20,$FontSize, _('Personal No :').' '.$myrow['id']);
$pdf->addText($XPos-10, $YPos+5,$FontSize,  _('Date   :').''.date("Y/m/d"));
$pdf->addText($XPos-10, $YPos-10,$FontSize, _('Full Name  :').''.$myrow['emp_fname'].' '.$myrow['emp_lname']);
$pdf->addText($XPos-10, $YPos-25,$FontSize, _('Leave Applied For  :').''.$myrow['leavetype']);
$pdf->addText($XPos-10, $YPos-40,$FontSize, _('Sum of Leave Days   :').''.$value2);
$pdf->addText($XPos-10, $YPos-55,$FontSize, _('Balance of Leave Days   :').' '.$value3);


// Draws a box with round corners around 'Delivery To' info:
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-20-$FontSize*1, 200, $FontSize, $_SESSION['CompanyRecord']['coyname'], 'left');
$FontSize=10;
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-20-$FontSize*2, 200, $FontSize, $_SESSION['CompanyRecord']['regoffice1'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-20-$FontSize*3, 200, $FontSize,  _('Ph') . ': ' . $_SESSION['CompanyRecord']['telephone'] .' ' . _('Fax'). ': ' . $_SESSION['CompanyRecord']['fax'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-20-$FontSize*4, 200, $FontSize, $_SESSION['CompanyRecord']['email'], 'left');

// Draws a box with round corners around around 'Quotation For' info:
$YPos -= 50;

$pdf->RoundRectangle(
	$XPos-10,// RoundRectangle $XPos.
	$YPos+60+10,// RoundRectangle $YPos.
	200+10+10+10,// RoundRectangle $Width.
	80+10+10,// RoundRectangle $Height.
	10,// RoundRectangle $RadiusX.
	10);// RoundRectangle $RadiusY.
	
// Prints quotation info:
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-20-$FontSize*1, 200, $FontSize,  _('Printed On'). ': '.date("d, M Y"), 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-20-$FontSize*2, 200, $FontSize, _('Ref No.'). ': '.$_GET['id'], 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-20-$FontSize*3, 200, $FontSize, _('Page').': '.$PageNumber, 'right');

$FontSize=10;

// Prints table header:
$YPos -= 55;
$XPos = 40;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize, _('Start Date'));
$LeftOvers = $pdf->addTextWrap(120,$YPos,85,$FontSize, _('Endate'));
$LeftOvers = $pdf->addTextWrap(200,$YPos,300,$FontSize, _('Total Leave Days'),'left');
$LeftOvers = $pdf->addTextWrap(280,$YPos,300,$FontSize, _('Status'),'left');
// Draws a box with round corners around HD permissions:
$pdf->RoundRectangle(
	$Left_Margin,// RoundRectangle $XPos.
	$YPos+$FontSize+5,// RoundRectangle $YPos.
	$Page_Width-$Left_Margin-$Right_Margin,// RoundRectangle $Width.
	$YPos+$FontSize-$Bottom_Margin+5,// RoundRectangle $Height.
	10,// RoundRectangle $RadiusX.
	10);// RoundRectangle $RadiusY.

// Line under table headings:
$LineYPos = $YPos - $FontSize -1;
$pdf->line($Page_Width-$Right_Margin, $LineYPos, $Left_Margin, $LineYPos);

$YPos -= $FontSize;// This is to use addTextWrap's $YPos instead of normal $YPos.

?>
