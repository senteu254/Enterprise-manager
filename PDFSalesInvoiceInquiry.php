<?php

/* $Id: PDFDIFOT.php 6943 2014-10-27 07:06:42Z daintree $*/

include ('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

$InputError=0;

if (isset($_POST['FromDate']) AND !Is_Date($_POST['FromDate'])){
	$msg = _('The date from must be specified in the format') . ' ' . $_SESSION['DefaultDateFormat'];
	$InputError=1;
}
if (isset($_POST['ToDate']) AND !Is_Date($_POST['ToDate'])){
	$msg =  _('The date to must be specified in the format') . ' ' .  $_SESSION['DefaultDateFormat'];
	$InputError=1;
}

if (!isset($_POST['FromDate']) OR !isset($_POST['ToDate']) OR $InputError==1){

	 $Title = _('Sales Invoice Inquiry Report');
	 include ('includes/header.inc');

	echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/transactions.png" title="' . $Title . '" alt="" />' . ' '
		. _('Sales Invoice Inquiry Report') . '</p>';

	 echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
     echo '<div>';
	 echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	 echo '<table class="selection">
			<tr>
				<td>' . _('From Date') . ':</td>
				<td><input type="text" required="required" autofocus="autofocus" class="date" alt="' .$_SESSION['DefaultDateFormat'].'" name="FromDate" maxlength="10" size="10" value="' . Date($_SESSION['DefaultDateFormat'], Mktime(0,0,0,Date('m')-1,0,Date('y'))) . '" /></td>
			</tr>
			<tr>
				<td>' . _('To Date') . ':</td>
				<td><input type="text" required="required" class="date" alt="'.$_SESSION['DefaultDateFormat'].'" name="ToDate" maxlength="10" size="10" value="' . Date($_SESSION['DefaultDateFormat']) . '" /></td>
			</tr>';


	 echo '<tr>
				<td>' . _('Sales Person') . ':</td>
				<td>';
	$sql = "SELECT salesmanname, salesmancode FROM salesman";
	 $result = DB_query($sql);


	 echo '<select name="SalesPerson">';
	 echo '<option selected="selected" value="All">' . _('Over All Sales Persons') . '</option>';

	while ($myrow=DB_fetch_array($result)){
		echo '<option value="' . $myrow['salesmancode'] . '">' . $myrow['salesmanname'] . '</option>';
	}

	 echo '</select></td></tr>';

	 echo '<tr>
			<td>' . _('Inventory Location') . ':</td>
			<td><select name="Location">
				<option selected="selected" value="All">' . _('All Locations') . '</option>';

	$result= DB_query("SELECT locations.loccode, locationname FROM locations INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1");
	while ($myrow=DB_fetch_array($result)){
		echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
	}
	 echo '</select></td></tr>';
	 
	echo '<tr>
				<td>' . _('Customers') . ':</td>
				<td>';
	$sql = "SELECT debtorno, name FROM debtorsmaster";
	 $result = DB_query($sql);


	 echo '<select name="Customer">';
	 echo '<option selected="selected" value="All">' . _('All Customers') . '</option>';

	while ($myrow=DB_fetch_array($result)){
		echo '<option value="' . $myrow['debtorno'] . '">' . $myrow['name'] . '</option>';
	}

	 echo '</select></td></tr>';
	 echo '
		</table>
		<br />
		<div class="centre">
		<input type="submit" name="Go" value="' . _('Create PDF') . '" />
		</div>
		</div>
	</form>';

	 if ($InputError==1){
	 	prnMsg($msg,'error');
	 }
	 include('includes/footer.inc');
	 exit;
} else {
	 include('includes/ConnectDB.inc');
}
if($_POST['Customer']=='All'){
$customer ="";
}else{
$customer =" AND debtorsmaster.debtorno='".$_POST['Customer']."'";
}
if ($_POST['SalesPerson']=='All' AND $_POST['Location']=='All'){
			$sql = "SELECT debtortrans.trandate,
							debtortrans.transno,
							debtortrans.ovamount,
							debtortrans.ovgst,
							debtortrans.alloc,
							debtorsmaster.name,
							custbranch.brname,
							salesman.salesmanname,
							debtortrans.order_,
							salesorders.customerref
							FROM debtortrans 
							INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno 
							INNER JOIN salesorders ON debtortrans.order_ = salesorders.orderno 
							INNER JOIN custbranch ON custbranch.branchcode = debtortrans.branchcode 
							INNER JOIN salesman ON salesman.salesmancode = debtortrans.salesperson
							INNER JOIN locationusers ON locationusers.loccode=salesorders.fromstkloc AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
							WHERE debtortrans.trandate>='" . FormatDateForSQL($_POST['FromDate']) . "'
							AND debtortrans.trandate <='" . FormatDateForSQL($_POST['ToDate']) . "'
							AND debtortrans.type=10 ".$customer."";

} elseif ($_POST['SalesPerson']!='All' AND $_POST['Location']=='All') {
			$sql = "SELECT debtortrans.trandate,
							debtortrans.transno,
							debtortrans.ovamount,
							debtortrans.ovgst,
							debtortrans.alloc,
							debtorsmaster.name,
							custbranch.brname,
							salesman.salesmanname,
							debtortrans.order_,
							salesorders.customerref
							FROM debtortrans 
							INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno 
							INNER JOIN salesorders ON debtortrans.order_ = salesorders.orderno 
							INNER JOIN custbranch ON custbranch.branchcode = debtortrans.branchcode 
							INNER JOIN salesman ON salesman.salesmancode = debtortrans.salesperson
							INNER JOIN locationusers ON locationusers.loccode=salesorders.fromstkloc AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
							WHERE debtortrans.trandate>='" . FormatDateForSQL($_POST['FromDate']) . "'
							AND debtortrans.trandate <='" . FormatDateForSQL($_POST['ToDate']) . "'
							AND debtortrans.salesperson='" . $_POST['SalesPerson'] ."'
							AND debtortrans.type=10 ".$customer."";

} elseif ($_POST['SalesPerson']=='All' AND $_POST['Location']!='All') {

			$sql = "SELECT debtortrans.trandate,
							debtortrans.transno,
							debtortrans.ovamount,
							debtortrans.ovgst,
							debtortrans.alloc,
							debtorsmaster.name,
							custbranch.brname,
							salesman.salesmanname,
							debtortrans.order_,
							salesorders.customerref
							FROM debtortrans 
							INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno 
							INNER JOIN salesorders ON debtortrans.order_ = salesorders.orderno 
							INNER JOIN custbranch ON custbranch.branchcode = debtortrans.branchcode 
							INNER JOIN salesman ON salesman.salesmancode = debtortrans.salesperson
							INNER JOIN locationusers ON locationusers.loccode=salesorders.fromstkloc AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
							WHERE debtortrans.trandate>='" . FormatDateForSQL($_POST['FromDate']) . "'
							AND debtortrans.trandate <='" . FormatDateForSQL($_POST['ToDate']) . "'
							AND salesorders.fromstkloc='" . $_POST['Location'] . "'
							AND debtortrans.type=10 ".$customer."";

} elseif ($_POST['SalesPerson']!='All' AND $_POST['Location']!='All'){
			$sql = "SELECT debtortrans.trandate,
							debtortrans.transno,
							debtortrans.ovamount,
							debtortrans.ovgst,
							debtortrans.alloc,
							debtorsmaster.name,
							custbranch.brname,
							salesman.salesmanname,
							debtortrans.order_,
							salesorders.customerref
							FROM debtortrans 
							INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno 
							INNER JOIN salesorders ON debtortrans.order_ = salesorders.orderno 
							INNER JOIN custbranch ON custbranch.branchcode = debtortrans.branchcode 
							INNER JOIN salesman ON salesman.salesmancode = debtortrans.salesperson
							INNER JOIN locationusers ON locationusers.loccode=salesorders.fromstkloc AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
							WHERE debtortrans.trandate>='" . FormatDateForSQL($_POST['FromDate']) . "'
							AND debtortrans.trandate <='" . FormatDateForSQL($_POST['ToDate']) . "'
							AND debtortrans.salesperson='" . $_POST['SalesPerson'] ."'
							AND salesorders.fromstkloc='" . $_POST['Location'] . "'
							AND debtortrans.type=10 ".$customer."";

}

$Result=DB_query($sql,'','',false,false); //dont error check - see below

if (DB_error_no()!=0){
	$Title = _('Sales Inquiry Report Error');
	include('includes/header.inc');
	prnMsg( _('An error occurred getting the days between delivery requested and actual invoice'),'error');
	if ($debug==1){
		prnMsg( _('The SQL used to get the days between requested delivery and actual invoice dates was') . "<br />$sql",'error');
	}
	include ('includes/footer.inc');
	exit;
} elseif (DB_num_rows($Result) == 0){
	$Title = _('Sales Inquiry Report Error');
  	include('includes/header.inc');
	prnMsg( _('There were no variances between deliveries and orders found in the database within the period from') . ' ' . $_POST['FromDate'] . ' ' . _('to') . ' ' . $_POST['ToDate'] . '. ' . _('Please try again selecting a different date range'), 'info');
	if ($debug==1) {
		prnMsg( _('The SQL that returned no rows was') . '<br />' . $sql,'error');
	}
	include('includes/footer.inc');
	exit;
}
$PaperSize ='A4_Landscape';
include('includes/PDFStarter.php');

/*PDFStarter.php has all the variables for page size and width set up depending on the users default preferences for paper size */

//$pdf->addInfo('Title',_('Dispatches After Day(s) from Requested Delivery Date'));
$pdf->addInfo('Subject',_('Delivery Dates from') . ' ' . $_POST['FromDate'] . ' ' . _('to') . ' ' . $_POST['ToDate']);
$line_height=12;
$PageNumber = 1;
$TotalDiffs = 0;

include ('includes/PDFSalesInvoicePageHeader.inc');

$TotTax=0;
$TotAmt=0;
$TotCum=0;
$TotAloc=0;
$TotBal=0;

while ($myrow=DB_fetch_array($Result)){
			$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,40,$FontSize,ConvertSQLDate($myrow['trandate']), 'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+50,$YPos,80,$FontSize,$myrow['transno'], 'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+80,$YPos,130,$FontSize,$myrow['name'], 'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+230,$YPos,130,$FontSize,$myrow['brname'], 'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+360,$YPos,50,$FontSize,$myrow['order_'], 'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+410,$YPos,70,$FontSize,$myrow['customerref'], 'left');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+480,$YPos,50,$FontSize,locale_number_format($myrow['ovgst'],2), 'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+530,$YPos,70,$FontSize,locale_number_format($myrow['ovamount'],2), 'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+600,$YPos,70,$FontSize,locale_number_format(($myrow['ovgst']+$myrow['ovamount']),2), 'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+650,$YPos,70,$FontSize,locale_number_format($myrow['alloc'],2), 'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+710,$YPos,70,$FontSize,locale_number_format((($myrow['ovgst']+$myrow['ovamount'])-$myrow['alloc']),2), 'right');

			$YPos -= ($line_height);
			$TotalDiffs++;
			
		$TotTax += $myrow['ovgst'];
		$TotAmt += $myrow['ovamount'];
		$TotCum += ($myrow['ovgst']+$myrow['ovamount']);
		$TotAloc += $myrow['alloc'];
		$TotBal += (($myrow['ovgst']+$myrow['ovamount'])-$myrow['alloc']);

			if ($YPos - (2 *$line_height) < $Bottom_Margin){
		  /*Then set up a new page */
			  $PageNumber++;
		  include ('includes/PDFSalesInvoicePageHeader.inc');
			} /*end of new page header  */
} /* end of while there are delivery differences to print */
			$pdf->SetFont('','B');
			$YPos -= ($line_height);
			$LeftOvers = $pdf->addTextWrap($Left_Margin+480,$YPos,50,$FontSize,locale_number_format($TotTax,2), 'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+530,$YPos,70,$FontSize,locale_number_format($TotAmt,2), 'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+600,$YPos,70,$FontSize,locale_number_format($TotCum,2), 'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+650,$YPos,70,$FontSize,locale_number_format($TotAloc,2), 'right');
			$LeftOvers = $pdf->addTextWrap($Left_Margin+710,$YPos,70,$FontSize,locale_number_format($TotBal,2), 'right');

$ReportFileName = $_SESSION['DatabaseName'] . '_SAlesInquiry_' . date('Y-m-d').'.pdf';
$pdf->OutputI($ReportFileName);
$pdf->__destruct();
?>
