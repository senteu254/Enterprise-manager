<?php


/* $Id: PrintCustTrans.php 7124 2015-02-05 02:12:56Z vvs2012 $ */

include('includes/session.inc');
$Title = _('Supplier Payments Report');
$ViewTopic = 'ARReports';
$BookMark = 'Print Supplier Payments Report';

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
	$pdf->addInfo('Author', 'peter ' . $Version);

		
		$title ='Supplier Payments report';
		$subj ='Supplier Payments';
		
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
	$suppname="";
	}else{
	$suppname="AND c.suppname='". $_POST['StockCat'] ."'";
	}
		
			   $sql ="SELECT * ,SUM((a.ovamount + a.ovgst)) AS totalamount,
							  a.alloc AS allocated,
							  a.inputdate,
							  a.transtext,
							  a.suppreference AS invoice,
							  c.suppname,
							  c.supplierid
						 FROM supptrans a
						 INNER JOIN suppliers c ON a.supplierno=c.supplierid
						  ".$suppname."
						 GROUP BY a.transtext";
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
				include('includes/PDFsupplierpaymentsbalancesheader.php');
				$FirstPage = False;
				$i=0;
				while ($myrow2=DB_fetch_array($result)){;
                $i++;
				$FormatedrequiredDate = ConvertSQLDate($myrow2['inputdate']);
				//$Amountp=($myrow2['allocated']);
				 //$LeftOvers = $pdf->addTextWrap($Left_Margin+0,$YPos,300,$FontSize,$i);
                 $LeftOvers = $pdf->addTextWrap($Left_Margin+0,$YPos,300,$FontSize,$myrow2['OrderNo']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+50,$YPos,300,$FontSize,$myrow2['invoice']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+121,$YPos,300,$FontSize,$myrow2['transtext']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+330,$YPos,300,$FontSize,$FormatedrequiredDate);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+385,$YPos,300,$FontSize,$myrow2['suppname']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+612,$YPos,300,$FontSize,locale_number_format($myrow2['totalamount']),2,'right');
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+675,$YPos,300,$FontSize,locale_number_format($myrow2['allocated']),2,'right');
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+715,$YPos,300,$FontSize,locale_number_format($myrow2['totalamount']-$myrow2['allocated'],2,'right'));
				
				$YPos -= ($line_height);

				if ($YPos <= $Bottom_Margin) {

						/* head up a new invoice/credit note page */
						/*draw the vertical column lines right to the bottom */
						PrintLinesToBottom ();
					include('includes/PDFsupplierpaymentsbalancesheader.php');
					} //end if need a new page headed up

				} //end while there invoice are line items to print out
			} /*end if there are stock movements to show on the invoice or credit note*/

			$YPos -= $line_height;

			/* check to see enough space left to print the 4 lines for the totals/footer */
			if (($YPos-$Bottom_Margin)<(2*$line_height)) {
				PrintLinesToBottom ();
				include('includes/PDFsupplierpaymentsbalancesheader.php');
			}
			/* Print a column vertical line  with enough space for the footer */
			/* draw the vertical column lines to 4 lines shy of the bottom to leave space for invoice footer info ie totals etc */
			
			/* Print a column vertical line */
			$pdf->line($Left_Margin+49, $TopOfColHeadings+16,$Left_Margin+49,$Bottom_Margin);
            /* Print a column vertical line */
			$pdf->line($Left_Margin+120, $TopOfColHeadings+16,$Left_Margin+120,$Bottom_Margin);
			/* Print a column vertical line */
			//$pdf->line($Left_Margin+190, $TopOfColHeadings+16,$Left_Margin+190,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+330, $TopOfColHeadings+16,$Left_Margin+330,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+384, $TopOfColHeadings+16,$Left_Margin+384,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+593, $TopOfColHeadings+16,$Left_Margin+593,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+658, $TopOfColHeadings+16,$Left_Margin+658,$Bottom_Margin);

			/* Print a column vertical line */
			$pdf->line($Left_Margin+712, $TopOfColHeadings+16,$Left_Margin+712,$Bottom_Margin);
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
				$LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,400,$FontSize,' ' . _($status));
                $LeftOvers = $pdf->addTextWrap($Left_Margin+80,$YPos,400,$FontSize,' ' . _($original));
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

			$pdf->line($Left_Margin+49, $TopOfColHeadings+16,$Left_Margin+49,$Bottom_Margin);
            /* Print a column vertical line */
			$pdf->line($Left_Margin+120, $TopOfColHeadings+16,$Left_Margin+120,$Bottom_Margin);
			/* Print a column vertical line */
			//$pdf->line($Left_Margin+190, $TopOfColHeadings+16,$Left_Margin+190,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+330, $TopOfColHeadings+16,$Left_Margin+330,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+384, $TopOfColHeadings+16,$Left_Margin+384,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+593, $TopOfColHeadings+16,$Left_Margin+593,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+658, $TopOfColHeadings+16,$Left_Margin+658,$Bottom_Margin);

			/* Print a column vertical line */
			$pdf->line($Left_Margin+712, $TopOfColHeadings+16,$Left_Margin+712,$Bottom_Margin);			
			$PageNumber++;
}