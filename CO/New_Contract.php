<link rel="stylesheet" type="text/css" href="CO/css/datepickr.css" />
<?php
include('includes/CurrenciesArray.php'); // To get the currency name from the currency code.
if (isset($_GET['SelectedBook'])){
	$SelectedBook = mb_strtoupper($_GET['SelectedBook']);
} elseif (isset($_POST['SelectedBook'])){
	$SelectedBook = mb_strtoupper($_POST['SelectedBook']);
}
	//insert records
	if(isset($_POST['Submit'])){
	$name = $_POST['name'];
	$num = $_POST['num'];
	$begin = $_POST['begin'];
	$end = $_POST['end'];
	$am = $_POST['amnt'];
	$curr =  $_POST['curr'];
	$desc = $_POST['descr'];
	$type = $_POST['type'];
	$date = date("d-m-Y");
	$user= $_SESSION['UserID'];
	
	$amnt = str_replace(",", "",$am);
	if(!is_numeric($amnt)){
	$_SESSION['err_msg']='
				<p><strong>Amount is Invalid!</strong></p>
				<a href="#" class="close">close</a>';
				header("Location:'".$mainlink."'Contract");
				exit;
	}else{
	$query=DB_query("SELECT Contract_Number FROM contract_details WHERE Contract_Number='".$num."'");
	$numrows=DB_num_rows($query);
		if($numrows>0){
		$_SESSION['err_msg']='
				<p><strong>Invalid Contract Number! The Contract Number you entered already Exist</strong></p>
				<a href="#" class="close">close</a>';
				header("Location:'".$mainlink."'Contract");
				exit;
		}else{

	$sql = "INSERT INTO contract_details (Contract_Name,Contract_Number,TypeID,Begin_Date,End_Date,Amount,Currency,Date_Added,Added_By,Description) VALUES                          ('$name','$num','$type','$begin','$end','$amnt','$curr','$date','$user','$desc')";
	
	DB_query($sql);
	$_SESSION['msg']='
				<div style="padding-top:-10%;"><strong>Contract was Added succesifully!</strong></div>
				<a href="#" class="close">close</a>';
				echo "<script>window.location.href = 'index.php?Application=CON&Page=Contract'</script>";
				//header("Location:index.php?Application=CON&Page=Contract");
				exit;
	
	}
	}
	}
	//End insert records
if (isset($_GET['delete'])) {
	$result=DB_query("DELETE FROM contract_details WHERE ContractID='" .$_GET["SelectedBook"]. "'");
	//DB_query($result);
	echo'<div style=""><strong>Contract Deleted Successfully!</strong></div>';
				echo "<script>window.location.href = 'index.php?Application=CON&Page=Contract'</script>";
				//header("Location:index.php?Application=CON&Page=Contract");
				exit;
	
	unset($SelectedBook);
	unset($_GET['delete']);
}
?>
				
				<div class="box" style="width:130%;">
					<!-- Box Head -->
					<div class="box-head" style="background:#337ab7; color: white; border-radius: 7px; no-repeat 0 0; padding:0 0 0 15px;">
						<h2 class="left">Add New Contract</h2>						
					</div>
					<form action="" method="post" enctype="multipart/form-data" name="post">
					 <?php //echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . ''.$mainlink.'Contract">'; ?>
					<?php echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';?>	
						<!-- Form -->
						<div class="form">
								<p>
									
									<label>Contract Number <span>(Required Field)</span></label>
									<input type="text" class="field size4" required="true" name="num" />
								</p>
								<p>
									<span class="req">max 100 symbols</span>
									<label>Contract Name<span>(Required Field)</span></label>
									<input type="text" class="field size1" required="true" name="name" />
								</p>
								<table border="0" style="margin-left:0.2%; width:100%;">
								  <tr>
								  <td><label>Contract Type</label>
								  <select class="field size4" required="true" name="type">
								  <option selected="selected" value="">--Please Select Type--</option>
								  <?php
								  $Q="SELECT TypeID,Name FROM contract_type";
								  $res=DB_query($Q);
								  $num=DB_num_rows($res);
								  if ($num==""){
								  echo "<option value=>No Types Found</option>";
								  }else{
								  while($row=DB_fetch_array($res)){
								  $typeid=$row['TypeID'];
								  $name=$row['Name'];
								  echo "<option value=".$typeid.">".$name."</option>";
								  }
								  }
								  ?>								  
								  </select>
									<span>(Required Field)</span>
								  </td>
									<td>
									<label>Begin Date</label>
								  <input type="text" placeholder="Begin Date" id="datepick" readonly="true" class="field size4" required="true" name="begin" />
									<span>(Required Field)</span>
									</td>
									<td></td>
									<td>
									<label>End Date</label>
									<input type="text" placeholder="End Date" id="datepick2" readonly="true" class="field size4" required="true" name="end" />
									<span>(Required Field)</span>
									</td>
								  </tr>
								  <tr>
								  <td>
								  <label>Amount </label>
								  <input type="text" class="field size4" required="true" name="amnt" />
								  <span>(Required Field)</span>
								  </td>
								  <td>
								  <label>Currency </label>
								  <select  class="field"  name="curr">
								  <option selected="selected" value="">--Please Select Currency--</option>
								   <?php
								  $Qq="SELECT currabrev,
												country,
												hundredsname,
												rate,
												decimalplaces,
												webcart
											FROM currencies";
								  $re=DB_query($Qq);
								  $num=DB_num_rows($re);
								  if ($num==""){
								  echo "<option value=>No Currency Found</option>";
								  }else{
								  while($row=DB_fetch_array($re)){
								  $abb=$row['currabrev'];
								  $name=$CurrencyName[$row['currabrev']];
								   echo "<option value=".$abb.">".$abb.",".$name."</option>";
								  }
								  }
								  ?>	
								  </select>
								  <span>(Required Field)</span>
								  </td>
								  <td></td>
								  </tr>
								</table>
								<p>
								<label>Contract Description</label>
								<textarea name="descr" style="width:720px;" class="field" rows="3"></textarea>
								</p>	
									
						</div>
						<!-- End Form -->
						
						<!-- Form Buttons -->
						<div class="buttons">
							<input type="submit" name="Submit" value="Submit" />
						</div>
						<!-- End Form Buttons -->
					</form>
					
				</div>
	<?php
	function DB_result($res, $row, $field=0) {
    $res->data_seek($row);
    $datarow = $res->fetch_array();
    return $datarow[$field];
	} 
	// Search By Name or Email
	$strSQL = "SELECT  * FROM contract_details a inner join contract_type b on a.TypeID=b.TypeID ORDER BY ContractID ASC";
	$objQuery = DB_query($strSQL);
	$Num_Rows = DB_num_rows($objQuery);


	$Per_Page = 25;   // Per Page

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
<div class="box" style="width:130%;">
					<!-- Box Head -->
					<div class="box-head" style="background:#337ab7; color: white; border-radius: 7px; no-repeat 0 0; padding:0 0 0 15px;">
						<h2 class="left">Current Contracts</h2>
						<div class="right">
							<label>search Contracts</label>
							<input type="text" class="field small-field" />
							<!--<input type="submit" value="search" />-->
							<input type="submit" name="Submit" value="Search" />
						</div>
					</div>
					<!-- End Box Head -->	
                    <br>
					<!-- Table -->
					<div class="table">
					<!--<form action="" method="post" enctype="multipart/form-data" name="delete">-->
					<script type="text/javascript">
					
					//-->
					function confirmationDelete(anchor)
					{
					   var conf = confirm('Are you sure want to delete this Contract?');
					   if(conf)
						  window.location=anchor.attr("href");
					}
					</script>
					
					<form name="form1" method="post" action="">
					<?php echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';?>
						<table style="width:100%;" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<th align="center">Contract No.</th>
								<th>Contract Name</th>
								<th>Type</th>
								<th>Days</th>
								<th>Amount</th>
								<th class="ac">Content Control</th>
							</tr>
							<?php
							for($i=$Page_Start;$i<$Page_End;$i++)
							{
							$num=$i+1;
							?>
							<tr>
								<td align="center"><?=DB_result($objQuery,$i,"Contract_Number");?></td>
								<td><?=DB_result($objQuery,$i,"Contract_Name");?></td>
								<td><span title="<?=DB_result($objQuery,$i,"Type_Description");?>"><?=DB_result($objQuery,$i,"Name");?></span></td>
								<td>
								<?php									
									$begin = DB_result($objQuery,$i,"Begin_Date");  date("M, d\<\s\u\p\>S\<\/\s\u\p\>", strtotime($begin));
									$end =DB_result($objQuery,$i,"End_Date"); date("M, d\<\s\u\p\>S\<\/\s\u\p\> Y", strtotime($end));
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
								<td><?php $amount=DB_result($objQuery,$i,"Amount"); echo number_format($amount, 2);?></td>
								<td>
		<?php echo'<a onclick="javascript:confirmationDelete($(this));return false;" href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Application=CON&Page=Contract&SelectedBook=' . DB_result($objQuery,$i,"ContractID") . '&amp;delete=yes" class="ico ask del">Delete</a>';?>
					<a href="index.php?Application=CON&Page=Edit_Contract&id=<?=DB_result($objQuery,$i,"ContractID");?>"class="ico ask edit">Edit</a></td>
							</tr>
							<?php
							}
							?>
						</table>
						</form>
						

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
	
	//mysqli_close($conn);
		
?>
						</div>
						<!-- End Pagging -->
						
					</div>
					<!-- Table -->
					
				</div>

<script type="text/javascript" src="CO/js/datepickr.js"></script>
		<script type="text/javascript">
			new datepickr('datepick');
			
			new datepickr('datepick2');
			
			new datepickr('datepick3', {
				'fullCurrentMonth': false,
				'dateFormat': 'l, F j'
			});
			
			new datepickr('datepick4', {
				dateFormat: '\\l\\e jS F Y', /* need to double escape characters that you don't want formatted */
				weekdays: ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'],
				months: ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'],
				suffix: { 1: 'er' },
				defaultSuffix: '' /* the suffix that is used if nothing matches the suffix object, default 'th' */
			});
		</script>

