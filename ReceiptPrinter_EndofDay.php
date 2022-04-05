<?php

 function _strlen($str,$str2,$str3, $use_encoding=FALSE, $encoding='utf8'){

		$len1 = strlen($str);
		$len2 = strlen($str2);
		$len3 = strlen($str3);
		if(($len1+$len2+$len3)>48){
			$diff=48-($len1+$len2+$len3);
			$newstr = substr($str, 0, $diff); 
			return $newstr.''.$str2.''.$str3;
		}elseif(($len1+$len2+$len3)< 48){
			$float= 48-($len1+$len2+$len3);
			$space = str_repeat(" ", $float);
			$newstr = $str.''.$space.''.$str2.''.$str3;
			return $newstr;
		}
    }
 function _strlen2($str1, $str2, $encoding='utf8'){
			$len1 = strlen($str1);
			$len2 = strlen($str2);
			$float= (48-($len1+$len2));
			$space = str_repeat(" ", $float);
			$newstr = $str1.''.$space.''.$str2;
			return $newstr;
		
    }

 if (isset($_GET['TransNo'])) {
	$FromTransNo = trim($_GET['TransNo']);
} elseif (isset($_POST['TransNo'])) {
	$FromTransNo = filter_number_format($_POST['TransNo']);
} else {
	$FromTransNo = '';
}

$date = $_GET['Date'];
$todate = $_GET['ToDate'];
$Type = $_GET['Type'];

include('includes/session.inc');
require __DIR__ . '/ReceiptPrinterLib/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage;

$connector = new WindowsPrintConnector("E-PoS");
$printer = new Printer($connector);
	
/* Initialize */
$printer -> initialize();

/* Text */
$printer -> setJustification(Printer::JUSTIFY_CENTER);
$printer -> text($_SESSION['CompanyRecord']['coyname']."\n");
$printer -> text($_SESSION['CompanyRecord']['regoffice1'].', '.$_SESSION['CompanyRecord']['regoffice2'].' '.$_SESSION['CompanyRecord']['regoffice6']."\n");
$printer -> text("Email:".$_SESSION['CompanyRecord']['email']."\n");
$printer -> text("TEL:".$_SESSION['CompanyRecord']['telephone']."  ".$_SESSION['TaxAuthorityReferenceName'] . ': ' . $_SESSION['CompanyRecord']['gstno']."\n");

$printer -> feed();
$printer -> text("END OF DAY at ".$date." ".date('h:i:s A')."\n");

if($Type==2){
$printer -> setUnderline(1);
$printer -> text("SUMMARY REPORT\n");
$printer -> setUnderline(0);
$printer -> setJustification(Printer::JUSTIFY_LEFT);
$printer -> text("------------------------------------------------\n");

			$FromDate = FormatDateForSQL($date);
			$ToDate = FormatDateForSQL($date);

			$sql = "SELECT debtortrans.trandate,
							debtortrans.transno,
						SUM(CASE WHEN stockmoves.type=10 THEN
							price*(1-discountpercent)* -qty
							ELSE 0 END) as salesvalue,
						 SUM(CASE WHEN stockmoves.type=11 THEN
							price*(1-discountpercent)* (-qty)
							ELSE 0 END) as returnvalue
					FROM stockmoves
					INNER JOIN debtortrans
					ON stockmoves.type=debtortrans.type
					AND stockmoves.transno=debtortrans.transno
					WHERE (stockmoves.type=10 or stockmoves.type=11)
					AND show_on_inv_crds =1
					AND debtortrans.debtorno =200
					AND debtortrans.trandate>='" . $FromDate . "'
					AND debtortrans.trandate<='" . $ToDate . "'";

			$sql .= " GROUP BY debtortrans.trandate ORDER BY debtortrans.trandate";

	$ErrMsg = _('The sales data could not be retrieved because') . ' - ' . DB_error_msg();
	$SalesResult = DB_query($sql,$ErrMsg);
	
	$CumulativeTotalSales = 0;
	
	$SalesRow=DB_fetch_array($SalesResult);
	$printer -> text(_strlen2("INVOICES TOTAL",locale_number_format($SalesRow['salesvalue'],$_SESSION['CompanyRecord']['decimalplaces']))."\n");
	$printer -> text("================================================\n");
	$printer -> feed();
	
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

			$sql .= " GROUP BY banktrans.transdate,banktranstype ORDER BY banktranstype";

	$ErrMsg = _('The sales data could not be retrieved because') . ' - ' . DB_error_msg();
	$SalesResult = DB_query($sql,$ErrMsg);
	$CumulativeTotalSales = 0;	
	while ($SalesRow=DB_fetch_array($SalesResult)) {	
	$printer -> text(_strlen2(strtoupper($SalesRow['paymentname']),locale_number_format($SalesRow['salesvalue'],$_SESSION['CompanyRecord']['decimalplaces']))."\n");
	$CumulativeTotalSales += $SalesRow['salesvalue'];
	}
	
	}else{
$printer -> setUnderline(1);
$printer -> text("DETAILED REPORT\n");
$printer -> setUnderline(0);
$printer -> setJustification(Printer::JUSTIFY_LEFT);
$printer -> text("------------------------------------------------\n");
$printer -> setEmphasis(true);
$printer -> text(_strlen2("Invoice","Amount")."\n");
$printer -> setEmphasis(false); // Reset
$printer -> text("------------------------------------------------\n");

			$FromDate = FormatDateForSQL($date);
			$ToDate = FormatDateForSQL($todate);

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

			$sql .= " GROUP BY debtortrans.trandate,transno ORDER BY debtortrans.trandate,transno ASC";

	$ErrMsg = _('The sales data could not be retrieved because') . ' - ' . DB_error_msg();
	$SalesResult = DB_query($sql,$ErrMsg);
	
	$CumulativeTotalSales = 0;
	
	while ($SalesRow=DB_fetch_array($SalesResult)) {
	$printer -> text(_strlen2($SalesRow['transno'],locale_number_format($SalesRow['salesvalue'],$_SESSION['CompanyRecord']['decimalplaces']))."\n");
	$CumulativeTotalSales += $SalesRow['salesvalue'];
	}
	$printer -> setEmphasis(true);
	$printer -> text(_strlen2("TOTAL",locale_number_format($CumulativeTotalSales,$_SESSION['CompanyRecord']['decimalplaces']))."\n");
	$printer -> setEmphasis(false);
	
	
$printer -> text("------------------------------------------------\n");
$printer -> setEmphasis(true);
$printer -> text(_strlen2("Returns","Amount")."\n");
$printer -> setEmphasis(false); // Reset
$printer -> text("------------------------------------------------\n");

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
	
	$CumulativeTotalRe = 0;
	
	while ($RRow=DB_fetch_array($Returns)) {
	$printer -> text(_strlen2($RRow['transno'],locale_number_format($RRow['returnvalue'],$_SESSION['CompanyRecord']['decimalplaces']))."\n");
	$CumulativeTotalRe += $RRow['returnvalue'];
	}
	$printer -> setEmphasis(true);
	$printer -> text(_strlen2("TOTAL",locale_number_format($CumulativeTotalRe,$_SESSION['CompanyRecord']['decimalplaces']))."\n");
	$printer -> setEmphasis(false);

$printer -> text("================================================\n");
$printer -> setEmphasis(true);
$printer -> text(_strlen2("Receipts","Amount")."\n");
$printer -> setEmphasis(false); // Reset
$printer -> text("------------------------------------------------\n");
$printer -> feed(1);
	
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

			$sql .= " ORDER BY banktranstype,banktrans.transno ASC";

	$ErrMsg = _('The sales data could not be retrieved because') . ' - ' . DB_error_msg();
	$SalesResult = DB_query($sql,$ErrMsg);
	
	
	$CumulativeTotalSales = 0;
	$PrdTotalSales=0;
	$PaymentType = "";
	$LastPeriodHeading = 'First Run Through';
	
	while ($SalesRow=DB_fetch_array($SalesResult)) {
	
	if($PaymentType != $SalesRow['banktranstype']){
		if ($LastPeriodHeading != 'First Run Through'){
		$printer -> setEmphasis(true);
		$printer -> text(_strlen2("TOTAL",locale_number_format($PrdTotalSales,$_SESSION['CompanyRecord']['decimalplaces']))."\n");
		$printer -> setEmphasis(false);
		}
		$printer -> feed();
		$printer -> setEmphasis(true);
		$printer -> setUnderline(1);
		$printer -> text(strtoupper($SalesRow['paymentname'])."\n");
		$printer -> setUnderline(0);
		$printer -> setEmphasis(false);
		$PaymentType = $SalesRow['banktranstype'];
		$PrdTotalSales =0;
		$LastPeriodHeading = '';
	}
	
	$printer -> text(_strlen2($SalesRow['transno'],locale_number_format($SalesRow['salesvalue'],$_SESSION['CompanyRecord']['decimalplaces']))."\n");
	
	$PrdTotalSales += $SalesRow['salesvalue'];
	$CumulativeTotalSales += $SalesRow['salesvalue'];
	}
	$printer -> setEmphasis(true);
	$printer -> text(_strlen2("TOTAL",locale_number_format($PrdTotalSales,$_SESSION['CompanyRecord']['decimalplaces']))."\n");
	$printer -> feed();
	}
	$printer -> setEmphasis(true);
	$printer -> text(_strlen2("GRAND TOTAL",locale_number_format($CumulativeTotalSales,$_SESSION['CompanyRecord']['decimalplaces']))."\n");
	$printer -> setEmphasis(false);
	$printer -> text("================================================\n");
	$printer -> feed();
	
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
	
	while ($SalesRow=DB_fetch_array($SalesResult)) {
	$printer -> text(_strlen2($SalesRow['label'],locale_number_format($SalesRow['total'],$_SESSION['CompanyRecord']['decimalplaces']))."\n");
		if($SalesRow['type'] ==12){
		$CumulativeTotalSales += $SalesRow['total'];
		}
	}
	
	$printer -> feed();
	$printer -> feed();
	$printer -> setEmphasis(true);
	$printer -> text(_strlen2("TOTAL CASH ON HAND",locale_number_format($CumulativeTotalSales,$_SESSION['CompanyRecord']['decimalplaces']))."\n");
	$printer -> setEmphasis(false);
	
$printer -> text("================================================\n");
$printer -> setJustification(Printer::JUSTIFY_CENTER);
$printer -> text("Generated By : ".$_SESSION['UserID']."\n");
$printer -> feed();
$printer -> feed();
$printer -> cut();

/* Pulse */
$printer -> pulse();

/* Always close the printer! On some PrintConnectors, no actual
 * data is sent until the printer is closed. */
$printer -> close();
echo "<script>window.close();</script>";