<?php

/* $Id: ContractBOM.php 4553 2011-04-18 00:09:23Z daintree $*/

include('includes/DefineContractClass.php');

include('includes/session.inc');

$Title = _('Contract Bill of Materials');

$identifier=$_GET['identifier'];

/* If a contract header doesn't exist, then go to
 * Contracts.php to create one
 */

if (!isset($_SESSION['Contract'.$identifier])){
	header('Location:' . $RootPath . '/Contracts.php');
	exit;
}

$ViewTopic= 'Contracts';
$BookMark = 'AddToContract';

include('includes/header.inc');

if (isset($_POST['UpdateLines']) OR isset($_POST['BackToHeader'])) {
	if($_SESSION['Contract'.$identifier]->Status!=2){ //dont do anything if the customer has committed to the contract
		foreach ($_SESSION['Contract'.$identifier]->ContractBOM as $ContractComponent) {
			if (filter_number_format($_POST['Qty'.$ContractComponent->ComponentID])==0){
				//this is the same as deleting the line - so delete it
				$_SESSION['Contract'.$identifier]->Remove_ContractComponent($ContractComponent->ComponentID);
			} else {
				$_SESSION['Contract'.$identifier]->ContractBOM[$ContractComponent->ComponentID]->Quantity=filter_number_format($_POST['Qty'.$ContractComponent->ComponentID]);
			}
		} // end loop around the items on the contract BOM
	} // end if the contract is not currently committed to by the customer
}// end if the user has hit the update lines or back to header buttons


if (isset($_POST['BackToHeader'])){
	echo '<meta http-equiv="Refresh" content="0; url=' . $RootPath . '/FarmContract.php?identifier='.$identifier. '" />';
	echo '<br />';
	prnMsg(_('You should automatically be forwarded to the Contract page. If this does not happen perhaps the browser does not support META Refresh') . '<a href="' . $RootPath . '/Contracts.php?identifier='.$identifier . '">' . _('click here') . '</a> ' . _('to continue'),'info');
	include('includes/footer.inc');
	exit;
}

if (isset($_POST['Search'])){  /*ie seach for stock items */

	if ($_POST['Keywords'] AND $_POST['StockCode']) {
		prnMsg(_('Stock description keywords have been used in preference to the Stock code extract entered'), 'info');
	}

	if ($_POST['Keywords']) {
		//insert wildcard characters in spaces
		$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';

		if ($_POST['StockCat']=='All'){
			$sql = "SELECT farmdescriptions.description_Id,
						farmdescriptions.description,
						farmdescriptions.units,
						farmdescriptions.cost
					FROM farmdescriptions 
					WHERE farmdescriptions.description " . LIKE . " '$SearchString'
					ORDER BY farmdescriptions.description_Id";
		} else {
			$sql = "SELECT farmdescriptions.description_Id,
						farmdescriptions.description,
						farmdescriptions.units,
						farmdescriptions.cost
					FROM farmdescriptions 
					WHERE farmdescriptions.description " . LIKE . " '$SearchString'
					AND farmdescriptions.description_Id='" . $_POST['StockCat'] . "'
					ORDER BY farmdescriptions.description_Id";
		}

	} elseif ($_POST['StockCode']){

		$_POST['StockCode'] = '%' . $_POST['StockCode'] . '%';

		if ($_POST['StockCat']=='All'){
			$sql = "SELECT farmdescriptions.description_Id,
						farmdescriptions.description,
						farmdescriptions.units,
						farmdescriptions.cost
					FROM farmdescriptions 
					WHERE farmdescriptions.description_Id " . LIKE . " '" . $_POST['StockCode'] . "'
					ORDER BY farmdescriptions.description_Id";
		} else {
			$sql = "SELECT farmdescriptions.description_Id,
						farmdescriptions.description,
						farmdescriptions.units,
						farmdescriptions.cost
					FROM farmdescriptions 
					WHERE farmdescriptions.description_Id " . LIKE . " '" . $_POST['StockCode'] . "'
					AND farmdescriptions.description_Id='" . $_POST['StockCat'] . "'
					ORDER BY farmdescriptions.description_Id";
		}

	} else {
		if ($_POST['StockCat']=='All'){
			$sql = "SELECT farmdescriptions.description_Id,
						farmdescriptions.description,
						farmdescriptions.units,
						farmdescriptions.cost
					FROM farmdescriptions 
					ORDER BY farmdescriptions.description_Id";
		} else {
			$sql = "SELECT farmdescriptions.description_Id,
						farmdescriptions.description,
						farmdescriptions.units,
						farmdescriptions.cost
					FROM farmdescriptions 
					AND farmdescriptions.description_Id='" . $_POST['StockCat'] . "'
					ORDER BY farmdescriptions.description_Id";
		}
	}

	$ErrMsg = _('There is a problem selecting the part records to display because');
	$DbgMsg = _('The SQL statement that failed was');
	$SearchResult = DB_query($sql,$ErrMsg,$DbgMsg);

	if (DB_num_rows($SearchResult)==0 AND $debug==1){
		prnMsg( _('There are no products to display matching the criteria provided'),'warn');
	}
	if (DB_num_rows($SearchResult)==1){
		$myrow=DB_fetch_array($SearchResult);
		$_GET['NewItem'] = $myrow['stockid'];
		DB_data_seek($SearchResult,0);
	}

} //end of if search


if(isset($_GET['Delete'])){
	if($_SESSION['Contract'.$identifier]->Status!=2){
		$_SESSION['Contract'.$identifier]->Remove_ContractComponent($_GET['Delete']);
	} else {
		prnMsg( _('The contract BOM cannot be altered because the customer has already placed the order'),'warn');
	}
}

if (isset($_POST['NewItem'])){ /* NewItem is set from the part selection list as the part code selected */
	for ($i=0;$i < $_POST['CountOfItems'];$i++) {
		$AlreadyOnThisBOM = 0;
		if (filter_number_format($_POST['Qty'.$i])>0){
			if (count($_SESSION['Contract'.$identifier]->ContractBOM)!=0){

				foreach ($_SESSION['Contract'.$identifier]->ContractBOM AS $Component) {

				/* do a loop round the items on the order to see that the item
				is not already on this order */
					if ($Component->StockID == trim($_POST['description_Id'.$i])) {
						$AlreadyOnThisBOM = 1;
						prnMsg( _('The item') . ' ' . trim($_POST['description_Id'.$i]) . ' ' . _('is already in the bill of material for this contract. The system will not allow the same item on the contract more than once. However you can change the quantity required for the item.'),'error');
					}
				} /* end of the foreach loop to look for preexisting items of the same code */
			}

			if ($AlreadyOnThisBOM!=1){

				$sql = "SELECT farmdescriptions.description_Id,
								farmdescriptions.description,
								farmdescriptions.units,
								farmdescriptions.cost
							FROM farmdescriptions 
							WHERE farmdescriptions.description_Id = '". trim($_POST['StockID'.$i]) . "'";

				$ErrMsg = _('The item details could not be retrieved');
				$DbgMsg = _('The SQL used to retrieve the item details but failed was');
				$result1 = DB_query($sql,$ErrMsg,$DbgMsg);

				if ($myrow = DB_fetch_array($result1)){

					$_SESSION['Contract'.$identifier]->Add_To_ContractBOM (trim($_POST['StockID'.$i]),
																			$myrow['description'],
																			'',
																			filter_number_format($_POST['Qty'.$i]), /* Qty */
																			$myrow['cost'],
																			$myrow['units'],
																			$myrow['decimalplaces']);
				} else {
					prnMsg (_('The item code') . ' ' . trim($_POST['StockID'.$i]) . ' ' . _('does not exist in the database and therefore cannot be added to the contract BOM'),'error');
					if ($debug==1){
						echo '<br />' . $sql;
					}
					include('includes/footer.inc');
					exit;
				}
			} /* end of if not already on the contract BOM */
		} /* the quantity of the item is > 0 */
	}
} /* end of if its a new item */

/* This is where the order as selected should be displayed  reflecting any deletions or insertions*/

echo '<form id="ContractBOMForm" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?identifier='.$identifier. '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

if (count($_SESSION['Contract'.$identifier]->ContractBOM)>0){
	echo '<p class="page_title_text">
			<img src="'.$RootPath.'/css/'.$Theme.'/images/contract.png" title="' . _('Contract Bill of Material') . '" alt="" />  '.$_SESSION['Contract'.$identifier]->CustomerName . '
		</p>';

	echo '<table class="selection">';

	if (isset($_SESSION['Contract'.$identifier]->ContractRef)) {
		echo  '<tr>
				<th colspan="7">' . _('Contract Reference:') .' '. $_SESSION['Contract'.$identifier]->ContractRef . '</th>
			</tr>';
	}

	echo '<tr>
			<th>' . _('Item Code') . '</th>
			<th>' . _('Description') . '</th>
			<th>' . _('Quantity') . '</th>
			<th>' . _('UOM')  . '</th>
			<th>' . _('Unit Cost') .  '</th>
			<th>' . _('Sub-total') . '</th>
		</tr>';

	$_SESSION['Contract'.$identifier]->total = 0;
	$k = 0;  //row colour counter
	$TotalCost =0;
	foreach ($_SESSION['Contract'.$identifier]->ContractBOM as $ContractComponent) {

		$LineTotal = $ContractComponent->Quantity * $ContractComponent->ItemCost;

		$DisplayLineTotal = locale_number_format($LineTotal,$_SESSION['CompanyRecord']['decimalplaces']);

		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}

		echo '<td>' . $ContractComponent->StockID . '</td>
			  <td>' . $ContractComponent->ItemDescription . '</td>
			  <td><input type="text" class="number" required="required" title="' . _('Enter the quantity of this component required to complete the contract') . '" name="Qty' . $ContractComponent->ComponentID . '" size="11" value="' . locale_number_format($ContractComponent->Quantity,$ContractComponent->DecimalPlaces)  . '" /></td>
			  <td>' . $ContractComponent->UOM . '</td>
			  <td class="number">' . locale_number_format($ContractComponent->ItemCost,$_SESSION['CompanyRecord']['decimalplaces']) . '</td>
			  <td class="number">' . $DisplayLineTotal . '</td>
			  <td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?identifier='.$identifier. '&amp;Delete=' . $ContractComponent->ComponentID . '" onclick="return MakeConfirm(\'' . _('Are you sure you wish to delete this item from the contract BOM?') . ',' . _('Confirm Deletion') . '\');">' . _('Delete') . '</a></td></tr>';
		$TotalCost += $LineTotal;
	}

	$DisplayTotal = locale_number_format($TotalCost,$_SESSION['CompanyRecord']['decimalplaces']);
	echo '<tr>
			<td colspan="5" class="number">' . _('Total Cost') . '</td>
			<td class="number"><b>' . $DisplayTotal . '</b></td>
		</tr>
		</table>';
	echo '<br />
			<div class="centre"><input type="submit" name="UpdateLines" value="' . _('Update Lines') . '" />';
	echo '<input type="submit" name="BackToHeader" value="' . _('Back To Contract Header') . '" /></div>';

} /*Only display the contract BOM lines if there are any !! */

if (!isset($_GET['Edit'])) {
	$sql="SELECT categoryid,
				categorydescription
			FROM stockcategory
			WHERE stocktype<>'L'
			AND stocktype<>'D'
			ORDER BY categorydescription";
	$ErrMsg = _('The supplier category details could not be retrieved because');
	$DbgMsg = _('The SQL used to retrieve the category details but failed was');
	$result1 = DB_query($sql,$ErrMsg,$DbgMsg);
	echo '<p class="page_title_text">
			<img src="'.$RootPath.'/css/'.$Theme.'/images/magnifier.png" title="' . _('Print') . '" alt="" />' . ' ' . _('Search For Stock Items') .
		'</p>';
	echo '<table class="selection">
			<tr></tr>
			<tr>
				<td><select name="StockCat">';

	echo '<option selected="selected" value="All">' . _('All') . '</option>';
	while ($myrow1 = DB_fetch_array($result1)) {
		if (isset($_POST['StockCat']) and $_POST['StockCat']==$myrow1['categoryid']){
			echo '<option selected="selected" value="'. $myrow1['categoryid'] . '">' . $myrow1['categorydescription'] . '</option>';
		} else {
			echo '<option value="'. $myrow1['categoryid'] . '">' . $myrow1['categorydescription'] . '</option>';
		}
	}

	unset($_POST['Keywords']);
	unset($_POST['StockCode']);

	if (!isset($_POST['Keywords'])) {
		$_POST['Keywords']='';
	}

	if (!isset($_POST['StockCode'])) {
		$_POST['StockCode']='';
	}

	echo '</select></td>
			<td>' . _('Enter text extracts in the description') . ':</td>
			<td><input type="text" autofocus="autofocus" title="' . _('Enter any text that should appear in the item description as the basis of your search') . '" name="Keywords" size="20" maxlength="25" value="' . $_POST['Keywords'] . '" /></td>
		</tr>
		<tr>
			<td></td>
			<td><b>' . _('OR') . ' </b>' . _('Enter extract of the Stock Code') . ':</td>
			<td><input type="text" title="' . _('Enter any part of an item code to seach for all matching items containing that text in the code') . '" name="StockCode" size="15" maxlength="18" value="' . $_POST['StockCode'] . '" /></td>
		</tr>
		<tr>
			<td></td>
			<td><b>' . _('OR') . ' </b><a target="_blank" href="'.$RootPath.'/Stocks.php">' . _('Create a New Stock Item') . '</a></td>
		</tr>
		</table>
		<br />
		<div class="centre">
			<input type="submit" name="Search" value="' . _('Search Now') . '" />
		</div>
		<br />';

}

if (isset($SearchResult)) {

	echo '<table cellpadding="1">';

	$TableHeader = '<tr>
						<th>' . _('Code')  . '</th>
						<th>' . _('Description') . '</th>
						<th>' . _('Units') . '</th>
						<th>' . _('Image') . '</th>
						<th>' . _('Quantity') . '</th>
					</tr>';
	echo $TableHeader;

	$k=0; //row colour counter
	$i=0;
	while ($myrow=DB_fetch_array($SearchResult)) {

		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}

		if (file_exists( $_SESSION['part_pics_dir'] . '/' . $myrow['description_Id'] . '.jpg') ) {
			$ImageSource = '<img src="GetStockImage.php?automake=1&amp;textcolor=FFFFFF&amp;bgcolor=CCCCCC&amp;StockID=' . $myrow['stockid']. '&amp;text=&amp;width=50&amp;height=50" />';
		} else {
			$ImageSource = '<i>' . _('No Image') . '</i>';
		}

		echo '<td>' . $myrow['description_Id'] . '</td>
				<td>' . $myrow['description'] . '</td>
				<td>' . $myrow['units'] . '</td>
				<td>' . $ImageSource . '</td>
				<td><input class="number" type="text" title="' . _('Enter the quantity required of this item to complete the contract') . '" required="required" size="6" value="0" name="Qty'.$i.'" />
				<input type="hidden" name="StockID' . $i . '" value="' . $myrow['description_Id'] . '" />
				</td>
			</tr>';
		$i++;
		if ($i == $_SESSION['DisplayRecordsMax']){
			break;
		}
#end of page full new headings if
	}

#end of while loop
	echo '</table>
			<input type="hidden" name="CountOfItems" value="'. $i . '" />';
	if ($i == $_SESSION['DisplayRecordsMax']){

		prnMsg( _('Only the first') . ' ' . $_SESSION['DisplayRecordsMax'] . ' ' . _('can be displayed') . '. ' . _('Please restrict your search to only the parts required'),'info');
	}
	echo '<br />
		<div class="centre">
			<input type="submit" name="NewItem" value="' . _('Add to Contract Bill Of Material') .'" />
		</div>';
}#end if SearchResults to show

echo '<hr />
    </div>
	</form>';
include('includes/footer.inc');
?>