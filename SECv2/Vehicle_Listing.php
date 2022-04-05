
<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php
if (isset($_POST['CheckOutID']) && $_POST['CheckOutID'] !="") {

		$sql = "UPDATE vehicle_timein SET check_out=1, remarks_out='".$_POST['remarks']."', sec_officer_out='".$_SESSION['UsersRealName']."'
		       WHERE CheckID=".$_POST['CheckOutID']."";
		$result = DB_query($sql);
		echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success!</h4>Vehicle record Number ' . $_POST['VID'] . ' has been Checked Out</div>';
}

$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
 
$per_page = 30; // Set how many records do you want to display per page.
$startpoint = ($page * $per_page) - $per_page;


						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$statement = " SELECT a.VehicleNo, RegNo, Make, Org, DriverName, IdNo, phoneno, Destination, check_out, time_in, time_out
										FROM vehicle_register a
										INNER JOIN vehicle_timein b ON b.VehicleNo = a.VehicleNo
										WHERE DATE(time_in) = DATE(NOW()) ";

						$sqlforPages = $statement;
						$sql = $statement." GROUP BY a.VehicleNo ORDER BY time_in DESC LIMIT {$startpoint} , {$per_page}";
						$welcome_viewed = DB_query($sql,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($welcome_viewed);
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			
			 error_reporting( error_reporting() & ~E_NOTICE ); if(!empty($_SESSION['msgx'])) echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success</h4>
                ' . ucwords($_SESSION['msgx']). '
              </div>'; unset($_SESSION['msgx']); 
			 if(!empty($_SESSION['errmsg'])) echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>
                ' . ucwords($_SESSION['errmsg']). '
              </div>'; unset($_SESSION['errmsg']); 
	?>
					<fieldset>
						<div class="box-body no-padding">
              <div class="mailbox-controls">
                <!-- Check all button -->
				<div class="row">
                <div class="col-xs-1">
                <!-- /.btn-group -->
                <a href=""><button type="button" title="Refresh" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button></a>
				</div>
				<div class="col-xs-4">
				<div class="input-group input-group-sm">
                <input type="text" placeholder="Search Owner Name..." id="myInput" onkeyup="myFunction()" name="Searchfield" value="" class="form-control">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-info btn-flat"><i class="fa fa-search"></i></button>
                    </span>
              </div> 
			  </div>
			  <div class="col-xs-7">
					<div class="pull-right">
				<div class="btn-group">
				<?php
				echo pagination_inbox($sqlforPages,$per_page,$page,$url='?Application=SEC2&Link=Visitors&');
				?>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.pull-right -->
				</div>

              </div>
              <div class="table-responsive mailbox-messages">
			  
                <table id="myTable" style="width:100%;" class="table table-hover table-striped">
				<thead>
				<th>Status</th><th>Reg No</th><th>Make</th><th>Driver Name</th><th>Phone Number</th><th>Destination</th><th>Action</th>
				</thead>
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
					
                  echo '<tr>
                    <td width="35" class="mailbox-star">'.($row['check_out']==0 ? '<a style="color:green;" href="#"><i class="fa fa-star text-yellow">Active</i> </a>':'<a style="color:red;" href="#"><i class="fa fa-star-o text-yellow">Out</i> </a>').'</td>
                    <td class="mailbox-name"><a href="index.php?Application=SEC2&Link=VehicleRead&VID='.$row['VehicleNo'].'">'.strtoupper($row['RegNo']).'</a></td>
                    <td class="mailbox-subject">'.ucwords(strtolower($row['Make'])).'</td>
                    <td class="mailbox-attachment">'.$row['DriverName'].'</td>
                    <td class="mailbox-date">'.$row['phoneno'].'</td>
					<td class="mailbox-date">'.$row['Destination'].'</td>
					<td width="100">'.($row['check_out']==0 ? '<a href="Vehicle_CheckOut.php?VID='.$row['VehicleNo'].'" rel="facebox"><button type="button" class="btn btn-danger btn-sm"><i class="fa fa-sign-out" aria-hidden="true"></i> CheckOut</button></a>':''.date("h:i A",strtotime($row['time_out'])).'').'</td>
                  </tr>';
				  }
				  }else{
				  echo '<tr><td class="alert-danger" colspan="7"><center><b style="color:#FF0000">No Vehicle Registered Today</b></center></td></tr>';
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
						$sql = " SELECT a.VehicleNo, RegNo, Make, Org, DriverName, IdNo, phoneno, Destination, check_out, time_in, time_out
										FROM vehicle_register a
										INNER JOIN vehicle_timein b ON b.VehicleNo = a.VehicleNo
										WHERE DATE(time_in) < DATE(NOW()) AND check_out=0";
						$welcome_viewed = DB_query($sql,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($welcome_viewed);
	if($num_rows >0){					
	?>				
	
					
		</div>
		</div>
	<div class="panel panel-danger">
		<div class="panel-heading">Vehicles with Pending Check Out</div>
			<div class="panel-body">
			
			<div class="table-responsive mailbox-messages">
			
			 <table id="myTable" style="width:100%;" class="table table-hover table-striped">
				<thead>
				<th>Status</th><th>Reg No</th><th>Make</th><th>Driver Name</th><th>Phone Number</th><th>Destination</th><th>Action</th>
				</thead>
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
					
                  echo '<tr>
                    <td class="mailbox-star">'.($row['check_out']==0 ? '<a style="color:red;" href="#"><i class="fa fa-star text-yellow">Active for '.calculate_time_span($row['time_in']).'</i> </a>':'<a style="color:red;" href="#"><i class="fa fa-star-o text-yellow">Out</i> </a>').'</td>
                    <td class="mailbox-name"><a href="index.php?Application=SEC2&Link=VehicleRead&VID='.$row['VehicleNo'].'">'.strtoupper($row['RegNo']).'</a></td>
                    <td class="mailbox-subject">'.ucwords(strtolower($row['Make'])).'</td>
                    <td class="mailbox-attachment">'.$row['DriverName'].'</td>
                    <td class="mailbox-date">'.$row['phoneno'].'</td>
					<td class="mailbox-date">'.$row['Destination'].'</td>
					<td width="100">'.($row['check_out']==0 ? '<a href="Vehicle_CheckOut.php?VID='.$row['VehicleNo'].'" rel="facebox"><button type="button" class="btn btn-danger btn-sm"><i class="fa fa-sign-out" aria-hidden="true"></i> CheckOut</button></a>':''.date("h:i A",strtotime($row['time_out'])).'').'</td>
                  </tr>';
				  }
				  }else{
				  echo '<tr><td class="alert-danger" colspan="7"><center><b style="color:#FF0000">No Vehicle Registered</b></center></td></tr>';
				  }
				  
				  ?>
                  </tbody>
                </table>
			  
                <!-- /.table -->
              </div>
			  <?php } ?>
<script>
function myFunction() {
  var input, filter, table, tr, td, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[2];
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