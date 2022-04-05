
<?php
	if (isset($_POST['Status'])){
		
		$band = addslashes("$_POST[band]");
		$appointment = addslashes("$_POST[appointment]");
		$category = addslashes("$_POST[appcat]");
		$grade = $_POST['grade'];
		$pos = addslashes("$_POST[emp_pos]");
		$id_pos = addslashes("$_POST[id_pos]");
		$depart = addslashes("$_POST[id_dept]");
		$section= addslashes("$_POST[id_sec]");
		$docapp= FormatDateForSQL($_POST['docapp']);

		
		$qry =DB_query("UPDATE employee SET
					band = '$band',
					appointment_name = '$appointment',
					appointment_category = '$category',
					grade = '$grade',
					emp_pos = '$pos',
					id_pos = '$id_pos',
					id_dept = '$depart',
					id_sec = '$section',
					datecurrentapp ='$docapp'
					WHERE employee.emp_id='".$_GET[id]."'");
			if ($qry){
				echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Employment Details Successfully Updated
              </div>';
			}
			else{
			echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
               Employment Details Not Updated!
              </div>';
				}

	}


function Band(){
$sql="SELECT band_id FROM band";
$result=DB_query($sql);
while ($row=DB_fetch_array($result)) {
    $band_id=$row["band_id"];
    echo "<option value=".$band_id."";?><?=$band_id == ''.$band.'' ? ' selected="selected"' : '';?><?php echo ">".$band_id."</option>";
}
}

 
							?>
							<form action="" id="form" method="post" class="form-horizontal">
							<?php
							echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
							?>
							<div class="form-group">
							  <div class="col-md-4">Band
							<select id='band' name='band' onChange="Gd(this);" class='form-control' >
							<OPTION VALUE="">Choose</OPTION>
							<?php 
							$sql="SELECT band_id FROM band";
							$result=DB_query($sql);
							while ($row=DB_fetch_array($result)) {
							$band_id=$row["band_id"];
							echo "<option value=".$band_id."".($band_id == $band ? ' selected="selected"' : '').">".$band_id."</option>";
							}
							?>
									   
							</select>
		
							  </div>
							  <div class="col-md-4">Appointment Category
						
						<select id='appcat' name='appcat' onChange="Ap(this);" class='form-control' >
							<OPTION VALUE="">Choose</OPTION>
							<?php 
							$sql="SELECT id,description FROM appointment_category";
							$result=DB_query($sql);
							while ($row=DB_fetch_array($result)) {
							$ban=$row["id"];
							echo '<option value="'.$ban.'" '.($ban == $category ? ' selected="selected"' : '').'>'.$row["description"].'</option>';
							}
							?>
									   
							</select>
							  </div>
							<div id="outputapp">
							  <div class="col-md-4">Appointment

								<select id="appointment" name="appointment" class="form-control">
									 <OPTION VALUE="">Choose</OPTION>
									 <?php if(isset($category)){
									 $sql="SELECT appointment_name FROM appointment Where category='".$category."'";
										$result=DB_query($sql);
										while ($row=DB_fetch_array($result)) {
										echo '<option value="'.$row["appointment_name"].'" '.($row["appointment_name"] == $appointment ? ' selected="selected"' : '').'>'.$row["appointment_name"].'</option>';
										}
									}
									 ?>
									   
							</select>
							  </div>
							  </div>
							</div>
							
							  <div class="form-group">
							  <div id="outputgrade">
							  <div class="col-md-4">Grade

								<select id="grade" name="grade" class="form-control">
									 <OPTION VALUE="">Choose</OPTION>
									 <?php if(isset($band)){
									 $sqls="SELECT grade FROM grade Where band_id='".$band."'";
										$results=DB_query($sqls);
										while ($row=DB_fetch_array($results)){
										echo '<option value="'.$row['grade'].'" '.($row["grade"] == $grade ? ' selected="selected"' : '').'>'.$row['grade'].'</option>';
										}
									}
									 ?>
									   
							</select>
							  </div>
							  </div>
							  
							  <div class="col-md-4">Position
								<select id="emp_pos" name="emp_pos" class="form-control">
								<?php
								$err = array('Administrative','Program Head','Section Head','Teaching Personnel','Secretary','Staff');
								foreach($err as $val){
								echo '<option '.($val==$pos ? 'selected':'').' value="'.$val.'">'.$val.'</option>';
								}
								?>
								</select>
							  </div>
							  
							  <div class="col-md-4">Position Type
								   <?php
									   $select = "SELECT * FROM ldays";
									   $qry = DB_query($select);
									   
									   echo "<select id='id_pos' name='id_pos' class='form-control'>";
									   echo "<option>Position Type</option>";
									   while($recs = DB_fetch_array($qry)){
										  echo '<option value="'.$recs['id_pos'].'" '.($recs['id_pos'] == $id_pos ? ' selected="selected"' : '').'>'.$recs['pos_stat'].'</option>';
									   }
										echo"</select>";
									?>
							  </div>
							 
							  </div>
							  
							  
							  
								   <?php
function Dep(){
$sql="SELECT * FROM departments";
$result=DB_query($sql);
while ($row=DB_fetch_array($result)) {
    $depart_name=$row["description"];
    $id_dept=$row["departmentid"];
	echo "<option value=".$id_dept."";?><?=$id_dept == ''.$_POST['id'].'' ? ' selected="selected"' : '';?><?php echo ">".$depart_name."</option>";
}
}
?>

                        <div class="form-group">
							  <div class="col-md-4">Department
							<select id='id_dept' name='id_dept' onChange="Dept(this);" class='form-control'  >
									     <OPTION VALUE="">Choose</OPTION>
									   <?php
									   $sql="SELECT * FROM departments";
							$result=DB_query($sql);
							while ($row=DB_fetch_array($result)) {
								$depart_name=$row["description"];
								$id_dept=$row["departmentid"];
								echo "<option value=".$id_dept."";?><?=$id_dept == ''.$depart.'' ? ' selected="selected"' : '';?><?php echo ">".$depart_name."</option>";
							}
									   ?>
							</select>
		
							  </div>
							
							  <div class="col-md-4">Section
						<div id="section">
						<select id='id_sec' name='id_sec' class='form-control'>
									<OPTION VALUE=0>Choose</OPTION>
									   <?php if(isset($section)){
									   $sql="SELECT * FROM section Where id_sec='".$section."'";
									$result=DB_query($sql);
									$row=DB_fetch_array($result);
										$section_name=$row["section_name"];
										$id_sec=$row["id_sec"];
									echo '<OPTION selected="selected" VALUE='.$section.'>'.$section_name.'</OPTION>';
									}
									 ?> 
							</select>
							</div>
							  </div>
						<div class="col-md-4">Date of Current Appointment
								  <input name="docapp" class="date" <?php echo 'alt="' . $_SESSION['DefaultDateFormat'] .'"'; ?> autocomplete="off" value="<?php echo ConvertSQLDate($docapp); ?>" id="form-control" type="text" />
							  </div>
							</div>
							<!-- Button (Double) -->
							<div class="form-group">
							  <label class="col-md- control-label" for="submit"></label>
							  <div class="col-md-8">
								<div id="btn"></div>
							  </div>
							</div>
							</form>
	<script type="text/javascript" src="js/jquery-1.9.1.js"></script>							
<script>
function Ap(sel) {
	var state_id = sel.options[sel.selectedIndex].value;  
	if (state_id.length > 0 ) { 
	 $.ajax({
			type: "GET",
			url: "Ajax_File.php",
			data: "id="+state_id+"&Request=Appointment",
			cache: false,
			beforeSend: function () { 
				$('#outputapp').html('<img src="loader.gif" alt="Loading..." width="24" height="24">');
			},
			success: function(html) {    
				$("#outputapp").html( html );
			}
		});
	}
}
function Gd(sel) {
	var state_id = sel.options[sel.selectedIndex].value;  
	if (state_id.length > 0 ) { 
	 $.ajax({
			type: "GET",
			url: "Ajax_File.php",
			data: "id="+state_id+"&Request=Grade",
			cache: false,
			beforeSend: function () { 
				$('#outputgrade').html('<img src="loader.gif" alt="Loading..." width="24" height="24">');
			},
			success: function(html) {    
				$("#outputgrade").html( html );
			}
		});
	}
}
</script>

<script>
function Dept(sel) {
	var sec = sel.options[sel.selectedIndex].value;  
	if (sec.length > 0 ) { 
	 $.ajax({
			type: "GET",
			url: "Ajax_Dept.php",
			data: "de="+sec,
			cache: false,
			beforeSend: function () { 
				$('#section').html('<img src="loader.gif" alt="loading..." width="24" height="24">');
			},
			success: function(html) {    
				$("#section").html( html );
			}
		});
	}
}

$().ready(function() {
 $('select').attr({
                    'disabled': 'disabled'
                });
$('input').attr({
                    'disabled': 'disabled'
                });
 $('#btn').html('<button id="clicker" class="btn btn-primary">Edit</button>');
    $('#clicker').click(function() {
        $('select').each(function() {
            if ($(this).attr('disabled')) {
                $(this).removeAttr('disabled');
				 $('#btn').html('<button id="submit" name="Status" class="btn btn-primary">Update</button>');
            }
            
        });
	 $('input').each(function() {
            if ($(this).attr('disabled')) {
                $(this).removeAttr('disabled');
            }
            
        });
    });
});
</script>
