
<?php
	
//$supplierid = $_SESSION['supplierid'];
/* $Id: SelectProduct.php 7096 2015-01-24 03:08:00Z turbopt $*/
$PricesSecurity = 12;//don't show pricing info unless security token 12 available to user
$SuppliersSecurity = 9; //don't show supplier purchasing info unless security token 9 available to user

include ('includes/session.inc');
$Title = _('SupplimentaryTracking');
/* webERP manual links before header.inc */
$ViewTopic= 'Suppliers Payments';
$BookMark = 'Votebook Supplimentary Tracking';

include ('includes/header.inc');
include ('includes/SQL_CommonFunctions.inc');

echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/inventory.png" title="' . _('Votebook Supplimentary Tracking') . '" alt="" />' . ' ' . _('Votebook Supplimentary Tracking') . '</p>';
echo'</br>';
// end of showing search facilities
/* displays item options if there is one and only one selected */
	if (!isset($_POST['TransAfterDate']))  {
	$_POST['TransAfterDate'] = Date($_SESSION['DefaultDateFormat'], Mktime(0, 0, 0, Date('m') - $_SESSION['NumberOfMonthMustBeShown'], Date('d'), Date('Y')) );
}
if (!isset($_POST['TransToDate']))  {
	$_POST['TransToDate'] = Date($_SESSION['DefaultDateFormat'], Mktime(0, 0, 0, Date('m')) );
} // end displaying item options if there is one and only one record
if(isset($_POST['PDF'])){
			include ('includes/class.pdf.php');

/* This invoice is hard coded for A4 Landscape invoices or credit notes so can't use PDFStarter.inc */
  ob_end_clean();
	$Page_Width=842;
	$Page_Height=595;
	$Top_Margin=30;
	$Bottom_Margin=30;
	$Left_Margin=40;
	$Right_Margin=30;


	$pdf = new Cpdf('L', 'pt', 'A4');
	$pdf->addInfo('Creator', 'kofc http');
	$pdf->addInfo('Author', 'admin ' . $Version);

		
		$title ='Votebook Supplimentary report';
		$subj ='Votebook Supplimentary';
		
		$pdf->addInfo('Title',_($title));
		$pdf->addInfo('Subject',_($subj));
	

	$pdf->setAutoPageBreak(0);
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->AddPage();
	$pdf->cMargin = 0;
/* END Brought from class.pdf.php constructor */

	$FirstPage = true;
	$line_height=16;

	//Keep a record of the user's language
	$UserLanguage = $_SESSION['Language'];
	
	/* retrieve the invoice details from the database to print
	notice that salesorder record must be present to print the invoice purging of sales orders will
	nobble the invoice reprints */
  
	 $i=0;  
				$sql3="SELECT *,b.Votehead as fromx, c.Votehead as tox, a.To_Votecode as vote
							   FROM supplmentarytracking a
							   INNER JOIN voteheadmaintenance b ON a.From_Votecode=b.Votecode
							   INNER JOIN voteheadmaintenance c ON a.To_Votecode=c.Votecode
							   LEFT JOIN www_users d ON a.user=d.userid";
							   
			$result3=DB_query($sql3);
			
			if (DB_error_no()!=0 OR DB_num_rows($result3)==0){

				$Title = _('Transaction Print Error Report');
				//include ('includes/header.inc');
				echo '<br />' . _('There was a problem retrieving the information from the database');
				if ($debug==1) {
					echo '<br />' . _('The SQL used to get this information that failed was') . '<br />' . $sql;
				}
				include('includes/footer.inc');
				exit;
			} else {
				$FontSize = 10;
				$PageNumber = 1;
				include('includes/PDFExpensesHeader2.php');
				$FirstPage = False;
				while ($myrow2=DB_fetch_array($result3)){
				
                		$i++;	 
				 	 $sql1 = "SELECT * FROM  funds_allocations WHERE votecode=".$myrow2['vote']." AND Financial_Year='".Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],+0))."'";
		$result1 = DB_query($sql1);
		$Row = DB_fetch_array($result1);
		   $totalalloc=locale_number_format($Row['allocated_Fund']+$myrow2['Amount'],2);
		   $totalAlloc=($myrow2['allocated_Fund']+ $myrow2['suppliementary']);	
		    $totalfund+=$Row['allocated_Fund'];
			$alloc+=$myrow2['Amount'];
				 //$sum=($comm + $myrow2['amt']);
				 //$Ava_Balance=($totalAlloc-$sum);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+0,$YPos,95,$FontSize,$i);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+17,$YPos,255,$FontSize,$myrow2['fromx']);
                 $LeftOvers = $pdf->addTextWrap($Left_Margin+209,$YPos,250,$FontSize,$myrow2['tox']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+410,$YPos,95,$FontSize,locale_number_format($Row['allocated_Fund'],2),'right');
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+475,$YPos,95,$FontSize,locale_number_format($myrow2['Amount'],2),'right');
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+545,$YPos,95,$FontSize,$totalalloc,'right');
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+600,$YPos,95,$FontSize,ConvertSQLDate($myrow2['alloc_Date']),'right');
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+670,$YPos,95,$FontSize,$myrow2['realname'],'right');
				// $LeftOvers = $pdf->addTextWrap($Left_Margin+678,$YPos,95,$FontSize,locale_number_format($Ava_Balance,2),'right');

				$YPos -= ($line_height);

				if ($YPos <= $Bottom_Margin) {

						/* head up a new invoice/credit note page */
						/*draw the vertical column lines right to the bottom */
						PrintLinesToBottom ();
					include('includes/PDFExpensesHeader2.php');
					} //end if need a new page headed up

				} //end while there invoice are line items to print out
			} /*end if there are stock movements to show on the invoice or credit note*/

			$YPos -= $line_height;

			/* check to see enough space left to print the 4 lines for the totals/footer */
			if (($YPos-$Bottom_Margin)<(2*$line_height)) {
				PrintLinesToBottom ();
				include('includes/PDFExpensesHeader2.php');
			}
			/* Print a column vertical line  with enough space for the footer */
			/* draw the vertical column lines to 4 lines shy of the bottom to leave space for invoice footer info ie totals etc */
			
			/* Print a column vertical line */
			$pdf->line($Left_Margin+210, $TopOfColHeadings+12,$Left_Margin+210,$Bottom_Margin+(1.6*$line_height));
            /* Print a column vertical line */
			$pdf->line($Left_Margin+428, $TopOfColHeadings+39,$Left_Margin+428,$Bottom_Margin+(1.6*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+505, $TopOfColHeadings+12,$Left_Margin+505,$Bottom_Margin+(1.6*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+570, $TopOfColHeadings+12,$Left_Margin+570,$Bottom_Margin+(1.6*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+640, $TopOfColHeadings+12,$Left_Margin+640,$Bottom_Margin+(1.6*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+695, $TopOfColHeadings+12,$Left_Margin+695,$Bottom_Margin+(1.6*$line_height));

			/* Print a column vertical line */
			//$pdf->line($Left_Margin+550, $TopOfColHeadings+12,$Left_Margin+550,$Bottom_Margin+(4*$line_height));

			/* Print a column vertical line */
			
			//$pdf->line($Left_Margin+587, $TopOfColHeadings+12,$Left_Margin+587,$Bottom_Margin+(4*$line_height));
			
			/* Print a column vertical line */

			//$pdf->line($Left_Margin+670, $TopOfColHeadings+12,$Left_Margin+670,$Bottom_Margin+(4*$line_height));

			/* Rule off at bottom of the vertical lines */
			$pdf->line($Left_Margin, $Bottom_Margin+(4*$line_height),$Page_Width-$Right_Margin,$Bottom_Margin+(4*$line_height));
			/* Now print out the footer and totals */
			/* Print out the invoice text entered */
			$YPos = $Bottom_Margin+(3*$line_height);

		//      $pdf->addText($Page_Width-$Right_Margin-392, $YPos - ($line_height*3)+22,$FontSize, _('Bank Code:***** Bank Account:*****'));
		//	$FontSize=10;

			$FontSize =8;
			
			if (mb_strlen($LeftOvers)>0) {
				$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-24,280,$FontSize,$LeftOvers);
				if (mb_strlen($LeftOvers)>0) {
					$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-36,280,$FontSize,$LeftOvers);
					/*If there is some of the InvText leftover after 3 lines 200 wide then it is not printed :( */
				}
			}
			$FontSize = 10;
			/*rule off for total */
			$pdf->line($Page_Width-$Right_Margin-771, $YPos-(2*$line_height)+10,$Page_Width-$Right_Margin,$YPos-(2*$line_height)+10);
			/*vertical to separate totals from comments and ROMALPA  added by langat pete*/
			$pdf->line($Page_Width-$Right_Margin-77, $YPos+$line_height,$Page_Width-$Right_Margin-77,$Bottom_Margin);

			$YPos+=10;			    
				//$pdf->addText($Page_Width-$Right_Margin-760, $YPos - ($line_height*2)+20,$FontSize, 'TOTAL : ','right'); //total field/*
				//$pdf->addText($Page_Width-$Right_Margin-153, $YPos - ($line_height*2)+20,$FontSize,locale_number_format($commpayments,2),'right');
				//$pdf->addText($Page_Width-$Right_Margin-235, $YPos - ($line_height*2)+20,$FontSize,locale_number_format($alloc,2),'right');
				//$pdf->addText($Page_Width-$Right_Margin-345, $YPos - ($line_height*2)+20,$FontSize,locale_number_format($totalfund,2),'right');
				//$pdf->addText($Page_Width-$Right_Margin-386, $YPos - ($line_height*2)+20,$FontSize,locale_number_format($totalallocations,2),'right');
				//$pdf->addText($Page_Width-$Right_Margin-917, $YPos  - ($line_height*2)+20,$FontSize,$MainTot,'right');
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+672,$YPos - ($line_height*2)+20,100,$FontSize,$MainTot,'right');
				$FontSize=9;
				$YPos-=4;
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-=35,90,$FontSize,'Description :');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+60,$YPos,400,$FontSize,$myrow['comment']);
				while (mb_strlen($LeftOvers)>0 AND $YPos > $Bottom_Margin) {
					$YPos-=12;
					$LeftOvers = $pdf->addTextWrap($Left_Margin+60,$YPos,400,$FontSize,$LeftOvers);
				}
				
				/*print out bank details */
				$YPos-=45;
				$status ='FY'.'&nbsp;'.Date('Y',YearEndDate($_SESSION['YearEnd'],-1)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd']));
				$original ='VOTE BOOK SUB-ALLOCATION STATUS RETURNS AS AT '.date($_SESSION['DefaultDateFormat']).'';
				//$date = .date("d, M Y"), 'right').;
				
				$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,400,$FontSize,' ' . _($status));
                $LeftOvers = $pdf->addTextWrap($Left_Margin+80,$YPos,400,$FontSize,' ' . _($original));
		$pdf->OutputI($_SESSION['DatabaseName'] . '_Expenses Report_.pdf');
		//$tempname = date(DATE_ATOM);
		//$tempname = str_replace(":", "_", $tempname);
		//$pdf->OutputF('C:/Invoices/'.$_SESSION['DatabaseName'] . '_' . $InvOrCredit . '_' . $FromTransNo . '_' . $tempname . '.pdf');
	$pdf->__destruct();
	//Now change the language back to the user's language
	$_SESSION['Language'] = $UserLanguage;
	include('includes/LanguageSetup.php');
function PrintLinesToBottom () {

	global $pdf;
	global $PageNumber;
	global $TopOfColHeadings;
	global $Left_Margin;
	global $Bottom_Margin;
	global $line_height;

			$pdf->line($Left_Margin+240, $TopOfColHeadings+12,$Left_Margin+240,$Bottom_Margin+(0.0*$line_height));
            /* Print a column vertical line */
			$pdf->line($Left_Margin+300, $TopOfColHeadings+12,$Left_Margin+300,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+380, $TopOfColHeadings+12,$Left_Margin+380,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+460, $TopOfColHeadings+12,$Left_Margin+460,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+540, $TopOfColHeadings+39,$Left_Margin+540,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+615, $TopOfColHeadings+12,$Left_Margin+615,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+695, $TopOfColHeadings+12,$Left_Margin+695,$Bottom_Margin+(0.0*$line_height));			
			$PageNumber++;
}
			  
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

echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<table class="selection" style=font-size:10pt>

<tr><td>',
		_('Show all Allocation From'), ':<input alt="', $_SESSION['DefaultDateFormat'], '" class="date" id="datepicker" maxlength="10" minlength="0" name="TransAfterDate" required="required" size="12" tabindex="1" type="text" value="', $_POST['TransAfterDate'], '" />',_('To'), ':<input alt="', $_SESSION['DefaultDateFormat'], '" class="date" id="datepicker" maxlength="10" minlength="0" name="TransToDate" required="required" size="12" tabindex="1" type="text" value="', $_POST['TransToDate'], '" />

		</td></tr>

</tr></table>';
echo '<div class="centre"><input type="submit" name="Search" value="' . _('Search Now') . '" /></div><br />
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
		///////////////////////////////edited by langat peter/////////////////////////////////////////
			$SQL = "SELECT *,b.Votehead as fromx, c.Votehead as tox, a.To_Votecode as vote
			           FROM supplmentarytracking a
					   INNER JOIN voteheadmaintenance b ON a.From_Votecode=b.Votecode
					   INNER JOIN voteheadmaintenance c ON a.To_Votecode=c.Votecode
					   LEFT JOIN www_users d ON a.user=d.userid
					   WHERE a.alloc_Date >='" . FormatDateForSQL($_POST['TransAfterDate']) . "'
					   AND a.alloc_Date <='" . FormatDateForSQL($_POST['TransToDate']) . "'";
					   
	$ErrMsg = _('No stock items were returned by the SQL because');
	$DbgMsg = _('The SQL that returned an error was');
	$SearchResult = DB_query($SQL, $ErrMsg, $DbgMsg);
	if (DB_num_rows($SearchResult) == 0) {
		prnMsg(_('There is no funds Re-Allocation made on the selected date,try Again'), 'info');
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
		                    <th>' . _('') . '</th>
							<th class="ascending">' . _('From Vote Head.') . '</th>
		                    <th class="ascending">' . _('To Vote Head') . '</th>
							<th class="ascending">' . _('Original Amnt') . '</th>
							<th class="ascending">' . _('Amnt Re-Allocated') . '</th>
							<th class="ascending">' . _('Total Amnt') . '</th>
							 <th class="ascending">' . _('Date Re allocated') . '</th>
							 <th class="ascending">' . _('User') . '</th>
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
			 $sql1 = "SELECT * FROM  funds_allocations WHERE votecode=".$myrow['vote']." AND Financial_Year='".Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],+0))."'";
		$result1 = DB_query($sql1);
		$Row = DB_fetch_array($result1);
		   $totalalloc=locale_number_format($Row['allocated_Fund']+$myrow['Amount'],2);			   
		  echo '<td style=font-size:10pt>' . $i . '</td> 
		        <td>' . $myrow['fromx'] . '</td>
		        <td>' . $myrow['tox'] . '</td>
				<td>' . locale_number_format($Row['allocated_Fund'],2) . '</td>
		        <td>' . locale_number_format($myrow['Amount'],2) . '</td>
				<td>' . $totalalloc . '</td>
				<td>' . ConvertSQLDate($myrow['alloc_Date']) . '</td>
				<td>' . $myrow['realname'] . '</td>';
				echo'</tr>';
				

	$j++;

			if ($j == 30 AND ($RowIndex + 1 != $_SESSION['DisplayRecordsMax'])) {
				$j = 1;
				echo $TableHeader;
			}
		
			$RowIndex = $RowIndex + 1;
			
			//end of page full new headings if
		}
		//end of while loop
			
		echo '</table>
              </div>
	 <div class="centre">
						<input type="submit" name="PDF" value="' . _('Print PDF') . '" /><br><br><br>
					</div>';
					
					echo'</form>					
              <br />';
			
    
        }
    }
 
/* end display list if there is more than one record */
include ('includes/footer.inc');
?>