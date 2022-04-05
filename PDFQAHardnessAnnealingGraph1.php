<?php

/* $Id: PrintCustTrans.php 7124 2015-02-05 02:12:56Z vvs2012 $ */

include('includes/session.inc');

$ViewTopic = 'ARReports';
$BookMark = 'PrintRequisition';

if (isset($_GET['id'])) {
$id = $_GET['id'];
}else{
prnMsg( _('There was a problem retrieving the details'),'error');
}

	include ('includes/class.pdf.php');

	/* This invoice is hard coded for A4 Landscape invoices or credit notes so can't use PDFStarter.inc */

	$Page_Width=595;
	$Page_Height=842;
	$Top_Margin=30;
	$Bottom_Margin=30;
	$Left_Margin=40;
	$Right_Margin=30;


	$pdf = new Cpdf('P', 'pt', 'A4');
	$pdf->addInfo('Creator', 'Berkley http://www.berkley.co.ke');
	$pdf->addInfo('Author', 'Kalfrique ' . $Version);

		$title ='Hardness Annealing Graph';
		$subj ='Hardness Annealing Graph';
		
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

			$sql="SELECT d.id,
			a.sheetname,
			b.typename,
			c.operation,
			a.max_limit,
			a.min_limit,
			a.description,
			d.`brasslot`,
			d.`machineno`,
			d.`shift`,
			d.`date`,
			d.`technician`
			FROM qarecordingsheet a
			INNER JOIN qaoperationtype b ON b.id=a.typeid
			INNER JOIN qaoperation c ON c.id=a.operationid
			INNER JOIN qaannealinghardness d ON d.sheetid=a.id
			WHERE d.id='" . $id . "'";

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

				$FontSize = 10;
				$PageNumber = 1;

				include('includes/PDFQAHardnessAnnealingHeader.php');
				$FirstPage = False;

				$pdf->addJpegFromFile('companies/' .$_SESSION['DatabaseName'] .  '/reports/hardnessgraph_'.$id.'.png',$Left_Margin+5,$YPos-350,0,350);

			$FontSize = 10;
			
					$sql = "SELECT *
		FROM qaannealingremarks
		WHERE refid='" . $_GET['id'] . "' ORDER BY approver ASC";

$result=DB_query($sql, $ErrMsg);

$ListCount = 0;
$YPos -= 370;// Increment a line down for the next line item.
$pdf->line($Page_Width-$Right_Margin, $YPos+$FontSize, $Left_Margin, $YPos+$FontSize);
$YPos += 3;
if (DB_num_rows($result)>0){
	while ($myrow2=DB_fetch_array($result)){

        $ListCount ++;
		$YPos -= $line_height;// Increment a line down for the next line item.

		$FontSize = 10;// Font size for the line item.
		
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,220,$FontSize, strtoupper($myrow2['approvertitle']).' ('.ucwords(strtolower($myrow2['approvername'])).')', 'left');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+220,$YPos+$FontSize,250,$FontSize, ucwords(strtolower($myrow2['remarks'])));

		$pdf->SetFont('','B');
		//$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,250,8, ucwords(strtolower($myrow2['approvername'])));
		$LeftOvers = $pdf->addTextWrap($Left_Margin+420,$YPos+$FontSize,100,8, ConvertSQLDateTime($myrow2['remarkdate']),'right');
		$pdf->SetFont('','');

	}// Ends while there are line items to print out.
	}	
	
	$LeftOvers = $pdf->addTextWrap($Left_Margin+650,$YPos+$FontSize,100,8, 'HARDNESS','right');	

		$pdf->OutputI($_SESSION['DatabaseName'] . '_HardnessGraph_'. $id .'.pdf');

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

