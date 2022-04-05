<?php
/*	$Id: PDFQuotationPortrait.php 4491 2011-02-15 06:31:08Z daintree $ */

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/
ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

//Get Out if we have no order number to work with
If (!isset($_GET['id']) || $_GET['id']==""){
        $Title = _('Select Maintenance Request To Print');
        include('includes/header.inc');
        echo '<div class="centre">
				<br />
				<br />
				<br />';
        prnMsg( _('Select a Maintenance Request to Print before calling this page') , 'error');
        echo '<br />
				<br />
				<br />
				<table class="table_index">
				<tr>
					<td class="menu_group_item">
						<ul><li><a href="'. $RootPath . '/IRQ_MaintenanceRequest.php?Ref=Completed">' . _('Maintenance Request') . '</a></li>
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
$ErrMsg = _('There was a problem retrieving the Maintenance Request header details for Request Number') . ' ' . $_GET['id'] . ' ' . _('from the database');

$sql = "SELECT * FROM irq_request z 
							INNER JOIN irq_maintenance a on a.maintenanceid = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							WHERE z.requestid='" . $_GET['id'] . "'  AND userid='".$_SESSION['UserID']."' ORDER BY Requesteddate DESC";

$result=DB_query($sql, $ErrMsg);

//If there are no rows, there's a problem.
if (DB_num_rows($result)==0){
	$Title = _('Print Maintenance Request Error');
	include('includes/header.inc');
	 echo '<div class="centre">
			<br />
			<br />
			<br />';
	prnMsg( _('Unable to Locate Maintenance Request Number') . ' : ' . $_GET['id'] . ' ', 'error');
	echo '<br />
			<br />
			<br />
			<table class="table_index">
			<tr>
				<td class="menu_group_item">
					<ul><li><a href="'. $RootPath . '/IRQ_MaintenanceRequest.php?Ref=Completed">' . _('Outstanding Maintenance Request') . '</a></li></ul>
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
$pdf->addInfo('Title', _('Maintenance Request') );
$pdf->addInfo('Subject', _('Maintenance Request') . ' ' . $_GET['id']);
$FontSize = 12;
$line_height = 12;// Recommended: $line_height = $x * $FontSize.

	/*Yes there are line items to start the ball rolling with a page header */
	include('includes/PDFMaintenanceHeader.php');

		$FontSize = 10;// Font size for the line item.

		//$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize, _('Job Card No.'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize, _('Department'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-20,100,$FontSize, _('Section'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-40,100,$FontSize, _('Machine Type'));
		$LeftOvers = $pdf->addTextWrap(350,$YPos-40,100,$FontSize, _('Machine No. :'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-60,100,$FontSize, $date);
		$LeftOvers = $pdf->addTextWrap(350,$YPos-60,100,$FontSize, _('Time :'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-80,100,$FontSize, _('Urgency'));
		
		//$LeftOvers = $pdf->addTextWrap(180,$YPos,300,$FontSize, $myrow['cardno']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos,400,$FontSize, $myrow['description']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-20,300,$FontSize, $myrow['section']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-40,300,$FontSize, $myrow['mctype']);
		$LeftOvers = $pdf->addTextWrap(420,$YPos-40,300,$FontSize, $myrow['mcno']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-60,100,$FontSize, date("d, M Y",strtotime($myrow['breakdowndate'])));
		$LeftOvers = $pdf->addTextWrap(420,$YPos-60,100,$FontSize, date("h:i A",strtotime($myrow['breakdowndate'])));
		$LeftOvers = $pdf->addTextWrap(180,$YPos-80,200,$FontSize, $myrow['urgency']);
		$YPos = $YPos-90;
	$pdf->addText($XPos, $YPos, $FontSize, $message.':');
	$Width2 = $Page_Width-$Right_Margin-200;// Width to print salesorders.comments.
	$LeftOver = trim($myrow['problem']);
	while(mb_strlen($LeftOver) > 1) {
		$YPos -= $FontSize;
		if ($YPos < ($Bottom_Margin)) {// Begins new page.
			include ('includes/PDFQuotationPageHeader.inc');
		}
		$LeftOver = $pdf->addTextWrap(180, $YPos, $Width2, $FontSize, $LeftOver);
	}
	
	$sqls = "SELECT * FROM irq_authorize_state b
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							WHERE b.requisitionid='" . $_GET['id'] . "'   GROUP BY b.level";
	$result=DB_query($sqls, $ErrMsg);
	
	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-20,100,$FontSize, _('AUTHORITY'));
	$x=20;
	$YPos=$YPos-20;
	while($myrow1 = DB_fetch_array($result)){	
	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-$x,100,$FontSize, $myrow1['approver_name']);
	$LeftOvers = $pdf->addTextWrap(130,$YPos-$x,100,$FontSize, 'Approved');
	$x +=20;
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Prints company logo:
$XPos = $Page_Width/2 - 140;
$YPos=330;
$pdf->addTextWrap(0, $YPos+80, $Page_Width, 18,'--------------------------------------------------------------------------------------------------', 'center');
$pdf->addJpegFromFile($_SESSION['LogoFile'],$XPos+70,$YPos-20,0,60);

// Prints 'Quotation' title:

$pdf->addTextWrap(0, $YPos+60, $Page_Width, 18,$tt, 'center');
$pdf->addTextWrap(0, $YPos+60, $Page_Width-10, 18,'(Copy)', 'right');

$YPos +=40;
// Draws a box with round corners around 'Delivery To' info:
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $YPos-$FontSize*1, 200, $FontSize, $_SESSION['CompanyRecord']['coyname'], 'left');
$FontSize=10;
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $YPos-$FontSize*2, 200, $FontSize, $_SESSION['CompanyRecord']['regoffice1'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $YPos-$FontSize*3, 200, $FontSize,  _('Ph') . ': ' . $_SESSION['CompanyRecord']['telephone'] .' ' . _('Fax'). ': ' . $_SESSION['CompanyRecord']['fax'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $YPos-$FontSize*4, 200, $FontSize, $_SESSION['CompanyRecord']['email'], 'left');

// Prints quotation info:
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $YPos-$FontSize*1, 200, $FontSize,  _('Printed On'). ': '.date("d, M Y"), 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $YPos-$FontSize*2, 200, $FontSize, _('Ref No.'). ': '.$_GET['id'], 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $YPos-$FontSize*3, 200, $FontSize, _('Page').': '.$PageNumber, 'right');

$FontSize=10;

// Prints table header:
$YPos -= 100;
$XPos = 40;

// Draws a box with round corners around line items:
$pdf->RoundRectangle(
	$Left_Margin,// RoundRectangle $XPos.
	$YPos+$FontSize+5,// RoundRectangle $YPos.
	$Page_Width-$Left_Margin-$Right_Margin,// RoundRectangle $Width.
	$YPos+$FontSize-$Bottom_Margin+10,// RoundRectangle $Height.
	10,// RoundRectangle $RadiusX.
	10);// RoundRectangle $RadiusY.

$YPos -= $FontSize;// This is to use addTextWrap's $YPos instead of normal $YPos.

		$FontSize = 10;// Font size for the line item.

		//$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize, _('Job Card No.'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize, _('Department'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-20,100,$FontSize, _('Section'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-40,100,$FontSize, _('Machine Type'));
		$LeftOvers = $pdf->addTextWrap(350,$YPos-40,100,$FontSize, _('Machine No. :'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-60,100,$FontSize, $date);
		$LeftOvers = $pdf->addTextWrap(350,$YPos-60,100,$FontSize, _('Time :'));
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-80,100,$FontSize, _('Urgency'));
		
		//$LeftOvers = $pdf->addTextWrap(180,$YPos,300,$FontSize, $myrow['cardno']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos,400,$FontSize, $myrow['description']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-20,300,$FontSize, $myrow['section']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-40,300,$FontSize, $myrow['mctype']);
		$LeftOvers = $pdf->addTextWrap(420,$YPos-40,300,$FontSize, $myrow['mcno']);
		$LeftOvers = $pdf->addTextWrap(180,$YPos-60,100,$FontSize, date("d, M Y",strtotime($myrow['breakdowndate'])));
		$LeftOvers = $pdf->addTextWrap(420,$YPos-60,100,$FontSize, date("h:i A",strtotime($myrow['breakdowndate'])));
		$LeftOvers = $pdf->addTextWrap(180,$YPos-80,200,$FontSize, $myrow['urgency']);
		$YPos = $YPos-90;
	$pdf->addText($XPos, $YPos, $FontSize, $message.':');
	$Width2 = $Page_Width-$Right_Margin-200;// Width to print salesorders.comments.
	$LeftOver = trim($myrow['problem']);
	while(mb_strlen($LeftOver) > 1) {
		$YPos -= $FontSize;
		if ($YPos < ($Bottom_Margin)) {// Begins new page.
			include ('includes/PDFQuotationPageHeader.inc');
		}
		$LeftOver = $pdf->addTextWrap(180, $YPos, $Width2, $FontSize, $LeftOver);
	}
	
	$sqls = "SELECT * FROM irq_authorize_state b
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							WHERE b.requisitionid='" . $_GET['id'] . "'   GROUP BY b.level";
	$result=DB_query($sqls, $ErrMsg);
	
	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-20,100,$FontSize, _('AUTHORITY'));
	$x=20;
	$YPos=$YPos-20;
	while($myrow1 = DB_fetch_array($result)){	
	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-$x,100,$FontSize, $myrow1['approver_name']);
	$LeftOvers = $pdf->addTextWrap(130,$YPos-$x,100,$FontSize, 'Approved');
	$x +=20;
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_GET['Email'])){ //email the invoice to address supplied

		include ('includes/htmlMimeMail.php');
		$FileName = $_SESSION['reports_dir'] . '/' . $_SESSION['DatabaseName'] . '_Requisition_'. $id .'.pdf';
		$pdf->Output($FileName,'F');
		$mail = new htmlMimeMail();

		$Attachment = $mail->getFile($FileName);
		$mail->setText(_('Dear '.$_GET['User'].', A Maintenance request has been created and is waiting for your authoritation. Please Find the attached document for details.'));
		$mail->SetSubject('MAINNTENANCE REQUISITION NEED YOUR AUTHORITATION');
		$mail->addAttachment($Attachment, $FileName, 'application/pdf');
		if($_SESSION['SmtpSetting'] == 0){
			$mail->setFrom($_SESSION['CompanyRecord']['coyname'] . ' <' . $_SESSION['CompanyRecord']['email'] . '>');
			$result = $mail->send(array($_GET['Email']));
		}else{
			$result = SendmailBySmtp($mail,array($_GET['Email']));
		}

		unlink($FileName); //delete the temporary file

		$_SESSION['msg'] = '<ul class="states"><li class="succes">' . _('Success: Requisition No. '). $id . ' ' . _('has been forwarded to'). ' ' . $_GET['User'] . ' ' . _('and emailed to') . ' ' . $_GET['Email']. '</li></ul>';
		header('location:IRQ_MaintenanceRequest.php?Ref='.$_GET['Ref']);

	} else { //its not an email just print the invoice to PDF
    $pdf->OutputI($_SESSION['DatabaseName'] . '_Maintenance_' . $_GET['id'] . '_' . date('Y-m-d') . '.pdf');
	}
    $pdf->__destruct();

ob_end_flush();
?>
