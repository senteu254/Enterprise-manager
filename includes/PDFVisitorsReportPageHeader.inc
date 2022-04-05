<?php

/* $Id: PDFInventoryPlanPageHeader.inc 3605 2010-07-11 20:59:10Z tim_schofield $*/
/*PDF page header for inventory planning report */

if ($PageNumber>1){
	$pdf->newPage();
}

$FontSize=10;
$YPos= $Page_Height-$Top_Margin;

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,$_SESSION['CompanyRecord']['coyname']);

$YPos -=$line_height;

$FontSize=10;

	$ReportTitle = _('Visitors Booking List Between') . ' ' . $_POST['datefrom'] . ' ' . _('to') . ' ' . $_POST['dateto'];

if ($_POST['Gate']=='All'){

	$LeftOvers = $pdf->addTextWrap($Left_Margin, $YPos,450,$FontSize, $ReportTitle . ' ' . _('for all Gates'));

} else {

	$LeftOvers = $pdf->addTextWrap($Left_Margin, $YPos,450,$FontSize, $ReportTitle . ' ' . _('for') . ' ' . $gate['description']);

}

$FontSize=10;
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-120,$YPos,120,$FontSize,_('Printed') . ': ' . Date($_SESSION['DefaultDateFormat']) . '   ' . _('Page') . ' ' . $PageNumber);

$YPos -=(2*$line_height);


/*Draw a rectangle to put the headings in     */

$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height);
$pdf->line($Left_Margin, $YPos+$line_height,$Left_Margin, $YPos- $line_height);
$pdf->line($Left_Margin, $YPos- $line_height,$Page_Width-$Right_Margin, $YPos- $line_height);
$pdf->line($Page_Width-$Right_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos- $line_height);

/*set up the headings */
$XPos = $Left_Margin+1;

$LeftOvers = $pdf->addTextWrap($XPos,$YPos,40,$FontSize,_('SN'),'left');
$LeftOvers = $pdf->addTextWrap(50,$YPos,150,$FontSize,_('Full Name of Visitor'),'left');
$LeftOvers = $pdf->addTextWrap(170,$YPos,100,$FontSize,_('ID/Passport No'),'left');
$LeftOvers = $pdf->addTextWrap(250,$YPos,100,$FontSize,_('From(Area)'),'left');
$LeftOvers = $pdf->addTextWrap(340,$YPos,130,$FontSize,_('Host'),'left');
$LeftOvers = $pdf->addTextWrap(460,$YPos,90,$FontSize,_('Department'),'left');
//$LeftOvers = $pdf->addTextWrap(480,$YPos,120,$FontSize,_('Purpose Of Visit'),'left');
$LeftOvers = $pdf->addTextWrap(620,$YPos,40,$FontSize,_('Time In'),'left');
$LeftOvers = $pdf->addTextWrap(730,$YPos,40,$FontSize,_('Time Out'),'left');
//$LeftOvers = $pdf->addTextWrap(670,$YPos,80,$FontSize,_('Sec Officer'),'left');
//$LeftOvers = $pdf->addTextWrap(740,$YPos,80,$FontSize,_('Remarks'),'left');

$YPos =$YPos - ($line_height);
$FontSize=10;
?>