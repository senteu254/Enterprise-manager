<?php
/*	$Id: PDFQuotationPortrait.php 4491 2011-02-15 06:31:08Z daintree $ */

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/

include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$line_height=16;
//Get Out if we have no order number to work with
If (!isset($_GET['PINo']) || $_GET['PINo']==""){
        $Title = _('Select Proforma Invoice To Print');
        include('includes/header.inc');
        echo '<div class="centre">
				<br />
				<br />
				<br />';
        prnMsg( _('Select a Proforma Invoce to Print before calling this page') , 'error');
        echo '<br />
				<br />
				<br />
				<table class="table_index">
				<tr>
					<td class="menu_group_item">
						<ul><li><a href="'. $RootPath . '/SelectSalesOrder.php?Proforma=Proformainvoice_Only">' . _('ProformaInvoice') . '</a></li>
						</ul>
					</td>
				</tr>
				</table>
				</div>
				<br />
				<br />
				<br />';
        include('includes/footer.inc');
        exit();
}

/*retrieve the order details from the database to print */
$ErrMsg = _('There was a problem retrieving the quotation header details for Order Number') . ' ' . $_GET['PINo'] . ' ' . _('from the database');

$sql = "SELECT salesorders.customerref,
				salesorders.comments,
				salesorders.orddate,
				salesorders.deliverto,
				salesorders.deladd1,
				salesorders.deladd2,
				salesorders.deladd3,
				salesorders.deladd4,
				salesorders.deladd5,
				salesorders.deladd6,
				salesorders.approver,
				debtorsmaster.name,
				debtorsmaster.currcode,
				debtorsmaster.address1,
				debtorsmaster.address2,
				debtorsmaster.address3,
				debtorsmaster.address4,
				debtorsmaster.address5,
				debtorsmaster.address6,
				shippers.shippername,
				salesorders.printedpackingslip,
				salesorders.datepackingslipprinted,
				salesorders.quotedate,
				salesorders.branchcode,
				locations.taxprovinceid,
				locations.locationname,
				currencies.decimalplaces AS currdecimalplaces
			FROM salesorders INNER JOIN debtorsmaster
			ON salesorders.debtorno=debtorsmaster.debtorno
			INNER JOIN shippers
			ON salesorders.shipvia=shippers.shipper_id
			INNER JOIN locations
			ON salesorders.fromstkloc=locations.loccode
			INNER JOIN currencies
			ON debtorsmaster.currcode=currencies.currabrev
			WHERE salesorders.quotation=2
			AND salesorders.orderno='" . $_GET['PINo'] ."'";

$result=DB_query($sql, $ErrMsg);

//If there are no rows, there's a problem.
if (DB_num_rows($result)==0){
	$Title = _('Print Quotation Error');
	include('includes/header.inc');
	 echo '<div class="centre">
			<br />
			<br />
			<br />';
	prnMsg( _('Unable to Locate Proforma Invoice Number') . ' : ' . $_GET['PINo'] . ' ', 'error');
	echo '<br />
			<br />
			<br />
			<table class="table_index">
			<tr>
				<td class="menu_group_item">
					<ul><li><a href="'. $RootPath . '/SelectSalesOrder.php?Proforma=Proformainvoice_Only">' . _('Outstanding Proforma Invoice') . '</a></li></ul>
				</td>
			</tr>
			</table>
			</div>
			<br />
			<br />
			<br />';
	include('includes/footer.inc');
	exit;
} elseif (DB_num_rows($result)==1){ /*There is only one order header returned - thats good! */
	$myrow = DB_fetch_array($result);
}

/*retrieve the order details from the database to print */

/* Then there's an order to print and its not been printed already (or its been flagged for reprinting/ge_Width=807;
)
LETS GO */
$PaperSize = 'A4';
include('includes/PDFStarter.php');
/*$PageNumber = 1;// RChacon: PDFStarter.php sets $PageNumber = 0.*/
$pdf->addInfo('Title', _('Customer Proforma invoice') );
$pdf->addInfo('Subject', _('Quotations') . ' ' . $_GET['PINo']);
$FontSize = 10;
$line_height = 12;// Recommended: $line_height = $x * $FontSize.

/* Now ... Has the order got any line items still outstanding to be invoiced */

$ErrMsg = _('There was a problem retrieving the Proforma invoice line details for Proforma invoice Number') . ' ' .
	$_GET['PINo'] . ' ' . _('from the database');

$sql = "SELECT salesorderdetails.stkcode,
		stockmaster.description,
		salesorderdetails.quantity,
		salesorderdetails.qtyinvoiced,
		salesorderdetails.unitprice,
		salesorderdetails.discountpercent,
		stockmaster.taxcatid,
		salesorderdetails.narrative,
		stockmaster.decimalplaces
	FROM salesorderdetails INNER JOIN stockmaster
		ON salesorderdetails.stkcode=stockmaster.stockid
	WHERE salesorderdetails.orderno='" . $_GET['PINo'] . "'";

$result=DB_query($sql, $ErrMsg);

$ListCount = 0;

if (DB_num_rows($result)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('includes/PDFProformaInvoicePortraitPageHeader.inc');

	$QuotationTotal = 0;
	$QuotationTotalEx = 0;
	$TaxTotal = 0;

	while ($myrow2=DB_fetch_array($result)){

        $ListCount ++;

		$YPos -= $line_height;// Increment a line down for the next line item.

		if ((mb_strlen($myrow2['narrative']) >200 AND $YPos-$line_height <= 75)
			OR (mb_strlen($myrow2['narrative']) >1 AND $YPos-$line_height <= 62)
			OR $YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('includes/PDFProformaInvoicePortraitPageHeader.inc');
		} //end if need a new page headed up

		$DisplayQty = locale_number_format($myrow2['quantity'],$myrow2['decimalplaces']);
		$DisplayPrevDel = locale_number_format($myrow2['qtyinvoiced'],$myrow2['decimalplaces']);
		$DisplayPrice = locale_number_format($myrow2['unitprice'],$myrow['currdecimalplaces']);
		$DisplayDiscount = locale_number_format($myrow2['discountpercent']*100,2) . '%';
		$SubTot =  $myrow2['unitprice']*$myrow2['quantity']*(1-$myrow2['discountpercent']);
		$TaxProv = $myrow['taxprovinceid'];
		$TaxCat = $myrow2['taxcatid'];
		$Branch = $myrow['branchcode'];
		$sql3 = " SELECT taxgrouptaxes.taxauthid
				FROM taxgrouptaxes INNER JOIN custbranch
				ON taxgrouptaxes.taxgroupid=custbranch.taxgroupid
				WHERE custbranch.branchcode='" .$Branch ."'";
		$result3=DB_query($sql3, $ErrMsg);
		while ($myrow3=DB_fetch_array($result3)){
			$TaxAuth = $myrow3['taxauthid'];
		}

		$sql4 = "SELECT * FROM taxauthrates
				WHERE dispatchtaxprovince='" .$TaxProv ."'
				AND taxcatid='" .$TaxCat ."'
				AND taxauthority='" .$TaxAuth ."'";
		$result4=DB_query($sql4, $ErrMsg);
		while ($myrow4=DB_fetch_array($result4)){
			$TaxClass = 100 * $myrow4['taxrate'];
		}

		$DisplayTaxClass = $TaxClass . '%';
		$TaxAmount =  (($SubTot/100)*(100+$TaxClass))-$SubTot;
		$DisplayTaxAmount = locale_number_format($TaxAmount,$myrow['currdecimalplaces']);

		$LineTotal = $SubTot + $TaxAmount;
		$DisplayTotal = locale_number_format($LineTotal,$myrow['currdecimalplaces']);

		$FontSize = 8;// Font size for the line item.

		$LeftOvers = $pdf->addText($Left_Margin, $YPos+$FontSize, $FontSize, $myrow2['stkcode']);
		$LeftOvers = $pdf->addText(120, $YPos+$FontSize, $FontSize, $myrow2['description']);
		//LeftOvers = $pdf->addTextWrap(180, $YPos,85,$FontSize,$DisplayQty,'right');
		$LeftOvers = $pdf->addTextWrap(230, $YPos,85,$FontSize,$DisplayQty,'right');
		
			$LeftOvers = $pdf->addTextWrap(280, $YPos,85,$FontSize,$DisplayPrice,'right');
		
		$LeftOvers = $pdf->addTextWrap(330, $YPos,85,$FontSize,$DisplayTaxClass,'right');
		$LeftOvers = $pdf->addTextWrap(410, $YPos,85,$FontSize,$DisplayTaxAmount,'center');// RChacon: To review align to right.**********
		$LeftOvers = $pdf->addTextWrap(480, $YPos,85, $FontSize, $DisplayTotal,'right');

		// Prints salesorderdetails.narrative:
		$FontSize2 = $FontSize*0.8;// Font size to print salesorderdetails.narrative.
		$Width2 = $Page_Width-$Right_Margin-120;// Width to print salesorderdetails.narrative.
		$LeftOvers = trim($myrow2['narrative']);
		while(mb_strlen($LeftOvers) > 1) {
			$YPos -= $FontSize2;
			if ($YPos < ($Bottom_Margin)) {// Begins new page.
				include('includes/PDFQuotationPortraitPageHeader.inc');
			}
			$LeftOvers = $pdf->addTextWrap(120, $YPos, $Width2, $FontSize2, $LeftOvers);
		}

		$QuotationTotal += $LineTotal;
		$QuotationTotalEx += $SubTot;
		$TaxTotal += $TaxAmount;

	}// Ends while there are line items to print out.
	//$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-90, $YPos, 100, $FontSize, $DisplayTotal,'right');
///////////////////////////
	$YPos -= $line_height;

			/* check to see enough space left to print the 4 lines for the totals/footer */
			if (($YPos-$Bottom_Margin)<(2*$line_height)) {
				PrintLinesToBottom ();
			}
			/* Print a column vertical line  with enough space for the footer */
			/* draw the vertical column lines to 4 lines shy of the bottom to leave space for invoice footer info ie totals etc */
			

			/* Print a column vertical line */
			$pdf->line($Left_Margin+70, $TopOfColHeadings+78,$Left_Margin+70,666,40,$Bottom_Margin);
            /* Print a column vertical line */
			//$pdf->line($Left_Margin+180, $TopOfColHeadings+78,$Left_Margin+180,635,40,$Bottom_Margin);
            /* Print a column vertical line */
			$pdf->line($Left_Margin+250, $TopOfColHeadings+78,$Left_Margin+250,666,40,$Bottom_Margin);
            /* Print a column vertical line */
			$pdf->line($Left_Margin+288, $TopOfColHeadings+78,$Left_Margin+288,666,40,$Bottom_Margin);
            /* Print a column vertical line */
			$pdf->line($Left_Margin+330, $TopOfColHeadings+78,$Left_Margin+330,666,40,$Bottom_Margin);
            /* Print a column vertical line */
			$pdf->line($Left_Margin+380, $TopOfColHeadings+78,$Left_Margin+380,666,40,$Bottom_Margin);
            /* Print a column vertical line */
			$pdf->line($Left_Margin+450, $TopOfColHeadings+30,$Left_Margin+450,666,40,$Bottom_Margin);
            /* Print a column vertical line */
			
			$pdf->line($Left_Margin, $Bottom_Margin+(4*$line_height),$Page_Width-$Right_Margin,$Bottom_Margin+(4*$line_height));

			/* Now print out the footer and totals */


			/* Print out the invoice text entered */
			$YPos = $Bottom_Margin+(3*$line_height);

		//      $pdf->addText($Page_Width-$Right_Margin-392, $YPos - ($line_height*3)+22,$FontSize, _('Bank Code:***** Bank Account:*****'));
		//	$FontSize=10;

			$FontSize =8;
///////////////////////////
	if ((mb_strlen($myrow['comments']) >200 AND $YPos-$line_height <= 75)
		OR (mb_strlen($myrow['comments']) >1 AND $YPos-$line_height <= 62)
		OR $YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('includes/PDFProformaInvoicePortraitPageHeader.inc');
	} //end if need a new page headed up
	$pdf->line($Page_Width-$Right_Margin-145, $YPos-(2*$line_height)+16,$Page_Width-$Right_Margin,$YPos-(2*$line_height)+16);
	$pdf->line($Page_Width-$Right_Margin-145, $YPos-(2*$line_height)+5,$Page_Width-$Right_Margin,$YPos-(2*$line_height)+5);
			/*vertical to separate totals from comments and ROMALPA */
			$pdf->line($Page_Width-$Right_Margin-145, $YPos+$line_height,$Page_Width-$Right_Margin-145,$Bottom_Margin);
			//$pdf->line($Page_Width-$Right_Margin-122, $YPos+$line_height,$Page_Width-$Right_Margin-122,$Bottom_Margin+27);

			$YPos+=10;
				//$pdf->addText($Page_Width-$Right_Margin-255, $YPos - ($line_height*2)+30,$FontSize, 'TOTAL : ','right'); //total field
				
				$FontSize=9;
				$YPos-=4;
	$FontSize = 8;
	$YPos -= $line_height;
	$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-90-655, $YPos, 655, $FontSize, _('Sub Total'),'right');
	$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-90, $YPos, 90, $FontSize, locale_number_format($QuotationTotalEx,$myrow['currdecimalplaces']), 'right');
	$YPos -= $FontSize;
	$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-90-655, $YPos-=2, 655, $FontSize, _('VAT'), 'right');
	$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-90, $YPos-=2, 90, $FontSize, locale_number_format($TaxTotal,$myrow['currdecimalplaces']), 'right');
	$YPos -= $FontSize;
	$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-90-655, $YPos-=2, 655, $FontSize, _('Total'),'right');
	$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-90, $YPos-=2, 90, $FontSize, locale_number_format($QuotationTotal,$myrow['currdecimalplaces']), 'right');
	
	$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-460-655, $YPos, 655, $FontSize, _('Approved By:'),'right');
	$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-460, $YPos, 90, $FontSize, $myrow['approver']);

	// Print salesorders.comments:
	//$YPos -= $FontSize*2;
	//$pdf->addText($XPos, $YPos+$FontSize, $FontSize, _('Notes').':');
	//$Width2 = $Page_Width-$Right_Margin-120;// Width to print salesorders.comments.
	//$LeftOvers = trim($myrow['comments']);
	//while(mb_strlen($LeftOvers) > 1) {
	//	$YPos -= $FontSize;
	//	if ($YPos < ($Bottom_Margin)) {// Begins new page.
		//	include ('includes/PDFProformaInvoicePortraitPageHeader.inc');
	//	}
	//	$LeftOvers = $pdf->addTextWrap(40, $YPos, $Width2, $FontSize, $LeftOvers);
	//}

} /*end if there are line details to show on the quotation*/


if ($ListCount == 0){
        $Title = _('Print  Profroma Invoice  Error');
        include('includes/header.inc');
        echo '<p>' .  _('There were no items on the  Profroma Invoice ') . '. ' . _('The Profroma Invoice cannot be printed').
                '<br /><a href="' . $RootPath . '/SelectSalesOrder.php?Quotations=Proformainvoice_Only">' .  _('Print Another Quotation').
                '</a>' . '<br />' .  '<a href="' . $RootPath . '/index.php">' . _('Back to the menu') . '</a>';
        include('includes/footer.inc');
	exit;
} else {
    $pdf->OutputI($_SESSION['DatabaseName'] . ' Profroma Invoice ' . $_GET['PINo'] . '_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
}

?>
