<?php
/*	$Id: PDFQuotationPortrait.php 4491 2011-02-15 06:31:08Z daintree $ */

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/
ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

//Get Out if we have no order number to work with
If (!isset($_GET['id']) || $_GET['id']==""){
        $Title = _('Select Maternity/Paternity Report To Print');
        include('includes/header.inc');
        echo '<div class="centre">
				<br />
				<br />
				<br />';
        prnMsg( _('Select a Maternity/Paternity Report to Print before calling this page') , 'error');
        echo '<br />
				<br />
				<br />
				<table class="table_index">
				<tr>
					<td class="menu_group_item">
						<ul><li><a href="index.php?Application=HR&Ref=MPReports">' . _('Maternity/Paternity Report') . '</a></li>
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
$ErrMsg = _('There was a problem retrieving the Maternity/Paternity Report header details for Request Number') . ' ' . $_GET['id'] . ' ' . _('from the database');
$sql = "(SELECT * ,employee.emp_id as id FROM employee, mp_log, departments,general_manager 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.emp_id = mp_log.emp_id
									AND general_manager.gm = mp_log.general_manager
									AND general_manager.gm ='4'
									AND employee.emp_id='" . $_GET['id'] . "' )
									UNION
									(SELECT * ,employee.emp_id as id FROM employee, mp_log, departments,program_head 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.emp_id = mp_log.emp_id
									AND program_head.prog_head = mp_log.prog_head
									AND program_head.prog_head ='4'
									AND employee.emp_id='" . $_GET['id'] . "'  )
									UNION
									(SELECT *,employee.emp_id as id FROM employee, mp_log, departments,managing_director 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.emp_id = mp_log.emp_id
									AND managing_director.md = mp_log.managing_director
									AND managing_director.md ='2'
									AND employee.emp_id='" . $_GET['id'] . "' )
									UNION
									(SELECT * ,employee.emp_id as id FROM employee, mp_log, departments,general_manager 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.emp_id = mp_log.emp_id
									AND general_manager.gm = mp_log.general_manager
									OR general_manager.gm ='1'
									AND employee.emp_id='" . $_GET['id'] . "' )
									UNION
									(SELECT * ,employee.emp_id as id FROM employee, mp_log, departments,program_head 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.emp_id = mp_log.emp_id
									AND program_head.prog_head = mp_log.prog_head
									OR program_head.prog_head ='1'
									AND employee.emp_id='" . $_GET['id'] . "'  )
									UNION
									(SELECT *,employee.emp_id as id FROM employee, mp_log, departments,managing_director 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.emp_id = mp_log.emp_id
									AND managing_director.md = mp_log.managing_director
									OR managing_director.md ='1'
									AND employee.emp_id='" . $_GET['id'] . "' )
									UNION
									(SELECT * ,employee.emp_id as id FROM employee, mp_log, departments,general_manager 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.emp_id = mp_log.emp_id
									AND general_manager.gm = mp_log.general_manager
									OR general_manager.gm ='5'
									AND employee.emp_id='" . $_GET['id'] . "' )
									UNION
									(SELECT * ,employee.emp_id as id FROM employee, mp_log, departments,program_head 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.emp_id = mp_log.emp_id
									AND program_head.prog_head = mp_log.prog_head
									OR program_head.prog_head ='5'
									AND employee.emp_id='" . $_GET['id'] . "'  )
									UNION
									(SELECT *,employee.emp_id as id FROM employee, mp_log, departments,managing_director 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.emp_id = mp_log.emp_id
									AND managing_director.md = mp_log.managing_director
									OR managing_director.md ='3'
									AND employee.emp_id='" . $_GET['id'] . "' )";
$result=DB_query($sql, $ErrMsg);
$sql = "(SELECT *,SUM(nodays) AS nod1 FROM employee, mp_log, departments,general_manager 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.emp_id = mp_log.emp_id
									AND general_manager.gm = mp_log.general_manager
									AND general_manager.gm ='4'
									AND employee.emp_id='" . $_GET['id'] . "' 
									GROUP BY employee.emp_id)
									UNION
									(SELECT *,SUM(nodays) AS nod1 FROM employee, mp_log, departments,program_head 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.emp_id = mp_log.emp_id
									AND program_head.prog_head = mp_log.prog_head
									AND program_head.prog_head ='4'
									AND employee.emp_id='" . $_GET['id'] . "' 
									GROUP BY employee.emp_id)
									UNION
									(SELECT *,SUM(nodays) AS nod1 FROM employee, mp_log, departments,managing_director 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.emp_id = mp_log.emp_id
									AND managing_director.md = mp_log.managing_director
									AND managing_director.md ='2'
									AND employee.emp_id='" . $_GET['id'] . "'
									GROUP BY employee.emp_id)
									";

$qry2 = DB_query($sql);
			$recc = DB_fetch_array($qry2);
			$cred = 90;
			$value2 = $recc ['nod1'];
			if(empty($value2)){
			$value2=0;
			}
			$value3 = $cred - $value2;

//If there are no rows, there's a problem.
if (DB_num_rows($result)==0){
	$Title = _('Print Maternity/Paternity Report Error');
	include('includes/header.inc');
	 echo '<div class="centre">
			<br />
			<br />
			<br />';
	prnMsg( _('Unable to Locate Maternity/Paternity Report Number') . ' : ' . $_GET['id'] . ' ', 'error');
	echo '<br />
			<br />
			<br />
			<table class="table_index">
			<tr>
				<td class="menu_group_item">
					<ul><li><a href="index.php?Application=HR&Ref=MPReports">' . _('Maternity/Paternity Report') . '</a></li></ul>
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
$pdf->addInfo('Title', _('Maternity/Paternity Report') );
$pdf->addInfo('Subject', _('Maternity/Paternity Report') . ' ' . $_GET['id']);
$FontSize = 12;
$line_height = 12;// Recommended: $line_height = $x * $FontSize.

/* Now ... Has the order got any line items still outstanding to be invoiced */

$ErrMsg = _('There was a problem retrieving the Maternity/Paternity Report line details for Maternity/Paternity Report Number') . ' ' .
	$_GET['id'] . ' ' . _('from the database');

$sql = "(SELECT *,employee.emp_id as id FROM employee, mp_log, departments,general_manager 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.emp_id = mp_log.emp_id
									AND general_manager.gm = mp_log.general_manager
									AND employee.grade = 'I-CHIEF OFFICER'
									AND employee.emp_id='" . $_GET['id'] . "' )
									UNION
									(SELECT *,employee.emp_id as id FROM employee, mp_log, departments,program_head 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.emp_id = mp_log.emp_id
									AND employee.grade != 'I-CHIEF OFFICER'
									AND employee.grade  != 'MANAGER'
									AND program_head.prog_head = mp_log.prog_head
									AND employee.emp_id='" . $_GET['id'] . "' )
									UNION
									(SELECT *,employee.emp_id as id FROM employee, mp_log, departments,managing_director 
									WHERE employee.id_dept = departments.departmentid 
									AND employee.emp_id = mp_log.emp_id
									AND employee.grade  = 'MANAGER'
									AND managing_director.md = mp_log.managing_director
									AND employee.emp_id='" . $_GET['id'] . "')";

$result=DB_query($sql, $ErrMsg);

$ListCount = 0;

if (DB_num_rows($result)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('PDFMPReportHeader.php');

	while ($myrow2=DB_fetch_array($result)){

        $ListCount ++;

		$YPos -= $line_height;// Increment a line down for the next line item.

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFMPReportHeader.php');
		} //end if need a new page headed up

		$FontSize = 10;// Font size for the line item.

		$LeftOvers = $pdf->addText($Left_Margin, $YPos+$FontSize, $FontSize, $myrow2['sdate']);
		$LeftOvers = $pdf->addText(120, $YPos+$FontSize, $FontSize, $myrow2['eddate']);
		$LeftOvers = $pdf->addText(200, $YPos+$FontSize, $FontSize, $myrow2['nodays']);
         $LeftOvers = $pdf->addText(280, $YPos+$FontSize, $FontSize, $myrow2['name_stat_md']);
		 $LeftOvers = $pdf->addText(280, $YPos+$FontSize, $FontSize, $myrow2['name_stat_gm']);
		 $LeftOvers = $pdf->addText(280, $YPos+$FontSize, $FontSize, $myrow2['name_stat_prog']);
		


	}// Ends while there are line items to print out.

	if ($YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('PDFMPReportHeader.php');
	} //end if need a new page headed up

} /*end if there are line details to show on the quotation*/


if ($ListCount == 0){
        $Title = _('Print Maternity/Paternity Report Error');
        include('includes/header.inc');
        echo '<p>' .  _('There were no Maternity/Paternity Reports') . '. ' . _('The Maternity/Paternity Report cannot be printed').
                '<br /><a href="index.php?Application=HR&Ref=MPReports">' .  _('Print Another Maternity/Paternity Report');
        include('includes/footer.inc');
	exit;
} else {
    $pdf->OutputI($_SESSION['DatabaseName'] . '_Maternity/Paternity Report_' . $_GET['id'] . '_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
}
ob_end_flush();
?>
