<?php

/* $Id: PDFStockLocTransfer.php 6941 2014-10-26 23:18:08Z daintree $*/
include('includes/session.inc');
$Title = _('Stock Location Transfer Docket Error');

include('includes/PDFStarter.php');

if (isset($_POST['TransferNo'])) {
	$_GET['TransferNo']=$_POST['TransferNo'];
}

if (!isset($_GET['TransferNo'])){

	include ('includes/header.inc');
	echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/maintenance.png" title="' . _('Search') .
		'" alt="" />' . ' ' . _('Reprint transfer docket') . '</p><br />';
	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table>
			<tr>
				<td>' . _('Transfer docket to reprint') . '</td>
				<td><input type="text" class="number" size="10" name="TransferNo" /></td>
			</tr>
		</table>';
	echo '<br />
          <div class="centre">
			<input type="submit" name="Print" value="' . _('Print') .'" />
          </div>';
    echo '</div>
          </form>';

	echo '<form method="post" action="' . $RootPath . '/PDFShipLabel.php">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<input type="hidden" name="Type" value="Transfer" />';
	echo '<table>
			<tr>
				<td>' . _('Transfer docket to reprint Shipping Labels') . '</td>
				<td><input type="text" class="number" size="10" name="ORD" /></td>
			</tr>
		</table>';
	echo '<br />
          <div class="centre">
			<input type="submit" name="Print" value="' . _('Print Shipping Labels') .'" />
          </div>';
    echo '</div>
          </form>';

	include ('includes/footer.inc');
	exit;
}

$pdf->addInfo('Title', _('Inventory Location Transfer BOL') );
$pdf->addInfo('Subject', _('Inventory Location Transfer BOL') . ' # ' . $_GET['TransferNo']);
$FontSize=10;
$PageNumber=1;
$line_height=30;

$ErrMsg = _('An error occurred retrieving the items on the transfer'). '.' . '<p>' .  _('This page must be called with a location transfer reference number').'.';
$DbgMsg = _('The SQL that failed while retrieving the items on the transfer was');
$sql = "SELECT loctransfers.reference,
			   loctransfers.stockid,
			   stockmaster.description,
			   loctransfers.shipqty,
			   loctransfers.recqty,
			   loctransfers.shipdate,
			   loctransfers.shiploc,
			   locations.locationname as shiplocname,
			   loctransfers.recloc,
			   locationsrec.locationname as reclocname,
			   stockmaster.decimalplaces
		FROM loctransfers
		INNER JOIN stockmaster ON loctransfers.stockid=stockmaster.stockid
		INNER JOIN locations ON loctransfers.shiploc=locations.loccode
		INNER JOIN locations AS locationsrec ON loctransfers.recloc = locationsrec.loccode
		INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
		INNER JOIN locationusers as locationusersrec ON locationusersrec.loccode=locationsrec.loccode AND locationusersrec.userid='" .  $_SESSION['UserID'] . "' AND locationusersrec.canview=1
		WHERE loctransfers.reference='" . $_GET['TransferNo'] . "'";

$result = DB_query($sql, $ErrMsg, $DbgMsg);

If (DB_num_rows($result)==0){

	include ('includes/header.inc');
	prnMsg(_('The transfer reference selected does not appear to be set up') . ' - ' . _('enter the items to be transferred first'),'error');
	include ('includes/footer.inc');
	exit;
}

$TransferRow = DB_fetch_array($result);

include ('includes/PDFStockLocTransferHeader.inc');
$line_height=30;
$FontSize=10;

do {

	$LeftOvers = $pdf->addTextWrap($Left_Margin, $YPos, 100, $FontSize, $TransferRow['stockid'], 'left');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+60, $YPos, 250, $FontSize, $TransferRow['description'], 'left');
	$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-100-100, $YPos, 100, $FontSize, locale_number_format($TransferRow['shipqty'],$TransferRow['decimalplaces']), 'right');
	$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-100, $YPos, 100, $FontSize, locale_number_format($TransferRow['recqty'],$TransferRow['decimalplaces']), 'right');
	
	$Sql="SELECT * FROM transferserialitems WHERE transferid='".$_GET['TransferNo']."' AND stockid='".$TransferRow['stockid']."'";
	$rest = DB_query($Sql);
	while ($myrow=DB_fetch_array($rest)) {
	$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-100-220, $YPos, 100, $FontSize, $myrow['serialno'], 'right');
	$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-100-170, $YPos, 100, $FontSize, locale_number_format($myrow['quantity'],$TransferRow['decimalplaces']), 'right');
	$YPos -= 12;
	}

	$pdf->line($Left_Margin, $YPos-2,$Page_Width-$Right_Margin, $YPos-2);

	$YPos -= $line_height;

	if ($YPos < $Bottom_Margin + $line_height) {
		$PageNumber++;
		include('includes/PDFStockLocTransferHeader.inc');
	}

} while ($TransferRow = DB_fetch_array($result));
$pdf->OutputI($_SESSION['DatabaseName'] . '_StockLocTrfShipment_' . date('Y-m-d') . '.pdf');
$pdf->__destruct();
?>
