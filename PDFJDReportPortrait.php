<?php
/*	$Id: PDFQuotationPortrait.php 4491 2011-02-15 06:31:08Z daintree $ */

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/
ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');



/*retrieve the order details from the database to print */

/* Then there's an order to print and its not been printed already (or its been flagged for reprinting/ge_Width=807;
)
LETS GO */
$PaperSize = 'A4';
include('includes/PDFStarter.php');
/*$PageNumber = 1;// RChacon: PDFStarter.php sets $PageNumber = 0.*/
$pdf->addInfo('Title', _('Job Description Report') );
$pdf->addInfo('Subject', _('Job Description Report') );
$FontSize = 12;
$line_height = 12;// Recommended: $line_height = $x * $FontSize.

/* Now ... Has the order got any line items still outstanding to be invoiced */

$ErrMsg = _('There was a problem retrieving the Job Description Report line details for Job Description Report Number') . ' ' .
	$_GET['id'] . ' ' . _('from the database');

$ErrMsg = _('There was a problem retrieving the Job Description details for this position code Number') . ' ' . $_GET['id'] . ' ' . _('from the database');
$sql = "SELECT *,a.position_code as id FROM job_details a
							INNER JOIN appointment g ON a.title = g.id
							WHERE a.position_code='" . $_GET['id'] . "'";
						
						
$ben ="SELECT *,a.position_code as id FROM job_details a
                            INNER JOIN job_benefits b ON a.position_code = b.position_code
                            WHERE a.position_code='" . $_GET['id'] . "' ";
$skill ="SELECT *,a.position_code as id FROM job_details a
					     	INNER JOIN job_skills c ON a.position_code = c.position_code
							WHERE a.position_code='" . $_GET['id'] . "'";
$cond ="SELECT *,a.position_code as id FROM job_details a
							INNER JOIN job_conditions d ON a.position_code = d.position_code
							WHERE a.position_code='" . $_GET['id'] . "'";
$req ="SELECT *,a.position_code as id FROM job_details a
							INNER JOIN job_requirements e ON a.position_code = e.position_code
							WHERE a.position_code='" . $_GET['id'] . "' ";
$duty ="SELECT *,a.position_code as id FROM job_details a
							INNER JOIN job_duties f ON a.position_code = f.position_code
							WHERE a.position_code='" . $_GET['id'] . "'";
							

$result=DB_query($sql, $ErrMsg);
$duties=DB_query($duty, $ErrMsg);
$skills=DB_query($skill, $ErrMsg);
$requirement=DB_query($req, $ErrMsg);
$benefit=DB_query($ben, $ErrMsg);
$condition=DB_query($cond, $ErrMsg);

$ListCount = 0;

if (DB_num_rows($result)>0){
	/*Yes there are line items to start the ball rolling with a page header */
	include('PDFJDReportHeader.php');

	while ($myrow2=DB_fetch_array($result)){

        $ListCount ++;

		$YPos -= $line_height;// Increment a line down for the next line item.

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFJDReportHeader.php');
		} //end if need a new page headed up

		$FontSize = 10;// Font size for the line item.
		$LeftOvers = $pdf->addText($Left_Margin+20, $YPos-$FontSize*0, 13, _('Title:'),'right');
		$LeftOvers = $pdf->addText($Left_Margin+50, $YPos-$FontSize*0-3, $FontSize, $myrow2['appointment_name']);
		$LeftOvers = $pdf->addText($Left_Margin+20, $YPos-$FontSize*2, 13,_('Department:'),'right');
		$LeftOvers = $pdf->addText($Left_Margin+90, $YPos-$FontSize*2-3, $FontSize, $myrow2['department']);
		$LeftOvers = $pdf->addText($Left_Margin+20, $YPos-$FontSize*4, 13, _('Grade:'),'right');
		$LeftOvers = $pdf->addText($Left_Margin+60, $YPos-$FontSize*4-3, $FontSize, $myrow2['grade']);
		$LeftOvers = $pdf->addText($Left_Margin+20, $YPos-$FontSize*6, 13,_('Reports To:'),'right');
		$LeftOvers = $pdf->addText($Left_Margin+90, $YPos-$FontSize*6-3, $FontSize, $myrow2['manager']);



	}// Ends while there are line items to print out.
	$LeftOvers = $pdf->addText($Left_Margin+20, $YPos-$FontSize*9, 13, _('Duties & Responsibilities'),'center');
	$i=1;
	while ($myrow2=DB_fetch_array($duties)){

        $ListCount ++;

		$YPos -= $line_height;// Increment a line down for the next line item.

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFJDReportHeader.php');
		} //end if need a new page headed up

		//$FontSize = 10;// Font size for the line item.
        
		$LeftOvers = $pdf->addText($Left_Margin+20, $YPos-$FontSize*10, $FontSize, $i.'.'.' '.$myrow2['duty']);
		$i++;



	}// Ends while there are line items to print out.
	$LeftOvers = $pdf->addText($Left_Margin+20, $YPos-$FontSize*13, 13, _('Requirements'),'center');
	$i=1;
	while ($myrow2=DB_fetch_array($requirement)){

        $ListCount ++;
        
		$YPos -= $line_height;// Increment a line down for the next line item.

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFJDReportHeader.php');
		} //end if need a new page headed up

		//$FontSize = 10;// Font size for the line item.
         
		$LeftOvers = $pdf->addText($Left_Margin+20, $YPos-$FontSize*14, $FontSize,$i.'.'.' '.$myrow2['requirement']);
		$i++;

	}// Ends while there are line items to print out.
	

	}// Ends while there are line items to print out.
	$LeftOvers = $pdf->addText($Left_Margin+20, $YPos-$FontSize*17, 13, _(' Skills'),'center');
	$i=1;
	while ($myrow2=DB_fetch_array($skills)){

        $ListCount ++;

		$YPos -= $line_height;// Increment a line down for the next line item.

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFJDReportHeader.php');
		} //end if need a new page headed up

		//$FontSize = 10;// Font size for the line item.
        
		//$LeftOvers = $pdf->addText($Left_Margin+20, $YPos-$FontSize*18, $FontSize,<img src="images/bullet_black.png" alt="bullet" />;
		$LeftOvers = $pdf->addText($Left_Margin+25, $YPos-$FontSize*18, $FontSize, $i.'.'.' '.$myrow2['skills']);
		$i++;



	}// Ends while there are line items to print out.

$LeftOvers = $pdf->addText($Left_Margin+20, $YPos-$FontSize*21, 13, _('Work Conditions'));
$i=1;
	while ($myrow2=DB_fetch_array($condition)){

        $ListCount ++;

		$YPos -= $line_height;// Increment a line down for the next line item.

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFJDReportHeader.php');
		} //end if need a new page headed up

		//$FontSize = 10;// Font size for the line item.
        
		$LeftOvers = $pdf->addText($Left_Margin+20, $YPos-$FontSize*22, $FontSize, $i.'.'.' '.$myrow2['condition']);
		$i++;



	}// Ends while there are line items to print out.

$LeftOvers = $pdf->addText($Left_Margin+20, $YPos-$FontSize*25, 13, _('Benefits'),'center');
$i=1;
	while ($myrow2=DB_fetch_array($benefit)){

        $ListCount ++;

		$YPos -= $line_height;// Increment a line down for the next line item.

		if ($YPos-$line_height <= 50){
		/* We reached the end of the page so finsih off the page and start a newy */
			include('PDFJDReportHeader.php');
		} //end if need a new page headed up

		//$FontSize = 10;// Font size for the line item.
        
		$LeftOvers = $pdf->addText($Left_Margin+20, $YPos-$FontSize*26, $FontSize, $i.'.'.' '.$myrow2['benefits']);
		$i++;



	}// Ends while there are line items to print out.



	if ($YPos-$line_height <= 50){
	/* We reached the end of the page so finsih off the page and start a newy */
		include('PDFJDReportHeader.php');
	} //end if need a new page headed up



if ($ListCount == 0){
        $Title = _('Print Job Description Report Error');
        include('includes/header.inc');
        echo '<p>' .  _('There were no Job Description Reports') . '. ' . _('The Job Description Report cannot be printed').
                '<br /><a href="index.php?Application=HR&Ref=SLReports">' .  _('Print Another Job Description Report');
        include('includes/footer.inc');
	exit;
} else {
    $pdf->OutputI($_SESSION['DatabaseName'] . '_Job Description Report_'  . '_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();
}
ob_end_flush();
?>
