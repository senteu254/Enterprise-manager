<?php
/* $Id: PDFPeriodStockTransListing.php 4307 2010-12-22 16:06:03Z tim_schofield $*/

include('includes/SQL_CommonFunctions.inc');
include ('includes/session.inc');

$InputError=0;
if (isset($_POST['FromDate']) AND !Is_Date($_POST['FromDate'])){
	$msg = _('The date must be specified in the format') . ' ' . $_SESSION['DefaultDateFormat'];
	$InputError=1;
	unset($_POST['FromDate']);
}



	 $Title = _('Stock Transaction Listing');
	 include ('includes/header.inc');

	echo '<div class="centre">
			<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/transactions.png" title="' . $Title . '" alt="" />' . ' '. _('Stock Transaction Listing') . '</p>
		</div>';

	if ($InputError==1){
		prnMsg($msg,'error');
	}

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">
		<div>
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
		<table class="selection">
		<tr>
			<td>' . _('Enter the date from which the transactions are to be listed') . ':</td>
			<td><input type="text" required="required" autofocus="autofocus" name="FromDate" maxlength="10" size="10" class="date" alt="' . $_SESSION['DefaultDateFormat'] . '" value="' . (isset($_POST['FromDate']) ? $_POST['FromDate'] : Date($_SESSION['DefaultDateFormat'])) . '" /></td>
		</tr>
		<tr>
			<td>' . _('Transaction type') . '</td>
			<td><select name="TransType">
				<option value="26">' . _('Work Order Receipt') . '</option>
				<option value="28">' . _('Work Order Issue') . '</option>
				</select></td>
		</tr>';

	$sql = "SELECT locations.loccode, locationname FROM locations INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1";
	$resultStkLocs = DB_query($sql);

	echo '<tr>
			<td>' . _('For Stock Location') . ':</td>
			<td><select required="required" name="StockLocation">
				<option value="All">' . _('All') . '</option>';

	while ($myrow=DB_fetch_array($resultStkLocs)){
		if (isset($_POST['StockLocation']) AND $_POST['StockLocation']!='All'){
			if ($myrow['loccode'] == $_POST['StockLocation']){
				echo '<option selected="selected" value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			} else {
				echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			}
		} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
			echo '<option selected="selected" value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
			$_POST['StockLocation']=$myrow['loccode'];
		} else {
			echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname'] . '</option>';
		}
	}
	echo '</select></td></tr>';
	
	echo '<tr>
			<td>' . _('Calibre') . ':</td>
			<td><select required="required" name="Calibre">
				<option value="All">' . _('All') . '</option>';
$cal = "SELECT calibre FROM workorders where calibre !='' GROUP BY calibre";
$resultcal = DB_query($cal);
	while ($myrow=DB_fetch_array($resultcal)){
			if ($myrow['calibre'] == $_POST['Calibre']){
				echo '<option selected="selected" value="' . $myrow['calibre'] . '">' . $myrow['calibre'] . '</option>';
			} else {
				echo '<option value="' . $myrow['calibre'] . '">' . $myrow['calibre'] . '</option>';
			}
	}
	echo '</select></td></tr>';

	echo '</table>
			<br />
			<div class="centre">
				<input type="submit" name="Go" value="' . _('Submit') . '" />
			</div>';
    echo '</div>
          </form>';

if (isset($_POST['FromDate'])){
if($_POST['Calibre'] =="All"){
$calibre = "";
}else{
$calibre = "AND workorders.calibre='".$_POST['Calibre']."'";
}

		    $date = FormatDateForSQL($_POST['FromDate']);
			$newdate = $_POST['FromDate'];
			$newdate_1 = Date($_SESSION['DefaultDateFormat'],strtotime ( '-1 day' , strtotime ( $date ) ));
			$newdate_2 = Date($_SESSION['DefaultDateFormat'],strtotime ( '-2 day' , strtotime ( $date ) ));
			$newdate_3 = Date($_SESSION['DefaultDateFormat'],strtotime ( '-3 day' , strtotime ( $date ) ));
			$newdate_4 = Date($_SESSION['DefaultDateFormat'],strtotime ( '-4 day' , strtotime ( $date ) ));
			$newdate_5 = Date($_SESSION['DefaultDateFormat'],strtotime ( '-5 day' , strtotime ( $date ) ));
			$newdate_6 = Date($_SESSION['DefaultDateFormat'],strtotime ( '-6 day' , strtotime ( $date ) ));
			$newdate_7 = Date($_SESSION['DefaultDateFormat'],strtotime ( '-7 day' , strtotime ( $date ) ));
			$newdate_8 = Date($_SESSION['DefaultDateFormat'],strtotime ( '-8 day' , strtotime ( $date ) ));
			$newdate_9 = Date($_SESSION['DefaultDateFormat'],strtotime ( '-9 day' , strtotime ( $date ) ));
			$newdate_10 = Date($_SESSION['DefaultDateFormat'],strtotime ( '-10 day' , strtotime ( $date ) ));
			
		  $sqla =" SELECT workorders.calibre AS CALIBRE, 
		  		stockmoves.stockid AS STOCKID,
				stockmaster.description AS DESCRIPTION,
CASE WHEN date_format(trandate, '%Y-%m-%d') = '".$date."' THEN FORMAT(SUM(stockmoves.qty),0) ELSE '-' END '".$newdate."',
CASE WHEN date_format(trandate, '%Y-%m-%d') =DATE_SUB('".$date."', INTERVAL 1 DAY) THEN FORMAT(SUM(stockmoves.qty),0) ELSE '-' END as '".$newdate_1."',
CASE WHEN date_format(trandate, '%Y-%m-%d') =DATE_SUB('".$date."', INTERVAL 2 DAY) THEN FORMAT(SUM(stockmoves.qty),0) ELSE '-' END as '".$newdate_2."',
CASE WHEN date_format(trandate, '%Y-%m-%d') =DATE_SUB('".$date."', INTERVAL 3 DAY) THEN FORMAT(SUM(stockmoves.qty),0) ELSE '-' END as '".$newdate_3."',
CASE WHEN date_format(trandate, '%Y-%m-%d') =DATE_SUB('".$date."', INTERVAL 4 DAY) THEN FORMAT(SUM(stockmoves.qty),0) ELSE '-' END as '".$newdate_4."',
CASE WHEN date_format(trandate, '%Y-%m-%d') =DATE_SUB('".$date."', INTERVAL 5 DAY) THEN FORMAT(SUM(stockmoves.qty),0) ELSE '-' END as '".$newdate_5."',
CASE WHEN date_format(trandate, '%Y-%m-%d') =DATE_SUB('".$date."', INTERVAL 6 DAY) THEN FORMAT(SUM(stockmoves.qty),0) ELSE '-' END as '".$newdate_6."',
CASE WHEN date_format(trandate, '%Y-%m-%d') =DATE_SUB('".$date."', INTERVAL 7 DAY) THEN FORMAT(SUM(stockmoves.qty),0) ELSE '-' END as '".$newdate_7."',
CASE WHEN date_format(trandate, '%Y-%m-%d') =DATE_SUB('".$date."', INTERVAL 8 DAY) THEN FORMAT(SUM(stockmoves.qty),0) ELSE '-' END as '".$newdate_8."',
CASE WHEN date_format(trandate, '%Y-%m-%d') =DATE_SUB('".$date."', INTERVAL 9 DAY) THEN FORMAT(SUM(stockmoves.qty),0) ELSE '-' END as '".$newdate_9."',
CASE WHEN date_format(trandate, '%Y-%m-%d') =DATE_SUB('".$date."', INTERVAL 10 DAY) THEN FORMAT(SUM(stockmoves.qty),0) ELSE '-' END as '".$newdate_10."'
			FROM stockmoves
			LEFT JOIN stockmaster
			ON stockmoves.stockid=stockmaster.stockid
			LEFT JOIN locations
			ON stockmoves.loccode=locations.loccode
			INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canview=1
			INNER JOIN woitems ON woitems.stockid = stockmoves.stockid
		    INNER JOIN workorders ON workorders.wo = woitems.wo
			WHERE type='".$_POST['TransType']."' 
			".$calibre." 
			AND stockmoves.loccode='".$_POST['StockLocation']."'
			AND (workorders.operator='" . $_SESSION['UserID'] . "' OR workorders.foreman='" . $_SESSION['UserID'] . "' OR " . $_SESSION['CanViewWorkOrder'] . "=1)
			GROUP BY workorders.calibre,stockmoves.stockid";
		$resultS=DB_query($sqla,'','',false,false);
		
		$k=0; //row colour counter
		$Calibre = "";
		echo '<table class="selection">';

		echo '<tr>';
	$fields=mysqli_fetch_fields($resultS);
	foreach($fields as $z => $f){
	echo '<th style="border-left:inset 1px;">'.$f->name.'</td>';
	}
	echo '</tr>';
	while($row=mysqli_fetch_row($resultS)){
			if ($k==1){
				echo '<tr class="EvenTableRows">';
				$k=0;
			} else {
				echo '<tr class="OddTableRows">';
				$k++;
			}
		foreach($row as $x => $sw){
		echo '<td>'.$sw.'</td>';
		}
	echo '</tr>';
	}
		
		echo '</table>';

}
	 include('includes/footer.inc');
	 exit;
?>
