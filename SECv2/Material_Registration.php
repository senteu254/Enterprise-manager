<script type="text/javascript" src="js/qsearch.js"></script>
	<!-- First, include the Webcam.js JavaScript Library -->
<script type="text/javascript" src="js/webcam.js"></script>
<?php
if(isset($_POST['SearchVisitor']) && $_POST['Type'] =="Visitor"){
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$results = "SELECT COUNT(VisitorNo), VisitorNo
									FROM visitor_register
									WHERE v_idno ='" . $_POST['idnumber'] . "'";
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
						$rows = DB_fetch_row($welcome_viewed);
				if($rows[0] >0){
				$VID = $rows[1];
				$Type = $_POST['Type'];
				}else{
					echo '<div class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>Visitor with ID Number '.$_POST['idnumber'].' is not registered in the system, please confirm the details and try again.</div>';
				}
	}	
if(isset($_POST['SearchVisitor']) && $_POST['Type'] =="Staff"){
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$results = "SELECT COUNT(emp_id), emp_id
									FROM employee
									WHERE emp_id ='" . $_POST['idnumber'] . "'";
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
						$rows = DB_fetch_row($welcome_viewed);
				if($rows[0] >0){
				$VID = $rows[1];
				$Type = $_POST['Type'];
				}else{
					echo '<div class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>Staff with Service Number '.$_POST['idnumber'].' is not registered in the system, please confirm the details and try again.</div>';
				}
	}		

if (isset($_POST['CheckIn'])) {
	$Cancel = 0;
if(isset($_POST['gate']) && $_POST['gate']==""){
$Cancel = 1;
echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>Please Select the gate you want to check in the visitor.</div>';
}

	if ($Cancel == 0) {
	if($_POST['Type']=='Staff'){
	$sql = "INSERT INTO staff_material_register (staffid,
										description,
										gate,
										destination,
										purpose,
										security_in)
								 VALUES ('" . $_POST['VID'] . "',
								 	'" . $_POST['itemname'] . "',
									'" . $_POST['gate'] . "',
									'" . $_POST['dest'] . "',
									'" . $_POST['purpose'] . "',
									'".$_SESSION['UsersRealName']."')";
	}else{
		$sql = "INSERT INTO visitor_material_register (visitorid,
										description,
										gate,
										destination,
										purpose,
										security_in)
								 VALUES ('" . $_POST['VID'] . "',
								 	'" . $_POST['itemname'] . "',
									'" . $_POST['gate'] . "',
									'" . $_POST['dest'] . "',
									'" . $_POST['purpose'] . "',
									'".$_SESSION['UsersRealName']."')";
		}
		$result = DB_query($sql);
		$VID = $_POST['VID'];
		$Type = $_POST['Type'];
echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success!</h4>Material has been Checked In successfully.</div>';

	} 
}

if (isset($_GET['CheckOutID']) && $_GET['CheckOutID'] !="") {
if($_GET['Type']=='Staff'){
	$sql = "UPDATE staff_material_register SET booked_out=1,
										booked_out_time=NOW(),
										security_out='".$_SESSION['UsersRealName']."'
						WHERE id = '".$_GET['CheckOutID']."'";
}else{
	$sql = "UPDATE visitor_material_register SET booked_out=1,
										booked_out_time=NOW(),
										security_out='".$_SESSION['UsersRealName']."'
						WHERE id = '".$_GET['CheckOutID']."'";
	}

			$ErrMsg = _('The material could not be updated because');
			$DbgMsg = _('The SQL that was used to update the material but failed was');
			$result = DB_query($sql, $ErrMsg, $DbgMsg);
		echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success!</h4>Material record Number ' . $_GET['CheckOutID'] . ' has been Checked Out</div>';
	$VID = $_GET['VID'];
	$Type = $_GET['Type'];
}
				
echo '<div id="loadingbackground">';
		echo '<div id="progressBar" ><br />
			 <i class="fa fa-spinner fa-pulse fa-4x fa-fw"></i><br>PROCESSING. PLEASE WAIT...
			</div>';
		echo '</div>';
?>

<form enctype="multipart/form-data" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
<?php if(isset($VID) && $VID !=""){ 
		if($Type =="Staff"){
		$results = "SELECT CONCAT(emp_fname, ' ', emp_mname, ' ', emp_lname) AS v_name,id_number AS v_idno,emp_cont AS v_phoneno,appointment_name AS v_from,personnel AS date,'' AS imagepath
									FROM employee
									WHERE emp_id ='" . $VID . "'";
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
						$rows = DB_fetch_array($welcome_viewed);
						$date =$rows['date'];
				}else{
		$results = "SELECT v_name,v_idno,v_phoneno,v_from,date,imagepath
									FROM visitor_register
									WHERE VisitorNo ='" . $VID . "'";
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
						$rows = DB_fetch_array($welcome_viewed);
						$date =date("d, M Y",strtotime($rows['date']));
				}
?>
			<input name="VID" type="hidden" value = "<?php echo $VID; ?>"/>
			<input name="Type" type="hidden" value = "<?php echo $Type; ?>"/>
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message">
			  <table  style="border:none; width:100%" class="table">
				<tr><td>
				<table  style="border:none; width:100%" class="table">
				<tr><td colspan="4">
                <h4 style="font-weight:bold; font-size:20px;"><?php echo strtoupper($rows['v_name']); ?></h4>
              </td></tr>
				<tr><td height="25">ID Number:</td><td><?php echo '<b>'.$rows['v_idno'].'</b>'; ?></td>
				<td width="150" height="25"><?php echo ($Type=="Staff" ? 'Appointment':'Residence')?>:</td><td><?php echo ' <b>'.strtoupper($rows['v_from']).'</b>'; ?></td></tr>
				<tr><td height="25">Phone Number: </td><td><?php echo '<b>'.strtoupper($rows['v_phoneno']).'</b>'; ?></td>
				<td height="25"><?php echo ($Type=="Staff" ? 'Personnel':'Registration Date')?>: </td><td><?php echo '<b>'.$date.'</b>'; ?></td></tr>
				</table>
				</td>
				<td width="100">
				<?php 
				if($rows['imagepath'] !=""){
				echo '<img height="135px" width="120px" style="border-radius:8px 8px 8px 8px;" src="SECv2/'.$rows['imagepath'].'" />';
				}else{
				echo '<img height="135px" width="120px" style="border-radius:8px 8px 8px 8px;" src="images/image.jpg" />';
				}
				?>
				</td>
				</tr>
				</table>
			  
	<div class="panel panel-default">
		<div class="panel-heading">Material Information</div>
			<div class="panel-body">
      <div class="col-md-12 col-md-offset-">
			<div class="form-group">
							  <div class="col-md-7">Item Description
							  <input autocomplete="off"  name="itemname" type="text" value = "" placeholder="Item Description" class="form-control input-md" required=""/>
							  </div>
							   <div class="col-md-5">Destination
							  	<input  autocomplete="off" name="dest" type="text" value = "" placeholder="Destination" class="form-control input-md" required=""/>
							  </div>
							   
							</div>
			
							<div class="form-group">
							  <div class="col-md-7">Purpose
							   <input  autocomplete="off" name="purpose" type="text" value = "" placeholder="Purpose" class="form-control input-md" required=""/>
							  </div>
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
			</div>
		</div>
		
		<?php
				echo '<table style="width:100%;" class="table table-hover table-striped"><tbody>';
				echo '<th>Status</th><th>Description</th><th>Gate</th><th>Time In</th><th>Action</th>';
				$ErrMsg = _('An error occurred in retrieving the records');
				$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
			if($Type == 'Staff'){
			$sqlx = " SELECT *, b.description as gatename, a.description as name, booked_out as status
										FROM staff_material_register a
										INNER JOIN gates b ON b.GateID = a.gate
										WHERE a.staffid=".$VID."
										ORDER BY booked_out ASC, booked_in_time DESC LIMIT 5";
			}else{
			$sqlx = " SELECT *, b.description as gatename, a.description as name, booked_out as status
										FROM visitor_material_register a
										INNER JOIN gates b ON b.GateID = a.gate
										WHERE a.visitorid=".$VID."
										ORDER BY booked_out ASC, booked_in_time DESC LIMIT 5";
				}
						$welcome_viewedx = DB_query($sqlx,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($welcome_viewedx);
					 if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewedx)){
                  echo '<tr>
                    <td width="35" class="mailbox-star">'.($row['status']==0 ? '<a style="color:green;" href="#"><i class="fa fa-star text-yellow">Active</i> </a>':'<a style="color:red;" href="#"><i class="fa fa-star-o text-yellow">Out</i> </a>').'</td>
                    <td class="mailbox-name"><a href="index.php?Application=SEC2&Link=MaterialRead&VID='.$row['id'].'">'.ucwords(strtolower($row['name'])).'</a></td>
                    <td class="mailbox-date">'.$row['gatename'].'</td>
					<td class="mailbox-date">'.date("d M Y h:i A",strtotime($row['booked_in_time'])).'</td>
					<td >'.($row['status']==0 ? '<a href="index.php?Application=SEC2&Link=NewMaterial&VID='.$VID.'&Type='.$Type.'&CheckOutID='.$row['id'].'" onclick="return confirm(\'Are you sure you want to check out this material?\');"><button type="button" class="btn btn-danger btn-sm"><i class="fa fa-sign-out" aria-hidden="true"></i> CheckOut</button></a>':''.date("d M Y h:i A",strtotime($row['booked_out_time'])).'').'</td>
                  </tr>';
				  }
				  }else{
				  echo '<tr><td class="alert-danger" colspan="7"><center><b style="color:#FF0000">No Material Registered</b></center></td></tr>';
				  }
			echo '<tbody></table>';
			?>
	
	<?php }else{ ?>
	
	<div class="form-group">
							  <div class="col-md-2">
							  <?php $arr = array('Visitor','Staff'); ?>
							  <select name="Type" class="form-control input-md">
							  <?php
							  foreach($arr as $val){
							  echo '<option '.($val ==$_POST['Type'] ? 'selected':'').' value="'.$val.'">'.$val.'</option>';
							  }
							  ?>
							  </select>
							  </div>
							  <div class="col-md-5">
							  <input autocomplete="off" pattern="(?!^\s+$)[^<>+]{1,40}" name="idnumber" type="number" value = "" placeholder="Enter Visitor ID No OR Staff Service No" class="form-control input-md" required=""/>
							  </div>
							   <div class="col-md-4">
							   <button id="submit" name="SearchVisitor" class="btn btn-primary"> <i class="icon fa fa-search"></i> Search</button>
							  </div>
							</div>
			
	<?php } ?>

</form>
            <!-- /.box-footer -->
          </div>
		  	<!-- Configure a few settings and attach camera -->
	
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
				
				document.getElementById('results').innerHTML = 
					'<h2>Processing:</h2>';
					
				Webcam.upload( data_uri, 'SECv2/saveimage.php', function(code, text) {
					document.getElementById('results').innerHTML = 
					'<img style="border-radius:8px 8px 0 0;" hight="160px" width="128px" src="SECv2/'+text+'"/>';
					document.getElementById('imagepath').value = text;
				} );	
			} );
			document.getElementById('showweb').style.display = 'none';
		}
	</script>

<style type="text/css">
#results {height:130px; width:130px; border-radius:8px 8px 0 0; background:#ccc; }

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