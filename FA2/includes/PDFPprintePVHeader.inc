<?php
/* $Id: PDFOrderPageHeader_generic.inc 5816 2013-02-07 07:54:41Z daintree $*/
/* pdf-php by R&OS code to set up a new sales order page */
if ($PageNumber>1){
	$pdf->newPage();
}

$XPos = $Page_Width/2 - 60;
$XPos = 46;
/* if the deliver blind flag is set on the order, we do not want to output
the company logo 
if ($DeliverBlind < 2) {
   $pdf->addJpegFromFile($_SESSION['LogoFile'],$XPos,750,0,70);
}
*/
$FontSize=14;
$pdf->SetFont('Times','B');
        //////////////////////////////////////////////////////////////////////////////////
		//$pdf->addText($XPos+120, 760,$FontSize, _('PAYMENT VOUCHER') . ' ' );
		$pdf->SetFont('Times','');
		$pdf->addText($XPos+430, 747,$FontSize=18, $myrow2['serialNo']);
		$pdf->SetFont('Times','B');
		$pdf->SetFont('Times','');
		$FontSize=12;
		//$descriptions='Amount in words: '.($myrow2['shortdescription']);
		
				//$LeftOvers = $pdf->addTextWrap($XPos,700,500,$FontSize,$descriptions);
				//while (mb_strlen($LeftOvers)>0) {
				//$YPos -= 15;
					//$LeftOvers = $pdf->addTextWrap($XPos+90,700+$YPos,400,$FontSize,$LeftOvers);
				//}
		$FontSize=8;
		$pdf->addText($XPos+105, 775,$FontSize, $myrow2['payeename']);
		//$pdf->line($XPos+505, $YPos+765,$XPos-10, $YPos+765);
		//$pdf->addText($XPos, 710,$FontSize, _('Amount in words:'.$myrow2['shortdescription'].'') . ' ' );
		//$pdf->addText($XPos-5, 690,$FontSize, _('REF:  KOFC/0604') . ' ' );
		//////////////////////////////////////////////////////////////////////////////////
	   //  $title = 'FROM:   '.($myrow2['datereq']);
		//$pdf->addText($XPos+320, 770,$FontSize, _($title) . ' ' );
		//$date = 'DATE:   '.($myrow2['datereq']);
		//$pdf->addText($XPos+320, 690,$FontSize, _($date) . ' ' );
	    //////////////////////////////////////////////////////////////////////////////////
		//$pdf->SetFont('Times','BU');
		
				//$pdf->SetFont('Times','');

/* if the deliver blind flag is set on the order, we do not want to output
the company contact info */
$XPos = 165;
$YPos = 820;

if ($DeliverBlind < 2) {
 $FontSize=8;
//$pdf->addText($XPos+320, $YPos+20,$FontSize, _('F.O.20(Revised)') . ' ' );
    $FontSize=11;
  //$pdf->addText($XPos+70, $YPos+20,$FontSize, _('REPUBLIC OF KENYA') . ' ' );
  $pdf->SetFont('Times','B');
	$FontSize=14;
   //$pdf->addText($XPos+60, $YPos,$FontSize, _('PAYMENT VOUCHER') . ' ' );
   $FontSize=12;
   //$pdf->addText($XPos+60, $YPos-19,$FontSize,  _('(VOTED PROVISION)') . ' ' );
}
$pdf->SetFont('Times','');
$XPos = 27;
$YPos = 480;

$FontSize=11;
//$pdf->addText($XPos, $YPos,$FontSize, _('Delivered To').':' );
//$pdf->addText($XPos, $YPos-15,$FontSize, $myrow['deliverto']);
//$pdf->addText($XPos, $YPos-30,$FontSize, $myrow['deladd1'].'-' . $myrow['deladd5']);
//$pdf->addText($XPos, $YPos-45,$FontSize, $myrow['deladd3'] . ' ' . $myrow['deladd4'] . ' ' . $myrow['deladd6']);

//$YPos -= 80;
$XPos = 250;
$YPos = 480;

//$pdf->addText($XPos, $YPos,$FontSize, _('PN No'). ':');
//$pdf->SetTextColor(255,0,0);
//$pdf->addText($XPos+50, $YPos+5,17, $_GET['voucherid']);
//$pdf->SetTextColor(0);
//$pdf->addText($XPos, $YPos-15,$FontSize, _('Order Ref No'). ':'.$myrow['orderno']);
//$pdf->addText($XPos, $YPos-30,$FontSize,  _('Delivery Date'). ':'.ConvertSQLDate($myrow['deliverydate']));
//$pdf->addText($XPos, $YPos-30,$FontSize,  _('Printed') . ': '.Date($_SESSION['DefaultDateFormat']));
//$LeftOvers = $pdf->addTextWrap($XPos,$YPos-67,170,$FontSize,_('Comments').':'.stripcslashes($myrow['comments']));
//$pdf->addText(60, 90,$FontSize=14,  _('ISSUED BY GM........................................................DATE.................................'));
//$pdf->addText(60, 70,$FontSize=14,  _('KOFC 56020004'));
//$pdf->addText(340, 70,$FontSize=14,  _('ISSUE 1 REV 2'));
///////////////////////////////////////////////////////////////////////////
//$doc=$myrow2['documents'];
		
				$LeftOvers = $pdf->addTextWrap(60,70,370,$FontSize,$doc);
				while (mb_strlen($LeftOvers)>0) {
				$YPos -=12;
					$LeftOvers = $pdf->addTextWrap(60,70+$YPos,370,$FontSize,$LeftOvers);
				}
//////////////////////////////////////////////////////////////////////////
//$pdf->addText(10, 110,$FontSize,$myrow2['documents']);
//$pdf->addText(20, 30,$FontSize,  _('KOFC 55020204'));
//$pdf->addText(310, 30,$FontSize,  _('ISSUE 3 REV 0'));
//$pdf->addText(200, 20,$FontSize,  _('Page'). ':'.$PageNumber);
/*
//$pdf->addText($XPos, $YPos,$FontSize, _('Customer').':');
//$pdf->addText($XPos, $YPos,$FontSize, _('Customer No.'). ' : ' . $myrow['debtorno']);
//$pdf->addText($XPos, $YPos-15,$FontSize, $myrow['name']);
//$pdf->addText($XPos, $YPos-30,$FontSize, $myrow['address1'].'-' . $myrow['address5']);
//$pdf->addText($XPos, $YPos-45,$FontSize, $myrow['address3'] . ' ' . $myrow['address4'] . ' ' . $myrow['address6']);

*/

$XPos = 50;
$YPos -= 91;
/*draw a nice curved corner box around the billing details */
/*from the top right */
//$pdf->partEllipse($XPos+360,$YPos+100,0,90,10,10);
/*line to the top left */
//$pdf->line($XPos+360, $YPos+110,$XPos, $YPos+110);
/*Dow top left corner */
//$pdf->partEllipse($XPos, $YPos+100,90,180,10,10);
/*Do a line to the bottom left corner */
//$pdf->line($XPos-10, $YPos+100,$XPos-10, $YPos+100);
/*Now do the bottom left corner 180 - 270 coming back west*/
//$pdf->partEllipse($XPos, $YPos+100,180,270,10,10);
/*Now a line to the bottom right */
//$pdf->line($XPos, $YPos+90,$XPos+360, $YPos+90);
/*Now do the bottom right corner */
//$pdf->partEllipse($XPos+360, $YPos+100,270,360,10,10);
/*Finally join up to the top right corner where started */
//$pdf->line($XPos+370, $YPos+30,$XPos+370, $YPos+30);

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
///////////////////////////////////////////////////////////////////////////////////////
$XPos = 50;

//$pdf->line($XPos+530, $YPos+271,$XPos-5, $YPos+271);
//$pdf->line($XPos+530, $YPos+270,$XPos-5, $YPos+270);
//$pdf->line($XPos+260, $YPos+270,$XPos+260, $YPos+407);
//$pdf->line($XPos+260, $YPos+270,$XPos+260, $YPos+407);
/////////////////////////////////////////////////////////////////////////////////////////
$XPos = 60;
$YPos -= 11;

/*draw a nice curved corner box around the billing details */
/*from the top right */
//$pdf->partEllipse($XPos+470,$YPos+280,0,90,10,10);
/*line to the top left */
//$pdf->line($XPos+470, $YPos+290,$XPos, $YPos+290);
/*header line to the top left */
//$pdf->line($XPos+490, $YPos+340,$XPos-23, $YPos+340);######################################################################
/*Dow top left corner */
//$pdf->partEllipse($XPos, $YPos+280,90,180,10,10);

/*Do a line to the bottom left corner */
//$pdf->line($XPos-10, $YPos+280,$XPos-10, $YPos-270);
/*Now do the bottom left corner 180 - 270 coming back west*/
//*Now a line to the bottom right */
//$pdf->line($XPos, $YPos-280,$XPos+470, $YPos-280);
/*Now do the bottom right corner */
//$pdf->partEllipse($XPos+470, $YPos-270,270,360,10,10);
/*Finally join up to the top right corner where started */
//$pdf->line($XPos+480, $YPos-270,$XPos+480, $YPos+280);
$FullPage = 1;


$YPos += 260;
$XPos = 60;

$header_line_height = $line_height+15;
 $FontSize=10;
 /*
$LeftOvers = $pdf->addTextWrap($XPos+70,$YPos+105,87,$FontSize, _('Particulars'),'left');
$LeftOvers = $pdf->addTextWrap(XPos+280,$YPos+105,280,$FontSize, _('LPO/LSO No.'),'left');
$LeftOvers = $pdf->addTextWrap(XPos+350,$YPos+105,350,$FontSize, _('Invoice No.'),'left');
$LeftOvers = $pdf->addTextWrap(XPos+460,$YPos+115,460,$FontSize, _('Amount'),'left');
$LeftOvers = $pdf->addTextWrap(XPos+450,$YPos+85,450,$FontSize, _('Sh'),'left');
$LeftOvers = $pdf->addTextWrap(XPos+520,$YPos+85,520,$FontSize, _('cts.'),'left');
*/
$YPos -= $line_height;

$FontSize =12;
?>