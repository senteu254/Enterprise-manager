<?php

/* $Id: PDFCOA.php 1 2014-09-15 06:31:08Z agaluski $ */

include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

if (isset($_GET['LotKey']))  {
	$SelectedCOA=$_GET['LotKey'];
} elseif (isset($_POST['LotKey'])) {
	$SelectedCOA=$_POST['LotKey'];
}
if (isset($_GET['ProdSpec']))  {
	$SelectedSpec=$_GET['ProdSpec'];
} elseif (isset($_POST['ProdSpec'])) {
	$SelectedSpec=$_POST['ProdSpec'];
}

if (isset($_GET['QASampleID']))  {
	$QASampleID=$_GET['QASampleID'];
} elseif (isset($_POST['QASampleID'])) {
	$QASampleID=$_POST['QASampleID'];
}

//Get Out if we have no Certificate of Analysis
If ((!isset($SelectedCOA) || $SelectedCOA=='') AND (!isset($QASampleID) OR $QASampleID=='')){
        $Title = _('Select Certificate of Analysis To Print');
        include('includes/header.inc');
		echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/printer.png" title="' . _('Print')  . '" alt="" />' . ' ' . $Title . '</p>';
        echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') .  '" method="post">
		<div>
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
		<table class="selection">
		<tr>
			<td>' . _('Enter Item') .':</td>
			<td><input type="text" name="ProdSpec" size="25" maxlength="25" /></td>
			<td>' . _('Enter Lot') .':</td>
			<td><input type="text" name="LotKey" size="25" maxlength="25" /></td>
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
			<td>' . _('Or Select Existing Lot') .':</td>';
	$SQLSpecSelect="SELECT sampleid,
							lotkey,
							prodspeckey,
							description
						FROM qasamples LEFT OUTER JOIN stockmaster
						ON stockmaster.stockid=qasamples.prodspeckey
						WHERE cert='1'
						ORDER BY lotkey";


	$ResultSelection=DB_query($SQLSpecSelect);
	echo '<td><select name="QASampleID" style="font-family: monospace; white-space:pre;">';
	echo '<option value="">' . str_pad(_('Lot/Serial'),15,'_'). str_pad(_('Item'),20, '_', STR_PAD_RIGHT). str_pad(_('Description'),20,'_') . '</option>';
	while ($MyRowSelection=DB_fetch_array($ResultSelection)){
		echo '<option value="' . $MyRowSelection['sampleid'] . '">' . str_pad($MyRowSelection['lotkey'],15, '_', STR_PAD_RIGHT). str_pad($MyRowSelection['prodspeckey'],20,'_') .htmlspecialchars($MyRowSelection['description'], ENT_QUOTES,'UTF-8', false)  . '</option>';
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


$ErrMsg = _('There was a problem retrieving the Lot Information') . ' ' .$SelectedCOA . ' ' . _('from the database');
if (isset($SelectedCOA)) {
	$sql = "SELECT lotkey,
					description,
					prodspeckey,
					name,
					method,
					qatests.units,
					type,
					testvalue,
					sampledate,
					SampleSize,
					ProductionDate,
					Batch,
					groupby,
					qasamples.sampleid
				FROM qasamples INNER JOIN sampleresults
				ON sampleresults.sampleid=qasamples.sampleid
				INNER JOIN qatests
				ON qatests.testid=sampleresults.testid
				LEFT OUTER JOIN stockmaster on stockmaster.stockid=qasamples.prodspeckey
				WHERE qasamples.lotkey='" .$SelectedCOA."'
				AND qasamples.prodspeckey='" .$SelectedSpec."'
				AND qasamples.sampleid='" .$QASampleID. "'
				AND qasamples.cert='1'
				AND sampleresults.showoncert='1'
				ORDER by groupby, sampleresults.testid";
} else {
	$sql = "SELECT lotkey,
					description,
					prodspeckey,
					name,
					method,
					qatests.units,
					type,
					testvalue,
					sampledate,
					SampleSize,
					ProductionDate,
					Batch,
					groupby,
					qasamples.sampleid
				FROM qasamples INNER JOIN sampleresults
				ON sampleresults.sampleid=qasamples.sampleid
				INNER JOIN qatests
				ON qatests.testid=sampleresults.testid
				LEFT OUTER JOIN stockmaster on stockmaster.stockid=qasamples.prodspeckey
				WHERE qasamples.sampleid='" .$QASampleID."'
				AND qasamples.cert='1'
				AND sampleresults.showoncert='1'
				ORDER by groupby, sampleresults.testid";
}
$result=DB_query($sql,$ErrMsg);

//If there are no rows, there's a problem.
if (DB_num_rows($result)==0){
	$Title = _('Print Certificate of Analysis Error');
	include('includes/header.inc');
	 echo '<div class="centre">
			<br />
			<br />
			<br />';
	prnMsg( _('Unable to Locate Lot') . ' : ' . $SelectedCOA . ' ', 'error');
	echo '<br />
			<br />
			<br />
			<table class="table_index">
			<tr>
				<td class="menu_group_item">
					<ul><li><a href="'. $RootPath . '/PDFCOA.php">' . _('Certificate of Analysis') . '</a></li></ul>
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
if ($QASampleID>'') {
	$myrow=DB_fetch_array($result);
	$SelectedCOA=$myrow['lotkey'];
	$Pkey=$myrow['prodspeckey'];
	DB_data_seek($result,0);
}else{
$myrow=DB_fetch_array($result);
	$QASampleID=$myrow['sampleid'];
	$Pkey=$myrow['prodspeckey'];
	DB_data_seek($result,0);
}
include('includes/PDFStarter.php');
$pdf->addInfo('Title', _('Certificate of Analysis') );
$pdf->addInfo('Subject', _('Certificate of Analysis') . ' ' . $SelectedCOA);
$FontSize=12;
$PageNumber = 1;
$HeaderPrinted=0;
$line_height=$FontSize*1.25;
$RectHeight=10;
$SectionHeading=0;
$CurSection='';
$SectionTitle='';
$SectionTrailer='';
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
		$myrow['description']=$myrow['prodspeckey'];
	}
	$Spec=$myrow['description'];
	$SampleDate=ConvertSQLDate($myrow['sampledate']);
	$ProdDate=$myrow['ProductionDate'];
	$Batch=number_format($myrow['Batch']);
	$SampleSize=$myrow['SampleSize'];
	$ProdSpecs = $myrow['prodspeckey'];

	foreach($SectionsArray as $row) {
		if ($myrow['groupby']==$row[0]) {
			$SectionColSizes=$row[4];
			$SectionColLabs=$row[5];
			$SectionAlign=$row[6];
		}
	}
	$TrailerPrinted=1;
	if ($HeaderPrinted==0) {
		include('includes/PDFCOAHeader.inc');
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
			include ('includes/PDFCOAHeader.inc');
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
	if ($myrow['testvalue'] > '') {
		$Value=$myrow['testvalue'];
	} //elseif ($myrow['rangemin'] > '') {
	//	$Value=$myrow['rangemin'] . ' - ' . $myrow['rangemax'];
	//}
	if (strtoupper($Value) <> 'NB' AND strtoupper($Value) <> 'NO BREAK') {
		$Value.= ' ' . $myrow['units'];
	}
	$x=0;
	foreach($SectionColLabs as $CurColLab) {
		$ColLabel=$CurColLab;
		$ColWidth=$SectionColSizes[$x];
		$ColAlign=$SectionAlign[$x];
		if($myrow['groupby']=='Hardness Test'){
		$Val = explode(',',$Value);
		switch ($x) {
			case 0;
				$DispValue=$myrow['name'];
				break;
			case 1;
				$DispValue=$Val[0];
				break;
			case 2;
				$DispValue=$Val[1];
				break;
			case 3;
				$DispValue=$Val[2];
				break;
			case 4;
				$DispValue=$Val[3];
				break;
			case 5;
				$DispValue=$Val[4];
				break;
		}
		}elseif($myrow['groupby']=='Wall Thickness Test'){
		$Val = explode(',',$Value);
		switch ($x) {
			case 0;
				$DispValue=$myrow['name'];
				break;
			case 1;
				$DispValue=$Val[0];
				break;
			case 2;
				$DispValue=$Val[1];
				break;
			case 3;
				$DispValue=$Val[2];
				break;
			case 4;
				$DispValue=$Val[3];
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
		include ('includes/PDFCOAHeader.inc');
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
	include ('includes/PDFCOAHeader.inc');
}

$FontSize=8;
$line_height=$FontSize*1.25;
$YPos -= $line_height;
//$YPos -= $line_height;
$sql = "SELECT confvalue
			FROM config
			WHERE confname='QualityCOAText'";

$result=DB_query($sql, $ErrMsg);
$myrow=DB_fetch_array($result);
$Disclaimer=$myrow[0];
$LeftOvers = $pdf->addTextWrap($XPos+5,$YPos,500,$FontSize,$Disclaimer);
while (mb_strlen($LeftOvers) > 1) {
	$YPos -= $line_height;
	$LeftOvers = $pdf->addTextWrap($XPos+5,$YPos,445,$FontSize, $LeftOvers, 'left');
}

if ($YPos < ($Bottom_Margin + 80)){ // Begins new page
		$PageNumber++;
		include ('includes/PDFCOAHeader.inc');
	}
$pdf->setFont('','B');
$LeftOvers = $pdf->addTextWrap($XPos+1,$YPos,'200',$FontSize,'QA TECHNICIANS','left');
$pdf->setFont('','');
$YPos -= $line_height;
$tech = DB_query("SELECT technician,realname FROM qasampletechnicians
						INNER JOIN www_users ON qasampletechnicians.technician = www_users.userid
						WHERE sampleidno = '".$QASampleID."'",	$db);
while($wo=DB_fetch_array($tech)){
$LeftOvers = $pdf->addTextWrap($XPos+1,$YPos,'200',$FontSize,$wo['technician'].' - '.$wo['realname'],'left',1);
$YPos -= $line_height;
}
$pdf->line($XPos+1, $YPos+7,$XPos+201, $YPos+7);

$YPos -= $line_height;
if ($YPos < ($Bottom_Margin + 80)){ // Begins new page
		$PageNumber++;
		include ('includes/PDFCOAHeader.inc');
	}
$title="";
$result = DB_query("SELECT * FROM qrsampleremarks
						WHERE sampleid = '".$QASampleID."'",	$db);
	while($my = DB_fetch_array($result)){
	if($title != $my['approver_title']){
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap($XPos+5,$YPos,445,$FontSize, $my['approver_title'], 'left');
$pdf->SetFont('');
$title = $my['approver_title'];
}
$YPos -= $line_height;
$LeftOvers = $pdf->addTextWrap($XPos+5,$YPos,445,$FontSize, $my['remark'], 'left');
$YPos -= $line_height*2;
$LeftOvers = $pdf->addTextWrap($XPos+5,$YPos,345,$FontSize, 'Remarks From: '.$my['approver_name'], 'left');
$LeftOvers = $pdf->addTextWrap($XPos+400,$YPos,445,$FontSize, ConvertSQLDateTime($my['remarkdate']), 'left');
$YPos -= $line_height;
if ($YPos < ($Bottom_Margin + 80)){ // Begins new page
		$PageNumber++;
		include ('includes/PDFCOAHeader.inc');
	}
while (mb_strlen($LeftOvers) > 1) {
	$YPos -= $line_height;
	$LeftOvers = $pdf->addTextWrap($XPos+5,$YPos,445,$FontSize, $LeftOvers, 'left');

}

if ($YPos < ($Bottom_Margin + 80)){ // Begins new page
		$PageNumber++;
		include ('includes/PDFCOAHeader.inc');
	}
}

$pdf->OutputI($_SESSION['DatabaseName'] . 'COA' . date('Y-m-d') . '.pdf');
$pdf->__destruct();

?>