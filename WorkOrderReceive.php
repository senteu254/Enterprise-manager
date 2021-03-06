<?php
/* $Id: WorkOrderReceive.php 7026 2014-12-16 20:55:46Z agaluski $*/

include('includes/session.inc');
$Title = _('Receive Work Order');
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

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
echo '<div>
		<a href="'. $RootPath . '/SelectWorkOrder.php">' . _('Back to Work Orders'). '</a>
		<br />
		<a href="'. $RootPath . '/WorkOrderCosting.php?WO=' .  $SelectedWO . '">' . _('Back to Costing'). '</a>
		<br />
	</div>';

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/group_add.png" title="' .
	_('Search') . '" alt="" />' . ' ' . $Title . '</p>';

echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

if (!isset($SelectedWO) OR !isset($StockID)) {
	/* This page can only be called with a purchase order number for invoicing*/
	echo '<div class="centre">
			<a href="' . $RootPath . '/SelectWorkOrder.php">' .  _('Select a work order to receive') . '</a>
		</div>';
	prnMsg(_('This page can only be opened if a work order has been selected. Please select a work order to receive first'),'info');
	include ('includes/footer.inc');
	exit;
} else {
	echo '<input type="hidden" name="WO" value="' .$SelectedWO . '" />';
	$_POST['WO']=$SelectedWO;
	echo '<input type="hidden" name="StockID" value="' .$StockID . '" />';
	$_POST['StockID']=$StockID;
}

if (isset($_POST['Process'])){ //user hit the process the work order receipts entered.

	$InputError = false; //ie assume no problems for a start - ever the optimist
	$ErrMsg = _('Could not retrieve the details of the selected work order item');
	$WOResult = DB_query("SELECT workorders.loccode,
								 locations.locationname,
								 workorders.requiredby,
								 workorders.startdate,
								 workorders.closed,
								 stockmaster.description,
								 stockmaster.controlled,
								 stockmaster.serialised,
								 stockmaster.decimalplaces,
								 stockmaster.units,
								 stockmaster.perishable,
								 woitems.qtyreqd,
								 woitems.qtyrecd,
								 woitems.qtyrejected,
								 woitems.stdcost,
								 stockcategory.wipact,
								 stockcategory.stockact
							FROM workorders INNER JOIN locations
							ON workorders.loccode=locations.loccode
							INNER JOIN woitems
							ON workorders.wo=woitems.wo
							INNER JOIN stockmaster
							ON woitems.stockid=stockmaster.stockid
							INNER JOIN stockcategory
							ON stockmaster.categoryid=stockcategory.categoryid
							INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canupd=1
							WHERE woitems.stockid='" . $_POST['StockID'] . "'
							AND workorders.wo='".$_POST['WO'] . "'",
							$ErrMsg);

	if (DB_num_rows($WOResult)==0){
		prnMsg(_('The selected work order item cannot be retrieved from the database'),'info');
        echo '</div>';
        echo '</form>';
		include('includes/footer.inc');
		exit;
	}
	$WORow = DB_fetch_array($WOResult);

	$QuantityReceived = 0;

	if($WORow['controlled']==1){ //controlled
		if ($WORow['serialised']==1){ //serialised
			for ($i=0;$i<$_POST['CountOfInputs'];$i++){
				if ($_SESSION['DefineControlledOnWOEntry']==1){
					if (isset($_POST['CheckItem' . $i])){
						$QuantityReceived ++;
						$QuantityRejected ++; //rejected
					}
				} else { //not predefined serial numbers
					if (mb_strlen($_POST['SerialNo' . $i])>0){
						$QuantityReceived ++;
						$QuantityRejected ++; //rejected
					}
				}
			}
		} else { //controlled but not serialised - just lot/batch control
			for ($i=0;$i<15;$i++){
				if (mb_strlen($_POST['BatchRef' . $i])>0){
				if (!is_numeric(filter_number_format($_POST['Qty' .$i]))){
				$InputError=true;
				prnMsg(_('The quantity entered '). _('for Batch/Lot'). ' ' . $_POST['BatchRef' . $i] . ' '. _('is not numeric - a number is expected'),'error');
				}elseif($_POST['RQty' .$i] !="" && !is_numeric(filter_number_format($_POST['RQty' .$i]))){
				$InputError=true;
				prnMsg(_('The quantity entered '). _('for Batch/Lot'). ' ' . $_POST['BatchRef' . $i] . ' '. _('is not numeric - a number is expected'),'error');
				}
					$BatchRef = $_POST['BatchRef' . $i];
					$QuantityReceived += filter_number_format($_POST['Qty' .$i]);
					$QuantityRejected += filter_number_format($_POST['RQty' .$i]); //rejected
					$BrassLotNo = $_POST['BrassLot']; //brass lot no
				}
			}
		} //end of lot/batch control

		if (!empty($_POST['ExpiryDate'])){
			if(!is_date($_POST['ExpiryDate'])){
				$InputError = true;
				prnMsg(_('The Expiry Date should be in date format'),'error');
			}
		}
	} else { //not controlled - an easy one!
		if (!is_numeric(filter_number_format($_POST['Qty']))){
			$InputError=true;
			prnMsg(_('The quantity entered is not numeric - a number is expected'),'error');
		}elseif (!is_numeric(filter_number_format($_POST['RQty']))){
			$InputError=true;
			prnMsg(_('The quantity entered is not numeric - a number is expected'),'error');
		} else {
			$QuantityReceived = filter_number_format($_POST['Qty']);
			$QuantityRejected = filter_number_format($_POST['RQty']); //rejected
			$BrassLotNo = $_POST['BrassLot']; //brass lot no
		}
	}

	if ($QuantityReceived + $QuantityRejected + $WORow['qtyrecd']+$WORow['qtyrejected'] > $WORow['qtyreqd'] *(1+$_SESSION['OverReceiveProportion'])){
		prnMsg(_('The quantity received is greater than the quantity required even after allowing for the configured allowable over-receive proportion. If this is correct then the work order must be modified first.'),'error');
		$InputError=true;
	}

	if ($WORow['serialised']==1){
		/* serialised items form has a possible $_POST['CountOfInputs'] fields for entry of serial numbers - 12 rows x 5 per row
		 * if serial numbers are defined at the time of work order entry $_SESSION['DefineControlledOnWOEntry']==1 then possibly more
		 * need to inspect $_POST['CountOfInputs']
		 */
		for($i=0;$i<$_POST['CountOfInputs'];$i++){
		//need to test if the serialised item exists first already
			if (trim($_POST['SerialNo' .$i]) != '' AND  ($_SESSION['DefineControlledOnWOEntry']==0
					OR ($_SESSION['DefineControlledOnWOEntry']==1 AND $_POST['CheckedItem'.$i]==true))){
					$SQL = "SELECT COUNT(*) FROM stockserialitems
							WHERE stockid='" . $_POST['StockID'] . "'
							AND loccode = '" . $_POST['IntoLocation'] . "'
							AND serialno = '" . $_POST['SerialNo' .$i] . "'";
					$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Could not check if a serial number for the stock item already exists because');
					$DbgMsg =  _('The following SQL to test for an already existing serialised stock item was used');
					$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
					$AlreadyExistsRow = DB_fetch_row($Result);

					if ($AlreadyExistsRow[0]>0){
						prnMsg(_('The serial number entered already exists. Duplicate serial numbers are prohibited. The duplicate item is:') . ' ' . $_POST['SerialNo'.$i] ,'error');
						$InputError = true;
					}
			}
		} //end loop throught the 60 fields for serial number entry
	}//end check on pre-existing serial numbered items

	if ($_SESSION['ProhibitNegativeStock']==1){
		/*Now look for autoissue components that would go negative */
		$SQL = "SELECT worequirements.stockid,
					   stockmaster.description,
					   locstock.quantity-(" . ($QuantityReceived+$QuantityRejected)  . "*worequirements.qtypu) AS qtyleft,
					   stockmaster.controlled,
					   stockmaster.serialised
				  FROM worequirements
				  INNER JOIN stockmaster
					ON worequirements.stockid=stockmaster.stockid
				  INNER JOIN locstock
					ON worequirements.stockid=locstock.stockid
				  WHERE worequirements.wo='" . $_POST['WO'] . "'
				  AND worequirements.parentstockid='" .$_POST['StockID'] . "'
				  AND locstock.loccode='" . $WORow['loccode'] . "'
				  AND stockmaster.mbflag <>'D'
				  AND worequirements.autoissue=1";

		$ErrMsg = _('Could not retrieve the component quantity left at the location once the component items are issued to the work order (for the purposes of checking that stock will not go negative) because');
		$Result = DB_query($SQL,$ErrMsg);
		while ($NegRow = DB_fetch_array($Result)){
			if ($NegRow['controlled']==1 and  $BatchRef > "") {
						$SQL = "SELECT quantity from stockserialitems WHERE (stockid= '" . $NegRow['stockid'] . "')
										AND (loccode = '" . $WORow['loccode'] . "')
										AND (serialno = '" . $BatchRef . "')";
						$Result = DB_query($SQL);
						//$CheckLot = DB_fetch_array($Result);
						if (DB_num_rows($Result)==0){
							$InputError = true;
							prnMsg(_('Receiving the selected quantity against this work order would result in negative stock for a component. The system parameters are set to prohibit negative stocks from occurring. This manufacturing receipt cannot be created until the stock on hand is corrected.'),'error',_('Component') . ' - ' .$NegRow['component'] . ' ' . $NegRow['description'] . ' - ' . _('Negative Stock Prohibited'));
						}
						else {
							$CheckLotRow = DB_fetch_row($Result);
							if ($CheckLotRow[0]<($QuantityReceived+$QuantityRejected)){
								$InputError = true;
								prnMsg(_('Receiving the selected quantity against this work order would result in negative stock for a component. The system parameters are set to prohibit negative stocks from occurring. This manufacturing receipt cannot be created until the stock on hand is corrected.'),'error',_('Component') . ' - ' .$NegRow['component'] . ' ' . $NegRow['description'] . ' - ' . _('Negative Stock Prohibited'));
							}
						}
					}else{
			if ($NegRow['qtyleft']<0){
				prnMsg(_('Receiving the selected quantity against this work order would result in negative stock for a component. The system parameters are set to prohibit negative stocks from occurring. This manufacturing receipt cannot be created until the stock on hand is corrected.'),'error',_('Component') . ' - ' .$NegRow['component'] . ' ' . $NegRow['description'] . ' - ' . _('Negative Stock Prohibited'));
				$InputError = true;
			} // end if negative would result
			}
		} //loop around the autoissue requirements for the work order
	}

	if ($InputError==false){
/************************ BEGIN SQL TRANSACTIONS ************************/

		$Result = DB_Txn_Begin();
		/*Now Get the next WOReceipt transaction type 26 - function in SQL_CommonFunctions*/
		$WOReceiptNo = GetNextTransNo(26, $db);

		$PeriodNo = GetPeriod(Date($_SESSION['DefaultDateFormat']), $db);

		if (!isset($_POST['ReceivedDate'])){
			$_POST['ReceivedDate'] = Date($_SESSION['DefaultDateFormat']);
		}

		$SQLReceivedDate = FormatDateForSQL($_POST['ReceivedDate']);
		$StockGLCode = GetStockGLCode($_POST['StockID'],$db);

	//Recalculate the standard for the item if there were no items previously received against the work order
		if (($WORow['qtyrecd']+$WORow['qtyrejected'])==0){
			$CostResult = DB_query("SELECT SUM((materialcost+labourcost+overheadcost)*bom.quantity) AS cost
									FROM stockmaster INNER JOIN bom
									ON stockmaster.stockid=bom.component
									WHERE bom.parent='" . $_POST['StockID'] . "'
									AND bom.loccode='" . $WORow['loccode'] . "'");
			$CostRow = DB_fetch_row($CostResult);
			if (is_null($CostRow[0]) OR $CostRow[0]==0){
					$Cost =0;
			} else {
					$Cost = $CostRow[0];
			}
			//Need to refresh the worequirments with the bom components now incase they changed
			$DelWORequirements = DB_query("DELETE FROM worequirements
											WHERE wo='" . $_POST['WO'] . "'
											AND parentstockid='" . $_POST['StockID'] . "'");

			//Recursively insert real component requirements
			WoRealRequirements($db, $_POST['WO'], $WORow['loccode'], $_POST['StockID']);

			//Need to check this against the current standard cost and do a cost update if necessary
			$sql = "SELECT materialcost+labourcost+overheadcost AS cost,
						  sum(quantity) AS totalqoh,
						  labourcost,
						  overheadcost
					FROM stockmaster INNER JOIN locstock
						ON stockmaster.stockid=locstock.stockid
					WHERE stockmaster.stockid='" . $_POST['StockID'] . "'
					GROUP BY materialcost,
							labourcost,
							overheadcost";
			$ItemResult = DB_query($sql);
			$ItemCostRow = DB_fetch_array($ItemResult);

			if (($Cost + $ItemCostRow['labourcost'] + $ItemCostRow['overheadcost']) != $ItemCostRow['cost']){ //the cost roll-up cost <> standard cost

				if ($_SESSION['CompanyRecord']['gllink_stock']==1 AND $ItemCostRow['totalqoh']!=0){

					$CostUpdateNo = GetNextTransNo(35, $db);
					$PeriodNo = GetPeriod(Date($_SESSION['DefaultDateFormat']), $db);

					$ValueOfChange = $ItemCostRow['totalqoh'] * (($Cost + $ItemCostRow['labourcost'] + $ItemCostRow['overheadcost']) - $ItemCostRow['cost']);

					$SQL = "INSERT INTO gltrans (type,
								typeno,
								trandate,
								periodno,
								account,
								narrative,
								amount)
							VALUES (35,
								'" . $CostUpdateNo . "',
								'" . Date('Y-m-d') . "',
								'" . $PeriodNo . "',
								'" . $StockGLCode['adjglact'] . "',
								'" . _('Cost roll on release of WO') . ': ' . $_POST['WO'] . ' - ' . $_POST['StockID'] . ' ' . _('cost was') . ' ' . $ItemCostRow['cost'] . ' ' . _('changed to') . ' ' . $Cost . ' x ' . _('Quantity on hand of') . ' ' . $ItemCostRow['totalqoh'] . "',
								'" . (-$ValueOfChange) . "')";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The GL credit for the stock cost adjustment posting could not be inserted because');
					$DbgMsg = _('The following SQL to insert the GLTrans record was used');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

					$SQL = "INSERT INTO gltrans (type,
								typeno,
								trandate,
								periodno,
								account,
								narrative,
								amount)
							VALUES (35,
								'" . $CostUpdateNo . "',
								'" . Date('Y-m-d') . "',
								'" . $PeriodNo . "',
								'" . $StockGLCode['stockact'] . "',
								'" . _('Cost roll on release of WO') . ': ' . $_POST['WO'] . ' - ' . $_POST['StockID'] . ' ' . _('cost was') . ' ' . $ItemCostRow['cost'] . ' ' . _('changed to') . ' ' . $Cost . ' x ' . _('Quantity on hand of') . ' ' . $ItemCostRow['totalqoh'] . "',
								'" . $ValueOfChange . "')";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The GL debit for stock cost adjustment posting could not be inserted because');
					$DbgMsg = _('The following SQL to insert the GLTrans record was used');
					$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
				}

				$SQL = "UPDATE stockmaster SET
							lastcostupdate='" . Date('Y-m-d') . "',
							materialcost='" . $Cost . "',
							labourcost='" . $ItemCostRow['labourcost'] . "',
							overheadcost='" . $ItemCostRow['overheadcost'] . "',
							lastcost='" . $ItemCostRow['cost'] . "'
						WHERE stockid='" . $_POST['StockID'] . "'";

				$ErrMsg = _('The cost details for the stock item could not be updated because');
				$DbgMsg = _('The SQL that failed was');
				$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
			} //cost as rolled up now <> current standard cost  so do adjustments
		}   //qty recd previously was 0 so need to check costs and do adjustments as required

		//Do the issues for autoissue components in the worequirements table
		$AutoIssueCompsResult = DB_query("SELECT worequirements.stockid,
												 qtypu,
												 materialcost+labourcost+overheadcost AS cost,
												 stockcategory.stockact,
												 stockcategory.stocktype,
												 stockmaster.controlled,
												 stockmaster.serialised
										  FROM worequirements
										  INNER JOIN stockmaster
										  ON worequirements.stockid=stockmaster.stockid
										  INNER JOIN stockcategory
										  ON stockmaster.categoryid=stockcategory.categoryid
										  WHERE wo='" . $_POST['WO'] . "'
										  AND parentstockid='" .$_POST['StockID'] . "'
										  AND autoissue=1");

		$WOIssueNo = GetNextTransNo(28,$db);
		while ($AutoIssueCompRow = DB_fetch_array($AutoIssueCompsResult)){

			//Note that only none-controlled items can be auto-issuers so don't worry about serial nos and batches of controlled ones
			/*Cost variances calculated overall on close of the work orders so NO need to check if cost of component has been updated subsequent to the release of the WO
			*/
			if ($AutoIssueCompRow['stocktype']!='L'){
				//Need to get the previous locstock quantity for the component at the location where the WO manuafactured
				$CompQOHResult = DB_query("SELECT locstock.quantity
											FROM locstock
											WHERE locstock.stockid='" . $AutoIssueCompRow['stockid'] . "'
											AND loccode= '" . $WORow['loccode'] . "'");
				if (DB_num_rows($CompQOHResult)==1){
							$LocQtyRow = DB_fetch_row($CompQOHResult);
							$NewQtyOnHand = $LocQtyRow[0] - ($AutoIssueCompRow['qtypu'] * ($QuantityReceived+$QuantityRejected));
				} else {
							/*There must actually be some error this should never happen */
							$NewQtyOnHand = 0;
				}

				$SQL = "UPDATE locstock
							SET quantity = quantity - " . ($AutoIssueCompRow['qtypu'] * ($QuantityReceived+$QuantityRejected)). "
							WHERE locstock.stockid = '" . $AutoIssueCompRow['stockid'] . "'
							AND loccode = '" . $WORow['loccode'] . "'";

				$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The location stock record could not be updated by the issue of stock to the work order from an auto issue component because');
				$DbgMsg =  _('The following SQL to update the location stock record was used');
				$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
			} else {
				$NewQtyOnHand =0;
			}
			$SQL = "INSERT INTO stockmoves (stockid,
											type,
											transno,
											loccode,
											trandate,
											userid,
											prd,
											reference,
											brasslotno,
											price,
											qty,
											standardcost,
											newqoh)
						VALUES ('" . $AutoIssueCompRow['stockid'] . "',
								28,
								'" . $WOIssueNo . "',
								'" . $WORow['loccode'] . "',
								'" . Date('Y-m-d H:i:s') . "',
								'" . $_SESSION['UserID'] . "',
								'" . $PeriodNo . "',
								'" . $_POST['WO'] . "',
								'" . $BrassLotNo . "',
								'" . $AutoIssueCompRow['cost'] . "',
								'" . -($AutoIssueCompRow['qtypu'] *($QuantityReceived+$QuantityRejected)) . "',
								'" . $AutoIssueCompRow['cost'] . "',
								'" . $NewQtyOnHand . "')";

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('stock movement record could not be inserted for an auto-issue component because');
			$DbgMsg =  _('The following SQL to insert the stock movement records was used');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
			
			/*Get the ID of the StockMove... */
			$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');
			/* Do the Controlled Item INSERTS HERE */
			
			if ($AutoIssueCompRow['controlled'] ==1){
			//the form is different for serialised items and just batch/lot controlled items
			if ($AutoIssueCompRow['serialised']==1){
				//serialised items form has multi select box of serial numbers that contains all the available serial numbers at the location selected
				foreach ($_POST['SerialNos'] as $SerialNo){
				/*  We need to add the StockSerialItem record and
					The StockSerialMoves as well */
				//need to test if the serialised item exists first already
					if (trim($SerialNo) != ""){

						$SQL = "UPDATE stockserialitems set quantity=0
										WHERE (stockid= '" . $AutoIssueCompRow['stockid'] . "')
										AND (loccode = '" . $WORow['loccode'] . "')
										AND (serialno = '" . $SerialNo . "')";
						$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be inserted because');
						$DbgMsg =  _('The following SQL to insert the serial stock item records was used');
						$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

						/** end of handle stockserialitems records */

						/* now insert the serial stock movement */
						$SQL = "INSERT INTO stockserialmoves (stockmoveno,
																stockid,
																serialno,
																moveqty)
									VALUES ('" . $StkMoveNo . "',
											'" . $AutoIssueCompRow['stockid'] . "',
											'" . $SerialNo . "',
											-1)";
						$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
						$DbgMsg = _('The following SQL to insert the serial stock movement records was used');
						$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
					}//non blank SerialNo
				} //end for all of the potential serialised entries in the multi select box
			} else { //the item is just batch/lot controlled not serialised
			/*the form for entry of batch controlled items is only 15 possible fields */
				for($i=0;$i<15;$i++){
				/*  We need to add the StockSerialItem record and
					The StockSerialMoves as well */
					//need to test if the batch/lot exists first already
					if (trim($_POST['BatchRef' .$i]) != ""){

						$SQL = "SELECT COUNT(*) FROM stockserialitems
								WHERE stockid='" .$AutoIssueCompRow['stockid'] . "'
								AND loccode = '" . $WORow['loccode'] . "'
								AND serialno = '" . $_POST['BatchRef' .$i] . "'";
						$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Could not check if a batch/lot reference for the item already exists because');
						$DbgMsg =  _('The following SQL to test for an already existing controlled item was used');
						$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
						$AlreadyExistsRow = DB_fetch_row($Result);

						if ($AlreadyExistsRow[0]>0){
							$SQL = "UPDATE stockserialitems SET quantity = quantity - " . ($AutoIssueCompRow['qtypu'] *($QuantityReceived+$QuantityRejected)) . "
										WHERE stockid='" . $AutoIssueCompRow['stockid'] . "'
										AND loccode = '" . $WORow['loccode'] . "'
										AND serialno = '" . $_POST['BatchRef' .$i] . "'";
						} else {
							$SQL = "INSERT INTO stockserialitems (stockid,
												loccode,
												serialno,
												qualitytext,
												quantity)
												VALUES ('" . $AutoIssueCompRow['stockid'] . "',
												'" . $WORow['loccode'] . "',
												'" . $_POST['BatchRef' . $i] . "',
												'',
												'" . -($AutoIssueCompRow['qtypu'] *($QuantityReceived+$QuantityRejected)) . "')";
						}

						$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The batch/lot item record could not be inserted because');
						$DbgMsg =  _('The following SQL to insert the batch/lot item records was used');
						$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

						/** end of handle stockserialitems records */

						/** now insert the serial stock movement **/
						$SQL = "INSERT INTO stockserialmoves (stockmoveno,
											stockid,
											serialno,
											moveqty)
									VALUES ('" . $StkMoveNo . "',
											'" .$AutoIssueCompRow['stockid'] . "',
											'" . $_POST['BatchRef'.$i]  . "',
											'" . -($AutoIssueCompRow['qtypu'] *($QuantityReceived+$QuantityRejected))  . "')";
						$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
						$DbgMsg = _('The following SQL to insert the serial stock movement records was used');
						$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
					}//non blank BundleRef
				} //end for all 15 of the potential batch/lot fields received
			} //end of the batch controlled stuff
		} //end if the woitem received here is a controlled item

			//Update the workorder record with the cost issued to the work order
			$SQL = "UPDATE workorders SET
						costissued = costissued+" . ($AutoIssueCompRow['qtypu'] * ($QuantityReceived+$QuantityRejected) * $AutoIssueCompRow['cost']) ."
					WHERE wo='" . $_POST['WO'] . "'";
			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Could not update the work order cost for an auto-issue component because');
			$DbgMsg =  _('The following SQL to update the work order cost was used');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

			if ($_SESSION['CompanyRecord']['gllink_stock']==1
				AND ($AutoIssueCompRow['qtypu'] * ($QuantityReceived+$QuantityRejected) * $AutoIssueCompRow['cost'])!=0){
			//if GL linked then do the GL entries to DR wip and CR stock

				$SQL = "INSERT INTO gltrans (type,
									typeno,
									trandate,
									periodno,
									account,
									narrative,
									amount)
							VALUES (28,
								'" . $WOIssueNo . "',
								'" . Date('Y-m-d') . "',
								'" . $PeriodNo . "',
								'" . $StockGLCode['wipact'] . "',
								'" . $_POST['WO'] . ' - ' . $_POST['StockID'] . ' ' . _('Component') . ': ' . $AutoIssueCompRow['stockid'] . ' - ' . ($QuantityReceived+$QuantityRejected) . ' x ' . $AutoIssueCompRow['qtypu'] . ' @ ' . locale_number_format($AutoIssueCompRow['cost'],$_SESSION['CompanyRecord']['decimalplaces']) . "',
								'" . ($AutoIssueCompRow['qtypu'] * ($QuantityReceived+$QuantityRejected) * $AutoIssueCompRow['cost']) . "')";

					$ErrMsg =   _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The WIP side of the work order issue GL posting could not be inserted because');
					$DbgMsg =  _('The following SQL to insert the WO issue GLTrans record was used');
					$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

				$SQL = "INSERT INTO gltrans (type,
									typeno,
									trandate,
									periodno,
									account,
									narrative,
									amount)
							VALUES (28,
								'" . $WOIssueNo . "',
								'" . Date('Y-m-d') . "',
								'" . $PeriodNo . "',
								'" . $AutoIssueCompRow['stockact'] . "',
								'" . $_POST['WO'] . ' - ' . $_POST['StockID'] . ' -> ' . $AutoIssueCompRow['stockid'] . ' - ' . ($QuantityReceived+$QuantityRejected) . ' x ' . $AutoIssueCompRow['qtypu'] . ' @ ' . locale_number_format($AutoIssueCompRow['cost'],$_SESSION['CompanyRecord']['decimalplaces']) . "',
								'" . -($AutoIssueCompRow['qtypu'] * ($QuantityReceived+$QuantityRejected) * $AutoIssueCompRow['cost']) . "')";

					$ErrMsg =   _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock side of the work order issue GL posting could not be inserted because');
					$DbgMsg =  _('The following SQL to insert the WO issue GLTrans record was used');
					$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
			}//end GL-stock linked

		} //end of auto-issue loop for all components set to auto-issue


		/* Need to get the current location quantity will need it later for the stock movement */
		$SQL = "SELECT locstock.quantity
				FROM locstock
				WHERE locstock.stockid='" . $_POST['StockID'] . "'
				AND loccode= '" . $_POST['IntoLocation'] . "'";

		$Result = DB_query($SQL);
		if (DB_num_rows($Result)==1){
			$LocQtyRow = DB_fetch_row($Result);
			$QtyOnHandPrior = $LocQtyRow[0];
		} else {
		/*There must actually be some error this should never happen */
			$QtyOnHandPrior = 0;
		}

		$SQL = "UPDATE locstock
				SET quantity = locstock.quantity + " . $QuantityReceived . "
				WHERE locstock.stockid = '" . $_POST['StockID'] . "'
				AND loccode = '" . $_POST['IntoLocation'] . "'";

		$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The location stock record could not be updated because');
		$DbgMsg =  _('The following SQL to update the location stock record was used');
		$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

		$WOReceiptNo = GetNextTransNo(26,$db);
		/*Insert stock movements - with unit cost */

		$SQL = "INSERT INTO stockmoves (stockid,
										type,
										transno,
										loccode,
										trandate,
										userid,
										price,
										prd,
										reference,
										brasslotno,
										qty,
										qtyrejected,
										standardcost,
										newqoh)
					VALUES ('" . $_POST['StockID'] . "',
							26,
							'" . $WOReceiptNo . "',
							'" . $_POST['IntoLocation'] . "',
							'" . Date('Y-m-d H:i:s') . "',
							'" . $_SESSION['UserID'] . "',
							'" . $WORow['stdcost'] . "',
							'" . $PeriodNo . "',
							'" . $_POST['WO'] . "',
							'" . $BrassLotNo . "',
							'" . $QuantityReceived . "',
							'" . $QuantityRejected . "',
							'" . $WORow['stdcost'] . "',
							'" . ($QtyOnHandPrior + $QuantityReceived) . "')";

		$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('stock movement records could not be inserted when processing the work order receipt because');
		$DbgMsg =  _('The following SQL to insert the stock movement records was used');
		$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

		/*Get the ID of the StockMove... */
		$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');
		/* Do the Controlled Item INSERTS HERE */

		if ($WORow['controlled'] ==1){
			//the form is different for serialised items and just batch/lot controlled items
			if ($WORow['serialised']==1){
				//serialised items form has a possible 60 fields for entry of serial numbers - 12 rows x 5 per row
				for($i=0;$i<$_POST['CountOfInputs'];$i++){
				/*  We need to add the StockSerialItem record and
					The StockSerialMoves as well */
					if (trim($_POST['SerialNo' .$i]) != ""){
						if ($_SESSION['DefineControlledOnWOEntry']==0 OR
							($_SESSION['DefineControlledOnWOEntry']==1 AND $_POST['CheckItem'.$i]==true)){

							$LastRef = trim($_POST['SerialNo' .$i]);
							//already checked to ensure there are no duplicate serial numbers entered
							if (isset($_POST['QualityText'.$i])){
								$QualityText = $_POST['QualityText'.$i];
							} else {
								$QualityText ='';
							}

							if(empty($_POST['ExpiryDate'])){
									$SQL = "INSERT INTO stockserialitems (stockid,
																	loccode,
																	serialno,
																	quantity,
																	qualitytext)
											VALUES ('" . $_POST['StockID'] . "',
													'" . $_POST['IntoLocation'] . "',
													'" . $_POST['SerialNo' . $i] . "',
													1,
													'" . $QualityText . "')";
							}else{// Store expiry date for perishable product

								$SQL = "INSERT INTO stockserialitems(stockid,
																	loccode,
																	serialno,
																	quantity,
																	qualitytext,
																	expirationdate)
											VALUES ('" . $_POST['StockID'] . "',
												'" . $_POST['IntoLocation'] . "',
												'" . $_POST['SerialNo' . $i] . "',
												1,
												'" . $QualityText . "',
												'" . FormatDateForSQL($_POST['ExpiryDate']) . "')";
							}

							$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be inserted because');
							$DbgMsg =  _('The following SQL to insert the serial stock item records was used');
							$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

							/** end of handle stockserialitems records */

							/** now insert the serial stock movement **/
							$SQL = "INSERT INTO stockserialmoves (stockmoveno,
																	stockid,
																	serialno,
																	moveqty)
										VALUES ('" . $StkMoveNo . "',
												'" . $_POST['StockID'] . "',
												'" . $_POST['SerialNo' .$i] . "',
												1)";
							$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
							$DbgMsg = _('The following SQL to insert the serial stock movement records was used');
							$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

							if ($_SESSION['DefineControlledOnWOEntry']==1){
								//need to delete the item from woserialnos
								$SQL = "DELETE FROM	woserialnos
											WHERE wo='" . $_POST['WO'] . "'
											AND stockid='" . $_POST['StockID'] ."'
											AND serialno='" . $_POST['SerialNo'.$i] . "'";
								$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The predefined serial number record could not be deleted because');
								$DbgMsg = _('The following SQL to delete the predefined work order serial number record was used');
								$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
							}
						}//end prefined controlled items or not
						if ($_SESSION['QualityLogSamples']==1) {
							CreateQASample($_POST['StockID'],$_POST['SerialNo'.$i], '', 'Created from Work Order', 0, 0,$db);
						}
					} //non blank SerialNo
				} //end for all of the potential serialised fields received
			} else { //the item is just batch/lot controlled not serialised
			/*the form for entry of batch controlled items is only 15 possible fields */
				for($i=0;$i<15;$i++){
				/*  We need to add the StockSerialItem record and
					The StockSerialMoves as well */
				//need to test if the batch/lot exists first already
					if (trim($_POST['BatchRef' .$i]) != ""){
						$LastRef = trim($_POST['BatchRef' .$i]);
						$SQL = "SELECT COUNT(*) FROM stockserialitems
								WHERE stockid='" . $_POST['StockID'] . "'
								AND loccode = '" . $_POST['IntoLocation'] . "'
								AND serialno = '" . $_POST['BatchRef' .$i] . "'";
						$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Could not check if a serial number for the stock item already exists because');
						$DbgMsg =  _('The following SQL to test for an already existing serialised stock item was used');
						$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
						$AlreadyExistsRow = DB_fetch_row($Result);
						if (isset($_POST['QualityText'.$i])){
							$QualityText = $_POST['QualityText'.$i];
						} else {
							$QualityText ='';
						}
						if ($AlreadyExistsRow[0]>0){
							$SQL = "UPDATE stockserialitems SET quantity = quantity + " . filter_number_format($_POST['Qty' . $i]) . ",
																qualitytext = '" . $QualityText . "'
										WHERE stockid='" . $_POST['StockID'] . "'
										AND loccode = '" . $_POST['IntoLocation'] . "'
										AND serialno = '" . $_POST['BatchRef' .$i] . "'";
						} else {
							if(empty($_POST['ExpiryDate'])){
								$SQL = "INSERT INTO stockserialitems (stockid,
																loccode,
																serialno,
																quantity,
																qualitytext)
										VALUES ('" . $_POST['StockID'] . "',
												'" . $_POST['IntoLocation'] . "',
												'" . $_POST['BatchRef' . $i] . "',
												'" . filter_number_format($_POST['Qty'.$i]) . "',
												'" . $_POST['QualityText'] . "')";


							}else{	//If it's a perishable product, add expiry date

								$SQL = "INSERT INTO stockserialitems (stockid,
																loccode,
																serialno,
																quantity,
																qualitytext,
																expirationdate)
										VALUES ('" . $_POST['StockID'] . "',
												'" . $_POST['IntoLocation'] . "',
												'" . $_POST['BatchRef' . $i] . "',
												'" . filter_number_format($_POST['Qty'.$i]) . "',
												'" . $_POST['QualityText'] . "',
												'" . FormatDateForSQL($_POST['ExpiryDate']) . "')";
							}
						}
						$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be inserted because');
						$DbgMsg =  _('The following SQL to insert the serial stock item records was used');
						$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

						/** end of handle stockserialitems records */

						/** now insert the serial stock movement **/
						$SQL = "INSERT INTO stockserialmoves (stockmoveno,
														stockid,
														serialno,
														moveqty,
														rejectedqty)
									VALUES ('" . $StkMoveNo . "',
											'" . $_POST['StockID'] . "',
											'" . $_POST['BatchRef'.$i]  . "',
											'" . filter_number_format($_POST['Qty'.$i])  . "',
											'" . filter_number_format($_POST['RQty'.$i])  . "')";
						$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
						$DbgMsg = _('The following SQL to insert the serial stock movement records was used');
						$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

						if ($_SESSION['DefineControlledOnWOEntry']==1){
							//check how many of the batch/bundle/lot has been received
							$SQL = "SELECT sum(moveqty) FROM stockserialmoves
										INNER JOIN stockmoves ON stockserialmoves.stockmoveno=stockmoves.stkmoveno
										WHERE stockmoves.type=26
										AND stockserialmoves.stockid='" . $_POST['StockID'] . "'
										AND stockserialmoves.serialno='" . 	$_POST['BatchRef'.$i] . "'";

							$BatchTotQtyResult = DB_query($SQL);
							$BatchTotQtyRow = DB_fetch_row($BatchTotQtyResult);
							if ($BatchTotQtyRow[0] >= $_POST['QtyReqd'.$i]){
								//need to delete the item from woserialnos
								$SQL = "DELETE FROM	woserialnos
										WHERE wo='" . $_POST['WO'] . "'
										AND stockid='" . $_POST['StockID'] ."'
										AND serialno='" . $_POST['BatchRef'.$i] . "'";
								$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The predefined batch/lot/bundle record could not be deleted because');
								$DbgMsg = _('The following SQL to delete the predefined work order batch/bundle/lot record was used');
								$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
							}
						}
						if ($_SESSION['QualityLogSamples']==1) {
							CreateQASample($_POST['StockID'],$_POST['BatchRef'.$i], '', 'Created from Work Order', 0 ,0,$db);
						}
					}//non blank BundleRef
				} //end for all of the potential batch/lot fields received
			} //end of the batch controlled stuff
		} //end if the woitem received here is a controlled item


		/* If GLLink_Stock then insert GLTrans to debit the GL Code  and credit GRN Suspense account at standard cost*/
		if ($_SESSION['CompanyRecord']['gllink_stock']==1 AND ($WORow['stdcost']*$QuantityReceived)!=0){
		/*GL integration with stock is activated so need the GL journals to make it so */

		/*first the debit the finished stock of the item received from the WO
		  the appropriate account was already retrieved into the $StockGLCode variable as the Processing code is kicked off
		  it is retrieved from the stock category record of the item by a function in SQL_CommonFunctions.inc*/

			$SQL = "INSERT INTO gltrans (type,
									typeno,
									trandate,
									periodno,
									account,
									narrative,
									amount)
							VALUES (26,
								'" . $WOReceiptNo . "',
								'" . Date('Y-m-d') . "',
								'" . $PeriodNo . "',
								'" . $StockGLCode['stockact'] . "',
								'" . $_POST['WO'] . " " . $_POST['StockID'] . " - " . DB_escape_string($WORow['description']) . ' x ' . $QuantityReceived . " @ " . locale_number_format($WORow['stdcost'],$_SESSION['CompanyRecord']['decimalplaces']) . "',
								'" . ($WORow['stdcost'] * $QuantityReceived) . "')";

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The receipt of work order finished stock GL posting could not be inserted because');
			$DbgMsg = _('The following SQL to insert the work order receipt of finished items GLTrans record was used');
			$Result = DB_query($SQL,$ErrMsg, $DbgMsg, true);

		/*now the credit WIP entry*/
			$SQL = "INSERT INTO gltrans (type,
									typeno,
									trandate,
									periodno,
									account,
									narrative,
									amount)
							VALUES (26,
								'" . $WOReceiptNo . "',
								'" . Date('Y-m-d') . "',
								'" . $PeriodNo . "',
								'" . $StockGLCode['wipact'] . "',
								'" . $_POST['WO'] . " " . $_POST['StockID'] . " - " . DB_escape_string($WORow['description']) . ' x ' . $QuantityReceived . " @ " . locale_number_format($WORow['stdcost'],$_SESSION['CompanyRecord']['decimalplaces']) . "',
								'" . -($WORow['stdcost'] * $QuantityReceived) . "')";

			$ErrMsg =   _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The WIP credit on receipt of finished items from a work order GL posting could not be inserted because');
			$DbgMsg =  _('The following SQL to insert the WIP GLTrans record was used');
			$Result = DB_query($SQL, $ErrMsg, $DbgMsg,true);

		} /* end of if GL and stock integrated and standard cost !=0 */

		if (!isset($LastRef)) {
			$LastRef = '';
		}
		//update the wo with the new qtyrecd
		$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' ._('Could not update the work order item record with the total quantity received because');
		$DbgMsg = _('The following SQL was used to update the work order');
		$UpdateWOResult =DB_query("UPDATE woitems
									SET qtyrecd=qtyrecd+" . $QuantityReceived . ",
										qtyrejected=qtyrejected+" . $QuantityRejected . ",
										nextlotsnref='" . $LastRef . "'
									WHERE wo='" . $_POST['WO'] . "'
									AND stockid='" . $_POST['StockID'] . "'",
									$ErrMsg,
									$DbgMsg,
									true);


		$Result = DB_Txn_Commit();

		prnMsg(_('The receipt of') . ' ' . ($QuantityReceived+$QuantityRejected) . ' ' . $WORow['units'] . ' ' . _('of')  . ' ' . $_POST['StockID'] . ' - ' . $WORow['description'] . ' ' . _('against work order') . ' '. $_POST['WO'] . ' ' . _('has been processed'),'info');
		echo '<a href="' . $RootPath . '/SelectWorkOrder.php">' . _('Select a different work order for receiving finished stock against'). '</a>';
		unset($_POST['WO']);
		unset($_POST['StockID']);
		unset($_POST['IntoLocation']);
		unset($_POST['Process']);
		for ($i=1;$i<$_POST['CountOfInputs'];$i++){
			unset($_POST['SerialNo'.$i]);
			unset($_POST['BatchRef'.$i]);
			unset($_POST['Qty'.$i]);
			unset($_POST['QualityText'.$i]);
			unset($_POST['QtyReqd'.$i]);
		}
        echo '</div>';
        echo '</form>';
		/*end of process work order goods received entry */
		include('includes/footer.inc');
		exit;
	} //end if there were not input errors reported - so the processing was allowed to continue
} //end of if the user hit the process button

/* Always display quantities received and recalc balance for all items on the order */

$ErrMsg = _('Could not retrieve the details of the selected work order item');

$WOResult = DB_query("SELECT workorders.loccode,
							 locations.locationname,
							 workorders.requiredby,
							 workorders.startdate,
							 workorders.closed,
							 stockmaster.description,
							 stockmaster.controlled,
							 stockmaster.serialised,
							 stockmaster.decimalplaces,
							 stockmaster.units,
							 stockmaster.perishable,
							 woitems.qtyreqd,
							 woitems.qtyrecd,
							 woitems.qtyrejected,
							 woitems.stdcost,
							 woitems.nextlotsnref,
							 workorders.calibre
					FROM workorders INNER JOIN locations
					ON workorders.loccode=locations.loccode
					INNER JOIN woitems
					ON workorders.wo=woitems.wo
					INNER JOIN stockmaster
					ON woitems.stockid=stockmaster.stockid
					INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canupd=1
					WHERE woitems.stockid='" . $_POST['StockID'] . "' AND workorders.wo='".$_POST['WO'] . "'",
					$ErrMsg);

if (DB_num_rows($WOResult)==0){
	prnMsg(_('The selected work order item cannot be retrieved from the database'),'info');
    echo '</div>';
    echo '</form>';
	include('includes/footer.inc');
	exit;
}
$WORow = DB_fetch_array($WOResult);

if ($WORow['closed']==1){
	prnMsg(_('The selected work order has been closed and variances calculated and posted. No more receipts of manufactured items can be received against this work order. You should make up a new work order to receive this item against.'),'info');
    echo '</div>';
    echo '</form>';
	include('includes/footer.inc');
	exit;
}

if (!isset($_POST['ReceivedDate'])){
	$_POST['ReceivedDate'] = Date($_SESSION['DefaultDateFormat']);
}
echo '<table class="selection">
		<tr>
			<td>' . _('Receive work order') . ':</td>
			<td>' . $_POST['WO']  . '</td><td>' . _('Item') . ':</td>
			<td>' . $_POST['StockID'] . ' - ' . $WORow['description'] . '</td>
		</tr>
		 <tr>
			<td>' . _('Manufactured at') . ':</td>
		 	<td>' . $WORow['locationname'] . '</td>
			<td>' . _('Required By') . ':</td>
			<td>' . ConvertSQLDate($WORow['requiredby']) . '</td>
		</tr>
		 <tr>
			<td>' . _('Quantity Ordered') . ':</td>
		 	<td class="number">' . locale_number_format($WORow['qtyreqd'],$WORow['decimalplaces']) . '</td>
			<td colspan="2">' . $WORow['units'] . '</td>
		</tr>
		 <tr>
			<td>' . _('Already Received') . ':</td>
		 	<td class="number">' . locale_number_format($WORow['qtyrecd'],$WORow['decimalplaces']) . '</td>
			<td colspan="2">' . $WORow['units'] . '</td>
		</tr>
		 <tr>
			<td>' . _('Already Rejected') . ':</td>
		 	<td class="number">' . locale_number_format($WORow['qtyrejected'],$WORow['decimalplaces']) . '</td>
			<td colspan="2">' . $WORow['units'] . '</td>
		</tr>
		 <tr>
			<td>' . _('Date Received') . ':</td>
			<td>' . Date($_SESSION['DefaultDateFormat']) . '</td>';
		//add expiry date for perishable product
		if($WORow['perishable']==1){
			echo '<td>' . _('Expiry Date') . ':<td>
			      <td><input type="text" name="ExpiryDate" class="date" alt="'.$_SESSION['DefaultDateFormat'].'"
				         required  />
			      </td>';
		}

		echo '<td>' . _('Received Into') . ':</td>
			<td><select name="IntoLocation">';


if (!isset($_POST['IntoLocation'])){
		$_POST['IntoLocation']=$WORow['loccode'];
}
$LocResult = DB_query("SELECT locations.loccode,locationname
						FROM locations
						INNER JOIN locationusers
							ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "'
							AND locationusers.canupd=1
						WHERE locations.usedforwo = 1");
while ($LocRow = DB_fetch_array($LocResult)){
	if ($_POST['IntoLocation'] ==$LocRow['loccode']){
		echo '<option selected="selected" value="' . $LocRow['loccode'] .'">' . $LocRow['locationname'] . '</option>';
	} else {
		echo '<option value="' . $LocRow['loccode'] .'">' . $LocRow['locationname'] . '</option>';
	}
}
echo '</select></td>
	</tr>
	</table>
	<br />';

//Now Setup the form for entering quantities received
echo '<table class="selection">';
echo '<tr>
            <td></td>
			<td>' . _('Brass Batch/Lot No') . ':</td><td>';
			$Lesult = DB_query("SELECT *
						FROM wobrasslotno,workorders
						WHERE wobrasslotno.wonumber=workorders.wo AND workorders.closed=0 AND wobrasslotno.calibre = '".$WORow['calibre']."'");
	echo '<select name="BrassLot" required="required">';
	echo '<option value="">--Select Brass Lot No--</option>';
	echo '<option value="N/A">-- Not Applicable --</option>';
while ($LoRow = DB_fetch_array($Lesult)){
			echo '<option value="'.$LoRow['lotno'].'">'.$LoRow['lotno'].'</option>';
		}
	echo '</select>';
	echo'</td>
		</tr>';
if($WORow['controlled']==1){ //controlled
	$LotSNRefLength =mb_strlen($WORow['nextlotsnref']);
	$EndOfTextPartPointer = 0;
	if (is_numeric($WORow['nextlotsnref'])){
		$LotSNRefNumeric =$WORow['nextlotsnref'];
		$StringBitOfLotSNRef ='';
	} else { //try to determine if the serial ref is an amalgamation of a text part and a numerical part and increment the numerical part only
		while (is_numeric(mb_substr($WORow['nextlotsnref'],$LotSNRefLength-$EndOfTextPartPointer-1)) AND
			mb_substr($WORow['nextlotsnref'],$LotSNRefLength-$EndOfTextPartPointer-1,1)!='-'){
			$EndOfTextPartPointer++;
			$LotSNRefNumeric = mb_substr($WORow['nextlotsnref'],$LotSNRefLength-$EndOfTextPartPointer);
			$StringBitOfLotSNRef = mb_substr($WORow['nextlotsnref'],0,$LotSNRefLength-$EndOfTextPartPointer);
		}
	}

	if ($WORow['serialised']==1){ //serialised
		echo '<tr>
				<th colspan="5">' . _('Serial Numbers Received') . '</th>
			</tr>
			<tr>';

		if ($_SESSION['DefineControlledOnWOEntry']==1){ //then potentially serial numbers already set up
			//retrieve the woserialnos
			$WOSNResult = DB_query("SELECT serialno, qualitytext
									FROM woserialnos
									WHERE wo='" . $_POST['WO'] . "'
									AND stockid='" . $_POST['StockID'] . "'");
			if (DB_num_rows($WOSNResult)==0){
				echo '<th colspan="5">' . _('No serial numbers defined yet') . '</th></tr>';
			} else {
				$i=0; //the SerialNo counter
				while ($WOSNRow = DB_fetch_row($WOSNResult)){
					if (($i/5 -intval($i/5))==0){
						echo '</tr>
							<tr>';
					}
					echo '<td><input type="checkbox" name="CheckItem' . $i . '" />' .  $WOSNRow[0]  . '<input type="hidden" name="SerialNo' . $i . '" value="' . $WOSNRow[0] . '" /><input type="hidden" name="QualityText' . $i . '" value="' . $WOSNRow[1] . '" /></td>';
					$i++;
				}
			}
		} else { //serial numbers not yet defined need to enter them manually now
			for ($i=0;$i<60;$i++){
				if (($i/5 -intval($i/5))==0){
					echo '</tr>
						<tr>';
				}
				echo '<td><input type="textbox" name="SerialNo' . $i . '" placeholder="'._('Serial No').'" ';
				if ($i==0){
					echo 'value="' . $StringBitOfLotSNRef . ($LotSNRefNumeric + 1) . '"';
				}
				echo '" /></td>';

			}
		}
		if (!isset($i)){
			$i=0;
		}
        echo '<td><input type="hidden" name="CountOfInputs" value="' . $i . '" /></td>';
		echo '</tr>';
		echo '<tr>
				<td colspan="5"></td>
			</tr>
			</table>';
		echo '<br />
			<div class="centre">
				<input type="submit" name="Process" value="' . _('Process Manufactured Items Received') . '" />
			</div>';
	} else { //controlled but not serialised - just lot/batch control

		if ($_SESSION['DefineControlledOnWOEntry']==1){ //then potentially batches/lots already set up
				echo '<tr>
				<th colspan="2">' . _('Batch/Lots Received') . '</th>
			</tr>';
			//retrieve them from woserialnos
			$WOSNResult = DB_query("SELECT serialno,
											quantity,
											qualitytext
									FROM woserialnos
									WHERE wo='" . $_POST['WO'] . "'
									AND stockid='" . $_POST['StockID'] . "'");
			if (DB_num_rows($WOSNResult)==0){
				echo '<th colspan="5">' . _('No batches/lots defined yet') . '</th>
					</tr>';
			} else {
				$i=0; //the Batch counter
				while ($WOSNRow = DB_fetch_row($WOSNResult)){
					if (($i/5 -intval($i/5))==0){
						echo '</tr><tr>';
					}
					echo '<td><input type="textbox" name="BatchRef' . $i . '" value="' . $WOSNRow[0] . '" placeholder="'._('Batch/Lot No').'" /></td>
						  <td><input type="textbox" class="number" name="Qty' . $i . '" placeholder="'._('Accepted Quantity').'" />
						  		<input type="hidden" name="QualityText' . $i . '" value="' . $WOSNRow[2] . '" />
						  		<input type="hidden" name="QtyReqd' . $i . '" value="' . locale_number_format($WOSNRow[1],'Variable') . '" /></td>
						  	</tr>';
					if (($i/5 -intval($i/5))==0){
						echo '</tr><tr>';
					}
					echo '<td></td>
						  <td><input type="textbox" class="number" name="RQty' . $i . '" value="0" placeholder="'._('Rejected Quantity').'" /></td>
						  	</tr>';
					$i++;
				}
			}
		} else { 
		echo '<tr>
				<th>' . _('Batch/Lots') . '</th><th>' . _('Received') . '</th><th>' . _('Rejected') . '</th>
			</tr>';
		// batches/lots yet to be set up enter them manually
			for ($i=0;$i<1;$i++){
				echo '<tr>
						<td><input type="textbox" name="BatchRef' . $i .'" placeholder="'._('Batch/Lot No').'" ';

				if ($i==0){
					echo 'value="' . $StringBitOfLotSNRef . ($LotSNRefNumeric + 1) . '"';
				}
				echo ' /></td>
						  <td><input type="textbox" class="number" name="Qty' . $i .'" /></td>
						  <td><input type="textbox" class="number" name="RQty' . $i . '" placeholder="'._('Rejected Quantity').'" /></td>
						  </tr>';
			}
		}
		echo '<tr>
                 <td><input type="hidden" name="CountOfInputs" value="' . $i . '" /></td>
              </tr>
              </table>';
		echo '<br />
			<div class="centre">
				<input type="submit" name="Process" value="' . _('Process Manufactured Items Received') . '" />
			</div>';
	} //end of lot/batch control
} else { //not controlled - an easy one!

	echo '
		<tr>
            <td><input type="hidden" name="CountOfInputs" value="1" /></td>
			<td>' . _('Accepted Quantity') . ':</td>
			<td><input type="text" class="number" name="Qty" required="required" title="'._('The input must be numeric').'" placeholder="'._('Accepted Qty').'" /></td>
		</tr>
		<tr>
            <td></td>
			<td>' . _('Rejected Quantity') . ':</td>
			<td><input type="text" class="number" name="RQty" title="'._('The input must be numeric').'" required="required" value="0" placeholder="'._('Rejected Qty').'" /></td>
		</tr>
		</table>';
	echo '<br />
		<div class="centre">
			<input type="submit" name="Process" value="' . _('Process Manufactured Items Received') . '" />
		</div>';
}
echo '</div>';
echo '</form>';

include('includes/footer.inc');
?>