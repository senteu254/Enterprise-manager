<link rel="stylesheet" href="FA2/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="FA2/iCheck/flat/blue.css">
<?php

if (isset($_GET['StockID'])) {
	//The page is called with a StockID
	$_GET['StockID'] = trim(mb_strtoupper($_GET['StockID']));
	$_POST['Select'] = trim(mb_strtoupper($_GET['StockID']));
}
//echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/inventory.png" title="' . _('View Production Item') . '" alt="" />' . ' ' . _('View Production Item') . '</p>';
/*if (isset($_GET['NewSearch']) or isset($_POST['Next']) or isset($_POST['Previous']) or isset($_POST['Go'])) {
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
}*/
// Always show the search facilities
/*$SQL = "SELECT stockid,  
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
}*/
// end of showing search facilities
/* displays item options if there is one and only one selected */
//echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=FA2&Ref=default&Link=View_Farm_Description" method="post">';
//echo '<div>';
//echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
/*echo '<table class="selection table table-hover">
<tr>';
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
echo '<td>' . _('Enter Description') . '</b>:</td><td>';
if (isset($_POST['Keywords'])) {
	echo '<input type="text" name="Keywords" value="' . $_POST['Keywords'] . '" title="' . _('Enter text that you wish to search for in the item description') . '" size="20" maxlength="25" />';
} else {
	echo '<input type="text" name="Keywords" title="' . _('Enter text that you wish to search for in the item description') . '" size="20" maxlength="25" />';
}
echo '</td></tr><tr><td></td>';
echo '<td><b>Enter Service Code</b>:</td>';
echo '<td>';
if (isset($_POST['StockCode'])) {
	echo '<input type="text" name="StockCode" value="' . $_POST['StockCode'] . '" title="' . _('Enter text that you wish to search for in the item code') . '" size="15" maxlength="18" />';
} else {
	echo '<input type="text" name="StockCode" title="' . _('Enter text that you wish to search for in the item code') . '" size="15" maxlength="18" />';
}
echo '</td></tr></table><br />';
echo '<div class="centre"><input type="submit" name="Search" value="' . _('Search Now') . '" /></div><br />';*/
//echo '</div>
    //  </form>';
// query for list of record(s)
/*if(isset($_POST['Go']) OR isset($_POST['Next']) OR isset($_POST['Previous'])) {
	$_POST['Search']='Search';
}*/
/*if (isset($_POST['Search']) OR isset($_POST['Go']) OR isset($_POST['Next']) OR isset($_POST['Previous'])) {
	if (!isset($_POST['Go']) AND !isset($_POST['Next']) AND !isset($_POST['Previous'])) {
		// if Search then set to first page
		$_POST['PageOffset'] = 1;
	}
	if ($_POST['Keywords'] AND $_POST['StockCode']) {
		prnMsg (_('Stock description keywords have been used in preference to the Stock code extract entered'), 'info');
	}
	if ($_POST['Keywords']) {*/
		//insert wildcard characters in spaces
		$SQL="SELECT a. description_Id,
			        b.stockid,
					a.cost,
					a.units,	
					a.description
					FROM farmdescriptions a,stockmaster b
					WHERE a.stockid=b.stockid ";
	$ErrMsg = _('No stock items were returned by the SQL because');
	$DbgMsg = _('The SQL that returned an error was');
	$SearchResult = DB_query($SQL, $ErrMsg, $DbgMsg);
	if (DB_num_rows($SearchResult) == 0) {
		prnMsg(_('No stock items were returned by this search please re-enter alternative criteria to try again'), 'info');
	}


/* end query for list of records */
/* display list if there is more than one record */
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
		echo '<table  id="myTable2" style="width:100%" class="selection table table-hover">
		<thead>';		
		$TableHeader = '<tr>
							<th class="ascending">' . _('DescriptionID') . '</th>
							<th class="ascending">' . _('Item Description') . '</th>
							<th>' . _('Units') . '</th>
							<th>' . _('Cost') . '</th>
							<th>' . _('Action') . '</th>
						</tr>
		</thead>
		<tbody>';
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
				echo"<td> <a href ='?Application=FA2&Ref=default&Link=Edit_Farm_Description_Item&description_Id=".$id."'>Edit</a>";
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
		echo '
		<tbody>
		</table>
              </div>
              </form>
              <br />';
	
/* end display list if there is more than one record */
?>