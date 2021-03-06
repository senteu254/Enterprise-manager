<?php

/* $Id: PDFProdSpec.php 1 2014-09-15 06:31:08Z agaluski $ */

include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

if (isset($_GET['KeyValue']))  {
	$SelectedProdSpec=$_GET['KeyValue'];
} elseif (isset($_POST['KeyValue'])) {
	$SelectedProdSpec=$_POST['KeyValue'];
} else {
	$SelectedProdSpec='';
}
//Get Out if we have no product specification
If (!isset($SelectedProdSpec) OR $SelectedProdSpec==''){
        $Title = _('Select Product Specification To Print');
        include('includes/header.inc');
		echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/printer.png" title="' . _('Print')  . '" alt="" />' . ' ' . $Title . '</p>';
        echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') .  '" method="post">
		<div>
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
		<table class="selection">
		<tr>
			<td>' . _('Enter Specification Name') .':</td>
			<td><input type="text" name="KeyValue" size="25" maxlength="25" /></td>
		</tr>
		</table>
		</div>
		<div>
		<input type="submit" name="PickSpec" value="' . _('Submit') . '" />
		</div>
		</form>
		<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') .  '" method="post">
		<div>
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
		<table class="selection">
		<tr>
			<td>' . _('Or Select Existing Specification') .':</td>';
	$SQLSpecSelect="SELECT DISTINCT(keyval),
							description
						FROM prodspecs LEFT OUTER JOIN stockmaster
						ON stockmaster.stockid=prodspecs.keyval";


	$ResultSelection=DB_query($SQLSpecSelect);
	echo '<td><select name="KeyValue">';

	while ($MyRowSelection=DB_fetch_array($ResultSelection)){
		echo '<option value="' . $MyRowSelection['keyval'] . '">' . $MyRowSelection['keyval'].' - ' .htmlspecialchars($MyRowSelection['description'], ENT_QUOTES,'UTF-8', false)  . '</option>';
	}
	echo '</select></td>';
	echo '</tr>
		</table>
		</div>
		<div>
		<input type="submit" name="PickSpec" value="' . _('Submit') . '" />
		</div>
		</form>';
    include('includes/footer.inc');
    exit();
}

/*retrieve the order details from the database to print */
$ErrMsg = _('There was a problem retrieving the Product Specification') . ' ' . $SelectedProdSpec . ' ' . _('from the database');

$sql = "SELECT keyval,
				description,
				longdescription,
				prodspecs.testid,
				name,
				method,
				qatests.units,
				type,
				numericvalue,
				prodspecs.targetvalue,
				prodspecs.rangemin,
				prodspecs.rangemax,
				groupby
			FROM prodspecs INNER JOIN qatests
			ON qatests.testid=prodspecs.testid
			LEFT OUTER JOIN stockmaster on stockmaster.stockid=prodspecs.keyval
			WHERE prodspecs.keyval='" .$SelectedProdSpec."'
			AND prodspecs.showonspec='1'
			ORDER by groupby, prodspecs.testid";

$result=DB_query($sql,$ErrMsg);

//If there are no rows, there's a problem.
if (DB_num_rows($result)==0){
	$Title = _('Print Product Specification Error');
	include('includes/header.inc');
	 echo '<div class="centre">
			<br />
			<br />
			<br />';
	prnMsg( _('Unable to Locate Specification') . ' : ' . $_SelectedProdSpec . ' ', 'error');
	echo '<br />
			<br />
			<br />
			<table class="table_index">
			<tr>
				<td class="menu_group_item">
					<ul><li><a href="'. $RootPath . '/PDFProdSpec.php">' . _('Product Specifications') . '</a></li></ul>
				</td>
			</tr>
			</table>
			</div>
			<br />
			<br />
			<br />';
	include('includes/footer.inc');
	exit;
}
$PaperSize = 'Letter';

include('includes/PDFStarter.php');
$pdf->addInfo('Title', _('Product Specification') );
$pdf->addInfo('Subject', _('Product Specification') . ' ' . $SelectedProdSpec);
$FontSize=12;
$PageNumber = 1;
$HeaderPrinted=0;
$line_height=$FontSize*1.25;
$RectHeight=12;
$SectionHeading=0;
$CurSection='';
$SectionTitle='';
$SectionTrailer='';
$Pkey = $SelectedProdSpec;
if($Pkey=="KOFC54030601"){
$hardness = array('Hardness Test',2, _('HARDNESS TEST'), _(''), array(105,100,100,100,100),array(_('SAMPLE'),_('38mm'),_('47mm'),_('57mm'),_('67mm')),array('left','center','center','center','center'));
$wallthickness = array('Wall Thickness Test',2, _('WALL THICKNESS TEST'), _(''), array(115,130,130,130),array(_('SAMPLE'),_('6.7mm'),_('37.7mm'),_('Neck')),array('left','center','center','center'));
}
if($Pkey=="KOFC54030602"){
$hardness = array('Hardness Test',2, _('HARDNESS TEST'), _(''), array(105,80,80,80,80,80),array(_('SAMPLE'),_('8mm'),_('29mm'),_('35mm'),_('38mm'),_('47mm')),array('left','center','center','center','center','center'));
$wallthickness = array('Wall Thickness Test',2, _('WALL THICKNESS TEST'), _(''), array(105,100,100,100,100),array(_('SAMPLE'),_('6.7mm'),_('12.7mm'),_('20.7mm'),_('37.7mm')),array('left','center','center','center','center'));
}
if($Pkey=="KOFC54030603"){
$hardness = array('Hardness Test',2, _('HARDNESS TEST'), _(''), array(105,50,50,50,50,50,50,50,50),array(_('SAMPLE'),_('6mm'),_('10mm'),_('14mm'),_('18mm'),_('22mm'),_('27mm'),_('35mm'),_('42mm')),array('left','center','center','center','center','center','center','center','center'));
$wallthickness = array('Wall Thickness Test',2, _('WALL THICKNESS TEST'), _(''), array(105,80,80,80,80,80),array(_('SAMPLE'),_('7mm'),_('14mm'),_('25mm'),_('35mm'),_('Mouth')),array('left','center','center','center','center','center'));
}
if($Pkey=="KOFC54030604"){
$hardness = array('Hardness Test',2, _('HARDNESS TEST'), _(''), array(54,41,41,41,41,41,41,41,41,41,41,41),array(_('SAMPLE'),_('6mm'),_('10mm'),_('14mm'),_('18mm'),_('22mm'),_('27mm'),_('35mm'),_('42mm'),_('46mm'),_('50mm'),_('54mm')),array('left','center','center','center','center','center','center','center','center','center','center','center'));
$wallthickness = array('Wall Thickness Test',2, _('WALL THICKNESS TEST'), _(''), array(105,80,80,80,80,80),array(_('SAMPLE'),_('7mm'),_('14mm'),_('25mm'),_('35mm'),_('Mouth')),array('left','center','center','center','center','center'));
}
if($Pkey=="KOFC54030605"){
$hardness = array('Hardness Test',2, _('HARDNESS TEST'), _(''), array(115,130,130,130,130),array(_('SAMPLE'),_('6mm'),_('10mm'),_('17mm')),array('left','center','center','center'));
$wallthickness = array('Wall Thickness Test',2, _('WALL THICKNESS TEST'), _(''), array(205,150,150),array(_('SAMPLE'),_('10mm'),_('18.64mm')),array('left','center','center'));
}

$SectionsArray=array(array('Major Defects',3, _('AQL MAJOR DEFECTS = 0.40'), '', array(325,80,100),array(_('DECTCTS'),_('RESULTS'),_('Test Method')),array('left','center','center')),
					 array('Critical Defects',2, _('AQL CRITICAL DEFECTS = 0.015'), _(''), array(325,80,100),array(_('DEFECTS'),_('RESULTS'),_('Test Method')),array('left','center','center')),
					 array('Base Hardness',2, _('BASE HARDNESS (ABCD >=165 HV5) OR (123 >=150 HV1)'), _(''), array(325,80,100),array(_('TEST'),_('RESULTS'),_('Test Method')),array('left','center','center')),
					 array('Special Check',2, _('SPECIAL CHECK'), _(''), array(325,80,100),array(_('TEST'),_('RESULTS'),_('Test Method')),array('left','center','center')),
					 $hardness ,
					 $wallthickness,
					 array('Primer Sensitivity',2, _('PRIMER SENSITIVITY'), _(''), array(325,80,100),array(_('TEST'),_('RESULTS'),_('Test Method')),array('left','center','center')),
					 array('EPVAT Test',2, _('EPVAT TEST'), _(''), array(325,80,100),array(_('TEST'),_('RESULTS'),_('Test Method')),array('left','center','center')),
					 array('minor Defects',2, _('AQL MINOR DEFECTS = 0.65'), '', array(325,80,100),array(_('DEFECTS'),_('RESULTS'),_('Test Method')),array('left','center','center')));

while ($myrow=DB_fetch_array($result)){
	if ($myrow['description']=='') {
		$myrow['description']=$myrow['keyval'];
	}
	$Spec=$myrow['description'];
	$SpecDesc=$myrow['longdescription'];
	foreach($SectionsArray as $row) {
		if ($myrow['groupby']==$row[0]) {
			$SectionColSizes=$row[4];
			$SectionColLabs=$row[5];
			$SectionAlign=$row[6];
		}
	}
	$TrailerPrinted=1;
	if ($HeaderPrinted==0) {
		include('includes/PDFProdSpecHeader.inc');
		$HeaderPrinted=1;
	}

	if ($CurSection!=$myrow['groupby']) {
		$SectionHeading=0;
		if ($CurSection!='' AND $PrintTrailer==1) {
			$pdf->line($XPos+1, $YPos+$RectHeight,$XPos+506, $YPos+$RectHeight);
		}
		$PrevTrailer=$SectionTrailer;
		$CurSection=$myrow['groupby'];
		foreach($SectionsArray as $row) {
			if ($myrow['groupby']==$row[0]) {
				$SectionTitle=$row[2];
				$SectionTrailer=$row[3];
			}
		}
	}

	if ($SectionHeading==0) {
		$XPos=65;
		if ($PrevTrailer>'' AND $PrintTrailer==1) {
			$PrevFontSize=$FontSize;
			$FontSize=8;
			$line_height=$FontSize*1.25;
			$LeftOvers = $pdf->addTextWrap($XPos+5,$YPos,500,$FontSize,$PrevTrailer,'left');
			$FontSize=$PrevFontSize;
			$line_height=$FontSize*1.25;
			$YPos -= $line_height;
			$YPos -= $line_height;
		}
		if ($YPos < ($Bottom_Margin + 90)){ // Begins new page
			$PrintTrailer=0;
			$PageNumber++;
			include ('includes/PDFProdSpecHeader.inc');
		}
		$LeftOvers = $pdf->addTextWrap($XPos,$YPos,500,$FontSize,$SectionTitle,'center');
		$YPos -= $line_height;
		$pdf->setFont('','B');
		$pdf->SetFillColor(200,200,200);
		$x=0;
		foreach($SectionColLabs as $CurColLab) {
			$ColLabel=$CurColLab;
			$ColWidth=$SectionColSizes[$x];
			$x++;
			$LeftOvers = $pdf->addTextWrap($XPos+1,$YPos,$ColWidth,$FontSize,$ColLabel,'center',1,'fill');
			$XPos+=$ColWidth;
		}
		$SectionHeading=1;
		$YPos -= $line_height;
		$pdf->setFont('','');
	} //$SectionHeading==0
	$XPos=65;
	$Value='';
	if ($myrow['targetvalue'] > '') {
		$Value=$myrow['targetvalue'];
	} elseif ($myrow['rangemin'] > '' OR $myrow['rangemax'] > '') {
		if ($myrow['rangemin'] > '' AND $myrow['rangemax'] == '') {
			$Value='> ' . $myrow['rangemin'];
		} elseif ($myrow['rangemin']== '' AND $myrow['rangemax'] > '') {
			$Value='< ' . $myrow['rangemax'];
		} else {
			$Value=$myrow['rangemin'] . ' - ' . $myrow['rangemax'];
		}
	}
	if (strtoupper($Value) <> 'NB' AND strtoupper($Value) <> 'NO BREAK') {
		$Value.= ' ' . $myrow['units'];
	}
	$x=0;

	foreach($SectionColLabs as $CurColLab) {
		$ColLabel=$CurColLab;
		$ColWidth=$SectionColSizes[$x];
		$ColAlign=$SectionAlign[$x];
		if($myrow['groupby']=='Hardness Test'){
		switch ($x) {
			case 0;
				$DispValue=$myrow['name'];
				break;
			case 1;
				$DispValue=$Value;
				
				break;
			case 2;
				$DispValue=$myrow['method'];
				break;
		}
		}else{
		switch ($x) {
			case 0;
				$DispValue=$myrow['name'];
				break;
			case 1;
				$DispValue=$Value;
				break;
			case 2;
				$DispValue=$myrow['method'];
				break;
		}
		}
		$LeftOvers = $pdf->addTextWrap($XPos+1,$YPos,$ColWidth,$FontSize,$DispValue,$ColAlign,1);
		$XPos+=$ColWidth;
		$x++;
	}
	$YPos -= $line_height;
	$XPos=65;
	$PrintTrailer=1;
	if ($YPos < ($Bottom_Margin + 80)){ // Begins new page
		$pdf->line($XPos+1, $YPos+$RectHeight,$XPos+506, $YPos+$RectHeight);
		$PrintTrailer=0;
		$PageNumber++;
		include ('includes/PDFProdSpecHeader.inc');
	}
	//echo 'PrintTrailer'.$PrintTrailer.' '.$PrevTrailer.'<br>' ;
} //while loop

$pdf->line($XPos+1, $YPos+$RectHeight,$XPos+506, $YPos+$RectHeight);
if ($SectionTrailer>'') {
	$PrevFontSize=$FontSize;
	$FontSize=8;
	$line_height=$FontSize*1.25;
	$LeftOvers = $pdf->addTextWrap($XPos+5,$YPos,500,$FontSize,$SectionTrailer,'left');
	$FontSize=$PrevFontSize;
	$line_height=$FontSize*1.25;
	$YPos -= $line_height;
	$YPos -= $line_height;
}
if ($YPos < ($Bottom_Margin + 85)){ // Begins new page
	$PageNumber++;
	include ('includes/PDFProdSpecHeader.inc');
}
$Disclaimer= _('The information provided on this datasheet should only be used as a guideline. Actual lot to lot values will vary.');
$FontSize=8;
$line_height=$FontSize*1.25;
$YPos -= $line_height;
$LeftOvers = $pdf->addTextWrap($XPos+5,$YPos,500,$FontSize,$Disclaimer);
$YPos -= $line_height;
$YPos -= $line_height;
$sql = "SELECT confvalue
			FROM config
			WHERE confname='QualityProdSpecText'";

$result=DB_query($sql,$ErrMsg);
$myrow=DB_fetch_array($result);
$Disclaimer=$myrow[0];
$LeftOvers = $pdf->addTextWrap($XPos+5,$YPos,500,$FontSize,$Disclaimer);
while (mb_strlen($LeftOvers) > 1) {
	$YPos -= $line_height;
	$LeftOvers = $pdf->addTextWrap($XPos+5,$YPos,445,$FontSize, $LeftOvers, 'left');
}

$pdf->OutputI($_SESSION['DatabaseName'] . '_ProductSpecification_' . date('Y-m-d') . '.pdf');
$pdf->__destruct();

?>