<?php
/* $Id: StockLocTransfer.php 6945 2014-10-27 07:20:48Z daintree $*/
/* Inventory Transfer - Bulk Dispatch */

include('includes/session.inc');
$Title = _('Inventory Location Transfer Shipment');
$BookMark = "LocationTransfers";
$ViewTopic = "Inventory";
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if (isset($_POST['Submit']) OR isset($_POST['EnterMoreItems'])){
/*Trap any errors in input */

	$InputError = False; /*Start off hoping for the best */
	$TotalItems = 0;
	//Make sure this Transfer has not already been entered... aka one way around the refresh & insert new records problem
	$result = DB_query("SELECT * FROM loctransfers WHERE reference='" . $_POST['Trf_ID'] . "'");
	if (DB_num_rows($result)!=0){
		$InputError = true;
		$ErrorMessage = _('This transaction has already been entered') . '. ' . _('Please start over now') . '<br />';
		unset($_POST['submit']);
		unset($_POST['EnterMoreItems']);
	}  else {

			$ErrorMessage='';
			
			foreach($_POST['StockID'] as $val){
				if (isset($_POST['Transfer_'. $val]) AND $_POST['Transfer_' . $val]!=''){
				
					foreach($_POST['Transfer_' . $val] as $serial){
					 $StockQTY = $_POST['QTY_'.$val.'_'.$serial];
					if (!is_numeric(filter_number_format($StockQTY))){
						$InputError = True;
						$ErrorMessage .= _('The quantity entered of'). ' ' . $StockQTY . ' '. _('for Lot Number'). ' ' . $serial . ' '. _('is not numeric') . '. ' . _('The quantity entered for transfers is expected to be numeric') . '<br />';
					}
					if (filter_number_format($StockQTY) <= 0){
						$InputError = True;
						$ErrorMessage .= _('The quantity entered for').' '. $serial . ' ' . _('is less than or equal to 0') . '. ' . _('Please correct this or remove the item') . '<br />';
					}
					if ($_SESSION['ProhibitNegativeStock']==1){
						$InTransitSQL="SELECT SUM(shipqty-recqty) as intransit
									FROM loctransfers
									WHERE stockid='" . $val . "'
										AND shiploc='".$_POST['FromStockLocation']."'
										AND shipqty>recqty";
						$InTransitResult=DB_query($InTransitSQL);
						$InTransitRow=DB_fetch_array($InTransitResult);
						$InTransitQuantity=$InTransitRow['intransit'];
						// Only if stock exists at this location
						$result = DB_query("SELECT quantity
										FROM locstock
										WHERE stockid='" . $val . "'
										AND loccode='".$_POST['FromStockLocation']."'");

						$myrow = DB_fetch_array($result);
						if (($myrow['quantity']-$InTransitQuantity) < filter_number_format($StockQTY)){
							$InputError = True;
							$ErrorMessage .= _('The part code entered of'). ' ' . $serial . ' '. _('does not have enough stock available for transfer.') . '.<br />';
						}
					}

					DB_free_result( $result );
					$TotalItems++;
					}
				}
			}//for all LinesCounter
			
		if ($TotalItems == 0){
			$InputError = True;
			$ErrorMessage .= _('You must enter at least 1 Stock Item to transfer') . '<br />';
		}

	/*Ship location and Receive location are different */
		if ($_POST['FromStockLocation']==$_POST['ToStockLocation']){
			$InputError=True;
			$ErrorMessage .= _('The transfer must have a different location to receive into and location sent from');
		}
	 } //end if the transfer is not a duplicated
}

if(isset($_POST['Submit']) AND $InputError==False){

	$ErrMsg = _('CRITICAL ERROR') . '! ' . _('Unable to BEGIN Location Transfer transaction');

	DB_Txn_Begin();
	
	foreach($_POST['StockID'] as $val){
	$QtyTot=0;
	$DecimalsSql = "SELECT decimalplaces
							FROM stockmaster
							WHERE stockid='" . $val . "'";
			$DecimalResult = DB_query($DecimalsSql);
			$DecimalRow = DB_fetch_array($DecimalResult);
			
	if (isset($_POST['Transfer_' . $val]) AND count($_POST['Transfer_' . $val])!=0){
				
		foreach($_POST['Transfer_' . $val] as $serial){
		$StockQTY = $_POST['QTY_'.$val.'_'.$serial];
		
	$sql ="INSERT INTO `transferserialitems`(transferid,`stockid`, `loccode`, `serialno`, `quantity`) 
											VALUES ('".$_POST['Trf_ID']."','".$val."','".$_POST['FromStockLocation']."','".$serial."','".$StockQTY."')";
	$ErrMsg = _('CRITICAL ERROR') . '! ' . _('Unable to enter Location Transfer record for'). ' '.$val;
	DB_query($sql, $ErrMsg);
	$QtyTot +=$StockQTY;
	}

			$sql = "INSERT INTO loctransfers (reference,
								stockid,
								shipqty,
								shipdate,
								shiploc,
								recloc)
						VALUES ('" . $_POST['Trf_ID'] . "',
							'" . $val . "',
							'" . round(filter_number_format($QtyTot), $DecimalRow['decimalplaces']) . "',
							'" . Date('Y-m-d H-i-s') . "',
							'" . $_POST['FromStockLocation']  ."',
							'" . $_POST['ToStockLocation'] . "')";
			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('Unable to enter Location Transfer record for'). ' '.$val;
			$resultLocShip = DB_query($sql, $ErrMsg);
		}
		}
	$ErrMsg = _('CRITICAL ERROR') . '! ' . _('Unable to COMMIT Location Transfer transaction');
	DB_Txn_Commit();

	prnMsg( _('The inventory transfer records have been created successfully'),'success');
	echo '<p><a href="'.$RootPath.'/PDFStockLocTransfer.php?TransferNo=' . $_POST['Trf_ID'] . '">' .  _('Print the Transfer Docket'). '</a></p>';
	include('includes/footer.inc');

} else {
	//Get next Inventory Transfer Shipment Reference Number
	if (isset($_GET['Trf_ID'])){
		$Trf_ID = $_GET['Trf_ID'];
	} elseif (isset($_POST['Trf_ID'])){
		$Trf_ID = $_POST['Trf_ID'];
	}

	if(!isset($Trf_ID)){
		$Trf_ID = GetNextTransNo(16,$db);
	}
	
	if(isset($_SESSION['StockID']) && isset($_SESSION['Qty'])){
foreach($_SESSION['StockID'] as $z => $val){
$_POST['StockID'.$z] = $val;
$_POST['StockQTY'.$z] = $_SESSION['Qty'][$z];
}
$_POST['FromStockLocation'] = $_SESSION['Locode'];
}
unset($_SESSION['StockID']);
unset($_SESSION['Qty']);
unset($_SESSION['Locode']);
	if (isset($InputError) and $InputError==true){
		echo '<br />';

		prnMsg($ErrorMessage, 'error');
		echo '<br />';

	}

	echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/supplier.png" title="' . _('Dispatch') . '" alt="" />' . ' ' . $Title . '</p>';

	echo '<form enctype="multipart/form-data" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	echo '<table class="selection">';
	echo '<tr>
			<th colspan="4"><input type="hidden" name="Trf_ID" value="' . $Trf_ID . '" /><h3>' .  _('Inventory Location Transfer Shipment Reference').' # '. $Trf_ID. '</h3></th>
		</tr>';

	$sql = "SELECT locations.loccode, locationname FROM locations INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canupd=1 ORDER BY locationname";
	$resultStkLocs = DB_query($sql);

	echo '<tr>
			<td>' . _('From Stock Location') . ':</td>
			<td><select name="FromStockLocation">';

	while ($myrow=DB_fetch_array($resultStkLocs)){
		if (isset($_POST['FromStockLocation'])){
			if ($myrow['loccode'] == $_POST['FromStockLocation']){
				echo '<option selected="selected" value="' . $myrow['loccode'] . '">' . $myrow['locationname']. '</option>';
			} else {
				echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			}
		} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
			echo '<option selected="selected" value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			$_POST['FromStockLocation']=$myrow['loccode'];
		} else {
			echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
		}
	}
	echo '</select></td>';

	DB_data_seek($resultStkLocs,0); //go back to the start of the locations result
	echo '<td>' . _('To Stock Location').':</td>
			<td><select name="ToStockLocation">';
	while ($myrow=DB_fetch_array($resultStkLocs)){
		if (isset($_POST['ToStockLocation'])){
			if ($myrow['loccode'] == $_POST['ToStockLocation']){
				echo '<option selected="selected" value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			} else {
				echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			}
		} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
			echo '<option selected="selected" value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			$_POST['ToStockLocation']=$myrow['loccode'];
		} else {
			echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
		}
	}
	echo '</select></td></tr>';
	
	$SQL = "SELECT stockcategory.categoryid,
				stockcategory.categorydescription
			FROM stockcategory, internalstockcatrole
			WHERE stockcategory.categoryid = internalstockcatrole.categoryid
				AND internalstockcatrole.secroleid= " . $_SESSION['AccessLevel'] . "
			ORDER BY stockcategory.categorydescription";
$result1 = DB_query($SQL);
if (DB_num_rows($result1) == 0) {
	echo '<p class="bad">' . _('Problem Report') . ':<br />' . _('There are no stock categories currently defined please use the link below to set them up') . '</p>';
	echo '<br />
		<a href="' . $RootPath . '/StockCategories.php">' . _('Define Stock Categories') . '</a>';
	exit;
}

	echo '<tr>
			<td>' . _('In Stock Category') . ':</td><td><select name="StockCat">';

if (!isset($_POST['StockCat'])) {
	$_POST['StockCat'] = '';
}
while ($myrow1 = DB_fetch_array($result1)) {
	if ($myrow1['categoryid'] == $_POST['StockCat']) {
		echo '<option selected="True" value="' . $myrow1['categoryid'] . '">' . $myrow1['categorydescription'] . '</option>';
	} else {
		echo '<option value="' . $myrow1['categoryid'] . '">' . $myrow1['categorydescription'] . '</option>';
	}
}
echo '</select></td>
		  </tr>
		  <tr>
		  <td></td>
		  <td><input type="submit" name="Enter" value="'. _('Update'). '" /><input name="EnterUpdate" type="hidden" value="1" /></td>
		  </tr>
		  </table>
		  <br />';
		  
		  if(isset($_POST['Enter'])){
		  //unset($_POST['LinesCounter']);
		  }
		  if(isset($_POST['EnterUpdate'])){

$SQLi = "SELECT stockmaster.stockid,
							stockmaster.description,
				(SELECT SUM(quantity)
			FROM stockserialitems
			INNER JOIN locationusers ON locationusers.loccode=stockserialitems.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
			WHERE stockserialitems.loccode='" . $_POST['FromStockLocation'] . "'
			AND stockid = stockmaster.stockid
			AND quantity <>0 GROUP BY stockid) as qty
					FROM stockmaster,
						stockcategory,
						internalstockcatrole
					WHERE stockmaster.categoryid=stockcategory.categoryid
						AND stockcategory.categoryid = internalstockcatrole.categoryid
						AND internalstockcatrole.secroleid= " . $_SESSION['AccessLevel'] . "
						AND stockmaster.discontinued=0
						AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					ORDER BY stockmaster.stockid";
					
	$ErrMsg = _('There is a problem selecting the part records to display because');
	$DbgMsg = _('The SQL used to get the part selection was');
	$result1 = DB_query($SQLi,$ErrMsg, $DbgMsg);	
	echo '<table class="selection">';
	while ($myrow = DB_fetch_array($result1)) {
	if($myrow['qty']>0){
	$sql = "SELECT serialno,
				quantity,
				expirationdate
			FROM stockserialitems
			INNER JOIN locationusers ON locationusers.loccode=stockserialitems.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
			WHERE stockserialitems.loccode='" . $_POST['FromStockLocation'] . "'
			AND stockid = '" . $myrow['stockid'] . "'
			AND quantity <>0";


$ErrMsg = _('The serial numbers/batches held cannot be retrieved because');
$LocStockResult = DB_query($sql, $ErrMsg);
echo '<tr><td><input type="hidden" name="StockID[]" size="21"  maxlength="20" value="' .$myrow['stockid']. '" />'.$myrow['stockid'].' - '.$myrow['description'].'</td><td>';
echo '<table class="selection">';
echo '<tr><td width="100">Lot No</td><td width="80">Qty on Hand</td><td>Transfer</td><td>Qty To Transfer</td></tr>';
while ($myro=DB_fetch_array($LocStockResult)) {
echo '<tr><td>'.$myro['serialno'].'</td><td>'.number_format($myro['quantity']).'</td>
		<td><input name="Transfer_'.$myrow['stockid'].'[]" type="checkbox" value="'.$myro['serialno'].'" /></td>
		<td><input name="QTY_'.$myrow['stockid'].'_'.$myro['serialno'].'" class="number" type="text" /></td></tr>';
}
echo '</table>';
echo '</td></tr>';
	}
	}
	echo '</table>';

	echo '<br />
		<div class="centre">
		<input type="submit" name="Submit" value="'. _('Create Transfer Shipment'). '" />
		<br />
		</div>
		</div>';

		} //close EnterUpdate
	echo '</form>';
	include('includes/footer.inc');
}
?>
