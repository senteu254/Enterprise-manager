<?php
/* $Id: SelectSupplier.php 6941 2014-10-26 23:18:08Z daintree $*/

include ('includes/session.inc');
$Title = _('Search Order');

/* webERP manual links before header.inc */
$ViewTopic= 'Order';
$BookMark = 'SelectOrder';

include ('includes/header.inc');
include ('includes/SQL_CommonFunctions.inc');

if (isset($_GET['SupplierID'])) {
	$_SESSION['SupplierID']=$_GET['SupplierID'];
}
// only get geocode information if integration is on, and supplier has been selected

if (!isset($_POST['PageOffset'])) {
	$_POST['PageOffset'] = 1;
} else {
	if ($_POST['PageOffset'] == 0) {
		$_POST['PageOffset'] = 1;
	}
}
if (isset($_POST['Select'])) { /*User has hit the button selecting a supplier */
	$_SESSION['SupplierID'] = $_POST['Select'];
	unset($_POST['Select']);
	unset($_POST['Keywords']);
	unset($_POST['SupplierCode']);
	unset($_POST['Search']);
	unset($_POST['Go']);
	unset($_POST['Next']);
	unset($_POST['Previous']);
}
if (isset($_POST['Search'])
	OR isset($_POST['Go'])
	OR isset($_POST['Next'])
	OR isset($_POST['Previous'])) {

	if (mb_strlen($_POST['Keywords']) > 0 AND mb_strlen($_POST['SupplierCode']) > 0) {
		prnMsg( _('Supplier name keywords have been used in preference to the Supplier code extract entered'), 'info' );
	}
	if ($_POST['Keywords'] == '' AND $_POST['SupplierCode'] == '') {
	$SQL = "SELECT grnno,
					purchorderdetails.orderno,
					purchorderdetails.quantityord,
					grns.supplierid,
					suppliers.suppname,
					grns.itemcode,
					grns.itemdescription,
					purchorderdetails.quantityrecd ,
					purchorderdetails.qtyinvoiced ,
					quantityinv,
					grns.stdcostunit,
					actprice,
					unitprice,
					suppliers.currcode,
					currencies.rate,
					currencies.decimalplaces as currdecimalplaces,
					stockmaster.decimalplaces as itemdecimalplaces
				FROM grns INNER JOIN purchorderdetails
				ON grns.podetailitem = purchorderdetails.podetailitem
				INNER JOIN suppliers
				ON grns.supplierid=suppliers.supplierid
				INNER JOIN currencies
				ON suppliers.currcode=currencies.currabrev
				LEFT JOIN stockmaster
				ON grns.itemcode=stockmaster.stockid
				GROUP BY purchorderdetails.orderno
				ORDER BY supplierid,grnno";	
	
		/*$SQL = "SELECT supplierid,
					suppname,
					currcode,
					address1,
					address2,
					address3,
					address4,
					telephone,
					email,
					suppliergroup,
					url
				FROM suppliers
				ORDER BY suppname";*/
	} else {
		if (mb_strlen($_POST['Keywords']) > 0) {
			$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
			//insert wildcard characters in spaces
			$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';
			$SQL = "SELECT grnno,
					purchorderdetails.orderno,
					purchorderdetails.quantityord,
					grns.supplierid,
					suppliers.suppname,
					grns.itemcode,
					grns.itemdescription,
					purchorderdetails.quantityrecd ,
					purchorderdetails.qtyinvoiced ,
					quantityinv,
					grns.stdcostunit,
					actprice,
					unitprice,
					suppliers.currcode,
					currencies.rate,
					currencies.decimalplaces as currdecimalplaces,
					stockmaster.decimalplaces as itemdecimalplaces
				FROM grns INNER JOIN purchorderdetails
				ON grns.podetailitem = purchorderdetails.podetailitem
				INNER JOIN suppliers
				ON grns.supplierid=suppliers.supplierid
				INNER JOIN currencies
				ON suppliers.currcode=currencies.currabrev
				LEFT JOIN stockmaster
				ON grns.itemcode=stockmaster.stockid
				WHERE suppliers.suppname " . LIKE . " '" . $SearchString . "'
				ORDER BY suppliers.suppname";
			
			/*$SQL = "SELECT supplierid,
							suppname,
							currcode,
							address1,
							address2,
							address3,
							address4,
							telephone,
							email,
							suppliergroup,
							url
						FROM suppliers
						WHERE suppname " . LIKE . " '" . $SearchString . "'
						ORDER BY suppname";*/
		} elseif ($_POST['SupplierCode'] > 0) {
			$_POST['SupplierCode'] = $_POST['SupplierCode'];
			$SQL = "SELECT grnno,
					purchorderdetails.orderno,
					purchorderdetails.quantityord,
					grns.supplierid,
					suppliers.suppname,
					suppliers.supplierid,
					grns.itemcode,
					grns.itemdescription,
					purchorderdetails.quantityrecd ,
					purchorderdetails.qtyinvoiced ,
					quantityinv,
					grns.stdcostunit,
					actprice,
					unitprice,
					suppliers.currcode,
					currencies.rate,
					currencies.decimalplaces as currdecimalplaces,
					stockmaster.decimalplaces as itemdecimalplaces
				FROM grns INNER JOIN purchorderdetails
				ON grns.podetailitem = purchorderdetails.podetailitem
				INNER JOIN suppliers
				ON grns.supplierid=suppliers.supplierid
				INNER JOIN currencies
				ON suppliers.currcode=currencies.currabrev
				LEFT JOIN stockmaster
				ON grns.itemcode=stockmaster.stockid
				WHERE purchorderdetails.orderno " . LIKE . " '%" . $_POST['SupplierCode'] . "%'
				ORDER BY suppliers.supplierid";
			
			/*$SQL = "SELECT supplierid,
							suppname,
							currcode,
							address1,
							address2,
							address3,
							address4,
							telephone,
							email,
							suppliergroup,
							url
						FROM suppliers
						WHERE supplierid " . LIKE . " '%" . $_POST['SupplierCode'] . "%'
						ORDER BY supplierid";*/
		}
	} //one of keywords or SupplierCode was more than a zero length string
	$result = DB_query($SQL);
	if (DB_num_rows($result) == 1) {
		$myrow = DB_fetch_row($result);
		$SingleSupplierReturned = $myrow[0];
	}
	$Its_A_Kitset_Assembly_Or_Dummy = false;
	$Its_A_Dummy = false;
	$Its_A_Kitset = false;
	$Its_A_Labour_Item = false;
	if (isset($SingleSupplierReturned)) { /*there was only one supplier returned */
 	   $_SESSION['SupplierID'] = $SingleSupplierReturned;
	   unset($_POST['Keywords']);
	   unset($_POST['SupplierCode']);
	   unset($_POST['Search']);
        } else {
               unset($_SESSION['SupplierID']);
        }
} //end of if search

echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/magnifier.png" title="' . _('Search') . '" alt="" />' . ' ' . _('Orders Received') . '</p>
	<table cellpadding="3" class="selection">
	<tr>
		<td>' . _('Enter a partial Supplier Name') . ':</td>
		<td>';
if (isset($_POST['Keywords'])) {
	echo '<input type="text" name="Keywords" value="' . $_POST['Keywords'] . '" size="20" maxlength="25" />';
} else {
	echo '<input type="text" name="Keywords" size="20" maxlength="25" />';
}
echo '</td>
		<td><b>' . _('OR') . '</b></td>
		<td>' . _('Enter a Order No.') . ':</td>
		<td>';
if (isset($_POST['SupplierCode'])) {
	echo '<input type="text" autofocus="autofocus" name="SupplierCode" value="' . $_POST['SupplierCode'] . '" size="15" maxlength="18" />';
} else {
	echo '<input type="text" autofocus="autofocus" name="SupplierCode" size="15" maxlength="18" />';
}
echo '</td></tr>
		</table>
		<br /><div class="centre"><input type="submit" name="Search" value="' . _('Search Now') . '" /></div>';
//if (isset($result) AND !isset($SingleSupplierReturned)) {
if (isset($_POST['Search'])) {
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
	echo '<input type="hidden" name="Search" value="' . _('Search Now') . '" />';
	echo '<br />
		<br />
		<br />
		<table cellpadding="2">';
	echo '<tr>
						<th>' . _('Code') . '</th>
						<th>' . _('Supplier Name') . '</th>
						<th>' . _('PO#') . '</th>
						<th>' . _('Item Code') . '</th>
						<th>' . _('Quantity ordered') . '</th>
						<th>' . _('Qty Received') . '</th>
						<th>' . _('Qty Invoiced') . '</th>
						<th>' . _('Qty Pending') . '</th>
						<th>' . _('Unit Price') . '</th>
						<th>' .'' . '</th>
						<th>' . _('Value of Goods Pending') . '</th>
						<th>' . '' . '</th>
						<th>' . _('Value of Goods received') . '</th>
						<th>' . '' . '</th>
					</tr>';
	$k = 0; //row counter to determine background colour
	$RowIndex = 0;
	if (DB_num_rows($result) <> 0) {
		DB_data_seek($result, ($_POST['PageOffset'] - 1) * $_SESSION['DisplayRecordsMax']);
	}
	while (($myrow = DB_fetch_array($result)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {
		if ($k == 1) {
			echo '<tr class="EvenTableRows">';
			$k = 0;
		} else {
			echo '<tr class="OddTableRows">';
			$k = 1;
		}
		$QtyPending = $myrow['quantityord'] - $myrow['quantityrecd'];
		$TotalHomeCurrency = $TotalHomeCurrency + ($QtyPending * $myrow['stdcostunit']);
		echo '<td>'.$myrow['supplierid'].'</td>
				<td>' . $myrow['suppname'] . '</td>
				<td>' . $myrow['orderno'] . '</td>
				<td>' . $myrow['itemcode'] . '</td>
				<td>' . $myrow['quantityord'] . '</td>
				<td>' . $myrow['quantityrecd'] . '</td>
				<td>' . $myrow['qtyinvoiced'] . '</td>
				<td>' . $QtyPending . '</td>
				<td>' . locale_number_format($myrow['unitprice'],$myrow['decimalplaces']) . '</td>
				<td>' . $myrow['currcode']. '</td>
				<td>'.locale_number_format(($QtyPending * $myrow['unitprice']),$myrow['decimalplaces']).'</td>
				<td>' . $myrow['currcode']. '</td>
				<td>'.locale_number_format(($myrow['quantityrecd'])*$myrow['unitprice'],$_SESSION['CompanyRecord']['decimalplaces']).'</td>
			</tr>';
		$RowIndex = $RowIndex + 1;
		//end of page full new headings if
	}
	//end of while loop
	echo '</table>';
	$all="SELECT COUNT(supplierid) AS allsuppliers
		      FROM suppliers";
			$total= DB_query($all);
	echo '<table>';
		echo '</table>';
	$Sup="SELECT c.groupname, COUNT(b.supplierid) AS totalsuppliers
		      FROM suppliers b 
			  INNER JOIN suppliergrouptype c ON b.suppliergroup=c.groupid	  
			  GROUP BY c.groupid";
			$suppg= DB_query($Sup);
	/*echo '<table>';
		echo'<th>Supplier Group</th><td></td>
			 <th>Number Of Suppliers</th>';
		while (($myrow3 = DB_fetch_array($suppg))) {
				
		  echo' <tr>	';
				  echo'<td>' . $myrow3['groupname'] . '</td><td></td> <td>'.$myrow3['totalsuppliers'] .'</td>';				 
		  echo'</tr>';
		}
		echo '</table>';*/
	//echo '<a href="PDFSuppliersList.php">Print PDF</a>';
}
//end if results to show
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
echo '</div>
      </form>';
// Only display the geocode map if the integration is turned on, and there is a latitude/longitude to display

include ('includes/footer.inc');
?>
