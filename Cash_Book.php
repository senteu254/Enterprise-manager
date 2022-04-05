<?php
include('includes/session.inc');
$Title = _('Cash Book Transactions');// Screen identificator.
$ViewTopic = 'Cash Book';// Filename's id in ManualContents.php's TOC.
$BookMark = 'Cash Book Report';// Anchor's id in the manual's html document.
include('includes/header.inc');
echo '<p class="page_title_text"><img alt="" src="'.$RootPath.'/css/'.$Theme.
	'/images/bank.png" title="' .
	_('Cash Book Report') . '" /> ' .// Icon title.
	_('Cash Book Report') . '</p>';// Page title.

if (!isset($_POST['Show'])) {
	echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	echo '<table class="selection">';

	$SQL = "SELECT 	bankaccountname,
					bankaccounts.accountcode,
					bankaccounts.currcode,
					chartmaster.accountname
			FROM bankaccounts,
				chartmaster,
				bankaccountusers
			WHERE bankaccounts.accountcode=chartmaster.accountcode
				AND bankaccounts.accountcode=bankaccountusers.accountcode
			AND bankaccountusers.userid = '" . $_SESSION['UserID'] ."'";

	$ErrMsg = _('The bank accounts could not be retrieved because');
	$DbgMsg = _('The SQL used to retrieve the bank accounts was');
	$AccountsResults = DB_query($SQL,$ErrMsg,$DbgMsg);

	echo '<tr>
			<td>' . _('Bank Account') . ':</td>
			<td><select name="BankAccount">';

	if (DB_num_rows($AccountsResults)==0){
		echo '</select></td>
				</tr></table>';
		prnMsg( _('Bank Accounts have not yet been defined. You must first') . ' <a href="' . $RootPath . '/BankAccounts.php">' . _('define the bank accounts') . '</a> ' . _('and general ledger accounts to be affected'),'warn');
		include('includes/footer.inc');
		exit;
	} else {
		while ($myrow=DB_fetch_array($AccountsResults)){
		/*list the bank account names */
			if (!isset($_POST['BankAccount']) AND $myrow['currcode']==$_SESSION['CompanyRecord']['currencydefault']){
				$_POST['BankAccount']=$myrow['accountcode'];
			}
			if ($_POST['BankAccount']==$myrow['accountcode']){
				echo '<option selected="selected" value="' . $myrow['accountcode'] . '">' . $myrow['accountname'] . ' - ' . $myrow['currcode'] . '</option>';
			} else {
				echo '<option value="' . $myrow['accountcode'] . '">' . $myrow['accountname'] . ' - ' . $myrow['currcode'] . '</option>';
			}
		}
		echo '</select></td></tr>';
	}
	//////////////////////////////////////////////////////////////****************//////////////////////////////////////////////////////////////////
	   echo'<tr>
				<td>' . _('Select Period From:') . '</td>
				<td><select name="FromPeriod">';
	$NextYear = date('Y-m-d');
	$sql = "SELECT periodno,
					lastdate_in_period
				FROM periods 
				ORDER BY periodno DESC";
	$Periods = DB_query($sql);
	while ($myrow=DB_fetch_array($Periods,$db)){
		if(isset($_POST['FromPeriod']) AND $_POST['FromPeriod']!=''){
			if( $_POST['FromPeriod']== $myrow['periodno']){
				echo '<option selected="selected" value="' . $myrow['periodno'] . '">' .MonthAndYearFromSQLDate($myrow['lastdate_in_period']) . '</option>';
			} else {
				echo '<option value="' . $myrow['periodno'] . '">' . MonthAndYearFromSQLDate($myrow['lastdate_in_period']) . '</option>';
			}
		} else {
			if($myrow['lastdate_in_period']==$DefaultFromDate){
				echo '<option selected="selected" value="' . $myrow['periodno'] . '">' . MonthAndYearFromSQLDate($myrow['lastdate_in_period']) . '</option>';
			} else {
				echo '<option value="' . $myrow['periodno'] . '">' . MonthAndYearFromSQLDate($myrow['lastdate_in_period']) . '</option>';
			}
		}
	}

	echo '</select></td>
		</tr>';
		if (!isset($_POST['ToPeriod']) OR $_POST['ToPeriod']==''){
		$DefaultToPeriod = GetPeriod(date($_SESSION['DefaultDateFormat'],mktime(0,0,0,Date('m')+1,0,Date('Y'))),$db);
	} else {
		$DefaultToPeriod = $_POST['ToPeriod'];
	}
	echo '<tr>
			<td>' . _('Select Period To:')  . '</td>
			<td><select name="ToPeriod">';
	$RetResult = DB_data_seek($Periods,0);
	while ($myrow=DB_fetch_array($Periods,$db)){

		if($myrow['periodno']==$DefaultToPeriod){
			echo '<option selected="selected" value="' . $myrow['periodno'] . '">' . MonthAndYearFromSQLDate($myrow['lastdate_in_period']) . '</option>';
		} else {
			echo '<option value ="' . $myrow['periodno'] . '">' . MonthAndYearFromSQLDate($myrow['lastdate_in_period']) . '</option>';
		}
	}
	echo '</select></td>';
		echo'</tr>';
	//////////////////////////////////////////////////////////////////*************************/////////////////////////////////////////////////////////////
	echo '<tr>
			<td>' . _('Transactions Dated From') . ':</td>
			<td><input type="text" name="FromTransDate" class="date" alt="'.$_SESSION['DefaultDateFormat'].'" required="required" maxlength="10" size="11" onchange="isDate(this, this.value, '."'".$_SESSION['DefaultDateFormat']."'".')" value="' .
				date($_SESSION['DefaultDateFormat']) . '" /></td>
		</tr>
		<tr>
			<td>' . _('Transactions Dated To') . ':</td>
			<td><input type="text" name="ToTransDate" class="date" alt="'.$_SESSION['DefaultDateFormat'].'"  required="required" maxlength="10" size="11" onchange="isDate(this, this.value, '."'".$_SESSION['DefaultDateFormat']."'".')" value="' . date($_SESSION['DefaultDateFormat']) . '" /></td>
		</tr>		
		</table>
		<br />
		<div class="centre">
			<input type="submit" name="Show" value="' . _('Show transactions'). '" />
		</div>
        </div>
		</form>';
} else {
	$SQL = "SELECT 	bankaccountname,
					bankaccounts.currcode,
					currencies.decimalplaces
			FROM bankaccounts
			INNER JOIN currencies
				ON bankaccounts.currcode = currencies.currabrev
			WHERE bankaccounts.accountcode='" . $_POST['BankAccount'] . "'";
	$BankResult = DB_query($SQL,_('Could not retrieve the bank account details'));
	$sql="SELECT 	banktrans.currcode,
					banktrans.amount,
					banktrans.amountcleared,
					banktrans.functionalexrate,
					banktrans.exrate,
					banktrans.banktranstype,
					banktrans.transdate,
					banktrans.transno,
					banktrans.ref,
					bankaccounts.bankaccountname,
					systypes.typename,
					systypes.typeid
				FROM banktrans
				INNER JOIN bankaccounts
				ON banktrans.bankact=bankaccounts.accountcode
				INNER JOIN systypes
				ON banktrans.type=systypes.typeid
				WHERE bankact='".$_POST['BankAccount']."'
				AND transdate>='" . FormatDateForSQL($_POST['FromTransDate']) . "'
				AND transdate<='" . FormatDateForSQL($_POST['ToTransDate']) . "'
				ORDER BY banktrans.transdate";
	$result = DB_query($sql);
	if (DB_num_rows($result)==0) {
		prnMsg(_('There are no transactions for this account in the date range selected'), 'info');
	} else {
		$BankDetailRow = DB_fetch_array($BankResult);
		
	#####################################################################################################################
		
	$sql22 = "SELECT *,c.accountname,a.accountcode,
					Sum(CASE WHEN b.period='" . $_POST['FromPeriod'] . "' THEN b.bfwd ELSE 0 END) AS firstprdbfwd,
					Sum(CASE WHEN b.period='" . $_POST['FromPeriod'] . "' THEN b.bfwdbudget ELSE 0 END) AS firstprdbudgetbfwd,
					Sum(CASE WHEN b.period='" . $_POST['ToPeriod'] . "' THEN b.bfwd + b.actual ELSE 0 END) AS lastprdcfwd,
					Sum(CASE WHEN b.period='" . $_POST['ToPeriod'] . "' THEN b.actual ELSE 0 END) AS monthactual,
					Sum(CASE WHEN b.period='" . $_POST['ToPeriod'] . "' THEN b.budget ELSE 0 END) AS monthbudget,
					Sum(CASE WHEN b.period='" . $_POST['ToPeriod'] . "' THEN b.bfwdbudget + b.budget ELSE 0 END) AS lastprdbudgetcfwd
					FROM bankaccounts a
					INNER JOIN  chartdetails b ON a.accountcode=b.accountcode
					INNER JOIN chartmaster c ON a.accountcode=c.accountcode
					WHERE a.accountcode='" . $_POST['BankAccount'] . "'";

	$result55 = DB_query($sql22);
	while ($myrow45 = DB_fetch_array($result55)) {
			$AccountPeriodActual = $myrow45['firstprdbfwd'];
			$Accountcode = $myrow45['accountcode'];
			$Accountname = $myrow45['bankaccountname']; 	
			$AccountNumber = $myrow45['bankaccountnumber'];
			$Accountcurrcode = $myrow45['currcode'];
			$AccountName = $myrow45['accountname'];
			//$AccountPeriodActual = $myrow45['lastprdcfwd'];
			$ActEnquiryURL = '<a href="'. $RootPath . '/GLAccountInquiry.php?FromPeriod=' . $_POST['FromPeriod'] . '&amp;ToPeriod=' . $_POST['ToPeriod'] . '&amp;
			Account=' . $myrow45['accountcode'] . '&amp;Show=Yes">' . $myrow45['accountcode'] . '</a>';
				}
		#######################################################################################################################
		//$sql_cash = "SELECT * FROM cash";
		$sql_cash ="SELECT *,a.Payment_Type FROM cash a
		            LEFT JOIN payment_voucher b ON a.VoucherID = b.voucherid
		            WHERE a.Account='" . $_POST['BankAccount'] . "'
					AND a.date>='" . FormatDateForSQL($_POST['FromTransDate']) . "'
				    AND a.date<='" . FormatDateForSQL($_POST['ToTransDate']) . "'";
		$result_cash = DB_query($sql_cash);
		
		echo'<table style="width:100%;" class="selection">';
		$i=0;
		?>
	 <style> 
	  .odd{background-color: white;}
	  .even{background-color:#CCCCCC;}   
	  </style>
	<tr>
		<th style="font-size:18px;" colspan="9"><strong><center><?php echo $AccountName; ?></center></strong></th>
		</tr>
	 <tr>
		<th style="font-size:18px;" colspan="4"><strong><center>Receipt (Debit Side)</center></strong></th>
		<th style="font-size:18px;" colspan="10" ><strong><center>Payments (Credit Side)</enter></strong></th>
	 </tr>
	<tr>
		<th  style="font-size:18px;" colspan="0">Pervious Month</th><td  style="font-size:18px;"><strong><?php echo $cashs['Pervious_Month']; ?></strong></td>
		<th  style="font-size:18px;" colspan="0">Balance B/F</th>  <td  style="font-size:18px;"><strong><?php echo locale_number_format($AccountPeriodActual,2); ?></strong></td>
	<?php                        
  echo'</tr>
	  <tr>
		<th>Date</th>
		<th>Ref</th>
		<th>Description</th>
		<th>Cash</th>
		<th>Bank</th>
		<th>Date</th>
		<th>Ref</th>
		<th>Description</th>
		<th>Cash</th>
		<th>Bank</th>
	  </tr>';
	  $Debittypeno='';
	while ($cashs  = DB_fetch_array($result_cash)){	 
				if($i%2 ==0){$class='even';}else{$class='odd';}
				$i++;
				if($cashs['type'] ==12) {
				 $Debittypeno =$cashs['TransNo'];
				 }else{
				 $Debittypeno =0;
				 }
				if($cashs['type'] ==12) {
				   /* $Ddate = $cashs['date'];
					$DAccount = $cashs['Account'];
					$DDesc = $cashs['descs'];
					$DebitAmount_cash = $cashs['debit'];
					$DebitAmount = locale_number_format($DebitAmount2,2);
					$DebitTotal3 += $cashs['debit'];
					$AccountPeriodActual3 = $AccountPeriodActual+$DebitTotal3;                
					$CreditAmount = '&nbsp;';
					$CDesc = '&nbsp;';
					$Cdate = '&nbsp;';*/
					$AccountPeriodActual3 = $AccountPeriodActual+$DebitTotal3;
				    $Ddate = $cashs['date'];
					$DAccount = $cashs['Account'];
					$DDesc = $cashs['descs'];
					$DebitAmountCash +=$cashs['debit'];
					
					##########################################################
					$DebitAmount_Cheque = $cashs['debit'];
					$DebitAmount = locale_number_format($DebitAmount_Cheque,2);
					if($cashs['Payment_Type'] =='Cash') {
					
					 }elseif($cashs['Payment_Type'] =='Cheque') {
					   $DebitAmountCheque +=$cashs['debit'];
					  //$Debittypeno =$cashs['TransNo'];
					 }
					##########################################################
					$DebitTotal3 += $cashs['debit'];
					//$AccountPeriodActual3 = $AccountPeriodActual;                
					$CreditAmount = '&nbsp;';
					$CDesc = '&nbsp;';
					$Cdate = '&nbsp;';
				} else{
				    $Cdate = $cashs['date'];
					$CAccount = $cashs['Account'];
					$Payment_Type = $cashs['Payment_Type'];
					$CDesc = $cashs['descs'];
					$CreditAmount2 = $cashs['credit'];
					$CreditAmount = locale_number_format($CreditAmount2,2);
					##########################################################################3
					if($cashs['Payment_Type'] =='Cash') {
			            $CreditAmountCash +=$cashs['credit'];
					 }else{
					   $CreditTotal += $cashs['credit'];
					 }
					#############################################################################
					
					$PV_Voucherid = $cashs['authorityref'];
					$URL_to_PV_Details = $RootPath . '/index.php?Application=PVM&Ref=default&Link=PV_Report?PV_ID=' . $myrow['authorityref'];
				   	
					$DebitAmount = '&nbsp;';
					$DDesc = '&nbsp;';
					$Ddate = '&nbsp;';
					$Total_Amount += $cashs['credit'];
				}
			$balance = $AccountPeriodActual3 - $CreditTotal;
			$balanceCash = $DebitAmountCash - $CreditAmountCash;
				echo '<tr class='.$class.' align="center" style=font-size:10pt>';
				echo'<td>' . $Ddate .'</td>
					  <td>'.$Debittypeno.'</td>
					  <td>'. $DDesc .'</td>';
					if($cashs['Payment_Type'] =='Cash') {
			   echo'<td>'.$DebitAmount.'</td>
					   <td></td>';
					 }else{
				echo'<td></td>
					  <td>'.$DebitAmount.'</td>';
					   }
					
					
					   				   
				echo'<td>'. $Cdate .'</td>
					  <td><a href="' . $URL_to_PV_Details . '">' .$PV_Voucherid . '</a></td>
					  <td>'. $CDesc .'</td>';
					  
					  if($cashs['Payment_Type'] =='Cash') {
			   echo'<td>'.$CreditAmount.'</td>
					   <td></td>';
					 }else{
				echo'<td></td>
					  <td>'.$CreditAmount.'</td>';
					   }			  
				echo'</tr>';
				}
				echo'<tr>
				 <td></td>
				</tr>';
				echo '<tr class='.$class.' align="center" style=font-size:10pt>
					  <td><strong></strong></td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <th><strong></strong></th>
					  <td colspan="2"><strong>Balance C/F</strong></td>
					  <td></td>
					  <td><strong>'. locale_number_format($CreditAmountCash,2) .'</td>
					  <th><strong>'. locale_number_format($CreditTotal,2) .'</strong></th>
				</tr>
				<tr class='.$class.' align="center" style=font-size:10pt>
					  <td><strong>Total Balance B/F</strong></td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td><strong>'. locale_number_format($balanceCash,2) .'</td>
					  <td><strong>'. locale_number_format($balance,2) .'</strong></td>
					</tr>
					<tr class='.$class.' align="center" style=font-size:10pt>
					  <td><strong>Total Receipts</strong></td>
					  <td></td>
					  <td></td>
					  <td><strong>'. locale_number_format($DebitAmountCash,2) .'</strong></td>
					  <th><strong>'. locale_number_format($AccountPeriodActual3,2) .'</strong></th>
					  <td colspan="2"><strong></strong></td>
					  <td></td>
					  <td><strong>'. locale_number_format($DebitAmountCash,2) .'</strong></td>
					  <th><strong>'. locale_number_format($AccountPeriodActual3,2) .'</strong></th>
				</tr>
				<tr></tr>
				<tr></tr>
				<tr class='.$class.' align="center" style=font-size:10pt>
					  <td><strong>Balance B/F</strong></td>
					  <td></td>
					  <td></td>
					  <td>'. locale_number_format($balanceCash,2) .'</td>
					  <th><strong>'. locale_number_format($balance,2) .'</strong></th>
					  <td colspan="2"><strong></strong></td>
					  <td></td>
					  <td></td>
					  <th><strong></strong></th>
				</tr>
					</table>';
					
	} //end if no bank trans in the range to show

	echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">
              <div>
                   <input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
                   <br />
                   <div class="centre">
                        <input type="submit" name="Return" value="' . _('Select Another Date'). '" />
                   </div>
             </div>
             </form>';
}
include('includes/footer.inc');
?>
