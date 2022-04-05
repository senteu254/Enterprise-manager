<script type="text/javascript" src="js/qsearch.js"></script>
	<!-- First, include the Webcam.js JavaScript Library -->
<script type="text/javascript" src="js/webcam.js"></script>
<?php
if(isset($_POST['SearchVisitor'])){
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$results = "SELECT COUNT(VisitorNo), VisitorNo
									FROM visitor_register
									WHERE v_idno ='" . $_POST['idnumber'] . "'";
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
						$rows = DB_fetch_row($welcome_viewed);
				if($rows[0] >0){
				$_SESSION['msg'] =  'Visitor with ID Number '.$_POST['idnumber'].' is registered in the system, please fill in the booking details below and Check in.';
				$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
				$redirect = htmlspecialchars($protocol.''.$_SERVER['HTTP_HOST'].''.$_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8').'?Application=SEC2&Link=VisitorRead&VID='.$rows[1].'';
						?>
						<script>
						window.location.href = "<?php echo $redirect; ?>";
						</script>
					<?php
				}else{
					$IDNumber = $_POST['idnumber'];	
				}
	}			

if (isset($_POST['CheckIn'])) {
	$Cancel = 0;
if(isset($_POST['gate']) && $_POST['gate']==""){
$Cancel = 1;
prnMsg(_('Please Select the gate you want to check in the visitor.'),'warn');
echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>Please Select the gate you want to check in the visitor.</div>';
}

	if ($Cancel == 0) {
	$VisitorID = GetNextTransNo(52, $db);
	$dt = new DateTime();
    $SQL_date = $dt->format('Y-m-d');	
			$sql = "INSERT INTO visitor_register (VisitorNo,
										v_name,
										v_idno,
										v_phoneno,
										v_from,
										date,
										imagepath)
								 VALUES ('" . $VisitorID . "',
								 	'" . $_POST['vname'] . "',
									'" . $_POST['idno'] . "',
									'" . $_POST['phoneno'] . "',
									'" . $_POST['vfrom'] . "',
									'" . $SQL_date . "',
									'". $_POST['imagepath'] ."')";

			$ErrMsg = _('The visitor') . ' ' . $_POST['name'] . ' ' . _('could not be added because');
			$DbgMsg = _('The SQL that was used to insert the visitor but failed was');

			$result = DB_query($sql, $ErrMsg, $DbgMsg);
	
	$checkid = GetNextTransNo(53, $db);
		$sql = "INSERT INTO visitor_timein (CheckID,
										VisitorNo,
										purpose,
										departmentid,
										host,
										sec_officer,
										GateID)
								 VALUES ('" . $checkid . "',
								 	'". $VisitorID . "',
									'". $_POST['purpose'] ."',
									'". $_POST['dept'] ."',
									'". $_POST['host'] ."',
									'". $_SESSION['UsersRealName'] ."',
									'". $_POST['gate'] . "')";
		$result = DB_query($sql);
		$_SESSION['msg'] =  'Visitor record Number ' . $VisitorID . ' has been Checked In';
				
$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
$redirect = htmlspecialchars($protocol.''.$_SERVER['HTTP_HOST'].''.$_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8').'?Application=SEC2&Link=VisitorRead&VID='.$VisitorID.'';

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
<?php if(isset($IDNumber) && $IDNumber !=""){ 
echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>Visitor with ID Number '.$IDNumber.' is not currently registered in the system, please fill in the form below and Check in.</div>';
?>
              <center id="showweb" style="display:none;">
			  <div class="panel panel-default" style="width:30%">
		<div class="panel-heading">Passport Photo <div class="pull-right"><i style="cursor:pointer; color:#FF0000;" onClick=" return document.getElementById('showweb').style.display = 'none';" class="fa fa-close"></i></div></div>
			<div class="panel-body">
			<span id="my_camera" ></span>
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
		//var image = document.getElementById('imagepath').value;
		var image = $("#imagepath").val();
		if(image !=""){
		$.ajax({
		type: "POST",
		data: ({key: image, Delete: "Delete"}),
		url:"SECv2/saveimage.php",
		success: function(response) {
		$(".resulta").slideDown().html(response); 
		}
		})
		}
		}
	</script>
			
			</div>
		</div>
		</center>
			  
			  <div class="mailbox-read-info">
                <input name="idno" type="hidden" value = "<?php echo $IDNumber; ?>"/>
              </div>
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message">
				<div class="panel panel-default">
		<div class="panel-heading">Booking Information</div>
			<div class="panel-body">
			<div class = "row">
      <div class="col-md-9 col-md-offset-">
			<div class="form-group">
							  <div class="col-md-7">Visitor Full Name
							  <input autocomplete="off" pattern="(?!^\s+$)[^<>+]{1,40}" name="vname" type="text" value = "" placeholder="Visitor Full Name" class="form-control input-md" required=""/>
							  </div>
							   <div class="col-md-5">Phone Number
							  	<input  autocomplete="off" name="phoneno" type="text" value = "" placeholder="Phone Number" class="form-control input-md" required=""/>
							  </div>
							   
							</div>
			
			<div class="form-group">
							  <div class="col-md-7">Host Name
							  <input id="key" autocomplete="off" pattern="(?!^\s+$)[^<>+]{1,40}" name="host" type="text" value = "" placeholder="Host Name" class="form-control input-md" required=""/>
							  <span id="result" style="z-index: 99;" class="result"><span class="loading"></span></span>
							  </div>
							   <div class="col-md-5">Deparment
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
							   
							</div>
							<div class="form-group">
							  <div class="col-md-7">Place of Residence
							   <input  autocomplete="off" pattern="(?!^\s+$)[^<>+]{1,40}" name="vfrom" type="text" value = "" placeholder="Place of Residence" class="form-control input-md" required=""/>
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
			<div class="col-md-3 col-md-offset-">
			<div class="panel panel-default">
			<div class="panel-body">

			<div id="results"></div><input id="imagepath" name="imagepath" type="hidden" value = ""/>
			<input type="button" class="btn btn-primary" onClick="start_snapshot()" style="width:130px; border-radius:0;" value="Take Snapshot" >
			
			</div>
		</div>
			</div>
			</div>				
					<div class="form-group">
							  <div class="col-md-12">Purpose
							  <textarea class="form-control input-md" name="purpose" cols="" rows=""></textarea>
							  </div>
							</div>
							
					<div class="form-group">
							  <div class="col-md-12">
								<button id="submit" name="CheckIn" class="btn btn-primary">Check In</button>
								<div class="pull-right">
								<a href=""><input class="btn btn-warning" name="Cancel" value="Cancel" type="button" /></a>
								</div>
							  </div>
							</div>
	
			</div>
		</div>
              </div>
	
	<?php }else{ ?>
	
	<div class="form-group">
							  <div class="col-md-5">
							  <input id="idnumbers" autocomplete="off" pattern="(?!^\s+$)[^<>+]{1,40}" name="idnumber" type="number" value = "" placeholder="Enter Visitor ID Number" class="form-control input-md" required=""/>
							  <span id="resultvisitor" style="z-index: 99;" class="resultvisitor"><span class="loading"></span></span>
							  </div>
							   <div class="col-md-4">
							   <button id="submit" name="SearchVisitor" class="btn btn-primary">Search Visitor</button>
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
			
$("#idnumbers").keyup( function(event){
	var key = $("#idnumbers").val();

	if( key != 0){
		$.ajax({
		type: "GET",
		data: ({key: key, Type: 'Visitor'}),
		url:"Sec_quicksearchVehicle.php",
		success: function(response) {
		$(".resultvisitor").slideDown().html(response); 
		}
		})
		
		}else{
		
		$(".resultvisitor").slideUp();
		$(".resultvisitor").val("");
		}
 }) 
			
		</script>
		<script language="JavaScript">
		function take_snapshot() {
			// take snapshot and get image data
			Webcam.snap( function(data_uri) {
				// display results in page
				
				document.getElementById('results').innerHTML = 
					'Processing...';
					
				Webcam.upload( data_uri, 'SECv2/saveimage.php', function(code, text) {
					document.getElementById('results').innerHTML = 
					'<img style="border-radius:8px 8px 0 0;" height="129px" width="128px" src="SECv2/'+text+'"/>';
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