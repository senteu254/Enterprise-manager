<link rel="stylesheet" href="PVP/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="PVP/dist/css/AdminLTE.min.css">
<?php
if (isset($_GET['StockID'])) {
	//The page is called with a StockID
	$_GET['StockID'] = trim(mb_strtoupper($_GET['StockID']));
	$_POST['Select'] = trim(mb_strtoupper($_GET['StockID']));
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
	
} 
// end displaying item options if there is one and only one record
echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<table style="width:100%; font-size:12px;" class="selection table table-hover">';



echo '<tr>';
/*echo '<td>' . _('Select type of service') . ':';
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
echo '</select></td>';*/
/////////////////////////////////////////////////////////////////////////////////////
if (isset($_GET['FromDate'])) {
	$_POST['TransAfterDate']=$_GET['FromDate'];
}
if (isset($_GET['FromDate'])) {
	$_POST['TransToDate']=$_GET['TODate'];
}
if (!isset($_POST['TransAfterDate']) OR !Is_Date($_POST['TransAfterDate'])) {
	$_POST['TransAfterDate'] = Date($_SESSION['DefaultDateFormat'],Mktime(0,0,0,Date('m')-12,Date('d'),Date('Y')));
}
if (!isset($_POST['TransToDate']) OR !Is_Date($_POST['TransToDate'])) {
	$_POST['TransToDate'] = Date($_SESSION['DefaultDateFormat'],Mktime(0,0,0,Date('m')));
}



echo '<td>' . _('Enter partial') . '<b> ' . _('Description') . '</b>:</td><td>';
if (isset($_POST['Keywords'])) {
	echo '<input type="text" name="Keywords" value="' . $_POST['Keywords'] . '" title="' . _('Enter text that you wish to search for in the item description') . '" size="20" maxlength="25" />';
} else {
	echo '<input type="text" name="Keywords" title="' . _('Enter text that you wish to search for in the item description') . '" size="20" maxlength="25" />';
}
echo '<td>' . _('Between') . ':';
				echo'<input type="text" required="required" class="date" alt="', $_SESSION['DefaultDateFormat'], '" id="datepicker" name="TransAfterDate" value="', $_POST['TransAfterDate'], '" minlength="0" maxlength="10" size="12" />';
echo _('TO'), ':<input type="text" required="required" class="date" alt="', $_SESSION['DefaultDateFormat'], '" id="datepicker" name="TransToDate" value="', $_POST['TransToDate'], '" minlength="0" maxlength="10" size="12" /></td>
		</tr></td>';
echo '</td></tr>';
echo'<tr>';
echo '<td><b>' . _('OR') . ' ' . '</b>' . _('Enter partial') . ' <b>' . _('Service Code') . '</b>:</td>';
echo '<td>';
if (isset($_POST['StockCode'])) {
	echo '<input type="text" name="StockCode" value="' . $_POST['StockCode'] . '" title="' . _('Enter text that you wish to search for in the item code') . '" size="20" maxlength="18" />';
} else {
	echo '<input type="text" name="StockCode" title="' . _('Enter text that you wish to search for in the item code') . '" size="15" maxlength="18" />';
}
echo '</td></tr></table><br />';
echo '<div class="centre"><input type="submit" name="Search" value="' . _('Search Now') . '" /></div><br />';
echo '</div>
      </form>';
	  $DateAfterCriteria = FormatDateForSQL($_POST['TransAfterDate']);
$TransToDate = FormatDateForSQL($_POST['TransToDate']);
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
		prnMsg (_('Stock description keywords have been used in preference to the Service code extract entered'), 'info');
	}
	if ($_POST['Keywords']) {
		//insert wildcard characters in spaces
		$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
		$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';
		if ($_POST['StockCat'] == 'All') {
			$SQL = "SELECT  *,c.stockid as groupid,b.units, c.description as desk FROM farmproduction a
					INNER JOIN farmproductionitems b ON a.Fid=b.fid
					INNER JOIN farmdescriptions c ON b.stockid=c.description_Id
					INNER JOIN stockmaster d ON c.stockid=d.stockid
					LEFT JOIN farmitemsource e ON e.source_Id=b.source
					WHERE c.description " . LIKE . " '$SearchString'
					AND a.date>='" . $DateAfterCriteria . "'
					AND a.date<='" . $TransToDate . "'
					ORDER BY d.stockid";
		} else {
			$SQL = "SELECT  *,c.stockid as groupid,b.units, c.description as desk FROM farmproduction a
					INNER JOIN farmproductionitems b ON a.Fid=b.fid
					INNER JOIN farmdescriptions c ON b.stockid=c.description_Id
					INNER JOIN stockmaster d ON c.stockid=d.stockid
					LEFT JOIN farmitemsource e ON e.source_Id=b.source
					WHERE c.description " . LIKE . " '$SearchString'
					AND a.date>='" . $DateAfterCriteria . "'
					AND a.date<='" . $TransToDate . "'
					ORDER BY d.stockid";
		}
	} elseif (isset($_POST['StockCode'])) {
		$_POST['StockCode'] = mb_strtoupper($_POST['StockCode']);
		if ($_POST['StockCat'] == 'All') {
			$SQL = "SELECT  *,c.stockid as groupid,b.units,c.description as desk FROM farmproduction a
					INNER JOIN farmproductionitems b ON a.Fid=b.fid
					INNER JOIN farmdescriptions c ON b.stockid=c.description_Id
					INNER JOIN stockmaster d ON c.stockid=d.stockid
					LEFT JOIN farmitemsource e ON e.source_Id=b.source
					WHERE c.description_Id " . LIKE . " '%" . $_POST['StockCode'] . "%'
					AND a.date>='" . $DateAfterCriteria . "'
					AND a.date<='" . $TransToDate . "'
					ORDER BY d.stockid";
		} else {
			$SQL = "SELECT  *,c.stockid as groupid,b.units, c.description as desk FROM farmproduction a
					INNER JOIN farmproductionitems b ON a.Fid=b.fid
					INNER JOIN farmdescriptions c ON b.stockid=c.description_Id
					INNER JOIN stockmaster d ON c.stockid=d.stockid
					LEFT JOIN farmitemsource e ON e.source_Id=b.source
					WHERE c.description_Id " . LIKE . " '%" . $_POST['StockCode'] . "%'
					AND a.date>='" . $DateAfterCriteria . "'
					AND a.date<='" . $TransToDate . "'
					ORDER BY d.stockid";
		}
	} elseif (!isset($_POST['StockCode']) AND !isset($_POST['Keywords'])) {
		if ($_POST['StockCat'] == 'All') {
		$SQL = "SELECT  *,c.stockid as groupid,b.units ,c.description as desk FROM farmproduction a
					INNER JOIN farmproductionitems b ON a.Fid=b.fid
					INNER JOIN farmdescriptions c ON b.stockid=c.description_Id
					INNER JOIN stockmaster d ON c.stockid=d.stockid
					LEFT JOIN farmitemsource e ON e.source_Id=b.source
					WHERE a.date>='" . $DateAfterCriteria . "'
					AND a.date<='" . $TransToDate . "'
					ORDER BY d.stockid, description_Id Group by d.stockid";
		} else {
			$SQL = "SELECT  *,c.stockid as groupid,b.units ,c.description as desk FROM farmproduction a
					INNER JOIN farmproductionitems b ON a.Fid=b.fid
					INNER JOIN farmdescriptions c ON b.stockid=c.description_Id
					INNER JOIN stockmaster d ON c.stockid=d.stockid
					LEFT JOIN farmitemsource e ON e.source_Id=b.source
					INNER JOIN farmservicesmaintenance f ON f.service_Id=a.service_Id
					WHERE a.date>='" . $DateAfterCriteria . "'
					AND a.date<='" . $TransToDate . "'
					ORDER BY d.stockid";
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
	echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=FA2&Ref=Dashboard" method="post">';
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
		?>
		<table id="myTable2"  style="width:120%; font-size:12px;" class="selection table table-hover">
		<thead>		
		<?php
		$TableHeader = '<tr>
		                    <th class="ascending">' . _('Date') . '</th>
							<th class="ascending">' . _('Code') . '</th>
							<th class="ascending">' . _('Item Description') . '</th>
							<th class="ascending">' . _('Source') . '</th>
							<th>' . _('Unitcost') . '</th>
							<th>' . _('Qty/No Of Ltrs') . '</th>
							<th>'._('Area Covered').'</th>
							<th>' . _('Total Cost') . '</th>
						</tr>';
						?>
						</thead>
		<tbody>
		<?php
		echo $TableHeader;
		$j = 1;
		$k = 0; //row counter to determine background colour
		$i=0;
		$RowIndex = 0;
		if (DB_num_rows($SearchResult) <> 0) {
			DB_data_seek($SearchResult, ($_POST['PageOffset'] - 1) * $_SESSION['DisplayRecordsMax']);
		}
		$group=NULL;
		$AmountGroup =0;
		$totals = 0;
		while (($myrow = DB_fetch_array($SearchResult))) {    

			$i++;
			
		/*	if($myrow['service_Id']=='CO'){
		 $So1= DB_query("SELECT sum((unitcost * areacovered)) AS `SUM_TOTAL` 
		 				FROM  farmproductionitems b
						INNER JOIN farmdescriptions c ON b.stockid=c.description_Id
						where c.stockid='".$myrow['groupid']."'
						GROUP BY c.stockid");      
		 $myrow21 = DB_fetch_array($So1);
		 $totals1 = $myrow21['SUM_TOTAL'];
		}elseif($myrow['service_Id']=='KF'){
		 $So= DB_query("SELECT sum((unitcost * quantity)) AS `SUM_TOTAL` 
		 				FROM  farmproductionitems b
						INNER JOIN farmdescriptions c ON b.stockid=c.description_Id
						where c.stockid='".$myrow['groupid']."'
						GROUP BY c.stockid");      
		 $myrow2 = DB_fetch_array($So);
		 $totals2 = $myrow2['SUM_TOTAL'];
		 
		}*/
		$Totalcost=($myrow['unitcost']*$myrow['quantity']);
         /*displayin the data */
		 if($myrow['groupid']!= $group){
		 if($AmountGroup>0){
		// echo '<tr><td colspan="6"></td><th>Total :</th><th class="number">'.locale_number_format($totals, 2).'</th></tr>';
		 }
		 echo '<tr><th colspan="9">'.$myrow['description'].'</th></tr>';
		 $group = $myrow['groupid'];
		 $totals = 0;
		 	
		$totals_all= $totals;
		$So= DB_query("SELECT sum((unitcost * quantity)) AS `SUM_TOTAL` 
		 				FROM  farmproductionitems b
						INNER JOIN farmdescriptions c ON b.stockid=c.description_Id
						where c.stockid='".$myrow['groupid']."'
						GROUP BY c.stockid");      
		 $myrow2 = DB_fetch_array($So);
		 $totals = $myrow2['SUM_TOTAL'];
		 }
		 
		if ($k == 1) {
				echo '<tr class="EvenTableRows">';
				$k = 0;
			} else {
				echo '<tr class="OddTableRows">';
				$k++;
			}
			if($myrow['service_Id']=='CO'){
			$Totalcost=($myrow['unitcost']*$myrow['quantity']);
			}elseif($myrow['service_Id']=='KF'){
           $Totalcost=($myrow['unitcost']*$myrow['areacovered']);
		   }
		   $totals_all2+= $Totalcost;
		   $FormatedDeliveryDate = ConvertSQLDate($myrow['date']);
		   echo '<td>' . $FormatedDeliveryDate . '</td>
			     <td>' . $myrow['description_Id'] . '</td>
				<td>' . $myrow['desk'] . '</td>
				<td>' . $myrow['source_Name'] . '</td>
				<td>' . locale_number_format($myrow['unitcost'],2) . '</td>
				<td>' . $myrow['quantity'] . '</td>
				<td>' .$myrow['areacovered']. '</td>
				<td class="number">' . locale_number_format($Totalcost,2) . '</td>
				</tr>';
			
			$z++;
			$AmountGroup++;
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
		echo '<tr><td colspan="6"></td><th>Total :</th><th class="number">'.locale_number_format($totals_all2, 2).'</th></tr>';
		//end of while loop
		?>
		</tbody>
		</table>
              </div>
              </form>
            <?php
		echo'<br />';
			 echo'<a href="PDFFarmproduction.php?datef='.$DateAfterCriteria.'&datet='.$TransToDate.'" target="_blank"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>';
			  //echo '<a href="PDFFarmproduction.php?datef='.$DateFrom.'&datet='.$DateTo.'">Print PDF</a>';

	}
}

/* end display list if there is more than one record */
?>