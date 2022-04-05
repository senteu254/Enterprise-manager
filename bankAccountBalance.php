<?php
include('includes/session.inc');
$Title = _('Bank Account Balance');;// Screen identificator.
$ViewTopic = 'BankAccountBalance';// Filename's id in ManualContents.php's TOC.
/* To do this section in the manual.
$BookMark = 'BankAccountBalance';// Anchor's id in the manual's html document.*/
include('includes/header.inc');
echo '<p class="page_title_text"><img alt="" src="'.$RootPath.'/css/'.$Theme.
	'/images/bank.png" title="' .
	_('Bank AccountBalance') . '" /> ' .// Icon title.
	_('Maintenance Of Bank Balance') . '</p>';// Page title.

if (isset($_POST['SelectedBankAccount'])){
	$SelectedBankAccount = mb_strtoupper($_POST['SelectedBankAccount']);
} elseif (isset($_GET['SelectedBankAccount'])){
	$SelectedBankAccount = mb_strtoupper($_GET['SelectedBankAccount']);
}

if (isset($_POST['Cancel'])) {
	unset($SelectedBankAccount);
}

if (isset($_POST['Process'])) {
	if ($_POST['SelectedBankAccount'] == '') {
		echo prnMsg(_('You have not selected any bank account'),'error');
		echo '<br />';
		unset($SelectedBankAccount);
		unset($_POST['SelectedBankAccount']);
	}
}

if (isset($_POST['submit'])) {
	//get all the unposted transactions for the first and successive periods ordered by account
 $balance = $_POST['bankbalance'];
 $SelectedBankAccount=$_POST['SelectedBankAccount'];
 $FromPeriod = $_POST['FromPeriod'];
 $ToPeriod = $_POST['ToPeriod'];
		
	        $sql = "UPDATE chartdetails SET bfwd = " . $balance . "
					WHERE period >=" . $FromPeriod . " AND period <=" . $ToPeriod . "
					AND accountcode = ".$SelectedBankAccount ."";
##################################################################################################
							
			$msg = _('Balance for') . ': ' . SelectedBankAccount.' '._('has been Updated') .'';
			$result = DB_query($sql);
			prnMsg($msg,'success');
}
if (!isset($SelectedBankAccount)){

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
    echo '<div>';
			echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			if (Date('m') > $_SESSION['YearEnd']){
		/*Dates in SQL format */
		$DefaultFromDate = Date ('Y-m-d', Mktime(0,0,0,$_SESSION['YearEnd'] + 2,0,Date('Y')));
		$FromDate = Date($_SESSION['DefaultDateFormat'], Mktime(0,0,0,$_SESSION['YearEnd'] + 2,0,Date('Y')));
	} else {
		$DefaultFromDate = Date ('Y-m-d', Mktime(0,0,0,$_SESSION['YearEnd'] + 2,0,Date('Y')-1));
		$FromDate = Date($_SESSION['DefaultDateFormat'], Mktime(0,0,0,$_SESSION['YearEnd'] + 2,0,Date('Y')-1));
	}
	/*GetPeriod function creates periods if need be the return value is not used */
	$NotUsedPeriodNo = GetPeriod($FromDate, $db);			
			echo'<table class="selection">
			<tr>
				<td>' . _('Select Bank Account') . ':</td>
				<td><select name="SelectedBankAccount">';
	$SQL = "SELECT a.accountcode AS Bankacc,
	              b.accountcode AS Characc,
				  a.bankaccountname,
				  b.accountname 
				FROM bankaccounts a
				INNER JOIN chartmaster b ON a.accountcode=b.accountcode";
	$result = DB_query($SQL);
	echo '<option value="">' . _('Not Yet Selected') . '</option>';
	while ($myrow = DB_fetch_array($result)) {
		if (isset($SelectedBankAccount) and $myrow['accountcode']==$SelectedBankAccount) {
			echo '<option selected="selected" value="';
		} else {
			echo '<option value="';
		}
		echo $myrow['Bankacc'] . '">' . $myrow['Bankacc'] . ' - ' . $myrow['accountname'] . '</option>';

	} //end while loop
	echo '</select></td></tr>
          <tr>
				<td>' . _('Select Period From:') . '</td>
				<td><select name="FromPeriod">';
	$NextYear = date('Y-m-d',strtotime('+1 Year'));
	$sql = "SELECT periodno,
					lastdate_in_period
				FROM periods
				WHERE lastdate_in_period < '" . $NextYear . "'
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
   	echo '</table>'; // close main table
    DB_free_result($result);

	echo '<br />
		<div class="centre">
			<input type="submit" name="Process" value="' . _('Process') . '" />
			<input type="submit" name="Cancel" value="' . _('Cancel') . '" />
		</div>';
	echo '</div>
          </form>';
}
//end of ifs and buts!
if (isset($_POST['process'])OR isset($SelectedBankAccount)) {
	$SQLName = "SELECT a.bankaccountname,a.accountcode AS account,b.accountcode AS acc
				FROM bankaccounts a
				INNER JOIN chartmaster b ON a.accountcode=b.accountcode
				WHERE a.accountcode='" .$SelectedBankAccount."'";
	$result = DB_query($SQLName);
	$myrow = DB_fetch_array($result);
	$SelectedBankName = $myrow['bankaccountname'];
	$SelectedBankaccountnumber  = $myrow['bankaccountnumber '];
	$SelectedBankaddress  = $myrow['bankaddress '];
	$FromPeriod  = $_POST['FromPeriod'];	
	$ToPeriod  = $_POST['ToPeriod'];
	

	echo '<br /><div class="centre"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">' . _('Current Balance for') . ' ' .$SelectedBankName . ' ' . _('bank account') .'</a></div>';
	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	echo '<input type="hidden" name="SelectedBankAccount" value="' . $SelectedBankAccount . '" />';
	echo '<input type="hidden" name="ToPeriod" value="' . $ToPeriod . '" />';
	echo '<input type="hidden" name="FromPeriod" value="' . $FromPeriod . '" />';

	$sql = "SELECT *,
					Sum(CASE WHEN b.period='" . $_POST['FromPeriod'] . "' THEN b.bfwd ELSE 0 END) AS firstprdbfwd,
					Sum(CASE WHEN b.period='" . $_POST['FromPeriod'] . "' THEN b.bfwdbudget ELSE 0 END) AS firstprdbudgetbfwd,
					Sum(CASE WHEN b.period='" . $_POST['ToPeriod'] . "' THEN b.bfwd + b.actual ELSE 0 END) AS lastprdcfwd,
					Sum(CASE WHEN b.period='" . $_POST['ToPeriod'] . "' THEN b.actual ELSE 0 END) AS monthactual,
					Sum(CASE WHEN b.period='" . $_POST['ToPeriod'] . "' THEN b.budget ELSE 0 END) AS monthbudget,
					Sum(CASE WHEN b.period='" . $_POST['ToPeriod'] . "' THEN b.bfwdbudget + b.budget ELSE 0 END) AS lastprdbudgetcfwd
					FROM bankaccounts a
					INNER JOIN  chartdetails b ON a.accountcode=b.accountcode
					WHERE a.accountcode='".$SelectedBankAccount ."'";

	$result = DB_query($sql);
	while ($myrow = DB_fetch_array($result)) {
			$AccountPeriodActual = $myrow['lastprdcfwd'] - $myrow['firstprdbfwd'];
			$Accountcode = $myrow['accountcode'];
			$Accountname = $myrow['bankaccountname']; 	
			$AccountNumber = $myrow['bankaccountnumber'];
			$Accountcurrcode = $myrow['currcode'];
			$AccountPeriodActual = $myrow['lastprdcfwd'];
			$ActEnquiryURL = '<a href="'. $RootPath . '/GLAccountInquiry.php?FromPeriod=' . $_POST['FromPeriod'] . '&amp;ToPeriod=' . $_POST['ToPeriod'] . '&amp;
			Account=' . $myrow['accountcode'] . '&amp;Show=Yes">' . $myrow['accountcode'] . '</a>';
				}
	echo '<br />
			<table class="selection">';
			if ($k==1){
		echo '<tr class="EvenTableRows">';
		$k=0;
	} else {
		echo '<tr class="OddTableRows">';
		$k=1;
	}
	echo '<tr><th colspan="3"><h3>' . _('Balance for bank account') . ' ' .$SelectedBankName. '</h3></th></tr>';
	echo'<tr>'.$SelectedBankaccountnumber.'</tr>';
	echo'<tr>'.$SelectedBankaddress.'</tr>';
	echo '<tr>
			<th>' . _('Code') . '</th>
			<td>'.$ActEnquiryURL.'</td>
			</tr>
			<tr>
			<th>' . _('Account Name') . '</th>
			<td>'.$Accountname.'</td>
		</tr>
		<tr>
			<th>' . _('Account #') . '</th>
			<td>'.$AccountNumber.'</td>
		</tr>
		<tr>
			<th>' . _('Currency') . '</th>
			<td>'.$Accountcurrcode.'</td>
		</tr>';

$k=0; //row colour counter

	//END WHILE LIST LOOP
	echo '</table>';

		echo '<br /><table  class="selection">'; //Main table
		echo '<tr>
				<td>' . _('Banlace') . ':</td>
				<td><input name="bankbalance" type="text" value="'.locale_number_format($AccountPeriodActual,2).'" /></td></tr>';

	   	echo '</table>'; // close main table

		echo '<br /><div class="centre"><input type="submit" name="submit" onclick="return confirm_alert(this);" value="' . _('Update Balnce') . '" />
		     <input type="submit" name="Cancel" value="' . _('Cancel') . '" /></div>';

		echo '</div>
              </form>';
}

include('includes/footer.inc');
?>
<script>
function confirm_alert(node) {
    return confirm("Are you sure you want to update balance for selected Bank?"); 
}
</script>