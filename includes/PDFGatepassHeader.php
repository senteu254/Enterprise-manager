<?php
/*	$Id: PDFQuotationPortraitPageHeader.inc 6822 2014-08-15 20:24:57Z rchacon $*/

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/

// $PageNumber is initialised in 0 by includes/PDFStarter.php.
$PageNumber ++;// Increments $PageNumber before printing.
if ($PageNumber>1) {// Inserts a page break if it is not the first page.
	$pdf->newPage();
}
// Prints company logo:
$XPos = 40;
$YPos = 560;
$FontSize = 9;
$pdf->addJpegFromFile($_SESSION['LogoFile'],$XPos,$YPos-70,0,60);

// Prints 'Document' title:
$tt = strtoupper($myrow['doc_name']);

$pdf->addTextWrap($XPos+20, $YPos, $Page_Width, 12,$tt, 'left');
// Draws a box with round corners around 'Delivery To' info:
$YPos -= 16;
$pdf->addTextWrap($Left_Margin+80, $YPos, 200, $FontSize, $_SESSION['CompanyRecord']['coyname'], 'left');
$YPos -= 16;
$pdf->addTextWrap($Right_Margin+90, $YPos, 200, $FontSize, $_SESSION['CompanyRecord']['regoffice1'], 'left');
$YPos -= 16;
$pdf->addTextWrap($Right_Margin+90, $YPos, 200, $FontSize,  _('Ph') . ': ' . $_SESSION['CompanyRecord']['telephone'] .' ' . _('Fax'). ': ' . $_SESSION['CompanyRecord']['fax'], 'left');
$YPos -= 16;
$pdf->addTextWrap($Right_Margin+90, $YPos, 200, $FontSize, $_SESSION['CompanyRecord']['email'], 'left');
// Prints 'Delivery To' info:

// Prints 'Quotation For' info:
$YPos -= 30;
if($myrow['doc_id']==7){

$pdf->addText($XPos-10, $YPos+15,$FontSize, _('Vehicle Company : ').$myrow['vcompany']);
$pdf->addText($XPos+220, $YPos+15,$FontSize, _('From Building : ').$myrow['building']);
$pdf->addText($XPos-10, $YPos,$FontSize,  _('Vehicle Type    : ').$myrow['vtype']);
$pdf->addText($XPos+220, $YPos,$FontSize,  _('Reg No : ').$myrow['vregno']);
$pdf->addText($XPos-10, $YPos-15,$FontSize, _('Driver\'s Name  : ').$myrow['driver_name']);
$pdf->addText($XPos+220, $YPos-15,$FontSize, _('License No : ').$myrow['licenseno']);
$pdf->addText($XPos-10, $YPos-30,$FontSize, _('Sr. Pass Name  : ').$myrow['passanger_name']);
$pdf->addText($XPos+220, $YPos-30,$FontSize, _('Date/Time :').''.date("d, M Y",strtotime($myrow['Requesteddate'])).' '.date("H:i",strtotime($myrow['Requesteddate'])));
$pdf->addText($XPos-10, $YPos-45,$FontSize, _('Delivery No  : ').$myrow['deliveryno']);
$pdf->addText($XPos-10, $YPos-60,$FontSize, _('Invoice No  : ').$myrow['invoiceno']);

}else{

$pdf->addText($XPos-10, $YPos+15,$FontSize, _('Bearer Name :').''.$myrow['driver_name']);
$pdf->addText($XPos-10, $YPos,$FontSize,  _('Vehicle Type    :').''.$myrow['vtype']);
$pdf->addText($XPos-10, $YPos-15,$FontSize, _('Vehicle Reg No  :').''.$myrow['vregno']);
$pdf->addText($XPos-10, $YPos-35,$FontSize, _('From Department  :').''.$myrow['description']);
$pdf->addText($XPos-10, $YPos-50,$FontSize, _('Item Destination :').''.$myrow['destination']);

}


// Draws a box with round corners around around 'Quotation For' info:
$YPos -= 50;
$pdf->RoundRectangle(
	$XPos-10,// RoundRectangle $XPos.
	$YPos+60+10,// RoundRectangle $YPos.
	200+70+90,// RoundRectangle $Width.
	80+10+10,// RoundRectangle $Height.
	10,// RoundRectangle $RadiusX.
	10);// RoundRectangle $RadiusY.

// Prints quotation info:
$pdf->addTextWrap($Page_Width-$Right_Margin-630, $Page_Height-$Top_Margin-20-$FontSize*1, 200, $FontSize,  _('Printed On'). ': '.date("d, M Y"), 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-630, $Page_Height-$Top_Margin-20-$FontSize*2, 200, $FontSize, _('Ref No.'). ': '.$_GET['id'], 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-630, $Page_Height-$Top_Margin-20-$FontSize*3, 200, $FontSize, _('Page').': '.$PageNumber, 'right');


// Prints table header:
$YPos -= 55;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize, _('Code'));
$LeftOvers = $pdf->addTextWrap(70,$YPos,85,$FontSize, _('Qty'));
$LeftOvers = $pdf->addTextWrap(120,$YPos,300,$FontSize, _('Description of Goods on Board.(Lot No.)'),'left');

// Draws a box with round corners around line items:
$pdf->RoundRectangle(
	$Left_Margin-10,// RoundRectangle $XPos.
	$YPos+$FontSize+5,// RoundRectangle $YPos.
	$XPos+320,// RoundRectangle $Width.
	$YPos+$FontSize-$Bottom_Margin-175,// RoundRectangle $Height.
	10,// RoundRectangle $RadiusX.
	10);// RoundRectangle $RadiusY.

// Line under table headings:
$LineYPos = $YPos - $FontSize;
$pdf->line($XPos+350, $LineYPos, $Left_Margin-10, $LineYPos);

$YPos -= $FontSize;// This is to use addTextWrap's $YPos instead of normal $YPos.


$XPos = 430;
$YPos = 560;
$pdf->addJpegFromFile($_SESSION['LogoFile'],$XPos,$YPos-70,0,60);

$pdf->addTextWrap($XPos+20, $YPos, $Page_Width, 12,$tt, 'left');
// Draws a box with round corners around 'Delivery To' info:
$YPos -= 16;
$pdf->addTextWrap($XPos+80, $YPos, 200, $FontSize, $_SESSION['CompanyRecord']['coyname'], 'left');
$YPos -= 16;
$pdf->addTextWrap($XPos+80, $YPos, 200, $FontSize, $_SESSION['CompanyRecord']['regoffice1'], 'left');
$YPos -= 16;
$pdf->addTextWrap($XPos+80, $YPos, 200, $FontSize,  _('Ph') . ': ' . $_SESSION['CompanyRecord']['telephone'] .' ' . _('Fax'). ': ' . $_SESSION['CompanyRecord']['fax'], 'left');
$YPos -= 16;
$pdf->addTextWrap($XPos+80, $YPos, 200, $FontSize, $_SESSION['CompanyRecord']['email'], 'left');
// Prints 'Delivery To' info:

// Prints 'Quotation For' info:
$YPos -= 30;
if($myrow['doc_id']==7){

$pdf->addText($XPos-10, $YPos+15,$FontSize, _('Vehicle Company : ').$myrow['vcompany']);
$pdf->addText($XPos+220, $YPos+15,$FontSize, _('From Building : ').$myrow['building']);
$pdf->addText($XPos-10, $YPos,$FontSize,  _('Vehicle Type    : ').$myrow['vtype']);
$pdf->addText($XPos+220, $YPos,$FontSize,  _('Reg No : ').$myrow['vregno']);
$pdf->addText($XPos-10, $YPos-15,$FontSize, _('Driver\'s Name  : ').$myrow['driver_name']);
$pdf->addText($XPos+220, $YPos-15,$FontSize, _('License No : ').$myrow['licenseno']);
$pdf->addText($XPos-10, $YPos-30,$FontSize, _('Sr. Pass Name  : ').$myrow['passanger_name']);
$pdf->addText($XPos+220, $YPos-30,$FontSize, _('Date/Time :').''.date("d, M Y",strtotime($myrow['Requesteddate'])).' '.date("H:i",strtotime($myrow['Requesteddate'])));
$pdf->addText($XPos-10, $YPos-45,$FontSize, _('Delivery No  : ').$myrow['deliveryno']);
$pdf->addText($XPos-10, $YPos-60,$FontSize, _('Invoice No  : ').$myrow['invoiceno']);


}else{

$pdf->addText($XPos-10, $YPos+15,$FontSize, _('Bearer Name :').''.$myrow['driver_name']);
$pdf->addText($XPos-10, $YPos,$FontSize,  _('Vehicle Type    :').''.$myrow['vtype']);
$pdf->addText($XPos-10, $YPos-15,$FontSize, _('Vehicle Reg No  :').''.$myrow['vregno']);
$pdf->addText($XPos-10, $YPos-35,$FontSize, _('From Department  :').''.$myrow['description']);
$pdf->addText($XPos-10, $YPos-50,$FontSize, _('Item Destination :').''.$myrow['destination']);

}


// Draws a box with round corners around around 'Quotation For' info:
$YPos -= 50;
$pdf->RoundRectangle(
	$XPos-10,// RoundRectangle $XPos.
	$YPos+60+10,// RoundRectangle $YPos.
	200+70+90,// RoundRectangle $Width.
	80+10+10,// RoundRectangle $Height.
	10,// RoundRectangle $RadiusX.
	10);// RoundRectangle $RadiusY.

// Prints quotation info:
$pdf->addTextWrap($Page_Width-$Right_Margin-230, $Page_Height-$Top_Margin-20-$FontSize*1, 200, $FontSize,  _('Printed On'). ': '.date("d, M Y"), 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-230, $Page_Height-$Top_Margin-20-$FontSize*2, 200, $FontSize, _('Ref No.'). ': '.$_GET['id'], 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-230, $Page_Height-$Top_Margin-20-$FontSize*3, 200, $FontSize, _('Page').': '.$PageNumber, 'right');

// Prints table header:
$YPos -= 55;
$LeftOvers = $pdf->addTextWrap($XPos,$YPos,100,$FontSize, _('Code'));
$LeftOvers = $pdf->addTextWrap($XPos+30,$YPos,85,$FontSize, _('Qty'));
$LeftOvers = $pdf->addTextWrap($XPos+80,$YPos,300,$FontSize, _('Description of Goods on Board.(Lot No.)'),'left');

// Draws a box with round corners around line items:
$pdf->RoundRectangle(
	$XPos-10,// RoundRectangle $XPos.
	$YPos+$FontSize+5,// RoundRectangle $YPos.
	$XPos-70,// RoundRectangle $Width.
	$YPos+$FontSize-$Bottom_Margin-175,// RoundRectangle $Height.
	10,// RoundRectangle $RadiusX.
	10);// RoundRectangle $RadiusY.

// Line under table headings:
$LineYPos = $YPos - $FontSize;
$pdf->line($XPos+350, $LineYPos, $XPos-10, $LineYPos);

$YPos -= $FontSize;// This is to use addTextWrap's $YPos instead of normal $YPos.

//-----------------------------------------------------------------------------------------------------------------------------------
if($myrow['doc_id']==7){
$LeftOvers = $pdf->addTextWrap($Left_Margin-10,$Bottom_Margin+164,300,$FontSize,'Goods Destination: '.$myrow['destination']);
$LeftOvers = $pdf->addTextWrap($XPos-10,$Bottom_Margin+164,300,$FontSize,'Goods Destination: '.$myrow['destination']);
}
$tit = array();
$comm = array();
$sql = "SELECT requisitionid, approver, approvaldate, approver_comment,Unread,Sent FROM irq_levels a
	 			INNER JOIN irq_approvers b ON a.approver_id=b.approver_id
				INNER JOIN irq_authorize_state c ON a.level_id  = c.level and requisitionid='".$_GET['id']."'
				WHERE a.doc_id=".$myrow['doc_id']."
				ORDER BY a.level_id ASC";
			$titles="SELECT approver_name FROM irq_levels a
	 			INNER JOIN irq_approvers b ON a.approver_id=b.approver_id
				INNER JOIN irq_authorize_state c ON a.level_id  = c.level and requisitionid='".$_GET['id']."'
				WHERE a.doc_id=".$myrow['doc_id']."
				ORDER BY a.level_id ASC";
			$DbgMsg = _('The SQL that was used to retrieve the information was');
			$ErrMsg = _('Could not check whether the level exists because');
			$results=DB_query($sql,$ErrMsg,$DbgMsg);
			$titleresults=DB_query($titles,$ErrMsg,$DbgMsg);
			$num=DB_num_rows($result);
			
	while($comment=DB_fetch_array($results)){
$comm[] = $comment;
}
$tit[] ='Issuing Officer';
while($title=DB_fetch_array($titleresults)){
$tit[] = $title['approver_name'];
}
				$YPos =$Bottom_Margin+146;
				$LeftOvers = $pdf->addTextWrap($Left_Margin-10,$YPos,300,$FontSize,'APPROVERS');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+190,$YPos,300,$FontSize,'COMMENTS');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+315,$YPos,100,$FontSize,'DATE');
				$pdf->line($Left_Margin-10, $Bottom_Margin+(4*$line_height)+95,390,$Bottom_Margin+(4*$line_height)+95);
				
				$LeftOvers = $pdf->addTextWrap($XPos-10,$YPos,300,$FontSize,'APPROVERS');
				$LeftOvers = $pdf->addTextWrap($XPos+190,$YPos,300,$FontSize,'COMMENTS');
				$LeftOvers = $pdf->addTextWrap($XPos+315,$YPos,100,$FontSize,'DATE');
				$pdf->line($XPos+350, $Bottom_Margin+(4*$line_height)+95,$XPos-10,$Bottom_Margin+(4*$line_height)+95);
				
					//while ($row=DB_fetch_array($results)) {
					for($i=0; $i < count($comm) and $i < count($tit); $i++) {
					$name = explode(' ',$comm[$i]['approver']);
					if(count($name)>2){
					$user = $name[0].' '.$name[1];
					}else{
					$user = $comm[$i]['approver'];
					}
					$LeftOvers = $pdf->addTextWrap($Left_Margin-10,$YPos-12,250,$FontSize,$tit[$i].' ('.$user.')');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+190,$YPos-12,400,$FontSize,$comm[$i]['approver_comment']);
					$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos-12,100,$FontSize,ConvertSQLDateTime($comm[$i]['approvaldate']),'right');
					
					$LeftOvers = $pdf->addTextWrap($XPos-10,$YPos-12,250,$FontSize,$tit[$i].' ('.$user.')');
					$LeftOvers = $pdf->addTextWrap($XPos+190,$YPos-12,400,$FontSize,$comm[$i]['approver_comment']);
					$LeftOvers = $pdf->addTextWrap($XPos+250,$YPos-12,100,$FontSize,ConvertSQLDateTime($comm[$i]['approvaldate']),'right');
					$YPos -= 13;
					}
		$LeftOvers = $pdf->addTextWrap($Left_Margin-10,$Bottom_Margin+65,505,$FontSize,'Receiving Person: .................................................................................... Sign: ...........................');
		$LeftOvers = $pdf->addTextWrap($Left_Margin-10,$Bottom_Margin+44,505,$FontSize,'Checking Security Person: ....................................................................... Sign: ..........................');
		$LeftOvers = $pdf->addTextWrap($Left_Margin-10,$Bottom_Margin+22,505,$FontSize,'Time Out (Inner Gate): ............... Name: ................................................... Sign: ........................');
		$LeftOvers = $pdf->addTextWrap($Left_Margin-10,$Bottom_Margin,505,$FontSize,'Time Out (Main Gate): ................ Name: ................................................. Sign: .........................');
		
		$LeftOvers = $pdf->addTextWrap($XPos-10,$Bottom_Margin+65,505,$FontSize,'Receiving Person: .................................................................................... Sign: ...........................');
		$LeftOvers = $pdf->addTextWrap($XPos-10,$Bottom_Margin+44,505,$FontSize,'Checking Security Person: ....................................................................... Sign: ..........................');
		$LeftOvers = $pdf->addTextWrap($XPos-10,$Bottom_Margin+22,505,$FontSize,'Time Out (Inner Gate): ............... Name: ................................................... Sign: ........................');
		$LeftOvers = $pdf->addTextWrap($XPos-10,$Bottom_Margin,505,$FontSize,'Time Out (Main Gate): ................ Name: ................................................. Sign: .........................');

if($myrow['doc_id']==7){
$pdf->addText(20, 20,$FontSize,  _('KOFC 59010202'));
$pdf->addText($XPos-10, 20,$FontSize,  _('KOFC 59010202'));
}else{
$pdf->addText(20, 20,$FontSize,  _('KOFC 59010201'));
$pdf->addText($XPos-10, 20,$FontSize,  _('KOFC 59010201'));
}		

$pdf->addText(130, 20,$FontSize,  _('ISO 9001:2008 Certified Institution'));
$pdf->addText(320, 20,$FontSize,  _('ISSUE 3 REV 0'));


$pdf->addText($XPos+110, 20,$FontSize,  _('ISO 9001:2008 Certified Institution'));
$pdf->addText($XPos+280, 20,$FontSize,  _('ISSUE 3 REV 0'));

$YPos = 351;
?>
