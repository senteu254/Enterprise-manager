<?php
if(isset($_POST['Save'])){
$id = $_POST['ids'];
$ErrMsg = _('The employee details cannot be inserted because');
for($i=0; $i<count($id); $i++){
$ids = $id[$i];
$q="SELECT COUNT(alloc_id) as num FROM leave_days_allocation WHERE emp_id='".$ids."' AND year='".$_POST['selectedyear']."' AND leave_type='".$_POST['selectedtype']."'";
$rest = DB_query($q,$ErrMsg);
$row = DB_fetch_array($rest);
if($row['num'] >0){
$sql = "UPDATE leave_days_allocation SET leave_days='".$_POST['leavedays']."' WHERE emp_id='".$ids."' AND year='".$_POST['selectedyear']."' AND leave_type='".$_POST['selectedtype']."'";
$qry = DB_query($sql,$ErrMsg);
}else{
$sql = "INSERT INTO leave_days_allocation (emp_id,leave_type,year,leave_days)
					VALUE('".$ids."','".$_POST['selectedtype']."','".$_POST['selectedyear']."','".$_POST['leavedays']."')";
$qry = DB_query($sql,$ErrMsg);
}
}
if ($qry){
	prnMsg(_("Leave Days allocated Successfully"),'success');
	echo '<p></p>';
}else {
	prnMsg(_("Not Added Please Try Again"),'error');
	echo '<p></p>';
	}
unset($_POST['Submit']);
}

?>
<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			?>
					<div class="container-fluid">
						<div class="box-body">
              <div class="form-group">
			<div class="col-md-4">Band
				<select class="form-control input-md" name="searchband">
				<option value="">--Please Select Band--</option>
				<?php
				  $qry = "SELECT * FROM band";
				  $rest=DB_query($qry);
				  while($row = DB_fetch_array($rest)){
                echo  '<option '.($_POST['searchband']==$row['band_id'] ? 'selected' : '').' value="'.$row['band_id'].'">'.$row['band_id'].'</option>';
				  }
				  ?>
				</select>
			 </div>
			 <div class="form-group">
			<div class="col-md-4">Service No
				<input name="srvno" value="<?php if(isset($_POST['srvno']) && $_POST['srvno'] !=""){ echo $_POST['srvno']; } ?>" class="form-control input-md" type="text" autocomplete="off" />
			 </div>
				</div>
				</div>
				
            </div>
			<div class="box-footer">
                <button type="submit" name="Submit" class="btn btn-primary"><i class="fa fa-share-square-o"></i> Submit</button>
              
            </div>
			<p></p>
			<br />
			</form>
			<form enctype="multipart/form-data" method="post" class="form-horizontal">
			<?php
			if(isset($_POST['Submit'])){
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			?>
			<div class="box-body">
			<div class="form-group">
			<div class="col-md-4">Leave Type
				<select class="form-control input-md" required="" name="selectedtype">
				<?php
				  $qry = "SELECT * FROM leave_types WHERE id=3";
				  $rest=DB_query($qry);
				  while($row = DB_fetch_array($rest)){
                echo  '<option '.($_POST['selectedtype']==$row['id'] ? 'selected' : '').' value="'.$row['id'].'">'.$row['type_name'].'</option>';
				  }
				  ?>
				</select>
			 </div>
				<div class="col-md-4">Year
				<select class="form-control input-md" required="" name="selectedyear">
				<?php
				  for($i =date('Y'); $i < date('Y')+4; $i++){
                echo  '<option '.($_POST['selectedyear']==$i ? 'selected' : '').' value="'.$i.'">'.$i.'</option>';
				  }
				  ?>
				</select>
			 </div>
			 <div class="col-md-4">Leave Days
				<input class="form-control input-md" name="leavedays" type="text" required />
			 </div>	
			 
				</div></div>
				<div class="table-responsive mailbox-messages">
			<table style="width:100%" class="table table-hover table-striped">
                  <tbody>
                  <tr>
                    <th class="mailbox-star">No.</th>
					<th><button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i></button></th>
                    <th class="mailbox-subject">Service No</th>
					<th class="mailbox-subject">Last Name</th>
					<th class="mailbox-subject">Other Names</th>
					<th class="mailbox-subject">Grade</th>
					<th class="mailbox-subject">Appointment</th>
					
                  </tr>
				  <?php
				  $i =1;
				  if(isset($_POST['srvno']) && $_POST['srvno'] !=""){
				  $search = " emp_id='".$_POST['srvno']."'";
				  }else{
				  $search = " band='".$_POST['searchband']."'";
				  }
				 $qrys = "SELECT * FROM `employee` where ".$search." ORDER BY emp_id ASC";
				  $rests=DB_query($qrys);
				  while($rows = DB_fetch_array($rests)){
                echo  '<tr>
					 <td class="mailbox-star">'.$i.'</td>
                    <td class="mailbox-star"><input type="checkbox" name="ids[]" value="'.$rows['emp_id'].'"></td>
                    <td class="mailbox-subject">'.$rows['emp_id'].'</td>';
				echo '<td class="mailbox-subject">'.$rows['emp_lname'].'</td>';
				echo '<td class="mailbox-subject">'.$rows['emp_fname'].' '.$rows['emp_mname'].'</td>';
				echo '<td class="mailbox-subject">'.$rows['grade'].'</td>';
				echo '<td class="mailbox-subject">'.$rows['appointment_name'].'</td>';
				echo '</td>';
					$i++;
				  }
				  ?>
				  
                  </tbody>
                </table>
				</div>

			<div class="box-footer">
                <div class="pull-right">
                <button type="submit" name="Save" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Save</button>
              </div>
              <button type="reset" class="btn btn-default"><i class="fa fa-times"></i> Discard</button>
            </div>
			<?php
			}
			?>
		</div>
					</form>
<script type="text/javascript" src="js/jquery-1.9.1.js"></script>						
