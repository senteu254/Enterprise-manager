<?php
/* $Id: SupplierInquiry.php 6941 2014-10-26 23:18:08Z daintree $*/

include('includes/session.inc');
$Title = _('Supplier Inquiry');
$ViewTopic = 'AccountsPayable';// Filename in ManualContents.php's TOC./* RChacon: Is there any content for Supplier Inquiry? */
$BookMark = 'AccountsPayable';// Anchor's id in the manual's html document.
include('includes/header.inc');

include('includes/SQL_CommonFunctions.inc');

// always figure out the SQL required from the inputs available

if(!isset($_GET['SupplierID']) AND !isset($_SESSION['SupplierID'])) {
	echo '<br />' . _('To display the enquiry a Supplier must first be selected from the Supplier selection screen') .
		 '<br />
			<div class="centre">
				<a href="' . $RootPath . '/SelectSupplier.php">' . _('Select a Supplier to Inquire On') . '</a>
			</div>';
	include('includes/footer.inc');
	//exit;
} else {
	if (isset($_GET['SupplierID'])) {
		$_SESSION['SupplierID'] = $_GET['SupplierID'];
	}
	$SupplierID = $_SESSION['SupplierID'];
}

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

$SQL = "SELECT suppliers.suppname,
		suppliers.currcode,
		supptrans.status,
		currencies.currency,
		currencies.decimalplaces AS currdecimalplaces,
		paymentterms.terms,
		supptrans.transtate,
		SUM(supptrans.ovamount + supptrans.ovgst - supptrans.alloc) AS balance,
		SUM(CASE WHEN paymentterms.daysbeforedue > 0 THEN
			CASE WHEN (TO_DAYS(Now()) - TO_DAYS(supptrans.trandate)) >= paymentterms.daysbeforedue
			THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
		ELSE
			CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1', 'MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= 0 THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
		END) AS due,
		SUM(CASE WHEN paymentterms.daysbeforedue > 0  THEN
			CASE WHEN (TO_DAYS(Now()) - TO_DAYS(supptrans.trandate)) > paymentterms.daysbeforedue
					AND (TO_DAYS(Now()) - TO_DAYS(supptrans.trandate)) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ")
			THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
		ELSE
			CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1','MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') .")) >= '" . $_SESSION['PastDueDays1'] . "')
			THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
		END) AS overdue1,
		Sum(CASE WHEN paymentterms.daysbeforedue > 0 THEN
			CASE WHEN TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(Now()) - TO_DAYS(supptrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ")
			THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
		ELSE
			CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(supptrans.trandate, " . INTERVAL('1','MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(supptrans.trandate))', 'DAY') . ")) >= '" . $_SESSION['PastDueDays2'] . "')
			THEN supptrans.ovamount + supptrans.ovgst - supptrans.alloc ELSE 0 END
		END ) AS overdue2
		FROM suppliers INNER JOIN paymentterms
		ON suppliers.paymentterms = paymentterms.termsindicator
     	INNER JOIN currencies
     	ON suppliers.currcode = currencies.currabrev
     	INNER JOIN supptrans
     	ON suppliers.supplierid = supptrans.supplierno
		WHERE suppliers.supplierid = '" . $SupplierID . "'
		AND supptrans.status=0
		AND supptrans.transtate=0
		GROUP BY suppliers.suppname,
      			currencies.currency,
      			currencies.decimalplaces,
      			paymentterms.terms,
      			paymentterms.daysbeforedue,
      			paymentterms.dayinfollowingmonth";

$ErrMsg = _('The supplier details could not be retrieved by the SQL because');
$DbgMsg = _('The SQL that failed was');

$SupplierResult = DB_query($SQL, $ErrMsg, $DbgMsg);

if (DB_num_rows($SupplierResult) == 0) {

	/*Because there is no balance - so just retrieve the header information about the Supplier - the choice is do one query to get the balance and transactions for those Suppliers who have a balance and two queries for those who don't have a balance OR always do two queries - I opted for the former */

	$NIL_BALANCE = True;

	$SQL = "SELECT suppliers.suppname,
					suppliers.currcode,
					currencies.currency,
					currencies.decimalplaces AS currdecimalplaces,
					paymentterms.terms
			FROM suppliers INNER JOIN paymentterms
		    ON suppliers.paymentterms = paymentterms.termsindicator
		    INNER JOIN currencies
		    ON suppliers.currcode = currencies.currabrev
			WHERE suppliers.supplierid = '" . $SupplierID . "'";

	$ErrMsg = _('The supplier details could not be retrieved by the SQL because');
	$DbgMsg = _('The SQL that failed was');

	$SupplierResult = DB_query($SQL, $ErrMsg, $DbgMsg);

} else {
	$NIL_BALANCE = False;
}

$SupplierRecord = DB_fetch_array($SupplierResult);

if ($NIL_BALANCE == True) {
	$SupplierRecord['balance'] = 0;
	$SupplierRecord['due'] = 0;
	$SupplierRecord['overdue1'] = 0;
	$SupplierRecord['overdue2'] = 0;
}
include('includes/CurrenciesArray.php'); // To get the currency name from the currency code.
echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/supplier.png" title="' . _('Supplier') . '" alt="" /> ' .
		_('Supplier') . ': ' .
			$SupplierID . ' - ' . $SupplierRecord['suppname'] . '<br />' .
		_('All amounts stated in') . ': ' .
			$SupplierRecord['currcode'] . ' - ' . $CurrencyName[$SupplierRecord['currcode']] . '<br />' .
		_('Terms') . ': ' .
			$SupplierRecord['terms'] . '</p>';

if (isset($_GET['HoldType']) AND isset($_GET['HoldTrans'])) {

	if ($_GET['HoldStatus'] == _('Hold')) {
		$SQL = "UPDATE supptrans SET hold=1
				WHERE type='" . $_GET['HoldType'] . "'
				AND transno='" . $_GET['HoldTrans'] . "'";
	} elseif ($_GET['HoldStatus'] == _('Release')) {
		$SQL = "UPDATE supptrans SET hold=0
				WHERE type='" . $_GET['HoldType'] . "'
				AND transno='" . $_GET['HoldTrans'] . "'";
	}

	$ErrMsg = _('The Supplier Transactions could not be updated because');
	$DbgMsg = _('The SQL that failed was');
	$UpdateResult = DB_query($SQL, $ErrMsg, $DbgMsg);

}

echo '<table class="selection">
	<tr><th>' . _('Total Balance') . '</th>
		<th>' . _('Balance C/F') . '</th>
		<th>' . _('C/Finacial Balance') . '</th>
		<th>' . _('Now Due') . '</th>
		<th>' . $_SESSION['PastDueDays1'] . '-' . $_SESSION['PastDueDays2'] . ' ' . _('Days Overdue') . '</th>
		<th>' . _('Over') . ' ' . $_SESSION['PastDueDays2'] . ' ' . _('Days Overdue') . '</th>
	</tr>';
echo '<tr>
	  <td class="number">' . locale_number_format($SupplierRecord['balance'],$SupplierRecord['currdecimalplaces']) . '</td>
	  <td class="number">' . locale_number_format(($balance),$SupplierRecord['currdecimalplaces']) . '</td>
	  <td class="number">' . locale_number_format($SupplierRecord['balance']-($SupplierRecord['balance'] - $SupplierRecord['due']),$SupplierRecord['currdecimalplaces']) . '</td>
	  <td class="number">' . locale_number_format(($SupplierRecord['due']-$SupplierRecord['overdue1']),$SupplierRecord['currdecimalplaces']) . '</td>
	  <td class="number">' . locale_number_format(($SupplierRecord['overdue1']-$SupplierRecord['overdue2']) ,$SupplierRecord['currdecimalplaces']) . '</td>
	   <td class="number">' . locale_number_format(($balance),$SupplierRecord['currdecimalplaces']) . '</td>
	  </tr>
	</table>';

echo '<br />
	<div class="centre">
		<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>
        <input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo _('Show all transactions after') . ': '  . '<input type="text" class="date" alt="' .$_SESSION['DefaultDateFormat'] .'" name="TransAfterDate" value="' . $_POST['TransAfterDate'] . '" maxlength="10" size="10" /> &nbsp;';
echo _('To') . ': &nbsp; '  . '<input type="text" class="date" alt="' .$_SESSION['DefaultDateFormat'] .'" name="TransToDate" value="' . $_POST['TransToDate'] . '" maxlength="10" size="10" />
	    <input type="submit" name="Refresh Inquiry" value="' . _('Refresh Inquiry') . '" />
    </div>
	</form>
	<br />';
echo '</div>';
$DateAfterCriteria = FormatDateForSQL($_POST['TransAfterDate']);
$DateToCriteria = FormatDateForSQL($_POST['TransToDate']);

$SQL = "SELECT supptrans.id,
			systypes.typename,
			supptrans.type,
			supptrans.status,
			supptrans.transno,
			supptrans.trandate,
			supptrans.suppreference,
			supptrans.transtate,
			supptrans.rate,
			(supptrans.ovamount + supptrans.ovgst) AS totalamount,
			supptrans.alloc AS allocated,
			supptrans.hold,
			supptrans.settled,
			supptrans.transtext,
			supptrans.supplierno
		FROM supptrans,
			systypes
		WHERE supptrans.type = systypes.typeid
		AND supptrans.supplierno = '" . $SupplierID . "'
		AND supptrans.trandate >= '" . $DateAfterCriteria . "'
		AND supptrans.trandate<='" .$DateToCriteria. "'
		AND(supptrans.type=20 OR supptrans.type=22 OR supptrans.type=21)
		AND supptrans.transtate=0
		AND (supptrans.suppreference IS NOT NULL OR supptrans.suppreference IS NULL)
		ORDER BY supptrans.trandate";

$ErrMsg = _('No transactions were returned by the SQL because');
$DbgMsg = _('The SQL that failed was');

$TransResult = DB_query($SQL, $ErrMsg, $DbgMsg);
/*****************************************************************************************************************************************/
$SQL_balance = "SELECT supptrans.id,
			systypes.typename,
			supptrans.type,
			supptrans.status,
			supptrans.transno,
			supptrans.trandate,
			supptrans.suppreference,
			supptrans.transtate,
			supptrans.rate,
			(supptrans.ovamount + supptrans.ovgst) AS totalamount,
			supptrans.alloc AS allocated,
			supptrans.hold,
			supptrans.settled,
			supptrans.transtext,
			supptrans.supplierno
		FROM supptrans,
			systypes
		WHERE supptrans.type = systypes.typeid
		AND supptrans.supplierno = '" . $SupplierID . "'
		AND supptrans.trandate >= '" . $DateAfterCriteria . "'
		AND supptrans.trandate<='" .$DateToCriteria. "'
		AND(supptrans.type=20 OR supptrans.type=22 OR supptrans.type=21)
		AND supptrans.transtate=0
		AND supptrans.status=0
		AND (supptrans.suppreference IS NOT NULL OR supptrans.suppreference IS NULL)
		ORDER BY supptrans.trandate";

$ErrMsg = _('No transactions were returned by the SQL because');
$DbgMsg = _('The SQL that failed was');

$TransResult_balance = DB_query($SQL_balance, $ErrMsg, $DbgMsg);
/*****************************************************************************************************************************************/
/*if (DB_num_rows($TransResult) == 0) {
echo '<br /><div class="centre">' . _('TRANSACTIONS SETTLED SINCE'), ' ', $_POST['TransAfterDate'], _(' &nbsp;To'), '  &nbsp;', $_POST['TransToDate'], '';
	//echo '<br /><div class="centre">' . _('There are no transactions to display since') . ' ' . $_POST['TransAfterDate'];
	echo '</div>';
	include('includes/footer.inc');
	exit;
}*/
$SQL54 = "SELECT supptrans.id,
			systypes.typename,
			supptrans.type,
			supptrans.transno,
			supptrans.trandate,
			supptrans.status,
			supptrans.transtate,
			supptrans.suppreference,
			supptrans.transtate,
			supptrans.rate,
			(supptrans.ovamount + supptrans.ovgst) AS totalamount,
			supptrans.alloc AS allocated,
			supptrans.hold,
			supptrans.settled,
			supptrans.transtext,
			supptrans.supplierno
		FROM supptrans,
			systypes
		WHERE supptrans.type = systypes.typeid
		AND supptrans.supplierno = '" . $SupplierID . "'
		AND supptrans.trandate >= '" . $DateAfterCriteria . "'
		AND supptrans.trandate<='" .$DateToCriteria. "'
		AND(supptrans.type=480 OR supptrans.type=490)
		AND supptrans.status=0
		AND supptrans.transtate=0
		AND suppreference IS NOT NULL
		ORDER BY supptrans.trandate";

$ErrMsg = _('No transactions were returned by the SQL because');
$DbgMsg = _('The SQL that failed was');

/*$Trans = DB_query($SQL54, $ErrMsg, $DbgMsg);*/
$Trans = DB_query($SQL54);
$row = DB_fetch_array($Trans);


$sql6 = "SELECT * FROM supptrans
				WHERE supplierno ='" . $SupplierID . "'
				AND type=22
				AND transtate=2";
		$ErrMsg = _('An error occurred in the attempt to get the outstanding GRNs for') . ' ' . $_POST['SuppName'] . '. ' . _('The message was') . ':';
  		$DbgMsg = _('The SQL that failed was') . ':';
		$Rev = DB_query($sql6,$ErrMsg,$DbgMsg);	
/*show a table of the transactions returned by the SQL */
echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/supplier.png" title="' . _('Closed Transaction for') . '" alt="" /> ' .
		_('Open Transaction') . ': </p>';
echo '<table width="90%" class="selection">
	<tr>
		<th class="ascending">' . _('Date') . '</th>
		<th class="ascending">' . _('Type') . '</th>
		<th class="ascending">' . _('Number') . '</th>
		<th class="ascending">' . _('Reference') . '</th>
		<th class="ascending">' . _('Comments') . '</th>
		<th class="ascending">' . _('Total') . '</th>
		<th class="ascending">' . _('Allocated') . '</th>
		<th class="ascending">' . _('Balance') . '</th>
		<th>' . _('More Info') . '</th>
		<th>' . _('More Info') . '</th>
	</tr>';

$j = 1;
$k = 0;// Row colour counter.
while ($myrowb=DB_fetch_array($TransResult_balance)) {
$balancebb+=$myrowb['totalamount']-$myrowb['allocated'];
}
while ($myrow=DB_fetch_array($TransResult)) {
//$balancebb+=$myrow['totalamount']-$myrow['allocated'];
	if ($myrow['hold'] == 0 AND $myrow['settled'] == 0) {
		$HoldValue = _('Hold');
	} elseif ($myrow['settled'] == 1) {
		$HoldValue = '';
	}else {
		$HoldValue = _('Release');
	}
	if ($myrow['hold'] == 1) {
//		echo "<tr bgcolor='#DD99BB'>";
	}elseif ($k == 1) {
		echo '<tr class="EvenTableRows">';
		$k = 0;
	} else {
		echo '<tr class="OddTableRows">';
		$k = 1;
	}

	$FormatedTranDate = ConvertSQLDate($myrow['trandate']);
	// All table-row (tag tr) must have 10 table-datacells (tag td).
	$BaseTD8 = '<td>' . ConvertSQLDate($myrow['trandate']) . '</td>
		<td>' . _($myrow['typename']) . '</td>
		<td class="number">' . $myrow['transno'] . '</td>
		<td>' . $myrow['suppreference'] . '</td>
		<td>' . $myrow['transtext'] . '</td>
		<td class="number">' . locale_number_format($myrow['totalamount'],$SupplierRecord['currdecimalplaces']) . '</td>
		<td class="number">' . locale_number_format($myrow['allocated'],$SupplierRecord['currdecimalplaces']) . '</td>
		<td class="number">' . locale_number_format($myrow['totalamount']-$myrow['allocated'],$SupplierRecord['currdecimalplaces']) . '</td>';

	$PaymentTD1 = '<td><a href="' . $RootPath . '/PaymentAllocations.php?SuppID=%s&amp;InvID=%s" title="' .
			_('Click to view payments') . '"><img alt="" src="' . $RootPath .
			'/css/' . $Theme . '/images/money_delete.png" width="16"/> ' . _('Payments') . '</a></td>';

/* To do: $HoldValueTD1*/

	$AllocationTD1 = '<td><a href="' . $RootPath . '/SupplierAllocations.php?AllocTrans=%s" title="' .
			_('Click to allocate funds') . '"><img alt="" src="' . $RootPath .
			'/css/' . $Theme . '/images/allocation.png" /> ' . _('Allocation') . '</a></td>';

	$GLEntriesTD1 = '<td><a href="' . $RootPath . '/GLTransInquiry.php?TypeID=%s&amp;TransNo=%s" target="_blank" title="' .
			_('Click to view the GL entries') . '"><img alt="" src="' . $RootPath .
			'/css/' . $Theme . '/images/gl.png" width="16" /> ' . _('GL Entries') . '</a></td>';

	if ($myrow['type'] == 20) { /*Show a link to allow GL postings to be viewed but no link to allocate */

		if ($_SESSION['CompanyRecord']['gllink_creditors'] == True) {
			if ($myrow['totalamount'] - $myrow['allocated'] == 0) {
				/*The trans is settled so don't show option to hold */
				printf($BaseTD8 . $PaymentTD1 . $GLEntriesTD1 . '</tr>',
					// $PaymentTD1 parameters:
					$myrow['supplierno'],
					$myrow['suppreference'],
					// $GLEntriesTD1 parameters:
					$myrow['type'],
					$myrow['transno']);

			} else {
				$AuthSQL="SELECT offhold
							FROM purchorderauth
							WHERE userid='" . $_SESSION['UserID'] . "'
							AND currabrev='" . $SupplierRecord['currcode']."'";
				$AuthResult=DB_query($AuthSQL);
				$AuthRow=DB_fetch_array($AuthResult);

				printf($BaseTD8);
				if ($AuthRow[0]==0) {
					echo '<td><a href="' .htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?HoldType=' . $myrow['type'] . '&amp;HoldTrans=' . $myrow['transno']. '&amp;HoldStatus=' . $HoldValue . '&amp;FromDate=' . $_POST['TransAfterDate'].'ToDate=' . $_POST['TransToDate'].'">' . $HoldValue  . '</a></td>';
				} else {
					if ($HoldValue==_('Release')) {
						echo '<td>' . $HoldValue  . '</a></td>';
					} else {
						echo '<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8'). '?HoldType=' . $myrow['type'] .'&amp;HoldTrans=' . $myrow['transno'] . '&amp;HoldStatus=' . $HoldValue . '&amp;FromDate=' . $_POST['TransAfterDate'] .'ToDate=' . $_POST['TransToDate'].'">' . $HoldValue  . '</a></td>';
					}
				}
				printf($GLEntriesTD1 . '</tr>',
					// $GLEntriesTD1 parameters:
					$myrow['type'],
					$myrow['transno']);

			}
		} else {
			if ($myrow['totalamount'] - $myrow['allocated'] == 0) {
				/*The trans is settled so don't show option to hold */
				printf($BaseTD8 . '<td>&nbsp;</td><td>&nbsp;</td></tr>');

			} else {
				printf($BaseTD8);
				echo '
					<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES,'UTF-8') . '/PaymentAllocations.php?SuppID=' .
						$myrow['type'] . '&amp;InvID=' . $myrow['transno'] . '">' . _('View Payments') . '</a></td>
					<td><a href="' . $HoldValue . '?HoldType=' . $_POST['TransAfterDate'] . '&amp;HoldTrans=' . $HoldValue . '&amp;HoldStatus=' .
						$RootPath . '&amp;FromDate='. $myrow['supplierno'] . '">' . $myrow['suppreference'] . '</a></td></tr>';
			}
		}

	} else { /*its a credit note or a payment */

		if ($_SESSION['CompanyRecord']['gllink_creditors'] == True) {
			printf($BaseTD8 . $AllocationTD1 . $GLEntriesTD1 . '</tr>',
				// $AllocationTD1 parameters:
				$myrow['id'],
				// $GLEntriesTD1 parameters:
				$myrow['type'],
				$myrow['transno']);

		} else { /*Not linked to GL */
			printf($BaseTD8 . $AllocationTD1 . '<td>&nbsp;</td></tr>',
				// $AllocationTD1 parameters:
				$myrow['id']);

		}
	}// End of page full new headings if
	
	
}// End of while loop

############################################################reversed balance###########################################################################
			
echo '</table>';
echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/supplier.png" title="' . _('Reversed payment') . '" alt="" /> ' .
		_('Reversed payment') . ':</p>';
echo '<br /><table cellpadding="2" class="selection">';
			$TableHeader = '<tr>
			                    <th>' . _('Payment Type') . '</th>
								<th>' . _('Total Amount') . '</th>
								<th>' . _('Date Paid') . '</th>
							</tr>';

			echo $TableHeader;

			/* show the GRNs outstanding to be invoiced that could be reversed */
			$RowCounter =0;
			$k=0;
			while ($row3=DB_fetch_array($Rev)) { 
			    $total+=$row3['ovamount'];
				$totalAmts=locale_number_format(-1*$total,2);
				$DisplayTotal = locale_number_format($total);
				$DisplayDateDel = ConvertSQLDate($row3['trandate']);
				
			if ($k == 1) {
				echo '<tr class="EvenTableRows">';
				$k = 0;
			} else {
				echo '<tr class="OddTableRows">';
				$k = 1;
			}
				echo '<td>', $row3['suppreference'], '</td>
				     <td>', locale_number_format($row3['ovamount'],2), '</td>
					<td>', ConvertSQLDate($row3['trandate']), '</td>';
				echo'</tr>';
		
	}
	if ($total<0){
	echo'<tr><td>Total<td>'.$totalAmts.'</tr>';
	}else{
	echo'There is No Reversed Payment for the selected supplier.';
	}
			echo '</table>';
#############################################################end reversed balance##########################################################################
#######################################################opening balance################################################################################
echo '</table>';
$ListCount = DB_num_rows($Trans);
	if ($ListCount > 0) {
echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/supplier.png" title="' . _('Opening Balance for ') . '" alt="" /> ' .
		_('Opening Balance For FY '.Date('Y',YearEndDate($_SESSION['YearEnd'],-1)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd'],0)).'') . ':</p>';
echo '<table width="90%" class="selection">';
	echo'<tr>
      <th class="ascending">' . _('Type') . '</th>
		<th class="ascending">' . _('Number') . '</th>
		<th class="ascending">' . _('Date') . '</th>
		<th class="ascending">' . _('Reference') . '</th>
		<th class="ascending">' . _('Order No') . '</th>
		<th class="ascending">' . _('Total') . '</th>
		<th class="ascending">' . _('Allocated') . '</th>
		<th class="ascending">' . _('Amount') . '</th>
	</tr>';
	
	$k = 0; //row colour counter
	$balance=$row['totalamount'] - $row['allocated'];
	if ($k == 1) {
		echo '<tr class="EvenTableRows">';
		$k = 0;
	} else {
		echo '<tr class="OddTableRows">';
		$k = 1;
	}
		echo '<td style="width:200px">', _($row['typename']), '</td>
			<td class="number">', $row['transno'], '</td>
			<td>' . ConvertSQLDate($row['trandate']) . '</td>
			<td>', $row['suppreference'], '</td>
			<td>', $row['OrderNo'], '</td>
			<td class="number">', locale_number_format($row['totalamount'], $CustomerRecord['decimalplaces']), '</td>
			<td class="number">', locale_number_format($row['alloc'], $CustomerRecord['decimalplaces']), '</td>
			<td class="number">', locale_number_format($balance,2), '</td>
			<td>&nbsp;</td>';
		echo'</tr>';
		
	
	
	echo'</table>';
	}else{
	echo'There is No balance carried forward';
	}
#############################################################end opening balance##########################################################################
echo '<table class="selection">
	<tr><th>' . _('Total Balance') . '</th>
		<th>' . _('Balance C/F') . '</th>
		<th>' . _('C/Finacial Balance') . '</th>
		<th>' . _('Now Due') . '</th>
		<th>' . $_SESSION['PastDueDays1'] . '-' . $_SESSION['PastDueDays2'] . ' ' . _('Days Overdue') . '</th>
		<th>' . _('Over') . ' ' . $_SESSION['PastDueDays2'] . ' ' . _('Days Overdue') . '</th>
	</tr>';
	$cur_balance=$balancebb+$balance;
echo '<tr>
	  <td class="number">' . locale_number_format($cur_balance,$SupplierRecord['currdecimalplaces']) . '</td>
	  <td class="number">' . locale_number_format(($balance),$SupplierRecord['currdecimalplaces']) . '</td>
	  <td class="number">' . locale_number_format($SupplierRecord['balance']-($SupplierRecord['balance'] - $SupplierRecord['due']),$SupplierRecord['currdecimalplaces']) . '</td>
	  <td class="number">' . locale_number_format(($SupplierRecord['due']-$SupplierRecord['overdue1']),$SupplierRecord['currdecimalplaces']) . '</td>
	  <td class="number">' . locale_number_format(($SupplierRecord['overdue1']-$SupplierRecord['overdue2']) ,$SupplierRecord['currdecimalplaces']) . '</td>
	   <td class="number">' . locale_number_format(($balance),$SupplierRecord['currdecimalplaces']) . '</td>
	  </tr>
	</table>';
include('includes/footer.inc');
?>
