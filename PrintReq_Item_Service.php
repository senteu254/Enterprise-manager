<?php

/* $Id: PrintCustTrans.php 7124 2015-02-05 02:12:56Z vvs2012 $ */

include('includes/session.inc');

$ViewTopic = 'ARReports';
$BookMark = 'PrintRequisition';

if (isset($_GET['id'])) {
$id = $_GET['id'];
}else{
prnMsg( _('There was a problem retrieving the Requisition details'),'error');
}

	include ('includes/class.pdf.php');

	/* This invoice is hard coded for A4 Landscape invoices or credit notes so can't use PDFStarter.inc */

	$Page_Width=842;
	$Page_Height=595;
	$Top_Margin=30;
	$Bottom_Margin=30;
	$Left_Margin=40;
	$Right_Margin=30;


	$pdf = new Cpdf('L', 'pt', 'A4');
	$pdf->addInfo('Creator', 'Berkley http://www.berkley.co.ke');
	$pdf->addInfo('Author', 'Kalfrique ' . $Version);

		if(isset($_SESSION['Doc_Store'])){
		$title ='Store Requisition and Issue Voucher';
		$subj ='Store Requisition and Issue Voucher';
		}else{
		$title ='Request For Purchase or Service';
		$subj ='Request For Purchase or Service';
		}
		$pdf->addInfo('Title',_($title));
		$pdf->addInfo('Subject',_($subj));
	

	$pdf->setAutoPageBreak(0);
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->AddPage();
	$pdf->cMargin = 0;
/* END Brought from class.pdf.php constructor */

	$FirstPage = true;
	$line_height=16;

	//Keep a record of the user's language
	$UserLanguage = $_SESSION['Language'];


	/* retrieve the invoice details from the database to print
	notice that salesorder record must be present to print the invoice purging of sales orders will
	nobble the invoice reprints */

			$sql="SELECT * FROM irq_request z 
							INNER JOIN irq_stockrequest a ON a.dispatchid = z.requestid
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_authorize_state b on a.dispatchid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN locations ON locations.loccode = a.loccode
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							WHERE a.dispatchid='" . $id . "'";

		$result=DB_query($sql, '',  '',false, false);

		if (DB_error_no()!=0) {
			$Title = _('Transaction Print Error Report');
			include ('includes/header.inc');
			prnMsg( _('There was a problem retrieving the Requisition details') . ' ' . _('from the database') . '. ','error');
			if ($debug==1) {
				prnMsg (_('The SQL used to get this information that failed was') . '<br />' . $sql,'error');
			}
			include ('includes/footer.inc');
			exit;
		}
		
			$myrow = DB_fetch_array($result);

			//Change the language to the customer's language			

			
			$sql = "SELECT stockmaster.stockid,
								stockmaster.longdescription,
								locstock.quantity,
								irq_stockrequestitems.quantity as qty,
								irq_stockrequestitems.uom,
								irq_stockrequestitems.qtydelivered,
								(stockmaster.materialcost * irq_stockrequestitems.quantity) AS fxnet,
								stockmaster.materialcost AS fxprice
							FROM stockmaster INNER JOIN irq_stockrequestitems
							ON irq_stockrequestitems.stockid = stockmaster.stockid
							INNER JOIN locstock
							ON locstock.stockid = stockmaster.stockid
							WHERE irq_stockrequestitems.cancelled !=1 AND irq_stockrequestitems.dispatchid='".$id."' AND locstock.loccode='". $myrow['loccode'] ."'";
			
			$result=DB_query($sql);
			if (DB_error_no()!=0 OR DB_num_rows($result)==0) {

				$Title = _('Transaction Print Error Report');
				$_SESSION['errmsg'] = '<ul class="states"><li class="error">' . _('<strong>Error:</strong> All Items Requested for Requisition No. '). $id . ' ' . _('has been Cancelled hence your print request cannot be completed.').'</li></ul>';
		header('location:index.php?Application=IRQ2');
				if ($debug==1) {
					echo '<br />' . _('The SQL used to get this information that failed was') . '<br />' . $sql;
				}
				
				exit;
			} else {

				$FontSize = 10;
				$PageNumber = 1;
				$YPos -=$line_height;

				include('includes/PDFRequestHeader.php');
				$FirstPage = False;
				$reason =1;
				while ($myrow2=DB_fetch_array($result)) {

					$DisplayNet=locale_number_format($myrow2['fxnet'],2);
					$DisplayPrice=locale_number_format($myrow2['fxprice'],2);
					$DisplayQty=locale_number_format($myrow2['qty'],2);
					$StockBalQty=locale_number_format($myrow2['quantity'],2);
					$Qtydelv=locale_number_format($myrow2['qtydelivered'],2);

					$LeftOvers = $pdf->addTextWrap($Left_Margin+3,$YPos,95,$FontSize,$myrow2['stockid']);
					//Get translation if it exists
					$LeftOvers = $pdf->addTextWrap($Left_Margin+100,$YPos,345,$FontSize,$myrow2['longdescription'],'left');
					$xx =0;
					while(mb_strlen($LeftOvers)>0){
					$YPos -=12;
					$xx +=12;
					$LeftOvers = $pdf->addTextWrap($Left_Margin+100,$YPos,345,$FontSize,$LeftOvers,'left');
					}
					$YPos += $xx;
					$line_height = $xx;
					$LeftOvers = $pdf->addTextWrap($Left_Margin+413,$YPos,96,$FontSize,$DisplayQty,'right');
					if($myrow['closed'] ==1){
					$LeftOvers = $pdf->addTextWrap($Left_Margin+483,$YPos,95,$FontSize,$Qtydelv,'center');
					}else{
					$LeftOvers = $pdf->addTextWrap($Left_Margin+483,$YPos,95,$FontSize,$StockBalQty,'right');
					}
					$LeftOvers = $pdf->addTextWrap($Left_Margin+583,$YPos,35,$FontSize,$myrow2['uom'],'center');
					if($reason == 1){
					$LeftOvers = $pdf->addTextWrap($Left_Margin+617,$YPos,85,$FontSize,$myrow['narrative'],'left');
					$y =0;
					while(mb_strlen($LeftOvers)>0){
					$YPos -=12;
					$y +=12;
					$LeftOvers = $pdf->addTextWrap($Left_Margin+617,$YPos,85,$FontSize,$LeftOvers,'left');
					}
					$YPos += $y;
					$reason =0;
					}
					$LeftOvers = $pdf->addTextWrap($Left_Margin+672,$YPos,98,$FontSize,$DisplayNet,'right');

					$YPos -= ($line_height+7);

					if ($YPos <= $Bottom_Margin) {
						/* head up a new invoice/credit note page */
						/*draw the vertical column lines right to the bottom */
						PrintLinesToBottom ();
						include ('includes/PDFRequestHeader.php');
					} //end if need a new page headed up

				} //end while there invoice are line items to print out
			} /*end if there are stock movements to show on the invoice or credit note*/

			$YPos -= $line_height;

			/* check to see enough space left to print the 4 lines for the totals/footer */
			if (($YPos-$Bottom_Margin)<(5*$line_height)) {
				PrintLinesToBottom ();
				include ('includes/PDFRequestHeader.php');
			}
			/* Print a column vertical line  with enough space for the footer */
			/* draw the vertical column lines to 4 lines shy of the bottom to leave space for invoice footer info ie totals etc */
			$pdf->line($Left_Margin+97, $TopOfColHeadings+12,$Left_Margin+97,$Bottom_Margin+(12*$line_height));

			/* Print a column vertical line */
			$pdf->line($Left_Margin+450, $TopOfColHeadings+12,$Left_Margin+450,$Bottom_Margin+(12*$line_height));
			
			/* Print a column vertical line */
			$pdf->line($Left_Margin+510, $TopOfColHeadings+12,$Left_Margin+510,$Bottom_Margin+(12*$line_height));

			/* Print a column vertical line */
			$pdf->line($Left_Margin+580, $TopOfColHeadings+12,$Left_Margin+580,$Bottom_Margin+(12*$line_height));

			/* Print a column vertical line */
			
			$pdf->line($Left_Margin+617, $TopOfColHeadings+12,$Left_Margin+617,$Bottom_Margin+(12*$line_height));
			
			/* Print a column vertical line */

			$pdf->line($Left_Margin+700, $TopOfColHeadings+12,$Left_Margin+700,$Bottom_Margin+(12*$line_height));

			/* Rule off at bottom of the vertical lines */
			$pdf->line($Left_Margin, $Bottom_Margin+(12*$line_height),$Page_Width-$Right_Margin,$Bottom_Margin+(12*$line_height));

			/* Now print out the footer and totals */


			/* Print out the invoice text entered */
			$YPos = $Bottom_Margin+(9*$line_height);

		//      $pdf->addText($Page_Width-$Right_Margin-392, $YPos - ($line_height*3)+22,$FontSize, _('Bank Code:***** Bank Account:*****'));
		//	$FontSize=10;

			$FontSize =8;
			
			if (mb_strlen($LeftOvers)>0) {
				$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-24,280,$FontSize,$LeftOvers);
				if (mb_strlen($LeftOvers)>0) {
					$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-36,280,$FontSize,$LeftOvers);
					/*If there is some of the InvText leftover after 3 lines 200 wide then it is not printed :( */
				}
			}
			$FontSize = 10;

		
$tit = array();
$comm = array();
			/*rule off for total */
			//$pdf->line($Page_Width-$Right_Margin-222, $YPos-(2*$line_height),$Page_Width-$Right_Margin,$YPos-(2*$line_height));

			/*vertical to separate totals from comments and ROMALPA */
			//$pdf->line($Page_Width-$Right_Margin-222, $YPos+$line_height,$Page_Width-$Right_Margin-222,$Bottom_Margin);
			$sql1 = "SELECT requisitionid, approver, approvaldate, approver_comment,Unread,Sent,level_id FROM irq_levels a
	 			INNER JOIN irq_approvers b ON a.approver_id=b.approver_id
				INNER JOIN irq_authorize_state c ON a.level_id  = c.level and requisitionid=(SELECT dispatchid FROM irq_stockrequestitems WHERE on_order='" . $id . "' LIMIT 1) AND autoid<>(select max(autoid) from irq_authorize_state vv where vv.requisitionid = c.requisitionid)
				WHERE a.doc_id=4";
				
			$sql2 = "SELECT requisitionid, approver, approvaldate, approver_comment,Unread,Sent,level_id FROM irq_levels a
	 			INNER JOIN irq_approvers b ON a.approver_id=b.approver_id
				INNER JOIN irq_authorize_state c ON a.level_id  = c.level and requisitionid='".$id."'
				WHERE a.doc_id=".$myrow['doc_id']."";
				
			$sql= $sql1." UNION ALL ".$sql2." ORDER BY approvaldate ASC";
			
			$titles1="SELECT approver_name,approvaldate FROM irq_levels a
	 			INNER JOIN irq_approvers b ON a.approver_id=b.approver_id
				INNER JOIN irq_authorize_state c ON a.level_id  = c.level and requisitionid=(SELECT dispatchid FROM irq_stockrequestitems WHERE on_order='" . $id . "' LIMIT 1) AND autoid<>(select max(autoid) from irq_authorize_state vv where vv.requisitionid = c.requisitionid)
				WHERE a.doc_id=4";
				
			$titles2="SELECT approver_name,approvaldate FROM irq_levels a
	 			INNER JOIN irq_approvers b ON a.approver_id=b.approver_id
				INNER JOIN irq_authorize_state c ON a.level_id  = c.level and requisitionid='".$id."'
				WHERE a.doc_id=".$myrow['doc_id']."";
			$titles= $titles1." UNION ALL ".$titles2." ORDER BY approvaldate ASC";	
			
			$DbgMsg = _('The SQL that was used to retrieve the information was');
			$ErrMsg = _('Could not check whether the level exists because');
			$results=DB_query($sql,$ErrMsg,$DbgMsg);
			$titleresults=DB_query($titles,$ErrMsg,$DbgMsg);
			$num=DB_num_rows($result);

while($comment=DB_fetch_array($results)){
$comm[] = $comment;
}
$tit[] ='Requisitioning Officer';
while($title=DB_fetch_array($titleresults)){
$tit[] = $title['approver_name'];
}

			$YPos+=26;
				$pdf->addText($Page_Width-$Right_Margin-220, $YPos - ($line_height*2)-10,$FontSize, ''); //total field
				$FontSize=9;
				$YPos-=4;
				$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,300,$FontSize,'APPROVERS');
				$pdf->line($Left_Margin, $Bottom_Margin+(10*$line_height)+8,$Page_Width-$Right_Margin,$Bottom_Margin+(10*$line_height)+8);
				$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos,300,$FontSize,'COMMENTS');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+675,$YPos,100,$FontSize,'DATE');
				
					//while ($row=DB_fetch_array($results)) {
					for($i=0; $i < count($comm) and $i < count($tit); $i++) {
					$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-12,250,$FontSize,$tit[$i].' ('.$comm[$i]['approver'].')');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+603,$YPos-12,150,$FontSize,ConvertSQLDateTime($comm[$i]['approvaldate']),'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos-12,400,$FontSize,$comm[$i]['approver_comment']);
					while (mb_strlen($LeftOvers)>0) {
					$YPos -= 10;
					$LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos-12,400,$FontSize,$LeftOvers);
					}
					
					//$YPos -= 10;
					}
					
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+180,$YPos,300,$FontSize,'Description :');
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+240,$YPos,300,$FontSize,$myrow['narrative']);
				//while (mb_strlen($LeftOvers)>0 AND $YPos > $Bottom_Margin) {
				//	$YPos-=12;
				//	$LeftOvers = $pdf->addTextWrap($Left_Margin+240,$YPos,300,$FontSize,$LeftOvers);
				//}
				/*print out bank details */

				$YPos-=12;
				if(isset($_SESSION['Doc_Store'])){
				$original ='Original : Stores';
				$copy ='Duplicate : User Department';
				}else{
				$original ='Original : Procurement';
				$copy ='Copy : To be Returned to Store';
				}
				$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$Bottom_Margin-10,240,$FontSize,' ' . _($original));
				$LeftOvers = $pdf->addTextWrap($Left_Margin+650,$Bottom_Margin-10,220,$FontSize,' ' . _($copy));

	if (isset($_GET['Email'])){ //email the invoice to address supplied

		include ('includes/htmlMimeMail.php');
		$FileName = $_SESSION['reports_dir'] . '/' . $_SESSION['DatabaseName'] . '_Requisition_'. $id .'.pdf';
		$pdf->Output($FileName,'F');
		$mail = new htmlMimeMail();

		$Attachment = $mail->getFile($FileName);
		$mail->setText(_('Dear '.$_GET['User'].', An internal stock request has been created and is waiting for your authoritation. Please Find the attached document for details.'));
		$mail->SetSubject('INTERNAL STOCK REQUEST NEED YOUR AUTHORITATION');
		$mail->addAttachment($Attachment, $FileName, 'application/pdf');
		if($_SESSION['SmtpSetting'] == 0){
			$mail->setFrom($_SESSION['CompanyRecord']['coyname'] . ' <' . $_SESSION['CompanyRecord']['email'] . '>');
			$result = $mail->send(array($_GET['Email']));
		}else{
			$result = SendmailBySmtp($mail,array($_GET['Email']));
		}

		unlink($FileName); //delete the temporary file

		$_SESSION['msg'] = '<ul class="states"><li class="succes">' . _('Success: Requisition No. '). $id . ' ' . _('has been forwarded to'). ' ' . $_GET['User'] . ' ' . _('and emailed to') . ' ' . $_GET['Email']. '</li></ul>';
		header('location:index.php?Application=IRQ2');

	} else { //its not an email just print the invoice to PDF
		$pdf->OutputI($_SESSION['DatabaseName'] . '_Requisition_'. $id .'.pdf');
		//$tempname = date(DATE_ATOM);
		//$tempname = str_replace(":", "_", $tempname);
		//$pdf->OutputF('C:/Invoices/'.$_SESSION['DatabaseName'] . '_' . $InvOrCredit . '_' . $FromTransNo . '_' . $tempname . '.pdf');

	}
	$pdf->__destruct();
	//Now change the language back to the user's language
	$_SESSION['Language'] = $UserLanguage;
	include('includes/LanguageSetup.php');


function PrintLinesToBottom () {

	global $pdf;
	global $PageNumber;
	global $TopOfColHeadings;
	global $Left_Margin;
	global $Bottom_Margin;
	global $line_height;
	
	
	$pdf->line($Left_Margin+97, $TopOfColHeadings+12,$Left_Margin+97,$Bottom_Margin);

			/* Print a column vertical line */
	$pdf->line($Left_Margin+450, $TopOfColHeadings+12,$Left_Margin+450,$Bottom_Margin);
			
			/* Print a column vertical line */
	$pdf->line($Left_Margin+510, $TopOfColHeadings+12,$Left_Margin+510,$Bottom_Margin);

			/* Print a column vertical line */
	$pdf->line($Left_Margin+580, $TopOfColHeadings+12,$Left_Margin+580,$Bottom_Margin);

			/* Print a column vertical line */
			
	$pdf->line($Left_Margin+617, $TopOfColHeadings+12,$Left_Margin+617,$Bottom_Margin);
			
	/* Print a column vertical line */

	$pdf->line($Left_Margin+700, $TopOfColHeadings+12,$Left_Margin+700,$Bottom_Margin);
	

	/* draw the vertical column lines right to the bottom */
	//$pdf->line($Left_Margin+97, $TopOfColHeadings+12,$Left_Margin+97,$Bottom_Margin);

	/* Print a column vertical line */
	//$pdf->line($Left_Margin+350, $TopOfColHeadings+12,$Left_Margin+450,$Bottom_Margin);

	/* Print a column vertical line */
	//$pdf->line($Left_Margin+450, $TopOfColHeadings+12,$Left_Margin+510,$Bottom_Margin);

	/* Print a column vertical line */
	//$pdf->line($Left_Margin+550, $TopOfColHeadings+12,$Left_Margin+580,$Bottom_Margin);

	/* Print a column vertical line */
	//$pdf->line($Left_Margin+587, $TopOfColHeadings+12,$Left_Margin+617,$Bottom_Margin);

	//$pdf->line($Left_Margin+670, $TopOfColHeadings+12,$Left_Margin+700,$Bottom_Margin);
	

	$PageNumber++;

}

?>

