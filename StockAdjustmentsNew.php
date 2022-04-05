<?php

/* $Id: StockAdjustments.php 7021 2014-12-14 02:04:44Z tehonu $*/

include('includes/session.inc');
$Title = _('Stock Adjustments');

/* webERP manual links before header.inc */
$ViewTopic= 'Inventory';
$BookMark = 'InventoryAdjustments';

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

$sql = "SELECT locations.loccode, locationname FROM locations INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canupd=1";
$resultStkLocs = DB_query($sql);
$LocationList=array();
while ($myrow=DB_fetch_array($resultStkLocs)){
	$LocationList[$myrow['loccode']]=$myrow['locationname'];
}

$SQL2 = "SELECT categoryid,
				categorydescription
		FROM stockcategory
		ORDER BY categorydescription";
$result1 = DB_query($SQL2);
$CatList=array();
while ($myrow=DB_fetch_array($result1)){
	$CatList[$myrow['categoryid']]=$myrow['categorydescription'];
}

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/supplier.png" title="' . _('Inventory Adjustment') . '" alt="" />' . ' ' . _('Inventory Adjustment') . '</p>';


if (isset($_POST['EnterAdjustment']) AND $_POST['EnterAdjustment']!= ''){

	
	foreach($_POST['StockCode'] as $StockID){
	$InputError = false; /*Start by hoping for the best */
	if (!is_numeric($_POST['Qty_'.$StockID])){
		prnMsg( _('The quantity entered must be numeric'),'error');
		$InputError = true;
	}elseif ($_POST['Qty_'.$StockID]==0){
		$InputError = true;
	}

	if ($_SESSION['ProhibitNegativeStock']==1){
		$SQL = "SELECT quantity FROM locstock
				WHERE stockid='" . $StockID . "'
				AND loccode='" . $_POST['StockLocation'] . "'";
		$CheckNegResult=DB_query($SQL);
		$CheckNegRow = DB_fetch_array($CheckNegResult);
		if ($CheckNegRow['quantity']+$_POST['Qty_'.$StockID] <0){
			$InputError=true;
			prnMsg(_('The system parameters are set to prohibit negative stocks. Processing this stock adjustment would result in negative stock at this location. This adjustment will not be processed.'),'error');
		}
	}

	if (!$InputError) {

/*All inputs must be sensible so make the stock movement records and update the locations stocks */

		$AdjustmentNumber = GetNextTransNo(17,$db);
		$PeriodNo = GetPeriod (Date($_SESSION['DefaultDateFormat']), $db);
		$SQLAdjustmentDate = FormatDateForSQL(Date($_SESSION['DefaultDateFormat']));

		$Result = DB_Txn_Begin();

		// Need to get the current location quantity will need it later for the stock movement
		$SQL="SELECT locstock.quantity
			FROM locstock
			WHERE locstock.stockid='" . $StockID . "'
			AND loccode= '" . $_POST['StockLocation'] . "'";
		$Result = DB_query($SQL);
		if (DB_num_rows($Result)==1){
			$LocQtyRow = DB_fetch_row($Result);
			$QtyOnHandPrior = $LocQtyRow[0];
		} else {
			// There must actually be some error this should never happen
			$QtyOnHandPrior = 0;
		}
		$SQL = "INSERT INTO stockmoves (stockid,
										type,
										transno,
										loccode,
										trandate,
										userid,
										prd,
										reference,
										qty,
										newqoh,
										standardcost)
									VALUES ('" . $StockID . "',
										17,
										'" . $AdjustmentNumber . "',
										'" . $_POST['StockLocation'] . "',
										'" . $SQLAdjustmentDate . "',
										'" . $_SESSION['UserID'] . "',
										'" . $PeriodNo . "',
										'" . $_POST['Narrative_'.$StockID] ."',
										'" . $_POST['Qty_'.$StockID] . "',
										'" . ($QtyOnHandPrior + $_POST['Qty'.$StockID]) . "',
										'" . $_POST['StandardCost_'.$StockID] . "')";

		$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock movement record cannot be inserted because');
		$DbgMsg =  _('The following SQL to insert the stock movement record was used');
		$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

/*Get the ID of the StockMove... */
		$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');

/*Insert the StockSerialMovements and update the StockSerialItems  for controlled items*/


		$SQL = "UPDATE locstock SET quantity = quantity + " . floatval($_POST['Qty_'.$StockID]) . "
				WHERE stockid='" . $StockID . "'
				AND loccode='" . $_POST['StockLocation'] . "'";

		$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' ._('The location stock record could not be updated because');
		$DbgMsg = _('The following SQL to update the stock record was used');
		$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

		if ($_SESSION['CompanyRecord']['gllink_stock']==1 AND $_POST['StandardCost_'.$StockID] > 0){

			$StockGLCodes = GetStockGLCode($StockID,$db);

			$SQL = "INSERT INTO gltrans (type,
										typeno,
										trandate,
										periodno,
										account,
										amount,
										narrative,
										tag)
								VALUES (17,
									'" .$AdjustmentNumber . "',
									'" . $SQLAdjustmentDate . "',
									'" . $PeriodNo . "',
									'" .  $StockGLCodes['adjglact'] . "',
									'" . round($_POST['StandardCost_'.$StockID] * -($_POST['Qty_'.$StockID]), $_SESSION['CompanyRecord']['decimalplaces']) . "',
									'" . $StockID . " x " . $_POST['Qty_'.$StockID] . " @ " .
										$_POST['StandardCost_'.$StockID] . " " . $_POST['Narrative_'.$StockID] . "',
									'" . $_POST['tag'] . "')";

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The general ledger transaction entries could not be added because');
			$DbgMsg = _('The following SQL to insert the GL entries was used');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

			$SQL = "INSERT INTO gltrans (type,
										typeno,
										trandate,
										periodno,
										account,
										amount,
										narrative,
										tag)
								VALUES (17,
									'" .$AdjustmentNumber . "',
									'" . $SQLAdjustmentDate . "',
									'" . $PeriodNo . "',
									'" .  $StockGLCodes['stockact'] . "',
									'" . round($_POST['StandardCost_'.$StockID] * $_POST['Qty_'.$StockID],$_SESSION['CompanyRecord']['decimalplaces']) . "',
									'" . $StockID . ' x ' . $_POST['Qty_'.$StockID] . ' @ ' . $_POST['StandardCost_'.$StockID] . ' ' . $_POST['Narrative_'.$StockID] . "',
									'" . $_POST['tag'] . "'
									)";

			$Errmsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The general ledger transaction entries could not be added because');
			$DbgMsg = _('The following SQL to insert the GL entries was used');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg,true);
		}

		EnsureGLEntriesBalance(17, $AdjustmentNumber,$db);

		$Result = DB_Txn_Commit();

		$ConfirmationText = _('A stock adjustment for'). ' ' . $StockID . ' '._('has been created from location').' ' . $_POST['StockLocation'] .' '. _('for a quantity of') . ' ' . locale_number_format($_POST['Qty_'.$StockID],$_SESSION['CompanyRecord']['decimalplaces']) ;
		prnMsg( $ConfirmationText,'success');
	} /* end if there was no input error */
	
	}

}/* end if the user hit enter the adjustment */


echo '<form action="'. htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

echo '<br />
	<table class="selection">
	<tr>
		<th colspan="4"><h3>' . _('Add Stock Details') . '</h3></th>
	</tr>';

echo '<tr><td>'. _('Category').':</td>
		<td><select name="Cat" onchange="submit();"> ';
foreach ($CatList as $code=>$Catname){
	if ($code == $_POST['Cat']){
		 echo '<option selected="selected" value="' . $code . '">' . $Catname . '</option>';
	} else {
		 echo '<option value="' . $code . '">' . $Catname . '</option>';
	}
}

echo '</select></td></tr>';
echo '<tr><td>'. _('Adjustment to Stock At Location').':</td>
		<td><select required name="StockLocation" onchange="submit();"> ';
		echo '<option value="">--Please Select Location--</option>';
foreach ($LocationList as $Loccode=>$Locationname){
	if ($Loccode == $_POST['StockLocation']){
		 echo '<option selected="selected" value="' . $Loccode . '">' . $Locationname . '</option>';
	} else {
		 echo '<option value="' . $Loccode . '">' . $Locationname . '</option>';
	}
}

echo '</select></td></tr>';
echo '<tr><td></td><td><input type="submit" name="SearchProducts" value="'. _('Search Products'). '" /></td></tr>';
echo '</form>';
if(isset($_POST['SearchProducts'])){
$sqls="SELECT description, 
				stockid,
				materialcost,
				labourcost,
				overheadcost,
				units,
				decimalplaces
			FROM stockmaster
			WHERE categoryid='".$_POST['Cat']."' AND discontinued=0";

	$results=DB_query($sqls);
echo '<form action="'. htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<tr>
		<th>' . _('Product Code').'</th>
		<th>' . _('Product Name').'</th>
		<th>' . _('Adjustment Quantity').'</th>
		<th>' .  _('Comments On Why').'</th>
	</tr>';
$k = 0;
while($myr=DB_fetch_array($results)){
echo '<input type="hidden" name="StockCode[]" size="21" value="' . $myr['stockid'] .'" maxlength="20" />';
echo '<input type="hidden" name="StockLocation" size="21" value="' . $_POST['StockLocation'] .'" maxlength="20" />';
echo '<input type="hidden" name="StandardCost_'.$myr['stockid'].'" size="21" value="' . ($myr['materialcost']+$myr['labourcost']+$myr['overheadcost']) .'" maxlength="20" />';
if ($k == 1) {
				echo '<tr class="EvenTableRows">';
				$k = 0;
			} else {
				echo '<tr class="OddTableRows">';
				$k++;
			}
echo '<td>' . $myr['stockid'] . '</td>';
echo '<td>' . $myr['description'] . '</td>';
echo '<td>';
	echo '<input type="text" class="number" name="Qty_'.$myr['stockid'].'" size="12" maxlength="12" value="' . locale_number_format($Quantity,$DecimalPlaces) . '" />';
echo '</td>
<td><input type="text" name="Narrative_'.$myr['stockid'].'" size="32" maxlength="30" value="" /></td>
</tr>';
	//Select the tag
echo '<tr style="display:none" >
		<td>' . _('Select Tag') . '</td>
		<td><select style="display:none" name="tag">';

$SQL = "SELECT tagref,
				tagdescription
		FROM tags
		ORDER BY tagref";

$result=DB_query($SQL);
echo '<option value="0">0 - ' . _('None') . '</option>';
while ($myrow=DB_fetch_array($result)){
	if (isset($_SESSION['Adjustment' . $identifier]->tag) AND $_SESSION['Adjustment' . $identifier]->tag==$myrow['tagref']){
		echo '<option selected="selected" value="' . $myrow['tagref'] . '">' . $myrow['tagref'].' - ' .$myrow['tagdescription'] . '</option>';
	} else {
		echo '<option value="' . $myrow['tagref'] . '">' . $myrow['tagref'].' - ' .$myrow['tagdescription']. '</option>';
	}
}
echo '</select></td></tr>';
// End select tag
}
echo '</table>
	<div class="centre">
	<br />
	<input type="submit" name="EnterAdjustment" value="'. _('Enter Stock Adjustment'). '" />
	<br /></div>';
echo '</form>';
}else{
echo '</table>';
}
echo ' </div>';
include('includes/footer.inc');
?>