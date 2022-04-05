<?php
/* $Id: PDFReceipt.php 6941 2014-10-26 23:18:08Z daintree $*/

include('includes/session.inc');

include('includes/PDFStarter.php');

$FontSize=10;
$pdf->addInfo('Title', _('Sales Receipt') );

$PageNumber=1;
$line_height=12;
if ($PageNumber>1){
	$pdf->newPage();
}

function number_to_words($Number) {

    if (($Number < 0) OR ($Number > 999999999)) {
		prnMsg(_('Number is out of the range of numbers that can be expressed in words'),'error');
		return _('error');
    }

	$Millions = floor($Number / 1000000);
	$Number -= $Millions * 1000000;
	$Thousands = floor($Number / 1000);
	$Number -= $Thousands * 1000;
	$Hundreds = floor($Number / 100);
	$Number -= $Hundreds * 100;
	$NoOfTens = floor($Number / 10);
	$NoOfOnes = $Number % 10;

	$NumberInWords = '';

	if ($Millions) {
		$NumberInWords .= number_to_words($Millions) . ' ' . _('million');
	}

    if ($Thousands) {
		$NumberInWords .= (empty($NumberInWords) ? '' : ' ') . number_to_words($Thousands) . ' ' . _('thousand');
	}

    if ($Hundreds) {
		$NumberInWords .= (empty($NumberInWords) ? '' : ' ') . number_to_words($Hundreds) . ' ' . _('hundred');
	}

	$Ones = array(	0 => '',
					1 => _('One'),
					2 => _('Two'),
					3 => _('Three'),
					4 => _('Four'),
					5 => _('Five'),
					6 => _('Six'),
					7 => _('Seven'),
					8 => _('Eight'),
					9 => _('Nine'),
					10 => _('Ten'),
					11 => _('Eleven'),
					12 => _('Twelve'),
					13 => _('Thirteen'),
					14 => _('Fourteen'),
					15 => _('Fifteen'),
					16 => _('Sixteen'),
					17 => _('Seventeen'),
					18 => _('Eighteen'),
					19 => _('Nineteen')	);

	$Tens = array(	0 => '',
					1 => '',
					2 => _('Twenty'),
					3 => _('Thirty'),
					4 => _('Forty'),
					5 => _('Fifty'),
					6 => _('Sixty'),
					7 => _('Seventy'),
					8 => _('Eighty'),
					9 => _('Ninety') );


    if ($NoOfTens OR $NoOfOnes) {
		if (!empty($NumberInWords)) {
			$NumberInWords .= ' ' . _('and') . ' ';
		}

		if ($NoOfTens < 2){
			$NumberInWords .= $Ones[$NoOfTens * 10 + $NoOfOnes];
		}
		else {
			$NumberInWords .= $Tens[$NoOfTens];
			if ($NoOfOnes) {
				$NumberInWords .= '-' . $Ones[$NoOfOnes];
			}
		}
	}

	if (empty($NumberInWords)){
		$NumberInWords = _('zero');
	}

	return $NumberInWords;
		}

$sql="SELECT MIN(id) as start FROM debtortrans WHERE type=12 AND transno='". $_GET['BatchNumber']. "'";
$result=DB_query($sql);
$myrow=DB_fetch_array($result);
$StartReceiptNumber=$myrow['start'];

$sql="SELECT debtorno,
			ovamount,
			invtext,
			banktranstype,
			debtortrans.trandate,
			salesperson
		FROM debtortrans,banktrans
		WHERE debtortrans.type=12
		and debtortrans.transno=banktrans.transno
		AND debtortrans.transno='" . $_GET['BatchNumber'] . "'
		AND debtortrans.id='". ($StartReceiptNumber-1+$_GET['ReceiptNumber']) ."'";
$result = DB_query($sql);
$myrow = DB_fetch_array($result);
$DebtorNo = $myrow['debtorno'];
$Amount = $myrow['ovamount'];
$Narrative = $myrow['invtext'];
$PaymentType = $myrow['banktranstype'];
$Date = date('d/m/Y H:i:s',strtotime($myrow['trandate']));
$Person = $myrow['salesperson'];

$FontSize=10;
$YPos= $Page_Height-$Top_Margin;
$XPos=0;

/* Prints company logo */
$pdf->addJpegFromFile($_SESSION['LogoFile'], $XPos+20, $YPos-50, 0, 60);

/* Prints company info */
$LeftOvers = $pdf->addTextWrap(100,$YPos,300,$FontSize,$_SESSION['CompanyRecord']['coyname']);
$pdf->addText(100, $YPos,$FontSize, $_SESSION['CompanyRecord']['regoffice1']);
$pdf->addText(100, $YPos-($line_height),$FontSize, $_SESSION['CompanyRecord']['regoffice2'] . ' '. $_SESSION['CompanyRecord']['regoffice3'] . ' ' . $_SESSION['CompanyRecord']['regoffice4'] . ' ' . $_SESSION['CompanyRecord']['regoffice5']);
    $pdf->addText(100, $YPos-($line_height*2),$FontSize, _('Tel No') . ': ' . $_SESSION['CompanyRecord']['telephone'] . ' ' . _('Fax'). ': ' . $_SESSION['CompanyRecord']['fax']);
$pdf->addText(100, $YPos-($line_height*3),$FontSize, $_SESSION['CompanyRecord']['email']);

$pdf->addText(230, $YPos-($line_height*3),14, 'OFFICIAL RECEIPT');

$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-180,$YPos-($line_height),550,$FontSize, _('Receipt Number ').'  : ' . $_GET['BatchNumber'] );
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-180,$YPos-($line_height*2.1),140,$FontSize, _('Date').': ' . $Date);
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-180,$YPos-($line_height*3.5),140,$FontSize, _('Printed').': ' . Date($_SESSION['DefaultDateFormat']));

$YPos -= 30;

$YPos -=$line_height;
//Note, this is ok for multilang as this is the value of a Select, text in option is different

$YPos -=(2*$line_height);

/*Draw a rectangle to put the headings in     */

$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height);

$FontSize=10;
//$YPos -= ($line_height);

$PageNumber++;


$sql = "SELECT 	currabrev,
				decimalplaces
			FROM currencies
			WHERE currabrev=(SELECT currcode
				FROM banktrans
				WHERE type=12
				AND transno='" . $_GET['BatchNumber']."')";
$result=DB_query($sql);
$myrow=DB_fetch_array($result);
$CurrencyCode=$myrow['currabrev'];
$DecimalPlaces=$myrow['decimalplaces'];

$sql="SELECT name,
             address1,
			 address2,
			 address3,
			 address4,
			 address5,
			 address6
		FROM debtorsmaster
		WHERE debtorno='".$DebtorNo."'";

$result=DB_query($sql);
$myrow=DB_fetch_array($result);

/* Prints customer info */
$LeftOvers = $pdf->addTextWrap(50,$YPos,300,$FontSize,_('Received From').' :');
$LeftOvers = $pdf->addTextWrap(150,$YPos,300,$FontSize, htmlspecialchars_decode($myrow['name']).', '.htmlspecialchars_decode($myrow['address2']));
$LeftOvers = $pdf->addTextWrap(150,$YPos-($line_height*1),300,$FontSize, htmlspecialchars_decode($myrow['address1']));
//$LeftOvers = $pdf->addTextWrap(150,$YPos-($line_height*2),300,$FontSize, htmlspecialchars_decode($myrow['address2']));
$LeftOvers = $pdf->addTextWrap(150,$YPos-($line_height*2),300,$FontSize, htmlspecialchars_decode($myrow['address3']).' '.htmlspecialchars_decode($myrow['address6']));
//$LeftOvers = $pdf->addTextWrap(150,$YPos-($line_height*4),300,$FontSize, htmlspecialchars_decode($myrow['address4']));
//$LeftOvers = $pdf->addTextWrap(150,$YPos-($line_height*5),300,$FontSize, htmlspecialchars_decode($myrow['address5']));
//$LeftOvers = $pdf->addTextWrap(150,$YPos-($line_height*6),300,$FontSize, htmlspecialchars_decode($myrow['address6']));

$YPos=$YPos-($line_height*4);

$LeftOvers = $pdf->addTextWrap(50,$YPos,300,$FontSize, _('The Sum Of').' :');
include('includes/CurrenciesArray.php'); // To get the currency name from the currency code.
$LeftOvers = $pdf->addTextWrap(150,$YPos,300,$FontSize, number_to_words(-$Amount).' ' . $CurrencyName[$CurrencyCode].' ('.locale_number_format(-$Amount,$DecimalPlaces).')');

$YPos=$YPos-($line_height);

$LeftOvers = $pdf->addTextWrap(50,$YPos,500,$FontSize, _('Details').' :');
$LeftOvers = $pdf->addTextWrap(150,$YPos,500,$FontSize, $Narrative);
$YPos=$YPos-($line_height);
$LeftOvers = $pdf->addTextWrap(50,$YPos,500,$FontSize, _('Payment Mode').' :');
$LeftOvers = $pdf->addTextWrap(150,$YPos,500,$FontSize, $PaymentType);

$YPos=$YPos-($line_height*2);

$LeftOvers = $pdf->addTextWrap(50,$YPos,500,$FontSize,_('Signed On Behalf Of').' :     '.$_SESSION['CompanyRecord']['coyname']);

$YPos=$YPos-($line_height*2);

$LeftOvers = $pdf->addTextWrap(150,$YPos,300,$FontSize,'______________________________________________________________________________');
$YPos=$YPos-($line_height);
$LeftOvers = $pdf->addTextWrap(150,$YPos,400,$FontSize,'You were Served By: '.$Person,'right');
$pdf->line($Left_Margin, $YPos-($line_height),$Page_Width-$Right_Margin, $YPos-($line_height));

$pdf->Output('Receipt-'.$_GET['ReceiptNumber'], 'I');
?>