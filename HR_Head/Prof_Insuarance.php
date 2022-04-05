<?php
if (isset($_POST['Insuarance']))
	{
		$a = addslashes("$_POST[name]");
		$b = addslashes("$_POST[no]");	
		
		$qry =DB_query("insert into insuarance_info (emp_id,name,number) values('".$_GET[id]."','$a','$b')");
			if ($qry){
				prnMsg( _('Insurance Information added'), 'success');
			}
			else{
			prnMsg( _('Insurance Information Not Added!'), 'error');
				}
	}
	
	
	if (isset($_POST['Update'])) {
		$sql="UPDATE insuarance_info SET
				name = '" . $_POST['name'] . "',
				number='".$_POST['no'] ."'
				WHERE insuaranceid='".$_POST['iid']."'";

		$ErrMsg = _('The Insurance Information cannot be updated because');
		$Result=DB_query($sql,$ErrMsg);
		if ($Result){
		prnMsg( _('Insurance Information Successfully Updated'), 'success');
			}
			else{
			prnMsg( _(' Insurance Information Not Updated!'), 'error');
				}
				          }
	
	
	
	if (isset($_GET['Delete'])) {

	$sql="DELETE FROM insuarance_info
		WHERE insuaranceid='".$_GET['iid']."'";

	$ErrMsg = _('The Insurance detail cannot be deleted because');
	$Result=DB_query($sql,$ErrMsg);
	if ($Result){
				prnMsg( _('Job Insurance  Details Successfully Deleted!'), 'success');
			}
			else{
			prnMsg( _(' Job Insurance Not Deleted!'), 'error');
				}

}
	?>
<form enctype="multipart/form-data" method="post" class="form-horizontal">
					<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			?>
									<?php if (isset($_GET['Edit'])) {
	echo'<div class="form-group">
							  
							   <div class="col-md-1">
								<input type="hidden"   value="' .$_GET['iid'] . '" " class="form-control" readonly />
							  </div>
							 </div>';
	
	echo '<input type="hidden" name="iid" value="'.$_GET['iid'].'" />';
	$sql="SELECT * FROM insuarance_info
			WHERE insuaranceid='".$_GET['iid']."'";
	$ErrMsg = _('The Job Skill details cannot be retrieved because');
	$result=DB_query($sql,$ErrMsg);
	$myrow=DB_fetch_array($result);
	    $_POST['rname'] = $myrow['name'];
		$_POST['rno']=$myrow['number'];
	
}

if (!isset($_POST['name'])){
	$_POST['name']='';
}
if (!isset($_POST['no'])){
	$_POST['no']='';
}
	
	?>						
	<div class="panel panel-default" id="addinfo" <?php echo ($_GET['Edit']=='Yes'? '':'style="display:none;"') ?>>
		<div class="panel-heading">Add Information</div>
			<div class="panel-body">
	<div class="form-group">
				<div class="col-md-4">
				<label>Insurance Name</label>  
				  <input id="nssf" name="name" type="text" value="<?php  echo $_POST['rname']; ?>" class="form-control input-md" required=""/>
				  </div>
				  <div class="col-md-4">
				  <label>Insurance No.</label>  
				  <input id="other" name="no"  type="text"  value="<?php  echo $_POST['rno']; ?>" class="form-control input-md" required="" />
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
							  <button id="submit" name="Insuarance" class="btn btn-primary">Enter New</button>
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
											<th>Number</th>
											<th width="100">Actions</th>
										</tr>
									</thead>
							<tbody>
							<?php
							$num = 1;
							$select = "SELECT * FROM insuarance_info WHERE emp_id='".$_GET['id']."'";
							$qry=DB_query($select);
							if(DB_num_rows($qry)>0){
					         while($row = DB_fetch_array($qry)){
							
								echo '<tr>
									<td>'.$num.'</td>
									<td>'.$row['name'].'</td>
									<td>'.$row['number'].'</td>
									<td><a href="index.php?Application=HR&Ref=Profile&Link=Insuarance&Edit=Yes&id='. $_GET['id'] .'&iid=' . $row['insuaranceid'] .'">' . _('Edit') . '</a> || <a href="index.php?Application=HR&Ref=Profile&Link=Insuarance&Delete=Yes&id='. $_GET['id'] .'&iid=' . $row['insuaranceid'] .'" onclick="return confirm(\'' . _('Are you sure you wish to delete this Insurance Details ?') . '\');">' . _('Delete') . '</a></td>
								</tr>';
								$num ++;
								}
								}else{
								echo '<td colspan="4" class="alert-danger"><center>No Records Found</center></td>';
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
