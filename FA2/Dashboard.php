<link rel="stylesheet" href="PVP/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="PVP/dist/css/AdminLTE.min.css">
	
	<?php
	$query1 = "SELECT * FROM payment_voucher 
		 WHERE process_level =0
		 AND state=2";
           $count2 = DB_num_rows(DB_query($query1));
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
              <strong style="font-size:24px"><?php echo count2; ?></strong>
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

	