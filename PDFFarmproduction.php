<?php

/* $Id: PrintCustTrans.php 7124 2015-02-05 02:12:56Z vvs2012 $ */

include('includes/session.inc');
$Title = _('Supplier Inquiry Report');
$ViewTopic = 'ARReports';
$BookMark = 'Supplier Inquiry Report';

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
	$pdf->addInfo('Author', 'peter ' . $Version);
		
	$title ='Supplier Inquiry Report';
	$subj ='Supplier Inquiry';
	
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
    $DateAfterCriteria=$_GET['datef'];
	$TransToDate=$_GET['datet'];
    //$_POST['StockCat']=$_GET['stockcat'];
   // $_POST['StockCode']=$_GET['stockcode'];



	
	/* retrieve the invoice details from the database to print
	notice that salesorder record must be present to print the invoice purging of sales orders will
	nobble the invoice reprints */
 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*	if (isset($_POST['StockCat']) && $_POST['StockCat']=='All'){
	$search="";
	}else{
	$search="AND c.stockid='". $_POST['StockCat'] ."'";
	}*/
			
			$sql = "SELECT  *,c.stockid as groupid,b.units, c.description as desk FROM farmproduction a
					INNER JOIN farmproductionitems b ON a.Fid=b.fid
					INNER JOIN farmdescriptions c ON b.stockid=c.description_Id
					INNER JOIN stockmaster d ON c.stockid=d.stockid
					WHERE a.date>='" . $DateAfterCriteria . "'
					AND a.date<='" . $TransToDate . "'"; 
		
			
            $TR=DB_query($sql);
			if (DB_error_no()!=0 OR DB_num_rows($TR)==0) {

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

				include('includes/PDFFarmproductionHeader.php');
				$FirstPage = False;
				$group=NULL;
				$i=0;
				while ($myrow=DB_fetch_array($TR)) {
				
                $i++;
				//$MainTotcost += $myrow['extcost'];
				//$MainTotprice += $myrow['extprice'];
			
							
                 $LeftOvers = $pdf->addTextWrap($Left_Margin+350,$YPos,50,$FontSize,$DisplayAll_Fund,'center');
					$Totalcost=($myrow['unitcost']*$myrow['quantity']);
					$Total=locale_number_format($Totalcost,2);
					$MainTotprice+=($myrow['unitcost']*$myrow['quantity']);
					//Get translation if it exists  stkcode
				$LeftOvers = $pdf->addTextWrap($Left_Margin+4,$YPos,96,$FontSize,$i);	
				$LeftOvers = $pdf->addTextWrap($Left_Margin+52,$YPos,150,$FontSize,$myrow['description_Id']);	
				$LeftOvers = $pdf->addTextWrap($Left_Margin+120,$YPos,95,$FontSize,$myrow['desk']);
				$LeftOvers = $pdf->addTextWrap($Left_Margin+342,$YPos,95,$FontSize,$myrow['date']);
				$LeftOvers = $pdf->addTextWrap($Left_Margin+392,$YPos,95,$FontSize,$myrow['units']);
				$LeftOvers = $pdf->addTextWrap($Left_Margin+452,$YPos,95,$FontSize,locale_number_format($myrow['unitcost'],2));
				$LeftOvers = $pdf->addTextWrap($Left_Margin+532,$YPos,95,$FontSize,$myrow['quantity']);
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+452,$YPos,95,$FontSize,$myrow['totalamount']);
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+402,$YPos,95,$FontSize,locale_number_format($myrow['totalamount'],$_SESSION['CompanyRecord']['decimalplaces']),'right');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+572,$YPos,95,$FontSize,$myrow['areacovered'],'right');
				$LeftOvers = $pdf->addTextWrap($Left_Margin+670,$YPos,95,$FontSize,$Total,'right');	
				if($myrow['groupid']!= $group){
				
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+670,$YPos,100,$FontSize,'Total','right');
				$group = $myrow['groupid'];
				}
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+590,$YPos,50,$FontSize,,'right');
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+652,$YPos,115,$FontSize,locale_number_format($myrow['totalamount']-$myrow['allocated'],$SupplierRecord['currdecimalplaces']),'right');

				$YPos -= ($line_height);

				if ($YPos <= $Bottom_Margin) {

						/* head up a new invoice/credit note page */
						/*draw the vertical column lines right to the bottom */
						PrintLinesToBottom ();
					include('includes/PDFFarmproductionHeader.php');
					} //end if need a new page headed up

				} //end while there invoice are line items to print out
			} /*end if there are stock movements to show on the invoice or credit note*/

			$YPos -= $line_height;

			/* check to see enough space left to print the 4 lines for the totals/footer */
			if (($YPos-$Bottom_Margin)<(2*$line_height)) {
				PrintLinesToBottom ();
				include('includes/PDFFarmproductionHeader.php');
			}
			/* Print a column vertical line  with enough space for the footer */
			/* draw the vertical column lines to 4 lines shy of the bottom to leave space for invoice footer info ie totals etc */
			

			/* Print a column vertical line */
			$pdf->line($Left_Margin+50, $TopOfColHeadings,$Left_Margin+50,$Bottom_Margin+(4*$line_height));
            /* Print a column vertical line */
			$pdf->line($Left_Margin+120, $TopOfColHeadings,$Left_Margin+120,$Bottom_Margin+(4*$line_height));
			$pdf->line($Left_Margin+340, $TopOfColHeadings,$Left_Margin+340,$Bottom_Margin+(4*$line_height));
            /* Print a column vertical line */
			$pdf->line($Left_Margin+392, $TopOfColHeadings,$Left_Margin+392,$Bottom_Margin+(4*$line_height));
			$pdf->line($Left_Margin+452, $TopOfColHeadings,$Left_Margin+452,$Bottom_Margin+(4*$line_height));
            /* Print a column vertical line */
			$pdf->line($Left_Margin+532, $TopOfColHeadings,$Left_Margin+532,$Bottom_Margin+(4*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+590, $TopOfColHeadings,$Left_Margin+590,$Bottom_Margin+(4*$line_height));
			/* Print a column vertical line */
			$pdf->line($Left_Margin+670, $TopOfColHeadings,$Left_Margin+670,$Bottom_Margin+(4*$line_height));

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
			$pdf->line($Page_Width-$Right_Margin-182, $YPos-(2*$line_height)+10,$Page_Width-$Right_Margin,$YPos-(2*$line_height)+10);

			/*vertical to separate totals from comments and ROMALPA */
			$pdf->line($Page_Width-$Right_Margin-182, $YPos+$line_height,$Page_Width-$Right_Margin-182,$Bottom_Margin);
			//$pdf->line($Page_Width-$Right_Margin-252, $YPos+$line_height,$Page_Width-$Right_Margin-252,$Bottom_Margin);
			$pdf->line($Page_Width-$Right_Margin-102, $YPos+$line_height,$Page_Width-$Right_Margin-102,$Bottom_Margin+27);
			$YPos+=10;
				//$pdf->addText($Page_Width-$Right_Margin-255, $YPos - ($line_height*2)+30,$FontSize, 'TOTAL : ','right'); //total field
				//$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-255,$YPos - ($line_height*2)+5,150,$FontSize+2,locale_number_format($MainTotprice,$_SESSION['CompanyRecord']['decimalplaces']),'right');
				$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-125,$YPos - ($line_height*2)+5,120,$FontSize+2,locale_number_format($MainTotprice,$_SESSION['CompanyRecord']['decimalplaces']),'right');
				$FontSize=9;
				$YPos-=4;
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,50,$FontSize,'Description :');
				//$LeftOvers = $pdf->addTextWrap($Left_Margin+60,$YPos,400,$FontSize,$myrow['comment']);
				while (mb_strlen($LeftOvers)>0 AND $YPos > $Bottom_Margin) {
					$YPos-=12;
					$LeftOvers = $pdf->addTextWrap($Left_Margin+60,$YPos,400,$FontSize,$LeftOvers);
				}
				/*print out bank details */

				$YPos-=12;
				$original ='This is to certify that the above information is in Accordance with the objectives of this Office';
				
				$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,400,$FontSize,' ' . _($original));

	
		$pdf->OutputI($_SESSION['DatabaseName'] . '_Farm Production_.pdf');
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

			$pdf->line($Left_Margin+50, $TopOfColHeadings,$Left_Margin+50,$Bottom_Margin);

			/* Print a column vertical line */
			$pdf->line($Left_Margin+120, $TopOfColHeadings,$Left_Margin+120,$Bottom_Margin);
			$pdf->line($Left_Margin+340, $TopOfColHeadings,$Left_Margin+340,$Bottom_Margin);

			/* Print a column vertical line */
			$pdf->line($Left_Margin+392, $TopOfColHeadings,$Left_Margin+392,$Bottom_Margin);

			/* Print a column vertical line */
			$pdf->line($Left_Margin+452, $TopOfColHeadings,$Left_Margin+452,$Bottom_Margin);
			$pdf->line($Left_Margin+532, $TopOfColHeadings,$Left_Margin+532,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+590, $TopOfColHeadings,$Left_Margin+590,$Bottom_Margin);
			$pdf->line($Left_Margin+670, $TopOfColHeadings,$Left_Margin+670,$Bottom_Margin);

			/* Print a column vertical line */
			
			//$pdf->line($Left_Margin+587, $TopOfColHeadings+12,$Left_Margin+587,$Bottom_Margin+(4*$line_height));
			
			/* Print a column vertical line */

			//$pdf->line($Left_Margin+670, $TopOfColHeadings+12,$Left_Margin+670,$Bottom_Margin);
			
			$PageNumber++;

}