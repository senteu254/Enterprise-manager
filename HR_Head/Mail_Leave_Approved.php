
<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php

$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
 
$per_page = 30; // Set how many records do you want to display per page.
$startpoint = ($page * $per_page) - $per_page;

						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$statement = " leave_annual,www_users,leave_types,leave_all_types,leave_approval_levels,employee,departments,section,chiefofficer
										WHERE leave_annual.added_by=www_users.userid 
										AND leave_annual.leave_type=leave_types.id 
										AND leave_annual.type=leave_all_types.id 
										AND leave_approval_levels.leave_type=leave_types.id 
										AND leave_annual.levelcheck >(SELECT MAX(levelcheck) FROM leave_approval_levels WHERE leave_type=leave_annual.leave_type)
										AND leave_approval_levels.levelcheck =(SELECT MAX(levelcheck) FROM leave_approval_levels WHERE leave_type=leave_annual.leave_type)
										AND leave_annual.emp_id=employee.emp_id
										AND employee.id_dept=departments.departmentid
										AND employee.id_sec=section.id_sec
										AND departments.departmentid=chiefofficer.id_dept
										AND send = 1 AND rejected=0 AND
										CASE WHEN leave_approval_levels.authoriser ='HOD' THEN departments.authoriser='".$_SESSION['UserID']."'
										WHEN leave_approval_levels.authoriser ='SH' THEN section.emp_id='".$_SESSION['UserID']."' 
										WHEN leave_approval_levels.authoriser ='CO' THEN chiefofficer.emp_id='".$_SESSION['UserID']."' 
										ELSE leave_approval_levels.authoriser ='".$_SESSION['UserID']."' END";
						$statement2 = " leave_off_duty,www_users,leave_types,leave_approval_levels,employee,departments,section,chiefofficer
										WHERE leave_off_duty.added_by=www_users.userid 
										AND leave_off_duty.leave_type=leave_types.id 
										AND leave_approval_levels.leave_type=leave_types.id 
										AND leave_off_duty.levelcheck >(SELECT MAX(levelcheck) FROM leave_approval_levels WHERE leave_type=1)
										AND leave_approval_levels.levelcheck = (SELECT MAX(levelcheck) FROM leave_approval_levels WHERE leave_type=1)
										AND leave_off_duty.emp_id=employee.emp_id
										AND employee.id_dept=departments.departmentid
										AND employee.id_sec=section.id_sec
										AND departments.departmentid=chiefofficer.id_dept
										AND send = 1 AND rejected=0 AND
										CASE WHEN leave_approval_levels.authoriser ='HOD' THEN departments.authoriser='".$_SESSION['UserID']."' 
										WHEN leave_approval_levels.authoriser ='SH' THEN section.emp_id='".$_SESSION['UserID']."' 
										WHEN leave_approval_levels.authoriser ='CO' THEN chiefofficer.emp_id='".$_SESSION['UserID']."' 
										ELSE leave_approval_levels.authoriser ='".$_SESSION['UserID']."' END";
						$statement3 = " leave_half_day,www_users,leave_types,leave_approval_levels,employee,departments,section,chiefofficer
										WHERE leave_half_day.added_by=www_users.userid 
										AND leave_half_day.leave_type=leave_types.id 
										AND leave_approval_levels.leave_type=leave_types.id 
										AND leave_half_day.levelcheck >(SELECT MAX(levelcheck) FROM leave_approval_levels WHERE leave_type=2)
										AND leave_approval_levels.levelcheck =(SELECT MAX(levelcheck) FROM leave_approval_levels WHERE leave_type=2)
										AND leave_half_day.emp_id=employee.emp_id
										AND employee.id_dept=departments.departmentid
										AND employee.id_sec=section.id_sec
										AND departments.departmentid=chiefofficer.id_dept
										AND send = 1 AND rejected=0 AND
										CASE WHEN leave_approval_levels.authoriser ='HOD' THEN departments.authoriser='".$_SESSION['UserID']."' 
										WHEN leave_approval_levels.authoriser ='SH' THEN section.emp_id='".$_SESSION['UserID']."' 
										WHEN leave_approval_levels.authoriser ='CO' THEN chiefofficer.emp_id='".$_SESSION['UserID']."' 
										ELSE leave_approval_levels.authoriser ='".$_SESSION['UserID']."' END";
  
						$results = "SELECT leave_id as id, realname, leave_all_types.type_name, narrative, date_added, leave_types.id as leave_type FROM {$statement}";
						$results2 = "SELECT off_id as id, realname, type_name, narrative, date_added,leave_types.id as leave_type FROM {$statement2}";
						$results3 = "SELECT half_id as id, realname, type_name, narrative, date_added,leave_types.id as leave_type FROM {$statement3}";
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
                <!-- /.btn-group -->
                <a href="index.php?Application=HR&Ref=LeaveApp&Link=LeaveApproved"><button type="button" title="Refresh" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button></a>
				<?php } ?>
                <div class="pull-right">
				<div class="btn-group">
				<?php
				echo pagination_inbox($sqlforPages,$per_page,$page,$url='?Application=HR&Ref=LeaveApp&Link=LeaveApproved&');
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
                    <td width="170" class="mailbox-name"><a href="index.php?Application=HR&Ref=LeaveApp&Link=LeaveApprovedRead&LID='.$row['id'].'&L_Type='.$row['leave_type'].'">'.ucwords(strtolower($realname)).'</a></td>
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