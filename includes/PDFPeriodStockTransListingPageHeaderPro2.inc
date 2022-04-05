<?php
/* $Id: PDFPeriodStockTransListingPageHeader.inc 4307 2010-12-22 16:06:03Z tim_schofield $*/

if ($PageNumber>1){
	$pdf->newPage();
}

$YPos = $Page_Height - $Top_Margin - 50;

$pdf->addJpegFromFile($_SESSION['LogoFile'],$Left_Margin,$YPos,0,50);

$FontSize=15;


$XPos = $Page_Width/2;
$YPos += 30;
$pdf->addText($XPos-50, $YPos,$FontSize, $_SESSION['CompanyRecord']['coyname']);
$FontSize=12;
$YPos -=30;
$pdf->addText($XPos, $YPos,$FontSize, $TransType . ' ' ._('dated from') . ' ' . $_POST['FromDate'] . ' ' . _('to') . ' ' . $_POST['ToDate']);


$XPos = $Page_Width-$Right_Margin-50;
$YPos -=30;
$pdf->addText($XPos, $YPos+10,$FontSize, _('Page') . ': ' . $PageNumber);

/*Now print out the company name and address */
$XPos = $Left_Margin;
$YPos -= $line_height;

/*draw a square grid for entering line items */
$pdf->line($XPos, $YPos,$Page_Width-$Right_Margin, $YPos);
$pdf->line($Page_Width-$Right_Margin, $YPos,$Page_Width-$Right_Margin, $Bottom_Margin);
$pdf->line($Page_Width-$Right_Margin, $Bottom_Margin,$XPos, $Bottom_Margin);
$pdf->line($Page_Width-$Right_Margin, $Bottom_Margin+20,$XPos+345, $Bottom_Margin+20);
$pdf->line($XPos, $Bottom_Margin,$XPos, $YPos);

$pdf->line($Left_Margin+170, $YPos,$Left_Margin+170, $Bottom_Margin);
$pdf->line($Left_Margin+230, $YPos,$Left_Margin+230, $Bottom_Margin);
$pdf->line($Left_Margin+280, $YPos,$Left_Margin+280, $Bottom_Margin);
$pdf->line($Left_Margin+344, $YPos,$Left_Margin+344, $Bottom_Margin);
$pdf->line($Left_Margin+394, $YPos,$Left_Margin+394, $Bottom_Margin);
$pdf->line($Left_Margin+454, $YPos,$Left_Margin+454, $Bottom_Margin);

$YPos -= $line_height;
/*Set up headings */
$FontSize=8;

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,160,$FontSize,_('Stock Item'), 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+172,$YPos,80,$FontSize,_('Work Order'), 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+232,$YPos,70,$FontSize,_('Trans Date'), 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+272,$YPos,70,$FontSize,_('Qty Accepted'), 'right');
$LeftOvers = $pdf->addTextWrap($Left_Margin+325,$YPos,70,$FontSize,_('Qty Rejected'), 'right');
$LeftOvers = $pdf->addTextWrap($Left_Margin+385,$YPos,70,$FontSize,_('Total Quantity'), 'right');
$LeftOvers = $pdf->addTextWrap($Left_Margin+442,$YPos,70,$FontSize,_('Qty on Hand'), 'right');
$YPos-=$line_height;

/*draw a line */
$pdf->line($XPos, $YPos,$Page_Width-$Right_Margin, $YPos);

$YPos -= ($line_height);
?>