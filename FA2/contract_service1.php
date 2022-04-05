<link rel="stylesheet" href="PVP/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="PVP/iCheck/flat/blue.css">
<?php

$doc=9;
$ProductionID = GetNextTransNo(390, $db);
if (isset($_GET['New'])) {
	$_SESSION['FP'] = new StockRequest1();
}

 if(isset($_POST['SelectedService'])){
 $InputError=0;
	if ($_POST['SP']=='') {
		prnMsg( _('You must select a Service Provider for Production'), 'error');
		$InputError=1;
	}
	if($InputError==0){
 $_SESSION['FP']->SP = $_POST['SP'];
 }
 }
 if (isset($_POST['SupplieridSelected'])){
	$_SESSION['FP']->Contractor = $_POST[$_POST['SupplieridSelected'] .'SuppSelected'];
    }

if (isset($_POST['Update'])) {
	$InputError=0;
	if ($_SESSION['FP']->SP =='CO') {
	if ($_POST['contractor']=='') {
		prnMsg( _('You must select the contractor'), 'error');
		$InputError=1;
	}
	}
	if ($_POST['field']=='') {
		prnMsg( _('You must select Production Field'), 'error');
		$InputError=1;
	}
	if ($InputError==0) {
		$_SESSION['FP']->Contractor=$_POST['contractor'];
		$_SESSION['FP']->Field=$_POST['field'];
		$_SESSION['FP']->Remarks=$_POST['remarks'];
		$_SESSION['FP']->prod_date=$_POST['prod_date'];
	}
}

if (isset($_POST['Edit'])) {
	$_SESSION['FP']->LineItems[$_POST['LineNumber']]->Quantity=$_POST['Quantity'];
	$_SESSION['FP']->LineItems[$_POST['LineNumber']]->AreaCovered=$_POST['AreaCovered'];
}

if (isset($_GET['Delete'])) {
	unset($_SESSION['FP']->LineItems[$_GET['Delete']]);
	echo '<br />';
	prnMsg( _('The line was successfully deleted'), 'success');
	echo '<br />';
}

foreach ($_POST as $key => $value) {
	if (mb_strstr($key,'StockID')) {
		$Index=mb_substr($key, 7);
		if (filter_number_format($_POST['AreaCovered'.$Index])>0) {
			$StockID=$value;
			$ItemDescription=$_POST['ItemDescription'.$Index];
			$cost=$_POST['cost'.$Index];
			$DecimalPlaces=$_POST['DecimalPlaces'.$Index];
			$Source=$_POST['Source'.$Index];
			$NewItem_array[$StockID] = filter_number_format($_POST['Quantity'.$Index]);			
			$NewItem_array1[$StockID] = filter_number_format($_POST['AreaCovered'.$Index]);
			$_POST['units'.$StockID]=$_POST['units'.$Index];
			$_SESSION['FP']->AddLine($StockID, $ItemDescription, $NewItem_array[$StockID],$NewItem_array1[$StockID], $_POST['units'.$StockID], $Source, $DecimalPlaces,$cost);
		}
	}
	
}


if (isset($_POST['Submit'])) {
	DB_Txn_Begin();
	$InputError=0;
	if ($_SESSION['FP']->SP=='') {
		prnMsg( _('You must select a service provider for production.'), 'error');
		$InputError=1;
	}
	if ($_SESSION['FP']->LineItems=='') {
		prnMsg( _('You must have atleast one item for the production to be submitted.'), 'error');
		$InputError=1;
	}
	if ($InputError==0) {		
		$HeaderSQL="INSERT INTO farmproduction (Fid,service_id,
												contractor,
												field,
												date)
										VALUES('$ProductionID',
											'" . $_SESSION['FP']->SP. "',
											'" . $_SESSION['FP']->Contractor. "',
											'" . $_SESSION['FP']->Field . "',
										    '" . $_SESSION['FP']->prod_date . "')";
		$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($HeaderSQL,$ErrMsg,$DbgMsg,true);

		foreach ($_SESSION['FP']->LineItems as $LineItems) {
			$LineSQL="INSERT INTO farmproductionitems (fid,
													stockid,
													description,
													source,
													units,
													unitcost,
													areacovered,
													quantity,
													tota_cost)
												VALUES(
													'$ProductionID',
													'".$LineItems->StockID."',
													'".$LineItems->ItemDescription."',
													'".$LineItems->Source."',
													'".$LineItems->UOM."',
													'".$LineItems->cost."',
													'".$LineItems->AreaCovered."',
													'".$LineItems->Quantity."',
													'".$LineItems->DecimalPlaces."')";
			$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request line record could not be inserted because');
			$DbgMsg = _('The following SQL to insert the request header record was used');
			$Result = DB_query($LineSQL,$ErrMsg,$DbgMsg,true);
		}
	
	DB_Txn_Commit();
	prnMsg( _('Farm Production has been entered.'), 'success');
	
	echo '<br /><div class="centre"><a href="'. htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?New=Yes">' . _('Start Another Farm Production') . '</a></div>';
	include('includes/footer.inc');
	unset($_SESSION['FP']);
	exit;
	
}

}

if (isset($_GET['Edit'])) {
	echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Application=FA2&Ref=default&Link=contract_service" method="post">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table class="selection table table-hover">';
	echo '<tr>
			<th colspan="2"><h4>' . _('Edit the Request Line') . '</h4></th>
		</tr>';
	echo '<tr>
			<td>' . _('Line number') . '</td>
			<td>' . $_SESSION['FP']->LineItems[$_GET['Edit']]->LineNumber . '</td>
		</tr>
		<tr>
			<td>' . _('Stock Code') . '</td>
			<td>' . $_SESSION['FP']->LineItems[$_GET['Edit']]->StockID . '</td>
		</tr>
		<tr>
			<td>' . _('Item Description') . '</td>
			<td>' . $_SESSION['FP']->LineItems[$_GET['Edit']]->ItemDescription . '</td>
		</tr>
		<tr>
			<td>' . _('Unit of Measure') . '</td>
			<td>' . $_SESSION['FP']->LineItems[$_GET['Edit']]->UOM . '</td>
		</tr>
		<tr>
		
			<td>' . _('Quantity') . '</td>
			<td><input type="text" class="number" name="Quantity" value="' . locale_number_format($_SESSION['FP']->LineItems[$_GET['Edit']]->Quantity, $_SESSION['FP']->LineItems[$_GET['Edit']]->DecimalPlaces) . '" /></td>
		</tr>';
	echo'<tr>
		
			<td>' . _('Area Covered') . '</td>
			<td><input type="text" class="number" name="AreaCovered" value="' . locale_number_format($_SESSION['FP']->LineItems[$_GET['Edit']]->AreaCovered, $_SESSION['FP']->LineItems[$_GET['Edit']]->DecimalPlaces) . '" /></td>
		</tr>';

	echo '<input type="hidden" name="LineNumber" value="' . $_SESSION['FP']->LineItems[$_GET['Edit']]->LineNumber . '" />';
	echo '</table>
		<br />';
	echo '<div class="centre">
			<input type="submit" name="Edit" value="' . _('Update Line') . '" />
		</div>
        </div>
		</form>';
	include('includes/footer.inc');	
	exit;
}

echo '<form action="'. htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Application=FA2&Ref=default&Link=contract_service" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
 if(isset($_POST['SearchContractors'])){
	if (!isset($_POST['PageOffset'])) {
	$_POST['PageOffset'] = 1;
} else {
	if ($_POST['PageOffset'] == 0) {
		$_POST['PageOffset'] = 1;
	}
}
if (isset($_POST['SearchContractors'])
	OR isset($_POST['Go'])
	OR isset($_POST['Next'])
	OR isset($_POST['Previous'])) {

	if (mb_strlen($_POST['Keywords']) > 0 AND mb_strlen($_POST['SupplierCode']) > 0) {
		prnMsg( _('Supplier name keywords have been used in preference to the Supplier code extract entered'), 'info' );
	}
	if ($_POST['Keywords'] == '' AND $_POST['SupplierCode'] == '') {
		$SQLi = "SELECT supplierid,
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
	} else {
		if (mb_strlen($_POST['Keywords']) > 0) {
			$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
			//insert wildcard characters in spaces
			$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';
			$SQLi = "SELECT supplierid,
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
						WHERE suppname " . LIKE . " '" . $SearchString . "'
						ORDER BY suppname";
		} elseif (mb_strlen($_POST['SupplierCode']) > 0) {
			$_POST['SupplierCode'] = mb_strtoupper($_POST['SupplierCode']);
			$SQLi = "SELECT supplierid,
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
						WHERE supplierid " . LIKE . " '%" . $_POST['SupplierCode'] . "%'
						ORDER BY supplierid";
		}
	} //one of keywords or SupplierCode was more than a zero length string
	$result = DB_query($SQLi);
	if (DB_num_rows($result) == 1) {
		$row = DB_fetch_row($result);
		$SingleSupplierReturned = $row[0];
	}
	if (isset($SingleSupplierReturned)) { /*there was only one supplier returned */
 	   $_SESSION['SupplierID'] = $SingleSupplierReturned;
	   unset($_POST['Keywords']);
	   unset($_POST['SupplierCode']);
	   unset($_POST['Search']);
        } else {
         unset($_SESSION['SupplierID']);
        }
}
  echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Select Contactor to Assign a service') . '" alt="" />' . '  ' .    _('Select Contactor to Assign service'). '</p>';
  echo'<table>';
	echo'<table cellpadding="3" class="selection table table-hover">
	<tr>
		<td>' . _('Enter a partial Name') . ':</td>
		<td>';
if (isset($_POST['Keywords'])) {
	echo '<input type="text" name="Keywords" value="' . $_POST['Keywords'] . '" size="20" maxlength="25" />';
} else {
	echo '<input type="text" name="Keywords" size="20" maxlength="25" />';
}
echo '</td>
		<td><b>' . _('OR') . '</b></td>
		<td>' . _('Enter a partial Code') . ':</td>
		<td>';
if (isset($_POST['SupplierCode'])) {
	echo '<input type="text" autofocus="autofocus" name="SupplierCode" value="' . $_POST['SupplierCode'] . '" size="15" maxlength="18" />';
} else {
	echo '<input type="text" autofocus="autofocus" name="SupplierCode" size="15" maxlength="18" />';
}
echo '</td></tr>
		</table>
		<br /><div class="centre"><input type="submit" name="SearchContractors" value="' . _('Search Now') . '" /></div>';
if (isset($_POST['SearchContractors'])) {
	$ListCount = DB_num_rows($result);
	$ListPageMax = ceil($ListCount / $_SESSION['DisplayRecordsMax']);
	if (isset($_POST['Next'])) {
		if ($_POST['PageOffset'] < $ListPageMax) {
			$_POST['PageOffset'] = $_POST['PageOffset'] + 1;
		}
	}
	if (isset($_POST['Previous'])) {
		if ($_POST['PageOffset'] > 1) {
			$_POST['PageOffset'] = $_POST['PageOffset'] - 1;
		}
	}
	if ($ListPageMax > 1) {
		echo '<p>&nbsp;&nbsp;' . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': </p>';
		echo '<select name="PageOffset">';
		$ListPage = 1;
		while ($ListPage <= $ListPageMax) {
			if ($ListPage == $_POST['PageOffset']) {
				echo '<option value="' . $ListPage . '" selected="selected">' . $ListPage . '</option>';
			} else {
				echo '<option value="' . $ListPage . '">' . $ListPage . '</option>';
			}
			$ListPage++;
		}
		echo '</select>
			<input type="submit" name="Go" value="' . _('Go') . '" />
			<input type="submit" name="Previous" value="' . _('Previous') . '" />
			<input type="submit" name="Next" value="' . _('Next') . '" />';
		echo '<br />';
	}
	//echo '<input type="hidden" name="Search" value="' . _('Search Now') . '" />';
	echo '<br />
		<br />
		<br />';
 echo' </table>';
	echo'<table cellpadding="2" class="selection table table-hover">';
	echo '<tr>
	  		<th class="ascending">' . _('Code') . '</th>
			<th class="ascending">' .'Contractor'. '</th>
			<th class="ascending">' .'Address'. '</th>
			<th class="ascending">' .'Telephone No'. '</th>
			<th class="ascending">' .'Email'. '</th>
			<th></th>
		   </tr>';
	   $i=0;
  
 $k = 0; //row counter to determine background colour
	$RowIndex = 0;
	if (DB_num_rows($result) <> 0) {
		DB_data_seek($result, ($_POST['PageOffset'] - 1) * $_SESSION['DisplayRecordsMax']);
	}
	while (($row = DB_fetch_array($result)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {
		if ($k == 1) {
			echo '<tr class="EvenTableRows">';
			$k = 0;
		} else {
			echo '<tr class="OddTableRows">';
			$k = 1;
		}
  echo'<td><input name="SupplieridSelected" type="submit" value="'. $row['supplierid'] .'" /></td>
  <td>'.$row['suppname'] .'</td>
  <td>'. $row['address1'] .'</td>
  <td>'. $row['telephone'] .'</td>
  <td>'. $row['email'] .'</td>
	<input name="'. $row['supplierid'] .'SuppSelected" type="hidden" value="'. $row['suppname'] .'" />
  <input name="SelectedField" type="hidden" value="'. $_POST['SelectedField'] .'" />
  <input name="SelectedDescription" type="hidden" value="'. $_POST['SelectedDescription'] .'" />
  </tr>';
  }
  }
  echo '</table>';
  if (isset($ListPageMax) and $ListPageMax > 1) {
	echo '<p>&nbsp;&nbsp;' . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': </p>';
	echo '<select name="PageOffset">';
	$ListPage = 1;
	while ($ListPage <= $ListPageMax) {
		if ($ListPage == $_POST['PageOffset']) {
			echo '<option value="' . $ListPage . '" selected="selected">' . $ListPage . '</option>';
		} else {
			echo '<option value="' . $ListPage . '">' . $ListPage . '</option>';
		}
		$ListPage++;
	}
	echo '</select>
		<input type="submit" name="Go" value="' . _('Go') . '" />
		<input type="submit" name="Previous" value="' . _('Previous') . '" />
		<input type="submit" name="Next" value="' . _('Next') . '" />';
	echo '<br />';
}
include('includes/footer.inc');
  exit;
	 
	 }
?>
<div style="width:60%" class="centre">
	<table style="width:120%" class="selection table table-hover">
  <tr>
    <th colspan="2"><h4><?php echo 'Initiate New Farm Service'; ?></h4></th>
  </tr>
  <?php if(!isset($_SESSION['FP']->SP)){ ?>
  <tr>
    <td>Service Provided By</td>
	<?php
	$sql="SELECT service_Id,  
                  service_Name
				  FROM  farmservicesmaintenance
				  WHERE service_Id ='CO'";

$result=DB_query($sql);
echo '<td><select name="SP">';
while ($myrow=DB_fetch_array($result)){
	if (isset($_SESSION['FP']->SP) AND $_SESSION['FP']->SP==$myrow['service_Id']){
		echo '<option selected="True" value="' . $myrow['service_Id'] . '">' . htmlspecialchars($myrow['service_Name'], ENT_QUOTES,'UTF-8') . '</option>';
	} else {
		echo '<option value="' . $myrow['service_Id'] . '">' . htmlspecialchars($myrow['service_Name'], ENT_QUOTES,'UTF-8') . '</option>';
	}
}
echo '</select>';
echo '</td></tr>';

}else{
echo '<tr><td>Service Provider</td>
<td>'; if($_SESSION['FP']->SP =='CO'){ echo 'CONTRACTOR';}else{ echo 'KOFC';} echo '</td></tr>';
if($_SESSION['FP']->SP =='CO'){
  echo'<tr>
  <td style="font-size:10pt">Contractor.</td><td><center></center><input type="text" disabled="true"  value="'.$_SESSION['FP']->Contractor.'" /><input  type="hidden" size="60"  name="contractor" value="'.$_SESSION['FP']->Contractor.'"/><input tabindex="4" type="submit" name="SearchContractors" value="' . _('Search') . '" /></td></tr>';
  }
    ///////////////////////////////////////////////////////////////////
 if (!isset($_SESSION['FP']->prod_date)) {
		$_SESSION['FP']->prod_date = date($_SESSION['DefaultDateFormat']);
	}
 echo '<tr>
			<td>' . _('Production Date') . ':</td>
			<td><input type="text" required="required" autofocus="autofocus" class="date" alt="' . $_SESSION['FP']->prod_date . '" name="prod_date" size="11" value="' . $_SESSION['FP']->prod_date . '" /></td>
		</tr>';
 ////////////////////////////////////////////////////////////////////
  echo '<tr>
    <td>Field Name</td>';
  echo '<td><select name="field"  onchange="this.myform.submit">';
     $SQL = "SELECT code,
					Field_Name,
					acres					
				    FROM farmfield
				    ORDER BY code";
				 $result=DB_query($SQL);
  echo '<option selected="selected" value="">--Select Service Field--</option>';
  while ($myrow=DB_fetch_array($result)){	
  $Field =  htmlspecialchars($myrow['Field_Name'],ENT_QUOTES,'UTF-8',false);
		if (isset($_SESSION['FP']->Field) AND $_SESSION['FP']->Field==$myrow['code']){
		echo '<option selected="selected" value="' . $myrow['code'] . '">' . $Field . '</option>';		
		} else {
	    echo '<option value="' . $myrow['code'] . '">' .$Field. '</option>';
		}
		}
  echo '</select>
  </tr>';
  }
echo '</table>';
echo '</div>';
if(!isset($_SESSION['FP']->SP)){
echo'<div class="centre"><input name="SelectedService" type="submit" value="Submit" /></div>';
}else{
echo '<div class="centre">
		<input type="submit" name="Update" value="Update" />
  </div>';
  }
		echo '<br />';
	echo '</form>';
	
	
if (isset($_SESSION['FP']->Field) && isset($_SESSION['FP']->SP)) {


$i = 0; //Line Item Array pointer
echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Application=FA2&Ref=default&Link=contract_service" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<br />
	<table class="selection table table-hover">
	<tr style="height:15px">
		<th colspan="12"><h5>' . _('Details of Farm Service') . '</h5></th>
	</tr>
	<tr style="font-size:8pt">
		<th>' .  _('No.') . '</th>
		<th class="ascending">' .  _('Item Code') . '</th>
		<th class="ascending">' .  _('Item Description'). '</th>
		<th class="ascending">' .  _('Source'). '</th>
		<th>' .  _('Unit Cost'). '</th>
		<th class="ascending">' .  _('Qty/No./Ltrs'). '</th>
		<th class="ascending">' .  _('Area Coverved'). '</th>
		<th>' .  _('UOM'). '</th>
		<th>' .  _('Total Cost'). '</th>
		<th colspan="2">' .  _('Actions'). '</th></br>
	</tr>';

$k=0;
foreach ($_SESSION['FP']->LineItems as $LineItems) {
$i++;
	if ($k==1){
		echo '<tr style="font-size:12px;" class="EvenTableRows">';
		$k=0;
	} else {
		echo '<tr style="font-size:12px;" class="OddTableRows">';
		$k++;
	}
		
	$SourceResult= DB_query("SELECT source_Name FROM  farmitemsource
								         WHERE source_Id='". $LineItems->Source ."'");
							$my = DB_fetch_array($SourceResult);
	echo '<td>' . $i  . '</td>
			<td>' . $LineItems->StockID . '</td>
			<td>' . $LineItems->ItemDescription . '</td>
			<td>' . $LineItems->$Source . '</td>
			<td>'.locale_number_format($LineItems->cost, $_SESSION['StandardCostDecimalPlaces']).'</td>
			<td class="number">' . locale_number_format($LineItems->Quantity, $LineItems->DecimalPlaces) . '</td>
			<td class="number">' . locale_number_format($LineItems->AreaCovered, $LineItems->DecimalPlaces) . '</td>
			<td>' . $LineItems->UOM . '</td>
			<td>' . locale_number_format($LineItems->cost*($LineItems->AreaCovered), $_SESSION['StandardCostDecimalPlaces']) . '</td>
			<td><a href="'. htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Application=FA2&Ref=default&Link=contract_service&Edit='.$LineItems->LineNumber.'">' . _('Edit') . '</a></td>
			<td><a href="'. htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Application=FA2&Ref=default&Link=contract_service&Delete='.$LineItems->LineNumber.'">' . _('Delete') . '</a></td>
		</tr>';
}
echo '</table>
	<br />
	
	<div class="centre">
		<input type="submit" name="Submit" value="' . _('Submit') . '" />
	</div>';
	
	echo '<br />
    </div>
    </form>';

echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Application=FA2&Ref=default&Link=contract_service" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/magnifier.png" title="' . _('Search') . '" alt="" />' . ' ' . _('Search for Production Item'). '</p>';
$SQL = "SELECT categoryid,
				categorydescription
		FROM stockcategory WHERE categoryid='FS'";
$result1 = DB_query($SQL);
if (DB_num_rows($result1) == 0) {
	echo '<p class="bad">' . _('Problem Report') . ':<br />' . _('There are no stock categories currently defined please use the link below to set them up') . '</p>';
	echo '<br />
		<a href="' . $RootPath . '/Stocks.php.php">' . _('Define Farm Service') . '</a>';
	exit;
}

?>
<table style="width:130%;" class="selection table table-hover">
<?php 
	echo '<tr style="font-size:12px;">
	<td>' . _('Select type of service') . ':';
echo '<select name="StockCat">';
if (!isset($_POST['StockCat'])) {
	$_POST['StockCat'] ='';
}
if ($_POST['StockCat'] == 'All') {
	echo '<option selected="selected" value="All">' . _('All') . '</option>';
} else {
	echo '<option value="All">' . _('All') . '</option>';
}
while ($myrow1 = DB_fetch_array($result1)) {
	if ($myrow1['categoryid'] == $_POST['StockCat']) {
		echo '<option selected="selected" value="' . $myrow1['categoryid'] . '">' . $myrow1['categorydescription'] . '</option>';
	} else {
		echo '<option value="' . $myrow1['categoryid'] . '">' . $myrow1['categorydescription'] . '</option>';
	}
}
echo '</select></td>';

	
echo '<td></h3>' . _('Enter') . ' <b>' . _('Stock Code') . '</b>:</td>';

if (isset($_POST['StockCode'])) {
	echo '<td><input type="text"  name="StockCode" value="' . $_POST['StockCode'] . '" size="15" maxlength="18" /></td>';
} else {
	echo '<td><input type="text" name="StockCode" size="15" maxlength="18" /></td>';
	//////////////////////////////
	////////////////////////////
}
echo '<td>' . _('Enter partial') . '<b> ' . _('Description') . '</b>:</td><td>';
if (isset($_POST['Keywords'])) {
	echo '<input type="text" name="Keywords" value="' . $_POST['Keywords'] . '" title="' . _('Enter text that you wish to search for in the item description') . '" size="20" maxlength="25" />';
} else {
	echo '<input type="text" name="Keywords" title="' . _('Enter text that you wish to search for in the item description') . '" size="20" maxlength="25" />';
}
echo '</td>';
echo '</tr>
	</table>
	<br />
	<div class="centre">
		<input type="submit" name="Search" value="' . _('Search Now') . '" />
	</div>
	<br />
	</div>
	</form>';

if (isset($_POST['Search']) or isset($_POST['Next']) or isset($_POST['Prev'])){

	if ($_POST['Keywords']!='' AND $_POST['StockCode']=='') {
		prnMsg ( _('Order Item description has been used in search'), 'warn' );
	} elseif ($_POST['StockCode']!='' AND $_POST['Keywords']=='') {
		prnMsg ( _('Stock Code has been used in search'), 'warn' );
	} 
	if (isset($_POST['Keywords']) AND mb_strlen($_POST['Keywords'])>0) {
		//insert wildcard characters in spaces
		$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
		$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';

		if ($_POST['StockCat']=='All'){
			$SQL="SELECT b.stockid,
					a.description_Id,
					a.cost,
					a.units,	
					a.description
				    FROM farmdescriptions a,stockmaster b
					WHERE a.stockid=b.stockid 
					AND a.description " . LIKE . " '$SearchString'";
		} else {
			$SQL="SELECT b.stockid,
					a.description_Id,
					a.cost,
					a.units,	
					a.description
				    FROM farmdescriptions a, stockmaster b
					WHERE a.stockid=b.stockid 
					AND a.description " . LIKE . " '$SearchString'
				    AND b.stockid='". $_POST['StockCat'] ."'";
		} 

	}elseif (mb_strlen($_POST['source_Name'])>0){

		$_POST['source_Name'] = mb_strtoupper($_POST['source_Name']);
		$SearchString = '%' . $_POST['source_Name'] . '%';

		if ($_POST['StockCat']=='All'){
			$SQL="SELECT b.stockid,
					a.description_Id,
					a.cost,
					a.units,	
					a.description,
				    FROM farmdescriptions a,stockmaster b
					AND b.stockid=b.stockid";
		} else {
			$SQL="SELECT b.stockid,
					a.description_Id,
					a.cost,
					a.units,	
					a.description
				    FROM farmdescriptions a,stockmaster b
					WHERE a.stockid=b.stockid
					AND a.stockid=c.stockid'";
		}

	}
	 elseif (mb_strlen($_POST['StockCode'])>0){

		$_POST['StockCode'] = mb_strtoupper($_POST['StockCode']);
		$SearchString = '%' . $_POST['StockCode'] . '%';

		if ($_POST['StockCat']=='All'){
			$SQL="SELECT b.stockid,
					a.description_Id,
					a.cost,
					a.units,	
					a.description
				    FROM farmdescriptions a,stockmaster b
					WHERE a.stockid=b.stockid 
					AND a.description_Id='". $_POST['StockCode'] ."'";
		} else {
			$SQL="SELECT b.stockid,
					a.description_Id,
					a.cost,
					a.units,	
					a.description
				    FROM farmdescriptions a, stockmaster b
					WHERE a.stockid=b.stockid";
		}

	} else {
		if ($_POST['StockCat']=='All'){
		$SQL="SELECT b.stockid,
					a.description_Id,
					a.cost,
					a.units,	
					a.description
				    FROM farmdescriptions a, stockmaster b
					WHERE a.stockid=b.stockid";
		} else {
		$SQL="SELECT b.stockid,
					a.description_Id,
					a.cost,
					a.units,	
					a.description
				    FROM farmdescriptions a, stockmaster b
					WHERE a.stockid=b.stockid 
					AND b.stockid='". $_POST['StockCat'] ."'";
		}
	}

	if (isset($_POST['Next'])) {
		$Offset = $_POST['NextList'];
	}
	if (isset($_POST['Prev'])) {
		$Offset = $_POST['Previous'];
	}
	if (!isset($Offset) or $Offset<0) {
		$Offset=0;
	}
	$SQL = $SQL . ' LIMIT ' . $_SESSION['DefaultDisplayRecordsMax'] . ' OFFSET ' . ($_SESSION['DefaultDisplayRecordsMax']*$Offset);

	$ErrMsg = _('There is a problem selecting the part records to display because');
	$DbgMsg = _('The SQL used to get the part selection was');
	$SearchResult = DB_query($SQL,$ErrMsg, $DbgMsg);


	if (DB_num_rows($SearchResult)==0 ){
		prnMsg (_('There are no products available meeting the criteria specified'),'info');
	}
	if (DB_num_rows($SearchResult)<$_SESSION['DisplayRecordsMax']){
		$Offset=0;
	}

} //end of if search
/* display list if there is more than one record */
if (isset($searchresult) AND !isset($_POST['Select'])) {
	echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Application=FA2&Ref=default&Link=contract_service" method="post">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	$ListCount = DB_num_rows($searchresult);
	if ($ListCount > 0) {
		// If the user hit the search button and there is more than one item to show
		$ListPageMax = ceil($ListCount / $_SESSION['DisplayRecordsMax']);
		if (isset($_POST['Next'])) {
			if ($_POST['PageOffset'] < $ListPageMax) {
				$_POST['PageOffset'] = $_POST['PageOffset'] + 1;
			}
		}
		if (isset($_POST['Previous'])) {
			if ($_POST['PageOffset'] > 1) {
				$_POST['PageOffset'] = $_POST['PageOffset'] - 1;
			}
		}
		if ($_POST['PageOffset'] > $ListPageMax) {
			$_POST['PageOffset'] = $ListPageMax;
		}
		if ($ListPageMax > 1) {
			echo '<div class="centre"><br />&nbsp;&nbsp;' . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';
			echo '<select name="PageOffset">';
			$ListPage = 1;
			while ($ListPage <= $ListPageMax) {
				if ($ListPage == $_POST['PageOffset']) {
					echo '<option value=' . $ListPage . ' selected>' . $ListPage . '</option>';
				} else {
					echo '<option value=' . $ListPage . '>' . $ListPage . '</option>';
				}
				$ListPage++;
			}
			echo '</select>
				<input type="submit" name="Go" value="' . _('Go') . '" />
				<input type="submit" name="Previous" value="' . _('Previous') . '" />
				<input type="submit" name="Next" value="' . _('Next') . '" />
				<input type="hidden" name=Keywords value="'.$_POST['Keywords'].'" />
				<input type="hidden" name=StockCat value="'.$_POST['StockCat'].'" />
				<input type="hidden" name=StockCode value="'.$_POST['StockCode'].'" />
				<br />
				</div>';
		}
		echo '<table cellpadding="2" class="selection table table-hover">';
		echo '<tr>
				<th>' . _('Code') . '</th>
				<th>' . _('Description') . '</th>
				<th>' . _('Total Qty On Hand') . '</th>
				<th>' . _('Units') . '</th>
				<th>' . _('Stock Status') . '</th>
			</tr>';
		$j = 1;
		$k = 0; //row counter to determine background colour
		$RowIndex = 0;
		if (DB_num_rows($searchresult) <> 0) {
			DB_data_seek($searchresult, ($_POST['PageOffset'] - 1) * $_SESSION['DisplayRecordsMax']);
		}
		while (($myrow = DB_fetch_array($searchresult)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {
			if ($k == 1) {
				echo '<tr class="EvenTableRows">';
				$k = 0;
			} else {
				echo '<tr class="OddTableRows">';
				$k++;
			}
			if ($myrow['mbflag'] == 'D') {
				$qoh = _('N/A');
			} else {
				$qoh = locale_number_format($myrow['qoh'], $myrow['decimalplaces']);
			}
			if ($myrow['discontinued']==1){
				$ItemStatus = '<p class="bad">' . _('Obsolete') . '</p>';
			} else {
				$ItemStatus ='';
			}

			echo '<td><input type="submit" name="Select" value="' . $myrow['StockID'] . '" /></td>
					<td>' . $myrow['description'] . '</td>
					<td class="number">' . $units . '</td>
					<td>' . $myrow['units'] . '</td>
					<td><a target="_blank" href="' . $RootPath . '/FarmItemStatus.php?StockID=' . $myrow['stockid'].'">' . _('View') . '</a></td>
					<td>' . $ItemStatus . '</td>
				</tr>';
			//end of page full new headings if
		}
		//end of while loop
		echo '</table>
              </div>
              </form>
              <br />';
	}
}
/* end display list if there is more than one record */

if (isset($SearchResult)) {
	$j = 1;
	echo '<br />
		<div class="page_help_text">' . _('Select an item by entering the quantity required.  Click Add to Production when ready.') . '</div>
		<br />
		<form action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Application=FA2&Ref=default&Link=contract_service" method="post" id="orderform">

		<div>
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
		<table class="selection table table-hover">
		<tr>
			<td>
				<input type="hidden" name="Previous" value="'.($Offset-1).'" />
				<input tabindex="'.($j+8).'" type="submit" name="Prev" value="'._('Prev').'" /></td>
				<td style="text-align:center" colspan="4">
				<input type="hidden" name="order_items" value="1" />
				<input tabindex="'.($j+9).'" type="submit" value="'._('Add to Production').'" /></td>
			<td>
				<input type="hidden" name="NextList" value="'.($Offset+1).'" />
				<div align="right"><input tabindex="'.($j+10).'" type="submit" name="Next" value="'._('Next').'" /></div></td>
			</tr>
			<tr>
				<th class="ascending">' . _('Code') . '</th>
				<th class="ascending">' . _('Description') . '</th>
				<th class="ascending">' . _('Source') . '</th>
				<th>' . _('Units') . '</th>
				<th>' . _('Unit Cost') . '</th>
				<th class="ascending">' . _('Area Covered.') . '</th>
				<th class="ascending">' . _('Quantity /No/Ltrs.') . '</th>
			</tr>';
	$ImageSource = _('No Image');

	$k=0; //row colour counter
	$i=0;
	while ($myrow=DB_fetch_array($SearchResult)) {
		
		// Find the quantity on outstanding sales orders
		

		// Find the quantity on purchase orders
	

		// Find the quantity on works orders
		$sql = "SELECT SUM(woitems.qtyreqd - woitems.qtyrecd) AS dedm
			   FROM woitems
			   WHERE stockid='" . $myrow['stockid'] ."'";
		$ErrMsg = _('The order details for this product cannot be retrieved because');
		$WoResult = DB_query($sql,$ErrMsg);

		$WoRow = DB_fetch_row($WoResult);
		if ($WoRow[0]!=null){
			$WoQty =  $WoRow[0];
		} else {
			$WoQty = 0;
		}

		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}
		$OnOrder = $PurchQty + $WoQty;
		$Available = $QOH - $DemandQty + $OnOrder;
		echo '<td>' . $myrow['description_Id'] . '</td>
				<td>' . $myrow['description'] . '</td>
				<td><select name="Source'.$i.'">';
				echo'<option value="KOFC">KOFC</option>
					  <option value="Contract">Contract</option>';
					  echo'</select>
				</td>
				<td>' . $myrow['units'] . '</td>
				<td>' . $myrow['cost'] . '</td>
				<td><input class="number" ' . ($i==0 ? 'autofocus="autofocus"':'') . ' tabindex="'.($j+7).'" type="text" size="6" name="AreaCovered'.$i.'" value="0" />
				<input type="hidden" name="StockID'.$i.'" value="'.$myrow['description_Id'].'" />
				<td><input class="number" ' . ($i==0 ? 'autofocus="autofocus"':'') . ' tabindex="'.($j+7).'" type="text" size="6" name="Quantity'.$i.'" value="0" />
				<input type="hidden" name="StockID'.$i.'" value="'.$myrow['description_Id'].'" />
				</td>
			</tr>';
		echo '<input type="hidden" name="DecimalPlaces'.$i.'" value="1" />';
		echo '<input type="hidden" name="ItemDescription'.$i.'" value="' . $myrow['description'] . '" />';
		echo '<input type="hidden" name="cost'.$i.'" value="' . $myrow['cost'] . '" />';
		echo '<input type="hidden" name="units'.$i.'" value="' . $myrow['units'] . '" />';
		$i++;
	}
#end of while loop
	echo '<tr>
			<td><input type="hidden" name="Previous" value="'.($Offset-1).'" />
				<input tabindex="'.($j+7).'" type="submit" name="Prev" value="'._('Prev').'" /></td>
			<td style="text-align:center" colspan="4"><input type="hidden" name="order_items" value="1" />
				<input tabindex="'.($j+8).'" type="submit" value="'._('Add to Production').'" /></td>
			<td><input type="hidden" name="NextList" value="'.($Offset+1).'" />
				<div align="right"><input tabindex="'.($j+9).'" type="submit" name="Next" value="'._('Next').'" /></div></td>
		<tr/>
		
		</table>
       </div>
       </form>';
}#end if SearchResults to show
}
//*********************************************************************************************************

?>
