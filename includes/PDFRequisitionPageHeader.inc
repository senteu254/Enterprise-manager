<?php

if ($PageNumber>1){
	$pdf->newPage();
}

$YPos = $Page_Height - $Top_Margin - 50;

$pdf->addJpegFromFile($_SESSION['LogoFile'],$Left_Margin,$YPos,0,50);

$FontSize=15;
$sqlds = "SELECT locations.loccode, locationname FROM locations WHERE loccode='".$_POST['StockLocation']."'";
	$resultStockLocation = DB_query($sqlds);
	$myrloc=DB_fetch_array($resultStockLocation);
$sqldz = "SELECT departmentid, description FROM departments WHERE departmentid='".$_POST['department']."'";
	$resultTypez = DB_query($sqldz);
	$myrz=DB_fetch_array($resultTypez);
$sqld = "SELECT doc_id, doc_name FROM irq_documents WHERE doc_id='".$_POST['Type']."'";
	$resultType = DB_query($sqld);
	$myr=DB_fetch_array($resultType);

if($_POST['department']==0){
$myrz['description'] ="All";
}
if($_POST['StockLocation']==0){
$myrloc['locationname']="All";
}

//$XPos = $Page_Width/2;
$YPos += 30;
$pdf->addText($XPos+110, $YPos+25,$FontSize, $_SESSION['CompanyRecord']['coyname']);
$FontSize=12;
$pdf->addText($XPos+110, $YPos+5,$FontSize, 'Department : '.$myrz['description']);
$pdf->addText($XPos+110, $YPos-10,$FontSize, 'Location      : '.$myrloc['locationname']);
$YPos -=30;
$pdf->addText($XPos+110, $YPos,$FontSize, $myr['doc_name'] . ' ' ._('dated from') . ' ' . $_POST['FromDate'] . ' ' . _('to') . ' ' . $_POST['ToDate']);


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
//$pdf->line($Page_Width-$Right_Margin, $Bottom_Margin+20,$XPos+345, $Bottom_Margin+20);
$pdf->line($XPos, $Bottom_Margin,$XPos, $YPos);

$pdf->line($Left_Margin+70, $YPos,$Left_Margin+70, $Bottom_Margin);
$pdf->line($Left_Margin+125, $YPos,$Left_Margin+125, $Bottom_Margin);
$pdf->line($Left_Margin+275, $YPos,$Left_Margin+275, $Bottom_Margin);

$YPos -= $line_height;
/*Set up headings */
$FontSize=8;

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,160,$FontSize,_('Date Req'), 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+75,$YPos,70,$FontSize,_('Req No'), 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+130,$YPos,70,$FontSize,_('Requester'), 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos,70,$FontSize,_('Item Code'), 'right');
$LeftOvers = $pdf->addTextWrap($Left_Margin+320,$YPos,70,$FontSize,_('Description'), 'right');
$LeftOvers = $pdf->addTextWrap($Left_Margin+550,$YPos,70,$FontSize,_('Qty Req'), 'right');
$LeftOvers = $pdf->addTextWrap($Left_Margin+600,$YPos,70,$FontSize,_('Qty Issued'), 'right');
$LeftOvers = $pdf->addTextWrap($Left_Margin+700,$YPos,70,$FontSize,_('Status'), 'right');
$YPos-=$line_height;

/*draw a line */
$pdf->line($XPos, $YPos,$Page_Width-$Right_Margin, $YPos);

$YPos -= ($line_height);
?>