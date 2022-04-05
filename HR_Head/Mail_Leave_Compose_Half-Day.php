<?php
if(isset($_POST['Save'])){
$userid = $_POST['empid'];
$from= FormatDateForSQL($_POST['from']);
$out = $_POST['timeout'];
$in = $_POST['timein'];
$reason = $_POST['reason'];
$dest= $_POST['destination'];
if(isset($_POST['Draft'])){
$send = 0;
}else{
$send = 1;
}

$LeaveID = GetNextTransNo(111, $db);

 $datetimeout = strtotime($out);
  $datetimein = strtotime($in);
if($from < date('Y-m-d')){
prnMsg(_("Date cannot be Earlier than today! Please review and try again"),'error');
	echo '<p></p>';
}elseif($datetimeout < time()){
prnMsg(_("Expected Time Out cannot be Earlier than Now! Please review and try again"),'error');
	echo '<p></p>';
}elseif(($datetimein-$datetimeout)/60 < 10){
prnMsg(_("Expected Time In cannot be Earlier than Time Out! Please review and try again"),'error');
	echo '<p></p>';
}else{
$narrative = 'Permission to <b>be away from duty</b> from <b>'.date("h:i A",$datetimeout).'</b> to <b>'.date("h:i A",$datetimein).'</b> on <b>'.$_POST['from'].'</b>. Reason being <b>'.$reason.'</b>.';
$ErrMsg = _('The Information cannot be inserted because');
$sql = "INSERT INTO `leave_half_day`(`half_id`, `emp_id`, `leave_type`, `date`, `timeout`, `timein`, `reason`, `destination`, `narrative`, `send`, `added_by`) 
									VALUES (".$LeaveID.",'".$userid."',2,'".$from."','".$out."','".$in."','".$reason."','".$dest."','".$narrative."','".$send."','".$_SESSION['UserID']."')";
$qry = DB_query($sql,$ErrMsg);
if ($qry){
if($send==0){
	prnMsg(_("Leave Application has been saved in your draft"),'success');
	echo '<p></p>';
	}else{
	prnMsg(_("Leave Application has been Sent Successfully"),'success');
	echo '<p></p>';
	}
unset($_POST['destination']);
unset($_POST['reason']);
}else {
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
             <!-- <div class="form-group">
				<div class="col-md-7">Name:
				<input type="text" value="<?php echo $_SESSION['UsersRealName']; ?>" class="form-control input-md" disabled="disabled"/>
			  </div>
			<div class="col-md-5">Service No.
				<input type="text" value="<?php echo $_SESSION['UserID']; ?>"  class="form-control input-md" disabled="disabled"/>
			 </div>
				</div>-->
			<div class="form-group">
			<?php
				$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');	
						$results = "SELECT * FROM employee";
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
					echo '<div class="col-md-5">Applicant';
					echo '<select name="empid" class="form-control input-md">';
						while($rows = DB_fetch_array($welcome_viewed)){
						echo '<option '.(($_SESSION['UserID']== $rows['emp_id'] or $_POST['empid']== $rows['emp_id'])? 'selected="selected"' : '').' value="'.$rows['emp_id'].'">'.$rows['emp_id'].' - '.$rows['emp_lname'].' '.$rows['emp_fname'].' '.$rows['emp_mname'].'</option>';
						}
					echo '</select>';
					echo '</div>';
				?>
				</div>
			<div class="form-group">
				<div class="col-md-4">From: (Format: mm/dd/yyyy)
			<?php
			echo '<input type="text" class="date" alt="d/m/Y" name="from" value="'.date('d/m/Y').'" id="form-control" required="" />';
			?>
			  </div>
			<div class="col-md-3">Time Out:
			<?php
			echo '<select name="timeout" class="form-control input-md">';
				 for($hours=7; $hours<18; $hours++) // the interval for hours is '1'
				for($mins=0; $mins<60; $mins+=30) // the interval for mins is '30'
			echo '<option>'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
                       .str_pad($mins,2,'0',STR_PAD_LEFT).'</option>';
			echo '</select>';
			?>
			 </div>
			 <div class="col-md-3">Expected Time In:
			<?php
			echo '<select name="timein" class="form-control input-md">';
				 for($hours=7; $hours<18; $hours++) // the interval for hours is '1'
				for($mins=0; $mins<60; $mins+=30) // the interval for mins is '30'
			echo '<option>'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
                       .str_pad($mins,2,'0',STR_PAD_LEFT).'</option>';
			echo '</select>';
			?>
			 </div>
				</div>
			  <div class="form-group">
				<div class="col-md-7">Reason:
				<textarea name="reason" class="form-control input-md" required="" ><?php echo $_POST['reason'] ?></textarea>
			 </div>
			<div class="col-md-5">Destination:
				<input name="destination"  type="text"  value="<?php echo $_POST['destination'] ?>" class="form-control input-md" required=""/>
			 </div>
				</div>
            </div>
			<div class="box-footer">
              <div class="pull-right">
			  <input type="hidden" name="Save" value="Save" />
                <button type="submit" name="Draft" class="btn btn-default"><i class="fa fa-pencil"></i> Draft</button>
                <button type="submit" name="Send" class="btn btn-primary"><i class="fa fa-envelope-o"></i> Send</button>
              </div>
              <button type="reset" class="btn btn-default"><i class="fa fa-times"></i> Discard</button>
            </div>

						</div>
					</form>
