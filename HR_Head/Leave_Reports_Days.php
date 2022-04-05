<link rel="stylesheet" href="HR_Head/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="HR_Head/iCheck/flat/blue.css">
<form enctype="multipart/form-data" action="index.php?Application=HR&Ref=LeaveDaysReports" method="post">
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
					<th>Srv No</th>
					<th>Employee Name</th>
					<th>Leave Type</th>
					<th>Bal BFW</th>
					<th>Granted</th>
					<th>Total</th>
					<th>Applied</th>
					<th>Balance</th>
                  </tr>
				  <?php
				  $print ='';
				  $SoL ='';
 $page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 30; // Set how many records do you want to display per page.
$startpoint = ($page * $per_page) - $per_page;
if(isset($_GET["dept"])){$_POST["selectedtype"]=$_GET["dept"];}
if(isset($_GET["year"])){$_POST["selectedyear"]=$_GET["year"]; $_POST['Search']="1";}

				  if(isset($_POST['Search'])){ 
 
				  if($_POST['selectedtype'] !=0){
				  $sort = " year='".$_POST['selectedyear']."' AND d.departmentid=".$_POST['selectedtype']." ORDER BY d.description ASC,emp_fname ASC";
				  }else{
				  $sort =" year='".$_POST['selectedyear']."' ORDER BY d.description ASC,emp_fname ASC";
				  }
				  
				$state = "leave_days_allocation a
				  			INNER JOIN employee b ON a.emp_id = b.emp_id
							INNER JOIN leave_types c ON a.leave_type=c.id
							INNER JOIN departments d ON b.id_dept = d.departmentid
							WHERE ".$sort;
				$sql = "SELECT *, IFNULL((SELECT SUM(days) FROM leave_annual z 
				WHERE z.type=a.leave_type AND z.emp_id=a.emp_id AND YEAR(z.date_added)=a.year AND rejected=0 
				AND z.levelcheck >(SELECT MAX(levelcheck) FROM leave_approval_levels WHERE leave_type=z.leave_type) GROUP BY z.emp_id),0) as applied 
				FROM {$state} LIMIT {$startpoint} , {$per_page}";
				  $rest=DB_query($sql);

				  $i =1;
				  
				  $dept ="";
				  $user ="";
				  if(DB_num_rows($rest)>0){
				  while($row = DB_fetch_array($rest)){
				  if($dept != $row['departmentid']){
				  $no_user = 1;
				   echo  '<tr style="font-size:10px">
                    <th class="mailbox-subject" colspan="8">'.$row['description'].'</th>
					  </tr>';
				  $dept = $row['departmentid'];
				  }
                echo  '<tr style="font-size:10px">
					<td class="mailbox-subject">'.$row['emp_id'].'</td>
					<td class="mailbox-subject">'.$row['emp_fname'].' '.$row['emp_lname'].'</td>
					<td class="mailbox-subject">'.str_replace('Application', '', $row['type_name']).'</td>
					<td><span class="pull-right">'.$row['opening_bal'].'</span></td>
					<td><span class="pull-right">'.$row['leave_days'].'</span></td>
					<td><span class="pull-right">'.($row['opening_bal']+$row['leave_days']).'</span></td>
					<td><span class="pull-right">'.$row['applied'].'</span></td>
					<td class="mailbox-subject"><span class="pull-right">'.($row['opening_bal']+$row['leave_days']-$row['applied']).'</span></td>
					  </tr>';
				  $i++;
				  }
				  $search = 'dept='.$_POST['selectedtype'].'&year='.$_POST['selectedyear'];
				  $print ='<a href="PDFLeaveReportDaysPortrait.php?'.$search.'"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>';
				  
				  }else{
				  echo '<tr><td class="alert-danger" colspan="9"><center>No Records Found</center></td></tr>';
				  }
				  }else{
				  echo '<tr><td class="alert-danger" colspan="9"><center>No Records Found</center></td></tr>';
				  }
				  ?>
				  
                  </tbody>
                </table>
      <?php echo $print; ?>
	  <div class="pull-right">
				<div class="btn-group">
				<?php
				echo pagination($state,$per_page,$page,$url='?Application=HR&Ref=LeaveDaysReports&'.$search.'&');
				?>
                  </div>
                  <!-- /.btn-group -->
                </div>
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
