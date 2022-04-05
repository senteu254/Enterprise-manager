<?php
/* $Id: SelectProduct.php 7096 2015-01-24 03:08:00Z turbopt $*/

$PricesSecurity = 12;//don't show pricing info unless security token 12 available to user
$SuppliersSecurity = 9; //don't show supplier purchasing info unless security token 9 available to user

include ('includes/session.inc');
$Title = _('Search Outstanding Local Purchase Order');
/* webERP manual links before header.inc */
$ViewTopic= ' Search Outstanding Local Purchase Order';
$BookMark = 'Quotation';

include ('includes/header.inc');
include ('includes/SQL_CommonFunctions.inc');

if (isset($_GET['StockID'])) {
	//The page is called with a StockID
	$_GET['StockID'] = trim(mb_strtoupper($_GET['StockID']));
	$_POST['Select'] = trim(mb_strtoupper($_GET['StockID']));
}
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/inventory.png" title="' . _('Search Outstanding Local Purchase Order') . '" alt="" />' . ' ' . _(' Search Outstanding Local Purchase Order') . '</p>';
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
	
	
	
	////////////////////////////////////////////////////////////////////////////////////////////
	echo '</table>'; 
} // end displaying item options if there is one and only one record
echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<table class="selection">
<tr>';
echo'<td>' . _('Type of Order') . ':
<select required="required" autofocus="autofocus" name="OrderType">
				<option selected="selected" value="LPO">' . _('LPO') . '</option>
				<option value="LSO">' . _('LSO') . '</option>
				</select>';
			

	if (!isset($_POST['DateFrom'])) {
	if ($_POST['OrderType'] == 'LPO') {
		$DateSQL = "SELECT min(orddate) as fromdate,
							max(orddate) as todate
						FROM purchorders";
		}else{
		$DateSQL = "SELECT min(orddate) as fromdate,
							max(orddate) as todate
						FROM lsorders";
		}
		$DateResult = DB_query($DateSQL);
		$DateRow = DB_fetch_array($DateResult);
		$DateFrom = $DateRow['fromdate'];
		$DateTo = $DateRow['todate'];
	} else {
		$DateFrom = FormatDateForSQL($_POST['DateFrom']);
		$DateTo = FormatDateForSQL($_POST['DateTo']);
	}
	echo'</td>';
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
echo '<td>' . _('LPO Between') . ':&nbsp;
			<input type="text" name="DateFrom" value="' . ConvertSQLDate($DateFrom) . '"  class="date" size="10" alt="' . $_SESSION['DefaultDateFormat'] . '"  />
		' . _('and') . ':&nbsp;
			<input type="text" name="DateTo" value="' . ConvertSQLDate($DateTo) . '"  class="date" size="10" alt="' . $_SESSION['DefaultDateFormat'] . '"  />
</td>

</tr><tr>
<td><b>' . _('OR') . ' ' . '</b>' . _('Enter') . ' <b>' . _('LPO No.') . '</b>:</td>';
echo '<td>';
if (isset($_POST['StockCode'])) {
	echo '<input type="text" name="StockCode" value="' . $_POST['StockCode'] . '" title="' . _('Enter text that you wish to search for in the item code') . '" size="35" maxlength="50" />';
} else {
	echo '<input type="text" name="StockCode" title="' . _('Enter text that you wish to search for in the item code') . '" size="35" maxlength="50" />';
}
echo '</td></tr>
<tr></tr></table><br />';
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
	if ($_POST['OrderType'] == 'LPO') {
	if ($_POST['Keywords']) {
		//insert wildcard characters in spaces
		$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
		$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';
		if ($_POST['StockCat'] == 'All') {
			 $SQL="SELECT purchorders.realorderno,
							purchorders.orderno,
							suppliers.suppname,
							purchorders.orddate,
							purchorders.deliverydate,
							purchorders.status,
							purchorders.initiator,
							purchorders.requisitionno,
							suppliers.currcode,
							suppliergrouptype.groupid,
							suppliers.suppliergroup,
							currencies.decimalplaces AS currdecimalplaces,
							SUM(purchorderdetails.unitprice*purchorderdetails.quantityord) AS ordervalue
						FROM purchorders
					    INNER JOIN purchorderdetails ON purchorders.orderno = purchorderdetails.orderno
						INNER JOIN suppliers ON purchorders.supplierno=suppliers.supplierid
						INNER JOIN currencies ON suppliers.currcode=currencies.currabrev
						INNER JOIN suppliergrouptype  ON suppliers.suppliergroup=suppliergrouptype.groupid
						WHERE orddate>='" . $DateFrom . "'
						AND orddate<='" . $DateTo . "'
						GROUP BY purchorders.orderno ASC,
							purchorders.realorderno,
							suppliers.suppname,
							purchorders.orddate,
							purchorders.status,
							purchorders.initiator,
							purchorders.requisitionno,
							purchorders.allowprint,
							suppliers.currcode,
							currencies.decimalplaces";
		} else {
			  $SQL="SELECT purchorders.realorderno,
							purchorders.orderno,
							suppliers.suppname,
							purchorders.orddate,
							purchorders.deliverydate,
							purchorders.status,
							purchorders.initiator,
							purchorders.requisitionno,
							purchorders.allowprint,
							suppliers.currcode,
							suppliergrouptype.groupid,
							suppliers.suppliergroup,
							currencies.decimalplaces AS currdecimalplaces,
							SUM(purchorderdetails.unitprice*purchorderdetails.quantityord) AS ordervalue
						FROM purchorders 
						INNER JOIN purchorderdetails ON purchorders.orderno = purchorderdetails.orderno
						INNER JOIN suppliers ON purchorders.supplierno = suppliers.supplierid
						INNER JOIN currencies ON suppliers.currcode=currencies.currabrev
						INNER JOIN suppliergrouptype  ON suppliers.suppliergroup=suppliergrouptype.groupid
						WHERE suppliergrouptype.groupid='" . $_POST['StockCat'] . "'
						AND orddate>='" . $DateFrom . "'
						AND orddate<='" . $DateTo . "'
						GROUP BY purchorders.orderno ASC,
							purchorders.realorderno,
							suppliers.suppname,
							purchorders.orddate,
							purchorders.status,
							purchorders.initiator,
							purchorders.requisitionno,
							purchorders.allowprint,
							suppliers.currcode,
							currencies.decimalplaces";
		}
	} elseif (isset($_POST['StockCode'])) {
		$_POST['StockCode'] = mb_strtoupper($_POST['StockCode']);
		if ($_POST['StockCat'] == 'All') {
			  $SQL="SELECT purchorders.realorderno,
							purchorders.orderno,
							suppliers.suppname,
							purchorders.orddate,
							purchorders.deliverydate,
							purchorders.status,
							purchorders.initiator,
							purchorders.requisitionno,
							purchorders.allowprint,
							suppliers.currcode,
							suppliergrouptype.groupid,
							suppliers.suppliergroup,
							currencies.decimalplaces AS currdecimalplaces,
							SUM(purchorderdetails.unitprice*purchorderdetails.quantityord) AS ordervalue
						FROM purchorders
					    INNER JOIN purchorderdetails ON purchorders.orderno = purchorderdetails.orderno
						INNER JOIN suppliers ON  purchorders.supplierno=suppliers.supplierid
						INNER JOIN currencies ON suppliers.currcode=currencies.currabrev
						INNER JOIN suppliergrouptype  ON suppliers.suppliergroup=suppliergrouptype.groupid
						WHERE purchorders.orderno " . LIKE . " '%" . $_POST['StockCode'] . "%'
						AND orddate>='" . $DateFrom . "'
						AND orddate<='" . $DateTo . "'
						GROUP BY purchorders.orderno ASC,
							purchorders.realorderno,
							suppliers.suppname,
							purchorders.orddate,
							purchorders.status,
							purchorders.initiator,
							purchorders.requisitionno,
							purchorders.allowprint,
							suppliers.currcode,
							currencies.decimalplaces";
		} else {
			$SQL="SELECT purchorders.realorderno,
							purchorders.orderno,
							suppliers.suppname,
							purchorders.orddate,
							purchorders.deliverydate,
							purchorders.status,
							purchorders.initiator,
							purchorders.requisitionno,
							purchorders.allowprint,
							suppliers.currcode,
							suppliergrouptype.groupid,
							suppliers.suppliergroup,
							currencies.decimalplaces AS currdecimalplaces,
							SUM(purchorderdetails.unitprice*purchorderdetails.quantityord) AS ordervalue
						FROM purchorders
					   INNER JOIN purchorderdetails ON purchorders.orderno = purchorderdetails.orderno
						INNER JOIN suppliers ON  purchorders.supplierno = suppliers.supplierid
						INNER JOIN currencies ON suppliers.currcode=currencies.currabrev 
						INNER JOIN suppliergrouptype  ON suppliers.suppliergroup=suppliergrouptype.groupid
						WHERE purchorders.orderno " . LIKE . " '%" . $_POST['StockCode'] . "%'
						AND suppliergrouptype.groupid='" . $_POST['StockCat'] . "'
						AND orddate>='" . $DateFrom . "'
						AND orddate<='" . $DateTo . "'
						GROUP BY purchorders.orderno ASC,
							purchorders.realorderno,
							suppliers.suppname,
							purchorders.orddate,
							purchorders.status,
							purchorders.initiator,
							purchorders.requisitionno,
							purchorders.allowprint,
							suppliers.currcode,
							currencies.decimalplaces";
		}
	} elseif (!isset($_POST['StockCode']) AND !isset($_POST['Keywords'])) {
		if ($_POST['StockCat'] == 'All') {
			$SQL="SELECT purchorders.realorderno,
							purchorders.orderno,
							suppliers.suppname,
							purchorders.orddate,
							purchorders.deliverydate,
							purchorders.status,
							purchorders.initiator,
							purchorders.requisitionno,
							purchorders.allowprint,
							suppliers.currcode,
							suppliergrouptype.groupid,
							suppliers.suppliergroup,
							currencies.decimalplaces AS currdecimalplaces,
							SUM(purchorderdetails.unitprice*purchorderdetails.quantityord) AS ordervalue
						FROM purchorders 
						INNER JOIN purchorderdetails ON purchorders.orderno = purchorderdetails.orderno
						INNER JOIN suppliers ON purchorders.supplierno = suppliers.supplierid
						INNER JOIN currencies ON suppliers.currcode=currencies.currabrev
						INNER JOIN suppliergrouptype  ON suppliers.suppliergroup=suppliergrouptype.groupid
						WHERE orddate>='" . $DateFrom . "'
						AND orddate<='" . $DateTo . "'
						GROUP BY purchorders.orderno ASC,
							purchorders.realorderno,
							suppliers.suppname,
							purchorders.orddate,
							purchorders.status,
							purchorders.initiator,
							purchorders.requisitionno,
							purchorders.allowprint,
							suppliers.currcode,
							currencies.decimalplaces";
		} else {
			$SQL="SELECT purchorders.realorderno,
							purchorders.orderno,
							suppliers.suppname,
							purchorders.orddate,
							purchorders.deliverydate,
							purchorders.status,
							purchorders.initiator,
							purchorders.requisitionno,
							purchorders.allowprint,
							suppliers.currcode,
							suppliergrouptype.groupid,
							suppliers.suppliergroup,
							currencies.decimalplaces AS currdecimalplaces,
							SUM(purchorderdetails.unitprice*purchorderdetails.quantityord) AS ordervalue
						FROM purchorders
					   INNER JOIN purchorderdetails ON purchorders.orderno = purchorderdetails.orderno
						INNER JOIN suppliers ON purchorders.supplierno = suppliers.supplierid
						INNER JOIN currencies ON suppliers.currcode=currencies.currabrev
						INNER JOIN suppliergrouptype  ON suppliers.suppliergroup=suppliergrouptype.groupid
						WHERE suppliergrouptype.groupid='" . $_POST['StockCat'] . "'
						AND orddate>='" . $DateFrom . "'
						AND orddate<='" . $DateTo . "'
						GROUP BY purchorders.orderno ASC,
							purchorders.realorderno,
							suppliers.suppname,
							purchorders.orddate,
							purchorders.status,
							purchorders.initiator,
							purchorders.requisitionno,
							purchorders.allowprint,
							suppliers.currcode,
							currencies.decimalplaces";
		}
	}
	}elseif ($_POST['OrderType'] == 'LSO') {
	if ($_POST['Keywords']) {
		//insert wildcard characters in spaces
		$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
		$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';
		if ($_POST['StockCat'] == 'All') {
			 $SQL="SELECT lsorders.realorderno,
							lsorders.orderno,
							suppliers.suppname,
							lsorders.orddate,
							lsorders.deliverydate,
							lsorders.status,
							lsorders.initiator,
							lsorders.requisitionno,
							lsorders.allowprint,
							suppliers.currcode,
							suppliergrouptype.groupid,
							suppliers.suppliergroup,
							currencies.decimalplaces AS currdecimalplaces,
							SUM(lsorderdetails.unitprice* lsorderdetails.quantityord) AS ordervalue
						FROM lsorders
					    INNER JOIN lsorderdetails ON lsorders.orderno = lsorderdetails.orderno
						INNER JOIN suppliers ON  lsorders.supplierno = suppliers.supplierid
						INNER JOIN currencies ON suppliers.currcode=currencies.currabrev
						INNER JOIN suppliergrouptype  ON suppliers.suppliergroup=suppliergrouptype.groupid
						WHERE orddate>='" . $DateFrom . "'
						AND orddate<='" . $DateTo . "'
						GROUP BY lsorders.orderno ASC,
							lsorders.realorderno,
							suppliers.suppname,
							lsorders.orddate,
							lsorders.status,
							lsorders.initiator,
							lsorders.requisitionno,
							lsorders.allowprint,
							suppliers.currcode,
							currencies.decimalplaces";
		} else {
			  $SQL="SELECT lsorders.realorderno,
							lsorders.orderno,
							suppliers.suppname,
							lsorders.orddate,
							lsorders.deliverydate,
							lsorders.status,
							lsorders.initiator,
							lsorders.requisitionno,
							lsorders.allowprint,
							suppliers.currcode,
							suppliergrouptype.groupid,
							suppliers.suppliergroup,
							currencies.decimalplaces AS currdecimalplaces,
							SUM(lsorderdetails.unitprice*lsorderdetails.quantityord) AS ordervalue
						FROM lsorders INNER JOIN lsorderdetails ON lsorders.orderno = lsorderdetails.orderno
						INNER JOIN suppliers ON  lsorders.supplierno = suppliers.supplierid
						INNER JOIN currencies ON suppliers.currcode=currencies.currabrev
						INNER JOIN suppliergrouptype  ON suppliers.suppliergroup=suppliergrouptype.groupid
						WHERE suppliergrouptype.groupid='" . $_POST['StockCat'] . "'
						AND orddate>='" . $DateFrom . "'
						AND orddate<='" . $DateTo . "'
						GROUP BY lsorders.orderno ASC,
							lsorders.realorderno,
							suppliers.suppname,
							lsorders.orddate,
							lsorders.status,
							lsorders.initiator,
							lsorders.requisitionno,
							lsorders.allowprint,
							suppliers.currcode,
							currencies.decimalplaces";
		}
	} elseif (isset($_POST['StockCode'])) {
		$_POST['StockCode'] = mb_strtoupper($_POST['StockCode']);
		if ($_POST['StockCat'] == 'All') {
			  $SQL="SELECT lsorders.realorderno,
							lsorders.orderno,
							suppliers.suppname,
							lsorders.orddate,
							lsorders.deliverydate,
							lsorders.status,
							lsorders.initiator,
							lsorders.requisitionno,
							lsorders.allowprint,
							suppliers.currcode,
							suppliergrouptype.groupid,
							suppliers.suppliergroup,
							currencies.decimalplaces AS currdecimalplaces,
							SUM(lsorderdetails.unitprice*lsorderdetails.quantityord) AS ordervalue
						FROM lsorders
					    INNER JOIN lsorderdetails ON lsorders.orderno = lsorderdetails.orderno
						INNER JOIN suppliers ON lsorders.supplierno = suppliers.supplierid
						INNER JOIN currencies ON suppliers.currcode=currencies.currabrev
						INNER JOIN suppliergrouptype  ON suppliers.suppliergroup=suppliergrouptype.groupid
						WHERE lsorders.orderno " . LIKE . " '%" . $_POST['StockCode'] . "%'
						AND orddate>='" . $DateFrom . "'
						AND orddate<='" . $DateTo . "'
						GROUP BY lsorders.orderno ASC,
							lsorders.realorderno,
							suppliers.suppname,
							lsorders.orddate,
							lsorders.status,
							lsorders.initiator,
							lsorders.requisitionno,
							lsorders.allowprint,
							suppliers.currcode,
							currencies.decimalplaces";
		} else {
			$SQL="SELECT lsorders.realorderno,
							lsorders.orderno,
							suppliers.suppname,
							lsorders.orddate,
							lsorders.deliverydate,
							lsorders.status,
							lsorders.initiator,
							lsorders.requisitionno,
							lsorders.allowprint,
							suppliers.currcode,
							suppliergrouptype.groupid,
							suppliers.suppliergroup,
							currencies.decimalplaces AS currdecimalplaces,
							SUM(lsorderdetails.unitprice*lsorderdetails.quantityord) AS ordervalue
						FROM lsorders
					    INNER JOIN lsorderdetails ON lsorders.orderno = lsorderdetails.orderno
						INNER JOIN suppliers ON lsorders.supplierno = suppliers.supplierid
						INNER JOIN currencies ON suppliers.currcode=currencies.currabrev
						INNER JOIN suppliergrouptype  ON suppliers.suppliergroup=suppliergrouptype.groupid
						WHERE lsorders.orderno " . LIKE . " '%" . $_POST['StockCode'] . "%'
						AND suppliergrouptype.groupid='" . $_POST['StockCat'] . "'
						AND orddate>='" . $DateFrom . "'
						AND orddate<='" . $DateTo . "'
						GROUP BY lsorders.orderno ASC,
							lsorders.realorderno,
							suppliers.suppname,
							lsorders.orddate,
							lsorders.status,
							lsorders.initiator,
							lsorders.requisitionno,
							lsorders.allowprint,
							suppliers.currcode,
							currencies.decimalplaces";
		}
	} elseif (!isset($_POST['StockCode']) AND !isset($_POST['Keywords'])) {
		if ($_POST['StockCat'] == 'All') {
			$SQL="SELECT lsorders.realorderno,
							lsorders.orderno,
							suppliers.suppname,
							lsorders.orddate,
							lsorders.deliverydate,
							lsorders.status,
							lsorders.initiator,
							lsorders.requisitionno,
							lsorders.allowprint,
							suppliers.currcode,
							suppliergrouptype.groupid,
							suppliers.suppliergroup,
							currencies.decimalplaces AS currdecimalplaces,
							SUM(lsorderdetails.unitprice*lsorderdetails.quantityord) AS ordervalue
						FROM lsorders 
						INNER JOIN lsorderdetails ON lsorders.orderno = lsorderdetails.orderno
						INNER JOIN suppliers ON lsorders.supplierno = suppliers.supplierid
						INNER JOIN currencies ON suppliers.currcode=currencies.currabrev
						INNER JOIN suppliergrouptype  ON suppliers.suppliergroup=suppliergrouptype.groupid
						WHERE orddate>='" . $DateFrom . "'
						AND orddate<='" . $DateTo . "'
						GROUP BY lsorders.orderno ASC,
							lsorders.realorderno,
							suppliers.suppname,
							lsorders.orddate,
							lsorders.status,
							lsorders.initiator,
							lsorders.requisitionno,
							lsorders.allowprint,
							suppliers.currcode,
							currencies.decimalplaces";
		} else {
			$SQL="SELECT lsorders.realorderno,
							lsorders.orderno,
							suppliers.suppname,
							lsorders.orddate,
							lsorders.deliverydate,
							lsorders.status,
							lsorders.initiator,
							lsorders.requisitionno,
							lsorders.allowprint,
							suppliers.currcode,
							suppliergrouptype.groupid,
							suppliers.suppliergroup,
							currencies.decimalplaces AS currdecimalplaces,
							SUM(lsorderdetails.unitprice*lsorderdetails.quantityord) AS ordervalue
						FROM lsorders 
						INNER JOIN lsorderdetails ON lsorders.orderno = lsorderdetails.orderno
						INNER JOIN suppliers ON  lsorders.supplierno = suppliers.supplierid
						INNER JOIN currencies ON suppliers.currcode=currencies.currabrev
						INNER JOIN suppliergrouptype  ON suppliers.suppliergroup=suppliergrouptype.groupid
						WHERE suppliergrouptype.groupid='" . $_POST['StockCat'] . "'
						AND orddate>='" . $DateFrom . "'
						AND orddate<='" . $DateTo . "'
						GROUP BY lsorders.orderno ASC,
							lsorders.realorderno,
							suppliers.suppname,
							lsorders.orddate,
							lsorders.status,
							lsorders.initiator,
							lsorders.requisitionno,
							lsorders.allowprint,
							suppliers.currcode,
							currencies.decimalplaces";
		}
	}
	
	}
	$ErrMsg = _('No stock items were returned by the SQL because');
	$DbgMsg = _('The SQL that returned an error was');
	$SearchResult = DB_query($SQL, $ErrMsg, $DbgMsg);
	if (DB_num_rows($SearchResult) == 0) {
		prnMsg(_('No transaction returned by this search please re-enter alternative criteria to try again'), 'info');
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
						<th class="ascending">' . _('Order#') . '</th>
						<th class="ascending">' . _('Order Date') . '</th>
						<th class="ascending">' . _('Delivery Date') . '</th>
			            <th class="ascending">' . _('Supplier Name') . '</th>
						 <th class="ascending">' . _('Currency') . '</th>
						<th class="ascending">' . _('Order Total') . '</th>
						<th class="ascending">' . _('Status') . '</th>
						</tr>';
		echo $TableHeader;
		$j = 1;
		$k = 0; //row counter to determine background colour
		$RowIndex = 0;
		if (DB_num_rows($SearchResult) <> 0) {
			DB_data_seek($SearchResult, ($_POST['PageOffset'] - 1) * $_SESSION['DisplayRecordsMax']);
		}
		while (($myrow = DB_fetch_array($SearchResult))) {
		   //$myrow['orddate'];
			//$FormatedDeliveryDate = ConvertSQLDate($myrow['orddate']);
			$FormatedOrderValue = locale_number_format($myrow['ordervalue'], $myrow['currdecimalplaces']);
			if ($k == 1) {
				echo '<tr class="EvenTableRows">';
				$k = 0;
			} else {
				echo '<tr class="OddTableRows">';
				$k++;
			}
			
		  echo '<td>' . $myrow['orderno'] . '</td>
				<td>' . $myrow['orddate']. '</td>
				<td>' . $myrow['deliverydate'] . '</td>
				<td>' . $myrow['suppname'] . '</td>
				<td>' . $myrow['currcode'] . '</td>
				<td>' .$FormatedOrderValue . '</td>
				<td>' .$myrow['status'] . '</td>';
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
		if ($_POST['OrderType'] == 'LPO') {
		if ($_POST['StockCat'] == 'All') {
		$Supp="SELECT c.groupname,a.orddate, COUNT(a.supplierno) AS total
		      FROM  purchorders a
			  INNER JOIN suppliers b ON a.supplierno=b.supplierid
			  INNER JOIN suppliergrouptype c ON b.suppliergroup=c.groupid
			  WHERE a.orddate>='" . $DateFrom . "'
			  AND a.orddate<='" . $DateTo . "'	  
			  GROUP BY c.groupid";
			}else{
		$Supp="SELECT c.groupname,a.orddate, COUNT(a.supplierno) AS total
		      FROM  purchorders a
			  INNER JOIN suppliers b ON a.supplierno=b.supplierid
			  INNER JOIN suppliergrouptype c ON b.suppliergroup=c.groupid
	       	  WHERE c.groupid='" . $_POST['StockCat'] . "'	
			  AND a.orddate>='" . $DateFrom . "'
			  AND a.orddate<='" . $DateTo . "'	  
			  GROUP BY c.groupid";
		}
			 }elseif ($_POST['OrderType'] == 'LSO') {
		if ($_POST['StockCat'] == 'All') {
		 $Supp="SELECT c.groupname,a.orddate, COUNT(a.supplierno) AS total
		      FROM  lsorders a
			  INNER JOIN suppliers b ON a.supplierno=b.supplierid
			  INNER JOIN suppliergrouptype c ON b.suppliergroup=c.groupid
			  WHERE a.orddate>='" . $DateFrom . "'
			  AND a.orddate<='" . $DateTo . "'	
			  GROUP BY c.groupid";
			 }else {
			 $Supp="SELECT c.groupname,a.orddate, COUNT(a.supplierno) AS total
		      FROM  lsorders a
			  INNER JOIN suppliers b ON a.supplierno=b.supplierid
			  INNER JOIN suppliergrouptype c ON b.suppliergroup=c.groupid
			  WHERE c.groupid='" . $_POST['StockCat'] . "'		
			  WHERE a.orddate>='" . $DateFrom . "'
			  AND a.orddate<='" . $DateTo . "'			  
			  GROUP BY c.groupid";
			 }
			 }
			  $suppgroup = DB_query($Supp);
		echo '<table>';
		echo'<th>Supplier Group</th><td></td>
			 <th>Number Of LPO/LSO</th>';
		while (($myrow3 = DB_fetch_array($suppgroup))) {
				
		  echo' <tr>	';
		  if($_POST['StockCat']=="All"){		       
		          echo'<td>' . $myrow3['groupname'] . '</td><td></td> <td>'.$myrow3['total'] .'</td>';
				  }else{
				  echo'<td>' . $myrow3['groupname'] . '</td><td></td> <td>'.$myrow3['total'] .'</td>';
				  }
		  echo'</tr>';
		}
		echo '</table>';
		echo '</table>
              </div>
              </form>
              <br />';
	}
	echo '<a href="PDFlpogrouptype.php?stockcat='.$_POST['StockCat'].'&type='.$_POST['OrderType'].'&datef='.$DateFrom.'&datet='.$DateTo.'">Print PDF</a>';
}


include ('includes/footer.inc');
?>