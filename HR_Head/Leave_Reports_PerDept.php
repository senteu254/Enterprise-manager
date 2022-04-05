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
			<option value="0">All Departments</option>
				<?php
				  $qry = "SELECT * FROM departments";
				  $rest=DB_query($qry);
				  while($row = DB_fetch_array($rest)){
                echo  '<option '.($_POST['selectedtype']==$row['departmentid'] ? 'selected' : '').' value="'.$row['departmentid'].'">'.$row['description'].'</option>';
				  }
				  ?>
			</select>
				</div>
			<div class="form-group">
			Year
			<select class="form-control input-md" required="" name="selectedyear">
				<?php
				  $year = date('Y')-5;
				  for($i = date('Y'); $i > $year; $i--){
                echo  '<option '.($_POST['selectedyear']==$i ? 'selected' : '').' value="'.$i.'">'.$i.'</option>';
				  }
				  ?>
			</select>
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
					<th>Leave Type</th>
					<th>From Date</th>
					<th>To Date</th>
					<th>Days</th>
					<th>Status</th>
					<th>Applied On</th>
                  </tr>
				  <?php
				  $print ='';
				  $SoL ='';
				  if(isset($_POST['Search'])){ 
 
				  if($_POST['selectedtype'] !=0){
				  $sort = " AND YEAR(date_added)='".$_POST['selectedyear']."' AND d.departmentid=".$_POST['selectedtype'];
				  }else{
				  $sort =" AND YEAR(date_added)='".$_POST['selectedyear']."'";
				  }
				  
				$qry = "SELECT a.emp_id,emp_lname,emp_fname,description,z.type_name,from_date,to_date,days,date_added,d.departmentid,
				IFNULL((SELECT emp_id FROM leave_annual z 
				WHERE z.leave_id=a.leave_id AND z.rejected=0 
				AND z.levelcheck >(SELECT MAX(levelcheck) FROM leave_approval_levels WHERE leave_type=z.leave_type)),0) as approved, rejected FROM leave_annual a
				  			INNER JOIN employee b ON a.emp_id = b.emp_id
							INNER JOIN leave_types c ON a.leave_type=c.id
							INNER JOIN departments d ON b.id_dept = d.departmentid
							INNER JOIN leave_all_types z ON a.type=z.id
							WHERE send=1 ".$sort;
				$qry2 = "SELECT a.emp_id,emp_lname,emp_fname,description,type_name,from_date,to_date,days,date_added,d.departmentid,
				IFNULL((SELECT emp_id FROM leave_off_duty z 
				WHERE z.off_id=a.off_id AND z.rejected=0 
				AND z.levelcheck >(SELECT MAX(levelcheck) FROM leave_approval_levels WHERE leave_type=z.leave_type)),0) as approved, rejected FROM leave_off_duty a
				  			INNER JOIN employee b ON a.emp_id = b.emp_id
							INNER JOIN leave_types c ON a.leave_type=c.id
							INNER JOIN departments d ON b.id_dept = d.departmentid
							WHERE send=1 ".$sort;
				$qry3 = "SELECT a.emp_id,emp_lname,emp_fname,description,type_name,date as from_date,date as to_date,0,date_added,d.departmentid,
				IFNULL((SELECT emp_id FROM leave_half_day z 
				WHERE z.half_id=a.half_id AND z.rejected=0 
				AND z.levelcheck >(SELECT MAX(levelcheck) FROM leave_approval_levels WHERE leave_type=z.leave_type)),0) as approved, rejected FROM leave_half_day a
				  			INNER JOIN employee b ON a.emp_id = b.emp_id
							INNER JOIN leave_types c ON a.leave_type=c.id
							INNER JOIN departments d ON b.id_dept = d.departmentid
							WHERE send=1 ".$sort;

				  $sql = $qry." UNION ALL ".$qry2." UNION ALL ".$qry3." ORDER BY departmentid ASC,emp_id ASC,date_added DESC";
				  $rest=DB_query($sql);

				  $i =1;
				  
				  $dept ="";
				  $user ="";
				  if(DB_num_rows($rest)>0){
				  while($row = DB_fetch_array($rest)){
				  if($dept != $row['departmentid']){
				  $no_user = 1;
				   echo  '<tr style="font-size:10px">
                    <th class="mailbox-subject" colspan="6">'.$row['description'].'</th>
					  </tr>';
				  $dept = $row['departmentid'];
				  }
				  if($user != $row['emp_id']){
				  
				   echo  '<tr style="font-size:10px">
                    <td class="mailbox-subject" colspan="6"><strong>'.$no_user.'. '.$row['emp_id'].' - '.$row['emp_lname'].' '.$row['emp_fname'].'</strong></td>
					  </tr>';
				  $user = $row['emp_id'];
				  $no_user ++;
				  }
                echo  '<tr style="font-size:10px">
					<td class="mailbox-subject">'.str_replace('Application', '', $row['type_name']).'</td>
					<td class="mailbox-subject">'.ConvertSQLDate($row['from_date']).'</td>
					<td class="mailbox-subject">'.ConvertSQLDate($row['to_date']).'</td>
					<td class="mailbox-subject">'.$row['days'].'</td>
					<td class="mailbox-subject">'.($row['rejected']==1 ? '<b style="background-color:#e74c3c;">Rejected</b>' : ''.($row['approved']==0 ? '<b style="background-color:#2980b9;">Pending</b>' : '<b style="background-color:#27ae60;">Approved</b>').'').'</td>
					<td class="mailbox-subject">'.ConvertSQLDate($row['date_added']).'</td>
					  </tr>';
				  $i++;
				  }
				  $search = 'dept='.$_POST['selectedtype'].'&year='.$_POST['selectedyear'];
				  $print ='<a target="_blank" href="PDFLeaveReportPerDeptPortrait.php?'.$search.'"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>';
				  
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

<style type="text/css">
b {
    border-radius: 2px;
	padding-right:5px;
	padding-left:5px;
	padding-bottom:2px;
	color:#FFFFFF;
	font-weight:bold;
    width: 35px;
	font-family:"Times New Roman", Times, serif;
}
</style>
