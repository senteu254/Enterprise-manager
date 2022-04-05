<?php
	//$PageSecurity=0;
	include('includes/session.inc');
	$Title=_('Vote Book Report');
	include('includes/header.inc');
	if (isset($_GET['BookNo'])) {
	$_POST['BookNo'] = $_GET['BookNo'];
}
    //echo '<tr><center><td colspan="1"><input name="Back" type="hidden" value=""/><a href="Votebook_Commitment.php">Back to Commitments</a></center></tr>';
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Voted Item Expenses') . '" alt="" />' . ' ' . _(     'Voted Item Expenses') . '</p>';
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


		echo'	<td></td><td>
			<input name="Search" type="submit" value="Search" />
			<td>';
	   
	    echo' </tr></table>'; 
		//////////////////////////////////////
		 echo '</form>';
		 if(isset($_POST['Search'])){
		 if(isset($_POST['BookNo']) && $_POST['BookNo'] !=""){
		 $sort=" WHERE e.Vbook='". $_POST['BookNo'] ."'";
		 }else{
		 $sort ="";
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
							 (SELECT SUM(COALESCE(a.commitments,0)) FROM commitment a WHERE a.voted_Item=b.votecode) AS commitments,
							 (SELECT SUM(COALESCE(a.decommitment,0)) FROM commitment a WHERE a.voted_Item=b.votecode) AS decom,
							 b.allocated_Fund,
							 b.votecode,
							 b.suppliementary,
		 					 b.voted_Item,
							 e.Votehead,
							 e.Votecode,
							 (SELECT SUM(COALESCE(c.amount,0)) FROM votepaymenttrans c WHERE c.VoteCode=b.votecode) AS amt
							 FROM voteheadmaintenance e
							 INNER JOIN funds_allocations b ON b.votecode=e.Votecode
							 ". $sort ."
							 GROUP BY e.Votecode");
  
	 while($myrow = DB_fetch_array($result)){	
	 $i++;
	 if($i%2 ==0){$class='even';}else{$class='odd';}
	 $id = $myrow['voucherID'];
     $Cur_Balance=($myrow['allocated_Fund']-$myrow['commitments']);
	 $totalAlloc=($myrow['allocated_Fund']+$myrow['suppliementary']);
	 $comm=($myrow['commitments']-$myrow['decom']);
	 $sum=($comm + $myrow['amt']);
	 $Ava_Balance=($totalAlloc-$sum);
	 
	echo "<tr class=".$class." align='center' style=font-size:10pt>";
	echo"<td>" .$myrow['votecode']."</font></td>"; 
    echo"<td>" .$myrow['Votehead']."</font></td>"; 
	echo"<td style=text-align:right>" .locale_number_format($myrow['allocated_Fund'],2)."</font></td>";
	echo"<td style=text-align:right>" .locale_number_format($myrow['suppliementary'],2)."</font></td>"; 
	echo"<td style=text-align:right>".locale_number_format($totalAlloc,2). "</td>";
	echo"<td style=text-align:right>".locale_number_format($comm,2). "</td>";
	echo"<td style=text-align:right>".locale_number_format($myrow['amt'],2). "</td>";
	echo"<td style=text-align:right>".locale_number_format($sum,2). "</td>";
	if ($Ava_Balance<0){
	echo"<td style=text-align:right><font color='red'>".locale_number_format($Ava_Balance,2). "</font color></td>";
	}else{
	echo"<td style=text-align:right>".locale_number_format($Ava_Balance,2). "</td>";
	}	
	echo "</tr>";
	$myrow++;
	 }
	 echo '</table>';
	  echo '<a href="PDFExpensesReport.php">Print PDF</a>';
	  }
	
include('includes/footer.inc');
	?>
		