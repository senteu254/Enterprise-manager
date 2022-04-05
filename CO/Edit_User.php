<?php
	ob_start();
 if(!isset($_SESSION['Username'])){
         header("Location: login.php");
    }
if(empty($_GET['id'])){
	$_SESSION['err_msg']='
					<p><strong>No User was selected!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:".$mainlink."Users");
					exit;
}
if(!is_numeric($_GET['id'])){
session_start();
	$_SESSION['err_msg']='
					<p><strong>Invalid ID!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:".$mainlink."Users");
					exit;
}
	$id=$_GET['id'];
	
	$strSQL1 = "SELECT * FROM user_details WHERE Username='$id' OR Login_ID='$id'";
	$objQuery1 = DB_query($conn,$strSQL1);
	$Num_Rows1 = DB_num_rows($objQuery1);
	while($row=DB_fetch_array($objQuery1)){
	$id=$row['Login_ID'];
	$user=$row['Username'];
	$pass=$row['Password'];
	$fname=$row['Full_Name'];
	}
	//insert records
	if(isset($_POST['Submit'])){
	
	$nfname = $_POST['fname'];
	$nuser = $_POST['user'];
	$npass = $_POST['pass'];
	$repass = $_POST['repass'];
	$date = date("F j, Y, g:i a");
	$insert = "UPDATE user_details SET Username='$nuser',Password='$npass',Full_Name='$nfname',Date_Added='$date' WHERE Login_ID=$id";
	
	DB_query($insert);
	$_SESSION['msg']='
				<p><strong>User was Updated succesifully!</strong></p>
				<a href="#" class="close">close</a>';
				header("Location:".$mainlink."Users");
	
	}
	//End insert records

?>
<script>
        function validate(){

        var a = document.getElementById("pass").value;
        var b = document.getElementById("repass").value;
        if (a!=b) {
        alert("Passwords do no match");
        return false;
        }
    }
   </script>
				
				<div class="box">
					<!-- Box Head -->
					<div class="box-head" style="color: black;">
						<h2 class="left">Edit User Login Details</h2>
						
					</div>
					<form action="" onSubmit="return validate();" method="post">
						
						<!-- Form -->
						<div class="form">
						<p>
									<span class="req">max 100 symbols</span>
									<label>Full Name <span>(Required Field)</span></label>
									<input type="text" class="field size1" required="true" value="<?php echo $fname; ?>" name="fname" />
						</p>
						  <p>
									
							  <label></label>
								<label>Username <span>(Required Field)</span></label>
							  <input type="text" class="field size4" required="true" value="<?php echo $user; ?>" name="user" />
							</p>
								<p>
									
									<label>Password <span>(Required Field)</span></label>
									<input type="password" class="field size4" id="pass" required="true" value="<?php echo $pass; ?>" name="pass" />
								</p>
								<p>
									
									<label>Retype Password <span>(Required Field)</span></label>
									<input type="password" class="field size4" id="repass" required="true"  name="repass" />
								</p>

									
						</div>
						<!-- End Form -->
						
						<!-- Form Buttons -->
						<div class="buttons">
							<input type="submit" on name="Submit" value="Submit" />
						</div>
						<!-- End Form Buttons -->
					</form>
					
				</div>





