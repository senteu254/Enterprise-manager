<?php
/* $Id: PDFTransPageHeader.inc 6826 2014-08-17 21:53:13Z rchacon $ */

/*	Please note that addTextWrap() prints a font-size-height further down than
	addText() and other functions. Use addText() instead of addTextWrap() to
	print left aligned elements.*/


if (!$FirstPage){ /* only initiate a new page if its not the first */
	$pdf->newPage();
}

$YPos = $Page_Height-$Top_Margin;

$pdf->addJpegFromFile($_SESSION['LogoFile'],$Page_Width/2 -70,$YPos-80,0,60);
$FontSize =12;

		$pdf->addText($Page_Width - 285, $YPos, $FontSize, _($myrow['sheetname']), 'center');
		$pdf->addText($Page_Width - 285, $YPos-17, $FontSize, _($myrow['typename']), 'center');
		$pdf->addText($Page_Width - 285, $YPos-35, $FontSize, _($myrow['operation']), 'center');

$XPos = $Page_Width - 280;
$YPos -= 111;
/*draw a nice curved corner box around the billing details */
/*from the top right */
$pdf->partEllipse($XPos+255,$YPos+100,0,90,10,10);
/*line to the top left */
$pdf->line($XPos+255, $YPos+110,$XPos, $YPos+110);
/*Dow top left corner */
$pdf->partEllipse($XPos, $YPos+100,90,180,10,10);
/*Do a line to the bottom left corner */
$pdf->line($XPos-10, $YPos+100,$XPos-10, $YPos+5);
/*Now do the bottom left corner 180 - 270 coming back west*/
$pdf->partEllipse($XPos, $YPos+5,180,270,10,10);
/*Now a line to the bottom right */
$pdf->line($XPos, $YPos-5,$XPos+255, $YPos-5);
/*Now do the bottom right corner */
$pdf->partEllipse($XPos+255, $YPos+5,270,360,10,10);
/*Finally join up to the top right corner where started */
$pdf->line($XPos+265, $YPos+5,$XPos+265, $YPos+100);

$YPos = $Page_Height - $Top_Margin - 30;

$FontSize = 11;

	$pdf->addText($Page_Width-285, $YPos-26, $FontSize, _('Record No'));
	$pdf->SetTextColor(255,0,0);
	$pdf->addText($Page_Width-200, $YPos-25, $FontSize, $myrow['id']);
	$pdf->SetTextColor(0);
	$pdf->addText($Page_Width-285, $YPos-44, $FontSize, _('Date'));
	$pdf->addText($Page_Width-200, $YPos-44, $FontSize, ConvertSQLDate($myrow['date']));
	$pdf->addText($Page_Width-285, $YPos-65, $FontSize, _('Shift'));
	$pdf->addText($Page_Width-200, $YPos-65, $FontSize, $myrow['shift']);


$pdf->addText($Page_Width-268, $YPos-88, $FontSize, _('Page'));
$pdf->addText($Page_Width-180, $YPos-88, $FontSize, $PageNumber);

/*End of the text in the right side box */

/*Now print out the company name and address in the middle under the logo */

$XPos = $Left_Margin;
$YPos = $Page_Height - $Top_Margin;

$FontSize=10;
$pdf->addText($XPos, $YPos, $FontSize, $_SESSION['CompanyRecord']['coyname']);

$FontSize=8;
$pdf->addText($XPos, $YPos-10, $FontSize, $_SESSION['TaxAuthorityReferenceName'] . ': ' . $_SESSION['CompanyRecord']['gstno']);
$pdf->addText($XPos, $YPos-19,$FontSize, $_SESSION['CompanyRecord']['regoffice1']);
$pdf->addText($XPos, $YPos-28,$FontSize, $_SESSION['CompanyRecord']['regoffice2']);
$pdf->addText($XPos, $YPos-37,$FontSize, $_SESSION['CompanyRecord']['regoffice3'] . ' ' . $_SESSION['CompanyRecord']['regoffice4'] . ' ' . $_SESSION['CompanyRecord']['regoffice5']);
$pdf->addText($XPos, $YPos-46, $FontSize, $_SESSION['CompanyRecord']['regoffice6']);
$pdf->addText($XPos, $YPos-54, $FontSize, _('Phone') . ':' . $_SESSION['CompanyRecord']['telephone'] . ' ' . _('Fax') . ': ' . $_SESSION['CompanyRecord']['fax']);
$pdf->addText($XPos, $YPos-63, $FontSize, _('Email') . ': ' . $_SESSION['CompanyRecord']['email']);



$XPos = $Left_Margin;

$YPos = $Page_Height - $Top_Margin - 80;
/*draw a line under the company address and charge to address
$pdf->line($XPos, $YPos,$Right_Margin, $YPos); */

$XPos = $Page_Width/2;

$XPos = $Left_Margin;
$YPos -= ($line_height*2);
/*draw a box with nice round corner for entering line items */
/*90 degree arc at top right of box 0 degrees starts a bottom */
$pdf->partEllipse($Page_Width-$Right_Margin-10, $Bottom_Margin+390,0,90,10,10);
/*line to the top left */
$pdf->line($Page_Width-$Right_Margin-10, $Bottom_Margin+400,$Left_Margin+10, $Bottom_Margin+400);
/*Dow top left corner */
$pdf->partEllipse($Left_Margin+10, $Bottom_Margin+390,90,180,10,10);
/*Do a line to the bottom left corner */
$pdf->line($Left_Margin, $Bottom_Margin+390,$Left_Margin, $Bottom_Margin+10);
/*Now do the bottom left corner 180 - 270 coming back west*/
$pdf->partEllipse($Left_Margin+10, $Bottom_Margin+10,180,270,10,10);
/*Now a line to the bottom right */
$pdf->line($Left_Margin+10, $Bottom_Margin,$Page_Width-$Right_Margin-10, $Bottom_Margin);
/*Now do the bottom right corner */
$pdf->partEllipse($Page_Width-$Right_Margin-10, $Bottom_Margin+10,270,360,10,10);
/*Finally join up to the top right corner where started */
$pdf->line($Page_Width-$Right_Margin, $Bottom_Margin+10,$Page_Width-$Right_Margin, $Bottom_Margin+390);


$YPos -= ($line_height*2);
/*Set up headings */
$FontSize=10;
$pdf->addText($Left_Margin + 2, $YPos, $FontSize, _('Brass LOT No') . ':');
$pdf->addText($Left_Margin+68, $YPos, $FontSize, strtoupper($myrow['brasslot']));
$pdf->addText($Left_Margin + 150, $YPos, $FontSize, _('Machine No') . ':');
$pdf->addText($Left_Margin+210, $YPos, $FontSize, strtoupper($myrow['machineno']));

/*Print a vertical line */
$pdf->line($Left_Margin+320, $YPos+$line_height-9,$Left_Margin+320,$YPos-18);

$pdf->addText($Left_Margin+330, $YPos, $FontSize, _('Technician') . ':');
$pdf->addText($Left_Margin+380, $YPos, $FontSize, $myrow['technician']);

$YPos -= 8;
/*draw a line */
$pdf->line($XPos, $YPos-10,$Page_Width-$Right_Margin, $YPos-10);

/*$YPos -= 12;

$TopOfColHeadings = $YPos-10;

$pdf->addText($Left_Margin+5, $YPos, $FontSize, _('Item Code'));
$pdf->addText($Left_Margin+100, $YPos, $FontSize, _('Description'));
$pdf->addText($Left_Margin+450, $YPos, $FontSize, _('Qty Required'));
if($myrow['closed'] ==1){
$pdf->addText($Left_Margin+510, $YPos, $FontSize, _('Qty Delivered'));
}else{
$pdf->addText($Left_Margin+510, $YPos, $FontSize, _('Stock Balance'));
}
$pdf->addText($Left_Margin+585, $YPos, $FontSize, _('UOM'));
$pdf->addText($Left_Margin+615, $YPos, $FontSize, _('Reason for Req'));
$pdf->addText($Left_Margin+700, $YPos, $FontSize, _('Approx. Cost'));

$YPos-=8;

//draw a line 
$pdf->line($XPos, $YPos-5,$Page_Width-$Right_Margin, $YPos-5);*/

$YPos -= ($line_height);
$FontSize=10;

?>
