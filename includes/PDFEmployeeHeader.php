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
$FontSize =15;

       // $first='';
		$second='Employees Report';
		//$date = 'AT '.date($_SESSION['DefaultDateFormat']);
		//$pdf->addText($Page_Width - 265, $YPos, $FontSize, _($first), 'centre');
		$FontSize =13;
		$pdf->addText($Page_Width - 160, $YPos-8, $FontSize, _($second), 'centre');
		//$pdf->addText($Page_Width - 205, $YPos-24, $FontSize, _($date), 'centre');
		//$pdf->addText($Page_Width - 200, $YPos-33, $FontSize, _(''), 'centre');

$XPos = $Page_Width - 265;
$YPos -= 111;
/*draw a nice curved corner box around the billing details */
/*from the top right */
//$pdf->partEllipse($XPos+225,$YPos+100,0,90,10,10);
/*line to the top left */
//$pdf->line($XPos+225, $YPos+110,$XPos, $YPos+110);
/*Dow top left corner */
//$pdf->partEllipse($XPos, $YPos+100,90,180,10,10);
/*Do a line to the bottom left corner */
//$pdf->line($XPos-10, $YPos+100,$XPos-10, $YPos+45);
/*Now do the bottom left corner 180 - 270 coming back west*/
//$pdf->partEllipse($XPos, $YPos+45,180,270,10,10);
/*Now a line to the bottom right */
//$pdf->line($XPos, $YPos+35,$XPos+225, $YPos+35);
/*Now do the bottom right corner */
//$pdf->partEllipse($XPos+225, $YPos+45,270,360,10,10);
/*Finally join up to the top right corner where started */
//$pdf->line($XPos+235, $YPos+45,$XPos+235, $YPos+100);

$YPos = $Page_Height - $Top_Margin - 25;

$FontSize = 10;

	//$pdf->addText($Page_Width-268, $YPos-16, $FontSize, _('Address:6634 Eldoret'));
	//$pdf->addText($Page_Width-268, $YPos-39, $FontSize, _('Date '));
	//$pdf->addText($Page_Width-268, $YPos-39, $FontSize, _('Department:Procurement'));
	//$pdf->addText($Page_Width-180, $YPos-39, $FontSize, ConvertSQLDate["'.date("d-m-Y").'"]);
	//$pdf->addText($Page_Width-180, $YPos-39, $FontSize, $myrow['Requesteddate']);
	//$pdf->addText($Page_Width-268, $YPos-30, $FontSize, _('Financial Year '.': '.Date('Y',YearEndDate($_SESSION['YearEnd'],-1)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd']))));
	//$pdf->addText($Page_Width-180, $YPos-52, $FontSize, $myrow['year']);
	//$pdf->addText($Page_Width-268, $YPos-65, $FontSize, _('Submitted By:Admin'));
	//$pdf->addText($Page_Width-180, $YPos-65, $FontSize, $myrow['requesting_officer']);


$pdf->addText($Page_Width-128, $YPos-60, $FontSize, _('Page'));
$pdf->addText($Page_Width-80, $YPos-60, $FontSize, $PageNumber);

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
//$YPos -= ($line_height*2);

$msq='Please Check the under-listed Employees Details';

$pdf->addText($Left_Margin, $YPos-8, $FontSize, _($msq) . ': ');
/*draw a box with nice round corner for entering line items */
/*90 degree arc at top right of box 0 degrees starts a bottom */
$pdf->partEllipse($Page_Width-$Right_Margin-10, $Bottom_Margin+427,0,90,10,10);
/*line to the top left */
$pdf->line($Page_Width-$Right_Margin-10, $Bottom_Margin+437,$Left_Margin+10, $Bottom_Margin+437);
/*Dow top left corner */
$pdf->partEllipse($Left_Margin+10, $Bottom_Margin+427,90,180,10,10);
/*Do a line to the bottom left corner */
$pdf->line($Left_Margin, $Bottom_Margin+427,$Left_Margin, $Bottom_Margin+10);
/*Now do the bottom left corner 180 - 270 coming back west*/
$pdf->partEllipse($Left_Margin+10, $Bottom_Margin+10,180,270,10,10);
/*Now a line to the bottom right */
$pdf->line($Left_Margin+10, $Bottom_Margin,$Page_Width-$Right_Margin-10, $Bottom_Margin);
/*Now do the bottom right corner */
$pdf->partEllipse($Page_Width-$Right_Margin-10, $Bottom_Margin+10,270,360,10,10);
/*Finally join up to the top right corner where started */
$pdf->line($Page_Width-$Right_Margin, $Bottom_Margin+10,$Page_Width-$Right_Margin, $Bottom_Margin+427);


//$YPos -= ($line_height*2);
/*Set up headings */
//$FontSize=10;
//$FontSize =13;
//$pdf->addText($Left_Margin + 20, $YPos, $FontSize, _('ACCOUNTS') . ':');
//$pdf->addText($Left_Margin+330, $YPos, $FontSize, _('COMMITMENTS') . '');
//$pdf->addText($Left_Margin+600, $YPos, $FontSize, _('PAYMENTS') . '');

$FontSize=8;
/*Print a vertical line */

/*Print a vertical line */
//$pdf->line($Left_Margin+240, $YPos+$line_height-7,$Left_Margin+240,$YPos-18);

//$pdf->addText($Left_Margin+453, $YPos, $FontSize, _('Requesting Officer') . ':');
//$pdf->addText($Left_Margin+550, $YPos, $FontSize, $myrow['requesting_officer']);

$YPos -= 8;
/*draw a line */
//$pdf->line($XPos, $YPos-10,$Page_Width-$Right_Margin, $YPos-10);

$YPos -= 12;

$TopOfColHeadings = $YPos-10;

$pdf->addText($Left_Margin+5, $YPos, $FontSize, _('No.'));
$pdf->addText($Left_Margin+75, $YPos, $FontSize, _('Employee Name'));
$pdf->addText($Left_Margin+245, $YPos, $FontSize, _('ID Number'));
$pdf->addText($Left_Margin+300, $YPos, $FontSize, _('Gender'));
$pdf->addText($Left_Margin+380, $YPos, $FontSize, _('Department'));
$pdf->addText($Left_Margin+470, $YPos, $FontSize, _('Section'));
$pdf->addText($Left_Margin+575, $YPos, $FontSize, _('Band'));
$pdf->addText($Left_Margin+630, $YPos, $FontSize, _('Appointment'));
$pdf->addText($Left_Margin+720, $YPos, $FontSize, _('Grade'));
//$pdf->addText($Left_Margin+555, $YPos, $FontSize, _('Cummulative Commitments'));
//$pdf->addText($Left_Margin+585, $YPos, $FontSize, _('Total Cost'));
//$pdf->addText($Left_Margin+690, $YPos, $FontSize, _('Total Cost'));

$YPos-=8;

/*draw a line */
$pdf->line($XPos, $YPos-5,$Page_Width-$Right_Margin, $YPos-5);

$YPos -= ($line_height);
$FontSize=10;

?>
