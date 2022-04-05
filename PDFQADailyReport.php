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
		FROM qadailyreport
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

$PaperSize = 'A4';
include('includes/PDFStarter.php');
/*$PageNumber = 1;// RChacon: PDFStarter.php sets $PageNumber = 0.*/
$pdf->addInfo('Title', _('BLD. 54 QA DAILY REPORT') );
$pdf->addInfo('Subject', _('BLD. 54 QA DAILY REPORT') . ' ' . $_GET['id']);
$FontSize = 12;
$line_height = 12;// Recommended: $line_height = $x * $FontSize.

include('includes/PDFQADailyReportHeader.php');

if($myrow['calibre']=='7.62x51mm Ball'){
	$velocity = '833.5 &plusmn; 15 m/s';
	$pressure = '< 3600 Bars';
	$pressure1 = '< 2300 Bars';
	$accuracy = '< 150 mm';
	$force = '>= 27.21 Kgf';
	$rate = '650 to 750 Rds/Min';
	$at = 'At 3" 100% Misfire';
	$at1 = 'At 16" 100% Fire';
	$v = 'V25m';
	}elseif($myrow['calibre']=='5.56x45mm Ball'){
	$velocity = '915 &plusmn; 12 m/s';
	$pressure = '< 3800 Bars';
	$pressure1 = '< 550 Bars';
	$accuracy = '< 221 mm';
	$force = '>= 20.4 Kgf';
	$rate = '700 to 1000 Rds/Min';
	$at = 'At 3" 100% Misfire';
	$at1 = 'At 14" 100% Fire';
	$v = 'V25m';
	}elseif($myrow['calibre']=='9x19mm Para'){
	$velocity = '370 &plusmn; 10 m/s';
	$pressure = '< 2300 Bars';
	$pressure1 = '< 880 Bars';
	$accuracy = '< 200 mm';
	$force = '>= 20.4 Kgf';
	$rate = '550 to 650 Rds/Min';
	$at = 'At 3" 100% Misfire';
	$at1 = 'At 12" 100% Fire';
	$v = 'V16m';
	}elseif($myrow['calibre']=='7.62x51mm Tracer'){
	$velocity = '833 &plusmn; 15 m/s';
	$pressure = '< 3600 Bars';
	$pressure1 = '> 550 Bars';
	$accuracy = '< 300 mm';
	$force = '&ge; 27.21 Kgf';
	$rate = '650 to 750 Rds/Min';
	$at = 'At 3" 100% Misfire<br />At 16" 100% Fire';
	$v = 'V25m';
	}

		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-10,520,$FontSize, 'EPVAT TEST');
		$pdf->line($Page_Width-$Right_Margin-450, $YPos-13, $Left_Margin, $YPos-13);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos-27,520,$FontSize, 'Required Velocity');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+400,$YPos-27,520,$FontSize, 'Obtained Velocity');
		$pdf->SetFont('');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-40,520,$FontSize, 'Mean Velocity');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+160,$YPos-40,520,$FontSize, $v);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+260,$YPos-40,520,$FontSize, $velocity);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+410,$YPos-40,520,$FontSize, $myrow['velocity']);
		
		$YPos -=10;
		$pdf->line($Page_Width-$Right_Margin, $YPos-50, $Left_Margin, $YPos-50);
		
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos-60,520,$FontSize, 'Required Pressure');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+400,$YPos-60,520,$FontSize, 'Obtained Pressure');
		$pdf->SetFont('');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-73,520,$FontSize, 'Mean Mouth pressure');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+160,$YPos-73,520,$FontSize, '');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+260,$YPos-73,520,$FontSize, $pressure);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+410,$YPos-73,520,$FontSize, $myrow['mouthpressure']);
		
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-86,520,$FontSize, 'Mean Port pressure');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+160,$YPos-86,520,$FontSize, '');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+260,$YPos-86,520,$FontSize, $pressure1);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+410,$YPos-86,520,$FontSize, $myrow['portpressure']);
		
		$YPos -=10;
		$pdf->line($Page_Width-$Right_Margin, $YPos-95, $Left_Margin, $YPos-95);
		$pdf->line($Page_Width-$Right_Margin, $YPos-110, $Left_Margin, $YPos-110);
		
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-106,520,$FontSize, 'Mean Accuracy (H + L)');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos-118,520,$FontSize, 'Required Accuracy');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+400,$YPos-118,520,$FontSize, 'Obtained Accuracy');
		$pdf->SetFont('');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-131,520,$FontSize, 'Bullet Production');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+140,$YPos-131,520,$FontSize, 'Lot No : '.$myrow['bulletproductionlot']);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+260,$YPos-131,520,$FontSize, $accuracy);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+410,$YPos-131,520,$FontSize, $myrow['bulletproduction']);
		
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-144,520,$FontSize, 'Loading (PC 530)');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+140,$YPos-144,520,$FontSize, 'Lot No : '.$myrow['loadinglot']);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+260,$YPos-144,520,$FontSize, $accuracy);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+410,$YPos-144,520,$FontSize, $myrow['loading']);
		
		$YPos -=10;
		$pdf->line($Page_Width-$Right_Margin, $YPos-155, $Left_Margin, $YPos-155);
				
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos-164,520,$FontSize, 'Required Force');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+400,$YPos-164,520,$FontSize, 'Obtained Force');
		$pdf->SetFont('');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-177,520,$FontSize, 'Bullet Extraction Force');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+140,$YPos-177,520,$FontSize, 'Lot No : '.$myrow['bextractionlot']);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+260,$YPos-177,520,$FontSize, $force);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+410,$YPos-177,520,$FontSize, $myrow['bextraction']);
		
		$YPos -=10;
		$pdf->line($Page_Width-$Right_Margin, $YPos-185, $Left_Margin, $YPos-185);
		
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos-197,520,$FontSize, 'Required Standard');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+400,$YPos-197,520,$FontSize, 'Remarks');
		$pdf->SetFont('');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-210,520,$FontSize, 'Mercurous Nitrate Test');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+140,$YPos-210,520,$FontSize, '');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+260,$YPos-210,520,$FontSize, 'No Cracks(0)');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+410,$YPos-210,520,$FontSize, $myrow['mercurous']);
		
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-223,520,$FontSize, 'Water Tightness Test');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+140,$YPos-223,520,$FontSize, '');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+260,$YPos-223,520,$FontSize, '<= 3 Out of 20 Leaks');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+410,$YPos-223,520,$FontSize, $myrow['watertightness']);
		
		$YPos -=10;
		$pdf->line($Page_Width-$Right_Margin, $YPos-230, $Left_Margin, $YPos-230);
		
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos-243,520,$FontSize, 'Required Rate/Minute');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+400,$YPos-243,520,$FontSize, 'Remarks');
		$pdf->SetFont('');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-256,520,$FontSize, 'Rate of Fire');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+140,$YPos-256,520,$FontSize, 'Lot No : '. $myrow['ratefirelot']);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+260,$YPos-256,520,$FontSize, $rate);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+410,$YPos-256,520,$FontSize, $myrow['ratefire']);
		
		$YPos -=10;
		$pdf->line($Page_Width-$Right_Margin, $YPos-265, $Left_Margin, $YPos-265);
		
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos-276,520,$FontSize, 'Required Standard');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+400,$YPos-276,520,$FontSize, 'Remarks');
		$pdf->SetFont('');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-289,520,$FontSize, 'Rate of Fire');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+140,$YPos-289,520,$FontSize, 'Lot No : '. $myrow['sensitivitylot']);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+260,$YPos-289,520,$FontSize, $at);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+260,$YPos-302,520,$FontSize, $at1);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+410,$YPos-289,520,$FontSize, $myrow['sensitivity']);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+410,$YPos-302,520,$FontSize, $myrow['sensitivity1']);
		
		//$pdf->line($Page_Width-$Right_Margin, $YPos-315, $Left_Margin, $YPos-315);
		
		$sql = "SELECT *
		FROM qadailyreportremarks
		WHERE refid='" . $_GET['id'] . "' ORDER BY approver ASC";

$result=DB_query($sql, $ErrMsg);

$ListCount = 0;
$YPos -= 350;// Increment a line down for the next line item.

if (DB_num_rows($result)>0){
	while ($myrow2=DB_fetch_array($result)){

        $ListCount ++;
		$YPos -= $line_height;// Increment a line down for the next line item.

		$FontSize = 10;// Font size for the line item.
		$Space = 30;
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
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize-4,520,$FontSize, $myrow2['remarks']);
		if (mb_strlen($LeftOvers)>0) {
				$YPos -= $line_height;// Increment a line down for the next line item.
				$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $LeftOvers);
				$inline = $line_height;
			}
		$YPos -= $Space -$inline;

		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,8, ucwords(strtolower($myrow2['approvername'])));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,8, ConvertSQLDateTime($myrow2['remarkdate']),'right');
		$pdf->SetFont('','');

	}// Ends while there are line items to print out.
	}
	$LeftOvers = $pdf->addTextWrap($Left_Margin,$Bottom_Margin-14,150,$FontSize, 'KOFC 54060501');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+450,$Bottom_Margin-14,150,$FontSize, 'ISSUE 3 REV 0');
		
    $pdf->OutputD($_SESSION['DatabaseName'] . '_QADailyReport_' . $_GET['id'] . '_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
ob_end_flush();
?>
