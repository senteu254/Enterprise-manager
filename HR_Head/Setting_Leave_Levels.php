<?php
if(isset($_POST['Save'])){
$id = $_POST['id'];
$ErrMsg = _('The employee details cannot be inserted because');
for($i=0; $i<count($id); $i++){
$ids = $id[$i];
$approver = $_POST['approver_'.$ids];
$sql = "UPDATE leave_approval_levels SET authoriser ='".$approver."' WHERE id=".$ids."";
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
			<div class="col-md-4">Leave Type
				<select class="form-control input-md" required="" name="selectedtype">
				<?php
				  $qry = "SELECT * FROM leave_types ORDER BY id";
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
				 $qrys = "SELECT id, level_position, authoriser FROM `leave_approval_levels`
				 			WHERE leave_type=".$_POST['selectedtype']." ORDER BY level_code ASC";
				  $rests=DB_query($qrys);
				  while($rows = DB_fetch_array($rests)){
                echo  '<tr>
                    <td class="mailbox-star">'.$i.'</i></a></td>
                    <td class="mailbox-subject">'.$rows['level_position'].'</td>';
				echo '<td class="mailbox-subject">';
				echo '<input name="id[]" value="'.$rows['id'].'" type="hidden" />';
				echo '<select class="form-control input-md" required="" name="approver_'.$rows['id'].'">';
				echo '<option selected value="">--Please Select Authoriser--</option>';
				echo '<option '.($rows['authoriser']=="HOD" ? 'selected' : '').' value="HOD">Head of Department (HOD)</option>';
				echo '<option '.($rows['authoriser']=="CO" ? 'selected' : '').' value="CO">Chief Officer</option>';
				echo '<option '.($rows['authoriser']=="SH" ? 'selected' : '').' value="SH">Section Head</option>';
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
<script type="text/javascript" src="js/jquery-1.9.1.js"></script>						
