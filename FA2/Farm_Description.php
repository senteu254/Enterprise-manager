<link rel="stylesheet" href="FA2/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="FA2/iCheck/flat/blue.css">
<?php

/*************************************************************************************************************/
$UnitName= DB_query("SELECT unitname FROM  unitsofmeasure
								         WHERE unitid='".$_POST['CategoryID']."'");
							$my = DB_fetch_array($UnitName);
if (isset($_POST["Save"])) {  
	$description_Id= $_POST['description_Id'] ;	
	$stockID=$_POST['stockid'];
	$description=$_POST['description'] ;
	$company=$_POST['company'] ;
	$cost=$_POST['cost'] ;
	$UnitName=$_POST['CategoryID'];
	
	 $result4 = DB_query("SELECT description_Id
								FROM farmdescriptions
								WHERE description_Id='" . $description_Id ."'");
if(empty($description_Id)) {
	prnMsg(_('Descriptions ID cannot be Empty '),'error');
	$InputError = 1;
	}elseif(empty($stockID)) {
	prnMsg(_('Stock Id Number cannot be Empty '),'error');
	$InputError = 1;
	}elseif(empty($description)) {
	prnMsg(_('Production Descriptions cannot be Empty '),'error');
	$InputError = 1;
	}elseif(empty($cost)) {
	prnMsg(_('Cost of description/item cannot be Empty '),'error');
	$InputError = 1;
	}elseif (DB_num_rows($result4)==1){
	prnMsg(_('The Descriptions ID   entered is already in the database - duplicate Service ID are prohibited by the system. Try choosing an alternative Descriptions ID'),'error');
	$InputError = 1;
	}elseif ($InputError == 0){
	DB_query("INSERT INTO `farmdescriptions`(description_Id,stockid,description,cost,units) 
					 VALUES ('$description_Id','$stockID','$description','$cost','" . $_POST['CategoryID'] . "')");
							 $InsResult = DB_query($sql,$ErrMsg,$DbgMsg,true);
								DB_Txn_Commit();
								if (DB_error_no() ==0) {
						prnMsg( _('New descriptions ') .' ' . '<a href="Farm_Description.php?description_Id=' . $description_Id . '">' . $description_Id . '</a> '. _(' has been added to      Production Service'),'success'); 
     }
     }
	 }
	 
/*************************************************************************************************************/

if (isset($_GET['StockID'])) {
	//The page is called with a StockID
	$_GET['StockID'] = trim(mb_strtoupper($_GET['StockID']));
	$_POST['Select'] = trim(mb_strtoupper($_GET['StockID']));
}
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/inventory.png" title="' . _('Inventory Items') . '" alt="" />' . ' ' . _('Production Description') . '</p>';
if (isset($_GET['NewSearch']) or isset($_POST['Next']) or isset($_POST['Previous']) or isset($_POST['Go'])) {
	unset($StockID);
	unset($_SESSION['SelectedStockItem']);
	unset($_POST['Select']);
}
if (!isset($_POST['PageOffset'])) {
	$_POST['PageOffset'] = 1;
} else {
	if ($_POST['PageOffset'] == 0) {
		$_POST['PageOffset'] = 1;
	}
}
if (isset($_POST['StockCode'])) {
	$_POST['StockCode'] = trim(mb_strtoupper($_POST['StockCode']));
}
// Always show the search facilities
$SQL = "SELECT categoryid,
				categorydescription
		FROM stockcategory WHERE categoryid='FS'";
$result1 = DB_query($SQL);
if (DB_num_rows($result1) == 0) {
	echo '<p class="bad">' . _('Problem Report') . ':<br />' . _('There are no stock categories currently defined please use the link below to set them up') . '</p>';
	echo '<br /><a href="' . $RootPath . '/StockCategories.php">' . _('Define Stock Categories') . '</a>';
	exit;
}
// end of showing search facilities
/* displays item options if there is one and only one selected */
if (!isset($_POST['Search']) AND (isset($_POST['Select']) OR isset($_SESSION['SelectedStockItem']))) {
	if (isset($_POST['Select'])) {
		$_SESSION['SelectedStockItem'] = $_POST['Select'];
		$StockID = $_POST['Select'];
		unset($_POST['Select']);
	} else {
	$StockID = $_SESSION['SelectedStockItem'];
	}
	$result = DB_query("SELECT stockmaster.description,
								stockmaster.longdescription,
								stockcategory.stocktype,
								stockmaster.units,
								stockmaster.decimalplaces,
								stockmaster.controlled,
								stockmaster.serialised,
								stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost AS cost,
								stockmaster.discontinued,
								stockmaster.eoq,
								stockmaster.volume,
								stockmaster.grossweight,
								stockcategory.categorydescription,
								stockmaster.categoryid
						FROM stockmaster INNER JOIN stockcategory
						ON stockmaster.categoryid=stockcategory.categoryid
						WHERE stockid='" . $StockID . "'
						AND stockmaster.mbflag!='B'
					    AND stockmaster.mbflag!='A'
						AND stockmaster.mbflag!='M'
					    AND stockmaster.mbflag!='K'");
	$myrow = DB_fetch_array($result);
	$Its_A_Kitset_Assembly_Or_Dummy = false;
	$Its_A_Dummy = false;
	$Its_A_Kitset = false;
	$Its_A_Labour_Item = false;
	if ($myrow['discontinued']==1){
		$ItemStatus = '<p class="bad">' ._('Obsolete') . '</p>';
	} else {
		$ItemStatus = '';
	}
	
	echo '<table width="90%" class="selection table table-hover">
			<tr>
				<th colspan="3"><img src="' . $RootPath . '/css/' . $Theme . '/images/inventory.png" title="' . _('Inventory') . '" alt="" /><b title="' . $myrow['longdescription'] . '">' . ' ' . $StockID . ' - ' . $myrow['description'] . '</b> ' . $ItemStatus . '</th>
			</tr>';


	echo '<tr>
			<td style="width:70%" valign="top">
			<table>'; //nested table
	echo '<tr><th class="number">' . _('Category') . ':</th> <td colspan="2" class="select">' . $myrow['categorydescription'] , '</td></tr>';
	echo '<tr><th class="number">' . _('Item Type') . ':</th>
			<td colspan="2" class="select">';
	switch ($myrow['mbflag']) {
		case 'A':
			echo _('Assembly Item');
			$Its_A_Kitset_Assembly_Or_Dummy = True;
		break;
		case 'K':
			echo _('Kitset Item');
			$Its_A_Kitset_Assembly_Or_Dummy = True;
			$Its_A_Kitset = True;
		break;
		case 'D':
			echo _('Service/Labour Item');
			$Its_A_Kitset_Assembly_Or_Dummy = True;
			$Its_A_Dummy = True;
			if ($myrow['stocktype'] == 'L') {
				$Its_A_Labour_Item = True;
			}
		break;
		case 'B':
			echo _('Purchased Item');
		break;
		default:
			echo _('Service');
		break;
	}
	echo '</td><th class="number">' . _('Control Level') . ':</th><td class="select">';
	if ($myrow['serialised'] == 1) {
		echo _('serialised');
	} elseif ($myrow['controlled'] == 1) {
		echo _('Batchs/Lots');
	} else {
		echo _('N/A');
	}
	echo '</td><th class="number">' . _('Units') . ':</th>
			<td class="select">' . $myrow['units'] . '</td></tr>';
	echo '<tr><th class="number">' . _('Volume') . ':</th>
			<td class="select" colspan="2">' . locale_number_format($myrow['volume'], 3) . '</td>
			<th class="number">' . _('Weight') . ':</th>
			<td class="select">' . locale_number_format($myrow['grossweight'], 3) . '</td>
			<th class="number">' . _('EOQ') . ':</th>
			<td class="select">' . locale_number_format($myrow['eoq'], $myrow['decimalplaces']) . '</td></tr>';
	if (in_array($PricesSecurity, $_SESSION['AllowedPageSecurityTokens']) OR !isset($PricesSecurity)) {
		echo '<tr><th >' . _('High Range') . ':</th>
				<td class="select">';
		$PriceResult = DB_query("SELECT typeabbrev,
										price
								FROM prices
								WHERE currabrev ='" . $_SESSION['CompanyRecord']['currencydefault'] . "'
								AND typeabbrev = '" . $_SESSION['DefaultPriceList'] . "'
								AND debtorno=''
								AND branchcode=''
								AND startdate <= '". Date('Y-m-d') ."' AND ( enddate >= '" . Date('Y-m-d') . "' OR enddate = '0000-00-00')
								AND stockid='" . $StockID . "'");
		if ($myrow['mbflag'] == 'K' OR $myrow['mbflag'] == 'A') {
			$CostResult = DB_query("SELECT SUM(bom.quantity * (stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost)) AS cost
									FROM bom INNER JOIN stockmaster
									ON bom.component=stockmaster.stockid
									WHERE bom.parent='" . $StockID . "'
                                    AND bom.effectiveafter <= '" . date('Y-m-d') . "'
                                    AND bom.effectiveto > '" . date('Y-m-d') . "'");
			$CostRow = DB_fetch_row($CostResult);
			$Cost = $CostRow[0];
		} else {
			$Cost = $myrow['cost'];
		}
		if (DB_num_rows($PriceResult) == 0) {
			echo _('No Default Price Set in Home Currency') . '</td></tr>';
			$Price = 0;
		} else {
			$PriceRow = DB_fetch_row($PriceResult);
			$Price = $PriceRow[1];
			echo $PriceRow[0] . '</td>
				<td class="select">' . locale_number_format($Price, $_SESSION['CompanyRecord']['decimalplaces']) . '</td>
				<th class="number">' . _('Gross Profit') . '</th>
				<td class="select">';
			if ($Price > 0) {
				$GP = locale_number_format(($Price - $Cost) * 100 / $Price, 1);
			} else {
				$GP = _('N/A');
			}
			echo $GP . '%' . '</td>
				</tr>';
		}
		if ($myrow['mbflag'] == 'K' OR $myrow['mbflag'] == 'A') {
			$CostResult = DB_query("SELECT SUM(bom.quantity * (stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost)) AS cost
									FROM bom INNER JOIN
										stockmaster
									ON bom.component=stockmaster.stockid
									WHERE bom.parent = '" . $StockID . "'
                                    AND bom.effectiveafter <= '" . date('Y-m-d') . "'
                                    AND bom.effectiveto > '" . date('Y-m-d') . "'");
			$CostRow = DB_fetch_row($CostResult);
			$Cost = $CostRow[0];
		} else {
			$Cost = $myrow['cost'];
		}
		echo '<tr>';
				//start least price
				echo '<tr><th >' . _('Lowest Range') . ':</th>
				<td class="select">';
		$PriceResult = DB_query("SELECT typeabbrev,
										lprice
								FROM prices
								WHERE currabrev ='" . $_SESSION['CompanyRecord']['currencydefault'] . "'
								AND typeabbrev = '" . $_SESSION['DefaultPriceList'] . "'
								AND debtorno=''
								AND branchcode=''
								AND startdate <= '". Date('Y-m-d') ."' AND ( enddate >= '" . Date('Y-m-d') . "' OR enddate = '0000-00-00')
								AND stockid='" . $StockID . "'");
		
		if (DB_num_rows($PriceResult) == 0) {
			echo _('No Default Price Set in Home Currency') . '</td></tr>';
			$Price = 0;
		} else { 
			$PriceRow = DB_fetch_row($PriceResult);
			$Price = $PriceRow[1];
			echo $PriceRow[0] . '</td>
				<td class="select">' . locale_number_format($Price, $_SESSION['CompanyRecord']['decimalplaces']) . '</td>';
				}
				//End least price
			
				
			echo'</tr>';
	} //end of if PricesSecuirty allows viewing of prices
	echo '</table>'; //end of first nested table
	// Item Category Property mod: display the item properties
	echo '<table class="selection table table-hover">';

	$sql = "SELECT stkcatpropid,
					label,
					controltype,
					defaultvalue
				FROM stockcatproperties
				WHERE categoryid ='" . $myrow['categoryid'] . "'
				AND reqatsalesorder =0
				ORDER BY stkcatpropid";
	$PropertiesResult = DB_query($sql);
	$PropertyCounter = 0;
	$PropertyWidth = array();
	while ($PropertyRow = DB_fetch_array($PropertiesResult)) {
		$PropValResult = DB_query("SELECT value
									FROM stockitemproperties
									WHERE stockid='" . $StockID . "'
									AND stkcatpropid ='" . $PropertyRow['stkcatpropid']."'");
		$PropValRow = DB_fetch_row($PropValResult);
		if (DB_num_rows($PropValResult)==0){
			$PropertyValue = _('Not Set');
		} else {
			$PropertyValue = $PropValRow[0];
		}
		echo '<tr>
				<th align="right">' . $PropertyRow['label'] . ':</th>';
		switch ($PropertyRow['controltype']) {
			case 0:
			case 1:
				echo '<td class="select" style="width:60px">' . $PropertyValue;
			break;
			case 2; //checkbox
				echo '<td class="select" style="width:60px">';
				if ($PropertyValue == _('Not Set')){
					echo _('Not Set');
				} elseif ($PropertyValue == 1){
					echo _('Yes');
				} else {
					echo _('No');
				}
			break;
		} //end switch
	echo '</td></tr>';
	$PropertyCounter++;
} 
$SQL = "SELECT * FROM  unitsofmeasure";
$unit = DB_query($SQL);
if (DB_num_rows($unit) == 0) {
	echo '<p class="bad">' . _('Problem Report') . ':<br />' . _('There are no Units of Measure defined please') . '</p>';
	exit;
}//end loop round properties for the item category
 echo '</table></td>';
/********************************************************************************************************************************/
 echo'<table align="center" style="width:50%" class="selection table table-hover">';
 echo '<form action="" method="post" name="myform" enctype="multipart/form-data" target="_self">';
	echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';
echo'<input type="hidden" size="10" maxlength="60" value="'.$StockID.'" name="stockid" />';
	echo'<tr>
	<td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Description Id</td><td><input type="text" size="25" maxlength="80" placeholder="Description Id"        name="description_Id" /><td><center></center>
	</td> 
	</tr>
   <tr><td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Descriptions</td><td><input type="text" size="40" placeholder="Description of the Service" maxlength="100" name="description" /><td><td><center></center></td></tr>';	
      
 echo' <tr><td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cost</td><td><input type="text" size="25" placeholder="Cost of the Service/item" maxlength="60" name="cost" /><td><td><center></center></td></tr>';
 ////////////////////////////////////////////////////////////////////////////////

echo '<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . _('Units') . ':</td>
		<td><select name="CategoryID" onchange="ReloadForm(ItemForm.UpdateCategories)">';

$sql = "SELECT categoryid, categorydescription FROM stockcategory";
$ErrMsg = _('The stock categories could not be retrieved because');
$DbgMsg = _('The SQL used to retrieve stock categories and failed was');
$result = DB_query($sql,$ErrMsg,$DbgMsg);

while ($myrow4=DB_fetch_array($unit)){
	if (!isset($_POST['CategoryID']) OR  $myrow4['unitname']==$_POST['CategoryID']){
		echo '<option selected="selected" value="'. $myrow4['unitname'] . '">' . $myrow4['unitname'] . '</option>';
	} else {
		echo '<option value="'. $myrow4['unitname'] . '">' . $myrow4['unitname'] . '</option>';
	}
	$UnitName=$myrow4['unitname'];
}

if (!isset($_POST['CategoryID'])) {
	$_POST['CategoryID']=$UnitName;
}

echo '</select><a target="_blank" href="'. $RootPath . '/UnitsOfMeasure.php">' . _('Add or modify units') . '</a></td>
	</tr>';
 
  ////////////////////////////////////////////////////////////////////////////////
	echo'<tr>
	<td>&nbsp;</td><td><input type="submit" name="Save" value="Save" /></td></tr>
	</tr>';
 echo'</form>';
 echo'</table>';
	
/***********************************************************************************************************************/
//echo '<a href="' . $RootPath . '/Stocks.php?">' . _('Insert New Item') . '</a><br />';
} // end displaying item options if there is one and only one record
echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=FA2&Ref=Farm_Description" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/magnifier.png" title="' . _('Search') . '" alt="" />' . ' ' . _('Search for Production Service'). '</p>';
echo '<a href="' . $RootPath . '/Stocks.php?">' . _('Insert New Item') . '</a><br />';
echo '<table class="selection table table-hover"><tr>';
echo '<td>' . _('In Stock Category') . ':';
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
echo '<td>' . _('Enter partial') . '<b> ' . _('Description') . '</b>:</td><td>';
if (isset($_POST['Keywords'])) {
	echo '<input type="text" autofocus="autofocus" name="Keywords" value="' . $_POST['Keywords'] . '" title="' . _('Enter text that you wish to search for in the item description') . '" size="20" maxlength="25" />';
} else {
 echo '<input type="text" autofocus="autofocus" name="Keywords" title="' . _('Enter text that you wish to search for in the item description') . '" size="20" maxlength="25" />';
}
echo '</td></tr><tr><td></td>';
echo '<td><b>' . _('OR') . ' ' . '</b>' . _('Enter partial') . ' <b>' . _('Stock Code') . '</b>:</td>';
echo '<td>';
if (isset($_POST['StockCode'])) {
	echo '<input type="text" name="StockCode" value="' . $_POST['StockCode'] . '" title="' . _('Enter text that you wish to search for in the item code') . '" size="15" maxlength="18" />';
} else {
	echo '<input type="text" name="StockCode" title="' . _('Enter text that you wish to search for in the item code') . '" size="15" maxlength="18" />';
}
echo '</td></tr></table><br />';
echo '<div class="centre"><input type="submit" name="Search" value="' . _('Search Now') . '" /></div><br />';
echo '</div>
      </form>';
// query for list of record(s)
if(isset($_POST['Go']) OR isset($_POST['Next']) OR isset($_POST['Previous'])) {
	$_POST['Search']='Search';
}
if (isset($_POST['Search']) OR isset($_POST['Go']) OR isset($_POST['Next']) OR isset($_POST['Previous'])) {
	if (!isset($_POST['Go']) AND !isset($_POST['Next']) AND !isset($_POST['Previous'])) {
		// if Search then set to first page
		$_POST['PageOffset'] = 1;
	}
	if ($_POST['Keywords'] AND $_POST['StockCode']) {
		prnMsg (_('Stock description keywords have been used in preference to the Stock code extract entered'), 'info');
	}
	if ($_POST['Keywords']) {
		//insert wildcard characters in spaces
		$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
		$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';
		if ($_POST['StockCat'] == 'All') {
			$SQL = "SELECT stockmaster.stockid,
							stockmaster.description,
							stockmaster.longdescription,
							SUM(locstock.quantity) AS qoh,
							stockmaster.units,
							
							stockmaster.discontinued,
							stockmaster.decimalplaces
						FROM stockmaster LEFT JOIN stockcategory
						ON stockmaster.categoryid=stockcategory.categoryid,
							locstock
						WHERE stockmaster.stockid=locstock.stockid
						AND stockmaster.description " . LIKE . " '$SearchString' 
					    AND stockmaster.mbflag!='B'
					    AND stockmaster.mbflag!='A'
						AND stockmaster.mbflag!='M'
					    AND stockmaster.mbflag!='K'
						GROUP BY stockmaster.stockid,
							stockmaster.description,
							stockmaster.longdescription,
							stockmaster.units,
							stockmaster.mbflag,
							stockmaster.discontinued,
							stockmaster.decimalplaces
						ORDER BY stockmaster.discontinued, stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
							stockmaster.description,
							stockmaster.longdescription,
							SUM(locstock.quantity) AS qoh,
							stockmaster.units,
							
							stockmaster.discontinued,
							stockmaster.decimalplaces
						FROM stockmaster INNER JOIN locstock
						ON stockmaster.stockid=locstock.stockid
						WHERE description " . LIKE . " '$SearchString'
						AND categoryid='" . $_POST['StockCat'] . "'
						AND stockmaster.mbflag!='B'
					    AND stockmaster.mbflag!='A'
						AND stockmaster.mbflag!='M'
					    AND stockmaster.mbflag!='K'
						GROUP BY stockmaster.stockid,
							stockmaster.description,
							stockmaster.longdescription,
							stockmaster.units,
							stockmaster.mbflag,
							stockmaster.discontinued,
							stockmaster.decimalplaces
						ORDER BY stockmaster.discontinued, stockmaster.stockid";
		}
	} elseif (isset($_POST['StockCode'])) {
		$_POST['StockCode'] = mb_strtoupper($_POST['StockCode']);
		if ($_POST['StockCat'] == 'All') {
			$SQL = "SELECT stockmaster.stockid,
							stockmaster.description,
							stockmaster.longdescription,
						
							stockmaster.discontinued,
							SUM(locstock.quantity) AS qoh,
							stockmaster.units,
							stockmaster.decimalplaces
						FROM stockmaster
						INNER JOIN stockcategory
						ON stockmaster.categoryid=stockcategory.categoryid
						INNER JOIN locstock ON stockmaster.stockid=locstock.stockid
						WHERE stockmaster.stockid " . LIKE . " '%" . $_POST['StockCode'] . "%'
						AND stockmaster.mbflag!='B'
					    AND stockmaster.mbflag!='A'
						AND stockmaster.mbflag!='M'
					    AND stockmaster.mbflag!='K'
						GROUP BY stockmaster.stockid,
							stockmaster.description,
							stockmaster.longdescription,
							stockmaster.units,
							stockmaster.mbflag,
							stockmaster.discontinued,
							stockmaster.decimalplaces
						ORDER BY stockmaster.discontinued, stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.longdescription,
						
						stockmaster.discontinued,
						sum(locstock.quantity) as qoh,
						stockmaster.units,
						stockmaster.decimalplaces
					FROM stockmaster INNER JOIN locstock
					ON stockmaster.stockid=locstock.stockid
					WHERE stockmaster.stockid " . LIKE . " '%" . $_POST['StockCode'] . "%'
					    AND stockmaster.mbflag!='B'
					    AND stockmaster.mbflag!='A'
						AND stockmaster.mbflag!='M'
					    AND stockmaster.mbflag!='K'
					AND categoryid='" . $_POST['StockCat'] . "'
					GROUP BY stockmaster.stockid,
						stockmaster.description,
						stockmaster.longdescription,
						stockmaster.units,
						stockmaster.mbflag,
						stockmaster.discontinued,
						stockmaster.decimalplaces
					ORDER BY stockmaster.discontinued, stockmaster.stockid";
		}
	} elseif (!isset($_POST['StockCode']) AND !isset($_POST['Keywords'])) {
		if ($_POST['StockCat'] == 'All') {
			$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.longdescription,
						
						stockmaster.discontinued,
						SUM(locstock.quantity) AS qoh,
						stockmaster.units,
						stockmaster.decimalplaces
					FROM stockmaster
					LEFT JOIN stockcategory
					ON stockmaster.categoryid=stockcategory.categoryid,
						locstock
					WHERE stockmaster.stockid=locstock.stockid
					AND stockmaster.mbflag!='B'
					    AND stockmaster.mbflag!='A'
						AND stockmaster.mbflag!='M'
					    AND stockmaster.mbflag!='K'
					GROUP BY stockmaster.stockid,
						stockmaster.description,
						stockmaster.longdescription,
						stockmaster.units,
						stockmaster.mbflag,
						stockmaster.discontinued,
						stockmaster.decimalplaces
					ORDER BY stockmaster.discontinued, stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.longdescription,
						
						stockmaster.discontinued,
						SUM(locstock.quantity) AS qoh,
						stockmaster.units,
						stockmaster.decimalplaces
					FROM stockmaster INNER JOIN locstock
					ON stockmaster.stockid=locstock.stockid
					WHERE categoryid='" . $_POST['StockCat'] . "'
					AND stockmaster.mbflag!='B'
					    AND stockmaster.mbflag!='A'
						AND stockmaster.mbflag!='M'
					    AND stockmaster.mbflag!='K'
					GROUP BY stockmaster.stockid,
						stockmaster.description,
						stockmaster.longdescription,
						stockmaster.units,
						stockmaster.mbflag,
						stockmaster.discontinued,
						stockmaster.decimalplaces
					ORDER BY stockmaster.discontinued, stockmaster.stockid";
		}
	}
	$ErrMsg = _('No stock items were returned by the SQL because');
	$DbgMsg = _('The SQL that returned an error was');
	$SearchResult = DB_query($SQL, $ErrMsg, $DbgMsg);
	if (DB_num_rows($SearchResult) == 0) {
		prnMsg(_('No stock items were returned by this search please re-enter alternative criteria to try again'), 'info');
	}
	unset($_POST['Search']);
}
/* end query for list of records */
/* display list if there is more than one record */
if (isset($SearchResult) AND !isset($_POST['Select'])) {
	echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=FA2&Ref=Farm_Description" method="post">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	$ListCount = DB_num_rows($SearchResult);
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
					echo '<option value="' . $ListPage . '" selected="selected">' . $ListPage . '</option>';
				} else {
					echo '<option value="' . $ListPage . '">' . $ListPage . '</option>';
				}
				$ListPage++;
			}
			echo '</select>
				<input type="submit" name="Go" value="' . _('Go') . '" />
				<input type="submit" name="Previous" value="' . _('Previous') . '" />
				<input type="submit" name="Next" value="' . _('Next') . '" />
				<input type="hidden" name="Keywords" value="'.$_POST['Keywords'].'" />
				<input type="hidden" name="StockCat" value="'.$_POST['StockCat'].'" />
				<input type="hidden" name="StockCode" value="'.$_POST['StockCode'].'" />
				<br />
				</div>';
		      }
		echo '<table id="ItemSearchTable" class="selection table table-hover">';
		$TableHeader = '<tr>
							<th>' . _('Stock Status') . '</th>
							<th class="ascending">' . _('Code') . '</th>
							<th class="ascending">' . _('Description') . '</th>
							
							<th>' . _('Units') . '</th>
						</tr>';
		echo $TableHeader;
		$j = 1;
		$k = 0; //row counter to determine background colour
		$RowIndex = 0;
		if (DB_num_rows($SearchResult) <> 0) {
			DB_data_seek($SearchResult, ($_POST['PageOffset'] - 1) * $_SESSION['DisplayRecordsMax']);
		}
		while (($myrow = DB_fetch_array($SearchResult)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {
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

			echo '<td>' . $ItemStatus . '</td>
				<td><input type="submit" name="Select" value="' . $myrow['stockid'] . '" /></td>
				<td title="'. $myrow['longdescription'] . '">' . $myrow['description'] . '</td>
				
				<td>' . $myrow['units'] . '</td>
				<td><a target="_blank" href="' . $RootPath . '/StockStatus.php?StockID=' . $myrow['stockid'].'">' . _('View') . '</a></td>
				</tr>';
/*
			$j++;

			if ($j == 20 AND ($RowIndex + 1 != $_SESSION['DisplayRecordsMax'])) {
				$j = 1;
				echo $TableHeader;
			}
			*/
			$RowIndex = $RowIndex + 1;
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
?>