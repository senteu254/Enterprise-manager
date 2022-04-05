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
$Title = _('Transaction Details');

/* webERP manual links before header.inc */
//$ViewTopic= 'Customer Tax Info';
//$BookMark = 'Customer Tax Info';

include ('includes/header.inc');
include ('includes/SQL_CommonFunctions.inc');
if(!isset($_GET['SupplierID']) AND !isset($_SESSION['SupplierID'])) {
	echo '<br />' . _('To display the enquiry a Supplier must first be selected from the Supplier selection screen') .
		 '<br />
			<div class="centre">
				<a href="' . $RootPath . '/SelectSupplier.php">' . _('Select a Supplier to Inquire On') . '</a>
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
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/inventory.png" title="' . _('Transaction Details') . '" alt="" />' . ' ' . _('Transaction Details') . '</p>';
		if (isset($_SESSION['SupplierID'])) {
	$SupplierName = '';
	$SQLy = "SELECT suppliers.suppname
			FROM suppliers
			WHERE suppliers.supplierid ='" . $_SESSION['SupplierID'] . "'";
	$SupplierNameResult = DB_query($SQLy);
	if (DB_num_rows($SupplierNameResult) == 1) {
		$myrow8 = DB_fetch_row($SupplierNameResult);
		$SupplierName = $myrow8[0];
	}
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/supplier.png" title="' . _('Supplier') . '" alt="" />' . ' ' . _('Supplier/Contractor') . ' : <b>' . $_SESSION['SupplierID'] . ' - ' . $SupplierName . '</b> ' . _('has been selected') . '.</p>';
	}
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


	echo '<tr>';			
		 
} // end displaying item options if there is one and only one record
echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';




echo '<table class="selection" style=font-size:10pt><tr>';
/*
echo '<td>' . _('Enter Invoice') . '<b> ' . _('No.') . '</b>:</td><td>';
if (isset($_POST['Keywords'])) {
	echo '<input type="text" name="Keywords" value="' . $_POST['Keywords'] . '" title="' . _('Enter text that you wish to search for in the item description') . '" size="20" maxlength="25" />';
} else {
	echo '<input type="text" name="Keywords" title="' . _('Enter text that you wish to search for in the item description') . '" size="20" maxlength="25" />';
}
echo '</td>';
*/
echo '<td><b>' . _('') . ' ' . '</b>' . _('Enter Invoice.') . ' <b>' . _('No.') . '</b>:</td>';
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
		
			$SQL = "SELECT a.typeno,
							b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE a.supplierid=b.supplierid
						   AND a.supplierid=".$SupplierID."";
		} else {
				$SQL = "SELECT a.typeno,
							b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE a.supplierid=b.supplierid
						   AND a.supplierid=".$SupplierID."";
		}
	} elseif (isset($_POST['StockCode'])) {
		$_POST['StockCode'] = mb_strtoupper($_POST['StockCode']);
		if ($_POST['StockCat'] == 'All') {
				$SQL = "SELECT a.typeno,
				           b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE a.supplierid=b.supplierid
						   AND a.supplierid=".$SupplierID."";
		} else {
				$SQL = "SELECT a.typeno,
				          b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE a.supplierid=b.supplierid
						   AND a.supplierid=".$SupplierID."
						   AND a.typeno " . LIKE . " '%" . $_POST['StockCode'] . "%'";
		}
	} elseif (!isset($_POST['StockCode']) AND !isset($_POST['Keywords'])) {
		if ($_POST['StockCat'] == 'All') {
			$SQL = "SELECT a.typeno,
						   b.suppname,	
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE a.supplierid=b.supplierid
						   AND a.supplierid=".$SupplierID."
						   AND a.typeno " . LIKE . " '%" . $_POST['StockCode'] . "%'";
		} else {
			$SQL = "SELECT a.typeno,
						  b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE a.supplierid=b.supplierid
						   AND a.supplierid=".$SupplierID."";
		}
	}
	$ErrMsg = _('No stock items were returned by the SQL because');
	$DbgMsg = _('The SQL that returned an error was');
	$SearchResult = DB_query($SQL, $ErrMsg, $DbgMsg);
	if (DB_num_rows($SearchResult) == 0) {
		prnMsg(_('There is No transaction made to this supplier/contractor '), 'info');
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
		                    <th class="ascending">' . _('Invoice NO.') . '</th>
							<th class="ascending">' . _('Transaction Date') . '</th>
							<th class="ascending">' . _('Invoiced Amount') . '</th>
							<th class="ascending">' . _('Withholding VAT Amount') . '</th>
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
		while (($myrow = DB_fetch_array($SearchResult))) {
			if ($k == 1) {
				echo '<tr class="EvenTableRows">';
				$k = 0;
			} else {
				echo '<tr class="OddTableRows">';
				$k++;
			}
			$i++;
			$Totalcost=0.06*($myrow['amount']);
			$Amountp=$myrow['amount'];
		  echo '<td style=font-size:10pt>' . $i . '</td> 
		         <td>' . $myrow['typeno'] . '</td>
		         <td>' . $myrow['trandate'] . '</td>
				<td>' . locale_number_format($Amountp,2) . '</td>
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
      $m1=DB_query("SELECT a.typeno,
						   b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE a.supplierid=b.supplierid
						   AND a.supplierid=".$SupplierID."");
while($mm2=DB_fetch_array($m1)){
$mm3=$mm2['supplierid'];
}
if(isset($mm3)){
echo "<form action='Customer_Transactions.xls'><input type='submit' value='Export as CSV' />";
require_once("excelwriter.class.php");

$excel=new ExcelWriter("Customer_Transactions.xls");// creating an instance of the Excelwriter class
if($excel==false)	
echo $excel->error;
			$myArr=array("Sr.No.",
						"Invoice No.",
						"Transaction Date",
						"Amount Invoiced",
						"PIN of Withholdee",
						"Name of Withholdee",
						"Withholding VAT Amount");// an array of the names at the top of excell sheet
     $excel->writeLine($myArr);

     $qry=DB_query("SELECT a.typeno,
						   b.suppname,
			               a.trandate,
						   a.supplierid,
						   b.taxref,
						   a.amount 
						   FROM  withholdingtransactions a,suppliers b
						   WHERE a.supplierid=b.supplierid
						   AND a.supplierid=".$SupplierID."");// selecting from the database
if($qry!=false)
{
	$i=1;
	while($res=DB_fetch_array($qry))
	{
	$Totalcost=0.06*($res['amount']);
	$Amountpaid=1*($res['amount']);
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