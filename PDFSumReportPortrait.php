<?php
/*	$Id: PDFQuotationPortrait.php 4491 2011-02-15 06:31:08Z daintree $ */

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/
ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

//Get Out if we have no order number to work with
If (!isset($_GET['id']) || $_GET['id']==""){
        $Title = _('Select  Report To Print');
        include('includes/header.inc');
        echo '<div class="centre">
				<br />
				<br />
				<br />';
        prnMsg( _('Select a  Report to Print before calling this page') , 'error');
        echo '<br />
				<br />
				<br />
				<table class="table_index">
				<tr>
					<td class="menu_group_item">
						<ul><li><a href="index.php?Application=HR&Ref=SumRep">' . _('Summary Report') . '</a></li>
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
$ErrMsg = _('There was a problem retrieving  header details for Personal Number') . ' ' . $_GET['id'] . ' ' . _('from the database');

				
$sql = "(SELECT *,employee.emp_id as id FROM employee, leaves, departments,vacation_log,mp_log,hd_log,off_log 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.grade = 'I-CHIEF OFFICER' 
									AND (vacation_log.emp_id= employee.emp_id
									OR mp_log.emp_id= employee.emp_id 
									OR hd_log.emp_id= employee.emp_id 
									OR off_log.emp_id= employee.emp_id
									OR employee.emp_id = leaves.emp_id ) 
									AND employee.emp_id='" . $_GET['id'] . "' )
									UNION
									(SELECT *,employee.emp_id as id FROM employee, leaves, departments ,vacation_log,mp_log,hd_log,off_log
									WHERE employee.id_dept = departments.departmentid 
									AND employee.grade != 'I-CHIEF OFFICER'
									AND employee.grade  != 'MANAGER'
									AND (vacation_log.emp_id= employee.emp_id
									OR mp_log.emp_id= employee.emp_id 
									OR hd_log.emp_id= employee.emp_id 
									OR off_log.emp_id= employee.emp_id
									OR employee.emp_id = leaves.emp_id )  
									AND employee.emp_id='" . $_GET['id'] . "' )
									UNION
									(SELECT *,employee.emp_id as id FROM employee, leaves, departments ,vacation_log,mp_log,hd_log,off_log
									WHERE employee.id_dept = departments.departmentid  
									AND employee.grade  = 'MANAGER'
									AND (vacation_log.emp_id= employee.emp_id
									OR mp_log.emp_id= employee.emp_id 
									OR hd_log.emp_id= employee.emp_id 
									OR off_log.emp_id= employee.emp_id
									OR employee.emp_id = leaves.emp_id ) 
									AND employee.emp_id='" . $_GET['id'] . "')";


$res=DB_query($sql,$ErrMsg);
//If there are no rows, there's a problem.
if (DB_num_rows($res)==0){
	$Title = _('Print  Report Error');
	include('includes/header.inc');
	 echo '<div class="centre">
			<br />
			<br />
			<br />';
	prnMsg( _('Unable to Locate  Individual Summary Report Header Details for Personal Number') . ' : ' . $_GET['id'] . ' ', 'error');
	echo '<br />
			<br />
			<br />
			<table class="table_index">
			<tr>
				<td class="menu_group_item">
					<ul><li><a href="index.php?Application=HR&Ref=SumRep">' . _('Summary Report') . '</a></li></ul>
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
	$myrow = DB_fetch_array($res);
}
/*retrieve the order details from the database to print */

/* Then there's an order to print and its not been printed already (or its been flagged for reprinting/ge_Width=807;
)
LETS GO */
$PaperSize = 'A4';
include('includes/PDFStarter.php');
/*$PageNumber = 1;// RChacon: PDFStarter.php sets $PageNumber = 0.*/
$pdf->addInfo('Title', _(' Individual Summary Report') );
$pdf->addInfo('Subject', _(' Individual Summary Report') . ' ' . $_GET['id']);
$FontSize = 12;
$line_height = 12;// Recommended: $line_height = $x * $FontSize.

/* Now ... Has the order got any line items still outstanding to be invoiced */

$ErrMsg = _('There was a problem retrieving the  Report line details for Personal Number') . ' ' .
	$_GET['id'] . ' ' . _('from the database');
	

$sql = "(SELECT *,employee.emp_id as id FROM employee, leaves, departments,general_manager
									WHERE employee.id_dept = departments.departmentid 
									AND employee.grade = 'I-CHIEF OFFICER' 
									AND employee.emp_id = leaves.emp_id 
									AND general_manager.gm = leaves.general_manager
									AND employee.emp_id='" . $_GET['id'] . "' )
									UNION
									(SELECT *,employee.emp_id as id FROM employee, leaves, departments,program_head 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.grade != 'I-CHIEF OFFICER'
									AND employee.grade  != 'MANAGER'
									AND employee.emp_id = leaves.emp_id 
									AND program_head.prog_head  = leaves.prog_head  
									AND employee.emp_id='" . $_GET['id'] . "' )
									UNION
									(SELECT *,employee.emp_id as id FROM employee, leaves, departments,managing_director
									WHERE employee.id_dept = departments.departmentid  
									AND employee.grade  = 'MANAGER'
									AND  employee.emp_id = leaves.emp_id 
									AND managing_director.md = leaves.managing_director
									AND employee.emp_id='" . $_GET['id'] . "')";
$sql1 = "(SELECT *,employee.emp_id as id FROM employee, vacation_log, departments,general_manager
									WHERE employee.id_dept = departments.departmentid 
									AND employee.grade = 'I-CHIEF OFFICER' 
									AND employee.emp_id = vacation_log.emp_id 
									AND general_manager.gm = vacation_log.general_manager
									AND employee.emp_id='" . $_GET['id'] . "' )
									UNION
									(SELECT *,employee.emp_id as id FROM employee, vacation_log, departments,program_head 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.grade != 'I-CHIEF OFFICER'
									AND employee.grade  != 'MANAGER'
									AND employee.emp_id = vacation_log.emp_id 
									AND program_head.prog_head  = vacation_log.prog_head  
									AND employee.emp_id='" . $_GET['id'] . "' )
									UNION
									(SELECT *,employee.emp_id as id FROM employee, vacation_log, departments,managing_director
									WHERE employee.id_dept = departments.departmentid  
									AND employee.grade  = 'MANAGER'
									AND  employee.emp_id = vacation_log.emp_id 
									AND managing_director.md = vacation_log.managing_director
									AND employee.emp_id='" . $_GET['id'] . "')";
$sql2 = "(SELECT *,employee.emp_id as id FROM employee, mp_log, departments,general_manager
									WHERE employee.id_dept = departments.departmentid 
									AND employee.grade = 'I-CHIEF OFFICER' 
									AND employee.emp_id = mp_log.emp_id 
									AND general_manager.gm = mp_log.general_manager
									AND employee.emp_id='" . $_GET['id'] . "' )
									UNION
									(SELECT *,employee.emp_id as id FROM employee, mp_log, departments,program_head 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.grade != 'I-CHIEF OFFICER'
									AND employee.grade  != 'MANAGER'
									AND employee.emp_id = mp_log.emp_id 
									AND program_head.prog_head  = mp_log.prog_head  
									AND employee.emp_id='" . $_GET['id'] . "' )
									UNION
									(SELECT *,employee.emp_id as id FROM employee, mp_log, departments,managing_director
									WHERE employee.id_dept = departments.departmentid  
									AND employee.grade  = 'MANAGER'
									AND  employee.emp_id = mp_log.emp_id 
									AND managing_director.md = mp_log.managing_director
									AND employee.emp_id='" . $_GET['id'] . "')";
									
$sql3 = "SELECT *,employee.emp_id as id,TIMEDIFF(etime,stime) AS timedifference FROM employee, hd_log, departments,approve_hr
									WHERE employee.id_dept = departments.departmentid  
									AND employee.emp_id = hd_log.emp_id 
									AND approve_hr.hr_approve = hd_log.hr_approve
									AND employee.emp_id='" . $_GET['id'] . "' ";
$sql4 = "SELECT *,employee.emp_id as id FROM employee, off_log, departments,approve_hr
									WHERE employee.id_dept = departments.departmentid  
									AND employee.emp_id = off_log.emp_id 
									AND approve_hr.hr_approve = off_log.hr_approve
									AND employee.emp_id='" . $_GET['id'] . "' ";



$result=DB_query($sql, $ErrMsg);
$result1=DB_query($sql1, $ErrMsg);
$result2=DB_query($sql2, $ErrMsg);
$result3=DB_query($sql3, $ErrMsg);
$result4=DB_query($sql4, $ErrMsg);

$ListCount = 0;

if (DB_num_rows($result)>0 || DB_num_rows($result1)>0 || DB_num_rows($result2)>0  || DB_num_rows($result3)>0 || DB_num_rows($result4)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('PDFSumReportHeader.php');

	while ($myrow2=DB_fetch_array($result)){

        $ListCount ++;

		$YPos -= $line_height;// Increment a line down for the next line item.

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFSumReportHeader.php');
		} //end if need a new page headed up
		$FontSize = 10;// Font size for the line item.

		$LeftOvers = $pdf->addText($Left_Margin, $YPos+$FontSize, $FontSize, $myrow2['edate']);
		$LeftOvers = $pdf->addText(120, $YPos+$FontSize, $FontSize, $myrow2['endate']);
		$LeftOvers = $pdf->addText(200, $YPos+$FontSize, $FontSize, $myrow2['no_days']);
		$LeftOvers = $pdf->addText(280, $YPos+$FontSize, $FontSize, $myrow2['leavetype']);
        $LeftOvers = $pdf->addText(420, $YPos+$FontSize, $FontSize, $myrow2['name_stat_md']);
		 $LeftOvers = $pdf->addText(420, $YPos+$FontSize, $FontSize, $myrow2['name_stat_gm']);
		 $LeftOvers = $pdf->addText(420, $YPos+$FontSize, $FontSize, $myrow2['name_stat_prog']);
		


	}// Ends while there are line items to print out.
	while ($myrow2=DB_fetch_array($result1)){

        $ListCount ++;

		$YPos -= $line_height;// Increment a line down for the next line item.

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFSumReportHeader.php');
		} //end if need a new page headed up
		$FontSize = 10;// Font size for the line item.

		$LeftOvers = $pdf->addText($Left_Margin, $YPos+$FontSize, $FontSize, $myrow2['sdate']);
		$LeftOvers = $pdf->addText(120, $YPos+$FontSize, $FontSize, $myrow2['eddate']);
		$LeftOvers = $pdf->addText(200, $YPos+$FontSize, $FontSize, $myrow2['nodays']);
		$LeftOvers = $pdf->addText(280, $YPos+$FontSize, $FontSize, $myrow2['leavetype']);
        $LeftOvers = $pdf->addText(420, $YPos+$FontSize, $FontSize, $myrow2['name_stat_md']);
		 $LeftOvers = $pdf->addText(420, $YPos+$FontSize, $FontSize, $myrow2['name_stat_gm']);
		 $LeftOvers = $pdf->addText(420, $YPos+$FontSize, $FontSize, $myrow2['name_stat_prog']);
		


	}// Ends while there are line items to print out.
	while ($myrow2=DB_fetch_array($result2)){

        $ListCount ++;

		$YPos -= $line_height;// Increment a line down for the next line item.

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFSumReportHeader.php');
		} //end if need a new page headed up
		$FontSize = 10;// Font size for the line item.

		$LeftOvers = $pdf->addText($Left_Margin, $YPos+$FontSize, $FontSize, $myrow2['sdate']);
		$LeftOvers = $pdf->addText(120, $YPos+$FontSize, $FontSize, $myrow2['eddate']);
		$LeftOvers = $pdf->addText(200, $YPos+$FontSize, $FontSize, $myrow2['nodays']);
		$LeftOvers = $pdf->addText(280, $YPos+$FontSize, $FontSize, $myrow2['leavetype']);
        $LeftOvers = $pdf->addText(420, $YPos+$FontSize, $FontSize, $myrow2['name_stat_md']);
		 $LeftOvers = $pdf->addText(420, $YPos+$FontSize, $FontSize, $myrow2['name_stat_gm']);
		 $LeftOvers = $pdf->addText(420, $YPos+$FontSize, $FontSize, $myrow2['name_stat_prog']);
		


	}// Ends while there are line items to print out.
	while ($myrow2=DB_fetch_array($result3)){

        $ListCount ++;

		$YPos -= $line_height;// Increment a line down for the next line item.

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFSumReportHeader.php');
		} //end if need a new page headed up
		$FontSize = 10;// Font size for the line item.
//$diff=date_diff($myrow2['stime'],$myrow2['etime']);
		$LeftOvers = $pdf->addText($Left_Margin, $YPos+$FontSize, $FontSize, $myrow2['stime']);
		$LeftOvers = $pdf->addText(120, $YPos+$FontSize, $FontSize, $myrow2['etime']);
		$LeftOvers = $pdf->addText(200, $YPos+$FontSize, $FontSize,$myrow2['timedifference']);
		$LeftOvers = $pdf->addText(280, $YPos+$FontSize, $FontSize, $myrow2['leavetype']);
        $LeftOvers = $pdf->addText(420, $YPos+$FontSize, $FontSize, $myrow2['name_stat_hr']);
		


	}// Ends while there are line items to print out.

while ($myrow2=DB_fetch_array($result4)){

        $ListCount ++;

		$YPos -= $line_height;// Increment a line down for the next line item.

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFSumReportHeader.php');
		} //end if need a new page headed up
		$FontSize = 10;// Font size for the line item.

		$LeftOvers = $pdf->addText($Left_Margin, $YPos+$FontSize, $FontSize, $myrow2['sdate']);
		$LeftOvers = $pdf->addText(120, $YPos+$FontSize, $FontSize, $myrow2['eddate']);
		$LeftOvers = $pdf->addText(200, $YPos+$FontSize, $FontSize, $myrow2['nodays']);
		$LeftOvers = $pdf->addText(280, $YPos+$FontSize, $FontSize, $myrow2['leavetype']);
        $LeftOvers = $pdf->addText(420, $YPos+$FontSize, $FontSize, $myrow2['name_stat_hr']);
		


	}// Ends while there are line items to print out.


	if ($YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('PDFSumReportHeader.php');
	} //end if need a new page headed up

} /*end if there are line details to show on the quotation*/


if ($ListCount == 0){
        $Title = _('Print  Individual Summary Report Error');
        include('includes/header.inc');
        echo '<p>' .  _('There were no  Individual Summary Reports') . '. ' . _('The  Report cannot be printed').
                '<br /><a href="index.php?Application=HR&Ref=SumRep">' .  _('Print Another  Report');
        include('includes/footer.inc');
	exit;
} else {
    $pdf->OutputI($_SESSION['DatabaseName'] . '_Report_' . $_GET['id'] . '_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
}
ob_end_flush();
?>
