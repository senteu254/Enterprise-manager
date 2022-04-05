<script type="text/javascript" src="js/qsearch.js"></script>
<?php
if(!is_numeric($_GET['VID'])){
ob_start();
die('<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                Invalid Request, Please try Again
              </div>');
}
$VID = $_GET['VID'];
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$results = "SELECT VehicleNo,
										RegNo,
										Make,
										Org,
										DriverName,
										IdNo,
										phoneno,
										Destination,
										Date,
										description
									FROM vehicle_register a
									INNER JOIN departments b ON b.departmentid = a.departmentid
									WHERE a.VehicleNo ='" . $VID . "'";
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
						$rows = DB_fetch_array($welcome_viewed);
					

if (isset($_POST['CheckIn'])) {
	$Cancel = 0;
if(isset($_POST['gate']) && $_POST['gate']==""){
$Cancel = 1;
echo '<div class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>Please Select the gate you want to check in the visitor.</div>';
}
$sql= "SELECT COUNT(*) FROM vehicle_timein WHERE VehicleNo='" . $VID . "' AND GateID='". $_POST['gate'] ."' AND check_out=0";
	$result = DB_query($sql);
	$myrow = DB_fetch_row($result);
	if ($myrow[0] > 0) {
	$Cancel = 1;
	echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>Cannot Check In this vehicle because it has a pending checked in through this gate. Please check out first to allow you check in again.</div>';
	}

	if ($Cancel == 0) {
	$checkid = GetNextTransNo(55, $db);
		$sql = "INSERT INTO vehicle_timein (CheckID,
										VehicleNo,
										remarks,
										sec_officer,
										GateID)
								 VALUES ('" . $checkid . "',
								 	'". $VID . "',
									'". $_POST['purpose'] ."',
									'". $_SESSION['UserID'] ."',
									'". $_POST['gate'] . "')";
		$result = DB_query($sql);
		echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success!</h4>Vehicle record Number ' . $VID . ' has been Checked In</div>';
	} //end if Delete supplier
}

if (isset($_POST['CheckOut'])) {

		$sql = "UPDATE vehicle_timein SET check_out=1, remarks_out='".$_POST['remarks']."', time_out=NOW(), sec_officer_out='".$_SESSION['UserID']."'
		       WHERE CheckID=".$_POST['checkid']."";
		$result = DB_query($sql);
		echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success!</h4>Vehicle record Number ' . $VID . ' has been Checked Out</div>';
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
				<table  style="border:none; width:100%" class="table">
				<tr><td colspan="4">
                <h4 style="font-weight:bold; font-size:20px;"><?php echo strtoupper($rows['RegNo']); ?> (<?php echo strtoupper($rows['Make']); ?>)</h4> 
              </td></tr>
				<tr><td height="25">Organization:</td><td><?php echo '<b>'.$rows['Org'].'</b>'; ?></td>
				<td width="150" height="25">Driver Name:</td><td><?php echo ' <b>'.strtoupper($rows['DriverName']).'</b>'; ?></td></tr>
				<tr><td height="25">Destination:</td><td><?php echo '<b>'.$rows['Destination'].'</b>'; ?></td>
				<td width="150" height="25">ID Number:</td><td><?php echo ' <b>'.$rows['IdNo'].'</b>'; ?></td></tr>
				<tr><td height="25">Department: </td><td><?php echo '<b>'.strtoupper($rows['description']).'</b>'; ?></td>
				<td height="25">Phone Number:</td><td><?php echo '<b>'.$rows['phoneno'].'</b>'; ?></td></tr>
				</table>
				
				<div class="panel panel-default">
		<div class="panel-heading">Booking Information</div>
			<div class="panel-body">
			<form enctype="multipart/form-data" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
			<div class="form-group">
							  <div class="col-md-5">Gate
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
							   <div class="col-md-7">Purpose
							  <input type="text" placeholder="Comment" name="purpose" value="" class="form-control">
							  </div>
							</div>
							
					<div class="form-group">
							  <div class="col-md-8">
								<button id="submit" name="CheckIn" class="btn btn-primary">Check In</button>
							  </div>
							</div>
			</form>
			
			<form enctype="multipart/form-data" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
			<?php
				echo '<table style="width:100%;" class="table table-hover table-striped"><tbody>';
				echo '<tr>
						<th width="15%">' . _('Gate') . '</th>
						<th>' . _('Purpose') . '</th>
						<th>' . _('Time In') . '</th>
						<th><center>' . _('Time Out') . '</center></th>
					</tr>';
			$sqls= "SELECT gates.description as gate,
							remarks,
							time_in,
							time_out,
							check_out,
							CheckID
							FROM vehicle_timein 
							INNER JOIN gates ON gates.GateID=vehicle_timein.GateID 
							WHERE VehicleNo=".$VID."
							GROUP BY vehicle_timein.CheckID, vehicle_timein.GateID ORDER BY check_out ASC, vehicle_timein.time_in DESC
							LIMIT 10";
			$result = DB_query($sqls);
					while($row=DB_fetch_array($result)){
				echo '<tr><td><center >'.$row['gate'].'</center></td>
					<td>' . $row['remarks'] . '</td>
					<td width="160px">' . date("d, M Y h:i A",strtotime($row['time_in'])) . '</td>
					<td width="160px">' . ($row['check_out']==0 ? '<button onclick="popshow(\'popDiv\','.$row['CheckID'].')" type="button" class="btn btn-danger btn-sm"> CheckOut</button>' : date("d, M Y h:i A",strtotime($row['time_out']))) . '</td>';
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
	
	
	echo '<table style="width: 300px;
						background:#FFFFFF;
						height: 150px;
						position: absolute;
						color: #000000;
						/* To align popup window at the center of screen*/
						top: 35%;
						left: 73%;
						margin-top: 180px;
						margin-left: -280px;">
			<tr>';
				echo "<th>" . _('Please Give Us Any Remarks') . ": </th></tr>";
			echo '<tr>
				<td>
				<textarea name="remarks" class="form-control input-md" ></textarea>
				<input name="checkid" id="checkids" type="hidden" />
				</td>			
			</tr>
			<tr>
				<td><button type="submit" name="CheckOut" class="btn btn-primary btn-sm"> ' . _('CheckOut') . '</button> <div class="pull-right"><button onclick="hide(\'popDiv\')" type="button" class="btn btn-danger btn-sm"> Cancel</button></div>';
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