
<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php
if (isset($_POST['CheckOut'])) {
		$sql = "UPDATE vehicle_timeout SET check_in=1, KmIn='".filter_number_format($_POST['kmin'])."', time_in='".date('Y-m-d H:i:s')."', sec_officer_in='".$_SESSION['UsersRealName']."'
		       WHERE CheckID=".$_POST['checkid']."";
		$result = DB_query($sql);
		echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success!</h4>Vehicle record Number ' . $VID . ' has been Checked In successfully</div>';
}

$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
 
$per_page = 30; // Set how many records do you want to display per page.
$startpoint = ($page * $per_page) - $per_page;


						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$statement = " SELECT a.VehicleNo, RegNo, Make, DriverName, Destination, check_in, time_in, time_out, Details, CheckID, KmOut
										FROM vehicle_kofc_register a
										INNER JOIN vehicle_timeout b ON a.VehicleNo = b.VehicleNo AND check_in=0
										WHERE DATE(time_out) = DATE(NOW()) ";

						$sqlforPages = $statement;
						$sql = $statement." GROUP BY a.VehicleNo ORDER BY time_out DESC LIMIT {$startpoint} , {$per_page}";
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
                <input type="text" placeholder="Search Reg Number..." id="myInput" onkeyup="myFunction()" name="Searchfield" value="" class="form-control">
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
				<th>Status</th><th>Reg No</th><th>Make</th><th>Driver Name</th><th>Destination</th><th>Time Out</th><th>KM Out</th><th>Action</th>
				</thead>
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
					
                  echo '<tr>
                    <td width="35" class="mailbox-star">'.($row['check_in']==0 ? '<a style="color:green;" href="#"><i class="fa fa-star text-yellow">Active</i> </a>':'<a style="color:red;" href="#"><i class="fa fa-star-o text-yellow">In</i> </a>').'</td>
                    <td class="mailbox-name"><a href="index.php?Application=SEC2&Link=KOFCVehicleRead&VID='.$row['VehicleNo'].'">'.strtoupper($row['RegNo']).'</a></td>
                    <td class="mailbox-subject">'.ucwords(strtolower($row['Make'])).'</td>
                    <td class="mailbox-attachment">'.$row['DriverName'].'</td>
					<td class="mailbox-date">'.$row['Destination'].'</td>
					<td class="mailbox-date">'.date("h:i A",strtotime($row['time_out'])).'</td>
					<td class="mailbox-date">'.number_format($row['KmOut']).'</td>
					<td width="100">'.($row['check_in']==0 ? '<button onclick="popshow(\'popDiv\','.$row['CheckID'].')" type="button" class="btn btn-danger btn-sm"> CheckIn</button>':''.date("h:i A",strtotime($row['time_out'])).'').'</td>
                  </tr>';
				  }
				  }else{
				  echo '<tr><td class="alert-danger" colspan="8"><center><b style="color:#FF0000">No Vehicle Registered Today</b></center></td></tr>';
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
						$sql = "SELECT a.VehicleNo, RegNo, Make, DriverName, Destination, check_in, time_in, time_out, Details, CheckID, KmOut
										FROM vehicle_kofc_register a
										INNER JOIN vehicle_timeout b ON a.VehicleNo = b.VehicleNo AND check_in=0
										WHERE DATE(time_out) < DATE(NOW()) AND check_in=0";
						$welcome_viewed = DB_query($sql,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($welcome_viewed);
	if($num_rows >0){					
	?>				
	
					
		</div>
		</div>
	<div class="panel panel-danger">
		<div class="panel-heading">Vehicles with Pending Check In</div>
			<div class="panel-body">
			
			<div class="table-responsive mailbox-messages">				
				<table id="myTable" style="width:100%;" class="table table-hover table-striped">
				<thead>
				<th>Status</th><th>Reg No</th><th>Make</th><th>Driver Name</th><th>Destination</th><th>Time Out</th><th>KM Out</th><th>Action</th>
				</thead>
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
					
                  echo '<tr>
                    <td class="mailbox-star">'.($row['check_in']==0 ? '<a style="color:red;" href="#"><i class="fa fa-star text-yellow">Active for '.calculate_time_span($row['time_out']).'</i> </a>':'<a style="color:red;" href="#"><i class="fa fa-star-o text-yellow">In</i> </a>').'</td>
                    <td class="mailbox-name"><a href="index.php?Application=SEC2&Link=KOFCVehicleRead&VID='.$row['VehicleNo'].'">'.strtoupper($row['RegNo']).'</a></td>
                    <td class="mailbox-subject">'.ucwords(strtolower($row['Make'])).'</td>
                    <td class="mailbox-attachment">'.$row['DriverName'].'</td>
					<td class="mailbox-date">'.$row['Destination'].'</td>
					<td class="mailbox-date">'.date("d, M Y  h:i A",strtotime($row['time_out'])).'</td>
					<td class="mailbox-date">'.number_format($row['KmOut']).'</td>
					<td width="100">'.($row['check_in']==0 ? '<button onclick="popshow(\'popDiv\','.$row['CheckID'].')" type="button" class="btn btn-danger btn-sm"> CheckIn</button>':''.date("h:i A",strtotime($row['time_out'])).'').'</td>
                  </tr>';
				  }
				  }else{
				  echo '<tr><td class="alert-danger" colspan="8"><center><b style="color:#FF0000">No Vehicle Registered Today</b></center></td></tr>';
				  }
				  
				  ?>
                  </tbody>
                </table>
			  
                <!-- /.table -->
              </div>
			  <?php 
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
	
	echo '<form enctype="multipart/form-data" onsubmit="return document.getElementById(\'loadingbackground\').style.display = \'block\';" method="post" class="form-horizontal">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table style="width: 200px;
						background:#FFFFFF;
						height: 120px;
						position: absolute;
						color: #000000;
						/* To align popup window at the center of screen*/
						top: 0%;
						left: 73%;
						margin-top: 130px;
						margin-left: -280px;">
			<tr>';
				echo "<th>" . _('Please Provide KM In') . ": </th></tr>";
			echo '<tr>
				<td>
				<input name="kmin" type="number" />
				<input name="checkid" id="checkids" type="hidden" required />
				</td>			
			</tr>
			<tr>
				<td><button type="submit" name="CheckOut" class="btn btn-primary btn-sm"> ' . _('CheckIn') . '</button> <div class="pull-right"><button onclick="hide(\'popDiv\')" type="button" class="btn btn-danger btn-sm"> Cancel</button></div>';
	echo '</td>
			</tr>
			</table>';
	
	echo '</div>';
			  ?>
	</form>
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
		  <script type="text/javascript">
			function popshow(div,id) {
				document.getElementById(div).style.display = 'block';
				document.getElementById('checkids').value = id;
			}
			function hide(div) {
				document.getElementById(div).style.display = 'none';
			}
			//To detect escape button
			document.onkeydown = function(evt) {
				evt = evt || window.event;
				if (evt.keyCode == 27) {
					hide('popDiv');
				}
			};
		</script>