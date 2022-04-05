<?php
/*	$Id: PDFQuotationPortrait.php 4491 2011-02-15 06:31:08Z daintree $ */

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/
ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

//Get Out if we have no order number to work with
If (!isset($_GET['id']) || $_GET['id']==""){
        $Title = _('Select Gatepass To Print');
        include('includes/header.inc');
        echo '<div class="centre">
				<br />
				<br />
				<br />';
        prnMsg( _('Select a Gatepass to Print before calling this page') , 'error');
        echo '<br />
				<br />
				<br />
				<table class="table_index">
				<tr>
					<td class="menu_group_item">
						<ul><li><a href="'. $RootPath . '/IRQ_GatepassRequest.php?Ref=Completed">' . _('Gatepass') . '</a></li>
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
$ErrMsg = _('There was a problem retrieving the Gatepass header details for Request Number') . ' ' . $_GET['id'] . ' ' . _('from the database');

$sql = "SELECT * FROM irq_request z 
							INNER JOIN irq_gatepass a on a.gatepassid = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							WHERE z.requestid='" . $_GET['id'] . "'  ORDER BY Requesteddate DESC";

$result=DB_query($sql, $ErrMsg);

//If there are no rows, there's a problem.
if (DB_num_rows($result)==0){
	$Title = _('Print Gatepass Error');
	include('includes/header.inc');
	 echo '<div class="centre">
			<br />
			<br />
			<br />';
	prnMsg( _('Unable to Locate Gatepass Number') . ' : ' . $_GET['id'] . ' ', 'error');
	echo '<br />
			<br />
			<br />
			<table class="table_index">
			<tr>
				<td class="menu_group_item">
					<ul><li><a href="'. $RootPath . '/IRQ_GatepassRequest.php?Ref=Completed">' . _('Outstanding Gatepass') . '</a></li></ul>
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
$PaperSize = 'A4_Landscape';
include('includes/PDFStarter.php');
/*$PageNumber = 1;// RChacon: PDFStarter.php sets $PageNumber = 0.*/
$pdf->addInfo('Title', _('Gatepass Request') );
$pdf->addInfo('Subject', _('Gatepass') . ' ' . $_GET['id']);
$line_height = 12;// Recommended: $line_height = $x * $FontSize.

/* Now ... Has the order got any line items still outstanding to be invoiced */

$ErrMsg = _('There was a problem retrieving the gatepass line details for gatepass Number') . ' ' .
	$_GET['id'] . ' ' . _('from the database');

$sql = "SELECT * FROM irq_gatepass_items
							WHERE gatepassid='" . $_GET['id'] . "'";

$result=DB_query($sql, $ErrMsg);

$ListCount = 0;

if (DB_num_rows($result)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('includes/PDFGatepassHeader.php');
$YPos -= $line_height;
	while ($myrow2=DB_fetch_array($result)){

        $ListCount ++;
		$FontSize = 10;// Font size for the line item.

		$LeftOvers = $pdf->addText($Left_Margin, $YPos+$FontSize, $FontSize, $myrow2['id']);
		$LeftOvers = $pdf->addText(70, $YPos+$FontSize, $FontSize, $myrow2['qty']);
		$LeftOvers = $pdf->addText(120, $YPos+$FontSize,$FontSize, $myrow2['description']);
		
		$LeftOvers = $pdf->addText($XPos, $YPos+$FontSize, $FontSize, $myrow2['id']);
		$LeftOvers = $pdf->addText($XPos+30, $YPos+$FontSize, $FontSize, $myrow2['qty']);
		$LeftOvers = $pdf->addText($XPos+80, $YPos+$FontSize, $FontSize, $myrow2['description']);
		
	if ($ListCount >= 11){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('includes/PDFGatepassHeader.php');
		$ListCount = 0;
	} //end if need a new page headed up
	$YPos -= $line_height;// Increment a line down for the next line item.
	}// Ends while there are line items to print out.

} /*end if there are line details to show on the quotation*/


if (isset($_GET['Email'])){ //email the invoice to address supplie

		include ('includes/htmlMimeMail.php');
		$FileName = $_SESSION['reports_dir'] . '/' . $_SESSION['DatabaseName'] . '_Requisition_'. $id .'.pdf';
		$pdf->Output($FileName,'F');
		$mail = new htmlMimeMail();

		$Attachment = $mail->getFile($FileName);
		$mail->setText(_('Dear '.$_GET['User'].', A Gatepass request has been created and is waiting for your authoritation. Please Find the attached document for details.'));
		$mail->SetSubject('GATEPASS REQUISITION NEED YOUR AUTHORITATION');
		$mail->addAttachment($Attachment, $FileName, 'application/pdf');
		if($_SESSION['SmtpSetting'] == 0){
			$mail->setFrom($_SESSION['CompanyRecord']['coyname'] . ' <' . $_SESSION['CompanyRecord']['email'] . '>');
			$result = $mail->send(array($_GET['Email']));
		}else{
			$result = SendmailBySmtp($mail,array($_GET['Email']));
		}

		unlink($FileName); //delete the temporary file

		$_SESSION['msg'] = '<ul class="states"><li class="succes">' . _('Success: Requisition No. '). $id . ' ' . _('has been forwarded to'). ' ' . $_GET['User'] . ' ' . _('and emailed to') . ' ' . $_GET['Email']. '</li></ul>';
		header('location:IRQ_GatepassRequest.php?Ref='.$_GET['Ref']);

	}

if ($ListCount == 0){
        $Title = _('Print Gatepass Error');
        include('includes/header.inc');
        echo '<p>' .  _('There were no items on the Gatepass') . '. ' . _('The Gatepass cannot be printed').
                '<br /><a href="' . $RootPath . '/IRQ_GatepassRequest.php?Ref=Completed">' .  _('Print Another Gatepass').
                '</a>' . '<br />' .  '<a href="' . $RootPath . '/index.php">' . _('Back to the menu') . '</a>';
        include('includes/footer.inc');
	exit;
} else {

    $pdf->OutputI($_SESSION['DatabaseName'] . '_Gatepass_' . $_GET['id'] . '_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
}
ob_end_flush();
?>
