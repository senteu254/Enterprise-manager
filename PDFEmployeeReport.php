<?php

/* $Id: PrintCustTrans.php 7124 2015-02-05 02:12:56Z vvs2012 $ */

include('includes/session.inc');
$Title = _('Employees Report');
$ViewTopic = 'ARReports';
$BookMark = 'Print Employees Report';

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
	$pdf->addInfo('Title',_($Title));
	$pdf->addInfo('Subject',_('Employees'));
	

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
            
		
				$sql="SELECT *, e.emp_id
							 FROM employee e
							 LEFT JOIN departments b ON b.departmentid=e.id_dept
							 LEFT JOIN section s ON s.id_sec=e.id_sec
							 WHERE stat=1
							 ORDER BY b.description ASC, emp_fname ASC";
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
				$FontSize = 9;
				$PageNumber = 1;
				include('includes/PDFEmployeeHeader.php');
				$FirstPage = False;
				$No =1;
				while ($myrow2=DB_fetch_array($result)){
          
				 $pdf->SetTextColor(0,0,0);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+0,$YPos,50,$FontSize,$No);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+25,$YPos,38,$FontSize,$myrow2['emp_id']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+60,$YPos,150,$FontSize,'- '.$myrow2['emp_fname'].' '.$myrow2['emp_mname'].' '.$myrow2['emp_lname']);
                 $LeftOvers = $pdf->addTextWrap($Left_Margin+242,$YPos,95,$FontSize,$myrow2['id_number']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+295,$YPos,95,$FontSize,$myrow2['emp_gen']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+330,$YPos,125,$FontSize,ucwords(strtolower($myrow2['description'])));
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+460,$YPos,95,$FontSize,ucwords(strtolower($myrow2['section_name'])));
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+570,$YPos,95,$FontSize,$myrow2['band']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+600,$YPos,95,$FontSize,ucwords(strtolower($myrow2['appointment_name'])));
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+700,$YPos,95,$FontSize,$myrow2['grade']);
				$YPos -= ($line_height);
				
				$No++;

				if ($YPos <= $Bottom_Margin) {

						/* head up a new invoice/credit note page */
						/*draw the vertical column lines right to the bottom */
						PrintLinesToBottom ();
					include('includes/PDFEmployeeHeader.php');
					} //end if need a new page headed up

				} //end while there invoice are line items to print out
			} /*end if there are stock movements to show on the invoice or credit note*/

			$YPos -= $line_height;

			/* check to see enough space left to print the 4 lines for the totals/footer */
			if (($YPos-$Bottom_Margin)<(2*$line_height)) {
				PrintLinesToBottom ();
				include('includes/PDFEmployeeHeader.php');
			}
			/* Print a column vertical line  with enough space for the footer */
			/* draw the vertical column lines to 4 lines shy of the bottom to leave space for invoice footer info ie totals etc */
			
			/* Print a column vertical line */
			$pdf->line($Left_Margin+240, $TopOfColHeadings+12,$Left_Margin+240,$Bottom_Margin);
            /* Print a column vertical line */
			$pdf->line($Left_Margin+295, $TopOfColHeadings+12,$Left_Margin+295,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+330, $TopOfColHeadings+12,$Left_Margin+330,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+460, $TopOfColHeadings+12,$Left_Margin+460,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+570, $TopOfColHeadings+12,$Left_Margin+570,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+600, $TopOfColHeadings+12,$Left_Margin+600,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+700, $TopOfColHeadings+12,$Left_Margin+700,$Bottom_Margin);

			/* Print a column vertical line */
		$pdf->OutputI($_SESSION['DatabaseName'] . '_EmployeeReport_.pdf');
	$pdf->__destruct();
	//Now change the language back to the user's language

function PrintLinesToBottom () {

	global $pdf;
	global $PageNumber;
	global $TopOfColHeadings;
	global $Left_Margin;
	global $Bottom_Margin;
	global $line_height;

			$pdf->line($Left_Margin+240, $TopOfColHeadings+12,$Left_Margin+240,$Bottom_Margin);
            /* Print a column vertical line */
			$pdf->line($Left_Margin+295, $TopOfColHeadings+12,$Left_Margin+295,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+330, $TopOfColHeadings+12,$Left_Margin+330,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+460, $TopOfColHeadings+12,$Left_Margin+460,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+570, $TopOfColHeadings+12,$Left_Margin+570,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+600, $TopOfColHeadings+12,$Left_Margin+600,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+700, $TopOfColHeadings+12,$Left_Margin+700,$Bottom_Margin);			
			$PageNumber++;
}