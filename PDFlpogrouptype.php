<?php
/* $Id: PrintCustTrans.php 7124 2015-02-05 02:12:56Z vvs2012 $ */

include('includes/session.inc');
$Title = _('Supplier Report');
$ViewTopic = 'ARReports';
$BookMark = 'PrintSupplier Report';

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
	$pdf->addInfo('Author', 'berkley ' . $Version);

		
		$title ='supplier report';
		$subj ='supplier';
		
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
	$_POST['OrderType']=$_GET['type'];
	  $DateFrom=$_GET['datef'];
	 $DateTo=$_GET['datet'];
	
	/* retrieve the invoice details from the database to print
	notice that salesorder record must be present to print the invoice purging of sales orders will
	nobble the invoice reprints */
    if (isset($_POST['StockCat']) && $_POST['StockCat']=='All'){
	$group="";
	}else{
	$group="AND suppliergrouptype.groupid='" . $_POST['StockCat'] . "'";
	}
	if ($_POST['OrderType'] == 'LPO') {
				  $SQL="SELECT purchorders.realorderno,
							purchorders.orderno,
							suppliers.suppname,
							purchorders.orddate,
							purchorders.deliverydate,
							purchorders.status,
							purchorders.initiator,
							purchorders.requisitionno,
							purchorders.allowprint,
							suppliers.currcode,
							currencies.decimalplaces AS currdecimalplaces,
							SUM(purchorderdetails.unitprice*purchorderdetails.quantityord) AS ordervalue
						FROM purchorders 
						INNER JOIN purchorderdetails ON purchorders.orderno = purchorderdetails.orderno
						INNER JOIN suppliers ON  purchorders.supplierno = suppliers.supplierid
						INNER JOIN currencies ON suppliers.currcode=currencies.currabrev
						INNER JOIN suppliergrouptype  ON suppliers.suppliergroup=suppliergrouptype.groupid
						WHERE purchorders.orddate>='" . $DateFrom . "'
					    AND purchorders.orddate<='" . $DateTo . "'
						".$group."
						GROUP BY purchorders.orderno ASC,
							purchorders.realorderno,
							suppliers.suppname,
							purchorders.orddate,
							purchorders.status,
							purchorders.initiator,
							purchorders.requisitionno,
							purchorders.allowprint,
							suppliers.currcode,
							currencies.decimalplaces
							";
					}elseif ($_POST['OrderType'] == 'LSO') {
					 $SQL="SELECT lsorders.realorderno,
							lsorders.orderno,
							suppliers.suppname,
							lsorders.orddate,
							lsorders.deliverydate,
							lsorders.status,
							lsorders.initiator,
							lsorders.requisitionno,
							lsorders.allowprint,
							suppliers.currcode,
							currencies.decimalplaces AS currdecimalplaces,
							SUM(lsorderdetails.unitprice*lsorderdetails.quantityord) AS ordervalue
						FROM lsorders 
						INNER JOIN lsorderdetails ON lsorders.orderno = lsorderdetails.orderno
						INNER JOIN suppliers ON  lsorders.supplierno = suppliers.supplierid
						INNER JOIN currencies ON suppliers.currcode=currencies.currabrev
						INNER JOIN suppliergrouptype  ON suppliers.suppliergroup=suppliergrouptype.groupid
						WHERE lsorders.orddate>='" . $DateFrom . "'
					    AND lsorders.orddate<='" . $DateTo . "'
						".$group."
						GROUP BY lsorders.orderno ASC,
							lsorders.realorderno,
							suppliers.suppname,
							lsorders.orddate,
							lsorders.status,
							lsorders.initiator,
							lsorders.requisitionno,
							lsorders.allowprint,
							suppliers.currcode,
							currencies.decimalplaces
							";
					}
			$result=DB_query($SQL);
			if (DB_error_no()!=0 OR DB_num_rows($result)==0){

				$Title = _('Transaction Print Error Report');
				include ('includes/header.inc');
				echo '<br />' . _('There was a problem retrieving the information from the database');
				if ($debug==1) {
					echo '<br />' . _('The SQL used to get this information that failed was') . '<br />' . $SQL;
				}
				include('includes/footer.inc');
				exit;
			} else {
				$FontSize = 10;
				$PageNumber = 1;
				include('includes/PDFlpogrouptypeheader.php');
				$FirstPage = False;
				$i=0;
				$total=0;
				
				while ($myrow2=DB_fetch_array($result)){
				$FormatedOrderDate = ConvertSQLDate($myrow2['orddate']);
				$FormatedDeliveryDate = ConvertSQLDate($myrow2['deliverydate']);
				$FormatedOrderValue = locale_number_format($myrow2['ordervalue'], $myrow2['currdecimalplaces']);
				 $total+=1;
                $i++;
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+5,$YPos,300,$FontSize,$i);
                 $LeftOvers = $pdf->addTextWrap($Left_Margin+50,$YPos,300,$FontSize,$myrow2['orderno']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+121,$YPos,300,$FontSize,$FormatedOrderDate);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+201,$YPos,300,$FontSize,$FormatedDeliveryDate);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+281,$YPos,300,$FontSize,$myrow2['suppname']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+581,$YPos,300,$FontSize,$myrow2['currcode']);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+660,$YPos,300,$FontSize,$FormatedOrderValue);
				 $LeftOvers = $pdf->addTextWrap($Left_Margin+721,$YPos,300,$FontSize,$myrow2['status']);
				
				$YPos -= ($line_height);

				if ($YPos <= $Bottom_Margin) {

						/* head up a new invoice/credit note page */
						/*draw the vertical column lines right to the bottom */
						PrintLinesToBottom ();
					include('includes/PDFlpogrouptypeheader.php');
					} //end if need a new page headed up

				} //end while there invoice are line items to print out
			} /*end if there are stock movements to show on the invoice or credit note*/

			$YPos -= $line_height;

			/* check to see enough space left to print the 4 lines for the totals/footer */
			if (($YPos-$Bottom_Margin)<(2*$line_height)) {
				PrintLinesToBottom ();
				include('includes/PDFlpogrouptypeheader.php');
			}
			/* Print a column vertical line  with enough space for the footer */
			/* draw the vertical column lines to 4 lines shy of the bottom to leave space for invoice footer info ie totals etc */
			
			/* Print a column vertical line */
			$pdf->line($Left_Margin+35, $TopOfColHeadings+16,$Left_Margin+35,$Bottom_Margin);
            /* Print a column vertical line */
			$pdf->line($Left_Margin+120, $TopOfColHeadings+16,$Left_Margin+120,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+200, $TopOfColHeadings+16,$Left_Margin+200,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+280, $TopOfColHeadings+16,$Left_Margin+280,$Bottom_Margin);
				/* Print a column vertical line */
			$pdf->line($Left_Margin+568, $TopOfColHeadings+16,$Left_Margin+568,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+630, $TopOfColHeadings+16,$Left_Margin+630,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+710, $TopOfColHeadings+16,$Left_Margin+710,$Bottom_Margin);
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
			//$pdf->line($Page_Width-$Right_Margin-771, $YPos-(2*$line_height)+10,$Page_Width-$Right_Margin,$YPos-(2*$line_height)+10);
			/*vertical to separate totals from comments and ROMALPA  added by langat pete*/
			//$pdf->line($Page_Width-$Right_Margin-77, $YPos+$line_height,$Page_Width-$Right_Margin-77,$Bottom_Margin);

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
				if ($_POST['OrderType'] == 'LPO') {
		if ($_POST['StockCat'] == 'All') {
		$Supp="SELECT c.groupname,a.orddate, COUNT(a.supplierno) AS total
		      FROM  purchorders a
			  INNER JOIN suppliers b ON a.supplierno=b.supplierid
			  INNER JOIN suppliergrouptype c ON b.suppliergroup=c.groupid	
			  WHERE a.orddate>='" . $DateFrom . "'
			  AND a.orddate<='" . $DateTo . "'	  
			  GROUP BY c.groupid";
			}else{
		$Supp="SELECT c.groupname,a.orddate, COUNT(a.supplierno) AS total
		      FROM  purchorders a
			  INNER JOIN suppliers b ON a.supplierno=b.supplierid
			  INNER JOIN suppliergrouptype c ON b.suppliergroup=c.groupid
	       	  WHERE c.groupid='" . $_POST['StockCat'] . "'		  
			  AND a.orddate>='" . $DateFrom . "'
			  AND a.orddate<='" . $DateTo . "'	
			  GROUP BY c.groupid";
		}
			 }elseif ($_POST['OrderType'] == 'LSO') {
		if ($_POST['StockCat'] == 'All') {
		 $Supp="SELECT c.groupname,a.orddate, COUNT(a.supplierno) AS total
		      FROM  lsorders a
			  INNER JOIN suppliers b ON a.supplierno=b.supplierid
			  INNER JOIN suppliergrouptype c ON b.suppliergroup=c.groupid
			  WHERE a.orddate>='" . $DateFrom . "'
			  AND a.orddate<='" . $DateTo . "'	
			  GROUP BY c.groupid";
			 }else {
			 $Supp="SELECT c.groupname,a.orddate, COUNT(a.supplierno) AS total
		      FROM  lsorders a
			  INNER JOIN suppliers b ON a.supplierno=b.supplierid
			  INNER JOIN suppliergrouptype c ON b.suppliergroup=c.groupid
			  WHERE a.orddate>='" . $DateFrom . "'
			  AND a.orddate<='" . $DateTo . "'	
			  AND c.groupid='" . $_POST['StockCat'] . "'				  
			  GROUP BY c.groupid";
			 }
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

		$pdf->line($Left_Margin+35, $TopOfColHeadings+16,$Left_Margin+35,$Bottom_Margin);
            /* Print a column vertical line */
			$pdf->line($Left_Margin+120, $TopOfColHeadings+16,$Left_Margin+120,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+200, $TopOfColHeadings+16,$Left_Margin+200,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+280, $TopOfColHeadings+16,$Left_Margin+280,$Bottom_Margin);
				/* Print a column vertical line */
			$pdf->line($Left_Margin+568, $TopOfColHeadings+16,$Left_Margin+568,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+630, $TopOfColHeadings+16,$Left_Margin+630,$Bottom_Margin);
			/* Print a column vertical line */
			$pdf->line($Left_Margin+710, $TopOfColHeadings+16,$Left_Margin+710,$Bottom_Margin);
			/* Print a column vertical line */
			//$pdf->line($Left_Margin+695, $TopOfColHeadings+12,$Left_Margin+695,$Bottom_Margin+(0.0*$line_height));			
			$PageNumber++;
}