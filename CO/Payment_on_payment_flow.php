<?php
	function DB_result($res, $row, $field=0) {
    $res->data_seek($row);
    $datarow = $res->fetch_array();
    return $datarow[$field];
} 

	$strSQL = "SELECT  *,a.Description as payment_desc FROM contract_payment a
			   INNER JOIN contract_assignment b ON a.ContractID = b.ContractID
			   INNER JOIN suppliers c ON b.SupplierID = c.SupplierID
			   LEFT JOIN contract_details d ON b.ContractID = d.ContractID
			   WHERE a.status=0
			   GROUP BY a.PaymentID";
			   
	$objQuery = DB_query($strSQL);
	$Num_Rows = DB_num_rows($objQuery);
	
	$strSQL2 = "SELECT  *,a.Description as payment_desc FROM contract_payment a
			   INNER JOIN contract_assignment b ON a.ContractID = b.ContractID
			   INNER JOIN suppliers c ON b.SupplierID = c.SupplierID
			   LEFT JOIN contract_details d ON b.ContractID = d.ContractID
			   WHERE a.status=1
			   GROUP BY a.PaymentID";
			   
	$objQuery2 = DB_query($strSQL2);
	$Num_Rows2 = DB_num_rows($objQuery2);


	$Per_Page = 10;   // Per Page contract_payment

	$Page = $_GET["Num"];
	if(!$_GET["Num"])
	{
		$Page=1;
	}

	$Prev_Page = $Page-1;
	$Next_Page = $Page+1;

	$Page_Start = (($Per_Page*$Page)-$Per_Page);
	if($Num_Rows<=$Per_Page)
	{
		$Num_Pages =1;
	}
	else if(($Num_Rows % $Per_Page)==0)
	{
		$Num_Pages =($Num_Rows/$Per_Page) ;
	}
	else
	{
		$Num_Pages =($Num_Rows/$Per_Page)+1;
		$Num_Pages = (int)$Num_Pages;
	}
	$Page_End = $Per_Page * $Page;
	IF ($Page_End > $Num_Rows)
	{
		$Page_End = $Num_Rows;
	}

	?>
	<style>
.logo{display:none;}
</style>


<div class="box" style="width:140%;">

					<!-- Box Head -->
					<div class="box-head" style="background:#337ab7; width:90%; color: white; border-radius: 7px; no-repeat 0 0; padding:0 0 0 15px;">
						<h2 class="left">Contracts On Payment Flow <span class="logo"><?php //echo $sortby; ?></span></h2>
						<div class="right">
						<span class="none">
							
							<!--<form action="" method="post" enctype="multipart/form-data" target="_parent">-->
							
							<?php 
					echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . 'index.php?Application=CON&Page=Payment_flow" id="form">';
					echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';?>
							<table border="0">
							  <tr>
								<td><label>Search By Contract</label><input name="search" type="text" class="field small-field" /></td>
								<td><input type="submit" name="searchbtn" value="search" /></td>
							  </tr>
							</table>			  
							</form>
						  </span>
						</div>
					</div>
					<br>
					<!-- End Box Head -->	
					<h4>Contract Not yet Invoiced for payment</h4>
					<br>
					<!-- Table -->
					<div class="table">
					<!--<form action="" method="get" enctype="multipart/form-data" name="delete">-->
						<?php 
					echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . 'index.php?Application=CON&Page=Payment_flow" id="form">';
					echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';?>
						<table style="font-size:10px; width:98%;" class="tble" border="0" cellspacing="0" cellpadding="0">
							<tr>
							
								<th width="13px"><?=DB_result($objQuery,$i,"ContractID");?></th>
								
								<th align="left">Contract No.</th>
								<th align="left" width="30%">Description</th>
								<th align="left">Supplier Name</th>
								<th align="left">Amount</th>
								<th align="left">Begin Date</th>
								<th align="left">End Date</th>
								<th align="left">No of Days</th>
							</tr>
							<?php
							for($i=$Page_Start;$i<$Page_End;$i++)
							{
							$num=$i+1;
							?>
							<tr>
							
							  <td><?=DB_result($objQuery,$i,"ContractID");?></td>
								<td><?=DB_result($objQuery,$i,"Contract_Number");?></td>
								<td><?=DB_result($objQuery,$i,"payment_desc");?></td>
								<td><?=DB_result($objQuery,$i,"suppname");?></td>
								<td><? $amnt=DB_result($objQuery,$i,"Amount_Paid"); echo number_format($amnt, 2);?></td>
								<td><?php $begin = DB_result($objQuery,$i,"Begin_Date"); echo date("M, d\<\s\u\p\>S\<\/\s\u\p\> y", strtotime($begin));?></td>
								<td><?php $end = DB_result($objQuery,$i,"End_Date"); echo date("M, d\<\s\u\p\>S\<\/\s\u\p\> y", strtotime($end));?></td>
								<td>
								<?php $end = DB_result($objQuery,$i,"End_Date");
									 $now = strtotime($end); // or your date as well
									 $your_date = strtotime($begin);
									 $datediff = $now - $your_date;
									 $days= floor($datediff/(60*60*24));
									 if($days==1){
									 echo $days." Day";
									 }else{
									 echo $days." Days";
									 }
								
								?>
								</td>
							</tr>
							<?php
							}
							?>
						</table>

					  </form>
</div>


<!--vvvvvvvvvvvvvvvvvv-->
<h4>Contract Invoiced for payment</h4>
<br>
<div class="table">
					<!--<form action="" method="get" enctype="multipart/form-data" name="delete">-->
						<?php 
					echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . 'index.php?Application=CON&Page=Payment_flow" id="form">';
					echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';?>
						<table style="font-size:10px; width:98%;" class="tble" border="0" cellspacing="0" cellpadding="0">
							<tr>
							
								<th width="13px"><?=DB_result($objQuery,$i,"ContractID");?></span></th>
								
								<th align="left">Contract No.</th>
								<th align="left" width="30%">Description</th>
								<th align="left">Supplier Name</th>
								<th align="left">Amount</th>
								<th align="left">Begin Date</th>
								<th align="left">End Date</th>
								<th align="left">No of Days</th>
							</tr>
							<?php
							for($i=$Page_Start;$i<$Page_End;$i++)
							{
							$num=$i+1;
							?>
							<tr>
							
							  <td><?=DB_result($objQuery2,$i,"ContractID");?></td>
								<td><?=DB_result($objQuery2,$i,"Contract_Number");?></td>
								<td><?=DB_result($objQuery2,$i,"payment_desc");?></td>
								<td><?=DB_result($objQuery2,$i,"suppname");?></td>
								<td><? $amnt=DB_result($objQuery2,$i,"Amount_Paid"); echo number_format($amnt, 2);?></td>
								<td><?php $begin = DB_result($objQuery2,$i,"Begin_Date"); echo date("M, d\<\s\u\p\>S\<\/\s\u\p\> y", strtotime($begin));?></td>
								<td><?php $end = DB_result($objQuery2,$i,"End_Date"); echo date("M, d\<\s\u\p\>S\<\/\s\u\p\> y", strtotime($end));?></td>
								<td>
								<?php $end = DB_result($objQuery2,$i,"End_Date");
									 $now = strtotime($end); // or your date as well
									 $your_date = strtotime($begin);
									 $datediff = $now - $your_date;
									 $days= floor($datediff/(60*60*24));
									 if($days==1){
									 echo $days." Day";
									 }else{
									 echo $days." Days";
									 }
								
								?>
								</td>
							</tr>
							<?php
							}
							?>
						</table>

					  </form>
</div>
<!--ccccccccccccccccccc-->
						<!-- Pagging -->
						<div class="pagging">
Total <?= $Num_Rows;?> Records : <?=$Num_Pages;?> Page :
	<?php
	if($Prev_Page)
	{
		echo " <a href='$_SERVER[SCRIPT_NAME]?Page=Payment_flow&Num=$Prev_Page'><< Previous</a> ";
	}

	for($i=1; $i<=$Num_Pages; $i++){
		if($i != $Page)
		{
			echo "<a href='$_SERVER[SCRIPT_NAME]?Page=Payment_flow&Num=$i'>$i</a>";
		}
		else
		{
			echo "<b> $i </b>";
		}
	}
	if($Page!=$Num_Pages)
	{
		echo " <a href ='$_SERVER[SCRIPT_NAME]?Page=Payment_flow&Num=$Next_Page'>></a> ";
	}
	
	//DB_close();
		
?>

						</div>
						<!-- End Pagging -->
						
					</div>
					<!-- Table -->
					
				</div>

