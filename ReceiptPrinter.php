<?php

 function _strlen($str,$str2,$str3, $use_encoding=FALSE, $encoding='utf8'){
		$len1 = mb_strlen($str, $encoding);
		$len2 = mb_strlen($str2, $encoding);
		$len3 = mb_strlen($str3, $encoding);
		$tot =($len1+$len2+$len3);
		if($tot>48){
			$str1 = substr($str, 0, 25); 
			$float= 48-(25+$len2+$len3);
			$space = str_repeat(" ", $float);
			$newstr = $str1.$str2.''.$space.''.$str3;
			return $newstr;
		}else{
			$len11 = 25-$len1;
			$space1 = str_repeat(" ", $len11);
			$len111 = $len1+$len11;
			$float= 48-($len111+$len2+$len3);
			$space = str_repeat(" ", $float);
			$newstr = $str.''.$space1.''.$str2.''.$space.''.$str3;
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
 function _strlen3($str,$str2,$str3, $use_encoding=FALSE, $encoding='utf8'){

		$len1 = strlen($str);
		$len2 = strlen($str2);
		$len3 = strlen($str3);
		if(($len1+$len2+$len3)>48){
			$diff=48-($len1+$len2+$len3);
			$newstr = substr($str, 0, $diff); 
			return $newstr.''.$str2.''.$str3;
		}elseif(($len1+$len2+$len3)< 48){
			$len11 = 23-$len1;
			$space1 = str_repeat(" ", $len11);
			$len111 = $len1+$len11;
			$float= 48-($len111+$len2+$len3);
			$space = str_repeat(" ", $float);
			$newstr = $str.''.$space1.''.$str2.''.$space.''.$str3;
			return $newstr;
		}
    }

 if (isset($_GET['TransNo'])) {
	$FromTransNo = trim($_GET['TransNo']);
} elseif (isset($_POST['TransNo'])) {
	$FromTransNo = filter_number_format($_POST['TransNo']);
} else {
	$FromTransNo = '';
}

if (isset($_GET['MpesaCode'])) {
$MpesaCode = $_GET['MpesaCode'];
$MpesaAmt = $_GET['MpesaAmt'];
}
include('includes/session.inc');
require __DIR__ . '/ReceiptPrinterLib/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage;

if(isset($_GET['Re-Print']) && $_GET['Re-Print']==1){
$connector = new WindowsPrintConnector("E-PoS");
}else{
$connector = new WindowsPrintConnector("E-PoS");
}

$printer = new Printer($connector);

$sql = "SELECT debtortrans.trandate,
							debtortrans.ovamount,
							debtortrans.ovdiscount,
							debtortrans.ovgst,
							debtortrans.rate,
							invtext
						FROM debtortrans WHERE debtortrans.type=10
						AND debtortrans.transno='" . $FromTransNo . "'";
	$result=DB_query($sql, '',  '',false, false);
	$myrow = DB_fetch_array($result);
$sql2 = "SELECT stockmoves.stockid,
								SUBSTRING(stockmaster.description,1,25) AS description,
								SUM(-stockmoves.qty) as quantity,
								stockmoves.discountpercent,
								((1 - stockmoves.discountpercent) * stockmoves.price * SUM(-stockmoves.qty)) AS fxnet,
								(stockmoves.price) AS fxprice,
								stockmaster.controlled,
								stockmaster.serialised,
								stockmoves.stkmoveno
							FROM stockmoves INNER JOIN stockmaster
							ON stockmoves.stockid = stockmaster.stockid
							WHERE stockmoves.type=10
							AND stockmoves.transno=" . $FromTransNo . "
							AND stockmoves.show_on_inv_crds=1 
							GROUP BY stockmoves.stockid
							ORDER BY stockmoves.stkmoveno ASC";
	$result2=DB_query($sql2);
	
	$sql3 = "SELECT taxcatname,
					SUM((1 - stockmoves.discountpercent) * stockmoves.price * (-stockmoves.qty)) AS fxnet, 
					(taxrate/(1+taxrate))*SUM((1 - stockmoves.discountpercent) * stockmoves.price * (-stockmoves.qty)) as tax,
					stockmaster.taxcatid
							FROM stockmoves INNER JOIN stockmaster
							ON stockmoves.stockid = stockmaster.stockid
							INNER JOIN taxcategories
							ON taxcategories.taxcatid = stockmaster.taxcatid
							INNER JOIN stockmovestaxes 
							ON stockmovestaxes.stkmoveno=stockmoves.stkmoveno
							WHERE stockmoves.type=10
							AND stockmoves.transno=" . $FromTransNo . "
							AND stockmoves.show_on_inv_crds=1 GROUP BY stockmaster.taxcatid
							ORDER BY stockmaster.taxcatid ASC";
	$result3=DB_query($sql3);
	
	$sql4 = "SELECT paymentname,amount, debtortrans.transno FROM banktrans
						INNER JOIN debtortrans ON debtortrans.transno=banktrans.transno
						INNER JOIN paymentmethods ON paymentmethods.paymentid=banktrans.banktranstype
						 WHERE debtortrans.type=12 AND debtortrans.reference='" . $FromTransNo . "'";
	$result4=DB_query($sql4);
	
	$sql5 = "SELECT debtortrans.transno, tendered, change_bal FROM debtortrans
						 WHERE debtortrans.type=12 AND debtortrans.reference='" . $FromTransNo . "'";
	$result5=DB_query($sql5);
	$myrow5 = DB_fetch_array($result5);
	$receiptNo = $myrow5['transno'];
	$Tendered = $myrow5['tendered'];
	$Change = $myrow5['change_bal'];
	
/* Initialize */
$printer -> initialize();

/* Text */
$printer -> setJustification(Printer::JUSTIFY_CENTER);
$printer -> setTextSize(1, 2);
$printer -> setEmphasis(true);
$printer -> text($_SESSION['CompanyRecord']['coyname']."\n");
$printer -> setEmphasis(false);
 $printer -> setTextSize(1,1);
$printer -> text($_SESSION['CompanyRecord']['regoffice1'].', '.$_SESSION['CompanyRecord']['regoffice2'].' '.$_SESSION['CompanyRecord']['regoffice6']."\n");
$printer -> text("Email:".$_SESSION['CompanyRecord']['email']."\n");
$printer -> text("TEL:".$_SESSION['CompanyRecord']['telephone']."  ".$_SESSION['TaxAuthorityReferenceName'] . ': ' . $_SESSION['CompanyRecord']['gstno']."\n");
/* Barcodes - see barcode.php for more detail */
$printer -> setBarcodeHeight(80);
$printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
$printer -> barcode($receiptNo);
//$printer -> feed();
 $printer -> setJustification(Printer::JUSTIFY_LEFT);
$printer -> text("Receipt No: ".$receiptNo."\n");
if($myrow['invtext'] !=""){
$printer -> text("Customer: ".$myrow['invtext']."\n");
}
$printer -> text("Date:".date('d-M-y'));
$printer -> setJustification(Printer::JUSTIFY_RIGHT);
$printer -> text("   Time:".date('h:i:s A')."\n");
$printer -> setJustification(Printer::JUSTIFY_LEFT);
$printer -> text("------------------------------------------------\n");
$printer -> setEmphasis(true);
$printer -> text("Description(s)            Qty Price      Total\n");
$printer -> setEmphasis(false); // Reset
$printer -> text("================================================\n");
$printer -> setJustification(Printer::JUSTIFY_LEFT);
while ($myrow2=DB_fetch_array($result2)) {
$DisplayPrice=locale_number_format($myrow2['fxprice'],2);
$DisplayNet=locale_number_format($myrow2['fxnet'],2);
if ($myrow2['controlled']==1){
$printer -> text($myrow2['description']."\n");
$GetControlMovts = DB_query("SELECT moveqty,
									serialno
							FROM stockserialmoves
							WHERE stockmoveno='" . $myrow2['stkmoveno'] . "'");
	//if ($myrow2['serialised']==1){
	while ($ControlledMovtRow = DB_fetch_array($GetControlMovts)){
$printer -> text(_strlen(' => '.$ControlledMovtRow['serialno']," ".(-$ControlledMovtRow['moveqty'])."x ".$DisplayPrice," ".locale_number_format(((-$ControlledMovtRow['moveqty'])*$myrow2['fxprice']),2))."\n");	
	//}
	}
}else{
$printer -> text(_strlen($myrow2['description']," ".$myrow2['quantity']." x ".$DisplayPrice," ".$DisplayNet)."\n");
//$line = sprintf('%-40.40s %5.0f %13.2f %13.2f', substr($myrow2['description'], 0, 25), $myrow2['quantity'], $DisplayPrice, $DisplayNet);
//$printer->text($line);
}

}

$printer -> text("------------------------------------------------\n");
$printer -> setEmphasis(true);
$printer -> setJustification(Printer::JUSTIFY_RIGHT);

$DisplaySubTot = locale_number_format($myrow['ovamount'],2);
$DisplayTax = locale_number_format($myrow['ovgst'],2);
$DisplayTotal = locale_number_format($myrow['ovgst']+$myrow['ovamount'],2);
$DisplayTotalCash = locale_number_format($Tendered,2);
$Change = locale_number_format($Change,2);

$printer -> text(_strlen2("TOTAL",$DisplayTotal)."\n");
$printer -> text(_strlen2("TENDERED",$DisplayTotalCash)."\n");
/*while($rowz=DB_fetch_array($result4)){
$printer -> text(_strlen2(strtoupper($rowz['paymentname']),locale_number_format($rowz['amount'],2))."\n");
}*/
$printer -> text(_strlen2("CHANGE",$Change)."\n");
$printer -> setEmphasis(false); // Reset
$printer -> text("------------------------------------------------\n");
$printer -> text(_strlen2("Total Sale",$DisplaySubTot)."\n");
$printer -> text(_strlen2("VAT",$DisplayTax)."\n");
$printer -> text(_strlen2("Amount Due",$DisplayTotal)."\n");
$printer -> text("------------------------------------------------\n");
$printer -> setJustification(Printer::JUSTIFY_CENTER);
$printer -> setUnderline(1);
$printer -> text(_strlen("TAX CATEGORY","AMOUNT"."              ","TAX")."\n");
$printer -> setUnderline(0);
$printer -> setJustification(Printer::JUSTIFY_LEFT);
while($row=DB_fetch_array($result3)){
$printer -> text(_strlen3($row['taxcatname'],locale_number_format($row['fxnet'],2),locale_number_format($row['tax'],2))."\n");
}

$printer -> text("------------------------------------------------\n");
$printer -> setJustification(Printer::JUSTIFY_LEFT);
$printer -> text("You were served by : ".$_SESSION['UserID']."\n");
$printer -> text("------------------------------------------------\n");
$printer -> setJustification(Printer::JUSTIFY_CENTER);
$printer -> text("Thank you for shopping with us.\n");
$printer -> feed();
$printer -> feed();
$printer -> feed();
$printer -> cut();

if(isset($MpesaCode) and $MpesaCode !=""){
$printer -> setJustification(Printer::JUSTIFY_CENTER);
$printer -> text("MPESA TRANSACTION\n");
$printer -> text($_SESSION['CompanyRecord']['coyname']."\n");
$printer -> text($_SESSION['CompanyRecord']['regoffice1'].', '.$_SESSION['CompanyRecord']['regoffice2'].' '.$_SESSION['CompanyRecord']['regoffice6']."\n");
$printer -> text("Email:".$_SESSION['CompanyRecord']['email']."\n");
$printer -> text("TEL:".$_SESSION['CompanyRecord']['telephone']."  ".$_SESSION['TaxAuthorityReferenceName'] . ': ' . $_SESSION['CompanyRecord']['gstno']."\n");
$printer -> setJustification(Printer::JUSTIFY_LEFT);
$printer -> text("Receipt No: ".$receiptNo."\n");
$printer -> text("Date:".date('d-M-y'));
$printer -> setJustification(Printer::JUSTIFY_RIGHT);
$printer -> text("   Time:".date('h:i:s A')."\n");
$printer -> setJustification(Printer::JUSTIFY_CENTER);
$printer -> text("------------------------------------------------\n");
$printer -> text(_strlen2($MpesaCode,$MpesaAmt)."\n");
$printer -> text("------------------------------------------------\n");
$printer -> text("Operator : ".$_SESSION['UserID']."\n");
$printer -> feed();
$printer -> cut();
}
/* Pulse */
$printer -> pulse();

/* Always close the printer! On some PrintConnectors, no actual
 * data is sent until the printer is closed. */
$printer -> close();
echo "<script>window.close();</script>";