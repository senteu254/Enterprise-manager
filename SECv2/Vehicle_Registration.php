<script type="text/javascript" src="js/qsearch.js"></script>
	<!-- First, include the Webcam.js JavaScript Library -->
<script type="text/javascript" src="js/webcam.js"></script>
<?php		

if (isset($_POST['CheckIn'])) {
	$Cancel = 0;
if(isset($_POST['gate']) && $_POST['gate']==""){
$Cancel = 1;
echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>Please Select the gate you want to check in the visitor.</div>';
}

	if ($Cancel == 0) {
	$VehicleID = GetNextTransNo(54, $db);
	$checkid = GetNextTransNo(55, $db);
	$sql = "INSERT INTO vehicle_register (VehicleNo,
										RegNo,
										Make,
										Org,
										DriverName,
										IdNo,
										phoneno,
										Destination,
										Date,
										departmentid)
								 VALUES ('" . $VehicleID . "',
								 	'" . $_POST['regno'] . "',
									'" . $_POST['make'] . "',
									'" . $_POST['org'] . "',
									'" . $_POST['driver'] . "',
									'" . $_POST['idno'] . "',
									'" . $_POST['phoneno'] . "',
									'" . $_POST['dest'] . "',
									'DATE(NOW())',
									'" . $_POST['departmentid'] . "')";
		
		$sqlcheck = "INSERT INTO vehicle_timein (CheckID,
										VehicleNo,
										remarks,
										sec_officer,
										GateID)
								 VALUES ('" . $checkid . "',
								 	'" . $VehicleID . "',
									'". $_POST['purpose'] ."',
									'". $_SESSION['UserID'] ."',
									'" . $_POST['gate'] . "')";
		$resultcheck = DB_query($sqlcheck);
		$result = DB_query($sql);
		$Type = $_POST['Type'];
	$_SESSION['msg'] =  'Vehicle has been Checked In successfully.';
				$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
				$redirect = htmlspecialchars($protocol.''.$_SERVER['HTTP_HOST'].''.$_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8').'?Application=SEC2&Link=VehicleRead&VID='.$VehicleID.'';
						?>
						<script>
						window.location.href = "<?php echo $redirect; ?>";
						</script>
					<?php

	} 
}

				
echo '<div id="loadingbackground">';
		echo '<div id="progressBar" ><br />
			 <i class="fa fa-spinner fa-pulse fa-4x fa-fw"></i><br>PROCESSING. PLEASE WAIT...
			</div>';
		echo '</div>';
?>

<form enctype="multipart/form-data" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message">
			  
      <div class="col-md-12 col-md-offset-">
			<div class="form-group">
							  <div class="col-md-6">Vehicle Reg No.
							  <input autocomplete="off"  name="regno" type="text" value = "" placeholder="Vehicle Reg No (KBA 123V)" class="form-control input-md" required=""/>
							  </div>
							   <div class="col-md-6">Organization
							   <input  autocomplete="off" name="org" type="text" value = "" placeholder="Organization" class="form-control input-md" />
							  </div>
							   
							</div>
			
							<div class="form-group">
							  <div class="col-md-6">Make
							  	<input  autocomplete="off" name="make" type="text" value = "" placeholder="Make" class="form-control input-md" required=""/>
							  </div>
							  <div class="col-md-6">Driver Name
							   <input  autocomplete="off" name="driver" type="text" value = "" placeholder="Driver Name" class="form-control input-md" required=""/>
							  </div>
							</div>
							
							<div class="form-group">
							  <div class="col-md-6">Destination
							  <input  autocomplete="off" name="dest" type="text" value = "" placeholder="Destination" class="form-control input-md" required=""/>
							  </div>
							  <div class="col-md-6">ID Number
							   <input  autocomplete="off" name="idno" type="text" value = "" placeholder="ID Number" class="form-control input-md" required="" />
							  </div>
							</div>
							
							<div class="form-group">
							  <div class="col-md-6">Department
							   <?php 
								echo '<select required="required" class="form-control" name="departmentid">';
								echo '<option selected="selected" value="">--Please Select Department--</option>';
								$result=DB_query("SELECT departmentid, description FROM departments");
								while ($myrow = DB_fetch_array($result)) {
									echo '<option value="' . $myrow['departmentid'] . '">' . $myrow['description'] . '</option>';
								} //end while loop
								echo '</select>';
								
								?>
							  </div>
							  <div class="col-md-6">Phone No
							   <input  autocomplete="off" name="phoneno" type="text" value = "" placeholder="Phone No" class="form-control input-md" required=""/>
							  </div>
							</div>
							
							<div class="form-group">
							  <div class="col-md-6">Gate
								<?php 
							   $sql = "SELECT gates.GateID,
										gates.description
									FROM gates";
									$result = DB_query($sql);
							   ?>
							  	<select id="gate" name="gate" required="true" class="form-control">
								<?php
								echo '<option selected="selected" value="">--Please Select Gate--</option>';
								while ($myrow = DB_fetch_array($result)) {
								echo '<option value="'. $myrow[0] .'">'. $myrow[0] .'-'. $myrow[1] .'</option>';
								}
								?>
								</select>
							  </div>
							  <div class="col-md-6">Purpose
							   <input  autocomplete="off" name="purpose" type="text" value = "" placeholder="Purpose" class="form-control input-md" />
							  </div>
							</div>
							
			</div>
							
					<div class="form-group">
							  <div class="col-md-12">
								<button id="submit" name="CheckIn" class="btn btn-primary">Check In</button>
								<div class="pull-right">
								<a href="index.php?Application=SEC2&Link=NewMaterial"><input class="btn btn-warning" name="Cancel" value="Cancel" type="button" /></a>
								</div>
							  </div>
							</div>

		</div>

</form>
            <!-- /.box-footer -->
          </div>
