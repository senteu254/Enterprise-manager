<?php
	function DB_result($res, $row, $field=0) {
    $res->data_seek($row);
    $datarow = $res->fetch_array();
    return $datarow[$field];
} 

	$strSQL = "SELECT  * FROM contract_details a
			   INNER JOIN contract_assignment b on a.ContractID = b.ContractID
			   INNER JOIN suppliers c on b.SupplierID = c.SupplierID";
	$objQuery = DB_query($strSQL);
	$Num_Rows = DB_num_rows($objQuery);



	$Per_Page = 15;   // Per Page

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
<div id="printarea">
<div class="logo"><title>::<?php echo $compname;?>::</title><table><tr><td><? if($logo ==NULL){echo "<img src='css/images/nologo.jpg' width='200px' height='130px' alt=''>";}else{ echo "<img src='".$logo."' width='200px' height='130px' alt=''>";}?></td><td><h1><? echo $compname;?></h1><h3><? echo $slogan;?></h3><h3><? echo $address;?></h3></td></tr></table></div>

<div class="box">

					<!-- Box Head -->
					<div class="box-head" style="background:#337ab7; color: white; border-radius: 7px; no-repeat 0 0; padding:0 0 0 15px;">
						<h2 class="left">Current Active Contracts <span class="logo"><?php echo $sortby; ?></span></h2>
						<div class="right">
						<span class="none">
							
							<form action="" method="post" enctype="multipart/form-data" target="_parent">
							
							<?php 
					echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=CON&Page=contract_dashboard.php" id="form">';
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
					<!-- End Box Head -->	
					<div class="logo">All Amount stated in <strong>Ksh.</strong>
					</div>
					<!-- Table -->
					<div class="table">
					<!--<form action="" method="get" enctype="multipart/form-data" name="delete">-->
						<?php 
					echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=CON&Page=Dashboard" id="form">';
					echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';?>
						<table style="font-size:10px;"width="100%" class="tble" border="0" cellspacing="0" cellpadding="0">
							<tr>
							
								<th width="13px"><div class="logo" align="center">No.</div><span class="none"><input name="users[]" value="<?=DB_result($objQuery,$i,"ContractID");?>" onClick="toggle(this)"  type="checkbox" class="checkbox" /></span></th>
								
								<th align="left">Contract No.</th>
								<th align="left">Description</th>
								<th align="left">Supplier Name</th>
								<th align="left">Amount</th>
								<th align="left">Begin Date</th>
								<th align="left">No of Days</th>
							</tr>
							<?php
							for($i=$Page_Start;$i<$Page_End;$i++)
							{
							$num=$i+1;
							?>
							<tr>
							
							  <td><div class="logo" align="center"><?php echo $num; ?></div>
							    <span class="none">
							    <input type="checkbox" class="checkbox" name="users[]" value="<?=DB_result($objQuery,$i,"ContractID");?>" />
						      </span></td>
								<td><?=DB_result($objQuery,$i,"Contract_Number");?></td>
								<td><?=DB_result($objQuery,$i,"Contract_Name");?></td>
								<td><?=DB_result($objQuery,$i,"suppname");?></td>
								<td><? DB_result($objQuery,$i,"Amount"); ?></td>
								<td><?php $begin = DB_result($objQuery,$i,"Begin_Date"); echo date("M, d\<\s\u\p\>S\<\/\s\u\p\> y", strtotime($begin));?></td>
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
						<!-- Pagging -->
						<div class="pagging">
												Total <?= $Num_Rows;?> Records : <?=$Num_Pages;?> Page :
	<?php
	if($Prev_Page)
	{
		echo " <a href='$_SERVER[SCRIPT_NAME]?Page=View&Num=$Prev_Page'><< Previous</a> ";
	}

	for($i=1; $i<=$Num_Pages; $i++){
		if($i != $Page)
		{
			echo "<a href='$_SERVER[SCRIPT_NAME]?Page=View&Num=$i'>$i</a>";
		}
		else
		{
			echo "<b> $i </b>";
		}
	}
	if($Page!=$Num_Pages)
	{
		echo " <a href ='$_SERVER[SCRIPT_NAME]?Page=View&Num=$Next_Page'>Next>></a> ";
	}
	
	//DB_close();
		
?>

						</div>
						<!-- End Pagging -->
						
					</div>
					<!-- Table -->
					
				</div>

