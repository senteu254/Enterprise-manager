<?php
	//$PageSecurity=0;
	include('includes/session.inc');
	$Title=_('Daily payment Voucher Expenses Report');
	include('includes/header.inc');
	if (isset($_GET['BookNo'])) {
	$_POST['BookNo'] = $_GET['BookNo'];
}
	if (isset($_GET['Yearend'])) {
	$_POST['Yearend'] = $_GET['Yearend'];
}
    //echo '<tr><center><td colspan="1"><input name="Back" type="hidden" value=""/><a href="Votebook_Commitment.php">Back to Commitments</a></center></tr>';
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Daily payment Voucher Expenses Report') . '" alt="" />' . ' ' . _(     'Daily payment voucher Expenses Report') . '</p>';
	echo '<form action="" method="post">';
	echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';
	
	echo '<table><tr>
		<td>' . _('Select Book No.') . ':</td>
		<td>';

	$result2 = DB_query("SELECT bookid, bookName FROM votebookmaintenance ORDER BY bookid");
	if (DB_num_rows($result2) == 0) {
		$DataError = 1;
		echo '<tr><td colspan="2">' . prnMsg(_('No books has been defined in the system'), 'error') . '</td></tr>';
	} else {
		// If OK show select box with option selected
		echo '<select name="BookNo">
				<option value="">' . _('All') . '</option>';
		while ($myrow = DB_fetch_array($result2)) {
			if ($_POST['BookNo'] == $myrow['bookName']) {
				echo '<option selected="selected" value="' . $myrow['bookName'] . '">' . $myrow['bookName'] . '</option>';
			}
			else {
				echo '<option value="' . $myrow['bookName'] . '">' . $myrow['bookName'] . '</option>';
			}
		} //end while loop
		DB_data_seek($result2, 0);
		echo '</select></td>';
	}
/***************************************Financial year*******************************************************************************/
echo'<td>Financial Year</td><td>
<select name="Yearend">';echo'<option value="' .  Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],-1)) . '">'.Date('Y',YearEndDate($_SESSION['YearEnd'],-2)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd'],-1)).'</option>';			   
echo'<option selected value="'.  Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],0)) . '">'.Date('Y',YearEndDate($_SESSION['YearEnd'],-1)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd'],0)).'</option>';

echo'<option value="'.  Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],+1)) . '">'.Date('Y',YearEndDate($_SESSION['YearEnd'],0)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd'],+1)).'</option>';
	echo '</select></td>';
/**********************************************************************************************************************************/
		echo'	<td></td><td>
			<input name="Search" type="submit" value="Search" />
			<td>';
	   
	    echo' </tr></table>'; 
		//////////////////////////////////////
		 echo '</form>';
		 if(isset($_POST['Search'])){
		 if(isset($_POST['BookNo']) && $_POST['BookNo'] !=""){
		 $sort=" WHERE e.Vbook='". $_POST['BookNo'] ."' AND ";
		 }else{
		 $sort =" WHERE ";
		 }
		 if(isset($_POST['Yearend'])){
		 $Fyear="b.Financial_Year='". $_POST['Yearend'] ."'";
		 }else{
		echo'There is Allocation in this Fincial year.';
		 }
		 echo'<table cellpadding="2" class="selection">';
			echo'<tr>
			<th class="ascending">' . _('Votecode') . '</th>
			<th class="ascending">' . _('Vote Heads') . '</th>
				<th>' . _('Allocation') . '</th>
				<th>' . _('Supp Allacation') . '</th>
				<th>'._('Total Allocations').'</th>
				<th>' . _('Total Commitments') . '</th>
				<th>' . _('Total Payable') . '</th>
				<th>' . _('Pay + Commitments') . '</th>
				<th>'._('Available Balance').'</th>
			</tr>';
			?>
   <style> 
  .odd{background-color: white;}
  .even{background-color:#CCCCCC;}   
   </style>
	<?php
	$i=0;   
	$result=DB_query("SELECT 
							 (SELECT SUM(COALESCE(a.commitments,0)) FROM commitment a WHERE a.voted_Item=b.votecode and b.Financial_Year=a.Fyear) AS commitments,
							 (SELECT SUM(COALESCE(a.decommitment,0)) FROM commitment a WHERE a.voted_Item=b.votecode and b.Financial_Year=a.Fyear) AS decom,
							 b.allocated_Fund,
							 b.votecode,
							 b.suppliementary,
		 					 b.voted_Item,
							 e.Votehead,
							 e.Votecode,
							 e.Vbook,
							 d.bookName as groupid,
							 (SELECT SUM(COALESCE(c.Amount,0)) FROM pvpaymenttrans c WHERE c.VoteCode=b.votecode AND c.Fy=b.Financial_Year) AS amt
							 FROM voteheadmaintenance e
							 INNER JOIN funds_allocations b ON b.votecode=e.Votecode
							 INNER JOIN votebookmaintenance d ON e.Vbook=d.bookName
							  ". $sort ." 
							  ".$Fyear."
							 GROUP BY e.Votecode
							 ORDER BY e.Vbook,b.votecode");
	    $group=NULL;
		$AmountGroup =0;
		$totals = 0;
		$payables=0;
	 while($myrow = DB_fetch_array($result)){
   	
	 $i++;
 
		 
	 if($i%2 ==0){$class='even';}else{$class='odd';}
	 $id = $myrow['voucherID'];
	 $code = $myrow['votecode'];
	 $ViewOrderscommited  = $RootPath . '/VotebookOrderscommited.php?VoteCode=' . $myrow['votecode'].'&fyear='.$_POST['Yearend'];
	 $Viewallpayables  = $RootPath . '/VotebookLinePayables.php?VoteCode=' . $myrow['votecode'].'&fyear='.$_POST['Yearend'];
     $Cur_Balance=($myrow['allocated_Fund']-$myrow['commitments']);
	 $totalAlloc=($myrow['allocated_Fund']+$myrow['suppliementary']);
	 $comm=($myrow['commitments']-$myrow['decom']);
	 $sum=($comm + $myrow['amt']);
	 $Ava_Balance=($totalAlloc-$sum);
	 $Ava_Balance2+= $comm;
	 //$amt22=str_replace(',','',$myrow['amt']);//$myrow['Amount'];
	
	  #######################################################################################################################################3
   if($myrow['groupid']!= $group){
   if($AmountGroup>0){
    //echo '<tr><td colspan="7"></td><th>Total :</th><th class="number">'.locale_number_format($Ava_Balance2,2).'</th></tr>';
	}
	 echo '<tr><th colspan="9" style="background-color:#006699; color:white;">'.$myrow['Vbook'].'</th></tr>';
	 $group = $myrow['groupid'];	 
	 }
   $sql333 = DB_query("SELECT *,b.total FROM pvpaymenttrans a
                 LEFT JOIN payment_voucher b ON a.Voucherid=b.voucherid		  
                 WHERE a.VoteCode = '" . $code. "'
				 AND a.Fy='".$_POST['Yearend']."'");
	$payables=0;
		while ($myrow3333 = DB_fetch_array($sql333)) {
	$payables+=str_replace(',','',$myrow3333['Amount']);//$myrow['Amount'];
	}
	$totals_paid=$comm+$payables;
	 $Ava_Balance33=($totalAlloc-$totals_paid);
   #######################################################################################################################################3	
	echo "<tr class=".$class." align='center' style=font-size:10pt>";
	echo"<td>" .$myrow['votecode']."</font></td>"; 
    echo"<td>" .$myrow['Votehead']."</font></td>"; 
	echo"<td style=text-align:right>" .locale_number_format($myrow['allocated_Fund'],2)."</font></td>";
	echo"<td style=text-align:right>" .locale_number_format($myrow['suppliementary'],2)."</font></td>"; 
	echo"<td style=text-align:right>".locale_number_format($totalAlloc,2). "</td>";
	echo"<td style=text-align:right><a href=" . $ViewOrderscommited . ">".locale_number_format($comm,2). "</a></td>";
	echo"<td style=text-align:right><a href=" . $Viewallpayables . ">".locale_number_format($payables,2). "</a></td>";
	//echo"<td style=text-align:right>".locale_number_format($myrow['amt'],2). "</td>";str_replace(',','',$myrow['total']);
	echo"<td style=text-align:right>".locale_number_format($totals_paid,2). "</td>";
	if ($Ava_Balance33<0){
	echo"<td style=text-align:right><font color='red'>".locale_number_format($Ava_Balance33,2). "</font color></td>";
	}else{
	echo"<td style=text-align:right>".locale_number_format($Ava_Balance33,2). "</td>";
	}	
	echo "</tr>";
	$AmountGroup++;
	$myrow++;
	 }	 
	 echo '</table>';
	 echo '<a href="PDFDaily_Payment_Voucher_Expenses.php?book='.$_POST['BookNo'].'&fyear='.$_POST['Yearend'].'">Print PDF</a>';
	  }
	
include('includes/footer.inc');
	?>
		