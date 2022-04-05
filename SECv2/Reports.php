<?php				
		
 error_reporting( error_reporting() & ~E_NOTICE ); if(!empty($_SESSION['msg'])) echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success</h4>
                ' . ucwords($_SESSION['msg']). '
              </div>'; unset($_SESSION['msg']); 
			 if(!empty($_SESSION['errmsg'])) echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>
                ' . ucwords($_SESSION['errmsg']). '
              </div>'; unset($_SESSION['errmsg']); 
?>             
<!-- /.mailbox-controls -->
              <div class="mailbox-read-message">
	<div class = "row">
<div class="col-md-4 col-md-offset-">
				<div class="panel panel-default">
			<div class="panel-body">
				<ul class="nav nav-pills nav-stacked">
                <li <?php echo (($_GET['Rep']=="Visitors" || !isset($_GET['Rep'])) ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>Reports&Rep=Visitors">Visitors Booking Report </a></li>
				<li <?php echo (($_GET['Rep']=="Materials") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>Reports&Rep=Materials">Material Booking Report</a></li>
				 <li <?php echo (($_GET['Rep']=="Vehicles") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>Reports&Rep=Vehicles">Vehicles Booking Report </a></li>
                <li <?php echo (($_GET['Rep']=="KOFCVehicles") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>Reports&Rep=KOFCVehicles">Vehicles Booking Report (KOFC) </a></li
              </ul>
			</div>
		</div>
		</div>
			<?php
	if(isset($_POST['GenerateReport'])){
$view = (isset($_GET['Rep']) && $_GET['Rep'] != '') ? $_GET['Rep'] : '';
					switch ($view) {
						case 'Visitors' :
							?>
								<script>
								window.location.href = "Sec_VisitorBookingReport.php<?php echo '?Gate='.$_POST['Gate'].'&From='.$_POST['datefrom'].'&To='.$_POST['dateto'].''; ?>";
								</script>
							<?php
							break;
						case 'Vehicles' :
							?>
								<script>
								window.location.href = "Sec_VehicleBookingReport.php<?php echo '?Gate='.$_POST['Gate'].'&From='.$_POST['datefrom'].'&To='.$_POST['dateto'].''; ?>";
								</script>
							<?php
							break;
						case 'KOFCVehicles' :
							?>
								<script>
								window.location.href = "Sec_VehicleBookingReportKOFC.php<?php echo '?From='.$_POST['datefrom'].'&To='.$_POST['dateto'].''; ?>";
								</script>
							<?php
							break;
						case 'Materials' :
							?>
								<script>
								window.location.href = "Sec_MaterialBookingReport.php<?php echo '?Gate='.$_POST['Gate'].'&From='.$_POST['datefrom'].'&To='.$_POST['dateto'].'&Report='.$_POST['Report'].''; ?>";
								</script>
							<?php
							break;
						default :
							?>
								<script>
								window.location.href = "Sec_VisitorBookingReport.php<?php echo '?Gate='.$_POST['Gate'].'&From='.$_POST['datefrom'].'&To='.$_POST['dateto'].''; ?>";
								</script>
							<?php
							break;
						}

}
	?>	
		<div class="col-md-8 col-md-offset-">
				<div class="panel panel-default">
		<div class="panel-heading">Select Criteria</div>
			<div class="panel-body">
			<form enctype="multipart/form-data" action=""  method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
			<div class="form-group">
			<?php if(isset($_GET['Rep']) && $_GET['Rep'] =='KOFCVehicles'){
				}else{
			 ?>
							  <div class="col-md-4">Gate
							   <?php 
							   $sql = "SELECT gates.GateID,
										gates.description
									FROM gates";
									$result = DB_query($sql);
							   ?>
							  	<select id="gate" name="Gate" required="true" class="form-control">
								<?php
								echo '<option value="All">All Gates</option>';
								while ($myrow = DB_fetch_array($result)) {
								echo '<option value="'. $myrow[0] .'">'. $myrow[1] .'</option>';
								}
								?>
								</select>
							  </div>
							  <?php } ?>
							   <div class="col-md-4">Date From:
							  <?php echo '<input type="text" class="date" id="form-control" alt="' .$_SESSION['DefaultDateFormat'] . '" name="datefrom" value="' . Date($_SESSION['DefaultDateFormat']) . '" required=""/>'; ?>
							  </div>
							  <div class="col-md-4">Date To:
							  <?php echo '<input type="text" class="date" id="form-control" alt="' .$_SESSION['DefaultDateFormat'] . '" name="dateto" value="' . Date($_SESSION['DefaultDateFormat']) . '" required=""/>'; ?>
							  </div>
							</div>
							<?php if(isset($_GET['Rep']) && $_GET['Rep']=='Materials'){ ?>
							<div class="form-group">
							  <div class="col-md-5">Report
							   <?php 
							   $array = array('Visitors'=>'Visitors Report', 'Staff'=>'Staff Report');
							   ?>
							  	<select name="Report" required="true" class="form-control">
								<?php
								foreach($array as $key=>$val) {
								echo '<option value="'. $key .'">'. $val .'</option>';
								}
								?>
								</select>
							  </div>
							 </div>
							 <?php } ?>
							
					<div class="form-group">
							  <div class="col-md-8">
								<button id="submit" name="GenerateReport" class="btn btn-primary"><i class="fa fa-file-pdf-o" ></i> Generate Report</button>
							  </div>
							</div>
			</form>
	
			</div>
		</div>
		</div>
	</div>
</div>

			
			 
            <!-- /.box-footer -->
            <div class="box-footer">
			  <!--<a href="PrintReq_Item_Service.php?<?php echo 'id='.$VID; ?>" target="_blank"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>-->
            </div>
			

            <!-- /.box-footer -->
          </div>
  