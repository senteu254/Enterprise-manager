<?php
if(!is_numeric($_GET['LID'])){
ob_start();
die('<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                Invalid Request, Please try Again
              </div>');
}
$LID = $_GET['LID'];
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
				if($_GET['L_Type']==1){	
						$results = "SELECT *, employee.emp_id as em_num FROM leave_off_duty, www_users, leave_types, leave_approval_levels, employee, departments, section, chiefofficer
										WHERE leave_off_duty.added_by=www_users.userid 
										AND leave_off_duty.leave_type=leave_types.id 
										AND leave_approval_levels.leave_type=leave_types.id
										AND leave_approval_levels.levelcheck=leave_off_duty.levelcheck
										AND leave_off_duty.emp_id=employee.emp_id
										AND employee.id_dept=departments.departmentid
										AND employee.id_sec=section.id_sec
										AND departments.departmentid=chiefofficer.id_dept
										AND send = 1 AND rejected=0 AND off_id=".$LID." AND
										CASE WHEN leave_approval_levels.authoriser ='HOD' THEN departments.authoriser='".$_SESSION['UserID']."' 
										WHEN leave_approval_levels.authoriser ='SH' THEN section.emp_id='".$_SESSION['UserID']."' 
										WHEN leave_approval_levels.authoriser ='CO' THEN chiefofficer.emp_id='".$_SESSION['UserID']."' 
										ELSE leave_approval_levels.authoriser ='".$_SESSION['UserID']."' END";
						$forward = "UPDATE leave_off_duty SET levelcheck=levelcheck+1,  ".$_POST['LevelCode']."='".date('Y-m-d H:m:s')."' WHERE off_id=".$LID."";
						
						$forward2 = "INSERT INTO leave_approval (`leave_id`, `approver`, `status`, `approver_name`) 
												VALUES(".$LID.",'".$_POST['LevelCheck']."',1,'".$_SESSION['UsersRealName']."')";
						
						
						$reject = "UPDATE leave_off_duty SET ".$_POST['LevelCode']."='".date('Y-m-d H:m:s')."', rejected=1, reject_reason='".$_POST['comment']."' WHERE off_id=".$LID."";
						$reject2 = "INSERT INTO leave_approval (`leave_id`, `approver`, `status`, `approver_name`) 
												VALUES(".$LID.",'".$_POST['LevelCheck']."',2,'".$_SESSION['UsersRealName']."')";
				}elseif($_GET['L_Type']==2){	
						$results = "SELECT *, employee.emp_id as em_num FROM leave_half_day, www_users, leave_types, leave_approval_levels, employee, departments, section, chiefofficer
										WHERE leave_half_day.added_by=www_users.userid 
										AND leave_half_day.leave_type=leave_types.id 
										AND leave_approval_levels.leave_type=leave_types.id 
										AND leave_approval_levels.levelcheck=leave_half_day.levelcheck
										AND leave_half_day.emp_id=employee.emp_id
										AND employee.id_dept=departments.departmentid
										AND employee.id_sec=section.id_sec
										AND departments.departmentid=chiefofficer.id_dept
										AND send = 1 AND rejected=0 AND half_id=".$LID." AND
										CASE WHEN leave_approval_levels.authoriser ='HOD' THEN departments.authoriser='".$_SESSION['UserID']."' 
										WHEN leave_approval_levels.authoriser ='SH' THEN section.emp_id='".$_SESSION['UserID']."' 
										WHEN leave_approval_levels.authoriser ='CO' THEN chiefofficer.emp_id='".$_SESSION['UserID']."' 
										ELSE leave_approval_levels.authoriser ='".$_SESSION['UserID']."' END";
						$forward = "UPDATE leave_half_day SET levelcheck=levelcheck+1, ".$_POST['LevelCode']."='".date('Y-m-d H:m:s')."' WHERE half_id=".$LID."";
						
						$forward2 = "INSERT INTO leave_approval (`leave_id`, `approver`, `status`, `approver_name`) 
												VALUES(".$LID.",'".$_POST['LevelCheck']."',1,'".$_SESSION['UsersRealName']."')";
						
						$reject = "UPDATE leave_half_day SET  ".$_POST['LevelCode']."='".date('Y-m-d H:m:s')."', rejected=1, reject_reason='".$_POST['comment']."' WHERE half_id=".$LID."";
						$reject2 = "INSERT INTO leave_approval (`leave_id`, `approver`, `status`, `approver_name`) 
												VALUES(".$LID.",'".$_POST['LevelCheck']."',2,'".$_SESSION['UsersRealName']."')";
				}elseif($_GET['L_Type']>=3){	
						$results = "SELECT *, employee.emp_id as em_num,leave_all_types.type_name FROM leave_annual,www_users,leave_types,leave_all_types,leave_approval_levels,employee,departments,section,chiefofficer
										WHERE leave_annual.added_by=www_users.userid 
										AND leave_annual.leave_type=leave_types.id 
										AND leave_annual.type=leave_all_types.id 
										AND leave_approval_levels.leave_type=leave_types.id 
										AND leave_approval_levels.levelcheck=leave_annual.levelcheck
										AND leave_annual.emp_id=employee.emp_id
										AND employee.id_dept=departments.departmentid
										AND employee.id_sec=section.id_sec
										AND departments.departmentid=chiefofficer.id_dept
										AND send = 1 AND rejected=0 AND leave_id=".$LID." AND
										CASE WHEN leave_approval_levels.authoriser ='HOD' THEN departments.authoriser='".$_SESSION['UserID']."' 
										WHEN leave_approval_levels.authoriser ='SH' THEN section.emp_id='".$_SESSION['UserID']."' 
										WHEN leave_approval_levels.authoriser ='CO' THEN chiefofficer.emp_id='".$_SESSION['UserID']."' 
										ELSE leave_approval_levels.authoriser ='".$_SESSION['UserID']."' END";
						$forward = "UPDATE leave_annual SET levelcheck=levelcheck+1,  ".$_POST['LevelCode']."='".date('Y-m-d H:m:s')."' WHERE leave_id=".$LID."";
						
						$forward2 = "INSERT INTO leave_approval (`leave_id`, `approver`, `status`, `approver_name`) 
												VALUES(".$LID.",'".$_POST['LevelCheck']."',1,'".$_SESSION['UsersRealName']."')";
						
						$reject = "UPDATE leave_annual SET ".$_POST['LevelCode']."='".date('Y-m-d H:m:s')."', rejected=1, reject_reason='".$_POST['comment']."' WHERE leave_id=".$LID."";
						$reject2 = "INSERT INTO leave_approval (`leave_id`, `approver`, `status`, `approver_name`) 
												VALUES(".$LID.",'".$_POST['LevelCheck']."',2,'".$_SESSION['UsersRealName']."')";
				}else{
				die('<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                Invalid Request, Please try Again
              </div>');
				}
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
						$rows = DB_fetch_array($welcome_viewed);
		
		$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
		$redirect = htmlspecialchars($protocol.''.$_SERVER['HTTP_HOST'].''.$_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8').'?Application=HR&Ref=LeaveApp&Link=LeaveInbox';
		if(isset($_POST['Reject'])){
		$welcome_viewed = DB_query($reject,$ErrMsg,$DbgMsg);
		$welcome_viewed = DB_query($reject2,$ErrMsg,$DbgMsg);
		?>
		<script>
		window.setTimeout(function(){
        window.location.href = "<?php echo $redirect; ?>";
		}, 500);
		</script>
		<?php
		//die('<div class="success"><b>Leave Request has been Rejected. You will be redirected Shortly...<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i><span class="sr-only">Loading...</span></b></div>');
		die('<div class="alert alert-info alert-dismissible">
                <h4><i class="icon fa fa-info"></i> Alert!</h4>
                Leave Request has been Rejected. You will be redirected Shortly...<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
              </div>');
		}
		
		if(isset($_POST['Forward'])){
		$welcome_viewed = DB_query($forward,$ErrMsg,$DbgMsg);
		$welcome_viewed = DB_query($forward2,$ErrMsg,$DbgMsg);
		?>
		<script>
		window.setTimeout(function(){
        window.location.href = "<?php echo $redirect; ?>";
		}, 1000);
		</script>
		<?php
		//die('<div class="success"><b>Leave Request has been approved and forwarded Successfully. You will be redirected Shortly...<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i><span class="sr-only">Loading...</span></b></div>');
		die('<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Leave Request has been approved and forwarded Successfully. You will be redirected Shortly...<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
              </div>');
		}
		
		
echo '<div id="popDiv" style="z-index: 999;
									width: 100%;
									height: 100%;
									top: 0;
									left: 0;
									display: none;
									position: absolute;				
									background-color: #fff;
									background-color: rgba(255,255,255,0.7);
									filter: alpha(opacity = 50);">';
	
	
	echo '<table style="width: 300px;
						height: 200px;
						position: absolute;
						color: #000000;
						/* To align popup window at the center of screen*/
						top: 50%;
						left: 50%;
						margin-top: -100px;
						margin-left: -150px;">
			<tr>
				<th>' . _('Please Leave a Reason for Decline') . ':</th>
			</tr>';
			echo '<form method="post" action="" id="form">';
			echo '<tr>
				<td>
				<textarea required name="comment" class="form-control input-md"></textarea>
				<input type="hidden" name="LevelCode" value="' . $rows['level_code'] . '" />
				<input type="hidden" name="LevelCheck" value="' . $rows['levelcheck'] . '" />
				<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
				</td>
			</tr>
			<tr>
				<td><input type="submit" name="Reject" value="' . _('Submit') . '" />
				</form>';
	echo "<input type=submit onclick=hide('popDiv') value=" . _('Cancel') . " />";
	echo '</td>
			</tr>
			</table>';
	
	echo '</div>';		

?>
              
			  <div class="mailbox-read-info">
                <h4><?php echo $rows['type_name']; ?></h4>
                <h5>From: <?php echo ucwords(strtolower($rows['realname'])); ?>
                  <span class="mailbox-read-time pull-right"><?php echo  date("d M. Y",strtotime($rows['date_added'])).' '. date("h:i A",strtotime($rows['date_added'])); ?></span></h5>
              </div>
			  <legend></legend>
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message">
                <p>Hello Sir/Madam,</p>
				
				<table style="width:100%">
				<tr><td width="120" height="25">Name:</td><td><?php echo ' <b>'.strtoupper($rows['emp_lname'].' '.$rows['emp_fname'].' '.$rows['emp_mname']).'</b>'; ?></td></tr>
				<tr><td height="25">Personal No.:</td><td><?php echo '<b>'.$rows['em_num'].'</b>'; ?></td></tr>
				<tr><td height="25">Department: </td><td><?php echo '<b>'.strtoupper($rows['description']).'</b>'; ?></td></tr>
				<tr><td height="25">Section: </td><td><?php echo '<b>'.strtoupper($rows['section_name']).'</b>'; ?></td></tr>
				</table><br>
				<p><?php echo $rows['narrative']; ?></p>
				<?php if($_GET['L_Type']>=3){ ?>
				<p>Incase I cannot be reached Please contact</p>
				<p><?php echo '<b>'.strtoupper($rows['em_name']).'</b>'; ?></p>
				<p><?php echo '<b>'.strtoupper($rows['em_address']).'</b>'; ?></p>
				<p><?php echo '<b>'.strtoupper($rows['em_phone']).'</b>'; ?></p>
				<?php } ?>
				<br />
                <p>Thanks,<br><?php echo ucwords(strtolower($rows['realname'])); ?></p>
              </div>
			  <form enctype="multipart/form-data" method="post" class="form-horizontal">
			<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			echo '<input type="hidden" name="LevelCode" value="' . $rows['level_code'] . '" />';
			echo '<input type="hidden" name="LevelCheck" value="' . $rows['levelcheck'] . '" />';
			?>
            <!-- /.box-footer -->
            <div class="box-footer">
              <div class="pull-right">
                <a href=# onclick="pop('popDiv')"><button type="button" name="Reject" class="btn btn-warning"><i class="fa fa-reply"></i> Reject</button></a>
                <button type="submit" name="Forward" onClick="return confirm('Are you absolutely sure you want to Forward this request?')" class="btn btn-default"><i class="fa fa-share"></i> Forward</button>
              </div>
			  <a href="PDFLeave.php?<?php echo 'LID='.$LID.'&L_Type='.$_GET['L_Type']; ?>" target="_blank"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>
            </div>
			</form>
            <!-- /.box-footer -->
          </div>