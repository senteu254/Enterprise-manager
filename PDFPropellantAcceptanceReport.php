<?php
/*	$Id: PDFQuotationPortrait.php 4491 2011-02-15 06:31:08Z daintree $ */

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/
ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

//Get Out if we have no order number to work with
If (!isset($_GET['id']) || $_GET['id']==""){
        $Title = _('Select Report To Print');
        include('includes/header.inc');
        echo '<div class="centre">
				<br />
				<br />
				<br />';
        prnMsg( _('Select a Report to Print before calling this page') , 'error');
        echo '<br />
				<br />
				<br />
				<table class="table_index">
				<tr>
					<td class="menu_group_item">
						<ul><li><a href="index.php">' . _('Main Menu') . '</a></li>
						</ul>
					</td>
				</tr>
				</table>
				</div>
				<br />
				<br />
				<br />';
        include('includes/footer.inc');
        exit();
}

/*retrieve the order details from the database to print */
$ErrMsg = _('There was a problem retrieving the  header details for Request Number') . ' ' . $_GET['id'] . ' ' . _('from the database');

$sql = "SELECT *
		FROM qapropellantacceptance
		WHERE id='" . $_GET['id'] . "'";

$result=DB_query($sql, $ErrMsg);

//If there are no rows, there's a problem.
if (DB_num_rows($result)==0){
	$Title = _('Print Report Error');
	include('includes/header.inc');
	 echo '<div class="centre">
			<br />
			<br />
			<br />';
	prnMsg( _('Unable to Locate Report Number') . ' : ' . $_GET['id'] . ' ', 'error');
	echo '<br />
			<br />
			<br />
			<table class="table_index">
			<tr>
				<td class="menu_group_item">
					<ul><li><a href="index.php">' . _('Main Menu') . '</a></li></ul>
				</td>
			</tr>
			</table>
			</div>
			<br />
			<br />
			<br />';
	include('includes/footer.inc');
	exit;
} else{ /*There is only one order header returned - thats good! */
	$myrow = DB_fetch_array($result);
}
/*retrieve the order details from the database to print */

/* Then there's an order to print and its not been printed already (or its been flagged for reprinting/ge_Width=807;
)
LETS GO */
$PaperSize = 'A4';
include('includes/PDFStarter.php');
/*$PageNumber = 1;// RChacon: PDFStarter.php sets $PageNumber = 0.*/
$pdf->addInfo('Title', _('Propellant Acceptance Report') );
$pdf->addInfo('Subject', _('Propellant Acceptance Report') . ' ' . $_GET['id']);
$FontSize = 12;
$line_height = 12;// Recommended: $line_height = $x * $FontSize.

/* Now ... Has the order got any line items still outstanding to be invoiced */

$ErrMsg = _('There was a problem retrieving the Report line details for Report Number') . ' ' .
	$_GET['id'] . ' ' . _('from the database');

$sqlz = "SELECT *
		FROM qarawmatacceptanceremarks
		WHERE refid='" . $_GET['id'] . "' ORDER BY approver ASC";

$result=DB_query($sqlz, $ErrMsg);

$ListCount = 0;
$Title = "";


// Prints company logo:
$XPos = $Page_Width/2 - 140;
$pdf->addJpegFromFile($_SESSION['LogoFile'],$XPos+90,735,0,60);

// Draws a box with round corners around 'Delivery To' info:
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-$FontSize*1, 300, $FontSize, $_SESSION['CompanyRecord']['coyname'], 'left');
$FontSize=10;
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-$FontSize*2, 200, $FontSize, $_SESSION['CompanyRecord']['regoffice1'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-$FontSize*3, 200, $FontSize,  _('Ph') . ': ' . $_SESSION['CompanyRecord']['telephone'] .' ' . _('Fax'). ': ' . $_SESSION['CompanyRecord']['fax'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-$FontSize*4, 200, $FontSize, $_SESSION['CompanyRecord']['email'], 'left');

	
// Prints quotation info:
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-$FontSize*1, 200, $FontSize,  _('Printed On'). ': '.date("d, M Y"), 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-$FontSize*2, 200, $FontSize, _('Ref No.'). ': '.$_GET['id'], 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-$FontSize*3, 200, $FontSize, _('Page').': '.$PageNumber, 'right');
// Prints 'Quotation' title:
	$tt = _('PROPELLANT ACCEPTANCE FORM');

$pdf->addTextWrap(0, $Page_Height-$Top_Margin-95, $Page_Width, 14,$tt, 'center');

// Prints 'Delivery To' info:
$XPos = 46;
$YPos = 775;
$FontSize=12;
// Prints 'Quotation For' info:
$YPos -= 80;

//$pdf->addText($XPos-10, $YPos+10,$FontSize, _('Personal No :').''.$myrow['personal_no']);
//$pdf->addText($XPos-10, $YPos-10,$FontSize,  _('Date   :').''.date("Y/m/d"));
//$pdf->addText($XPos-10, $YPos-30,$FontSize, _('Full Name  :').''.$myrow['emp_fname'].''.''.$myrow['emp_lname']);
//$pdf->addText($XPos-10, $YPos-50,$FontSize, _('Total Number of property   :').''.$myrow['total']);

// Draws a box with round corners around around 'Quotation For' info:

$FontSize=10;

// Prints table header:
//$YPos -= 55;
$XPos = 40;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize, _('COMPANY'));
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(100,$YPos,200,$FontSize, $myrow['company']);
$pdf->SetFont('');
$LeftOvers = $pdf->addTextWrap(350,$YPos,200,$FontSize, _('CHARGE DETERMINATION'),'left');
//$pdf->SetFont('','B');
//$LeftOvers = $pdf->addTextWrap(410,$YPos+1,200,$FontSize, $myrow['country'],'left');
//$pdf->SetFont('');

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-13,300,$FontSize, _('COUNTRY'));
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(100,$YPos-13,200,$FontSize, $myrow['country']);
$pdf->SetFont('');
$LeftOvers = $pdf->addTextWrap(350,$YPos-13,300,$FontSize, _('DATE'),'left');
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(410,$YPos-13,415,$FontSize, ConvertSQLDate($myrow['date']),'left');
$pdf->SetFont('');
// Draws a box with round corners around line items:
$pdf->RoundRectangle(
	$Left_Margin,// RoundRectangle $XPos.
	$YPos+$FontSize+5,// RoundRectangle $YPos.
	$Page_Width-$Left_Margin-$Right_Margin,// RoundRectangle $Width.
	$YPos+$FontSize-$Bottom_Margin+5,// RoundRectangle $Height.
	10,// RoundRectangle $RadiusX.
	10);// RoundRectangle $RadiusY.

// Line under table headings:
$LineYPos = $YPos - $FontSize -8;
$pdf->line($Page_Width-$Right_Margin, $LineYPos, $Left_Margin, $LineYPos);

$YPos -= $FontSize+35;// This is to use addTextWrap's $YPos instead of normal $YPos.

$pdf->line($Left_Margin+20, $YPos+27, $Left_Margin+20, $YPos-82);
$pdf->line($Left_Margin+90, $YPos+27, $Left_Margin+90, $YPos-82);
$pdf->line($Left_Margin+140, $YPos+27, $Left_Margin+140, $YPos-82);
$pdf->line($Left_Margin+190, $YPos+27, $Left_Margin+190, $YPos-82);
$pdf->line($Left_Margin+242, $YPos+27, $Left_Margin+242, $YPos-82);
$pdf->line($Left_Margin+295, $YPos+27, $Left_Margin+295, $YPos-82);
$pdf->line($Left_Margin+340, $YPos+27, $Left_Margin+340, $YPos-82);
$pdf->line($Left_Margin+390, $YPos+10, $Left_Margin+390, $YPos-82);
$pdf->line($Left_Margin+440, $YPos+10, $Left_Margin+440, $YPos-82);
$pdf->line($Left_Margin+480, $YPos+10, $Left_Margin+480, $YPos-82);
//$pdf->line($Page_Width-$Right_Margin, $YPos+$FontSize+10, $Left_Margin, $YPos+$FontSize+10);
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,30,$FontSize, 'No', 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+20,$YPos,100,$FontSize, 'Calibre', 'left');
//$LeftOvers = $pdf->addTextWrap($Left_Margin+145,$YPos+13,100,$FontSize, 'Powder Lot', 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+90,$YPos,100,$FontSize, 'Powder Lot', 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+140,$YPos,100,$FontSize, 'Primer Lot', 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+190,$YPos+13,100,$FontSize, 'Pre', 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+190,$YPos,100,$FontSize, 'Shipment', 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+243,$YPos+13,100,$FontSize, 'KOFC ', 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+243,$YPos,100,$FontSize, 'Determined', 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+295,$YPos+13,100,$FontSize, 'NATO', 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+295,$YPos,100,$FontSize, 'STD', 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+350,$YPos+13,200,$FontSize, 'EPVAT PARAMETERS (MEAN)', 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+340,$YPos,100,$FontSize, 'Chamber', 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+390,$YPos,100,$FontSize, 'Port', 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+440,$YPos,130,$FontSize, 'Time', 'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+480,$YPos,100,$FontSize, 'Velocity', 'left');
$pdf->line($Page_Width-$Right_Margin, $YPos-3, $Left_Margin, $YPos-3);

$YPos -= 13;// This is to use addTextWrap's $YPos instead of normal $YPos.
$Lines =13;
$i=1;
$sqlsx = "SELECT *
		FROM qapropellantacceptancedata
		WHERE refno='" . $_GET['id']. "' ORDER BY num ASC";
	$resx = DB_query($sqlsx);
while ($myx=DB_fetch_array($resx)){
$pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize, $i, 'left');
$pdf->addTextWrap($Left_Margin+20,$YPos,100,$FontSize, $myx['calibre'], 'left');
$pdf->addTextWrap($Left_Margin+90,$YPos,100,$FontSize, $myx['plot'], 'left');
$pdf->addTextWrap($Left_Margin+140,$YPos,100,$FontSize, $myx['prlot'], 'left');
$pdf->addTextWrap($Left_Margin+190,$YPos,100,$FontSize, $myx['pre_shipment'], 'left');
$pdf->addTextWrap($Left_Margin+243,$YPos,100,$FontSize, $myx['kofcdosage'], 'left');
$pdf->addTextWrap($Left_Margin+295,$YPos,100,$FontSize, $myx['natostd'], 'left');
$pdf->addTextWrap($Left_Margin+340,$YPos,100,$FontSize, $myx['chamber'], 'left');
$pdf->addTextWrap($Left_Margin+390,$YPos,100,$FontSize, $myx['port'], 'left');
$pdf->addTextWrap($Left_Margin+440,$YPos,100,$FontSize, $myx['time'], 'left');
$pdf->addTextWrap($Left_Margin+480,$YPos,100,$FontSize, $myx['velocity'], 'left');
$YPos -= 13;
$Lines += 13;
$i++;
}	

$YPos -= $FontSize+72-$Lines;// This is to use addTextWrap's $YPos instead of normal $YPos.

$pdf->line($Page_Width-$Right_Margin, $YPos, $Left_Margin, $YPos);
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-$FontSize-2,520,$FontSize, 'INSPECTORS', 'left');

$YPos -=25;
if($myrow['inspectors'] !=""){
$arr = explode(',',$myrow['inspectors']);
for($i = 0; $i<count($arr)-1; $i++){
$usern = DB_query("SELECT userid,realname FROM www_users WHERE userid='".$arr[$i]."'");
$myro = DB_fetch_array($usern);
$pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize, $myro['userid'], 'left');
$pdf->addTextWrap($Left_Margin+40,$YPos,300,$FontSize, $myro['realname'], 'left');
$YPos -= 13;
}
}


$YPos -= $FontSize+20;// This is to use addTextWrap's $YPos instead of normal $YPos.
	while ($myrow2=DB_fetch_array($result)){

        $ListCount ++;
		$YPos -= $line_height;// Increment a line down for the next line item.

		$FontSize = 10;// Font size for the line item.
		if($Title !=$myrow2['approvertitle']){
		//$LeftOvers = $pdf->addText($Left_Margin, $YPos+$FontSize, $FontSize, $myrow2['approvertitle'],'','', 'center');
		$pdf->SetFont('','B');
		$pdf->line($Page_Width-$Right_Margin, $YPos+$FontSize+10, $Left_Margin, $YPos+$FontSize+10);
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $myrow2['approvertitle'], 'left');
		//$Title =$myrow2['approvertitle'];
		$pdf->line($Page_Width-$Right_Margin, $YPos+$FontSize-3, $Left_Margin, $YPos+$FontSize-3);
		$YPos -= $line_height;// Increment a line down for the next line item.
		$pdf->SetFont('');
		//$Space = 30;
		}
		$inline =0;
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $myrow2['remarks']);
		while (mb_strlen($LeftOvers)>0) {
				$YPos -= $line_height;// Increment a line down for the next line item.
				$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $LeftOvers);
				$inline += $line_height;
			}
		$YPos -= 20;
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,8, ucwords(strtolower($myrow2['approvername'])));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,8, ConvertSQLDateTime($myrow2['remarkdate']),'right');
		$pdf->SetFont('','');
		$YPos -= 3;
		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('includes/PDFConformanceReportHeader.php');
		} //end if need a new page headed up

	}// Ends while there are line items to print out.

	if ($YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('includes/PDFConformanceReportHeader.php');
	} //end if need a new page headed up


    $pdf->OutputI($_SESSION['DatabaseName'] . '_NonConformanceReport_' . $_GET['id'] . '_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();

ob_end_flush();
?>
