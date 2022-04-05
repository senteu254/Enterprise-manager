<?php
ob_start();
if (is_numeric($_GET['id'])){
	$id=$_GET['id'];
	}else{
		session_start();
	$_SESSION['err_msg']='
					<p><strong>Invalid ID Please check and try Again!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:".$mainlink."");
					exit;
	}
$strSQL1 = "SELECT * FROM contract_type WHERE TypeID=$id";
	$objQuery1 = DB_query($strSQL1);
	$Num_Rows1 = DB_num_rows($objQuery1);
	while($row=DB_fetch_array($objQuery1)){
	$id=$row['TypeID'];
	$name=$row['Name'];
	$desc=$row['Type_Description'];
	
	}
if(isset($_POST['Submit'])){
session_start();

$name = $_POST['name'];
$desc = $_POST['desc'];
$user = $_SESSION['Username'];
$date = date("F j, Y");

$insert = "UPDATE contract_type SET Name='$name',Type_Description='$desc',Date_Added='$date',Added_By='$user' WHERE TypeID=$id";

DB_query($insert);
$_SESSION['msg']='
			<p><strong>Contract Type was Updated succesifully!</strong></p>
			<a href="#" class="close">close</a>';
			header("Location:".$mainlink."Type");


}

?>

<div class="box">
					<!-- Box Head -->
					<div class="box-head" style="color: black;">
						<h2>New Contract Type</h2>
					</div>
					<!-- End Box Head -->
					
					<form action="" method="post">
						
						<!-- Form -->
						<div class="form">
						
								<p>
									<span class="req">max 100 symbols</span>
									<label> Type Name <span>(Required Field)</span></label>
									<input type="text" class="field size1" value="<?php echo $name; ?>" name="name" />
								</p>
								<p>
									
									<label>Description <span>(Required Field)</span></label>
									<textarea name="desc" class="field size1"  rows=""><?php echo $desc; ?></textarea>
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
