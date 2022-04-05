<?php

ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$PaperSize = 'A4';
include('includes/PDFStarter.php');
/*$PageNumber = 1;// RChacon: PDFStarter.php sets $PageNumber = 0.*/
$pdf->addInfo('Title', _('Leave Report') );
$pdf->addInfo('Subject', _('Leave Report'));
$FontSize = 12;
$line_height = 12;// Recommended: $line_height = $x * $FontSize.

$array = array(0=>'All Leaves',1=>'Approved Leaves',2=>'Pending for Approval Leaves',3=>'Rejected Leaves');
$array2 = array(0=>'All Leave Types',1=>'Off Duty Permission',2=>'Half Day Permission',3=>'Annual Leave',4=>'Sick Leave',5=>'Maternity/Paternity Leave');


$ErrMsg = _('There was a problem retrieving the Leave Report line details for Leave Report Number') . ' ' . _('from the database');

if(isset($_GET['SoL']) && $_GET['SoL']==1){
				  $sort = "AND a.levelcheck >(SELECT MAX(z.levelcheck) FROM leave_approval_levels z WHERE z.leave_type=a.leave_type) AND rejected=0
				  			AND (from_date <= '".date('Y-m-d')."' AND '".date('Y-m-d')."' <= to_date)";
				  $sort1 = "AND a.levelcheck >(SELECT MAX(z.levelcheck) FROM leave_approval_levels z WHERE z.leave_type=a.leave_type) AND rejected=0
				  			AND (date = '".date('Y-m-d')."')";
				  }else{
				  if($_GET['status']==1){
				  $sort = "AND a.levelcheck >(SELECT MAX(z.levelcheck) FROM leave_approval_levels z WHERE z.leave_type=a.leave_type) AND rejected=0 
				  			AND date_added >='".FormatDateForSQL($_GET['from'])."' AND date_added <='".FormatDateForSQL($_GET['to'])."'";
				  $sort1 =$sort;
				  }elseif($_GET['status']==2){
				  $sort = "AND a.levelcheck <(SELECT MAX(z.levelcheck) FROM leave_approval_levels z WHERE z.leave_type=a.leave_type) AND rejected=0
				  			AND date_added >='".FormatDateForSQL($_GET['from'])."' AND date_added <='".FormatDateForSQL($_GET['to'])."'";
				  $sort1 =$sort;
				  }elseif($_GET['status']==3){
				  $sort = "AND rejected=1 AND date_added >='".FormatDateForSQL($_GET['from'])."' AND date_added <='".FormatDateForSQL($_GET['to'])."'";
				  $sort1 =$sort;
				  }else{
				  $sort = "AND date_added >='".FormatDateForSQL($_GET['from'])."' AND date_added <='".FormatDateForSQL($_GET['to'])."'";
				  $sort1 =$sort;
				  }
				  }
				  if($_POST['selectedtype']>=3){
				  $type = "AND type=".$_POST['selectedtype'];
				  }else{
				  $type="";
				  }
				  
				$qry = "SELECT a.emp_id,emp_lname,emp_fname,description,z.type_name,from_date,to_date,days,date_added FROM leave_annual a
				  			INNER JOIN employee b ON a.emp_id = b.emp_id
							INNER JOIN leave_types c ON a.leave_type=c.id
							INNER JOIN departments d ON b.id_dept = d.departmentid
							INNER JOIN leave_all_types z ON a.type=z.id
							WHERE send=1 ".$type." ".$sort;
				$qry2 = "SELECT a.emp_id,emp_lname,emp_fname,description,type_name,from_date,to_date,days,date_added FROM leave_off_duty a
				  			INNER JOIN employee b ON a.emp_id = b.emp_id
							INNER JOIN leave_types c ON a.leave_type=c.id
							INNER JOIN departments d ON b.id_dept = d.departmentid
							WHERE send=1 ".$sort;
				$qry3 = "SELECT a.emp_id,emp_lname,emp_fname,description,type_name,date as from_date,date as to_date,0,date_added FROM leave_half_day a
				  			INNER JOIN employee b ON a.emp_id = b.emp_id
							INNER JOIN leave_types c ON a.leave_type=c.id
							INNER JOIN departments d ON b.id_dept = d.departmentid
							WHERE send=1 ".$sort1;
				if($_GET['type']==1){
				  $rest=DB_query($qry2);
				  }elseif($_GET['type']==2){
				  $rest=DB_query($qry3);
				  }elseif($_GET['type']==0){
				  $sql = $qry." UNION ALL ".$qry2." UNION ALL ".$qry3." ORDER BY date_added DESC";
				  $rest=DB_query($sql);
				  }else{
				  $rest=DB_query($qry);
				  }

$ListCount = 0;
if (DB_num_rows($rest)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('PDFLeaveReportHeader.php');
$FontSize = 8;
	while ($myrow=DB_fetch_array($rest)){
        $ListCount ++;
		$YPos -= $line_height;// Increment a line down for the next line item.
		
		$LeftOvers = $pdf->addTextWrap($Left_Margin-20,$YPos,100,$FontSize, $myrow['emp_id']);
		$LeftOvers = $pdf->addTextWrap(80,$YPos,110,$FontSize, $myrow['emp_lname'].' '.$myrow['emp_fname']);
		$LeftOvers = $pdf->addTextWrap(200,$YPos,95,$FontSize,  $myrow['description'],'left');
		$LeftOvers = $pdf->addTextWrap(300,$YPos,100,$FontSize, str_replace('Application', '', $myrow['type_name']),'left');
		$LeftOvers = $pdf->addTextWrap(400,$YPos,50,$FontSize, ConvertSQLDate($myrow['from_date']),'left');
		$LeftOvers = $pdf->addTextWrap(450,$YPos,50,$FontSize, ConvertSQLDate($myrow['to_date']),'left');
		$LeftOvers = $pdf->addTextWrap(493,$YPos,30,$FontSize, $myrow['days'],'center');
		$LeftOvers = $pdf->addTextWrap(520,$YPos,300,$FontSize, ConvertSQLDate($myrow['date_added']),'left');


		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFLeaveReportHeader.php');
		} //end if need a new page headed up
	}// Ends while there are line items to print out.

	if ($YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('PDFLeaveReportHeader.php');
	} //end if need a new page headed up

} /*end if there are line details to show on the quotation*/


if ($ListCount == 0){
        $Title = _('Print Leave Report Error');
        include('includes/header.inc');
        echo '<p>' .  _('There were no Leave Reports') . '. ' . _('The Leave Report cannot be printed').
                '<br /><a href="index.php?Application=HR&Ref=OffReports">' .  _('Print Another Leave Report');
        include('includes/footer.inc');
	exit;
} else {
    $pdf->OutputD($_SESSION['DatabaseName'] . '_Leave Report_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
}
ob_end_flush();
?>
