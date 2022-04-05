<?php

/* $Id: PrintCustTrans.php 7124 2015-02-05 02:12:56Z vvs2012 $ */

include('includes/session.inc');
$Title = _('Commitment Report');
$ViewTopic = 'ARReports';
$BookMark = 'PrintCommitment Report';

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
	$pdf->addInfo('Author', 'Beatrice ' . $Version);

		
		$title ='Preventive report';
		$subj ='Preventive';
		
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
            
		
			$sql = "SELECT *,ADDDATE(lastcompleted,frequencydays) AS endate FROM irq_maintenance a
        INNER JOIN fixedassets b ON a.mcno = b.serialno
		INNER JOIN fixedassettasks c ON b.assetid =c.assetid
		INNER JOIN irq_request d ON a.maintenanceid =d.requestid
		INNER JOIN irq_documents e ON d.doc_id =e.doc_id 
		WHERE c.taskid='". $_GET['id']."' AND e.doc_id='6'";
			$result=DB_query($sql);
			$myrow=DB_fetch_array($result);
			$d=$myrow['breakdowndate'];
			$dat=strtotime($d);
			$date=date("Y/m/d",$dat);
			$time=date("H:i",$dat);
			
			$ed=$myrow['endate'];
			$eds=strtotime($ed);
			$endate=date("Y/m/d",$eds);
			$diff=abs($endate-$date);
			if (DB_error_no()!=0 OR DB_num_rows($result)==0) {

				$Title = _('Transaction Print Error Report');
				include ('includes/header.inc');
				echo '<br />' . _('There was a problem retrieving the information from the database');
				if ($debug==1) {
					echo '<br />' . _('The SQL used to get this information that failed was') . '<br />' . $sql;
				}
				include('includes/footer.inc');
				exit;
			} else {

				$FontSize = 10;
				$PageNumber = 1;

				include('PDFPreventiveMaintenanceCompleteHeader.php');
				$FirstPage = False;
				while ($myrow=DB_fetch_array($result)) {

					
				
					$DisplayCommitments=locale_number_format($myrow2['commitments'],2);
					//$DisplayCummulativeCom=locale_number_format($myrow2['cummulative_Com'],2);
					$DisplayTot='';
					$Tot+='cummulative_Com';
					$MainTot=locale_number_format($Tot,2);
					
                    $LeftOvers = $pdf->addTextWrap($Left_Margin+250,$YPos,50,$FontSize,$myrow2['date'],'center');
					
					//Get translation if it exists
					$LeftOvers = $pdf->addTextWrap($Left_Margin+30,$YPos,96,$FontSize,$myrow2['lpo_No']);	
					$LeftOvers = $pdf->addTextWrap($Left_Margin+420,$YPos,95,$FontSize,$myrow2['voted_Item'],'center');							     					
					$LeftOvers = $pdf->addTextWrap($Left_Margin+633,$YPos,95,$FontSize,$DisplayCommitments,'center');
					//$LeftOvers = $pdf->addTextWrap($Left_Margin+553,$YPos,35,$FontSize,$DisplayCummulativeCom,'center');;
					//$LeftOvers = $pdf->addTextWrap($Left_Margin+590,$YPos,50,$FontSize,,'right');
					$LeftOvers = $pdf->addTextWrap($Left_Margin+672,$YPos,90,$FontSize,$DisplayTot,'right');

					$YPos -= ($line_height);

					if ($YPos <= $Bottom_Margin) {

						/* head up a new invoice/credit note page */
						/*draw the vertical column lines right to the bottom */
						PrintLinesToBottom ();
					include('PDFPreventiveMaintenanceCompleteHeader.php');
					} //end if need a new page headed up

				} //end while there invoice are line items to print out
			} /*end if there are stock movements to show on the invoice or credit note*/

			$YPos -= $line_height;

			/* check to see enough space left to print the 4 lines for the totals/footer */
			if (($YPos-$Bottom_Margin)<(2*$line_height)) {
				PrintLinesToBottom ();
				include('PDFPreventiveMaintenanceCompleteHeader.php');
			}
			/* Print a column vertical line  with enough space for the footer */
			/* draw the vertical column lines to 4 lines shy of the bottom to leave space for invoice footer info ie totals etc */
			$pdf->line($Left_Margin+200, $TopOfColHeadings+12,$Left_Margin+200,$Bottom_Margin+(4*$line_height));

			/* Print a column vertical line */
			$pdf->line($Left_Margin+400, $TopOfColHeadings+12,$Left_Margin+400,$Bottom_Margin+(4*$line_height));

			/* Print a column vertical line */
			$pdf->line($Left_Margin+600, $TopOfColHeadings+12,$Left_Margin+600,$Bottom_Margin+(4*$line_height));

			/* Print a column vertical line */
			//$pdf->line($Left_Margin+550, $TopOfColHeadings+12,$Left_Margin+550,$Bottom_Margin+(4*$line_height));

			/* Print a column vertical line */
			
			//$pdf->line($Left_Margin+587, $TopOfColHeadings+12,$Left_Margin+587,$Bottom_Margin+(4*$line_height));
			
			/* Print a column vertical line */

			//$pdf->line($Left_Margin+670, $TopOfColHeadings+12,$Left_Margin+670,$Bottom_Margin+(4*$line_height));

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

		


			$YPos+=10;
				$pdf->addText($Page_Width-$Right_Margin-220, $YPos - ($line_height*2)+30,$FontSize, 'Date : ','right'); //total field
				//$pdf->addText($Page_Width-$Right_Margin-80, $YPos - ($line_height*2)-5,$FontSize,$MainTot,'right'); //total field
				$LeftOvers = $pdf->addTextWrap($Left_Margin+672,$YPos - ($line_height*2)-15,90,$FontSize,$MainTot,'right');
				$FontSize=9;
				$YPos-=4;
				$pdf->addText($Page_Width-$Right_Margin-180, $YPos - ($line_height*2)+30,$FontSize, '............................................. ','right');
				
				$pdf->addText($Page_Width-$Right_Margin-220, $YPos - ($line_height*2)+20,$FontSize, 'Time : ','right'); //total field
				//$pdf->addText($Page_Width-$Right_Margin-80, $YPos - ($line_height*2)-5,$FontSize,$MainTot,'right'); //total field
				$LeftOvers = $pdf->addTextWrap($Left_Margin+672,$YPos - ($line_height*2)-15,90,$FontSize,$MainTot,'right');
				$FontSize=9;
				$YPos-=4;
				$pdf->addText($Page_Width-$Right_Margin-180, $YPos - ($line_height*2)+20,$FontSize, '.......................................... ','right');
				
				$pdf->addText($Page_Width-$Right_Margin-220, $YPos - ($line_height*2)+10,$FontSize, 'Sign : ','right'); //total field
				//$pdf->addText($Page_Width-$Right_Margin-80, $YPos - ($line_height*2)-5,$FontSize,$MainTot,'right'); //total field
				$LeftOvers = $pdf->addTextWrap($Left_Margin+672,$YPos - ($line_height*2)-15,90,$FontSize,$MainTot,'right');
				$FontSize=9;
				$YPos-=4;
				$pdf->addText($Page_Width-$Right_Margin-180, $YPos - ($line_height*2)+10,$FontSize, '.......................................... ','right');
				
				while (mb_strlen($LeftOvers)>0 AND $YPos > $Bottom_Margin) {
					$YPos-=12;
					$LeftOvers = $pdf->addTextWrap($Left_Margin+60,$YPos,400,$FontSize,$LeftOvers);
				}
				/*print out bank details */

				$YPos-=4;
				
				$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,400,$FontSize,'HAND OVER TO USER');
                $LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-10,400,$FontSize,'Name :'.'....................................................................');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-20,400,$FontSize,'MC downtime :'.'....................................................................');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-30,400,$FontSize,'Remarks :'.'....................................................................');
	
		$pdf->OutputD($_SESSION['DatabaseName'] . '_Preventive Report_.pdf');
		//$tempname = date(DATE_ATOM);
		//$tempname = str_replace(":", "_", $tempname);
		//$pdf->OutputF('C:/Invoices/'.$_SESSION['DatabaseName'] . '_' . $InvOrCredit . '_' . $FromTransNo . '_' . $tempname . '.pdf');

	
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

			$pdf->line($Left_Margin+77, $TopOfColHeadings+12,$Left_Margin+97,$Bottom_Margin);

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