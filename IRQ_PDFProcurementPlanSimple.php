<?php

/* $Id: PrintCustTrans.php 7124 2015-02-05 02:12:56Z vvs2012 $ */

include('includes/session.inc');
$Title = _('Procurement Plan Report');
$ViewTopic = 'ARReports';
$BookMark = 'PrintProcurementPlan';

if (isset($_POST['SubmitRequest'])) {

	$sql="SELECT planid FROM irq_procurementplan
							WHERE departmentid='" . $_POST['dept'] . "' AND year='".$_POST['year']."'";
	$r=DB_query($sql, '',  '',false, false);
	$nums= DB_num_rows($r);
	if($nums >0){
	$w = DB_fetch_array($r);
	$id = $w['planid'];
	}else{
	include('includes/header.inc');
	prnMsg( _('There is no Procurement Plan for the Selected Department for the Financial Year '.$_POST['year'].'.'), 'error');
	echo '<br /><div class="centre"><a href="'. htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">' . _('Go Back') . '</a></div>';
	include('includes/footer.inc');
	exit;
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

		
		$title ='Procurement Plan';
		$subj ='Procurement Plan';
		
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
							INNER JOIN irq_procurementplan a ON a.planid = z.requestid
							INNER JOIN departments b ON a.departmentid = b.departmentid
							INNER JOIN irq_documents c ON z.doc_id = c.doc_id
							WHERE z.requestid='" . $id . "'";

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
								stockmaster.description,
								irq_procurementplanitems.quantity+irq_procurementplanitems.quantity2+irq_procurementplanitems.quantity3 as qty,
								irq_procurementplanitems.uom,
								stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost AS cost
							FROM stockmaster INNER JOIN irq_procurementplanitems
							ON irq_procurementplanitems.stockid = stockmaster.stockid
							WHERE irq_procurementplanitems.planid='".$id."'";
			
			$result=DB_query($sql);
			if (DB_error_no()!=0 OR DB_num_rows($result)==0) {

				$Title = _('Transaction Print Error Report');
				include ('includes/header.inc');
				echo '<br />' . _('There was a problem retrieving the Procurement plan details for Request number') . ' ' . $id . ' ' . _('from the database');
				if ($debug==1) {
					echo '<br />' . _('The SQL used to get this information that failed was') . '<br />' . $sql;
				}
				include('includes/footer.inc');
				exit;
			} else {

				$FontSize = 10;
				$PageNumber = 1;

				include('includes/PDFProcurementPlanHeaderSimple.php');
				$FirstPage = False;
				while ($myrow2=DB_fetch_array($result)) {

					
					$DisplayPrice=locale_number_format($myrow2['cost'],2);
					$DisplayQty=locale_number_format($myrow2['qty'],0);
					$DisplayTot=locale_number_format($myrow2['cost']*$myrow2['qty'],2);
					$Tot+=$myrow2['cost']*$myrow2['qty'];
					$MainTot=locale_number_format($Tot,2);

					$LeftOvers = $pdf->addTextWrap($Left_Margin+3,$YPos,95,$FontSize,$myrow2['stockid']);
					//Get translation if it exists
					$LeftOvers = $pdf->addTextWrap($Left_Margin+100,$YPos,251,$FontSize,$myrow2['description']);
					$LeftOvers = $pdf->addTextWrap($Left_Margin+353,$YPos,96,$FontSize,$DisplayPrice,'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+433,$YPos,95,$FontSize,$DisplayQty,'center');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+553,$YPos,35,$FontSize,$myrow2['uom'],'centre');
					//$LeftOvers = $pdf->addTextWrap($Left_Margin+590,$YPos,50,$FontSize,,'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+672,$YPos,90,$FontSize,$DisplayTot,'right');

					$YPos -= ($line_height);

					if ($YPos <= $Bottom_Margin) {

						/* head up a new invoice/credit note page */
						/*draw the vertical column lines right to the bottom */
						PrintLinesToBottom ();
						include ('includes/PDFProcurementPlanHeaderSimple.php');
					} //end if need a new page headed up

				} //end while there invoice are line items to print out
			} /*end if there are stock movements to show on the invoice or credit note*/

			$YPos -= $line_height;

			/* check to see enough space left to print the 4 lines for the totals/footer */
			if (($YPos-$Bottom_Margin)<(2*$line_height)) {
				PrintLinesToBottom ();
				include ('includes/PDFProcurementPlanHeaderSimple.php');
			}
			/* Print a column vertical line  with enough space for the footer */
			/* draw the vertical column lines to 4 lines shy of the bottom to leave space for invoice footer info ie totals etc */
			$pdf->line($Left_Margin+97, $TopOfColHeadings+12,$Left_Margin+97,$Bottom_Margin+(4*$line_height));

			/* Print a column vertical line */
			$pdf->line($Left_Margin+350, $TopOfColHeadings+12,$Left_Margin+350,$Bottom_Margin+(4*$line_height));

			/* Print a column vertical line */
			$pdf->line($Left_Margin+450, $TopOfColHeadings+12,$Left_Margin+450,$Bottom_Margin+(4*$line_height));

			/* Print a column vertical line */
			$pdf->line($Left_Margin+550, $TopOfColHeadings+12,$Left_Margin+550,$Bottom_Margin+(4*$line_height));

			/* Print a column vertical line */
			
			//$pdf->line($Left_Margin+587, $TopOfColHeadings+12,$Left_Margin+587,$Bottom_Margin+(4*$line_height));
			
			/* Print a column vertical line */

			$pdf->line($Left_Margin+670, $TopOfColHeadings+12,$Left_Margin+670,$Bottom_Margin+(4*$line_height));

			/* Rule off at bottom of the vertical lines */
			$pdf->line($Left_Margin, $Bottom_Margin+(4*$line_height),$Page_Width-$Right_Margin,$Bottom_Margin+(4*$line_height));

			/* Now print out the footer and totals */


			/* Print out the invoice text entered */
			$YPos = $Bottom_Margin+(3*$line_height);

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

		

			/*rule off for total */
			$pdf->line($Page_Width-$Right_Margin-222, $YPos-(2*$line_height)+10,$Page_Width-$Right_Margin,$YPos-(2*$line_height)+10);

			/*vertical to separate totals from comments and ROMALPA */
			$pdf->line($Page_Width-$Right_Margin-222, $YPos+$line_height,$Page_Width-$Right_Margin-222,$Bottom_Margin);

			$YPos+=10;
				$pdf->addText($Page_Width-$Right_Margin-220, $YPos - ($line_height*2)-5,$FontSize, 'GRAND TOTAL : ','right'); //total field
				//$pdf->addText($Page_Width-$Right_Margin-80, $YPos - ($line_height*2)-5,$FontSize,$MainTot,'right'); //total field
				$LeftOvers = $pdf->addTextWrap($Left_Margin+672,$YPos - ($line_height*2)-15,90,$FontSize,$MainTot,'right');
				$FontSize=9;
				$YPos-=4;
				$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,50,$FontSize,'Description :');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+60,$YPos,400,$FontSize,$myrow['comment']);
				while (mb_strlen($LeftOvers)>0 AND $YPos > $Bottom_Margin) {
					$YPos-=12;
					$LeftOvers = $pdf->addTextWrap($Left_Margin+60,$YPos,400,$FontSize,$LeftOvers);
				}
				/*print out bank details */

				$YPos-=12;
				$original ='This is to certify that the above Procurement Plan is in Accordance with the objectives of this Office';
				
				$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,400,$FontSize,' ' . _($original));

	if (isset($_GET['Email'])){ //email the invoice to address supplied

		include ('includes/htmlMimeMail.php');
		$FileName = $_SESSION['reports_dir'] . '/' . $_SESSION['DatabaseName'] . '_Plan_'. $id .'.pdf';
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
		header('location:IRQ_PurchaseOrService.php?Ref='.$_GET['Ref']);

	} else { //its not an email just print the invoice to PDF
		$pdf->OutputD($_SESSION['DatabaseName'] . '_Requisition_'. $id .'.pdf');
		//$tempname = date(DATE_ATOM);
		//$tempname = str_replace(":", "_", $tempname);
		//$pdf->OutputF('C:/Invoices/'.$_SESSION['DatabaseName'] . '_' . $InvOrCredit . '_' . $FromTransNo . '_' . $tempname . '.pdf');

	}
	$pdf->__destruct();
	//Now change the language back to the user's language
	$_SESSION['Language'] = $UserLanguage;
	include('includes/LanguageSetup.php');

} //if post submitrequest

function PrintLinesToBottom () {

	global $pdf;
	global $PageNumber;
	global $TopOfColHeadings;
	global $Left_Margin;
	global $Bottom_Margin;
	global $line_height;

			$pdf->line($Left_Margin+97, $TopOfColHeadings+12,$Left_Margin+97,$Bottom_Margin);

			/* Print a column vertical line */
			$pdf->line($Left_Margin+350, $TopOfColHeadings+12,$Left_Margin+350,$Bottom_Margin);

			/* Print a column vertical line */
			$pdf->line($Left_Margin+450, $TopOfColHeadings+12,$Left_Margin+450,$Bottom_Margin);

			/* Print a column vertical line */
			$pdf->line($Left_Margin+550, $TopOfColHeadings+12,$Left_Margin+550,$Bottom_Margin);

			/* Print a column vertical line */
			
			//$pdf->line($Left_Margin+587, $TopOfColHeadings+12,$Left_Margin+587,$Bottom_Margin+(4*$line_height));
			
			/* Print a column vertical line */

			$pdf->line($Left_Margin+670, $TopOfColHeadings+12,$Left_Margin+670,$Bottom_Margin);
			
			$PageNumber++;

}
include('includes/header.inc');

?>
<form action="" method="post" enctype="multipart/form-data" target="_parent">
<table>
<tr>
<th colspan="2"><h4>Simple Procurement Plan Report</h4></th>
</tr>
<tr>
<td>Department</td>
						<?php
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';			
	// any internal department allowed
	if($_SESSION['AllowedDepartment'] == 0){
	// any internal department allowed
	$sql="SELECT departmentid,
				description
			FROM departments
			ORDER BY description";
}else{
	// just 1 internal department allowed
	$sql="SELECT departmentid,
				description
			FROM departments
			WHERE departmentid = '". $_SESSION['AllowedDepartment'] ."'
			ORDER BY description";
}
$result=DB_query($sql);
echo '<td><select name="dept">';
echo '<option selected="selected" value="">--Please Select Requesting Department--</option>';
while ($myrow=DB_fetch_array($result)){
	if (isset($_POST['dept']) AND $_POST['dept']==$myrow['departmentid']){
		echo '<option selected="True" value="' . $myrow['departmentid'] . '">' . htmlspecialchars($myrow['description'], ENT_QUOTES,'UTF-8') . '</option>';
	} else {
		echo '<option value="' . $myrow['departmentid'] . '">' . htmlspecialchars($myrow['description'], ENT_QUOTES,'UTF-8') . '</option>';
	}
}
echo '</select>';
echo '</td>';
				
?>					
  </tr>
  <tr>
<td>Financial Year Ends </td>
<td>
<?php
$sql3="SELECT year FROM irq_procurementplan GROUP BY year";
$result3=DB_query($sql3);
	 echo '<select name="year">';
	 while ($myrow3=DB_fetch_array($result3)){
	 if (isset($_POST['year']) AND $_POST['year']==$myrow3['year']){
	 echo '<option selected="true">'.str_pad($myrow3['year'],2,'0',STR_PAD_LEFT).'</option>';
	 }else{
        echo '<option>'.str_pad($myrow3['year'],2,'0',STR_PAD_LEFT).'</option>';
		}
		}
	echo '</select>';
					   ?>
</td>
</tr>
<tr>
<td></td>
<td><input name="SubmitRequest" type="submit" value="Submit" /></td>
</tr>
  </table>
</form>

<?php
include('includes/footer.inc');
?>