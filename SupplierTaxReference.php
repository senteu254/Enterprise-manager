   <script language="javascript">
  function download()
{
	window.location='CustomerTransactions.xls';
}  
  
  </script> 
<?php
	
//$supplierid = $_SESSION['supplierid'];
/* $Id: SelectProduct.php 7096 2015-01-24 03:08:00Z turbopt $*/
$PricesSecurity = 12;//don't show pricing info unless security token 12 available to user
$SuppliersSecurity = 9; //don't show supplier purchasing info unless security token 9 available to user

include ('includes/session.inc');
$Title = _('Supplier Transaction Details');
/* webERP manual links before header.inc */
$ViewTopic= 'Customer Tax Info';
$BookMark = 'Customer Tax Info';

include ('includes/header.inc');
include ('includes/SQL_CommonFunctions.inc');

/*
if(!isset($_GET['SupplierID']) AND !isset($_SESSION['SupplierID'])) {
	echo '<br />' . _('There is no payment transaction which it has been made in the system to suppliers') .
		 '<br />
			<div class="centre">
			</div>';
	include('includes/footer.inc');
	exit;
} else {
	if (isset($_GET['SupplierID'])) {
		$_SESSION['SupplierID'] = $_GET['SupplierID'];
	}
	$SupplierID = $_SESSION['SupplierID'];
}


if (isset($_GET['StockID'])) {
	//The page is called with a StockID
	$_GET['StockID'] = trim(mb_strtoupper($_GET['StockID']));
	$_POST['Select'] = trim(mb_strtoupper($_GET['StockID']));
}
*/
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/inventory.png" title="' . _('Supplier Transaction Details') . '" alt="" />' . ' ' . _('Supplier Transaction Details') . '</p>';
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
$SQL = "SELECT suppliers.supplierid,
				suppliers.suppname
			FROM suppliers
			ORDER BY suppliers.supplierid";
$result1 = DB_query($SQL);
if (DB_num_rows($result1) == 0) {
	echo '<p class="bad">' . _('Problem Report') . ':<br />' . _('There are no stock categories currently defined please use the link below to set them up') . '</p>';
	echo '<br /><a href="' . $RootPath . '/StockCategories.php">' . _('Customer Tax Info') . '</a>';
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
	$SQL2="SELECT * FROM taxinfo ";
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
echo '<table class="selection" style=font-size:10pt><tr>';
echo '<td>' . _('Select Supplier') . ':';
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
echo '<td>' . _('Enter partial') . '<b> ' . _('Description') . '</b>:</td><td>';
if (isset($_POST['Keywords'])) {
	echo '<input type="text" name="Keywords" value="' . $_POST['Keywords'] . '" title="' . _('Enter text that you wish to search for in the item description') . '" size="20" maxlength="25" />';
} else {
	echo '<input type="text" name="Keywords" title="' . _('Enter text that you wish to search for in the item description') . '" size="20" maxlength="25" />';
}
echo '</td></tr><tr><td></td>';
echo '<td><b>' . _('OR') . ' ' . '</b>' . _('Enter partial') . ' <b>' . _('Supplier Code') . '</b>:</td>';
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
		
			$SQL = "SELECT *,a.typeno,
							b.suppname,
							b.taxgroupid,
			                a.trandate,
						    a.supplierid,
						    a.amount,
							b.supplierid,
						    b.taxref,	
						   SUM(e.amt) as totalpaid
						   FROM  withholdingtransactions a
						   INNER JOIN suppliers b ON a.supplierid=b.supplierid
						   LEFT JOIN supptrans c ON a.typeno=c.suppreference
						   LEFT JOIN  purchorderdetails d ON c.OrderNo=d.orderno
						   LEFT JOIN suppallocs e ON a.typeno=e.invoice							   
						   WHERE b.suppname " . LIKE . " '$SearchString'
						   GROUP BY a.typeno";
		} else {
				$SQL = "SELECT *,a.typeno,
							b.suppname,
							b.taxgroupid,
			                a.trandate,
						    a.supplierid,
						    a.amount,
							b.supplierid,
						    b.taxref,
						   SUM(e.amt) as totalpaid
						   FROM  withholdingtransactions a
						   INNER JOIN suppliers b ON a.supplierid=b.supplierid
						   LEFT JOIN supptrans c ON a.typeno=c.suppreference
						   LEFT JOIN  purchorderdetails d ON c.OrderNo=d.orderno
						   LEFT JOIN suppallocs e ON a.typeno=e.invoice	
						   WHERE b.suppname " . LIKE . " '$SearchString'
						   AND b.supplierid='". $_POST['StockCat'] ."'
						   GROUP BY a.typeno";
		}
	} elseif (isset($_POST['StockCode'])) {
		$_POST['StockCode'] = mb_strtoupper($_POST['StockCode']);
		if ($_POST['StockCat'] == 'All') {
				$SQL = "SELECT *, a.typeno,
							b.suppname,
							b.taxgroupid,
			                a.trandate,
						    a.supplierid,
							a.amount,
							b.supplierid,
						    b.taxref,	
						   SUM(e.amt) as totalpaid
						   FROM  withholdingtransactions a
						   INNER JOIN suppliers b ON a.supplierid=b.supplierid
						   LEFT JOIN supptrans c ON a.typeno=c.suppreference
						   LEFT JOIN  purchorderdetails d ON c.OrderNo=d.orderno
						   LEFT JOIN suppallocs e ON a.typeno=e.invoice	
						   WHERE b.supplierid " . LIKE . " '%" . $_POST['supplierid'] . "%'
						   GROUP BY a.typeno"; 
		} else {
				$SQL = "SELECT *, a.typeno,
							b.suppname,
							b.taxgroupid,
			                a.trandate,
						    a.supplierid,
						    a.amount,							
							b.supplierid,
						    b.taxref,	
						   SUM(e.amt) as totalpaid
						   FROM  withholdingtransactions a
						   INNER JOIN suppliers b ON a.supplierid=b.supplierid
						   LEFT JOIN supptrans c ON a.typeno=c.suppreference
						   LEFT JOIN  purchorderdetails d ON c.OrderNo=d.orderno
						   LEFT JOIN suppallocs e ON a.typeno=e.invoice	
						   WHERE b.supplierid " . LIKE . " '%" . $_POST['supplierid'] . "%'
						   AND b.supplierid='". $_POST['StockCat'] ."'
						   GROUP BY a.typeno";
		}
	} elseif (!isset($_POST['StockCode']) AND !isset($_POST['Keywords'])) {
		if ($_POST['StockCat'] == 'All') {
			$SQL = "SELECT *,a.typeno,
							b.suppname,
							b.taxgroupid,
			                a.trandate,
						    a.supplierid,
						    a.amount,
						    b.taxref,
							b.supplierid,	
						   SUM(e.amt) as totalpaid
						   FROM  withholdingtransactions a
						   INNER JOIN suppliers b ON a.supplierid=b.supplierid
						   LEFT JOIN supptrans c ON a.typeno=c.suppreference
						   LEFT JOIN  purchorderdetails d ON c.OrderNo=d.orderno
						   LEFT JOIN suppallocs e ON a.typeno=e.invoice	
						   RIGHT JOIN suppallocs e ON a.typeno=e.invoice
						   GROUP BY a.typeno";
		} else {
			$SQL = "SELECT *,a.typeno,
							b.suppname,
							b.taxgroupid,
			                a.trandate,
						    a.supplierid,
							a.amount,
						    b.taxref,
							b.supplierid,
						   SUM(e.amt) as totalpaid
						   FROM  withholdingtransactions a
						   INNER JOIN suppliers b ON a.supplierid=b.supplierid
						   LEFT JOIN supptrans c ON a.typeno=c.suppreference
						   LEFT JOIN  purchorderdetails d ON c.OrderNo=d.orderno
						   LEFT JOIN suppallocs e ON a.typeno=e.invoice	
						   RIGHT JOIN suppallocs e ON a.typeno=e.invoice
						   GROUP BY a.typeno";
		}
	}
	$ErrMsg = _('No stock items were returned by the SQL because');
	$DbgMsg = _('The SQL that returned an error was');
	$SearchResult = DB_query($SQL, $ErrMsg, $DbgMsg);
	if (DB_num_rows($SearchResult) == 0) {
		prnMsg(_('No transaction made against the supplier/contractor'), 'info');
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
		echo '<table id="ItemSearchTable"  class="selection" style=font-size:10pt>';
		$TableHeader = '<tr>
		                    <th>' . _('Sr.No.') . '</th>
							<th class="ascending">' ._('Date') . '</th>
		                    <th class="ascending">' . _('Invoice No.') . '</th>							
							<th class="ascending">' . _('Amt Invoiced') . '</th>
							<th>' . _('Item descriptions') . '</th>
							<th>' . _('Order No.') . '</th>
							<th>' . _('Supplier') . '</th>
							<th>' . _('Amt Paid') . '</th>
							<th>' . _('Balance') . '</th>
							<th class="ascending">' . _('Withholding') . '<br />' . _('VAT Amount') . '</th>
						</tr>';
		echo $TableHeader;
		$i=0;
		$j = 1;
		$k = 0; //row counter to determine background colour
		$RowIndex = 0;
		if (DB_num_rows($SearchResult) <> 0) {
			DB_data_seek($SearchResult, ($_POST['PageOffset'] - 1) * $_SESSION['DisplayRecordsMax']);
		}
		$i=0;
		while ($myrow = DB_fetch_array($SearchResult)) {
			if ($k == 1) {
				echo '<tr class="EvenTableRows">';
				$k = 0;
			} else {
				echo '<tr class="OddTableRows">';
				$k++;
			}
			$i++;
			$totalinvoice=($myrow['ovamount']+ $myrow['ovgst']);
			$Totalcost=0.06*($totalinvoice);
			$Amountp=$myrow['amount'];
			$balance=$totalinvoice- $myrow['amt'];
			$balance=($totalinvoice- $myrow['totalpaid']);
			
		  echo '<td style=font-size:10pt>' . $i . '</td> 
		         <td>' . ConvertSQLDate($myrow['trandate']) . '</td>
		         <td>' . $myrow['typeno'] . '</td>		        
				<td>' . locale_number_format($totalinvoice,2) . '</td>
				<td>' . $myrow['itemdescription'] . '</td>
				<td>' . $myrow['OrderNo'] . '</td>
				<td>' . $myrow['suppname'] . '</td>
				<td>' . locale_number_format($myrow['totalpaid'],2) . '</td>
				<td>' . locale_number_format($balance,2) . '</td>
				<td>' . locale_number_format($Totalcost,2) . '</td>';
				echo'</tr>';
				

	$j++;

			if ($j == 15 AND ($RowIndex + 1 != $_SESSION['DisplayRecordsMax'])) {
				$j = 1;
				echo $TableHeader;
			}
		
			$RowIndex = $RowIndex + 1;
			
			//end of page full new headings if
		}
		//end of while loop
			
		echo '</table>
              </div>
              </form>
              <br />';
			  
    	$supplierid = $_SESSION['supplierid'];
		/**	  
      $m1=DB_query("SELECT a.typeno,
						   b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE a.supplierid=b.supplierid");
	**/
	/**************************************************************************************************************/
	if ($_POST['Keywords']) {
		//insert wildcard characters in spaces
		$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
		$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';
		if ($_POST['StockCat'] == 'All') {
		
		 $m1=DB_query("SELECT a.typeno,
							b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE b.suppname " . LIKE . " '$SearchString'
						   AND a.supplierid=b.supplierid");
		} else {
				$m1=DB_query("SELECT a.typeno,
							b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE b.suppname " . LIKE . " '$SearchString'
						   AND a.supplierid='". $_POST['StockCat'] ."'
						   AND a.supplierid=b.supplierid");
		}
	} elseif (isset($_POST['StockCode'])) {
		$_POST['StockCode'] = mb_strtoupper($_POST['StockCode']);
		if ($_POST['StockCat'] == 'All') {
				$m1=DB_query("SELECT a.typeno,
				           b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE a.supplierid " . LIKE . " '%" . $_POST['supplierid'] . "%'
						   AND a.supplierid=b.supplierid"); 
		} else {
				$m1=DB_query("SELECT a.typeno,
				          b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE a.supplierid " . LIKE . " '%" . $_POST['supplierid'] . "%'
						   AND a.supplierid='". $_POST['StockCat'] ."'
						   AND a.supplierid=b.supplierid");
		}
	} elseif (!isset($_POST['StockCode']) AND !isset($_POST['Keywords'])) {
		if ($_POST['StockCat'] == 'All') {
			$m1=DB_query("SELECT a.typeno,
						   b.suppname,	
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE a.supplierid=b.supplierid");
		} else {
			$m1=DB_query("SELECT a.typeno,
						  b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE a.supplierid=b.supplierid");
		}
	}
	/**************************************************************************************************************/
while($mm2=DB_fetch_array($m1)){
$mm3=$mm2['supplierid'];
}
if(isset($mm3)){
echo "<form action='CustomerTransactions.xls'><input type='submit' value='Export as CSV' />";
require_once("excelwriter.class.php");

$excel=new ExcelWriter("CustomerTransactions.xls");// creating an instance of the Excelwriter class
if($excel==false)	
echo $excel->error;
			$myArr=array("Sr.No.",
						"Invoice No.",
						"Transaction Date",
						"Invoiced Amount",
						"PIN of Withholdee",
						"Name of Withholdee",
						"Withholding VAT Amount");// an array of the names at the top of excell sheet
     $excel->writeLine($myArr);
/*************************************************************************************************************************
     $qry=DB_query("SELECT a.typeno,
						   b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE a.supplierid=b.supplierid");// selecting from the database
/**********************************************************************************************************************************/
if ($_POST['Keywords']) {
		//insert wildcard characters in spaces
		$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
		$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';
		if ($_POST['StockCat'] == 'All') {
		
		 $qry=DB_query("SELECT a.typeno,
							b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE b.suppname " . LIKE . " '$SearchString'
						   AND a.supplierid=b.supplierid");
		} else {
				$qry=DB_query("SELECT a.typeno,
							b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE b.suppname " . LIKE . " '$SearchString'
						   AND a.supplierid='". $_POST['StockCat'] ."'
						   AND a.supplierid=b.supplierid");
		}
	} elseif (isset($_POST['StockCode'])) {
		$_POST['StockCode'] = mb_strtoupper($_POST['StockCode']);
		if ($_POST['StockCat'] == 'All') {
				$qry=DB_query("SELECT a.typeno,
				           b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE a.supplierid " . LIKE . " '%" . $_POST['supplierid'] . "%'
						   AND a.supplierid=b.supplierid"); 
		} else {
				$qry=DB_query("SELECT a.typeno,
				          b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE a.supplierid " . LIKE . " '%" . $_POST['supplierid'] . "%'
						   AND a.supplierid='". $_POST['StockCat'] ."'
						   AND a.supplierid=b.supplierid");
		}
	} elseif (!isset($_POST['StockCode']) AND !isset($_POST['Keywords'])) {
		if ($_POST['StockCat'] == 'All') {
			$qry=DB_query("SELECT a.typeno,
						   b.suppname,	
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE a.supplierid=b.supplierid");
		} else {
			$qry=DB_query("SELECT a.typeno,
						  b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE a.supplierid=b.supplierid");
		}
	}
//*******************************************************************************************************************************/
if($qry!=false)
{
	$i=1;
	while($res=DB_fetch_array($qry))
	{
	$Totalcost=$res['amount'];
	$Amountpaid=$res['amount'];
			$myArr=array($i,
						$res['typeno'],
						$res['trandate'],
						locale_number_format($Amountpaid,2),
						$res['taxref'],
						$res['suppname'],
						locale_number_format($Totalcost,2),);//fetching from the database and writing to the excel file
		$excel->writeLine($myArr);
		$i++;
	}
        }
          }
        }
    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////

/* end display list if there is more than one record */
include ('includes/footer.inc');
?>