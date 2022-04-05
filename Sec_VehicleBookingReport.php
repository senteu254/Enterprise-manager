<?php

/* $Id: InventoryPlanning.php 6963 2014-11-06 03:06:03Z tehonu $ */

include('includes/session.inc');
/* webERP manual links before header.inc */
$ViewTopic= "Inventory";
$BookMark = "PlanningReport";

include ('includes/SQL_CommonFunctions.inc');
$_POST['Gate'] = $_GET['Gate'];
$_POST['datefrom'] = $_GET['From'];
$_POST['dateto'] = $_GET['To'];
//if (isset($_POST['PrintPDF'])) {

	include ('includes/class.pdf.php');

	/* A4_Landscape */

	$Page_Width=842;
	$Page_Height=595;
	$Top_Margin=20;
	$Bottom_Margin=20;
	$Left_Margin=25;
	$Right_Margin=22;

// Javier: now I use the native constructor
//	$PageSize = array(0,0,$Page_Width,$Page_Height);

/* Standard PDF file creation header stuff */

// Javier: better to not use references
//	$pdf = & new Cpdf($PageSize);
	$pdf = new Cpdf('L', 'pt', 'A4');
	$pdf->addInfo('Creator','webERP http://www.weberp.org');
	$pdf->addInfo('Author','webERP ' . $Version);
	$pdf->addInfo('Title',_('Vehicles Booking Report') . ' ' . Date($_SESSION['DefaultDateFormat']));
	$pdf->addInfo('Subject',_('Vehicles Booking'));

/* Javier: I have brought this piece from the pdf class constructor to get it closer to the admin/user,
	I corrected it to match TCPDF, but it still needs some check, after which,
	I think it should be moved to each report to provide flexible Document Header and Margins in a per-report basis. */
	$pdf->setAutoPageBreak(0);	// Javier: needs check.
	$pdf->setPrintHeader(false);	// Javier: I added this must be called before Add Page
	$pdf->AddPage();
//	$this->SetLineWidth(1); 	   Javier: It was ok for FPDF but now is too gross with TCPDF. TCPDF defaults to 0'57 pt (0'2 mm) which is ok.
	$pdf->cMargin = 0;		// Javier: needs check.
/* END Brought from class.pdf.php constructor */

// Javier:
	$PageNumber = 1;
	$line_height = 12;

      /*Now figure out the inventory data to report for the category range under review
      need QOH, QOO, QDem, Sales Mth -1, Sales Mth -2, Sales Mth -3, Sales Mth -4*/
	if ($_POST['Gate']=='All'){
		$SQL = "SELECT *,d.description as dept, c.description as gate
					FROM vehicle_register a
					INNER JOIN vehicle_timein b ON a.VehicleNo=b.VehicleNo
					INNER JOIN gates c ON b.GateID=c.GateID
					INNER JOIN departments d ON a.departmentid=d.departmentid
					WHERE DATE_FORMAT(b.time_in,'%Y-%m-%d') >='".FormatDateForSQL($_POST['datefrom'])."' AND DATE_FORMAT(b.time_in,'%Y-%m-%d') <='".FormatDateForSQL($_POST['dateto'])."'
					ORDER BY b.time_in DESC";
	} else {
		$SQL = "SELECT *,d.description as dept
					FROM vehicle_register a
					INNER JOIN vehicle_timein b ON a.VehicleNo=b.VehicleNo
					INNER JOIN departments d ON a.departmentid=d.departmentid
					WHERE DATE_FORMAT(b.time_in,'%Y-%m-%d') >='".FormatDateForSQL($_POST['datefrom'])."' AND DATE_FORMAT(b.time_in,'%Y-%m-%d') <='".FormatDateForSQL($_POST['dateto'])."' AND b.GateID=".$_POST['Gate']."
					ORDER BY b.time_in DESC";
					
		$gate="SELECT description FROM gates WHERE GateID=".$_POST['Gate']."";
		$RGate = DB_query($gate, '', '', false, false);
		$gate = DB_fetch_array($RGate);
		

	}
	$Result = DB_query($SQL, '', '', false, false);

	if (DB_error_no() !=0) {
	  $Title = _('Vehicles Booking') . ' - ' . _('Problem Report') . '....';
	  include('includes/header.inc');
	   prnMsg(_('The Vehicles Booking could not be retrieved by the SQL because') . ' - ' . DB_error_msg(),'error');
	   echo '<br /><a href="' .$RootPath .'/index.php">' . _('Back to the menu') . '</a>';
	   if ($debug==1){
	      echo '<br />' . $SQL;
	   }
	   include('includes/footer.inc');
	   exit;
	}
		$ListCount = 0;
	include ('includes/PDFVehicleReportPageHeader.inc');

	while ($rows = DB_fetch_array($Result)){
	
	if($rows['time_in']!=""){
	$timein = date('d M Y Hm',strtotime($rows['time_in'])).'Hrs';
	}else{
	$timein = '';
	}
	if($rows['check_out']==1){
	$timeout = date('d M Y Hm',strtotime($rows['time_out'])).'Hrs';
	}else{
	$timeout = '';
	}

		$YPos -=$line_height;
		$LeftOvers = $pdf->addTextWrap($Left_Margin, $YPos, 25, $FontSize, $ListCount+1, 'left');
		$LeftOvers = $pdf->addTextWrap(50, $YPos, 120,$FontSize,$rows['DriverName'],'left');
		$LeftOvers = $pdf->addTextWrap(170, $YPos, 40,$FontSize,$rows['IdNo'],'left');
		$LeftOvers = $pdf->addTextWrap(240, $YPos, 130,$FontSize,$rows['Org'],'left');
		$LeftOvers = $pdf->addTextWrap(370, $YPos, 90,$FontSize,strtoupper($rows['RegNo']),'left');
		$LeftOvers = $pdf->addTextWrap(440, $YPos, 80,$FontSize,ucwords(strtolower($rows['Make'])),'left');
		$LeftOvers = $pdf->addTextWrap(520, $YPos, 100,$FontSize,$rows['remarks'],'left');
		$LeftOvers = $pdf->addTextWrap(620, $YPos, 100,$FontSize,$timein,'left');
		$LeftOvers = $pdf->addTextWrap(720, $YPos, 100,$FontSize,$timeout,'left');
		$YPos -= ($line_height);
		if ($_POST['Gate']=='All'){
		$LeftOvers = $pdf->addTextWrap(50, $YPos, 260,$FontSize,'Gate : '.$rows['gate'],'left');
		}
		$LeftOvers = $pdf->addTextWrap(170, $YPos, 260,$FontSize,'Department : '.$rows['dept'],'left');
		$LeftOvers = $pdf->addTextWrap(440, $YPos, 280,$FontSize,'Destination : '.$rows['Destination'],'left');
		$LeftOvers = $pdf->addTextWrap(620, $YPos, 60,$FontSize,$rows['sec_officer'],'left');
		$LeftOvers = $pdf->addTextWrap(720, $YPos, 80,$FontSize,$rows['sec_officer_out'],'left');
		$YPos -= ($line_height);
		$pdf->line($Left_Margin, $YPos,$Page_Width-$Right_Margin, $YPos);
		if ($YPos < $Bottom_Margin + $line_height){
		   $PageNumber++;
		   include('includes/PDFVehicleReportPageHeader.inc');
		}
		$ListCount++;
	} /*end inventory valn while loop */

	$YPos -= (2*$line_height);

	//$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height);

	if ($ListCount == 0){
		$_SESSION['errmsg'] = _('There were no Vehicles Booking in the range specified');
		?>
								<script>
								window.location.href = "index.php?Application=SEC2&Link=Reports&Rep=Vehicles";
								</script>
							<?php
		exit;
	} else {
		$pdf->OutputD($_SESSION['DatabaseName'] . '_Vehicles_Booking_' . Date('Y-m-d') . '.pdf');
		$pdf-> __destruct();
	}

/*} else { //The option to print PDF was not hit 

	$Title=_('Vehicles Booking Reporting');
	include('includes/header.inc');

	echo '<p class="page_title_text">
			<img src="'.$RootPath.'/css/'.$Theme.'/images/inventory.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p>';


	echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
	echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table class="selection">';
	
	echo '<tr>
			<td>' . _('Gate') . ':</td>
			<td><select name="Gate">';

	$sql = "SELECT * FROM gates";
	$LocnResult=DB_query($sql);

	echo '<option value="All">' . _('All Gates') . '</option>';

	while ($myrow=DB_fetch_array($LocnResult)){
		echo '<option value="' . $myrow['GateID'] . '">' . $myrow['description'] . '</option>';
	}
	echo '</select>
			</td>
		</tr>';

	echo '<tr>
			<td>' . _('Date From') . ':</td>
			<td>
			<input type="text" required="required" class="date" alt="' .$_SESSION['DefaultDateFormat'] . '" name="datefrom" maxlength="10" size="10" value="' . Date($_SESSION['DefaultDateFormat']) . '" />
			</td>
	</tr>
	<tr>
			<td>' . _('Date To') . ':</td>
			<td>
			<input type="text" required="required" class="date" alt="' .$_SESSION['DefaultDateFormat'] . '" name="dateto" maxlength="10" size="10" value="' . Date($_SESSION['DefaultDateFormat']) . '" />
			</td>
	</tr>
	</table>
	<br />
	<div class="centre">
		<input type="submit" name="PrintPDF" value="' . _('Print PDF') . '" />
	</div>
	</div>
	</form>';

	include('includes/footer.inc');

} //end of else not PrintPDF 
*/
?>