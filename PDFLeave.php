<?php
ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$PaperSize = 'A5_Landscape';
include('includes/PDFStarter.php');
/*$PageNumber = 1;// RChacon: PDFStarter.php sets $PageNumber = 0.*/
$pdf->addInfo('Title', _('Leave Report') );
$pdf->addInfo('Subject', _('Leave Report'));
$FontSize = 12;
$line_height = 12;// Recommended: $line_height = $x * $FontSize.
$LID = $_GET['LID'];
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
				if($_GET['L_Type']==1){	
						$results = "SELECT *, b.emp_id as em_num FROM leave_off_duty a
							INNER JOIN employee b ON a.emp_id=b.emp_id
							INNER JOIN www_users c ON a.added_by=c.userid
							INNER JOIN leave_types d ON a.leave_type=d.id
							LEFT JOIN departments e ON e.departmentid= b.id_dept
							LEFT JOIN section f ON b.id_sec=f.id_sec
							WHERE send = 1 AND off_id=".$LID."";
				}elseif($_GET['L_Type']==2){	
						$results = "SELECT *, b.emp_id as em_num FROM leave_half_day a
							INNER JOIN employee b ON a.emp_id=b.emp_id
							INNER JOIN www_users c ON a.added_by=c.userid
							INNER JOIN leave_types d ON a.leave_type=d.id
							LEFT JOIN departments e ON e.departmentid= b.id_dept
							LEFT JOIN section f ON b.id_sec=f.id_sec
							WHERE send = 1 AND half_id=".$LID."";
				}elseif($_GET['L_Type']>=3){	
						$results = "SELECT *, b.emp_id as em_num,z.type_name, d.id as id FROM leave_annual a
							INNER JOIN employee b ON a.emp_id=b.emp_id
							INNER JOIN www_users c ON a.added_by=c.userid
							INNER JOIN leave_types d ON a.leave_type=d.id
							INNER JOIN leave_all_types z ON a.type=z.id
							LEFT JOIN departments e ON e.departmentid= b.id_dept
							LEFT JOIN section f ON b.id_sec=f.id_sec
							WHERE send = 1 AND leave_id=".$LID."";
				}else{
				die('<b style="color:#FF0000">Invalid Request, Please try Again</b>');
				}
				$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
				$myrow = DB_fetch_array($welcome_viewed);

// $PageNumber is initialised in 0 by includes/PDFStarter.php.
$PageNumber ++;// Increments $PageNumber before printing.
if ($PageNumber>1) {// Inserts a page break if it is not the first page.
	$pdf->newPage();
}

// Prints company logo:
$XPos = 50;
$pdf->addJpegFromFile($_SESSION['LogoFile'],$XPos,330,0,60);

// Prints 'Quotation' title:

$tt = strtoupper($myrow['type_name']);


$pdf->addTextWrap(0, $Page_Height-$Top_Margin-80, $Page_Width, 14,$tt, 'center');

// Prints 'Delivery To' info:
$XPos = 46;
$YPos = 360;
$FontSize=12;
// Prints 'Quotation For' info:
$YPos -= 70;

// Draws a box with round corners around 'Delivery To' info:
$pdf->addTextWrap($Page_Width-$Right_Margin-430, $Page_Height-$Top_Margin-10-$FontSize*1, 300, $FontSize, $_SESSION['CompanyRecord']['coyname'], 'left');
$FontSize=10;
$pdf->addTextWrap($Page_Width-$Right_Margin-430, $Page_Height-$Top_Margin-20-$FontSize*2, 200, $FontSize, $_SESSION['CompanyRecord']['regoffice1'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-430, $Page_Height-$Top_Margin-20-$FontSize*3, 200, $FontSize,  _('Ph') . ': ' . $_SESSION['CompanyRecord']['telephone'] .' ' . _('Fax'). ': ' . $_SESSION['CompanyRecord']['fax'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-430, $Page_Height-$Top_Margin-20-$FontSize*4, 200, $FontSize, $_SESSION['CompanyRecord']['email'], 'left');

// Prints quotation info:
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-20-$FontSize*1, 200, $FontSize,  _('Printed On'). ': '.date("d, M Y"), 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-20-$FontSize*2, 200, $FontSize, _('Ref No.'). ': '.$_GET['LID'], 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-20-$FontSize*3, 200, $FontSize, _('Page').': '.$PageNumber, 'right');

$XPos = 40;
$LeftOvers = $pdf->addTextWrap($Left_Margin-20,$YPos-10,400,$FontSize, _('Names : ................................................................................................'));
$LeftOvers = $pdf->addTextWrap(70,$YPos-5,200,12, strtoupper($myrow['emp_fname'].' '.$myrow['emp_mname'].' '.$myrow['emp_lname']));
$LeftOvers = $pdf->addTextWrap(300,$YPos-10,300,$FontSize, _('Personal No.: ..........................................................................'),'left');
$LeftOvers = $pdf->addTextWrap(400,$YPos-5,300,12, $myrow['em_num'],'left');

$YPosx = 30;
$LeftOvers = $pdf->addTextWrap($Left_Margin-20,$YPos-10-$YPosx,400,$FontSize, _('Department : .........................................................................................'));
$LeftOvers = $pdf->addTextWrap(80,$YPos-5-$YPosx,200,12, strtoupper($myrow['description']));
$LeftOvers = $pdf->addTextWrap(300,$YPos-10-$YPosx,300,$FontSize, _('Section : ..................................................................................'),'left');
$LeftOvers = $pdf->addTextWrap(350,$YPos-5-$YPosx,200,12, strtoupper($myrow['section_name']),'left');

$YPosx = 60;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-5-$YPosx,490,12, strip_tags($myrow['narrative']));
while (mb_strlen($LeftOvers)>0) {
	$YPos -= 15;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-5-$YPosx,490,12,$LeftOvers);
	$YPosz += 15;
	}

if($_GET['L_Type']>=3){
$LeftOvers = $pdf->addTextWrap($Left_Margin-20,$YPos-5-$YPosx,300,12, 'Incase I cannot be reached Please contact:- ','left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+200,$YPos-5-$YPosx,300,12, strtoupper($myrow['em_name']),'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+200,$YPos-18-$YPosx,300,12, ucwords(strtolower($myrow['em_address'])),'left');
$LeftOvers = $pdf->addTextWrap($Left_Margin+200,$YPos-30-$YPosx,300,12, strtoupper($myrow['em_phone']),'left');
$YPosx += 30;
}
//$YPosx = 120;

$rest = "SELECT *,levelcheck as lc FROM leave_approval_levels a
				LEFT JOIN leave_approval b ON a.levelcheck=b.approver AND b.leave_id='".$LID."'
				WHERE a.leave_type='".$myrow['id']."'";
$welcom = DB_query($rest,$ErrMsg,$DbgMsg);
while($rowb = DB_fetch_array($welcom)){
if($rowb['lc'] ==1 && $myrow['approver1'] !=NULL && $myrow['rejected']==0){
$check =$rowb['accept_msg'].' By '.$rowb['approver_name'];
$date = ConvertSQLDate($myrow['approver1']);
}elseif($rowb['lc'] ==1 && $myrow['approver1'] !=NULL && $myrow['rejected']==1){
$check =$rowb['reject_msg'].' By '.$rowb['approver_name'];
$date = ConvertSQLDate($myrow['approver1']);
}elseif($rowb['lc'] ==2 && $myrow['approver2'] !=NULL && $myrow['rejected']==0){
$check =$rowb['accept_msg'].' By '.$rowb['approver_name'];
$date = ConvertSQLDate($myrow['approver2']);
}elseif($rowb['lc'] ==2 && $myrow['approver2'] !=NULL && $myrow['rejected']==1){
$check =$rowb['reject_msg'].' By '.$rowb['approver_name'];
$date = ConvertSQLDate($myrow['approver2']);
}elseif($rowb['lc'] ==3 && $myrow['approver3'] !=NULL && $myrow['rejected']==0){
$check =$rowb['accept_msg'].' By '.$rowb['approver_name'];
$date = ConvertSQLDate($myrow['approver3']);
}elseif($rowb['lc'] ==3 && $myrow['approver3'] !=NULL && $myrow['rejected']==1){
$check =$rowb['reject_msg'].' By '.$rowb['approver_name'];
$date = ConvertSQLDate($myrow['approver3']);
}elseif($rowb['lc'] ==4 && $myrow['approver4'] !=NULL && $myrow['rejected']==0){
$check =$rowb['accept_msg'].' By '.$rowb['approver_name'];
$date = ConvertSQLDate($myrow['approver4']);
}elseif($rowb['lc'] ==4 && $myrow['approver4'] !=NULL && $myrow['rejected']==1){
$check =$rowb['reject_msg'].' By '.$rowb['approver_name'];
$date = ConvertSQLDate($myrow['approver4']);
}else{
$check = '';
$date = '';
}

$YPos -= 20;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-10-$YPosx,400,12, $rowb['level_position']);
$pdf->setFont('','B');
$LeftOvers = $pdf->addTextWrap($Left_Margin+200,$YPos-10-$YPosx,400,$FontSize, ucwords(strtolower($check)));
$pdf->setFont('','');
$LeftOvers = $pdf->addTextWrap($Left_Margin+460,$YPos-10-$YPosx,400,$FontSize, $date);
$YPosz += 20;
}

$YPos +=$YPosz;
if($myrow['id']==2){
$LeftOvers = $pdf->addTextWrap($Left_Margin,70,500,12, _('Name of Filling Security Person:....................................................................................................'));
}
// Draws a box with round corners around line items:
$pdf->RoundRectangle(
	$Left_Margin-20,// RoundRectangle $XPos.
	$YPos+$FontSize+5,// RoundRectangle $YPos.
	$Page_Width-$Left_Margin-$Right_Margin+30,// RoundRectangle $Width.
	$YPos+$FontSize-$Bottom_Margin+5,// RoundRectangle $Height.
	10,// RoundRectangle $RadiusX.
	10);// RoundRectangle $RadiusY.

$pdf->OutputI($_SESSION['DatabaseName'] . '_Leave Report_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();

ob_end_flush();
?>
