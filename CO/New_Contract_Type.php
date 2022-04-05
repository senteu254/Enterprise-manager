<?php
ob_start();

function DB_result($res, $row, $field=0) {
$res->data_seek($row);
$datarow = $res->fetch_array();
return $datarow[$field];
} 
if(isset($_POST['Submit'])){
session_start();
$InputError = 0;
$name = $_POST['name'];
$desc = $_POST['desc'];
$user = $_SESSION['Username'];
$date = date("F j, Y");
if ( trim($name) == '' ) {
		$InputError = 1;
		prnMsg(_('Type Name may not be empty'),'error');
		$Errors[$i] = 'Type Name';
		$i++;
}elseif ( trim($desc) == '' ) {
		$InputError = 1;
		prnMsg(_('Description may not be empty'),'error');
		$Errors[$i] = 'Description';
		$i++;
	}
	
if (isset($_POST['Submit']) AND $InputError !=1) {
	
$insert = "Insert into contract_type (Name,Type_Description,Added_By,Date_Added) values ('$name','$desc','$user','$date')";

DB_query($insert);
$_SESSION['msg']='
			<p><strong>Contract Type was Inserted succesifully!</strong></p>';
			header("Location:".$mainlink."Type");


}
}

?>
<style> 
  .odd{background-color: white;}
  .even{background-color:#CCCCCC;}   
   </style>
<div class="box-headd" style="width:120%;">
				<div id="navigation">
			<ul>
			     <!--<li><a <?php //if(!empty($class8)){echo $class8;}?> href="<?php //echo $mainlink; ?>System" ><span>System</span></a></li>-->
				<li><a <?php if(!empty($class1)){echo $class1;}?>href="<?php echo $mainlink; ?>Type"><span>Contract Type</span></a></li>
			    <li><a <?php if(!empty($class9)){echo $class9;}?>href="<?php echo $mainlink; ?>Commitee_Roles"><span>Commitee roles</span></a></li>
			</ul>
		</div>
		</div>
		<br>
<div class="box" style="width:120%;">
					<!-- Box Head -->
					<div class="box-head" style="color: black; width:120%;">
						<h2>New Contract Type</h2>
					</div>
					<!-- End Box Head -->
					
					<form action="" method="post">
						
						<!-- Form -->
						<div class="form">
						
								<p>
									<span class="req">max 100 symbols</span>
									<label> Type Name <span>(Required Field)</span></label>
									<input type="text" required="required" class="field size1" name="name" />
								</p>
								<p>
									
									<label>Description <span>(Required Field)</span></label>
									<textarea name="desc" required="required" style="width:710px;" class="field"  rows="3"></textarea>
								</p>
								
									
						</div>
						<!-- End Form -->
						
						<!-- Form Buttons -->
						<div class="buttons">
							<input type="submit" name="Submit" value="Submit" />
						<?php echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';?>
						</div>
						<!-- End Form Buttons -->
					</form>
				</div>
				
				<?php
	
	// Search By Name or Email
	$strSQL = "SELECT  * FROM contract_type ORDER BY TypeID ASC";
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

<div class="box" style="width:120%;">
					<!-- Box Head -->
					<!--<div class="box-head" style="color: black; width:118%;">-->
					<div class="box-head" style="background:#337ab7; color: white; border-radius: 7px; no-repeat 0 0; padding:0 0 0 15px;">
						<h2 class="left">Current Contract Types</h2>
						<div class="right">
							<label>search Type</label>
							<input type="text" class="field small-field" />
							<input type="submit" value="search" />
						</div>
					</div>
					<!-- End Box Head -->	

					<!-- Table -->
					<div class="table">
					<form action="" method="get" enctype="multipart/form-data" name="delete">
					<?php echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';?>
					<br>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<th width="13"><input name="users[]" value="<?=DB_result($objQuery,$i,"TypeID");?>" onClick="toggle(this)"  type="checkbox" class="checkbox" /></th>
								<th>Type ID.</th>
								<th>Type Name</th>
								<th>Description</th>
								<th>Date Added</th>
								<th class="ac">Content Control</th>
							</tr>
							<?php
							
							for($i=$Page_Start;$i<$Page_End;$i++)
							{
							$num=$i+1;
							/* $i++;
							 if($i%2 ==0){$class='even';}else{$class='odd';}
							 echo "<tr class=".$class.">";*/
							?>
							<tr>
								<td><input type="checkbox" class="checkbox" name="users[]" value="<?=DB_result($objQuery,$i,"TypeID");?>" ></td>
								<td><h3><?php $bid=DB_result($objQuery,$i,"TypeID"); echo sprintf('%03d',$bid);?></h3></td>
								<td><?=DB_result($objQuery,$i,"Name");?></td>
								<td><?=DB_result($objQuery,$i,"Type_Description");?></td>
								<td><?=DB_result($objQuery,$i,"Date_Added");?></td>
								<td><a href="Delete_Type.php?id=<?=DB_result($objQuery,$i,"TypeID");?>" class="ico ask del">Delete</a><a href="<?php echo $mainlink; ?>Edit_Type&id=<?=DB_result($objQuery,$i,"TypeID");?>" class="ico ask edit">Edit</a></td>
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
