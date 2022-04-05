<link rel="stylesheet" type="text/css" href="CO/css/datepickr.css" />
<?php
include('includes/CurrenciesArray.php'); // To get the currency name from the currency code.
	ob_start();
 $id=$_GET['id'];
	
	$strSQL1 = "SELECT * FROM contract_details WHERE ContractID=$id";
	$objQuery1 = DB_query($strSQL1);
	$Num_Rows1 = DB_num_rows($objQuery1);
	while($row=DB_fetch_array($objQuery1)){
	$no=$row['Contract_Number'];
	$name=$row['Contract_Name'];
	$begin=$row['Begin_Date'];
	$end=$row['End_Date'];
	$amt=$row['Amount'];
	$curr=$row['Currency'];
	$desc1=$row['Description'];
	$type=$row['TypeID'];
	}
	$SQLc = "SELECT  ContractID FROM contract_payment WHERE ContractID=$id";
				$Queryc = DB_query($SQLc);
				$num=DB_num_rows($Queryc);
				if($num >0){
				$lock='disabled="disabled"';
				}else{
				$lock="";
				}
	//insert records
	if(isset($_POST['Submit'])){
	
	$name = $_POST['name'];
	$begin2 = $_POST['begin'];
	$end = $_POST['end'];
	$am = $_POST['amnt'];
	$curr = $_POST['curr'];
	$desc = $_POST['descr'];
	$type = $_POST['type'];
	$date = date("d-m-Y");
	$user= $_SESSION['Username'];
	
	$amnt = str_replace(",", "",$am);
	/*if(!is_numeric($amnt)){
	$_SESSION['err_msg']='
				<p><strong>Amount is Invalid!</strong></p>
				<a href="#" class="close">close</a>';
				//header("Location:dashboard.php?Page=Edit_Contract&id=$id");
				echo "<script>window.location.href = 'index.php?Application=CON&Page=Edit_Contract&id=$id'</script>";
				exit;
	}else{*/

	$insert = "UPDATE contract_details SET Contract_Name='$name',TypeID='$type',Begin_Date='$begin2',End_Date='$end',Amount='$amnt',Currency='$curr',Date_Added='$date',Added_By='$user',Description='$desc' WHERE ContractID=$id";
	
	DB_query($insert);
	$_SESSION['msg']='
				<p><strong>Contract was Updated succesifully!</strong></p>
				<a href="#" class="close">close</a>';
				echo "<script>window.location.href = 'index.php?Application=CON&Page=Contract'</script>";
				exit;
	
	//}
	}
	//End insert records

?>
				
				<div class="box">
					<!-- Box Head -->
					<div class="box-head" style="background:#337ab7; color: white; border-radius: 7px; no-repeat 0 0; padding:0 0 0 15px;">
						<h2 class="left">Add New Contract</h2>
						
					</div>
					<form action="" method="post">
					<?php echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';?>		
						<!-- Form -->
						<div class="form">
								<p>
									
									<label>Contract Number</label>
									
									<span class="field size3"><?php echo $no;  ?></span>
								</p>
								<p>
									<span class="req">max 100 symbols</span>
									<label>Contract Name<span>(Required Field)</span></label>
									<input type="text" value="<?php echo $name;  ?>" class="field size1" required="true" name="name" />
								</p>
								<table width="100%" border="0">
								  <tr>
								  <td><label>Contract Type</label>
								  <select style="width:175px" class="field size4" required="true" name="type">
								  <?php
								  //include_once('inc/config.php');
								  $Q="SELECT TypeID,Name FROM contract_type";
								  $res=DB_query($Q);
								  $num=DB_num_rows($res);
								  if ($num==""){
								  echo "<option value=>No Types Found</option>";
								  }else{
								  while($row=DB_fetch_array($res)){
								  $typeid=$row['TypeID'];
								  $name=$row['Name'];
								  echo "<option value=".$typeid."";?><?=$typeid == ''.$type.'' ? ' selected="selected"' : '';?><?php echo ">".$name."</option>";
								  }
								  }
								  ?>								  
								  </select>
									<span>(Required Field)</span>
								  </td>
									<td>
									<label>Begin Date</label>
								  <input type="text" placeholder="Begin Date" value="<?php echo $begin;  ?>" id="datepick" readonly="true" class="field size4" required="true" name="begin" />
									<span>(Required Field)</span>
									</td>
									<td></td>
									<td>
									<label>End Date</label>
									<input type="text" placeholder="End Date" value="<?php echo $end;  ?>" id="datepick2" readonly="true" class="field size4" required="true" name="end" />
									<span>(Required Field)</span>
									</td>
								  </tr>
								  <tr>
								  <td>
								  <label>Amount </label>
								  <input type="text" <?php echo $lock;  ?> class="field size4" value="<?php echo $amt;  ?>" required="true" name="amnt" />
								  <span>(Required Field)</span>
								  </td>
								  <td>
								  <label>Currency </label>
								  <select <?php echo $lock; ?> style="width:175px" class="field size4" required="true" name="curr">
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
								<label>Description</label>
								<textarea name="descr" style="width:710px;" class="field" rows="3"><?php echo $desc1; ?></textarea>
								</p>	
									
						</div>
						<!-- End Form -->
						
						<!-- Form Buttons -->
						<div class="buttons">
							<input type="submit" name="Submit"  value="Submit" />
						</div>
						<!-- End Form Buttons -->
					</form>
					
				</div>

<script type="text/javascript" src="js/datepickr.js"></script>
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

