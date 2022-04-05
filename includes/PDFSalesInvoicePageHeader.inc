<?php
/* $Id: PDFDIFOTPageHeader.inc 5680 2012-09-25 08:21:52Z daintree $*/
if ($PageNumber>1){
	$pdf->newPage();
}

$YPos = $Page_Height - $Top_Margin - 50;

$pdf->addJpegFromFile($_SESSION['LogoFile'],$Left_Margin,$YPos,0,50);

$FontSize=15;

$XPos = $Left_Margin;
//$YPos -= 40;
$pdf->addText($XPos, $YPos,$FontSize, _('Sales Invoice Inquiry Report'));
$FontSize=12;

if ($_POST['CategoryID']!='All') {
	$pdf->addText($XPos, $YPos-20,$FontSize, _('For Inventory Location') . ' ' . $_POST['Location'] . ' '. _('From') . ' ' . $_POST['FromDate'] . ' ' . _('to') . ' ' .  $_POST['ToDate']);
} else {
	$pdf->addText($XPos, $YPos-20,$FontSize, _('From') . ' ' . $_POST['FromDate'] . ' ' . _('to') . ' ' .  $_POST['ToDate']);
}
if ($_POST['SalesPerson']!='All'){
	$pdf->addText($XPos+300, $YPos-20, $FontSize, _('Sales Person : ') . ' '. $_POST['SalesPerson']);
}

$XPos = $Page_Width-$Right_Margin-50;
$YPos -=30;
$pdf->addText($XPos, $YPos,$FontSize, _('Page') . ': ' . $PageNumber);

/*Now print out the company name and address */
$XPos = $Left_Margin;
$YPos -= $line_height;


$YPos -= $line_height;
/*Set up headings */
$FontSize=8;

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,40,$FontSize,_('Inv Date'), 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+40,$YPos,200,$FontSize,_('Inv No'), 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+80,$YPos,100,$FontSize,_('Customer Name'), 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+230,$YPos,190,$FontSize,_('Customer Branch'), 'centre');
$LeftOvers = $pdf->addTextWrap($Left_Margin+360,$YPos,45,$FontSize,_('Order No'), 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+410,$YPos,45,$FontSize,_('Customer Ref'), 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+490,$YPos,50,$FontSize,_('Tax'), 'centre');
$LeftOvers = $pdf->addTextWrap($Left_Margin+560,$YPos,50,$FontSize,_('Amount'), 'centre');
$LeftOvers = $pdf->addTextWrap($Left_Margin+620,$YPos,80,$FontSize,_('Tot Amount'), 'centre');
$LeftOvers = $pdf->addTextWrap($Left_Margin+690,$YPos,50,$FontSize,_('Receipt'), 'centre');
$LeftOvers = $pdf->addTextWrap($Left_Margin+740,$YPos,50,$FontSize,_('Balance'), 'centre');

$YPos-=$line_height;

/*draw a line */
$pdf->line($XPos, $YPos,$Page_Width-$Right_Margin, $YPos);

$YPos -= ($line_height);
?>