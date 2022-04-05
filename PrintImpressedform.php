<?php

/* $Id: PrintCustOrder_generic.php 7093 2015-01-22 20:15:40Z vvs2012 $*/


include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
//Get Out if we have no order number to work with
$PaperSize = 'A4';
include('includes/PDFStarter.php');
//$pdf->selectFont('./fonts/Helvetica.afm');
$pdf->addInfo('Title', _('Request for Petty Cash') );
$pdf->addInfo('Subject', _('Impressed Form') . ' ' . $_GET['voucherid']);
$FontSize=12;
$line_height=20;
$PageNumber = 1;

$ListCount = 0;
$Count = 0;
$FullPage = 0;
$SelectedUser=$_GET['voucherid'];
	$ErrMsg = _('There was a problem retrieving the picking details for Delivery Number') . ' ' . $_GET['voucherid'] . ' ' . _('from the database');

	$sql= "SELECT a.voucherid,
	                a.serialNo,
					a.datereq,
					a.label,
					a.payeename,
					a.particulars,
					a.amount,
					a.process_level,
					a.total,
					a.documents,
					a.department,
					a.shortdescription,
					a.reason,
					b.departmentid,
					b.description,
					c.voucherno,
					c.approver,
					c.approvaldate,
					c.recommendation
				FROM impressed a
				INNER JOIN departments b ON a.department=b.departmentid 
				INNER JOIN impressed_approval c ON a.voucherid=c.voucherno
				WHERE a.process_level>=7
				AND a.voucherid=".$SelectedUser."";
	$result=DB_query($sql, $ErrMsg);
	
	if (DB_num_rows($result)>0){
		/*Yes there are line items to start the ball rolling with a page header */
	
		$XPos += 20;
		
		while ($myrow2=DB_fetch_array($result)){
		//$i++;
			include('includes/PDFPageHeaderImpressed.inc');
			
		$pat  = unserialize($myrow2['particulars']);
	    $amount = unserialize($myrow2['amount']);
         
          $ListCount ++;
			$Count ++;

			$DisplayQty = locale_number_format($myrow2['qtydelivered'],$myrow2['decimalplaces']);
          
			 
			//$pdf->line($XPos+480, $YPos+13,$XPos-10, $YPos+13);
	for($i = 0; $i < count($pat); $i++){
	$particulars = $pat[$i];
	$amnt = $amount[$i];
	 $a = $i+1;
	 
	$pdf->line($XPos+405, $YPos+50,$XPos+405, $YPos-2);//from top to bottom  letf
	$pdf->line($XPos+35, $YPos+50,$XPos+35, $YPos-2);//from top to bottom right letf
   $pdf->line($XPos+480, $YPos+13,$XPos-10, $YPos+13);
	$YPos -= 15;
	 $i>0;
	//$total+=$amnt;
	        $LeftOvers = $pdf->addTextWrap($XPos,$YPos+30,87,$FontSize,$a);
			$LeftOvers = $pdf->addTextWrap(100,$YPos+30,195,$FontSize,$particulars);
			$LeftOvers = $pdf->addTextWrap(480,$YPos+30,50,$FontSize,locale_number_format($amnt,2),'right');
			
	}
	$total+=
	$pdf->addText($XPos+55,$YPos+28,$FontSize, _('TOTAL') . ' ' );
	$pdf->addTextWrap($XPos+410,$YPos+15,200,$FontSize,$myrow2['total']);
	//$pdf->addText($XPos+420,$YPos+28,$FontSize, locale_number_format($myrow2['total'],2) . ' ' );
	  $pdf->line($XPos+480, $YPos+13,$XPos-10, $YPos+13);
			//$LeftOvers = $pdf->addTextWrap(330,$YPos,60,$FontSize,$DisplayQty,'right');
			$XPos = 15;
			if ($YPos-$line_height <= 50){
			/* We reached the end of the page so finsih off the page and start a newy */
				$PageNumber++;
				$Count = 0;
				$FullPage=1;
				include ('includes/PDFPageHeaderImpressed.inc');
			} //end if need a new page headed up
			else {
				/*increment a line down for the next line item */
				$YPos -= ($line_height);
			}
	/*user*/
	$pdf->SetFont('Times','B');
$pdf->addText($XPos+35,$YPos+25,$FontSize, _('Reason') . ': '.$myrow2['reason'].'' );
$sqlU= "SELECT a.voucherid,
                    b.realname,
					c.voucherno,
					c.approver,
					c.process_level,
					c.approvaldate,
					c.recommendation
				FROM impressed a
				INNER JOIN impressed_approval c ON a.voucherid=c.voucherno
				INNER JOIN www_users b ON c.approver=b.userid
				WHERE c.process_level=1
				AND a.voucherid=".$SelectedUser."";
	$resultU=DB_query($sqlU, $ErrMsg);
	
	$pdf->addText($XPos+35,$YPos-8,$FontSize, _('1) User Name') . ' ............................................................................Date......................................................' );
	while ($myrowU=DB_fetch_array($resultU)){
	$pdf->addText($XPos+110,$YPos-5,$FontSize, $myrowU['realname']);
$pdf->addText($XPos+395,$YPos-5,$FontSize, ConvertSQLDate($myrowU['approvaldate']));
//$pdf->addText($XPos+35,$YPos-30,$FontSize, _('1) User Name') . '............... ' . $myrowU['approver'] .  '.................................................Date................' .ConvertSQLDate($myrowU['approvaldate']). '....................' );
}
//$pdf->line($XPos+525, $YPos-40,$XPos+35, $YPos-40);
/*Head of Department*/
$pdf->SetFont('Times','BU');
$pdf->addText($XPos+35,$YPos-25,$FontSize, _('2) Requisitioning Department Comments(HOD)') . ' ' );
$pdf->SetFont('Times','B');
$pdf->addText($XPos+35,$YPos-45,$FontSize, _('Comments') . ' ...............................................................................................................................................' );
################################################################################################################################################################
$sql9= "SELECT a.voucherid,
                    b.realname,
					c.voucherno,
					c.approver,
					c.process_level,
					c.approvaldate,
					c.recommendation
				FROM impressed a
				INNER JOIN impressed_approval c ON a.voucherid=c.voucherno
				INNER JOIN www_users b ON c.approver=b.userid
				WHERE c.process_level=2
				AND a.voucherid=".$SelectedUser."";
	$result9=DB_query($sql9, $ErrMsg);
###############################################################################################################################################################

$pdf->addText($XPos+35,$YPos-63,$FontSize, _('Name') . ' ......................................................................................Date.........................................................' );
while ($myrow9=DB_fetch_array($result9)){
$pdf->addText($XPos+105,$YPos-43,$FontSize, $myrow9['recommendation']);
$pdf->addText($XPos+105,$YPos-60,$FontSize, $myrow9['realname']);
$pdf->addText($XPos+395,$YPos-60,$FontSize, ConvertSQLDate($myrow9['approvaldate']));
}
//$pdf->line($XPos+525, $YPos-85,$XPos+35, $YPos-85);
/*Controlled department*/
$pdf->SetFont('Times','BU');
$pdf->addText($XPos+35,$YPos-80,$FontSize, _('3) Recommendation by Controlling  Department(HRM/SM)') . ' ' );
$pdf->SetFont('Times','B');
$pdf->addText($XPos+35,$YPos-95,$FontSize, _('Comments if item is available in stock/On Order/Transport Available') . ' ..........................................' );
$pdf->addText($XPos+35,$YPos-118,$FontSize,   _('') . ' .................................................................................................................................................................' );
$sql3= "SELECT a.voucherid,
                    b.realname,
					c.voucherno,
					c.approver,
					c.process_level,
					c.approvaldate,
					c.recommendation
				FROM impressed a
				INNER JOIN impressed_approval c ON a.voucherid=c.voucherno
				INNER JOIN  www_users b ON c.approver=b.userid
				WHERE c.process_level=3
				AND a.voucherid=".$SelectedUser."";
	$result3=DB_query($sql3, $ErrMsg);
###############################################################################################################################################################

$pdf->addText($XPos+35,$YPos-138,$FontSize,  _('Name') . ' ......................................................................................Date.........................................................' );
while ($myrow3=DB_fetch_array($result3)){
$pdf->addText($XPos+105,$YPos-113,$FontSize, $myrow3['recommendation']);
$pdf->addText($XPos+105,$YPos-133,$FontSize, $myrow3['realname']);
$pdf->addText($XPos+395,$YPos-133,$FontSize, ConvertSQLDate($myrow3['approvaldate']));
}
/*procurement manager*/
$pdf->SetFont('Times','BU');
$pdf->addText($XPos+35,$YPos-153,$FontSize, _('4) Recommendation By Procurement (HOD)') . ' ' );
$pdf->SetFont('Times','B');
$sql4= "SELECT a.voucherid,
                    b.realname,
					c.voucherno,
					c.approver,
					c.process_level,
					c.approvaldate,
					c.recommendation
				FROM impressed a
				INNER JOIN impressed_approval c ON a.voucherid=c.voucherno
				INNER JOIN  www_users b ON c.approver=b.userid
				WHERE c.process_level=4
				AND a.voucherid=".$SelectedUser."";
	$result4=DB_query($sql4, $ErrMsg);
###############################################################################################################################################################

$pdf->addText($XPos+35,$YPos-168,$FontSize, _('Comments') . ' ..............................................................................................................................................' );

$pdf->addText($XPos+35,$YPos-186,$FontSize, _('Name') . ' ......................................................................................Date.........................................................' );
while ($myrow4=DB_fetch_array($result4)){
$pdf->addText($XPos+105,$YPos-166,$FontSize, $myrow4['recommendation']);
$pdf->addText($XPos+105,$YPos-183,$FontSize, $myrow4['realname']);
$pdf->addText($XPos+395,$YPos-183,$FontSize, ConvertSQLDate($myrow4['approvaldate']));
}
//$pdf->line($XPos+525, $YPos-170,$XPos+35, $YPos-170);
/*AIE Holder*/
$pdf->SetFont('Times','BU');
$pdf->addText($XPos+35,$YPos-208,$FontSize, _('5) Approval(GM/MD)') . ' ' );
$pdf->SetFont('Times','B');
$sql5= "SELECT a.voucherid,
                    b.realname,
					c.voucherno,
					c.approver,
					c.process_level,
					c.approvaldate,
					c.recommendation
				FROM impressed a
				INNER JOIN impressed_approval c ON a.voucherid=c.voucherno
				INNER JOIN  www_users b ON c.approver=b.userid
				WHERE c.process_level=5
				AND a.voucherid=".$SelectedUser."";
	$result5=DB_query($sql5, $ErrMsg);
$pdf->addText($XPos+35,$YPos-223,$FontSize, _('Comments') . ' ..............................................................................................................................................' );

$pdf->addText($XPos+35,$YPos-241,$FontSize, _('Name') . ' ......................................................................................Date.........................................................' );
while ($myrow5=DB_fetch_array($result5)){
$pdf->addText($XPos+105,$YPos-221,$FontSize, $myrow5['recommendation']);
$pdf->addText($XPos+105,$YPos-238,$FontSize, $myrow5['realname']);
$pdf->addText($XPos+395,$YPos-238,$FontSize, ConvertSQLDate($myrow5['approvaldate']));
}
//$pdf->line($XPos+525, $YPos-210,$XPos+35, $YPos-210);
/*finance manager*/
$pdf->SetFont('Times','BU');
$pdf->addText($XPos+35,$YPos-260,$FontSize, _('6)Action By Finance Department (CA/PM)') . ' ' );
$pdf->SetFont('Times','B');
$sql6= "SELECT a.voucherid,
                    b.realname,
					c.voucherno,
					c.approver,
					c.process_level,
					c.approvaldate,
					c.recommendation
				FROM impressed a
				INNER JOIN impressed_approval c ON a.voucherid=c.voucherno
				INNER JOIN  www_users b ON c.approver=b.userid
				WHERE c.process_level=6
				AND a.voucherid=".$SelectedUser."";
	$result6=DB_query($sql6, $ErrMsg);
$pdf->addText($XPos+35,$YPos-279,$FontSize, _('Comments') . ' ..............................................................................................................................................' );

$pdf->addText($XPos+35,$YPos-297,$FontSize, _('Name') . ' ......................................................................................Date.........................................................' );
while ($myrow6=DB_fetch_array($result6)){
$pdf->addText($XPos+105,$YPos-277,$FontSize, $myrow6['recommendation']);
$pdf->addText($XPos+105,$YPos-294,$FontSize, $myrow6['realname']);
$pdf->addText($XPos+395,$YPos-294,$FontSize, ConvertSQLDate($myrow6['approvaldate']));
}
//$pdf->line($XPos+525, $YPos-250,$XPos+35, $YPos-250);
/*Paying Officer/Cashier */
$pdf->SetFont('Times','BU');
$pdf->addText($XPos+35,$YPos-315,$FontSize, _('7. Paying Officer/Cashier') . '' );
$pdf->SetFont('Times','B');
$sql7= "SELECT a.voucherid,
                    b.realname,
					c.voucherno,
					c.approver,
					c.process_level,
					c.approvaldate,
					c.recommendation
				FROM impressed a
				INNER JOIN impressed_approval c ON a.voucherid=c.voucherno
				INNER JOIN www_users b ON c.approver=b.userid
				WHERE c.process_level=7
				AND a.voucherid=".$SelectedUser."";
	$result7=DB_query($sql7, $ErrMsg);
	
$pdf->addText($XPos+35,$YPos-335,$FontSize, _('Name') . ' ......................................................................................Date.........................................................' );
while ($myrow7=DB_fetch_array($result7)){
$pdf->addText($XPos+105,$YPos-333,$FontSize, $myrow7['realname']);
$pdf->addText($XPos+395,$YPos-333,$FontSize, ConvertSQLDate($myrow7['approvaldate']));
}
$pdf->SetFont('Times','BI');
$pdf->addText($XPos+35,$YPos-353,$FontSize, _('Note: All funds must be accounted for within 48 hours of receipt surrendering of funds to be') . '' );
 $pdf->addText($XPos+65,$YPos-365,$FontSize, _('accompanied with receipt voucher (KOFC 52010202)') . '' );
//$pdf->line($XPos+525, $YPos-300,$XPos+35, $YPos-300);
}
}
	/*----------------------------------------------------------------------------------------------------------------------------*/
	$YP =715;
	$FontSize =10;
	
	
	$FontSize =11;
	

 /*end for loop to print the whole lot twice */

if ($ListCount == 0) {
	$Title = _('Print Picking Slip Error');
	include('includes/header.inc');
	
	include('includes/footer.inc');
	exit;
} else {
    	$pdf->OutputI($_SESSION['DatabaseName'] . 'ImpressedForm' . date('Y-m-d') . '.pdf');
    	$pdf->__destruct();
	
}

?>