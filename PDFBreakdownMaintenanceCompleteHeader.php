<?php
/* $Id: PDFTransPageHeader.inc 6826 2014-08-17 21:53:13Z rchacon $ */

/*	Please note that addTextWrap() prints a font-size-height further down than
	addText() and other functions. Use addText() instead of addTextWrap() to
	print left aligned elements.*/


if (!$FirstPage){ /* only initiate a new page if its not the first */
	$pdf->newPage();
}

$YPos = $Page_Height-$Top_Margin;

$pdf->addJpegFromFile($_SESSION['LogoFile'],$Page_Width/2 -120,$YPos-80,0,60);
$FontSize =15;

       // $first='';
		$second='Breakdown Maintenance Complete Form';
		//$pdf->addText($Page_Width - 265, $YPos, $FontSize, _($first), 'centre');
		$FontSize =13;
		$pdf->addText($Page_Width - 260, $YPos-15, $FontSize, _($second), 'centre');
		//$pdf->addText($Page_Width - 200, $YPos-33, $FontSize, _(''), 'centre');

$XPos = $Page_Width - 265;
$YPos -= 111;
/*draw a nice curved corner box around the maintenance  details */
/*from the top right */
$pdf->partEllipse($XPos+225,$YPos+100,0,90,10,10);
/*line to the top left */
$pdf->line($XPos+225, $YPos+110,$XPos, $YPos+110);
/*Dow top left corner */
$pdf->partEllipse($XPos, $YPos+100,90,180,10,10);
/*Do a line to the bottom left corner */
$pdf->line($XPos-10, $YPos+100,$XPos-10, $YPos+5);
/*Now do the bottom left corner 180 - 270 coming back west*/
$pdf->partEllipse($XPos, $YPos+5,180,270,10,10);
/*Now a line to the bottom right */
$pdf->line($XPos, $YPos-5,$XPos+225, $YPos-5);
/*Now do the bottom right corner */
$pdf->partEllipse($XPos+225, $YPos+5,270,360,10,10);
/*Finally join up to the top right corner where started */
$pdf->line($XPos+235, $YPos+5,$XPos+235, $YPos+100);

$YPos = $Page_Height - $Top_Margin - 25;

$FontSize = 10;

	//$pdf->addText($Page_Width-268, $YPos-26, $FontSize, _('Plan Control No'));
	$pdf->addText($Page_Width-268, $YPos-39, $FontSize, _('Date '));
	$pdf->addText($Page_Width-180, $YPos-39, $FontSize, date("Y/m/d"));
	$pdf->addText($Page_Width-268, $YPos-52, $FontSize, _('Job Card No'));
	$pdf->addText($Page_Width-180, $YPos-52, $FontSize, $myrow['cardno']);
	$pdf->addText($Page_Width-268, $YPos-65, $FontSize, _('Equipment'));
	$pdf->addText($Page_Width-180, $YPos-65, $FontSize, $myrow['mcno']);
	$pdf->addText($Page_Width-268, $YPos-78, $FontSize, _('Tech Name(s)'));
	$pdf->addText($Page_Width-180, $YPos-78, $FontSize, $myrow['userresponsible']);


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

$XPos = $Page_Width/2-120;


$XPos = $Left_Margin;
$YPos -= ($line_height*2);
$FontSize=12;
$msq='KENYA ORDNANCE FACTORIES CORPORATION  ';
$msq1='MAINTENANCE DEPARTMENT';
$msq2='MAINTENANCE COMPLETION FORM';

$pdf->addText($Left_Margin+230, $YPos, $FontSize, _($msq) . ': ');
$pdf->addText($Left_Margin+230, $YPos+10, $FontSize, _($msq1) . ': ');
$pdf->addText($Left_Margin+230, $YPos+20, $FontSize, _($msq2) . ': ');


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
$pdf->addText($Left_Margin + 2, $YPos, $FontSize, _('Problem Observed') . ':');
$pdf->addText($Left_Margin+500, $YPos, $FontSize, $myrow['problem'] );
/*Print a vertical line */

/*Print a vertical line */
$pdf->line($Left_Margin+200, $YPos+$line_height-6,$Left_Margin+200,$YPos-155);

//$pdf->addText($Left_Margin+453, $YPos, $FontSize, _('Requesting Officer') . ':');
//$pdf->addText($Left_Margin+550, $YPos, $FontSize, $myrow['requesting_officer']);

$pdf->addText($Left_Margin + 2, $YPos-20, $FontSize, _('Causes') . ':');
$pdf->addText($Left_Margin+500, $YPos-20, $FontSize, _() );

$pdf->line($XPos, $YPos-10,$Page_Width-$Right_Margin, $YPos-10);

$pdf->addText($Left_Margin + 2, $YPos-40, $FontSize, _('Undertaken') . ':');
$pdf->addText($Left_Margin+500, $YPos-40, $FontSize, _() );

$pdf->line($XPos, $YPos-30,$Page_Width-$Right_Margin, $YPos-30);

$pdf->addText($Left_Margin + 2, $YPos-60, $FontSize, _('Special tools for intervention/modification') . ':');
$pdf->addText($Left_Margin+500, $YPos-60, $FontSize, _() );

$pdf->line($XPos, $YPos-50,$Page_Width-$Right_Margin, $YPos-50);

$pdf->addText($Left_Margin + 2, $YPos-80, $FontSize, _('Spares Used') . ':');
$pdf->addText($Left_Margin+500, $YPos-80, $FontSize, _() );

$pdf->line($XPos, $YPos-70,$Page_Width-$Right_Margin, $YPos-70);

$pdf->addText($Left_Margin + 2, $YPos-100, $FontSize, _('Cost') . ':');
$pdf->addText($Left_Margin+500, $YPos-100, $FontSize,_() );

$pdf->line($XPos, $YPos-90,$Page_Width-$Right_Margin, $YPos-90);

$pdf->addText($Left_Margin + 2, $YPos-120, $FontSize, _('Reports/Suggestions') . ':');
$pdf->addText($Left_Margin+500, $YPos-120, $FontSize, _() );

$pdf->line($XPos, $YPos-110,$Page_Width-$Right_Margin, $YPos-110);


//$YPos -= 8;
/*draw a line */

//$YPos -= 12;

$TopOfColHeadings = $YPos-160;

$pdf->addText($Left_Margin+200, $YPos-160, $FontSize, _('Time End'). ':'.' '.$myrow['']);
$pdf->addText($Left_Margin+5, $YPos-160, $FontSize, _('Time Start'). ':'.' '.$time);
$pdf->addText($Left_Margin+400, $YPos-160, $FontSize, _('Start Date'). ':'.' '.$date);
$pdf->addText($Left_Margin+600, $YPos-160, $FontSize, _('End Date'). ':'.' '.$endate);

$pdf->line($XPos, $YPos-150,$Page_Width-$Right_Margin, $YPos-150);

$pdf->addText($Left_Margin+80, $YPos-180, $FontSize, _('Total Time:'));
$pdf->addText($Left_Margin+200, $YPos-180, $FontSize,$diff);

$pdf->line($XPos, $YPos-170,$Page_Width-$Right_Margin, $YPos-170);

$pdf->addText($Left_Margin+200, $YPos-200, $FontSize, _('Date'). ':');
$pdf->addText($Left_Margin+30, $YPos-200, $FontSize, _('CTMO'). ':');
$pdf->addText($Left_Margin+430, $YPos-200, $FontSize, _('P.OFFR SIGN'). ':');
$pdf->addText($Left_Margin+650, $YPos-200, $FontSize, _('Date'). ':');

$pdf->line($XPos, $YPos-190,$Page_Width-$Right_Margin, $YPos-190);

$pdf->addText($Left_Margin+200, $YPos-220, $FontSize, _());
$pdf->addText($Left_Margin+30, $YPos-220, $FontSize, _());
$pdf->addText($Left_Margin+430, $YPos-220, $FontSize, _());
$pdf->addText($Left_Margin+650, $YPos-220, $FontSize, _());

$pdf->line($XPos, $YPos-210,$Page_Width-$Right_Margin, $YPos-210);


$pdf->line($XPos, $YPos-230,$Page_Width-$Right_Margin, $YPos-230);
/*draw a line */


$YPos -= ($line_height);
$FontSize=10;

?>
