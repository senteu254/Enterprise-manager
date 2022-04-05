<?php
/* $Id: PDFBOMListingPageHeader.inc 3242 2009-12-16 22:06:53Z tim_schofield $*/
/*PDF page header for inventory valuation report */

$PageNumber++;
/* first time round $PageNumber will only be 1 and page created on initiation of $pdf */
if ($PageNumber>1){
	$pdf->newPage();
}

$FontSize=8;
$YPos= $Page_Height-$Top_Margin;
$pdf->addJpegFromFile($_SESSION['LogoFile'],$Page_Width/2 -260,$YPos-35,0,60);

$pdf->addText($Left_Margin+80, $YPos+10,$FontSize, $_SESSION['CompanyRecord']['coyname']);
$pdf->addText($Left_Margin+80, $YPos, $FontSize, $_SESSION['CompanyRecord']['regoffice1']);
$pdf->addText($Left_Margin+80, $YPos-10, $FontSize, $_SESSION['CompanyRecord']['regoffice2']);
$pdf->addText($Left_Margin+80, $YPos-20,$FontSize, $_SESSION['CompanyRecord']['regoffice3'] . ' ' . $_SESSION['CompanyRecord']['regoffice4'] . ' ' . $_SESSION['CompanyRecord']['regoffice5']);
	$pdf->addText($Left_Margin+80, $YPos-30,$FontSize, $_SESSION['CompanyRecord']['regoffice6']);

$YPos -=$line_height;

$FontSize =10;

$pdf->addText($Left_Margin, $YPos-55, $FontSize, _('Procurement Status Report'));



$FontSize = 8;
$pdf->addText($Page_Width-$Right_Margin-120,$YPos,$FontSize, _('Printed') . ': ' . Date($_SESSION['DefaultDateFormat']) . '   ' . _('Page') . ' ' . $PageNumber);

$YPos -=(7*$line_height);

/*Draw a rectangle to put the headings in     */
$pdf->line($Page_Width-$Right_Margin, $YPos-5,$Left_Margin, $YPos-5);
$pdf->line($Page_Width-$Right_Margin, $YPos+$line_height,$Left_Margin, $YPos+$line_height);
$pdf->line($Page_Width-$Right_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos-5);
$pdf->line($Left_Margin, $YPos+$line_height,$Left_Margin, $YPos-5);

/*set up the headings */
$Xpos = $Left_Margin+1;

$LeftOvers = $pdf->addTextWrap($Xpos,$YPos,320 - $Left_Margin,$FontSize,_('Descriptions'),'centre');
$LeftOvers = $pdf->addTextWrap(300,$YPos,100,$FontSize,_('Qty Ord'),'centre');
$LeftOvers = $pdf->addTextWrap(350,$YPos,100,$FontSize,_('Qty Recd'),'centre');
$LeftOvers = $pdf->addTextWrap(400,$YPos,100,$FontSize,_('Qty Invoiced'),'centre');
$LeftOvers = $pdf->addTextWrap(460,$YPos,100,$FontSize,_('Type'),'centre');
$LeftOvers = $pdf->addTextWrap(496,$YPos,100,$FontSize,_('Nature'),'centre');
$LeftOvers = $pdf->addTextWrap(530,$YPos,100,$FontSize,_('Status'),'centre');

$YPos =$YPos - (2*$line_height);

$FontSize=10;
?>