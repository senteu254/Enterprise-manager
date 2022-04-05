<?php


	if (isset($_POST['Occurences']))
	{
	
		$a = addslashes("$_POST[type]");	
		$bb = strtotime("$_POST[date]");
		$b = date("Y/m/d",$bb);
		$c = addslashes("$_POST[brief]");	
		
		$qry ="insert into work_occurences (personal_no,type,date,brief) values('".$_GET['id']."','$a','$b','$c')";
		$ErrMsg = _('The authentication details cannot be inserted because');
		$Res=DB_query($qry,$ErrMsg);
		if ($Res){
		prnMsg( _('Occurence Successfully Inserted'), 'success');
			}
			else{
			prnMsg( _('Not Inserted!'), 'error');
				}
		unset($_POST['type']);
		unset($_POST['date']);
		unset($_POST['brief']);
		}
		
		
		if (isset($_POST['Update'])) {
		$sql="UPDATE work_occurences SET
				type = '" . $_POST['type'] . "',
				date='".date('Y/m/d',strtotime("$_POST[date]")) ."',
				brief='" .$_POST['brief']."'
				WHERE id='".$_POST['woid']."'
				AND personal_no='".$_GET['id']."'";

		$ErrMsg = _('The work occurence cannot be updated because');
		$Result=DB_query($sql,$ErrMsg);
		if ($Result){
				prnMsg( _('Occurence Successfully Updated'), 'success');
			}
			
			else{
			prnMsg( _('Not Updated!Please try Again'), 'error');
				}

unset($_POST['type']);
unset($_POST['date']);
unset($_POST['brief']);
	
}

if (isset($_GET['Delete'])) {
	$sql="DELETE FROM work_occurences
		WHERE id='".$_GET['woid']."'";

	$ErrMsg = _('The work occurence cannot be deleted because');
	$Result=DB_query($sql,$ErrMsg);
	if ($Result){
				prnMsg( _('Occurence Successfully Deleted'), 'success');
			}
			else{
			prnMsg( _('Not Deleted! Please try Again'), 'error');
				}

}
	
	?>
<form action=""  enctype="multipart/form-data" id="form" method="post" class="form-horizontal">
<?php
			
echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
							
	if (isset($_GET['Edit'])) {
	
	echo '<input type="hidden" name="woid" value="'.$_GET['woid'].'" />';
	$sql="SELECT * FROM work_occurences
			WHERE id='".$_GET['woid']."'";
	$ErrMsg = _('The work occurence details cannot be retrieved because');
	$result=DB_query($sql,$ErrMsg);
	$myrow=DB_fetch_array($result);
	$type = $myrow['type'];
	$_POST['date'] = $myrow['date'];
	$_POST['brief'] = $myrow['brief'];
}

if (!isset($_POST['type'])){
	$_POST['type']='';
}
if (!isset($_POST['date'])){
	$_POST['date']='';
}
if (!isset($_POST['brief'])){
	 $_POST['brief']= '';
}?>

	<div class="panel panel-default" id="addinfo" <?php echo ($_GET['Edit']=='Yes'? '':'style="display:none;"') ?>>
		<div class="panel-heading">Add Information</div>
			<div class="panel-body">								
	<div class="form-group">
		<div class="col-md-6">
			<label> Occurence Type</label>
			<select id="type" name="type" class="form-control">
				<?php
				
				if(isset($type) && $type=="Absentism/Abandonment"){
				echo '<option selected="selected">Absentism/Abandonment</option>';
				echo '<option>Abuse of office(corruption)</option>
				  <option>Intoxication</option>
				  <option>Violence/Hostility</option>
				  <option>Sabotage(Witholding resoucrces)</option>';
				}elseif(isset($type) && $type=="Abuse of office(corruption)"){
				echo '<option selected="selected">Abuse of office(corruption)</option>';
				echo '<option>Absentism/Abandonment</option>
				  <option>Intoxication</option>
				  <option>Violence/Hostility</option>
				  <option>Sabotage(Witholding resoucrces)</option>';
				}elseif(isset($type) && $type=="Intoxication"){
				echo '<option selected="selected">Intoxication</option>';
				echo '<option>Absentism/Abandonment</option>
				  <option>Abuse of office(corruption)</option>
				  <option>Violence/Hostility</option>
				  <option>Sabotage(Witholding resoucrces)</option>';
				}elseif(isset($type) && $type=="Violence/Hostility"){
				echo '<option selected="selected">Violence/Hostility</option>';
				echo '<option>Absentism/Abandonment</option>
				  <option>Abuse of office(corruption)</option>
				  <option>Intoxication</option>
				  <option>Sabotage(Witholding resoucrces)</option>';
				}elseif(isset($type) && $type=="Sabotage(Witholding resoucrces)"){
				echo '<option selected="selected">Sabotage(Witholding resoucrces)</option>';
				echo '<option>Absentism/Abandonment</option>
				  <option>Abuse of office(corruption)</option>
				  <option>Intoxication</option>
				  <option>Violence/Hostility</option>';
				}
				else{
				?>
				  <OPTION VALUE="">Choose</OPTION>
				  <option>Absentism/Abandonment</option>
				  <option>Abuse of office(corruption)</option>
				  <option>Intoxication</option>
				  <option>Violence/Hostility</option>
				  <option>Sabotage(Witholding resoucrces)</option>
				  <?php } ?>
				 
				  
			</select>
		  </div>
			<?php 
			 echo'<div class="col-md-6">
			  <label>Occurence Date</label>
				<input type="text" id="date" name="date"  value="' . $_POST['date'] . '" " class="form-control"  />
			  </div>
			  </div>
			  <div class="form-group">
			  <div class="col-md-12">  
				<label> Occurence Brief</label>      
						<textarea class="form-control" id="brief" name="brief"   placeholder="Occurence Brief" >' .$_POST['brief'] . '</textarea>
				   </div>
				 </div>';
					 
					 if (isset($_GET['Edit'])) {
	
						echo '<div class="form-group">
							  <div class="col-md-8">
							  <button id="submit" name="Update" class="btn btn-primary">Update</button>
							 <a href="index.php?Application=HR&Ref=Profile&Link=Occurences&id='.$_GET['id'].'"><input name="" class="btn btn-default" type="button" value="Cancel" /></a>
							  </div>
							</div>';
							} else {
							echo'<div class="form-group">
							  <div class="col-md-8">
							  <button id="submit" name="Occurences" class="btn btn-primary">Enter New</button>
							  <input name="" class="btn btn-default" onclick="myCheck2();" type="button" value="Cancel" />
							  </div>
							</div>
						';
						}
						?>
				</div>
				</div>


	<div class="col-md-4">
	<div class="form-group">
	<input name="" class="btn btn-success" id="addbtn" onclick="myCheck();" type="button" value="Add New" />
	</div>
	</div>
<?php

echo '<table class="table table-hover" style="margin-top:10px; width:100%;">
     <tr >
		<th width="50" scope="col">' . _('S/No') . '</th>
		<th>' . _(' Type') . '</th>
		<th>' . _(' Date') . '</th>
		<th>' . _('  Brief') . '</th>
		<th width="100">' . _('Actions') . '</th>
    </tr>';

								$select = "SELECT * FROM work_occurences where personal_no='".$_GET['id']."'";
									$i=0;
								$ErrMsg = _('The work occurence details cannot be retrieved because');
								$qry1=DB_query($select,$ErrMsg);
								if(DB_num_rows($qry1)>0){
								while($rec = DB_fetch_array($qry1)){
								$i++;
									echo '<tr>';
									
											echo'<td>' . $i . '</td>
											<td>' . $rec['type'] . '</td>
											<td>' . $rec['date'] . '</td>
											<td>' . $rec['brief'] . '</td>
											<td width="50"><a href="index.php?Application=HR&Ref=Profile&Link=Occurences&Edit=Yes&id='. $_GET['id'] .'&woid=' . $rec['id'] .'">' . _('Edit') . '</a> || <a href="index.php?Application=HR&Ref=Profile&Link=Occurences&Delete=Yes&id='. $_GET['id'] .'&woid=' . $rec['id'] .'" onclick="return confirm(\'' . _('Are you sure you wish to delete this Work Occurence?') . '\');">' . _('Delete') . '</a></td>
										</tr>';
								
								}
								}else{
								echo '<td colspan="5" class="alert-danger"><center>No Records Found</center></td>';
								}
echo '</table>';
?>

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
