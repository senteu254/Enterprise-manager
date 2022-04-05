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
$pdf->addJpegFromFile($_SESSION['LogoFile'],$XPos-100,770,0,60);

// Prints 'Quotation' title:
//$pdf->addTextWrap(0, $Page_Height-$Top_Margin-18, $Page_Width, 14, _('PROFORMA INVOICE').$_GET['PINo'], 'center');
$XPos =140;
$YPos =810;
$FontSize=8;
$myrow = array_map(html_entity_decode, $myrow);
$pdf->addText($XPos, $YPos+15,$FontSize,$_SESSION['CompanyRecord']['coyname']);
$pdf->addText($XPos, $YPos+10,$_SESSION['CompanyRecord']['regoffice1']);
$pdf->addText($XPos, $YPos+5,$FontSize=8,  _('	Tel') . ': ' . $_SESSION['CompanyRecord']['telephone']);
$pdf->addText($XPos, $YPos-5,$FontSize=8,  _('	Mail') . ': ' . $_SESSION['CompanyRecord']['email']);
$pdf->addText($XPos, $YPos-15,$FontSize=8,  _('	Fax') . ': ' . $_SESSION['CompanyRecord']['fax']);
$pdf->addText($XPos, $YPos-25,$FontSize=8, $myrow['deladd3'] . ' ' . $myrow['deladd4'] . ' ' . $myrow['deladd5']);
//$pdf->addText($XPos=230, $YPos-60,$FontSize, _('PROFORMA INVOICE No.:'.$_GET['PINo']), 'center');
// Prints company info:
$XPos = -70;
$YPos = 750;
$FontSize = 8;
//$pdf->addText($XPos, $YPos, $FontSize, $_SESSION['CompanyRecord']['coyname']);
//$YPos -= $FontSize;
//$FontSize = 10;
//$pdf->addText($XPos, $YPos, $FontSize, $_SESSION['CompanyRecord']['regoffice1']);
//$pdf->addText($XPos, $YPos-$FontSize*1, $FontSize, $_SESSION['CompanyRecord']['regoffice2']);

//require_once('includes/CurrenciesArray.php');// To get the currency name from the currency code.
//$pdf->addText($XPos = 50, $YPos-$FontSize*10,$FontSize,

//	_('All amounts stated in') . ' ' . $myrow['currcode'] . ' - ' . $CurrencyName[$myrow['currcode']]);

// Prints table header:
// Prints 'Delivery To' info:

$pdf->addText($XPos=210, $YPos+22,$FontSize=15, _('PROFORMA INVOICE No.:'), 'center');
$pdf->SetTextColor(0,0,0);
$pdf->SetTextColor(255,0,0);
$pdf->addText($XPos=390, $YPos+25,$FontSize=19, _(''.$_GET['PINo']), 'center');
$pdf->SetTextColor(0,0,0);
$XPos = 96;
$YPos -=40;
$FontSize = 8;
$myrow = array_map(html_entity_decode, $myrow);
$pdf->addText($XPos, $YPos+15,$FontSize, _('To').':' );
$pdf->addText($XPos, $YPos+5,$FontSize, $myrow['deliverto']);
$pdf->addText($XPos, $YPos-5,$FontSize, $myrow['deladd1']);
$pdf->addText($XPos, $YPos-15,$FontSize, $myrow['deladd2']);
$pdf->addText($XPos, $YPos-25,$FontSize, $myrow['deladd3'] . ' ' . $myrow['deladd4'] . ' ' . $myrow['deladd5']);
///////////////////////
$XPos = 400;
$YPos -=0;
$FontSize = 8;
$myrow = array_map(html_entity_decode, $myrow);
$pdf->addText($XPos, $YPos+15,$FontSize, _('Proforma Invoice For').':' );
$pdf->addText($XPos, $YPos+5,$FontSize, $myrow['name']);
$pdf->addText($XPos, $YPos-5,$FontSize, $myrow['address1']);
$pdf->addText($XPos, $YPos-15,$FontSize,  $myrow['address2']);
$pdf->addText($XPos, $YPos-25,$FontSize, $myrow['deladd3'] . ' ' . $myrow['deladd4'] . ' ' . $myrow['deladd5']);
///////////////////////
// Prints 'Quotation For' info:
$YPos -= 47;
/////////////////////edited by peter//////////////////////////////
//$pdf->addText($XPos, $YPos,$FontSize, _('Proforma Invoice For').':');
//$pdf->addText($XPos, $YPos-15,$FontSize, $myrow['name']);
//$pdf->addText($XPos, $YPos-30,$FontSize, $myrow['address1']);
//$pdf->addText($XPos, $YPos-45,$FontSize, $myrow['address2']);
//$pdf->addText($XPos, $YPos-60,$FontSize, $myrow['address3'] . ' ' . $myrow['address4'] . ' ' . $myrow['address5']);
///////////////////////end of edit/////////////////////////////

// Draws a box with round corners around 'Delivery To' info:
$XPos = 50;
$YPos += 95;
$pdf->RoundRectangle(
	$XPos-10,// RoundRectangle $XPos.
	$YPos+4-11,// RoundRectangle $YPos.
	508+8+8,// RoundRectangle $Width.
	60+10+5,// RoundRectangle $Height.
	10,// RoundRectangle $RadiusX.
	10);// RoundRectangle $RadiusY.

// Draws a box with round corners around around 'Quotation For' info:
//$YPos -= 90;
//$pdf->RoundRectangle(
//XPos-10,// RoundRectangle $XPos.
	//$YPos+60+10,// RoundRectangle $YPos.
	//160+10+10,// RoundRectangle $Width.
	//60+10+10,// RoundRectangle $Height.
	//10,// RoundRectangle $RadiusX.
	//10);// RoundRectangle $RadiusY.

// Prints quotation info:

//$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-$FontSize*2, 200, $FontSize, _('Your Ref'). ': '.$myrow['customerref'], 'right');
//$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-$FontSize*4, 200, $FontSize, _('Page').': '.$PageNumber, 'right');

$FontSize = 8;

// Prints the currency name:
//require_once('includes/CurrenciesArray.php');// To get the currency name from the currency code.
//$pdf->addText($Page_Width/2+$Left_Margin, $YPos+5, $FontSize,

	//_('All amounts stated in') . ' ' . $myrow['currcode'] . ' - ' . $CurrencyName[$myrow['currcode']]);

// Prints table header:
$YPos -= 105;
$XPos = 50;
$FontSize = 8;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize, _('Item Code'));
$LeftOvers = $pdf->addTextWrap(120,$YPos,235,$FontSize, _('Item Description'));
$LeftOvers = $pdf->addTextWrap(230,$YPos,85,$FontSize, _('Qty'),'right');
//$LeftOvers = $pdf->addTextWrap(230,$YPos,85,$FontSize,_('Price'),'right');
$LeftOvers = $pdf->addTextWrap(280,$YPos,85,$FontSize, _('Price'),'right');
$LeftOvers = $pdf->addTextWrap(330,$YPos,85,$FontSize, _('Tax Class'),'right');
$LeftOvers = $pdf->addTextWrap(400,$YPos,85,$FontSize, _('Tax Amount'),'right');
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-90, $YPos, 90, $FontSize, _('Total'),'right');

// Draws a box with round corners around line items:
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
