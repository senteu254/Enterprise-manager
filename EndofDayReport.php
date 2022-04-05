<?php

/* $Id: SalesByTypePeriodInquiry.php 4261 2010-12-22 15:56:50Z tim_schofield $*/

include('includes/session.inc');
$Title = _('End Of Day Report');
include('includes/header.inc');

if (!isset($_POST['Date'])){
		$_POST['Date'] = Date($_SESSION['DefaultDateFormat']);
	}
if (!isset($_POST['ToDate'])){
		$_POST['ToDate'] = Date($_SESSION['DefaultDateFormat']);
	}

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/transactions.png" title="' . _('End Of Day Report') . '" alt="" />' . ' ' . _('End Of Day Report') . '</p>';
echo '<div class="page_help_text">' . _('Select the parameters for the report') . '</div><br />';


echo '<form id="Form1" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
$array = array(1=>"Detailed End Of Day Report",2=>"Summary End Of Day Report");
echo '<table cellpadding="2" class="selection"><tr>
			<td>' . _('From Date') . ':</td>
			<td><input type="text" class="date" alt="' . $_SESSION['DefaultDateFormat'] . '" name="Date" maxlength="10" size="11" value="' . $_POST['Date'] . '" /></td>
			</tr>
			<tr>
			<td>' . _('To Date') . ':</td>
			<td><input type="text" class="date" alt="' . $_SESSION['DefaultDateFormat'] . '" name="ToDate" maxlength="10" size="11" value="' . $_POST['ToDate'] . '" /></td>
			</tr>
		<tr><td>' . _('Type') . ':</td><td><select name="Type">';
		foreach($array as $key=>$value){
		echo '<option '.($key==$_POST['Type'] ? 'selected':'').' value="'.$key.'">'.$value.'</option>';
		}
	echo '</select></td></tr>
		</table>';


echo '<br />
		<div class="centre">
			<input tabindex="4" type="submit" name="ShowSales" value="' . _('Show End of Day') . '" />
		</div>
        </div>
		</form>
		<br />';


if (isset($_POST['ShowSales'])){

			$FromDate = FormatDateForSQL($_POST['Date']);
			$ToDate = FormatDateForSQL($_POST['ToDate']);
			
	if (isset($_POST['Type']) && $_POST['Type']==2){
			
			$sql = "SELECT debtortrans.trandate,
							debtortrans.transno,
						SUM(CASE WHEN stockmoves.type=10 THEN
							price*(1-discountpercent)* -qty
							ELSE 0 END) as salesvalue
					FROM stockmoves
					INNER JOIN debtortrans
					ON stockmoves.type=debtortrans.type
					AND stockmoves.transno=debtortrans.transno
					WHERE stockmoves.type=10
					AND show_on_inv_crds =1
					AND debtortrans.debtorno =200
					AND debtortrans.trandate>='" . $FromDate . "'
					AND debtortrans.trandate<='" . $ToDate . "'";

			$sql .= " GROUP BY debtortrans.trandate ORDER BY debtortrans.trandate,debtortrans.transno";

	$ErrMsg = _('The sales data could not be retrieved because') . ' - ' . DB_error_msg();
	$SalesResult = DB_query($sql,$ErrMsg);


	echo '<table cellpadding="2" class="selection">';

	$CumulativeTotalSales = 0;

	$k=0;
	$SalesRow=DB_fetch_array($SalesResult);
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}

		echo '<td>Invoices Total</td>
				<td class="number">' . locale_number_format($SalesRow['salesvalue'],$_SESSION['CompanyRecord']['decimalplaces']) . '</td>
			</tr>';
	
	
				$sqlqq = "SELECT debtortrans.trandate,
							debtortrans.transno,
						 SUM(CASE WHEN stockmoves.type=11 THEN
							price*(1-discountpercent)* (-qty)
							ELSE 0 END) as returnvalue
					FROM stockmoves
					INNER JOIN debtortrans
					ON stockmoves.type=debtortrans.type
					AND stockmoves.transno=debtortrans.transno
					WHERE stockmoves.type=11
					AND show_on_inv_crds =1
					AND debtortrans.debtorno =200
					AND debtortrans.trandate>='" . $FromDate . "'
					AND debtortrans.trandate<='" . $ToDate . "'
                    GROUP BY debtortrans.trandate ORDER BY debtortrans.trandate,debtortrans.transno";

	$ErrMsg = _('The sales data could not be retrieved because') . ' - ' . DB_error_msg();
	$Return = DB_query($sqlqq,$ErrMsg);
	$Return=DB_fetch_array($Return);
		echo '<tr class="EvenTableRows"><td>Returns Total</td>
				<td class="number">' . locale_number_format(-$Return['returnvalue'],$_SESSION['CompanyRecord']['decimalplaces']) . '</td>
			</tr>';

			$sql = "SELECT banktrans.transdate,
							banktrans.transno,
						    SUM(amount) as salesvalue,
						    banktranstype,
							paymentname
					FROM banktrans
					INNER JOIN paymentmethods ON paymentmethods.paymentid=banktrans.banktranstype
					WHERE banktrans.type=12
					AND banktrans.transdate>='" . $FromDate . "'
					AND banktrans.transdate<='" . $ToDate . "'";

			$sql .= " GROUP BY banktrans.transdate, banktranstype ORDER BY banktranstype,banktrans.transno";

	$ErrMsg = _('The sales data could not be retrieved because') . ' - ' . DB_error_msg();
	$SalesResult = DB_query($sql,$ErrMsg);

	//echo '<table cellpadding="2" class="selection">';

	echo'<tr>
		<th>' . _('Receipt No') . '</th>
		<th>' . _('Amount') . '</th>
		</tr>';

	$CumulativeTotalSales = 0;
	$PrdTotalSales=0;
	$PaymentType = "";
	$LastPeriodHeading = 'First Run Through';

	$k=0;
	while ($SalesRow=DB_fetch_array($SalesResult)) {

		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}
		echo '<td>' . $SalesRow['paymentname'] . '</td>
				<td class="number">' . locale_number_format($SalesRow['salesvalue'],$_SESSION['CompanyRecord']['decimalplaces']) . '</td>
			</tr>';
		$CumulativeTotalSales += $SalesRow['salesvalue'];	
	}
	echo '<th class="number">' . _('GRAND Total') . '</th>
		<th class="number">' . locale_number_format($CumulativeTotalSales,$_SESSION['CompanyRecord']['decimalplaces']) . '</th>
		</tr>'; 

	
	if ($k==1){
		echo '<tr class="EvenTableRows"><td colspan="8"><hr /></td></tr>';
		echo '<tr class="OddTableRows">';
	} else {
		echo '<tr class="OddTableRows"><td colspan="8"><hr /></td></tr>';
		echo '<tr class="EvenTableRows">';
	}

			$sql = "SELECT debtortrans.trandate,
							debtortrans.type,
						CASE WHEN debtortrans.type=10 THEN
							'Invoice to Account'
							ELSE 'Charge to Account' END as label,
						SUM(ABS(ovamount)+ABS(ovgst)) as total
					FROM debtortrans
					WHERE (debtortrans.type=10 or debtortrans.type=12)
					AND debtortrans.debtorno <>200
					AND debtortrans.trandate>='" . $FromDate . "'
					AND debtortrans.trandate<='" . $ToDate . "'";
					
				$sql .= " GROUP BY debtortrans.trandate,debtortrans.type ORDER BY debtortrans.type";

	$ErrMsg = _('The sales data could not be retrieved because') . ' - ' . DB_error_msg();
	$SalesResult = DB_query($sql,$ErrMsg);

	$k=0;
	while ($SalesRow=DB_fetch_array($SalesResult)) {
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}

		echo '<td>' . $SalesRow['label'] . '</td>
				<td class="number">' . locale_number_format($SalesRow['total'],$_SESSION['CompanyRecord']['decimalplaces']) . '</td>
			</tr>';
		
		if($SalesRow['type'] ==12){
		//$CumulativeTotalSales += $SalesRow['total'];
		}
	}
	
	if ($k==1){
		echo '<tr class="EvenTableRows"><td colspan="8"><hr /></td></tr>';
		echo '<tr class="OddTableRows">';
	} else {
		echo '<tr class="OddTableRows"><td colspan="8"><hr /></td></tr>';
		echo '<tr class="EvenTableRows">';
	}
	echo '<th class="number">' . _('Total Cash on Hand') . '</th>
		<th class="number">' . locale_number_format($CumulativeTotalSales,$_SESSION['CompanyRecord']['decimalplaces']) . '</th>
		</tr>'; 

	
	if ($k==1){
		echo '<tr class="EvenTableRows"><td colspan="8"><hr /></td></tr>';
		echo '<tr class="OddTableRows">';
	} else {
		echo '<tr class="OddTableRows"><td colspan="8"><hr /></td></tr>';
		echo '<tr class="EvenTableRows">';
	}

	echo '</table>';

	
	}else{
			
			$sql = "SELECT debtortrans.trandate,
							debtortrans.transno,
						SUM(CASE WHEN stockmoves.type=10 THEN
							price*(1-discountpercent)* -qty
							ELSE 0 END) as salesvalue
					FROM stockmoves
					INNER JOIN debtortrans
					ON stockmoves.type=debtortrans.type
					AND stockmoves.transno=debtortrans.transno
					WHERE stockmoves.type=10
					AND show_on_inv_crds =1
					AND debtortrans.debtorno =200
					AND debtortrans.trandate>='" . $FromDate . "'
					AND debtortrans.trandate<='" . $ToDate . "'";

			$sql .= " GROUP BY debtortrans.trandate,transno ORDER BY debtortrans.trandate,transno";

	$ErrMsg = _('The sales data could not be retrieved because') . ' - ' . DB_error_msg();
	$SalesResult = DB_query($sql,$ErrMsg);


	echo '<table cellpadding="2" class="selection">';

	echo'<tr>
		<th>' . _('Invoice') . '</th>
		<th>' . _('Amount') . '</th>
		</tr>';

	$CumulativeTotalSales = 0;

	$k=0;
	while ($SalesRow=DB_fetch_array($SalesResult)) {
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}

		echo '<td>' . $SalesRow['transno'] . '</td>
				<td class="number">' . locale_number_format($SalesRow['salesvalue'],$_SESSION['CompanyRecord']['decimalplaces']) . '</td>
			</tr>';

		$CumulativeTotalSales += $SalesRow['salesvalue'];
	}
	

	echo '<th class="number">' . _('Total') . '</th>
		<th class="number">' . locale_number_format($CumulativeTotalSales,$_SESSION['CompanyRecord']['decimalplaces']) . '</th>
		</tr>';

		if ($k==1){
		echo '<tr class="EvenTableRows"><td colspan="8"><hr /></td></tr>';
		echo '<tr class="OddTableRows">';
	} else {
		echo '<tr class="OddTableRows"><td colspan="8"><hr /></td></tr>';
		echo '<tr class="EvenTableRows">';
	}
	
			$sqlqq = "SELECT debtortrans.trandate,
							debtortrans.transno,
						 SUM(CASE WHEN stockmoves.type=11 THEN
							price*(1-discountpercent)* (-qty)
							ELSE 0 END) as returnvalue
					FROM stockmoves
					INNER JOIN debtortrans
					ON stockmoves.type=debtortrans.type
					AND stockmoves.transno=debtortrans.transno
					WHERE stockmoves.type=11
					AND show_on_inv_crds =1
					AND debtortrans.debtorno =200
					AND debtortrans.trandate>='" . $FromDate . "'
					AND debtortrans.trandate<='" . $ToDate . "'
                    GROUP BY debtortrans.trandate,debtortrans.transno ORDER BY debtortrans.trandate,debtortrans.transno";

	$ErrMsg = _('The sales data could not be retrieved because') . ' - ' . DB_error_msg();
	$Returns = DB_query($sqlqq,$ErrMsg);
	
	echo'<tr>
		<th>' . _('Returns') . '</th>
		<th>' . _('Amount') . '</th>
		</tr>';
	$CumulativeTotalReturns = 0;
		$k=0;
	while ($Return=DB_fetch_array($Returns)) {

		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}
		echo '<td>' . $Return['transno'] . '</td>
				<td class="number">' . locale_number_format(-$Return['returnvalue'],$_SESSION['CompanyRecord']['decimalplaces']) . '</td>
			</tr>';
		$CumulativeTotalReturns += $Return['returnvalue'];	
	}
	echo '<th class="number">' . _('Total') . '</th>
		<th class="number">' . locale_number_format(-$CumulativeTotalReturns,$_SESSION['CompanyRecord']['decimalplaces']) . '</th>
		</tr>';
	

			$sql = "SELECT banktrans.transdate,
							banktrans.transno,
						    amount as salesvalue,
						    banktranstype,
							paymentname
					FROM banktrans
					INNER JOIN paymentmethods ON paymentmethods.paymentid=banktrans.banktranstype
					INNER JOIN debtortrans ON debtortrans.transno=banktrans.transno
					WHERE banktrans.type=12
					AND debtortrans.debtorno=200
					AND banktrans.transdate>='" . $FromDate . "'
					AND banktrans.transdate<='" . $ToDate . "'";

			$sql .= " ORDER BY banktranstype,banktrans.transno";

	$ErrMsg = _('The sales data could not be retrieved because') . ' - ' . DB_error_msg();
	$SalesResults = DB_query($sql,$ErrMsg);

	//echo '<table cellpadding="2" class="selection">';

	echo'<tr>
		<th>' . _('Receipt No') . '</th>
		<th>' . _('Amount') . '</th>
		</tr>';

	$CumulativeTotalSales = 0;
	$PrdTotalSales=0;
	$PaymentType = "";
	$LastPeriodHeading = 'First Run Through';

	$k=0;
	while ($SalesRow=DB_fetch_array($SalesResults)) {
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}

		if($PaymentType != $SalesRow['banktranstype']){
		if ($LastPeriodHeading != 'First Run Through'){
		echo '<th class="number">' . _('Total') . '</th>
		<th class="number">' . locale_number_format($PrdTotalSales,$_SESSION['CompanyRecord']['decimalplaces']) . '</th>
		</tr>';
		}
		echo '<th colspan="2">' . $SalesRow['paymentname'] . '</th></tr>';
		$PaymentType = $SalesRow['banktranstype'];
		$PrdTotalSales =0;
		$LastPeriodHeading = '';
		}
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}
		echo '<td>' . $SalesRow['transno'] . '</td>
				<td class="number">' . locale_number_format($SalesRow['salesvalue'],$_SESSION['CompanyRecord']['decimalplaces']) . '</td>
			</tr>';

		$PrdTotalSales += $SalesRow['salesvalue'];
		$CumulativeTotalSales += $SalesRow['salesvalue'];
	}
	echo '<th class="number">' . _('Total') . '</th>
		<th class="number">' . locale_number_format($PrdTotalSales,$_SESSION['CompanyRecord']['decimalplaces']) . '</th>
		</tr>';
	
	if ($k==1){
		echo '<tr class="EvenTableRows"><td colspan="8"><hr /></td></tr>';
		echo '<tr class="OddTableRows">';
	} else {
		echo '<tr class="OddTableRows"><td colspan="8"><hr /></td></tr>';
		echo '<tr class="EvenTableRows">';
	}
	echo '<th class="number">' . _('GRAND Total') . '</th>
		<th class="number">' . locale_number_format($CumulativeTotalSales,$_SESSION['CompanyRecord']['decimalplaces']) . '</th>
		</tr>'; 

	
	if ($k==1){
		echo '<tr class="EvenTableRows"><td colspan="8"><hr /></td></tr>';
		echo '<tr class="OddTableRows">';
	} else {
		echo '<tr class="OddTableRows"><td colspan="8"><hr /></td></tr>';
		echo '<tr class="EvenTableRows">';
	}

			$sql = "SELECT debtortrans.trandate,
							debtortrans.type,
						CASE WHEN debtortrans.type=10 THEN
							'Invoice to Account'
							ELSE 'Charge to Account' END as label,
						SUM(ABS(ovamount)+ABS(ovgst)) as total
					FROM debtortrans
					WHERE (debtortrans.type=10 or debtortrans.type=12)
					AND debtortrans.debtorno <>200
					AND debtortrans.trandate>='" . $FromDate . "'
					AND debtortrans.trandate<='" . $ToDate . "'";
					
				$sql .= " GROUP BY debtortrans.type ORDER BY debtortrans.type";

	$ErrMsg = _('The sales data could not be retrieved because') . ' - ' . DB_error_msg();
	$SalesResult = DB_query($sql,$ErrMsg);

	$k=0;
	while ($SalesRow=DB_fetch_array($SalesResult)) {
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}

		echo '<td>' . $SalesRow['label'] . '</td>
				<td class="number">' . locale_number_format($SalesRow['total'],$_SESSION['CompanyRecord']['decimalplaces']) . '</td>
			</tr>';
		
		if($SalesRow['type'] ==12){
		$CumulativeTotalSales += $SalesRow['total'];
		}
	}
	
	if ($k==1){
		echo '<tr class="EvenTableRows"><td colspan="8"><hr /></td></tr>';
		echo '<tr class="OddTableRows">';
	} else {
		echo '<tr class="OddTableRows"><td colspan="8"><hr /></td></tr>';
		echo '<tr class="EvenTableRows">';
	}
	echo '<th class="number">' . _('Total Cash on Hand') . '</th>
		<th class="number">' . locale_number_format($CumulativeTotalSales,$_SESSION['CompanyRecord']['decimalplaces']) . '</th>
		</tr>'; 

	
	if ($k==1){
		echo '<tr class="EvenTableRows"><td colspan="8"><hr /></td></tr>';
		echo '<tr class="OddTableRows">';
	} else {
		echo '<tr class="OddTableRows"><td colspan="8"><hr /></td></tr>';
		echo '<tr class="EvenTableRows">';
	}

	echo '</table>';
	
	}

	echo '<a target="_blank" href="'.$RootPath.'/ReceiptPrinter_EndofDay.php?Date='.$_POST['Date'].'&ToDate='.$_POST['ToDate'].'&Type='.$_POST['Type'].'" ><input  type="submit" style="height:50px;" class="Submit" name="" value="' . _('Print End of Day') . '" /></a>';

} //end of if user hit show sales

include('includes/footer.inc');
?>
