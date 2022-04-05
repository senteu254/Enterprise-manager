<?php
	
	//$PageSecurity=0;
	
	include('includes/session.inc');
	$Title=_('Vote Book Report');
	include('includes/header.inc');

	
	
    echo '<tr><center><td colspan="1"><input name="Back" type="hidden" value=""/><a href="Votebook_Commitment.php">Back to Commitments</a></center></tr>';
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Voted Item Expenses') . '" alt="" />' . ' ' . _(     'Voted Item Expenses') . '</p>';
	echo '<form action="" method="post">';
	echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';
	echo '<table><tr>
			<td>' . _('Search by Voted Item') . ':</td><td>
			<input name="item" type="text" />
			<td>
	     <tr>
		 <tr>
			<td></td><td>
			<input name="Search" type="submit" value="Search" />
			<td>
	     <tr></table>'; 
		 echo '</form>';
		  $search="";
		 if(isset($_POST['Search'])){
		 if(isset($_POST['item']) && $_POST['item'] !=""){
		 $search="where voted_Item  LIKE '%$_POST[item]%'";
		 }else{
		 $search="";
		 }
		 }
      ?>

	<table align="center" style="width:55%">
	<th>
   	<tr style="font-size:10pt">
    <th width="2%">Vote Heads</td>
	<th width="2%">Allocation</td>
	<th width="2%">Supplemetary Allacation</td>
	<th width="2%"> Total Allocations</th>
    <th width="2%"> Total Commitments</th>
	 <th width="2%">Total Payable</th>
	<th width="2%">Payments + Commitments</th>
	<th width="2%">Available Balance</td>
	</th>
   <style> 

  .odd{background-color: white;} 

  .even{background-color:#CCCCCC;} 
  
   </style>
	<?php
   
    
	$i=0;   
	$result=DB_query("SELECT a.lpo_No,             	 
	                         SUM(a.commitments) AS commitments,
							 SUM(a.decommitment) AS decom,
							 b.allocated_Fund,
							 b.suppliementary,
		 					 b.voted_Item,
							 c.OrderNo,
							 c.suppreference,
							 SUM(d.amt) AS amt,
							 d.invoice
							 FROM commitment a
							 INNER JOIN funds_allocations b ON a.voted_Item=b.votecode
							 INNER JOIN supptrans c ON a.lpo_No	=c.OrderNo	
							 INNER JOIN suppallocs d ON c.suppreference=d.invoice
							 GROUP BY a.voted_Item");

	 while($test = DB_fetch_array($result)){
	 $i++;
	 if($i%2 ==0){$class='even';}else{$class='odd';}
	 $id = $test['voucherID'];
     $Cur_Balance=($test['allocated_Fund']-$test['commitments']);
	 $totalAlloc=($test['allocated_Fund']+ $test['suppliementary']);
	 $comm=($test['commitments']-$test['decom']);
	 $sum=($comm + $test['amt']);
	 $Ava_Balance=($totalAlloc-$sum);
	echo "<tr class=".$class." align='center' style=font-size:10pt>";
    echo"<td><center>" .$test['voted_Item']."</font></center></td>"; 
	echo"<td><center>" .locale_number_format($test['allocated_Fund'],2)."</font></center></td>";
	echo"<td><center>" .locale_number_format($test['suppliementary'],2)."</font></center></td>"; 
	echo"<td><center>".locale_number_format($totalAlloc,2). "</center></td>";
	echo"<td><center>".locale_number_format($comm,2). "</center></td>";
	echo"<td><center>".locale_number_format($test['amt'],2). "</center></td>";
	echo"<td><center>".locale_number_format($sum,2). "</center></td>";
	echo"<td><center>".locale_number_format($Ava_Balance,2). "</center></td>";
	
	echo "</tr>";
	$test++;
	 }
	 echo '</table>';
	  echo '<a href="PDFExpensesReport.php">Print PDF</a>';
include('includes/footer.inc');
	?>
		