<?php
	ob_start();
	
function DB_result($res, $row, $field=0) {
    $res->data_seek($row);
    $datarow = $res->fetch_array();
    return $datarow[$field];
	} 
	//insert records
	if(isset($_POST['Submit'])){
	$nfname = $_POST['fname'];
	$nuser = $_POST['user'];
	$npass = $_POST['pass'];
	$repass = $_POST['repass'];
	$date = date("F j, Y, g:i a");

	$insert = "Insert into user_details (Username,Password,Full_Name,Date_Added) values ('$nuser','$npass','$nfname','$date')";
	
	DB_query($insert);
	$_SESSION['msg']='
				<p><strong>User was Inserted succesifully!</strong></p>
				<a href="#" class="close">close</a>';
				header("Location:".$mainlink."Page=Users");
	
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
						<h2 class="left">New System User</h2>
						
					</div>
					<form action="" onSubmit="return validate();" method="post">
						
						<!-- Form -->
						<div class="form">
						<p>
									<span class="req">max 100 symbols</span>
									<label>Full Name <span>(Required Field)</span></label>
									<input type="text" class="field size1" required="true"  name="fname" />
						</p>
						  <p>
									
							  <label></label>
								<label>Username <span>(Required Field)</span></label>
							  <input type="text" class="field size4" required="true"  name="user" />
							</p>
								<p>
									
									<label>Password <span>(Required Field)</span></label>
									<input type="password" id="pass" class="field size4" required="true"  name="pass" />
								</p>
								<p>
									
									<label>Retype Password <span>(Required Field)</span></label>
									<input type="password" id="repass" class="field size4" required="true"  name="repass" />
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
// Search By Name or Email
	$strSQL = "SELECT  * FROM user_details ORDER BY Date_Added DESC";
	$objQuery = DB_query($conn,$strSQL);
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

<div class="box">
					<!-- Box Head -->
					<div class="box-head" style="color: black;">
						<h2 class="left">Current Users</h2>
						<div class="right">
							<label>search user</label>
							<input type="text" class="field small-field" />
							<input type="submit" value="search" />
						</div>
					</div>
					<!-- End Box Head -->	

					<!-- Table -->
					<div class="table">
					<form action="" method="get" enctype="multipart/form-data" name="delete">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<th width="13"><input name="users[]" value="<?=DB_result($objQuery,$i,"Login_ID");?>" onClick="toggle(this)"  type="checkbox" class="checkbox" /></th>
								<th>Login ID.</th>
								<th>Full Name</th>
								<th>Username</th>
								<th>Date Added</th>
								<th>Content Controls</th>
							</tr>
							<?php
							for($i=$Page_Start;$i<$Page_End;$i++)
							{
							$num=$i+1;
							?>
							<tr>
								<td><input type="checkbox" class="checkbox" name="users[]" value="<?=mysqli_result($objQuery,$i,"Login_ID");?>" ></td>
								<td><?=DB_result($objQuery,$i,"Login_ID");?>/<? echo date('Y') ?></td>
								<td><?=DB_result($objQuery,$i,"Full_Name");?></td>
								<td><?=DB_result($objQuery,$i,"Username");?></td>
								<td><a href="#"><?=DB_result($objQuery,$i,"Date_Added");?></a></td>
								<td><a href="Delete_User.php?id=<?=DB_result($objQuery,$i,"Login_ID");?>" class="ico ask del">Delete</a><a href="dashboard.php?Page=Edit_Users&id=<?=mysqli_result($objQuery,$i,"Login_ID");?>" class="ico ask edit">Edit</a></td>
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



