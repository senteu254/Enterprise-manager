
<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php
if(isset($_POST['Delete'])){
	$id = $_POST['ids'];
	for($i=0; $i<count($id); $i++){
	if($_POST['type_'.$id[$i]]==1){
		$delete = "DELETE FROM leave_off_duty WHERE off_id=".$id[$i]."";
		$welcome_viewed = DB_query($delete,$ErrMsg,$DbgMsg);
	}elseif($_POST['type_'.$id[$i]]==2){
		$delete = "DELETE FROM leave_half_day WHERE half_id=".$id[$i]."";
		$welcome_viewed = DB_query($delete,$ErrMsg,$DbgMsg);
	}elseif($_POST['type_'.$id[$i]]>=3){
		$delete = "DELETE FROM leave_annual WHERE leave_id=".$id[$i]."";
		$welcome_viewed = DB_query($delete,$ErrMsg,$DbgMsg);
	}
	}
	echo '<b style="color:#3300FF">Record(s) Deleted Successfully.</b>';
}
if(isset($_POST['Forward'])){
	$id = $_POST['ids'];
	for($i=0; $i<count($id); $i++){
	if($_POST['type_'.$id[$i]]==1){
		$delete = "UPDATE leave_off_duty SET send=1 WHERE off_id=".$id[$i]."";
		$welcome_viewed = DB_query($delete,$ErrMsg,$DbgMsg);
	}elseif($_POST['type_'.$id[$i]]==2){
		$delete = "UPDATE leave_half_day SET send=1 WHERE half_id=".$id[$i]."";
		$welcome_viewed = DB_query($delete,$ErrMsg,$DbgMsg);
	}elseif($_POST['type_'.$id[$i]]>=3){
		$delete = "UPDATE leave_annual SET send=1 WHERE leave_id=".$id[$i]."";
		$welcome_viewed = DB_query($delete,$ErrMsg,$DbgMsg);
	}
	}
	echo '<b style="color:#3300FF">Record(s) Forwarded Successfully.</b>';
}


$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
 
$per_page = 30; // Set how many records do you want to display per page.
$startpoint = ($page * $per_page) - $per_page;

						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');	
						$statement = " leave_annual,www_users,leave_types,leave_all_types
										WHERE leave_annual.added_by=www_users.userid 
										AND leave_annual.leave_type=leave_types.id 
										AND leave_annual.type=leave_all_types.id
										AND send = 0 
										AND added_by='".$_SESSION['UserID']."'"; // Change `records` according to your table name.
						$statement2 = " leave_off_duty,www_users,leave_types WHERE leave_off_duty.added_by=www_users.userid AND leave_off_duty.leave_type=leave_types.id AND send = 0 AND added_by='".$_SESSION['UserID']."'";
						$statement3 = " leave_half_day,www_users,leave_types WHERE leave_half_day.added_by=www_users.userid AND leave_half_day.leave_type=leave_types.id AND send = 0 AND added_by='".$_SESSION['UserID']."'";
  
						$results = "SELECT leave_id as id, realname, leave_all_types.type_name, narrative, date_added, leave_type FROM {$statement}";
						$results2 = "SELECT off_id as id, realname, type_name, narrative, date_added,leave_type FROM {$statement2}";
						$results3 = "SELECT half_id as id, realname, type_name, narrative, date_added,leave_type FROM {$statement3}";
						$sqlforPages = $results." UNION ALL ".$results2." UNION ALL ".$results3."";
						$sql = $results." UNION ALL ".$results2." UNION ALL ".$results3." ORDER BY date_added DESC LIMIT {$startpoint} , {$per_page}";
						$welcome_viewed = DB_query($sql,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($welcome_viewed);
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	?>
					<fieldset>
						<div class="box-body no-padding">
              <div class="mailbox-controls">
                <!-- Check all button -->
				<?php if($num_rows>0){ ?>
                <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                </button>
                <div class="btn-group">
				<button type="Submit" name="Delete" onClick="return confirm('Are you absolutely sure you want to delete?')" title="Delete" class="btn btn-default btn-sm"><i class="fa fa-trash"></i></button>
                  <button type="Submit" name="Forward" onClick="return confirm('Are you absolutely sure you want to Forward?')" title="Forward" class="btn btn-default btn-sm"><i class="fa fa-share"></i></button>
                </div>
                <!-- /.btn-group -->
                <a href="index.php?Application=HR&Ref=LeaveApp&Link=LeaveDraft"><button type="button" title="Refresh" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button></a>
				<?php } ?>
                <div class="pull-right">
				<div class="btn-group">
				<?php
				echo pagination_inbox($sqlforPages,$per_page,$page,$url='?Application=HR&Ref=LeaveApp&Link=LeaveDraft&');
				?>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.pull-right -->
              </div>
              <div class="table-responsive mailbox-messages">
			  
                <table style="width:100%" class="table table-hover table-striped">
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
					$words = '<b>'.$row['type_name'].'</b> - '.$row['narrative'];
					if($row['leave_type']==1){
					$truncated = (strlen($words) > 84) ? substr($words, 0, 84).'...' : $words;
					}else{
					$truncated = (strlen($words) > 83) ? substr($words, 0, 83).'...' : $words;
					}
					$realname = (strlen($row['realname']) > 21) ? substr($row['realname'].'wwww', 0, 21) : $row['realname'];
                  echo '<tr>
                    <td width="35"><input type="checkbox" name="ids[]" value="'.$row['id'].'"><input name="type_'.$row['id'].'" type="hidden" value="'.$row['leave_type'].'" /></td>
                    <td width="35" class="mailbox-star"><a href="#"><i class="fa fa-star text-yellow"></i></a></td>
                    <td width="170" class="mailbox-name"><a href="index.php?Application=HR&Ref=LeaveApp&Link=LeaveDraftRead&LID='.$row['id'].'&L_Type='.$row['leave_type'].'">'.ucwords(strtolower($realname)).'</a></td>
                    <td class="mailbox-subject">'.$truncated.'
                    </td>
                    <td class="mailbox-attachment"></td>
                    <td width="110" class="mailbox-date">'.calculate_time_span($row['date_added']).'</td>
                  </tr>';
				  }
				  }else{
				  echo '<tr><td class="alert-danger" colspan="6"><center><b style="color:#FF0000">No Records to display</b></center></td></tr>';
				  }
				  
				  ?>
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>

						</fieldset>
					</form>