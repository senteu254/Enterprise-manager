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
if($myrow['doc_id']==5){
$tt = _('Half-Day Permission ');
$date= _('Date Of Filing');
}else{
$tt = _('Half-Day Permission');
}
$pdf->addTextWrap(0, $Page_Height-$Top_Margin-18, $Page_Width, 18,$tt, 'center');

// Prints 'Delivery To' info:
$XPos = 46;
$YPos = 770;
$YPos -= 30;
// Draws a box with round corners around 'Delivery To' info:
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-30-$FontSize*1, 200, $FontSize, $_SESSION['CompanyRecord']['coyname'], 'left');
$FontSize=10;
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-33-$FontSize*2, 200, $FontSize, $_SESSION['CompanyRecord']['regoffice1'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-33-$FontSize*3, 200, $FontSize,  _('Ph') . ': ' . $_SESSION['CompanyRecord']['telephone'] .' ' . _('Fax'). ': ' . $_SESSION['CompanyRecord']['fax'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-33-$FontSize*4, 200, $FontSize, $_SESSION['CompanyRecord']['email'], 'left');

// Prints quotation info:
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-30-$FontSize*1, 200, $FontSize,  _('Printed On'). ': '.date("d, M Y"), 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-30-$FontSize*2, 200, $FontSize, _('Ref No.'). ': '.$_GET['id'], 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-30-$FontSize*3, 200, $FontSize, _('Page').': '.$PageNumber, 'right');

$FontSize=10;

// Prints table header:
$YPos -= 55;
$XPos = 40;

// Draws a box with round corners around line items:
$pdf->RoundRectangle(
	$Left_Margin,// RoundRectangle $XPos.
	$YPos+$FontSize+5,// RoundRectangle $YPos.
	$Page_Width-$Left_Margin-$Right_Margin,// RoundRectangle $Width.
	$YPos+$FontSize-$Bottom_Margin-400,// RoundRectangle $Height.
	10,// RoundRectangle $RadiusX.
	10);// RoundRectangle $RadiusY.

$YPos -= $FontSize;// This is to use addTextWrap's $YPos instead of normal $YPos.

?>
