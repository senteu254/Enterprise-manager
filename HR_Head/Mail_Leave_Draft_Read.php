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
						$results = "SELECT *, b.emp_id as em_num FROM leave_off_duty a
							INNER JOIN employee b ON a.emp_id=b.emp_id
							INNER JOIN www_users c ON a.added_by=c.userid
							INNER JOIN leave_types d ON a.leave_type=d.id
							LEFT JOIN departments e ON e.departmentid= b.id_dept
							LEFT JOIN section f ON b.id_sec=f.id_sec
							WHERE send = 0 AND off_id=".$LID."";
						$delete = "DELETE FROM leave_off_duty WHERE off_id=".$LID."";
						$forward = "UPDATE leave_off_duty SET send=1 WHERE off_id=".$LID."";
				}elseif($_GET['L_Type']==2){	
						$results = "SELECT *, b.emp_id as em_num FROM leave_half_day a
							INNER JOIN employee b ON a.emp_id=b.emp_id
							INNER JOIN www_users c ON a.added_by=c.userid
							INNER JOIN leave_types d ON a.leave_type=d.id
							LEFT JOIN departments e ON e.departmentid= b.id_dept
							LEFT JOIN section f ON b.id_sec=f.id_sec
							WHERE send = 0 AND half_id=".$LID."";
						$delete = "DELETE FROM leave_half_day WHERE half_id=".$LID."";
						$forward = "UPDATE leave_half_day SET send=1 WHERE half_id=".$LID."";
				}elseif($_GET['L_Type']>=3){	
						$results = "SELECT *, b.emp_id as em_num,z.type_name FROM leave_annual a
							INNER JOIN employee b ON a.emp_id=b.emp_id
							INNER JOIN www_users c ON a.added_by=c.userid
							INNER JOIN leave_types d ON a.leave_type=d.id
							INNER JOIN leave_all_types z ON a.type=z.id 
							LEFT JOIN departments e ON e.departmentid= b.id_dept
							LEFT JOIN section f ON b.id_sec=f.id_sec
							WHERE send = 0 AND leave_id=".$LID."";
						$delete = "DELETE FROM leave_annual WHERE leave_id=".$LID."";
						$forward = "UPDATE leave_annual SET send=1 WHERE leave_id=".$LID."";
				}else{
				die('<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                Invalid Request, Please try Again
              </div>');
				}
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
						$rows = DB_fetch_array($welcome_viewed);
		
		$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
		$redirect = htmlspecialchars($protocol.''.$_SERVER['HTTP_HOST'].''.$_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8').'?Application=HR&Ref=LeaveApp&Link=LeaveDraft';
		if(isset($_POST['Delete'])){
		$welcome_viewed = DB_query($delete,$ErrMsg,$DbgMsg);
		?>
		<script>
		window.setTimeout(function(){
        window.location.href = "<?php echo $redirect; ?>";
		}, 1000);
		</script>
		<?php
		//die('<b style="color:#3300FF">Record Deleted Successfully. You will be redirected Shortly...</b>');
		die('<div class="alert alert-info alert-dismissible">
                <h4><i class="icon fa fa-info"></i> Alert!</h4>
                Record Deleted Successfully. You will be redirected Shortly...<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
              </div>');
		}
		
		if(isset($_POST['Forward'])){
		$welcome_viewed = DB_query($forward,$ErrMsg,$DbgMsg);
		?>
		<script>
		window.setTimeout(function(){
        window.location.href = "<?php echo $redirect; ?>";
		}, 1000);
		</script>
		<?php
		//die('<b style="color:#3300FF">Your Leave has been forwarded Successfully. You will be redirected Shortly...<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></b>');
		die('<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Leave Request has been approved and forwarded Successfully. You will be redirected Shortly...<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
              </div>');
		}

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
			?>
            <!-- /.box-footer -->
            <div class="box-footer">
              <div class="pull-right">
                <button type="submit" name="Forward" onClick="return confirm('Are you absolutely sure you want to Send?')" class="btn btn-default"><i class="fa fa-share"></i> Forward</button>
              </div>
              <button type="submit" name="Delete" onClick="return confirm('Are you absolutely sure you want to delete?')" class="btn btn-default"><i class="fa fa-trash"></i> Delete</button>
			  <button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button>
            </div>
			</form>
            <!-- /.box-footer -->
          </div>