
	
<!--<form name="drop_list" action="<?php //$_SERVER['PHP_SELF']; ?>" method="post">-->
<?php 
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=CON&Page=Commitee">';
echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';?>

<div class="box" style="width:120%;">
					<!-- Box Head -->
					<div class="box-head" style="background:#337ab7; color: white; border-radius: 7px; no-repeat 0 0; padding:0 0 0 15px;">
					
						<h2 class="left"><span class="logo" >Select User Name:</span>
						<select class="field small-field" required="true" onChange="this.form.submit();" name="id">
						<option selected="selected" value="">--Please Select A User--</option>
						<?php
						$SQL = "SELECT *,userid FROM www_users";
						$Query = DB_query($SQL);
						while($row=DB_fetch_array($Query)){
						$id=$row['userid'];
						$name=$row['realname'];
						echo "<option value=".$id."";?><?=$id == ''.$_POST['id'].'' ? ' selected="selected"' : '';?><?php echo ">".$id."--".$name."</option>";
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

					<!-- Table -->
					<div class="table">
		<?php
		if(is_numeric($_POST['id'])){
		$id=$_POST['id'];
		$strSQL = "SELECT * FROM www_users WHERE userid='".$id."'";
		$objQuery = DB_query($strSQL);
		$Num_Rows = DB_num_rows($objQuery);
		while($rows=DB_fetch_array($objQuery)){
		$sid=$rows['userid'];
		$nameselected=$rows['realname'];
		$phn=$rows['phone'];
		$email=$rows['email'];
		$cper='';
		$cphn='';
		
		}
		?>
						<table style="width:100%; " border="0" cellspacing="0" cellpadding="0">
							<tr>
								<th>Name:</th>
								<td><strong><?php echo $nameselected;?></strong></td>
								<th>Supplier No.:</th>
								<th><?php	echo $invID = 'SN/'.sprintf('%03d',$sid).'/'.date('Y');	?></th>
								
							</tr>
							<tr>
								<th>Phone No:</th>
								<td><?php echo $phn;?></td>
								<th>Email Address:</th>
								<td><a href="#"><?php echo $email;?></a></td>
							</tr>
							<tr>
								<th>Contact Person:</th>
								<td><?php echo $cper;?></td>
								<th>Phone No.:</th>
								<td><?php echo $cphn;?></td>
							</tr>
						</table>
						<?php
						}else{
						?>
					<div align="center" id="txtHint"><b><span style="font-size:14px">No Supplier Selected!</span></b></div>
						<?php }	?>

					</div>
					<!-- Table -->
					
				</div>

<div class="box" style="width:120%;">
					<!-- Box Head 
					<div class="box-head" style="color: black;">
					-->
					<div class="box-head" style="background:#337ab7; color: white; border-radius: 7px; no-repeat 0 0; padding:0 0 0 15px;">
						<h2 class="left">Contract Assignment</h2>
						
					</div>
					<!-- End Box Head -->	
					<!-- Table -->
					
				<div class="table">
				<?php
				if(isset($_POST['btnAdd'])){
				if($_POST["cont"]==""){
				echo '<div class="msg msg-error"><p>No Contract selected to Assign Supplier No. <strong>'.$invID.'</strong>.</p></div>';
				}else{
					$suppid = $_POST['id'];
					$cont = $_POST['cont'];
					$date = date("d-m-Y");
					$insert = "Insert into contract_assignment (ContractID,SupplierID,Date_Assigned,Assigned_By)
								values ('$cont','$sid','$date','".$_SESSION['UserID']."')";
					DB_query($insert);
					DB_query("UPDATE contract_details SET Status=1 WHERE ContractID=$cont");
					echo '<div class="msg msg-ok"><p>Contract No <strong>'.$cont.'</strong> Assigned to <strong>'.$invID.'</strong> Successfully</p></div>';
					
					}
				}
				if(isset($_POST['btnRemove'])){
				if($_POST["contid"]==""){
				echo '<div class="msg msg-error"><p>No Contract selected for Supplier No. <strong>'.$invID.'</strong>.</p></div>';
				}else{
				$SQLc = "SELECT  ContractID FROM contract_payment WHERE ContractID='" .$_POST["contid"]. "'";
				$Queryc = DB_query($SQLc);
				$num=DB_num_rows($Queryc);
				if($num >0){
				echo '<div class="msg msg-error"><p style="font-size:10px; width=100%;">Contract No. <strong>'.$_POST['contid'].'</strong> Can not be Removed from Supplier No. <strong>'.$invID.'</strong> Because the Contract is already Underway</p></div>';
				}else{
				$result=DB_query("DELETE FROM contract_assignment WHERE ContractID='" .$_POST["contid"]. "'");
				DB_query("UPDATE contract_details SET Status=0 WHERE ContractID='".$_POST["contid"]."'");
				echo '<div class="msg msg-ok"><p>Contract No. <strong>'.$_POST["contid"].'</strong> Removed from Supplier No. <strong>'.$invID.'</strong> Successfully</p></div>';
				}
				}
				}
				?>
	<table border="0" class="tble" cellspacing="0" cellpadding="0">
		<tr>
       <td><label style="font-size:14px; font-weight:bold">Contract Name</label>
		<select size="6" name="cont" style="width: 300px;float:left;">
		<?php
		$SQLc = "SELECT  * FROM  contract_payment a
		         INNER JOIN contract_details b ON a.ContractID=b.ContractID";
		$Queryc = DB_query($SQLc);
		while($rowc=DB_fetch_array($Queryc)){
		$contract=$rowc['Description'];
		$id=$rowc['ContractID'];
		$no=$rowc['Contract_Number'];
		
		echo "<option value=".$id.">".$contract."</option>";
		}
		?>
		</select>
      <td width="110px">
		<input style=" background-color:#0066FF; font-weight:bold; height:30px; width:100px;" name="btnAdd" ;="" value="Assign &gt;" type="submit"><br></br>
		<input style=" background-color:#CC3300; font-weight:bold; height:30px; width:100px;" name="btnRemove" ;="" value="&lt;Remove" type="submit">
	</td>
    <td>
<label style="font-size:14px; font-weight:bold"><?php if(isset($nameselected)){echo "Assigned to ".$nameselected;}else{echo "";} ?></label>
	<select   size="6" name="contid" style="width: 300px;float:left;">
	<?php
	$S = "SELECT  contract_details.Contract_Name,contract_details.Contract_Number,contract_assignment.ContractID FROM contract_details 
	      INNER JOIN contract_assignment ON contract_details.ContractID=contract_assignment.ContractID WHERE SupplierID=$sid";
	$Q = DB_query($S);
	while($row=DB_fetch_array($Q)){
	$contr=$row['Description'];
	$id2=$row['ContractID'];
	$nom=$row['Contract_Number'];
	
	echo "<option value=".$id2.">".$nom."-".$contr."</option>";
	}
	?>
	</select></td>
  </tr>
  </table>
</div>
<!-- Table -->							
</form>
</body>			
</div>
				
</div> <!-- print -->
