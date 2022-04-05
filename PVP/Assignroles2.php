<?php
if(isset($_POST['Save'])){
$id = $_POST['id'];
$ErrMsg = _('PV Roles Cannot be inserted because');
for($i=0; $i<count($id); $i++){
$ids = $id[$i];
$approver = $_POST['approver_'.$ids];
$sql = "UPDATE pvroles SET authoriser ='".$approver."' WHERE id=".$ids."";
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
				
			<p></p>
			<br />
			</form>
			<form enctype="multipart/form-data" method="post" class="form-horizontal">
			<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			echo '<input type="hidden" name="selectedtype" value="' . $_POST['selectedtype'] . '" />';
			echo '<input type="hidden" name="Submit" value="Submit" />';
			
			?>
			<table style="width:70%" class="table table-hover table-striped">
                  <tbody>
                  <tr>
                    <th class="mailbox-star">No.</th>
                    <th class="mailbox-subject">PV Level</th>
                    <th width="200" >PV Role</th>
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
				 $qrys = "SELECT * FROM pvroles ORDER BY id";
				  $rests=DB_query($qrys);
				  while($rows = DB_fetch_array($rests)){
                echo  '<tr>
                    <td class="mailbox-star">'.$i.'</i></a></td>
                    <td class="mailbox-subject">'.$rows['level'].'</td>
					<td class="mailbox-subject">'.$rows['pvrole'].'</td>';
				echo '<td class="mailbox-subject">';
				echo '<input name="id[]" value="'.$rows['id'].'" type="hidden" />';
				echo '<select class="form-control input-md" required="" name="approver_'.$rows['id'].'">';
				echo '<option selected value="">--Please Select Authoriser--</option>';
				echo '<option value="AIE">AIE Holder</option>';
				foreach($users as $row1){
                echo  '<option '.($rows['authoriser']==$row1['userid'] ? 'selected' : '').' value="'.$row1['userid'].'">'.$row1['userid'].' - '.$row1['realname'].'</option>';
				  }
				echo '</select></td>';
					$i++;
				  }
				  ?>
				  
                  </tbody>
                </table>

			<div class="box-footer">
                <div class="pull-center">
            <center><button type="submit" name="Save" class="btn btn-primary"><i class="fa fa-share-square-o"></i> Save</button></center>
              </div><br>
                <!--<center><button type="reset" class="btn btn-default"><i class="fa fa-times"></i> Discard</button></center>-->
            </div>
			<?php
			
			?>
		</div>
					</form>
<script type="text/javascript" src="js/jquery-1.9.1.js"></script>						
