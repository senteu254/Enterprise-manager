<?php

/* $Id: PrintCustTrans.php 7124 2015-02-05 02:12:56Z vvs2012 $ */

include('includes/session.inc');

$SQLI  = DB_query("SELECT * FROM tenders a
						INNER JOIN tenderitems c ON a.tenderid =c.tenderid	
						INNER JOIN stockmaster f ON c.stockid=f.stockid
						INNER JOIN tendersuppliers d ON a.tenderid=d.tenderid	
						INNER JOIN suppliers e ON d.supplierid=e.supplierid
				        WHERE d.supplierid=" .$_GET['SupplierID']. " AND a.tenderid=".$_GET['TenderID']." 
				        GROUP BY d.supplierid");
$myrow4 = DB_fetch_array($SQLI);

$ViewTopic = 'ARReports';
$BookMark = 'PrintSupplierQuotation Report';

	include ('includes/class.pdf.php');
/* This invoice is hard coded for A4 Landscape invoices or credit notes so can't use PDFStarter.inc */

	$Page_Width=842;
	$Page_Height=595;
	$Top_Margin=30;
	$Bottom_Margin=53;
	$Left_Margin=40;
	$Right_Margin=30;

	$pdf = new Cpdf('L', 'pt', 'A4');
	$pdf->addInfo('Creator', 'kofc http');
	$pdf->addInfo('Author', 'petero ' . $Version);
		
		$title ='Quatation report';
		$subj ='Quotation';
		
		$pdf->addInfo('Title',_($title));
		$pdf->addInfo('Subject',_($subj));	

	$pdf->setAutoPageBreak(0);
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->AddPage();
	$pdf->cMargin = 0;
/* END Brought from class.pdf.php constructor */

	$FirstPage = true;
	$line_height=14;

	//Keep a record of the user's language
	$UserLanguage = $_SESSION['Language'];
	
	/* retrieve the invoice details from the database to print
	notice that salesorder record must be present to print the invoice purging of sales orders will
	nobble the invoice reprints */           
		
				  $sql="SELECT * FROM suppliers a
						INNER JOIN tendersuppliers b ON a.supplierid=b.supplierid
						INNER JOIN tenderitems c ON b.tenderid=c.tenderid
						INNER JOIN tenders d ON c.tenderid=d.tenderid
						INNER JOIN stockmaster e ON c.stockid=e.stockid
						WHERE b.supplierid=" .$_GET['SupplierID']. " AND d.tenderid=".$_GET['TenderID']."";
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
				  $i=0;
				include('includes/PDFSuppliersQuotationheader.php');
				$FirstPage = False;
				while ($myrow2=DB_fetch_array($result)){
               $i++;	
				 $pdf->SetTextColor(0,0,0);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+0,$YPos,95,$FontSize,$i,1,'T');
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+25,$YPos,320,$FontSize,$myrow2['description'],1,'T');
                 $LeftOvers = $pdf->addTextWrap($Left_Margin+300,$YPos,50,$FontSize,$myrow2['units'],1,1);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+350,$YPos,50,$FontSize,$myrow2['quantity'],1,1);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+400,$YPos,372,$FontSize,$myrow2[''],1,'T');
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+425,$YPos,95,$FontSize,$myrow2['']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+500,$YPos,95,$FontSize,$myrow2['']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+575,$YPos,95,$FontSize,$myrow2['']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+650,$YPos,95,$FontSize,$myrow2['']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+678,$YPos,95,$FontSize,$myrow2['']);
				
				$YPos -= ($line_height);
                
				if ($YPos <= $Bottom_Margin) {
          
						/* head up a new invoice/credit note page */
						/*draw the vertical column lines right to the bottom */
						PrintLinesToBottom ();
					include('includes/PDFSuppliersQuotationheader.php');
					} //end if need a new page headed up

				} //end while there invoice are line items to print out
				$pdf->line($XPos, $YPos+10,$Page_Width-$Right_Margin, $YPos+10);
			} /*end if there are stock movements to show on the invoice or credit note*/
			
			
			$YPos -= $line_height;
			
			
            //$FontSize = 10;
			/*rule off for total */
			
			/* check to see enough space left to print the 4 lines for the totals/footer 
			if (($YPos-$Bottom_Margin)<(3*$line_height)) {
				PrintLinesToBottom ();
				include('includes/PDFSuppliersQuotationheader.php');
			}
			/* Print a column vertical line  with enough space for the footer */
			/* draw the vertical column lines to 4 lines shy of the bottom to leave space for invoice footer info ie totals etc */
			$pdf->line($Left_Margin+24, $TopOfColHeadings+39,$Left_Margin+24,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+300, $TopOfColHeadings+39,$Left_Margin+300,$Bottom_Margin+(0.0*$line_height));
            /* Print a column vertical line */
			$pdf->line($Left_Margin+350, $TopOfColHeadings+39,$Left_Margin+350,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+400, $TopOfColHeadings+39,$Left_Margin+400,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+449, $TopOfColHeadings+39,$Left_Margin+449,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+510, $TopOfColHeadings+39,$Left_Margin+510,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+575, $TopOfColHeadings+39,$Left_Margin+575,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+650, $TopOfColHeadings+39,$Left_Margin+650,$Bottom_Margin+(0.0*$line_height));
			//* Print a column vertical line */
			$pdf->line($Left_Margin+715, $TopOfColHeadings+39,$Left_Margin+715,$Bottom_Margin+(0.0*$line_height));

			/* Print a column vertical line */
			//$pdf->line($Left_Margin+550, $TopOfColHeadings+12,$Left_Margin+550,$Bottom_Margin+(4*$line_height));

			/* Print a column vertical line */
			
			//$pdf->line($Left_Margin+587, $TopOfColHeadings+12,$Left_Margin+587,$Bottom_Margin+(4*$line_height));
			
			/* Print a column vertical line */

			//$pdf->line($Left_Margin+670, $TopOfColHeadings+12,$Left_Margin+670,$Bottom_Margin+(4*$line_height));

			/* Rule off at bottom of the vertical lines */
			//$pdf->line($Left_Margin, $Bottom_Margin+(4*$line_height),$Page_Width-$Right_Margin,$Bottom_Margin+(4*$line_height));
			/* Now print out the footer and totals */
			/* Print out the invoice text entered */
			//$YPos = $Bottom_Margin+(-1*$line_height);

		//  $pdf->addText($Page_Width-$Right_Margin-392, $YPos - ($line_height*3)+22,$FontSize, _('Bank Code:***** Bank Account:*****'));
		//	$FontSize=10;

			//$FontSize =8;
			
			//if (mb_strlen($LeftOvers)>0) {
			//	$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-24,280,$FontSize,$LeftOvers);
			//	if (mb_strlen($LeftOvers)>0) {
			
			$msq1='FOR OFFICIAL USE ONLY Sellers sign and stamp......................................................';
			$msq2='Date...............................................';
			$msq90='Opened by(1)............................................Designation:...........................Sign..........................';
			$msq91='Opened by(2)............................................Designation:...........................Sign..........................';
			$p='Opened by(3)............................................Designation:...........................Sign..........................';
			$m='Date:             .............................................Time:......................................';
			//$msg93='Date......................Time:.....................';
			$pdf->addText($XPos, $Bottom_Margin,$FontSize, _($msq1) . ' ');
			$pdf->addText($XPos, $Bottom_Margin-39,$FontSize, _($msq2) . ' ');
			$pdf->addText($XPos-=440, $Bottom_Margin,$FontSize, _($msq90) . ' ');
			$pdf->addText($XPos, $Bottom_Margin-13,$FontSize, _($msq91) . ' ');
			$pdf->addText($XPos, $Bottom_Margin-26,$FontSize, _($p) . ' ');
			$pdf->addText($XPos, $Bottom_Margin-39,$FontSize, _($m) . ' ');
			///		$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos-36,280,$FontSize,$LeftOvers);
					/*If there is some of the InvText leftover after 3 lines 200 wide then it is not printed :( */
			//	}
			//}
			
			
			//$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,400,$FontSize,' ' . _($status));
			//$LeftOvers = $pdf->addTextWrap($Left_Margin+80,$YPos,400,$FontSize,' ' . _($original));
			
				
		    $pdf->OutputD($_SESSION['DatabaseName'] . '_Quotation Report_.pdf');
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

          $pdf->line($Left_Margin+24, $TopOfColHeadings+39,$Left_Margin+24,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+300, $TopOfColHeadings+39,$Left_Margin+300,$Bottom_Margin+(0.0*$line_height));
            /* Print a column vertical line */
			$pdf->line($Left_Margin+350, $TopOfColHeadings+39,$Left_Margin+350,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+400, $TopOfColHeadings+39,$Left_Margin+400,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+449, $TopOfColHeadings+39,$Left_Margin+449,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+510, $TopOfColHeadings+39,$Left_Margin+510,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+575, $TopOfColHeadings+39,$Left_Margin+575,$Bottom_Margin+(0.0*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+650, $TopOfColHeadings+39,$Left_Margin+650,$Bottom_Margin+(0.0*$line_height));
			//* Print a column vertical line */
			$pdf->line($Left_Margin+715, $TopOfColHeadings+39,$Left_Margin+715,$Bottom_Margin+(0.0*$line_height));
			$PageNumber++;
			$FontSize = 10;
			/*rule off for total */
			
}