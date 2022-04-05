
<?php
include('includes/CurrenciesArray.php'); // To get the currency name from the currency code.
ob_start();

	function DB_result($res, $row, $field=0) {
    $res->data_seek($row);
    $datarow = $res->fetch_array();
    return $datarow[$field];
	} 

?>

					<!-- Box Head--> 
					<!--<form action="" method="post" name="check" enctype="multipart/form-data" target="_parent">-->
					<form action="" method="post" enctype="multipart/form-data" name="post">
					<?php 
				//echo '<form method="post" name="check" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=CON&Page=Payment">';
				echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';?>
                  
					<div class="box-head" style="background:#337ab7; color: white; border-radius: 7px; no-repeat 0 0; padding:0 0 0 15px;">
					<h2 class="left">Select Contractor:
						<select class="field small-field" required="true" onChange="this.form.submit();" name="Supplier">
						<option selected="selected" value="">--Please Select A Contractor--</option>
						<?php
						$SQL = "SELECT * FROM suppliers";
						$Query = DB_query($SQL);
						while($row=DB_fetch_array($Query)){
						$id=$row['supplierid'];
						$name=$row['suppname'];
						echo "<option value=".$id."";?><?=$id == ''.$_POST['Supplier'].'' ? ' selected="selected"' : '';?><?php echo ">".$name."</option>";
						}
						?>
						
						</select>
						</h2>
						<div class="right">
						<span class="none">
							<table border="0">
							  <tr>
								<td><label></label><input name="filter" type="text" class="field small-field" /></td>
								<td><input type="submit" name="filterbtn" value="filter Contractor" /></td>
							  </tr>
							</table>			  
						  </span>
						</div>
						
					</div>
					
					<!-- End Box Head -->	
<?php
	
if (isset($_POST['Supplier'])){
	//header('Location:?Application=CON&Page=Payment?Payment&SID='.$_POST['Supplier'].'');
	echo "<script>window.location.href = 'index.php?Application=CON&Page=Payment?Payment&SID='".$_POST['Supplier']."''</script>";
	}
	if($_POST['Supplier']){
	echo '<div class="box">';
	if(!is_numeric($_POST['Supplier']) or $_POST['Supplier']==''){
echo '<div align=center style="font-size:16px; font-weight:bold; color:#FF3366">No Supplier Selected</div>';

}else{
		$id=$_POST['Supplier'];
		?>
		<!--<form action="" method="post" enctype="multipart/form-data" name="post">-->
	<?php echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=CON&Page=Payment" id="form">';
	echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo "<h2>&nbsp;Select Contract:";
	echo '<select class="field small-field" required="true" onchange="this.form.submit();" name="Contract">';
	echo '<option selected="selected" value="">--Please Select A Contract--</option>';
	$SQL = "SELECT  a.Contract_Name,a.Contract_Number,a.ContractID FROM 
							contract_details a
								left join
							contract_assignment b
								on a.ContractID = b.ContractID
						WHERE b.SupplierID='".$_POST['Supplier']."'";
	$Query = DB_query($SQL);
	$Num_Rows = DB_num_rows($Query);
	while($roows=DB_fetch_array($Query)){
	$Contract_name=$roows['Contract_Name'];
	$Contract_ID=$roows['ContractID'];
	$Contract_number=$roows['Contract_Number'];
	
	echo "<option value=".$Contract_ID."";?><?=$Contract_ID == ''.$_POST['Contract'].'' ? ' selected="selected"' : '';?><?php echo ">".$Contract_number."-".$Contract_name."</option>";
	}
	echo "</select></h2>";
	}
	echo '</div>';
	}
	//insert records
	if(isset($_POST['Submit'])){
		
	if(isset($_POST['rate']	)){
	$postrate = $_POST['rate'];
	$foregn = $_POST['paid'];
	$foregn_curr = str_replace(",", "",$foregn);
	if(!is_numeric($foregn_curr)){
	$_SESSION['err_msg']='
				<p><strong>Amount is Invalid!</strong></p>
				<a href="" class="close">close</a>';
				echo "<script>window.location.href = 'index.php?Application=CON&Page=Payment?Payment&SID='".$_POST['Supplier']."'&CID='".$_POST['Contract']."''</script>";
				exit;
	}
	$local_curr = $postrate*$foregn_curr;
	$new_bal=$_POST['amntbalance']-$local_curr;
	}else{
	$postrate = 1;
	$foregn = $_POST['paid'];
	$foregn_curr = str_replace(",", "",$foregn);
	if(!is_numeric($foregn_curr)){
	$_SESSION['err_msg']='
				<p><strong>Amount is Invalid!</strong></p>
				<a href="" class="close">close</a>';
				echo "<script>window.location.href = 'index.php?Application=CON&Page=Payment&SID='".$_POST['Supplier']."'&CID='".$_POST['Contract']."''</script>";
				exit;
	}
	$local_curr = $postrate*$foregn_curr;
	$new_bal=$_POST['amntbalance']-$local_curr;
	}
	
	$contractid = $_POST['Contract'];
	$issued = $_POST['paid'];
	$amnt_issued = str_replace(",", "",$issued);
	if(!is_numeric($amnt_issued)){
	$_SESSION['err_msg']='
				<p><strong>Amount is Invalid!</strong></p>
				<a href="#" class="close">close</a>';
				echo "<script>window.location.href = 'index.php?Application=CON&Page=Payment&SID=".$_POST['Supplier']."&CID=".$_POST['Contract']."'</script>";
				exit;
	}
	$postcurr = $_POST['currency'];
	$remark = $_POST['purpose'];
	$date = date("F j, Y");
	$app_by=$_SESSION['Username'];
	
	if($new_bal <0){
	$_SESSION['err_msg']='
				<p><strong>You Have Exceeded amount allocated</strong></p>
				<a href="" class="close">close</a>';
			echo "<script>window.location.href = 'index.php?Application=CON&Page=Payment&SID=".$_POST['Supplier']."&CID=".$_POST['Contract']."'</script>";
				exit;
	}

	$insert = "Insert into contract_payment (ContractID,Amount_Paid,Currency,Amount_FC,Amount_LC,Exchange_Rate,Balance,Description,Date_Paid,Paid_By) values ('$contractid','$amnt_issued','$postcurr','$foregn_curr','$local_curr','$postrate','$new_bal','$remark','$date','$app_by')";
	
	DB_query($insert);
	$_SESSION['msg']='<p>Payment was Issued and Accounts Updated succesifully!</p>';
	
 echo "<script>window.location.href = 'index.php?Application=CON&Page=Payment&SID=".$_POST['Supplier']."&CID=".$_POST['Contract']."'</script>";
 exit;

	}
	//End insert records	
	
	if (isset($_POST['Contract'])){
	echo "<script>window.location.href = 'index.php?Application=CON&Page=Payment?Payment&SID='".$_POST['Supplier']."'&CID='".$_POST['Contract']."''</script>";
	//header('Location:'.$mainlink.'Payment&SID='.$_POST['Supplier'].'&CID='.$_POST['Contract'].'');
	}

if (isset($_POST['Contract'])){
					
if(!is_numeric($_POST['Contract']) or $_POST['Contract']==''){
echo '<div align=center style="font-size:16px; font-weight:bold; color:#FF3366">No Contract Selected</div>';
exit;
}else{
$id=$_POST['Contract'];
	
	$SQLc = "SELECT * FROM contract_details WHERE ContractID='".$_POST['Contract']."'";
	$objQuery =DB_query($SQLc);
	$Num_Rows = DB_num_rows($objQuery);
	while($row=DB_fetch_array($objQuery)){
	$cname=$row['Contract_Name'];
	$number=$row['Contract_Number'];
	$begin=$row['Begin_Date'];
	$end=$row['End_Date'];
	$amnt=$row['Amount'];
	$curr=$row['Currency'];
	}
	
	$SQ = "SELECT * FROM contract_payment WHERE ContractID='".$_POST['Contract']."'";
	$Query = DB_query($SQ);
	$Num_Rows = DB_num_rows($Query);
	if(!$Num_Rows){
	$balance=$amnt;
	}
	while($rows=DB_fetch_array($Query)){
	$amntpaid=$rows['Amount_Paid'];
	}
	$qry = DB_query("SELECT SUM(Amount_LC) AS total_paid FROM contract_payment WHERE ContractID='".$_POST['Contract']."'");
	$row = DB_fetch_assoc($qry);
	$balance=$amnt-$row['total_paid'];
	
	//track payment
	$SQL = "SELECT PaymentID,Amount_LC,Date_Paid FROM contract_payment WHERE ContractID='".$_POST['Contract']."'";
	$Querytrack = DB_query($SQL);
	//track payment
}

?>
<div class="box">
					<!-- Table -->
					<div class="table">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<th>Contract Assigned</th>
								<td><strong><?php echo $cname;?></strong></td>
								<th>Contract No.</th>
								<th><span class="refno"><? echo $number;?></span></th>
							</tr>
							<tr>
								<th>Begin Date</th>
								<td><?php echo $begin;?></td>
								<th>End Date</th>
								<td><?php echo $end;?></td>
								
								<input type="hidden" id="get" value="<?php echo $curr;?>" name="contract_curr" /> 
								<input type="hidden" value="<?php echo $balance;?>" name="amntbalance" />
							</tr>
							<tr>
								<th>Total Allocation</th> 
								<td style="font-size:14px"><strong><u><?php echo $curr.".".number_format($amnt, 2);?></u></strong></td>
								<th>Balance</th>
								<td style="font-size:14px"><strong><u><?php echo $curr.".".number_format($balance, 2);?></u></strong></td>
							</tr>
						</table>
					</div>
					<!-- Table -->	
					</div>
					
					<div class="box">
					<!-- Box Head -->
					<div class="box-head" style="background:#337ab7; color: white; border-radius: 7px; no-repeat 0 0; padding:0 0 0 15px;">
						<h2 class="left">Payment in Phases</h2>
						
					</div>
						<!-- Form -->
						<div class="form">
						<table style="width:102%; margin-left:-1%;" border="0">
							  <tr>
								<td width="220"><label>Amount Authorized <span>(Required Field)</span></label><input type="text" id="amount" class="field size4" required="true" name="paid" /></td>
								<td width="250"><label>Currency<span>(Required Field)</span></label>
								<select class="field" required="true" onchange="myFunction()" id="post" name="currency">
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
								</td>
								<td width="200"><span id="result"></span></td>
							  </tr>
							  <tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td><span style="font-weight:bold; font-size:14px; text-decoration:underline;" id="local"></span></td>
							  </tr>
							</table>

								<p>
									
									<label>Payment Remarks <span>(Required Field)</span></label>
									<textarea class="field" rows="4" required="true" name="purpose" cols="88"></textarea>
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
				<?php } ?>
				
				

<style>
.ok p{ background-color:#fffac2; width:180px; border:solid 1px #dbd6a2; color:#5e5c40; }
.error { background-color:#f3c598; width:180px; border:solid 1px #e8b084; color:#ba4c32;  }
</style>



				<script>
function myFunction() {
    var post, get, voteable;
    post = String(document.getElementById("post").value);
	get = String(document.getElementById("get").value);
    
	if ( (post == "") || (get == "")){
	
	document.getElementById("result").innerHTML = "<label>Error Message</label><div class=error><p>Please Select the Currency</p></div>";
		
	}else{
        voteable = (post == get) ? "<label>Info Message</label><div class='ok'><p>Payment in Local Currency</p></div>" : "<label>Exchange Rate</label><input type=text onkeyup=Total(); id=rate class=field size4 required=true name=rate />";
		
    document.getElementById("result").innerHTML = voteable;
}
}
function currencyFormat (num) {
    return document.getElementById("get").value+"." + num.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
}
function Total() {
var x = document.getElementById("rate").value;
var y = document.getElementById("amount").value;
var z = x * y;
var w = currencyFormat(z);
document.getElementById("local").innerHTML = w;
}
</script>

