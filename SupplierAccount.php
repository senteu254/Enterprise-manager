<?php
/* $Id: CustomerAccount.php 7004 2014-11-24 15:56:19Z rchacon $*/
/* Shows customer account/statement on screen rather than PDF. */

include('includes/session.inc');
$Title = _('Supplier Account');// Screen identification.
$ViewTopic = 'ARInquiries';// Filename in ManualContents.php's TOC.
$BookMark = 'SupplierAccount';// Anchor's id in the manual's html document.
include('includes/header.inc');

// always figure out the SQL required from the inputs available

if (!isset($_GET['SupplierID']) and !isset($_SESSION['SupplierID'])) {
	prnMsg(_('To display the account a Supplier must first be selected from the customer selection screen'), 'info');
	echo '<br /><div class="centre"><a href="', $RootPath, '/Selectsupplier.php">', _('Select a Suplier Account to Display'), '</a></div>';
	include('includes/footer.inc');
	exit;
} else {
	if (isset($_GET['SupplierID'])) {
		$_SESSION['SupplierID'] = stripslashes($_GET['SupplierID']);
	}
	$SupplierID = $_SESSION['SupplierID'];
}
//Check if the users have proper authority
/*if ($_SESSION['SalesmanLogin'] != '') {
	$ViewAllowed = false;
	$sql = "SELECT salesman FROM custbranch WHERE debtorno = '" . $CustomerID . "'";
	$ErrMsg = _('Failed to retrieve sales data');
	$result = DB_query($sql,$ErrMsg);
	if(DB_num_rows($result)>0) {
		while($myrow = DB_fetch_array($result)) {
			if ($_SESSION['SalesmanLogin'] == $myrow['salesman']) {
				$ViewAllowed = true;
			}
		}
	} else {
		prnMsg(_('There is no salesman data set for this customer'),'error');
		include('includes/footer.inc');
		exit;
	}
	if (!$ViewAllowed) {
		prnMsg(_('You have no authority to review this customer account'),'error');
		include('includes/footer.inc');
		exit;
	}
}
*/

if (!isset($_POST['TransAfterDate']))  {
	$_POST['TransAfterDate'] = Date($_SESSION['DefaultDateFormat'], Mktime(0, 0, 0, Date('m') - $_SESSION['NumberOfMonthMustBeShown'], Date('d'), Date('Y')) );
}
if (!isset($_POST['TransToDate']))  {
	$_POST['TransToDate'] = Date($_SESSION['DefaultDateFormat'], Mktime(0, 0, 0, Date('m')) );
}
$Transactions = array();

/*now get all the settled transactions which were allocated this month */
$ErrMsg = _('There was a problem retrieving the transactions that were settled over the course of the last month for'). ' ' . SupplierID. ' ' . _('from the database');
if ($_SESSION['Show_Settled_LastMonth']==1) {
	$sql = "SELECT DISTINCT supptrans.id,
						supptrans.type,
						systypes.typename,
						supptrans.suppreference,
						supptrans.transtext,
						supptrans.OrderNo,
						supptrans.transno,
						supptrans.trandate,
						supptrans.ovamount+supptrans.ovgst AS totalamount,
						supptrans.alloc,
						supptrans.ovamount+supptrans.ovgst-supptrans.alloc AS balance,
						supptrans.settled
				FROM supptrans INNER JOIN systypes
					ON supptrans.type=systypes.typeid
				INNER JOIN suppallocs
					ON (supptrans.id=suppallocs.transid_allocfrom
						OR supptrans.id=suppallocs.transid_allocto)
				WHERE suppallocs.datealloc >='" . FormatDateForSQL($_POST['TransAfterDate']) . "'
				AND suppallocs.datealloc <='" . FormatDateForSQL($_POST['TransToDate']) . "'
				AND supptrans.supplierno='" . $SupplierID . "'
				AND supptrans.settled=1
				ORDER BY supptrans.id";

	$SetldTrans=DB_query($sql, $ErrMsg);
	$NumberOfRecordsReturned = DB_num_rows($SetldTrans);
	while ($myrow=DB_fetch_array($SetldTrans)) {
		$Transactions[] =  $myrow;
	}
} else {
	$NumberOfRecordsReturned=0;
}

/*now get all the outstanding transaction ie Settled=0 */
$ErrMsg =  _('There was a problem retrieving the outstanding transactions for') . ' ' .	$SupplierID . ' '. _('from the database') . '.';
$sql = "SELECT supptrans.id,
			supptrans.type,
			systypes.typename,
			supptrans.suppreference,
			supptrans.transtext,
			supptrans.OrderNo,
			supptrans.transno,
			supptrans.trandate,
			supptrans.ovamount+supptrans.ovgst as totalamount,
			supptrans.alloc,
			supptrans.ovamount+supptrans.ovgst-supptrans.alloc as balance,
			supptrans.settled
		FROM supptrans INNER JOIN systypes
			ON supptrans.type=systypes.typeid
		WHERE supptrans.supplierno='" . $SupplierID . "'
		AND supptrans.settled=0";



$sql .= " ORDER BY supptrans.id";

$OstdgTrans=DB_query($sql, $ErrMsg);
while ($myrow=DB_fetch_array($OstdgTrans)) {
	$Transactions[] =  $myrow;
}

$NumberOfRecordsReturned += DB_num_rows($OstdgTrans);

$SQL = "SELECT suppliers.suppname,
			suppliers.address1,
			suppliers.address2,
			suppliers.address3,
			suppliers.address4,
			suppliers.address5,
			suppliers.address6,
			currencies.currency,
			currencies.decimalplaces,
			paymentterms.terms,
			SUM(supptrans.ovamount + supptrans.ovgst - supptrans.alloc) AS balance,
			SUM(CASE WHEN paymentterms.daysbeforedue > 0 THEN
				CASE WHEN (TO_DAYS(Now()) - TO_DAYS(supptrans.trandate)) >=
				paymentterms.daysbeforedue
				THEN supptrans.ovamount - supptrans.alloc
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
				THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc
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
			suppliers.supplierid = '" . $SupplierID . "'";


$SQL .= " GROUP BY
			suppliers.suppname,
			suppliers.address1,
			suppliers.address2,
			suppliers.address3,
			suppliers.address4,
			suppliers.address5,
			suppliers.address6,
			currencies.decimalplaces,
			currencies.currency,
			paymentterms.terms,
			paymentterms.daysbeforedue,
			paymentterms.dayinfollowingmonth";

$ErrMsg = _('The Supplier details could not be retrieved by the SQL because');
$CustomerResult = DB_query($SQL, $ErrMsg);

$CustomerRecord = DB_fetch_array($CustomerResult);

echo '<div class="noprint toplink">
		<a href="', $RootPath, '/SelectSupplier.php">', _('Back to Supplier Screen'), '</a>
	</div>';

echo '<table width="100%">
		<tr><th colspan="2">', _('Supplier Statement For'), ': ', stripslashes($SupplierID), ' - ', $CustomerRecord['suppname'], '</th></tr>
		<tr><td colspan="2">', $CustomerRecord['address1'], '</td></tr>';
if($CustomerRecord['address2']!='') {// If not empty, output this line.
	echo '<tr><td colspan="2">', $CustomerRecord['address2'], '</td></tr>';
}
if($CustomerRecord['address3']!='') {// If not empty, output this line.
	echo '<tr><td colspan="2">', $CustomerRecord['address3'], '</td></tr>';
}
echo '	<tr><td colspan="2">', $CustomerRecord['address4'], '</td></tr>
		<tr><td colspan="2">', $CustomerRecord['address5'], ' ', $CustomerRecord['address6'], '</td></tr>
		<tr><th>', _('All amounts stated in'), ':</th><td>', $CustomerRecord['currency'], '</td></tr>
		<tr><th>', _('Terms'), ':</th><td>', $CustomerRecord['terms'], '</th></tr></tr>
	</table>';

if ($CustomerRecord['dissallowinvoices'] != 0) {
	echo '<br /><b><font color="red" size="4">', _('ACCOUNT ON HOLD'), '</font></b><br />';
}
echo '<br /><form onSubmit="return VerifyForm(this);" action="', htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'), '" method="post" class="centre noprint">
		<input name="FormID" type="hidden" value="', $_SESSION['FormID'], '" />',
		_('Show all transactions From'), ':<input alt="', $_SESSION['DefaultDateFormat'], '" class="date" id="datepicker" maxlength="10" minlength="0" name="TransAfterDate" required="required" size="12" tabindex="1" type="text" value="', $_POST['TransAfterDate'], '" />',_('To'), ':<input alt="', $_SESSION['DefaultDateFormat'], '" class="date" id="datepicker" maxlength="10" minlength="0" name="TransToDate" required="required" size="12" tabindex="1" type="text" value="', $_POST['TransToDate'], '" />',
		' &nbsp; &nbsp;<input name="Refresh Inquiry" tabindex="3" type="submit" value="', _('Refresh Inquiry'), '" />
	</form>';

/* Show a table of the invoices returned by the SQL. */

echo '<br /><table class="selection">
	<thead>
		<tr>
			<th class="ascending">', _('Type'), '</th>
			<th class="ascending">', _('Number'), '</th>
			<th class="ascending">', _('Date'), '</th>
			<th>', _('Supplier No.'), '</th>
			<th class="ascending">', _('Reference'), '</th>
			<th>', _('Comments'), '</th>
			<th>', _('Order'), '</th>
			<th>', _('Total Amount'), '</th>
			<th>', _('Payment'), '</th>
			<th>', _('Allocated'), '</th>			
			<th>', _('Balance'), '</th>
			<th>', _('C/Payment'), '</th>
			<th class="noprint" colspan="4">&nbsp;</th>
			
		</tr>
	</thead><tbody>';

$k = 0; //row colour counter
$OutstandingOrSettled = '';
if ($_SESSION['InvoicePortraitFormat'] == 1) { //Invoice/credits in portrait
	$PrintCustomerTransactionScript = 'PrintCustTransPortrait.php';
} else { //produce pdfs in landscape
	$PrintCustomerTransactionScript = 'PrintCustTrans.php';
}
foreach ($Transactions as $MyRow) {

		if ($MyRow['settled']==1 AND $OutstandingOrSettled=='') {
		echo '<tr><th colspan="11">', _('TRANSACTIONS SETTLED SINCE'), ' ', $_POST['TransAfterDate'], _(' &nbsp;To'), '  &nbsp;', $_POST['TransToDate'], '</th><th class="noprint" colspan="4">&nbsp;</th></tr>';
		$OutstandingOrSettled='Settled';
	} elseif (($OutstandingOrSettled=='Settled' OR $OutstandingOrSettled=='') AND $MyRow['settled']==0) {
		echo '<tr><th colspan="11">', _('OUTSTANDING TRANSACTIONS'), ' ', $_POST['TransAfterDate'], _(' &nbsp;To'), '  &nbsp;', $_POST['TransToDate'], '</th><th class="noprint" colspan="4">&nbsp;</th></tr>';
		$OutstandingOrSettled='Outstanding';
	}

	if ($k == 1) {
		echo '<tr class="EvenTableRows">';
		$k = 0;
	} else {
		echo '<tr class="OddTableRows">';
		$k = 1;
	}


	$FormatedTranDate = ConvertSQLDate($MyRow['trandate']);


	if ($MyRow['type']==20) { //its an invoice
		echo '<td>', _($MyRow['typename']), '</td>
			<td class="number">', $MyRow['transno'], '</td>
			<td>', ConvertSQLDate($MyRow['trandate']), '</td>
			<td>', $MyRow['supplierno'], '</td>
			<td>', $MyRow['suppreference'], '</td>
			<td style="width:200px">', $MyRow['transtext'], '</td>
			<td class="number">', $MyRow['OrderNo'], '</td>
			<td class="number">', locale_number_format($MyRow['totalamount'], $CustomerRecord['decimalplaces']), '</td>
			<td>&nbsp;</td>
			<td class="number">', locale_number_format($MyRow['alloc'], $CustomerRecord['decimalplaces']), '</td>
			<td class="number">', locale_number_format($MyRow['balance'], $CustomerRecord['decimalplaces']), '</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>';
			/*<td class="noprint" title="', _('Click to preview the invoice'), '">
				<a href="', $RootPath, '/PrintCustTrans.php?FromTransNo=', $MyRow['transno'], '&amp;InvOrCredit=Invoice"><img alt="" src="', $RootPath, '/css/', $Theme, '/images/preview.png" /> ', _('HTML'), '</a>
			</td>
			<td class="noprint" title="', _('Click for PDF'), '">
				<a href="', $RootPath, '/', $PrintCustomerTransactionScript, '?FromTransNo=', $MyRow['transno'], '&amp;InvOrCredit=Invoice&amp;PrintPDF=True"><img alt="" src="', $RootPath, '/css/', $Theme, '/images/pdf.png" /> ', _('PDF'), '</a>*/
			/*</td>
			<td class="noprint" title="', _('Click to email the invoice'), '">
				<a href="', $RootPath, '/EmailCustTrans.php?FromTransNo=', $MyRow['transno'], '&amp;InvOrCredit=Invoice"><img alt="" src="', $RootPath, '/css/', $Theme, '/images/email.png" />', _('Email'), '</a>
			</td>
			<td class="noprint">&nbsp;</td>*/
		echo'</tr>';

	} elseif ($MyRow['type'] == 11) {//credit note
		echo '<td>', _($MyRow['typename']), '</td>
				<td class="number">', $MyRow['transno'], '</td>
				<td>', ConvertSQLDate($MyRow['trandate']), '</td>
				<td>', $MyRow['supplierno'], '</td>
				<td>', $MyRow['suppreference'], '</td>
				<td style="width:200px">', $MyRow['transtext'], '</td>
				<td class="number">', $MyRow['OrderNo'], '</td>
				<td>&nbsp;</td>
				<td class="number">', locale_number_format($MyRow['totalamount'], $CustomerRecord['decimalplaces']), '</td>
				<td class="number">', locale_number_format($MyRow['alloc'], $CustomerRecord['decimalplaces']), '</td>
				<td class="number">', locale_number_format($MyRow['balance'], $CustomerRecord['decimalplaces']), '</td>
				<td class="noprint" title="', _('Click to preview the credit note'), '">
					<a href="', $RootPath, '/PrintCustTrans.php?FromTransNo=', $MyRow['transno'], '&amp;InvOrCredit=Credit"><img alt="" src="', $RootPath, '/css/', $Theme, '/images/preview.png" />', _('HTML'), '</a>
				</td>
				<td class="noprint" title="', _('Click for PDF'), '">
					<a href="', $RootPath, '/', $PrintCustomerTransactionScript, '?FromTransNo=', $MyRow['transno'], '&amp;InvOrCredit=Credit&amp;PrintPDF=True"><img alt="" src="', $RootPath, '/css/', $Theme, '/images/pdf.png" />', _('PDF'), '</a>
				</td>
				<td class="noprint" title="', _('Click to email the credit note'), '">
					<a href="', $RootPath, '/EmailCustTrans.php?FromTransNo=', $MyRow['transno'], '&amp;InvOrCredit=Credit"><img alt="" src="', $RootPath, '/css/', $Theme, '/images/email.png" />', _('Email'), '</a>
				</td>
				<td class="noprint" title="', _('Click to allocate funds'), '">
					<a href="', $RootPath, '/SupplierAllocations.php?AllocTrans=', $MyRow['id'], '"><img alt="" src="', $RootPath, '/css/', $Theme, '/images/allocation.png" />', _('Allocation'), '</a>
				</td>
			</tr>';

	} elseif ($MyRow['type'] == 22 and $MyRow['totalamount'] < 0) {
		$total += $MyRow['totalamount'];
		/* Show transactions where:
		 * - Is receipt
		 */
		echo '<td>', _($MyRow['typename']), '</td>
				<td class="number">', $MyRow['transno'], '</td>
				<td>', ConvertSQLDate($MyRow['trandate']), '</td>
				<td>', $MyRow['supplierno'], '</td>
				<td>', $MyRow['suppreference'], '</td>
				<td style="width:200px">', $MyRow['transtext'], '</td>
				<td class="number">', $MyRow['OrderNo'], '</td>
				<td>&nbsp;</td>
				<td class="number">', locale_number_format($MyRow['totalamount'], $CustomerRecord['decimalplaces']), '</td>
				<td class="number">', locale_number_format($MyRow['alloc'], $CustomerRecord['decimalplaces']), '</td>
				<td class="number">', locale_number_format($MyRow['balance'], $CustomerRecord['decimalplaces']), '</td>
				<td class="number">', locale_number_format($total, $CustomerRecord['decimalplaces']), '</td>
				<td class="noprint" title="', _('Click to allocate funds'), '">
					<a href="', $RootPath, '/SupplierAllocations.php?AllocTrans=', $MyRow['id'], '"><img alt="" src="', $RootPath, '/css/', $Theme, '/images/allocation.png" />', _('Allocation'), '</a>
				</td>
			
			</tr>';

	} elseif ($MyRow['type'] == 22 and $MyRow['totalamount'] > 0) {

		/* Show transactions where:
		* - Is a negative receipt
		* - User cannot view GL transactions
		*/
		echo '<td>', _($MyRow['typename']), '</td>
				<td class="number">', $MyRow['transno'], '</td>
				<td>', ConvertSQLDate($MyRow['trandate']), '</td>
				<td>', $MyRow['supplierno'], '</td>
				<td>', $MyRow['suppreference'], '</td>
				<td style="width:200px">', $MyRow['transtext'], '</td>
				<td class="number">', $MyRow['OrderNo'], '</td>
				<td class="number">', locale_number_format($MyRow['totalamount'], $CustomerRecord['decimalplaces']), '</td>
				<td>&nbsp;</td>
				<td class="number">', locale_number_format($MyRow['alloc'], $CustomerRecord['decimalplaces']), '</td>
				<td class="number">', locale_number_format($MyRow['balance'], $CustomerRecord['decimalplaces']), '</td>
				<td class="noprint">&nbsp;</td>
				
			</tr>';
	}
}
//end of while loop

echo '</tbody></table>
	<br />
	<table class="selection" width="70%">
		<tr>
			<th style="width:20%">', _('Total Balance'), '</th>
			<th style="width:20%">', _('Current'), '</th>
			<th style="width:20%">', _('Now Due'), '</th>
			<th style="width:20%">', $_SESSION['PastDueDays1'], '-', $_SESSION['PastDueDays2'], ' ', _('Days Overdue'), '</th>
			<th style="width:20%">', _('Over'), ' ', $_SESSION['PastDueDays2'], ' ', _('Days Overdue'), '</th>
		</tr>
		<tr>
			<td class="number">', locale_number_format($CustomerRecord['balance'], $CustomerRecord['decimalplaces']), '</td>
			<td class="number">', locale_number_format(($CustomerRecord['balance'] - $CustomerRecord['due']), $CustomerRecord['decimalplaces']), '</td>
			<td class="number">', locale_number_format(($CustomerRecord['due'] - $CustomerRecord['overdue1']), $CustomerRecord['decimalplaces']), '</td>
			<td class="number">', locale_number_format(($CustomerRecord['overdue1'] - $CustomerRecord['overdue2']), $CustomerRecord['decimalplaces']), '</td>
			<td class="number">', locale_number_format($CustomerRecord['overdue2'], $CustomerRecord['decimalplaces']), '</td>
		</tr>
	</table>';

include('includes/footer.inc');
?>