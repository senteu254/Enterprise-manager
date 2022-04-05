<?php
if(isset($_POST['Save'])){
$id = $_POST['id'];
$ErrMsg = _('The employee details cannot be inserted because');
for($i=0; $i<count($id); $i++){
$ids = $id[$i];
$approver = $_POST['approver_'.$ids];
$sql = "UPDATE qa_approval_levels SET authoriser ='".$approver."' WHERE id=".$ids."";
$qry = DB_query($sql,$ErrMsg);
}
if ($qry){
	prnMsg(_("Information Updated Successfully"),'success');
	echo '<p></p>';
}else {
	prnMsg(_("Not Added Please Try Again"),'error');
	echo '<p></p>';
	}
	
}

?>
<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			?>
					<div class="container-fluid">
						<div class="box-body">
              <div class="form-group">
			<div class="col-md-4">Document Type
				<select class="form-control input-md" required="" name="selectedtype">
				<?php
				  $qry = "SELECT * FROM qa_documents_types ORDER BY id";
				  $rest=DB_query($qry);
				  while($row = DB_fetch_array($rest)){
                echo  '<option '.($_POST['selectedtype']==$row['id'] ? 'selected' : '').' value="'.$row['id'].'">'.$row['type_name'].'</option>';
				  }
				  ?>
				</select>
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
			echo '<input type="hidden" name="selectedtype" value="' . $_POST['selectedtype'] . '" />';
			echo '<input type="hidden" name="Submit" value="Submit" />';
			
			?>
			<table style="width:100%" class="table table-hover table-striped">
                  <tbody>
                  <tr>
                    <th class="mailbox-star">No.</th>
                    <th class="mailbox-subject">Position</th>
                    <th width="400" >Authoriser</th>
                  </tr>
				  <?php
				   $qry = "SELECT * FROM www_users";
				  $rest=DB_query($qry);
				  while($row = DB_fetch_array($rest)){
                $users[] = $row;
				  }
				  mysqli_free_result($rest);
				  $i =1;
				 $qrys = "SELECT id, level_position, authoriser FROM `qa_approval_levels`
				 			WHERE type=".$_POST['selectedtype']." ORDER BY levelcheck ASC";
				  $rests=DB_query($qrys);
				  while($rows = DB_fetch_array($rests)){
                echo  '<tr>
                    <td class="mailbox-star">'.$i.'</i></a></td>
                    <td class="mailbox-subject">'.$rows['level_position'].'</td>';
				echo '<td class="mailbox-subject">';
				echo '<input name="id[]" value="'.$rows['id'].'" type="hidden" />';
				echo '<select class="form-control input-md" required="" name="approver_'.$rows['id'].'">';
				echo '<option selected value="">--Please Select Authoriser--</option>';
				echo '<option '.($rows['authoriser']=="QAT" ? 'selected' : '').' value="QAT">QA Technician</option>';
				echo '<option '.($rows['authoriser']=="MS" ? 'selected' : '').' value="MS">Machine Setter</option>';
				foreach($users as $z){
                echo  '<option '.($rows['authoriser']==$z['userid'] ? 'selected' : '').' value="'.$z['userid'].'">'.$z['userid'].' - '.$z['realname'].'</option>';
				  }
				echo '</select></td>';
					$i++;
				  }
				  ?>
				  
                  </tbody>
                </table>

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
			</div></div>
			<form enctype="multipart/form-data" method="post" action="<?php echo  htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&Link=Settings'; ?>" class="form-horizontal">		
		<div class="panel panel-primary">
		<div class="panel-heading">QA Technicians</div>
			<div class="panel-body">
			<div class="box-body no-padding">
              <div class="mailbox-controls">
              <div class="table-responsive mailbox-messages">
			  <?php
			  echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			  if(isset($_POST['AddTech'])){
			  $sql = "INSERT INTO qat (serviceno) VALUES('".$_POST['userx']."')";
			  DB_query($sql);
			  prnMsg(_("Technician Added Successfully"),'success');
			  }
			  if(isset($_GET['Delete']) && $_GET['Delete']='Yes'){
			  $sql = "DELETE FROM qat WHERE id=".$_GET['ID'];
			  DB_query($sql);
			  prnMsg(_("Technician Removed Successfully"),'success');
			  }
			  echo 'Select QAT:<select name="userx" style="width:250px;">';
			echo '<option value="">--Please Select Name of QAT--</option>';
			$userx = DB_query("SELECT userid,realname FROM www_users",	$db);
			while($myus = DB_fetch_array($userx)){
			echo '<option value="'.$myus['userid'].'">'.$myus['userid'].' - '.$myus['realname'].'</option>';
			}
			echo '</select> <input name="AddTech" type="submit" class="btn btn-primary" value="Add" />';
			  ?>
                <table id="myTable1" style="width:100%;" class="table table-hover table-striped">
				<thead>
				<tr><th>Service No</th>
				<th>Name</th>
				<th width="100">Action</th></tr>
				</thead>
                  <tbody>
				  <?php
				  $userz = DB_query("SELECT userid,realname,qat.id FROM www_users,qat WHERE qat.serviceno=www_users.userid");
				  if(DB_num_rows($userz)>0){
			  		while($rowz = DB_fetch_array($userz)){
                  echo '<tr>
					<td class="mailbox-date">'.$rowz['userid'].'</td>
					<td class="mailbox-date">'.$rowz['realname'].'</td>
					<td ><a href="index.php?Application=QA&Link=Settings&ID='.$rowz['id'].'&amp;Delete=Yes">Remove</a></td>';
                  echo '</tr>';
				  }
				  }else{
				  echo '<tr><td class="alert-danger" colspan="3"><center><b style="color:#FF0000">No Record Found</b></center></td></tr>';
				  }
				  
				  ?>
                  </tbody>
                </table>
				
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>
			</div>
			</div>
			</div>
			</form>
			
<script type="text/javascript" src="js/jquery-1.9.1.js"></script>						
