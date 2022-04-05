<?php
/* $Id: CounterSales.php 4469 2011-01-15 02:28:37Z daintree $*/

include('includes/DefineCartClass.php');
include('includes/DefinePayClass.php');
include('includes/DefineSerialItems.php');

/* Session started in session.inc for password checking and authorisation level check
config.php is in turn included in session.inc $PageSecurity now comes from session.inc (and gets read in by GetConfig.php*/

include('includes/session.inc');

$Title = _('Counter Sales');
/* webERP manual links before header.inc */
$ViewTopic= 'SalesOrders';
$BookMark = 'SalesOrderCounterSales';

include('includes/header.inc');
include('includes/GetPrice.inc');
include('includes/SQL_CommonFunctions.inc');
include('includes/GetSalesTransGLCodes.inc');

$AlreadyWarnedAboutCredit = false;
$_SESSION['ProhibitSaleBelowCost'] =1;

if (empty($_GET['identifier'])) {
	$identifier=date('U');
} else {
	$identifier=$_GET['identifier'];
}

if (isset($_GET['OrderNumber'])) {
	$_POST['SearchOrder']='True';
	$_POST['OrderNumber'] = $_GET['OrderNumber'];
}
if (isset($_SESSION['Items'.$identifier]) AND isset($_POST['Comments'])){
	$_SESSION['Items'.$identifier]->Comments = $_POST['Comments'];
	}
if (isset($_SESSION['Items'.$identifier]) AND isset($_POST['CustRef'])){
	//update the Items object variable with the data posted from the form
	$_SESSION['Items'.$identifier]->CustRef = $_POST['CustRef'];
	$_SESSION['Items'.$identifier]->Comments = $_POST['Comments'];
	//$_SESSION['Items'.$identifier]->DeliverTo = $_POST['DeliverTo'];
	//$_SESSION['Items'.$identifier]->PhoneNo = $_POST['PhoneNo'];
	//$_SESSION['Items'.$identifier]->Email = $_POST['Email'];
	if ($_SESSION['SalesmanLogin'] != '') {
		$_SESSION['Items' . $identifier]->SalesPerson = $_SESSION['SalesmanLogin'];
	}else{
		$_SESSION['Items' . $identifier]->SalesPerson = $_POST['SalesPerson'];
	}
}

if(isset($_POST['SearchReceipt']) && isset($_POST['InvoiceNumber']) && $_POST['InvoiceNumber'] !='') {
	$_POST['InvoiceNumber']=intval($_POST['InvoiceNumber']);
	unset($_SESSION['Items' . $identifier]->LineItems);
	unset($_SESSION['Items' . $identifier]);

	$_SESSION['ProcessingCredit'] = intval($_POST['InvoiceNumber']);
	$_SESSION['Items' . $identifier] = new cart;
	
	$_SESSION['Items'.$identifier]->TRANSACTION ="RETURN";
	
	$sql5 = "SELECT debtortrans.reference, debtortrans.transno FROM debtortrans
						 WHERE debtortrans.type=12 AND debtortrans.transno='" . $_POST['InvoiceNumber'] . "'";
	$result5=DB_query($sql5);
	$myrow5 = DB_fetch_array($result5);
	$_POST['InvoiceNumber'] = $myrow5['reference'];

/*read in all the guff from the selected invoice into the Items cart	*/


	$InvoiceHeaderSQL = "SELECT DISTINCT
								debtortrans.id as transid,
								debtortrans.debtorno,
								debtorsmaster.name,
								debtortrans.branchcode,
								debtortrans.reference,
								debtortrans.invtext,
								debtortrans.order_,
								debtortrans.trandate,
								debtortrans.tpe,
								debtortrans.shipvia,
								debtortrans.ovfreight,
								debtortrans.rate AS currency_rate,
								debtorsmaster.currcode,
								custbranch.defaultlocation,
								custbranch.taxgroupid,
								salesorders.salesperson,
								stockmoves.loccode,
								locations.taxprovinceid,
								currencies.decimalplaces
							FROM debtortrans INNER JOIN debtorsmaster
							ON debtortrans.debtorno = debtorsmaster.debtorno
							INNER JOIN custbranch
							ON debtortrans.branchcode = custbranch.branchcode
							AND debtortrans.debtorno = custbranch.debtorno
							INNER JOIN salesorders
							ON debtortrans.order_ = salesorders.orderno
							INNER JOIN currencies
							ON debtorsmaster.currcode = currencies.currabrev
							INNER JOIN stockmoves
							ON stockmoves.transno=debtortrans.transno
							AND stockmoves.type=debtortrans.type
							INNER JOIN locations ON
							stockmoves.loccode = locations.loccode
							INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canupd=1
							WHERE debtortrans.transno = '" . intval($_POST['InvoiceNumber']) . "'
							AND stockmoves.type=10";

	if($_SESSION['SalesmanLogin'] != '') {
		$sql .= " AND debtortrans.salesperson='" . $_SESSION['SalesmanLogin'] . "'";
	}
	$ErrMsg = _('A credit cannot be produced for the selected invoice') . '. ' . _('The invoice details cannot be retrieved because');
	$DbgMsg = _('The SQL used to retrieve the invoice details was');
	$GetInvHdrResult = DB_query($InvoiceHeaderSQL,$ErrMsg,$DbgMsg);

	if(DB_num_rows($GetInvHdrResult)==1) {

		$myrow = DB_fetch_array($GetInvHdrResult);

/*CustomerID variable registered by header.inc */
		$_SESSION['Items' . $identifier]->DebtorNo = $myrow['debtorno'];
		$_SESSION['Items' . $identifier]->TransID = $myrow['transid'];
		$_SESSION['Items' . $identifier]->Branch = $myrow['branchcode'];
		$_SESSION['Items' . $identifier]->CustomerName = $myrow['name'];
		$_SESSION['Items' . $identifier]->CustRef = $myrow['reference'];
		$_SESSION['Items' . $identifier]->Comments = $myrow['invtext'];
		$_SESSION['Items' . $identifier]->DefaultSalesType =$myrow['tpe'];
		$_SESSION['Items' . $identifier]->DefaultCurrency = $myrow['currcode'];
		$_SESSION['Items' . $identifier]->Location = $myrow['loccode'];
		$_SESSION['Old_FreightCost'] = $myrow['ovfreight'];
		$_SESSION['CurrencyRate'] = $myrow['currency_rate'];
		$_SESSION['Items' . $identifier]->OrderNo = $myrow['order_'];
		$_SESSION['Items' . $identifier]->ShipVia = $myrow['shipvia'];
		$_SESSION['Items' . $identifier]->TaxGroup = $myrow['taxgroupid'];
		$_SESSION['Items' . $identifier]->FreightCost = $myrow['ovfreight'];
		$_SESSION['Items' . $identifier]->DispatchTaxProvince = $myrow['taxprovinceid'];
		$_SESSION['Items' . $identifier]->GetFreightTaxes();
		$_SESSION['Items' . $identifier]->CurrDecimalPlaces = $myrow['decimalplaces'];
		$_SESSION['Items' . $identifier]->SalesPerson = $myrow['salesperson'];
		
		$SelectedCustomer = $myrow['debtorno'];
		$SelectedBranch = $myrow['branchcode'];
		$_SESSION['Items'.$identifier]->ReceiptN = $myrow5['transno'];

		DB_free_result($GetInvHdrResult);

/*now populate the line items array with the stock movement records for the invoice*/

		$LineItemsSQL = "SELECT stockmoves.stkmoveno,
								stockmoves.stockid,
								stockmaster.description,
								stockmaster.longdescription,
								stockmaster.volume,
								stockmaster.grossweight,
								stockmaster.mbflag,
								stockmaster.controlled,
								stockmaster.serialised,
								stockmaster.decimalplaces,
								stockmaster.taxcatid,
								stockmaster.units,
								stockmaster.discountcategory,
								(stockmoves.price * " . $_SESSION['CurrencyRate'] . ") AS price, -
								stockmoves.qty as quantity,
								stockmoves.discountpercent,
								stockmoves.trandate,
								stockmaster.materialcost
									+ stockmaster.labourcost
									+ stockmaster.overheadcost AS standardcost,
								stockmoves.narrative
							FROM stockmoves, stockmaster
							WHERE stockmoves.stockid = stockmaster.stockid
							AND stockmoves.transno ='" . $_POST['InvoiceNumber'] . "'
							AND stockmoves.type=10
							AND stockmoves.show_on_inv_crds=1";

		$ErrMsg = _('This invoice can not be credited using this program') . '. ' . _('A manual credit note will need to be prepared') . '. ' . _('The line items of the order cannot be retrieved because');
		$Dbgmsg = _('The SQL used to get the transaction header was');

		$LineItemsResult = DB_query($LineItemsSQL,$ErrMsg, $DbgMsg);

		if(DB_num_rows($LineItemsResult)>0) {

			while($myrow=DB_fetch_array($LineItemsResult)) {

				$LineNumber = $_SESSION['Items' . $identifier]->LineCounter;

				$_SESSION['Items' . $identifier]->add_to_cart($myrow['stockid'],
														$myrow['quantity'],
														$myrow['description'],
														$myrow['longdescription'],
														$myrow['price'],
														$myrow['discountpercent'],
														$myrow['units'],
														$myrow['volume'],
														$myrow['grossweight'],
														0,
														$myrow['mbflag'],
														$myrow['trandate'],
														0,
														$myrow['discountcategory'],
														$myrow['controlled'],
														$myrow['serialised'],
														$myrow['decimalplaces'],
														$myrow['narrative'],
														'No',
														-1,
														$myrow['taxcatid'],
														'',
														'',
														'',
														$myrow['standardcost']);

				$_SESSION['Items' . $identifier]->GetExistingTaxes($LineNumber, $myrow['stkmoveno']);

				if($myrow['controlled']==1) {/* Populate the SerialItems array too*/

					$SQL = "SELECT 	serialno,
									moveqty
							FROM stockserialmoves
							WHERE stockmoveno='" . $myrow['stkmoveno'] . "'
							AND stockid = '" . $myrow['stockid'] . "'";

					$ErrMsg = _('This invoice can not be credited using this program') . '. ' . _('A manual credit note will need to be prepared') . '. ' . _('The line item') . ' ' . $myrow['stockid'] . ' ' . _('is controlled but the serial numbers or batch numbers could not be retrieved because');
					$DbgMsg = _('The SQL used to get the controlled item details was');
					$SerialItemsResult = DB_query($SQL,$ErrMsg, $DbgMsg);

					while($SerialItemsRow = DB_fetch_array($SerialItemsResult)) {
						$_SESSION['Items' . $identifier]->LineItems[$LineNumber]->SerialItems[$SerialItemsRow['serialno']] = new SerialItem($SerialItemsRow['serialno'], -$SerialItemsRow['moveqty']);
						$_SESSION['Items' . $identifier]->LineItems[$LineNumber]->QtyDispatched -= $SerialItemsRow['moveqty'];
					}
				} /* end if the item is a controlled item */
			} /* loop thro line items from stock movement records */

		} else { /* there are no stock movement records created for that invoice */

			echo '<div class="centre"><a href="' . $RootPath . '/index.php">' . _('Back to the menu') . '</a></div>';
			prnMsg( _('There are no line items that were retrieved for this invoice') . '. ' . _('The automatic credit program can not create a credit note from this invoice'),'warn');
			include('includes/footer.inc');
			exit;
		} //end of checks on returned data set
		DB_free_result($LineItemsResult);
	} else {
		prnMsg( _('This Receipt can not be credited using the automatic facility') . '<br />' . _('CRITICAL ERROR') . ': ' . _('Please report that a duplicate DebtorTrans header record was found for Receipt Number') . ' ' . $_SESSION['ProcessingCredit'].' or it does not exist','warn');
		//include('includes/footer.inc');
		//exit;
	} //valid invoice record returned from the entered invoice number

}

if(isset($_POST['SearchInv']) && isset($_POST['InvNumber']) && $_POST['InvNumber'] !='') {
	$_POST['InvNumber']=intval($_POST['InvNumber']);
	unset($_SESSION['Items' . $identifier]->LineItems);
	unset($_SESSION['Items' . $identifier]);

	$_SESSION['ProcessingInv'] = intval($_POST['InvNumber']);
	$_SESSION['Items' . $identifier] = new cart;
	
	$_SESSION['Items'.$identifier]->TRANSACTION ="RECEIPT INVOICE";

/*read in all the guff from the selected invoice into the Items cart	*/


	$InvoiceHeaderSQL = "SELECT DISTINCT
								debtortrans.id as transid,
								debtortrans.debtorno,
								debtorsmaster.name,
								debtortrans.branchcode,
								debtortrans.reference,
								debtortrans.invtext,
								debtortrans.order_,
								debtortrans.trandate,
								debtortrans.tpe,
								debtortrans.shipvia,
								debtortrans.ovfreight,
								debtortrans.rate AS currency_rate,
								debtorsmaster.currcode,
								custbranch.defaultlocation,
								custbranch.taxgroupid,
								salesorders.salesperson,
								stockmoves.loccode,
								locations.taxprovinceid,
								currencies.decimalplaces
							FROM debtortrans INNER JOIN debtorsmaster
							ON debtortrans.debtorno = debtorsmaster.debtorno
							INNER JOIN custbranch
							ON debtortrans.branchcode = custbranch.branchcode
							AND debtortrans.debtorno = custbranch.debtorno
							INNER JOIN salesorders
							ON debtortrans.order_ = salesorders.orderno
							INNER JOIN currencies
							ON debtorsmaster.currcode = currencies.currabrev
							INNER JOIN stockmoves
							ON stockmoves.transno=debtortrans.transno
							AND stockmoves.type=debtortrans.type
							INNER JOIN locations ON
							stockmoves.loccode = locations.loccode
							INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canupd=1
							WHERE debtortrans.transno = '" . intval($_POST['InvNumber']) . "'
							AND stockmoves.type=10 and settled=0";

	if($_SESSION['SalesmanLogin'] != '') {
		$sql .= " AND debtortrans.salesperson='" . $_SESSION['SalesmanLogin'] . "'";
	}
	$ErrMsg = _('A credit cannot be produced for the selected invoice') . '. ' . _('The invoice details cannot be retrieved because');
	$DbgMsg = _('The SQL used to retrieve the invoice details was');
	$GetInvHdrResult = DB_query($InvoiceHeaderSQL,$ErrMsg,$DbgMsg);

	if(DB_num_rows($GetInvHdrResult)==1) {

		$myrow = DB_fetch_array($GetInvHdrResult);

/*CustomerID variable registered by header.inc */
		$_SESSION['Items' . $identifier]->DebtorNo = $myrow['debtorno'];
		$_SESSION['Items' . $identifier]->TransID = $myrow['transid'];
		$_SESSION['Items' . $identifier]->Branch = $myrow['branchcode'];
		$_SESSION['Items' . $identifier]->CustomerName = $myrow['name'];
		$_SESSION['Items' . $identifier]->CustRef = $myrow['reference'];
		$_SESSION['Items' . $identifier]->Comments = $myrow['invtext'];
		$_SESSION['Items' . $identifier]->DefaultSalesType =$myrow['tpe'];
		$_SESSION['Items' . $identifier]->DefaultCurrency = $myrow['currcode'];
		$_SESSION['Items' . $identifier]->Location = $myrow['loccode'];
		$_SESSION['Old_FreightCost'] = $myrow['ovfreight'];
		$_SESSION['CurrencyRate'] = $myrow['currency_rate'];
		$_SESSION['Items' . $identifier]->OrderNo = $myrow['order_'];
		$_SESSION['Items' . $identifier]->ShipVia = $myrow['shipvia'];
		$_SESSION['Items' . $identifier]->TaxGroup = $myrow['taxgroupid'];
		$_SESSION['Items' . $identifier]->FreightCost = $myrow['ovfreight'];
		$_SESSION['Items' . $identifier]->DispatchTaxProvince = $myrow['taxprovinceid'];
		$_SESSION['Items' . $identifier]->GetFreightTaxes();
		$_SESSION['Items' . $identifier]->CurrDecimalPlaces = $myrow['decimalplaces'];
		$_SESSION['Items' . $identifier]->SalesPerson = $myrow['salesperson'];
		
		$SelectedCustomer = $myrow['debtorno'];
		$SelectedBranch = $myrow['branchcode'];
		$_SESSION['Items'.$identifier]->InvoiceN = $_POST['InvNumber'];

		DB_free_result($GetInvHdrResult);

/*now populate the line items array with the stock movement records for the invoice*/

		$LineItemsSQL = "SELECT stockmoves.stkmoveno,
								stockmoves.stockid,
								stockmaster.description,
								stockmaster.longdescription,
								stockmaster.volume,
								stockmaster.grossweight,
								stockmaster.mbflag,
								stockmaster.controlled,
								stockmaster.serialised,
								stockmaster.decimalplaces,
								stockmaster.taxcatid,
								stockmaster.units,
								stockmaster.discountcategory,
								(stockmoves.price * " . $_SESSION['CurrencyRate'] . ") AS price, -
								stockmoves.qty as quantity,
								stockmoves.discountpercent,
								stockmoves.trandate,
								stockmaster.materialcost
									+ stockmaster.labourcost
									+ stockmaster.overheadcost AS standardcost,
								stockmoves.narrative
							FROM stockmoves, stockmaster
							WHERE stockmoves.stockid = stockmaster.stockid
							AND stockmoves.transno ='" . $_POST['InvNumber'] . "'
							AND stockmoves.type=10
							AND stockmoves.show_on_inv_crds=1";

		$ErrMsg = _('This invoice can not be retrieved using this program') . '. ' . _('The line items of the order cannot be retrieved because');
		$Dbgmsg = _('The SQL used to get the transaction header was');

		$LineItemsResult = DB_query($LineItemsSQL,$ErrMsg, $DbgMsg);

		if(DB_num_rows($LineItemsResult)>0) {

			while($myrow=DB_fetch_array($LineItemsResult)) {

				$LineNumber = $_SESSION['Items' . $identifier]->LineCounter;

				$_SESSION['Items' . $identifier]->add_to_cart($myrow['stockid'],
														$myrow['quantity'],
														$myrow['description'],
														$myrow['longdescription'],
														$myrow['price'],
														$myrow['discountpercent'],
														$myrow['units'],
														$myrow['volume'],
														$myrow['grossweight'],
														0,
														$myrow['mbflag'],
														$myrow['trandate'],
														0,
														$myrow['discountcategory'],
														$myrow['controlled'],
														$myrow['serialised'],
														$myrow['decimalplaces'],
														$myrow['narrative'],
														'No',
														-1,
														$myrow['taxcatid'],
														'',
														'',
														'',
														$myrow['standardcost']);

				$_SESSION['Items' . $identifier]->GetExistingTaxes($LineNumber, $myrow['stkmoveno']);

				if($myrow['controlled']==1) {/* Populate the SerialItems array too*/

					$SQL = "SELECT 	serialno,
									moveqty
							FROM stockserialmoves
							WHERE stockmoveno='" . $myrow['stkmoveno'] . "'
							AND stockid = '" . $myrow['stockid'] . "'";

					$ErrMsg = _('This invoice can not be credited using this program') . '. ' . _('A manual credit note will need to be prepared') . '. ' . _('The line item') . ' ' . $myrow['stockid'] . ' ' . _('is controlled but the serial numbers or batch numbers could not be retrieved because');
					$DbgMsg = _('The SQL used to get the controlled item details was');
					$SerialItemsResult = DB_query($SQL,$ErrMsg, $DbgMsg);

					while($SerialItemsRow = DB_fetch_array($SerialItemsResult)) {
						$_SESSION['Items' . $identifier]->LineItems[$LineNumber]->SerialItems[$SerialItemsRow['serialno']] = new SerialItem($SerialItemsRow['serialno'], -$SerialItemsRow['moveqty']);
						$_SESSION['Items' . $identifier]->LineItems[$LineNumber]->QtyDispatched -= $SerialItemsRow['moveqty'];
					}
				} /* end if the item is a controlled item */
			} /* loop thro line items from stock movement records */

		} else { /* there are no stock movement records created for that invoice */

			//echo '<div class="centre"><a href="' . $RootPath . '/index.php">' . _('Back to the menu') . '</a></div>';
			prnMsg( _('There are no line items that were retrieved for this invoice') . '. ','warn');
			//include('includes/footer.inc');
			//exit;
		} //end of checks on returned data set
		DB_free_result($LineItemsResult);
	} else {
		prnMsg( _('This Invoice can not be retrieved using the automatic facility') . '<br />' . _('CRITICAL ERROR') . ': ' . _('Please report that a duplicate record was found for Invoice Number') . ' ' . $_SESSION['ProcessingInv'].' or it does not exist','warn');
		//include('includes/footer.inc');
		//exit;
	} //valid invoice record returned from the entered invoice number

}

if(isset($_POST['SearchOrder']) && isset($_POST['OrderNumber']) && $_POST['OrderNumber'] !='') {

	unset($_SESSION['Items'.$identifier]->LineItems);
	unset ($_SESSION['Items'.$identifier]);

	$_SESSION['ProcessingOrder']=(int)$_POST['OrderNumber'];
	$_GET['OrderNumber']=(int)$_POST['OrderNumber'];
	$_SESSION['Items'.$identifier] = new cart;
	$_SESSION['Items'.$identifier]->TRANSACTION ="INVOICE SALES ORDER";
/*read in all the guff from the selected order into the Items cart  */

	$OrderHeaderSQL = "SELECT salesorders.orderno,
								salesorders.debtorno,
								debtorsmaster.name,
								salesorders.branchcode,
								salesorders.customerref,
								salesorders.comments,
								salesorders.orddate,
								salesorders.ordertype,
								salesorders.shipvia,
								salesorders.deliverto,
								salesorders.deladd1,
								salesorders.deladd2,
								salesorders.deladd3,
								salesorders.deladd4,
								salesorders.deladd5,
								salesorders.deladd6,
								salesorders.contactphone,
								salesorders.contactemail,
								salesorders.salesperson,
								salesorders.freightcost,
								salesorders.deliverydate,
								debtorsmaster.currcode,
								salesorders.fromstkloc,
								locations.taxprovinceid,
								custbranch.taxgroupid,
								currencies.rate as currency_rate,
								currencies.decimalplaces,
								custbranch.defaultshipvia,
								custbranch.specialinstructions
						FROM salesorders INNER JOIN debtorsmaster
						ON salesorders.debtorno = debtorsmaster.debtorno
						INNER JOIN custbranch
						ON salesorders.branchcode = custbranch.branchcode
						AND salesorders.debtorno = custbranch.debtorno
						INNER JOIN currencies
						ON debtorsmaster.currcode = currencies.currabrev
						INNER JOIN locations
						ON locations.loccode=salesorders.fromstkloc
						INNER JOIN locationusers ON locationusers.loccode=salesorders.fromstkloc AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canupd=1
						WHERE salesorders.orderno = '" . $_GET['OrderNumber']."'";

	if ($_SESSION['SalesmanLogin'] != '') {
		$OrderHeaderSQL .= " AND salesorders.salesperson='" . $_SESSION['SalesmanLogin'] . "'";
	}

	$ErrMsg = _('The order cannot be retrieved because');
	$DbgMsg = _('The SQL to get the order header was');
	$GetOrdHdrResult = DB_query($OrderHeaderSQL,$ErrMsg,$DbgMsg);

	if (DB_num_rows($GetOrdHdrResult)==1) {

		$myrow = DB_fetch_array($GetOrdHdrResult);

		$_SESSION['Items'.$identifier]->DebtorNo = $myrow['debtorno'];
		$_SESSION['Items'.$identifier]->OrderNo = $myrow['orderno'];
		$_SESSION['Items'.$identifier]->Branch = $myrow['branchcode'];
		$_SESSION['Items'.$identifier]->CustomerName = $myrow['name'];
		$_SESSION['Items'.$identifier]->CustRef = $myrow['customerref'];
		$_SESSION['Items'.$identifier]->Comments = $myrow['comments'];
		$_SESSION['Items'.$identifier]->DefaultSalesType =$myrow['ordertype'];
		$_SESSION['Items'.$identifier]->DefaultCurrency = $myrow['currcode'];
		$_SESSION['Items'.$identifier]->CurrDecimalPlaces = $myrow['decimalplaces'];
		$BestShipper = $myrow['shipvia'];
		$_SESSION['Items'.$identifier]->ShipVia = $myrow['shipvia'];

		if (is_null($BestShipper)){
		   $BestShipper=0;
		}
		$_SESSION['Items'.$identifier]->DeliverTo = $myrow['deliverto'];
		$_SESSION['Items'.$identifier]->DeliveryDate = ConvertSQLDate($myrow['deliverydate']);
		$_SESSION['Items'.$identifier]->BrAdd1 = $myrow['deladd1'];
		$_SESSION['Items'.$identifier]->BrAdd2 = $myrow['deladd2'];
		$_SESSION['Items'.$identifier]->BrAdd3 = $myrow['deladd3'];
		$_SESSION['Items'.$identifier]->BrAdd4 = $myrow['deladd4'];
		$_SESSION['Items'.$identifier]->BrAdd5 = $myrow['deladd5'];
		$_SESSION['Items'.$identifier]->BrAdd6 = $myrow['deladd6'];
		$_SESSION['Items'.$identifier]->PhoneNo = $myrow['contactphone'];
		$_SESSION['Items'.$identifier]->Email = $myrow['contactemail'];
		$_SESSION['Items'.$identifier]->SalesPerson = $myrow['salesperson'];

		$_SESSION['Items'.$identifier]->Location = $myrow['fromstkloc'];
		$_SESSION['Items'.$identifier]->FreightCost = $myrow['freightcost'];
		$_SESSION['Old_FreightCost'] = $myrow['freightcost'];
//		$_POST['ChargeFreightCost'] = $_SESSION['Old_FreightCost'];
		$_SESSION['Items'.$identifier]->Orig_OrderDate = $myrow['orddate'];
		$_SESSION['CurrencyRate'] = $myrow['currency_rate'];
		$_SESSION['Items'.$identifier]->TaxGroup = $myrow['taxgroupid'];
		$_SESSION['Items'.$identifier]->DispatchTaxProvince = $myrow['taxprovinceid'];
		$_SESSION['Items'.$identifier]->GetFreightTaxes();
		$_SESSION['Items'.$identifier]->SpecialInstructions = $myrow['specialinstructions'];
		
		$_SESSION['Items'.$identifier]->OrderN = $myrow['orderno'];

		DB_free_result($GetOrdHdrResult);

/*now populate the line items array with the sales order details records */

		$LineItemsSQL = "SELECT stkcode,
								stockmaster.description,
								stockmaster.longdescription,
								stockmaster.controlled,
								stockmaster.serialised,
								stockmaster.volume,
								stockmaster.grossweight,
								stockmaster.units,
								stockmaster.decimalplaces,
								stockmaster.mbflag,
								stockmaster.taxcatid,
								stockmaster.discountcategory,
								salesorderdetails.unitprice,
								salesorderdetails.quantity,
								salesorderdetails.discountpercent,
								salesorderdetails.actualdispatchdate,
								salesorderdetails.qtyinvoiced,
								salesorderdetails.narrative,
								salesorderdetails.orderlineno,
								salesorderdetails.poline,
								salesorderdetails.itemdue,
								stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS standardcost
							FROM salesorderdetails INNER JOIN stockmaster
							 	ON salesorderdetails.stkcode = stockmaster.stockid
							WHERE salesorderdetails.orderno ='" . $_GET['OrderNumber'] . "'
							AND salesorderdetails.quantity - salesorderdetails.qtyinvoiced >0
							ORDER BY salesorderdetails.orderlineno";

		$ErrMsg = _('The line items of the order cannot be retrieved because');
		$DbgMsg = _('The SQL that failed was');
		$LineItemsResult = DB_query($LineItemsSQL,$ErrMsg,$DbgMsg);

		if (DB_num_rows($LineItemsResult)>0) {

			while ($myrow=DB_fetch_array($LineItemsResult)) {

				$_SESSION['Items'.$identifier]->add_to_cart($myrow['stkcode'],
											$myrow['quantity'],
											$myrow['description'],
											$myrow['longdescription'],
											$myrow['unitprice'],
											$myrow['discountpercent'],
											$myrow['units'],
											$myrow['volume'],
											$myrow['grossweight'],
											0,
											$myrow['mbflag'],
											$myrow['actualdispatchdate'],
											$myrow['qtyinvoiced'],
											$myrow['discountcategory'],
											$myrow['controlled'],
											$myrow['serialised'],
											$myrow['decimalplaces'],
											htmlspecialchars_decode($myrow['narrative']),
											'No',
											$myrow['orderlineno'],
											$myrow['taxcatid'],
											'',
											$myrow['itemdue'],
											$myrow['poline'],
											$myrow['standardcost']);	/*NB NO Updates to DB */

				/*Calculate the taxes applicable to this line item from the customer branch Tax Group and Item Tax Category */

				$_SESSION['Items'.$identifier]->GetTaxes($myrow['orderlineno']);

			} /* line items from sales order details */
		} else { /* there are no line items that have a quantity to deliver */
			echo '<br />';
			prnMsg( _('There are no ordered items with a quantity left to Invoice. There is nothing left to invoice'));
			//include('includes/footer.inc');
			//exit;

		} //end of checks on returned data set
		DB_free_result($LineItemsResult);

	} else { /*end if the order was returned sucessfully */

		echo '<br />' .
		prnMsg( _('This order item could not be retrieved. Please select another order'), 'warn');
		//include ('includes/footer.inc');
		//exit;
	} //valid order returned from the entered order number
}

//==============================================================================================
if (isset($_POST['SelectedCustomer']) && isset($_POST['SelectedBranch'])){
		$SelectedCustomer = $_POST['SelectedCustomer'];
		$SelectedBranch = $_POST['SelectedBranch'];
}

/* will only be true if page called from customer selection form or set because only one customer
 record returned from a search so parse the $SelectCustomer string into customer code and branch code */
if (isset($SelectedCustomer)) {

	$_SESSION['Items'.$identifier]->DebtorNo = trim($SelectedCustomer);
	$_SESSION['Items'.$identifier]->Branch = trim($SelectedBranch);

	// Now check to ensure this account is not on hold */
	$sql = "SELECT debtorsmaster.name,
					holdreasons.dissallowinvoices,
					debtorsmaster.salestype,
					salestypes.sales_type,
					debtorsmaster.currcode,
					debtorsmaster.customerpoline,
					paymentterms.terms,
					currencies.decimalplaces
			FROM debtorsmaster INNER JOIN holdreasons
			ON debtorsmaster.holdreason=holdreasons.reasoncode
			INNER JOIN salestypes
			ON debtorsmaster.salestype=salestypes.typeabbrev
			INNER JOIN paymentterms
			ON debtorsmaster.paymentterms=paymentterms.termsindicator
			INNER JOIN currencies
			ON debtorsmaster.currcode=currencies.currabrev
			WHERE debtorsmaster.debtorno = '" . $_SESSION['Items'.$identifier]->DebtorNo. "'";

	$ErrMsg = _('The details of the customer selected') . ': ' .  $_SESSION['Items'.$identifier]->DebtorNo . ' ' . _('cannot be retrieved because');
	$DbgMsg = _('The SQL used to retrieve the customer details and failed was') . ':';
	$result =DB_query($sql,$ErrMsg,$DbgMsg);

	$myrow = DB_fetch_array($result);
	if ($myrow[1] != 1){
		if ($myrow[1]==2){
			prnMsg(_('The') . ' ' . htmlspecialchars($myrow[0], ENT_QUOTES, 'UTF-8', false) . ' ' . _('account is currently flagged as an account that needs to be watched. Please contact the credit control personnel to discuss'),'warn');
		}

		$_SESSION['RequireCustomerSelection']=0;
		$_SESSION['Items'.$identifier]->CustomerName = $myrow['name'];

# the sales type determines the price list to be used by default the customer of the user is
# defaulted from the entry of the userid and password.

		$_SESSION['Items'.$identifier]->DefaultSalesType = $myrow['salestype'];
		$_SESSION['Items'.$identifier]->SalesTypeName = $myrow['sales_type'];
		$_SESSION['Items'.$identifier]->DefaultCurrency = $myrow['currcode'];
		$_SESSION['Items'.$identifier]->DefaultPOLine = $myrow['customerpoline'];
		$_SESSION['Items'.$identifier]->PaymentTerms = $myrow['terms'];
		$_SESSION['Items'.$identifier]->CurrDecimalPlaces = $myrow['decimalplaces'];

# the branch was also selected from the customer selection so default the delivery details from the customer branches table CustBranch. The order process will ask for branch details later anyway
		$result = GetCustBranchDetails($identifier);

		if (DB_num_rows($result)==0){

			prnMsg(_('The branch details for branch code') . ': ' . $_SESSION['Items'.$identifier]->Branch . ' ' . _('against customer code') . ': ' . $_SESSION['Items'.$identifier]->DebtorNo . ' ' . _('could not be retrieved') . '. ' . _('Check the set up of the customer and branch'),'error');

			if ($debug==1){
				prnMsg( _('The SQL that failed to get the branch details was') . ':<br />' . $sql . 'warning');
			}
			include('includes/footer.inc');
			exit;
		}
		// add echo
		echo '<br />';
		$myrow = DB_fetch_array($result);
		if ($_SESSION['SalesmanLogin']!=NULL AND $_SESSION['SalesmanLogin']!=$myrow['salesman']){
			prnMsg(_('Your login is only set up for a particular salesperson. This customer has a different salesperson.'),'error');
			include('includes/footer.inc');
			exit;
		}
		$_SESSION['Items'.$identifier]->DeliverTo = $myrow['brname'];
		$_SESSION['Items'.$identifier]->DelAdd1 = $myrow['braddress1'];
		$_SESSION['Items'.$identifier]->DelAdd2 = $myrow['braddress2'];
		$_SESSION['Items'.$identifier]->DelAdd3 = $myrow['braddress3'];
		$_SESSION['Items'.$identifier]->DelAdd4 = $myrow['braddress4'];
		$_SESSION['Items'.$identifier]->DelAdd5 = $myrow['braddress5'];
		$_SESSION['Items'.$identifier]->DelAdd6 = $myrow['braddress6'];
		$_SESSION['Items'.$identifier]->PhoneNo = $myrow['phoneno'];
		$_SESSION['Items'.$identifier]->Email = $myrow['email'];
		//$_SESSION['Items'.$identifier]->Location = $myrow['defaultlocation'];
		$_SESSION['Items'.$identifier]->ShipVia = $myrow['defaultshipvia'];
		$_SESSION['Items'.$identifier]->DeliverBlind = $myrow['deliverblind'];
		$_SESSION['Items'.$identifier]->SpecialInstructions = $myrow['specialinstructions'];
		$_SESSION['Items'.$identifier]->DeliveryDays = $myrow['estdeliverydays'];
		//$_SESSION['Items'.$identifier]->LocationName = $myrow['locationname'];
		if ($_SESSION['SalesmanLogin']!= NULL AND $_SESSION['SalesmanLogin']!=''){
			$_SESSION['Items'.$identifier]->SalesPerson = $_SESSION['SalesmanLogin'];
		} else {
			$_SESSION['Items'.$identifier]->SalesPerson = $myrow['salesman'];
		}
		if ($_SESSION['Items'.$identifier]->SpecialInstructions)
		  prnMsg($_SESSION['Items'.$identifier]->SpecialInstructions,'warn');

		if ($_SESSION['CheckCreditLimits'] > 0){  /*Check credit limits is 1 for warn and 2 for prohibit sales */
			$_SESSION['Items'.$identifier]->CreditAvailable = GetCreditAvailable($_SESSION['Items'.$identifier]->DebtorNo,$db);

			if ($_SESSION['CheckCreditLimits']==1 AND $_SESSION['Items'.$identifier]->CreditAvailable <=0){
				prnMsg(_('The') . ' ' . htmlspecialchars($myrow[0], ENT_QUOTES, 'UTF-8', false) . ' ' . _('account is currently at or over their credit limit'),'warn');
			} elseif ($_SESSION['CheckCreditLimits']==2 AND $_SESSION['Items'.$identifier]->CreditAvailable <=0){
				prnMsg(_('No more orders can be placed by') . ' ' . htmlspecialchars($myrow[0], ENT_QUOTES, 'UTF-8', false) . ' ' . _(' their account is currently at or over their credit limit'),'warn');
				include('includes/footer.inc');
				exit;
			}
		}

	} else {
		prnMsg(_('The') . ' ' . htmlspecialchars($myrow[0], ENT_QUOTES, 'UTF-8', false) . ' ' . _('account is currently on hold please contact the credit control personnel to discuss'),'warn');
	}

}

if(isset($_POST['SelectedLoc'])){
		$sqlresult =DB_query("SELECT loccode,locationname FROM locations WHERE loccode='".$_POST['SelectedLoc']."'");	
		$row = DB_fetch_row($sqlresult);
		$_SESSION['Items'.$identifier]->Location = $row[0];
		$_SESSION['Items'.$identifier]->LocationName = $row[1];
		
		}
//==============================================================================================

/*if (isset($_SESSION['Pay'.$identifier])){
	//update the Items object variable with the data posted from the form
	$_SESSION['Pay'.$identifier]->ID = $_POST['CustRef'];
	$_SESSION['Pay'.$identifier]->Comments = $_POST['Comments'];
}
*/
if (isset($_POST['QuickEntry'])){
	unset($_POST['PartSearch']);
}

if (isset($_POST['SelectingOrderItems'])){
	foreach ($_POST as $FormVariable => $Quantity) {
		if (mb_strpos($FormVariable,'OrderQty')!==false) {
			$NewItemArray[$_POST['StockID' . mb_substr($FormVariable,8)]] = filter_number_format($Quantity);
		}
	}
}

if (isset($_GET['NewItem'])){
	$NewItem = trim($_GET['NewItem']);
}

if (isset($_GET['NewOrder'])){
	/*New order entry - clear any existing order details from the Items object and initiate a newy*/
	 if (isset($_SESSION['Items'.$identifier])){
		unset ($_SESSION['Items'.$identifier]->LineItems);
		$_SESSION['Items'.$identifier]->ItemsOrdered=0;
		unset ($_SESSION['Items'.$identifier]);
		
		unset ($_SESSION['Pay'.$identifier]->LineItems);
		unset ($_SESSION['Pay'.$identifier]);
	}
}


if (!isset($_SESSION['Items'.$identifier])){
	/* It must be a new order being created $_SESSION['Items'.$identifier] would be set up from the order
	modification code above if a modification to an existing order. Also $ExistingOrder would be
	set to 1. The delivery check screen is where the details of the order are either updated or
	inserted depending on the value of ExistingOrder */
	

	$_SESSION['ExistingOrder'. $identifier] = 0;
	$_SESSION['Items'.$identifier] = new cart;
	$_SESSION['Pay'.$identifier] = new pay;
	$_SESSION['PrintedPackingSlip'] = 0; /*Of course 'cos the order ain't even started !!*/
	$_SESSION['Items'.$identifier]->TRANSACTION ="SALES ORDER"; //needed
	/*Get the default customer-branch combo from the user's default location record */
	$sql = "SELECT cashsalecustomer,
				cashsalebranch,
				locationname,
				taxprovinceid
			FROM locations
			WHERE loccode='" . $_SESSION['UserStockLocation'] ."'";
	$result = DB_query($sql);
	if (DB_num_rows($result)==0) {
		prnMsg(_('Your user account does not have a valid default inventory location set up. Please see the system administrator to modify your user account.'),'error');
		include('includes/footer.inc');
		exit;
	} else {
		$myrow = DB_fetch_array($result); //get the only row returned

		if ($myrow['cashsalecustomer']=='' OR $myrow['cashsalebranch']==''){
			prnMsg(_('To use this script it is first necessary to define a cash sales customer for the location that is your default location. The default cash sale customer is defined under set up ->Inventory Locations Maintenance. The customer should be entered using the customer code and a valid branch code of the customer entered.'),'error');
			include('includes/footer.inc');
			exit;
		}
		if (isset($_GET['DebtorNo'])) {
			$_SESSION['Items'.$identifier]->DebtorNo = $_GET['DebtorNo'];
			$_SESSION['Items'.$identifier]->Branch = $_GET['BranchNo'];
		} else {
			$_SESSION['Items'.$identifier]->Branch = $myrow['cashsalebranch'];
			$_SESSION['Items'.$identifier]->DebtorNo = $myrow['cashsalecustomer'];
		}

		$_SESSION['Items'.$identifier]->LocationName = $myrow['locationname'];
		$_SESSION['Items'.$identifier]->Location = $_SESSION['UserStockLocation'];
		$_SESSION['Items'.$identifier]->DispatchTaxProvince = $myrow['taxprovinceid'];

		// Now check to ensure this account exists and set defaults */
		$sql = "SELECT debtorsmaster.name,
					holdreasons.dissallowinvoices,
					debtorsmaster.salestype,
					salestypes.sales_type,
					debtorsmaster.currcode,
					debtorsmaster.customerpoline,
					paymentterms.terms,
					currencies.decimalplaces
				FROM debtorsmaster INNER JOIN holdreasons
				ON debtorsmaster.holdreason=holdreasons.reasoncode
				INNER JOIN salestypes
				ON debtorsmaster.salestype=salestypes.typeabbrev
				INNER JOIN paymentterms
				ON debtorsmaster.paymentterms=paymentterms.termsindicator
				INNER JOIN currencies
				ON debtorsmaster.currcode=currencies.currabrev
				WHERE debtorsmaster.debtorno = '" . $_SESSION['Items'.$identifier]->DebtorNo . "'";

		$ErrMsg = _('The details of the customer selected') . ': ' .  $_SESSION['Items'.$identifier]->DebtorNo . ' ' . _('cannot be retrieved because');
		$DbgMsg = _('The SQL used to retrieve the customer details and failed was') . ':';
		// echo $sql;
		$result =DB_query($sql,$ErrMsg,$DbgMsg);

		$myrow = DB_fetch_array($result);
		if ($myrow['dissallowinvoices'] != 1){
			if ($myrow['dissallowinvoices']==2){
				prnMsg($myrow['name'] . ' ' . _('Although this account is defined as the cash sale account for the location.  The account is currently flagged as an account that needs to be watched. Please contact the credit control personnel to discuss'),'warn');
			}

			$_SESSION['RequireCustomerSelection']=0;
			$_SESSION['Items'.$identifier]->CustomerName = $myrow['name'];
			// the sales type is the price list to be used for this sale
			$_SESSION['Items'.$identifier]->DefaultSalesType = $myrow['salestype'];
			$_SESSION['Items'.$identifier]->SalesTypeName = $myrow['sales_type'];
			$_SESSION['Items'.$identifier]->DefaultCurrency = $myrow['currcode'];
			$_SESSION['Items'.$identifier]->DefaultPOLine = $myrow['customerpoline'];
			$_SESSION['Items'.$identifier]->PaymentTerms = $myrow['terms'];
			$_SESSION['Items'.$identifier]->CurrDecimalPlaces = $myrow['decimalplaces'];
			/* now get the branch defaults from the customer branches table CustBranch. */

			$sql = "SELECT custbranch.brname,
				       custbranch.braddress1,
				       custbranch.defaultshipvia,
				       custbranch.deliverblind,
				       custbranch.specialinstructions,
				       custbranch.estdeliverydays,
				       custbranch.salesman,
				       custbranch.taxgroupid,
				       custbranch.defaultshipvia
				FROM custbranch
				WHERE custbranch.branchcode='" . $_SESSION['Items'.$identifier]->Branch . "'
				AND custbranch.debtorno = '" . $_SESSION['Items'.$identifier]->DebtorNo . "'";
            $ErrMsg = _('The customer branch record of the customer selected') . ': ' . $_SESSION['Items'.$identifier]->Branch . ' ' . _('cannot be retrieved because');
			$DbgMsg = _('SQL used to retrieve the branch details was') . ':';
			$result =DB_query($sql,$ErrMsg,$DbgMsg);

			if (DB_num_rows($result)==0){

				prnMsg(_('The branch details for branch code') . ': ' . $_SESSION['Items'.$identifier]->Branch . ' ' . _('against customer code') . ': ' . $_SESSION['Items'.$identifier]->DebtorNo . ' ' . _('could not be retrieved') . '. ' . _('Check the set up of the customer and branch'),'error');

				if ($debug==1){
					echo '<br />' . _('The SQL that failed to get the branch details was') . ':<br />' . $sql;
				}
				include('includes/footer.inc');
				exit;
			}
			// add echo
			echo '<br />';
			$myrow = DB_fetch_array($result);

			$_SESSION['Items'.$identifier]->DeliverTo = $myrow['brname'];
			$_SESSION['Items'.$identifier]->DelAdd1 = $myrow['braddress1'];
			$_SESSION['Items'.$identifier]->ShipVia = $myrow['defaultshipvia'];
			$_SESSION['Items'.$identifier]->DeliverBlind = $myrow['deliverblind'];
			$_SESSION['Items'.$identifier]->SpecialInstructions = $myrow['specialinstructions'];
			$_SESSION['Items'.$identifier]->DeliveryDays = $myrow['estdeliverydays'];
			$_SESSION['Items'.$identifier]->TaxGroup = $myrow['taxgroupid'];
			$_SESSION['Items'.$identifier]->SalesPerson = $myrow['salesman'];

			if ($_SESSION['Items'.$identifier]->SpecialInstructions) {
				prnMsg($_SESSION['Items'.$identifier]->SpecialInstructions,'warn');
			}

			if ($_SESSION['CheckCreditLimits'] > 0 AND $AlreadyWarnedAboutCredit==false) {  /*Check credit limits is 1 for warn and 2 for prohibit sales */
				$_SESSION['Items'.$identifier]->CreditAvailable = GetCreditAvailable($_SESSION['Items'.$identifier]->DebtorNo,$db);

				if ($_SESSION['CheckCreditLimits']==1 AND $_SESSION['Items'.$identifier]->CreditAvailable <=0){
					prnMsg(_('The') . ' ' . $myrow['brname'] . ' ' . _('account is currently at or over their credit limit'),'warn');
					$AlreadyWarnedAboutCredit = true;
				} elseif ($_SESSION['CheckCreditLimits']==2 AND $_SESSION['Items'.$identifier]->CreditAvailable <=0){
					prnMsg(_('No more orders can be placed by') . ' ' . $myrow[0] . ' ' . _(' their account is currently at or over their credit limit'),'warn');
					$AlreadyWarnedAboutCredit = true;
					include('includes/footer.inc');
					exit;
				}
			}

		} else {
			prnMsg($myrow['brname'] . ' ' . _('Although the account is defined as the cash sale account for the location  the account is currently on hold. Please contact the credit control personnel to discuss'),'warn');
		}

	}
} // end if its a new sale to be set up ...

//==================================================================================================
if(isset($_POST['Payments']) && $_POST['Payments'] =="AddPayments"){
$Counterror = 0;
if(isset($_POST['MpesaPhoneNo']) && $_POST['MpesaPhoneNo'] !=""){
$ch = curl_init();   
$url= "https://berkley.co.ke/bpesa/";
curl_setopt($ch,CURLOPT_URL,$url.'posttosale.php?token=yukX9EvbJkQbC&PhoneNo='.trim($_POST['MpesaPhoneNo']));
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
$output=curl_exec($ch);
curl_close($ch);
$arr = json_decode(trim($output), TRUE);
if($arr['cash'] == 'error'){
$Counterror = 1;
prnMsg(_('An Error Occured! Please try Again'),'error');
}elseif($arr['cash'] == 0){
$Counterror = 1;
prnMsg(_('There is no transaction made with this number ('.$_POST['MpesaPhoneNo'].')'),'error');
}else{
$Counterror = 0;
$_POST['PaymentMethod'] = 5;
$_POST['AmountPayment'] = $_SESSION['Items'.$identifier]->MpesaAmt = $arr['cash'];
$_SESSION['Items'.$identifier]->MpesaTransID = $arr['TransID'];
$_SESSION['Items'.$identifier]->MpesaDate = $arr['TransTime'];
$_SESSION['Items'.$identifier]->MpesaNo = $arr['MSISDN'];
$_SESSION['Items'.$identifier]->MpesaFName = $arr['FirstName'];
$_SESSION['Items'.$identifier]->MpesaMName = $arr['MiddleName'];
$_SESSION['Items'.$identifier]->MpesaLName = $arr['LastName'];
$_SESSION['Items'.$identifier]->MpesaBal = $arr['OrgAccountBalance'];

}
}
if($Counterror ==0){
$PaymentMethodsResult = DB_query("SELECT paymentid, paymentname FROM paymentmethods WHERE paymentid='".$_POST['PaymentMethod']."'");
$MethodRow = DB_fetch_array($PaymentMethodsResult);
foreach ($_SESSION['Pay'.$identifier]->LineItems as $Items){
$IDS[$Items->LineNumber] = $Items->ID;
$lines[$Items->ID] = $Items->LineNumber;
}
if (in_array($_POST['PaymentMethod'], $IDS)) {
    $_SESSION['Pay'.$identifier]->update_pay_item($lines[$_POST['PaymentMethod']], filter_number_format($_POST['AmountPayment']));
}else{
$_SESSION['Pay'.$identifier]->add_to_pay($MethodRow['paymentid'],
										$MethodRow['paymentname'],
										   filter_number_format($_POST['AmountPayment']));
	}

}

}
//==================================================================================================

if (isset($_POST['CancelOrder']) && $_POST['CancelOrder'] =="CancelSale") {


	unset($_SESSION['Items'.$identifier]->LineItems);
	$_SESSION['Items'.$identifier]->ItemsOrdered = 0;
	unset($_SESSION['Items'.$identifier]);
	$_SESSION['Items'.$identifier] = new cart;

	echo '<br /><br />';
	prnMsg(_('This sale has been cancelled as requested'),'success');
	echo '<br /><br /><a href="' .htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">' . _('Start a new Counter Sale') . '</a>';
	include('includes/footer.inc');
	exit;

} else { /*Not cancelling the order */
	
	//echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/inventory.png" title="' . _('Counter Sales') . '" alt="" />' . ' ';
	//echo $_SESSION['Items'.$identifier]->CustomerName . ' ' . _('Counter Sale') . ' ' ._('from') . ' ' . $_SESSION['Items'.$identifier]->LocationName . ' ' . _('inventory') . ' (' . _('all amounts in') . ' ' . $_SESSION['Items'.$identifier]->DefaultCurrency . ')';
	//echo '</p>';
	
}

if (isset($_POST['Search']) or isset($_POST['Next']) or isset($_POST['Previous'])){

	if ($_POST['Keywords']!='' AND $_POST['StockCode']=='') {
		$msg = _('Item description has been used in search');
	} else if ($_POST['StockCode']!='' AND $_POST['Keywords']=='') {
		$msg = _('Item Code has been used in search');
	} else if ($_POST['Keywords']=='' AND $_POST['StockCode']=='') {
		$msg = _('Stock Category has been used in search');
	}
	if (isset($_POST['Keywords']) AND mb_strlen($_POST['Keywords'])>0) {
		//insert wildcard characters in spaces
		$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
		$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';

		if ($_POST['StockCat']=='All'){
			$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units,
						stockmaster.decimalplaces
					FROM stockmaster INNER JOIN stockcategory
					ON stockmaster.categoryid=stockcategory.categoryid
					WHERE (stockcategory.stocktype='F' OR stockcategory.stocktype='D' OR stockcategory.stocktype='L')
					AND stockmaster.mbflag <>'G'
					AND stockmaster.controlled <> 1
					AND stockmaster.description " . LIKE . " '" . $SearchString . "'
					AND stockmaster.discontinued=0
					ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units,
						stockmaster.decimalplaces
					FROM stockmaster INNER JOIN stockcategory
					ON  stockmaster.categoryid=stockcategory.categoryid
					WHERE (stockcategory.stocktype='F' OR stockcategory.stocktype='D' OR stockcategory.stocktype='L')
					AND stockmaster.mbflag <>'G'
					AND stockmaster.controlled <> 1
					AND stockmaster.discontinued=0
					AND stockmaster.description " . LIKE . " '" . $SearchString . "'
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					ORDER BY stockmaster.stockid";
		}

	} else if (mb_strlen($_POST['StockCode'])>0){

		$_POST['StockCode'] = mb_strtoupper($_POST['StockCode']);
		$SearchString = '%' . $_POST['StockCode'] . '%';

		if ($_POST['StockCat']=='All'){
			$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units,
						stockmaster.decimalplaces
					FROM stockmaster INNER JOIN stockcategory
					  ON stockmaster.categoryid=stockcategory.categoryid
					WHERE (stockcategory.stocktype='F' OR stockcategory.stocktype='D' OR stockcategory.stocktype='L')
					AND stockmaster.stockid " . LIKE . " '" . $SearchString . "'
					AND stockmaster.mbflag <>'G'
					AND stockmaster.controlled <> 1
					AND stockmaster.discontinued=0
					ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units,
						stockmaster.decimalplaces
					FROM stockmaster INNER JOIN stockcategory
					ON stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D' OR stockcategory.stocktype='L')
					AND stockmaster.stockid " . LIKE . " '" . $SearchString . "'
					AND stockmaster.mbflag <>'G'
					AND stockmaster.controlled <> 1
					AND stockmaster.discontinued=0
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					ORDER BY stockmaster.stockid";
		}

	} else {
		if ($_POST['StockCat']=='All'){
			$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units,
						stockmaster.decimalplaces
					FROM stockmaster INNER JOIN stockcategory
					ON  stockmaster.categoryid=stockcategory.categoryid
					WHERE (stockcategory.stocktype='F' OR stockcategory.stocktype='D' OR stockcategory.stocktype='L')
					AND stockmaster.mbflag <>'G'
					AND stockmaster.controlled <> 1
					AND stockmaster.discontinued=0
					ORDER BY stockmaster.stockid";
        	} else {
			$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units,
						stockmaster.decimalplaces
					FROM stockmaster INNER JOIN stockcategory
					ON stockmaster.categoryid=stockcategory.categoryid
					WHERE (stockcategory.stocktype='F' OR stockcategory.stocktype='D' OR stockcategory.stocktype='L')
					AND stockmaster.mbflag <>'G'
					AND stockmaster.controlled <> 1
					AND stockmaster.discontinued=0
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					ORDER BY stockmaster.stockid";
		  }
	}

	if (isset($_POST['Next'])) {
		$Offset = $_POST['NextList'];
	}
	if (isset($_POST['Previous'])) {
		$Offset = $_POST['PreviousList'];
	}
	if (!isset($Offset) OR $Offset < 0) {
		$Offset = 0;
	}
	$SQL = $SQL . ' LIMIT ' . $_SESSION['DefaultDisplayRecordsMax'].' OFFSET ' . strval($_SESSION['DefaultDisplayRecordsMax']*$Offset);

	$ErrMsg = _('There is a problem selecting the part records to display because');
	$DbgMsg = _('The SQL used to get the part selection was');
	$SearchResult = DB_query($SQL,$ErrMsg, $DbgMsg);

	if (DB_num_rows($SearchResult)==0 ){
		prnMsg (_('There are no products available meeting the criteria specified'),'info');
	}
	if (DB_num_rows($SearchResult)==1){
		$myrow=DB_fetch_array($SearchResult);
		$NewItem = $myrow['stockid'];
		DB_data_seek($SearchResult,0);
	}
	if (DB_num_rows($SearchResult)< $_SESSION['DisplayRecordsMax']){
		$Offset=0;
	}

} //end of if search


/* Always do the stuff below */

echo '<form name="reculculateform" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?identifier='.$identifier . '" id="SelectParts" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

//Get The exchange rate used for GPPercent calculations on adding or amending items
if ($_SESSION['Items'.$identifier]->DefaultCurrency != $_SESSION['CompanyRecord']['currencydefault']){
	$ExRateResult = DB_query("SELECT rate FROM currencies WHERE currabrev='" . $_SESSION['Items'.$identifier]->DefaultCurrency . "'");
	if (DB_num_rows($ExRateResult)>0){
		$ExRateRow = DB_fetch_row($ExRateResult);
		$ExRate = $ExRateRow[0];
	} else {
		$ExRate =1;
	}
} else {
	$ExRate = 1;
}


if(isset($_POST['barcode']) && $_POST['barcode'] !='' && !isset($_POST['ItemSelection'])){

$SQL = "SELECT stockmaster.stockid
					FROM stockmaster 
					WHERE stockmaster.barcode='".$_POST['barcode']."'";
$result=DB_query($SQL);
$row=DB_fetch_array($result);

$DefaultDeliveryDate = DateAdd(Date($_SESSION['DefaultDateFormat']),'d',$_SESSION['Items'.$identifier]->DeliveryDays);
$_POST['part_1'] = $row['stockid'];
$_POST['ItemDue_1'] = $DefaultDeliveryDate;
$_POST['qty_1'] = 1;
$_POST['QuickEntry'] = 'Quick Entry';

}
/*Process Quick Entry */
/* If enter is pressed on the quick entry screen, the default button may be Recalculate */
 if (isset($_POST['SelectingOrderItems'])
		OR isset($_POST['QuickEntry'])
		OR isset($_POST['Recalculate'])){

	/* get the item details from the database and hold them in the cart object */

	/*Discount can only be set later on  -- after quick entry -- so default discount to 0 in the first place */
	$Discount = 0;
	$AlreadyWarnedAboutCredit = false;
	$i=1;
	while ($i<=$_SESSION['QuickEntries']
			AND isset($_POST['part_' . $i])
			AND $_POST['part_' . $i]!='') {

		$QuickEntryCode = 'part_' . $i;
		$QuickEntryQty = 'qty_' . $i;
		$QuickEntryPOLine = 'poline_' . $i;
		$QuickEntryItemDue = 'ItemDue_' . $i;

		$i++;

		if (isset($_POST[$QuickEntryCode])) {
			$NewItem = mb_strtoupper($_POST[$QuickEntryCode]);
		}
		if (isset($_POST[$QuickEntryQty])) {
			$NewItemQty = filter_number_format($_POST[$QuickEntryQty]);
		}
		if (isset($_POST[$QuickEntryItemDue])) {
			$NewItemDue = $_POST[$QuickEntryItemDue];
		} else {
			$NewItemDue = DateAdd (Date($_SESSION['DefaultDateFormat']),'d', $_SESSION['Items'.$identifier]->DeliveryDays);
		}
		if (isset($_POST[$QuickEntryPOLine])) {
			$NewPOLine = $_POST[$QuickEntryPOLine];
		} else {
			$NewPOLine = 0;
		}

		if (!isset($NewItem)){
			unset($NewItem);
			break;	/* break out of the loop if nothing in the quick entry fields*/
		}

		if(!Is_Date($NewItemDue)) {
			prnMsg(_('An invalid date entry was made for ') . ' ' . $NewItem . ' ' . _('The date entry') . ' ' . $NewItemDue . ' ' . _('must be in the format') . ' ' . $_SESSION['DefaultDateFormat'],'warn');
			//Attempt to default the due date to something sensible?
			$NewItemDue = DateAdd (Date($_SESSION['DefaultDateFormat']),'d', $_SESSION['Items'.$identifier]->DeliveryDays);
		}
		/*Now figure out if the item is a kit set - the field MBFlag='K'*/
		$sql = "SELECT stockmaster.mbflag,
						stockmaster.controlled
				FROM stockmaster
				WHERE stockmaster.stockid='". $NewItem ."'";

		$ErrMsg = _('Could not determine if the part being ordered was a kitset or not because');
		$DbgMsg = _('The sql that was used to determine if the part being ordered was a kitset or not was ');
		$KitResult = DB_query($sql,$ErrMsg,$DbgMsg);


		if (DB_num_rows($KitResult)==0){
			prnMsg( _('The item code') . ' ' . $NewItem . ' ' . _('could not be retrieved from the database and has not been added to the order'),'warn');
		} elseif ($myrow=DB_fetch_array($KitResult)){
			if ($myrow['mbflag']=='K'){	/*It is a kit set item */
				$sql = "SELECT bom.component,
							bom.quantity
						FROM bom
						WHERE bom.parent='" . $NewItem . "'
                        AND bom.effectiveafter <= '" . date('Y-m-d') . "'
                        AND bom.effectiveto > '" . date('Y-m-d') . "'";

				$ErrMsg =  _('Could not retrieve kitset components from the database because') . ' ';
				$KitResult = DB_query($sql,$ErrMsg,$DbgMsg);

				$ParentQty = $NewItemQty;
				while ($KitParts = DB_fetch_array($KitResult)) {
					$NewItem = $KitParts['component'];
					$NewItemQty = $KitParts['quantity'] * $ParentQty;
					$NewPOLine = 0;
					include('includes/SelectOrderItems_IntoCart.inc');
					$_SESSION['Items'.$identifier]->GetTaxes(($_SESSION['Items'.$identifier]->LineCounter - 1));
				}

			} else if ($myrow['mbflag']=='G'){
				prnMsg(_('Phantom assemblies cannot be sold, these items exist only as bills of materials used in other manufactured items. The following item has not been added to the order:') . ' ' . $NewItem, 'warn');
			//} else if ($myrow['controlled']==1){
			//	prnMsg(_('The system does not currently cater for counter sales of lot controlled or serialised items'),'warn');
			} else if ($NewItemQty<=0 && $myrow['controlled']==0) {
				prnMsg(_('Only items entered with a positive quantity can be added to the sale'),'warn');
			} else { /*Its not a kit set item*/
				include('includes/SelectOrderItems_IntoCart.inc');				
				$_SESSION['Items'.$identifier]->GetTaxes(($_SESSION['Items'.$identifier]->LineCounter - 1));
			}
		}
	 }
	 unset($NewItem);
 } /* end of if quick entry */

 /*Now do non-quick entry delete/edits/adds */

if ((isset($_SESSION['Items'.$identifier])) OR isset($NewItem)) {

	if (isset($_GET['Delete'])){
		$_SESSION['Items'.$identifier]->remove_from_cart($_GET['Delete']);  /*Don't do any DB updates*/
	}
	if (isset($_GET['DelPay'])){
		$_SESSION['Pay'.$identifier]->remove_from_pay($_GET['DelPay']);  /*Don't do any DB updates*/
	}
	$AlreadyWarnedAboutCredit = false;
	foreach ($_SESSION['Items'.$identifier]->LineItems as $OrderLine) {

		if (isset($_POST['Quantity_' . $OrderLine->LineNumber])){

			$Quantity = round(filter_number_format($_POST['Quantity_' . $OrderLine->LineNumber]),$OrderLine->DecimalPlaces);

				if (ABS($OrderLine->Price - filter_number_format($_POST['Price_' . $OrderLine->LineNumber]))>0.01){
					/*There is a new price being input for the line item */

					$Price = filter_number_format($_POST['Price_' . $OrderLine->LineNumber]);
					$_POST['GPPercent_' . $OrderLine->LineNumber] = (($Price*(1-(filter_number_format($_POST['Discount_' . $OrderLine->LineNumber])/100))) - $OrderLine->StandardCost*$ExRate)/($Price *(1-filter_number_format($_POST['Discount_' . $OrderLine->LineNumber]))/100);

				} elseif (ABS($OrderLine->GPPercent - filter_number_format($_POST['GPPercent_' . $OrderLine->LineNumber]))>=0.01) {
					/* A GP % has been input so need to do a recalculation of the price at this new GP Percentage */

					prnMsg(_('Recalculated the price from the GP % entered - the GP % was') . ' ' . $OrderLine->GPPercent . '  the new GP % is ' . filter_number_format($_POST['GPPercent_' . $OrderLine->LineNumber]),'info');

					$Price = ($OrderLine->StandardCost*$ExRate)/(1 -((filter_number_format($_POST['GPPercent_' . $OrderLine->LineNumber]) + filter_number_format($_POST['Discount_' . $OrderLine->LineNumber]))/100));
				} else {
					$Price = filter_number_format($_POST['Price_' . $OrderLine->LineNumber]);
				}
				$DiscountPercentage = filter_number_format($_POST['Discount_' . $OrderLine->LineNumber]);
				if ($_SESSION['AllowOrderLineItemNarrative'] == 1) {
					$Narrative = $_POST['Narrative_' . $OrderLine->LineNumber];
				} else {
					$Narrative = '';
				}

				if (!isset($OrderLine->DiscountPercent)) {
					$OrderLine->DiscountPercent = 0;
				}

			if ($Quantity<0 OR $Price < 0 OR $DiscountPercentage >100 OR $DiscountPercentage <0){
				prnMsg(_('The item could not be updated because you are attempting to set the quantity ordered to less than 0 or the price less than 0 or the discount more than 100% or less than 0%'),'warn');
			} else if ($OrderLine->Quantity !=$Quantity
						OR $OrderLine->Price != $Price
						OR abs($OrderLine->DiscountPercent -$DiscountPercentage/100) >0.001
						OR $OrderLine->Narrative != $Narrative
						OR $OrderLine->ItemDue != $_POST['ItemDue_' . $OrderLine->LineNumber]
						OR $OrderLine->POLine != $_POST['POLine_' . $OrderLine->LineNumber]) {

				$_SESSION['Items'.$identifier]->update_cart_item($OrderLine->LineNumber,
																$Quantity,
																$Price,
																$DiscountPercentage/100,
																$Narrative,
																'Yes', /*Update DB */
																$_POST['ItemDue_' . $OrderLine->LineNumber],
																$_POST['POLine_' . $OrderLine->LineNumber],
																filter_number_format($_POST['GPPercent_' . $OrderLine->LineNumber]),
																$identifier);
			}
		} //page not called from itself - POST variables not set
	}
}

if (isset($_POST['Recalculate'])) {
	foreach ($_SESSION['Items'.$identifier]->LineItems as $OrderLine) {
	if ($_SESSION['ProhibitSaleBelowCost']==1){
		$NFound = false;
			$SQL = "SELECT stockmaster.materialcost, description
		 			FROM stockmaster
					WHERE stockmaster.stockid='" . $OrderLine->StockID . "'";

			$ErrMsg = _('Could not retrieve the quantity left at the location once this order is invoiced (for the purposes of checking that stock will not go negative because)');
			$Results = DB_query($SQL,$ErrMsg);
			$CheckNRow = DB_fetch_array($Results);
				if ($CheckNRow['materialcost'] > $OrderLine->Price){
					prnMsg( _('Invoicing the selected order would result in a loss. The system parameters are set to prohibit sale below cost price from occurring. This invoice cannot be created until the price is corrected.'),'error',$OrderLine->StockID . ' ' . $CheckNRow['description'] . ' - ' . _('Sale below Cost Prohibited'));
					$NFound = true;
				}elseif($CheckNRow['materialcost'] == $OrderLine->Price){
				prnMsg( _('Price is same as Cost.'),'error',$OrderLine->StockID . ' ' . $CheckNRow['description'] . ' - ' . _('Sale made at Cost Price'));
				}

	}//end of testing for below cost
	
		$NewItem=$OrderLine->StockID;
		$sql = "SELECT stockmaster.mbflag,
						stockmaster.controlled
				FROM stockmaster
				WHERE stockmaster.stockid='". $OrderLine->StockID."'";

		$ErrMsg = _('Could not determine if the part being ordered was a kitset or not because');
		$DbgMsg = _('The sql that was used to determine if the part being ordered was a kitset or not was ');
		$KitResult = DB_query($sql,$ErrMsg,$DbgMsg);
		if ($myrow=DB_fetch_array($KitResult)){
			if ($myrow['mbflag']=='K'){	/*It is a kit set item */
				$sql = "SELECT bom.component,
								bom.quantity
							FROM bom
							WHERE bom.parent='" . $OrderLine->StockID. "'
                            AND bom.effectiveafter <= '" . date('Y-m-d') . "'
                            AND bom.effectiveto > '" . date('Y-m-d') . "'";

				$ErrMsg = _('Could not retrieve kitset components from the database because');
				$KitResult = DB_query($sql,$ErrMsg);

				$ParentQty = $NewItemQty;
				while ($KitParts = DB_fetch_array($KitResult)){
					$NewItem = $KitParts['component'];
					$NewItemQty = $KitParts['quantity'] * $ParentQty;
					$NewPOLine = 0;
					$NewItemDue = date($_SESSION['DefaultDateFormat']);
					$_SESSION['Items'.$identifier]->GetTaxes($OrderLine->LineNumber);
				}

			} else { /*Its not a kit set item*/
				$NewItemDue = date($_SESSION['DefaultDateFormat']);
				$NewPOLine = 0;
				$_SESSION['Items'.$identifier]->GetTaxes($OrderLine->LineNumber);
			}
		}
		unset($NewItem);
	} /* end of if its a new item */
}

if (isset($NewItem)){
/* get the item details from the database and hold them in the cart object make the quantity 1 by default then add it to the cart
Now figure out if the item is a kit set - the field MBFlag='K'
* controlled items and ghost/phantom items cannot be selected because the SQL to show items to select doesn't show 'em
* */
	$AlreadyWarnedAboutCredit = false;

	$sql = "SELECT stockmaster.mbflag,
				stockmaster.taxcatid
			FROM stockmaster
			WHERE stockmaster.stockid='". $NewItem ."'";

	$ErrMsg =  _('Could not determine if the part being ordered was a kitset or not because');

	$KitResult = DB_query($sql,$ErrMsg);

	$NewItemQty = 1; /*By Default */
	$Discount = 0; /*By default - can change later or discount category override */

	if ($myrow=DB_fetch_array($KitResult)){
	   	if ($myrow['mbflag']=='K'){	/*It is a kit set item */
			$sql = "SELECT bom.component,
						bom.quantity
					FROM bom
					WHERE bom.parent='" . $NewItem . "'
                    AND bom.effectiveafter <= '" . date('Y-m-d') . "'
                    AND bom.effectiveto > '" . date('Y-m-d') . "'";

			$ErrMsg = _('Could not retrieve kitset components from the database because');
			$KitResult = DB_query($sql,$ErrMsg);

			$ParentQty = $NewItemQty;
			while ($KitParts = DB_fetch_array($KitResult)){
				$NewItem = $KitParts['component'];
				$NewItemQty = $KitParts['quantity'] * $ParentQty;
				$NewPOLine = 0;
				$NewItemDue = date($_SESSION['DefaultDateFormat']);
				include('includes/SelectOrderItems_IntoCart.inc');
				$_SESSION['Items'.$identifier]->GetTaxes(($_SESSION['Items'.$identifier]->LineCounter - 1));
			}

		} else { /*Its not a kit set item*/
			$NewItemDue = date($_SESSION['DefaultDateFormat']);
			$NewPOLine = 0;

			include('includes/SelectOrderItems_IntoCart.inc');
			$_SESSION['Items'.$identifier]->GetTaxes(($_SESSION['Items'.$identifier]->LineCounter - 1));
		}

	} /* end of if its a new item */

} /*end of if its a new item */

if (isset($NewItemArray) AND isset($_POST['SelectingOrderItems'])){
/* get the item details from the database and hold them in the cart object make the quantity 1 by default then add it to the cart */
/*Now figure out if the item is a kit set - the field MBFlag='K'*/
	$AlreadyWarnedAboutCredit = false;

	foreach($NewItemArray as $NewItem => $NewItemQty) {
		if($NewItemQty > 0)	{
			$sql = "SELECT stockmaster.mbflag
					FROM stockmaster
					WHERE stockmaster.stockid='". $NewItem ."'";

			$ErrMsg =  _('Could not determine if the part being ordered was a kitset or not because');

			$KitResult = DB_query($sql,$ErrMsg);

			//$NewItemQty = 1; /*By Default */
			$Discount = 0; /*By default - can change later or discount category override */

			if ($myrow=DB_fetch_array($KitResult)){
				if ($myrow['mbflag']=='K'){	/*It is a kit set item */
					$sql = "SELECT bom.component,
	        					bom.quantity
		          			FROM bom
							WHERE bom.parent='" . $NewItem . "'
                            AND bom.effectiveafter <= '" . date('Y-m-d') . "'
                            AND bom.effectiveto > '" . date('Y-m-d') . "'";

					$ErrMsg = _('Could not retrieve kitset components from the database because');
					$KitResult = DB_query($sql,$ErrMsg);

					$ParentQty = $NewItemQty;
					while ($KitParts = DB_fetch_array($KitResult)){
						$NewItem = $KitParts['component'];
						$NewItemQty = $KitParts['quantity'] * $ParentQty;
						$NewItemDue = date($_SESSION['DefaultDateFormat']);
						$NewPOLine = 0;
						include('includes/SelectOrderItems_IntoCart.inc');
						$_SESSION['Items'.$identifier]->GetTaxes(($_SESSION['Items'.$identifier]->LineCounter - 1));
					}

				} else { /*Its not a kit set item*/
					$NewItemDue = date($_SESSION['DefaultDateFormat']);
					$NewPOLine = 0;
					include('includes/SelectOrderItems_IntoCart.inc');
					$_SESSION['Items'.$identifier]->GetTaxes(($_SESSION['Items'.$identifier]->LineCounter - 1));
				}
			} /* end of if its a new item */
		} /*end of if its a new item */
	}
}


/* Now Run through each line of the order again to work out the appropriate discount from the discount matrix */
$DiscCatsDone = array();
foreach ($_SESSION['Items'.$identifier]->LineItems as $OrderLine) {

	if ($OrderLine->DiscCat !='' AND ! in_array($OrderLine->DiscCat,$DiscCatsDone)){
		$DiscCatsDone[]=$OrderLine->DiscCat;
		$QuantityOfDiscCat = 0;

		foreach ($_SESSION['Items'.$identifier]->LineItems as $OrderLine_2) {
			/* add up total quantity of all lines of this DiscCat */
			if ($OrderLine_2->DiscCat==$OrderLine->DiscCat){
				$QuantityOfDiscCat += $OrderLine_2->Quantity;
			}
		}
		$result = DB_query("SELECT MAX(discountrate) AS discount
							FROM discountmatrix
							WHERE salestype='" .  $_SESSION['Items'.$identifier]->DefaultSalesType . "'
							AND discountcategory ='" . $OrderLine->DiscCat . "'
							AND quantitybreak <= '" . $QuantityOfDiscCat ."'");
		$myrow = DB_fetch_row($result);
		if ($myrow[0]==NULL){
			$DiscountMatrixRate = 0;
		} else {
			$DiscountMatrixRate = $myrow[0];
		}
		if ($myrow[0]!=0){ /* need to update the lines affected */
			foreach ($_SESSION['Items'.$identifier]->LineItems as $OrderLine_2) {
				if ($OrderLine_2->DiscCat==$OrderLine->DiscCat){
					$_SESSION['Items'.$identifier]->LineItems[$OrderLine_2->LineNumber]->DiscountPercent = $DiscountMatrixRate;
					$_SESSION['Items'.$identifier]->LineItems[$OrderLine_2->LineNumber]->GPPercent = (($_SESSION['Items'.$identifier]->LineItems[$OrderLine_2->LineNumber]->Price*(1-$DiscountMatrixRate)) - $_SESSION['Items'.$identifier]->LineItems[$OrderLine_2->LineNumber]->StandardCost*$ExRate)/($_SESSION['Items'.$identifier]->LineItems[$OrderLine_2->LineNumber]->Price *(1-$DiscountMatrixRate)/100);
				}
			}
		}
	}
} /* end of discount matrix lookup code */



/* **********************************
 * Invoice Processing Here
 * **********************************
 * */
 if (isset($_POST['ProcessReturn']) AND $_POST['ProcessReturn'] != ''){

	$InputError = false; //always assume the best
	//but check for the worst
	if ($_SESSION['Items' . $identifier]->LineCounter == 0){
		prnMsg(_('There are no lines on this return. Please enter lines to return first'),'error');
		$InputError = true;
	}
	if (abs(filter_number_format($_POST['AmountPaid']) -round($_SESSION['Items' . $identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items' . $identifier]->CurrDecimalPlaces))>=0.01) {
		prnMsg(_('The amount entered as payment to the customer does not equal the amount of the return. Please correct amount and re-enter'),'error');
		$InputError = true;
	}

	if ($InputError == false) { //all good so let's get on with the processing

	/* Now Get the area where the sale is to from the branches table */

		$SQL = "SELECT 	area,
						defaultshipvia
				FROM custbranch
				WHERE custbranch.debtorno ='". $_SESSION['Items' . $identifier]->DebtorNo . "'
				AND custbranch.branchcode = '" . $_SESSION['Items' . $identifier]->Branch . "'";

		$ErrMsg = _('We were unable to load the area where the sale is to from the custbranch table');
		$Result = DB_query($SQL, $ErrMsg);
		$myrow = DB_fetch_row($Result);
		$Area = $myrow[0];
		$DefaultShipVia = $myrow[1];
		DB_free_result($Result);

	/*company record read in on login with info on GL Links and debtors GL account*/

		if ($_SESSION['CompanyRecord']==0){
			/*The company data and preferences could not be retrieved for some reason */
			prnMsg( _('The company information and preferences could not be retrieved. See your system administrator'), 'error');
			include('includes/footer.inc');
			exit;
		}

	// *************************************************************************
	//   S T A R T   O F   C R E D I T  N O T E   S Q L   P R O C E S S I N G
	// *************************************************************************
		$result = DB_Txn_Begin();

	/*Now Get the next invoice number - GetNextTransNo() function in SQL_CommonFunctions
	 * GetPeriod() in includes/DateFunctions.inc */

		$CreditNoteNo = GetNextTransNo(11, $db);
		$PeriodNo = GetPeriod(Date($_SESSION['DefaultDateFormat']), $db);

		$ReturnDate = Date('Y-m-d');

	/*Now insert the DebtorTrans */

		$SQL = "INSERT INTO debtortrans (transno,
										type,
										debtorno,
										branchcode,
										trandate,
										inputdate,
										prd,
										reference,
										tpe,
										ovamount,
										ovgst,
										rate,
										invtext,
										shipvia,
										alloc,
										salesperson )
			VALUES ('". $CreditNoteNo . "',
					11,
					'" . $_SESSION['Items' . $identifier]->DebtorNo . "',
					'" . $_SESSION['Items' . $identifier]->Branch . "',
					'" . $ReturnDate . "',
					'" . date('Y-m-d H-i-s') . "',
					'" . $PeriodNo . "',
					'" . $_SESSION['Items' . $identifier]->CustRef  . "',
					'" . $_SESSION['Items' . $identifier]->DefaultSalesType . "',
					'" . -$_SESSION['Items' . $identifier]->total . "',
					'" . filter_number_format(-$_POST['TaxTotal']) . "',
					'" . $ExRate . "',
					'" . $_SESSION['Items' . $identifier]->Comments . "',
					'" . $_SESSION['Items' . $identifier]->ShipVia . "',
					'" . (-$_SESSION['Items' . $identifier]->total - filter_number_format($_POST['TaxTotal'])) . "',
					'" . $_SESSION['Items' . $identifier]->SalesPerson . "' )";

		$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor transaction record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the debtor transaction record was used');
	 	$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		$DebtorTransID = DB_Last_Insert_ID($db,'debtortrans','id');

	/* Insert the tax totals for each tax authority where tax was charged on the invoice */
		foreach ($_SESSION['Items' . $identifier]->TaxTotals AS $TaxAuthID => $TaxAmount) {

			$SQL = "INSERT INTO debtortranstaxes (debtortransid,
													taxauthid,
													taxamount)
										VALUES ('" . $DebtorTransID . "',
											'" . $TaxAuthID . "',
											'" . -$TaxAmount/$ExRate . "')";

			$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor transaction taxes records could not be inserted because');
			$DbgMsg = _('The following SQL to insert the debtor transaction taxes record was used');
	 		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		}

		//Loop around each item on the sale and process each in turn
		foreach ($_SESSION['Items' . $identifier]->LineItems as $ReturnItemLine) {
			 /* Update location stock records if not a dummy stock item
			 need the MBFlag later too so save it to $MBFlag */
			$Result = DB_query("SELECT mbflag FROM stockmaster WHERE stockid = '" . $ReturnItemLine->StockID . "'");
			$myrow = DB_fetch_row($Result);
			$MBFlag = $myrow[0];
			if ($MBFlag=='B' OR $MBFlag=='M') {
				$Assembly = False;

				/* Need to get the current location quantity
				will need it later for the stock movement */
				$SQL="SELECT locstock.quantity
								FROM locstock
								WHERE locstock.stockid='" . $ReturnItemLine->StockID . "'
								AND loccode= '" . $_SESSION['Items' . $identifier]->Location . "'";
				$ErrMsg = _('WARNING') . ': ' . _('Could not retrieve current location stock');
				$Result = DB_query($SQL, $ErrMsg);

				if (DB_num_rows($Result)==1){
					$LocQtyRow = DB_fetch_row($Result);
					$QtyOnHandPrior = $LocQtyRow[0];
				} else {
					/* There must be some error this should never happen */
					$QtyOnHandPrior = 0;
				}

				$SQL = "UPDATE locstock
							SET quantity = locstock.quantity + " . $ReturnItemLine->Quantity . "
						WHERE locstock.stockid = '" . $ReturnItemLine->StockID . "'
						AND loccode = '" . $_SESSION['Items' . $identifier]->Location . "'";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Location stock record could not be updated because');
				$DbgMsg = _('The following SQL to update the location stock record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

			} else if ($MBFlag=='A'){ /* its an assembly */
				/*Need to get the BOM for this part and make
				stock moves for the components then update the Location stock balances */
				$Assembly=True;
				$StandardCost =0; /*To start with - accumulate the cost of the comoponents for use in journals later on */
				$SQL = "SELECT bom.component,
						bom.quantity,
						stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost AS standard
						FROM bom,
							stockmaster
						WHERE bom.component=stockmaster.stockid
						AND bom.parent='" . $ReturnItemLine->StockID . "'
                        AND bom.effectiveafter <= '" . date('Y-m-d') . "'
                        AND bom.effectiveto > '" . date('Y-m-d') . "'";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Could not retrieve assembly components from the database for'). ' '. $ReturnItemLine->StockID . _('because').' ';
				$DbgMsg = _('The SQL that failed was');
				$AssResult = DB_query($SQL,$ErrMsg,$DbgMsg,true);

				while ($AssParts = DB_fetch_array($AssResult,$db)){

					$StandardCost += ($AssParts['standard'] * $AssParts['quantity']) ;
					/* Need to get the current location quantity
					will need it later for the stock movement */
					$SQL="SELECT locstock.quantity
									FROM locstock
									WHERE locstock.stockid='" . $AssParts['component'] . "'
									AND loccode= '" . $_SESSION['Items' . $identifier]->Location . "'";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Can not retrieve assembly components location stock quantities because ');
					$DbgMsg = _('The SQL that failed was');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
					if (DB_num_rows($Result)==1){
						$LocQtyRow = DB_fetch_row($Result);
						$QtyOnHandPrior = $LocQtyRow[0];
					} else {
						/*There must be some error this should never happen */
						$QtyOnHandPrior = 0;
					}
					if (empty($AssParts['standard'])) {
						$AssParts['standard']=0;
					}
					$SQL = "INSERT INTO stockmoves (stockid,
													type,
													transno,
													loccode,
													trandate,
													userid,
													debtorno,
													branchcode,
													prd,
													reference,
													qty,
													standardcost,
													show_on_inv_crds,
													newqoh
						) VALUES (
													'" . $AssParts['component'] . "',
													 11,
													'" . $CreditNoteNo . "',
													'" . $_SESSION['Items' . $identifier]->Location . "',
													'" . $ReturnDate . "',
													'" . $_SESSION['UserID'] . "',
													'" . $_SESSION['Items' . $identifier]->DebtorNo . "',
													'" . $_SESSION['Items' . $identifier]->Branch . "',
													'" . $PeriodNo . "',
													'" . _('Assembly') . ': ' . $ReturnItemLine->StockID . "',
													'" . $AssParts['quantity'] * $ReturnItemLine->Quantity . "',
													'" . $AssParts['standard'] . "',
													0,
													newqoh + " . ($AssParts['quantity'] * $ReturnItemLine->Quantity) . " )";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records for the assembly components of'). ' '. $ReturnItemLine->StockID . ' ' . _('could not be inserted because');
					$DbgMsg = _('The following SQL to insert the assembly components stock movement records was used');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);


					$SQL = "UPDATE locstock
							SET quantity = locstock.quantity + " . ($AssParts['quantity'] * $ReturnItemLine->Quantity) . "
							WHERE locstock.stockid = '" . $AssParts['component'] . "'
							AND loccode = '" . $_SESSION['Items' . $identifier]->Location . "'";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Location stock record could not be updated for an assembly component because');
					$DbgMsg = _('The following SQL to update the locations stock record for the component was used');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
				} /* end of assembly explosion and updates */

				/*Update the cart with the recalculated standard cost from the explosion of the assembly's components*/
				$_SESSION['Items' . $identifier]->LineItems[$ReturnItemLine->LineNumber]->StandardCost = $StandardCost;
				$ReturnItemLine->StandardCost = $StandardCost;
			} /* end of its an assembly */

			// Insert stock movements - with unit cost
			$LocalCurrencyPrice = ($ReturnItemLine->Price / $ExRate);

			if (empty($ReturnItemLine->StandardCost)) {
				$ReturnItemLine->StandardCost=0;
			}
			if ($MBFlag=='B' OR $MBFlag=='M'){
				$SQL = "INSERT INTO stockmoves (stockid,
												type,
												transno,
												loccode,
												trandate,
												userid,
												debtorno,
												branchcode,
												price,
												prd,
												reference,
												qty,
												discountpercent,
												standardcost,
												newqoh,
												narrative )
						VALUES ('" . $ReturnItemLine->StockID . "',
								11,
								'" . $CreditNoteNo . "',
								'" . $_SESSION['Items' . $identifier]->Location . "',
								'" . $ReturnDate . "',
								'" . $_SESSION['UserID'] . "',
								'" . $_SESSION['Items' . $identifier]->DebtorNo . "',
								'" . $_SESSION['Items' . $identifier]->Branch . "',
								'" . $LocalCurrencyPrice . "',
								'" . $PeriodNo . "',
								'" . $OrderNo . "',
								'" . $ReturnItemLine->Quantity . "',
								'" . $ReturnItemLine->DiscountPercent . "',
								'" . $ReturnItemLine->StandardCost . "',
								'" . ($QtyOnHandPrior + $ReturnItemLine->Quantity) . "',
								'" . $ReturnItemLine->Narrative . "' )";
			} else {
			// its an assembly or dummy and assemblies/dummies always have nil stock (by definition they are made up at the time of dispatch  so new qty on hand will be nil
				if (empty($ReturnItemLine->StandardCost)) {
					$ReturnItemLine->StandardCost = 0;
				}
				$SQL = "INSERT INTO stockmoves (stockid,
												type,
												transno,
												loccode,
												trandate,
												userid,
												debtorno,
												branchcode,
												price,
												prd,
												qty,
												discountpercent,
												standardcost,
												narrative )
						VALUES ('" . $ReturnItemLine->StockID . "',
								'11',
								'" . $CreditNoteNo . "',
								'" . $_SESSION['Items' . $identifier]->Location . "',
								'" . $ReturnDate . "',
								'" . $_SESSION['UserID'] . "',
								'" . $_SESSION['Items' . $identifier]->DebtorNo . "',
								'" . $_SESSION['Items' . $identifier]->Branch . "',
								'" . $LocalCurrencyPrice . "',
								'" . $PeriodNo . "',
								'" . $ReturnItemLine->Quantity . "',
								'" . $ReturnItemLine->DiscountPercent . "',
								'" . $ReturnItemLine->StandardCost . "',
								'" . $ReturnItemLine->Narrative . "')";
			}

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records could not be inserted because');
			$DbgMsg = _('The following SQL to insert the stock movement records was used');
			$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		/*Get the ID of the StockMove... */
			$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');

		/*Insert the taxes that applied to this line */
			foreach ($ReturnItemLine->Taxes as $Tax) {

				$SQL = "INSERT INTO stockmovestaxes (stkmoveno,
														taxauthid,
														taxrate,
														taxcalculationorder,
														taxontax)
						VALUES ('" . $StkMoveNo . "',
								'" . $Tax->TaxAuthID . "',
								'" . $Tax->TaxRate . "',
								'" . $Tax->TaxCalculationOrder . "',
								'" . $Tax->TaxOnTax . "')";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Taxes and rates applicable to this invoice line item could not be inserted because');
				$DbgMsg = _('The following SQL to insert the stock movement tax detail records was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
			} //end for each tax for the line


		/*Insert Sales Analysis records */
			$SalesValue = 0;
			if ($ExRate>0){
				$SalesValue = $ReturnItemLine->Price * $ReturnItemLine->Quantity / $ExRate;
			}

			$SQL="SELECT COUNT(*),
						salesanalysis.stockid,
						salesanalysis.stkcategory,
						salesanalysis.cust,
						salesanalysis.custbranch,
						salesanalysis.area,
						salesanalysis.periodno,
						salesanalysis.typeabbrev,
						salesanalysis.salesperson
					FROM salesanalysis,
						custbranch,
						stockmaster
					WHERE salesanalysis.stkcategory=stockmaster.categoryid
					AND salesanalysis.stockid=stockmaster.stockid
					AND salesanalysis.cust=custbranch.debtorno
					AND salesanalysis.custbranch=custbranch.branchcode
					AND salesanalysis.area=custbranch.area
					AND salesanalysis.salesperson='" . $_SESSION['Items' . $identifier]->SalesPerson . "'
					AND salesanalysis.typeabbrev ='" . $_SESSION['Items' . $identifier]->DefaultSalesType . "'
					AND salesanalysis.periodno='" . $PeriodNo . "'
					AND salesanalysis.cust " . LIKE . " '" . $_SESSION['Items' . $identifier]->DebtorNo . "'
					AND salesanalysis.custbranch " . LIKE . " '" . $_SESSION['Items' . $identifier]->Branch . "'
					AND salesanalysis.stockid " . LIKE . " '" . $ReturnItemLine->StockID . "'
					AND salesanalysis.budgetoractual=1
					GROUP BY salesanalysis.stockid,
						salesanalysis.stkcategory,
						salesanalysis.cust,
						salesanalysis.custbranch,
						salesanalysis.area,
						salesanalysis.periodno,
						salesanalysis.typeabbrev,
						salesanalysis.salesperson";

			$ErrMsg = _('The count of existing Sales analysis records could not run because');
			$DbgMsg = _('SQL to count the no of sales analysis records');
			$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

			$myrow = DB_fetch_row($Result);

			if ($myrow[0]>0){  /*Update the existing record that already exists */

				$SQL = "UPDATE salesanalysis
							SET amt=amt-" . ($SalesValue) . ",
								cost=cost-" . ($ReturnItemLine->StandardCost * $ReturnItemLine->Quantity) . ",
								qty=qty -" . $ReturnItemLine->Quantity . ",
								disc=disc-" . ($ReturnItemLine->DiscountPercent * $SalesValue) . "
							WHERE salesanalysis.area='" . $myrow[5] . "'
								AND salesanalysis.salesperson='" . $_SESSION['Items' . $identifier]->SalesPerson . "'
								AND typeabbrev ='" . $_SESSION['Items' . $identifier]->DefaultSalesType . "'
								AND periodno = '" . $PeriodNo . "'
								AND cust " . LIKE . " '" . $_SESSION['Items' . $identifier]->DebtorNo . "'
								AND custbranch " . LIKE . " '" . $_SESSION['Items' . $identifier]->Branch . "'
								AND stockid " . LIKE . " '" . $ReturnItemLine->StockID . "'
								AND salesanalysis.stkcategory ='" . $myrow[2] . "'
								AND budgetoractual=1";

			} else { /* insert a new sales analysis record */

				$SQL = "INSERT INTO salesanalysis (	typeabbrev,
													periodno,
													amt,
													cost,
													cust,
													custbranch,
													qty,
													disc,
													stockid,
													area,
													budgetoractual,
													salesperson,
													stkcategory	)
					SELECT '" . $_SESSION['Items' . $identifier]->DefaultSalesType . "',
						'" . $PeriodNo . "',
						'" . -($SalesValue) . "',
						'" . -($ReturnItemLine->StandardCost * $ReturnItemLine->Quantity) . "',
						'" . $_SESSION['Items' . $identifier]->DebtorNo . "',
						'" . $_SESSION['Items' . $identifier]->Branch . "',
						'" . -$ReturnItemLine->Quantity . "',
						'" . -($ReturnItemLine->DiscountPercent * $SalesValue) . "',
						'" . $ReturnItemLine->StockID . "',
						custbranch.area,
						1,
						'" . $_SESSION['Items' . $identifier]->SalesPerson . "',
						stockmaster.categoryid
					FROM stockmaster,
						custbranch
					WHERE stockmaster.stockid = '" . $ReturnItemLine->StockID . "'
					AND custbranch.debtorno = '" . $_SESSION['Items' . $identifier]->DebtorNo . "'
					AND custbranch.branchcode='" . $_SESSION['Items' . $identifier]->Branch . "'";
			}

			$ErrMsg = _('Sales analysis record could not be added or updated because');
			$DbgMsg = _('The following SQL to insert the sales analysis record was used');
			$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		/* If GLLink_Stock then insert GLTrans to credit stock and debit cost of sales at standard cost*/

			if ($_SESSION['CompanyRecord']['gllink_stock']==1 AND $ReturnItemLine->StandardCost !=0){

		/*first the cost of sales entry*/
				$SubTotal1 = round(($ReturnItemLine->StandardCost * -$ReturnItemLine->Quantity) * (1 - $ReturnItemLine->DiscountPercent),$_SESSION['Items'.$identifier]->CurrDecimalPlaces);
				foreach ($ReturnItemLine->Taxes AS $Tax) {
				$SubTotal = ($SubTotal1/(1+$Tax->TaxRate));
				}

				$SQL = "INSERT INTO gltrans (	type,
												typeno,
												trandate,
												periodno,
												account,
												narrative,
												amount)
										VALUES ( 11,
												'" . $CreditNoteNo . "',
												'" . $ReturnDate . "',
												'" . $PeriodNo . "',
												'" . GetCOGSGLAccount($Area, $ReturnItemLine->StockID, $_SESSION['Items' . $identifier]->DefaultSalesType, $db) . "',
												'" . $_SESSION['Items' . $identifier]->DebtorNo . " - " . $ReturnItemLine->StockID . " x " . -$ReturnItemLine->Quantity . " @ " . $ReturnItemLine->StandardCost . "',
												'" . $SubTotal . "')"; //$ReturnItemLine->StandardCost * -$ReturnItemLine->Quantity

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The cost of sales GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		/*now the stock entry*/
		
				$SubTotal1 = round(($ReturnItemLine->StandardCost * $ReturnItemLine->Quantity) * (1 - $ReturnItemLine->DiscountPercent),$_SESSION['Items'.$identifier]->CurrDecimalPlaces);
				foreach ($ReturnItemLine->Taxes AS $Tax) {
				$SubTotal = ($SubTotal1/(1+$Tax->TaxRate));
				}
				$StockGLCode = GetStockGLCode($ReturnItemLine->StockID,$db);

				$SQL = "INSERT INTO gltrans (type,
											typeno,
											trandate,
											periodno,
											account,
											narrative,
											amount )
										VALUES ( 11,
											'" . $CreditNoteNo . "',
											'" . $ReturnDate . "',
											'" . $PeriodNo . "',
											'" . $StockGLCode['stockact'] . "',
											'" . $_SESSION['Items' . $identifier]->DebtorNo . " - " . $ReturnItemLine->StockID . " x " . -$ReturnItemLine->Quantity . " @ " . $ReturnItemLine->StandardCost . "',
											'" . $SubTotal . "')"; //($ReturnItemLine->StandardCost * $ReturnItemLine->Quantity)

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock side of the cost of sales GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
			} /* end of if GL and stock integrated and standard cost !=0 */

			if ($_SESSION['CompanyRecord']['gllink_debtors']==1 AND $ReturnItemLine->Price !=0){

		//Post sales transaction to GL credit sales
				$SalesGLAccounts = GetSalesGLAccount($Area, $ReturnItemLine->StockID, $_SESSION['Items' . $identifier]->DefaultSalesType, $db);
				
				$SubTotal1 = round(($ReturnItemLine->Price * $ReturnItemLine->Quantity/$ExRate) * (1 - $ReturnItemLine->DiscountPercent),$_SESSION['Items'.$identifier]->CurrDecimalPlaces);
				foreach ($ReturnItemLine->Taxes AS $Tax) {
				$SubTotal = ($SubTotal1/(1+$Tax->TaxRate));
				}

				$SQL = "INSERT INTO gltrans (type,
											typeno,
											trandate,
											periodno,
											account,
											narrative,
											amount )
										VALUES ( 11,
											'" . $CreditNoteNo . "',
											'" . $ReturnDate . "',
											'" . $PeriodNo . "',
											'" . $SalesGLAccounts['salesglcode'] . "',
											'" . $_SESSION['Items' . $identifier]->DebtorNo . " - " . $ReturnItemLine->StockID . " x " . -$ReturnItemLine->Quantity . " @ " . $ReturnItemLine->Price . "',
											'" . $SubTotal . "')"; //($ReturnItemLine->Price * $ReturnItemLine->Quantity/$ExRate)

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales GL posting could not be inserted because');
				$DbgMsg = '<br />' ._('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

				if ($ReturnItemLine->DiscountPercent !=0){

					$SQL = "INSERT INTO gltrans (type,
												typeno,
												trandate,
												periodno,
												account,
												narrative,
												amount )
												VALUES ( 11,
													'" . $CreditNoteNo . "',
													'" . $ReturnDate . "',
													'" . $PeriodNo . "',
													'" . $SalesGLAccounts['discountglcode'] . "',
													'" . $_SESSION['Items' . $identifier]->DebtorNo . " - " . $ReturnItemLine->StockID . " @ " . ($ReturnItemLine->DiscountPercent * 100) . "%',
													'" . -($ReturnItemLine->Price * $ReturnItemLine->Quantity * $ReturnItemLine->DiscountPercent/$ExRate) . "')";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales discount GL posting could not be inserted because');
					$DbgMsg = _('The following SQL to insert the GLTrans record was used');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
				} /*end of if discount !=0 */
			} /*end of if sales integrated with debtors */
		} /*end of OrderLine loop */

		if ($_SESSION['CompanyRecord']['gllink_debtors']==1){

	/*Post debtors transaction to GL debit debtors, credit freight re-charged and credit sales */
			if (($_SESSION['Items' . $identifier]->total + filter_number_format($_POST['TaxTotal'])) !=0) {
				$SQL = "INSERT INTO gltrans (	type,
												typeno,
												trandate,
												periodno,
												account,
												narrative,
												amount	)
											VALUES ( 11,
												'" . $CreditNoteNo . "',
												'" . $ReturnDate . "',
												'" . $PeriodNo . "',
												'" . $_SESSION['CompanyRecord']['debtorsact'] . "',
												'" . $_SESSION['Items' . $identifier]->DebtorNo . "',
												'" . -(($_SESSION['Items' . $identifier]->total + filter_number_format($_POST['TaxTotal']))/$ExRate) . "')";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The total debtor GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the total debtors control GLTrans record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
			}


			foreach ( $_SESSION['Items' . $identifier]->TaxTotals as $TaxAuthID => $TaxAmount){
				if ($TaxAmount !=0 ){
					$SQL = "INSERT INTO gltrans (	type,
													typeno,
													trandate,
													periodno,
													account,
													narrative,
													amount	)
												VALUES ( 11,
													'" . $CreditNoteNo . "',
													'" . $ReturnDate . "',
													'" . $PeriodNo . "',
													'" . $_SESSION['Items' . $identifier]->TaxGLCodes[$TaxAuthID] . "',
													'" . $_SESSION['Items' . $identifier]->DebtorNo . "',
													'" . ($TaxAmount/$ExRate) . "')";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The tax GL posting could not be inserted because');
					$DbgMsg = _('The following SQL to insert the GLTrans record was used');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
				}
			}

			EnsureGLEntriesBalance(11,$CreditNoteNo,$db);

			/*Also if GL is linked to debtors need to process the debit to bank and credit to debtors for the payment */
			/*Need to figure out the cross rate between customer currency and bank account currency */

			if ($_POST['AmountPaid']!=0){
				$PaymentNumber = GetNextTransNo(12,$db);
				$SQL="INSERT INTO gltrans (type,
											typeno,
											trandate,
											periodno,
											account,
											narrative,
											amount)
						VALUES (12,
							'" . $PaymentNumber . "',
							'" . $ReturnDate . "',
							'" . $PeriodNo . "',
							'" . $_POST['BankAccount'] . "',
							'" . $_SESSION['Items' . $identifier]->LocationName . ' ' . _('Counter Return') . ' ' . $CreditNoteNo . "',
							'" . -(filter_number_format($_POST['AmountPaid'])/$ExRate) . "')";
				$DbgMsg = _('The SQL that failed to insert the GL transaction for the bank account debit was');
				$ErrMsg = _('Cannot insert a GL transaction for the bank account debit');
				$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

				/* Now Debit Debtors account with negative receipt/payment to customer */
				$SQL="INSERT INTO gltrans ( type,
											typeno,
											trandate,
											periodno,
											account,
											narrative,
											amount)
						VALUES (12,
							'" . $PaymentNumber . "',
							'" . $ReturnDate . "',
							'" . $PeriodNo . "',
							'" . $_SESSION['CompanyRecord']['debtorsact'] . "',
							'" . $_SESSION['Items' . $identifier]->LocationName . ' ' . _('Counter Return') . ' ' . $CreditNoteNo . "',
							'" . (filter_number_format($_POST['AmountPaid'])/$ExRate) . "')";
				$DbgMsg = _('The SQL that failed to insert the GL transaction for the debtors account credit was');
				$ErrMsg = _('Cannot insert a GL transaction for the debtors account credit');
				$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
			}//amount paid was not zero

			EnsureGLEntriesBalance(12,$PaymentNumber,$db);

		} /*end of if Sales and GL integrated */

		if ($_POST['AmountPaid']!=0){
			if (!isset($PaymentNumber)){
				$PaymentNumber = GetNextTransNo(12,$db);
			}
			//Now need to add the receipt banktrans record
			//First get the account currency that it has been banked into
			$result = DB_query("SELECT rate FROM currencies
								INNER JOIN bankaccounts
								ON currencies.currabrev=bankaccounts.currcode
								WHERE bankaccounts.accountcode='" . $_POST['BankAccount'] . "'");
			$myrow = DB_fetch_row($result);
			$BankAccountExRate = $myrow[0];

			/*
			 * Some interesting exchange rate conversion going on here
			 * Say :
			 * The business's functional currency is NZD
			 * Customer location counter sales are in AUD - 1 NZD = 0.80 AUD
			 * Banking money into a USD account - 1 NZD = 0.68 USD
			 *
			 * Customer sale is for $100 AUD
			 * GL entries  conver the AUD 100 to NZD  - 100 AUD / 0.80 = $125 NZD
			 * Banktrans entries convert the AUD 100 to USD using 100/0.8 * 0.68
			*/

			//insert the banktrans record in the currency of the bank account

			$SQL="INSERT INTO banktrans (type,
										transno,
										bankact,
										ref,
										exrate,
										functionalexrate,
										transdate,
										banktranstype,
										amount,
										currcode)
					VALUES (12,
						'" . $PaymentNumber . "',
						'" . $_POST['BankAccount'] . "',
						'" . $_SESSION['Items' . $identifier]->LocationName . ' ' . _('Counter Sale') . ' ' . $CreditNoteNo . "',
						'" . $ExRate . "',
						'" . $BankAccountExRate . "',
						'" . $ReturnDate . "',
						'" . $_POST['PaymentMethod'] . "',
						'" . -filter_number_format($_POST['AmountPaid']) * $BankAccountExRate . "',
						'" . $_SESSION['Items' . $identifier]->DefaultCurrency . "')";

			$DbgMsg = _('The SQL that failed to insert the bank account transaction was');
			$ErrMsg = _('Cannot insert a bank transaction');
			$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

			//insert a new debtortrans for the receipt

			$SQL = "INSERT INTO debtortrans (transno,
											type,
											debtorno,
											trandate,
											inputdate,
											prd,
											reference,
											rate,
											ovamount,
											alloc,
											invtext)
					VALUES ('" . $PaymentNumber . "',
						12,
						'" . $_SESSION['Items' . $identifier]->DebtorNo . "',
						'" . $ReturnDate . "',
						'" . date('Y-m-d H-i-s') . "',
						'" . $PeriodNo . "',
						'" . $CreditNoteNo . "',
						'" . $ExRate . "',
						'" . filter_number_format($_POST['AmountPaid']) . "',
						'" . filter_number_format($_POST['AmountPaid']) . "',
						'" . $_SESSION['Items' . $identifier]->LocationName . ' ' . _('Counter Sale') ."')";

			$DbgMsg = _('The SQL that failed to insert the customer receipt transaction was');
			$ErrMsg = _('Cannot insert a receipt transaction against the customer because') ;
			$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

			$ReceiptDebtorTransID = DB_Last_Insert_ID($db,'debtortrans','id');


			//and finally add the allocation record between receipt and invoice

			$SQL = "INSERT INTO custallocns (	amt,
												datealloc,
												transid_allocfrom,
												transid_allocto )
									VALUES  ('" . filter_number_format($_POST['AmountPaid']) . "',
											'" . $ReturnDate . "',
											 '" . $DebtorTransID . "',
											 '" . $ReceiptDebtorTransID . "')";
			$DbgMsg = _('The SQL that failed to insert the allocation of the receipt to the credit note was');
			$ErrMsg = _('Cannot insert the customer allocation of the receipt to the invoice because');
			$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		} //end if $_POST['AmountPaid']!= 0

		DB_Txn_Commit();
	// *************************************************************************
	//   E N D   O F   C R E D I T  N O T E   S Q L   P R O C E S S I N G
	// *************************************************************************

		unset($_SESSION['Items' . $identifier]->LineItems);
		unset($_SESSION['Items' . $identifier]);

		echo prnMsg( _('Credit Note number'). ' '. $CreditNoteNo .' '. _('processed'), 'success');

		echo '<br /><div class="centre">';
		
		echo '<a target="_blank" href="'.$RootPath.'/ReceiptPrinter_CreditNote.php?TransNo='.$CreditNoteNo.'"> <img src="'.$RootPath.'/dist/Receipt-Printer_icon.png" title="' . _('Print this Receipt') . '" alt="" /></a><br /><br />';
		

		if ($_SESSION['InvoicePortraitFormat']==0){
			echo '<img src="'.$RootPath.'/css/'.$Theme.'/images/printer.png" title="' . _('Print') . '" alt="" />' . ' ' . '<a target="_blank" href="'.$RootPath.'/PrintCustTrans.php?FromTransNo='.$CreditNoteNo.'&InvOrCredit=Credit&PrintPDF=True">' .  _('Print this credit note'). ' (' . _('Landscape') . ')</a><br /><br />';
		} else {
			echo '<img src="'.$RootPath.'/css/'.$Theme.'/images/printer.png" title="' . _('Print') . '" alt="" />' . ' ' . '<a target="_blank" href="'.$RootPath.'/PrintCustTransPortrait.php?FromTransNo='.$CreditNoteNo.'&InvOrCredit=Credit&PrintPDF=True" onClick="return window.location=\'index.php\'">' .  _('Print this credit note'). ' (' . _('Portrait') . ')</a><br /><br />';
		}
		echo '<br /><br /><a href="' .htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '"><img src="'.$RootPath.'/dist/sale.png" title="' . _('Start a new Counter Sale') . '" alt="" /></a></div>';
		exit;
	}
	// There were input errors so don't process nuffin
} else {
	//pretend the user never tried to commit the sale
	unset($_POST['ProcessReturn']);
}
 
 if(isset($_POST['ProcessSalesOrder']) AND $_POST['ProcessSalesOrder'] != ''){
 

/* finally write the order header to the database and then the order line details */

	$DelDate = FormatDateforSQL($_SESSION['Items'.$identifier]->DeliveryDate);
	$QuotDate = FormatDateforSQL($_SESSION['Items'.$identifier]->QuoteDate);
	$ConfDate = FormatDateforSQL($_SESSION['Items'.$identifier]->ConfirmedDate);

	$Result = DB_Txn_Begin();

	$OrderNo = GetNextTransNo(30, $db);

	$HeaderSQL = "INSERT INTO salesorders (
								orderno,
								debtorno,
								branchcode,
								customerref,
								comments,
								orddate,
								ordertype,
								shipvia,
								deliverto,
								deladd1,
								deladd2,
								deladd3,
								deladd4,
								deladd5,
								deladd6,
								contactphone,
								contactemail,
								salesperson,
								freightcost,
								fromstkloc,
								deliverydate,
								quotedate,
								confirmeddate,
								quotation,
								approver,
								deliverblind)
							VALUES (
								'". $OrderNo . "',
								'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
								'" . $_SESSION['Items'.$identifier]->Branch . "',
								'". DB_escape_string($_SESSION['Items'.$identifier]->CustRef) ."',
								'". DB_escape_string($_SESSION['Items'.$identifier]->Comments) ."',
								'" . Date("Y-m-d H:i") . "',
								'" . $_SESSION['Items'.$identifier]->DefaultSalesType . "',
								'" . $_SESSION['Items'.$identifier]->ShipVia ."',
								'". DB_escape_string($_SESSION['Items'.$identifier]->DeliverTo) . "',
								'" . DB_escape_string($_SESSION['Items'.$identifier]->DelAdd1) . "',
								'" . DB_escape_string($_SESSION['Items'.$identifier]->DelAdd2) . "',
								'" . DB_escape_string($_SESSION['Items'.$identifier]->DelAdd3) . "',
								'" . DB_escape_string($_SESSION['Items'.$identifier]->DelAdd4) . "',
								'" . DB_escape_string($_SESSION['Items'.$identifier]->DelAdd5) . "',
								'" . DB_escape_string($_SESSION['Items'.$identifier]->DelAdd6) . "',
								'" . $_SESSION['Items'.$identifier]->PhoneNo . "',
								'" . $_SESSION['Items'.$identifier]->Email . "',
								'" . $_SESSION['Items'.$identifier]->SalesPerson . "',
								'" . $_SESSION['Items'.$identifier]->FreightCost ."',
								'" . $_SESSION['Items'.$identifier]->Location ."',
								'" . $DelDate . "',
								'" . $QuotDate . "',
								'" . $ConfDate . "',
								'" . $_SESSION['Items'.$identifier]->Quotation . "',
								'". DB_escape_string($_SESSION['Items'.$identifier]->Approver) ."',
								'" . $_SESSION['Items'.$identifier]->DeliverBlind ."'
								)";

	$ErrMsg = _('The order cannot be added because');
	$InsertQryResult = DB_query($HeaderSQL,$ErrMsg);


	$StartOf_LineItemsSQL = "INSERT INTO salesorderdetails (
											orderlineno,
											orderno,
											stkcode,
											unitprice,
											quantity,
											discountpercent,
											narrative,
											poline,
											itemdue)
										VALUES (";
	$DbgMsg = _('The SQL that failed was');
	foreach ($_SESSION['Items'.$identifier]->LineItems as $StockItem) {

		$LineItemsSQL = $StartOf_LineItemsSQL ."
					'" . $StockItem->LineNumber . "',
					'" . $OrderNo . "',
					'" . $StockItem->StockID . "',
					'" . $StockItem->Price . "',
					'" . $StockItem->Quantity . "',
					'" . floatval($StockItem->DiscountPercent) . "',
					'" . DB_escape_string($StockItem->Narrative) . "',
					'" . $StockItem->POLine . "',
					'" . FormatDateForSQL($StockItem->ItemDue) . "'
				)";
		$ErrMsg = _('Unable to add the sales order line');
		$Ins_LineItemResult = DB_query($LineItemsSQL,$ErrMsg,$DbgMsg,true);

		/*Now check to see if the item is manufactured
		 * 			and AutoCreateWOs is on
		 * 			and it is a real order (not just a quotation)*/

	} /* end inserted line items into sales order details */

	$result = DB_Txn_Commit();
	echo '<br />';
		prnMsg(_('Order Number') . ' ' . $OrderNo . ' ' . _('has been entered'),'success');
	
	if (count($_SESSION['AllowedPageSecurityTokens'])>1){
		/* Only allow print of packing slip for internal staff - customer logon's cannot go here */

			echo '<br /><table class="selection">
					<tr>
						<td><img src="'.$RootPath.'/css/'.$Theme.'/images/printer.png" title="' . _('Print') . '" alt="" /></td>
						<td>' . ' ' . '<a target="_blank" href="' . $RootPath . '/PrintCustOrder_SalesOrder.php?identifier='.$identifier . '&amp;TransNo=' . $OrderNo . '">' .  _('Print Sales Order') . ' (' . _('Preprinted stationery') . ')'  . '</a></td>
					</tr>';

			echo '<tr>
					<td><img src="'.$RootPath.'/css/'.$Theme.'/images/reports.png" title="' . _('Invoice') . '" alt="" /></td>
					<td>' . ' ' . '<a href="' . $RootPath . '/CounterSales.php?identifier='.$identifier . '&amp;OrderNumber=' . $OrderNo .'">' .  _('Confirm Dispatch and Produce Invoice')  . '</a></td>
				</tr>';

			echo '</table>';
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		echo '<br /><table class="selection">
				<tr>
					<td><img src="'.$RootPath.'/css/'.$Theme.'/images/sales.png" title="' . _('Order') . '" alt="" /></td>
					<td>' . ' ' . '<a href="'. $RootPath .'/CounterSales.php?NewOrder=Yes">' .  _('Back To Counter Sale')  . '</a></td>
				</tr>
				</table>';
	} else {
		/*its a customer logon so thank them */
		prnMsg(_('Thank you for your business'),'success');
	}

	unset($_SESSION['Items'.$identifier]->LineItems);
	unset($_SESSION['Items'.$identifier]);
	include('includes/footer.inc');
	exit;

 }

if (isset($_POST['ProcessInvoice']) AND $_POST['ProcessInvoice'] != ''){
	$Change = 0;
	$InputError = false; //always assume the best
	//but check for the worst
	if ($_SESSION['Items'.$identifier]->LineCounter == 0){
		prnMsg(_('There are no lines on this sale. Please enter lines to invoice first'),'error');
		$InputError = true;
	}
	//if (abs(filter_number_format($_POST['AmountPaid']) -(round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces)))>=0.01) {
		//prnMsg(_('The amount entered as payment does not equal the amount of the invoice. Please ensure the customer has paid the correct amount and re-enter'),'error');
		//$InputError = true;
	//}
	foreach ($_SESSION['Items'.$identifier]->LineItems as $OrderLine) {
	if ($OrderLine->Controlled==1){
		if(empty($OrderLine->SerialItems)){
		prnMsg(_('Only items entered with a positive quantity can be added to the sale'),'error');
		$InputError = true;
	}
	}
	}
/*	if (filter_number_format($_POST['AmountPaid']) -(round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces))<0.00) {
		prnMsg(_('The amount entered as payment does not equal the amount of the invoice. Please ensure the customer has paid the correct amount and re-enter'),'error');
		$InputError = true;
	}elseif(filter_number_format($_POST['AmountPaid']) -(round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces))>=0.01){
	$Change = filter_number_format($_POST['AmountPaid']) -(round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces));
	$_POST['AmountPaid'] = (round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces));
	}*/

	if ($_SESSION['ProhibitSaleBelowCost']==1){ // checks for negative stock after processing invoice
	//sadly this check does not combine quantities occuring twice on and order and each line is considered individually :-(
		$NFound = false;
		foreach ($_SESSION['Items'.$identifier]->LineItems as $OrderLine) {
			$SQL = "SELECT stockmaster.materialcost, description
		 			FROM stockmaster
					WHERE stockmaster.stockid='" . $OrderLine->StockID . "'";

			$ErrMsg = _('Could not retrieve the quantity left at the location once this order is invoiced (for the purposes of checking that stock will not go negative because)');
			$Results = DB_query($SQL,$ErrMsg);
			$CheckNRow = DB_fetch_array($Results);
				if ($CheckNRow['materialcost'] > $OrderLine->Price){
					prnMsg( _('Invoicing the selected order would result in a loss. The system parameters are set to prohibit sale below cost price from occurring. This invoice cannot be created until the price is corrected.'),'error',$OrderLine->StockID . ' ' . $CheckNRow['description'] . ' - ' . _('Sale below Cost Prohibited'));
					$NFound = true;
				}elseif($CheckNRow['materialcost'] == $OrderLine->Price){
				prnMsg( _('Price is same as Cost.'),'info',$OrderLine->StockID . ' ' . $CheckNRow['description'] . ' - ' . _('Sale made at Cost Price'));
				}

		} //end of loop around items on the order for negative check

		if ($NFound){
			$InputError = true;
		}

	}//end of testing for below cost
	
	if ($_SESSION['ProhibitNegativeStock']==1){ // checks for negative stock after processing invoice
	//sadly this check does not combine quantities occuring twice on and order and each line is considered individually :-(
		$NegativesFound = false;
		foreach ($_SESSION['Items'.$identifier]->LineItems as $OrderLine) {
			$SQL = "SELECT stockmaster.description,
					   		locstock.quantity,
					   		stockmaster.mbflag
		 			FROM locstock
		 			INNER JOIN stockmaster
					ON stockmaster.stockid=locstock.stockid
					WHERE stockmaster.stockid='" . $OrderLine->StockID . "'
					AND locstock.loccode='" . $_SESSION['Items'.$identifier]->Location . "'";

			$ErrMsg = _('Could not retrieve the quantity left at the location once this order is invoiced (for the purposes of checking that stock will not go negative because)');
			$Result = DB_query($SQL,$ErrMsg);
			$CheckNegRow = DB_fetch_array($Result);
			if ($CheckNegRow['mbflag']=='B' OR $CheckNegRow['mbflag']=='M'){
				if ($CheckNegRow['quantity'] < $OrderLine->Quantity){
					prnMsg( _('Invoicing the selected order would result in negative stock. The system parameters are set to prohibit negative stocks from occurring. This invoice cannot be created until the stock on hand is corrected.'),'error',$OrderLine->StockID . ' ' . $CheckNegRow['description'] . ' - ' . _('Negative Stock Prohibited'));
					$NegativesFound = true;
				}
			} else if ($CheckNegRow['mbflag']=='A') {

				/*Now look for assembly components that would go negative */
				$SQL = "SELECT bom.component,
							   stockmaster.description,
							   locstock.quantity-(" . $OrderLine->Quantity  . "*bom.quantity) AS qtyleft
						FROM bom
						INNER JOIN locstock
						ON bom.component=locstock.stockid
						INNER JOIN stockmaster
						ON stockmaster.stockid=bom.component
						WHERE bom.parent='" . $OrderLine->StockID . "'
						AND locstock.loccode='" . $_SESSION['Items'.$identifier]->Location . "'
                        AND bom.effectiveafter <= '" . date('Y-m-d') . "'
                        AND bom.effectiveto > '" . date('Y-m-d') . "'";

				$ErrMsg = _('Could not retrieve the component quantity left at the location once the assembly item on this order is invoiced (for the purposes of checking that stock will not go negative because)');
				$Result = DB_query($SQL,$ErrMsg);
				while ($NegRow = DB_fetch_array($Result)){
					if ($NegRow['qtyleft']<0){
						prnMsg(_('Invoicing the selected order would result in negative stock for a component of an assembly item on the order. The system parameters are set to prohibit negative stocks from occurring. This invoice cannot be created until the stock on hand is corrected.'),'error',$NegRow['component'] . ' ' . $NegRow['description'] . ' - ' . _('Negative Stock Prohibited'));
						$NegativesFound = true;
					} // end if negative would result
				} //loop around the components of an assembly item
			}//end if its an assembly item - check component stock

		} //end of loop around items on the order for negative check

		if ($NegativesFound){
			prnMsg(_('The parameter to prohibit negative stock is set and invoicing this sale would result in negative stock. No futher processing can be performed. Alter the sale first changing quantities or deleting lines which do not have sufficient stock.'),'error');
			$InputError = true;
		}

	}//end of testing for negative stocks


	if ($InputError == false) { //all good so let's get on with the processing

	/* Now Get the area where the sale is to from the branches table */

		$SQL = "SELECT area,
						defaultshipvia
				FROM custbranch
				WHERE custbranch.debtorno ='". $_SESSION['Items'.$identifier]->DebtorNo . "'
				AND custbranch.branchcode = '" . $_SESSION['Items'.$identifier]->Branch . "'";

		$ErrMsg = _('We were unable to load the area from the custbranch table where the sale is to ');
		$Result = DB_query($SQL, $ErrMsg);
		$myrow = DB_fetch_row($Result);
		$Area = $myrow[0];
		$DefaultShipVia = $myrow[1];
		DB_free_result($Result);

	/*company record read in on login with info on GL Links and debtors GL account*/

		if ($_SESSION['CompanyRecord']==0){
			/*The company data and preferences could not be retrieved for some reason */
			prnMsg( _('The company information and preferences could not be retrieved. See your system administrator'), 'error');
			include('includes/footer.inc');
			exit;
		}

	// *************************************************************************
	//   S T A R T   O F   I N V O I C E   S Q L   P R O C E S S I N G
	// *************************************************************************
		$result = DB_Txn_Begin();
	/*First add the order to the database - it only exists in the session currently! */
		$OrderNo = GetNextTransNo(30, $db);
		$InvoiceNo = GetNextTransNo(10, $db);
		//delivery number
		$DeliveryNo = GetNextTransNo(56, $db);
		$PeriodNo = GetPeriod(Date($_SESSION['DefaultDateFormat']), $db);

		$HeaderSQL = "INSERT INTO salesorders (
								orderno,
								debtorno,
								branchcode,
								customerref,
								comments,
								orddate,
								ordertype,
								shipvia,
								deliverto,
								deladd1,
								deladd2,
								deladd3,
								deladd4,
								deladd5,
								deladd6,
								contactphone,
								contactemail,
								salesperson,
								freightcost,
								fromstkloc,
								deliverydate,
								confirmeddate,
								quotation,
								approver,
								deliverblind)
							VALUES (
								'". $OrderNo . "',
								'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
								'" . $_SESSION['Items'.$identifier]->Branch . "',
								'". DB_escape_string($_SESSION['Items'.$identifier]->CustRef) ."',
								'". DB_escape_string($_SESSION['Items'.$identifier]->Comments) ."',
								'" . Date("Y-m-d H:i") . "',
								'" . $_SESSION['Items'.$identifier]->DefaultSalesType . "',
								'" . $_SESSION['Items'.$identifier]->ShipVia ."',
								'". DB_escape_string($_SESSION['Items'.$identifier]->DeliverTo) . "',
								'" . DB_escape_string($_SESSION['Items'.$identifier]->DelAdd1) . "',
								'" . DB_escape_string($_SESSION['Items'.$identifier]->DelAdd2) . "',
								'" . DB_escape_string($_SESSION['Items'.$identifier]->DelAdd3) . "',
								'" . DB_escape_string($_SESSION['Items'.$identifier]->DelAdd4) . "',
								'" . DB_escape_string($_SESSION['Items'.$identifier]->DelAdd5) . "',
								'" . DB_escape_string($_SESSION['Items'.$identifier]->DelAdd6) . "',
								'" . $_SESSION['Items'.$identifier]->PhoneNo . "',
								'" . $_SESSION['Items'.$identifier]->Email . "',
								'" . $_SESSION['Items'.$identifier]->SalesPerson . "',
								'" . $_SESSION['Items'.$identifier]->FreightCost ."',
								'" . $_SESSION['Items'.$identifier]->Location ."',
								'" . Date('Y-m-d') . "',
								'" . Date('Y-m-d') . "',
								'" . $_SESSION['Items'.$identifier]->Quotation . "',
								'". DB_escape_string($_SESSION['Items'.$identifier]->Approver) ."',
								'" . $_SESSION['Items'.$identifier]->DeliverBlind ."'
								)";
		$DbgMsg = _('Trouble inserting the sales order header. The SQL that failed was');
		$ErrMsg = _('The order cannot be added because');
		$InsertQryResult = DB_query($HeaderSQL,$ErrMsg,$DbgMsg,true);

		$StartOf_LineItemsSQL = "INSERT INTO salesorderdetails (orderlineno,
																orderno,
																stkcode,
																unitprice,
																quantity,
																discountpercent,
																narrative,
																itemdue,
																actualdispatchdate,
																qtyinvoiced,
																completed)
															VALUES (";

		$DbgMsg = _('Trouble inserting a line of a sales order. The SQL that failed was');
		foreach ($_SESSION['Items'.$identifier]->LineItems as $StockItem) {

			$LineItemsSQL = $StartOf_LineItemsSQL .
					"'".$StockItem->LineNumber . "',
					'" . $OrderNo . "',
					'" . $StockItem->StockID . "',
					'". $StockItem->Price . "',
					'" . $StockItem->Quantity . "',
					'" . floatval($StockItem->DiscountPercent) . "',
					'" . $StockItem->Narrative . "',
					'" . Date('Y-m-d') . "',
					'" . Date('Y-m-d') . "',
					'" . $StockItem->Quantity . "',
					1)";

			$ErrMsg = _('Unable to add the sales order line');
			$Ins_LineItemResult = DB_query($LineItemsSQL,$ErrMsg,$DbgMsg,true);

			/*Now check to see if the item is manufactured
			 * 			and AutoCreateWOs is on
			 * 			and it is a real order (not just a quotation)*/

			if ($StockItem->MBflag=='M'
				AND $_SESSION['AutoCreateWOs']==1){ //oh yeah its all on!

				//now get the data required to test to see if we need to make a new WO
				$QOHResult = DB_query("SELECT SUM(quantity) FROM locstock WHERE stockid='" . $StockItem->StockID . "'");
				$QOHRow = DB_fetch_row($QOHResult);
				$QOH = $QOHRow[0];

				$SQL = "SELECT SUM(salesorderdetails.quantity - salesorderdetails.qtyinvoiced) AS qtydemand
						FROM salesorderdetails INNER JOIN salesorders
						ON salesorderdetails.orderno=salesorders.orderno
						WHERE salesorderdetails.stkcode = '" . $StockItem->StockID . "'
						AND salesorderdetails.completed = 0
						AND salesorders.quotation = 0";
				$DemandResult = DB_query($SQL);
				$DemandRow = DB_fetch_row($DemandResult);
				$QuantityDemand = $DemandRow[0];

				$SQL = "SELECT SUM((salesorderdetails.quantity-salesorderdetails.qtyinvoiced)*bom.quantity) AS dem
						FROM salesorderdetails INNER JOIN salesorders
						ON salesorderdetails.orderno=salesorders.orderno
						INNER JOIN bom
						ON salesorderdetails.stkcode=bom.parent
						INNER JOIN stockmaster
						ON stockmaster.stockid=bom.parent
						WHERE salesorderdetails.quantity-salesorderdetails.qtyinvoiced > 0
						AND bom.component='" . $StockItem->StockID . "'
						AND salesorderdetails.completed=0
						AND salesorders.quotation=0";
				$AssemblyDemandResult = DB_query($SQL);
				$AssemblyDemandRow = DB_fetch_row($AssemblyDemandResult);
				$QuantityAssemblyDemand = $AssemblyDemandRow[0];

				// Get the QOO due to Purchase orders for all locations. Function defined in SQL_CommonFunctions.inc
				$QuantityPurchOrders= GetQuantityOnOrderDueToPurchaseOrders($StockItem->StockID, '');
				// Get the QOO dues to Work Orders for all locations. Function defined in SQL_CommonFunctions.inc
				$QuantityWorkOrders = GetQuantityOnOrderDueToWorkOrders($StockItem->StockID, '');

				//Now we have the data - do we need to make any more?
				$ShortfallQuantity = $QOH-$QuantityDemand-$QuantityAssemblyDemand+$QuantityPurchOrders+$QuantityWorkOrders;

				if ($ShortfallQuantity < 0) { //then we need to make a work order
					//How many should the work order be for??
					if ($ShortfallQuantity + $StockItem->EOQ < 0){
						$WOQuantity = -$ShortfallQuantity;
					} else {
						$WOQuantity = $StockItem->EOQ;
					}

					$WONo = GetNextTransNo(40,$db);
					$ErrMsg = _('Unable to insert a new work order for the sales order item');
					$InsWOResult = DB_query("INSERT INTO workorders (wo,
													 loccode,
													 requiredby,
													 startdate)
									 VALUES ('" . $WONo . "',
											'" . $_SESSION['DefaultFactoryLocation'] . "',
											'" . Date('Y-m-d') . "',
											'" . Date('Y-m-d'). "')",
											$ErrMsg,
											$DbgMsg,
											true);
					//Need to get the latest BOM to roll up cost
					$CostResult = DB_query("SELECT SUM((materialcost+labourcost+overheadcost)*bom.quantity) AS cost
																	FROM stockmaster INNER JOIN bom
																	ON stockmaster.stockid=bom.component
																	WHERE bom.parent='" . $StockItem->StockID . "'
																	AND bom.loccode='" . $_SESSION['DefaultFactoryLocation'] . "'");
					$CostRow = DB_fetch_row($CostResult);
					if (is_null($CostRow[0]) OR $CostRow[0]==0){
						$Cost =0;
						prnMsg(_('In automatically creating a work order for') . ' ' . $StockItem->StockID . ' ' . _('an item on this sales order, the cost of this item as accumulated from the sum of the component costs is nil. This could be because there is no bill of material set up ... you may wish to double check this'),'warn');
					} else {
						$Cost = $CostRow[0];
					}

					// insert parent item info
					$sql = "INSERT INTO woitems (wo,
												 stockid,
												 qtyreqd,
												 stdcost)
									 VALUES ('" . $WONo . "',
											 '" . $StockItem->StockID . "',
											 '" . $WOQuantity . "',
											 '" . $Cost . "')";
					$ErrMsg = _('The work order item could not be added');
					$result = DB_query($sql,$ErrMsg,$DbgMsg,true);

					//Recursively insert real component requirements - see includes/SQL_CommonFunctions.in for function WoRealRequirements
					WoRealRequirements($db, $WONo, $_SESSION['DefaultFactoryLocation'], $StockItem->StockID);

					$FactoryManagerEmail = _('A new work order has been created for') .
										":\n" . $StockItem->StockID . ' - ' . $StockItem->ItemDescription . ' x ' . $WOQuantity . ' ' . $StockItem->Units .
										"\n" . _('These are for') . ' ' . $_SESSION['Items'.$identifier]->CustomerName . ' ' . _('there order ref') . ': '  . $_SESSION['Items'.$identifier]->CustRef . ' ' ._('our order number') . ': ' . $OrderNo;

					if ($StockItem->Serialised AND $StockItem->NextSerialNo>0){
						//then we must create the serial numbers for the new WO also
						$FactoryManagerEmail .= "\n" . _('The following serial numbers have been reserved for this work order') . ':';

						for ($i=0;$i<$WOQuantity;$i++){

							$result = DB_query("SELECT serialno FROM stockserialitems
													WHERE serialno='" . ($StockItem->NextSerialNo + $i) . "'
													AND stockid='" . $StockItem->StockID ."'");
							if (DB_num_rows($result)!=0){
								$WOQuantity++;
								prnMsg(($StockItem->NextSerialNo + $i) . ': ' . _('This automatically generated serial number already exists - it cannot be added to the work order'),'error');
							} else {
								$sql = "INSERT INTO woserialnos (wo,
																	stockid,
																	serialno)
														VALUES ('" . $WONo . "',
																'" . $StockItem->StockID . "',
																'" . ($StockItem->NextSerialNo + $i)	 . "')";
								$ErrMsg = _('The serial number for the work order item could not be added');
								$result = DB_query($sql,$ErrMsg,$DbgMsg,true);
								$FactoryManagerEmail .= "\n" . ($StockItem->NextSerialNo + $i);
							}
						} //end loop around creation of woserialnos
						$NewNextSerialNo = ($StockItem->NextSerialNo + $WOQuantity +1);
						$ErrMsg = _('Could not update the new next serial number for the item');
						$UpdateSQL="UPDATE stockmaster SET nextserialno='" . $NewNextSerialNo . "' WHERE stockid='" . $StockItem->StockID . "'";
						$UpdateNextSerialNoResult = DB_query($UpdateSQL,$ErrMsg,$DbgMsg,true);
					} // end if the item is serialised and nextserialno is set

					$EmailSubject = _('New Work Order Number') . ' ' . $WONo . ' ' . _('for') . ' ' . $StockItem->StockID . ' x ' . $WOQuantity;
					//Send email to the Factory Manager
					if($_SESSION['SmtpSetting']==0){
							mail($_SESSION['FactoryManagerEmail'],$EmailSubject,$FactoryManagerEmail);

					}else{
							include('includes/htmlMimeMail.php');
							$mail = new htmlMimeMail();
							$mail->setSubject($EmailSubject);
							$result = SendmailBySmtp($mail,array($_SESSION['FactoryManagerEmail']));
					}

				} //end if with this sales order there is a shortfall of stock - need to create the WO
			}//end if auto create WOs in on
		} /* end inserted line items into sales order details */

		prnMsg(_('Order Number') . ' ' . $OrderNo . ' ' . _('has been entered'),'success');

	/* End of insertion of new sales order */

	/*Now Get the next invoice number - GetNextTransNo() function in SQL_CommonFunctions
	 * GetPeriod() in includes/DateFunctions.inc */



		$DefaultDispatchDate = Date('Y-m-d');

	/*Update order header for invoice charged on */
		$SQL = "UPDATE salesorders SET comments = CONCAT(comments,'" . ' ' . _('Invoice') . ': ' . "','" . $InvoiceNo . "') WHERE orderno= '" . $OrderNo."'";

		$ErrMsg = _('CRITICAL ERROR') . ' ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales order header could not be updated with the invoice number');
		$DbgMsg = _('The following SQL to update the sales order was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

	/*Now insert the DebtorTrans */

		$SQL = "INSERT INTO debtortrans (transno,
										type,
										debtorno,
										branchcode,
										trandate,
										inputdate,
										prd,
										reference,
										tpe,
										order_,
										ovamount,
										ovgst,
										rate,
										invtext,
										shipvia,
										salesperson,
										bankacc )
			VALUES (
				'". $InvoiceNo . "',
				10,
				'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
				'" . $_SESSION['Items'.$identifier]->Branch . "',
				'" . $DefaultDispatchDate . "',
				'" . date('Y-m-d H-i-s') . "',
				'" . $PeriodNo . "',
				'" . $_SESSION['Items'.$identifier]->CustRef  . "',
				'" . $_SESSION['Items'.$identifier]->DefaultSalesType . "',
				'" . $OrderNo . "',
				'" . $_SESSION['Items'.$identifier]->total . "',
				'" . filter_number_format($_POST['TaxTotal']) . "',
				'" . $ExRate . "',
				'" . $_SESSION['Items'.$identifier]->Comments . "',
				'" . $_SESSION['Items'.$identifier]->ShipVia . "',
				'" . $_SESSION['Items'.$identifier]->SalesPerson . "',
				'".$_POST['BankAccount']."')";

		$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor transaction record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the debtor transaction record was used');
	 	$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		$DebtorTransID = DB_Last_Insert_ID($db,'debtortrans','id');

	/* Insert the tax totals for each tax authority where tax was charged on the invoice */
		foreach ($_SESSION['Items'.$identifier]->TaxTotals AS $TaxAuthID => $TaxAmount) {

			$SQL = "INSERT INTO debtortranstaxes (debtortransid,
													taxauthid,
													taxamount)
										VALUES ('" . $DebtorTransID . "',
											'" . $TaxAuthID . "',
											'" . $TaxAmount/$ExRate . "')";

			$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor transaction taxes records could not be inserted because');
			$DbgMsg = _('The following SQL to insert the debtor transaction taxes record was used');
	 		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		}

		//Loop around each item on the sale and process each in turn
		foreach ($_SESSION['Items'.$identifier]->LineItems as $OrderLine) {
		
		/*DELIVERY NOTE DETAILS HERE*/
			/*-----------------------------------------------------------------------------------------------------------------------------------*/
			
			$SQLi = "INSERT INTO deliverynotes (deliverynotenumber,
														deliverynotelineno,
														salesorderno,
														salesorderlineno,
														qtydelivered,
														deliverydate )
													VALUES ('" . $DeliveryNo . "',
														'" . $OrderLine->LineNumber . "',
														'" . $OrderNo . "',
														'" . $OrderLine->LineNumber . "',
														'" . $OrderLine->QtyDispatched . "',
														'" . $DefaultDispatchDate . "' )";
			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records could not be inserted because');
			$DbgMsg = _('The following SQL to insert the stock movement records was used');
			$DeliveryResult = DB_query($SQLi,$ErrMsg,$DbgMsg,true);
			/*-----------------------------------------------------------------------------------------------------------------------------------*/

			 /* Update location stock records if not a dummy stock item
			 need the MBFlag later too so save it to $MBFlag */
			$Result = DB_query("SELECT mbflag FROM stockmaster WHERE stockid = '" . $OrderLine->StockID . "'");
			$myrow = DB_fetch_row($Result);
			$MBFlag = $myrow[0];
			if ($MBFlag=='B' OR $MBFlag=='M') {
				$Assembly = False;

				/* Need to get the current location quantity
				will need it later for the stock movement */
				$SQL="SELECT locstock.quantity
								FROM locstock
								WHERE locstock.stockid='" . $OrderLine->StockID . "'
								AND loccode= '" . $_SESSION['Items'.$identifier]->Location . "'";
				$ErrMsg = _('WARNING') . ': ' . _('Could not retrieve current location stock');
				$Result = DB_query($SQL, $ErrMsg);

				if (DB_num_rows($Result)==1){
					$LocQtyRow = DB_fetch_row($Result);
					$QtyOnHandPrior = $LocQtyRow[0];
				} else {
					/* There must be some error this should never happen */
					$QtyOnHandPrior = 0;
				}

				$SQL = "UPDATE locstock
							SET quantity = locstock.quantity - " . $OrderLine->Quantity . "
							WHERE locstock.stockid = '" . $OrderLine->StockID . "'
							AND loccode = '" . $_SESSION['Items'.$identifier]->Location . "'";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Location stock record could not be updated because');
				$DbgMsg = _('The following SQL to update the location stock record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

			} else if ($MBFlag=='A'){ /* its an assembly */
				/*Need to get the BOM for this part and make
				stock moves for the components then update the Location stock balances */
				$Assembly=True;
				$StandardCost =0; /*To start with - accumulate the cost of the comoponents for use in journals later on */
				$SQL = "SELECT bom.component,
						bom.quantity,
						stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost AS standard
						FROM bom,
							stockmaster
						WHERE bom.component=stockmaster.stockid
						AND bom.parent='" . $OrderLine->StockID . "'
                        AND bom.effectiveafter <= '" . date('Y-m-d') . "'
                        AND bom.effectiveto > '" . date('Y-m-d') . "'";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Could not retrieve assembly components from the database for'). ' '. $OrderLine->StockID . _('because').' ';
				$DbgMsg = _('The SQL that failed was');
				$AssResult = DB_query($SQL,$ErrMsg,$DbgMsg,true);

				while ($AssParts = DB_fetch_array($AssResult)){

					$StandardCost += ($AssParts['standard'] * $AssParts['quantity']) ;
					/* Need to get the current location quantity
					will need it later for the stock movement */
					$SQL="SELECT locstock.quantity
									FROM locstock
									WHERE locstock.stockid='" . $AssParts['component'] . "'
									AND loccode= '" . $_SESSION['Items'.$identifier]->Location . "'";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Can not retrieve assembly components location stock quantities because ');
					$DbgMsg = _('The SQL that failed was');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
					if (DB_num_rows($Result)==1){
						$LocQtyRow = DB_fetch_row($Result);
						$QtyOnHandPrior = $LocQtyRow[0];
					} else {
						/*There must be some error this should never happen */
						$QtyOnHandPrior = 0;
					}
					if (empty($AssParts['standard'])) {
						$AssParts['standard']=0;
					}
					$SQL = "INSERT INTO stockmoves (stockid,
													type,
													transno,
													loccode,
													trandate,
													userid,
													debtorno,
													branchcode,
													prd,
													reference,
													qty,
													standardcost,
													show_on_inv_crds,
													newqoh)
										VALUES ('" . $AssParts['component'] . "',
												 10,
												'" . $InvoiceNo . "',
												'" . $_SESSION['Items'.$identifier]->Location . "',
												'" . $DefaultDispatchDate . "',
												'" . $_SESSION['UserID'] . "',
												'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
												'" . $_SESSION['Items'.$identifier]->Branch . "',
												'" . $PeriodNo . "',
												'" . _('Assembly') . ': ' . $OrderLine->StockID . ' ' . _('Order') . ': ' . $OrderNo . "',
												'" . -$AssParts['quantity'] * $OrderLine->Quantity . "',
												'" . $AssParts['standard'] . "',
												0,
												newqoh-" . ($AssParts['quantity'] * $OrderLine->Quantity) . " )";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records for the assembly components of'). ' '. $OrderLine->StockID . ' ' . _('could not be inserted because');
					$DbgMsg = _('The following SQL to insert the assembly components stock movement records was used');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);


					$SQL = "UPDATE locstock
							SET quantity = locstock.quantity - " . $AssParts['quantity'] * $OrderLine->Quantity . "
							WHERE locstock.stockid = '" . $AssParts['component'] . "'
							AND loccode = '" . $_SESSION['Items'.$identifier]->Location . "'";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Location stock record could not be updated for an assembly component because');
					$DbgMsg = _('The following SQL to update the locations stock record for the component was used');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
				} /* end of assembly explosion and updates */

				/*Update the cart with the recalculated standard cost from the explosion of the assembly's components*/
				$_SESSION['Items'.$identifier]->LineItems[$OrderLine->LineNumber]->StandardCost = $StandardCost;
				$OrderLine->StandardCost = $StandardCost;
			} /* end of its an assembly */

			// Insert stock movements - with unit cost
			$LocalCurrencyPrice = ($OrderLine->Price / $ExRate);

			if (empty($OrderLine->StandardCost)) {
				$OrderLine->StandardCost=0;
			}
			if ($MBFlag=='B' OR $MBFlag=='M'){
				$SQL = "INSERT INTO stockmoves (stockid,
												type,
												transno,
												loccode,
												trandate,
												userid,
												debtorno,
												branchcode,
												price,
												prd,
												reference,
												qty,
												discountpercent,
												standardcost,
												newqoh,
												narrative )
						VALUES ('" . $OrderLine->StockID . "',
								10,
								'" . $InvoiceNo . "',
								'" . $_SESSION['Items'.$identifier]->Location . "',
								'" . $DefaultDispatchDate . "',
								'" . $_SESSION['UserID'] . "',
								'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
								'" . $_SESSION['Items'.$identifier]->Branch . "',
								'" . $LocalCurrencyPrice . "',
								'" . $PeriodNo . "',
								'" . $OrderNo . "',
								'" . -$OrderLine->Quantity . "',
								'" . $OrderLine->DiscountPercent . "',
								'" . $OrderLine->StandardCost . "',
								'" . ($QtyOnHandPrior - $OrderLine->Quantity) . "',
								'" . $OrderLine->Narrative . "' )";
			} else {
			// its an assembly or dummy and assemblies/dummies always have nil stock by definition they are made up at the time of dispatch  so new qty on hand will be nil
				if (empty($OrderLine->StandardCost)) {
					$OrderLine->StandardCost = 0;
				}
				$SQL = "INSERT INTO stockmoves (stockid,
												type,
												transno,
												loccode,
												trandate,
												userid,
												debtorno,
												branchcode,
												price,
												prd,
												reference,
												qty,
												discountpercent,
												standardcost,
												narrative )
						VALUES ('" . $OrderLine->StockID . "',
										10,
										'" . $InvoiceNo . "',
										'" . $_SESSION['Items'.$identifier]->Location . "',
										'" . $DefaultDispatchDate . "',
										'" . $_SESSION['UserID'] . "',
										'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
										'" . $_SESSION['Items'.$identifier]->Branch . "',
										'" . $LocalCurrencyPrice . "',
										'" . $PeriodNo . "',
										'" . $OrderNo . "',
										'" . -$OrderLine->Quantity . "',
										'" . $OrderLine->DiscountPercent . "',
										'" . $OrderLine->StandardCost . "',
										'" . $OrderLine->Narrative . "')";
			}

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records could not be inserted because');
			$DbgMsg = _('The following SQL to insert the stock movement records was used');
			$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		/*Get the ID of the StockMove... */
			$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');

		/*Insert the taxes that applied to this line */
			foreach ($OrderLine->Taxes as $Tax) {

				$SQL = "INSERT INTO stockmovestaxes (stkmoveno,
									taxauthid,
									taxrate,
									taxcalculationorder,
									taxontax)
						VALUES ('" . $StkMoveNo . "',
							'" . $Tax->TaxAuthID . "',
							'" . $Tax->TaxRate . "',
							'" . $Tax->TaxCalculationOrder . "',
							'" . $Tax->TaxOnTax . "')";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Taxes and rates applicable to this invoice line item could not be inserted because');
				$DbgMsg = _('The following SQL to insert the stock movement tax detail records was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
			} //end for each tax for the line

			// Controlled stuff not currently handled by counter orders

			//Insert the StockSerialMovements and update the StockSerialItems  for controlled items

			if ($OrderLine->Controlled ==1){
				foreach($OrderLine->SerialItems as $Item){
								//We need to add the StockSerialItem record and the StockSerialMoves as well

					$SQL = "UPDATE stockserialitems
							SET quantity= quantity - " . $Item->BundleQty . "
							WHERE stockid='" . $OrderLine->StockID . "'
							AND loccode='" . $_SESSION['Items'.$identifier]->Location . "'
							AND serialno='" . $Item->BundleRef . "'";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be updated because');
					$DbgMsg = _('The following SQL to update the serial stock item record was used');
					$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

					// now insert the serial stock movement

					$SQL = "INSERT INTO stockserialmoves (stockmoveno,
										stockid,
										serialno,
										moveqty)
						VALUES (" . $StkMoveNo . ",
							'" . $OrderLine->StockID . "',
							'" . $Item->BundleRef . "',
							" . -$Item->BundleQty . ")";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
					$DbgMsg = _('The following SQL to insert the serial stock movement records was used');
					$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
				}// foreach controlled item in the serialitems array
			} //end if the orderline is a controlled item

			//End of controlled stuff not currently handled by counter orders
			
			
			
			$SalesValue = 0;
			if ($ExRate>0){
				$SalesValue = $OrderLine->Price * $OrderLine->Quantity / $ExRate;
			}

		/*Insert Sales Analysis records */

			$SQL="SELECT COUNT(*),
					salesanalysis.stockid,
					salesanalysis.stkcategory,
					salesanalysis.cust,
					salesanalysis.custbranch,
					salesanalysis.area,
					salesanalysis.periodno,
					salesanalysis.typeabbrev,
					salesanalysis.salesperson
				FROM salesanalysis,
					custbranch,
					stockmaster
				WHERE salesanalysis.stkcategory=stockmaster.categoryid
				AND salesanalysis.stockid=stockmaster.stockid
				AND salesanalysis.cust=custbranch.debtorno
				AND salesanalysis.custbranch=custbranch.branchcode
				AND salesanalysis.area=custbranch.area
				AND salesanalysis.salesperson='" . $_SESSION['Items'.$identifier]->SalesPerson . "'
				AND salesanalysis.typeabbrev ='" . $_SESSION['Items'.$identifier]->DefaultSalesType . "'
				AND salesanalysis.periodno='" . $PeriodNo . "'
				AND salesanalysis.cust " . LIKE . " '" . $_SESSION['Items'.$identifier]->DebtorNo . "'
				AND salesanalysis.custbranch " . LIKE . " '" . $_SESSION['Items'.$identifier]->Branch . "'
				AND salesanalysis.stockid " . LIKE . " '" . $OrderLine->StockID . "'
				AND salesanalysis.budgetoractual=1
				GROUP BY salesanalysis.stockid,
					salesanalysis.stkcategory,
					salesanalysis.cust,
					salesanalysis.custbranch,
					salesanalysis.area,
					salesanalysis.periodno,
					salesanalysis.typeabbrev,
					salesanalysis.salesperson";

			$ErrMsg = _('The count of existing Sales analysis records could not run because');
			$DbgMsg = _('SQL to count the no of sales analysis records');
			$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

			$myrow = DB_fetch_row($Result);

			if ($myrow[0]>0){  /*Update the existing record that already exists */

				$SQL = "UPDATE salesanalysis
							SET amt=amt+" . ($SalesValue) . ",
								cost=cost+" . ($OrderLine->StandardCost * $OrderLine->Quantity) . ",
								qty=qty +" . $OrderLine->Quantity . ",
								disc=disc+" . ($OrderLine->DiscountPercent * $SalesValue) . "
							WHERE salesanalysis.area='" . $myrow[5] . "'
							AND salesanalysis.salesperson='" . $_SESSION['Items'.$identifier]->SalesPerson . "'
							AND typeabbrev ='" . $_SESSION['Items'.$identifier]->DefaultSalesType . "'
							AND periodno = '" . $PeriodNo . "'
							AND cust " . LIKE . " '" . $_SESSION['Items'.$identifier]->DebtorNo . "'
							AND custbranch " . LIKE . " '" . $_SESSION['Items'.$identifier]->Branch . "'
							AND stockid " . LIKE . " '" . $OrderLine->StockID . "'
							AND salesanalysis.stkcategory ='" . $myrow[2] . "'
							AND budgetoractual=1";

			} else { /* insert a new sales analysis record */

				$SQL = "INSERT INTO salesanalysis (	typeabbrev,
													periodno,
													amt,
													cost,
													cust,
													custbranch,
													qty,
													disc,
													stockid,
													area,
													budgetoractual,
													salesperson,
													stkcategory	)
					SELECT '" . $_SESSION['Items'.$identifier]->DefaultSalesType . "',
						'" . $PeriodNo . "',
						'" . ($SalesValue) . "',
						'" . ($OrderLine->StandardCost * $OrderLine->Quantity) . "',
						'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
						'" . $_SESSION['Items'.$identifier]->Branch . "',
						'" . $OrderLine->Quantity . "',
						'" . ($OrderLine->DiscountPercent * $SalesValue) . "',
						'" . $OrderLine->StockID . "',
						custbranch.area,
						1,
						'" . $_SESSION['Items'.$identifier]->SalesPerson . "',
						stockmaster.categoryid
					FROM stockmaster,
						custbranch
					WHERE stockmaster.stockid = '" . $OrderLine->StockID . "'
					AND custbranch.debtorno = '" . $_SESSION['Items'.$identifier]->DebtorNo . "'
					AND custbranch.branchcode='" . $_SESSION['Items'.$identifier]->Branch . "'";
			}

			$ErrMsg = _('Sales analysis record could not be added or updated because');
			$DbgMsg = _('The following SQL to insert the sales analysis record was used');
			$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		/* If GLLink_Stock then insert GLTrans to credit stock and debit cost of sales at standard cost*/

			if ($_SESSION['CompanyRecord']['gllink_stock']==1 AND $OrderLine->StandardCost !=0){

		/*first the cost of sales entry*/

				$SQL = "INSERT INTO gltrans (	type,
												typeno,
												trandate,
												periodno,
												account,
												narrative,
												amount)
										VALUES ( 10,
												'" . $InvoiceNo . "',
												'" . $DefaultDispatchDate . "',
												'" . $PeriodNo . "',
												'" . GetCOGSGLAccount($Area, $OrderLine->StockID, $_SESSION['Items'.$identifier]->DefaultSalesType, $db) . "',
												'" . $_SESSION['Items'.$identifier]->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->Quantity . " @ " . $OrderLine->StandardCost . "',
												'" . $OrderLine->StandardCost * $OrderLine->Quantity . "')";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The cost of sales GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		/*now the stock entry*/
				$StockGLCode = GetStockGLCode($OrderLine->StockID,$db);

				$SQL = "INSERT INTO gltrans (type,
											typeno,
											trandate,
											periodno,
											account,
											narrative,
											amount )
										VALUES ( 10,
											'" . $InvoiceNo . "',
											'" . $DefaultDispatchDate . "',
											'" . $PeriodNo . "',
											'" . $StockGLCode['stockact'] . "',
											'" . $_SESSION['Items'.$identifier]->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->Quantity . " @ " . $OrderLine->StandardCost . "',
											'" . (-$OrderLine->StandardCost * $OrderLine->Quantity) . "')";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock side of the cost of sales GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
			} /* end of if GL and stock integrated and standard cost !=0 */

			if ($_SESSION['CompanyRecord']['gllink_debtors']==1 AND $OrderLine->Price !=0){

		//Post sales transaction to GL credit sales
				$SalesGLAccounts = GetSalesGLAccount($Area, $OrderLine->StockID, $_SESSION['Items'.$identifier]->DefaultSalesType, $db);
				
				$SubTotal1 = round($OrderLine->Quantity * $OrderLine->Price * (1 - $OrderLine->DiscountPercent),$_SESSION['Items'.$identifier]->CurrDecimalPlaces);
				foreach ($OrderLine->Taxes AS $Tax) {
				$SubTotal = ($SubTotal1/(1+$Tax->TaxRate));
				}

				$SQL = "INSERT INTO gltrans (type,
											typeno,
											trandate,
											periodno,
											account,
											narrative,
											amount )
										VALUES ( 10,
											'" . $InvoiceNo . "',
											'" . $DefaultDispatchDate . "',
											'" . $PeriodNo . "',
											'" . $SalesGLAccounts['salesglcode'] . "',
											'" . $_SESSION['Items'.$identifier]->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->Quantity . " @ " . $OrderLine->Price . "',
											'" . (-$SubTotal/$ExRate) . "')"; // original (-$OrderLine->Price * $OrderLine->Quantity/$ExRate)

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales GL posting could not be inserted because');
				$DbgMsg = '<br />' ._('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

				if ($OrderLine->DiscountPercent !=0){

					$SQL = "INSERT INTO gltrans (type,
												typeno,
												trandate,
												periodno,
												account,
												narrative,
												amount )
												VALUES ( 10,
													'" . $InvoiceNo . "',
													'" . $DefaultDispatchDate . "',
													'" . $PeriodNo . "',
													'" . $SalesGLAccounts['discountglcode'] . "',
													'" . $_SESSION['Items'.$identifier]->DebtorNo . " - " . $OrderLine->StockID . " @ " . ($OrderLine->DiscountPercent * 100) . "%',
													'" . ($OrderLine->Price * $OrderLine->Quantity * $OrderLine->DiscountPercent/$ExRate) . "')";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales discount GL posting could not be inserted because');
					$DbgMsg = _('The following SQL to insert the GLTrans record was used');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
				} /*end of if discount !=0 */
			} /*end of if sales integrated with debtors */
		} /*end of OrderLine loop */

		if ($_SESSION['CompanyRecord']['gllink_debtors']==1){

	/*Post debtors transaction to GL debit debtors, credit freight re-charged and credit sales */
			if (($_SESSION['Items'.$identifier]->total + filter_number_format($_POST['TaxTotal'])) !=0) {
				$SQL = "INSERT INTO gltrans (	type,
												typeno,
												trandate,
												periodno,
												account,
												narrative,
												amount	)
											VALUES ( 10,
												'" . $InvoiceNo . "',
												'" . $DefaultDispatchDate . "',
												'" . $PeriodNo . "',
												'" . $_SESSION['CompanyRecord']['debtorsact'] . "',
												'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
												'" . (($_SESSION['Items'.$identifier]->total + filter_number_format($_POST['TaxTotal']))/$ExRate) . "')";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The total debtor GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the total debtors control GLTrans record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
			}


			foreach ( $_SESSION['Items'.$identifier]->TaxTotals as $TaxAuthID => $TaxAmount){
				if ($TaxAmount !=0 ){
					$SQL = "INSERT INTO gltrans (	type,
													typeno,
													trandate,
													periodno,
													account,
													narrative,
													amount	)
												VALUES ( 10,
													'" . $InvoiceNo . "',
													'" . $DefaultDispatchDate . "',
													'" . $PeriodNo . "',
													'" . $_SESSION['Items'.$identifier]->TaxGLCodes[$TaxAuthID] . "',
													'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
													'" . (-$TaxAmount/$ExRate) . "')";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The tax GL posting could not be inserted because');
					$DbgMsg = _('The following SQL to insert the GLTrans record was used');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
				}
			}

			EnsureGLEntriesBalance(10,$InvoiceNo,$db);


		} /*end of if Sales and GL integrated */

		DB_Txn_Commit();
	// *************************************************************************
	//   E N D   O F   I N V O I C E   S Q L   P R O C E S S I N G
	// *************************************************************************

		unset($_SESSION['Items'.$identifier]->LineItems);
		unset($_SESSION['Items'.$identifier]);

		echo prnMsg( _('Invoice number'). ' '. $InvoiceNo .' '. _('processed'), 'success');

		echo '<br /><div class="centre">';

		//echo '<a target="_blank" href="'.$RootPath.'/ReceiptPrinter_Invoice.php?TransNo='.$InvoiceNo.'"> <img src="'.$RootPath.'/dist/Receipt-Printer_icon.png" title="' . _('Print this Invoice') . '" alt="" /></a><br /><br />';
		
		echo '<br /><table class="selection">
					<tr>
						<td><img src="'.$RootPath.'/css/'.$Theme.'/images/printer.png" title="' . _('Print') . '" alt="" /></td>
						<td>' . ' ' . '<a target="_blank" href="' . $RootPath . '/PrintCustOrder_SalesOrder.php?identifier='.$identifier . '&amp;TransNo=' . $OrderNo . '">' .  _('Print Sales Order') . ' (' . _('Preprinted stationery') . ')'  . '</a></td>
					</tr>';
			if ($_SESSION['InvoicePortraitFormat']==0){
			echo '<tr>
					<td><img src="'.$RootPath.'/css/'.$Theme.'/images/printer.png" title="' . _('Print') . '" alt="" /></td>
					<td>' . ' ' . '<a target="_blank" href="'.$RootPath.'/PrintCustTrans.php?FromTransNo='.$InvoiceNo.'&amp;InvOrCredit=Invoice&amp;PrintPDF=True">' .  _('Print this invoice*'). ' (' . _('Landscape') . ')</a></td>
				</tr>';
			} else {	
			echo '<tr>
					<td><img src="'.$RootPath.'/css/'.$Theme.'/images/printer.png" title="' . _('Print') . '" alt="" /></td>
					<td>' . ' ' . '<a target="_blank" href="'.$RootPath.'/PrintCustTransPortrait.php?FromTransNo='.$InvoiceNo.'&amp;InvOrCredit=Invoice&amp;PrintPDF=True">' .  _('Print this invoice*'). ' (' . _('Portrait') . ')</a></td>
				</tr>';
				}
				
			echo '<tr>
					<td><img src="'.$RootPath.'/css/'.$Theme.'/images/printer.png" title="' . _('Print') . '" alt="" /></td>
					<td>' . ' ' . '<a target="_blank" href="'.$RootPath.'/PrintCustOrder_PickingNote.php?TransNo='.$DeliveryNo.'">' .  _('Print Picking Note'). '</a></td>
				</tr>';

			echo '</table>';
		
		echo '<br /><br /><a href="' .htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '"><img src="'.$RootPath.'/dist/sale.png" title="' . _('Start a new Counter Sale') . '" alt="" /></a></div>';
	
	exit;
	}
	
	// There were input errors so don't process nuffin
} else {
	//pretend the user never tried to commit the sale
	unset($_POST['ProcessInvoice']);
}

if (isset($_POST['ProcessOrderInvoice']) AND $_POST['ProcessOrderInvoice'] != ''){

/* SQL to process the postings for sales invoices...

	$InputError = false; //always assume the best
	//but check for the worst
	if ($_SESSION['Items'.$identifier]->LineCounter == 0){
		prnMsg(_('There are no lines on this sale. Please enter lines to invoice first'),'error');
		$InputError = true;
	}
	//if (abs(filter_number_format($_POST['AmountPaid']) -(round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces)))>=0.01) {
		//prnMsg(_('The amount entered as payment does not equal the amount of the invoice. Please ensure the customer has paid the correct amount and re-enter'),'error');
		//$InputError = true;
	//}
	foreach ($_SESSION['Items'.$identifier]->LineItems as $OrderLine) {
	if ($OrderLine->Controlled==1){
		if(empty($OrderLine->SerialItems)){
		prnMsg(_('Only items entered with a positive quantity can be added to the sale'),'error');
		$InputError = true;
	}
	}
	}
/*	if (filter_number_format($_POST['AmountPaid']) -(round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces))<0.00) {
		prnMsg(_('The amount entered as payment does not equal the amount of the invoice. Please ensure the customer has paid the correct amount and re-enter'),'error');
		$InputError = true;
	}elseif(filter_number_format($_POST['AmountPaid']) -(round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces))>=0.01){
	$Change = filter_number_format($_POST['AmountPaid']) -(round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces));
	$_POST['AmountPaid'] = (round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces));
	}*/

	if ($_SESSION['ProhibitSaleBelowCost']==1){ // checks for negative stock after processing invoice
	//sadly this check does not combine quantities occuring twice on and order and each line is considered individually :-(
		$NFound = false;
		foreach ($_SESSION['Items'.$identifier]->LineItems as $OrderLine) {
			$SQL = "SELECT stockmaster.materialcost, description
		 			FROM stockmaster
					WHERE stockmaster.stockid='" . $OrderLine->StockID . "'";

			$ErrMsg = _('Could not retrieve the quantity left at the location once this order is invoiced (for the purposes of checking that stock will not go negative because)');
			$Results = DB_query($SQL,$ErrMsg);
			$CheckNRow = DB_fetch_array($Results);
				if ($CheckNRow['materialcost'] > $OrderLine->Price){
					prnMsg( _('Invoicing the selected order would result in a loss. The system parameters are set to prohibit sale below cost price from occurring. This invoice cannot be created until the price is corrected.'),'error',$OrderLine->StockID . ' ' . $CheckNRow['description'] . ' - ' . _('Sale below Cost Prohibited'));
					$NFound = true;
				}elseif($CheckNRow['materialcost'] == $OrderLine->Price){
				prnMsg( _('Price is same as Cost.'),'info',$OrderLine->StockID . ' ' . $CheckNRow['description'] . ' - ' . _('Sale made at Cost Price'));
				}

		} //end of loop around items on the order for negative check

		if ($NFound){
			$InputError = true;
		}

	}//end of testing for below cost
	
	if ($_SESSION['ProhibitNegativeStock']==1){ // checks for negative stock after processing invoice
	//sadly this check does not combine quantities occuring twice on and order and each line is considered individually :-(
		$NegativesFound = false;
		foreach ($_SESSION['Items'.$identifier]->LineItems as $OrderLine) {
			$SQL = "SELECT stockmaster.description,
					   		locstock.quantity,
					   		stockmaster.mbflag
		 			FROM locstock
		 			INNER JOIN stockmaster
					ON stockmaster.stockid=locstock.stockid
					WHERE stockmaster.stockid='" . $OrderLine->StockID . "'
					AND locstock.loccode='" . $_SESSION['Items'.$identifier]->Location . "'";

			$ErrMsg = _('Could not retrieve the quantity left at the location once this order is invoiced (for the purposes of checking that stock will not go negative because)');
			$Result = DB_query($SQL,$ErrMsg);
			$CheckNegRow = DB_fetch_array($Result);
			if ($CheckNegRow['mbflag']=='B' OR $CheckNegRow['mbflag']=='M'){
				if ($CheckNegRow['quantity'] < $OrderLine->Quantity){
					prnMsg( _('Invoicing the selected order would result in negative stock. The system parameters are set to prohibit negative stocks from occurring. This invoice cannot be created until the stock on hand is corrected.'),'error',$OrderLine->StockID . ' ' . $CheckNegRow['description'] . ' - ' . _('Negative Stock Prohibited'));
					$NegativesFound = true;
				}
			} else if ($CheckNegRow['mbflag']=='A') {

				/*Now look for assembly components that would go negative */
				$SQL = "SELECT bom.component,
							   stockmaster.description,
							   locstock.quantity-(" . $OrderLine->Quantity  . "*bom.quantity) AS qtyleft
						FROM bom
						INNER JOIN locstock
						ON bom.component=locstock.stockid
						INNER JOIN stockmaster
						ON stockmaster.stockid=bom.component
						WHERE bom.parent='" . $OrderLine->StockID . "'
						AND locstock.loccode='" . $_SESSION['Items'.$identifier]->Location . "'
                        AND bom.effectiveafter <= '" . date('Y-m-d') . "'
                        AND bom.effectiveto > '" . date('Y-m-d') . "'";

				$ErrMsg = _('Could not retrieve the component quantity left at the location once the assembly item on this order is invoiced (for the purposes of checking that stock will not go negative because)');
				$Result = DB_query($SQL,$ErrMsg);
				while ($NegRow = DB_fetch_array($Result)){
					if ($NegRow['qtyleft']<0){
						prnMsg(_('Invoicing the selected order would result in negative stock for a component of an assembly item on the order. The system parameters are set to prohibit negative stocks from occurring. This invoice cannot be created until the stock on hand is corrected.'),'error',$NegRow['component'] . ' ' . $NegRow['description'] . ' - ' . _('Negative Stock Prohibited'));
						$NegativesFound = true;
					} // end if negative would result
				} //loop around the components of an assembly item
			}//end if its an assembly item - check component stock

		} //end of loop around items on the order for negative check

		if ($NegativesFound){
			prnMsg(_('The parameter to prohibit negative stock is set and invoicing this sale would result in negative stock. No futher processing can be performed. Alter the sale first changing quantities or deleting lines which do not have sufficient stock.'),'error');
			$InputError = true;
		}

	}//end of testing for negative stocks


	if ($InputError == false) { //all good so let's get on with the processing

	/* Now Get the area where the sale is to from the branches table */

		$SQL = "SELECT area,
						defaultshipvia
				FROM custbranch
				WHERE custbranch.debtorno ='". $_SESSION['Items'.$identifier]->DebtorNo . "'
				AND custbranch.branchcode = '" . $_SESSION['Items'.$identifier]->Branch . "'";

		$ErrMsg = _('We were unable to load the area from the custbranch table where the sale is to ');
		$Result = DB_query($SQL, $ErrMsg);
		$myrow = DB_fetch_row($Result);
		$Area = $myrow[0];
		$DefaultShipVia = $myrow[1];
		DB_free_result($Result);

	/*company record read in on login with info on GL Links and debtors GL account*/

		if ($_SESSION['CompanyRecord']==0){
			/*The company data and preferences could not be retrieved for some reason */
			prnMsg( _('The company information and preferences could not be retrieved. See your system administrator'), 'error');
			include('includes/footer.inc');
			exit;
		}

	// *************************************************************************
	//   S T A R T   O F   I N V O I C E   S Q L   P R O C E S S I N G
	// *************************************************************************
	/*First add the order to the database - it only exists in the session currently! */
	//----------------------------------------------------------------------------------------------------------------------
$SQL = "SELECT id, transno, ovamount
				FROM debtortrans
				WHERE order_ = '" . $_SESSION['ProcessingOrder']."'";
	$Result = DB_query($SQL);
	if (DB_num_rows($Result) >0){
	$myrows = DB_fetch_row($Result);
	$id = $myrows[0];
	$InvoiceNo = $myrows[1];
	$ovamount = $myrows[2];
	$NewSalesOder = FALSE;
	
	}else{

//-----------------------------------------------------------------------------------------------------------------------
	$NewSalesOder = TRUE;

	$InvoiceNo = GetNextTransNo(10, $db);
	$PeriodNo = GetPeriod($DefaultDispatchDate, $db);

	}
	//delivery number
	$DeliveryNo = GetNextTransNo(56, $db);
	$OrderNo = $_SESSION['ProcessingOrder'];
	$result = DB_Txn_Begin();
	
	/* End of insertion of new sales order */

	/*Now Get the next invoice number - GetNextTransNo() function in SQL_CommonFunctions
	 * GetPeriod() in includes/DateFunctions.inc */



		$DefaultDispatchDate = Date('Y-m-d');

if($NewSalesOder == TRUE){
	/*Update order header for invoice charged on */
		$SQL = "UPDATE salesorders SET comments = CONCAT(comments,'" . ' ' . _('Invoice') . ': ' . "','" . $InvoiceNo . "') WHERE orderno= '" . $OrderNo."'";

		$ErrMsg = _('CRITICAL ERROR') . ' ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales order header could not be updated with the invoice number');
		$DbgMsg = _('The following SQL to update the sales order was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

	/*Now insert the DebtorTrans */

		$SQL = "INSERT INTO debtortrans (transno,
										type,
										debtorno,
										branchcode,
										trandate,
										inputdate,
										prd,
										reference,
										tpe,
										order_,
										ovamount,
										ovgst,
										rate,
										invtext,
										shipvia,
										salesperson,
										bankacc )
			VALUES (
				'". $InvoiceNo . "',
				10,
				'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
				'" . $_SESSION['Items'.$identifier]->Branch . "',
				'" . $DefaultDispatchDate . "',
				'" . date('Y-m-d H-i-s') . "',
				'" . $PeriodNo . "',
				'" . $_SESSION['Items'.$identifier]->CustRef  . "',
				'" . $_SESSION['Items'.$identifier]->DefaultSalesType . "',
				'" . $OrderNo . "',
				'" . $_SESSION['Items'.$identifier]->total . "',
				'" . filter_number_format($_POST['TaxTotal']) . "',
				'" . $ExRate . "',
				'" . $_SESSION['Items'.$identifier]->Comments . "',
				'" . $_SESSION['Items'.$identifier]->ShipVia . "',
				'" . $_SESSION['Items'.$identifier]->SalesPerson . "',
				'".$_POST['BankAccount']."')";

		$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor transaction record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the debtor transaction record was used');
	 	$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		$DebtorTransID = DB_Last_Insert_ID($db,'debtortrans','id');

	/* Insert the tax totals for each tax authority where tax was charged on the invoice */
		foreach ($_SESSION['Items'.$identifier]->TaxTotals AS $TaxAuthID => $TaxAmount) {

			$SQL = "INSERT INTO debtortranstaxes (debtortransid,
													taxauthid,
													taxamount)
										VALUES ('" . $DebtorTransID . "',
											'" . $TaxAuthID . "',
											'" . $TaxAmount/$ExRate . "')";

			$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor transaction taxes records could not be inserted because');
			$DbgMsg = _('The following SQL to insert the debtor transaction taxes record was used');
	 		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		}
	
	}else{
	
	$SQLi = "UPDATE debtortrans
					SET ovamount = ovamount + " . $_SESSION['Items'.$identifier]->total . ",
					ovgst = ovgst + " . $TaxTotal . ",
					ovfreight = ovfreight + " . filter_number_format($_POST['ChargeFreightCost']) . ",
					trandate = '".$DefaultDispatchDate."',
					inputdate = '".date('Y-m-d H-i-s')."'
					WHERE transno = '" . $InvoiceNo . " '";

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales order detail record could not be updated because');
			$DbgMsg = _('The following SQL to update the sales order detail record was used');
			$Result = DB_query($SQLi,$ErrMsg,$DbgMsg,true);

foreach ($TaxTotals AS $TaxAuthID => $TaxAmount) {			
$SQLs = "UPDATE debtortranstaxes
					SET taxamount = taxamount + " . ($TaxAmount/$_SESSION['CurrencyRate']) . "
					WHERE debtortransid = '" . $id . " '";

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales order detail record could not be updated because');
			$DbgMsg = _('The following SQL to update the sales order detail record was used');
			$Result = DB_query($SQLs,$ErrMsg,$DbgMsg,true);
			
		}
	
	}

		//Loop around each item on the sale and process each in turn
		foreach ($_SESSION['Items'.$identifier]->LineItems as $OrderLine) {
		
		/*DELIVERY NOTE DETAILS HERE*/
			/*-----------------------------------------------------------------------------------------------------------------------------------*/
			
			$SQLi = "INSERT INTO deliverynotes (deliverynotenumber,
														deliverynotelineno,
														salesorderno,
														salesorderlineno,
														qtydelivered,
														deliverydate )
													VALUES ('" . $DeliveryNo . "',
														'" . $OrderLine->LineNumber . "',
														'" . $_SESSION['ProcessingOrder'] . "',
														'" . $OrderLine->LineNumber . "',
														'" . $OrderLine->QtyDispatched . "',
														'" . $DefaultDispatchDate . "' )";
			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records could not be inserted because');
			$DbgMsg = _('The following SQL to insert the stock movement records was used');
			$DeliveryResult = DB_query($SQLi,$ErrMsg,$DbgMsg,true);
			/*-----------------------------------------------------------------------------------------------------------------------------------*/
		
			 /* Update location stock records if not a dummy stock item
			 need the MBFlag later too so save it to $MBFlag */
			$Result = DB_query("SELECT mbflag FROM stockmaster WHERE stockid = '" . $OrderLine->StockID . "'");
			$myrow = DB_fetch_row($Result);
			$MBFlag = $myrow[0];
			if ($MBFlag=='B' OR $MBFlag=='M') {
				$Assembly = False;

				/* Need to get the current location quantity
				will need it later for the stock movement */
				$SQL="SELECT locstock.quantity
								FROM locstock
								WHERE locstock.stockid='" . $OrderLine->StockID . "'
								AND loccode= '" . $_SESSION['Items'.$identifier]->Location . "'";
				$ErrMsg = _('WARNING') . ': ' . _('Could not retrieve current location stock');
				$Result = DB_query($SQL, $ErrMsg);

				if (DB_num_rows($Result)==1){
					$LocQtyRow = DB_fetch_row($Result);
					$QtyOnHandPrior = $LocQtyRow[0];
				} else {
					/* There must be some error this should never happen */
					$QtyOnHandPrior = 0;
				}

				$SQL = "UPDATE locstock
							SET quantity = locstock.quantity - " . $OrderLine->Quantity . "
							WHERE locstock.stockid = '" . $OrderLine->StockID . "'
							AND loccode = '" . $_SESSION['Items'.$identifier]->Location . "'";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Location stock record could not be updated because');
				$DbgMsg = _('The following SQL to update the location stock record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

			} else if ($MBFlag=='A'){ /* its an assembly */
				/*Need to get the BOM for this part and make
				stock moves for the components then update the Location stock balances */
				$Assembly=True;
				$StandardCost =0; /*To start with - accumulate the cost of the comoponents for use in journals later on */
				$SQL = "SELECT bom.component,
						bom.quantity,
						stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost AS standard
						FROM bom,
							stockmaster
						WHERE bom.component=stockmaster.stockid
						AND bom.parent='" . $OrderLine->StockID . "'
                        AND bom.effectiveafter <= '" . date('Y-m-d') . "'
                        AND bom.effectiveto > '" . date('Y-m-d') . "'";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Could not retrieve assembly components from the database for'). ' '. $OrderLine->StockID . _('because').' ';
				$DbgMsg = _('The SQL that failed was');
				$AssResult = DB_query($SQL,$ErrMsg,$DbgMsg,true);

				while ($AssParts = DB_fetch_array($AssResult)){

					$StandardCost += ($AssParts['standard'] * $AssParts['quantity']) ;
					/* Need to get the current location quantity
					will need it later for the stock movement */
					$SQL="SELECT locstock.quantity
									FROM locstock
									WHERE locstock.stockid='" . $AssParts['component'] . "'
									AND loccode= '" . $_SESSION['Items'.$identifier]->Location . "'";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Can not retrieve assembly components location stock quantities because ');
					$DbgMsg = _('The SQL that failed was');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
					if (DB_num_rows($Result)==1){
						$LocQtyRow = DB_fetch_row($Result);
						$QtyOnHandPrior = $LocQtyRow[0];
					} else {
						/*There must be some error this should never happen */
						$QtyOnHandPrior = 0;
					}
					if (empty($AssParts['standard'])) {
						$AssParts['standard']=0;
					}
					$SQL = "INSERT INTO stockmoves (stockid,
													type,
													transno,
													loccode,
													trandate,
													userid,
													debtorno,
													branchcode,
													prd,
													reference,
													qty,
													standardcost,
													show_on_inv_crds,
													newqoh)
										VALUES ('" . $AssParts['component'] . "',
												 10,
												'" . $InvoiceNo . "',
												'" . $_SESSION['Items'.$identifier]->Location . "',
												'" . $DefaultDispatchDate . "',
												'" . $_SESSION['UserID'] . "',
												'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
												'" . $_SESSION['Items'.$identifier]->Branch . "',
												'" . $PeriodNo . "',
												'" . _('Assembly') . ': ' . $OrderLine->StockID . ' ' . _('Order') . ': ' . $OrderNo . "',
												'" . -$AssParts['quantity'] * $OrderLine->Quantity . "',
												'" . $AssParts['standard'] . "',
												0,
												newqoh-" . ($AssParts['quantity'] * $OrderLine->Quantity) . " )";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records for the assembly components of'). ' '. $OrderLine->StockID . ' ' . _('could not be inserted because');
					$DbgMsg = _('The following SQL to insert the assembly components stock movement records was used');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);


					$SQL = "UPDATE locstock
							SET quantity = locstock.quantity - " . $AssParts['quantity'] * $OrderLine->Quantity . "
							WHERE locstock.stockid = '" . $AssParts['component'] . "'
							AND loccode = '" . $_SESSION['Items'.$identifier]->Location . "'";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Location stock record could not be updated for an assembly component because');
					$DbgMsg = _('The following SQL to update the locations stock record for the component was used');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
				} /* end of assembly explosion and updates */

				/*Update the cart with the recalculated standard cost from the explosion of the assembly's components*/
				$_SESSION['Items'.$identifier]->LineItems[$OrderLine->LineNumber]->StandardCost = $StandardCost;
				$OrderLine->StandardCost = $StandardCost;
			} /* end of its an assembly */

			// Insert stock movements - with unit cost
			$LocalCurrencyPrice = ($OrderLine->Price / $ExRate);

			if (empty($OrderLine->StandardCost)) {
				$OrderLine->StandardCost=0;
			}
			if ($MBFlag=='B' OR $MBFlag=='M'){
				$SQL = "INSERT INTO stockmoves (stockid,
												type,
												transno,
												loccode,
												trandate,
												userid,
												debtorno,
												branchcode,
												price,
												prd,
												reference,
												qty,
												discountpercent,
												standardcost,
												newqoh,
												narrative )
						VALUES ('" . $OrderLine->StockID . "',
								10,
								'" . $InvoiceNo . "',
								'" . $_SESSION['Items'.$identifier]->Location . "',
								'" . $DefaultDispatchDate . "',
								'" . $_SESSION['UserID'] . "',
								'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
								'" . $_SESSION['Items'.$identifier]->Branch . "',
								'" . $LocalCurrencyPrice . "',
								'" . $PeriodNo . "',
								'" . $OrderNo . "',
								'" . -$OrderLine->Quantity . "',
								'" . $OrderLine->DiscountPercent . "',
								'" . $OrderLine->StandardCost . "',
								'" . ($QtyOnHandPrior - $OrderLine->Quantity) . "',
								'" . $OrderLine->Narrative . "' )";
			} else {
			// its an assembly or dummy and assemblies/dummies always have nil stock by definition they are made up at the time of dispatch  so new qty on hand will be nil
				if (empty($OrderLine->StandardCost)) {
					$OrderLine->StandardCost = 0;
				}
				$SQL = "INSERT INTO stockmoves (stockid,
												type,
												transno,
												loccode,
												trandate,
												userid,
												debtorno,
												branchcode,
												price,
												prd,
												reference,
												qty,
												discountpercent,
												standardcost,
												narrative )
						VALUES ('" . $OrderLine->StockID . "',
										10,
										'" . $InvoiceNo . "',
										'" . $_SESSION['Items'.$identifier]->Location . "',
										'" . $DefaultDispatchDate . "',
										'" . $_SESSION['UserID'] . "',
										'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
										'" . $_SESSION['Items'.$identifier]->Branch . "',
										'" . $LocalCurrencyPrice . "',
										'" . $PeriodNo . "',
										'" . $OrderNo . "',
										'" . -$OrderLine->Quantity . "',
										'" . $OrderLine->DiscountPercent . "',
										'" . $OrderLine->StandardCost . "',
										'" . $OrderLine->Narrative . "')";
			}

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records could not be inserted because');
			$DbgMsg = _('The following SQL to insert the stock movement records was used');
			$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		/*Get the ID of the StockMove... */
			$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');

		/*Insert the taxes that applied to this line */
			foreach ($OrderLine->Taxes as $Tax) {

				$SQL = "INSERT INTO stockmovestaxes (stkmoveno,
									taxauthid,
									taxrate,
									taxcalculationorder,
									taxontax)
						VALUES ('" . $StkMoveNo . "',
							'" . $Tax->TaxAuthID . "',
							'" . $Tax->TaxRate . "',
							'" . $Tax->TaxCalculationOrder . "',
							'" . $Tax->TaxOnTax . "')";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Taxes and rates applicable to this invoice line item could not be inserted because');
				$DbgMsg = _('The following SQL to insert the stock movement tax detail records was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
			} //end for each tax for the line

			// Controlled stuff not currently handled by counter orders

			//Insert the StockSerialMovements and update the StockSerialItems  for controlled items

			if ($OrderLine->Controlled ==1){
				foreach($OrderLine->SerialItems as $Item){
								//We need to add the StockSerialItem record and the StockSerialMoves as well

					$SQL = "UPDATE stockserialitems
							SET quantity= quantity - " . $Item->BundleQty . "
							WHERE stockid='" . $OrderLine->StockID . "'
							AND loccode='" . $_SESSION['Items'.$identifier]->Location . "'
							AND serialno='" . $Item->BundleRef . "'";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be updated because');
					$DbgMsg = _('The following SQL to update the serial stock item record was used');
					$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

					// now insert the serial stock movement

					$SQL = "INSERT INTO stockserialmoves (stockmoveno,
										stockid,
										serialno,
										moveqty)
						VALUES (" . $StkMoveNo . ",
							'" . $OrderLine->StockID . "',
							'" . $Item->BundleRef . "',
							" . -$Item->BundleQty . ")";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
					$DbgMsg = _('The following SQL to insert the serial stock movement records was used');
					$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
				}// foreach controlled item in the serialitems array
			} //end if the orderline is a controlled item

			//End of controlled stuff not currently handled by counter orders
			
			
			
			$SalesValue = 0;
			if ($ExRate>0){
				$SalesValue = $OrderLine->Price * $OrderLine->Quantity / $ExRate;
			}

		/*Insert Sales Analysis records */

			$SQL="SELECT COUNT(*),
					salesanalysis.stockid,
					salesanalysis.stkcategory,
					salesanalysis.cust,
					salesanalysis.custbranch,
					salesanalysis.area,
					salesanalysis.periodno,
					salesanalysis.typeabbrev,
					salesanalysis.salesperson
				FROM salesanalysis,
					custbranch,
					stockmaster
				WHERE salesanalysis.stkcategory=stockmaster.categoryid
				AND salesanalysis.stockid=stockmaster.stockid
				AND salesanalysis.cust=custbranch.debtorno
				AND salesanalysis.custbranch=custbranch.branchcode
				AND salesanalysis.area=custbranch.area
				AND salesanalysis.salesperson='" . $_SESSION['Items'.$identifier]->SalesPerson . "'
				AND salesanalysis.typeabbrev ='" . $_SESSION['Items'.$identifier]->DefaultSalesType . "'
				AND salesanalysis.periodno='" . $PeriodNo . "'
				AND salesanalysis.cust " . LIKE . " '" . $_SESSION['Items'.$identifier]->DebtorNo . "'
				AND salesanalysis.custbranch " . LIKE . " '" . $_SESSION['Items'.$identifier]->Branch . "'
				AND salesanalysis.stockid " . LIKE . " '" . $OrderLine->StockID . "'
				AND salesanalysis.budgetoractual=1
				GROUP BY salesanalysis.stockid,
					salesanalysis.stkcategory,
					salesanalysis.cust,
					salesanalysis.custbranch,
					salesanalysis.area,
					salesanalysis.periodno,
					salesanalysis.typeabbrev,
					salesanalysis.salesperson";

			$ErrMsg = _('The count of existing Sales analysis records could not run because');
			$DbgMsg = _('SQL to count the no of sales analysis records');
			$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

			$myrow = DB_fetch_row($Result);

			if ($myrow[0]>0){  /*Update the existing record that already exists */

				$SQL = "UPDATE salesanalysis
							SET amt=amt+" . ($SalesValue) . ",
								cost=cost+" . ($OrderLine->StandardCost * $OrderLine->Quantity) . ",
								qty=qty +" . $OrderLine->Quantity . ",
								disc=disc+" . ($OrderLine->DiscountPercent * $SalesValue) . "
							WHERE salesanalysis.area='" . $myrow[5] . "'
							AND salesanalysis.salesperson='" . $_SESSION['Items'.$identifier]->SalesPerson . "'
							AND typeabbrev ='" . $_SESSION['Items'.$identifier]->DefaultSalesType . "'
							AND periodno = '" . $PeriodNo . "'
							AND cust " . LIKE . " '" . $_SESSION['Items'.$identifier]->DebtorNo . "'
							AND custbranch " . LIKE . " '" . $_SESSION['Items'.$identifier]->Branch . "'
							AND stockid " . LIKE . " '" . $OrderLine->StockID . "'
							AND salesanalysis.stkcategory ='" . $myrow[2] . "'
							AND budgetoractual=1";

			} else { /* insert a new sales analysis record */

				$SQL = "INSERT INTO salesanalysis (	typeabbrev,
													periodno,
													amt,
													cost,
													cust,
													custbranch,
													qty,
													disc,
													stockid,
													area,
													budgetoractual,
													salesperson,
													stkcategory	)
					SELECT '" . $_SESSION['Items'.$identifier]->DefaultSalesType . "',
						'" . $PeriodNo . "',
						'" . ($SalesValue) . "',
						'" . ($OrderLine->StandardCost * $OrderLine->Quantity) . "',
						'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
						'" . $_SESSION['Items'.$identifier]->Branch . "',
						'" . $OrderLine->Quantity . "',
						'" . ($OrderLine->DiscountPercent * $SalesValue) . "',
						'" . $OrderLine->StockID . "',
						custbranch.area,
						1,
						'" . $_SESSION['Items'.$identifier]->SalesPerson . "',
						stockmaster.categoryid
					FROM stockmaster,
						custbranch
					WHERE stockmaster.stockid = '" . $OrderLine->StockID . "'
					AND custbranch.debtorno = '" . $_SESSION['Items'.$identifier]->DebtorNo . "'
					AND custbranch.branchcode='" . $_SESSION['Items'.$identifier]->Branch . "'";
			}

			$ErrMsg = _('Sales analysis record could not be added or updated because');
			$DbgMsg = _('The following SQL to insert the sales analysis record was used');
			$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		/* If GLLink_Stock then insert GLTrans to credit stock and debit cost of sales at standard cost*/

			if ($_SESSION['CompanyRecord']['gllink_stock']==1 AND $OrderLine->StandardCost !=0){

		/*first the cost of sales entry*/

				$SQL = "INSERT INTO gltrans (	type,
												typeno,
												trandate,
												periodno,
												account,
												narrative,
												amount)
										VALUES ( 10,
												'" . $InvoiceNo . "',
												'" . $DefaultDispatchDate . "',
												'" . $PeriodNo . "',
												'" . GetCOGSGLAccount($Area, $OrderLine->StockID, $_SESSION['Items'.$identifier]->DefaultSalesType, $db) . "',
												'" . $_SESSION['Items'.$identifier]->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->Quantity . " @ " . $OrderLine->StandardCost . "',
												'" . $OrderLine->StandardCost * $OrderLine->Quantity . "')";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The cost of sales GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		/*now the stock entry*/
				$StockGLCode = GetStockGLCode($OrderLine->StockID,$db);

				$SQL = "INSERT INTO gltrans (type,
											typeno,
											trandate,
											periodno,
											account,
											narrative,
											amount )
										VALUES ( 10,
											'" . $InvoiceNo . "',
											'" . $DefaultDispatchDate . "',
											'" . $PeriodNo . "',
											'" . $StockGLCode['stockact'] . "',
											'" . $_SESSION['Items'.$identifier]->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->Quantity . " @ " . $OrderLine->StandardCost . "',
											'" . (-$OrderLine->StandardCost * $OrderLine->Quantity) . "')";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock side of the cost of sales GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
			} /* end of if GL and stock integrated and standard cost !=0 */

			if ($_SESSION['CompanyRecord']['gllink_debtors']==1 AND $OrderLine->Price !=0){

		//Post sales transaction to GL credit sales
				$SalesGLAccounts = GetSalesGLAccount($Area, $OrderLine->StockID, $_SESSION['Items'.$identifier]->DefaultSalesType, $db);
				
				$SubTotal1 = round($OrderLine->Quantity * $OrderLine->Price * (1 - $OrderLine->DiscountPercent),$_SESSION['Items'.$identifier]->CurrDecimalPlaces);
				foreach ($OrderLine->Taxes AS $Tax) {
				$SubTotal = ($SubTotal1/(1+$Tax->TaxRate));
				}

				$SQL = "INSERT INTO gltrans (type,
											typeno,
											trandate,
											periodno,
											account,
											narrative,
											amount )
										VALUES ( 10,
											'" . $InvoiceNo . "',
											'" . $DefaultDispatchDate . "',
											'" . $PeriodNo . "',
											'" . $SalesGLAccounts['salesglcode'] . "',
											'" . $_SESSION['Items'.$identifier]->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->Quantity . " @ " . $OrderLine->Price . "',
											'" . (-$SubTotal/$ExRate) . "')"; // original (-$OrderLine->Price * $OrderLine->Quantity/$ExRate)

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales GL posting could not be inserted because');
				$DbgMsg = '<br />' ._('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

				if ($OrderLine->DiscountPercent !=0){

					$SQL = "INSERT INTO gltrans (type,
												typeno,
												trandate,
												periodno,
												account,
												narrative,
												amount )
												VALUES ( 10,
													'" . $InvoiceNo . "',
													'" . $DefaultDispatchDate . "',
													'" . $PeriodNo . "',
													'" . $SalesGLAccounts['discountglcode'] . "',
													'" . $_SESSION['Items'.$identifier]->DebtorNo . " - " . $OrderLine->StockID . " @ " . ($OrderLine->DiscountPercent * 100) . "%',
													'" . ($OrderLine->Price * $OrderLine->Quantity * $OrderLine->DiscountPercent/$ExRate) . "')";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales discount GL posting could not be inserted because');
					$DbgMsg = _('The following SQL to insert the GLTrans record was used');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
				} /*end of if discount !=0 */
			} /*end of if sales integrated with debtors */
		} /*end of OrderLine loop */

		if ($_SESSION['CompanyRecord']['gllink_debtors']==1){

	/*Post debtors transaction to GL debit debtors, credit freight re-charged and credit sales */
			if (($_SESSION['Items'.$identifier]->total + filter_number_format($_POST['TaxTotal'])) !=0) {
				$SQL = "INSERT INTO gltrans (	type,
												typeno,
												trandate,
												periodno,
												account,
												narrative,
												amount	)
											VALUES ( 10,
												'" . $InvoiceNo . "',
												'" . $DefaultDispatchDate . "',
												'" . $PeriodNo . "',
												'" . $_SESSION['CompanyRecord']['debtorsact'] . "',
												'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
												'" . (($_SESSION['Items'.$identifier]->total + filter_number_format($_POST['TaxTotal']))/$ExRate) . "')";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The total debtor GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the total debtors control GLTrans record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
			}


			foreach ( $_SESSION['Items'.$identifier]->TaxTotals as $TaxAuthID => $TaxAmount){
				if ($TaxAmount !=0 ){
					$SQL = "INSERT INTO gltrans (	type,
													typeno,
													trandate,
													periodno,
													account,
													narrative,
													amount	)
												VALUES ( 10,
													'" . $InvoiceNo . "',
													'" . $DefaultDispatchDate . "',
													'" . $PeriodNo . "',
													'" . $_SESSION['Items'.$identifier]->TaxGLCodes[$TaxAuthID] . "',
													'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
													'" . (-$TaxAmount/$ExRate) . "')";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The tax GL posting could not be inserted because');
					$DbgMsg = _('The following SQL to insert the GLTrans record was used');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
				}
			}

			EnsureGLEntriesBalance(10,$InvoiceNo,$db);


		} /*end of if Sales and GL integrated */

		DB_Txn_Commit();
	// *************************************************************************
	//   E N D   O F   I N V O I C E   S Q L   P R O C E S S I N G
	// *************************************************************************

		unset($_SESSION['Items'.$identifier]->LineItems);
		unset($_SESSION['Items'.$identifier]);

		echo prnMsg( _('Invoice number'). ' '. $InvoiceNo .' '. _('processed'), 'success');

		echo '<br /><div class="centre">';

		//echo '<a target="_blank" href="'.$RootPath.'/ReceiptPrinter_Invoice.php?TransNo='.$InvoiceNo.'"> <img src="'.$RootPath.'/dist/Receipt-Printer_icon.png" title="' . _('Print this Invoice') . '" alt="" /></a><br /><br />';
	
	echo '<img src="'.$RootPath.'/css/'.$Theme.'/images/printer.png" title="' . _('Print') . '" alt="" />' . ' ' . '<a target="_blank" href="'.$RootPath.'/PrintCustOrder_PickingNote.php?TransNo='.$DeliveryNo.'">' .  _('Print Picking Note'). '</a><br /><br />';
		
		if ($_SESSION['InvoicePortraitFormat']==0){
			echo '<img src="'.$RootPath.'/css/'.$Theme.'/images/printer.png" title="' . _('Print') . '" alt="" />' . ' ' . '<a target="_blank" href="'.$RootPath.'/PrintCustTrans.php?FromTransNo='.$InvoiceNo.'&amp;InvOrCredit=Invoice&amp;PrintPDF=True">' .  _('Print this invoice'). ' (' . _('Landscape') . ')</a><br /><br />';
		} else {
			echo '<img src="'.$RootPath.'/css/'.$Theme.'/images/printer.png" title="' . _('Print') . '" alt="" />' . ' ' . '<a target="_blank" href="'.$RootPath.'/PrintCustTransPortrait.php?FromTransNo='.$InvoiceNo.'&amp;InvOrCredit=Invoice&amp;PrintPDF=True">' .  _('Print this invoice'). ' (' . _('Portrait') . ')</a><br /><br />';
		}
		
		echo '<br /><br /><a href="' .htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '"><img src="'.$RootPath.'/dist/sale.png" title="' . _('Start a new Counter Sale') . '" alt="" /></a></div>';
	
	exit;
	}
}


if (isset($_POST['ProcessReceiptInvoice']) AND $_POST['ProcessReceiptInvoice'] != ''){

if (filter_number_format($_POST['AmountPaid']) -(round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces))<0.00) {
		prnMsg(_('The amount entered as payment does not equal the amount of the invoice. Please ensure the customer has paid the correct amount and re-enter'),'error');
		$InputError = true;
	}elseif(filter_number_format($_POST['AmountPaid']) -(round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces))>=0.01){
	$Change = filter_number_format($_POST['AmountPaid']) -(round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces));
	$_POST['AmountPaid'] = (round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces));
	}
			/*Accumulate the total debtors credit including discount */
if ($InputError == false) {
			if (!isset($ReceiptNumber)){
				$ReceiptNumber = GetNextTransNo(12,$db);
			}
			$PeriodNo = GetPeriod(Date($_SESSION['DefaultDateFormat']), $db);
			$DefaultDispatchDate = Date('Y-m-d');
			$InvoiceNo = $_SESSION['Items'.$identifier]->InvoiceN;
			$DebtorTransID = $_SESSION['Items' . $identifier]->TransID;
			
			//First get the account currency that it has been banked into
			$result = DB_query("SELECT rate FROM currencies
								INNER JOIN bankaccounts
								ON currencies.currabrev=bankaccounts.currcode
								WHERE bankaccounts.accountcode='" . $_POST['BankAccount'] . "'");
			$myrow = DB_fetch_row($result);
			$BankAccountExRate = $myrow[0];
			
				$SQL="INSERT INTO gltrans (type,
											typeno,
											trandate,
											periodno,
											account,
											narrative,
											amount)
						VALUES (12,
							'" . $ReceiptNumber . "',
							'" . $DefaultDispatchDate . "',
							'" . $PeriodNo . "',
							'" . $_POST['BankAccount'] . "',
							'" . $_SESSION['Items'.$identifier]->LocationName . ' ' . _('Counter Sale') . ' ' . $InvoiceNo ."',
							'" . (filter_number_format($_POST['AmountPaid'])/$ExRate) . "')";
				$DbgMsg = _('The SQL that failed to insert the GL transaction for the bank account debit was');
				$ErrMsg = _('Cannot insert a GL transaction for the bank account debit');
				$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

				/* Now Credit Debtors account with receipt */
				$SQL="INSERT INTO gltrans ( type,
						typeno,
						trandate,
						periodno,
						account,
						narrative,
						amount)
				VALUES (12,
					'" . $ReceiptNumber . "',
					'" . $DefaultDispatchDate . "',
					'" . $PeriodNo . "',
					'" . $_SESSION['CompanyRecord']['debtorsact'] . "',
					'" . $_SESSION['Items'.$identifier]->LocationName . ' ' . _('Counter Sale') . ' ' . $InvoiceNo ."',
					'" . -(filter_number_format($_POST['AmountPaid'])/$ExRate) . "')";
				$DbgMsg = _('The SQL that failed to insert the GL transaction for the debtors account credit was');
				$ErrMsg = _('Cannot insert a GL transaction for the debtors account credit');
				$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
			
			EnsureGLEntriesBalance(12,$ReceiptNumber,$db);
			//Now need to add the receipt banktrans record
			
			if(isset($_SESSION['Items'.$identifier]->MpesaTransID)){
			$result = DB_query("INSERT INTO `mpesa_payments`(`ReceiptNo`, `TransID`, `TransTime`, `TransAmount`, `MSISDN`, `FirstName`, `MiddleName`, `LastName`, `OrgAccountBalance`) VALUES ('".$ReceiptNumber."','".$_SESSION['Items'.$identifier]->MpesaTransID."','".$_SESSION['Items'.$identifier]->MpesaDate."','".$_SESSION['Items'.$identifier]->MpesaAmt."','".$_SESSION['Items'.$identifier]->MpesaNo."','".$_SESSION['Items'.$identifier]->MpesaFName."','".$_SESSION['Items'.$identifier]->MpesaMName."','".$_SESSION['Items'.$identifier]->MpesaLName."','".$_SESSION['Items'.$identifier]->MpesaBal."')");
			}
			/*
			 * Some interesting exchange rate conversion going on here
			 * Say :
			 * The business's functional currency is NZD
			 * Customer location counter sales are in AUD - 1 NZD = 0.80 AUD
			 * Banking money into a USD account - 1 NZD = 0.68 USD
			 *
			 * Customer sale is for $100 AUD
			 * GL entries  conver the AUD 100 to NZD  - 100 AUD / 0.80 = $125 NZD
			 * Banktrans entries convert the AUD 100 to USD using 100/0.8 * 0.68
			*/

			//insert the banktrans record in the currency of the bank account
	foreach ($_SESSION['Pay'.$identifier]->LineItems as $Line) {
		if($Change >0 && $Line->PaymentType ==2){
		$Line->AmtPay = $Line->AmtPay-$Change;
		}
	
			$SQL="INSERT INTO banktrans (type,
						transno,
						bankact,
						ref,
						exrate,
						functionalexrate,
						transdate,
						banktranstype,
						amount,
						currcode)
					VALUES (12,
						'" . $ReceiptNumber . "',
						'" . $_POST['BankAccount'] . "',
						'" . $_SESSION['Items'.$identifier]->LocationName . ' ' . _('Counter Sale') . ' ' . $InvoiceNo . "',
						'" . $ExRate . "',
						'" . $BankAccountExRate . "',
						'" . $DefaultDispatchDate . "',
						'" . $Line->PaymentType . "',
						'" . filter_number_format($Line->AmtPay) * $BankAccountExRate . "',
						'" . $_SESSION['Items'.$identifier]->DefaultCurrency . "')";

			$DbgMsg = _('The SQL that failed to insert the bank account transaction was');
			$ErrMsg = _('Cannot insert a bank transaction');
			$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		}
			//insert a new debtortrans for the receipt

			$SQL = "INSERT INTO debtortrans (transno,
							type,
							debtorno,
							trandate,
							inputdate,
							prd,
							reference,
							rate,
							ovamount,
							alloc,
							invtext,
							settled,
							salesperson)
					VALUES ('" . $ReceiptNumber . "',
						12,
						'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
						'" . $DefaultDispatchDate . "',
						'" . date('Y-m-d H-i-s') . "',
						'" . $PeriodNo . "',
						'" . $InvoiceNo . "',
						'" . $ExRate . "',
						'" . (filter_number_format($_POST['AmountPaid'])*-1) . "',
						'" . (filter_number_format($_POST['AmountPaid'])*-1) . "',
						'" . _('Cash').' - '. $InvoiceNo ."',
						'1',
						'" . $_SESSION['Items'.$identifier]->SalesPerson . "')";

			$DbgMsg = _('The SQL that failed to insert the customer receipt transaction was');
			$ErrMsg = _('Cannot insert a receipt transaction against the customer because') ;
			$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

			$ReceiptDebtorTransID = DB_Last_Insert_ID($db,'debtortrans','id');

			$SQL = "UPDATE debtorsmaster SET lastpaiddate = '" . $DefaultDispatchDate . "',
											lastpaid='" . filter_number_format($_POST['AmountPaid']) . "'
									WHERE debtorsmaster.debtorno='" . $_SESSION['Items'.$identifier]->DebtorNo . "'";

			$DbgMsg = _('The SQL that failed to update the date of the last payment received was');
			$ErrMsg = _('Cannot update the customer record for the date of the last payment received because');
			$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

			//and finally add the allocation record between receipt and invoice

			$SQL = "INSERT INTO custallocns (	amt,
												datealloc,
												transid_allocfrom,
												transid_allocto )
									VALUES  ('" . filter_number_format($_POST['AmountPaid']) . "',
											'" . $DefaultDispatchDate . "',
											 '" . $ReceiptDebtorTransID . "',
											 '" . $DebtorTransID . "')";
			$DbgMsg = _('The SQL that failed to insert the allocation of the receipt to the invoice was');
			$ErrMsg = _('Cannot insert the customer allocation of the receipt to the invoice because');
			$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
			
			$Settled = 1;
			$SQL = "UPDATE debtortrans
					SET alloc = '" . filter_number_format($_POST['AmountPaid']) . "',
					settled = '" . $Settled . "'
					WHERE id = '" . $DebtorTransID."'";
			if( !$Result = DB_query($SQL) ) {
				$Error = _('Could not update difference on exchange');
			}
		// Update the receipt or credit note
		$SQL = "UPDATE debtortrans
					SET alloc = '" . -filter_number_format($_POST['AmountPaid']) . "',
					settled = '" . $Settled . "'
					WHERE id = '" . $ReceiptDebtorTransID."'";

		if( !$Result = DB_query($SQL) ) {
			$Error = _('Could not update receipt or credit note');
		}
			
	echo prnMsg( _('Receipt number'). ' '. $ReceiptNumber .' '. _('processed'), 'success');

		echo '<br /><div class="centre">';

		echo '<a target="_blank" href="'.$RootPath.'/ReceiptPrinter.php?TransNo='.$InvoiceNo.'&Change='.$Change.$mpesacode.$mpesaamt.'"> <img src="'.$RootPath.'/dist/Receipt-Printer_icon.png" title="' . _('Print this Receipt') . '" alt="" /></a><br /><br />';
		
		echo '<br /><br /><a href="' .htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '"><img src="'.$RootPath.'/dist/sale.png" title="' . _('Start a new Counter Sale') . '" alt="" /></a></div>';
	exit;		
		} //end if $_POST['AmountPaid']!= 0

}
 
if (isset($_POST['ProcessSale']) AND $_POST['ProcessSale'] != ''){
	$Change = 0;
	$Tendered = $_POST['AmountPaid'];
	$InputError = false; //always assume the best
	//but check for the worst
	if ($_SESSION['Items'.$identifier]->LineCounter == 0){
		prnMsg(_('There are no lines on this sale. Please enter lines to invoice first'),'error');
		$InputError = true;
	}
	/*if (abs(filter_number_format($_POST['AmountPaid']) -(round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces)))>=0.01) {
		prnMsg(_('The amount entered as payment does not equal the amount of the invoice. Please ensure the customer has paid the correct amount and re-enter'),'error');
		$InputError = true;
	}*/
	foreach ($_SESSION['Items'.$identifier]->LineItems as $OrderLine) {
	if ($OrderLine->Controlled==1){
		if(empty($OrderLine->SerialItems)){
		prnMsg(_('Only items entered with a positive quantity can be added to the sale'),'error');
		$InputError = true;
	}
	}
	}
	if (filter_number_format($_POST['AmountPaid']) -(round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces))<0.00) {
		prnMsg(_('The amount entered as payment does not equal the amount of the invoice. Please ensure the customer has paid the correct amount and re-enter'),'error');
		$InputError = true;
	}elseif(filter_number_format($_POST['AmountPaid']) -(round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces))>=0.01){
	$Change = filter_number_format($_POST['AmountPaid']) -(round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces));
	$_POST['AmountPaid'] = (round($_SESSION['Items'.$identifier]->total+filter_number_format($_POST['TaxTotal']),$_SESSION['Items'.$identifier]->CurrDecimalPlaces));
	}

	if ($_SESSION['ProhibitSaleBelowCost']==1){ // checks for negative stock after processing invoice
	//sadly this check does not combine quantities occuring twice on and order and each line is considered individually :-(
		$NFound = false;
		foreach ($_SESSION['Items'.$identifier]->LineItems as $OrderLine) {
			$SQL = "SELECT stockmaster.materialcost, description
		 			FROM stockmaster
					WHERE stockmaster.stockid='" . $OrderLine->StockID . "'";

			$ErrMsg = _('Could not retrieve the quantity left at the location once this order is invoiced (for the purposes of checking that stock will not go negative because)');
			$Results = DB_query($SQL,$ErrMsg);
			$CheckNRow = DB_fetch_array($Results);
				if ($CheckNRow['materialcost'] > $OrderLine->Price){
					prnMsg( _('Invoicing the selected order would result in a loss. The system parameters are set to prohibit sale below cost price from occurring. This invoice cannot be created until the price is corrected.'),'error',$OrderLine->StockID . ' ' . $CheckNRow['description'] . ' - ' . _('Sale below Cost Prohibited'));
					$NFound = true;
				}elseif($CheckNRow['materialcost'] == $OrderLine->Price){
				prnMsg( _('Price is same as Cost.'),'info',$OrderLine->StockID . ' ' . $CheckNRow['description'] . ' - ' . _('Sale made at Cost Price'));
				}

		} //end of loop around items on the order for negative check

		if ($NFound){
			$InputError = true;
		}

	}//end of testing for below cost
	
	if ($_SESSION['ProhibitNegativeStock']==1){ // checks for negative stock after processing invoice
	//sadly this check does not combine quantities occuring twice on and order and each line is considered individually :-(
		$NegativesFound = false;
		foreach ($_SESSION['Items'.$identifier]->LineItems as $OrderLine) {
			$SQL = "SELECT stockmaster.description,
					   		locstock.quantity,
					   		stockmaster.mbflag
		 			FROM locstock
		 			INNER JOIN stockmaster
					ON stockmaster.stockid=locstock.stockid
					WHERE stockmaster.stockid='" . $OrderLine->StockID . "'
					AND locstock.loccode='" . $_SESSION['Items'.$identifier]->Location . "'";

			$ErrMsg = _('Could not retrieve the quantity left at the location once this order is invoiced (for the purposes of checking that stock will not go negative because)');
			$Result = DB_query($SQL,$ErrMsg);
			$CheckNegRow = DB_fetch_array($Result);
			if ($CheckNegRow['mbflag']=='B' OR $CheckNegRow['mbflag']=='M'){
				if ($CheckNegRow['quantity'] < $OrderLine->Quantity){
					prnMsg( _('Invoicing the selected order would result in negative stock. The system parameters are set to prohibit negative stocks from occurring. This invoice cannot be created until the stock on hand is corrected.'),'error',$OrderLine->StockID . ' ' . $CheckNegRow['description'] . ' - ' . _('Negative Stock Prohibited'));
					$NegativesFound = true;
				}
			} else if ($CheckNegRow['mbflag']=='A') {

				/*Now look for assembly components that would go negative */
				$SQL = "SELECT bom.component,
							   stockmaster.description,
							   locstock.quantity-(" . $OrderLine->Quantity  . "*bom.quantity) AS qtyleft
						FROM bom
						INNER JOIN locstock
						ON bom.component=locstock.stockid
						INNER JOIN stockmaster
						ON stockmaster.stockid=bom.component
						WHERE bom.parent='" . $OrderLine->StockID . "'
						AND locstock.loccode='" . $_SESSION['Items'.$identifier]->Location . "'
                        AND bom.effectiveafter <= '" . date('Y-m-d') . "'
                        AND bom.effectiveto > '" . date('Y-m-d') . "'";

				$ErrMsg = _('Could not retrieve the component quantity left at the location once the assembly item on this order is invoiced (for the purposes of checking that stock will not go negative because)');
				$Result = DB_query($SQL,$ErrMsg);
				while ($NegRow = DB_fetch_array($Result)){
					if ($NegRow['qtyleft']<0){
						prnMsg(_('Invoicing the selected order would result in negative stock for a component of an assembly item on the order. The system parameters are set to prohibit negative stocks from occurring. This invoice cannot be created until the stock on hand is corrected.'),'error',$NegRow['component'] . ' ' . $NegRow['description'] . ' - ' . _('Negative Stock Prohibited'));
						$NegativesFound = true;
					} // end if negative would result
				} //loop around the components of an assembly item
			}//end if its an assembly item - check component stock

		} //end of loop around items on the order for negative check

		if ($NegativesFound){
			prnMsg(_('The parameter to prohibit negative stock is set and invoicing this sale would result in negative stock. No futher processing can be performed. Alter the sale first changing quantities or deleting lines which do not have sufficient stock.'),'error');
			$InputError = true;
		}

	}//end of testing for negative stocks


	if ($InputError == false) { //all good so let's get on with the processing

	/* Now Get the area where the sale is to from the branches table */

		$SQL = "SELECT area,
						defaultshipvia
				FROM custbranch
				WHERE custbranch.debtorno ='". $_SESSION['Items'.$identifier]->DebtorNo . "'
				AND custbranch.branchcode = '" . $_SESSION['Items'.$identifier]->Branch . "'";

		$ErrMsg = _('We were unable to load the area from the custbranch table where the sale is to ');
		$Result = DB_query($SQL, $ErrMsg);
		$myrow = DB_fetch_row($Result);
		$Area = $myrow[0];
		$DefaultShipVia = $myrow[1];
		DB_free_result($Result);

	/*company record read in on login with info on GL Links and debtors GL account*/

		if ($_SESSION['CompanyRecord']==0){
			/*The company data and preferences could not be retrieved for some reason */
			prnMsg( _('The company information and preferences could not be retrieved. See your system administrator'), 'error');
			include('includes/footer.inc');
			exit;
		}

	// *************************************************************************
	//   S T A R T   O F   I N V O I C E   S Q L   P R O C E S S I N G
	// *************************************************************************
		$result = DB_Txn_Begin();
	/*First add the order to the database - it only exists in the session currently! */
		$OrderNo = GetNextTransNo(30, $db);
		$InvoiceNo = GetNextTransNo(10, $db);
		$PeriodNo = GetPeriod(Date($_SESSION['DefaultDateFormat']), $db);

		$HeaderSQL = "INSERT INTO salesorders (	orderno,
												debtorno,
												branchcode,
												customerref,
												comments,
												orddate,
												ordertype,
												shipvia,
												deliverto,
												deladd1,
												contactphone,
												contactemail,
												fromstkloc,
												deliverydate,
												confirmeddate,
												deliverblind,
												salesperson)
											VALUES (
												'" . $OrderNo . "',
												'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
												'" . $_SESSION['Items'.$identifier]->Branch . "',
												'". $_SESSION['Items'.$identifier]->CustRef ."',
												'". $_SESSION['Items'.$identifier]->Comments ."',
												'" . Date('Y-m-d H:i') . "',
												'" . $_SESSION['Items'.$identifier]->DefaultSalesType . "',
												'" . $_SESSION['Items'.$identifier]->ShipVia . "',
												'". $_SESSION['Items'.$identifier]->DeliverTo . "',
												'" . _('Counter Sale') . "',
												'" . $_SESSION['Items'.$identifier]->PhoneNo . "',
												'" . $_SESSION['Items'.$identifier]->Email . "',
												'" . $_SESSION['Items'.$identifier]->Location ."',
												'" . Date('Y-m-d') . "',
												'" . Date('Y-m-d') . "',
												0,
												'" . $_SESSION['Items'.$identifier]->SalesPerson . "')";
		$DbgMsg = _('Trouble inserting the sales order header. The SQL that failed was');
		$ErrMsg = _('The order cannot be added because');
		$InsertQryResult = DB_query($HeaderSQL,$ErrMsg,$DbgMsg,true);

		$StartOf_LineItemsSQL = "INSERT INTO salesorderdetails (orderlineno,
																orderno,
																stkcode,
																unitprice,
																quantity,
																discountpercent,
																narrative,
																itemdue,
																actualdispatchdate,
																qtyinvoiced,
																completed)
															VALUES (";

		$DbgMsg = _('Trouble inserting a line of a sales order. The SQL that failed was');
		foreach ($_SESSION['Items'.$identifier]->LineItems as $StockItem) {

			$LineItemsSQL = $StartOf_LineItemsSQL .
					"'".$StockItem->LineNumber . "',
					'" . $OrderNo . "',
					'" . $StockItem->StockID . "',
					'". $StockItem->Price . "',
					'" . $StockItem->Quantity . "',
					'" . floatval($StockItem->DiscountPercent) . "',
					'" . $StockItem->Narrative . "',
					'" . Date('Y-m-d') . "',
					'" . Date('Y-m-d') . "',
					'" . $StockItem->Quantity . "',
					1)";

			$ErrMsg = _('Unable to add the sales order line');
			$Ins_LineItemResult = DB_query($LineItemsSQL,$ErrMsg,$DbgMsg,true);

			/*Now check to see if the item is manufactured
			 * 			and AutoCreateWOs is on
			 * 			and it is a real order (not just a quotation)*/

			if ($StockItem->MBflag=='M'
				AND $_SESSION['AutoCreateWOs']==1){ //oh yeah its all on!

				//now get the data required to test to see if we need to make a new WO
				$QOHResult = DB_query("SELECT SUM(quantity) FROM locstock WHERE stockid='" . $StockItem->StockID . "'");
				$QOHRow = DB_fetch_row($QOHResult);
				$QOH = $QOHRow[0];

				$SQL = "SELECT SUM(salesorderdetails.quantity - salesorderdetails.qtyinvoiced) AS qtydemand
						FROM salesorderdetails INNER JOIN salesorders
						ON salesorderdetails.orderno=salesorders.orderno
						WHERE salesorderdetails.stkcode = '" . $StockItem->StockID . "'
						AND salesorderdetails.completed = 0
						AND salesorders.quotation = 0";
				$DemandResult = DB_query($SQL);
				$DemandRow = DB_fetch_row($DemandResult);
				$QuantityDemand = $DemandRow[0];

				$SQL = "SELECT SUM((salesorderdetails.quantity-salesorderdetails.qtyinvoiced)*bom.quantity) AS dem
						FROM salesorderdetails INNER JOIN salesorders
						ON salesorderdetails.orderno=salesorders.orderno
						INNER JOIN bom
						ON salesorderdetails.stkcode=bom.parent
						INNER JOIN stockmaster
						ON stockmaster.stockid=bom.parent
						WHERE salesorderdetails.quantity-salesorderdetails.qtyinvoiced > 0
						AND bom.component='" . $StockItem->StockID . "'
						AND salesorderdetails.completed=0
						AND salesorders.quotation=0";
				$AssemblyDemandResult = DB_query($SQL);
				$AssemblyDemandRow = DB_fetch_row($AssemblyDemandResult);
				$QuantityAssemblyDemand = $AssemblyDemandRow[0];

				// Get the QOO due to Purchase orders for all locations. Function defined in SQL_CommonFunctions.inc
				$QuantityPurchOrders= GetQuantityOnOrderDueToPurchaseOrders($StockItem->StockID, '');
				// Get the QOO dues to Work Orders for all locations. Function defined in SQL_CommonFunctions.inc
				$QuantityWorkOrders = GetQuantityOnOrderDueToWorkOrders($StockItem->StockID, '');

				//Now we have the data - do we need to make any more?
				$ShortfallQuantity = $QOH-$QuantityDemand-$QuantityAssemblyDemand+$QuantityPurchOrders+$QuantityWorkOrders;

				if ($ShortfallQuantity < 0) { //then we need to make a work order
					//How many should the work order be for??
					if ($ShortfallQuantity + $StockItem->EOQ < 0){
						$WOQuantity = -$ShortfallQuantity;
					} else {
						$WOQuantity = $StockItem->EOQ;
					}

					$WONo = GetNextTransNo(40,$db);
					$ErrMsg = _('Unable to insert a new work order for the sales order item');
					$InsWOResult = DB_query("INSERT INTO workorders (wo,
													 loccode,
													 requiredby,
													 startdate)
									 VALUES ('" . $WONo . "',
											'" . $_SESSION['DefaultFactoryLocation'] . "',
											'" . Date('Y-m-d') . "',
											'" . Date('Y-m-d'). "')",
											$ErrMsg,
											$DbgMsg,
											true);
					//Need to get the latest BOM to roll up cost
					$CostResult = DB_query("SELECT SUM((materialcost+labourcost+overheadcost)*bom.quantity) AS cost
																	FROM stockmaster INNER JOIN bom
																	ON stockmaster.stockid=bom.component
																	WHERE bom.parent='" . $StockItem->StockID . "'
																	AND bom.loccode='" . $_SESSION['DefaultFactoryLocation'] . "'");
					$CostRow = DB_fetch_row($CostResult);
					if (is_null($CostRow[0]) OR $CostRow[0]==0){
						$Cost =0;
						prnMsg(_('In automatically creating a work order for') . ' ' . $StockItem->StockID . ' ' . _('an item on this sales order, the cost of this item as accumulated from the sum of the component costs is nil. This could be because there is no bill of material set up ... you may wish to double check this'),'warn');
					} else {
						$Cost = $CostRow[0];
					}

					// insert parent item info
					$sql = "INSERT INTO woitems (wo,
												 stockid,
												 qtyreqd,
												 stdcost)
									 VALUES ('" . $WONo . "',
											 '" . $StockItem->StockID . "',
											 '" . $WOQuantity . "',
											 '" . $Cost . "')";
					$ErrMsg = _('The work order item could not be added');
					$result = DB_query($sql,$ErrMsg,$DbgMsg,true);

					//Recursively insert real component requirements - see includes/SQL_CommonFunctions.in for function WoRealRequirements
					WoRealRequirements($db, $WONo, $_SESSION['DefaultFactoryLocation'], $StockItem->StockID);

					$FactoryManagerEmail = _('A new work order has been created for') .
										":\n" . $StockItem->StockID . ' - ' . $StockItem->ItemDescription . ' x ' . $WOQuantity . ' ' . $StockItem->Units .
										"\n" . _('These are for') . ' ' . $_SESSION['Items'.$identifier]->CustomerName . ' ' . _('there order ref') . ': '  . $_SESSION['Items'.$identifier]->CustRef . ' ' ._('our order number') . ': ' . $OrderNo;

					if ($StockItem->Serialised AND $StockItem->NextSerialNo>0){
						//then we must create the serial numbers for the new WO also
						$FactoryManagerEmail .= "\n" . _('The following serial numbers have been reserved for this work order') . ':';

						for ($i=0;$i<$WOQuantity;$i++){

							$result = DB_query("SELECT serialno FROM stockserialitems
													WHERE serialno='" . ($StockItem->NextSerialNo + $i) . "'
													AND stockid='" . $StockItem->StockID ."'");
							if (DB_num_rows($result)!=0){
								$WOQuantity++;
								prnMsg(($StockItem->NextSerialNo + $i) . ': ' . _('This automatically generated serial number already exists - it cannot be added to the work order'),'error');
							} else {
								$sql = "INSERT INTO woserialnos (wo,
																	stockid,
																	serialno)
														VALUES ('" . $WONo . "',
																'" . $StockItem->StockID . "',
																'" . ($StockItem->NextSerialNo + $i)	 . "')";
								$ErrMsg = _('The serial number for the work order item could not be added');
								$result = DB_query($sql,$ErrMsg,$DbgMsg,true);
								$FactoryManagerEmail .= "\n" . ($StockItem->NextSerialNo + $i);
							}
						} //end loop around creation of woserialnos
						$NewNextSerialNo = ($StockItem->NextSerialNo + $WOQuantity +1);
						$ErrMsg = _('Could not update the new next serial number for the item');
						$UpdateSQL="UPDATE stockmaster SET nextserialno='" . $NewNextSerialNo . "' WHERE stockid='" . $StockItem->StockID . "'";
						$UpdateNextSerialNoResult = DB_query($UpdateSQL,$ErrMsg,$DbgMsg,true);
					} // end if the item is serialised and nextserialno is set

					$EmailSubject = _('New Work Order Number') . ' ' . $WONo . ' ' . _('for') . ' ' . $StockItem->StockID . ' x ' . $WOQuantity;
					//Send email to the Factory Manager
					if($_SESSION['SmtpSetting']==0){
							mail($_SESSION['FactoryManagerEmail'],$EmailSubject,$FactoryManagerEmail);

					}else{
							include('includes/htmlMimeMail.php');
							$mail = new htmlMimeMail();
							$mail->setSubject($EmailSubject);
							$result = SendmailBySmtp($mail,array($_SESSION['FactoryManagerEmail']));
					}

				} //end if with this sales order there is a shortfall of stock - need to create the WO
			}//end if auto create WOs in on
		} /* end inserted line items into sales order details */

		prnMsg(_('Order Number') . ' ' . $OrderNo . ' ' . _('has been entered'),'success');

	/* End of insertion of new sales order */

	/*Now Get the next invoice number - GetNextTransNo() function in SQL_CommonFunctions
	 * GetPeriod() in includes/DateFunctions.inc */



		$DefaultDispatchDate = Date('Y-m-d');

	/*Update order header for invoice charged on */
		$SQL = "UPDATE salesorders SET comments = CONCAT(comments,'" . ' ' . _('Invoice') . ': ' . "','" . $InvoiceNo . "') WHERE orderno= '" . $OrderNo."'";

		$ErrMsg = _('CRITICAL ERROR') . ' ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales order header could not be updated with the invoice number');
		$DbgMsg = _('The following SQL to update the sales order was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

	/*Now insert the DebtorTrans */

		$SQL = "INSERT INTO debtortrans (transno,
										type,
										debtorno,
										branchcode,
										trandate,
										inputdate,
										prd,
										reference,
										tpe,
										order_,
										ovamount,
										ovgst,
										rate,
										invtext,
										shipvia,
										alloc,
										settled,
										salesperson )
			VALUES (
				'". $InvoiceNo . "',
				10,
				'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
				'" . $_SESSION['Items'.$identifier]->Branch . "',
				'" . $DefaultDispatchDate . "',
				'" . date('Y-m-d H-i-s') . "',
				'" . $PeriodNo . "',
				'" . $_SESSION['Items'.$identifier]->CustRef  . "',
				'" . $_SESSION['Items'.$identifier]->DefaultSalesType . "',
				'" . $OrderNo . "',
				'" . $_SESSION['Items'.$identifier]->total . "',
				'" . filter_number_format($_POST['TaxTotal']) . "',
				'" . $ExRate . "',
				'" . $_SESSION['Items'.$identifier]->Comments . "',
				'" . $_SESSION['Items'.$identifier]->ShipVia . "',
				'" . ($_SESSION['Items'.$identifier]->total + filter_number_format($_POST['TaxTotal'])) . "',
				'1',
				'" . $_SESSION['Items'.$identifier]->SalesPerson . "')";

		$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor transaction record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the debtor transaction record was used');
	 	$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		$DebtorTransID = DB_Last_Insert_ID($db,'debtortrans','id');

	/* Insert the tax totals for each tax authority where tax was charged on the invoice */
		foreach ($_SESSION['Items'.$identifier]->TaxTotals AS $TaxAuthID => $TaxAmount) {

			$SQL = "INSERT INTO debtortranstaxes (debtortransid,
													taxauthid,
													taxamount)
										VALUES ('" . $DebtorTransID . "',
											'" . $TaxAuthID . "',
											'" . $TaxAmount/$ExRate . "')";

			$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor transaction taxes records could not be inserted because');
			$DbgMsg = _('The following SQL to insert the debtor transaction taxes record was used');
	 		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		}

		//Loop around each item on the sale and process each in turn
		foreach ($_SESSION['Items'.$identifier]->LineItems as $OrderLine) {
			 /* Update location stock records if not a dummy stock item
			 need the MBFlag later too so save it to $MBFlag */
			$Result = DB_query("SELECT mbflag FROM stockmaster WHERE stockid = '" . $OrderLine->StockID . "'");
			$myrow = DB_fetch_row($Result);
			$MBFlag = $myrow[0];
			if ($MBFlag=='B' OR $MBFlag=='M') {
				$Assembly = False;

				/* Need to get the current location quantity
				will need it later for the stock movement */
				$SQL="SELECT locstock.quantity
								FROM locstock
								WHERE locstock.stockid='" . $OrderLine->StockID . "'
								AND loccode= '" . $_SESSION['Items'.$identifier]->Location . "'";
				$ErrMsg = _('WARNING') . ': ' . _('Could not retrieve current location stock');
				$Result = DB_query($SQL, $ErrMsg);

				if (DB_num_rows($Result)==1){
					$LocQtyRow = DB_fetch_row($Result);
					$QtyOnHandPrior = $LocQtyRow[0];
				} else {
					/* There must be some error this should never happen */
					$QtyOnHandPrior = 0;
				}

				$SQL = "UPDATE locstock
							SET quantity = locstock.quantity - " . $OrderLine->Quantity . "
							WHERE locstock.stockid = '" . $OrderLine->StockID . "'
							AND loccode = '" . $_SESSION['Items'.$identifier]->Location . "'";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Location stock record could not be updated because');
				$DbgMsg = _('The following SQL to update the location stock record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

			} else if ($MBFlag=='A'){ /* its an assembly */
				/*Need to get the BOM for this part and make
				stock moves for the components then update the Location stock balances */
				$Assembly=True;
				$StandardCost =0; /*To start with - accumulate the cost of the comoponents for use in journals later on */
				$SQL = "SELECT bom.component,
						bom.quantity,
						stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost AS standard
						FROM bom,
							stockmaster
						WHERE bom.component=stockmaster.stockid
						AND bom.parent='" . $OrderLine->StockID . "'
                        AND bom.effectiveafter <= '" . date('Y-m-d') . "'
                        AND bom.effectiveto > '" . date('Y-m-d') . "'";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Could not retrieve assembly components from the database for'). ' '. $OrderLine->StockID . _('because').' ';
				$DbgMsg = _('The SQL that failed was');
				$AssResult = DB_query($SQL,$ErrMsg,$DbgMsg,true);

				while ($AssParts = DB_fetch_array($AssResult)){

					$StandardCost += ($AssParts['standard'] * $AssParts['quantity']) ;
					/* Need to get the current location quantity
					will need it later for the stock movement */
					$SQL="SELECT locstock.quantity
									FROM locstock
									WHERE locstock.stockid='" . $AssParts['component'] . "'
									AND loccode= '" . $_SESSION['Items'.$identifier]->Location . "'";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Can not retrieve assembly components location stock quantities because ');
					$DbgMsg = _('The SQL that failed was');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
					if (DB_num_rows($Result)==1){
						$LocQtyRow = DB_fetch_row($Result);
						$QtyOnHandPrior = $LocQtyRow[0];
					} else {
						/*There must be some error this should never happen */
						$QtyOnHandPrior = 0;
					}
					if (empty($AssParts['standard'])) {
						$AssParts['standard']=0;
					}
					$SQL = "INSERT INTO stockmoves (stockid,
													type,
													transno,
													loccode,
													trandate,
													userid,
													debtorno,
													branchcode,
													prd,
													reference,
													qty,
													standardcost,
													show_on_inv_crds,
													newqoh)
										VALUES ('" . $AssParts['component'] . "',
												 10,
												'" . $InvoiceNo . "',
												'" . $_SESSION['Items'.$identifier]->Location . "',
												'" . $DefaultDispatchDate . "',
												'" . $_SESSION['UserID'] . "',
												'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
												'" . $_SESSION['Items'.$identifier]->Branch . "',
												'" . $PeriodNo . "',
												'" . _('Assembly') . ': ' . $OrderLine->StockID . ' ' . _('Order') . ': ' . $OrderNo . "',
												'" . -$AssParts['quantity'] * $OrderLine->Quantity . "',
												'" . $AssParts['standard'] . "',
												0,
												newqoh-" . ($AssParts['quantity'] * $OrderLine->Quantity) . " )";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records for the assembly components of'). ' '. $OrderLine->StockID . ' ' . _('could not be inserted because');
					$DbgMsg = _('The following SQL to insert the assembly components stock movement records was used');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);


					$SQL = "UPDATE locstock
							SET quantity = locstock.quantity - " . $AssParts['quantity'] * $OrderLine->Quantity . "
							WHERE locstock.stockid = '" . $AssParts['component'] . "'
							AND loccode = '" . $_SESSION['Items'.$identifier]->Location . "'";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Location stock record could not be updated for an assembly component because');
					$DbgMsg = _('The following SQL to update the locations stock record for the component was used');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
				} /* end of assembly explosion and updates */

				/*Update the cart with the recalculated standard cost from the explosion of the assembly's components*/
				$_SESSION['Items'.$identifier]->LineItems[$OrderLine->LineNumber]->StandardCost = $StandardCost;
				$OrderLine->StandardCost = $StandardCost;
			} /* end of its an assembly */

			// Insert stock movements - with unit cost
			$LocalCurrencyPrice = ($OrderLine->Price / $ExRate);

			if (empty($OrderLine->StandardCost)) {
				$OrderLine->StandardCost=0;
			}
			if ($MBFlag=='B' OR $MBFlag=='M'){
				$SQL = "INSERT INTO stockmoves (stockid,
												type,
												transno,
												loccode,
												trandate,
												userid,
												debtorno,
												branchcode,
												price,
												prd,
												reference,
												qty,
												discountpercent,
												standardcost,
												newqoh,
												narrative )
						VALUES ('" . $OrderLine->StockID . "',
								10,
								'" . $InvoiceNo . "',
								'" . $_SESSION['Items'.$identifier]->Location . "',
								'" . $DefaultDispatchDate . "',
								'" . $_SESSION['UserID'] . "',
								'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
								'" . $_SESSION['Items'.$identifier]->Branch . "',
								'" . $LocalCurrencyPrice . "',
								'" . $PeriodNo . "',
								'" . $OrderNo . "',
								'" . -$OrderLine->Quantity . "',
								'" . $OrderLine->DiscountPercent . "',
								'" . $OrderLine->StandardCost . "',
								'" . ($QtyOnHandPrior - $OrderLine->Quantity) . "',
								'" . $OrderLine->Narrative . "' )";
			} else {
			// its an assembly or dummy and assemblies/dummies always have nil stock (by definition they are made up at the time of dispatch  so new qty on hand will be nil
				if (empty($OrderLine->StandardCost)) {
					$OrderLine->StandardCost = 0;
				}
				$SQL = "INSERT INTO stockmoves (stockid,
												type,
												transno,
												loccode,
												trandate,
												userid,
												debtorno,
												branchcode,
												price,
												prd,
												reference,
												qty,
												discountpercent,
												standardcost,
												narrative )
						VALUES ('" . $OrderLine->StockID . "',
										10,
										'" . $InvoiceNo . "',
										'" . $_SESSION['Items'.$identifier]->Location . "',
										'" . $DefaultDispatchDate . "',
										'" . $_SESSION['UserID'] . "',
										'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
										'" . $_SESSION['Items'.$identifier]->Branch . "',
										'" . $LocalCurrencyPrice . "',
										'" . $PeriodNo . "',
										'" . $OrderNo . "',
										'" . -$OrderLine->Quantity . "',
										'" . $OrderLine->DiscountPercent . "',
										'" . $OrderLine->StandardCost . "',
										'" . $OrderLine->Narrative . "')";
			}

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Stock movement records could not be inserted because');
			$DbgMsg = _('The following SQL to insert the stock movement records was used');
			$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		/*Get the ID of the StockMove... */
			$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');

		/*Insert the taxes that applied to this line */
			foreach ($OrderLine->Taxes as $Tax) {

				$SQL = "INSERT INTO stockmovestaxes (stkmoveno,
									taxauthid,
									taxrate,
									taxcalculationorder,
									taxontax)
						VALUES ('" . $StkMoveNo . "',
							'" . $Tax->TaxAuthID . "',
							'" . $Tax->TaxRate . "',
							'" . $Tax->TaxCalculationOrder . "',
							'" . $Tax->TaxOnTax . "')";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Taxes and rates applicable to this invoice line item could not be inserted because');
				$DbgMsg = _('The following SQL to insert the stock movement tax detail records was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
			} //end for each tax for the line

			// Controlled stuff not currently handled by counter orders

			//Insert the StockSerialMovements and update the StockSerialItems  for controlled items

			if ($OrderLine->Controlled ==1){
				foreach($OrderLine->SerialItems as $Item){
								//We need to add the StockSerialItem record and the StockSerialMoves as well

					$SQL = "UPDATE stockserialitems
							SET quantity= quantity - " . $Item->BundleQty . "
							WHERE stockid='" . $OrderLine->StockID . "'
							AND loccode='" . $_SESSION['Items'.$identifier]->Location . "'
							AND serialno='" . $Item->BundleRef . "'";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be updated because');
					$DbgMsg = _('The following SQL to update the serial stock item record was used');
					$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

					// now insert the serial stock movement

					$SQL = "INSERT INTO stockserialmoves (stockmoveno,
										stockid,
										serialno,
										moveqty)
						VALUES (" . $StkMoveNo . ",
							'" . $OrderLine->StockID . "',
							'" . $Item->BundleRef . "',
							" . -$Item->BundleQty . ")";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
					$DbgMsg = _('The following SQL to insert the serial stock movement records was used');
					$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
				}// foreach controlled item in the serialitems array
			} //end if the orderline is a controlled item

			//End of controlled stuff not currently handled by counter orders
			
			
			
			$SalesValue = 0;
			if ($ExRate>0){
				$SalesValue = $OrderLine->Price * $OrderLine->Quantity / $ExRate;
			}

		/*Insert Sales Analysis records */

			$SQL="SELECT COUNT(*),
					salesanalysis.stockid,
					salesanalysis.stkcategory,
					salesanalysis.cust,
					salesanalysis.custbranch,
					salesanalysis.area,
					salesanalysis.periodno,
					salesanalysis.typeabbrev,
					salesanalysis.salesperson
				FROM salesanalysis,
					custbranch,
					stockmaster
				WHERE salesanalysis.stkcategory=stockmaster.categoryid
				AND salesanalysis.stockid=stockmaster.stockid
				AND salesanalysis.cust=custbranch.debtorno
				AND salesanalysis.custbranch=custbranch.branchcode
				AND salesanalysis.area=custbranch.area
				AND salesanalysis.salesperson='" . $_SESSION['Items'.$identifier]->SalesPerson . "'
				AND salesanalysis.typeabbrev ='" . $_SESSION['Items'.$identifier]->DefaultSalesType . "'
				AND salesanalysis.periodno='" . $PeriodNo . "'
				AND salesanalysis.cust " . LIKE . " '" . $_SESSION['Items'.$identifier]->DebtorNo . "'
				AND salesanalysis.custbranch " . LIKE . " '" . $_SESSION['Items'.$identifier]->Branch . "'
				AND salesanalysis.stockid " . LIKE . " '" . $OrderLine->StockID . "'
				AND salesanalysis.budgetoractual=1
				GROUP BY salesanalysis.stockid,
					salesanalysis.stkcategory,
					salesanalysis.cust,
					salesanalysis.custbranch,
					salesanalysis.area,
					salesanalysis.periodno,
					salesanalysis.typeabbrev,
					salesanalysis.salesperson";

			$ErrMsg = _('The count of existing Sales analysis records could not run because');
			$DbgMsg = _('SQL to count the no of sales analysis records');
			$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

			$myrow = DB_fetch_row($Result);

			if ($myrow[0]>0){  /*Update the existing record that already exists */

				$SQL = "UPDATE salesanalysis
							SET amt=amt+" . ($SalesValue) . ",
								cost=cost+" . ($OrderLine->StandardCost * $OrderLine->Quantity) . ",
								qty=qty +" . $OrderLine->Quantity . ",
								disc=disc+" . ($OrderLine->DiscountPercent * $SalesValue) . "
							WHERE salesanalysis.area='" . $myrow[5] . "'
							AND salesanalysis.salesperson='" . $_SESSION['Items'.$identifier]->SalesPerson . "'
							AND typeabbrev ='" . $_SESSION['Items'.$identifier]->DefaultSalesType . "'
							AND periodno = '" . $PeriodNo . "'
							AND cust " . LIKE . " '" . $_SESSION['Items'.$identifier]->DebtorNo . "'
							AND custbranch " . LIKE . " '" . $_SESSION['Items'.$identifier]->Branch . "'
							AND stockid " . LIKE . " '" . $OrderLine->StockID . "'
							AND salesanalysis.stkcategory ='" . $myrow[2] . "'
							AND budgetoractual=1";

			} else { /* insert a new sales analysis record */

				$SQL = "INSERT INTO salesanalysis (	typeabbrev,
													periodno,
													amt,
													cost,
													cust,
													custbranch,
													qty,
													disc,
													stockid,
													area,
													budgetoractual,
													salesperson,
													stkcategory	)
					SELECT '" . $_SESSION['Items'.$identifier]->DefaultSalesType . "',
						'" . $PeriodNo . "',
						'" . ($SalesValue) . "',
						'" . ($OrderLine->StandardCost * $OrderLine->Quantity) . "',
						'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
						'" . $_SESSION['Items'.$identifier]->Branch . "',
						'" . $OrderLine->Quantity . "',
						'" . ($OrderLine->DiscountPercent * $SalesValue) . "',
						'" . $OrderLine->StockID . "',
						custbranch.area,
						1,
						'" . $_SESSION['Items'.$identifier]->SalesPerson . "',
						stockmaster.categoryid
					FROM stockmaster,
						custbranch
					WHERE stockmaster.stockid = '" . $OrderLine->StockID . "'
					AND custbranch.debtorno = '" . $_SESSION['Items'.$identifier]->DebtorNo . "'
					AND custbranch.branchcode='" . $_SESSION['Items'.$identifier]->Branch . "'";
			}

			$ErrMsg = _('Sales analysis record could not be added or updated because');
			$DbgMsg = _('The following SQL to insert the sales analysis record was used');
			$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		/* If GLLink_Stock then insert GLTrans to credit stock and debit cost of sales at standard cost*/

			if ($_SESSION['CompanyRecord']['gllink_stock']==1 AND $OrderLine->StandardCost !=0){

		/*first the cost of sales entry*/

				$SQL = "INSERT INTO gltrans (	type,
												typeno,
												trandate,
												periodno,
												account,
												narrative,
												amount)
										VALUES ( 10,
												'" . $InvoiceNo . "',
												'" . $DefaultDispatchDate . "',
												'" . $PeriodNo . "',
												'" . GetCOGSGLAccount($Area, $OrderLine->StockID, $_SESSION['Items'.$identifier]->DefaultSalesType, $db) . "',
												'" . $_SESSION['Items'.$identifier]->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->Quantity . " @ " . $OrderLine->StandardCost . "',
												'" . $OrderLine->StandardCost * $OrderLine->Quantity . "')";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The cost of sales GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		/*now the stock entry*/
				$StockGLCode = GetStockGLCode($OrderLine->StockID,$db);

				$SQL = "INSERT INTO gltrans (type,
											typeno,
											trandate,
											periodno,
											account,
											narrative,
											amount )
										VALUES ( 10,
											'" . $InvoiceNo . "',
											'" . $DefaultDispatchDate . "',
											'" . $PeriodNo . "',
											'" . $StockGLCode['stockact'] . "',
											'" . $_SESSION['Items'.$identifier]->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->Quantity . " @ " . $OrderLine->StandardCost . "',
											'" . (-$OrderLine->StandardCost * $OrderLine->Quantity) . "')";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock side of the cost of sales GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
			} /* end of if GL and stock integrated and standard cost !=0 */

			if ($_SESSION['CompanyRecord']['gllink_debtors']==1 AND $OrderLine->Price !=0){

		//Post sales transaction to GL credit sales
				$SalesGLAccounts = GetSalesGLAccount($Area, $OrderLine->StockID, $_SESSION['Items'.$identifier]->DefaultSalesType, $db);
				
				$SubTotal1 = round($OrderLine->Quantity * $OrderLine->Price * (1 - $OrderLine->DiscountPercent),$_SESSION['Items'.$identifier]->CurrDecimalPlaces);
				foreach ($OrderLine->Taxes AS $Tax) {
				$SubTotal = ($SubTotal1/(1+$Tax->TaxRate));
				}

				$SQL = "INSERT INTO gltrans (type,
											typeno,
											trandate,
											periodno,
											account,
											narrative,
											amount )
										VALUES ( 10,
											'" . $InvoiceNo . "',
											'" . $DefaultDispatchDate . "',
											'" . $PeriodNo . "',
											'" . $SalesGLAccounts['salesglcode'] . "',
											'" . $_SESSION['Items'.$identifier]->DebtorNo . " - " . $OrderLine->StockID . " x " . $OrderLine->Quantity . " @ " . $OrderLine->Price . "',
											'" . (-$SubTotal/$ExRate) . "')"; // original (-$OrderLine->Price * $OrderLine->Quantity/$ExRate)

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales GL posting could not be inserted because');
				$DbgMsg = '<br />' ._('The following SQL to insert the GLTrans record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

				if ($OrderLine->DiscountPercent !=0){

					$SQL = "INSERT INTO gltrans (type,
												typeno,
												trandate,
												periodno,
												account,
												narrative,
												amount )
												VALUES ( 10,
													'" . $InvoiceNo . "',
													'" . $DefaultDispatchDate . "',
													'" . $PeriodNo . "',
													'" . $SalesGLAccounts['discountglcode'] . "',
													'" . $_SESSION['Items'.$identifier]->DebtorNo . " - " . $OrderLine->StockID . " @ " . ($OrderLine->DiscountPercent * 100) . "%',
													'" . ($OrderLine->Price * $OrderLine->Quantity * $OrderLine->DiscountPercent/$ExRate) . "')";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The sales discount GL posting could not be inserted because');
					$DbgMsg = _('The following SQL to insert the GLTrans record was used');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
				} /*end of if discount !=0 */
			} /*end of if sales integrated with debtors */
		} /*end of OrderLine loop */

		if ($_SESSION['CompanyRecord']['gllink_debtors']==1){

	/*Post debtors transaction to GL debit debtors, credit freight re-charged and credit sales */
			if (($_SESSION['Items'.$identifier]->total + filter_number_format($_POST['TaxTotal'])) !=0) {
				$SQL = "INSERT INTO gltrans (	type,
												typeno,
												trandate,
												periodno,
												account,
												narrative,
												amount	)
											VALUES ( 10,
												'" . $InvoiceNo . "',
												'" . $DefaultDispatchDate . "',
												'" . $PeriodNo . "',
												'" . $_SESSION['CompanyRecord']['debtorsact'] . "',
												'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
												'" . (($_SESSION['Items'.$identifier]->total + filter_number_format($_POST['TaxTotal']))/$ExRate) . "')";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The total debtor GL posting could not be inserted because');
				$DbgMsg = _('The following SQL to insert the total debtors control GLTrans record was used');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
			}


			foreach ( $_SESSION['Items'.$identifier]->TaxTotals as $TaxAuthID => $TaxAmount){
				if ($TaxAmount !=0 ){
					$SQL = "INSERT INTO gltrans (	type,
													typeno,
													trandate,
													periodno,
													account,
													narrative,
													amount	)
												VALUES ( 10,
													'" . $InvoiceNo . "',
													'" . $DefaultDispatchDate . "',
													'" . $PeriodNo . "',
													'" . $_SESSION['Items'.$identifier]->TaxGLCodes[$TaxAuthID] . "',
													'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
													'" . (-$TaxAmount/$ExRate) . "')";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The tax GL posting could not be inserted because');
					$DbgMsg = _('The following SQL to insert the GLTrans record was used');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
				}
			}

			EnsureGLEntriesBalance(10,$InvoiceNo,$db);

			/*Also if GL is linked to debtors need to process the debit to bank and credit to debtors for the payment */
			/*Need to figure out the cross rate between customer currency and bank account currency */

			if ($_POST['AmountPaid']!=0){
				$ReceiptNumber = GetNextTransNo(12,$db);
				$SQL="INSERT INTO gltrans (type,
											typeno,
											trandate,
											periodno,
											account,
											narrative,
											amount)
						VALUES (12,
							'" . $ReceiptNumber . "',
							'" . $DefaultDispatchDate . "',
							'" . $PeriodNo . "',
							'" . $_POST['BankAccount'] . "',
							'" . $_SESSION['Items'.$identifier]->LocationName . ' ' . _('Counter Sale') . ' ' . $InvoiceNo . "',
							'" . (filter_number_format($_POST['AmountPaid'])/$ExRate) . "')";
				$DbgMsg = _('The SQL that failed to insert the GL transaction for the bank account debit was');
				$ErrMsg = _('Cannot insert a GL transaction for the bank account debit');
				$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

				/* Now Credit Debtors account with receipt */
				$SQL="INSERT INTO gltrans ( type,
						typeno,
						trandate,
						periodno,
						account,
						narrative,
						amount)
				VALUES (12,
					'" . $ReceiptNumber . "',
					'" . $DefaultDispatchDate . "',
					'" . $PeriodNo . "',
					'" . $_SESSION['CompanyRecord']['debtorsact'] . "',
					'" . $_SESSION['Items'.$identifier]->LocationName . ' ' . _('Counter Sale') . ' ' . $InvoiceNo . "',
					'" . -(filter_number_format($_POST['AmountPaid'])/$ExRate) . "')";
				$DbgMsg = _('The SQL that failed to insert the GL transaction for the debtors account credit was');
				$ErrMsg = _('Cannot insert a GL transaction for the debtors account credit');
				$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
			}//amount paid we not zero

			EnsureGLEntriesBalance(12,$ReceiptNumber,$db);

		} /*end of if Sales and GL integrated */
		if ($_POST['AmountPaid']!=0){
			if (!isset($ReceiptNumber)){
				$ReceiptNumber = GetNextTransNo(12,$db);
			}
			//Now need to add the receipt banktrans record
			//First get the account currency that it has been banked into
			$result = DB_query("SELECT rate FROM currencies
								INNER JOIN bankaccounts
								ON currencies.currabrev=bankaccounts.currcode
								WHERE bankaccounts.accountcode='" . $_POST['BankAccount'] . "'");
			$myrow = DB_fetch_row($result);
			$BankAccountExRate = $myrow[0];
			
			if(isset($_SESSION['Items'.$identifier]->MpesaTransID)){
			$result = DB_query("INSERT INTO `mpesa_payments`(`ReceiptNo`, `TransID`, `TransTime`, `TransAmount`, `MSISDN`, `FirstName`, `MiddleName`, `LastName`, `OrgAccountBalance`) VALUES ('".$ReceiptNumber."','".$_SESSION['Items'.$identifier]->MpesaTransID."','".$_SESSION['Items'.$identifier]->MpesaDate."','".$_SESSION['Items'.$identifier]->MpesaAmt."','".$_SESSION['Items'.$identifier]->MpesaNo."','".$_SESSION['Items'.$identifier]->MpesaFName."','".$_SESSION['Items'.$identifier]->MpesaMName."','".$_SESSION['Items'.$identifier]->MpesaLName."','".$_SESSION['Items'.$identifier]->MpesaBal."')");
			}
			/*
			 * Some interesting exchange rate conversion going on here
			 * Say :
			 * The business's functional currency is NZD
			 * Customer location counter sales are in AUD - 1 NZD = 0.80 AUD
			 * Banking money into a USD account - 1 NZD = 0.68 USD
			 *
			 * Customer sale is for $100 AUD
			 * GL entries  conver the AUD 100 to NZD  - 100 AUD / 0.80 = $125 NZD
			 * Banktrans entries convert the AUD 100 to USD using 100/0.8 * 0.68
			*/

			//insert the banktrans record in the currency of the bank account
	foreach ($_SESSION['Pay'.$identifier]->LineItems as $Line) {
		if($Change >0 && $Line->PaymentType ==2){
		$Line->AmtPay = $Line->AmtPay-$Change;
		}
	
			$SQL="INSERT INTO banktrans (type,
						transno,
						bankact,
						ref,
						exrate,
						functionalexrate,
						transdate,
						banktranstype,
						amount,
						currcode)
					VALUES (12,
						'" . $ReceiptNumber . "',
						'" . $_POST['BankAccount'] . "',
						'" . $_SESSION['Items'.$identifier]->LocationName . ' ' . _('Counter Sale') . ' ' . $InvoiceNo . "',
						'" . $ExRate . "',
						'" . $BankAccountExRate . "',
						'" . $DefaultDispatchDate . "',
						'" . $Line->PaymentType . "',
						'" . filter_number_format($Line->AmtPay) * $BankAccountExRate . "',
						'" . $_SESSION['Items'.$identifier]->DefaultCurrency . "')";

			$DbgMsg = _('The SQL that failed to insert the bank account transaction was');
			$ErrMsg = _('Cannot insert a bank transaction');
			$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		}
			//insert a new debtortrans for the receipt

			$SQL = "INSERT INTO debtortrans (transno,
							type,
							debtorno,
							trandate,
							inputdate,
							prd,
							reference,
							rate,
							ovamount,
							alloc,
							invtext,
							settled,
							salesperson,
							tendered,
							change_bal)
					VALUES ('" . $ReceiptNumber . "',
						12,
						'" . $_SESSION['Items'.$identifier]->DebtorNo . "',
						'" . $DefaultDispatchDate . "',
						'" . date('Y-m-d H-i-s') . "',
						'" . $PeriodNo . "',
						'" . $InvoiceNo . "',
						'" . $ExRate . "',
						'" . -filter_number_format($_POST['AmountPaid']) . "',
						'" . -filter_number_format($_POST['AmountPaid']) . "',
						'" . $_SESSION['Items'.$identifier]->LocationName . ' ' . _('Counter Sale') ."',
						'1',
						'" . $_SESSION['Items'.$identifier]->SalesPerson . "',
						'".$Tendered."',
						'".$Change."')";

			$DbgMsg = _('The SQL that failed to insert the customer receipt transaction was');
			$ErrMsg = _('Cannot insert a receipt transaction against the customer because') ;
			$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

			$ReceiptDebtorTransID = DB_Last_Insert_ID($db,'debtortrans','id');

			$SQL = "UPDATE debtorsmaster SET lastpaiddate = '" . $DefaultDispatchDate . "',
											lastpaid='" . filter_number_format($_POST['AmountPaid']) . "'
									WHERE debtorsmaster.debtorno='" . $_SESSION['Items'.$identifier]->DebtorNo . "'";

			$DbgMsg = _('The SQL that failed to update the date of the last payment received was');
			$ErrMsg = _('Cannot update the customer record for the date of the last payment received because');
			$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

			//and finally add the allocation record between receipt and invoice

			$SQL = "INSERT INTO custallocns (	amt,
												datealloc,
												transid_allocfrom,
												transid_allocto )
									VALUES  ('" . filter_number_format($_POST['AmountPaid']) . "',
											'" . $DefaultDispatchDate . "',
											 '" . $ReceiptDebtorTransID . "',
											 '" . $DebtorTransID . "')";
			$DbgMsg = _('The SQL that failed to insert the allocation of the receipt to the invoice was');
			$ErrMsg = _('Cannot insert the customer allocation of the receipt to the invoice because');
			$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		} //end if $_POST['AmountPaid']!= 0

		DB_Txn_Commit();
	// *************************************************************************
	//   E N D   O F   I N V O I C E   S Q L   P R O C E S S I N G
	// *************************************************************************
	
		$mpesacode = isset($_SESSION['Items'.$identifier]->MpesaTransID) ? "&MpesaCode=".$_SESSION['Items'.$identifier]->MpesaTransID : '';
		$mpesaamt = isset($_SESSION['Items'.$identifier]->MpesaAmt) ? "&MpesaAmt=".$_SESSION['Items'.$identifier]->MpesaAmt : '';

		unset($_SESSION['Items'.$identifier]->LineItems);
		unset($_SESSION['Items'.$identifier]);

		echo prnMsg( _('Invoice number'). ' '. $InvoiceNo .' '. _('processed'), 'success');

		echo '<br /><div class="centre">';

		echo '<a target="_blank" href="'.$RootPath.'/ReceiptPrinter.php?TransNo='.$InvoiceNo.'&Change='.$Change.$mpesacode.$mpesaamt.'"> <img src="'.$RootPath.'/dist/Receipt-Printer_icon.png" title="' . _('Print this Receipt') . '" alt="" /></a><br /><br />';
		
		echo '<br /><br /><a href="' .htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '"><img src="'.$RootPath.'/dist/sale.png" title="' . _('Start a new Counter Sale') . '" alt="" /></a></div>';

	}else{
	unset($_POST['ProcessSale']);
	}
	// There were input errors so don't process nuffin
} else {
	//pretend the user never tried to commit the sale
	unset($_POST['ProcessSale']);
}
/*******************************
 * end of Invoice Processing
 * *****************************
*/


/* Now show the stock item selection search stuff below */
if (!isset($_POST['ProcessSale'])){
		 /* show the quick entry form variable */

		//echo '<div class="page_help_text"><b>' . _('Use this form to add items quickly if the item codes are already known') . '</b></div><br />';
        if (count($_SESSION['Items'.$identifier]->LineItems)==0) {
            echo '<input type="hidden" name="CustRef" value="' . $_SESSION['Items'.$identifier]->CustRef . '" />';
            echo '<input type="hidden" name="Comments" value="' . $_SESSION['Items'.$identifier]->Comments . '" />';
            echo '<input type="hidden" name="DeliverTo" value="' . $_SESSION['Items'.$identifier]->DeliverTo . '" />';
            echo '<input type="hidden" name="PhoneNo" value="' . $_SESSION['Items'.$identifier]->PhoneNo . '" />';
            echo '<input type="hidden" name="Email" value="' . $_SESSION['Items'.$identifier]->Email . '" />';
        }
		$DefaultDeliveryDate = DateAdd(Date($_SESSION['DefaultDateFormat']),'d',$_SESSION['Items'.$identifier]->DeliveryDays);
		
		?>

		<link rel="stylesheet" type="text/css" href="dist/bootswatch/flatly/bootstrap.min.css"/>

			<!--[if lte IE 8]>
		<link rel="stylesheet" media="print" href="dist/print.css" type="text/css" />
		<![endif]-->
		<!-- start mincss template tags -->
		<link rel="stylesheet" type="text/css" href="dist/jquery-ui/jquery-ui.min.css"/>
		<link rel="stylesheet" type="text/css" href="dist/opensourcepos.min.css?rel=9202eddfb2"/>
		<!-- end mincss template tags -->
		<!-- start minjs template tags -->
		<script type="text/javascript" src="dist/opensourcepos.min.js?rel=f6ce7fb090"></script>
		<!-- end minjs template tags -->


<body>
	<div class="wrapper">
		

		<div class="container">
			<div class="row">

<div id="register_wrapper">
<?php

$sql = "SELECT debtortrans.transno FROM debtortrans WHERE debtortrans.type=10 AND debtortrans.debtorno=200 ORDER BY transno DESC LIMIT 1";
	$result=DB_query($sql, '',  '',false, false);
	$myrow = DB_fetch_array($result);
?>
<!-- Top register controls -->

	<form action="" id="mode_form" class="form-horizontal panel panel-default" method="post" accept-charset="utf-8">  

	</form>
	<form action="" name="formless" id="add_item_form" class="form-horizontal panel panel-default" method="post" >
    <?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>      
		<div class="panel-body form-group">
		<span class="pull-left">
		<span style="font-size:26px; color:#0099FF;"><span class="glyphicon glyphicon-shopping-cart"></span> Sales Register</span>
		</span>
		
				<span class="pull-right">
					<a target="_blank" href="<?php echo $RootPath.'/AgedDebtors.php'; ?>" class="btn btn-success btn-sm" id="sales_takings_button" title="Top Sales"><span class="glyphicon glyphicon-print">&nbsp;</span>Debtor Analysis</a>&nbsp;&nbsp;</span>
				<span class="pull-right">
		<a target="_blank" href="<?php echo $RootPath.'/SelectCompletedOrder.php'; ?>" class="btn btn-primary btn-sm" id="sales_takings_button" title="Order Inquiry"><span class="glyphicon glyphicon-print">&nbsp;</span>Order Inquiry</a>&nbsp;&nbsp;</span>
				</span>&nbsp;&nbsp;
			
				<span class="pull-right">						
					<a target="_blank" href="<?php echo $RootPath.'/PDFCustTransListing.php'; ?>" class="btn btn-danger btn-sm" style=" color:#FFFFFF;" id="sales_takings_button" title="Transactions"><span class="glyphicon glyphicon-list-alt">&nbsp;</span>Transactions</a>&nbsp;&nbsp;</span>
				</span>
		</div>
		
				<div class="panel-body form-group">
				<span class="pull-left">
				Register Mode: 
		<?php
		if(isset($_POST['TRANSACTION'])){
		$_SESSION['Items'.$identifier]->TRANSACTION = $_POST['TRANSACTION'];
		} //'SALES'=>'NEW SALES','RETURN'=>'RETURN SALE'
		$array = array('SALES ORDER'=>'NEW SALES ORDER','INVOICE'=>'NEW INVOICE','INVOICE SALES ORDER'=>'INVOICE SALES ORDER');
		$array['RECEIPT INVOICE']='RECEIPT INVOICE';
	
		?>
		<select name="TRANSACTION" class="selectpicker show-menu-arrow" onChange="TransTypes();" data-style="btn-default btn-sm" data-width="fit">
		<?php
		foreach($array as $key=>$arr){
		echo '<option '.($key == $_SESSION['Items'.$identifier]->TRANSACTION ? 'selected' : '').' value="'.$key.'">'.$arr.'</option>';
		}
		?>
		</select>
		</span>
		<span class="pull-right">
		Store Location:
		<?php
		$sqlresult =DB_query("SELECT loccode,locationname FROM locations WHERE cashsalecustomer !=''");	
	echo '<select name="SelectedLoc" class="selectpicker show-menu-arrow" onChange="TransTypes();" data-style="btn-default btn-sm" data-width="fit">';
		while($row = DB_fetch_row($sqlresult)){
		echo '<option '.($row[0] == $_SESSION['Items'.$identifier]->Location ? 'selected' : '').' value="'.$row[0].'">'.$row[1].'</option>';
		}
	echo '</select>';
		?>
		</span>		
		</div>
	</form>
	
	<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?identifier='.$identifier ; ?>" name="form" id="add_item_form" class="form-horizontal panel panel-default" method="post" accept-charset="utf-8">
    <?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>    
		<div class="panel-body form-group">
			<ul>
			<?php if($_SESSION['Items'.$identifier]->TRANSACTION =="RETURN"){ ?>
			<li class="pull-left first_li">
					<label for="item" class='control-label'>Find or Scan Receipt</label>
				</li>
				<li class="pull-left">
					<input type="text" autocomplete="off" autofocus="autofocus" name="InvoiceNumber" value="<?php echo (isset($_SESSION['Items'.$identifier]->ReceiptN) ? $_SESSION['Items'.$identifier]->ReceiptN : ''); ?>" placeholder="Start typing Receipt Number.."  class="form-control input-sm" size="30" tabindex="1"  />
				</li>
				<li class="pull-left">
				<input type="submit" id="SearchReceipt" style="height:36px" class="btn btn-sm btn-success pull-right" name="SearchReceipt" value="Search Receipt" />
				</li>
				
			<?php 
			}elseif($_SESSION['Items'.$identifier]->TRANSACTION =="INVOICE SALES ORDER"){ 
			?>
			<li class="pull-left first_li">
					<label for="item" class='control-label'>Find or Scan Sales Order</label>
				</li>
				<li class="pull-left">
					<input type="text" autocomplete="off" autofocus="autofocus" name="OrderNumber" value="<?php echo (isset($_SESSION['Items'.$identifier]->OrderN) ? $_SESSION['Items'.$identifier]->OrderN : ''); ?>" placeholder="Start typing Sales Order Number.."  class="form-control input-sm" size="30" tabindex="1"  />
				</li>
				<li class="pull-left">
				<input type="submit" id="SearchOrder" style="height:36px" class="btn btn-sm btn-success pull-right" name="SearchOrder" value="Search Sales Order" />
				</li>
				
			<?php 
			}elseif($_SESSION['Items'.$identifier]->TRANSACTION =="RECEIPT INVOICE"){ 
			?>
			<li class="pull-left first_li">
					<label for="item" class='control-label'>Find or Scan Invoice</label>
				</li>
				<li class="pull-left">
					<input type="text" autocomplete="off" autofocus="autofocus" name="InvNumber" value="<?php echo (isset($_SESSION['Items'.$identifier]->InvoiceN) ? $_SESSION['Items'.$identifier]->InvoiceN : ''); ?>" placeholder="Start typing Invoice Number.."  class="form-control input-sm" size="30" tabindex="1"  />
				</li>
				<li class="pull-left">
				<input type="submit" id="SearchInv" style="height:36px" class="btn btn-sm btn-success pull-right" name="SearchInv" value="Search Invoice" />
				</li>
			
			<?php }else{ ?>
				<li class="pull-left first_li">
					<label for="item" class='control-label'>Find or Scan Item</label>
				</li>
				<li class="pull-left">
					<input type="text" autocomplete="off" autofocus="autofocus" name="barcode" value="" placeholder="Start typing Item Name or scan Barcode..." onKeyUp="AutoComplete();" id="item" class="form-control input-sm" size="50" tabindex="1"  />
					<span class="ui-helper-hidden-accessible" role="status"></span><div id="output"></div>
				</li>
				<?php } ?>
				<li class="pull-right">
					<a href="<?php echo $RootPath.'/Stocks.php'; ?>"><button type="button" class='btn btn-info btn-sm pull-right modal-dlg' title='New Item'> <span class="glyphicon glyphicon-tag">&nbsp</span>New Item</button></a>
				</li>
			</ul>
		</div>
	</form>

<!-- Sale Items List -->

	<table class="sales_table_100" style="border:none" id="register">
		<thead>
			<tr>
				<th style="width: 5%;">Del</th>
				<th style="width: 10%;">Item #</th>
				<th style="width: 35%;">Item Name</th>
				<th style="width: 10%;">Price</th>
				<th style="width: 10%;">QOH</th>
				<th style="width: 10%;">Quantity</th>
				<th style="width: 10%;">Disc %</th>
				<th style="width: 10%;">Total</th>
				<th style="width: 5%;">Update</th>
			</tr>
		</thead>

		<tbody id="cart_contents">
							<tr>
		<?php
		if (count($_SESSION['Items'.$identifier]->LineItems)>0 ){ /*only show order lines if there are any */
/*
// *************************************************************************
//   T H I S   W H E R E   T H E   S A L E  I S   D I S P L A Y E D
// *************************************************************************
*/
		
	$_SESSION['Items'.$identifier]->total = 0;
	$_SESSION['Items'.$identifier]->totalVolume = 0;
	$_SESSION['Items'.$identifier]->totalWeight = 0;
	$TaxTotals = array();
	$TaxGLCodes = array();
	$TaxTotal =0;
	$k =0;  //row colour counter
	foreach ($_SESSION['Items'.$identifier]->LineItems as $OrderLine) {
	$QOHResult = DB_query("SELECT sum(quantity)
						FROM locstock
						WHERE stockid = '" . $OrderLine->StockID . "' AND loccode='".$_SESSION['Items'.$identifier]->Location."'");
		$QOHRow = DB_fetch_row($QOHResult);
		$QOH = locale_number_format($QOHRow[0]);
		
		if ($OrderLine->Controlled==1){
		if(empty($OrderLine->SerialItems)){
			$_SESSION['Items'.$identifier]->LineItems[$OrderLine->LineNumber]->Quantity = 0;
			$OrderLine->Quantity =0;
			}
		}
		if($_POST['taxtype_' . $OrderLine->LineNumber . '']=='Excl'){
		$SubTotal = round($OrderLine->Quantity * $OrderLine->Price * (1 - $OrderLine->DiscountPercent),$_SESSION['Items'.$identifier]->CurrDecimalPlaces);
		}else{
		$SubTotal1 = round($OrderLine->Quantity * $OrderLine->Price * (1 - $OrderLine->DiscountPercent),$_SESSION['Items'.$identifier]->CurrDecimalPlaces);
		foreach ($OrderLine->Taxes AS $Tax) {
		$SubTotal = ($SubTotal1/(1+$Tax->TaxRate));
		}
		}		
		$DisplayDiscount = locale_number_format(($OrderLine->DiscountPercent * 100),2);
		$QtyOrdered = $OrderLine->Quantity;
		$QtyRemain = $QtyOrdered - $OrderLine->QtyInv;

		if ($OrderLine->QOHatLoc < $OrderLine->Quantity AND ($OrderLine->MBflag=='B' OR $OrderLine->MBflag=='M')) {
			/*There is a stock deficiency in the stock location selected */
			$RowStarter = '<tr style="background-color:#EEAABB">';
		} elseif ($k==1){
			$RowStarter = '<tr class="OddTableRows">';
			$k=0;
		} else {
			$RowStarter = '<tr class="EvenTableRows">';
			$k=1;
		}

		echo $RowStarter;
		echo '<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?identifier='.$identifier . '&amp;Delete=' . $OrderLine->LineNumber . '" onclick="return confirm(\'' . _('Are You Sure?') . '\');"><span class="glyphicon glyphicon-trash"></span></a></td>';
		echo '<td><input type="hidden" name="POLine_' .	 $OrderLine->LineNumber . '" value="" />';
		echo '<input type="hidden" name="ItemDue_' .	 $OrderLine->LineNumber . '" value="'.$OrderLine->ItemDue.'" />';

		echo '<a target="_blank" href="' . $RootPath . '/StockStatus.php?identifier='.$identifier . '&amp;StockID=' . $OrderLine->StockID . '&amp;DebtorNo=' . $_SESSION['Items'.$identifier]->DebtorNo . '">' . $OrderLine->StockID . '</a></td>';

		echo '<td title="' . $OrderLine->LongDescription . '">' . $OrderLine->ItemDescription . '</td>';

		echo '<td><input class="number" type="text" autocomplete="off" onBlur="Recalculate2();" onfocus="TextBox();" name="Price_' . $OrderLine->LineNumber . '" required="required" size="10" maxlength="16" value="' . locale_number_format($OrderLine->Price,$_SESSION['Items'.$identifier]->CurrDecimalPlaces) . '" /></td>';
		
		echo '<td>'.$QOH.'</td>';

		if ($OrderLine->Controlled==1){
		echo '<td class="number"><input type="hidden" autocomplete="off" name="Quantity_' . $OrderLine->LineNumber . '"  value="' . locale_number_format($OrderLine->Quantity,$OrderLine->DecimalPlaces) . '" /><a href="' . $RootPath .'/ConfirmDispatchControlled_CounterSale.php?identifier=' . $identifier . '&amp;LineNo='. $OrderLine->LineNumber.'">' .locale_number_format($OrderLine->Quantity,$OrderLine->DecimalPlaces) . '</a></td>';
		}else{
		echo '<td><input class="number" tabindex="2" autocomplete="off" onBlur="Recalculate2();" onfocus="TextBox();" type="text" name="Quantity_' . $OrderLine->LineNumber . '" required="required" size="5" maxlength="7" value="' . locale_number_format($OrderLine->Quantity,$OrderLine->DecimalPlaces) . '" /></td>';
		}

		if (in_array($_SESSION['PageSecurityArray']['OrderEntryDiscountPricing'], $_SESSION['AllowedPageSecurityTokens'])){
			echo '<td><input class="number" onBlur="Recalculate2();" onfocus="TextBox();" type="text" autocomplete="off" name="Discount_' . $OrderLine->LineNumber . '" required="required" size="5" maxlength="5" value="' . locale_number_format(($OrderLine->DiscountPercent * 100),$_SESSION['Items'.$identifier]->CurrDecimalPlaces) . '" />
			<input type="hidden" name="GPPercent_' . $OrderLine->LineNumber . '" value="' . $OrderLine->GPPercent . '" /></td>';
		} else {
			echo '
				<input type="hidden" name="Discount_' . $OrderLine->LineNumber . '" value="' . locale_number_format(($OrderLine->DiscountPercent * 100),$_SESSION['Items'.$identifier]->CurrDecimalPlaces) . '" />
				<input type="hidden" name="GPPercent_' . $OrderLine->LineNumber . '" value="' . locale_number_format($OrderLine->GPPercent,$_SESSION['Items'.$identifier]->CurrDecimalPlaces) . '" />';
		}
		//echo '<td class="number">' . locale_number_format($SubTotal,$_SESSION['Items'.$identifier]->CurrDecimalPlaces) . '</td>';
		$LineDueDate = $OrderLine->ItemDue;
		if (!Is_Date($OrderLine->ItemDue)){
			$LineDueDate = DateAdd (Date($_SESSION['DefaultDateFormat']),'d', $_SESSION['Items'.$identifier]->DeliveryDays);
			$_SESSION['Items'.$identifier]->LineItems[$OrderLine->LineNumber]->ItemDue= $LineDueDate;
		}
		$i=0; // initialise the number of taxes iterated through
		$TaxLineTotal =0; //initialise tax total for the line

		//Tax Exclusive
		if($_POST['taxtype_' . $OrderLine->LineNumber . '']=='Excl'){
		foreach ($OrderLine->Taxes AS $Tax) {
			if (empty($TaxTotals[$Tax->TaxAuthID])) {
				$TaxTotals[$Tax->TaxAuthID]=0;
			}
			if ($Tax->TaxOnTax ==1){
				$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate * ($SubTotal + $TaxLineTotal));
				$TaxLineTotal += ($Tax->TaxRate * ($SubTotal + $TaxLineTotal));
			} else {
				$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate * $SubTotal);
				$TaxLineTotal += ($Tax->TaxRate * $SubTotal);
			}
			$TaxGLCodes[$Tax->TaxAuthID] = $Tax->TaxGLCode;
		}
		} //Tax Exc End	
		//Start Inc Tax
		else{
		foreach ($OrderLine->Taxes AS $Tax) {
			if (empty($TaxTotals[$Tax->TaxAuthID])) {
				$TaxTotals[$Tax->TaxAuthID]=0;
			}
			if ($Tax->TaxOnTax ==1){
				$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate/(1+$Tax->TaxRate) * ($SubTotal1 + $TaxLineTotal));
				$TaxLineTotal += ($Tax->TaxRate/(1+$Tax->TaxRate) * ($SubTotal1 + $TaxLineTotal));
			} else {
				$TaxTotals[$Tax->TaxAuthID] += ($Tax->TaxRate/(1+$Tax->TaxRate) * $SubTotal1);
				$TaxLineTotal += ($Tax->TaxRate/(1+$Tax->TaxRate) * $SubTotal1);
			}
			$TaxGLCodes[$Tax->TaxAuthID] = $Tax->TaxGLCode;
		}
		} //End Inc 
		

		$TaxTotal += $TaxLineTotal;
		$_SESSION['Items'.$identifier]->TaxTotals=$TaxTotals;
		$_SESSION['Items'.$identifier]->TaxGLCodes=$TaxGLCodes;
		echo '<td class="number">' . locale_number_format($SubTotal + $TaxLineTotal ,$_SESSION['Items'.$identifier]->CurrDecimalPlaces) . '</td>';
		
		//echo '<form name="reculculateform" method="post">';
		//echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
		echo '<td> <a href="#" onclick="Recalculate();"><span class="glyphicon glyphicon-refresh"></span></a><input type="hidden" name="Recalculate" value="Re-Calculate" /></td></tr>';
		//echo '</form>';

		if ($_SESSION['AllowOrderLineItemNarrative'] == 1){
			echo $RowStarter;
			echo '<input type="hidden" name="Narrative" value="" />';
		} else {
			echo '<input type="hidden" name="Narrative" value="" />';
		}

		$_SESSION['Items'.$identifier]->total = $_SESSION['Items'.$identifier]->total + $SubTotal;
		$_SESSION['Items'.$identifier]->totalVolume = $_SESSION['Items'.$identifier]->totalVolume + $OrderLine->Quantity * $OrderLine->Volume;
		$_SESSION['Items'.$identifier]->totalWeight = $_SESSION['Items'.$identifier]->totalWeight + $OrderLine->Quantity * $OrderLine->Weight;

	} /* end of loop around items */

	echo '<input type="hidden" name="TaxTotal" value="'.$TaxTotal.'" />
			<input type="hidden" size="25" maxlength="25" name="DeliverTo" value="' . stripslashes($_SESSION['Items'.$identifier]->DeliverTo) . '" />
			<input type="hidden" size="25" maxlength="25" name="PhoneNo" value="' . stripslashes($_SESSION['Items'.$identifier]->PhoneNo) . '" />
			<input type="hidden" placeholder="contact@domain.com" size="25" maxlength="30" name="Email" value="' . stripslashes($_SESSION['Items'.$identifier]->Email) . '" />
			';


} # end of if lines
else{
echo "<td colspan='10'>
						<div class='alert alert-dismissible alert-info'>There are no Items in the cart.</div>
					</td>";
}
		?>
					
				</tr>
					</tbody>
	</table>
</div>

<!-- Overall Sale -->

<div id="overall_sale" class="panel panel-default">
	<div class="panel-body">

				<div class="form-group" id="select_customer">
				
				<?php //if($_SESSION['Items'.$identifier]->TRANSACTION =="SALES ORDER"){ ?>
				<form action="" name="form4" id="add_item_form" class="form-horizontal panel panel-default" method="post" accept-charset="utf-8">
				<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?> 
				<?php if($_SESSION['Items'.$identifier]->TRANSACTION !="RETURN"){ ?>
					<input type="text" autocomplete="off" name="customer" value="" placeholder="Start typing Customer Name..." onKeyUp="AutoCompleteCust();" id="customer" class="form-control input-sm" size="50" tabindex="1"  />
				<?php }?>
					<span class="ui-helper-hidden-accessible" role="status"></span><div id="outputcustomer"></div>
					<table style="width:100%; border-left:none; border-right:none;" class="sales_table_100" id="sale_totals">
			<tr><th style="width: 20%; text-align:left;">Delivered To:</th><th style="width: 55%; text-align:right;"><?php echo $_SESSION['Items'.$identifier]->DeliverTo; ?></th></tr>
			<tr><th style="text-align:left;">Address:</th><th style="width: 55%; text-align:right;"><?php echo $_SESSION['Items'.$identifier]->DelAdd1; ?></th></tr>
			<tr><th style="text-align:left;">Location:</th><th style="width: 55%; text-align:right;"><?php echo $_SESSION['Items'.$identifier]->LocationName ?></th></tr>
				</table>
				</form>
				<?php if($_SESSION['Items'.$identifier]->TRANSACTION =="INVOICE SALES ORDER" or $_SESSION['Items'.$identifier]->TRANSACTION =="INVOICE"){ ?>
				<strong>Comment:</strong> <input type="text" placeholder="Comment" size="20" maxlength="30" name="Comments" autocomplete="off" value="<?php echo stripcslashes($_SESSION['Items'.$identifier]->Comments); ?>" />
				<?php } ?>
				<?php if($_SESSION['Items'.$identifier]->TRANSACTION =="SALES ORDER"){ ?>
			<strong>Customer Ref:</strong> <input type="text" size="25" maxlength="25" name="CustRef" value="<?php echo stripcslashes($_SESSION['Items'.$identifier]->CustRef); ?>" /> 
			<?php
		echo '<strong>Sales Person: &nbsp;&nbsp;</strong> <select style="width:66%" name="SalesPerson">';
		$SalesPeopleResult = DB_query("SELECT salesmancode, salesmanname FROM salesman WHERE current=1");
		if (!isset($_POST['SalesPerson']) AND $_SESSION['SalesmanLogin']!=NULL ){
			$_SESSION['Items'.$identifier]->SalesPerson = $_SESSION['SalesmanLogin'];
		}

		while ($SalesPersonRow = DB_fetch_array($SalesPeopleResult)){
			if ($SalesPersonRow['salesmancode']==$_SESSION['Items'.$identifier]->SalesPerson){
				echo '<option selected="selected" value="' . $SalesPersonRow['salesmancode'] . '">' . $SalesPersonRow['salesmanname'] . '</option>';
			} else {
				echo '<option value="' . $SalesPersonRow['salesmancode'] . '">' . $SalesPersonRow['salesmanname'] . '</option>';
			}
		}

		echo '</select>';
		}
		?>
		
		<?php
					
		//}else{
		//echo '<label id="customer_label" for="customer" class="control-label" style="margin-bottom: 1em; margin-top: -1em;">Banked to (Optional)</label>';
					$BankAccountsResult = DB_query("SELECT bankaccountname, accountcode, bankaddress FROM bankaccounts");

	echo '<select name="BankAccount" class="selectpicker show-menu-arrow" data-style="btn-default btn-sm" data-width="fit">';
	while ($BankAccountsRow = DB_fetch_array($BankAccountsResult)){
		if (isset($_POST['BankAccount']) AND $_POST['BankAccount']	== $BankAccountsRow['accountcode']){
			echo '<option selected="selected" value="' . $BankAccountsRow['accountcode'] . '">' . $BankAccountsRow['bankaddress'] . '</option>';
		} else {
			echo '<option value="' . $BankAccountsRow['accountcode'] . '">' . $BankAccountsRow['bankaddress'] . '</option>';
		}
	}
	echo '</select>';
//}
					?>

				</div>	
			<?php if (count($_SESSION['Items'.$identifier]->LineItems)>0 ){ ?>
		<table style="width:100%; border-left:none; border-right:none;" class="sales_table_100" id="sale_totals">
			<tr>
				<th style="width: 55%; text-align:left;">Subtotal</th>
				<th style="width: 45%; text-align: right;"><?php echo locale_number_format(($_SESSION['Items'.$identifier]->total),$_SESSION['Items'.$identifier]->CurrDecimalPlaces); ?></th>
			</tr>
			
			<tr>
				<th style="width: 55%; text-align:left;"><?php echo $Tax->TaxRate*100 .'%'; ?></th>
				<th style="width: 45%; text-align: right;"><?php echo locale_number_format($TaxTotal,$_SESSION['Items'.$identifier]->CurrDecimalPlaces); ?></th>
			</tr>
			
			<tr>
				<th style='width: 55%; text-align:left;'>Total</th>
				<th style="width: 45%; text-align: right;"><span id="sale_total"><?php echo locale_number_format(($_SESSION['Items'.$identifier]->total+$TaxTotal),$_SESSION['Items'.$identifier]->CurrDecimalPlaces); ?></span></th>
			</tr>
			<tbody style="border-top:solid;"><tr>
					<th style="width: 55%;">Payments Total</th>
					<th style="width: 45%; text-align: right;">0.00</th>
				</tr>
				<tr>
					<th style="width: 55%;">Amount Due</th>
					<th style="width: 45%; text-align: right;"><span id="sale_amount_due"><?php echo locale_number_format(($_SESSION['Items'.$identifier]->total+$TaxTotal),$_SESSION['Items'.$identifier]->CurrDecimalPlaces); ?></span></th>
				</tr>
			</tbody>
		</table>
		<?php if($_SESSION['Items'.$identifier]->TRANSACTION =="SALES" or $_SESSION['Items'.$identifier]->TRANSACTION =="RECEIPT INVOICE"){ ?>
		<div id="payment_details">
						<table style="width:100%; border:none;" class="sales_table_100">
							<tbody>
							<?php
							if (!isset($_POST['PaymentMethod'])){
							$_POST['PaymentMethod'] =2;
							}
							echo '<td>Customer:</td><td><input type="text" placeholder="Customer Name" size="20" maxlength="30" name="Comments" autocomplete="off" value="' . stripcslashes($_SESSION['Items'.$identifier]->Comments) . '" /></td>';
							$PaymentMethodsResult = DB_query("SELECT paymentid, paymentname FROM paymentmethods");
							echo '<tr>
							<td>' . _('Payment Type') . ':</td>
							<td><select name="PaymentMethod" class="selectpicker show-menu-arrow" data-style="btn-default btn-sm" data-width="fit">'; //onChange="PaymentTypes();" for mpesa purposes
					while ($PaymentMethodRow = DB_fetch_array($PaymentMethodsResult)){
						if (isset($_POST['PaymentMethod']) AND $_POST['PaymentMethod'] == $PaymentMethodRow['paymentid']){
							echo '<option selected="selected" value="' . $PaymentMethodRow['paymentid'] . '">' . $PaymentMethodRow['paymentname'] . '</option>';
						} else {
							echo '<option value="' . $PaymentMethodRow['paymentid'] . '">' . $PaymentMethodRow['paymentname'] . '</option>';
						}
					}
					echo '</select></td>
						</tr>';
						$Tots = 0;
						foreach ($_SESSION['Pay'.$identifier]->LineItems as $Line) {
						$Tots += $Line->AmtPay;
						}
						$NowTemndered = ($_SESSION['Items'.$identifier]->total+$TaxTotal)-$Tots;	
						?>
							
							<tr>
							<?php //if(isset($_POST['PaymentMethod']) && $_POST['PaymentMethod']==5){ ?>
								<!--<td><span id="amount_tendered_label">Phone No.</span></td>
								<td>
								<input type="text" name="MpesaPhoneNo"  title="Enter the customer Phone number, this must be the number used to make the Mpesa transaction" value="" id="" class="form-control input-sm non-giftcard-input" size="5" tabindex="5">
								</td>-->
								<?php //}else{ ?>
								<td><span id="amount_tendered_label">Amount Tendered</span></td>
								<td>
								<input type="text" name="AmountPayment" required="required" autocomplete="off" title="Enter the amount paid by the customer, this must equal the amount of the sale" value="<?php echo locale_number_format(($NowTemndered < 0 ? 0:$NowTemndered),$_SESSION['Items'.$identifier]->CurrDecimalPlaces); ?>" id="amount_tendered" class="form-control input-sm non-giftcard-input" size="5" tabindex="5">
								</td>
								<?php //}?>
							</tr>
						</tbody></table>
						<?php if($Tots>0): ?>
<table style="width:100%;" class="sales_table_100">
							<thead>
							<tr id="register">
								<th></th>
								<th>Payment Type</th>
								<th>Amount</th>
							</tr>
						</thead>
						<tbody id="cart_contents">
						<?php
						foreach ($_SESSION['Pay'.$identifier]->LineItems as $Line) {
						?>
							<tr>
								<?php echo '<td width="20" height="20"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?identifier='.$identifier . '&amp;DelPay=' . $Line->LineNumber . '" onclick="return confirm(\'' . _('Are You Sure?') . '\');"><span class="glyphicon glyphicon-trash"></span></a></td>'; ?>
								<td height="20"><?php echo $Line->PaymentDescription; ?></td>
								<td class="number"><?php echo number_format($Line->AmtPay,2); ?></td>
							</tr>
						<?php } ?>
						<tr>
								<th></th>
								<th style="font-size:14px;">Total Payment:</th>
								<td class="number" style="height:20px; font-size:14px; border-top:solid;"><?php echo number_format($Tots,2); ?></td><input type="hidden" name="AmountPaid" required="required"  value="<?php echo $Tots; ?>" id="amount_tendered" />
							</tr>
						</tbody>
						</table>
						<?php if(isset($_SESSION['Items'.$identifier]->MpesaTransID) && $_SESSION['Items'.$identifier]->MpesaTransID !=''): ?>
						<table style="width:100%;" class="sales_table_100">
						<tr><th colspan="2"><center>MPESA TRANSACTION</center></th></tr>
						<tbody>
							<tr><td>Transaction No.</td><th><?php echo $_SESSION['Items'.$identifier]->MpesaTransID; ?></th></tr>
							<tr><td>Phone No.</td><th><?php echo $_SESSION['Items'.$identifier]->MpesaNo; ?></th></tr>
							<tr><td>Transaction No.</td><th><?php echo $_SESSION['Items'.$identifier]->MpesaFName.' '.$_SESSION['Items'.$identifier]->MpesaMName.' '.$_SESSION['Items'.$identifier]->MpesaLName; ?></th></tr>
						</tbody>
						</table>
						<?php endif; ?>
						<?php endif; ?>
				<input name="Payments" id="Payments" type="hidden" value="">
				<input type="button" onClick="Paymentsa();" id="Payments" style="height:36px" class="btn btn-sm btn-success pull-right" name="Payments" value="Add Payment" />
				
							</div>
		
	<?php 
		if(($_SESSION['Items'.$identifier]->total+$TaxTotal)-$Tots <1):
		if($_SESSION['Items'.$identifier]->TRANSACTION =="RECEIPT INVOICE"){
		echo '<input type="submit" id="ProcessSale" style="height:36px" class="btn btn-sm btn-success pull-right" name="ProcessReceiptInvoice" value="Accept and Process" />';
		}else{
		echo '<input type="submit" id="ProcessSale" style="height:36px" class="btn btn-sm btn-success pull-right" name="ProcessSale" value="Process The Sale" />';
		}
		endif;
		}elseif($_SESSION['Items'.$identifier]->TRANSACTION =="SALES ORDER"){
		echo '<input type="submit" id="ProcessSalesOrder" style="height:36px" class="btn btn-sm btn-success pull-right" name="ProcessSalesOrder" value="Process Sales Order" />';
		}elseif($_SESSION['Items'.$identifier]->TRANSACTION =="INVOICE"){
		echo '<input type="submit" id="ProcessInvoice" style="height:36px" class="btn btn-sm btn-success pull-right" name="ProcessInvoice" value="Process Invoice" />';
		}elseif($_SESSION['Items'.$identifier]->TRANSACTION =="INVOICE SALES ORDER"){
		echo '<input type="submit" id="ProcessInvoice" style="height:36px" class="btn btn-sm btn-success pull-right" name="ProcessOrderInvoice" value="Process Invoice" />';
		}elseif($_SESSION['Items'.$identifier]->TRANSACTION =="RETURN"){
		echo '<input type="hidden" name="AmountPaid" required="required"  value="'.($_SESSION['Items'.$identifier]->total+$TaxTotal).'" id="amount_tendered" />';
		echo '<input type="submit" id="ProcessReturn" style="height:36px" class="btn btn-sm btn-success pull-right" name="ProcessReturn" value="Process Return" />';
		}
  		echo '<input name="CancelOrder" type="hidden" id="CancelOrder" value=""><input type="button" style="height:36px" class="btn btn-sm btn-danger pull-left" name="CancelOrder" id="CancelOrder" value="' . _('Cancel Sale') . '" onclick="CancelSale();" />';

	}else{ ?>
	<table style="width:100%; border-left:none; border-right:none;" class="sales_table_100" id="sale_totals">
			<tbody style="border-top:solid;"><tr>
					<th style="width: 55%;">Payments Total</th>
					<th style="width: 45%; text-align: right;"><?php echo $_SESSION['Items'.$identifier]->DefaultCurrency .'.0.00'; ?></th>
				</tr>
				<tr>
					<th style="width: 55%;">Amount Due</th>
					<th style="width: 45%; text-align: right;"><span id="sale_amount_due"><?php echo $_SESSION['Items'.$identifier]->DefaultCurrency .'.0.00'; ?></span></th>
				</tr>
			</tbody>
		</table>
	
	<?php } ?>
			</div>
</div>
<script type="text/javascript" src = "js/jquery-1.9.1.js"></script>
<script type="text/javascript">
function AutoComplete() {
	var state_id = document.getElementsByName('barcode')[0].value;;  
	if (state_id.length > 0 ) { 
	 $.ajax({
			type: "GET",
			url: "Ajax_CounterSale.php",
			data: "Keywords="+state_id,
			cache: false,
			beforeSend: function () { 
				$('#output').html('<i class="fa fa-spinner fa-pulse fa-2x fa-fw">');
			},
			success: function(html) {    
				$("#output").html( html );
			}
		});
	}else{
	$("#output").html( '<div style="background:#FFFFFF;position: absolute; z-index: 99;min-width:376px;">No Items to display</div>' );
	}
}

function AutoCompleteCust() {
	var state_id = document.getElementsByName('customer')[0].value;;  
	if (state_id.length > 0 ) { 
	 $.ajax({
			type: "GET",
			url: "Ajax_Customer.php",
			data: "Keywords="+state_id,
			cache: false,
			beforeSend: function () { 
				$('#outputcustomer').html('<i class="fa fa-spinner fa-pulse fa-2x fa-fw">');
			},
			success: function(html) {    
				$("#outputcustomer").html( html );
			}
		});
	}else{
	$("#outputcustomer").html( '<div style="background:#FFFFFF;position: absolute; z-index: 99;min-width:300px;">No Customer to display</div>' );
	}
}

function Paymentsa(){
		document.getElementById('Payments').value= "AddPayments";
		document.reculculateform.action='<?php echo htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?identifier='.$identifier ; ?>';
		document.reculculateform.submit();
	}
	
function CancelSale(){
		if(confirm('Are you sure you wish to cancel this sale?')){
		document.getElementById('CancelOrder').value= "CancelSale";
		document.reculculateform.action='<?php echo htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?identifier='.$identifier ; ?>';
		document.reculculateform.submit();
		}
	}

function customer(id, branchcode){
		document.form4.SelectedCustomer.value= id;
		document.form4.SelectedBranch.value= branchcode;
		document.form4.action='<?php echo htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?identifier='.$identifier ; ?>';
		document.form4.submit();
		//$(".edithide").hide();
	}

function edit(id){
		document.form.part_1.value= id;
		document.form.action='<?php echo htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?identifier='.$identifier ; ?>';
		document.form.submit();
		//$(".edithide").hide();
	}

function Recalculate(){
		document.reculculateform.action='<?php echo htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?identifier='.$identifier ; ?>';
		document.reculculateform.submit();
		//$(".edithide").hide();
	}
function Recalculate2(){
		document.reculculateform.action='<?php echo htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?identifier='.$identifier ; ?>';
		document.reculculateform.submit();
		//$(".edithide").hide();
	}
function TextBox() {
   document.getElementById("ProcessSale").style.display = "none";
}
function TransTypes(){
		document.formless.action='<?php echo htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?identifier='.$identifier ; ?>';
		document.formless.submit();
		//$(".edithide").hide();
	}
	
function PaymentTypes(){
		document.reculculateform.action='<?php echo htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?identifier='.$identifier ; ?>';
		document.reculculateform.submit();
		//$(".edithide").hide();
	}
	
$(document).ready(function() {
    $("input:text").focus(function() { $(this).select(); } );
});
</script>

		</div>
	</div>	
</body>
		
<?php
  	
echo '</form>';
}
//include('includes/footer.inc');
function GetCustBranchDetails($identifier) {
		global $db;
		$sql = "SELECT custbranch.brname,
						custbranch.branchcode,
						custbranch.braddress1,
						custbranch.braddress2,
						custbranch.braddress3,
						custbranch.braddress4,
						custbranch.braddress5,
						custbranch.braddress6,
						custbranch.phoneno,
						custbranch.email,
						custbranch.defaultlocation,
						custbranch.defaultshipvia,
						custbranch.deliverblind,
						custbranch.specialinstructions,
						custbranch.estdeliverydays,
						locations.locationname,
						custbranch.salesman
					FROM custbranch
					INNER JOIN locations
					ON custbranch.defaultlocation=locations.loccode
					WHERE custbranch.branchcode='" . $_SESSION['Items'.$identifier]->Branch . "'
					AND custbranch.debtorno = '" . $_SESSION['Items'.$identifier]->DebtorNo . "'";

		$ErrMsg = _('The customer branch record of the customer selected') . ': ' . $_SESSION['Items'.$identifier]->DebtorNo . ' ' . _('cannot be retrieved because');
		$DbgMsg = _('SQL used to retrieve the branch details was') . ':';
		$result = DB_query($sql,$ErrMsg,$DbgMsg);
		return $result;
}

?>
<div style="height:300px;"></div>