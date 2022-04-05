<?php
/* $Id: SelectProduct.php 7096 2015-01-24 03:08:00Z turbopt $*/

$PricesSecurity = 12;//don't show pricing info unless security token 12 available to user
$SuppliersSecurity = 9; //don't show supplier purchasing info unless security token 9 available to user

include ('includes/session.inc');
$Title = _('View Production Item');
/* webERP manual links before header.inc */
$ViewTopic= 'Production Descriptions';
$BookMark = 'Productions';

include ('includes/header.inc');
include ('includes/SQL_CommonFunctions.inc');

if (isset($_GET['StockID'])) {
	//The page is called with a StockID
	$_GET['StockID'] = trim(mb_strtoupper($_GET['StockID']));
	$_POST['Select'] = trim(mb_strtoupper($_GET['StockID']));
}
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/inventory.png" title="' . _('View Production Item') . '" alt="" />' . ' ' . _('View Production Item') . '</p>';
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
$SQL = "SELECT stockid,  
                  description,
				  units
				  FROM stockmaster 
				  WHERE stockmaster.mbflag!='B'
				  AND stockmaster.mbflag!='A'
				  AND stockmaster.mbflag!='M'
				  AND stockmaster.mbflag!='K'";
$result1 = DB_query($SQL);
if (DB_num_rows($result1) == 0) {
	echo '<p class="bad">' . _('Problem Report') . ':<br />' . _('There are no Farm Item Description in the system  defined, please use the link below to set them up') . '</p>';
	echo '<br /><a href="' . $RootPath . '/Farm_Description.php">' . _('Item Descriptions') . '</a>';
	
	include ('includes/footer.inc');
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
	$SQL2="SELECT a. description_Id,
			        b.stockid,
					a.cost,
					a.units,	
					a.description
				    FROM farmdescriptions a,stockmaster b
					ORDER BY a.description_Id";
	$myrow = DB_fetch_array($SQL2);
	$Its_A_Kitset_Assembly_Or_Dummy = false;
	$Its_A_Dummy = false;
	$Its_A_Kitset = false;
	$Its_A_Labour_Item = false;
	if ($myrow['discontinued']==1){
		$ItemStatus = '<p class="bad">' ._('Obsolete') . '</p>';
	} else {
		$ItemStatus = '';
	}
	//////////////////////////////////////////////
	echo '<table width="90%">
			<tr>
				<th colspan="3"><img src="' . $RootPath . '/css/' . $Theme . '/images/inventory.png" title="' . _('Inventory') . '" alt="" /><b title="' . $myrow['longdescription'] . '">' . ' ' . $StockID . ' - ' . $myrow['description'] . '</b> ' . $ItemStatus . '</th>
			</tr>';


	echo '<tr>
			<td style="width:40%" valign="top">
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
			echo _('Manufactured Item');
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
	if (in_array($PricesSecurity, $_SESSION['AllowedPageSecurityTokens']) OR !isset($PricesSecurity)) {} //end of if PricesSecuirty allows viewing of prices
	////////////////////////////////////////////////////////////////////////////////////////////
	echo '</table>'; 
} // end displaying item options if there is one and only one record
echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<table class="selection"><tr>';
echo '<td>' . _('Select type of service') . ':';
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
	if ($myrow1['stockid'] == $_POST['StockCat']) {
		echo '<option selected="selected" value="' . $myrow1['stockid'] . '">' . $myrow1['description'] . '</option>';
	} else {
		echo '<option value="' . $myrow1['stockid'] . '">' . $myrow1['description'] . '</option>';
	}
}
echo '</select></td>';
echo '<td>' . _('Enter partial') . '<b> ' . _('Description') . '</b>:</td><td>';
if (isset($_POST['Keywords'])) {
	echo '<input type="text" name="Keywords" value="' . $_POST['Keywords'] . '" title="' . _('Enter text that you wish to search for in the item description') . '" size="20" maxlength="25" />';
} else {
	echo '<input type="text" name="Keywords" title="' . _('Enter text that you wish to search for in the item description') . '" size="20" maxlength="25" />';
}
echo '</td></tr><tr><td></td>';
echo '<td><b>' . _('OR') . ' ' . '</b>' . _('Enter partial') . ' <b>' . _('Service Code') . '</b>:</td>';
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
			$SQL="SELECT a. description_Id,
			        b.stockid,
					a.cost,
					a.units,	
					a.description
					FROM farmdescriptions a,stockmaster b
					WHERE a.description " . LIKE . " '$SearchString'
					AND a.stockid=b.stockid ";
		} else {
			$SQL="SELECT a. description_Id,
			        b.stockid,
					a.cost,
					a.units,	
					a.description
					FROM farmdescriptions a,stockmaster b
					WHERE a.description " . LIKE . " '$SearchString'
					AND a.stockid=b.stockid 
					AND b.stockid='" . $_POST['StockCat'] . "'";
		}
	} elseif (isset($_POST['StockCode'])) {
		$_POST['StockCode'] = mb_strtoupper($_POST['StockCode']);
		if ($_POST['StockCat'] == 'All') {
			$SQL="SELECT a. description_Id,
			        b.stockid,
					a.cost,
					a.units,	
					a.description
					FROM farmdescriptions a,stockmaster b
					WHERE a.description_Id " . LIKE . " '%" . $_POST['StockCode'] . "%'
					AND a.stockid=b.stockid ";
		} else {
			$SQL="SELECT a. description_Id,
			        b.stockid,
					a.cost,
					a.units,	
					a.description
					FROM farmdescriptions a,stockmaster b
					WHERE a.description_Id " . LIKE . " '%" . $_POST['StockCode'] . "%'
					AND a.stockid=b.stockid 
					AND b.stockid='" . $_POST['StockCat'] . "'";
		}
	} elseif (!isset($_POST['StockCode']) AND !isset($_POST['Keywords'])) {
		if ($_POST['StockCat'] == 'All') {
			$SQL="SELECT a. description_Id,
			        b.stockid,
					a.cost,
					a.units,	
					a.description
					FROM farmdescriptions a,stockmaster b
					AND a.stockid=b.stockid ";
		} else {
			$SQL="SELECT a. description_Id,
			        b.stockid,
					a.cost,
					a.units,	
					a.description
					FROM farmdescriptions a,stockmaster b
					WHERE b.stockid='" . $_POST['StockCat'] . "'
					AND a.stockid=b.stockid ";
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
	echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
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
		echo '<table id="ItemSearchTable" class="selection">';
		$TableHeader = '<tr>
							<th class="ascending">' . _('DescriptionID') . '</th>
							<th class="ascending">' . _('Item Description') . '</th>
							<th>' . _('Units') . '</th>
							<th>' . _('Cost') . '</th>
						</tr>';
		echo $TableHeader;
		$j = 1;
		$k = 0; //row counter to determine background colour
		$RowIndex = 0;
		if (DB_num_rows($SearchResult) <> 0) {
			DB_data_seek($SearchResult, ($_POST['PageOffset'] - 1) * $_SESSION['DisplayRecordsMax']);
		}
		while (($myrow = DB_fetch_array($SearchResult))) {
			if ($k == 1) {
				echo '<tr class="EvenTableRows">';
				$k = 0;
			} else {
				echo '<tr class="OddTableRows">';
				$k++;
			}
			
          $id = $myrow['description_Id'];
			echo '<td>' . $myrow['description_Id'] . '</td>
				<td>' . $myrow['description'] . '</td>
				<td>' . $myrow['units'] . '</td>
				<td>' . $myrow['cost'] . '</td>';
				echo"<td> <a href ='EditFarmdescriptionItem.php?description_Id=$id'>Edit</a>";
				echo'</tr>';
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

include ('includes/footer.inc');
?>