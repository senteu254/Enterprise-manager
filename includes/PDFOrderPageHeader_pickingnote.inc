<?php
/* $Id: PDFOrderPageHeader_generic.inc 5816 2013-02-07 07:54:41Z daintree $*/
/* pdf-php by R&OS code to set up a new sales order page */
if ($PageNumber>1){
	$pdf->newPage();
}

$XPos = 46;
$YPos = 770;
/* if the deliver blind flag is set on the order, we do not want to output
the company logo */
if ($DeliverBlind < 2) {
    $pdf->addJpegFromFile($_SESSION['LogoFile'],$XPos,$YPos,0,60);
}
$XPos += 90;
$YPos += 60;
if ($DeliverBlind < 2) {
    $FontSize=11;
    $pdf->addText($XPos, $YPos,$FontSize, $_SESSION['CompanyRecord']['coyname']);
    $FontSize =8;
    $pdf->addText($XPos, $YPos-15,$FontSize, $_SESSION['CompanyRecord']['regoffice1']);
    $pdf->addText($XPos, $YPos-24,$FontSize, $_SESSION['CompanyRecord']['regoffice2'] . ' '. $_SESSION['CompanyRecord']['regoffice3'] . ' ' . $_SESSION['CompanyRecord']['regoffice4'] . ' ' . $_SESSION['CompanyRecord']['regoffice5']);
    $pdf->addText($XPos, $YPos-33,$FontSize, _('Tel No') . ': ' . $_SESSION['CompanyRecord']['telephone'] . ' ' . _('Fax'). ': ' . $_SESSION['CompanyRecord']['fax']);
    $pdf->addText($XPos, $YPos-42,$FontSize, $_SESSION['CompanyRecord']['email']);
}


$XPos -= 80;
$YPos -= 60;

$FontSize=12;
$pdf->addText($XPos+180, $YPos+16,$FontSize, _('PICKING NOTE') );
$FontSize=9;
$pdf->addText($XPos, $YPos,$FontSize, _('Delivered To').':' );
$pdf->addText($XPos+70, $YPos,$FontSize, $myrow['deliverto']);
$pdf->addText($XPos+70, $YPos-12,$FontSize, $myrow['deladd1'].'-' . $myrow['deladd5']);
$pdf->addText($XPos+70, $YPos-24,$FontSize, $myrow['deladd3'] . ' ' . $myrow['deladd4'] . ' ' . $myrow['deladd6']);

$XPos += 290;

$pdf->addText($XPos, $YPos-2,$FontSize, _('PN No'). ':');
$pdf->addText($XPos+50, $YPos,13, $_GET['TransNo']);
$pdf->addText($XPos+110, $YPos-2,$FontSize, _('Order No'). ':'.$myrow['orderno']);
//$pdf->addText($XPos, $YPos-13,$FontSize,  _('Delivery Date'). ':'.ConvertSQLDate($myrow['deliverydate'])); 
$pdf->addText($XPos, $YPos-13,$FontSize,  _('Printed') . ': '.Date($_SESSION['DefaultDateFormat']));
$pdf->addText($XPos,$YPos-25,$FontSize,_('Comments').':'.stripcslashes($myrow['comments']));

$XPos -= 300;
$YPos -= 91;
/*draw a nice curved corner box around the billing details */
/*from the top right */
$pdf->partEllipse($XPos+500,$YPos+80,0,90,10,10);
/*line to the top left */
$pdf->line($XPos+500, $YPos+90,$XPos, $YPos+90);
/*Dow top left corner */
$pdf->partEllipse($XPos, $YPos+80,90,180,10,10);
/*Do a line to the bottom left corner */
$pdf->line($XPos-10, $YPos+80,$XPos-10, $YPos+60);
/*Now do the bottom left corner 180 - 270 coming back west*/
$pdf->partEllipse($XPos, $YPos+60,180,270,10,10);
/*Now a line to the bottom right */
$pdf->line($XPos, $YPos+50,$XPos+500, $YPos+50);
/*Now do the bottom right corner */
$pdf->partEllipse($XPos+500, $YPos+60,270,360,10,10);
/*Finally join up to the top right corner where started */
$pdf->line($XPos+510, $YPos+60,$XPos+510, $YPos+80);


//$pdf->addText($XPos, $YPos-100,$FontSize, _('Shipped by'). ' : ' . $myrow['shippername']);

if (mb_strlen($LeftOvers)>1){
	$LeftOvers = $pdf->addTextWrap($XPos,$YPos-145,170,$FontSize,$LeftOvers);
	if (mb_strlen($LeftOvers)>1){
		$LeftOvers = $pdf->addTextWrap($XPos,$YPos-160,170,$FontSize,$LeftOvers);
		if (mb_strlen($LeftOvers)>1){
			$LeftOvers = $pdf->addTextWrap($XPos,$YPos-175,170,$FontSize,$LeftOvers);
			if (mb_strlen($LeftOvers)>1){
				$LeftOvers = $pdf->addTextWrap($XPos,$YPos-180,170,$FontSize,$LeftOvers);
			}
		}
	}
}
$Dynamic -= 60;
$YPos -= 101;



$YPos += 120;

$header_line_height = $line_height+25;

$LeftOvers = $pdf->addTextWrap($XPos,$YPos+10,87,$FontSize, _('Item Code'),'left');
$LeftOvers = $pdf->addTextWrap($XPos+100,$YPos+10,185,$FontSize, _('Item Description'),'left');
$LeftOvers = $pdf->addTextWrap($XPos+380,$YPos+10,30,$FontSize, _('UOM'),'center');
$LeftOvers = $pdf->addTextWrap($XPos+460,$YPos+10,40,$FontSize, _('Qty'),'right');

$YPos -= $line_height;

$FontSize =8;

?>
