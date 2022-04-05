<?php
/*	$Id: PDFQuotationPortrait.php 4491 2011-02-15 06:31:08Z daintree $ */

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/
ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$sql="SELECT ADDDATE(lastcompleted,frequencydays) AS endate FROM  fixedassettasks";
$Result=DB_query($sql);
while($myrow=DB_fetch_array($Result)){
$endat=$myrow['endate'];
}
$now=date('d/m/Y');
$endate=strtotime($endat);

$diff=$endate-$now;

//Get Out if we have no order number to work with
If (!isset($_GET['id']) || $_GET['id']==""){
        $Title = _('Select Maintenance Complete Form To Print');
        include('includes/header.inc');
        echo '<div class="centre">
				<br />
				<br />
				<br />';
        prnMsg( _('Select Maintenance Complete Form to Print before calling this page') , 'error');
        echo '<br />
				<br />
				<br />
				<table class="table_index">
				<tr>
					<td class="menu_group_item">
						<ul><li><a href="MaintenanceUserSchedule.php">' . _('Maintenance') . '</a></li>
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

 else if(isset($_GET['id']) && $diff>0){
?>
<script>
window.alert("Not Yet Complete!");
window.location ="MaintenanceUserSchedule.php";
</script>

<?php
}

else{

/*retrieve the order details from the database to print */
$ErrMsg = _('There was a problem retrieving the Maintenance details for Request Number') . ' ' . $_GET['id'] . ' ' . _('from the database');

$sql = "SELECT *,ADDDATE(lastcompleted,frequencydays) AS endate FROM irq_maintenance a
        INNER JOIN fixedassets b ON a.mcno = b.serialno
		INNER JOIN fixedassettasks c ON b.assetid =c.assetid  
		WHERE c.taskid='". $_GET['id']."'";

$result=DB_query($sql, $ErrMsg);

//If there are no rows, there's a problem.
if (DB_num_rows($result)==0){
	$Title = _('Print Maintenance Complete Form Error');
	include('includes/header.inc');
	 echo '<div class="centre">
			<br />
			<br />
			<br />';
	prnMsg( _('Unable to Locate Task Number') . ' : ' . $_GET['id'] . ' ', 'error');
	echo '<br />
			<br />
			<br />
			<table class="table_index">
			<tr>
				<td class="menu_group_item">
					<ul><li><a href="MaintenanceUserSchedule.php">' . _('Maintenance') . '</a></li></ul>
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
$pdf->addInfo('Title', _('Maintenance Complete Form') );
$pdf->addInfo('Subject', _('Maintenance Complete Form') . ' ' . $_GET['id']);
$FontSize = 12;
$line_height = 12;// Recommended: $line_height = $x * $FontSize.

/* Now ... Has the order got any line items still outstanding to be invoiced */

$ErrMsg = _('There was a problem retrieving the Maintenance  details for Task Number') . ' ' .
	$_GET['id'] . ' ' . _('from the database');


$result=DB_query($sql, $ErrMsg);

$ListCount = 0;

if (DB_num_rows($result)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('PDFMaintenanceCompleteHeader.php');

	while ($myrow=DB_fetch_array($result)){

        $ListCount ++;

		$YPos -= $line_height;// Increment a line down for the next line item.

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFMaintenanceCompleteHeader.php');
		} //end if need a new page headed up

	

	}// Ends while there are line items to print out.

	if ($YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('PDFMaintenanceCompleteHeader.php');
	} //end if need a new page headed up

} /*end if there are line details to show on the quotation*/


if ($ListCount == 0){
        $Title = _('Print Maintenance Complete Form');
        include('includes/header.inc');
        echo '<p>' .  _('There were no Sick Leave Reports') . '. ' . _('The Maintenance Complete Form cannot be printed').
                '<br /><a href="MaintenanceUserSchedule.php">' .  _('Print Maintenance Complete Form');
        include('includes/footer.inc');
	exit;
} else {
    $pdf->OutputI($_SESSION['DatabaseName'] . '_Maintenance Complete Form_' . $_GET['id'] . '_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
       }
}

ob_end_flush();
?>
