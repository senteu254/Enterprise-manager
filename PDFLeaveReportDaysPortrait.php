<?php

ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$PaperSize = 'A4';
include('includes/PDFStarter.php');
/*$PageNumber = 1;// RChacon: PDFStarter.php sets $PageNumber = 0.*/
$pdf->addInfo('Title', _('Leave Report') );
$pdf->addInfo('Subject', _('Leave Report'));
$FontSize = 13;
$line_height = 13;// Recommended: $line_height = $x * $FontSize.



$ErrMsg = _('There was a problem retrieving the Leave Report line details for Leave Report Number') . ' ' . _('from the database');

				  if($_GET['dept'] !=0){
				  $sort = " year='".$_GET['year']."' AND d.departmentid=".$_GET['dept']." ORDER BY d.description ASC,emp_fname ASC";
				  }else{
				  $sort =" year='".$_GET['year']."' ORDER BY d.description ASC,emp_fname ASC";
				  }
				  
				$sql = "SELECT *, IFNULL((SELECT SUM(days) FROM leave_annual z 
				WHERE z.type=a.leave_type AND z.emp_id=a.emp_id AND YEAR(z.date_added)=a.year AND rejected=0 
				AND z.levelcheck >(SELECT MAX(levelcheck) FROM leave_approval_levels WHERE leave_type=z.leave_type) GROUP BY z.emp_id),0) as applied 
				FROM leave_days_allocation a
				  			INNER JOIN employee b ON a.emp_id = b.emp_id
							INNER JOIN leave_types c ON a.leave_type=c.id
							INNER JOIN departments d ON b.id_dept = d.departmentid
							WHERE ".$sort;
				  $rest=DB_query($sql);

$ListCount = 0;
if (DB_num_rows($rest)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('PDFLeaveReportDaysHeader.php');
$FontSize = 10;
$pdf->SetFillColor(206, 219, 226);
$dept ="";
$user ="";
	while ($myrow=DB_fetch_array($rest)){
        $ListCount ++;
		$YPos -= $line_height;// Increment a line down for the next line item.
		
		if($dept != $myrow['departmentid']){
		$no_user = 1;
		$LeftOvers = $pdf->addTextWrap($Left_Margin-20,$YPos,555,$FontSize,  $myrow['description'],'center',0,1);
		//$pdf->SetFillColor();
		$YPos -= $line_height;
		$dept = $myrow['departmentid'];
		 }
		$LeftOvers = $pdf->addTextWrap($Left_Margin-20,$YPos,100,$FontSize, $myrow['emp_id']);
		$LeftOvers = $pdf->addTextWrap(70,$YPos,110,$FontSize, $myrow['emp_fname'].' '.$myrow['emp_lname']);
		$LeftOvers = $pdf->addTextWrap(230,$YPos,200,$FontSize, str_replace('Application', '', $myrow['type_name']),'left');
		$LeftOvers = $pdf->addTextWrap(330,$YPos,40,$FontSize, $myrow['opening_bal'],'right');
		$LeftOvers = $pdf->addTextWrap(380,$YPos,40,$FontSize, $myrow['leave_days'],'right');
		$LeftOvers = $pdf->addTextWrap(430,$YPos,40,$FontSize, ($myrow['opening_bal']+$myrow['leave_days']),'right');
		$LeftOvers = $pdf->addTextWrap(480,$YPos,40,$FontSize, $myrow['applied'],'right');
		$LeftOvers = $pdf->addTextWrap(530,$YPos,40,$FontSize, ($myrow['opening_bal']+$myrow['leave_days']-$myrow['applied']),'right');


		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFLeaveReportDaysHeader.php');
		} //end if need a new page headed up
	}// Ends while there are line items to print out.

	if ($YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('PDFLeaveReportDaysHeader.php');
	} //end if need a new page headed up

} /*end if there are line details to show on the quotation*/


if ($ListCount == 0){
        $Title = _('Print Leave Report Error');
        include('includes/header.inc');
        echo '<p>' .  _('There were no Leave Reports') . '. ' . _('The Leave Report cannot be printed').
                '<br /><a href="index.php?Application=HR&Ref=LeaveReports">' .  _('Print Another Leave Report');
        include('includes/footer.inc');
	exit;
} else {
    $pdf->OutputD($_SESSION['DatabaseName'] . '_Leave_Report_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
}
ob_end_flush();
?>
