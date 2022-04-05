<?php
/* $Id: SelectProduct.php 7096 2015-01-24 03:08:00Z turbopt $*/

$PricesSecurity = 12;//don't show pricing info unless security token 12 available to user
$SuppliersSecurity = 9; //don't show supplier purchasing info unless security token 9 available to user

include ('includes/session.inc');
$Title = _('SuppliersPayments');
/* webERP manual links before header.inc */
$ViewTopic= 'SuppliersPayments';
$BookMark = 'Productions';

include ('includes/header.inc');
include ('includes/SQL_CommonFunctions.inc');

if (isset($_GET['StockID'])) {
	//The page is called with a StockID
	$_GET['StockID'] = trim(mb_strtoupper($_GET['StockID']));
	$_POST['Select'] = trim(mb_strtoupper($_GET['StockID']));
}
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/inventory.png" title="' . _('Suppliers Payments History') . '" alt="" />' . ' ' . _('Suppliers Payments History and Balance') . '</p>';
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
$SQL = "SELECT supplierid,  
                  suppname,
				  taxref
				  FROM  suppliers";
$result1 = DB_query($SQL);
if (DB_num_rows($result1) == 0) {
	echo '<p class="bad">' . _('Problem Report') . ':<br />' . _('There are no Farm Item Description in the system  defined, please use the link below to set them up') . '</p>';
	echo '<br /><a href="' . $RootPath . '/Farm_Description.php">' . _('Item Descriptions') . '</a>';
	
	include ('includes/footer.inc');
	exit;
}
// end of showing search facilities
/* displays item options if there is one and only one selected */

echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<table class="selection"><tr>';
echo '<td>' . _('Select Supplier Name') . ':';
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
	if ($myrow1['supplierid'] == $_POST['StockCat']) {
		echo '<option selected="selected" value="' . $myrow1['supplierid'] . '">' . $myrow1['suppname'] . '</option>';
	} else {
		echo '<option value="' . $myrow1['supplierid'] . '">' . $myrow1['suppname'] . '</option>';
	}
}
echo '</select></td>';
echo '<td>' . _('Enter partial') . '<b> ' . _('Supplier Name') . '</b>:</td><td>';
if (isset($_POST['Keywords'])) {
	echo '<input type="text" name="Keywords" value="' . $_POST['Keywords'] . '" title="' . _('Enter text that you wish to search for in the item description') . '" size="20" maxlength="25" />';
} else {
	echo '<input type="text" name="Keywords" title="' . _('Enter text that you wish to search for in the item description') . '" size="20" maxlength="25" />';
}
echo '</td></tr><tr><td></td>';
echo '<td><b>' . _('OR') . ' ' . '</b>' . _('Enter partial') . ' <b>' . _('Invoice No.') . '</b>:</td>';
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
			$SQL="SELECT * ,SUM((a.ovamount + a.ovgst)) AS totalamount,
							  a.alloc AS allocated,
							  a.inputdate,
							  a.transtext,
							  a.suppreference AS invoice,
							  c.suppname,
							  c.supplierid
						 FROM supptrans a
						 INNER JOIN suppliers c ON a.supplierno=c.supplierid
						 WHERE c.suppname " . LIKE . " '$SearchString'
						 GROUP BY a.transtext";
		} else {
			$SQL="SELECT * ,SUM((a.ovamount + a.ovgst)) AS totalamount,
							  a.alloc AS allocated,
							  a.inputdate,
							  a.transtext,
							  a.suppreference AS invoice,
							  c.suppname,
							  c.supplierid
						 FROM supptrans a
						 INNER JOIN suppliers c ON a.supplierno=c.supplierid
						 WHERE c.suppname " . LIKE . " '$SearchString'
					     AND c.supplierid='". $_POST['StockCat'] ."'
						 GROUP BY a.transtext";
		}
	} elseif (isset($_POST['StockCode'])) {
		$_POST['StockCode'] = mb_strtoupper($_POST['StockCode']);
		if ($_POST['StockCat'] == 'All') {
			$SQL="SELECT * ,SUM((a.ovamount + a.ovgst)) AS totalamount,
							  a.alloc AS allocated,
							  a.inputdate,
							  a.transtext,
							  a.suppreference AS invoice,
							  c.suppname,
							  c.supplierid
						FROM supptrans a
						INNER JOIN suppliers c ON a.supplierno=c.supplierid
					    WHERE a.suppreference " . LIKE . " '%" . $_POST['StockCode'] . "%'
					    GROUP BY a.transtext";
		} else {
				$SQL="SELECT * ,SUM((a.ovamount + a.ovgst)) AS totalamount,
							  a.alloc AS allocated,
							  a.inputdate,
							  a.transtext,
							  a.suppreference AS invoice,
							  c.suppname,
							  c.supplierid
						 FROM supptrans a
					     INNER JOIN suppliers c ON a.supplierno=c.supplierid
					     WHERE a.suppreference" . LIKE . " '%" . $_POST['StockCode'] . "%'
					     AND c.supplierid='". $_POST['StockCat'] ."'
						  GROUP BY a.transtext";
		}
	} elseif (!isset($_POST['StockCode']) AND !isset($_POST['Keywords'])) {
		if ($_POST['StockCat'] == 'All') {
			$SQL="SELECT * ,SUM((a.ovamount + a.ovgst)) AS totalamount,
							  a.alloc AS allocated,
							  a.inputdate,
							  a.transtext,
							  a.suppreference AS invoice,
							  c.suppname,
							  c.supplierid
						FROM supptrans a
						INNER JOIN suppliers c ON a.supplierno=c.supplierid
					     GROUP BY a.transtext";
		} else {
			$SQL="SELECT * ,SUM((a.ovamount + a.ovgst)) AS totalamount,
							  a.alloc AS allocated,
							  a.inputdate,
							  a.transtext,
							  a.suppreference AS invoice,
							  c.suppname,
							  c.supplierid
						 FROM supptrans a
						 INNER JOIN suppliers c ON a.supplierno=c.supplierid
					     WHERE c.supplierid='". $_POST['StockCat'] ."'
					      GROUP BY a.transtext";
		}
	}
	$ErrMsg = _('No stock items were returned by the SQL because');
	$DbgMsg = _('The SQL that returned an error was');
	$SearchResult = DB_query($SQL, $ErrMsg, $DbgMsg);
	if (DB_num_rows($SearchResult) == 0) {
		prnMsg(_('There is No payments which have been made in the system.'), 'info');
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
		                    <th>' . _('Sr.No.') . '</th>
							<th class="ascending">' . _('Order/Service #.') . '</th>
		                    <th class="ascending">' . _('Invoice No.') . '</th>
							 <th class="ascending">' . _('RV No.') . '</th>
							 <th class="ascending">' . _('Transaction Date.') . '</th>
							 <th class="ascending">' . _('Supplier Name') . '</th>
							 <th class="ascending">' ._('Total Amount').'<br/>'._(' of invoice'). '</th>
							 <th class="ascending">' . _('Amount') . '<br />' . _('Payed') . '</th>
							<th class="ascending">' ._('Balance'). '</th>
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
			
         $i++;
			$Amountp=($myrow['allocated']);
		  echo '<td style=font-size:10pt>' . $i . '</td> 
		        <td>' . $myrow['OrderNo'] . '</td>
		        <td>' . $myrow['invoice'] . '</td>
				<td>' . $myrow['transtext'] . '</td>
				<td>' .$myrow['inputdate'] . '</td>
				<td>' . $myrow['suppname'] . '</td>
				<td class="number">' . locale_number_format($myrow['totalamount'],2) . '</td>
				<td class="number">' . locale_number_format($Amountp,2) . '</td>';				
				echo'<td class="number">' .locale_number_format($myrow['totalamount']-$myrow['allocated'],2). '</td>';
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
	echo '<a href="PDFsupplierspaymentsbalances.php?stockcat='.$_POST['StockCat'].'">Print PDF</a>';
}
/* end display list if there is more than one record */

include ('includes/footer.inc');
?>