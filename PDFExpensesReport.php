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
	$pdf->addInfo('Creator', 'kofc http');
	$pdf->addInfo('Author', 'Kalfrique ' . $Version);

		
		$title ='Commitments report';
		$subj ='Commitments';
		
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
	 $_POST['BookNo']=$_GET['book'];
	 $_POST['Yearend']=$_GET['fyear'];


	/* retrieve the invoice details from the database to print
	notice that salesorder record must be present to print the invoice purging of sales orders will
	nobble the invoice reprints */
    
	 if(isset($_POST['BookNo']) && $_POST['BookNo'] !=""){
		 $sort=" WHERE e.Vbook='". $_POST['BookNo'] ."' ";
		 }
   if(isset($_POST['Yearend'])){
		 $Fyear="AND b.Financial_Year='". $_POST['Yearend'] ."'";
		 }else{
	echo'There is Allocation in this Fincial year.';
		 }			
		 $sql="SELECT 
						 (SELECT SUM(COALESCE(a.commitments,0)) FROM commitment a WHERE a.voted_Item=b.votecode and b.Financial_Year=a.Fyear) AS commitments,
						 (SELECT SUM(COALESCE(a.decommitment,0)) FROM commitment a WHERE a.voted_Item=b.votecode and b.Financial_Year=a.Fyear) AS decom,
						 b.allocated_Fund,
						 b.votecode,
						 b.suppliementary,
						 b.voted_Item,
						 e.Votehead,
						 e.Votecode,
						 (SELECT SUM(COALESCE(c.amount,0)) FROM votepaymenttrans c WHERE c.VoteCode=b.votecode AND c.Fy=b.Financial_Year) AS amt
						 FROM voteheadmaintenance e
						 INNER JOIN funds_allocations b ON b.votecode=e.Votecode
						  ". $sort ." 
						  ".$Fyear."
						 GROUP BY e.Votecode";
			$result=DB_query($sql);
			if (DB_error_no()!=0 OR DB_num_rows($result)==0){

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
				include('includes/PDFExpensesHeader.php');
				$FirstPage = False;
				while ($myrow2=DB_fetch_array($result)){
                			 
				 $DisplayCur_Balance=$Totalall-$paycom;
				 $DisplayAll_Fund=locale_number_format($myrow2['allocated_Fund'],2);
				 $DisplaySup=locale_number_format($myrow2['total_suplmentary'],2);
				 $DisplayComm=locale_number_format($myrow2['commitments'],2);
				 $Tot+=($myrow2['allocated_Fund']+ $myrow2['suppliementary'])-(($myrow2['commitments']-$myrow2['decom'])+ $myrow2['amt']);
				 $MainTot=locale_number_format($Tot,2);
			     $Mainallocation+=filter_number_format($myrow2['allocated_Fund'],2);
				 $Mainsuppallocation+=$myrow2['suppliementary'];
				 $totalallocations+=($myrow2['allocated_Fund']+ $myrow2['suppliementary']);
				 $Totalcommitements+=($myrow2['commitments']-$myrow2['decom']);
				 $totalpayables+=$myrow2['amt'];
				 $commpayments+=($myrow2['commitments']-$myrow2['decom'])+ $myrow2['amt'];
				 $total_avabal+=$Ava_Balance;
				 $totalAlloc=($myrow2['allocated_Fund']+ $myrow2['suppliementary']);
				 $comm=($myrow2['commitments']-$myrow2['decom']);
				 $sum=($comm + $myrow2['amt']);
				 $Ava_Balance=($totalAlloc-$sum);
				 
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+0,$YPos,250,$FontSize,$myrow2['Votehead']);
                 $LeftOvers = $pdf->addTextWrap($Left_Margin+215,$YPos,95,$FontSize,$DisplayAll_Fund,'right');
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+285,$YPos,95,$FontSize,locale_number_format($myrow2['suppliementary'],2),'right');
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+365,$YPos,95,$FontSize,locale_number_format($totalAlloc,2),'right');
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+445,$YPos,95,$FontSize,locale_number_format($comm,2),'right');
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+520,$YPos,95,$FontSize,locale_number_format($myrow2['amt'],2),'right');
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+595,$YPos,95,$FontSize,locale_number_format($sum,2),'right');
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+678,$YPos,95,$FontSize,locale_number_format($Ava_Balance,2),'right');

				$YPos -= ($line_height);

				if ($YPos <= $Bottom_Margin) {

						/* head up a new invoice/credit note page */
						/*draw the vertical column lines right to the bottom */
						PrintLinesToBottom ();
					include('includes/PDFExpensesHeader.php');
					} //end if need a new page headed up

				} //end while there invoice are line items to print out
			} /*end if there are stock movements to show on the invoice or credit note*/

			$YPos -= $line_height;

			/* check to see enough space left to print the 4 lines for the totals/footer */
			if (($YPos-$Bottom_Margin)<(2*$line_height)) {
				PrintLinesToBottom ();
				include('includes/PDFExpensesHeader.php');
			}
			/* Print a column vertical line  with enough space for the footer */
			/* draw the vertical column lines to 4 lines shy of the bottom to leave space for invoice footer info ie totals etc */
			
			/* Print a column vertical line */
			$pdf->line($Left_Margin+240, $TopOfColHeadings+12,$Left_Margin+240,$Bottom_Margin+(1.6*$line_height));
            /* Print a column vertical line */
			$pdf->line($Left_Margin+310, $TopOfColHeadings+12,$Left_Margin+310,$Bottom_Margin+(1.6*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+380, $TopOfColHeadings+12,$Left_Margin+380,$Bottom_Margin+(1.6*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+460, $TopOfColHeadings+12,$Left_Margin+460,$Bottom_Margin+(1.6*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+540, $TopOfColHeadings+39,$Left_Margin+540,$Bottom_Margin+(1.6*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+615, $TopOfColHeadings+12,$Left_Margin+615,$Bottom_Margin+(1.6*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+695, $TopOfColHeadings+12,$Left_Margin+695,$Bottom_Margin+(1.6*$line_height));

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
			/*rule off for total */
			$pdf->line($Page_Width-$Right_Margin-771, $YPos-(2*$line_height)+10,$Page_Width-$Right_Margin,$YPos-(2*$line_height)+10);
			/*vertical to separate totals from comments and ROMALPA  added by langat pete*/
			$pdf->line($Page_Width-$Right_Margin-77, $YPos+$line_height,$Page_Width-$Right_Margin-77,$Bottom_Margin);

			$YPos+=10;			    
				$pdf->addText($Page_Width-$Right_Margin-760, $YPos - ($line_height*2)+20,$FontSize, 'TOTAL : ','right'); //total field
				$pdf->addText($Page_Width-$Right_Margin-143, $YPos - ($line_height*2)+20,$FontSize,locale_number_format($commpayments,2),'right');
				$pdf->addText($Page_Width-$Right_Margin-220, $YPos - ($line_height*2)+20,$FontSize,locale_number_format($totalpayables,2),'right');
				$pdf->addText($Page_Width-$Right_Margin-295, $YPos - ($line_height*2)+20,$FontSize,locale_number_format($Totalcommitements,2),'right');
				$pdf->addText($Page_Width-$Right_Margin-386, $YPos - ($line_height*2)+20,$FontSize,locale_number_format($totalallocations,2),'right');
				$pdf->addText($Page_Width-$Right_Margin-425, $YPos  - ($line_height*2)+20,$FontSize,locale_number_format($Mainsuppallocation,2),'right');
				$pdf->addText($Page_Width-$Right_Margin-535, $YPos  - ($line_height*2)+20,$FontSize,locale_number_format($Mainallocation,2),'right');
				$pdf->addText($Page_Width-$Right_Margin-917, $YPos  - ($line_height*2)+20,$FontSize,$MainTot,'right');
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+672,$YPos - ($line_height*2)+20,100,$FontSize,$MainTot,'right');
				$FontSize=9;
				$YPos-=4;
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-=35,90,$FontSize,'Description :');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+60,$YPos,400,$FontSize,$myrow['comment']);
				while (mb_strlen($LeftOvers)>0 AND $YPos > $Bottom_Margin) {
					$YPos-=12;
					$LeftOvers = $pdf->addTextWrap($Left_Margin+60,$YPos,400,$FontSize,$LeftOvers);
				}
				
				/*print out bank details */
				$YPos-=45;
				$status ='FY'.'&nbsp;'.Date('Y',YearEndDate($_SESSION['YearEnd'],-1)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd']));
				$original ='VOTE BOOK STATUS RETURNS AS AT '.date($_SESSION['DefaultDateFormat']).'';
				//$date = .date("d, M Y"), 'right').;
				
				$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,400,$FontSize,' ' . _($status));
                $LeftOvers = $pdf->addTextWrap($Left_Margin+80,$YPos,400,$FontSize,' ' . _($original));
		$pdf->OutputI($_SESSION['DatabaseName'] . '_Expenses Report_.pdf');
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

			$pdf->line($Left_Margin+240, $TopOfColHeadings+12,$Left_Margin+240,$Bottom_Margin+(0.0*$line_height));
            /* Print a column vertical line */
			$pdf->line($Left_Margin+310, $TopOfColHeadings+12,$Left_Margin+310,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+380, $TopOfColHeadings+12,$Left_Margin+380,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+460, $TopOfColHeadings+12,$Left_Margin+460,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+540, $TopOfColHeadings+39,$Left_Margin+540,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+615, $TopOfColHeadings+12,$Left_Margin+615,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+695, $TopOfColHeadings+12,$Left_Margin+695,$Bottom_Margin+(0.0*$line_height));			
			$PageNumber++;
}