<?php
if (isset($_POST['Dependent']))
	{
		$a = addslashes("$_POST[name]");
		$b = addslashes("$_POST[rship]");
		$cc = strtotime($_POST['dod']);
		$c = date("Y-m-d",$cc);
		
		$dat = strtotime("now");
		$da = date("Y-m-d",$dat);		
		
		if($c < $da ){
		$qry =DB_query("INSERt INTO dependent_info (emp_id,name,relationship,dob) VALUES('".$_GET[id]."','$a','$b','$c')");
			if ($qry){
				echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Dependent Information added
              </div>';
			
			}
			else
			echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Dependent Information Not Inserted!
              </div>';
				}
				 if($c > $da ){
			echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Date of Birth cannot be Greater than Current Date
              </div>';
	}

 }
 
 
 if (isset($_POST['Update'])) {
		$sql="UPDATE dependent_info SET
				name = '" . $_POST['name'] . "',
				relationship='".$_POST['rship'] ."',
				dob='".$_POST['dod'] ."'
				WHERE dependentid='".$_POST['did']."'";

		$ErrMsg = _('The Dependent Information cannot be updated because');
		$Result=DB_query($sql,$ErrMsg);
		if ($Result){
		echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Dependent Details Successfully Updated!
              </div>';
			}
			else{
			echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
               Dependent Information Not Updated!
              </div>';
				}
				          }
	
 
 if (isset($_GET['Delete'])) {

	$sql="DELETE FROM dependent_info
		WHERE dependentid='".$_GET['did']."'";

	$ErrMsg = _('The Dependent detail cannot be deleted because');
	$Result=DB_query($sql,$ErrMsg);
	if ($Result){
			echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Dependent Details Successfully Deleted!
              </div>';
			}
			else{
			echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
               Dependent Not Deleted!
              </div>';
				}

}

if (isset($_GET['Edit'])) {
	$sql="SELECT * FROM dependent_info
			WHERE dependentid='".$_GET['did']."'";
	$ErrMsg = _('The Job Skill details cannot be retrieved because');
	$result=DB_query($sql,$ErrMsg);
	$myrow=DB_fetch_array($result);
		$_POST['rname']=$myrow['name'];
		$rrship=$myrow['relationship'];
		$_POST['rdob']=$myrow['dob'];
	
}

if (!isset($_POST['name'])){
	$_POST['name']='';
}
if (!isset($_POST['rship'])){
	$_POST['rship']='';
}
if (!isset($_POST['dod'])){
	$_POST['dod']='';
}
?>
<form enctype="multipart/form-data" method="post" class="form-horizontal">
					<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			?>
			
	<div class="panel panel-default" id="addinfo" <?php echo ($_GET['Edit']=='Yes'? '':'style="display:none;"') ?>>
		<div class="panel-heading">Add Information</div>
			<div class="panel-body">
			<div class="form-group">
				<div class="col-md-4">
				<label>Name</label>  
				<input id="kinname1" name="name" type="text" placeholder="Name" value="<?php  echo $_POST['rname']; ?>"  class="form-control input-md" required=""/>
				</div>
			<div class="col-md-3">
				<label>Relationship</label>
				<select id="rship" name="rship" class="form-control">
				<?php
								
				if(isset($rrship) && $rrship=="Spouse"){
				echo '<option selected="selected">Spouse</option>';
				echo '<option>Child</option>' ;
				}elseif(isset($rrship) && $rrship=="Child"){
				echo '<option selected="selected">Child</option>';
				echo '<option>Spouse</option>';
				}
				else{
				?>
				  <OPTION VALUE="">Choose</OPTION>
				   <option>Spouse</option>
				  <option>Child</option>
				  <?php } ?>
				 
				</select>
			  </div>
			<div class="col-md-4">
			<label>Date Of Birth</label> 
			<input id="dates" name="dod" type="text" placeholder="D.O.B" value="<?php  echo $_POST['rdob']; ?>" class="form-control input-md"/>
			</div>
			</div>
                       <!-- Button (Double) -->
							<?php if (isset($_GET['Edit'])) {
						echo '<input type="hidden" name="did" value="'.$_GET['did'].'" />';
						echo '<div class="form-group">
							  <div class="col-md-12">
							  <button id="submit" name="Update" class="btn btn-primary">Update</button>
							  <a href="index.php?Application=HR&Ref=Profile&Link=Dependent&id='.$_GET['id'].'"><input name="" class="btn btn-default" type="button" value="Cancel" /></a>
							  </div>
							</div>';
							} else {
							echo'<div class="form-group">
							  <div class="col-md-12">
							  <button id="submit" name="Dependent" class="btn btn-primary">Enter New</button>
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
							<table class='table table-hover' style='margin-top:10px; width:100%;'>
									<thead>
										<tr>
											<th>No.</th>
											<th>Name</th>
											<th>Relationship</th>
											<th>D.O.B</th>
											<th width="100">Actions</th>
										</tr>
									</thead>
							<tbody>
							<?php
							$select = "SELECT * FROM dependent_info WHERE emp_id='".$_GET['id']."'";
							$qry=DB_query($select);
							$i=0;
							if(DB_num_rows($qry)>0){
					         while($row = DB_fetch_array($qry)){
							 $i++;
							
								echo '<tr>
									<td>'.$i.'</td>
									<td>'.$row['name'].'</td>
									<td>'.$row['relationship'].'</td>
									<td>'.$row['dob'].'</td>
									<td><a href="index.php?Application=HR&Ref=Profile&Link=Dependent&Edit=Yes&id='. $_GET['id'] .'&did=' . $row['dependentid'] .'">' . _('Edit') . '</a> || <a href="index.php?Application=HR&Ref=Profile&Link=Dependent&Delete=Yes&id='. $_GET['id'] .'&did=' .$row['dependentid'] .'" onclick="return confirm(\'' . _('Are you sure you wish to delete this Dependent Details ?') . '\');">' . _('Delete') . '</a></td>
								</tr>';
								}
								}else{
								echo '<td colspan="5" class="alert-danger"><center>No Records Found</center></td>';
								}
								?>
							</tbody>
							</table>

					</form>
<link rel="stylesheet" href="js/smoothness/jquery-ui.css" />
    
    <!-- Load jQuery JS -->
    <script src="js/jquery-1.9.1.js"></script>
    <!-- Load jQuery UI Main JS  -->
    <script src="js/jquery-ui.js"></script>
    
    <!-- Load SCRIPT.JS which will create datepicker for input field  -->
    <script src="HR_Head/script.js"></script>
    
    <link rel="stylesheet" href="HR_Head/runnable.css" />
	
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