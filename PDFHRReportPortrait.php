<?php
/*	$Id: PDFQuotationPortrait.php 4491 2011-02-15 06:31:08Z daintree $ */

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/
ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

//Get Out if we have no order number to work with
If (!isset($_GET['id']) || $_GET['id']==""){
        $Title = _('Select Number of Employees Report To Print');
        include('includes/header.inc');
        echo '<div class="centre">
				<br />
				<br />
				<br />';
        prnMsg( _('Select a Number of Employees Report to Print before calling this page') , 'error');
        echo '<br />
				<br />
				<br />
				<table class="table_index">
				<tr>
					<td class="menu_group_item">
						<ul><li><a href="index.php?Application=HR&Ref=HrReport">' . _('Employee Report') . '</a></li>
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
$ErrMsg = _('There was a problem retrieving the Number of Employees  header details for Department Id') . ' ' . $_GET['id'] . ' ' . _('from the database');

$sql = "SELECT description,organization_chart.no_of_employees as dept,COUNT( employee.id_dept) as total FROM organization_chart, employee,departments  
						WHERE departments.departmentid = employee.id_dept
						AND departments.departmentid  = organization_chart.id_dept
						AND employee.stat  = '1'
						AND departments.departmentid='" . $_GET['id'] . "'
						GROUP BY  employee.id_dept";
						
						
$result=DB_query($sql, $ErrMsg);

//If there are no rows, there's a problem.
if (DB_num_rows($result)==0){
	$Title = _('Print Number of Employees Report Error');
	include('includes/header.inc');
	 echo '<div class="centre">
			<br />
			<br />
			<br />';
	prnMsg( _('Unable to Locate Number of Employees Report Number') . ' : ' . $_GET['id'] . ' ', 'error');
	echo '<br />
			<br />
			<br />
			<table class="table_index">
			<tr>
				<td class="menu_group_item">
					<ul><li><a href="index.php?Application=HR&Ref=HrReport">' . _('Employee Report') . '</a></li></ul>
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
							$total = $myrow['dept'];
							$emp = $myrow['total'];
							$diff=$total-$emp;
							$diffm=$emp-$total;
}
/*retrieve the order details from the database to print */

/* Then there's an order to print and its not been printed already (or its been flagged for reprinting/ge_Width=807;
)
LETS GO */
$PaperSize = 'A4';
include('includes/PDFStarter.php');
/*$PageNumber = 1;// RChacon: PDFStarter.php sets $PageNumber = 0.*/
$pdf->addInfo('Title', _('Number of Employees Report') );
$pdf->addInfo('Subject', _('Number of Employees Report') . ' ' . $_GET['id']);
$FontSize = 12;
$line_height = 12;// Recommended: $line_height = $x * $FontSize.

/* Now ... Has the order got any line items still outstanding to be invoiced */

$ErrMsg = _('There was a problem retrieving the Number of Employees Report line details for Department Id') . ' ' .
	$_GET['id'] . ' ' . _('from the database');

						
						$sql = "SELECT section_name,organization_chart.no_of_employees as sec,COUNT( employee.id_sec) as totalsec FROM organization_chart, employee,section,departments  
						WHERE  section .id_sec = employee.id_sec
						AND section .id_sec  = organization_chart.id_sec
						AND section .id_dept  = departments.departmentid
						AND employee .id_dept  = departments.departmentid
						AND employee.stat  = '1'
						AND departments.departmentid='" . $_GET['id'] . "'
						GROUP BY  employee.id_sec";
				
$result=DB_query($sql, $ErrMsg);

$ListCount = 0;

if (DB_num_rows($result)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('PDFHRReportHeader.php');

	while ($myrow2=DB_fetch_array($result)){
                            
							
							$sec = $rmyrow2['section_name'];
							$sectotal = $myrow2['sec'];
							$secemp = $myrow2['totalsec'];
							$diffsec=$sectotal-$secemp;
							$diffsecm=$secemp-$sectotal;
        $ListCount ++;

		$YPos -= $line_height;// Increment a line down for the next line item.

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFHRReportHeader.php');
		} //end if need a new page headed up

		$FontSize = 10;// Font size for the line item.
echo $sec;
		$LeftOvers = $pdf->addText($Left_Margin, $YPos+$FontSize, $FontSize, $myrow2['section_name']);
		$LeftOvers = $pdf->addText(150, $YPos+$FontSize, $FontSize, $myrow2['sec']);
		$LeftOvers = $pdf->addText(200, $YPos+$FontSize, $FontSize, $diffsecm);


	}// Ends while there are line items to print out.

	if ($YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('PDFHRReportHeader.php');
	} //end if need a new page headed up

} /*end if there are line details to show on the quotation*/


if ($ListCount == 0){
        $Title = _('Print Number of Employees Report Error');
        include('includes/header.inc');
        echo '<p>' .  _('There were no Number of Employees Reports') . '. ' . _('The Number of Employees Report cannot be printed').
                '<br /><a href="index.php?Application=HR&Ref=HrReport">' .  _('Print Another Number of Employees Report');
        include('includes/footer.inc');
	exit;
} else {
    $pdf->OutputI($_SESSION['DatabaseName'] . '_Number of Employees Report_' . $_GET['id'] . '_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
}
ob_end_flush();
?>
