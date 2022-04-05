<?php
/*	$Id: PDFQuotationPortrait.php 4491 2011-02-15 06:31:08Z daintree $ */

/*	Please note that addTextWrap prints a font-size-height further down than
	addText and other functions.*/
ob_start();
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

function secondsToWords($seconds)
{
    $ret = "";

    /*** get the days ***/
    $days = intval(intval($seconds) / (3600*24));
    if($days> 0)
    {
        $ret .= "$days days ";
    }

    /*** get the hours ***/
    $hours = (intval($seconds) / 3600) % 24;
    if($hours > 0)
    {
        $ret .= "$hours hours ";
    }

    /*** get the minutes ***/
    $minutes = (intval($seconds) / 60) % 60;
    if($minutes > 0)
    {
        $ret .= "$minutes minutes ";
    }

    /*** get the seconds ***/
    $seconds = intval($seconds) % 60;
    if ($seconds > 0) {
        $ret .= "$seconds seconds";
    }

    return $ret;
}

//Get Out if we have no order number to work with
If (!isset($_GET['id']) || $_GET['id']==""){
        $Title = _('Select Report To Print');
        include('includes/header.inc');
        echo '<div class="centre">
				<br />
				<br />
				<br />';
        prnMsg( _('Select a Report to Print before calling this page') , 'error');
        echo '<br />
				<br />
				<br />
				<table class="table_index">
				<tr>
					<td class="menu_group_item">
						<ul><li><a href="index.php">' . _('Main Menu') . '</a></li>
						</ul>
					</td>
				</tr>
				</table>
				</div>
				<br />
				<br />
				<br />';
        include('includes/footer.inc');
        exit();
}

/*retrieve the order details from the database to print */
$ErrMsg = _('There was a problem retrieving the  header details for Request Number') . ' ' . $_GET['id'] . ' ' . _('from the database');
$LID =$_GET['id'];
$sql = "SELECT *,f.realname as manager,g.realname as responsible,TIME_TO_SEC(TIMEDIFF(enddate,startdate)) as diff FROM fixedassets z 
							INNER JOIN fixedassetlocations a on a.locationid = z.assetlocation
							INNER JOIN fixedassetcategories c on c.categoryid = z.assetcategoryid 
							INNER JOIN fixedassettasks e ON z.assetid = e.assetid
							INNER JOIN www_users f ON e.manager=f.userid
							INNER JOIN www_users g ON e.userresponsible=g.userid
							WHERE e.requestid='" . $LID . "'";

$result=DB_query($sql, $ErrMsg);

//If there are no rows, there's a problem.
if (DB_num_rows($result)==0){
	$Title = _('Print Report Error');
	include('includes/header.inc');
	 echo '<div class="centre">
			<br />
			<br />
			<br />';
	prnMsg( _('Unable to Locate Report Number') . ' : ' . $_GET['id'] . ' ', 'error');
	echo '<br />
			<br />
			<br />
			<table class="table_index">
			<tr>
				<td class="menu_group_item">
					<ul><li><a href="index.php">' . _('Main Menu') . '</a></li></ul>
				</td>
			</tr>
			</table>
			</div>
			<br />
			<br />
			<br />';
	include('includes/footer.inc');
	exit;
} else{ /*There is only one order header returned - thats good! */
	$myrow = DB_fetch_array($result);
}
/*retrieve the order details from the database to print */

/* Then there's an order to print and its not been printed already (or its been flagged for reprinting/ge_Width=807;
)
LETS GO */
$PaperSize = 'A4';
include('includes/PDFStarter.php');
/*$PageNumber = 1;// RChacon: PDFStarter.php sets $PageNumber = 0.*/
$pdf->addInfo('Title', _('Maintenance Completion Report') );
$pdf->addInfo('Subject', _('Maintenance Completion Report') . ' ' . $_GET['id']);
$FontSize = 12;
$line_height = 12;// Recommended: $line_height = $x * $FontSize.

$Title = "";

	$PageNumber ++;// Increments $PageNumber before printing.
if ($PageNumber>1) {// Inserts a page break if it is not the first page.
	$pdf->newPage();
}

// Prints company logo:
$XPos = $Page_Width/2 - 140;
$pdf->addJpegFromFile($_SESSION['LogoFile'],$XPos+90,735,0,60);

// Draws a box with round corners around 'Delivery To' info:
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-$FontSize*1, 300, $FontSize, $_SESSION['CompanyRecord']['coyname'], 'left');
$FontSize=10;
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-$FontSize*2, 200, $FontSize, $_SESSION['CompanyRecord']['regoffice1'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-$FontSize*3, 200, $FontSize,  _('Ph') . ': ' . $_SESSION['CompanyRecord']['telephone'] .' ' . _('Fax'). ': ' . $_SESSION['CompanyRecord']['fax'], 'left');
$pdf->addTextWrap($Page_Width-$Right_Margin-530, $Page_Height-$Top_Margin-$FontSize*4, 200, $FontSize, $_SESSION['CompanyRecord']['email'], 'left');

	
// Prints quotation info:
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-$FontSize*1, 200, $FontSize,  _('Printed On'). ': '.date("d, M Y"), 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-$FontSize*2, 200, $FontSize, _('Ref No.'). ': '.$_GET['id'], 'right');
$pdf->addTextWrap($Page_Width-$Right_Margin-200, $Page_Height-$Top_Margin-$FontSize*3, 200, $FontSize, _('Page').': '.$PageNumber, 'right');
// Prints 'Quotation' title:
$tt = _('MAITENANCE COMPLETION FORM');

$pdf->addTextWrap(0, $Page_Height-$Top_Margin-95, $Page_Width, 14,$tt, 'center');

// Prints 'Delivery To' info:
$XPos = 46;
$YPos = 775;
$FontSize=12;
// Prints 'Quotation For' info:
$YPos -= 80;

//$pdf->addText($XPos-10, $YPos+10,$FontSize, _('Personal No :').''.$myrow['personal_no']);
//$pdf->addText($XPos-10, $YPos-10,$FontSize,  _('Date   :').''.date("Y/m/d"));
//$pdf->addText($XPos-10, $YPos-30,$FontSize, _('Full Name  :').''.$myrow['emp_fname'].''.''.$myrow['emp_lname']);
//$pdf->addText($XPos-10, $YPos-50,$FontSize, _('Total Number of property   :').''.$myrow['total']);

// Draws a box with round corners around around 'Quotation For' info:

$FontSize=10;

// Prints table header:
//$YPos -= 55;
$XPos = 40;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize, _('JOB CARD NO'));
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(110,$YPos,270,$FontSize, ': '.$myrow['jobcardno']);
$pdf->SetFont('');
$LeftOvers = $pdf->addTextWrap(380,$YPos,200,$FontSize, _('DATE'),'left');
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(440,$YPos,200,$FontSize, ': '.ConvertSQLDate($myrow['expstartdate']),'left');
$pdf->SetFont('');

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-13,100,$FontSize, _('EQUIPMENT'));
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(110,$YPos-13,270,$FontSize, ': '.$myrow['description'],'left');
$pdf->SetFont('');
$LeftOvers = $pdf->addTextWrap(380,$YPos-13,300,$FontSize, _('M/C NO'),'left');
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(440,$YPos-13,415,$FontSize, ': '.$myrow['mcno'],'left');
$pdf->SetFont('');
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-26,100,$FontSize, _('CATEGORY'));
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(110,$YPos-26,270,$FontSize, ': '.$myrow['categorydescription']);
$pdf->SetFont('');
$LeftOvers = $pdf->addTextWrap(380,$YPos-26,300,$FontSize, _('BUILDING'),'left');
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(440,$YPos-26,270,$FontSize, ': '.$myrow['locationdescription'],'left');
$pdf->SetFont('');

$YPos=$YPos-50;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize, _('RESPONSIBLE TECH'));
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(140,$YPos,270,$FontSize, ': '.strtoupper($myrow['manager']));
$pdf->SetFont('');
$LeftOvers = $pdf->addTextWrap(380,$YPos,200,$FontSize, _('EXP START DATE'),'left');
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(470,$YPos,200,$FontSize, ': '.ConvertSQLDate($myrow['expstartdate']),'left');
$pdf->SetFont('');

$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-13,100,$FontSize, _('ASSISTANT TECH'));
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(140,$YPos-13,270,$FontSize, ': '.strtoupper($myrow['responsible']),'left');
$pdf->SetFont('');
$LeftOvers = $pdf->addTextWrap(380,$YPos-13,300,$FontSize, _('EXP COMP DATE'),'left');
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(470,$YPos-13,415,$FontSize, ': '.ConvertSQLDate($myrow['expcompletedate']),'left');
$pdf->SetFont('');
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-26,100,$FontSize, _('TASK DESCRIPTION'));
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(140,$YPos-26,270,$FontSize, ': '.$myrow['taskdescription']);
$pdf->SetFont('');
$LeftOvers = $pdf->addTextWrap(380,$YPos-26,300,$FontSize, _('NO OF DAYS'),'left');
$pdf->SetFont('','B');
$LeftOvers = $pdf->addTextWrap(470,$YPos-26,270,$FontSize, ': '.$myrow['frequencydays'],'left');
$pdf->SetFont('');

$YPos=$YPos+50;
// Draws a box with round corners around line items:
$pdf->RoundRectangle(
	$Left_Margin,// RoundRectangle $XPos.
	$YPos+$FontSize+5,// RoundRectangle $YPos.
	$Page_Width-$Left_Margin-$Right_Margin,// RoundRectangle $Width.
	$YPos+$FontSize-$Bottom_Margin+5,// RoundRectangle $Height.
	10,// RoundRectangle $RadiusX.
	10);// RoundRectangle $RadiusY.

// Line under table headings:
$LineYPos = $YPos - $FontSize -25;
$pdf->line($Page_Width-$Right_Margin, $LineYPos, $Left_Margin, $LineYPos);
$LineYPos = $YPos - $FontSize -75;
$pdf->line($Page_Width-$Right_Margin, $LineYPos, $Left_Margin, $LineYPos);

$YPos -= $FontSize+87;// This is to use addTextWrap's $YPos instead of normal $YPos.

		$YPos -= $line_height;// Increment a line down for the next line item.

		$FontSize = 10;// Font size for the line item.
		//$LeftOvers = $pdf->addText($Left_Margin, $YPos+$FontSize, $FontSize, $myrow2['approvertitle'],'','', 'center');
		$pdf->SetFont('','B');
		//$pdf->line($Page_Width-$Right_Margin, $YPos+$FontSize+10, $Left_Margin, $YPos+$FontSize+10);
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, 'MAINTENANCE REPORT', 'left');
		$pdf->line($Page_Width-$Right_Margin, $YPos+$FontSize-3, $Left_Margin, $YPos+$FontSize-3);
		$YPos -= $line_height+5;// Increment a line down for the next line item.
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, 'REASON FOR REPAIR/SERVICE', 'left');
		$pdf->SetFont('');
		$YPos -= $line_height;
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $myrow['causes']);
		while (mb_strlen($LeftOvers)>0) {
				$YPos -= $line_height;// Increment a line down for the next line item.
				$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $LeftOvers);
				$inline = $line_height;
			}
		$YPos = 515;
		$pdf->line($Page_Width-$Right_Margin, $YPos+20, $Left_Margin, $YPos+20);
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, 'REPAIRS UNDERTAKEN', 'left');
		$pdf->SetFont('');
		$YPos -= $line_height;
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $myrow['repairsused']);
		while (mb_strlen($LeftOvers)>0) {
				$YPos -= $line_height;// Increment a line down for the next line item.
				$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $LeftOvers);
				$inline = $line_height;
			}
		$YPos = 460;
		$pdf->line($Page_Width-$Right_Margin, $YPos+20, $Left_Margin, $YPos+20);
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, 'SPECIAL TOOLS FOR INTERVENTION/MODIFICATION', 'left');
		$pdf->SetFont('');
		$YPos -= $line_height;
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $myrow['toolsused']);
		while (mb_strlen($LeftOvers)>0) {
				$YPos -= $line_height;// Increment a line down for the next line item.
				$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $LeftOvers);
				$inline = $line_height;
			}
		$YPos = 405;
		$pdf->line($Page_Width-$Right_Margin, $YPos+20, $Left_Margin, $YPos+20);
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, 'SPARES USED', 'left');
		$pdf->SetFont('');
		$YPos -= $line_height;
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $myrow['sparesused']);
		while (mb_strlen($LeftOvers)>0) {
				$YPos -= $line_height;// Increment a line down for the next line item.
				$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $LeftOvers);
				$inline = $line_height;
			}
		$YPos = 350;
		$pdf->line($Page_Width-$Right_Margin, $YPos+20, $Left_Margin, $YPos+20);
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, 'COST', 'left');
		$pdf->SetFont('');
		$YPos -= $line_height;
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $myrow['estcost']);
		while (mb_strlen($LeftOvers)>0) {
				$YPos -= $line_height;// Increment a line down for the next line item.
				$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $LeftOvers);
				$inline = $line_height;
			}
		$YPos = 300;
		$pdf->line($Page_Width-$Right_Margin, $YPos+20, $Left_Margin, $YPos+20);
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, 'REPORT/SUGGESTIONS', 'left');
		$pdf->SetFont('');
		$YPos -= $line_height;
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $myrow['suggestions']);
		while (mb_strlen($LeftOvers)>0) {
				$YPos -= $line_height;// Increment a line down for the next line item.
				$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $LeftOvers);
				$inline = $line_height;
			}
		$YPos = 250;
		$pdf->line($Page_Width-$Right_Margin, $YPos+20, $Left_Margin, $YPos+20);
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-5,100,$FontSize, _('START DATE & TIME'));
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap(140,$YPos-5,270,$FontSize, ': '.date("d, M Y h:i A",strtotime($myrow['startdate'])),'left');
		$pdf->SetFont('');
		$LeftOvers = $pdf->addTextWrap(380,$YPos-5,300,$FontSize, _('TOTAL TIME TAKEN'),'left');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-26,100,$FontSize, _('END DATE & TIME'));
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap(140,$YPos-26,270,$FontSize, ': '.date("d, M Y h:i A",strtotime($myrow['enddate'])));
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap(390,$YPos-26,270,$FontSize, secondsToWords($myrow['diff']),'left');
		$pdf->SetFont('');
		$YPos = 190;
		$pdf->line($Page_Width-$Right_Margin, $YPos+20, $Left_Margin, $YPos+20);
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, 'VERIFICATION BY FOREMAN', 'left');
		$pdf->SetFont('');
		$YPos -= $line_height;
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, 'REMARKS : '.$myrow['foremanremarks']);
		while (mb_strlen($LeftOvers)>0) {
				$YPos -= $line_height;// Increment a line down for the next line item.
				$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $LeftOvers);
				$inline = $line_height;
			}
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,250,$FontSize, _('FOREMAN :').$myrow['foreman']);
		$LeftOvers = $pdf->addTextWrap(400,$YPos,150,$FontSize, _('DATE : ').date('d/m/Y H:i:s',strtotime($myrow['foremanremarksdate'])),'right');
		
		$YPos = 130;
		$pdf->line($Page_Width-$Right_Margin, $YPos+20, $Left_Margin, $YPos+20);
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, 'HAND OVER TO USER', 'left');
		$pdf->SetFont('');
		$YPos -= $line_height;
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, 'REMARKS : '.$myrow['userremarks']);
		while (mb_strlen($LeftOvers)>0) {
				$YPos -= $line_height;// Increment a line down for the next line item.
				$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos+$FontSize,520,$FontSize, $LeftOvers);
				$inline = $line_height;
			}
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,100,$FontSize, _('TIME'));
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+70,$YPos,100,$FontSize, ': '.$myrow['timereceived']);
		$pdf->SetFont('');
		$LeftOvers = $pdf->addTextWrap(330,$YPos,100,$FontSize, _('M/C DOWN TIME'),'left');
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap(420,$YPos,150,$FontSize, ': '.$myrow['mcdowntime'],'left');
		$pdf->SetFont('');
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos-20,100,$FontSize, _('USER NAME'));
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+70,$YPos-20,250,$FontSize, ': '.$myrow['user']);
		$pdf->SetFont('');
		$LeftOvers = $pdf->addTextWrap(330,$YPos-20,100,$FontSize, _('DATE'),'left');
		$pdf->SetFont('','B');
		$LeftOvers = $pdf->addTextWrap(420,$YPos-20,100,$FontSize, ': '.date('d/m/Y H:i:s',strtotime($myrow['userremarksdate'])),'left');
		$YPos = 15;
		$LeftOvers = $pdf->addTextWrap($Left_Margin-15,$YPos,100,$FontSize, _('KOFC 53010105'));
		$LeftOvers = $pdf->addTextWrap(500,$YPos,100,$FontSize, _('ISSUE 3 REV 2'));

    $pdf->OutputI($_SESSION['DatabaseName'] . '_NonConformanceReport_' . $_GET['id'] . '_' . date('Y-m-d') . '.pdf');
    $pdf->__destruct();

ob_end_flush();
?>
