<?php
if(!is_numeric($_GET['VID'])){
ob_start();
die('<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                Invalid Request, Please try Again
              </div>');
}
$VID = $_GET['VID'];					
$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
$redirect = htmlspecialchars($protocol.''.$_SERVER['HTTP_HOST'].''.$_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8').'?Application=SEC2&Link=Materials';
if (isset($_GET['CheckOutID']) && $_GET['CheckOutID'] =="Staff") {
	$sql = "UPDATE staff_material_register SET booked_out=1,
										booked_out_time=NOW(),
										security_out='".$_SESSION['UsersRealName']."'
						WHERE id = '".$VID."'";

			$ErrMsg = _('The material could not be updated because');
			$DbgMsg = _('The SQL that was used to update the material but failed was');
			$result = DB_query($sql, $ErrMsg, $DbgMsg);
		$_SESSION['msg'] = 'Material record Number ' . $VID . ' has been Checked Out';
	?>
		<script>
        window.location.href = "<?php echo $redirect; ?>";
		</script>
	<?php
}elseif(isset($_GET['CheckOutID']) && $_GET['CheckOutID'] =="Visitor"){
	$sql = "UPDATE visitor_material_register SET booked_out=1,
										booked_out_time=NOW(),
										security_out='".$_SESSION['UsersRealName']."'
						WHERE id = '".$VID."'";

			$ErrMsg = _('The material could not be updated because');
			$DbgMsg = _('The SQL that was used to update the material but failed was');
			$result = DB_query($sql, $ErrMsg, $DbgMsg);
		$_SESSION['msgx'] = 'Material record Number ' . $VID . ' has been Checked Out';
		?>
		<script>
        window.location.href = "<?php echo $redirect; ?>";
		</script>
		<?php
}

						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
		if($_GET['Type']=='Staff'){
						$results = "SELECT *, b.description as gatename, a.description as name, a.purpose,CONCAT(emp_fname, ' ', emp_mname, ' ', emp_lname) AS v_name,id_number AS v_idno,emp_cont AS v_phoneno,appointment_name AS v_from,personnel AS date
										FROM staff_material_register a
										INNER JOIN gates b ON b.GateID = a.gate
										INNER JOIN employee c ON a.staffid = c.emp_id
									WHERE a.id ='" . $VID . "'";
				}else{
				$results = "SELECT *, b.description as gatename, a.description as name, a.purpose
										FROM visitor_material_register a
										INNER JOIN gates b ON b.GateID = a.gate
										INNER JOIN visitor_register c ON a.visitorid = c.VisitorNo
									WHERE a.id ='" . $VID . "'";
				}
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
						$rows = DB_fetch_array($welcome_viewed);
				
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
                <h4 style="font-weight:bold; font-size:20px;"><?php echo strtoupper($rows['name']); ?></h4>
              </td></tr>
			  <tr><td height="25">Owner Name:</td><td><?php echo '<b>'.strtoupper($rows['v_name']).'</b>'; ?></td>
				<td width="150" height="25">Gate:</td><td><?php echo ' <b>'.strtoupper($rows['gatename']).'</b>'; ?></td></tr>
				<tr><td height="25">ID Number:</td><td><?php echo '<b>'.$rows['v_idno'].'</b>'; ?></td>
				<td width="150" height="25"><?php echo ($_GET['Type']=="Staff" ? 'Appointment':'Residence')?>:</td><td><?php echo ' <b>'.strtoupper($rows['v_from']).'</b>'; ?></td></tr>
				<tr><td height="25">Phone Number: </td><td><?php echo '<b>'.strtoupper($rows['v_phoneno']).'</b>'; ?></td>
				<td height="25">Registration Date: </td><td><?php echo '<b>'.date("d, M Y h:i A",strtotime($rows['booked_in_time'])).'</b>'; ?></td></tr>
				<tr><td height="25">Destination:</td><td><?php echo '<b>'.strtoupper($rows['destination']).'</b>'; ?></td>
				<td width="150" height="25">Puporse:</td><td><?php echo ' <b>'.$rows['purpose'].'</b>'; ?></td></tr>
				</table>
				
              </div>

			
			 
            <!-- /.box-footer -->
            <div class="box-footer">
			<?php
			if($rows['booked_out'] ==0){
			?>
			  <a href="index.php?Application=SEC2&Link=MaterialRead&<?php echo 'VID='.$_GET['VID'].'&CheckOutID='.$_GET['Type'].''; ?>" onclick="return confirm('Are you sure you want to check out this material?');"><button type="button" class="btn btn-primary btn-sm"><i class="fa fa-sign-out" aria-hidden="true"></i> CheckOut</button></a>	   
			<?php } ?>
			<div class="pull-right"><a href="index.php?Application=SEC2&Link=Materials"><button type="button" class="btn btn-danger btn-sm"> Cancel</button></a>
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