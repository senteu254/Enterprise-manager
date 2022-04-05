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
$line_height = 12;// Recommended: $line_height = $x * $FontSize.



$ErrMsg = _('There was a problem retrieving the Leave Report line details for Leave Report Number') . ' ' . _('from the database');

				  if($_GET['dept'] !=0){
				  $sort = " AND YEAR(date_added)='".$_GET['year']."' AND d.departmentid=".$_GET['dept'];
				  }else{
				  $sort =" AND YEAR(date_added)='".$_GET['year']."'";
				  }
				  
				$qry = "SELECT a.emp_id,emp_lname,emp_fname,description,z.type_name,from_date,to_date,days,date_added,d.departmentid,
				IFNULL((SELECT emp_id FROM leave_annual z 
				WHERE z.leave_id=a.leave_id AND z.rejected=0 
				AND z.levelcheck >(SELECT MAX(levelcheck) FROM leave_approval_levels WHERE leave_type=z.leave_type)),0) as approved, rejected FROM leave_annual a
				  			INNER JOIN employee b ON a.emp_id = b.emp_id
							INNER JOIN leave_types c ON a.leave_type=c.id
							INNER JOIN departments d ON b.id_dept = d.departmentid
							INNER JOIN leave_all_types z ON a.type=z.id
							WHERE send=1 ".$sort;
				$qry2 = "SELECT a.emp_id,emp_lname,emp_fname,description,type_name,from_date,to_date,days,date_added,d.departmentid,
				IFNULL((SELECT emp_id FROM leave_off_duty z 
				WHERE z.off_id=a.off_id AND z.rejected=0 
				AND z.levelcheck >(SELECT MAX(levelcheck) FROM leave_approval_levels WHERE leave_type=z.leave_type)),0) as approved, rejected FROM leave_off_duty a
				  			INNER JOIN employee b ON a.emp_id = b.emp_id
							INNER JOIN leave_types c ON a.leave_type=c.id
							INNER JOIN departments d ON b.id_dept = d.departmentid
							WHERE send=1 ".$sort;
				$qry3 = "SELECT a.emp_id,emp_lname,emp_fname,description,type_name,date as from_date,date as to_date,0,date_added,d.departmentid,
				IFNULL((SELECT emp_id FROM leave_half_day z 
				WHERE z.half_id=a.half_id AND z.rejected=0 
				AND z.levelcheck >(SELECT MAX(levelcheck) FROM leave_approval_levels WHERE leave_type=z.leave_type)),0) as approved, rejected FROM leave_half_day a
				  			INNER JOIN employee b ON a.emp_id = b.emp_id
							INNER JOIN leave_types c ON a.leave_type=c.id
							INNER JOIN departments d ON b.id_dept = d.departmentid
							WHERE send=1 ".$sort;

				  $sql = $qry." UNION ALL ".$qry2." UNION ALL ".$qry3." ORDER BY departmentid ASC,emp_id ASC,date_added DESC";
				  $rest=DB_query($sql);

$ListCount = 0;
if (DB_num_rows($rest)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('PDFLeaveReportPerDeptHeader.php');
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
		if($user != $myrow['emp_id']){
		$pdf->SetTextColor(60, 7, 252);		  
		$LeftOvers = $pdf->addTextWrap($Left_Margin-20,$YPos,400,$FontSize, $no_user.'.   '.$myrow['emp_id'].' - '.$myrow['emp_lname'].' '.$myrow['emp_fname']);
		$pdf->SetTextColor('');
		$YPos -= $line_height;
		$user = $myrow['emp_id'];
		$no_user ++;
		}
		
		//$LeftOvers = $pdf->addTextWrap($Left_Margin-20,$YPos,100,$FontSize, $myrow['emp_id']);
		//$LeftOvers = $pdf->addTextWrap(80,$YPos,110,$FontSize, $myrow['emp_lname'].' '.$myrow['emp_fname']);
		//$LeftOvers = $pdf->addTextWrap(200,$YPos,95,$FontSize,  $myrow['description'],'left');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+20,$YPos,200,$FontSize, str_replace('Application', '', $myrow['type_name']),'left');
		$LeftOvers = $pdf->addTextWrap(200,$YPos,50,$FontSize, ConvertSQLDate($myrow['from_date']),'left');
		$LeftOvers = $pdf->addTextWrap(300,$YPos,50,$FontSize, ConvertSQLDate($myrow['to_date']),'left');
		$LeftOvers = $pdf->addTextWrap(380,$YPos,30,$FontSize, $myrow['days'],'center');
		$LeftOvers = $pdf->addTextWrap(440,$YPos,80,$FontSize, ($myrow['rejected']==1 ? 'Rejected' : ''.($myrow['approved']==0 ? 'Pending' : 'Approved').''),'left');
		$LeftOvers = $pdf->addTextWrap(500,$YPos,300,$FontSize, ConvertSQLDate($myrow['date_added']),'left');


		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFLeaveReportPerDeptHeader.php');
		} //end if need a new page headed up
	}// Ends while there are line items to print out.

	if ($YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('PDFLeaveReportPerDeptHeader.php');
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
