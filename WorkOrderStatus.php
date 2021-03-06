<?php
/* $Id: WorkOrderStatus.php 6942 2014-10-27 02:48:29Z daintree $*/

include('includes/session.inc');
$Title = _('Work Order Status Inquiry');
include('includes/header.inc');

if (isset($_GET['WO'])) {
	$SelectedWO = $_GET['WO'];
} elseif (isset($_POST['WO'])){
	$SelectedWO = $_POST['WO'];
} else {
	unset($SelectedWO);
}
if (isset($_GET['StockID'])) {
	$StockID = $_GET['StockID'];
} elseif (isset($_POST['StockID'])){
	$StockID = $_POST['StockID'];
} else {
	unset($StockID);
}


$ErrMsg = _('Could not retrieve the details of the selected work order item');
$WOResult = DB_query("SELECT workorders.loccode,
							 locations.locationname,
							 workorders.requiredby,
							 workorders.calibre,
							 workorders.startdate,
							 workorders.closed,
							 stockmaster.description,
							 stockmaster.decimalplaces,
							 stockmaster.units,
							 woitems.qtyreqd,
							 woitems.qtyrecd,
							 woitems.qtyrejected,
							 SUM(locstock.quantity) AS qoh,
							 stockmaster.controlled
						FROM workorders INNER JOIN locations
						ON workorders.loccode=locations.loccode
						INNER JOIN woitems
						ON workorders.wo=woitems.wo
						INNER JOIN stockmaster
						ON woitems.stockid=stockmaster.stockid
						INNER JOIN locstock 
						ON stockmaster.stockid=locstock.stockid
						INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
						WHERE woitems.stockid='" . $StockID . "'
						AND woitems.wo ='" . $SelectedWO . "'
						GROUP BY stockmaster.stockid,
						stockmaster.description,
						stockmaster.longdescription,
						stockmaster.units,
						stockmaster.mbflag,
						stockmaster.discontinued,
						stockmaster.decimalplaces",
						$ErrMsg);

if (DB_num_rows($WOResult)==0){
	prnMsg(_('The selected work order item cannot be retrieved from the database'),'info');
	include('includes/footer.inc');
	exit;
}
$WORow = DB_fetch_array($WOResult);

echo '<a href="'. $RootPath . '/SelectWorkOrder.php">' . _('Back to Work Orders'). '</a><br />';
echo '<a href="'. $RootPath . '/WorkOrderCosting.php?WO=' .  $SelectedWO . '">' . _('Back to Costing'). '</a><br />';

echo '<p class="page_title_text">
		<img src="'.$RootPath.'/css/'.$Theme.'/images/group_add.png" title="' .
	_('Search') . '" alt="" />' . ' ' . $Title.'
	</p>';

echo '<table cellpadding="2" class="selection">
	<tr>
		<td class="label">' . _('Work order Number') . ':</td>
		<td>' . $SelectedWO  . '</td>
		<td class="label">' . _('Item') . ':</td>
		<td>' . $StockID . ' - ' . $WORow['description'] . '</td>
	</tr>
 	<tr>
		<td class="label">' . _('Manufactured at') . ':</td>
		<td>' . $WORow['locationname'] . '</td>
		<td class="label">' . _('Calibre') . ':</td>
		<td>' . $WORow['calibre'] . '</td>
	</tr>
 	<tr>
		<td class="label">' . _('Quantity Ordered') . ':</td>
		<td class="number">' . locale_number_format($WORow['qtyreqd'],$WORow['decimalplaces']) . ' ' . $WORow['units'] . '</td>
		<td class="label">' . _('Required By') . ':</td>
		<td>' . ConvertSQLDate($WORow['requiredby']) . '</td>
	</tr>
	<tr>
		<td class="label">' . _('Quantity On Hand') . ':</td>';
		if ($WORow['controlled']==1){
			echo '<td class="number"><a target="_blank" href="' . $RootPath . '/StockSerialItems.php?Location=' . $WORow['loccode'] . '&amp;StockID=' .$StockID . '">' . _('Batches') . '</a>';
		}else{
		echo '<td class="number">';
		}
		echo '' . locale_number_format($WORow['qoh'],$WORow['decimalplaces']) . ' ' . $WORow['units'] . '</td>';
	echo '<td class="label">' . _('Start Date') . ':</td>
		<td>' . ConvertSQLDate($WORow['startdate']) . '</td>
	</tr>
 	<tr>
		<td class="label"></td>
		<td colspan="3">';
		$RResult = DB_query("SELECT stockmoves.trandate,
									stockmoves.qty,
									stockmoves.qtyrejected,
									stockmoves.brasslotno,
									stockmoves.standardcost,
									stockmaster.decimalplaces,
									(SELECT serialno FROM stockserialmoves
                                    WHERE stockserialmoves.stockmoveno = stockmoves.stkmoveno
                                    LIMIT 1)as serialno
								FROM stockmoves INNER JOIN stockmaster
								ON stockmoves.stockid = stockmaster.stockid
								WHERE stockmoves.type=26
								AND stockmoves.reference = '" . $SelectedWO . "'
								AND stockmoves.stockid = '" . $StockID . "'",
								_('Could not retrieve the issues of the item because:'));

if (DB_num_rows($RResult)>0){
		
		echo '<table class="selection" style="width:100%">';
		echo '<th>' . _('Date Received') . '</th><th>' . _('Brass Batch No') . '</th><th>' . _('Stamp Lot No') . '</th><th>' . _('Already Received') . '</th><th>' . _('Already Rejected') . '</th></tr>';
		while ($RRow = DB_fetch_array($RResult)){
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k++;
		}
		echo '<tr>
		<td>' . ConvertSQLDateTime($RRow['trandate']) . '</td>
		<td class="number">' . $RRow['brasslotno'] . '</td>
		<td class="number">' . $RRow['serialno'] . '</td>
		<td class="number">' . locale_number_format($RRow['qty'],$WORow['decimalplaces']) . ' ' . $WORow['units'] . '</td>
		<td class="number">' . locale_number_format($RRow['qtyrejected'],$WORow['decimalplaces']) . ' ' . $WORow['units'] . '</td>
		</tr>';
			}
		echo '<tr><td></td><td></td><th>Total Quantity :</th><th class="number">' . locale_number_format($WORow['qtyrecd'],$WORow['decimalplaces']) . ' ' . $WORow['units'] . '</th><th class="number">' . locale_number_format($WORow['qtyrejected'],$WORow['decimalplaces']) . ' ' . $WORow['units'] . '</th></tr>';
		echo '</table>';
		}
	echo '</td></tr>
	</table><br />';

	//set up options for selection of the item to be issued to the WO
	echo '<table class="selection">
			<tr>
				<th colspan="5"><h3>' . _('Material Requirements For this Work Order') . '</h3></th>
			</tr>';
	echo '<tr>
			<th colspan="2">' . _('Item') . '</th>
			<th>' . _('Qty Required') . '</th>
			<th>' . _('Qty Issued') . '</th>
		</tr>';

	$RequirmentsResult = DB_query("SELECT worequirements.stockid,
										stockmaster.description,
										stockmaster.decimalplaces,
										autoissue,
										qtypu
									FROM worequirements INNER JOIN stockmaster
									ON worequirements.stockid=stockmaster.stockid
									WHERE wo='" . $SelectedWO . "'
									AND worequirements.parentstockid='" . $StockID . "'");
		$IssuedAlreadyResult = DB_query("SELECT stockid,
						SUM(-qty) AS total
					FROM stockmoves
					WHERE stockmoves.type=28
					AND reference='".$SelectedWO."'
					GROUP BY stockid");
	while ($IssuedRow = DB_fetch_array($IssuedAlreadyResult)){
		$IssuedAlreadyRow[$IssuedRow['stockid']] = $IssuedRow['total'];
	}

	while ($RequirementsRow = DB_fetch_array($RequirmentsResult)){
		if ($RequirementsRow['autoissue']==0){
			echo '<tr>
					<td>' . _('Manual Issue') . '</td>
					<td>' . $RequirementsRow['stockid'] . ' - ' . $RequirementsRow['description'] . '</td>';
		} else {
			echo '<tr>
					<td class="notavailable">' . _('Auto Issue') . '</td>
					<td class="notavailable">' .$RequirementsRow['stockid'] . ' - ' . $RequirementsRow['description']  . '</td>';
		}
		if (isset($IssuedAlreadyRow[$RequirementsRow['stockid']])){
			$Issued = $IssuedAlreadyRow[$RequirementsRow['stockid']];
			unset($IssuedAlreadyRow[$RequirementsRow['stockid']]);
		}else{
			$Issued = 0;
		}
		echo '<td class="number">'.locale_number_format($WORow['qtyreqd']*$RequirementsRow['qtypu'],$RequirementsRow['decimalplaces']).'</td>
			<td class="number">'.locale_number_format($Issued,$RequirementsRow['decimalplaces']).'</td></tr>';
	}
	/* Now do any additional issues of items not in the BOM */
	if(count($IssuedAlreadyRow)>0){
		$AdditionalStocks = implode("','",array_keys($IssuedAlreadyRow));
		$RequirementsSQL = "SELECT stockid,
						description,
							decimalplaces
				FROM stockmaster WHERE stockid IN ('".$AdditionalStocks."')";
		$RequirementsResult = DB_query($RequirementsSQL);
			$AdditionalStocks = array();
			while($myrow = DB_fetch_array($RequirementsResult)){
				$AdditionalStocks[$myrow['stockid']]['description'] = $myrow['description'];
				$AdditionalStocks[$myrow['stockid']]['decimalplaces'] = $myrow['decimalplaces'];
			}
			foreach ($IssuedAlreadyRow as $StockID=>$Issued) {
			echo '<tr>
				<td>'._('Additional Issue').'</td>
				<td>'.$StockID . ' - '.$AdditionalStocks[$StockID]['description'].'</td>';
				echo '<td class="number">0</td>
					<td class="number">'.locale_number_format($Issued,$AdditionalStocks[$StockID]['decimalplaces']).'</td>
					</tr>';
			}
		}

	echo '</table>';
	include('includes/footer.inc');

?>