<?php
/*	$Id: PDFQuotationPortrait.php 4491 2011-02-15 06:31:08Z daintree $ */

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/
ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
if (isset($_GET['SelectedUser'])){
	$SelectedUser = $_GET['SelectedUser'];
} elseif (isset($_POST['SelectedUser'])){
	$SelectedUser = $_POST['SelectedUser'];
}
if (isset($_GET['SelectedUser'])){
	$SelectedUser = $_GET['SelectedUser'];
} elseif (isset($_POST['SelectedUser'])){
	$SelectedUser = $_POST['SelectedUser'];
}

if (isset($_POST['Comment'])) {
$InputError = 0;

if (isset($SelectedUser)) {
$sql = "UPDATE qaprimersensitivity SET remarks='" . $_POST['remarks'] . "',
						remarker='" . $_SESSION['UsersRealName'] ."',
						remarkstime='" . date('Y-m-d H:m:s') ."'
					WHERE testno = '". $SelectedUser . "'";
					
	//prnMsg( _('Remarks has been updated successfully'), 'success' );
		
	}

	if ($InputError!=1){
		//run the SQL from either of the above possibilites
		$ErrMsg = _('The user alterations could not be processed because');
		$DbgMsg = _('The SQL that was used to update the user and failed was');
		$result = DB_query($sql,$ErrMsg,$DbgMsg);
		unset($_POST['remarks']);
		//unset($SelectedUser);
	}
	
}
	$sql = "SELECT *
		FROM qaprimersensitivity
		WHERE testno='" . $SelectedUser . "'";

	$result = DB_query($sql);
	if (DB_num_rows($result)==0){
	$Title = _('Print Report Error');
	include('includes/header.inc');
	 echo '<div class="centre">
			<br />
			<br />
			<br />';
	prnMsg( _('Unable to Locate Report Number') . ' : ' . $SelectedUser . ' ', 'error');
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

switch ($myrow['calibre']) {
    case "5.56x45mm Ball":
        $req1 = "MEAN H.+5s <14Inches";
		$req2 = "MEAN H.-2s >3Inches";
        break;
    case "7.62x51mm Ball":
        $req1 = "MEAN H.+5s <16Inches";
		$req2 = "MEAN H.-2s >3Inches";
        break;
    case "9x19mm Para":
        $req1 = "MEAN H.+5s <12Inches";
		$req2 = "MEAN H.-2s >3Inches";
        break;
    default:
        $req1 = "";
		$req2 = "";
}
	
if($myrow['remarks'] ==""){
######################################################################################
$Title = _('Primer Sensitivity Curve Data Sheet');

include('includes/header.inc');
echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/maintenance.png" title="' .
		_('Print') . '" alt="" />' . ' ' . $Title . '</p>';

	$_POST['id'] = $myrow['testno'];
	$_POST['test'] = $myrow['test'];
	$_POST['machine'] = $myrow['depthrange'];
	$_POST['date'] = ConvertSQLDate($myrow['date']);
	$_POST['lot'] = $myrow['primerlot'];
	$_POST['calibre']	= $myrow['calibre'];
	
echo '<a href="' . $RootPath . '/QAPrimerDataSheet.php">' . _('Back to Main Menu') . '</a>';

echo '<table class="selection">
      <tr><td>';

	echo '<table class="selection">
			<tr height="30px">
				<td width="150px">' . _('Test No') . ':</td>
				<th width="200px">' . $_POST['test'] . '</th>
			</tr>';

	echo '<tr height="30px"><td>' . _('Date') .'</td>
			<td><b>'.$_POST['date'].'</b></td>
			<td>' . _('Calibre') . '</td>
			<td><strong>'.$_POST['calibre'].'</strong></td>
		</tr><tr height="30px">
		<td>' . _('Primer Depth Range.') . '</td>
			<td><b>'.$_POST['machine'].'</b></td>
			<td><strong>Requirement:</strong></td><td><b> '.$req1.'</br>'.$req2.'</b></td>
		</tr>
		<tr height="30px">
		<td>' .  _('Primer Lot No') . '</td>
		<td><b>'.$_POST['lot'] .'</b></td>
		<td>';

echo '</td>
	</tr>
	</table>';
	echo '</td>	<td>';
	
	echo '</td>
	</tr>
	<tr><td colspan="4">';
		
$sql = "SELECT *
		FROM qaprimersensitivitydata
		WHERE testno='" . $SelectedUser . "' ORDER BY height ASC";
	$results = DB_query($sql);
echo '<table class="selection">';
echo '<tr>
		<th width="100">H(d)</td>
		<th width="100">Height</td>
		<th width="100">No Fire</td>
		<th width="100">% (PI)</td>
		<th width="100">(KI)</td>
		<th width="100">KI &times; PI</td>
	</tr>';
$k1 = array(0,1,3,5,7,9,11,13,15,17,19);
$Sample = 25;
$i = 0;
$sumPI = 0;
$sumKP = 0;
while($myro = DB_fetch_array($results)){
$PI = ($myro['misfired']/$Sample);
$KP = ($PI*$k1[$i]);

if ($k==1){
			echo '<tr height="25" class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr height="25" class="OddTableRows">';
			$k=1;
		}
echo '
		<td ><center>'. ($myro['misfired']== $Sample ? $myro['height'] : '') .'</center></td>
		<td ><center>'.$myro['height'].'</center></td>
		<td ><center>'.$myro['misfired'].'</center></td>
		<td ><center>'. ($PI== 1 ? '..........' : round($PI,2)) .'</center></td>
		<td ><center>'. ($k1[$i]== 0 ? '...........' : $k1[$i]) .'</center></td>
		<td ><center>'. ($KP== 0 ? '...........' : $KP) .'</center></td>
	</tr>';
$i++;

$sumPI +=($PI== 1 ? 0 : $PI);
$sumKP +=$KP;
if($myro['misfired'] == $Sample){
$Hd = $myro['height'];
}

	}
echo '<tr class="OddTableRows" height="25"><td colspan="3" style="text-align:right"><strong>Total :</strong></td><th>'. $sumPI .'</th><td></td><th>'. $sumKP .'</th></tr>';
$meanH = ($sumPI+$Hd+0.5);
$sqPI = ($sumPI*$sumPI);
$SD = sqrt($sumKP-$sqPI);	
$meanH1 = $meanH + (5*$SD);
$meanH2 = $meanH - (2*$SD);
echo '<tr height="30"><td colspan="4"></td><td></td></tr>';
echo '<tr><td colspan="4">H(d) = Max Height Giving 100% Misfire</td><th>'.$Hd.'</th></tr>';
echo '<tr><td colspan="4">Y = Height Between Two Heights (linch)</td><th>1</th></tr>';
echo '<tr><td colspan="4">MEAN H. = Sum of PI + H(d) + &frac12;Y</td><th>'. round($meanH,2) .'</th></tr>';
echo '<tr><td colspan="4">(Sum of PI)&sup2;</td><th>'. round($sqPI,2) .'</th></tr>';
echo '<tr><td colspan="4">SD = &radic; { Sum of KI &times; PI - (Sum of PI)&sup2; }</td><th>'. round($SD,2) .'</th></tr>';
echo '<tr><td colspan="4">MEAN H. + 5s</td><th>'. round($meanH1,2) .'</th></tr>';
echo '<tr><td colspan="4">MEAN H. - 2s</td><th>'. round($meanH2,2) .'</th></tr>';
echo '</table>';
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') .  '">
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
		<input type="hidden" name="SelectedUser" value="' . $SelectedUser . '" />
		';
echo '<tr><td>Remarks :</td></tr>';	
echo '<tr><td><textarea name="remarks" cols="75" rows="3"></textarea></td></tr>';
echo '<tr><td><center><input name="Comment" type="submit" value="Submit" /></center></td></tr>';
echo '</td></tr>
	</table>';
echo '</form>';
	exit;
}

/*retrieve the order details from the database to print */
$ErrMsg = _('There was a problem retrieving the  header details for Request Number') . ' ' . $SelectedUser . ' ' . _('from the database');
/*
$sql = "SELECT *
		FROM qaprimersensitivity
		WHERE testno='" . $SelectedUser . "'";

$result=DB_query($sql, $ErrMsg);
*/

/* Then there's an order to print and its not been printed already (or its been flagged for reprinting/ge_Width=807;
)
LETS GO */
$PaperSize = 'A4';
include('includes/PDFStarter.php');
/*$PageNumber = 1;// RChacon: PDFStarter.php sets $PageNumber = 0.*/
$pdf->addInfo('Title', _('Primer Sensitivity Curve') );
$pdf->addInfo('Subject', _('Primer Sensitivity Curve') . ' ' . $SelectedUser);
$FontSize = 14;
$line_height = 18;// Recommended: $line_height = $x * $FontSize.

/* Now ... Has the order got any line items still outstanding to be invoiced */

$ErrMsg = _('There was a problem retrieving the Report line details for Report Number') . ' ' .
	$SelectedUser . ' ' . _('from the database');

$sql = "SELECT *
		FROM qaprimersensitivitydata
		WHERE testno='" . $SelectedUser . "' ORDER BY height ASC";

$result=DB_query($sql, $ErrMsg);

$ListCount = 0;
$Title = "";

if (DB_num_rows($result)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('includes/PDFPrimerCurveReportHeader.php');
$k1 = array(0,1,3,5,7,9,11,13,15,17,19);
$Sample = 25;
$i = 0;
$sumPI = 0;
$sumKP = 0;
	while ($myrow2=DB_fetch_array($result)){
$PI = ($myrow2['misfired']/$Sample);
$KP = ($PI*$k1[$i]);

        $ListCount ++;
		$YPos -= $line_height;// Increment a line down for the next line item.
		$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos+$FontSize,520,$FontSize, ($myrow2['misfired']== $Sample ? $myrow2['height'] : ''));
		$LeftOvers = $pdf->addTextWrap($Left_Margin+100,$YPos+$FontSize,520,$FontSize, $myrow2['height']);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+200,$YPos+$FontSize,520,$FontSize, $myrow2['misfired']);
		$LeftOvers = $pdf->addTextWrap($Left_Margin+300,$YPos+$FontSize,520,$FontSize, ($PI== 1 ? '..........' : round($PI,2)));
		$LeftOvers = $pdf->addTextWrap($Left_Margin+380,$YPos+$FontSize,520,$FontSize, ($k1[$i]== 0 ? '..........' : $k1[$i]));
		$LeftOvers = $pdf->addTextWrap($Left_Margin+450,$YPos+$FontSize,520,$FontSize, ($KP== 0 ? '...........' : $KP));
		
$i++;
$sumPI +=($PI== 1 ? 0 : $PI);
$sumKP +=$KP;
if($myrow2['misfired'] == $Sample){
$Hd = $myrow2['height'];
}

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('includes/PDFPrimerCurveReportHeader.php');
		} //end if need a new page headed up

	}// Ends while there are line items to print out.
$pdf->line($Page_Width-$Right_Margin, $YPos, $Left_Margin, $YPos);
$pdf->SetFont('','B');
$FontSize = 13;
$LeftOvers = $pdf->addTextWrap($Left_Margin+200,$YPos-$FontSize,520,$FontSize, 'Total :');
$LeftOvers = $pdf->addTextWrap($Left_Margin+300,$YPos-$FontSize,520,$FontSize, round($sumPI,2));
$LeftOvers = $pdf->addTextWrap($Left_Margin+450,$YPos-$FontSize,520,$FontSize, round($sumKP,2));
$pdf->SetFont('');
$meanH = ($sumPI+$Hd+0.5);
$sqPI = ($sumPI*$sumPI);
$SD = sqrt($sumKP-$sqPI);	
$meanH1 = $meanH + (5*$SD);
$meanH2 = $meanH - (2*$SD);

$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-$FontSize*4,520,$FontSize, 'H(d) = Max Height Giving 100% Misfire ');
$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-$FontSize*6,520,$FontSize, 'Y = Height Between Two Heights (Linch) ');
$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-$FontSize*8,520,$FontSize, 'MEAN H. = Sum of PI + H(d) + &frac12;Y ');
$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-$FontSize*10,520,$FontSize, '(Sum of PI)&sup2;');
$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-$FontSize*12,520,$FontSize, 'SD = Sqrt { (Sum of KI &times; PI) - (Sum of PI)&sup2;}');
$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-$FontSize*14,520,$FontSize, 'MEAN H. + 5s');
$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-$FontSize*16,520,$FontSize, 'MEAN H. - 2s');

$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap($Left_Margin+300,$YPos-$FontSize*4,520,$FontSize, $Hd);
$LeftOvers = $pdf->addTextWrap($Left_Margin+300,$YPos-$FontSize*6,520,$FontSize, '1');
$LeftOvers = $pdf->addTextWrap($Left_Margin+300,$YPos-$FontSize*8,520,$FontSize, round($meanH,2));
$LeftOvers = $pdf->addTextWrap($Left_Margin+300,$YPos-$FontSize*10,520,$FontSize, round($sqPI,2));
$LeftOvers = $pdf->addTextWrap($Left_Margin+300,$YPos-$FontSize*12,520,$FontSize, round($SD,2));
$LeftOvers = $pdf->addTextWrap($Left_Margin+300,$YPos-$FontSize*14,520,$FontSize, round($meanH1,2));
$LeftOvers = $pdf->addTextWrap($Left_Margin+300,$YPos-$FontSize*16,520,$FontSize, round($meanH2,2));
$pdf->SetFont('');

$YPos -= $FontSize*20;
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap($Left_Margin+4,$YPos-$FontSize+5,520,$FontSize, 'Remarks:');
$pdf->SetFont('');
$FontSize = 12;
$LeftOvers = $pdf->addTextWrap($Left_Margin+4,$YPos-$FontSize*2,520,$FontSize, $myrow['remarks']);
while(mb_strlen($LeftOvers)>0){
$YPos -=12;
$LeftOvers = $pdf->addTextWrap($Left_Margin+4,$YPos-$FontSize*2,520,$FontSize, $LeftOvers);
}
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap($Left_Margin+4,$YPos-$FontSize*3,520,8, ucwords(strtolower($myrow['remarker'])));
$LeftOvers = $pdf->addTextWrap($Left_Margin+440,$YPos-$FontSize*3,520,8, ConvertSQLDateTime($myrow['remarkstime']));
$pdf->SetFont('');
	if ($YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('includes/PDFPrimerCurveReportHeader.php');
	} //end if need a new page headed up

} /*end if there are line details to show on the quotation*/


if ($ListCount == 0){
        $Title = _('Print Report Error');
        include('includes/header.inc');
        echo '<p>' .  _('There were no Reports') . '. ' . _('The Report cannot be printed').
                '<br /><a href="index.php">' .  _('Print Another Report');
        include('includes/footer.inc');
	exit;
} else {
    $pdf->OutputD($_SESSION['DatabaseName'] . '_PrimerCurve_' . $SelectedUser . '_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
}
ob_end_flush();
?>
