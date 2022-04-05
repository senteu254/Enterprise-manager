<?php
if (!$FirstPage){ /* only initiate a new page if its not the first */
	$pdf->newPage();
}
$YPos = $Page_Height-$Top_Margin;

$pdf->addJpegFromFile($_SESSION['LogoFile'],$Page_Width/2 -30,$YPos-35,0,60);
$FontSize =10;
$FORM='FORM KOFC10';
$pdf->addText($XPos=735, $YPos+10, $FontSize, _($FORM) . ' ');

$msq='REQUEST FOR QUOTATION';

$quotationNo=$myrow4['quotation'];
//$pdf->SetTextColor(0,0,0);
$printedNn = 'Printed On '.date($_SESSION['DefaultDateFormat']);
$pdf->addText($XPos=365, $YPos-45, $FontSize, _($msq) . ' ');
$pdf->SetTextColor(255,0,0);
$pdf->addText($XPos=355, $YPos-55, $FontSize, _($quotationNo) . ' ');
$pdf->SetTextColor(0,0,0);
$pdf->addText($XPos=390, $YPos-65, $FontSize, _($printedNn) . ' ');

     
	$date = 'FROM ';
	$pdf->addText($XPos=570, $YPos-100, $FontSize, $_SESSION['CompanyRecord']['coyname']);
	$pdf->addText($XPos=570, $YPos-110, $FontSize, $_SESSION['CompanyRecord']['regoffice1']);
	$pdf->addText($XPos=570, $YPos-120, $FontSize, $_SESSION['CompanyRecord']['regoffice2']);
	$pdf->addText($XPos=570, $YPos-130,$FontSize, $_SESSION['CompanyRecord']['regoffice3'] . ' ' . $_SESSION['CompanyRecord']['regoffice4'] . ' ' . $_SESSION['CompanyRecord']['regoffice5']);
	$pdf->addText($XPos=570, $YPos-130,$FontSize, $_SESSION['CompanyRecord']['regoffice6']);
	$pdf->addText($XPos=570, $YPos-140,$FontSize, _('Phone') . ':' . $_SESSION['CompanyRecord']['telephone'] . ' ' . _('Fax') . ': ' . $_SESSION['CompanyRecord']['fax']);
		//$pdf->addText($Page_Width - 265, $YPos, $FontSize, _($first), 'centre');
		$FontSize =13;
		$pdf->addText($Page_Width - 260, $YPos-50, $FontSize, _($second), 'centre');
		$pdf->addText($Page_Width - 195, $YPos-80, $FontSize, _($date), 'centre');
		//$pdf->addText($Page_Width - 200, $YPos-33, $FontSize, _(''), 'centre');

$XPos = $Page_Width - 265;
$YPos -= 190;
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

$YPos = $Page_Height - $Top_Margin - 95;

$FontSize = 10;


$pdf->addText($Page_Width-108, $YPos+30, $FontSize, _('Page'));
$pdf->addText($Page_Width-70, $YPos+30, $FontSize, $PageNumber);

/*End of the text in the right side box */

/*Now print out the company name and address in the middle under the logo */
/****************************************************************************************************/
$XPos = $Page_Width - 793;
$YPos -= 90;
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
/****************************************************************************************************/
$XPos = $Left_Margin;
$YPos = $Page_Height - $Top_Margin;
$FontSize=10;

$pdf->addText($XPos+95, $YPos-85, $FontSize,'TO');
$pdf->addText($XPos, $YPos-100, $FontSize,$myrow4['suppname']);
//$pdf->addText($XPos, $YPos-115,$FontSize,$myrow4['address1']);
$XPos = $Left_Margin;
$pdf->addText($XPos, $YPos-110, $FontSize, _('Phone') . ':' . $_SESSION['CompanyRecord']['telephone'] . ' ' . _('Fax') . ': ' . $_SESSION['CompanyRecord']['fax']);
$pdf->addText($XPos, $YPos-120, $FontSize,$myrow4['email']);
$pdf->addText($XPos, $YPos-128, $FontSize,$myrow4['address6']);


$XPos = $Left_Margin;

$YPos = $Page_Height - $Top_Margin - 135;
/*draw a line under the company address and charge to address
$pdf->line($XPos, $YPos,$Right_Margin, $YPos); */

//$XPos = $Page_Width/2;

//$XPos = $Left_Margin;
$YPos -= ($line_height*2)-3;
//$pdf->addText($Left_Margin, $YPos-8, $FontSize, _($msq) . ': ');
/*draw a box with nice round corner for entering line items */
/*90 degree arc at top right of box 0 degrees starts a bottom */
/***********************************************not editet***************************************************************/
$pdf->partEllipse($Page_Width-$Right_Margin-5, $Bottom_Margin+290,0,90,5,5);
/*line to the top left */
$pdf->line($Page_Width-$Right_Margin-5, $Bottom_Margin+295,$Left_Margin+5, $Bottom_Margin+295);
/*Dow top left corner */
$pdf->partEllipse($Left_Margin+5, $Bottom_Margin+290,90,180,5,5);
/*Do a line to the bottom left corner */
$pdf->line($Left_Margin, $Bottom_Margin+290,$Left_Margin, $Bottom_Margin+10);
/*Now do the bottom left corner 180 - 270 coming back west*/
$pdf->partEllipse($Left_Margin+10, $Bottom_Margin+10,180,270,10,10);
/*Now a line to the bottom right */
$pdf->line($Left_Margin+10, $Bottom_Margin,$Page_Width-$Right_Margin-10, $Bottom_Margin);
/*Now do the bottom right corner */
$pdf->partEllipse($Page_Width-$Right_Margin-10, $Bottom_Margin+10,270,360,10,10);
/*Finally join up to the top right corner where started */
$pdf->line($Page_Width-$Right_Margin, $Bottom_Margin+10,$Page_Width-$Right_Margin, $Bottom_Margin+290);
/**********************************************not edited****************************************************************/

$YPos -= ($line_height*3);
/*Set up headings */
//$FontSize=10;
$FontSize =13;
$FontSize=8;
/*Print a vertical line */
$msq1='You are invited to submit quotation on materials  listed below';
$pdf->addText($XPos, $YPos+45,$FontSize, _($msq1) . ' ');
$msq1='NOTE:';
$pdf->addText($XPos, $YPos+35,$FontSize, _($msq1) . ' ');
$msq1='a)THIS IS NOT AN ORDER';
$pdf->addText($XPos, $YPos+25,$FontSize, _($msq1) . ' ');
$pdf->setfont('',B);
$msq1='b)This quotation should be submited to a buyer in a sealed envelop Not later than 0930 hours on '.$myrow4['requiredbydate'].'';
$pdf->setfont('','');
$pdf->addText($XPos, $YPos+15,$FontSize, _($msq1) . ' ');
$msq1='c)Your quotation should indicate the final unit price which includes all costs for delivery, discount, duty, sales tax and terms of payment.';
$pdf->addText($XPos, $YPos+5,$FontSize, _($msq1) . ' ');
$pdf->setfont('',B);
$sql8 = DB_query("SELECT locationname FROM locations WHERE loccode='" . $myrow4['location'] . "'");
		$myrow7 = DB_fetch_array($sql8);
$msqo=$myrow7['locationname'];
$pdf->setfont('','');
$pdf->addText($XPos+350, $YPos-5,$FontSize, _($msqo) . ' ');

$YPos -= 8;
/*draw a line */
//$pdf->line($XPos, $YPos-10,$Page_Width-$Right_Margin, $YPos-10);

$YPos -= 12;

$TopOfColHeadings = $YPos-35;
$pdf->setfont('',B);
$pdf->addText($Left_Margin+0, $YPos+3, $FontSize, _('SNo.'));
$pdf->addText($Left_Margin+25, $YPos+3, $FontSize, _('ITEM DESCRIPTION & SPECIFICATIONS'));
$pdf->addText($Left_Margin+313, $YPos+3, $FontSize, _('Unit'));
$pdf->addText($Left_Margin+355, $YPos+3, $FontSize, _('QTY RQD'));
$pdf->addText($Left_Margin+400, $YPos+3, $FontSize, _('Unit Price'));
$pdf->addText($Left_Margin+450, $YPos+3, $FontSize, _('Total Amount'));
$pdf->addText($Left_Margin+511, $YPos+3, $FontSize, _('Days To Deliver'));
$pdf->addText($Left_Margin+580, $YPos+3, $FontSize, _('Brand'));
$pdf->addText($Left_Margin+650, $YPos+3, $FontSize, _('Country of Origin'));
$pdf->addText($Left_Margin+720, $YPos+3, $FontSize, _('Remarks'));
$pdf->setfont('','');
$YPos-=5;

/*draw a line 
$pdf->line($XPos, $YPos-5,$Page_Width-$Right_Margin, $YPos-5);
$pdf->line($XPos, $YPos-20,$Page_Width-$Right_Margin, $YPos-20);
$pdf->line($XPos, $YPos-35,$Page_Width-$Right_Margin, $YPos-35);
$pdf->line($XPos, $YPos-51,$Page_Width-$Right_Margin, $YPos-51);
$pdf->line($XPos, $YPos-66,$Page_Width-$Right_Margin, $YPos-66);
$pdf->line($XPos, $YPos-82,$Page_Width-$Right_Margin, $YPos-82);
$pdf->line($XPos, $YPos-98,$Page_Width-$Right_Margin, $YPos-98);
$pdf->line($XPos, $YPos-113,$Page_Width-$Right_Margin, $YPos-113);
$pdf->line($XPos, $YPos-129,$Page_Width-$Right_Margin, $YPos-129);
$pdf->line($XPos, $YPos-145,$Page_Width-$Right_Margin, $YPos-145);
$pdf->line($XPos, $YPos-161,$Page_Width-$Right_Margin, $YPos-161);
$pdf->line($XPos, $YPos-177,$Page_Width-$Right_Margin, $YPos-177);
$pdf->line($XPos, $YPos-193,$Page_Width-$Right_Margin, $YPos-193);
$pdf->line($XPos, $YPos-209,$Page_Width-$Right_Margin, $YPos-209);
$pdf->line($XPos, $YPos-224,$Page_Width-$Right_Margin, $YPos-224);
$pdf->line($XPos, $YPos-242,$Page_Width-$Right_Margin, $YPos-242);
$pdf->line($XPos, $YPos-258,$Page_Width-$Right_Margin, $YPos-258);
$pdf->line($XPos, $YPos-274,$Page_Width-$Right_Margin, $YPos-274);
//$pdf->line($XPos, $YPos-290,$Page_Width-$Right_Margin, $YPos-290);
***/
$YPos -= ($line_height);
$FontSize=10;

?>
