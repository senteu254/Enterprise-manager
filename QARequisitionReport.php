<?php

/* $Id: InventoryQuantities.php 6944 2014-10-27 07:15:34Z daintree $ */

// InventoryQuantities.php - Report of parts with quantity. Sorts by part and shows
// all locations where there are quantities of the part

include('includes/session.inc');
If (isset($_POST['PrintPDF'])) {

	include('includes/PDFStarter.php');
	$pdf->addInfo('Title',_('Inventory Quantities Report'));
	$pdf->addInfo('Subject',_('Parts With Quantities'));
	$FontSize=9;
	$PageNumber=1;
	$line_height=15;
	$TopOfColHeadings=760;

	$Xpos = $Left_Margin+1;
	$WhereCategory = "  AND despatchdate BETWEEN '". FormatDateForSQL($_POST['FromDate']) ."' AND '". FormatDateForSQL($_POST['ToDate']) ."'";
	$CatDescription = ' ';
	if ($_POST['StockLoc'] != 'All') {
	    $WhereCategory = " AND stockrequest.loccode='" . $_POST['StockLoc'] . "' AND despatchdate BETWEEN '". FormatDateForSQL($_POST['FromDate']) ."' AND '". FormatDateForSQL($_POST['ToDate']) ."'";
		$sql= "SELECT loccode,
				locationname
			FROM locations
				WHERE loccode='" . $_POST['StockLoc'] . "' ";
		$result = DB_query($sql);
		$myrow = DB_fetch_row($result);
		$CatDescription = $myrow[1];
	}
		$sql = "SELECT stockrequestitems.stockid,
					stockmaster.description,
					stockrequestitems.qtydelivered,
					stockmaster.decimalplaces,
					stockmaster.serialised,
					stockmaster.controlled,
					stockrequest.narrative,
					stockrequest.despatchdate,
					stockrequestitems.User,
					stockrequestitems.serialno
				FROM stockrequest INNER JOIN stockrequestitems
				ON stockrequest.dispatchid=stockrequestitems.dispatchid
				INNER JOIN stockmaster
				ON stockrequestitems.stockid=stockmaster.stockid
				WHERE closed=1 AND stockrequestitems.qtydelivered >0 AND stockrequestitems.completed=1" .
				$WhereCategory . "
				ORDER BY stockrequestitems.stockid";


	$result = DB_query($sql,'','',false,true);

	if (DB_error_no() !=0) {
	  $Title = _('Inventory Quantities') . ' - ' . _('Problem Report');
	  include('includes/header.inc');
	   prnMsg( _('The Inventory Quantity report could not be retrieved by the SQL because') . ' '  . DB_error_msg(),'error');
	   echo '<br /><a href="' .$RootPath .'/index.php">' . _('Back to the menu') . '</a>';
	   if ($debug==1){
	      echo '<br />' . $sql;
	   }
	   include('includes/footer.inc');
	   exit;
	}
	if (DB_num_rows($result)==0){
			$Title = _('Print Inventory Quantities Report');
			include('includes/header.inc');
			prnMsg(_('There were no items with inventory quantities'),'error');
			echo '<br /><a href="'.$RootPath.'/index.php">' . _('Back to the menu') . '</a>';
			include('includes/footer.inc');
			exit;
	}
	
			$pdf->line($Page_Width-$Right_Margin-525, $TopOfColHeadings+12,$Page_Width-$Right_Margin-3,$TopOfColHeadings+12);
			$pdf->line($Page_Width-$Right_Margin-525, $TopOfColHeadings,$Page_Width-$Right_Margin-3,$TopOfColHeadings);
			$pdf->line($Page_Width-$Right_Margin-525, $Bottom_Margin,$Page_Width-$Right_Margin-3,$Bottom_Margin);
			PrintLinesToBottom ();

	PrintHeader($pdf,
				$YPos,
				$PageNumber,
				$Page_Height,
				$Top_Margin,
				$Left_Margin,
				$Page_Width,
				$Right_Margin,
				$CatDescription);

    $FontSize=8;

    $holdpart = " ";
	While ($myrow = DB_fetch_array($result,$db)){
	      //if ($myrow['stockid'] != $holdpart) {
			//  $YPos -=(2 * $line_height);
			//  $holdpart = $myrow['stockid'];
		 // } else {
	          $YPos -=($line_height);
		 // }

			// Parameters for addTextWrap are defined in /includes/class.pdf.php
			// 1) X position 2) Y position 3) Width
			// 4) Height 5) Text 6) Alignment 7) Border 8) Fill - True to use SetFillColor
			// and False to set to transparent

				$pdf->addTextWrap(50,$YPos,60,$FontSize,$myrow['serialno'],'',0);
				$pdf->addTextWrap(100,$YPos,170,$FontSize,$myrow['stockid'].' - '.$myrow['description'],'r',0);
				$pdf->addTextWrap(275,$YPos,60,$FontSize,ConvertSQLDate($myrow['despatchdate']),'',0);
				$pdf->addTextWrap(320,$YPos,50,$FontSize,$myrow['User'],'',0);
				$pdf->addTextWrap(350,$YPos,50,$FontSize,locale_number_format($myrow['qtydelivered'],$myrow['decimalplaces']),'right',0);
				$pdf->addTextWrap(400,$YPos,160,$FontSize,$myrow['narrative'],'left',0);

			if ($YPos < $Bottom_Margin + $line_height){
			   PrintHeader($pdf,$YPos,$PageNumber,$Page_Height,$Top_Margin,$Left_Margin,$Page_Width,
			               $Right_Margin,$CatDescription);
				$pdf->line($Page_Width-$Right_Margin-525, $TopOfColHeadings+12,$Page_Width-$Right_Margin-3,$TopOfColHeadings+12);
				$pdf->line($Page_Width-$Right_Margin-525, $TopOfColHeadings,$Page_Width-$Right_Margin-3,$TopOfColHeadings);
				$pdf->line($Page_Width-$Right_Margin-525, $Bottom_Margin,$Page_Width-$Right_Margin-3,$Bottom_Margin);
				PrintLinesToBottom ();
			}
	} /*end while loop */

	if ($YPos < $Bottom_Margin + $line_height){
	       PrintHeader($pdf,$YPos,$PageNumber,$Page_Height,$Top_Margin,$Left_Margin,$Page_Width,
	                   $Right_Margin,$CatDescription);
			$pdf->line($Page_Width-$Right_Margin-525, $TopOfColHeadings+12,$Page_Width-$Right_Margin-3,$TopOfColHeadings+12);
			$pdf->line($Page_Width-$Right_Margin-525, $TopOfColHeadings,$Page_Width-$Right_Margin-3,$TopOfColHeadings);
			$pdf->line($Page_Width-$Right_Margin-525, $Bottom_Margin,$Page_Width-$Right_Margin-3,$Bottom_Margin);
			PrintLinesToBottom ();
	}

/*Print out the grand totals */

	$pdf->OutputD($_SESSION['DatabaseName'] . '_Inventory_Quantities_' . Date('Y-m-d') . '.pdf');
	$pdf->__destruct();
} else { /*The option to print PDF was not hit so display form */

	$Title=_('Inventory Quantities Reporting');
	include('includes/header.inc');
echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/inventory.png" title="' . _('Inventory') . '" alt="" />' . ' ' . _('Inventory Quantities Report') . '</p>';
echo '<div class="page_help_text">' . _('Use this report to display the quantity of Internal Requests in different locations.') . '</div><br />';


	echo '<br />
		<br />
		<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">
        <div>
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
		<table class="selection">
		<tr>
			<td>' . _('Date From') . ':</td>
			<td><input type="text" required="required" name="FromDate" maxlength="10" size="10" class="date" alt="' . $_SESSION['DefaultDateFormat'] . '" value="'.Date($_SESSION['DefaultDateFormat']).'" /></td>
		</tr>
		<tr>
			<td>' . _('Date To') . ':</td>
			<td><input type="text" required="required" name="ToDate" maxlength="10" size="10" class="date" alt="' . $_SESSION['DefaultDateFormat'] . '" value="'.Date($_SESSION['DefaultDateFormat']).'" /></td>
		</tr>';

	$SQL="SELECT locations.loccode,
			locationname
		FROM locations
		INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canupd=1
		WHERE internalrequest = 1
		ORDER BY locationname";
	$result1 = DB_query($SQL);
	if (DB_num_rows($result1)==0){
		echo '</table>
			<p />';
		prnMsg(_('There are no stock location currently defined please use the link below to set them up'),'warn');
		echo '<br /><a href="' . $RootPath . '/Locations.php">' . _('Define Stock Location') . '</a>';
		include ('includes/footer.inc');
		exit;
	}

	echo '<tr>
			<td>' . _('In Stock Location') . ':</td>
			<td><select name="StockLoc">';
	if (!isset($_POST['StockLoc'])){
		$_POST['StockLoc']='All';
	}
	if ($_POST['StockLoc']=='All'){
		echo '<option selected="selected" value="All">' . _('All') . '</option>';
	} else {
		echo '<option value="All">' . _('All') . '</option>';
	}
	while ($myrow1 = DB_fetch_array($result1)) {
		if ($myrow1['loccode']==$_POST['StockLoc']){
			echo '<option selected="selected" value="' . $myrow1['loccode'] . '">' . $myrow1['locationname'] . '</option>';
		} else {
			echo '<option value="' . $myrow1['loccode'] . '">' . $myrow1['locationname'] . '</option>';
		}
	}
	echo '</select></td>
		</tr>
		</table>
		<br />
		<div class="centre">
			<input type="submit" name="PrintPDF" value="' . _('Print PDF') . '" />
		</div>';
    echo '</div>
          </form>';
	include('includes/footer.inc');

} /*end of else not PrintPDF */

function PrintHeader(&$pdf,&$YPos,&$PageNumber,$Page_Height,$Top_Margin,$Left_Margin,
                     $Page_Width,$Right_Margin,$CatDescription) {

	/*PDF page header for Reorder Level report */
	if ($PageNumber>1){
		$pdf->newPage();
		$TopOfColHeadings=760;
	}
	$line_height=12;
	$FontSize=9;
	$YPos= $Page_Height-$Top_Margin;

	$pdf->addTextWrap($Left_Margin,$YPos,300,$FontSize,$_SESSION['CompanyRecord']['coyname']);

	$YPos -=$line_height;

	$pdf->addTextWrap($Left_Margin,$YPos,150,$FontSize,_('Internal Stock Request Report For Q.A'));
	$pdf->addTextWrap($Page_Width-$Right_Margin-150,$YPos,160,$FontSize,_('Printed') . ': ' .
		 Date($_SESSION['DefaultDateFormat']) . '   ' . _('Page') . ' ' . $PageNumber,'left');
	$YPos -= $line_height;
	$pdf->addTextWrap($Left_Margin,$YPos,50,$FontSize,_('Location'));
	$pdf->addTextWrap(95,$YPos,50,$FontSize,$_POST['StockLoc']);
	$pdf->addTextWrap(160,$YPos,150,$FontSize,$CatDescription,'left');
	$YPos -=(2*$line_height);

	/*set up the headings */
	$Xpos = $Left_Margin+1;

	$pdf->addTextWrap(50,$YPos,100,$FontSize,_('Lot Number'), 'left');
	$pdf->addTextWrap(150,$YPos,150,$FontSize,_('Description'), 'left');
	$pdf->addTextWrap(280,$YPos,60,$FontSize,_('Date'), 'left');
	$pdf->addTextWrap(310,$YPos,50,$FontSize,_('Foreman'), 'right');
	$pdf->addTextWrap(350,$YPos,50,$FontSize,_('Quantity'), 'right');
	$pdf->addTextWrap(400,$YPos,50,$FontSize,_('Narrative'), 'left');


	$FontSize=8;
	$PageNumber++;
} // End of PrintHeader() function
function PrintLinesToBottom () {

	global $pdf;
	global $PageNumber;
	global $TopOfColHeadings;
	global $Left_Margin;
	global $Bottom_Margin;
	global $line_height;
			
			$pdf->line($Left_Margin, $TopOfColHeadings+12,$Left_Margin,$Bottom_Margin);
			
			$pdf->line($Left_Margin+60, $TopOfColHeadings+12,$Left_Margin+60,$Bottom_Margin);

			/* Print a column vertical line */
			$pdf->line($Left_Margin+235, $TopOfColHeadings+12,$Left_Margin+235,$Bottom_Margin);

			/* Print a column vertical line */
			$pdf->line($Left_Margin+280, $TopOfColHeadings+12,$Left_Margin+280,$Bottom_Margin);

			/* Print a column vertical line */
			$pdf->line($Left_Margin+320, $TopOfColHeadings+12,$Left_Margin+320,$Bottom_Margin);
			
			/* Print a column vertical line */
			$pdf->line($Left_Margin+360, $TopOfColHeadings+12,$Left_Margin+360,$Bottom_Margin);

			/* Print a column vertical line */
			$pdf->line($Left_Margin+522, $TopOfColHeadings+12,$Left_Margin+522,$Bottom_Margin);

}
?>