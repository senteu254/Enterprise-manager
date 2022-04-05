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

	 $Title = _('Stock Transfer Transaction Listing');
	 include ('includes/header.inc');

	echo '<div class="centre">
			<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/transactions.png" title="' . $Title . '" alt="" />' . ' '. _('Stock Transfer Transaction Listing') . '</p>
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
				<option value="16">' . _('Location Transfer') . '</option>
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
	
	echo '<tr>
			<td>' . _('Calibre') . ':</td>
			<td><select required="required" name="Calibre">
				<option value="All">' . _('All') . '</option>';
	$sql = "SELECT stockid,description FROM stockmaster WHERE categoryid='FINAMO' AND discontinued=0";
	$resultStkLocs = DB_query($sql);
	while ($myrow=DB_fetch_array($resultStkLocs)){
		if (isset($_POST['Calibre']) AND $_POST['Calibre']!='All'){
			if ($myrow['stockid'] == $_POST['Calibre']){
				echo '<option selected="selected" value="' . $myrow['stockid'] . '">' . strtoupper($myrow['description']) . '</option>';
			} else {
				echo '<option value="' . $myrow['stockid'] . '">' . strtoupper($myrow['description']) . '</option>';
			}
		}else {
			echo '<option value="' . $myrow['stockid'] . '">' . strtoupper($myrow['description']) . '</option>';
		}
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

if ($_POST['Calibre']=='All') {
$stockx = "";
}else{
$stockx = " AND stockmoves.stockid='".$_POST['Calibre']."'";
}

if ($_POST['StockLocation']=='All') {
	$sql= "SELECT stockmoves.type,
				stockmoves.stkmoveno,
				stockmoves.stockid,
				stockmaster.description,
				stockmaster.decimalplaces,
				stockmoves.transno,
				stockmoves.trandate,
				stockmoves.qty,
				stockmoves.reference,
				stockmoves.narrative,
				locations.locationname
			FROM stockmoves
			LEFT JOIN stockmaster
			ON stockmoves.stockid=stockmaster.stockid
			LEFT JOIN locations
			ON stockmoves.loccode=locations.loccode
			INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
			WHERE type='" . $_POST['TransType'] . "'
			AND date_format(trandate, '%Y-%m-%d')>='".FormatDateForSQL($_POST['FromDate'])."'
			AND date_format(trandate, '%Y-%m-%d')<='".FormatDateForSQL($_POST['ToDate'])."' 
			".$stockx."";
} else {
	$sql= "SELECT stockmoves.type,
				stockmoves.stkmoveno,
				stockmoves.stockid,
				stockmaster.description,
				stockmaster.decimalplaces,
				stockmoves.transno,
				stockmoves.trandate,
				stockmoves.qty,
				stockmoves.reference,
				stockmoves.narrative,
				locations.locationname
			FROM stockmoves
			LEFT JOIN stockmaster
			ON stockmoves.stockid=stockmaster.stockid
			LEFT JOIN locations
			ON stockmoves.loccode=locations.loccode
			INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
			WHERE type='" . $_POST['TransType'] . "'
			AND date_format(trandate, '%Y-%m-%d')>='".FormatDateForSQL($_POST['FromDate'])."'
			AND date_format(trandate, '%Y-%m-%d')<='".FormatDateForSQL($_POST['ToDate'])."'
			AND stockmoves.loccode='" . $_POST['StockLocation'] . "'
			".$stockx."";
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

$pdf->addInfo('Title',_('Stock Transfer Transaction Listing'));
$pdf->addInfo('Subject',_('Stock transaction listing from') . '  ' . $_POST['FromDate'] . ' ' . $_POST['ToDate']);
$line_height=12;
$PageNumber = 1;


switch ($_POST['TransType']) {
	case 10:
		$TransType=_('Customer Invoices');
		break;
	case 11:
		$TransType=_('Customer Credit Notes');
		break;
	case 16:
		$TransType=_('Location Transfers');
		break;
	case 17:
		$TransType=_('Stock Adjustments');
		break;
	case 25:
		$TransType=_('Purchase Order Deliveries');
		break;
	case 26:
		$TransType=_('Work Order Receipts');
		break;
	case 28:
		$TransType=_('Work Order Issues');
		break;
}

include ('includes/PDFPeriodStockTransListingPageHeader_Transfers.inc');

while ($myrow=DB_fetch_array($result)){
	
	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,160,$FontSize,$myrow['description'], 'left');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+162,$YPos,80,$FontSize,$myrow['transno'], 'left');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+202,$YPos,70,$FontSize,ConvertSQLDate($myrow['trandate']), 'left');
	$pdf->SetFont('','B');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+325,$YPos,70,$FontSize,locale_number_format($myrow['qty'],$myrow['decimalplaces']), 'right');
	$pdf->SetFont('','');
	//$LeftOvers = $pdf->addTextWrap($Left_Margin+382,$YPos,70,$FontSize,$myrow['locationname'], 'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+400,$YPos,120,$FontSize,$myrow['reference'], 'right');
	$sql="SELECT * FROM stockserialmoves WHERE stockmoveno=".$myrow['stkmoveno'];
	$res=DB_query($sql);
	while ($myr=DB_fetch_array($res)){
	$LeftOvers = $pdf->addTextWrap($Left_Margin+230,$YPos,70,$FontSize,$myr['serialno'], 'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+274,$YPos,70,$FontSize,locale_number_format($myr['moveqty'],$myrow['decimalplaces']), 'right');
	$YPos -= ($line_height);
	}

	$YPos -= ($line_height);

	  if ($YPos - (2 *$line_height) < $Bottom_Margin){
		  /*Then set up a new page */
			  $PageNumber++;
		  include ('includes/PDFPeriodStockTransListingPageHeader_Transfers.inc');
	  } /*end of new page header  */
} /* end of while there are customer receipts in the batch to print */


$YPos-=$line_height;

$ReportFileName = $_SESSION['DatabaseName'] . '_StockTransferListing_' . date('Y-m-d').'.pdf';
$pdf->OutputI($ReportFileName);
$pdf->__destruct();

?>