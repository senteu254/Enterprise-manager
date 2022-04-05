
<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php
if(isset($_GET['PID']) && $_GET['PID']!="" && is_numeric($_GET['PID'])){
$sqlz="SELECT * FROM fixedassetplanning WHERE planid=".$_GET['PID']."";
$RESz=DB_query($sqlz);
$rowz = DB_fetch_array($RESz);
$sql="INSERT INTO fixedassetplantask (planid,assetid,fyear,month,officer,sent)
					VALUES(".$rowz['planid'].",'".$rowz['assetid']."','".$rowz['fyend']."','".date('m')."','".$_SESSION['UserID'].'-'.$_SESSION['UsersRealName']."',1)";
$RES=DB_query($sql);
echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success</h4>
                Congratulations, Your Service Request for the selected machine has been forwarded for Maintenance.
              </div>';
}

						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$maintenance = " fixedassets z 
							INNER JOIN fixedassetlocations a on a.locationid = z.assetlocation 
							INNER JOIN fixedassetplanning e ON z.assetid = e.assetid
							WHERE fyend='".FormatDateForSQL(Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],0)))."' 
							AND FIND_IN_SET(".date('m').",months)>0 AND (SELECT COUNT(planid) FROM fixedassetplantask q WHERE q.planid=e.planid AND month=".date('m').")=0 AND (SELECT caneditMP FROM www_users WHERE userid='".$_SESSION['UserID']."')=1";
						$sql = "SELECT * FROM {$maintenance}";
						$welcome_viewed = DB_query($sql,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($welcome_viewed);
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	?>
					<fieldset>
						<div class="box-body no-padding">
              <div class="mailbox-controls">
                <!-- Check all button -->
              <div class="table-responsive mailbox-messages">
			  
                <table id="myTable" style="width:100%; font-size:10px;" class="table table-hover table-striped">
				<thead>
				<th>Status</th><th>Asset Name</th><th>Location</th><th>Status</th><th>Month</th><th>Planned By</th><th>PlannedOn</th><th>Action</th>
				</thead>
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
                  echo '<tr>
                    <td width="35" class="mailbox-star"><a style="color:green;" href="#"><i class="fa fa-star text-yellow">New</i> </a></td>
                    <td class="mailbox-name"><a href="index.php?Application=FA&Link=PlanningScheduleRead&VID='.$row['planid'].'">'.strtoupper($row['longdescription']).'</a></td>
                    <td class="mailbox-subject">'.$row['locationdescription'].'</td>
                    <td class="mailbox-attachment">'.$row['servicestatus'].'</td>
					<td class="mailbox-attachment">'.date('M').'</td>
					<td class="mailbox-attachment">'.$row['planningofficer'].'</td>
                    <td class="mailbox-date">'.ConvertSQLDate($row['inputdate']).'</td>
					<td class="mailbox-date"><a style="color:white; font-size:10px;" class="label label-success" onclick="return confirm(\'Are you sure you want to send this Request to Foreman for Maintenance Action?\')" href="index.php?Application=FA&Link=PlanningSchedule&PID='.$row['planid'].'"><i class="fa fa-send text-yellow"></i> Send Request</a></td>
                  </tr>';
				  }
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
	<?php
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$maintenance = " fixedassets z 
							INNER JOIN fixedassetlocations a on a.locationid = z.assetlocation 
							INNER JOIN fixedassetplanning e ON z.assetid = e.assetid
							INNER JOIN fixedassetplantask f ON e.planid = f.planid
							WHERE fyend='".FormatDateForSQL(Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],0)))."' AND (SELECT caneditMP FROM www_users WHERE userid='".$_SESSION['UserID']."')=1";
						$sql = "SELECT *, (SELECT COUNT(requestid) FROM fixedassettasks q WHERE q.requestid=e.planid AND completed=0) AS Num FROM {$maintenance}";
						$welcome_viewed = DB_query($sql,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($welcome_viewed);
	//if($num_rows >0){					
	?>				
	
					
		</div>
		</div>
	<div class="panel panel-danger">
		<div class="panel-heading">Machines Sent for Maintenance this Month</div>
			<div class="panel-body">
			
			<div class="table-responsive mailbox-messages">
			  
                <table id="myTable2" style="width:100%; font-size:10px;" class="table table-hover table-striped">
				<thead>
				<th>Status</th><th>Asset Name</th><th>Location</th><th>Status</th><th>Month</th><th>Planned By</th><th>Planned Date</th>
				</thead>
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
					$sql="SELECT COUNT(requestid) AS Num,completed,accepted FROM fixedassettasks q WHERE q.requestid=".$row['planid'];
					$res= DB_query($sql);
					$rowx = DB_fetch_array($res);
					if($rowx['Num']>0 && $rowx['completed']==2){
					$state = '<a style="color:blue;" href="#"><i class="fa fa-star">Completed</i> </a>';
					}elseif($rowx['Num']>0 && $rowx['completed']==1){
					$state = '<a style="color:green;" href="#"><i class="fa fa-star">Work Completed</i> </a>';
					}elseif($rowx['Num']>0 && $rowx['accepted']==1){
					$state = '<a style="color:green;" href="#"><i class="fa fa-star">Ongoing</i> </a>';
					}elseif($rowx['Num']>0 && $rowx['accepted']==0){
					$state = '<a style="color:orange;" href="#"><i class="fa fa-star">Scheduled</i> </a>';
					}else{
					$state = '<a style="color:#FF6600;" href="#"><i class="fa fa-star">Sent</i> </a>';
					}
                  echo '<tr>
                    <td width="35" class="mailbox-star">'.$state.'</td>
                    <td class="mailbox-name"><a href="index.php?Application=FA&Link=PlanningScheduleRead&VID='.$row['planid'].'">'.strtoupper($row['longdescription']).'</a></td>
                    <td class="mailbox-subject">'.$row['locationdescription'].'</td>
                    <td class="mailbox-attachment">'.$row['servicestatus'].'</td>
					 <td class="mailbox-attachment">'.$row['month'].'</td>
					<td class="mailbox-attachment">'.$row['officer'].'</td>
                    <td class="mailbox-date">'.ConvertSQLDate($row['inputdate']).'</td>
                  </tr>';
				  }
				  }
				  
				  ?>
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
			  <?php //} ?>
<script>
function myFunction() {
  var input, filter, table, tr, td, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>		