<?php
	ob_start();
    
    if(!isset($_SESSION['Username'])){
         header("Location: login.php");
    }
	if (is_numeric($_GET['id'])){
	$id=$_GET['id'];
	}else{
		session_start();
	$_SESSION['err_msg']='
					<p><strong>Invalid ID Please check and try Again!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php");
	}
	require_once('inc/config.php');
	
	$strSQL1 = "SELECT * FROM suppliers WHERE SupplierID=$id";
	$objQuery1 = mysqli_query($conn,$strSQL1) or die ("Error Query [".$strSQL1."]");
	$Num_Rows1 = mysqli_num_rows($objQuery1);
	while($row=mysqli_fetch_array($objQuery1)){
	$add=$row['Postal_Address'];
	$name=$row['Name'];
	$phn=$row['Phone_No'];
	$email=$row['Email_Add'];
	$cper=$row['Contact_Person'];
	$cphn=$row['Contact_No'];
	}
	//insert records
	if(isset($_POST['Submit'])){
	session_start();
	
	require_once('inc/config.php');
	$name = $_POST['name'];
	$email = $_POST['email'];
	$phn = $_POST['phn'];
	$cper = $_POST['cper'];
	$cphn = $_POST['cphn'];
	$add = $_POST['add'];
	$date = date("d-m-Y");
	$user= $_SESSION['Username'];

	$insert = "UPDATE suppliers SET Name='$name',Email_Add='$email',Phone_No='$phn',Contact_Person='$cper',Contact_No='$cphn',Postal_Address='$add',Date_Added='$date',Added_By='$user' WHERE SupplierID=$id";
	
	mysqli_query($conn,$insert) or die('Could not run Query: ' . mysqli_error());
	$_SESSION['msg']='
				<p><strong>Supplier was Updated succesifully!</strong></p>';
				header("Location:dashboard.php?Page=Suppliers");
				exit;
	
	}
	//End insert records

?>
				
				
				<div class="box">
					<!-- Box Head -->
					<div class="box-head">
						<h2 class="left">Edit Supplier Details</h2>
						
					</div>
					<form action="" method="post">
						
						<!-- Form -->
						<div class="form">
								<p>
									<span class="req">max 100 symbols</span>
									<label>Name <span>(Required Field)</span></label>
									<input type="text" class="field size1" required="true" name="name" value="<?php echo $name;  ?>" />
								</p>
								<p>
									
									<label>Postal Address<span>(Required Field)</span></label>
									<input type="text" class="field size1" required="true" name="add" value="<?php echo $add;  ?>" />
								</p>

								<p>
									<span class="req">max 100 symbols</span>
									<label>Email Address<span>(Required Field)</span></label>
									<input type="text" class="field size1" required="true" name="email" value="<?php echo $email;  ?>" />
								</p>
								<p>
									
									<label>Phone No <span>(Required Field)</span></label>
									<input type="text" class="field size1" required="true" name="phn" value="<?php echo $phn;  ?>" />
								</p>
								<p>
									
									<label>Contact Person<span>(Required Field)</span></label>
									<input type="text" class="field size1" required="true" name="cper" value="<?php echo $cper;  ?>" />
								</p>
								<p>
									
									<label>Contact person Phone No <span>(Required Field)</span></label>
									<input type="text" class="field size1" required="true" name="cphn" value="<?php echo $phn;  ?>" />
								</p>
									
						</div>
						<!-- End Form -->
						
						<!-- Form Buttons -->
						<div class="buttons">
							<input type="submit" name="Submit"  class="button" value="Submit" />
						</div>
						<!-- End Form Buttons -->
					</form>
					
				</div>
