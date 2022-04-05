<?php

/* $Id: PrintCustOrder_generic.php 7093 2015-01-22 20:15:40Z vvs2012 $*/


include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

//Get Out if we have no order number to work with
If (!isset($_GET['TransNo']) OR $_GET['TransNo']==""){
	$Title = _('Enter Picking Number To Print Delivery Note');
	include('includes/header.inc');
	echo '<div class="centre"><br /><br /><br />';
	echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/sales.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p><br />';
		echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="get" name="form">
		<div>
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
		<table class="selection">
		<tr>
			<td>' . _('Picking Note Number').' : ' . '</td>
			<td>
			<input name="TransNo" type="text" />
			</td>
		</tr>
		</table>';
	echo '<br />
		<div class="centre">
			<input type="submit" name="Process" value="' . _('Print Delivery Note') . '" />
		</div>
        </div>
		</form>';
		echo '<br />';
		$sqls = "SELECT deliverynotenumber,salesorderno,deliverydate
		FROM deliverynotes
		GROUP BY deliverynotenumber
		ORDER BY deliverynotenumber desc LIMIT 20";
$res=DB_query($sqls, $ErrMsg);
 
 echo '<table>
 <tr>
 <th>SN#</th><th>Delivery Number</th><th>Sales Order Number</th><th>Delivery Date</th><th>Action</th>
 </tr>';
 $sn =1;
 while($myr = DB_fetch_row($res)){
 echo '<tr>
 <td>'.$sn.'</td><td>'.$myr[0].'</td><td>'.$myr[1].'</td><td>'.ConvertSQLDate($myr[2]).'</td><td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?TransNo='.$myr[0].'" title="Print Delivery Note">Print</a></td>
 </tr>';
  $sn++;
 }
 echo '</table>';
		echo '<br />';
	include('includes/footer.inc');
	exit();
}

/*retrieve the order details from the database to print */
$ErrMsg = _('There was a problem retrieving the delivery header details for Delivery Number') . ' ' . $_GET['TransNo'] . ' ' . _('from the database');
$sql = "SELECT salesorderno
		FROM deliverynotes
		WHERE deliverynotenumber='" . $_GET['TransNo'] . "'";
$result=DB_query($sql, $ErrMsg);
 $myrowq = DB_fetch_array($result);

$sql = "SELECT salesorders.debtorno,
			salesorders.orderno,
    		salesorders.customerref,
			salesorders.comments,
			deliverynotes.deliverydate,
			salesorders.deliverto,
			salesorders.deladd1,
			salesorders.deladd2,
			salesorders.deladd3,
			salesorders.deladd4,
			salesorders.deladd5,
			salesorders.deladd6,
			salesorders.deliverblind,
			debtorsmaster.name,
			debtorsmaster.address1,
			debtorsmaster.address2,
			debtorsmaster.address3,
			debtorsmaster.address4,
			debtorsmaster.address5,
			debtorsmaster.address6,
			shippers.shippername,
			deliverynotes.printed,
			deliverynotes.printeddate,
			locations.locationname,
			salesorders.fromstkloc
		FROM deliverynotes 
		INNER JOIN salesorders
		ON salesorders.orderno=deliverynotes.salesorderno
		INNER JOIN debtorsmaster
		ON salesorders.debtorno=debtorsmaster.debtorno
		INNER JOIN shippers
		ON salesorders.shipvia=shippers.shipper_id
		INNER JOIN locations
		ON salesorders.fromstkloc=locations.loccode
		INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
		WHERE deliverynotes.deliverynotenumber='" . $_GET['TransNo'] . "' LIMIT 1";


$result=DB_query($sql, $ErrMsg);

//If there are no rows, there's a problem.
if (DB_num_rows($result)==0){
	$Title = _('Print Delivery Slip Error');
	include('includes/header.inc');
	echo '<div class="centre"><br /><br /><br />';
	prnMsg( _('Unable to Locate Picking Number') . ' : ' . $_GET['TransNo'] . ' ', 'error');
	echo '<br />
			<br />
			<br />
			<table class="table_index">
			<tr>
			<td class="menu_group_item">
			<li><a href="'. $RootPath . '/PrintCustOrder_DeliveryNote.php">' . _('Please Go Back') . '</a></li>
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
        if ($myrow['printed']==1 AND ($_GET['Reprint']!='OK' OR !isset($_GET['Reprint']))){
                $Title = _('Print Delivery Slip Error');
                include('includes/header.inc');
                echo '<p>';
                prnMsg( _('The delivery slip for delivery number') . ' ' . $_GET['TransNo'] . ' ' .
                        _('has previously been printed') . '. ' . _('It was printed on'). ' ' . ConvertSQLDate($myrow['printeddate']) .
                        '<br />' . _('This check is there to ensure that duplicate delivery slips are not produced and dispatched more than once to the customer'), 'warn' );
              echo '<a href="' . $RootPath. '/PrintCustOrder_DeliveryNote.php?TransNo=' . $_GET['TransNo'] . '&Reprint=OK">' .  _('Do a Re-Print') . ' (' . _('Plain paper') . ' - ' . _('A5') . ' ' . _('Potrait') . ') ' . _('Even Though Previously Printed'). '</a>';

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
$pdf->addInfo('Title', _('Customer Laser Packing Slip') );
$pdf->addInfo('Subject', _('Laser Packing slip for order') . ' ' . $_GET['TransNo']);
//$FontSize=12;
$line_height=12;
$PageNumber = 1;
$Copy = 'Office';

$ListCount = 0;
$Count = 0;
$FullPage = 0;

//for ($i=1;$i<=2;$i++){  //Print it out twice one copy for customer and one for office 
	if ($i==2){
		$PageNumber = 1;
		$Count = 0;
		$FullPage = 0;
		$pdf->newPage();
	}

	/* Now ... Has the order got any line items still outstanding to be invoiced */
	$ErrMsg = _('There was a problem retrieving the order details for Delivery Number') . ' ' . $_GET['TransNo'] . ' ' . _('from the database');

	$sql = "SELECT salesorderdetails.stkcode,
					stockmaster.description,
					stockmaster.units,
					deliverynotes.qtydelivered,
					salesorderdetails.unitprice,
					salesorderdetails.narrative,
					stockmaster.mbflag,
					stockmaster.decimalplaces
				FROM salesorderdetails
				INNER JOIN stockmaster
				ON salesorderdetails.stkcode=stockmaster.stockid
				INNER JOIN deliverynotes
				ON deliverynotes.salesorderlineno = salesorderdetails.orderlineno AND salesorderdetails.orderno = deliverynotes.salesorderno
				WHERE deliverynotes.deliverynotenumber='" . $_GET['TransNo'] . "'";
	$result=DB_query($sql, $ErrMsg);

	if (DB_num_rows($result)>0){
		/*Yes there are line items to start the ball rolling with a page header */
		include('includes/PDFOrderPageHeader_pickingnote.inc');
		//$YPos += 30;
		$YPosSUM =0;
		$Dynamic =-80;
		while ($myrow2=DB_fetch_array($result)){

            $ListCount ++;
			$Count ++;

			$DisplayQty = locale_number_format($myrow2['qtydelivered'],$myrow2['decimalplaces']);

			$LeftOvers = $pdf->addTextWrap($XPos,$YPos,87,$FontSize,$myrow2['stkcode']);
			$LeftOvers = $pdf->addTextWrap($XPos+100,$YPos,265,$FontSize,$myrow2['description']);
			$LeftOvers = $pdf->addTextWrap($XPos+380,$YPos,30,$FontSize,$myrow2['units'],'left');
			$LeftOvers = $pdf->addTextWrap($XPos+430,$YPos,70,$FontSize,$DisplayQty,'right');
			///////////////////////////////////////////////////////////////////////////////////

			if ($YPos-$line_height <= 50){
			/* We reached the end of the page so finsih off the page and start a newy */
				$PageNumber++;
				$Count = 0;
				$FullPage=1;
				include ('includes/PDFOrderPageHeader_pickingnote.inc');
			} //end if need a new page headed up
			else {
				/*increment a line down for the next line item */
				$YPos -= ($line_height);
				$YPosSUM += ($line_height);
				$Dynamic += ($line_height);
			}
		} //end while there are line items to print out
		//$Dynamic += $Dynami+12;
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

	//} /*end if there are order details to show on the order*/
if($Count >10){
$PageNumber++;
$Count = 0;
$FullPage=1;
include ('includes/PDFOrderPageHeader_pickingnote.inc');
}
	/*----------------------------------------------------------------------------------------------------------------------------*/
	$YP =$YPos-15-$Dynamic;
	$FontSize =8;
	$XPos -=12;
	$pdf->addText($XPos, $YP,$FontSize, _('Please Receive the above mentioned goods in good ORDER and CONDITION'));
	$pdf->addText($XPos, $YP-15,$FontSize, _('Authorized By'). ':');
	$pdf->addText($XPos, $YP-30,$FontSize,  _('Svc No'). ':.............................. '._('Name'). ':............................................................');
	//$pdf->addText($XPos, $YP-45,$FontSize, _('P/Svc No'). ':....................................................');
	//$pdf->addText($XPos, $YP-60,$FontSize,  _('Svc No'). ':........................................................');
	$pdf->addText($XPos, $YP-45,$FontSize,  _('Sign') . ':............................................... '._('Date') . ':..................................................');
	//$pdf->addText($XPos, $YP-90,$FontSize,  _('Date') . ':............................................................');
	
	$pdf->addText($XPos+260, $YP-15,$FontSize, _('Received By'). ':');
	$pdf->addText($XPos+260, $YP-30,$FontSize, _('Svc No'). ':.................................... '._('Name'). ':...................................................................');
	//$pdf->addText($XPos+260, $YP-45,$FontSize, _('P/Svc No'). ':......................................................');
	//$pdf->addText($XPos+260, $YP-60,$FontSize,  _('Svc No'). ':...............................');
	$pdf->addText($XPos+260, $YP-45,$FontSize,  _('Sign') . ':............................................................ '._('Date') . ':.................................................');
	//$pdf->addText($XPos+260, $YP-90,$FontSize,  _('Date') . ':.......................................');

	$pdf->addText(20, $YP-60,$FontSize,  _('KOFC 55020204'));
	$pdf->addText(220, $YP-60,$FontSize,  _('ISO 9001:2008 Certified Institution'));
	$pdf->addText(500, $YP-60,$FontSize,  _('ISSUE 3 REV 0'));
	//$pdf->addText(200, 20,$FontSize,  _('Page'). ':'.$PageNumber);

} /*end for loop to print the whole lot twice */

if ($ListCount == 0) {
	$Title = _('Print Delivery Slip Error');
	include('includes/header.inc');
	echo '<p>' .  _('There were no outstanding items on the order to deliver') . '. ' . _('A delivery slip cannot be printed').
			'<br /><a href="' . $RootPath . '/PrintCustOrder_DeliveryNote.php">' .  _('Print Another delivery Slip').
			'</a>
			<br /><a href="' . $RootPath . '/index.php">' . _('Back to the menu') . '</a>';
	include('includes/footer.inc');
	exit;
} else {
    	$pdf->OutputI($_SESSION['DatabaseName'] . '_DeliverySlip_' . date('Y-m-d') . '.pdf');
    	$pdf->__destruct();
}

?>