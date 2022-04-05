
   	 
<?php
$PageSecurity=0;
	include('includes/session.inc');
	$Title=_('Main Menu');
	include('includes/header.inc');
	/********************************************************************************/
	  $search="";
		 if(isset($_POST['Search Now'])){
		 if(isset($_POST['name']) && $_POST['name'] !=""){
		 $search="where suppname LIKE '%$_POST[name]%'";
		 }
		 elseif(isset($_POST['code']) && $_POST['code'] !=""){
		 $search="where supplierid LIKE '%$_POST[supplierid]%'";
		 }
		 elseif(isset($_POST['phone']) && $_POST['phone'] !=""){
		 $search="where telephone LIKE '%$_POST[telephone]%'";
		 }else{
		 $search="";
		 }
		 }
		/********************************************************************************/
	if(isset($_POST['SearchContractors'])){
	 if(isset($_POST['supplierid']) && $_POST['supplierid'] !=''){
	 $SQL = "SELECT supplierid,
					suppname,
					currcode,
					address1,
					address2,
					address3,
					address4,
					telephone,
					email,
					url
				FROM suppliers
				ORDER BY suppname";
					 
		$rest=DB_query($SQL);
		$r=DB_fetch_array($rest);
		$_POST['SupplierSelected'] = $r['supplierid'];
		$_POST[$r['supplierid'].'SuppSelected'] = $r['suppname'];
				
	 }else{
	$SQL = "SELECT supplierid,
					suppname,
					currcode,
					address1,
					address2,
					address3,
					address4,
					telephone,
					email,
					url
				FROM suppliers
				ORDER BY suppname";
	
		$rest=DB_query($SQL);
		echo '<form action="" method="post" enctype="multipart/form-data" target="_self">';	
		echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';	
		echo '<input name="account" type="hidden" value="'.$_POST['account'].'" />';	
		echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Select Contactor to Assign a service') . '" alt="" />' . ' ' .    _('Select Contactor to Assign service') . '</p>';
	 echo '<table>
  <tr style=font-size:10pt>
    <th><b>ID</b></th>
    <th><b>Contractor</b></th>
	<th><b>Address</b></th>
	<th><b>Telephone No</b></th>
	<th><b>Email</b></th>
	<th></th>
  </tr>';?>
    <style> 

  .odd{background-color: white;} 

  .even{background-color:#CCCCCC;} 
  
   </style>
  <?php
  $i=0;
   while($row=DB_fetch_array($rest)){
   $i++;
 if($i%2 ==0){$class='even';}else{$class='odd';}
  echo '<tr class=' .$class. ' style=font-size:10pt>
    <td><input name="SupplierSelected" type="submit" value="'. $row['supplierid'] .'" /></td>
    <td>'.$row['suppname'] .'</td>
	<td>'. $row['address1'] .'</td>
	<td>'. $row['telephone'] .'</td>
	<td>'. $row['email'] .'</td>
	<input name="'. $row['orderno'] .'SuppSelected" type="hidden" value="'. $row['suppname'] .'" />
    </tr>';
  }
  echo '</table></form>';

	include('includes/footer.inc');
	 exit;
	 }
	 }

	/********************************************************************************/
echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/contract.png" title="' . _('Contract') . '" alt="" />' . ' ' . _('Contract: Select Contractor') . '</p>';

//echo '<tr><td colspan="4"><center><input name="Submit" type="submit" value="New Contract" /></center></td></tr>';

echo '&nbsp;&nbsp;<a href="' . $RootPath . '/Contracts.php">' . _('New Contract') . '</a></div><br />';
	echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?identifier=' . $identifier .'" name="CustomerSelection" method="post">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	echo '<table cellpadding="3" class="selection">
			<tr>
			<td><h5>' . _('Part of the Contrator Name') . ':</h5></td>
			<td><input name="name" type="text" /></td>			
			<td><h2><b>' . _('OR') . '</b></h2></td>
			<td><h5>' .  _('Part of the Contractor Code'). ':</h5></td>
			<td><input name="code" type="text" /></td>
			<td><h2><b>' . _('OR') . '</b></h2></td>
			<td><h5>' . _('Part of the Contractor Phone Number') . ':</h5></td>
			<td><input name="phone" type="text" /></td>
		</tr>
		</table>
		<br />
		<div class="centre">
			<input tabindex="4" type="submit" name="SearchContractors" value="' . _('Search Now') . '" />
			<input tabindex="5" type="submit" name="reset" value="' . _('Reset') .'" />
		</div>';

	if (isset($result_CustSelect)) {

		echo '<br /><table cellpadding="2" class="selection">';

		$TableHeader = '<tr>
							<th>' . _('Supplier Id') . '</th>
							<th>' . _('Supplier') . '</th>
							<th>' . _('Addrees') . '</th>
							<th>' . _('Telephone') . '</th>
							<th>' . _('Fax') . '</th>
						</tr>';
		echo $TableHeader;

		$j = 1;
		$k = 0; //row counter to determine background colour
		$LastCustomer='';
		while ($myrow=DB_fetch_array($result_CustSelect)) {

			if ($k==1){
				echo '<tr class="EvenTableRows">';
				$k=0;
			} else {
				echo '<tr class="OddTableRows">';
				$k=1;
			}
			if ($LastCustomer != $myrow['name']) {
				echo '<td>' .  $myrow['name']  . '</td>';
			} else {
				echo '<td></td>';
			}
			echo '<td><input type="submit" name="Submit'.$j.'" value="' . $myrow['suppname'] . '" /></td>
					<input type="hidden" name="SelectedSupplier'.$j.'" value="'. $myrow['supplierid'] . '" />
					<input type="hidden" name="SelectedSupplier'.$j.'" value="' . $myrow['supplierid'] . '" />
					<td>' . $myrow['telephone']  . '</td>
					<td>' . $myrow['address1'] . '</td>
					<td>' . $myrow['faxno'] . '</td>
					</tr>';
			$LastCustomer=$myrow['name'];
			$j++;
//end of page full new headings if
		}
if (isset($_SESSION['SupplierID'])) {


		echo '</table></form>';
	}
	echo '<table class="selection">
			<tr>
				<td>' . _('Contract Reference') . ':</td>
				<td>';
	if ($_SESSION['Contract'.$identifier]->Status==0) {
		/*Then the contract has not become an order yet and we can allow changes to the ContractRef */
		echo '<input type="text" name="ContractRef" autofocus="autofocus" required="required" size="21" title="' . _('Enter the contract reference. This reference will be used as the item code so no more than 20 alpha-numeric characters or underscore') . '" data-type="no-illegal-chars" maxlength="20" value="' . $_SESSION['Contract'.$identifier]->ContractRef . '" />';
	} else {
		/*Just show the contract Ref - dont allow modification */
		echo '<input type="hidden" name="ContractRef" title="' . _('Enter the contract reference. This reference will be used as the item code so no more than 20 alpha-numeric characters or underscore') . '" data-type="no-illegal-chars" value="' . $_SESSION['Contract'.$identifier]->ContractRef . '" />' . $_SESSION['Contract'.$identifier]->ContractRef;
	}
	echo '</td>
		</tr>
		<tr>
			<td>' . _('Category') . ':</td>
			<td><select name="CategoryID" >';

	$sql = "SELECT categoryid, categorydescription FROM stockcategory";
	$ErrMsg = _('The stock categories could not be retrieved because');
	$DbgMsg = _('The SQL used to retrieve stock categories and failed was');
	$result = DB_query($sql,$ErrMsg,$DbgMsg);

	while ($myrow=DB_fetch_array($result)){
		if (!isset($_SESSION['Contract'.$identifier]->CategoryID) or $myrow['categoryid']==$_SESSION['Contract'.$identifier]->CategoryID){
			echo '<option selected="selected" value="'. $myrow['categoryid'] . '">' . $myrow['categorydescription'] . '</option>';
		} else {
			echo '<option value="'. $myrow['categoryid'] . '">' . $myrow['categorydescription'] . '</option>';
		}
	}

	echo '</select><a target="_blank" href="'. $RootPath . '/StockCategories.php">' . _('Add or Modify Contract Categories') . '</a></td></tr>';

	$sql = "SELECT locations.loccode, locationname FROM locations INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canupd=1";
	$ErrMsg = _('The stock locations could not be retrieved because');
	$DbgMsg = _('The SQL used to retrieve stock locations and failed was');
	$result = DB_query($sql,$ErrMsg,$DbgMsg);

	echo '<tr>
			<td>' . _('Location') . ':</td>
			<td><select name="LocCode" >';
	while ($myrow=DB_fetch_array($result)){
		if (!isset($_SESSION['Contract'.$identifier]->LocCode) or $myrow['loccode']==$_SESSION['Contract'.$identifier]->LocCode){
			echo '<option selected="selected" value="'. $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
		} else {
			echo '<option value="'. $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
		}
	}

	echo '</select></td></tr>';
	$sql = "SELECT code, description FROM workcentres INNER JOIN locationusers ON locationusers.loccode=workcentres.location AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canupd=1";
	$result = DB_query($sql);

	if (DB_num_rows($result)==0){
		prnMsg( _('There are no work centres set up yet') . '. ' . _('Please use the link below to set up work centres'),'warn');
		echo '<br /><a href="'.$RootPath.'/WorkCentres.php">' . _('Work Centre Maintenance') . '</a>';
		include('includes/footer.inc');
		exit;
	}
	echo '<tr><td>' . _('Default Work Centre') . ': </td><td>';

	echo '<select name="DefaultWorkCentre">';

	while ($myrow = DB_fetch_array($result)) {
		if (isset($_POST['DefaultWorkCentre']) and $myrow['code']==$_POST['DefaultWorkCentre']) {
			echo '<option selected="selected" value="'.$myrow['code'] . '">' . $myrow['description'] . '</option>';
		} else {
			echo '<option value="'.$myrow['code'] . '">' . $myrow['description'] . '</option>';
		}
	} //end while loop

	DB_free_result($result);

	echo '</select></td>
		</tr>
		<tr>
			<td>' . _('Contract Description') . ':</td>
			<td><textarea name="ContractDescription" style="width:100%" required="required" title="' . _('A description of the contract is required') . '" minlength="5" rows="5" cols="40">' . $_SESSION['Contract'.$identifier]->ContractDescription . '</textarea></td>
		</tr><tr>
			<td>' .  _('Drawing File') . ' .jpg' . ' ' . _('format only') .':</td>
			<td><input type="file" id="Drawing" name="Drawing" /></td>
		</tr>';

	if (!isset($_SESSION['Contract'.$identifier]->RequiredDate)) {
		$_SESSION['Contract'.$identifier]->RequiredDate = DateAdd(date($_SESSION['DefaultDateFormat']),'m',1);
	}

	echo '<tr>
			<td>' . _('Required Date') . ':</td>
			<td><input type="text" required="required" class="date" alt="' .$_SESSION['DefaultDateFormat'] . '" name="RequiredDate" size="11" value="' . $_SESSION['Contract'.$identifier]->RequiredDate . '" /></td>
		</tr>';

	echo '<tr>
			<td>' . _('Customer Reference') . ':</td>
			<td><input type="text" name="CustomerRef" required="required" title="' . _('Enter the reference that the customer uses for this contract') . '" size="21" maxlength="20" value="' . $_SESSION['Contract'.$identifier]->CustomerRef . '" /></td>
		</tr>';
	if (!isset($_SESSION['Contract'.$identifier]->Margin)){
		$_SESSION['Contract'.$identifier]->Margin =50;
	}
	echo '<tr>
			<td>' . _('Gross Profit') . ' %:</td>
			<td><input class="number" type="text" name="Margin"  required="required" size="6" maxlength="6" value="' . locale_number_format($_SESSION['Contract'.$identifier]->Margin, 2) . '" /></td>
		</tr>';

	if ($_SESSION['CompanyRecord']['currencydefault'] != $_SESSION['Contract'.$identifier]->CurrCode){
		echo '<tr>
				<td>' . $_SESSION['Contract'.$identifier]->CurrCode . ' ' . _('Exchange Rate') . ':</td>
				<td><input class="number" type="text" name="ExRate"  required="required" title="' . _('The exchange rate between the customer\'s currency and the functional currency of the business must be entered') . '" size="10" maxlength="10" value="' . locale_number_format($_SESSION['Contract'.$identifier]->ExRate,'Variable') . '" /></td>
			</tr>';
	} else {
		echo '<input type="hidden" name="ExRate" value="' . locale_number_format($_SESSION['Contract'.$identifier]->ExRate,'Variable') . '" />';
	}

	echo '<tr>
			<td>' . _('Contract Status') . ':</td>
			<td>';

	$StatusText = array();
	$StatusText[0] = _('Setup');
	$StatusText[1] = _('Quote');
	$StatusText[2] = _('Completed');
	if ($_SESSION['Contract'.$identifier]->Status == 0){
		echo _('Contract Setup');
	} elseif ($_SESSION['Contract'.$identifier]->Status == 1){
		echo _('Customer Quoted');
	} elseif ($_SESSION['Contract'.$identifier]->Status == 2){
		echo _('Order Placed');
	}
	echo '<input type="hidden" name="Status" value="'.$_SESSION['Contract'.$identifier]->Status.'" />';
	echo '</td>
		</tr>';
	if ($_SESSION['Contract'.$identifier]->Status >=1) {
		echo '<tr>
				<td>' . _('Quotation Reference/Sales Order No') . ':</td>
				<td><a href="' . $RootPath . '/SelectSalesOrder.php?OrderNumber=' . $_SESSION['Contract'.$identifier]->OrderNo . '&amp;Quotations=Quotes_Only">' .  $_SESSION['Contract'.$identifier]->OrderNo . '</a></td>
			</tr>';
	}
	if ($_SESSION['Contract'.$identifier]->Status!=2 and isset($_SESSION['Contract'.$identifier]->WO)) {
		echo '<tr>
				<td>' . _('Contract Work Order Ref') . ':</td>
				<td>' . $_SESSION['Contract'.$identifier]->WO . '</td>
			</tr>';
	}
	echo '</table><br />';

	echo '<table>
			<tr>
				<td>
					<table class="selection">
						<tr>
							<th colspan="6">' . _('Stock Items Required') . '</th>
						</tr>';
	$ContractBOMCost = 0;
	if (count($_SESSION['Contract'.$identifier]->ContractBOM)!=0){
		echo '<tr>
				<th>' . _('Item Code') . '</th>
				<th>' . _('Item Description') . '</th>
				<th>' . _('Quantity') . '</th>
				<th>' . _('Unit') . '</th>
				<th>' . _('Unit Cost') . '</th>
				<th>' . _('Total Cost') . '</th>
			</tr>';

		foreach ($_SESSION['Contract'.$identifier]->ContractBOM as $Component) {
			echo '<tr>
					<td>' . $Component->StockID . '</td>
					<td>' . $Component->ItemDescription . '</td>
					<td class="number">' . locale_number_format($Component->Quantity, $Component->DecimalPlaces) . '</td>
					<td>' . $Component->UOM . '</td>
					<td class="number">' . locale_number_format($Component->ItemCost,$_SESSION['CompanyRecord']['decimalplaces']) . '</td>
					<td class="number">' . locale_number_format(($Component->ItemCost * $Component->Quantity),$_SESSION['CompanyRecord']['decimalplaces']) . '</td>
				</tr>';
			$ContractBOMCost += ($Component->ItemCost *  $Component->Quantity);
		}
		echo '<tr>
				<th colspan="5"><b>' . _('Total stock cost') . '</b></th>
					<th class="number"><b>' . locale_number_format($ContractBOMCost,$_SESSION['CompanyRecord']['decimalplaces']) . '</b></th>
				</tr>';
	} else { //there are no items set up against this contract
		echo '<tr>
				<td colspan="6"><i>' . _('None Entered') . '</i></td>
			</tr>';
	}
	echo '</table></td>'; //end of contract BOM table
	echo '<td valign="top">
			<table class="selection">
				<tr>
					<th colspan="4">' . _('Other Requirements') . '</th>
				</tr>';
	$ContractReqtsCost = 0;
	if (count($_SESSION['Contract'.$identifier]->ContractReqts)!=0){
		echo '<tr>
				<th>' . _('Requirement') . '</th>
				<th>' . _('Quantity') . '</th>
				<th>' . _('Unit Cost') . '</th>
				<th>' . _('Total Cost') . '</th>
			</tr>';
		foreach ($_SESSION['Contract'.$identifier]->ContractReqts as $Requirement) {
			echo '<tr>
					<td>' . $Requirement->Requirement . '</td>
					<td class="number">' . locale_number_format($Requirement->Quantity,'Variable') . '</td>
					<td class="number">' . locale_number_format($Requirement->CostPerUnit,$_SESSION['CompanyRecord']['decimalplaces']) . '</td>
					<td class="number">' . locale_number_format(($Requirement->CostPerUnit * $Requirement->Quantity),$_SESSION['CompanyRecord']['decimalplaces']) . '</td>
				</tr>';
			$ContractReqtsCost += ($Requirement->CostPerUnit * $Requirement->Quantity);
		}
		echo '<tr>
				<th colspan="3"><b>' . _('Total other costs') . '</b></th>
				<th class="number"><b>' . locale_number_format($ContractReqtsCost,$_SESSION['CompanyRecord']['decimalplaces']) . '</b></th>
			</tr>';
	} else { //there are no items set up against this contract
		echo '<tr>
				<td colspan="4"><i>' . _('None Entered') . '</i></td>
			</tr>';
	}
	echo '</table></td></tr></table>';
	echo '<br />';
	echo'<table class="selection">
			<tr>
				<th>' . _('Total Contract Cost') . '</th>
				<th class="number">' . locale_number_format(($ContractBOMCost+$ContractReqtsCost),$_SESSION['CompanyRecord']['decimalplaces']) . '</th>
				<th>' . _('Contract Price') . '</th>
				<th class="number">' . locale_number_format(($ContractBOMCost+$ContractReqtsCost)/((100-$_SESSION['Contract'.$identifier]->Margin)/100),$_SESSION['CompanyRecord']['decimalplaces']) . '</th>
			</tr>
		</table>';

	echo'<p></p>';
	echo '<div class="centre">
			<input type="submit" name="EnterContractBOM" value="' . _('Enter Items Required') . '" />
			<input type="submit" name="EnterContractRequirements" value="' . _('Enter Other Requirements') .'" />';
	if($_SESSION['Contract'.$identifier]->Status==0){ // not yet quoted
		echo '<input type="submit" name="CommitContract" value="' . _('Commit Changes') .'" />';
	} elseif($_SESSION['Contract'.$identifier]->Status==1){ //quoted but not yet ordered
		echo '<input type="submit" name="CommitContract" value="' . _('Update Quotation') .'" />';
	}
	if($_SESSION['Contract'.$identifier]->Status==0){ //not yet quoted
		echo ' <input type="submit" name="CreateQuotation" value="' . _('Create Quotation') .'" />
			</div>';
	} else {
		echo '</div>';
	}
	if ($_SESSION['Contract'.$identifier]->Status!=2) {
		echo '<div class="centre">
				 <br />
				 <input type="submit" name="CancelContract" value="' . _('Cancel and Delete Contract') . '" />
			  </div>';
	}
	echo '</form>';
}

	include('includes/footer.inc');
	?>