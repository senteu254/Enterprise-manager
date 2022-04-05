<?php
/* $Id: SelectProduct.php 7096 2015-01-24 03:08:00Z turbopt $*/
$PricesSecurity = 12;//don't show pricing info unless security token 12 available to user
$SuppliersSecurity = 9; //don't show supplier purchasing info unless security token 9 available to user
include ('includes/session.inc');
$Title = _('Procurement status for the financial year ' .Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],0)) . '');
/* webERP manual links before header.inc */
$ViewTopic= ' Procurement Order Status';
$BookMark = 'Orders';

include ('includes/header.inc');
include ('includes/SQL_CommonFunctions.inc');
if(isset($_POST['PrintPDF'])
AND isset($DateFrom))
{
include('includes/PDFStarter.php');
ob_end_clean();
	$pdf->addInfo('Title',_('Procurement Order Status Report'));
	$pdf->addInfo('Subject',_('Procurement Order Status Report'));
	$FontSize=12;
	$PageNumber=0;
	$line_height=12;

      /*Now figure out the bills to report for the part range under review */
	$SQL = "SELECT * FROM purchorders a
							INNER JOIN  purchorderdetails b ON a.orderno=b.orderno
							INNER JOIN suppliers c ON a.supplierno=c.supplierid
							AND a.orddate=>". $DateFrom ."
							GROUP BY a.orderno";

	$BOMResult = DB_query($SQL,'','',false,false); //dont do error trapping inside DB_query

	if (DB_num_rows($BOMResult)==0){
	   $Title = _('Bill of Materials Listing') . ' - ' . _('Problem Report');
	  // include('includes/header.inc');
	   prnMsg( _('The Bill of Material listing has no bills to report on'),'warn');
	  // include('includes/footer.inc');
	   exit;
	}

	include ('includes/PDFProcurementStatusReport.inc');

	$ParentPart = '';
$i;
	while ($BOMList = DB_fetch_array($BOMResult,$db)){
$i++;
		if ($ParentPart!=$BOMList['orderno']){

			$FontSize=10;
			if ($ParentPart!=''){ /*Then it's NOT the first time round */
				/* need to rule off from the previous parent listed */
				$YPos -=$line_height;
				$pdf->line($Page_Width-$Right_Margin, $YPos,$Left_Margin, $YPos);
				$YPos -=$line_height;
			}
			//$SQL = "SELECT description FROM stockmaster WHERE stockmaster.stockid = '" . $BOMList['parent'] . "'";
			$SQL = "SELECT * FROM purchorders a
								INNER JOIN  purchorderdetails b ON a.orderno=b.orderno
								INNER JOIN suppliers c ON a.supplierno=c.supplierid
								LEFT JOIN  payment_voucher_tracker d on a.orderno=d.orderno
								LEFT JOIN  payment_voucher e ON d.voucherno=e.voucherid
								WHERE b.orderno = '" . $BOMList['orderno'] . "'";
			$ParentResult = DB_query($SQL);
			//$ParentRow = DB_fetch_row($ParentResult);
			//$pdf->SetTextColor(0,0,255);
			$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,40-$Left_Margin,$FontSize,$i,'');
			
			$LeftOvers = $pdf->addTextWrap($Left_Margin+20,$YPos,40-$Left_Margin,$FontSize,$BOMList['orderno'] . ' - ' . $BOMList['suppname'],'left');
			//$pdf->SetTextColor(0,0,0);
			$ParentPart = $BOMList['orderno'];
		}
   while ($ParentRow = DB_fetch_array($ParentResult,$db)){
		$YPos -=$line_height;
		$FontSize=8;
		$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,180,$FontSize,'-');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+15,$YPos,180,$FontSize,$ParentRow['itemdescription'],'left');
		$LeftOvers = $pdf->addTextWrap(300,$YPos,200,$FontSize,$ParentRow['quantityord'],'left');

		$DisplayQuantity = locale_number_format($ParentRow['qtyinvoiced']);
		$LeftOvers = $pdf->addTextWrap(350,$YPos,50,$FontSize,$ParentRow['quantityrecd'],'left');
		$LeftOvers = $pdf->addTextWrap(400,$YPos,50,$FontSize,$ParentRow['qtyinvoiced'],'left');
		$LeftOvers = $pdf->addTextWrap(460,$YPos,100,$FontSize,'procured','left');
		$LeftOvers = $pdf->addTextWrap(500,$YPos,30,$FontSize,$ParentRow['qtyinvoiced'],'left');
		if($ParentRow['qtyinvoiced']>=6){
		$LeftOvers = $pdf->addTextWrap(530,$YPos,30,$FontSize,'Paid','left');
		}else{
		$LeftOvers = $pdf->addTextWrap(530,$YPos,30,$FontSize,'Unpaid','left');
		}
}
		if ($YPos < $Bottom_Margin + $line_height){
		   include('includes/PDFProcurementStatusReport.inc');
		}

	} /*end BOM Listing while loop */
	$YPos -=$line_height;
	$pdf->line($Page_Width-$Right_Margin, $YPos,$Left_Margin, $YPos);

    $pdf->OutputI($_SESSION['DatabaseName'] . '_ProcurementOrders_' . date('Y-m-d').'.pdf');
    $pdf->__destruct();

} 

if (isset($_GET['StockID'])) {
	//The page is called with a StockID
	$_GET['StockID'] = trim(mb_strtoupper($_GET['StockID']));
	$_POST['Select'] = trim(mb_strtoupper($_GET['StockID']));
}
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/inventory.png" title="' . _('Procurement status for the financial year ' .Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],0)) . '') . '" alt="" />Procurement status for the financial year '.Date('Y',YearEndDate($_SESSION['YearEnd'],-1)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd'],0)).'</p>';
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
$SQL = "SELECT* ,a.cell,b.loccode,b.locationname
                       FROM data_cell a
	                   INNER JOIN locations b ON a.c_name=b.loccode
					   INNER JOIN cell_maintenance c ON a.cell=c.cell_code
					   ORDER BY a.cell ASC";
$result1 = DB_query($SQL);
if (DB_num_rows($result1) == 0) {
	echo '<p class="bad">' . _('Problem Report') . ':<br />' . _('There are no Cells in the system  defined, please use the link below to set them up') . '</p>';
	echo '<br /><a href="' . $RootPath . '/Data_Cells.php">' . _('Procurement Data Cells') . '</a>';	
	include ('includes/footer.inc');
	exit;
}
// end of showing search facilities
/* displays item options if there is one and only one selected 
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
	
	
} // end displaying item options if there is one and only one record*/
echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '
<table class="selection">
<tr>';
//////////////////////////////////////////////
	if (!isset($_POST['DateFrom'])) {
		$DateSQL = "SELECT min(orddate ) as fromdate,
							max(orddate) as todate
						FROM purchorders";
		$DateResult = DB_query($DateSQL);
		$DateRow = DB_fetch_array($DateResult);
		$DateFrom = $DateRow['fromdate'];
		$DateTo = $DateRow['todate'];
	} else {
		$DateFrom = FormatDateForSQL($_POST['DateFrom']);
		$DateTo = FormatDateForSQL($_POST['DateTo']);
	}
	////////////////////////////////////////////
echo '<td>' . _('Select Cell') . ':';
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
	if ($myrow1['cell_code'] == $_POST['StockCat']) {
		echo '<option selected="selected" value="' . $myrow1['cell_code'] . '">' . $myrow1['cell_name'] . '</option>';
	} else {
		echo '<option value="' . $myrow1['cell_code'] . '">' . $myrow1['cell_name'] . '</option>';
	}
}
echo '</select></td>';
/*echo'<td>' . _('Type of Order') . ':
<select required="required" autofocus="autofocus" name="OrderType">
                <option selected="selected" value="LPO">' . _('LPO') . '</option>
				<option value="LSO">' . _('LSO') . '</option>
				</select>';*/
echo'<td>' . _('Type of Order') . '</td>';
		$OrderType = array('LPO','LSO');
		echo '<td><select required="required" name="OrderType">';
	foreach($OrderType as $type){
		if($type == $_POST['OrderType']){
		echo'<option selected="selected" value="'.$type.'">'.$type.'</option>';
		}else{
	  echo'<option value="'.$type.'">'.$type.'</option>';
	  }
	  }
	echo '</select>';
echo '<td>' . _('Orders Between') . ':&nbsp;
			<input type="text" name="DateFrom" value="' . ConvertSQLDate($DateFrom) . '"  class="date" size="10" alt="' . $_SESSION['DefaultDateFormat'] . '"  />
		' . _('and') . ':&nbsp;
			<input type="text" name="DateTo" value="' . ConvertSQLDate($DateTo) . '"  class="date" size="10" alt="' . $_SESSION['DefaultDateFormat'] . '"  />
</td>

</tr></table><br />';
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
	if ($_POST['OrderType'] == 'LPO') {				
			$SQL=DB_query("SELECT * FROM purchorders a
							INNER JOIN  purchorderdetails b ON a.orderno=b.orderno
							INNER JOIN grns f ON b.podetailitem = f.podetailitem
							INNER JOIN suppliers c ON a.supplierno=c.supplierid
							WHERE a.orddate >='" . $DateFrom . "'
							AND a.orddate <='" . $DateTo . "'
							AND f.podetailitem IS  NOT NULL
							GROUP BY a.orderno");
			}else{
			$SQL=DB_query("SELECT * FROM lsorders a
							INNER JOIN  lsorderdetails b ON a.orderno=b.orderno
							INNER JOIN suppliers c ON a.supplierno=c.supplierid
							INNER JOIN grns f ON b.podetailitem = f.podetailitem
							WHERE a.orddate>='" . $DateFrom . "'
							AND a.orddate<='" . $DateTo . "'
							GROUP BY a.orderno");
			
			}
	/*$SQL=DB_query("SELECT * FROM purchorders a
							INNER JOIN  purchorderdetails b ON a.orderno=b.orderno
							INNER JOIN grns f ON b.podetailitem = f.podetailitem
							INNER JOIN suppliers c ON a.supplierno=c.supplierid
							INNER JOIN  payment_voucher_tracker d on b.orderno=d.orderno
							INNER JOIN  payment_voucher e ON d.voucherno=e.voucherid
							WHERE a.orddate>='" . $DateFrom . "'
							AND a.orddate<='" . $DateTo . "'
							GROUP BY a.orderno");*/
							
	$ErrMsg = _('No Transaction were returned by the SQL because');
	$DbgMsg = _('The SQL that returned an error was');
	$SearchResult = $SQL;
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
		echo '<table style="width:80%;" class="selection">';
		$TableHeader = '<tr>
						<th class="ascending">' . _('Order #.') . '</th>
						<th class="ascending">' . _('Date Ordered') . '</th>						
						<th class="ascending">' . _('Proc Method') . '</th>
						<th>' . _('Supplier') . '</th>
						<th>' . _('Amount') . '</th>						
						<th>' . _('RV.NO') . '</th>
						<th>' . _('Remarks') . '</th>
						</tr>';
		echo $TableHeader;
		$j = 1;
		$k = 0; //row counter to determine background colour
		$RowIndex = 0;
		if (DB_num_rows($SearchResult) <> 0) {
			DB_data_seek($SearchResult, ($_POST['PageOffset'] - 1) * $_SESSION['DisplayRecordsMax']);
		}
		while (($myrow = DB_fetch_array($SQL))) {
			if ($k == 1) {
				echo '<tr class="OddTableRows">';
				$k = 0;
			} else {
				echo '<tr class="OddTableRows">';
				$k++;
			}
			//$Viewmore  = $RootPath . '/Suppliersquatationdetails.php?TenderID=' . $myrow['tenderid'];
		// $Suppliers  = $RootPath . '/Supplierdetailsprint.php?TenderID=' . $myrow['tenderid'];
		$total_amount=$myrow['unitprice'] * $myrow['quantityord'];
		  echo '<td>' . $myrow['orderno'] . '</td>
		        <td>' .  ConvertSQLDate($myrow['orddate']) . '</td>
				<td>Procured</td>
				<td>' . $myrow['suppname'] . '</td>
				<td>' . locale_number_format($total_amount, $myrow['currdecimalplaces']) . '</td>				
				<td>' . $myrow['grn_numbering'] . '</td>';
				if($LineRow['process_level']==8){
				echo'<td>paid</td>';
				}else{
				echo'<td style="color:#FF0000">Unpaid</td>';
				}
				echo'</tr>';
				if ($_POST['OrderType'] == 'LPO') {	
		$LINEITEM=DB_query("SELECT * FROM purchorders a
								     INNER JOIN  purchorderdetails b ON a.orderno=b.orderno							
							 	     WHERE a.orderno = " . $myrow['orderno'] . "");
								}else{
		$LINEITEM=DB_query("SELECT * FROM lsorders a
								     INNER JOIN  lsorderdetails b ON a.orderno=b.orderno
								     WHERE a.orderno = " . $myrow['orderno'] . "");								
								}
			echo '<tr>
				<td></td>
				<td colspan="5">
					<table style="width:90%;"class="selection">
					<tr>
						<th class="ascending">#</th>
						<th style="width:50%;" class="ascending">' . _('Description') . '</th>
						<th class="ascending">' . _('Qty Ord') . '</th>
			            <th class="ascending">' . _('Qty Recd') . '</th>
						<th class="ascending">' . _('Qty Invoiced') . '</th>
						<th class="ascending">' . _('Nature') . '</th>
						<th class="ascending">' . _('Status') . '</th>
					</tr>';
       $i;
		while (($LineRow = DB_fetch_array($LINEITEM))) {
		$i++;
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k++;
		}
		  echo '<td>' . $i . '</td>
		        <td>' . $LineRow['itemdescription'] . '</td>
				<td>' . $LineRow['quantityord'] . '</td>
		        <td>' . $LineRow['quantityrecd'] . '</td>
			    <td>' . $LineRow['qtyinvoiced'] . '</td>				
				<td>' . $LineRow['qtyinvoiced'] . '</td>';
			/*if($LineRow['process_level']==8){
				echo'<td>paid</td>';
				}else{
				echo'<td style="color:#FF0000">Upaid</td>';
				}*/
		
		
		echo'</tr>';
		
		}
		echo'</tr>
		</table>';
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
			  
              <br />';
	}
	//echo '<a href="PDFquotationgrouptype.php?stockcat='.$_POST['StockCat'].'">Print PDF</a>';
	//echo'<br /><div class="centre"><input tabindex="3" type="submit" name="PrintPDF" value="' . _('Print PDF') . '" /></div>
             echo'</div>
              </form>';

}
/* end display list if there is more than one record */

include ('includes/footer.inc');
?>