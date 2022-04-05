<script type="text/javascript" src="js/qsearch.js"></script>
<?php
if(!is_numeric($_GET['VID'])){
ob_start();
die('<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                Invalid Request, Please try Again
              </div>');
}
if(isset($_POST['UpdateVehicle']) && $_POST['VIDUP'] !=""){
$sql = "UPDATE vehicle_kofc_register SET RegNo='".$_POST['regno']."', Make='".$_POST['make']."', Details='".$_POST['details']."'
		       WHERE VehicleNo =".$_POST['VIDUP']."";
		$result = DB_query($sql);
		echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success!</h4>Vehicle record Number ' . $_POST['VIDUP'] . ' has been Updated sucessfully</div>';
}

$VID = $_GET['VID'];
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$results = "SELECT VehicleNo,
										RegNo,
										Make,
										Details
									FROM vehicle_kofc_register a
									WHERE a.VehicleNo ='" . $VID . "'";
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
						$rows = DB_fetch_array($welcome_viewed);
					

if (isset($_POST['CheckIn'])) {
	$Cancel = 0;

$sql= "SELECT COUNT(*) FROM vehicle_timeout WHERE VehicleNo='" . $VID . "' AND check_in=0";
	$result = DB_query($sql);
	$myrow = DB_fetch_row($result);
	if ($myrow[0] > 0) {
	$Cancel = 1;
	echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>Cannot Check Out this vehicle because it has a pending check Out through this gate. Please check in first to allow you check out again.</div>';
	}

	if ($Cancel == 0) {
	$checkid = GetNextTransNo(55, $db);
		$sql = "INSERT INTO vehicle_timeout (CheckID,
										VehicleNo,
										Destination, 
										DriverName, 
										KmOut,
										sec_officer,
										GateID)
								 VALUES ('" . $checkid . "',
								 	'" . $VID . "',
									'". $_POST['dest'] ."',
									'". $_POST['driver'] ."',
									'". filter_number_format($_POST['kmout']) ."',
									'". $_SESSION['UsersRealName'] ."',
									'10')";
		$result = DB_query($sql);
		echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success!</h4>Vehicle record Number ' . $VID . ' has been Checked Out Successully</div>';
	} //end if Delete supplier
}

if (isset($_POST['CheckOut'])) {
		$sql = "UPDATE vehicle_timeout SET check_in=1, KmIn='".filter_number_format($_POST['kmin'])."', time_in=NOW(), sec_officer_in='".$_SESSION['UsersRealName']."'
		       WHERE CheckID=".$_POST['checkid']."";
		$result = DB_query($sql);
		echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success!</h4>Vehicle record Number ' . $VID . ' has been Checked In successfully</div>';
}

				
echo '<div id="loadingbackground">';
		echo '<div id="progressBar" ><br />
			 <i class="fa fa-spinner fa-pulse fa-4x fa-fw"></i><br>PROCESSING. PLEASE WAIT...
			</div>';
		echo '</div>';
		
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
			  <form enctype="multipart/form-data" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; 
				  echo '<input type="hidden" name="VIDUP" value="' . $VID . '" />'; ?>
			<?php if(isset($_POST['Edit'])){
				 ?>
				 <div class="panel panel-default">
		<div class="panel-heading">Update Vehicle Information</div>
			<div class="panel-body">
			<div class="form-group">
								 <div class="col-md-4">Registration Number
							   <input  autocomplete="off" name="regno" type="text" value = "<?php echo strtoupper($rows['RegNo']); ?>" placeholder="Registration Number" class="form-control input-md" required=""/>
							  </div>
							    <div class="col-md-3">Make
							  <input  autocomplete="off" name="make" type="text" value = "<?php echo strtoupper($rows['Make']); ?>" placeholder="Make" class="form-control input-md" required=""/>
							  </div>
							 <div class="col-md-5">Details
							   <input autocomplete="off" name="details" type="text" value = "<?php echo strtoupper($rows['Details']); ?>" placeholder="Details" class="form-control input-md"/>
							  </div>
							</div>
							
					<div class="form-group">
							  <div class="col-md-12">
								<button id="submit" name="UpdateVehicle" class="btn btn-primary">Update</button>
								<div class="pull-right">
								<a href=""><input class="btn btn-warning" name="Cancel" value="Cancel" type="button" /></a>
								</div>
							  </div>
							</div>
				</div>
		</div>
					<?php }else{ ?>		
				<table  style="border:none; width:100%" class="table">
				<tr><td>
                <span style="font-weight:bold; font-size:20px;"><?php echo strtoupper($rows['RegNo']); ?> (<?php echo strtoupper($rows['Make']); ?>)</span> <div class="pull-right">
				<button id="submit" name="Edit" class="btn btn-default"><i class="fa fa-edit"></i> Edit</button>
              </td></tr>
				<tr><td height="25">Details : <?php echo '<b>'.$rows['Details'].'</b>'; ?></td></tr>
				</table>
				<?php } ?>
				</form>
				<div class="panel panel-default">
		<div class="panel-heading">Booking Information</div>
			<div class="panel-body">
			<form enctype="multipart/form-data" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
			<div class="form-group">
								 <div class="col-md-5">Kilometers Out
							   <input  autocomplete="off" name="kmout" type="text" value = "" placeholder="Kilometers Out" class="form-control input-md" required=""/>
							  </div>
							    <div class="col-md-7">Destination
							  <input  autocomplete="off" name="dest" type="text" value = "" placeholder="Destination" class="form-control input-md" required=""/>
							  </div>
							</div>
							<div class="form-group">
							 <div class="col-md-7">Driver Name
							   <input id="key" autocomplete="off" name="driver" type="text" value = "" placeholder="Driver Name" class="form-control input-md" required=""/>
							   <span id="result" style="z-index: 99; font-size:14px;" class="result"><span class="loading"></span></span>
							  </div>
							</div>
							
					<div class="form-group">
							  <div class="col-md-8">
								<button id="submit" name="CheckIn" class="btn btn-primary">Check Out</button>
							  </div>
							</div>
			</form>
			
			<form enctype="multipart/form-data" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
			<?php
				echo '<table style="width:100%;" class="table table-hover table-striped"><tbody>';
				echo '<tr>
						<th>' . _('Gate') . '</th>
						<th>' . _('Destination') . '</th>
						<th>' . _('Time Out') . '</th>
						<th>' . _('KM Out') . '</th>
						<th>' . _('Driver Name') . '</th>
						<th>' . _('KM In') . '</th>
						<th><center>' . _('Time In') . '</center></th>
					</tr>';
			$sqls= "SELECT gates.description as gate,
							`CheckID`, `VehicleNo`, `time_out`, `time_in`, `Destination`, `DriverName`, `KmOut`, `KmIn`, check_in
							FROM vehicle_timeout
							INNER JOIN gates ON gates.GateID=vehicle_timeout.GateID 
							WHERE VehicleNo=".$VID."
							GROUP BY vehicle_timeout.CheckID, vehicle_timeout.GateID ORDER BY check_in ASC, vehicle_timeout.time_in DESC
							LIMIT 10";
			$result = DB_query($sqls);
					while($row=DB_fetch_array($result)){
				echo '<tr><td><center >'.$row['gate'].'</center></td>
					<td>' . $row['Destination'] . '</td>
					<td width="160px">' . date("d, M Y h:i A",strtotime($row['time_out'])) . '</td>
					<td>' . number_format($row['KmOut']) . '</td>
					<td>' . $row['DriverName'] . '</td>
					<td>' . number_format($row['KmIn']) . '</td>
					<td>' . ($row['check_in']==0 ? '<button onclick="popshow(\'popDiv\','.$row['CheckID'].')" type="button" class="btn btn-danger btn-sm"> CheckIn</button>' : date("d, M Y h:i A",strtotime($row['time_in']))) . '</td>';
			echo '</tr>';
			}
			echo '<tbody></table>';
			
			
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
	
	
	echo '<table style="width: 200px;
						background:#FFFFFF;
						height: 120px;
						position: absolute;
						color: #000000;
						/* To align popup window at the center of screen*/
						top: 35%;
						left: 73%;
						margin-top: 180px;
						margin-left: -280px;">
			<tr>';
				echo "<th>" . _('Please Provide KM In') . ": </th></tr>";
			echo '<tr>
				<td>
				<input name="kmin" type="text" />
				<input name="checkid" id="checkids" type="hidden" />
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
			</div>
		</div>
              </div>

			
			 
            <!-- /.box-footer -->
            <div class="box-footer">
			  <!--<a href="PrintReq_Item_Service.php?<?php echo 'id='.$VID; ?>" target="_blank"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>-->
            </div>
			

            <!-- /.box-footer -->
          </div>
		  
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

<style type="text/css">
<!--
.title {
	font-size: x-large;
	font-family: "Times New Roman", Times, serif;
	font-weight: bold;
	padding-bottom:2px;
}
.bg{
	background-color:#00CCFF;
	font-family:"Times New Roman", Times, serif;
	font-size:16px;
	border-radius:4px 4px 1px 1px;
	padding-bottom:3px;
	padding:2px;
	color:#FFFFFF;
	font-weight:bold;
}
.line{
	border-bottom:inset;
	width:90%;
	border-bottom-color:#00CCFF;
}
.content {
    background-color:white;
    margin:0 auto;
    width:100%;
    /*border-collapse: collapse;*/
    border:thin outset #B3B3B3;
    
    -webkit-border-radius:  4px;
    -moz-border-radius:     4px;
    border-radius:          4px;
    -moz-box-shadow:    1px 1px 2px #C3C3C3;
	-webkit-box-shadow: 1px 1px 2px #C3C3C3;
	box-shadow:         1px 1px 2px #C3C3C3;
	-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')";
	filter: progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')
}


a{
text-decoration:none;
}
.time {color: #999999; font-size:10px;}
.image{
		 border-radius:25px;
		 width:50px;
		 height:50px;
		 padding:20px,20px,20px,20px;
}
/*bubble*/
.bubble
{
position: relative;
width: 90%;

min-height: 10px;
padding-left:18px;
background: #DEFFFF;
-webkit-border-radius:  4px;
    -moz-border-radius:     4px;
    border-radius:          4px;
    -moz-box-shadow:    1px 1px 2px #C3C3C3;
	-webkit-box-shadow: 1px 1px 2px #C3C3C3;
	box-shadow:         1px 1px 2px #C3C3C3;
	-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')";
	filter: progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3');

}

.bubble:after
{
content: '';
position: absolute;
border-style: solid;
border-width: 9px 15px 9px 0;
border-color: transparent #DEFFFF;
display: block;
width: 0;
z-index: 1;
left: -15px;
top: 7px;
}

.link{font-size:9px;}
-->
</style>