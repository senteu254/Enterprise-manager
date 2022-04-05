<?php
/* $Id: PDFPeriodStockTransListing.php 4307 2010-12-22 16:06:03Z tim_schofield $*/

include('includes/SQL_CommonFunctions.inc');
include ('includes/session.inc');

$InputError=0;
if (isset($_POST['FromDate']) AND !Is_Date($_POST['FromDate'])){
	$msg = _('The date must be specified in the format') . ' ' . $_SESSION['DefaultDateFormat'];
	$InputError=1;
	unset($_POST['FromDate']);
}

if (!isset($_POST['FromDate'])){

	 $Title = _('Stock Transaction Listing');
	 include ('includes/header.inc');

	echo '<div class="centre">
			<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/transactions.png" title="' . $Title . '" alt="" />' . ' '. _('Stock Transaction Listing') . '</p>
		</div>';

	if ($InputError==1){
		prnMsg($msg,'error');
	}

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">
		<div>
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
		<table class="selection">
		<tr>
			<td>' . _('Enter the date from which the transactions are to be listed') . ':</td>
			<td><input type="text" required="required" autofocus="autofocus" name="FromDate" maxlength="10" size="10" class="date" alt="' . $_SESSION['DefaultDateFormat'] . '" value="' . Date($_SESSION['DefaultDateFormat']) . '" /></td>
		</tr>
		<tr>
			<td>' . _('Enter the date to which the transactions are to be listed') . ':</td>
			<td><input type="text" required="required" name="ToDate" maxlength="10" size="10" class="date" alt="' . $_SESSION['DefaultDateFormat'] . '" value="' . Date($_SESSION['DefaultDateFormat']) . '" /></td>
		</tr>
		<tr>
			<td>' . _('Transaction type') . '</td>
			<td><select name="TransType">
				<option value="26">' . _('Work Order Receipt') . '</option>
				<option value="28">' . _('Work Order Issue') . '</option>
				</select></td>
		</tr>';

	$sql = "SELECT locations.loccode, locationname FROM locations INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1";
	$resultStkLocs = DB_query($sql);

	echo '<tr>
			<td>' . _('For Stock Location') . ':</td>
			<td><select required="required" name="StockLocation">
				<option value="All">' . _('All') . '</option>';

	while ($myrow=DB_fetch_array($resultStkLocs)){
		if (isset($_POST['StockLocation']) AND $_POST['StockLocation']!='All'){
			if ($myrow['loccode'] == $_POST['StockLocation']){
				echo '<option selected="selected" value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			} else {
				echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			}
		} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
			echo '<option selected="selected" value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			$_POST['StockLocation']=$myrow['loccode'];
		} else {
			echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
		}
	}
	echo '</select></td></tr>';
	
	$SQL = "SELECT workorders.wo,
								woitems.stockid,
								stockmaster.description
						FROM workorders
						INNER JOIN woitems ON workorders.wo=woitems.wo
						INNER JOIN stockmaster ON woitems.stockid=stockmaster.stockid
						INNER JOIN locationusers ON locationusers.loccode=workorders.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
						WHERE discontinued=0
						GROUP BY stockmaster.stockid
						ORDER BY workorders.wo,
								woitems.stockid";
	$resultLocs = DB_query($SQL);
	
	echo '<tr>
			<td>' . _('Item') . ':</td>
			<td><select autofocus="autofocus" required="required" minlength="1" size="12" name="Stock[]" multiple="multiple">';

	while ($myrow=DB_fetch_array($resultLocs)){
				echo '<option value="' . $myrow['stockid'] . '">' . $myrow['stockid'].' - '.$myrow['description'] . '</option>';
	}
	echo '</select></td></tr>';

	echo '</table>
			<br />
			<div class="centre">
				<input type="submit" name="Go" value="' . _('Create PDF') . '" />

			</div>';
    echo '</div>
          </form>';

	 include('includes/footer.inc');
	 exit;
} else {

	include('includes/ConnectDB.inc');
}


if ($_POST['StockLocation']=='All') {
	$sql= "SELECT stockmoves.type,
				stockmoves.stockid,
				stockmaster.description,
				stockmaster.decimalplaces,
				stockmoves.transno,
				stockmoves.trandate,
				SUM(stockmoves.qty) AS qty,
				SUM(stockmoves.qtyrejected) AS qtyrejected,
				stockmoves.reference,
				stockmoves.narrative,
				locations.locationname,
				stockmaster.controlled,
				stockmoves.stkmoveno
			FROM stockmoves
			LEFT JOIN stockmaster
			ON stockmoves.stockid=stockmaster.stockid
			LEFT JOIN locations
			ON stockmoves.loccode=locations.loccode
			INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
			WHERE type='" . $_POST['TransType'] . "'
			AND stockmoves.stockid IN ('". implode("','",$_POST['Stock'])."')
			AND date_format(trandate, '%Y-%m-%d')>='".FormatDateForSQL($_POST['FromDate'])."'
			AND date_format(trandate, '%Y-%m-%d')<='".FormatDateForSQL($_POST['ToDate'])."'
			GROUP BY stockmoves.stockid";
} else {
	$sql= "SELECT stockmoves.type,
				stockmoves.stockid,
				stockmaster.description,
				stockmaster.decimalplaces,
				stockmoves.transno,
				stockmoves.trandate,
				SUM(stockmoves.qty) AS qty,
				SUM(stockmoves.qtyrejected) AS qtyrejected,
				stockmoves.reference,
				stockmoves.narrative,
				locations.locationname,
				stockmaster.controlled,
				stockmoves.stkmoveno
			FROM stockmoves
			LEFT JOIN stockmaster
			ON stockmoves.stockid=stockmaster.stockid
			LEFT JOIN locations
			ON stockmoves.loccode=locations.loccode
			INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
			WHERE type='" . $_POST['TransType'] . "'
			AND stockmoves.stockid IN ('". implode("','",$_POST['Stock'])."')
			AND date_format(trandate, '%Y-%m-%d')>='".FormatDateForSQL($_POST['FromDate'])."'
			AND date_format(trandate, '%Y-%m-%d')<='".FormatDateForSQL($_POST['ToDate'])."'
			AND stockmoves.loccode='" . $_POST['StockLocation'] . "'
			GROUP BY stockmoves.stockid";
}
$result=DB_query($sql,'','',false,false);

if (DB_error_no()!=0){
	$Title = _('Transaction Listing');
	include('includes/header.inc');
	prnMsg(_('An error occurred getting the transactions'),'error');
	include('includes/footer.inc');
	exit;
} elseif (DB_num_rows($result) == 0){
	$Title = _('Transaction Listing');
	include('includes/header.inc');
	echo '<br />';
	prnMsg (_('There were no transactions found in the database between the dates') . ' ' . $_POST['FromDate'] . ' ' . _('and') . ' '. $_POST['ToDate']  . '<br />' ._('Please try again selecting a different date'), 'info');
	include('includes/footer.inc');
  	exit;
}

include('includes/PDFStarter.php');
/*PDFStarter.php has all the variables for page size and width set up depending on the users default preferences for paper size */

$pdf->addInfo('Title',_('Stock Transaction Listing'));
$pdf->addInfo('Subject',_('Stock transaction listing from') . '  ' . $_POST['FromDate'] . ' ' . $_POST['ToDate']);
$line_height=14;
$PageNumber = 1;


switch ($_POST['TransType']) {

	case 26:
		$TransType=_('Work Order Receipts');
		break;
	case 28:
		$TransType=_('Work Order Issues');
		break;
}

include ('includes/PDFPeriodStockTransListingPageHeaderPro.inc');

while ($myrow=DB_fetch_array($result)){
$sql = "SELECT serialno AS snlocstock,
				quantity AS qtylocstock,
				IFNULL((SELECT stockserialmoves.serialno
			FROM stockmoves
			INNER JOIN stockserialmoves ON stockserialmoves.stockmoveno=stockmoves.stkmoveno
			INNER JOIN locationusers ON locationusers.loccode=stockmoves.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
			WHERE type='" . $_POST['TransType'] . "'
			AND stockmoves.stockid IN ('". implode("','",$_POST['Stock'])."')
			AND date_format(trandate, '%Y-%m-%d')>='".FormatDateForSQL($_POST['FromDate'])."'
			AND date_format(trandate, '%Y-%m-%d')<='".FormatDateForSQL($_POST['ToDate'])."'
			AND stockmoves.loccode='" . $_POST['StockLocation'] . "'
			AND stockmoves.stockid = '" . $myrow['stockid'] . "'
			AND stockserialmoves.serialno = stockserialitems.serialno
			GROUP BY stockserialmoves.serialno),stockserialitems.serialno) AS serialno,
			IFNULL((SELECT SUM(moveqty) as mvstock
			FROM stockmoves
			INNER JOIN stockserialmoves ON stockserialmoves.stockmoveno=stockmoves.stkmoveno
			INNER JOIN locationusers ON locationusers.loccode=stockmoves.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
			WHERE type='" . $_POST['TransType'] . "'
			AND stockmoves.stockid IN ('". implode("','",$_POST['Stock'])."')
			AND date_format(trandate, '%Y-%m-%d')>='".FormatDateForSQL($_POST['FromDate'])."'
			AND date_format(trandate, '%Y-%m-%d')<='".FormatDateForSQL($_POST['ToDate'])."'
			AND stockmoves.loccode='" . $_POST['StockLocation'] . "'
			AND stockmoves.stockid = '" . $myrow['stockid'] . "'
			AND stockserialmoves.serialno = stockserialitems.serialno
			GROUP BY stockserialmoves.serialno),0) AS mvstock,
			IFNULL((SELECT SUM(rejectedqty)
			FROM stockmoves
			INNER JOIN stockserialmoves ON stockserialmoves.stockmoveno=stockmoves.stkmoveno
			INNER JOIN locationusers ON locationusers.loccode=stockmoves.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
			WHERE type='" . $_POST['TransType'] . "'
			AND stockmoves.stockid IN ('". implode("','",$_POST['Stock'])."')
			AND date_format(trandate, '%Y-%m-%d')>='".FormatDateForSQL($_POST['FromDate'])."'
			AND date_format(trandate, '%Y-%m-%d')<='".FormatDateForSQL($_POST['ToDate'])."'
			AND stockmoves.loccode='" . $_POST['StockLocation'] . "'
			AND stockmoves.stockid = '" . $myrow['stockid'] . "'
			AND stockserialmoves.serialno = stockserialitems.serialno
			GROUP BY stockserialmoves.serialno),0) AS mvstockreject
			FROM stockserialitems
			INNER JOIN locationusers ON locationusers.loccode=stockserialitems.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
			WHERE stockserialitems.loccode='" . $_POST['StockLocation'] . "'
			AND stockid = '" . $myrow['stockid'] . "'";

$ErrMsg = _('The serial numbers/batches held cannot be retrieved because');
$Stockmv = DB_query($sql, $ErrMsg);

$sqlq = "SELECT SUM(quantity) as qoh
			FROM locstock WHERE stockid='".$myrow['stockid']."'";
$LocStockq = DB_query($sqlq, $ErrMsg);
$myq=DB_fetch_array($LocStockq);

	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,525,$FontSize,$myrow['description'], 'left',1);
	//$LeftOvers = $pdf->addTextWrap($Left_Margin+155,$YPos,70,$FontSize,$myrow['reference'], 'right');
	//$LeftOvers = $pdf->addTextWrap($Left_Margin+232,$YPos,80,$FontSize,ConvertSQLDate($myrow['trandate']), 'left');
	$pdf->SetFont('','B');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+220,$YPos,64,$FontSize,locale_number_format($myrow['qty'],$myrow['decimalplaces']), 'right');
	$pdf->SetFont('');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+272,$YPos,70,$FontSize,locale_number_format($myrow['qtyrejected'],$myrow['decimalplaces']), 'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+340,$YPos,70,$FontSize,locale_number_format(($myrow['qtyrejected']+$myrow['qty']),$myrow['decimalplaces']), 'right');
	$pdf->SetFont('','B');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+452,$YPos,70,$FontSize,locale_number_format($myq['qoh'],$myrow['decimalplaces']), 'right');
	$pdf->SetFont('');
	$YPos -= ($line_height);
	if($myrow['controlled']==1){
	$fill = false;
	$pdf->SetFillColor(224,235,255);
	while ($myro=DB_fetch_array($Stockmv)) {
	if($myro['qtylocstock'] <>0 or $myro['mvstock']<>0){
	if($fill ==false){
	$fill = true;
	}else{
	$fill = false;
	}
	$LeftOvers = $pdf->addTextWrap($Left_Margin+171,$YPos,100,$FontSize,$myro['serialno'], 'left',0,$fill);
	$LeftOvers = $pdf->addTextWrap($Left_Margin+223,$YPos,61,$FontSize,locale_number_format($myro['mvstock'],$myrow['decimalplaces']), 'right',0,$fill);
	
	$LeftOvers = $pdf->addTextWrap($Left_Margin+286,$YPos,58,$FontSize,locale_number_format($myro['mvstockreject'],$myrow['decimalplaces']), 'right',0,$fill);
	$LeftOvers = $pdf->addTextWrap($Left_Margin+346,$YPos,63,$FontSize,locale_number_format(($myro['mvstockreject']+$myro['mvstock']),$myrow['decimalplaces']), 'right',0,$fill);
	
	$LeftOvers = $pdf->addTextWrap($Left_Margin+415,$YPos,100,$FontSize,$myro['snlocstock'], 'left',0,$fill);
	$LeftOvers = $pdf->addTextWrap($Left_Margin+462,$YPos,62,$FontSize,locale_number_format($myro['qtylocstock'],$myrow['decimalplaces']), 'right',0,$fill);
	$YPos -= ($line_height);
	if ($YPos < $Bottom_Margin){
		  //Then set up a new page 
			  $PageNumber++;
		  include ('includes/PDFPeriodStockTransListingPageHeaderPro.inc');
	  }
	}
	}
	}
	
	$TotQty +=$myrow['qty'];
	$TotQtyR += $myrow['qtyrejected'];
	$Tot += ($myrow['qty']+$myrow['qtyrejected']);

	  if ($YPos < $Bottom_Margin){
		  /*Then set up a new page */
			  $PageNumber++;
		  include ('includes/PDFPeriodStockTransListingPageHeaderPro.inc');
	  } /*end of new page header  */
} /* end of while there are customer receipts in the batch to print */

//$LeftOvers = $pdf->addTextWrap($Left_Margin+272,$Bottom_Margin+5,70,$FontSize,'TOTAL :', 'right');
//$LeftOvers = $pdf->addTextWrap($Left_Margin+322,$Bottom_Margin+5,70,$FontSize,locale_number_format($TotQty,$myrow['decimalplaces']), 'right');
//$LeftOvers = $pdf->addTextWrap($Left_Margin+382,$Bottom_Margin+5,70,$FontSize,locale_number_format($TotQtyR,$myrow['decimalplaces']), 'right');
//$LeftOvers = $pdf->addTextWrap($Left_Margin+452,$Bottom_Margin+5,70,$FontSize,locale_number_format($Tot,$myrow['decimalplaces']), 'right');

$YPos-=$line_height;

$ReportFileName = $_SESSION['DatabaseName'] . '_StockTransListing_' . date('Y-m-d').'.pdf';
$pdf->OutputD($ReportFileName);
$pdf->__destruct();

?>