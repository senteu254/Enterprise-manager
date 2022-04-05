<?php
	ob_start();
    
    if(!isset($_SESSION['Username'])){
         header("Location: login.php");
    }
if(empty($_GET['id'])){
session_start();
	$_SESSION['err_msg']='
					<p><strong>No Currency was selected!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php?Page=Currency");
					exit;
}
if(!is_numeric($_GET['id'])){
session_start();
	$_SESSION['err_msg']='
					<p><strong>Invalid ID!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php?Page=Currency");
					exit;
}
	$id=$_GET['id'];
	
	require_once('inc/config.php');
	
	$strSQL1 = "SELECT * FROM Currencies WHERE CurrencyID='$id'";
	$objQuery1 = mysqli_query($conn,$strSQL1) or die ("Error Query [".$strSQL1."]");
	$Num_Rows1 = mysqli_num_rows($objQuery1);
	while($row=mysqli_fetch_array($objQuery1)){
	$id=$row['CurrencyID'];
	$name=$row['Name'];
	$abbr=$row['Abbr'];
	}
	//insert records
	if(isset($_POST['Submit'])){
	session_start();
	
	require_once('inc/config.php');
	$name = $_POST['name'];
	$abbr = $_POST['abbr'];
	$insert = "UPDATE currencies SET Name='$name',Abbr='$abbr' WHERE CurrencyID=$id";
	
	mysqli_query($conn,$insert) or die('Could not run Query: ' . mysqli_error($conn));
	$_SESSION['msg']='
				<p><strong>Currency was Updated succesifully!</strong></p>
				<a href="#" class="close">close</a>';
				header("Location:dashboard.php?Page=Currency");
	
	}
	//End insert records

?>

				
				<div class="box">
					<!-- Box Head -->
					<div class="box-head">
						<h2 class="left">Edit Currency Details</h2>
						
					</div>
					<form action="" method="post">
						
						<!-- Form -->
						<div class="form">
						<p>
									<span class="req">max 100 symbols</span>
									<label>Currency Name <span>(Required Field)</span></label>
									<input type="text" class="field size1" required="true" value="<?php echo $name; ?>" name="name" />
						</p>
						  <p>
									
							  <label></label>
								<label>Abbreviation <span>(Required Field)</span></label>
							  <input type="text" class="field size4" required="true" value="<?php echo $abbr; ?>" name="abbr" />
							</p>
			
						</div>
						<!-- End Form -->
						
						<!-- Form Buttons -->
						<div class="buttons">
							<input type="submit" on name="Submit"  class="button" value="Submit" />
						</div>
						<!-- End Form Buttons -->
					</form>
					
				</div>





