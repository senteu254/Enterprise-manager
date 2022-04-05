<?php
/* $Id: PDFTransPageHeader.inc 6826 2014-08-17 21:53:13Z rchacon $ */

/*	Please note that addTextWrap() prints a font-size-height further down than
	addText() and other functions. Use addText() instead of addTextWrap() to
	print left aligned elements.*/


if (!$FirstPage){ /* only initiate a new page if its not the first */
	$pdf->newPage();
}

$YPos = $Page_Height-$Top_Margin;

$pdf->addJpegFromFile($_SESSION['LogoFile'],$Page_Width/2 -70,$YPos-60,0,60);
$FontSize =8;
   
		$second='Orders based on Supplier Group Type  ';
		$date = 'Date Range:' .ConvertSQLDate($DateFrom) .'  To  '.ConvertSQLDate($DateTo).'';
		$SourceResult= DB_query("SELECT groupname FROM  suppliergrouptype
								         WHERE groupid='".$_POST['StockCat'] ."'");
							$my = DB_fetch_array($SourceResult);
	if($_POST['StockCat']=="All"){
$grouptype = 'Group Type: All';
}else{
		$grouptype = 'Group Type: '.$my['groupname'].'';
	}
	if($_POST['OrderType']=="LPO"){
$ordertype = 'Type of Order: LPO';
}else{
		$ordertype = 'Type of Order: LSO';
	}
		//$pdf->addText($Page_Width - 265, $YPos, $FontSize, _($first), 'centre');
		$FontSize =13;
		 $pdf->setfont('',B);
		$pdf->addText($Page_Width - 270, $YPos-8, $FontSize, _($second), 'centre');
		$pdf->setfont('','');
		$pdf->addText($Page_Width - 275, $YPos-24, $FontSize, _($date), 'centre');
		$pdf->addText($Page_Width - 275, $YPos-40, $FontSize, _($grouptype), 'centre');
		$pdf->addText($Page_Width - 275, $YPos-55, $FontSize, _($ordertype), 'centre');
		//$pdf->addText($Page_Width - 200, $YPos-33, $FontSize, _(''), 'centre');
$FontSize =10;
$XPos = $Page_Width - 265;
$YPos -= 111;
/*draw a nice curved corner box around the billing details */
/*from the top right */
$pdf->partEllipse($XPos+225,$YPos+100,0,90,10,10);
/*line to the top left */
$pdf->line($XPos+225, $YPos+110,$XPos, $YPos+110);
/*Dow top left corner */
$pdf->partEllipse($XPos, $YPos+100,90,180,10,10);
/*Do a line to the bottom left corner */
$pdf->line($XPos-10, $YPos+100,$XPos-10, $YPos+45);
/*Now do the bottom left corner 180 - 270 coming back west*/
$pdf->partEllipse($XPos, $YPos+45,180,270,10,10);
/*Now a line to the bottom right */
$pdf->line($XPos, $YPos+35,$XPos+225, $YPos+35);
/*Now do the bottom right corner */
$pdf->partEllipse($XPos+225, $YPos+45,270,360,10,10);
/*Finally join up to the top right corner where started */
$pdf->line($XPos+235, $YPos+45,$XPos+235, $YPos+100);

$YPos = $Page_Height - $Top_Margin - 25;

$FontSize = 10;
$pdf->addText($Page_Width-268, $YPos-93, $FontSize, _('Page'));
$pdf->addText($Page_Width-180, $YPos-93, $FontSize, $PageNumber);

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

//$msq='Please Check the under-listed Voted Account';

$pdf->addText($Left_Margin, $YPos-8, $FontSize, _($msq) . ': ');
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
//$FontSize=10;
$FontSize =13;
$FontSize=10;
$YPos -=10;
/*draw a line */
//$pdf->line($XPos, $YPos-10,$Page_Width-$Right_Margin, $YPos-10);

$msqo = 'Printed On: '.date($_SESSION['DefaultDateFormat']);
$pdf->addText($XPos+10, $YPos+34,$FontSize, _($msqo) . ' ');

$YPos += 14;

$TopOfColHeadings = $YPos-12;

$pdf->addText($Left_Margin+6, $YPos, $FontSize, _('SNo.'));
$pdf->addText($Left_Margin+50, $YPos, $FontSize, _('Order#'));
$pdf->addText($Left_Margin+121, $YPos, $FontSize, _('Date Ordered'));
$pdf->addText($Left_Margin+201, $YPos, $FontSize, _('Date Delivered'));
$pdf->addText($Left_Margin+281, $YPos, $FontSize, _('supplier Name'));
$pdf->addText($Left_Margin+580, $YPos, $FontSize, _('Currency'));
$pdf->addText($Left_Margin+660, $YPos, $FontSize, _('Amount'));
$pdf->addText($Left_Margin+720, $YPos, $FontSize, _('Status'));
//$pdf->addText($Left_Margin+555, $YPos, $FontSize, _('Cummulative Commitments'));
//$pdf->addText($Left_Margin+585, $YPos, $FontSize, _('Total Cost'));
//$pdf->addText($Left_Margin+690, $YPos, $FontSize, _('Total Cost'));

$YPos-=8;

/*draw a line */
$pdf->line($XPos, $YPos-5,$Page_Width-$Right_Margin, $YPos-5);

$YPos -= ($line_height);
$FontSize=10;

?>
