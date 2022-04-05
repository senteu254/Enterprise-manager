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
$_POST['Report'] = $_GET['Report'];
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
	$pdf->addInfo('Title',_('Material Booking Report') . ' ' . Date($_SESSION['DefaultDateFormat']));
	$pdf->addInfo('Subject',_('Material Booking'));

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
	  if ($_POST['Report']=='Visitors'){
	if ($_POST['Gate']=='All'){
		$SQL = "SELECT *, b.purpose as purp, c.description as gates
					FROM visitor_register a
					INNER JOIN visitor_material_register b ON a.VisitorNo=b.visitorid
					INNER JOIN gates c ON b.gate=c.GateID
					WHERE DATE_FORMAT(b.booked_in_time,'%Y-%m-%d') >='".FormatDateForSQL($_POST['datefrom'])."' AND DATE_FORMAT(b.booked_in_time,'%Y-%m-%d') <='".FormatDateForSQL($_POST['dateto'])."'";
	} else {
		$SQL = "SELECT *, b.purpose as purp
					FROM visitor_register a
					INNER JOIN visitor_material_register b ON a.VisitorNo=b.visitorid
					WHERE DATE_FORMAT(b.booked_in_time,'%Y-%m-%d') >='".FormatDateForSQL($_POST['datefrom'])."' AND DATE_FORMAT(b.booked_in_time,'%Y-%m-%d') <='".FormatDateForSQL($_POST['dateto'])."' AND b.gate=".$_POST['Gate']."";
					
		$gate="SELECT description FROM gates WHERE GateID=".$_POST['Gate']."";
		$RGate = DB_query($gate, '', '', false, false);
		$gate = DB_fetch_array($RGate);
		

	}
	}else{
	if ($_POST['Gate']=='All'){
		$SQL = "SELECT *, CONCAT_WS(' ',emp_fname,emp_lname) AS v_name, id_number AS v_idno, b.purpose as purp, c.description as gates
					FROM employee a
					INNER JOIN staff_material_register b ON a.emp_id=b.staffid
					INNER JOIN gates c ON b.gate=c.GateID
					WHERE DATE_FORMAT(b.booked_in_time,'%Y-%m-%d') >='".FormatDateForSQL($_POST['datefrom'])."' AND DATE_FORMAT(b.booked_in_time,'%Y-%m-%d') <='".FormatDateForSQL($_POST['dateto'])."'";
	} else {
		$SQL = "SELECT *, CONCAT_WS(' ',emp_fname,emp_lname) AS v_name, id_number AS v_idno, b.purpose as purp
					FROM employee a
					INNER JOIN staff_material_register b ON a.emp_id=b.staffid
					WHERE DATE_FORMAT(b.booked_in_time,'%Y-%m-%d') >='".FormatDateForSQL($_POST['datefrom'])."' AND DATE_FORMAT(b.booked_in_time,'%Y-%m-%d') <='".FormatDateForSQL($_POST['dateto'])."' AND b.gate=".$_POST['Gate']."";
					
		$gate="SELECT description FROM gates WHERE GateID=".$_POST['Gate']."";
		$RGate = DB_query($gate, '', '', false, false);
		$gate = DB_fetch_array($RGate);
		

	}
	}
	$Result = DB_query($SQL, '', '', false, false);

	if (DB_error_no() !=0) {
	  $Title = _('Material Booking') . ' - ' . _('Problem Report') . '....';
	  include('includes/header.inc');
	   prnMsg(_('The Material Booking could not be retrieved by the SQL because') . ' - ' . DB_error_msg(),'error');
	   echo '<br /><a href="' .$RootPath .'/index.php">' . _('Back to the menu') . '</a>';
	   if ($debug==1){
	      echo '<br />' . $SQL;
	   }
	   include('includes/footer.inc');
	   exit;
	}
		$ListCount = 0;
	include ('includes/PDFMaterialReportPageHeader.inc');

	while ($rows = DB_fetch_array($Result)){
	
	if($rows['booked_in_time']!=""){
	$timein = date('d M Y Hm',strtotime($rows['booked_in_time'])).'Hrs';
	}else{
	$timein = '';
	}
	if($rows['booked_out_time']!=""){
	$timeout = date('d M Y Hm',strtotime($rows['booked_out_time'])).'Hrs';
	}else{
	$timeout = '';
	}

		$YPos -=$line_height;
		$LeftOvers = $pdf->addTextWrap($Left_Margin, $YPos, 25, $FontSize, $ListCount+1, 'left');
		$LeftOvers = $pdf->addTextWrap(50, $YPos, 120,$FontSize,ucwords(strtolower($rows['v_name'])),'left');
		$LeftOvers = $pdf->addTextWrap(170, $YPos, 50,$FontSize,$rows['v_idno'],'left');
		$LeftOvers = $pdf->addTextWrap(220, $YPos, 80,$FontSize,ucwords(strtolower($rows['v_from'])),'left');
		$LeftOvers = $pdf->addTextWrap(300, $YPos, 120,$FontSize,ucwords(strtolower($rows['description'])),'left');
		$LeftOvers = $pdf->addTextWrap(420, $YPos, 80,$FontSize,$rows['destination'],'left');
		$LeftOvers = $pdf->addTextWrap(500, $YPos, 120,$FontSize,$rows['purp'],'left');
		$LeftOvers = $pdf->addTextWrap(620, $YPos, 100,$FontSize,$timein,'left');
		$LeftOvers = $pdf->addTextWrap(720, $YPos, 100,$FontSize,$timeout,'left');
		$YPos -= ($line_height);
		if ($_POST['Gate']=='All'){
		$LeftOvers = $pdf->addTextWrap(50, $YPos, 260,$FontSize,'Gate : '.$rows['gates'],'left');
		}
		$LeftOvers = $pdf->addTextWrap(620, $YPos, 100,$FontSize,ucwords(strtolower($rows['security_in'])),'left');
		$LeftOvers = $pdf->addTextWrap(720, $YPos, 100,$FontSize,ucwords(strtolower($rows['security_out'])),'left');
		$YPos -= ($line_height);
		$pdf->line($Left_Margin, $YPos,$Page_Width-$Right_Margin, $YPos);
		if ($YPos < $Bottom_Margin + $line_height){
		   $PageNumber++;
		   include('includes/PDFMaterialReportPageHeader.inc');
		}
		$ListCount++;
	} //end inventory valn while loop 

	$YPos -= (2*$line_height);

	//$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height);

	if ($ListCount == 0){
		$_SESSION['errmsg'] = _('There were no Material Booking in the range specified');
		?>
								<script>
								window.location.href = "index.php?Application=SEC2&Link=Reports&Rep=Materials";
								</script>
							<?php
		exit;
	} else {
		$pdf->OutputD($_SESSION['DatabaseName'] . '_Material_Booking_' . Date('Y-m-d') . '.pdf');
		$pdf-> __destruct();
	}

/*} else { //The option to print PDF was not hit *

	$Title=_('Material Booking Reporting');
	include('includes/header.inc');

	echo '<p class="page_title_text">
			<img src="'.$RootPath.'/css/'.$Theme.'/images/inventory.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p>';


	echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
	echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table class="selection">';
	
	echo '<tr>
			<td>' . _('Report') . ':</td>
			<td><select name="Report">';

	$array = array('Visitors', 'Staff');
	$array2 = array('Visitors Report', 'Staff Report');

	for ($i=0; $i < count($array); $i++){
		echo '<option value="' . $array[$i] . '">' . $array2[$i] . '</option>';
	}
	echo '</select>
			</td>
		</tr>';
		
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

} //end of else not PrintPDF*/

?>