<?php

/* $Id: SupplierBalsAtPeriodEnd.php 6944 2014-10-27 07:15:34Z daintree $*/

include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

//include('includes/header.inc');
if (isset($_POST['Closebalanance'])
	AND mb_strlen($_POST['ToCriteria'])>=1){
	$date = 'AT '.date($_SESSION['DefaultDateFormat']);
	$Title=_('Customer Balances At '.$date.'');
	include('includes/header.inc');
	
	
$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
		  			currencies.currency,
		  			currencies.decimalplaces,
					SUM((debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/debtortrans.rate) AS balance,
					SUM(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc) AS fxbalance,
					SUM(CASE WHEN debtortrans.trandate > '" . $_POST['PeriodEnd'] . "' THEN
					(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount)/debtortrans.rate ELSE 0 END) AS afterdatetrans,
					SUM(CASE WHEN debtortrans.trandate > '" . $_POST['PeriodEnd'] . "'
						AND (debtortrans.type=11 OR debtortrans.type=12) THEN
						debtortrans.diffonexch ELSE 0 END) AS afterdatediffonexch,
					SUM(CASE WHEN debtortrans.trandate > '" . $_POST['PeriodEnd'] . "' THEN
					debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount ELSE 0 END
					) AS fxafterdatetrans
			FROM debtorsmaster INNER JOIN currencies
			ON debtorsmaster.currcode = currencies.currabrev
			INNER JOIN debtortrans
			ON debtorsmaster.debtorno = debtortrans.debtorno
			WHERE debtorsmaster.debtorno >= '" . $_POST['FromCriteria'] . "'
			AND debtorsmaster.debtorno <= '" . $_POST['ToCriteria'] . "'
			AND debtortrans.status=0
			GROUP BY debtorsmaster.debtorno,
				debtorsmaster.name,
				currencies.currency,
				currencies.decimalplaces";

	$SupplierResult = DB_query($SQL);
	
//include('includes/header.inc');
  	echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
	echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';	
			echo'<table style="width:780px;">';
			echo'<tr>
			<th>Srn</th>
			<th>Code</th>
			<th>Customer Name</th>
			<th>Customer Balance</th>
			<th>End of Fy Year</th>
			</tr>';

	$TotBal=0;
    $k=0;
	while ($SupplierBalances = DB_fetch_array($SupplierResult,$db)){
      $type=$SupplierBalances['type'];
		$Balance = $SupplierBalances['balance'] - $SupplierBalances['afterdatetrans'] + $SupplierBalances['afterdatediffonexch'];
		$FXBalance = $SupplierBalances['fxbalance'] - $SupplierBalances['fxafterdatetrans'];

		if (ABS($Balance)>0.009 OR ABS($FXBalance)>0.009) {
			$DisplayBalance = locale_number_format($SupplierBalances['balance'] - $SupplierBalances['afterdatetrans'] + $SupplierBalances['afterdatediffonexch'],$_SESSION['CompanyRecord']['decimalplaces']);
			$DisplayFXBalance = locale_number_format($SupplierBalances['fxbalance'] - $SupplierBalances['fxafterdatetrans'],$SupplierBalances['currdecimalplaces']);
$DisplayTotBalance = locale_number_format($TotBal,$_SESSION['CompanyRecord']['decimalplaces']);
			$TotBal += -1*($Balance);
			if ($k==1){
			  echo'<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}
			echo'<td></td>';
			echo'<td>'.$SupplierBalances['debtorno'].'</td>';
			echo'<td>'.$SupplierBalances['name'].'</td>';
			echo'<td>'.$DisplayBalance.'</td>';
			echo'<td>'.$_POST['PeriodEnd'].'</td>';
			
			echo'</tr>';	
			
				if (isset($_POST['Close'])) {
				
				if($DisplayBalance>0): $typed=580; else: $typed=590; endif;	
				$invoicesBF = GetNextTransNo($typed, $db);				
				
DB_query("UPDATE debtortrans SET status=1 WHERE status=0 AND debtorno='".$SupplierBalances['debtorno']."' AND trandate<='" . $_POST['PeriodEnd'] . "'");
//DB_query("UPDATE debtortrans SET status=1 WHERE status=0 AND trandate <='" . date('Y-m-d') . "'");

        $sql = "INSERT INTO debtortrans (transno,
										type,
										debtorno,
										branchcode,
										trandate,
										inputdate,
										prd,
										settled,
										reference,
										tpe,
										order_,
										rate,
										ovamount,
										ovgst,
										ovfreight,
										ovdiscount,
										diffonexch,
										alloc,
										invtext,
										shipvia,
										edisent,
										consignment,
										packages,
										salesperson,
										bankacc) 
					VALUES ('".$invoicesBF."',
				     	".$typed.",
						'".$SupplierBalances['debtorno']."',
						'".$SupplierBalances['debtorno']."',
					    '" . $_POST['PeriodEnd'] . "',
						'" . date('Y-m-d') . "',
						'".$PeriodNo."',
						0,
						'',
						'',	
						'',
						1,	    
						'".filter_number_format($DisplayBalance)."',
						'',
						0,
						'',
						'',
						'',
						'',
						'',
						'',
						'',
						'',
						'',
						'')";
		$resulta = DB_query($sql);
		$msq = _('Customer Balance has been  succesfully Closed');
		
	
 }
		}
		
	} /*end Supplier aged analysis while loop */
echo'</table>';

if(isset($msq)): prnMsg( $msq,'success'); endif;
echo'<br />
			<div class="centre">
			<input type="hidden" maxlength="6" size="7" name="FromCriteria" value="'.$_POST['FromCriteria'].'" />
			<input type="hidden" maxlength="6" size="7" name="ToCriteria" value="'.$_POST['ToCriteria'].'" />
			<input type="hidden" maxlength="6" size="7" name="PeriodEnd" value="'.$_POST['PeriodEnd'].'" />
			<input type="hidden" maxlength="6" size="7" name="Closebalanance" value="Closebalanance" />
				<input type="submit" name="Close" value="' . _('Close FY Balance') . '" />
			</div>';
			
			
echo'</form>';

	}else{ /*The option to print PDF was not hit */

	$Title=_('Customer Balances At A Period End');
	include('includes/header.inc');

	echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/transactions.png" title="' .
		_('Supplier Allocations') . '" alt="" />' . ' ' . $Title . '</p>';
	if (!isset($_POST['FromCriteria'])) {
		$_POST['FromCriteria'] = '1';
	}
	if (!isset($_POST['ToCriteria'])) {
		$_POST['ToCriteria'] = 'zzzzzz';
	}
	/*if $FromCriteria is not set then show a form to allow input	*/

	echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
    echo '<div>';
    echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

    echo '<table class="selection">';
    echo '<tr>
			<td>' . _('From Customer Code') . ':</td>
			<td><input type="text" maxlength="6" size="7" name="FromCriteria" value="'.$_POST['FromCriteria'].'" /></td>
		</tr>
		<tr>
			<td>' . _('To Customer Code') . ':</td>
			<td><input type="text" maxlength="6" size="7" name="ToCriteria" value="'.$_POST['ToCriteria'].'" /></td>
		</tr>
		<tr>
			<td>' . _('Balances As At') . ':</td>
			<td><select name="PeriodEnd">';

	$sql = "SELECT periodno,
					lastdate_in_period
			FROM periods
			ORDER BY periodno DESC";

	$ErrMsg = _('Could not retrieve period data because');
	$Periods = DB_query($sql,$ErrMsg);

	while ($myrow = DB_fetch_array($Periods,$db)){
		echo '<option value="' . $myrow['lastdate_in_period'] . '" selected="selected" >' . MonthAndYearFromSQLDate($myrow['lastdate_in_period'],'M',-1) . '</option>';
	}
	echo '</select></td>
		</tr>';
 		
	echo '</table>
			<br />
			<div class="centre">';
				//<input type="submit" name="PrintPDF" value="' . _('Print PDF') . '" /><br><br><br>
				echo'<input type="submit" name="Closebalanance" value="' . _('Search') . '" />
			</div>';
    echo '</div>
          </form>';
	include('includes/footer.inc');
}/*end of else not PrintPDF */

?>