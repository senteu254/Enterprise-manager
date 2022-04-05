<?php

/* $Id: PDFChequeListing.php 6943 2014-10-27 07:06:42Z daintree $*/

include('includes/SQL_CommonFunctions.inc');
include ('includes/session.inc');
$ViewTopic= 'GeneralLedger';
$BookMark = 'TransactiontListing';

$InputError=0;
if (isset($_POST['PrintPDF'])) {
	$PaperSize='A4_Landscape';
}

if (isset($_GET['PrintPDF'])) {
	$PrintPDF = $_GET['PrintPDF'];
	$_POST['PrintPDF'] = $PrintPDF;
	$PaperSize='A4_Landscape';
}
if (isset($_GET['SupplierID'])) {
		$_SESSION['SupplierID'] = stripslashes($_GET['SupplierID']);
	}
	$SupplierID = $_SESSION['SupplierID'];
if (isset($_POST['PrintPDF'])){
	

	include('includes/PDFStarter.php');
	$pdf->addInfo('Title', _('Supplier Statements') );
	$pdf->addInfo('Subject', _('Statements from') . ' ' . $_POST['SupplierID'] . ' ' . _('to') . ' ' . $_POST['SupplierID']);
	$PageNumber = 1;
	$line_height=16;

	$FirstStatement = True;
	
	$ErrMsg = _('There was a problem settling the old transactions.');
	$DbgMsg = _('The SQL used to settle outstanding transactions was');
	$sql = "UPDATE supptrans SET settled=1
			WHERE ABS(supptrans.ovamount+supptrans.ovgst-supptrans.alloc)<0.009";
	$SettleAsNec = DB_query($sql, $ErrMsg, $DbgMsg);

/*Figure out who all the suppliers in this range are */
	$ErrMsg= _('There was a problem retrieving the customer information for the statements from the database');
	$sql = "SELECT suppliers.supplierid,
				suppliers.suppname,
				suppliers.address1,
				suppliers.address2,
				suppliers.address3,
				suppliers.address4,
				suppliers.address5,
				suppliers.address6,
				suppliers.lastpaid,
				suppliers.lastpaiddate,
				currencies.currency,
				currencies.decimalplaces AS currdecimalplaces,
				paymentterms.terms
			FROM suppliers INNER JOIN currencies
				ON suppliers.currcode=currencies.currabrev
			INNER JOIN paymentterms
				ON suppliers.paymentterms=paymentterms.termsindicator
			WHERE suppliers.supplierid='" . $SupplierID ."'
			ORDER BY suppliers.supplierid";
	$StatementResults=DB_query($sql, $ErrMsg);

	if (DB_Num_Rows($StatementResults) == 0){
		$Title = _('Print Statements') . ' - ' . _('No Supplier Found');
	    require('includes/header.inc');
		echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/printer.png" title="' . _('Print') . '" alt="" />' . ' ' . _('Print Supplier Account Statements') . '</p>';
	prnMsg(_('Your account enables you to see only customers allocated to you'), 'warn', _('Note: Sales-person Login'));
//		echo '</div>';
		include('includes/footer.inc');
		exit();
	}

	while ($StmtHeader=DB_fetch_array($StatementResults)){	 /*loop through all the customers returned */
       
	/*now get all the outstanding transaction ie Settled=0 */
		$ErrMsg =  _('There was a problem retrieving the outstanding transactions for') . ' ' .	$StmtHeader['suppname'] . ' '. _('from the database') . '.';
		$sql = "SELECT systypes.typename,
					supptrans.transno,
					supptrans.type,
					supptrans.trandate,
					supptrans.status,
					supptrans.transtate,
					supptrans.ovamount+supptrans.ovgst as total,
					supptrans.alloc,
					supptrans.ovamount+supptrans.ovgst-supptrans.alloc as ostdg
				FROM supptrans INNER JOIN systypes
					ON supptrans.type=systypes.typeid
				WHERE supptrans.supplierno='" .  $SupplierID . "'
				AND supptrans.settled=0
				AND supptrans.transtate=0
				AND supptrans.trandate >='" . FormatDateForSQL($_POST['TransAfterDate']) . "'
				AND supptrans.trandate<='" . FormatDateForSQL($_POST['TransToDate']) . "'";

		/*if ($_SESSION['SalesmanLogin'] != '') {
			$sql .= " AND debtortrans.salesperson='" . $_SESSION['SalesmanLogin'] . "'";
		}*/

		$sql .= " ORDER BY supptrans.id";

		$OstdgTrans=DB_query($sql, $ErrMsg);

	   	$NumberOfRecordsReturned = DB_num_rows($OstdgTrans);

/*now get all the settled transactions which were allocated this month */
		$ErrMsg = _('There was a problem retrieving the transactions that were settled over the course of the last month for'). ' ' . $StmtHeader['suppname'] . ' ' . _('from the database');
	   	if ($_SESSION['Show_Settled_LastMonth']==1){
	   		$sql = "SELECT DISTINCT supptrans.id,
								systypes.typename,
								supptrans.transno,
								supptrans.trandate,
								supptrans.suppreference,
								supptrans.status,
								supptrans.transtate,
								supptrans.ovamount+supptrans.ovgst AS total,
								supptrans.alloc,
								supptrans.ovamount+supptrans.ovgst-supptrans.alloc AS ostdg,
								supptrans.ovamount+supptrans.ovgst AS ostdg2
						FROM supptrans INNER JOIN systypes
							ON supptrans.type=systypes.typeid
						INNER JOIN suppallocs
							ON (supptrans.id=suppallocs.transid_allocfrom
								OR supptrans.id=suppallocs.transid_allocto)
						WHERE supptrans.supplierno='" . $SupplierID . "'
						AND supptrans.settled=1
						AND supptrans.transtate=0
						AND suppallocs.datealloc >='" . FormatDateForSQL($_POST['TransAfterDate']) . "'
				        AND suppallocs.datealloc<='" . FormatDateForSQL($_POST['TransToDate']) . "'
						AND supptrans.trandate >='" . FormatDateForSQL($_POST['TransAfterDate']) . "'
				        AND supptrans.trandate<='" . FormatDateForSQL($_POST['TransToDate']) . "'
						AND supptrans.status=0";

			$sql .= " ORDER BY supptrans.id";

			$SetldTrans=DB_query($sql, $ErrMsg);
			$NumberOfRecordsReturned += DB_num_rows($SetldTrans);       
	   	}		

	  	if ( $NumberOfRecordsReturned >=1){

		/* Then there's a statement to print. So print out the statement header from the company record */

	      		$PageNumber =1;

			if ($FirstStatement==True){
				$FirstStatement=False;
	      		} else {
				$pdf->newPage();
	      		}
	      		include('includes/PDFSupplierStatementPageHeader.inc');


			if ($_SESSION['Show_Settled_LastMonth']==1){
				if (DB_num_rows($SetldTrans)>=1) {

					$FontSize=12;
					$pdf->addText($Left_Margin+1,$YPos+5,$FontSize, _('Settled Transactions'));

					$YPos -= (2*$line_height);

					$FontSize=10;

					while ($myrow=DB_fetch_array($SetldTrans)){
					
                         $tttt=$myrow['ostdg2'];
						$DisplayAlloc = locale_number_format($myrow['alloc'],$StmtHeader['currdecimalplaces']);
						$DisplayOutstanding = locale_number_format($myrow['ostdg'],$StmtHeader['currdecimalplaces']);
                        $balance+=$myrow['alloc'];
						$total_payed+=$myrow['ostdg'];
						$cbalance=locale_number_format($balance,$StmtHeader['currdecimalplaces']);
						$FontSize=9;

						$LeftOvers = $pdf->addTextWrap($Left_Margin+1,$YPos,110,$FontSize, _($myrow['typename']), 'left');
						$LeftOvers = $pdf->addTextWrap($Left_Margin+120,$YPos,80,$FontSize,$myrow['suppreference'], 'left');
						$LeftOvers = $pdf->addTextWrap($Left_Margin+216,$YPos,50,$FontSize,ConvertSQLDate($myrow['trandate']), 'left');

						$FontSize=10;
						if ($myrow['total']>0){
							$DisplayTotal = locale_number_format($myrow['total'],$StmtHeader['currdecimalplaces']);
							$LeftOvers = $pdf->addTextWrap($Left_Margin+300,$YPos,60,$FontSize,$DisplayTotal, 'right');
						} else {
							$DisplayTotal = locale_number_format(-$myrow['total'],$StmtHeader['currdecimalplaces']);
							$LeftOvers = $pdf->addTextWrap($Left_Margin+382,$YPos,60,$FontSize,$DisplayTotal, 'right');
						}
						$LeftOvers = $pdf->addTextWrap($Left_Margin+459,$YPos,60,$FontSize,$DisplayAlloc, 'right');
						$LeftOvers = $pdf->addTextWrap($Left_Margin+536,$YPos,60,$FontSize,$DisplayOutstanding, 'right');
						
						/*Now show also in the remittance advice sectin */
						$LeftOvers = $pdf->addTextWrap($Perforation+10,$YPos,90,$FontSize, _($myrow['transno']), 'left');
				        //$LeftOvers = $pdf->addTextWrap($Perforation+75,$YPos,30,$FontSize,$myrow['transno'], 'left');
						$LeftOvers = $pdf->addTextWrap($Perforation+90,$YPos,60,$FontSize,$cbalance, 'right');

						if ($YPos-$line_height <= $Bottom_Margin){
		/* head up a new statement page */

							$PageNumber++;
							$pdf->newPage();
							include ('includes/PDFSupplierStatementPageHeader.inc');
						} //end if need a new page headed up

						/*increment a line down for the next line item */
						$YPos -= ($line_height);

					} //end while there transactions settled this month to print out
				}
			} // end of if there are transaction that were settled this month

	      		if (DB_num_rows($OstdgTrans)>=1){

		      		$YPos -= ($line_height);
				if ($YPos-(2 * $line_height) <= $Bottom_Margin){
					$PageNumber++;
					$pdf->newPage();
					include ('includes/PDFSupplierStatementPageHeader.inc');
				}
			/*Now the same again for outstanding transactions */

			$FontSize=12;
			$pdf->addText($Left_Margin+1,$YPos+20,$FontSize, _('Outstanding Transactions') );
			$YPos -= $line_height;

			while ($myrow=DB_fetch_array($OstdgTrans)){

				$DisplayAlloc = locale_number_format($myrow['alloc'],$StmtHeader['currdecimalplaces']);
				$DisplayOutstanding = locale_number_format($myrow['ostdg'],$StmtHeader['currdecimalplaces']);                
				
				$totalpayed =1*($myrow['ostdg']);
				$cpayment=locale_number_format($totalpayed,$StmtHeader['currdecimalplaces']);
						
				$FontSize=9;
				$LeftOvers = $pdf->addTextWrap($Left_Margin+1,$YPos,110,$FontSize, _($myrow['typename']), 'left');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+135,$YPos,50,$FontSize,$myrow['transno'], 'left');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+211,$YPos,50,$FontSize,ConvertSQLDate($myrow['trandate']), 'left');

				$FontSize=10;
				if ($myrow['total']>0){
					$DisplayTotal = locale_number_format($myrow['total'],$StmtHeader['currdecimalplaces']);
					$LeftOvers = $pdf->addTextWrap($Left_Margin+300,$YPos,55,$FontSize,$DisplayTotal, 'right');
				} else {
					$DisplayTotal = locale_number_format(-$myrow['total'],$StmtHeader['currdecimalplaces']);
					$LeftOvers = $pdf->addTextWrap($Left_Margin+382,$YPos,55,$FontSize,$DisplayTotal, 'right');
				}

				$LeftOvers = $pdf->addTextWrap($Left_Margin+459,$YPos,59,$FontSize,$DisplayAlloc, 'right');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+536,$YPos,60,$FontSize,$DisplayOutstanding, 'right');

				/*Now show also in the remittance advice sectin */
				$FontSize=8;
				$LeftOvers = $pdf->addTextWrap($Perforation+10,$YPos,90,$FontSize, _($myrow['typename']), 'left');
				//$LeftOvers = $pdf->addTextWrap($Perforation+75,$YPos,30,$FontSize,$myrow['transno'], 'left');
				$LeftOvers = $pdf->addTextWrap($Perforation+90,$YPos,60,$FontSize,$cpayment, 'right');

				if ($YPos-$line_height <= $Bottom_Margin){
		/* head up a new statement page */

					$PageNumber++;
					$pdf->newPage();
					include ('includes/PDFSupplierStatementPageHeader.inc');
				} //end if need a new page headed up

				/*increment a line down for the next line item */
				$YPos -= ($line_height);

			} //end while there are outstanding transaction to print
		} // end if there are outstanding transaction to print


		/* check to see enough space left to print the totals/footer
		which is made up of 2 ruled lines, the totals/aging another 2 lines
		and details of the last payment made - in all 6 lines */
		if (($YPos-$Bottom_Margin)<(4*$line_height)){

		/* head up a new statement/credit note page */
			$PageNumber++;
			$pdf->newPage();
			include ('includes/PDFSupplierStatementPageHeader.inc');
		}
		
			/*Now figure out the aged analysis for the customer under review */

		$SQL = "SELECT suppliers.suppname,
						currencies.currency,
						paymentterms.terms,
						supptrans.status,
						supptrans.transtate,
						SUM(supptrans.ovamount + supptrans.ovgst - supptrans.alloc) AS balance,
						SUM(CASE WHEN paymentterms.daysbeforedue > 0 THEN
							CASE WHEN (TO_DAYS(Now()) - TO_DAYS(supptrans.trandate)) >=
							paymentterms.daysbeforedue
							THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc
							ELSE 0 END
						ELSE
							CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . interval('1', 'MONTH') . "), " . interval('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))','DAY') . ")) >= 0
							THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc
							ELSE 0 END
						END) AS due,
						Sum(CASE WHEN paymentterms.daysbeforedue > 0 THEN
							CASE WHEN TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) > paymentterms.daysbeforedue
							AND TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) >=
							(paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ")
							THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc
							ELSE 0 END
						ELSE
							CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . interval('1','MONTH') . "), " . interval('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))','DAY') .")) >= " . $_SESSION['PastDueDays1'] . ")
							THEN supptrans.ovamount + supptrans.ovgst- supptrans.alloc
							ELSE 0 END
						END) AS overdue1,
						Sum(CASE WHEN paymentterms.daysbeforedue > 0 THEN
							CASE WHEN TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) > paymentterms.daysbeforedue
							AND TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) >= (paymentterms.daysbeforedue +
							" . $_SESSION['PastDueDays2'] . ")
							THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc
							ELSE 0 END
						ELSE
							CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . interval('1','MONTH') . "), " .
							interval('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))','DAY') . "))
							>= " . $_SESSION['PastDueDays2'] . ")
							THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc
							ELSE 0 END
						END) AS overdue2
					FROM suppliers INNER JOIN paymentterms
						ON suppliers.paymentterms = paymentterms.termsindicator
					INNER JOIN currencies
						ON suppliers.currcode = currencies.currabrev
					INNER JOIN supptrans
						ON suppliers.supplierid = supptrans.supplierno
					WHERE
			  		suppliers.supplierid = '" .  $SupplierID . "'
					AND supptrans.status=0";                    

			$sql .= " GROUP BY
						suppliers.name,
						currencies.currency,
						paymentterms.terms,
						paymentterms.daysbeforedue,
						paymentterms.dayinfollowingmonth";

			$ErrMsg = 'The Supplier details could not be retrieved by the SQL because';
			$CustomerResult = DB_query($SQL);
      	
		/*there should be only one record returned ?? */
			$AgedAnalysis = DB_fetch_array($CustomerResult,$db);

		/*Now print out the footer and totals */
            $payed=($AgedAnalysis['ovamount']+$AgedAnalysis['ovgst'])-$AgedAnalysis['alloc'];
			$payed2=$AgedAnalysis['ovamount'];
			$pay+=$payed2;
			$DisplayDue = locale_number_format($AgedAnalysis['due']-$AgedAnalysis['overdue1'],$StmtHeader['currdecimalplaces']);
			$DisplayCurrent = locale_number_format($AgedAnalysis['balance']-$AgedAnalysis['due'],$StmtHeader['currdecimalplaces']);
			$DisplayBalance = locale_number_format($AgedAnalysis['balance'],$StmtHeader['currdecimalplaces']);
			$DisplayOverdue1 = locale_number_format($AgedAnalysis['overdue1']-$AgedAnalysis['overdue2'],$StmtHeader['currdecimalplaces']);
			$DisplayOverdue2 = locale_number_format($AgedAnalysis['overdue2'],$StmtHeader['currdecimalplaces']);


			$pdf->line($Page_Width-$Right_Margin, $Bottom_Margin+(4*$line_height),$Left_Margin,$Bottom_Margin+(4*$line_height));

			$FontSize=10;


			$pdf->addText($Left_Margin+75, ($Bottom_Margin+10)+(3*$line_height)+4, $FontSize, _('Current'). ' ');
			$pdf->addText($Left_Margin+158, ($Bottom_Margin+10)+(3*$line_height)+4, $FontSize, _('Past Due').' ');
			$pdf->addText($Left_Margin+242, ($Bottom_Margin+10)+(3*$line_height)+4, $FontSize, $_SESSION['PastDueDays1'] . '-' . $_SESSION['PastDueDays2'] . ' ' . _('days') );
			$pdf->addText($Left_Margin+315, ($Bottom_Margin+10)+(3*$line_height)+4, $FontSize, _('Over').' ' . $_SESSION['PastDueDays2'] . ' '. _('days'));
			$pdf->addText($Left_Margin+442, ($Bottom_Margin+10)+(3*$line_height)+4, $FontSize, _('Total Balance') );

			$LeftOvers = $pdf->addTextWrap($Left_Margin+37, $Bottom_Margin+(2*$line_height)+8,70,$FontSize,$DisplayCurrent, 'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+130, $Bottom_Margin+(2*$line_height)+8,70,$FontSize,$DisplayDue, 'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+222, $Bottom_Margin+(2*$line_height)+8,70,$FontSize,$DisplayOverdue1, 'right');

			$LeftOvers = $pdf->addTextWrap($Left_Margin+305, $Bottom_Margin+(2*$line_height)+8,70,$FontSize,$DisplayOverdue2, 'right');

			$LeftOvers = $pdf->addTextWrap($Left_Margin+432, $Bottom_Margin+(2*$line_height)+8,70,$FontSize,$cpayment, 'right');


			/*draw a line under the balance info */
			$YPos = $Bottom_Margin+(2*$line_height);
			$pdf->line($Left_Margin, $YPos,$Perforation,$YPos);


			if (mb_strlen($StmtHeader['lastpaiddate'])>1 AND $StmtHeader['lastpaid']!=0){
				$pdf->addText($Left_Margin+5, $Bottom_Margin+13, $FontSize, _('Last payment received').' ' . ConvertSQLDate($StmtHeader['lastpaiddate']) .
					'    ' . _('Amount received was').' ' . locale_number_format($StmtHeader['lastpaid'],$StmtHeader['currdecimalplaces']));

			}

			/* Show the bank account details */
			$pdf->addText($Perforation-250, $Bottom_Margin+32, $FontSize, _('Please make payments to our account:') . ' ' . $DefaultBankAccountNumber);
			$pdf->addText($Perforation-250, $Bottom_Margin+32-$line_height, $FontSize, _('Quoting your account reference') . ' ' . $StmtHeader['debtorno'] );

			/*also show the total due in the remittance section */
			if ($AgedAnalysis['balance']>0){ /*No point showing a negative balance for payment! */
					$FontSize=8;
					$LeftOvers = $pdf->addTextWrap($Perforation+2, $Bottom_Margin+(2*$line_height)+8,80,$FontSize, _('Total Balance'), 'left');
					$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-90, $Bottom_Margin+(2*$line_height)+8,88,$FontSize,$DisplayBalance, 'right');
					

			}
           $LeftOvers = $pdf->addTextWrap($Perforation+2, $Bottom_Margin+(2*$line_height)-10,80,$FontSize, _('Total payed'), 'left');
			$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-90, $Bottom_Margin+(2*$line_height)-10,88,$FontSize,$tttt, 'right');
		} /* end of check to see that there were statement transactons to print */

	} /* end loop to print statements */

	if (isset($pdf)){

        $pdf->OutputI($_SESSION['DatabaseName'] . '_SuppStatements_' . date('Y-m-d') . '.pdf');
        $pdf->__destruct();

	} else {
		$Title = _('Print Statements') . ' - ' . _('No Statements Found');
		include('includes/header.inc');
		echo '<br /><br /><br />' . prnMsg( _('There were no statements to print') );
	        echo '<br /><br /><br />';
	        include('includes/footer.inc');
	}

}

	
	$sql36 = "SELECT * FROM suppliers WHERE supplierid='" . $SupplierID ."'";
	$Result22 = DB_query($sql36);
	 $Title = _('Print PDF Transaction Listing For:');
	 while ($row22=DB_fetch_array($Result22)){
	 $Title2 = $row22['suppname'] ;
	 }
	 include ('includes/header.inc');

	echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/money_add.png" title="' .
		 $Title . '" alt="" />' . ' ' . $Title . '' . ' ' . $Title2 . '</p>';
   
	if ($InputError==1){
		prnMsg($msg,'error');
	}
  
	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
if (!isset($_POST['TransAfterDate']))  {
	$_POST['TransAfterDate'] = Date($_SESSION['DefaultDateFormat'], Mktime(0, 0, 0, Date('m') - $_SESSION['NumberOfMonthMustBeShown'], Date('d'), Date('Y')) );
}
if (!isset($_POST['TransToDate']))  {
	$_POST['TransToDate'] = Date($_SESSION['DefaultDateFormat'], Mktime(0, 0, 0, Date('m')) );
}
	echo '<div><input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" /></div>';
	echo '<table class="selection">

	 		<tr>
				<td>' . _('Transaction From') . ':</td>
				<td><input alt="', $_SESSION['DefaultDateFormat'], '" class="date" id="datepicker" maxlength="10" minlength="0" name="TransAfterDate" required="required" size="12" tabindex="1" type="text" value="', $_POST['TransAfterDate'], '" /></td>
			</tr>';
	 echo '<tr><td>' . _('Transaction To') . ':</td>
	 		<td><input alt="', $_SESSION['DefaultDateFormat'], '" class="date" id="datepicker" maxlength="10" minlength="0" name="TransToDate" required="required" size="12" tabindex="1" type="text" value="', $_POST['TransToDate'], '" /></td>
	</tr>
	
			</table>
			<div class="centre">
                <br />
				<input type="submit" name="PrintPDF" value="' . _('Print PDF') . '" />
			</div>
            </form>';

	 include('includes/footer.inc');
	 exit;




