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
$pdf->addJpegFromFile($_SESSION['LogoFile'],$XPos+90,735,0,60);

// Draws a box with round corners around 'Delivery To' info:
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-$FontSize*1, 400, $FontSize, $_SESSION['CompanyRecord']['coyname'], 'left');
$FontSize=10;
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-$FontSize*2-3, 200, $FontSize, $_SESSION['CompanyRecord']['regoffice1'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-$FontSize*3-3, 200, $FontSize,  _('Ph') . ': ' . $_SESSION['CompanyRecord']['telephone'] .' ' . _('Fax'). ': ' . $_SESSION['CompanyRecord']['fax'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-$FontSize*4-3, 200, $FontSize, $_SESSION['CompanyRecord']['email'], 'left');

	
// Prints quotation info:
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-$FontSize*1, 200, $FontSize,  _('Printed On'). ': '.date("d, M Y"), 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-$FontSize*2, 200, $FontSize, _('Test No.'). ': '.$myrow['test'], 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-$FontSize*3, 200, $FontSize, _('Page').': '.$PageNumber, 'right');
// Prints 'Quotation' title:
$tt = _('PRIMER SENSITIVITY CURVE');

$pdf->addTextWrap(0, $Page_Height-$Top_Margin-95, $Page_Width, 14,$tt, 'center');

// Prints 'Delivery To' info:
$XPos = 46;
$YPos = 775;
$FontSize=12;
// Prints 'Quotation For' info:
$YPos -= 80;

//$pdf->addText($XPos-10, $YPos+10,$FontSize, _('Personal No :').''.$myrow['personal_no']);
//$pdf->addText($XPos-10, $YPos-10,$FontSize,  _('Date   :').''.date("Y/m/d"));
//$pdf->addText($XPos-10, $YPos-30,$FontSize, _('Full Name  :').''.$myrow['emp_fname'].''.''.$myrow['emp_lname']);
//$pdf->addText($XPos-10, $YPos-50,$FontSize, _('Total Number of property   :').''.$myrow['total']);

// Draws a box with round corners around around 'Quotation For' info:

$FontSize=10;

// Prints table header:
//$YPos -= 55;

$XPos = 40;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize, _('DATE :'));
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(160,$YPos,200,$FontSize, $myrow['date']);
$pdf->SetFont('');
$LeftOvers = $pdf->addTextWrap(350,$YPos,200,$FontSize, _('CALIBRE :'),'left');
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(430,$YPos,200,$FontSize, $myrow['calibre'],'left');
$pdf->SetFont('');

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-13,150,$FontSize, _('PRIMER DEPTH RANGE :'));
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(160,$YPos-13,200,$FontSize, $myrow['depthrange']);
$pdf->SetFont('');
$LeftOvers = $pdf->addTextWrap(350,$YPos-13,300,$FontSize, _('REQUIREMENT :'),'left');
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(430,$YPos-13,415,$FontSize, $req1,'left');
$LeftOvers = $pdf->addTextWrap(430,$YPos-26,415,$FontSize, $req2,'left');
$pdf->SetFont('');

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-26,150,$FontSize, _('PRIMER LOT NO. :'));
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(160,$YPos-26,200,$FontSize, $myrow['primerlot']);
$pdf->SetFont('');
// Draws a box with round corners around line items:
$pdf->RoundRectangle(
	$Left_Margin,// RoundRectangle $XPos.
	$YPos+$FontSize+5,// RoundRectangle $YPos.
	$Page_Width-$Left_Margin-$Right_Margin,// RoundRectangle $Width.
	$YPos+$FontSize-$Bottom_Margin+5,// RoundRectangle $Height.
	10,// RoundRectangle $RadiusX.
	10);// RoundRectangle $RadiusY.

// Line under table headings:
$LineYPos = $YPos - $FontSize -22;
$pdf->line($Page_Width-$Right_Margin, $LineYPos, $Left_Margin, $LineYPos);

$YPos -= $FontSize+30;// This is to use addTextWrap's $YPos instead of normal $YPos.
$pdf->line($Page_Width-$Right_Margin, $LineYPos-20, $Left_Margin, $LineYPos-20);
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-7,150,$FontSize, _('H(d)'));
$LeftOvers = $pdf->addTextWrap($Left_Margin+100,$YPos-7,150,$FontSize, _('Height'));
$LeftOvers = $pdf->addTextWrap($Left_Margin+200,$YPos-7,150,$FontSize, _('No Fire'));
$LeftOvers = $pdf->addTextWrap($Left_Margin+300,$YPos-7,150,$FontSize, _('%(PI)'));
$LeftOvers = $pdf->addTextWrap($Left_Margin+380,$YPos-7,150,$FontSize, _('KI'));
$LeftOvers = $pdf->addTextWrap($Left_Margin+450,$YPos-7,150,$FontSize, _('KI &times; PI'));
$pdf->SetFont('');
$YPos -= $FontSize+11;// This is to use addTextWrap's $YPos instead of normal $YPos.
?>
