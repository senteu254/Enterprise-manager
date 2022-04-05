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
$tt = _('Maintenance Complete Form');

$pdf->addTextWrap(0, $Page_Height-$Top_Margin-18, $Page_Width, 18,$tt, 'center');

// Prints 'Delivery To' info:
$XPos = 46;
$YPos = 770;
$FontSize=12;
// Prints 'Quotation For' info:
$YPos -= 80;
$pdf->addText($XPos-10, $YPos+20,$FontSize, _('Job Card No :').''.$myrow['cardno']);
$pdf->addText($XPos-10, $YPos+5,$FontSize,  _('Date   :').''.date("Y/m/d"));
$pdf->addText($XPos-10, $YPos-10,$FontSize, _('Tech Name  :').''.$myrow['userresponsible']);
$pdf->addText($XPos-10, $YPos-25,$FontSize, _('M/C NO  :').''.$myrow['mcno']);
$pdf->addText($XPos-10, $YPos-40,$FontSize, _('Equipment  :').''.$myrow['description']);

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
	200+10+10,// RoundRectangle $Width.
	80+10+10,// RoundRectangle $Height.
	10,// RoundRectangle $RadiusX.
	10);// RoundRectangle $RadiusY.
	
// Prints quotation info:
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-20-$FontSize*1, 200, $FontSize,  _('Printed On'). ': '.date("d, M Y"), 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-20-$FontSize*2, 200, $FontSize, _('Ref No.'). ': '.$_GET['id'], 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-20-$FontSize*3, 200, $FontSize, _('Page').': '.$PageNumber, 'right');








$YPos -= 80;

$sdate=date_format($myrow['breakdowndate'],"Y-m-d");

$pdf->addText($XPos-10, $YPos+20,$FontSize, _('Date :').''.$sdate);
$pdf->addText($XPos-10, $YPos+5,$FontSize,  _('Time Start   :').''.date("Y/m/d"));
$pdf->addText($XPos-10, $YPos-10,$FontSize, _('Date  :').''.$myrow['endate']);
$pdf->addText($XPos-10, $YPos-25,$FontSize, _('Time End  :').''.$myrow['mcno']);
$pdf->addText($XPos-10, $YPos-40,$FontSize, _('Total Time Taken  :').''.($myrow['endate']-$myrow['breakdowndate']));


// Draws a box with round corners around around 'Quotation For' info:
$YPos -= 50;

$pdf->RoundRectangle(
	$XPos-10,// RoundRectangle $XPos.
	$YPos+60+10,// RoundRectangle $YPos.
	200+10+10,// RoundRectangle $Width.
	80+10+10,// RoundRectangle $Height.
	10,// RoundRectangle $RadiusX.
	10);// RoundRectangle $RadiusY.
	


?>
