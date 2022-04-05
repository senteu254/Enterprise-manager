<script type="text/javascript" src="js/qsearch.js"></script>
<?php
if(isset($_POST['SearchVehicle'])){
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$results = "SELECT COUNT(VehicleNo), VehicleNo
									FROM vehicle_kofc_register
									WHERE RegNo ='" . $_POST['regno'] . "'";
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
						$rows = DB_fetch_row($welcome_viewed);
				if($rows[0] >0){
				$_SESSION['msg'] =  'Vehicle with Registration Number '.$_POST['idnumber'].' is registered in the system, please fill in the booking details below and Check out.';
				$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
				$redirect = htmlspecialchars($protocol.''.$_SERVER['HTTP_HOST'].''.$_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8').'?Application=SEC2&Link=KOFCVehicleRead&VID='.$rows[1].'';
						?>
						<script>
						window.location.href = "<?php echo $redirect; ?>";
						</script>
					<?php
				}else{
					echo '<div class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>Vehicle with Registration Number '.$_POST['regno'].' is not registered in the system, please confirm the details and try again OR Register this vehicle for the first time</div>';
				$RegNumber = $_POST['regno'];
				}
	}		

if (isset($_POST['CheckIn'])) {
	$VehicleID = GetNextTransNo(54, $db);
	$checkid = GetNextTransNo(55, $db);
	$sql = "INSERT INTO vehicle_kofc_register (VehicleNo,
										RegNo,
										Make,
										Details)
								 VALUES ('" . $VehicleID . "',
								 	'" . $_POST['regno'] . "',
									'" . $_POST['make'] . "',
									'" . $_POST['details'] . "')";
		
		$sqlcheck = "INSERT INTO vehicle_timeout (CheckID,
										VehicleNo,
										Destination, 
										DriverName, 
										KmOut,
										sec_officer,
										GateID)
								 VALUES ('" . $checkid . "',
								 	'" . $VehicleID . "',
									'". $_POST['dest'] ."',
									'". $_POST['driver'] ."',
									'". filter_number_format($_POST['kmout']) ."',
									'". $_SESSION['UsersRealName'] ."',
									'10')";
		$resultcheck = DB_query($sqlcheck);
		$result = DB_query($sql);
	$_SESSION['msg'] =  'Vehicle has been Checked Out successfully.';
				$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
				$redirect = htmlspecialchars($protocol.''.$_SERVER['HTTP_HOST'].''.$_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8').'?Application=SEC2&Link=KOFCVehicleRead&VID='.$VehicleID.'';
						?>
						<script>
						window.location.href = "<?php echo $redirect; ?>";
						</script>
					<?php

}

				
echo '<div id="loadingbackground">';
		echo '<div id="progressBar" ><br />
			 <i class="fa fa-spinner fa-pulse fa-4x fa-fw"></i><br>PROCESSING. PLEASE WAIT...
			</div>';
		echo '</div>';
?>

<form enctype="multipart/form-data" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
		<?php  if(isset($RegNumber) && $RegNumber !=""){  ?>
              <!-- /.mailbox-controls --> 
              <div class="mailbox-read-message">		  
      <div class="col-md-12 col-md-offset-">
			<div class="form-group">
							  <div class="col-md-6">Vehicle Reg No.
							  <input autocomplete="off"  name="regno" type="text" value = "" placeholder="Vehicle Reg No (KBA 123V)" class="form-control input-md" required=""/>
							  </div>
							   <div class="col-md-6">Details (Vehicle Description)
							   <input  autocomplete="off" name="details" type="text" value = "" placeholder="Details" class="form-control input-md" />
							  </div>
							   
							</div>
			
							<div class="form-group">
							  <div class="col-md-6">Make
							  	<input  autocomplete="off" name="make" type="text" value = "" placeholder="Make" class="form-control input-md" required=""/>
							  </div>
							  <div class="col-md-6">Driver Name
							   <input id="key" autocomplete="off" name="driver" type="text" value = "" placeholder="Driver Name" class="form-control input-md" required=""/>
							   <span id="result" style="z-index: 99;" class="result"><span class="loading"></span></span>
							  </div>
							</div>
							
							<div class="form-group">
							  <div class="col-md-6">Destination
							  <input  autocomplete="off" name="dest" type="text" value = "" placeholder="Destination" class="form-control input-md" required=""/>
							  </div>
							  <div class="col-md-6">Kilometer Out
							   <input  autocomplete="off" name="kmout" type="text" value = "" placeholder="Kilometer Out" class="form-control input-md" required=""/>
							  </div>
							</div>
							
			</div>
							
					<div class="form-group">
							  <div class="col-md-12">
								<button id="submit" name="CheckIn" class="btn btn-primary">Check Out</button>
								<div class="pull-right">
								<a href="index.php?Application=SEC2&Link=KOFCVehicles"><input class="btn btn-warning" name="Cancel" value="Cancel" type="button" /></a>
								</div>
							  </div>
							</div>

		</div>
		
		<?php }else{ ?>
	
	<div class="form-group">
							  <div class="col-md-5">
							  <input id="regnos" autocomplete="off" pattern="(?!^\s+$)[^<>+]{1,40}" name="regno" type="text" value = "" placeholder="Enter Vehicle Reg Number" class="form-control input-md" required=""/>
							  <span id="resultvehicle" style="z-index: 99;" class="resultvehicle"><span class="loading"></span></span>
							  </div>
							   <div class="col-md-4">
							   <button id="submit" name="SearchVehicle" class="btn btn-primary">Search Vehicle</button>
							  </div>
							</div>
			
	<?php } ?>


</form>
            <!-- /.box-footer -->
          </div>
 <script type="text/javascript">
 $("#regnos").keyup( function(event){
	var key = $("#regnos").val();

	if( key != 0){
		$.ajax({
		type: "GET",
		data: ({key: key, Type: 'Vehicle'}),
		url:"Sec_quicksearchVehicle.php",
		success: function(response) {
		$(".resultvehicle").slideDown().html(response); 
		}
		})
		
		}else{
		
		$(".resultvehicle").slideUp();
		$(".resultvehicle").val("");
		}
 })
 </script>