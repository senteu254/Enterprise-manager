<?php

/* $Id: PO_OrderDetails.php 6941 2014-10-26 23:18:08Z daintree $*/

include('includes/session.inc');

if (isset($_GET['ContractNumber'])) {
	$Title = _('View Contract No.').' ' . $_GET['ContractNumber'];
	$_GET['ContractNumber']=$_GET['ContractNumber'];
} else {
	$Title = _('View Contract');
}
include('includes/header.inc');

//echo '<a href="'. $RootPath . '/FarmContractInquiry.php">' .  _('Back to Contract View'). '</a><br />';
//echo '<p class="page_title_text">
		//	<img src="'.$RootPath.'/css/'.$Theme.'/images/contract.png" title="' . _('Contract') . '" alt="" />';
			//echo _('View Contract')  . ' ';
//echo _('Contract') . '<br />' . $_SESSION['Contract'.$identifier]->CustomerName . '<br />' . $_SESSION['Contract'.$identifier]->ContractDescription . '</p>';
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$sql = "SELECT * FROM farmcontracts WHERE contractNo = '" .$_GET['ContractNumber']. "'";
		$GetResult = DB_query($sql, $ErrMsg);

if (DB_num_rows($GetResult)!=1) {
	echo '<br /><br />';
	if (DB_num_rows($GetResult) == 0){
		prnMsg ( _('Unable to locate this Contract Number') . ' '. $_GET['ContractNumber'] . '. ' . _('Please look up another one') . '. ' . _('The contract requested could not be retrieved') . ' - ' . _('the SQL returned either 0 or several contracts'), 'error');
	} else {
		prnMsg ( _('The contract requested could not be retrieved') . ' - ' . _('the SQL returned either several contracts'), 'error');
	}
        echo '<table class="table_index">
                <tr>
					<td class="menu_group_item">
						<li><a href="'. $RootPath . '/FarmContractInquiry.php">' . _('Outstanding Contracts') . '</a></li>
					</td>
				</tr>
				</table>';

	include('includes/footer.inc');
	exit;
}
$myrow = DB_fetch_array($GetResult);		
		
		
echo '<table class="selection">';
	echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/supplier.png" title="' .
		_('Viewing Contractww No.') . '" alt="" />' . ' ' . $Title . '</p>';
		echo'<tr>
			<th colspan="8"><b>' .  _('Contract Details'). '</b></th>
		</tr>';
	echo '<a href="'. $RootPath . '/FarmContractInquiry.php">' .  _('Back to Contract Inquiry'). '</a><br />';
	echo '<table class="selection" cellpadding="2">
		<tr>
			<th colspan="8"><b>' .  _('Contract Details'). '</b></th>
		</tr>
		<tr>
			<td><b>' . _('Supplier Code'). '</b></td>
			</tr>
			<tr>
			<td><a href="SelectSupplier.php?SupplierID='.$myrow['debtorno'].'">' . $myrow['debtorno'] . '</a></td>
		</tr>
		<tr>
			<td><b>' . _('Contract Refferences'). '</b></td>
			<tr>
			<td>' . ($myrow['contractref']) . '</td>
		</tr>
		<tr>
			<td><b>' . _('Contract Descriptions'). '</b></td>
			</tr>
			<tr>
			<td>' . wordwrap($myrow['contractdescription'],70,"<br>\n",TRUE). '</td>
		</tr>
	</table>';
	
	
	
	echo'</table>';
//echo wordwrap($str,15,"<br>\n",TRUE);
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Do the processing here after the variances are all calculated above
if (isset($_POST['CloseContract']) AND $_SESSION['Contract'.$identifier]->Status==2){

	include('includes/SQL_CommonFunctions.inc');

	$GLCodes = GetStockGLCode($_SESSION['Contract'.$identifier]->ContractRef,$db);
//Compare actual costs to original budgeted contract costs - if actual > budgeted - CR WIP and DR usage variance
	$Variance =  ($OtherReqtsBudget+$ContractBOMBudget)-($OtherReqtsActual+$ContractBOMActual);

	$ContractCloseNo = GetNextTransNo( 32  ,$db);
	$PeriodNo = GetPeriod(Date($_SESSION['DefaultDateFormat']), $db);

	DB_Txn_Begin();

	$SQL = "INSERT INTO gltrans (type,
								typeno,
								trandate,
								periodno,
								account,
								narrative,
								amount)
					VALUES ( 32,
							'" . $ContractCloseNo . "',
							'" . Date('Y-m-d') . "',
							'" . $PeriodNo . "',
							'" . $GLCodes['wipact'] . "',
							'" . _('Variance on contract') . ' ' . $_SESSION['Contract'.$identifier]->ContractRef . "',
							'" . -$Variance . "')";

	$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The gl entry of WIP for the variance on closing the contract could not be inserted because');
	$DbgMsg = _('The following SQL to insert the GLTrans record was used');
	$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
	$SQL = "INSERT INTO gltrans (type,
								typeno,
								trandate,
								periodno,
								account,
								narrative,
								amount)
					VALUES ( 32,
							'" . $ContractCloseNo . "',
							'" . Date('Y-m-d') . "',
							'" . $PeriodNo . "',
							'" . $GLCodes['materialuseagevarac'] . "',
							'" . _('Variance on contract') . ' ' . $_SESSION['Contract'.$identifier]->ContractRef . "',
							'" . $Variance . "')";

	$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The gl entry of WIP for the variance on closing the contract could not be inserted because');
	$DbgMsg = _('The following SQL to insert the GLTrans record was used');
	$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

//Now update the status of the contract to closed
	$SQL = "UPDATE contracts
				SET status=3
				WHERE contractref='" . $_SESSION['Contract'.$identifier]->ContractRef . "'";
	$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The status of the contract could not be updated to closed because');
	$DbgMsg = _('The following SQL to change the status of the contract was used');
	$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

/*Check if the contract work order is still open */
	$CheckIfWOOpenResult = DB_query("SELECT closed
									FROM workorders
									WHERE wo='" . $_SESSION['Contract'.$identifier]->WO . "'");
	$CheckWORow=DB_fetch_row($CheckIfWOOpenResult);
	if ($CheckWORow[0]==0){
		//then close the work order
		$CloseWOResult =DB_query("UPDATE workorders
									SET closed=1
									WHERE wo='" . $_SESSION['Contract'.$identifier]->WO . "'",
									_('Could not update the work order to closed because:'),
									_('The SQL used to close the work order was:'),
									true);


	/* Check if the contract BOM has received the contract item manually
	 * If not then process this as by closing the contract the user is saying it is complete
	 *  If work done on the contract is a write off then the user must also write off the stock of the contract item as a separate job
	 */

		$result =DB_query("SELECT qtyrecd FROM woitems
							WHERE stockid='" . $_SESSION['Contract'.$identifier]->ContractRef . "'
							AND wo='" . $_SESSION['Contract'.$identifier]->WO . "'");
		if (DB_num_rows($result)==1) {
			$myrow=DB_fetch_row($result);
			if ($myrow[0]==0){ //then the contract wo has not been received (it will only ever be for 1 item)

				$WOReceiptNo = GetNextTransNo(26, $db);

				/* Need to get the current location quantity will need it later for the stock movement */
				$SQL = "SELECT locstock.quantity
						FROM locstock
						WHERE locstock.stockid='" . $_SESSION['Contract'.$identifier]->ContractRef . "'
						AND loccode= '" . $_SESSION['Contract'.$identifier]->LocCode . "'";

				$Result = DB_query($SQL);
				if (DB_num_rows($Result)==1){
					$LocQtyRow = DB_fetch_row($Result);
					$QtyOnHandPrior = $LocQtyRow[0];
				} else {
				/*There must actually be some error this should never happen */
					$QtyOnHandPrior = 0;
				}

				$SQL = "UPDATE locstock
						SET quantity = locstock.quantity + 1
						WHERE locstock.stockid = '" . $_SESSION['Contract'.$identifier]->ContractRef . "'
						AND loccode= '" . $_SESSION['Contract'.$identifier]->LocCode . "'";

				$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The location stock record could not be updated because');
				$DbgMsg =  _('The following SQL to update the location stock record was used');
				$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

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
												qty,
												standardcost,
												newqoh)
							VALUES ('" . $_SESSION['Contract'.$identifier]->ContractRef . "',
									26,
									'" . $WOReceiptNo . "',
									'"  . $_SESSION['Contract'.$identifier]->LocCode . "',
									'" . Date('Y-m-d') . "',
									'" . $_SESSION['UserID'] . "',
									'" . ($OtherReqtsBudget+$ContractBOMBudget) . "',
									'" . $PeriodNo . "',
									'" .  $_SESSION['Contract'.$identifier]->WO . "',
									1,
									'" .  ($OtherReqtsBudget+$ContractBOMBudget)  . "',
									'" . ($QtyOnHandPrior + 1) . "')";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('stock movement records could not be inserted when processing the work order receipt because');
				$DbgMsg =  _('The following SQL to insert the stock movement records was used');
				$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);

				/*Get the ID of the StockMove... */
				$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');

				/* If GLLink_Stock then insert GLTrans to debit the GL Code  and credit GRN Suspense account at standard cost*/
				if ($_SESSION['CompanyRecord']['gllink_stock']==1 AND ($OtherReqtsBudget+$ContractBOMBudget)!=0){
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
											'" . $GLCodes['stockact'] . "',
											'" . $_SESSION['Contract'.$identifier]->WO . ' ' . $_SESSION['Contract'.$identifier]->ContractRef  . ' -  x 1 @ ' . locale_number_format(($OtherReqtsBudget+$ContractBOMBudget),2) . "',
											'" . ($OtherReqtsBudget+$ContractBOMBudget) . "')";

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The receipt of contract work order finished stock GL posting could not be inserted because');
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
											'" . $GLCodes['wipact'] . "',
											'" . $_SESSION['Contract'.$identifier]->WO . ' ' . $_SESSION['Contract'.$identifier]->ContractRef  . ' -  x 1 @ ' . locale_number_format(($OtherReqtsBudget+$ContractBOMBudget),2) . "',
											'" . -($OtherReqtsBudget+$ContractBOMBudget) . "')";

					$ErrMsg =   _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The WIP credit on receipt of finished items from a work order GL posting could not be inserted because');
					$DbgMsg =  _('The following SQL to insert the WIP GLTrans record was used');
					$Result = DB_query($SQL, $ErrMsg, $DbgMsg,true);

				} /* end of if GL and stock integrated and standard cost !=0 */

				//update the wo with the new qtyrecd
				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' ._('Could not update the work order item record with the total quantity received because');
				$DbgMsg = _('The following SQL was used to update the work order');
				$UpdateWOResult =DB_query("UPDATE woitems
										SET qtyrecd=qtyrecd+1
										WHERE wo='" . $_SESSION['Contract'.$identifier]->WO . "'
										AND stockid='" . $_SESSION['Contract'.$identifier]->ContractRef . "'",
										$ErrMsg,
										$DbgMsg,
										true);
			}//end if the contract wo was not received - work order item received/processed above if not
		}//end if there was a row returned from the woitems query
	} //end if the work order was still open (so end of closing it and processing receipt if necessary)

	DB_Txn_Commit();

	$_SESSION['Contract'.$identifier]->Status=3;
	prnMsg(_('The contract has been closed. No further charges can be posted against this contract.'),'success');

} //end if Closing the contract Close Contract button hit

if ($_SESSION['Contract'.$identifier]->Status ==2){//the contract is an order being processed now

	echo '<form  method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?SelectedContract=' . $_SESSION['Contract'.$identifier]->ContractRef . '&amp;identifier=' . $identifier . '">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<br />
		<div class="centre">
			<input type="submit" name="CloseContract" value="' . _('Close Contract') .  '" onclick="return confirm(\'' . _('Closing the contract will prevent further stock being issued to it and charges being made against it. Variances will be taken to the profit and loss account. Are You Sure?') . '\');" />
		</div>
        </div>
		</form>';
}

include('includes/footer.inc');
?>