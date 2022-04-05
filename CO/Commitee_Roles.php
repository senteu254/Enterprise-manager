
<?php
ob_start();

//insert records
	if(isset($_POST['Submit'])){
	$pname = $_POST['name'];
	$abbr = $_POST['abbr'];

	$insert = "INSERT INTO commitee_roles (c_id,commitee_role) VALUES ('$pname','$abbr')";
	
	DB_query($insert);
	$_SESSION['msg']='
				<p><strong>Commitee Role was Added succesifully!</strong></p>
				<a href="#" class="close">close</a>';
				//header("Location:".$mainlink."Commitee_Roles");
				echo "<script>window.location.href = 'index.php?Application=CON&Page=Commitee_Roles'</script>";
				exit;
	
	}
	//End insert records
	
	?>			
				
				<div class="box-head" style="background:#337ab7; color: white; border-radius: 7px; no-repeat 0 0; padding:0 0 0 15px;">
				<div id="navigation">
			<ul>
			     <!--<li><a <?php //if(!empty($class8)){echo $class8;}?> href="<?php //echo $mainlink; ?>System" ><span>System</span></a></li>-->
				<li><a <?php if(!empty($class1)){echo $class1;}?>href="<?php echo $mainlink; ?>Type"><span>Contract Type</span></a></li>
			    <li><a <?php if(!empty($class9)){echo $class9;}?>href="<?php echo $mainlink; ?>Commitee_Roles"><span>Commitee roles</span></a></li>
			</ul>
			<br>
		</div>
		</div>
				<div class="box">
					<!-- Box Head -->
					<div class="box-head" style="color: black;">
						<h2 class="left">Add Role Details</h2>
						
					</div>
					 <form enctype="multipart/form-data" action="" method="POST">
					 <?php echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';?>
						
						<!-- Form -->
						<div class="form">
						<p>
									<label>Role Code <span>(Required Field)</span></label>
									<input type="text" class="field size4" required="true" name="name" />
						</p>
						  <p>
									
							  <label></label>
								<label>Role Name <span>(Required Field)</span></label>
							  <input type="text" class="field size4" required="true" name="abbr" />
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


<?php
	function mysqli_result($res, $row, $field=0) {
    $res->data_seek($row);
    $datarow = $res->fetch_array();
    return $datarow[$field];
	} 
	// Search By Name or Email
	$strSQL = "SELECT  * FROM commitee_roles ORDER BY c_id ASC";
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

<div class="box">
					<!-- Box Head -->
					<div class="box-head" style="background:#337ab7; color: white; border-radius: 7px; no-repeat 0 0; padding:0 0 0 15px;">
						<h2 class="left">Current Roles</h2>
						<div class="right">
							<label>Search Role</label>
							<input type="text" class="field small-field" />
							<input type="submit" value="search" />
						</div>
					</div>
					<!-- End Box Head -->	

					<!-- Table -->
					<div class="table">
					<form action="" method="get" enctype="multipart/form-data" name="delete">
					<?php echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';?>
						<table style=" width:100%;" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<th align="center">Role ID</th>
								<th>Role Name</th>
							</tr>
							<?php
							/*for($i=$Page_Start;$i<$Page_End;$i++)
							{
							$num=$i+1;*/
								while($roows=DB_fetch_array($objQuery)){
								$c_id=$roows['c_id'];
								$commitee_role=$roows['commitee_role'];
								
							?>
							<tr>
								<td><?php echo $c_id ;?></td>
								<td><?php echo $commitee_role ;?></td>
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



