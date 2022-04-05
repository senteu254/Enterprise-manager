<?php

/* $Id: PrintCustOrder_generic.php 7093 2015-01-22 20:15:40Z vvs2012 $*/


include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

function PrintLinesToBottom () {

	global $pdf;
	global $PageNumber;
	global $TopOfColHeadings;
	global $Left_Margin;
	global $Bottom_Margin;
	global $line_height;
	
	/* draw the vertical column lines to 4 lines shy of the bottom to leave space for invoice footer info ie totals etc */
			$pdf->line(70, $TopOfColHeadings+12,70,$Bottom_Margin+(4*$line_height)-62);
			/* Print a column vertical line */
			$pdf->line(260, $TopOfColHeadings+12,260,$Bottom_Margin+(4*$line_height)-62);
			/* Print a column vertical line */
			$pdf->line(312, $TopOfColHeadings+12,312,$Bottom_Margin+(4*$line_height)-62);
			
			/* draw the vertical column lines to 4 lines shy of the bottom to leave space for invoice footer info ie totals etc */
			$pdf->line(445+60, $TopOfColHeadings+12,445+60,$Bottom_Margin+(4*$line_height)-62);
			/* Print a column vertical line */
			$pdf->line(445+250, $TopOfColHeadings+12,445+250,$Bottom_Margin+(4*$line_height-62));
			/* Print a column vertical line */
			$pdf->line(445+302, $TopOfColHeadings+12,445+302,$Bottom_Margin+(4*$line_height)-62);

	$PageNumber++;

}

//Get Out if we have no order number to work with
If (!isset($_GET['TransNo']) OR $_GET['TransNo']==""){
	$Title = _('Select Sales Order To Print');
	include('includes/header.inc');
	echo '<div class="centre"><br /><br /><br />';
	prnMsg( _('Select an Order Number to Print before calling this page') , 'error');
	echo '<br />
			<br />
			<br />
			<table class="table_index">
			<tr>
			<td class="menu_group_item">
            <ul>
			    <li><a href="'. $RootPath . '/SelectSalesOrder.php?">' . _('Outstanding Sales Orders') . '</a></li>
			    <li><a href="'. $RootPath . '/SelectCompletedOrder.php">' . _('Completed Sales Orders') . '</a></li>
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
$ErrMsg = _('There was a problem retrieving the sales order header details for Order Number') . ' ' . $_GET['TransNo'] . ' ' . _('from the database');

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
			salesorders.debtorno,
			salesorders.branchcode,
			salesorders.deliverydate,
			debtorsmaster.name,
			debtorsmaster.address1,
			debtorsmaster.address2,
			debtorsmaster.address3,
			debtorsmaster.address4,
			debtorsmaster.address5,
			debtorsmaster.address6,
			shippers.shippername,
			salesorders.printedpackingslip,
			salesorders.datepackingslipprinted,
			locations.locationname
		FROM salesorders INNER JOIN debtorsmaster
			ON salesorders.debtorno=debtorsmaster.debtorno
		INNER JOIN shippers
			ON salesorders.shipvia=shippers.shipper_id
		INNER JOIN locations
			ON salesorders.fromstkloc=locations.loccode
		INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
		WHERE salesorders.orderno='" . $_GET['TransNo'] . "'";


$result=DB_query($sql, $ErrMsg);

//If there are no rows, there's a problem.
if (DB_num_rows($result)==0){
	$Title = _('Print Sales Order Error');
	include('includes/header.inc');
	echo '<div class="centre"><br /><br /><br />';
	prnMsg( _('Unable to Locate Order Number') . ' : ' . $_GET['TransNo'] . ' ', 'error');
	echo '<br />
			<br />
			<br />
			<table class="table_index">
			<tr>
			<td class="menu_group_item">
			<li><a href="'. $RootPath . '/SelectSalesOrder.php">' . _('Outstanding Sales Orders') . '</a></li>
			<li><a href="'. $RootPath . '/SelectCompletedOrder.php">' . _('Completed Sales Orders') . '</a></li>
			</td>
			</tr>
			</table>
			</div>
			<br />
			<br />
			<br />';

	include('includes/footer.inc');
	exit();
} elseif (DB_num_rows($result)==1){ /*There is only one order header returned - thats good! */

        $myrow = DB_fetch_array($result);
        /* Place the deliver blind variable into a hold variable to used when
        producing the packlist */
        $DeliverBlind = $myrow['deliverblind'];
		/*
        if ($myrow['printedpackingslip']==1 AND ($_GET['Reprint']!='OK' OR !isset($_GET['Reprint']))){
                $Title = _('Print Sales Order Error');
                include('includes/header.inc');
                echo '<p>';
                prnMsg( _('The Sales Order for order number') . ' ' . $_GET['TransNo'] . ' ' .
                        _('has previously been printed') . '. ' . _('It was printed on'). ' ' . ConvertSQLDate($myrow['printeddate']) .
                        '<br />' . _('This check is there to ensure that duplicate Sales Orders are not produced and dispatched more than once to the customer'), 'warn' );
              echo '<a href="' . $RootPath. '/PrintCustOrder_SalesOrder.php?TransNo=' . $_GET['TransNo'] . '&Reprint=OK">' .  _('Do a Re-Print') . ' (' . _('Plain paper') . ' - ' . _('A4') . ' ' . _('landscape') . ') ' . _('Even Though Previously Printed'). '</a>';

                echo '<br /><br /><br />';
                echo  _('Or select another Order Number to Print');
                echo '<table class="table_index">
						<tr>
						<td class="menu_group_item">
                        <li><a href="'. $RootPath . '/SelectSalesOrder.php">' . _('Outstanding Sales Orders') . '</a></li>
                        <li><a href="'. $RootPath . '/SelectCompletedOrder.php">' . _('Completed Sales Orders') . '</a></li>
                        </td>
                        </tr>
                        </table>
                        </div>
                        <br />
                        <br />
                        <br />';

                include('includes/footer.inc');
                exit;
        }//packing slip has been printed.
		*/
}

/*retrieve the order details from the database to print */

/* Then there's an order to print and its not been printed already (or its been flagged for reprinting)
LETS GO */

$PaperSize = 'A4';
include('includes/PDFStarter.php');
//$pdf->selectFont('./fonts/Helvetica.afm');
$pdf->addInfo('Title', _('Customer Laser Sales Order') );
$pdf->addInfo('Subject', _('Laser Sales Order for order') . ' ' . $_GET['TransNo']);

$line_height=12;
$PageNumber = 1;
$Copy = 'Office';

$ListCount = 0;
$Count = 0;
$FullPage = 0;


	/* Now ... Has the order got any line items still outstanding to be invoiced */
	$ErrMsg = _('There was a problem retrieving the details for Order Number') . ' ' . $_GET['TransNo'] . ' ' . _('from the database');

	$sql = "SELECT salesorderdetails.stkcode,
			stockmaster.description,
			salesorderdetails.quantity,
			salesorderdetails.qtyinvoiced,
			salesorderdetails.unitprice,
			stockmaster.decimalplaces
		FROM salesorderdetails INNER JOIN stockmaster
			ON salesorderdetails.stkcode=stockmaster.stockid
		 WHERE salesorderdetails.orderno='" . $_GET['TransNo'] . "'";
	$result=DB_query($sql, $ErrMsg);

	if (DB_num_rows($result)>0){
		/*Yes there are line items to start the ball rolling with a page header */
		include('includes/PDFOrderPageHeader_salesorder.inc');
		//$YPos += 30;
		$YPosSUM =0;
		$Dynamic =-40;
		$TopOfColHeadings = $YPos+27;
		
		while ($myrow2=DB_fetch_array($result)){

            $ListCount ++;
			$ListCount1 ++;

			$DisplayQty = locale_number_format($myrow2['quantity'],$myrow2['decimalplaces']);
			$unitprice = locale_number_format($myrow2['unitprice'],$myrow2['decimalplaces']);
			$Price = locale_number_format(($myrow2['quantity']*$myrow2['unitprice']),$myrow2['decimalplaces']);
			$SubTot +=($myrow2['quantity']*$myrow2['unitprice']);

			$LeftOvers = $pdf->addTextWrap($XPos,$YPos,55,$FontSize,$DisplayQty,'left');
			$LeftOvers = $pdf->addTextWrap($XPos+60,$YPos,285,$FontSize,$myrow2['description']);
			$LeftOvers = $pdf->addTextWrap($XPos+380,$YPos,50,$FontSize,$unitprice,'right');
			$LeftOvers = $pdf->addTextWrap($XPos+430,$YPos,75,$FontSize,$Price,'right');
			///////////////////////////////////////////////////////////////////////////////////
			
			if ($YPos-$line_height <= 50){
			/* We reached the end of the page so finsih off the page and start a newy */
				PrintLinesToBottom ();
				$ListCount1 = 0;
				include ('includes/PDFOrderPageHeader_salesorder.inc');
			} //end if need a new page headed up
			else {
				/*increment a line down for the next line item */
				$YPos -= ($line_height);
				$YPosSUM += ($line_height);
				$Dynamic += ($line_height);
			}
			if ($myrow2['mbflag']=='A'){
				/*Then its an assembly item - need to explode into it's components for packing list purposes */
				$sql = "SELECT bom.component,
								bom.quantity,
								stockmaster.description,
								stockmaster.decimalplaces
						FROM bom INNER JOIN stockmaster
						ON bom.component=stockmaster.stockid
						WHERE bom.parent='" . $myrow2['stkcode'] . "'
                        AND bom.effectiveafter <= '" . date('Y-m-d') . "'
                        AND bom.effectiveto > '" . date('Y-m-d') . "'";
				$ErrMsg = _('Could not retrieve the components of the ordered assembly item');
				$AssemblyResult = DB_query($sql,$ErrMsg);
				$LeftOvers = $pdf->addTextWrap($XPos,$YPos,150,$FontSize, _('Assembly Components:-'));
				$YPos -= ($line_height);
				/*Loop around all the components of the assembly and list the quantity supplied */
				while ($ComponentRow=DB_fetch_array($AssemblyResult)){
					$DisplayQtySupplied = locale_number_format($ComponentRow['quantity']*($myrow2['quantity'] - $myrow2['qtyinvoiced']),$ComponentRow['decimalplaces']);
					$LeftOvers = $pdf->addTextWrap($XPos,$YPos,127,$FontSize,$ComponentRow['component']);
					$LeftOvers = $pdf->addTextWrap(147,$YPos,255,$FontSize,$ComponentRow['description']);
					$LeftOvers = $pdf->addTextWrap(503,$YPos,85,$FontSize,$DisplayQtySupplied,'right');
					if ($YPos-$line_height <= 50){
						/* We reached the end of the page so finsih off the page and start a newy */
						PrintLinesToBottom ();
						$ListCount1 = 0;
						include ('includes/PDFOrderPageHeader_salesorder.inc');
					} //end if need a new page headed up
					 else{
						/*increment a line down for the next line item */
						$YPos -= ($line_height);
					}
				} //loop around all the components of the assembly
			}
		} //end while there are line items to print out
		$YPos += $YPosSUM;
		$YPos -= 75;
		/*draw a nice curved corner box around the billing details */
		/*from the top right */
		$pdf->partEllipse($XPos+500,$YPos+100,0,90,10,10);
		/*line to the top left */
		$pdf->line($XPos+500, $YPos+110,$XPos, $YPos+110);
		/*header line to the top left */
		$pdf->line($XPos+510, $YPos+90,$XPos-10, $YPos+90);
		/*Dow top left corner */
		$pdf->partEllipse($XPos, $YPos+100,90,180,10,10);
		
		/*Do a line to the bottom left corner */
		$pdf->line($XPos-10, $YPos+100,$XPos-10, $YPos-$Dynamic);
		/*Now do the bottom left corner 180 - 270 coming back west*/
		$pdf->partEllipse($XPos, $YPos-$Dynamic,180,270,10,10);
		/*Now a line to the bottom right */
		$pdf->line($XPos, $YPos-$Dynamic-10,$XPos+500, $YPos-$Dynamic-10);
		/*Now do the bottom right corner */
		$pdf->partEllipse($XPos+500, $YPos-$Dynamic,270,360,10,10);
		/*Finally join up to the top right corner where started */
		$pdf->line($XPos+510, $YPos-$Dynamic,$XPos+510, $YPos+100);
	} /*end if there are order details to show on the order*/

	

$DisplaySubTot = locale_number_format($SubTot,$myrow2['decimalplaces']);
			/* Print a column vertical line  with enough space for the footer */
			/* draw the vertical column lines to 4 lines shy of the bottom to leave space for invoice footer info ie totals etc */
			$pdf->line($XPos+60, $YPos+110,$XPos+60,$YPos-$Dynamic+30);
			/* Print a column vertical line */
			$pdf->line($XPos+350, $YPos+110,$XPos+350,$YPos-$Dynamic-10);
			/* Print a column vertical line */
			$pdf->line($XPos+430, $YPos+110,$XPos+430,$YPos-$Dynamic-10);
			/* Rule off at bottom of the vertical lines */
			$pdf->line($XPos-10, $YPos-$Dynamic+30,$XPos+510,$YPos-$Dynamic+30);
			/* Now print out the footer and totals */
			$pdf->addText($XPos+380, $YPos-$Dynamic+30,$FontSize, _('Sub Total'));
			$LeftOvers = $pdf->addTextWrap($XPos+430,$YPos-$Dynamic+18,75,$FontSize,$DisplaySubTot, 'right');
			$pdf->line($XPos+430, $YPos-$Dynamic+10,$XPos+510,$YPos-$Dynamic+10);
			/*vertical to separate totals from comments and ROMALPA */
			//$pdf->line($XPos+480, 126,$XPos+480,$YPos-$Dynamic-40);
			$pdf->addText($XPos+380, $YPos-$Dynamic+5,$FontSize, _('TOTAL'));
			$LeftOvers = $pdf->addTextWrap($XPos+430,$YPos-$Dynamic-6,75,$FontSize,$DisplaySubTot, 'right');
	
	$YP =$YPos-$Dynamic;
	$pdf->addText(20, $YP-20,$FontSize,  _('KOFC 55020204'));
	$pdf->addText(220, $YP-20,$FontSize,  _('ISO 9001:2008 Certified Institution'));
	$pdf->addText(500, $YP-20,$FontSize,  _('ISSUE 3 REV 0'));

if ($ListCount == 0) {
	$Title = _('Print Sales Order Error');
	include('includes/header.inc');
	echo '<p>' .  _('There were no outstanding items on the order') . '. ' . _('A Sales Order cannot be printed').
			'<br /><a href="' . $RootPath . '/SelectSalesOrder.php">' .  _('Print Another delivery Slip').
			'</a>
			<br /><a href="' . $RootPath . '/index.php">' . _('Back to the menu') . '</a>';
	include('includes/footer.inc');
	exit;
} else {
    	$pdf->OutputI($_SESSION['DatabaseName'] . '_SalesOrder_' . date('Y-m-d') . '.pdf');
    	$pdf->__destruct();
}

?>