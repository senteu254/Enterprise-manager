<link rel="stylesheet" href="HR_Head/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="HR_Head/iCheck/flat/blue.css">
<form enctype="multipart/form-data" method="post">
<?php
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<link href="' . $RootPath . '/css/'. $_SESSION['Theme'] .'/default.css" rel="stylesheet" type="text/css" />';
echo '<script type="text/javascript" src = "js/jquery-1.9.1.js"></script>';
?>
<div class="container-fluid">
<div class = "row">
<div class="col-md-3 col-md-offset-">
	<div class="panel panel-default">
		<div class="panel-heading">Sort Criteria</div>
			<div class="panel-body">
			<div class="form-group">
			Leave Type
			<select class="form-control input-md" required="" name="selectedtype">
			<option value="0">All Leave Types</option>
				<?php
				  $qry = "SELECT * FROM leave_all_types";
				  $rest=DB_query($qry);
				  while($row = DB_fetch_array($rest)){
                echo  '<option '.($_POST['selectedtype']==$row['id'] ? 'selected' : '').' value="'.$row['id'].'">'.$row['type_name'].'</option>';
				  }
				  ?>
			</select>
				</div>
			<div class="form-group">
			Status
			<select class="form-control input-md" required="" name="status">
				<?php
				  $array = array(0=>'All Leaves',1=>'Approved Leaves',2=>'Pending for Approval Leaves',3=>'Rejected Leaves');
				  foreach($array as $key => $data){
                echo  '<option '.($_POST['status']==$key ? 'selected' : '').' value="'.$key.'">'.$data.'</option>';
				  }
				  ?>
			</select>
				</div>
				<div class="form-group">
				Applied On.: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; To.:<br />
			<?php
			echo '<input type="text" style="width:115px;" class="date" alt="d/m/Y" name="from" value="'.(isset($_POST['from']) ? $_POST['from'] : date('d/m/Y')).'" id="form-control" required="" />';
			echo '<input type="text" style="width:115px;" class="date" alt="d/m/Y" name="to" value="'.(isset($_POST['to']) ? $_POST['to'] : date('d/m/Y')).'" id="form-control" required="" />';
			?>
		
			 </div>
			 <div class="form-group">
			<div class="mailbox-messages">
			On Leave as at Now
			<input name="StillOnLeave" <?php echo ((isset($_POST['StillOnLeave']) && $_POST['StillOnLeave']==1)? 'checked="checked"':''); ?> type="checkbox" value="1" />
				</div>
			</div>
			<div class="box-footer">
              <div class="pull-right">
                <button type="submit" name="Search" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
              </div>
			  </div>
			
			</div>
		</div>
	</div>

	<div class="col-md-9 col-md-offset-">
	<div class="panel panel-default">
		<div class="panel-heading">Leave Report</div>
			<div class="panel-body">
				
			<!--/*----------------------------------------------------------------------------*/-->
			
				<div class="container-fluid">
			<table style="width:100%" class="table table-hover table-striped">
                  <tbody>
                  <tr>
                    <th>Srv No</th>
                    <th>Employee Name</th>
                    <th>Department</th>
					<th>Leave Type</th>
					<th>From Date</th>
					<th>To Date</th>
					<th>Days</th>
					<th>Applied On</th>
                  </tr>
				  <?php
				  $print ='';
				  $SoL ='';
				  if(isset($_POST['Search'])){ 
 
				  if(isset($_POST['StillOnLeave']) && $_POST['StillOnLeave']==1){
				  $sort = "AND a.levelcheck >(SELECT MAX(z.levelcheck) FROM leave_approval_levels z WHERE z.leave_type=a.leave_type) AND rejected=0
				  			AND (from_date <= '".date('Y-m-d')."' AND '".date('Y-m-d')."' <= to_date)";
				  $sort1 = "AND a.levelcheck >(SELECT MAX(z.levelcheck) FROM leave_approval_levels z WHERE z.leave_type=a.leave_type) AND rejected=0
				  			AND (date = '".date('Y-m-d')."')";
					$SoL = '&SoL='.$_POST['StillOnLeave'];
				  }else{
				  if($_POST['status']==1){
				  $sort = "AND a.levelcheck >(SELECT MAX(z.levelcheck) FROM leave_approval_levels z WHERE z.leave_type=a.leave_type) AND rejected=0 
				  			AND date_added >='".FormatDateForSQL($_POST['from'])."' AND date_added <='".FormatDateForSQL($_POST['to'])."'";
				  $sort1 =$sort;
				  }elseif($_POST['status']==2){
				  $sort = "AND a.levelcheck <(SELECT MAX(z.levelcheck) FROM leave_approval_levels z WHERE z.leave_type=a.leave_type) AND rejected=0
				  			AND date_added >='".FormatDateForSQL($_POST['from'])."' AND date_added <='".FormatDateForSQL($_POST['to'])."'";
				  $sort1 =$sort;
				  }elseif($_POST['status']==3){
				  $sort = "AND rejected=1 AND date_added >='".FormatDateForSQL($_POST['from'])."' AND date_added <='".FormatDateForSQL($_POST['to'])."'";
				  $sort1 =$sort;
				  }else{
				  $sort = "AND date_added >='".FormatDateForSQL($_POST['from'])."' AND date_added <='".FormatDateForSQL($_POST['to'])."'";
				  $sort1 =$sort;
				  }
				  }
				  
				  if($_POST['selectedtype']>=3){
				  $type = "AND type=".$_POST['selectedtype'];
				  }else{
				  $type="";
				  }
				  
				$qry = "SELECT a.emp_id,emp_lname,emp_fname,description,z.type_name,from_date,to_date,days,date_added FROM leave_annual a
				  			INNER JOIN employee b ON a.emp_id = b.emp_id
							INNER JOIN leave_types c ON a.leave_type=c.id
							INNER JOIN departments d ON b.id_dept = d.departmentid
							INNER JOIN leave_all_types z ON a.type=z.id
							WHERE send=1 ".$type." ".$sort;
				$qry2 = "SELECT a.emp_id,emp_lname,emp_fname,description,type_name,from_date,to_date,days,date_added FROM leave_off_duty a
				  			INNER JOIN employee b ON a.emp_id = b.emp_id
							INNER JOIN leave_types c ON a.leave_type=c.id
							INNER JOIN departments d ON b.id_dept = d.departmentid
							WHERE send=1 ".$sort;
				$qry3 = "SELECT a.emp_id,emp_lname,emp_fname,description,type_name,date as from_date,date as to_date,0,date_added FROM leave_half_day a
				  			INNER JOIN employee b ON a.emp_id = b.emp_id
							INNER JOIN leave_types c ON a.leave_type=c.id
							INNER JOIN departments d ON b.id_dept = d.departmentid
							WHERE send=1 ".$sort1;
				if($_POST['selectedtype']==1){
				  $rest=DB_query($qry2);
				  }elseif($_POST['selectedtype']==2){
				  $rest=DB_query($qry3);
				  }elseif($_POST['selectedtype']==0){
				  $sql = $qry." UNION ALL ".$qry2." UNION ALL ".$qry3." ORDER BY date_added DESC";
				  $rest=DB_query($sql);
				  }else{
				  $rest=DB_query($qry);
				  }
				  $i =1;
				  if(DB_num_rows($rest)>0){
				  while($row = DB_fetch_array($rest)){
                echo  '<tr style="font-size:10px">
                    <td class="mailbox-star">'.$row['emp_id'].'</i></a></td>
                    <td class="mailbox-subject">'.$row['emp_lname'].' '.$row['emp_fname'].'</td>
					<td class="mailbox-subject">'.$row['description'].'</td>
					<td class="mailbox-subject">'.str_replace('Application', '', $row['type_name']).'</td>
					<td class="mailbox-subject">'.ConvertSQLDate($row['from_date']).'</td>
					<td class="mailbox-subject">'.ConvertSQLDate($row['to_date']).'</td>
					<td class="mailbox-subject">'.$row['days'].'</td>
					<td class="mailbox-subject">'.ConvertSQLDate($row['date_added']).'</td>
					  </tr>';
				  $i++;
				  }
				  $search = 'type='.$_POST['selectedtype'].'&status='.$_POST['status'].'&from='.$_POST['from'].'&to='.$_POST['to'].''.$SoL;
				  $print ='<a target="_blank" href="PDFLeaveReportPortrait.php?'.$search.'"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>';
				  
				  }else{
				  echo '<tr><td class="alert-danger" colspan="8"><center>No Records Found</center></td></tr>';
				  }
				  }else{
				  echo '<tr><td class="alert-danger" colspan="8"><center>No Records Found</center></td></tr>';
				  }
				  ?>
				  
                  </tbody>
                </table>
      <?php echo $print; ?>
						</div>
					</form>					

			<!-------------------------------------------------------------------------------------->

			</div>
		</div>
	</div>
</div>
</div>
</form>
<script type="text/javascript" src="HR_Head/iCheck/jquery-2.2.3.min.js"></script>
<script src="HR_Head/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    //Enable iCheck plugin for checkboxes
    //iCheck for checkbox and radio inputs
    $('.mailbox-messages input[type="checkbox"]').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      radioClass: 'iradio_flat-blue'
    });

    //Enable check and uncheck all functionality
    $(".checkbox-toggle").click(function () {
      var clicks = $(this).data('clicks');
      if (clicks) {
        //Uncheck all checkboxes
        $(".mailbox-messages input[type='checkbox']").iCheck("uncheck");
        $(".fa", this).removeClass("fa-check-square-o").addClass('fa-square-o');
      } else {
        //Check all checkboxes
        $(".mailbox-messages input[type='checkbox']").iCheck("check");
        $(".fa", this).removeClass("fa-square-o").addClass('fa-check-square-o');
      }
      $(this).data("clicks", !clicks);
    });

    //Handle starring for glyphicon and font awesome
    $(".mailbox-star").click(function (e) {
      e.preventDefault();
      //detect type
      var $this = $(this).find("a > i");
      var glyph = $this.hasClass("glyphicon");
      var fa = $this.hasClass("fa");

      //Switch states
      if (glyph) {
        $this.toggleClass("glyphicon-star");
        $this.toggleClass("glyphicon-star-empty");
      }

      if (fa) {
        $this.toggleClass("fa-star");
        $this.toggleClass("fa-star-o");
      }
    });
  });
</script>
