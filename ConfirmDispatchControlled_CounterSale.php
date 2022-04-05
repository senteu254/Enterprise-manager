<?php

/* $Id: ConfirmDispatchControlled_Invoice.php 6409 2013-11-18 07:53:20Z exsonqu $*/

include('includes/DefineCartClass.php');
include('includes/DefineSerialItems.php');
include('includes/session.inc');
$Title = _('Specify Dispatched Controlled Items');

/* Session started in header.inc for password checking and authorisation level check */
include('includes/header.inc');


if (empty($_GET['identifier'])) {
	/*unique session identifier to ensure that there is no conflict with other order entry sessions on the same machine  */
	$identifier=date('U');
} else {
	$identifier=$_GET['identifier'];
}

if (isset($_GET['LineNo'])){
        $LineNo = (int)$_GET['LineNo'];
} elseif (isset($_POST['LineNo'])){
        $LineNo = (int)$_POST['LineNo'];
} else {
	echo '<div class="centre">
			<a href="' . $RootPath . '/CounterSales.php">' .  _('Back to Counter Sales') . '</a>
			<br />
			<br />';
	prnMsg( _('This page can only be opened if a line item on a sales order to be invoiced has been selected') . '. ' . _('Please do that first'),'error');
	echo '</div>';
	include('includes/footer.inc');
	exit;
}

if (!isset($_SESSION['Items'.$identifier])) {
	/* This page can only be called with a sales order number to invoice */
	echo '<div class="centre">
			<a href="' . $RootPath . '/CounterSales.php">' .  _('Back to Counter Sales') . '</a>
			<br />';
	prnMsg( _('This page can only be opened if a sales order and line item has been selected Please do that first'),'error');
	echo '</div>';
	include('includes/footer.inc');
	exit;
}


/*Save some typing by referring to the line item class object in short form */
$LineItem = &$_SESSION['Items'.$identifier]->LineItems[$LineNo];


//Make sure this item is really controlled
if ( $LineItem->Controlled != 1 ){
	echo '<div class="centre"><a href="' . $RootPath . '/CounterSales.php">' .  _('Back to Counter Sales'). '</a></div>';
	echo '<br />';
	prnMsg( _('The line item must be defined as controlled to require input of the batch numbers or serial numbers being sold'),'error');
	include('includes/footer.inc');
	exit;
}

/********************************************
  Get the page going....
********************************************/
echo '<div class="centre">';

echo '<br /><a href="'. $RootPath. '/CounterSales.php?identifier=' . $identifier . '">' .  _('Back to Counter Sale') . '</a>';

//echo '<br /><b>' .  _('Dispatch of up to').' '. locale_number_format($LineItem->Quantity-$LineItem->QtyInv, $LineItem->DecimalPlaces). ' '. _('Controlled items').' ' . $LineItem->StockID  . ' - ' . $LineItem->ItemDescription . ' '. _('on order').' ' . $_SESSION['Items'.$identifier]->OrderNo . ' '. _('to'). ' ' . $_SESSION['Items'.$identifier]->CustomerName . '</b></div>';

/** vars needed by InputSerialItem : **/
$StockID = $LineItem->StockID;
$RecvQty = $LineItem->Quantity-$LineItem->QtyInv;
$ItemMustExist = true;  /*Can only invoice valid batches/serial numbered items that exist */
$LocationOut = $_SESSION['Items'.$identifier]->Location;
$InOutModifier=1;
$ShowExisting=true;

include ('includes/InputSerialItems.php');

/*TotalQuantity set inside this include file from the sum of the bundles
of the item selected for dispatch */
$_SESSION['Items'.$identifier]->LineItems[$LineNo]->Quantity = $TotalQuantity;

include('includes/footer.inc');
exit;
?>
