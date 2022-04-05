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
$XPos = 50;
$pdf->addJpegFromFile($_SESSION['LogoFile'],$XPos,740,0,60);

// Prints 'Quotation' title:
if(isset($_GET['SoL']) && $_GET['SoL']==1){
$tt = _('On Leave as at '.date('d/m/Y'));
}else{
$tt = $array[$_GET['status']].' Report('.$array2[$_GET['type']].')';
}

$pdf->addTextWrap(0, $Page_Height-$Top_Margin-80, $Page_Width, 14,$tt, 'center');

// Prints 'Delivery To' info:
$XPos = 46;
$YPos = 770;
$FontSize=12;
// Prints 'Quotation For' info:
$YPos -= 70;

// Draws a box with round corners around 'Delivery To' info:
$pdf->addTextWrap($Page_Width-$Right_Margin-430, $Page_Height-$Top_Margin-10-$FontSize*1, 300, $FontSize, $_SESSION['CompanyRecord']['coyname'], 'left');
$FontSize=10;
$pdf->addTextWrap($Page_Width-$Right_Margin-430, $Page_Height-$Top_Margin-20-$FontSize*2, 200, $FontSize, $_SESSION['CompanyRecord']['regoffice1'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-430, $Page_Height-$Top_Margin-20-$FontSize*3, 200, $FontSize,  _('Ph') . ': ' . $_SESSION['CompanyRecord']['telephone'] .' ' . _('Fax'). ': ' . $_SESSION['CompanyRecord']['fax'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-430, $Page_Height-$Top_Margin-20-$FontSize*4, 200, $FontSize, $_SESSION['CompanyRecord']['email'], 'left');

// Prints quotation info:
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-20-$FontSize*1, 200, $FontSize,  _('Printed On'). ': '.date("d, M Y"), 'right');
//$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-20-$FontSize*2, 200, $FontSize, _('Ref No.'). ': '.$_GET['id'], 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-20-$FontSize*3, 200, $FontSize, _('Page').': '.$PageNumber, 'right');

$XPos = 40;
$LeftOvers = $pdf->addTextWrap($Left_Margin-20,$YPos,100,$FontSize, _('Srv No'));
$LeftOvers = $pdf->addTextWrap(80,$YPos,85,$FontSize, _('Employee Name'));
$LeftOvers = $pdf->addTextWrap(200,$YPos,300,$FontSize, _('Department'),'left');
$LeftOvers = $pdf->addTextWrap(300,$YPos,300,$FontSize, _('Leave Type'),'left');
$LeftOvers = $pdf->addTextWrap(400,$YPos,300,$FontSize, _('From Date'),'left');
$LeftOvers = $pdf->addTextWrap(450,$YPos,300,$FontSize, _('To Date'),'left');
$LeftOvers = $pdf->addTextWrap(493,$YPos,300,$FontSize, _('Days'),'left');
$LeftOvers = $pdf->addTextWrap(520,$YPos,300,$FontSize, _('Applied On'),'left');

// Draws a box with round corners around line items:
$pdf->RoundRectangle(
	$Left_Margin-20,// RoundRectangle $XPos.
	$YPos+$FontSize+5,// RoundRectangle $YPos.
	$Page_Width-$Left_Margin-$Right_Margin+30,// RoundRectangle $Width.
	$YPos+$FontSize-$Bottom_Margin+5,// RoundRectangle $Height.
	10,// RoundRectangle $RadiusX.
	10);// RoundRectangle $RadiusY.

// Line under table headings:
$LineYPos = $YPos - $FontSize -1;
$pdf->line($Page_Width-$Right_Margin+10, $LineYPos, $Left_Margin-20, $LineYPos);

$YPos -= $FontSize;// This is to use addTextWrap's $YPos instead of normal $YPos.

?>
