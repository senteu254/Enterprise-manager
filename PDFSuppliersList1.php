<?php

/* $Id: PrintCustTrans.php 7124 2015-02-05 02:12:56Z vvs2012 $ */

include('includes/session.inc');
$Title = _('PrintSuppliers Report');
$ViewTopic = 'ARReports';
$BookMark = 'PrintSuppliers List Report';

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
	$pdf->addInfo('Author', ' ' . $Version);

		
		$title ='PrintSuppliers report';
		$subj ='PrintSuppliers';
		
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
	$_POST['StockCat']=$_GET['stockcat'];
	
	/* retrieve the invoice details from the database to print
	notice that salesorder record must be present to print the invoice purging of sales orders will
	nobble the invoice reprints */
    if (isset($_POST['StockCat']) && $_POST['StockCat']=='All'){
	$quotation="";
	}else{
	$quotation="AND b.groupid='". $_POST['StockCat'] ."'";
	}
		$SQL = "SELECT  a.supplierid,
						a.suppname,
						a.suppliergroup,
						a.currcode,
						a.address1,
						a.address2,
						a.address3,
						a.address4,
						a.telephone,
						a.email,
						b.groupid,
						a.url
				FROM suppliers a
				INNER JOIN suppliergrouptype b ON a.suppliergroup=b.groupid
			    ".$quotation."
			    ORDER BY a.supplierid";
			$result=DB_query($SQL);
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
				include('includes/PDFSupplierListpeheader.php');
				$FirstPage = False;
				$i=0;
				while ($myrow2=DB_fetch_array($result)){
                $i++;
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+0,$YPos,300,$FontSize,$i);
                 $LeftOvers = $pdf->addTextWrap($Left_Margin+30,$YPos,300,$FontSize,$myrow2['supplierid']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+71,$YPos,300,$FontSize,$myrow2['suppname']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+300,$YPos,300,$FontSize,$myrow2['currcode']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+343,$YPos,300,$FontSize,$myrow2['address1']);
				// $LeftOvers = $pdf->addTextWrap($Left_Margin+495,$YPos,300,$FontSize,$myrow2['address3']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+495,$YPos,300,$FontSize,$myrow2['telephone']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+615,$YPos,300,$FontSize,$myrow2['email']);
				
				$YPos -= ($line_height);

				if ($YPos <= $Bottom_Margin) {

						/* head up a new invoice/credit note page */
						/*draw the vertical column lines right to the bottom */
						PrintLinesToBottom ();
					include('includes/PDFSupplierListpeheader.php');
					} //end if need a new page headed up

				} //end while there invoice are line items to print out
			} /*end if there are stock movements to show on the invoice or credit note*/

			$YPos -= $line_height;

			/* check to see enough space left to print the 4 lines for the totals/footer */
			if (($YPos-$Bottom_Margin)<(2*$line_height)) {
				PrintLinesToBottom ();
				include('includes/PDFSupplierListpeheader.php');
			}
			/* Print a column vertical line  with enough space for the footer */
			/* draw the vertical column lines to 4 lines shy of the bottom to leave space for invoice footer info ie totals etc */
			
			/* Print a column vertical line */
			$pdf->line($Left_Margin+29, $TopOfColHeadings+16,$Left_Margin+29,$Bottom_Margin);
            /* Print a column vertical line */
			$pdf->line($Left_Margin+70, $TopOfColHeadings+16,$Left_Margin+70,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+299, $TopOfColHeadings+16,$Left_Margin+299,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+342, $TopOfColHeadings+16,$Left_Margin+342,$Bottom_Margin);
			/* Print a column vertical line */
			//$pdf->line($Left_Margin+494, $TopOfColHeadings+16,$Left_Margin+494,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+494, $TopOfColHeadings+16,$Left_Margin+494,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+614, $TopOfColHeadings+16,$Left_Margin+614,$Bottom_Margin);

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
			 $pdf->SetTextColor(0,0,0);	 
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
			  	$totalsup="SELECT COUNT(supplierid) AS totalsupp
		      FROM suppliers ";
			  $allsup = DB_query($totalsup);
				while($myrow9 = DB_fetch_array($allsup)) {
				$suppliers='Total Suppliers:';
				$total=''.$myrow9['totalsupp'].'';
				$pdf->addText($XPos+660, $Bottom_Margin,$FontSize, _($suppliers) .'');
				$pdf->addText($XPos+730, $Bottom_Margin,$FontSize, _($total) .'');
					'</tr>';
					} 
		   if ($_POST['StockCat'] == 'All') {
		   $Supp="SELECT b.groupname,COUNT(a.supplierid) AS total
		      FROM suppliers a
			  INNER JOIN suppliergrouptype b ON a.suppliergroup=b.groupid
			  GROUP BY b.groupid";
			 }else {
			 $Supp="SELECT b.groupname,COUNT(a.supplierid) AS total
		      FROM suppliers a
			  INNER JOIN suppliergrouptype b ON a.suppliergroup=b.groupid
			  AND b.groupid='" . $_POST['StockCat'] . "'				  
			  GROUP BY b.groupid";
			 }
			  $suppgroup = DB_query($Supp);
			  
				
			while ($myrow4=DB_fetch_array($suppgroup)){
		    $groupname=''.$myrow4['groupname'].'';
			$totalorders=''.$myrow4['total'].'';
			$XPos += ($line_heigh);
			$pdf->addText($XPos, $Bottom_Margin,$FontSize, _($groupname) . '');
			$pdf->addText($XPos+60, $Bottom_Margin,$FontSize, _($totalorders) .'');			
            $line_heigh=75;
				}
		 
		 
		$pdf->OutputD($_SESSION['DatabaseName'] . '_Suppliers Quotation Group Report_.pdf');
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

			$pdf->line($Left_Margin+29, $TopOfColHeadings+16,$Left_Margin+29,$Bottom_Margin);
            /* Print a column vertical line */
			$pdf->line($Left_Margin+70, $TopOfColHeadings+16,$Left_Margin+70,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+299, $TopOfColHeadings+16,$Left_Margin+299,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+342, $TopOfColHeadings+16,$Left_Margin+342,$Bottom_Margin);
			/* Print a column vertical line */
			//$pdf->line($Left_Margin+494, $TopOfColHeadings+16,$Left_Margin+494,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+494, $TopOfColHeadings+16,$Left_Margin+494,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+614, $TopOfColHeadings+16,$Left_Margin+614,$Bottom_Margin);
			/* Print a column vertical line */
			//$pdf->line($Left_Margin+695, $TopOfColHeadings+12,$Left_Margin+695,$Bottom_Margin+(0.0*$line_height));			
			$PageNumber++;
}