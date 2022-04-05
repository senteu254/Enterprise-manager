<script type="text/javascript" src="js/qsearch.js"></script>
<script type="text/javascript" src="js/webcam.js"></script>
<?php
if(!is_numeric($_GET['VID'])){
ob_start();
die('<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                Invalid Request, Please try Again
              </div>');
}
if(isset($_POST['UpdateV']) && $_POST['VIDUP'] !=""){
$sql = "UPDATE visitor_register SET v_name='".$_POST['name']."', v_idno='".$_POST['idno']."', v_from='".$_POST['from']."', v_phoneno='".$_POST['phoneno']."'
		       WHERE VisitorNo =".$_POST['VIDUP']."";
		$result = DB_query($sql);
		echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success!</h4>Visitor record Number ' . $_POST['VIDUP'] . ' has been Updated sucessfully</div>';
}

$VID = $_GET['VID'];
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$results = "SELECT v_name,v_idno,v_phoneno,v_from,date,imagepath
									FROM visitor_register
									WHERE visitor_register.VisitorNo ='" . $VID . "'";
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
						$rows = DB_fetch_array($welcome_viewed);
					

if (isset($_POST['CheckIn'])) {
	$Cancel = 0;
if(isset($_POST['gate']) && $_POST['gate']==""){
$Cancel = 1;
prnMsg(_('Please Select the gate you want to check in the visitor.'),'warn');
echo '<div class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>Please Select the gate you want to check in the visitor.</div>';
}
$sql= "SELECT COUNT(*) FROM visitor_timein WHERE VisitorNo='" . $VID . "' AND GateID='". $_POST['gate'] ."' AND check_out=0";
	$result = DB_query($sql);
	$myrow = DB_fetch_row($result);
	if ($myrow[0] > 0) {
	$Cancel = 1;
	echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>Cannot Check In this visitor because he/she has a pending checked in through this gate. Please check out first to allow you check in again.</div>';
	}

	if ($Cancel == 0) {
	$checkid = GetNextTransNo(53, $db);
		$sql = "INSERT INTO visitor_timein (CheckID,
										VisitorNo,
										purpose,
										departmentid,
										host,
										sec_officer,
										GateID)
								 VALUES ('" . $checkid . "',
								 	'". $VID . "',
									'". $_POST['purpose'] ."',
									'". $_POST['dept'] ."',
									'". $_POST['host'] ."',
									'". $_SESSION['UsersRealName'] ."',
									'". $_POST['gate'] . "')";
		$result = DB_query($sql);
		echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success!</h4>Visitor record Number ' . $VID . ' has been Checked In</div>';
	} //end if Delete supplier
}

if (isset($_POST['CheckOut'])) {

		$sql = "UPDATE visitor_timein SET check_out=1, remarks='".$_POST['remarks']."', time_out='".date('Y-m-d H:i:s')."', sec_officer_checkout='".$_SESSION['UsersRealName']."'
		       WHERE CheckID=".$_POST['checkid']."";
		$result = DB_query($sql);
		echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success!</h4>Visitor record Number ' . $VID . ' has been Checked Out</div>';
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
<center id="showweb" style="display:none;">
			  <div class="panel panel-default" style="width:30%">
		<div class="panel-heading">Passport Photo <div class="pull-right"><i style="cursor:pointer; color:#FF0000;" onClick=" return document.getElementById('showweb').style.display = 'none';" class="fa fa-close"></i></div></div>
			<div class="panel-body">
			<span id="my_camera" ></span><input name="vid" id="vid" value="<?php echo $VID; ?>" type="hidden" />
			<input type="button" class="btn btn-primary" onClick="take_snapshot()" style="width:130px;" value="Take Snapshot" >
		<script language="JavaScript">
		Webcam.set({
			width: 192,
			height: 192,
			image_format: 'jpeg',
			jpeg_quality: 90
		});
		function start_snapshot() {
		Webcam.attach( '#my_camera' );
		document.getElementById('showweb').style.display = 'block';
		}
	</script>
			
			</div>
		</div>
		</center>

              <div class="mailbox-read-message">
<table  style="border:none; width:100%" class="table">
				<tr><td>
				<table  style="border:none; width:100%" class="table">
				<tr><td colspan="4">
                <div style="font-weight:bold; font-size:20px;"><?php echo strtoupper($rows['v_name']); ?><div class="pull-right">
				<a href="Sec_VisitorUpdate.php?VID=<?php echo $VID; ?>" rel="facebox"><button id="submit" name="Edit" class="btn btn-default"><i class="fa fa-edit"></i> Edit</button></a>
				</div></div>
				
              </td></tr>
				<tr><td height="25">ID Number:</td><td><?php echo '<b>'.$rows['v_idno'].'</b>'; ?></td>
				<td width="150" height="25">Residence:</td><td><?php echo ' <b>'.strtoupper($rows['v_from']).'</b>'; ?></td></tr>
				<tr><td height="25">Phone Number: </td><td><?php echo '<b>'.strtoupper($rows['v_phoneno']).'</b>'; ?></td>
				<td height="25">Registration Date: </td><td><?php echo '<b>'.date("d, M Y",strtotime($rows['date'])).'</b>'; ?></td></tr>
				</table>
				</td>
				<td width="100">
				<?php 
				if($rows['imagepath'] !=""){
				echo '<div id="results" onClick="start_snapshot()"><img height="135px" width="120px" style="border-radius:8px 8px 8px 8px;" src="SECv2/'.$rows['imagepath'].'" /></div><input id="imagepath" name="imagepath" type="hidden" value = "'.$rows['imagepath'].'"/>';
				}else{
				echo '<div id="results" onClick="start_snapshot()"><img height="135px" width="120px" style="border-radius:8px 8px 8px 8px;" src="images/image.jpg" /></div>';
				}
				?>
				</td>
				</tr>
				</table>
				
				<div class="panel panel-default">
		<div class="panel-heading">Booking Information</div>
			<div class="panel-body">
			<form enctype="multipart/form-data" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
			<div class="form-group">
							  <div class="col-md-5">Host Name
							  <input id="key" autocomplete="off" pattern="(?!^\s+$)[^<>+]{1,40}" name="host" type="text" value = "" placeholder="Host Name" class="form-control input-md" required=""/>
							  <span id="result" style="z-index: 99;" class="result"><span class="loading"></span></span>
							  </div>
							   <div class="col-md-4">Deparment
							  	<select id="dept" name="dept" required="true" class="form-control">
								<?php
								echo '<option selected="selected" value="">--Please Select Department--</option>';
								$result=DB_query("SELECT departmentid, description FROM departments");
								while ($myrow = DB_fetch_array($result)) {
									echo '<option value="' . $myrow['departmentid'] . '">' . $myrow['description'] . '</option>';
								} //end while loop
								
								?>
								</select>
							  </div>
							   <div class="col-md-3">Gate
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
							</div>
					<div class="form-group">
							  <div class="col-md-12">Purpose
							  <textarea class="form-control input-md" name="purpose" cols="" rows=""></textarea>
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
						<th width="10%">' . _('Gate') . '</th>
						<th>' . _('Host') . '</th>
						<th>' . _('Department') . '</th>
						<th>' . _('Purpose') . '</th>
						<th>' . _('Time In') . '</th>
						<th><center>' . _('Time Out') . '</center></th>
					</tr>';
			$sql= "SELECT gates.description as gate, 
							visitor_timein.time_in, 
							visitor_timein.host,
							visitor_timein.purpose,
							visitor_timein.time_out, 
							visitor_timein.sec_officer, 
							visitor_timein.sec_officer_checkout, 
							visitor_timein.remarks, 
							visitor_timein.check_out, 
							departments.description as dept,
							CheckID
							FROM visitor_timein 
							INNER JOIN gates ON gates.GateID=visitor_timein.GateID 
							INNER JOIN departments ON departments.departmentid=visitor_timein.departmentid
							WHERE visitor_timein.VisitorNo='" . $VID . "' GROUP BY visitor_timein.CheckID, visitor_timein.GateID ORDER BY check_out ASC, visitor_timein.time_in DESC";
			$result = DB_query($sql);
					while($row=DB_fetch_array($result)){
				echo '<tr style="font-size:10px"><td ><center >'.$row['gate'].'</center></td>
					<td>' . $row['host'] . '</td>
					<td>' . $row['dept'] . '</td>
					<td>' . $row['purpose']. '</td>
					<td><center>' . date("d, M Y h:i A",strtotime($row['time_in'])) . '</center></td>
					<td><center>' . ($row['check_out']==0 ? '<button onclick="popshow(\'popDiv\','.$row['CheckID'].')" type="button" class="btn btn-danger btn-sm"> CheckOut</button>' : date("d, M Y h:i A",strtotime($row['time_out']))) . '</center></td>';
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
			  <a href="#" target="_blank"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>
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
		<script language="JavaScript">
		function take_snapshot() {
			// take snapshot and get image data
			Webcam.snap( function(data_uri) {
				// display results in page
			var ids = document.getElementById('vid').value;
			var imgpt = $("#imagepath").val();
				document.getElementById('results').innerHTML = 
					'Processing...';
					
				Webcam.upload( data_uri, 'Sec_updateimage.php?VID='+ids+'&IMGPT='+imgpt, function(code, text) {
					document.getElementById('results').innerHTML = 
					'<img style="border-radius:8px 8px 8px 8px;" height="135px" width="120px" src="SECv2/'+text+'"/>';
					document.getElementById('imagepath').value = text;
				} );	
			} );
			document.getElementById('showweb').style.display = 'none';
		}
	</script>

<style type="text/css">
#results {height:135px; width:120px; border-radius:8px 8px 8px 8px; background:#ccc;}
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