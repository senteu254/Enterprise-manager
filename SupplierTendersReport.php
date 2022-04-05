<?php
/* $Id: SelectProduct.php 7096 2015-01-24 03:08:00Z turbopt $*/

$PricesSecurity = 12;//don't show pricing info unless security token 12 available to user
$SuppliersSecurity = 9; //don't show supplier purchasing info unless security token 9 available to user

include ('includes/session.inc');
$Title = _('Search Outstanding Quotations');
/* webERP manual links before header.inc */
$ViewTopic= ' Search Outstanding Quotations';
$BookMark = 'Quotation';

include ('includes/header.inc');
include ('includes/SQL_CommonFunctions.inc');

if (isset($_GET['StockID'])) {
	//The page is called with a StockID
	$_GET['StockID'] = trim(mb_strtoupper($_GET['StockID']));
	$_POST['Select'] = trim(mb_strtoupper($_GET['StockID']));
}
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/inventory.png" title="' . _('Search Outstanding Quotations') . '" alt="" />' . ' ' . _(' Search Outstanding Quotations') . '</p>';
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
$SQL = "SELECT groupid,  
                  groupname
				  FROM suppliergrouptype
				  ORDER BY groupid";
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
	
	
} // end displaying item options if there is one and only one record
echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<table class="selection"><tr>';
//////////////////////////////////////////////
	if (!isset($_POST['DateFrom'])) {
		$DateSQL = "SELECT min(requiredbydate ) as fromdate,
							max(requiredbydate) as todate
						FROM tenders";
		$DateResult = DB_query($DateSQL);
		$DateRow = DB_fetch_array($DateResult);
		$DateFrom = $DateRow['fromdate'];
		$DateTo = $DateRow['todate'];
	} else {
		$DateFrom = FormatDateForSQL($_POST['DateFrom']);
		$DateTo = FormatDateForSQL($_POST['DateTo']);
	}
	////////////////////////////////////////////
echo '<td>' . _('Select type of Supplier Group') . ':';
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
	if ($myrow1['groupid'] == $_POST['StockCat']) {
		echo '<option selected="selected" value="' . $myrow1['groupid'] . '">' . $myrow1['groupname'] . '</option>';
	} else {
		echo '<option value="' . $myrow1['groupid'] . '">' . $myrow1['groupname'] . '</option>';
	}
}
echo '</select></td>';
echo '<td>' . _('Enter partial') . '<b> ' . _('Supplier') . '</b>:</td><td>';
if (isset($_POST['Keywords'])) {
	echo '<input type="text" name="Keywords" value="' . $_POST['Keywords'] . '" title="' . _('Enter text that you wish to search for Supplier') . '" size="30" maxlength="40" />';
} else {
	echo '<input type="text" name="Keywords" title="' . _('Enter text that you wish to search for in the Supplier') . '" size="30" maxlength="40" />';
}
echo '</td>';
echo '<td>' . _('Quotations Between') . ':&nbsp;
			<input type="text" name="DateFrom" value="' . ConvertSQLDate($DateFrom) . '"  class="date" size="10" alt="' . $_SESSION['DefaultDateFormat'] . '"  />
		' . _('and') . ':&nbsp;
			<input type="text" name="DateTo" value="' . ConvertSQLDate($DateTo) . '"  class="date" size="10" alt="' . $_SESSION['DefaultDateFormat'] . '"  />
</td>

</tr><tr><td></td>';
echo '<td><b>' . _('OR') . ' ' . '</b>' . _('Enter') . ' <b>' . _('Quotation No.') . '</b>:</td>';
echo '<td>';
if (isset($_POST['StockCode'])) {
	echo '<input type="text" name="StockCode" value="' . $_POST['StockCode'] . '" title="' . _('Enter text that you wish to search for in the item code') . '" size="35" maxlength="50" />';
} else {
	echo '<input type="text" name="StockCode" title="' . _('Enter text that you wish to search for in the Quotation') . '" size="35" maxlength="50" />';
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
			prnMsg (_('supplier keywords have been used in preference to the Quotation extract entered'), 'info');
	}
	if ($_POST['Keywords']) {
		//insert wildcard characters in spaces
		$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
		$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';
		if ($_POST['StockCat'] == 'All') {
			 $SQL="SELECT * FROM tenders a
					INNER JOIN locationusers b ON a.location= b.loccode
					INNER JOIN tenderitems c ON a.tenderid =c.tenderid	
					INNER JOIN tendersuppliers d ON c.tenderid=d.tenderid
					INNER JOIN suppliers e ON d.supplierid=e.supplierid
					WHERE e.suppname " . LIKE . " '$SearchString'
					GROUP BY a.tenderid";
		} else {
			  $SQL="SELECT * FROM tenders a
					INNER JOIN locationusers b ON a.location= b.loccode
					INNER JOIN tenderitems c ON a.tenderid =c.tenderid	
					INNER JOIN tendersuppliers d ON c.tenderid=d.tenderid
					INNER JOIN suppliers e ON d.supplierid=e.supplierid
					INNER JOIN suppliergrouptype f ON e.suppliergroup=f.groupid
					WHERE e.suppname " . LIKE . " '$SearchString'
					AND a.requiredbydate>='" . $DateFrom . "'
					AND a.requiredbydate<='" . $DateTo . "'
					AND f.groupid='" . $_POST['StockCat'] . "'
					GROUP BY a.tenderid";
		}
	} elseif (isset($_POST['StockCode'])) {
		$_POST['StockCode'] = mb_strtoupper($_POST['StockCode']);
		if ($_POST['StockCat'] == 'All') {
			  $SQL="SELECT * FROM tenders a
					INNER JOIN locationusers b ON a.location= b.loccode
					INNER JOIN tenderitems c ON a.tenderid =c.tenderid	
					INNER JOIN tendersuppliers d ON c.tenderid=d.tenderid
					INNER JOIN suppliers e ON d.supplierid=e.supplierid
					INNER JOIN suppliergrouptype f ON e.suppliergroup=f.groupid
					WHERE a.quotation " . LIKE . " '%" . $_POST['StockCode'] . "%'
					AND a.requiredbydate>='" . $DateFrom . "'
					AND a.requiredbydate<='" . $DateTo . "'
					GROUP BY a.tenderid";
		} else {
			$SQL="SELECT * FROM tenders a
					INNER JOIN locationusers b ON a.location= b.loccode
					INNER JOIN tenderitems c ON a.tenderid =c.tenderid	
					INNER JOIN tendersuppliers d ON c.tenderid=d.tenderid
					INNER JOIN suppliers e ON d.supplierid=e.supplierid
					INNER JOIN suppliergrouptype f ON e.suppliergroup=f.groupid
					WHERE a.quotation " . LIKE . " '%" . $_POST['StockCode'] . "%'
					AND f.groupid='" . $_POST['StockCat'] . "'
					AND a.requiredbydate>='" . $DateFrom . "'
					AND a.requiredbydate<='" . $DateTo . "'
					GROUP BY a.tenderid";
		}
	} elseif (!isset($_POST['StockCode']) AND !isset($_POST['Keywords'])) {
		if ($_POST['StockCat'] == 'All') {
			$SQL="SELECT * FROM tenders a
					INNER JOIN locationusers b ON a.location= b.loccode
					INNER JOIN tenderitems c ON a.tenderid =c.tenderid	
					INNER JOIN tendersuppliers d ON c.tenderid=d.tenderid
					INNER JOIN suppliers e ON d.supplierid=e.supplierid
					INNER JOIN suppliergrouptype f ON e.suppliergroup=f.groupid
					AND a.requiredbydate>='" . $DateFrom . "'
					AND a.requiredbydate<='" . $DateTo . "'
					GROUP BY a.tenderid";
		} else {
			$SQL="SELECT * FROM tenders a
					INNER JOIN locationusers b ON a.location= b.loccode
					INNER JOIN tenderitems c ON a.tenderid =c.tenderid	
					INNER JOIN tendersuppliers d ON c.tenderid=d.tenderid
					INNER JOIN suppliers e ON d.supplierid=e.supplierid
					INNER JOIN suppliergrouptype f ON e.suppliergroup=f.groupid
					WHERE f.groupid='" . $_POST['StockCat'] . "'
					AND a.requiredbydate>='" . $DateFrom . "'
					AND a.requiredbydate<='" . $DateTo . "'
					GROUP BY a.tenderid";
		}
	}
	$ErrMsg = _('No stock items were returned by the SQL because');
	$DbgMsg = _('The SQL that returned an error was');
	$SearchResult = DB_query($SQL, $ErrMsg, $DbgMsg);
	if (DB_num_rows($SearchResult) == 0) {
		prnMsg(_('No Quotation were returned by this search please re-enter alternative criteria to try again'), 'info');
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
						<th class="ascending">' . _('Tender ID') . '</th>
						<th class="ascending">' . _('Quotation #') . '</th>
						<th class="ascending">' . _('Date Requred') . '</th>
			            <th class="ascending">' . _('View more') . '</th>
						<th>' . _('Print Quotation') . '</th>
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
			$Viewmore  = $RootPath . '/Suppliersquatationdetails.php?TenderID=' . $myrow['tenderid'];
		 $Suppliers  = $RootPath . '/Supplierdetailsprint.php?TenderID=' . $myrow['tenderid'];
			echo '<td>' . $myrow['tenderid'] . '</td>
				<td>' . $myrow['quotation'] . '</td>
				<td>' . $myrow['requiredbydate'] . '</td><td>
				<a href="' . $Viewmore . '">' . _('View more') . '</a></td>';
		     //echo'<td><a href="' . $Printquotation . '">' . _('Print Quotation') . '</a></td>
			  echo'<td><a href="' . $Suppliers . '">' . _('View Suppliers details') . '</a></td>';
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
	echo '<a href="PDFquotationgrouptype.php?stockcat='.$_POST['StockCat'].'">Print PDF</a>';

}
/* end display list if there is more than one record */

include ('includes/footer.inc');
?>