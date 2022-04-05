<?php
if(isset($_POST['Save'])){
$select = "SELECT * FROM leave_types WHERE type_name LIKE '%".$_POST['type']."%'";
$qry=DB_query($select);
$num=DB_num_rows($qry);
 if($num>0){
 prnMsg(_("<b>Duplicate entry!</b> Please Verify the information Entered and try Again."),'warn');
$error=1;
 }else{
		$ErrMsg = _('The employee details cannot be inserted because');
		$sql = "INSERT INTO leave_types
	(type_name,addedby)
						values('".$_POST['type']."','".$_SESSION['UserID']."')";
		$qry = DB_query($sql,$ErrMsg);
			if ($qry){
			 prnMsg(_("Information added Successfully"),'success');
			 echo '<p></p>';
				}
			else {
			prnMsg(_("Not Added Please Try Again"),'error');
			echo '<p></p>';
			 }
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
				<input id="id" name="type"  type="text"  class="form-control input-md" required=""/>
			 </div>
				</div>
            </div>
			<div class="box-footer">
              <div class="pull-right">
                <button type="button" name="Save" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Save</button>
              </div>
              <button type="reset" class="btn btn-default"><i class="fa fa-times"></i> Discard</button>
            </div>
			<p></p>
			<br />
			<table style="width:100%" class="table table-hover table-striped">
                  <tbody>
                  <tr>
                    <th><input type="checkbox"></th>
                    <th class="mailbox-star">No.</th>
                    <th class="mailbox-subject">Description</th>
                    <th width="50">Actions</th>
                  </tr>
				  <?php
				  $qry = "SELECT * FROM leave_types ORDER BY id";
				  $rest=DB_query($qry);
				  $i =1;
				  while($row = DB_fetch_array($rest)){
                echo  '<tr>
                    <td><input type="checkbox"></td>
                    <td class="mailbox-star">'.$i.'</i></a></td>
                    <td class="mailbox-subject">'.$row['type_name'].'</td>';
                echo "<td class='mailbox-date'><a href=# data-toggle='tooltip' data-placement='top' title='Edit'><span class='glyphicon glyphicon-list-alt' style='font-size:20px'></span></a>
                  </tr>";
				  $i++;
				  }
				  ?>
				  
                  </tbody>
                </table>

						</div>
					</form>
<script type="text/javascript" src="js/jquery-1.9.1.js"></script>						
