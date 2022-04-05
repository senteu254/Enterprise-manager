<?php

/* $Id: PDFGrn.php 6941 2014-10-26 23:18:08Z daintree $*/

include('includes/session.inc');

if (isset($_GET['GRNNo'])) {
	$GRNNo=$_GET['GRNNo'];
} else {
	$GRNNo='';
}

$FormDesign = simplexml_load_file($PathPrefix.'companies/'.$_SESSION['DatabaseName'].'/FormDesigns/GoodsReceived.xml');

// Set the paper size/orintation
$PaperSize = $FormDesign->PaperSize;
$line_height=$FormDesign->LineHeight;
include('includes/PDFStarter.php');
$PageNumber=1;
$pdf->addInfo('Title', _('Goods Received Note') );

if ($GRNNo == 'Preview'){
	$myrow['itemcode'] = str_pad('', 15,'x');
	$myrow['deliverydate'] = '0000-00-00';
	$myrow['itemdescription'] =  str_pad('', 30,'x');
	$myrow['qtyrecd'] = 99999999.99;
	$myrow['decimalplaces'] =2;
	$myrow['conversionfactor']=1;
	$myrow['supplierid'] = str_pad('', 10,'x');
	$myrow['suppliersunit'] = str_pad('', 10,'x');
	$myrow['units'] = str_pad('', 10,'x');

	$SuppRow['suppname'] = str_pad('', 30,'x');
	$SuppRow['address1'] = str_pad('', 30,'x');
	$SuppRow['address2'] = str_pad('', 30,'x');
	$SuppRow['address3'] = str_pad('', 30,'x');
	$SuppRow['address4'] = str_pad('', 20,'x');
	$SuppRow['address5'] = str_pad('', 10,'x');
	$SuppRow['address6'] = str_pad('', 10,'x');
	$NoOfGRNs =1;
} else { //NOT PREVIEW

	$sql="SELECT lsogrns.itemcode,
				lsogrns.grnno,
				lsogrns.deliverydate,
				lsogrns.itemdescription,
				lsogrns.qtyrecd,
				lsogrns.supplierid,
				lsorderdetails.suppliersunit,
				lsorderdetails.conversionfactor,
				stockmaster.units,
				stockmaster.decimalplaces
			FROM lsogrns INNER JOIN lsorderdetails
			ON lsogrns.podetailitem=lsorderdetails.podetailitem
			INNER JOIN lsorders on lsorders.orderno = lsorderdetails.orderno
			INNER JOIN locationusers ON locationusers.loccode=lsorders.intostocklocation AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
			LEFT JOIN stockmaster
			ON lsogrns.itemcode=stockmaster.stockid
			WHERE grnbatch='". $GRNNo ."'";

	$GRNResult=DB_query($sql);
	$NoOfGRNs = DB_num_rows($GRNResult);
	if($NoOfGRNs>0) { //there are GRNs to print

		$sql = "SELECT suppliers.suppname,
						suppliers.address1,
						suppliers.address2 ,
						suppliers.address3,
						suppliers.address4,
						suppliers.address5,
						suppliers.address6
				FROM lsogrns INNER JOIN suppliers
				ON lsogrns.supplierid=suppliers.supplierid
				WHERE grnbatch='". $GRNNo ."'";
		$SuppResult = DB_query($sql,_('Could not get the supplier of the selected GRN'));
		$SuppRow = DB_fetch_array($SuppResult);
	}
} // get data to print
if ($NoOfGRNs >0){
	include ('includes/LSO_PDFGrnHeader.inc'); //head up the page

	$FooterPrintedInPage= 0;
	$YPos=$FormDesign->Data->y;
	for ($i=1;$i<=$NoOfGRNs;$i++) {
		if ($GRNNo!='Preview'){
			$myrow = DB_fetch_array($GRNResult);
		}
		if (is_numeric($myrow['decimalplaces'])){
			$DecimalPlaces=$myrow['decimalplaces'];
		} else {
			$DecimalPlaces=2;
		}
		if (is_numeric($myrow['conversionfactor']) AND $myrow['conversionfactor'] !=0){
			$SuppliersQuantity=locale_number_format($myrow['qtyrecd']/$myrow['conversionfactor'],$DecimalPlaces);
		} else {
			$SuppliersQuantity=locale_number_format($myrow['qtyrecd'],$DecimalPlaces);
		}
		$OurUnitsQuantity=locale_number_format($myrow['qtyrecd'],$DecimalPlaces);
		$DeliveryDate = ConvertSQLDate($myrow['deliverydate']);

		$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column1->x,$Page_Height-$YPos,$FormDesign->Data->Column1->Length,$FormDesign->Data->Column1->FontSize, $myrow['itemcode']);
		$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column2->x,$Page_Height-$YPos,$FormDesign->Data->Column2->Length,$FormDesign->Data->Column2->FontSize, $myrow['itemdescription']);
		/*$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column3->x,$Page_Height-$YPos,$FormDesign->Data->Column3->Length,$FormDesign->Data->Column3->FontSize, $DeliveryDate);*/
		$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column3->x,$Page_Height-$YPos,$FormDesign->Data->Column3->Length,$FormDesign->Data->Column3->FontSize, $DeliveryDate, 'right');
		$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column4->x,$Page_Height-$YPos,$FormDesign->Data->Column4->Length,$FormDesign->Data->Column4->FontSize, $SuppliersQuantity, 'right');
		$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column5->x,$Page_Height-$YPos,$FormDesign->Data->Column5->Length,$FormDesign->Data->Column5->FontSize, $myrow['suppliersunit'], 'left');
		$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column6->x,$Page_Height-$YPos,$FormDesign->Data->Column6->Length,$FormDesign->Data->Column6->FontSize, $OurUnitsQuantity, 'right');
		$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column7->x,$Page_Height-$YPos,$FormDesign->Data->Column7->Length,$FormDesign->Data->Column7->FontSize, $myrow['units'], 'left');
		$YPos += $line_height;

		/* move to after serial print
		if($FooterPrintedInPage == 0){
			$LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x,$Page_Height-$FormDesign->ReceiptDate->y,$FormDesign->ReceiptDate->FontSize, _('Date of Receipt: ') . $DeliveryDate);
			$LeftOvers = $pdf->addText($FormDesign->SignedFor->x,$Page_Height-$FormDesign->SignedFor->y,$FormDesign->SignedFor->FontSize, _('Signed for ').'______________________');
			$FooterPrintedInPage= 1;
		}
		*/

		if ($YPos >= $FormDesign->LineAboveFooter->starty){
			/* We reached the end of the page so finsih off the page and start a newy */
			//$PageNumber++;	// $PageNumber++ available in PDFGrnHeader.inc
			$FooterPrintedInPage= 0;	//Set FooterPrintedInPage value zero print footer in new page
			$YPos=$FormDesign->Data->y;
			include ('includes/LSO_PDFGrnHeader.inc');
		} //end if need a new page headed up

		$SQL = "SELECT stockmaster.controlled
			    FROM stockmaster WHERE stockid ='" . $myrow['itemcode'] . "'";
		$CheckControlledResult = DB_query($SQL,'<br />' . _('Could not determine if the item was controlled or not because') . ' ');
		$ControlledRow = DB_fetch_row($CheckControlledResult);

		if ($ControlledRow[0]==1) { /*Then its a controlled item */
			$SQL = "SELECT stockserialmoves.serialno,
					stockserialmoves.moveqty
					FROM stockmoves INNER JOIN stockserialmoves
					ON stockmoves.stkmoveno= stockserialmoves.stockmoveno
					WHERE stockmoves.stockid='" . $myrow['itemcode'] . "'
					AND stockmoves.type =25
					AND stockmoves.transno='" . $GRNNo . "'";
			$GetStockMoveResult = DB_query($SQL,_('Could not retrieve the stock movement reference number which is required in order to retrieve details of the serial items that came in with this GRN'));
			while ($SerialStockMoves = DB_fetch_array($GetStockMoveResult)){
				$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column1->x-20,$Page_Height-$YPos,$FormDesign->Data->Column1->Length,$FormDesign->Data->Column1->FontSize, _('Lot/Serial:'),'right');
				$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column2->x,$Page_Height-$YPos,$FormDesign->Data->Column2->Length,$FormDesign->Data->Column2->FontSize, $SerialStockMoves['serialno']);
				$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column2->x,$Page_Height-$YPos,$FormDesign->Data->Column2->Length,$FormDesign->Data->Column2->FontSize, $SerialStockMoves['moveqty'],'right');
				$YPos += $line_height;

				if ($YPos >= $FormDesign->LineAboveFooter->starty){
					$FooterPrintedInPage= 0;
					$YPos=$FormDesign->Data->y;
					include ('includes/PDFGrnHeader.inc');
				} //end if need a new page headed up
			} //while SerialStockMoves
			$LeftOvers = $pdf->addTextWrap($FormDesign->Data->Column2->x,$Page_Height-$YPos,$FormDesign->Data->Column2->Length,$FormDesign->Data->Column2->FontSize, ' ');
			$YPos += $line_height;
			if ($YPos >= $FormDesign->LineAboveFooter->starty){
				$FooterPrintedInPage= 0;
				$YPos=$FormDesign->Data->y;
				include ('includes/PDFGrnHeader.inc');
			} //end if need a new page headed up
		} //controlled item*/
		    $original ='Commitee';
			$pdf->addText($XPos,60,$Bottom_Margin,$FontSize,' ' . _($original));
		if($FooterPrintedInPage == 0){
		$pdf->SetFont('','U');
		$pdf->SetFont('','B',12);
		  $LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x-620,$Page_Height-$FormDesign->ReceiptDate->y+80,$FormDesign->ReceiptDate->FontSize, _('Inspection and Acceptance Commitee '));
		  $pdf->SetFont('','',12);
		  $pdf->SetFont('','');
		   $LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x-620,$Page_Height-$FormDesign->ReceiptDate->y+60,$FormDesign->ReceiptDate->FontSize, _(                                     'Chairman..............................................'));     
		   $LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x-620,$Page_Height-$FormDesign->ReceiptDate->y+40,$FormDesign->ReceiptDate->FontSize, _('Member................................................'));
		   $LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x-620,$Page_Height-$FormDesign->ReceiptDate->y+20,$FormDesign->ReceiptDate->FontSize, _('Member................................................'));
		   $LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x-620,$Page_Height-$FormDesign->ReceiptDate->y,$FormDesign->ReceiptDate->FontSize, _('Mmeber................................................'));
		   $pdf->SetFont('','U');
		   $pdf->SetFont('','B',12);
		   $LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x-330,$Page_Height-$FormDesign->ReceiptDate->y+80,$FormDesign->ReceiptDate->FontSize, _('Q.C (Remarks) '));
		   $pdf->SetFont('','',12);
		   $pdf->SetFont('','');
		    $LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x-330,$Page_Height-$FormDesign->ReceiptDate->y+60,$FormDesign->ReceiptDate->FontSize, _('P/No....................................................'));
			  $LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x-330,$Page_Height-$FormDesign->ReceiptDate->y+40,$FormDesign->ReceiptDate->FontSize, _('Name...................................................'));
			    $LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x-330,$Page_Height-$FormDesign->ReceiptDate->y+20,$FormDesign->ReceiptDate->FontSize, _('Sign........................Date......................'));
				$pdf->SetFont('','U');
				$pdf->SetFont('','B',12);
			 $LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x-110,$Page_Height-$FormDesign->ReceiptDate->y+80,$FormDesign->ReceiptDate->FontSize, _('Received  Items  Indented'));
			 $pdf->SetFont('','',12);
			 $pdf->SetFont('','');
			  $LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x-110,$Page_Height-$FormDesign->ReceiptDate->y+60,$FormDesign->ReceiptDate->FontSize, _('P/No....................................................'));
			  $LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x-110,$Page_Height-$FormDesign->ReceiptDate->y+40,$FormDesign->ReceiptDate->FontSize, _('Name...................................................'));
			    $LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x-110,$Page_Height-$FormDesign->ReceiptDate->y+20,$FormDesign->ReceiptDate->FontSize, _('Sign........................Date......................'));
			//$LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x,$Page_Height-$FormDesign->ReceiptDate->y,$FormDesign->ReceiptDate->FontSize, _('Date of Receipt: ') . $DeliveryDate);
			//$LeftOvers = $pdf->addText($FormDesign->SignedFor->x,$Page_Height-$FormDesign->SignedFor->y,$FormDesign->SignedFor->FontSize, _('Signed for ').'______________________');
			$pdf->SetFontsize('','24');
			$pdf->SetFont('','B');
			$LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x-360,$Page_Height-$FormDesign->ReceiptDate->y+450,$FormDesign->ReceiptDate->FontSize, _('GOODS RECEIPT AND ISSUE VOUCHER'));
			 //$LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x+15,$Page_Height-$FormDesign->ReceiptDate->y-80,$FormDesign->ReceiptDate->FontSize, _('GOODS RECEIPT AND ISSUE VOUCHER'));
			 $pdf->SetFont('','');
			$pdf->SetFontsize('','');
			 $LeftOvers = $pdf->addText($FormDesign->ReceiptDate->x+15,$Page_Height-$FormDesign->ReceiptDate->y-20,$FormDesign->ReceiptDate->FontSize, _('KOFC 52010202'));
			 $pdf->SetFont('','',18);
			$FooterPrintedInPage= 1;
			$line_heigh=75;
			
		}
	} //end of loop around GRNs to print

    $pdf->OutputD($_SESSION['DatabaseName'] . '_GRN_' . $GRNNo . '_' . date('Y-m-d').'.pdf');
    $pdf->__destruct();
} else { //there were not GRNs to print
	$Title = _('GRN Error');
	include('includes/header.inc');
	prnMsg(_('There were no GRNs to print'),'warn');
	echo '<br /><a href="'.$RootPath.'/index.php">' .  _('Back to the menu') . '</a>';
	include('includes/footer.inc');
}
?>
