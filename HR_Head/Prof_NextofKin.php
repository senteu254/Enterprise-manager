<?php
if (isset($_POST['Kin']))
	{
		$a = addslashes("$_POST[name]");
		$b = addslashes("$_POST[rship]");
		$c = addslashes("$_POST[tel]");
		$d = addslashes("$_POST[add]");
		$e = addslashes("$_POST[email]");		
		
		$qry =DB_query("INSERT INTO next_of_kin (emp_id,name,relationship,phoneno,address,email) VALUES('".$_GET[id]."','$a','$b','$c','$d','$e')");
			if ($qry){
				echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Next of Kin Information added
              </div>';
			}else{
			echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Next of Kin Information Not  Added
              </div>';
			}
				}
	
	
	if (isset($_POST['Update'])) {
		$sql="UPDATE next_of_kin SET
				name = '" . $_POST['name'] . "',
				relationship='".$_POST['rship'] ."',
				phoneno='".$_POST['tel'] ."',
				address='".$_POST['add'] ."',
				email='".$_POST['email'] ."'
				WHERE kin_id='".$_POST['kid']."'";

		$ErrMsg = _('The Next of Kin Information cannot be updated because');
		$Result=DB_query($sql,$ErrMsg);
		if ($Result){
		echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Next of Kin Information Successfully Updated
              </div>';
			}else{
			echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
               Next of Kin Information Not Updated!
              </div>';
				}
				          }
	
	
	
	if (isset($_GET['Delete'])) {

	$sql="DELETE FROM next_of_kin
		WHERE kin_id='".$_GET['kid']."'";

	$ErrMsg = _('The Next of Kin detail cannot be deleted because');
	$Result=DB_query($sql,$ErrMsg);
	if ($Result){
				echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Next of Kin  Details Successfully Deleted!
              </div>';
			}
			else{
			echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
               Next of Kin Not Deleted!
              </div>';
				}

}
	?>
<form enctype="multipart/form-data" method="post" class="form-horizontal">
					<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

 if (isset($_GET['Edit'])) {
	
	echo '<input type="hidden" name="kid" value="'.$_GET['kid'].'" />';
	$sql="SELECT * FROM next_of_kin
			WHERE kin_id='".$_GET['kid']."'";
	$ErrMsg = _('The Job Skill details cannot be retrieved because');
	$result=DB_query($sql,$ErrMsg);
	$myrow=DB_fetch_array($result);
		$_POST['rname']=$myrow['name'];
		$_POST['rrship']=$myrow['relationship'];
		$_POST['rtel']=$myrow['phoneno'];
		$_POST['radd']=$myrow['address'];
		$_POST['rmail']=$myrow['email'];
	
}

if (!isset($_POST['name'])){
	$_POST['name']='';
}
if (!isset($_POST['rship'])){
	$_POST['rship']='';
}
if (!isset($_POST['tel'])){
	$_POST['tel']='';
}
if (!isset($_POST['add'])){
	$_POST['add']='';
}
if (!isset($_POST['email'])){
	$_POST['email']='';
}
?>
<div class="panel panel-default" id="addinfo" <?php echo ($_GET['Edit']=='Yes'? '':'style="display:none;"') ?>>
		<div class="panel-heading">Add Information</div>
			<div class="panel-body">
							<div class="form-group"> 
							  <div class="col-md-4">Full Name
							  <input id="kinname1" name="name" type="text" placeholder="Name of kin" value="<?php  echo $_POST['rname']; ?>" class="form-control input-md" required=""/>
								
							  </div>
							  <div class="col-md-4">Relationship
							  <input id="rship" name="rship" type="text" placeholder="Relationship" value="<?php  echo $_POST['rrship']; ?>" class="form-control input-md" required=""/>
								
							  </div>
							  <div class="col-md-4">Telephone No
							  <input id="tel" name="tel" type="text" placeholder="Telephone" value="<?php  echo $_POST['rtel']; ?>" class="form-control input-md" required=""/>
								
							  </div>
							</div>
							
							<div class="form-group">
							 
							  <div class="col-md-4">Address
							  <input id="addkin1" name="add" type="text" placeholder="Address"  value="<?php  echo $_POST['radd']; ?>" class="form-control input-md" required=""/>
								
							  </div>  
							  <div class="col-md-4">Email Address
							  <input id="mailkin1" name="email" type="text" placeholder="Email" value="<?php  echo $_POST['rmail']; ?>" class="form-control input-md"/>
								
							  </div>
							</div>
                      <!-- Button (Double) -->
                             <?php if (isset($_GET['Edit'])) {
	
						echo '<div class="form-group">
							  <div class="col-md-12">
							  <button id="submit" name="Update" class="btn btn-primary">Update</button>
							  <a href="index.php?Application=HR&Ref=Profile&Link=Kin&id='.$_GET['id'].'"><input name="" class="btn btn-default" type="button" value="Cancel" /></a>
							  </div>
							</div>';
							} else {
							echo'<div class="form-group">
							  <div class="col-md-12">
							  <button id="submit" name="Kin" class="btn btn-primary">Enter New</button>
							  <input name="" class="btn btn-default" onclick="myCheck2();" type="button" value="Cancel" />
							  </div>
							</div>';
							}
							?>

						</div>
						</div>					
							<div class="col-md-4">
						<div class="form-group">
						<input name="" class="btn btn-success" id="addbtn" onclick="myCheck();" type="button" value="Add New" />
						</div>
						</div>
							<table class='table table-hover' style='margin-top:10px; width:800px;'>
									<thead>
										<tr>
											<th>Id.</th>
											<th>Name</th>
											<th>Relationship</th>
											<th>Email</th>
											<th>Telephone</th>
											<th>Address</th>
											<th width="100">Actions</th>
										</tr>
									</thead>
							<tbody>
								<?php
							$select = "SELECT * FROM next_of_kin WHERE emp_id='".$_GET['id']."'";
							$qry=DB_query($select);
							$i=0;
							if(DB_num_rows($qry)>0){
					         while($row = DB_fetch_array($qry)){
							 $i++;
							
								echo '<tr>
									<td>'.$row['kin_id'].'</td>
									<td>'.$row['name'].'</td>
									<td>'.$row['relationship'].'</td>
									<td>'.$row['email'].'</td>
									<td>'.$row['phoneno'].'</td>
									<td>'.$row['address'].'</td>
									<td><a href="index.php?Application=HR&Ref=Profile&Link=Kin&Edit=Yes&id='. $_GET['id'] .'&kid=' . $row['kin_id'] .'">' . _('Edit') . '</a> || <a href="index.php?Application=HR&Ref=Profile&Link=Kin&Delete=Yes&id='. $_GET['id'] .'&kid=' . $row['kin_id'] .'" onclick="return confirm(\'' . _('Are you sure you wish to delete this Next of Kin Details ?') . '\');">' . _('Delete') . '</a></td>
								</tr>';
								}
								}else{
								echo '<td colspan="7" class="alert-danger"><center>No Records Found</center></td>';
								}
								?>
							</tbody>
							</table>
							
					</form>
			<script type="text/javascript">
	  function myCheck() {
  var addbtn = document.getElementById("addbtn");
  var text = document.getElementById("addinfo");
    text.style.display = "block";
	addbtn.style.display = "none";
}
function myCheck2() {
  var addbtn = document.getElementById("addbtn");
  var text = document.getElementById("addinfo");
    text.style.display = "none";
	addbtn.style.display = "block";
}
</script>