<?php
if (isset($_POST['Academic'])){
		$a = addslashes("$_POST[award]");
		$b = addslashes("$_POST[year]");
		$c = addslashes($_POST['grade']);	
		$d = addslashes($_POST['type']);

		$qry =DB_query("INSERT INTO employee_academics (emp_id,award,year,grade,type) VALUES('".$_GET['id']."','$a','$b','$c','$d')");
			if ($qry){
				echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Qualification Succesfully Added
              </div>';
			}else{
			echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
               Qualification Not Added
              </div>';
			  }

 }
 
 
 if (isset($_POST['UpdateAcademic'])) {
		$sql="UPDATE employee_academics SET
				award = '" . $_POST['award'] . "',
				year='".$_POST['year'] ."',
				grade='".$_POST['grade'] ."'
				WHERE id='".$_POST['aid']."'";

		$ErrMsg = _('The Dependent Information cannot be updated because');
		$Result=DB_query($sql,$ErrMsg);
		if ($Result){
		echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Qualification Information Successfully Updated
              </div>';
			}
			else{
			echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
               Qualification Not Updated
              </div>';
				}
			}
	
 
 if (isset($_GET['Delete'])) {

	$sql="DELETE FROM employee_academics
		WHERE id='".$_GET['aid']."'";

	$ErrMsg = _('The detail cannot be deleted because');
	$Result=DB_query($sql,$ErrMsg);
	if ($Result){
				echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Qualification Information Successfully Deleted
              </div>';
			}
			else{
			echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
               Qualification Not Deleted
              </div>';
				}

}

if (isset($_GET['Edit'])) {
	$sql="SELECT * FROM employee_academics
			WHERE id='".$_GET['aid']."'";
	$ErrMsg = _('The Job Skill details cannot be retrieved because');
	$result=DB_query($sql,$ErrMsg);
	$myrow=DB_fetch_array($result);
		$_POST['award']=$myrow['award'];
		$_POST['year']=$myrow['year'];
		$_POST['grade']=$myrow['grade'];
	
}

	?>
<form enctype="multipart/form-data" method="post" action="index.php?Application=HR&Ref=Profile&Link=Qualifications&id=<?php echo $_GET['id']; ?>" class="form-horizontal">
					<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			echo '<input type="hidden" name="type" value="ACADEMIC" />';
			?>
<div class="col-md-12 col-md-offset-" id="addinfo" <?php echo ($_GET['Edit']=='ACADEMIC'? '':'style="display:none;"') ?>>
	<div class="panel panel-default">
		<div class="panel-heading">Add Information</div>
			<div class="panel-body">
			<div class="form-group">
			<div class="col-md-4">
				<label>	Award/Attainment</label> 
					 <input id="award" name="award" type="text" placeholder="Award/Attainment" value="<?php  echo $_POST['award']; ?>"  class="form-control input-md" required=""/>
				</div>	
			<div class="col-md-4">
				<label>	Year</label> 
					 <input id="year" name="year" type="text" placeholder="Year" value="<?php  echo $_POST['year']; ?>"  class="form-control input-md" required=""/>
				</div>
			<div class="col-md-4">
				<label>	Class/ Grade</label> 
					 <input id="grade" name="grade" type="text" placeholder="Grade" value="<?php  echo $_POST['grade']; ?>"  class="form-control input-md" required=""/>
				</div>
				</div>	
                       <!-- Button (Double) -->
				<?php if (isset($_GET['Edit'])) {
				echo '<input type="hidden" name="aid" value="'.$_GET['aid'].'" />';
						echo'<div class="col-md-12">
							  <div class="form-group">
							  <button id="submit" name="UpdateAcademic" class="btn btn-primary">Update</button>
							  <a href="index.php?Application=HR&Ref=Profile&Link=Qualifications&id='.$_GET['id'].'"><input name="" class="btn btn-default" type="button" value="Cancel" /></a>
							  </div></div>';
							}else{
						echo'<div class="col-md-12">
							  <div class="form-group">
							  <button id="submit" name="Academic" class="btn btn-primary">Enter New</button>
							  <input name="" class="btn btn-default" onclick="myCheck2();" type="button" value="Cancel" />
							  </div></div>';
							}
							?>
				</div>
			</div>
		</div>
						<div class="col-md-4">
						<div class="form-group">
						<input name="" class="btn btn-success" id="addbtn" onclick="myCheck();" type="button" value="Add New" />
						</div>
						</div>
							<table class="table table-hover" style="width:100%;">
									<thead>
										<tr>
											<th>S/No</th>
											<th>Award/Attainment</th>
											<th>Year</th>
											<th>Class/Grade</th>
											<th width="100">Actions</th>
										</tr>
									</thead>
							<tbody>
							<?php
							$select = "SELECT * FROM employee_academics WHERE emp_id='".$_GET['id']."' AND type='ACADEMIC'";
							$qry=DB_query($select);
							$i=0;
							if(DB_num_rows($qry)>0){
					         while($row = DB_fetch_array($qry)){
							 $i++;
								echo '<tr>
									<td>'.$i.'</td>
									<td>'.$row['award'].'</td>
									<td>'.$row['year'].'</td>
									<td>'.$row['grade'].'</td>
									<td><a href="index.php?Application=HR&Ref=Profile&Link=Qualifications&Edit=ACADEMIC&id='. $_GET['id'] .'&aid=' . $row['id'] .'">' . _('Edit') . '</a> || <a href="index.php?Application=HR&Ref=Profile&Link=Qualifications&Delete=Yes&id='. $_GET['id'] .'&aid=' .$row['id'] .'" onclick="return confirm(\'' . _('Are you sure you wish to delete this Qualifications Details ?') . '\');">' . _('Delete') . '</a></td>
								</tr>';
								}
								}else{
								echo '<td colspan="5" class="alert-danger"><center>No Records Found</center></td>';
								}
								?>
							</tbody>
							</table>
							
					</form>
			</div>
		</div>
	</div>

<div class="col-md-12 col-md-offset-">
	<div class="panel panel-primary">
		<div class="panel-heading">Professional/Technical Qualifications (Starting with the Highest)</div>
			<div class="panel-body">
<form enctype="multipart/form-data" method="post" action="index.php?Application=HR&Ref=Profile&Link=Qualifications&id=<?php echo $_GET['id']; ?>" class="form-horizontal">
					<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			echo '<input type="hidden" name="type" value="PROFESSINAL" />';
			?>
<div class="col-md-12 col-md-offset-" id="addinfoProf" <?php echo ($_GET['Edit']=='PROFESSINAL'? '':'style="display:none;"') ?>>
	<div class="panel panel-default">
		<div class="panel-heading">Add Information</div>
			<div class="panel-body">
			<div class="form-group">
			<div class="col-md-4">
				<label>	Award/Attainment</label> 
					 <input id="award" name="award" type="text" placeholder="Award/Attainment" value="<?php  echo $_POST['award']; ?>"  class="form-control input-md" required=""/>
				</div>	
			<div class="col-md-4">
				<label>	Year</label> 
					 <input id="year" name="year" type="text" placeholder="Year" value="<?php  echo $_POST['year']; ?>"  class="form-control input-md" required=""/>
				</div>
			<div class="col-md-4">
				<label>	Class/ Grade</label> 
					 <input id="grade" name="grade" type="text" placeholder="Grade" value="<?php  echo $_POST['grade']; ?>"  class="form-control input-md" required=""/>
				</div>
				</div>	
                       <!-- Button (Double) -->
				<?php if (isset($_GET['Edit'])) {
				echo '<input type="hidden" name="aid" value="'.$_GET['aid'].'" />';
						echo'<div class="col-md-12">
							  <div class="form-group">
							  <button id="submit" name="UpdateAcademic" class="btn btn-primary">Update</button>
							  <a href="index.php?Application=HR&Ref=Profile&Link=Qualifications&id='.$_GET['id'].'"><input name="" class="btn btn-default" type="button" value="Cancel" /></a>
							  </div></div>';
							}else{
						echo'<div class="col-md-12">
							  <div class="form-group">
							  <button id="submit" name="Academic" class="btn btn-primary">Enter New</button>
							  <input name="" class="btn btn-default" onclick="myCheckProf2();" type="button" value="Cancel" />
							  </div></div>';
							}
							?>
				</div>
			</div>
		</div>
						<div class="col-md-4">
						<div class="form-group">
						<input name="" class="btn btn-success" id="addbtnProf" onclick="myCheckProf();" type="button" value="Add New" />
						</div>
						</div>
							<table class="table table-hover" style="width:100%;">
									<thead>
										<tr>
											<th>S/No</th>
											<th>Award/Attainment</th>
											<th>Year</th>
											<th>Class/Grade</th>
											<th width="100">Actions</th>
										</tr>
									</thead>
							<tbody>
							<?php
							$select = "SELECT * FROM employee_academics WHERE emp_id='".$_GET['id']."' AND type='PROFESSINAL'";
							$qry=DB_query($select);
							$i=0;
							if(DB_num_rows($qry)>0){
					         while($row = DB_fetch_array($qry)){
							 $i++;
								echo '<tr>
									<td>'.$i.'</td>
									<td>'.$row['award'].'</td>
									<td>'.$row['year'].'</td>
									<td>'.$row['grade'].'</td>
									<td><a href="index.php?Application=HR&Ref=Profile&Link=Qualifications&Edit=PROFESSINAL&id='. $_GET['id'] .'&aid=' . $row['id'] .'">' . _('Edit') . '</a> || <a href="index.php?Application=HR&Ref=Profile&Link=Qualifications&Delete=Yes&id='. $_GET['id'] .'&aid=' .$row['id'] .'" onclick="return confirm(\'' . _('Are you sure you wish to delete this Qualifications Details ?') . '\');">' . _('Delete') . '</a></td>
								</tr>';
								}
								}else{
								echo '<td colspan="5" class="alert-danger"><center>No Records Found</center></td>';
								}
								?>
							</tbody>
							</table>
							
					</form>					
				</div>
		</div>
	</div>

<div class="col-md-12 col-md-offset-">
	<div class="panel panel-primary">
		<div class="panel-heading">Courses and Training Attended</div>
			<div class="panel-body">
<form enctype="multipart/form-data" method="post" action="index.php?Application=HR&Ref=Profile&Link=Qualifications&id=<?php echo $_GET['id']; ?>" class="form-horizontal">
					<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			echo '<input type="hidden" name="type" value="COURSES" />';
			?>
<div class="col-md-12 col-md-offset-" id="addinfoCourse" <?php echo ($_GET['Edit']=='COURSES'? '':'style="display:none;"') ?>>
	<div class="panel panel-default">
		<div class="panel-heading">Add Information</div>
			<div class="panel-body">
			<div class="form-group">
			<div class="col-md-4">
				<label>Name of Course</label> 
					 <input id="award" name="award" type="text" placeholder="Name of Course" value="<?php  echo $_POST['award']; ?>"  class="form-control input-md" required=""/>
				</div>	
			<div class="col-md-4">
				<label>	Year</label> 
					 <input id="year" name="year" type="text" placeholder="Year" value="<?php  echo $_POST['year']; ?>"  class="form-control input-md" required=""/>
				</div>
			<div class="col-md-4">
				<label>	Duration</label> 
					 <input id="grade" name="grade" type="text" placeholder="Duration" value="<?php  echo $_POST['grade']; ?>"  class="form-control input-md" required=""/>
				</div>
				</div>	
                       <!-- Button (Double) -->
				<?php if (isset($_GET['Edit'])) {
				echo '<input type="hidden" name="aid" value="'.$_GET['aid'].'" />';
						echo'<div class="col-md-12">
							  <div class="form-group">
							  <button id="submit" name="UpdateAcademic" class="btn btn-primary">Update</button>
							  <a href="index.php?Application=HR&Ref=Profile&Link=Qualifications&id='.$_GET['id'].'"><input name="" class="btn btn-default" type="button" value="Cancel" /></a>
							  </div></div>';
							}else{
						echo'<div class="col-md-12">
							  <div class="form-group">
							  <button id="submit" name="Academic" class="btn btn-primary">Enter New</button>
							  <input name="" class="btn btn-default" onclick="myCheckCourse2();" type="button" value="Cancel" />
							  </div></div>';
							}
							?>
				</div>
			</div>
		</div>
						<div class="col-md-4">
						<div class="form-group">
						<input name="" class="btn btn-success" id="addbtnCourse" onclick="myCheckCourse();" type="button" value="Add New" />
						</div>
						</div>
							<table class="table table-hover" style="width:100%;">
									<thead>
										<tr>
											<th>S/No</th>
											<th>Name of Course</th>
											<th>Year</th>
											<th>Duration</th>
											<th width="100">Actions</th>
										</tr>
									</thead>
							<tbody>
							<?php
							$select = "SELECT * FROM employee_academics WHERE emp_id='".$_GET['id']."' AND type='COURSES'";
							$qry=DB_query($select);
							$i=0;
							if(DB_num_rows($qry)>0){
					         while($row = DB_fetch_array($qry)){
							 $i++;
								echo '<tr>
									<td>'.$i.'</td>
									<td>'.$row['award'].'</td>
									<td>'.$row['year'].'</td>
									<td>'.$row['grade'].'</td>
									<td><a href="index.php?Application=HR&Ref=Profile&Link=Qualifications&Edit=COURSES&id='. $_GET['id'] .'&aid=' . $row['id'] .'">' . _('Edit') . '</a> || <a href="index.php?Application=HR&Ref=Profile&Link=Qualifications&Delete=Yes&id='. $_GET['id'] .'&aid=' .$row['id'] .'" onclick="return confirm(\'' . _('Are you sure you wish to delete this Qualifications Details ?') . '\');">' . _('Delete') . '</a></td>
								</tr>';
								}
								}else{
								echo '<td colspan="5" class="alert-danger"><center>No Records Found</center></td>';
								}
								?>
							</tbody>
							</table>
							
					</form>	
				</div>
		</div>
	</div>

<div class="col-md-12 col-md-offset-">
	<div class="panel panel-primary">
		<div class="panel-heading">Current Registration/Membership to Professional Bodies</div>
			<div class="panel-body">
<form enctype="multipart/form-data" method="post" action="index.php?Application=HR&Ref=Profile&Link=Qualifications&id=<?php echo $_GET['id']; ?>" class="form-horizontal">
					<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			echo '<input type="hidden" name="type" value="MEMBERSHIP" />';
			?>
<input id="year" name="year" type="hidden" placeholder="Year" value="<?php  echo date('Y'); ?>"  class="form-control input-md"/>
<div class="col-md-12 col-md-offset-" id="addinfoMember" <?php echo ($_GET['Edit']=='MEMBERSHIP'? '':'style="display:none;"') ?>>
	<div class="panel panel-default">
		<div class="panel-heading">Add Information</div>
			<div class="panel-body">
			<div class="form-group">
			<div class="col-md-6">
				<label>Professional Body</label> 
					 <input id="award" name="award" type="text" placeholder="Name of Course" value="<?php  echo $_POST['award']; ?>"  class="form-control input-md" required=""/>
				</div>
			<div class="col-md-6">
				<label>	Membership Type</label> 
					 <input id="grade" name="grade" type="text" placeholder="Duration" value="<?php  echo $_POST['grade']; ?>"  class="form-control input-md" required=""/>
				</div>
				</div>	
                       <!-- Button (Double) -->
				<?php if (isset($_GET['Edit'])) {
				echo '<input type="hidden" name="aid" value="'.$_GET['aid'].'" />';
						echo'<div class="col-md-12">
							  <div class="form-group">
							  <button id="submit" name="UpdateAcademic" class="btn btn-primary">Update</button>
							  <a href="index.php?Application=HR&Ref=Profile&Link=Qualifications&id='.$_GET['id'].'"><input name="" class="btn btn-default" type="button" value="Cancel" /></a>
							  </div></div>';
							}else{
						echo'<div class="col-md-12">
							  <div class="form-group">
							  <button id="submit" name="Academic" class="btn btn-primary">Enter New</button>
							  <input name="" class="btn btn-default" onclick="myCheckMember2();" type="button" value="Cancel" />
							  </div></div>';
							}
							?>
				</div>
			</div>
		</div>
						<div class="col-md-4">
						<div class="form-group">
						<input name="" class="btn btn-success" id="addbtnMember" onclick="myCheckMember();" type="button" value="Add New" />
						</div>
						</div>
							<table class="table table-hover" style="width:100%;">
									<thead>
										<tr>
											<th>S/No</th>
											<th>Professional Body</th>
											<th>Membership Type</th>
											<th width="100">Actions</th>
										</tr>
									</thead>
							<tbody>
							<?php
							$select = "SELECT * FROM employee_academics WHERE emp_id='".$_GET['id']."' AND type='MEMBERSHIP'";
							$qry=DB_query($select);
							$i=0;
							if(DB_num_rows($qry)>0){
					         while($row = DB_fetch_array($qry)){
							 $i++;
								echo '<tr>
									<td>'.$i.'</td>
									<td>'.$row['award'].'</td>
									<td>'.$row['grade'].'</td>
									<td><a href="index.php?Application=HR&Ref=Profile&Link=Qualifications&Edit=MEMBERSHIP&id='. $_GET['id'] .'&aid=' . $row['id'] .'">' . _('Edit') . '</a> || <a href="index.php?Application=HR&Ref=Profile&Link=Qualifications&Delete=Yes&id='. $_GET['id'] .'&aid=' .$row['id'] .'" onclick="return confirm(\'' . _('Are you sure you wish to delete this Qualifications Details ?') . '\');">' . _('Delete') . '</a></td>
								</tr>';
								}
								}else{
								echo '<td colspan="4" class="alert-danger"><center>No Records Found</center></td>';
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

function myCheckProf() {
  var addbtn = document.getElementById("addbtnProf");
  var text = document.getElementById("addinfoProf");
    text.style.display = "block";
	addbtn.style.display = "none";
}
function myCheckProf2() {
  var addbtn = document.getElementById("addbtnProf");
  var text = document.getElementById("addinfoProf");
    text.style.display = "none";
	addbtn.style.display = "block";
}

function myCheckCourse() {
  var addbtn = document.getElementById("addbtnCourse");
  var text = document.getElementById("addinfoCourse");
    text.style.display = "block";
	addbtn.style.display = "none";
}
function myCheckCourse2() {
  var addbtn = document.getElementById("addbtnCourse");
  var text = document.getElementById("addinfoCourse");
    text.style.display = "none";
	addbtn.style.display = "block";
}

function myCheckMember() {
  var addbtn = document.getElementById("addbtnMember");
  var text = document.getElementById("addinfoMember");
    text.style.display = "block";
	addbtn.style.display = "none";
}
function myCheckMember2() {
  var addbtn = document.getElementById("addbtnMember");
  var text = document.getElementById("addinfoMember");
    text.style.display = "none";
	addbtn.style.display = "block";
}
</script>