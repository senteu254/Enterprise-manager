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

	 $Title = _('Requisition Report');
	 include ('includes/header.inc');

	echo '<div class="centre">
			<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/transactions.png" title="' . $Title . '" alt="" />' . ' '. _('Requisition Report') . '</p>
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
		</tr>';
		
	$sqld = "SELECT doc_id, doc_name FROM irq_documents WHERE doc_id IN (1,4) ORDER BY doc_id DESC";
	$resultType = DB_query($sqld);

	echo '<tr>
			<td>' . _('Document Type') . ':</td>
			<td><select required="required" name="Type">';

	while ($myro=DB_fetch_array($resultType)){
			if ($myro['doc_id'] == $_POST['type']){
				echo '<option selected="selected" value="' . $myro['doc_id'] . '">' . $myro['doc_name'] . '</option>';
			} else {
				echo '<option value="' . $myro['doc_id'] . '">' . $myro['doc_name'] . '</option>';
			}
	}
	echo '</select></td></tr>';
	
	$sqld = "SELECT departmentid, description FROM departments";
	$resultDept = DB_query($sqld);

	echo '<tr>
			<td>' . _('Department') . ':</td>
			<td><select required="required" name="department">';
	echo '<option value="0">All Department</option>';
	while ($myrow=DB_fetch_array($resultDept)){
			if ($myrow['departmentid'] == $_POST['department']){
				echo '<option selected="selected" value="' . $myrow['departmentid'] . '">' . $myrow['description'] . '</option>';
			} else {
				echo '<option value="' . $myrow['departmentid'] . '">' . $myrow['description'] . '</option>';
			}
	}
	echo '</select></td></tr>';

	$sql = "SELECT locations.loccode, locationname FROM locations INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1";
	$resultStkLocs = DB_query($sql);

	echo '<tr>
			<td>' . _('For Stock Location') . ':</td>
			<td><select required="required" name="StockLocation">';
	echo '<option value="0">All Store Locations</option>';
	while ($myrow=DB_fetch_array($resultStkLocs)){
			if ($myrow['loccode'] == $_POST['StockLocation']){
				echo '<option selected="selected" value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			} else {
				echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
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

if($_POST['department']==0){
$dept ="";
}else{
$dept =" AND a.departmentid='" . $_POST['department'] . "'";
}
if($_POST['StockLocation']==0){
$sloc="";
}else{
$sloc =" AND a.loccode='". $_POST['StockLocation'] ."'";
}
	$sql= "SELECT * FROM irq_request z 
							INNER JOIN irq_stockrequest a ON a.dispatchid = z.requestid
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN www_users b on z.initiator = b.userid
							INNER JOIN locations ON locations.loccode = a.loccode
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							WHERE z.doc_id='". $_POST['Type'] ."'
							".$sloc."
							".$dept."
							AND date_format(Requesteddate, '%Y-%m-%d')>='".FormatDateForSQL($_POST['FromDate'])."'
							AND date_format(Requesteddate, '%Y-%m-%d')<='".FormatDateForSQL($_POST['ToDate'])."'
							AND z.closed !=2 AND z.draft=0";

$result=DB_query($sql,'','',false,false);

if (DB_error_no()!=0){
	$Title = _('Requisition Report');
	include('includes/header.inc');
	prnMsg(_('An error occurred getting the transactions'),'error');
	include('includes/footer.inc');
	exit;
} elseif (DB_num_rows($result) == 0){
	$Title = _('Requisition Report');
	include('includes/header.inc');
	echo '<br />';
	prnMsg (_('There were no transactions found in the database between the dates') . ' ' . $_POST['FromDate'] . ' ' . _('and') . ' '. $_POST['ToDate']  . '<br />' ._('Please try again selecting a different date'), 'info');
	include('includes/footer.inc');
  	exit;
}
$PaperSize = 'A4_Landscape';
include('includes/PDFStarter.php');
/*PDFStarter.php has all the variables for page size and width set up depending on the users default preferences for paper size */

$pdf->addInfo('Title',_('Requisition Report'));
$pdf->addInfo('Subject',_('Requisition Report from') . '  ' . $_POST['FromDate'] . ' ' . $_POST['ToDate']);
$line_height=12;
$PageNumber = 1;

include ('includes/PDFRequisitionPageHeader.inc');

while ($myrow=DB_fetch_array($result)){

$sql = "SELECT *, irq_stockrequestitems.quantity as quantity,irq_stockrequestitems.qtydelivered as qtydelivered FROM stockmaster INNER JOIN irq_stockrequestitems
							ON irq_stockrequestitems.stockid = stockmaster.stockid
							WHERE irq_stockrequestitems.dispatchid='".$myrow['requestid']."'";

$ErrMsg = _('The serial numbers/batches held cannot be retrieved because');
$LocStockResult = DB_query($sql, $ErrMsg);

	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,772,$FontSize,date("d, M Y",strtotime($myrow['Requesteddate'])), 'left',1);
	$LeftOvers = $pdf->addTextWrap($Left_Margin+70,$YPos,70,$FontSize,$myrow['requestid'], 'left');
	//$LeftOvers = $pdf->addTextWrap($Left_Margin+232,$YPos,80,$FontSize,ConvertSQLDate($myrow['trandate']), 'left');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+125,$YPos,145,$FontSize,$myrow['realname'], 'left');
	//$YPos -= ($line_height);
	
	while ($myrow2=DB_fetch_array($LocStockResult)) {
	$LeftOvers = $pdf->addTextWrap($Left_Margin+280,$YPos,70,$FontSize,$myrow2['stockid'], 'left');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+345,$YPos,230,$FontSize,$myrow2['description'], 'left');
	if($myrow2['completed']==1 && $myrow2['cancelled']==0 && $myrow2['qtydelivered']>=$myrow2['quantity']){
	$status='Completed';
	}elseif($myrow2['completed']==1 && $myrow2['cancelled']==1 && $myrow2['qtydelivered']!=$myrow2['quantity'] && $myrow2['qtydelivered']!=0){
	$status='Partial Issue';
	}elseif($myrow2['cancelled']==1 && $myrow2['qtydelivered']==0){
	$status='Cancelled';
	}else{
	$status='Pending';
	}
	//$pdf->SetFont('','B');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+550,$YPos,70,$FontSize,$myrow2['quantity'], 'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+600,$YPos,70,$FontSize,$myrow2['qtydelivered'], 'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+650,$YPos,120,$FontSize,$status, 'right');
	//$pdf->SetFont('');
	$YPos -= ($line_height);
	if ($YPos < $Bottom_Margin){
		  //Then set up a new page 
			  $PageNumber++;
		  include ('includes/PDFRequisitionPageHeader.inc');
	  }
	}
	
$YPos-=$line_height;
	  if ($YPos < $Bottom_Margin){
		  /*Then set up a new page */
			  $PageNumber++;
		  include ('includes/PDFRequisitionPageHeader.inc');
	  } /*end of new page header  */
} /* end of while there are customer receipts in the batch to print */

//$YPos-=$line_height;

$ReportFileName = $_SESSION['DatabaseName'] . '_Requisition_' . date('Y-m-d').'.pdf';
$pdf->OutputI($ReportFileName);
$pdf->__destruct();

?>