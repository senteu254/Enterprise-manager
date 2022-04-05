<link rel="stylesheet" href="HR_Head/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="HR_Head/dist/css/AdminLTE.min.css">
	
	<?php
	$ErrMsg = _('An error occurred in retrieving the records');
	$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
	$results = "SELECT leave_id as id,leave_type,levelcheck,rejected FROM leave_annual,www_users WHERE leave_annual.emp_id=www_users.emp_id AND userid='".$_SESSION['UserID']."' AND send=1";
	$results2 = "SELECT off_id as id,leave_type,levelcheck,rejected FROM leave_off_duty,www_users WHERE leave_off_duty.emp_id=www_users.emp_id AND userid='".$_SESSION['UserID']."' AND send=1";
	$results3 = "SELECT half_id as id,leave_type,levelcheck,rejected FROM leave_half_day,www_users WHERE leave_half_day.emp_id=www_users.emp_id AND userid='".$_SESSION['UserID']."' AND send=1";
	$sqlquery = $results." UNION ALL ".$results2." UNION ALL ".$results3."";
						$query = "SELECT COUNT(*) as `num` FROM ({$sqlquery}) t";
						$welc = DB_query($query,$ErrMsg,$DbgMsg);
						$num = DB_fetch_array($welc);
						$approved = "SELECT COUNT(*) as `num` FROM ({$sqlquery}) t WHERE t.levelcheck >(SELECT MAX(levelcheck) FROM leave_approval_levels WHERE leave_type=t.leave_type) AND t.rejected =0";
						$app = DB_query($approved,$ErrMsg,$DbgMsg);
						$numapp = DB_fetch_array($app);
						$pending = "SELECT COUNT(*) as `num` FROM ({$sqlquery}) t WHERE t.levelcheck <=(SELECT MAX(levelcheck) FROM leave_approval_levels WHERE leave_type=t.leave_type) AND t.rejected =0";
						$bend = DB_query($pending,$ErrMsg,$DbgMsg);
						$numbend = DB_fetch_array($bend);
						$reject = "SELECT COUNT(*) as `num` FROM ({$sqlquery}) t WHERE t.rejected =1";
						$reject = DB_query($reject,$ErrMsg,$DbgMsg);
						$numreject = DB_fetch_array($reject);
	
	?>
	
		<div class="container-fluid">
		<div class="panel-body">
		<div class="row">
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="alert alert-info">
		  <div class="icon" style="float:right">
              <i class="fa  fa-envelope-o" style="font-size:70px"></i>
            </div>
            <div class="inner">
              <strong style="font-size:24px"><?php echo $num['num']; ?></strong>
              <p>All Leaves</p>
            </div>
            <a href="index.php?Application=HR&Ref=LeaveApp" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="alert alert-warning">
		   <div class="icon" style="float:right">
             <i class="fa fa-cogs" aria-hidden="true" style="font-size:70px"></i>
            </div>
            <div class="inner">
              <strong style="font-size:24px"><?php echo $numbend['num']; ?></strong>
              <p>Pending Leaves</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="index.php?Application=HR&Ref=LeaveApp&Link=LeaveSent" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="alert alert-success">
		  <div class="icon" style="float:right">
             <i class="fa fa-star" style="font-size:70px"></i>
            </div>
            <div class="inner">
              <strong style="font-size:24px"><?php echo $numapp['num']; ?></strong>
              <p>Approved Leaves</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
            <a href="index.php?Application=HR&Ref=LeaveApp&Link=LeaveApproved" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="alert alert-danger">
		  <div class="icon" style="float:right">
              <i class="fa fa-trash" style="font-size:70px"></i>
            </div>
            <div class="inner">
             <strong style="font-size:24px"><?php echo $numreject['num']; ?></strong>
              <p>Rejected Leaves</p>
            </div>
            <div class="icon">
            
            </div>
            <a href="index.php?Application=HR&Ref=LeaveApp&Link=LeaveTrash" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
      </div>

	
	</div>
</div> <!--end container-->
<?php include 'HR_Head/Chat.php'; ?>


	