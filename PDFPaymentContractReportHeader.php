<?php
/*	$Id: PDFQuotationPortraitPageHeader.inc 6822 2014-08-15 20:24:57Z rchacon $*/

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/
$id=$_GET['id'];
// $PageNumber is initialised in 0 by includes/PDFStarter.php.
$PageNumber ++;// Increments $PageNumber before printing.
if ($PageNumber>1) {// Inserts a page break if it is not the first page.
	$pdf->newPage();
}

// Prints company logo:
$XPos = 20;
$pdf->addJpegFromFile($_SESSION['LogoFile'],$XPos,750,0,60);
$books= DB_query("SELECT * FROM contract_details a
                  INNER JOIN contract_assignment b ON a.ContractID=b.ContractID
				  INNER JOIN suppliers c ON b.SupplierID=c.supplierid 
				  WHERE a.ContractID='".$id ."'");
$mybook = DB_fetch_array($books);
// Prints 'Quotation' title:
$XPos = 46;
$YPos = 770;
$FontSize=12;
// Prints 'Quotation For' info:
$YPos -= 70;
$FontSize = 8;
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-80, 200, $FontSize=12, 'Firm', 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-410, $Page_Height-$Top_Margin-80, $Page_Width+30, 12,':');
$pdf->addTextWrap($Page_Width-$Right_Margin-380, $Page_Height-$Top_Margin-80, $Page_Width+30, 12,$mybook['suppname']);
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-95, 200, $FontSize=12, 'Contract Name', 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-410, $Page_Height-$Top_Margin-95, $Page_Width+30, 12,':');
$pdf->addTextWrap($Page_Width-$Right_Margin-380, $Page_Height-$Top_Margin-95, $Page_Width+30, 12,$mybook['Contract_Name']);
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-110, 200, $FontSize=12, 'Contract No.', 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-410, $Page_Height-$Top_Margin-110, $Page_Width+30, 12,':');
$pdf->addTextWrap($Page_Width-$Right_Margin-380, $Page_Height-$Top_Margin-110, $Page_Width+30, 12,$mybook['Contract_Number']);
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-125, 200, $FontSize=12, 'Contract Date', 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-410, $Page_Height-$Top_Margin-125, $Page_Width+30, 12,':');
$pdf->addTextWrap($Page_Width-$Right_Margin-380, $Page_Height-$Top_Margin-125, $Page_Width+30, 12,$mybook['Begin_Date']);
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-140, 200, $FontSize=12, 'Contract Price', 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-410, $Page_Height-$Top_Margin-140, $Page_Width+30, 12,':');
$pdf->addTextWrap($Page_Width-$Right_Margin-380, $Page_Height-$Top_Margin-140, $Page_Width+30, 12,locale_number_format($mybook['Amount'],2));
$pdf->addTextWrap($Page_Width-$Right_Margin-270, $Page_Height-$Top_Margin-140, $Page_Width+30, 12,'Currency:');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-140, $Page_Width+30, 12,$mybook['Currency']);


// Prints 'Delivery To' info:Amount Allocated : 67,000.00


// Draws a box with round corners around 'Delivery To' info:
$pdf->addTextWrap($Page_Width-$Right_Margin-440, $Page_Height-$Top_Margin-5-$FontSize*1, 300, $FontSize, $_SESSION['CompanyRecord']['coyname'], 'left');
$FontSize=10;
$pdf->addTextWrap($Page_Width-$Right_Margin-440, $Page_Height-$Top_Margin-10-$FontSize*2, 200, $FontSize, $_SESSION['CompanyRecord']['regoffice1'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-440, $Page_Height-$Top_Margin-15-$FontSize*3, 200, $FontSize,  _('Ph') . ': ' . $_SESSION['CompanyRecord']['telephone'] .' ' . _('Fax'). ': ' . $_SESSION['CompanyRecord']['fax'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-440, $Page_Height-$Top_Margin-20-$FontSize*4, 200, $FontSize, $_SESSION['CompanyRecord']['email'], 'left');

// Prints quotation info:
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-10-$FontSize*1, 200, $FontSize,  _('Printed On'). ': '.date("d, M Y"), 'right');
//$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-20-$FontSize*2, 200, $FontSize, _('Ref No.'). ': '.$_GET['id'], 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-10-$FontSize*3, 200, $FontSize, _('Page').': '.$PageNumber, 'right');


$XPos = 40;
$LeftOvers = $pdf->addTextWrap($Left_Margin-20,$YPos-48,100,$FontSize, _('Payment ID'));
//$LeftOvers = $pdf->addTextWrap(60,$YPos-48,85,$FontSize, _('End Date'));
$LeftOvers = $pdf->addTextWrap(90,$YPos-48,300,$FontSize, _('Description'),'left');
//$LeftOvers = $pdf->addTextWrap(210,$YPos-48,300,$FontSize, _('Contract Name'),'left');
$LeftOvers = $pdf->addTextWrap(410,$YPos-48,300,$FontSize, _('Date Paid'),'left');
$LeftOvers = $pdf->addTextWrap(525,$YPos-48,300,$FontSize, _('Amount'),'left');

// Draws a box with round corners around line items:
$pdf->RoundRectangle(
	$Left_Margin-20,// RoundRectangle $XPos.
	$YPos-35,// RoundRectangle $YPos.
	$Page_Width-$Left_Margin-$Right_Margin+30,// RoundRectangle $Width.
	$YPos-40,// RoundRectangle $Height.
	10,// RoundRectangle $RadiusX.
	10);// RoundRectangle $RadiusY.

// Line under table headings:
$LineYPos = $YPos - $FontSize -1;
$pdf->line($Page_Width-$Right_Margin+10, $LineYPos-46, $Left_Margin-20, $LineYPos-46);

$YPos -= $FontSize;// This is to use addTextWrap's $YPos instead of normal $YPos.

?>
