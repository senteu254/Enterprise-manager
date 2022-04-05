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

$LID = $_GET['VID'];
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$results = "SELECT * FROM fixedassets z 
							INNER JOIN fixedassetlocations a on a.locationid = z.assetlocation
							INNER JOIN fixedassetcategories c on c.categoryid = z.assetcategoryid 
							INNER JOIN fixedassetplanning e ON z.assetid = e.assetid
							WHERE planid='" . $LID . "'";
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
						$rows = DB_fetch_array($welcome_viewed);

if (isset($_POST['SubmitComplete'])) {
$sql = "UPDATE fixedassettasks SET causes='" . $_POST['Causes'] . "',
										repairsused='" . $_POST['Repairs'] . "',
										toolsused='" . $_POST['Tools'] . "',
										sparesused='" . $_POST['Spares'] . "',
										estcost='" . $_POST['Cost'] . "',
										suggestions='" . $_POST['Report'] . "',
										startdate='" . FormatDateForSQL($_POST['StartDate']). ' '. $_POST['timestart'] . "',
										enddate='" . FormatDateForSQL($_POST['EndDate']). ' '. $_POST['timeend'] . "',
										completedby='" . $_SESSION['UsersRealName'] . "',
										completed=1
						WHERE requestid='".$LID."'";
	$ErrMsg = _('The fixed asset task details cannot be inserted because');
	$Result=DB_query($sql,$ErrMsg);
	$_SESSION['msg'] = 'Task Completed successfully';
}
if (isset($_POST['SubmitAccept'])) {
$sql = "UPDATE fixedassettasks SET accepted=1 WHERE requestid='".$LID."'";
	$ErrMsg = _('The fixed asset task details cannot be inserted because');
	$Result=DB_query($sql,$ErrMsg);
	$_SESSION['msg'] = 'Task Accepted successfully';
}
if (isset($_POST['SubmitReject1']) && $_POST['SubmitReject1']=="Rejected") {
$sql = "UPDATE fixedassettasks SET accepted=2, rejectmessage='".$_POST['reason']."',rejecteddate='".date('Y:m:d H:i:s')."' WHERE requestid='".$LID."'";
	$ErrMsg = _('The fixed asset task details cannot be inserted because');
	$Result=DB_query($sql,$ErrMsg);
	$_SESSION['msg'] = 'Task Rejected successfully';
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
<div class="mailbox-read-message">
 
				
		<?php		
		echo '<table class="table" style="width:100%; ">';
		echo '<tr>
		<td>Asset Code.</td>
		<td><strong>'.$rows['mcno'].'</strong></td>
		</tr><tr>
		<td >Asset Name</td>
		<td>'. strtoupper($rows['longdescription']) .'</td>
		</tr><tr>
		<td>Location</td>
		<td>'.strtoupper($rows['locationdescription']).'</td>
		</tr><tr>
		<td>Category</td>
		<td>'.$rows['categorydescription'].'</td>
		</tr><tr>
		<td>Purchased Date</td>
		<td>'.  $rows['datepurchased']. '</td>
		</tr><tr>
		<td>Planning Officer</td>
		<td>'.$rows['planningofficer'].'</td>
		</tr><tr>';
		$asset =$rows['assetid'];
		echo '</table>';
			?>
              </div>
				<?php
				echo '<table style="width:100%;" class="table table-hover table-striped"><tbody>';
				
			$sqls= "SELECT *,b.realname as managers,c.realname as responsible FROM fixedassettasks a,www_users b,www_users c
							WHERE a.manager=b.userid AND a.userresponsible=c.userid AND requestid='" . $LID . "'";
			$results = DB_query($sqls);
					while($row=DB_fetch_array($results)){
					$managers=$row['managers'];
				echo '<tr><td colspan="3">Job Card No. : <strong>' . $row['jobcardno'] . '</strong></td></tr>';
				echo '<tr>
					<td><strong style="text-decoration:underline;">Responsible Tech:</strong><br />' . $row['managers'] . '</td>
					<td><strong style="text-decoration:underline;">Assistant Tech:</strong><br />' . $row['responsible'] . '</strong></td>
					<td><strong style="text-decoration:underline;">Planned By:</strong><br />' . $row['assignedby']. '</strong></td>
					</tr><tr>
					<td><strong style="text-decoration:underline;">Expected Start Date:</strong><br />' . date("d, M Y",strtotime($row['expstartdate'])) . '</strong></td>
					<td><strong style="text-decoration:underline;">Expected Completion Date:</strong><br />' . date("d, M Y",strtotime($row['expcompletedate'])) . '</strong></td>
					<td><strong style="text-decoration:underline;">No of Days:</strong><br />' . $row['frequencydays'] . '</strong></td>
					</tr><tr>
					<td colspan="3"><strong style="text-decoration:underline;">Description:</strong><br />' . $row['taskdescription']. '</strong></td>
					';
			echo '</tr>';
			}
			echo '<tbody></table>';
			?>
			<?php
			$sqls= "SELECT COUNT(requestid) AS num, completed,accepted,rejectmessage,manager,rejecteddate FROM fixedassettasks
							WHERE requestid='" . $LID . "'";
			$resu = DB_query($sqls);
			$rowsq=DB_fetch_row($resu);
			if($rowsq[1]==1){
			
			$sqlx= "SELECT *, TIME_TO_SEC(TIMEDIFF(enddate,startdate)) as diff FROM fixedassettasks
							WHERE requestid='" . $LID . "'";
			$resx = DB_query($sqlx);
			$rowx=DB_fetch_array($resx);
		echo '<table class="table" style="width:100%; ">';
		echo '<tr><th colspan="3">MAINTENANCE REPORT</th></tr>';
		echo '<tr>
		<td colspan="2" style="width:50%"><strong style="text-decoration:underline;">CAUSES:</strong><br />'.$rowx['causes'].'</td>
		<td colspan="2" ><strong style="text-decoration:underline;">REPAIRS UNDERTAKEN:</strong><br />'.$rowx['repairsused'].'</td>
		</tr>
		<tr>
		<td colspan="2"><strong style="text-decoration:underline;">SPACIAL TOOLS FOR INTERVENTION/MODIFICATION:</strong><br />'.$rowx['toolsused'].'</td>
		<td colspan="2"><strong style="text-decoration:underline;">SPARES USED:</strong><br />'.$rowx['sparesused'].'</td>
		</tr>
		<tr>
		<td colspan="2"><strong style="text-decoration:underline;">COST:</strong><br />'.$rowx['estcost'].'</td>
		<td colspan="2"><strong style="text-decoration:underline;">REPORT/SUGGESTIONS:</strong><br />'.$rowx['suggestions'].'</td>
		</tr>';
		echo '<tr>
		<th colspan="3"><strong style="text-decoration:underline;">START DATE & TIME :</strong>'.date("d, M Y h:i A",strtotime($rowx['startdate'])). '<br /><strong style="text-decoration:underline;">END DATE & TIME &nbsp;&nbsp;&nbsp;&nbsp;:</strong>'.date("d, M Y h:i A",strtotime($rowx['enddate'])).'</th>
		<th><strong style="text-decoration:underline;">TOTAL TIME TAKEN:</strong><br />'.secondsToWords($rowx['diff']).'</th>
		</tr>';
		echo '</table>';
			
			}else{
			if($rowsq[2]==0 && $rowsq[4]==$_SESSION['UserID']){
			?>
			<form enctype="multipart/form-data" name="myForm" id="myForm" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
			<input type="hidden" id="reason" name="reason" value="" />
			<input type="hidden" id="SubmitReject" name="SubmitReject1" value="" />'; ?>
			<center><button id="submit" name="SubmitReject" onclick="myFunction();" class="btn btn-danger"><i class="fa fa-times"></i> Reject</button>&nbsp;&nbsp;&nbsp;
			<button id="submit" name="SubmitAccept" onclick="return confirm('Are you sure you want to Accept this Task?');" class="btn btn-success"><i class="fa fa-check"></i> Accept</button></center>
			</form>
			<?php }elseif($rowsq[2]==1){ ?>
			<div class="panel panel-default">
		<div class="panel-heading">Maintenance Report</div>
			<div class="panel-body">
			<form enctype="multipart/form-data" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
			
		<div class="form-group">
		 			<div class="col-md-4">Start Date
							  <input autocomplete="off" type="text" class="date" alt="d/m/Y" name="StartDate" value="<?php echo date('d/m/Y'); ?>" id="form-control" required="" />
							  </div>
							  <div class="col-md-2">Time
							  <?php
							 echo '<select id="form-control" name="timestart">';
							 for($hours=0; $hours<24; $hours++) // the interval for hours is '1'
							for($mins=0; $mins<60; $mins+=30) // the interval for mins is '30'
								echo '<option>'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
											   .str_pad($mins,2,'0',STR_PAD_LEFT).'</option>';
							echo '</select>';
					  			 ?>
							  </div>
							  <div class="col-md-4">End Date
							   <input autocomplete="off" type="text" class="date" alt="d/m/Y" name="EndDate" value="<?php echo date('d/m/Y'); ?>" id="form-control" required="" />
							   </div>
							   <div class="col-md-2">Time
							  <?php
							 echo '<select id="form-control" name="timeend">';
							 for($hours=0; $hours<24; $hours++) // the interval for hours is '1'
							for($mins=0; $mins<60; $mins+=30) // the interval for mins is '30'
								echo '<option>'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
											   .str_pad($mins,2,'0',STR_PAD_LEFT).'</option>';
							echo '</select>';
					  			 ?>
							  </div>
					</div>
					<div class="form-group">
							   <div class="col-md-6">Causes/Reason for repair/Service
							  	 <textarea class="form-control input-md" name="Causes" cols="" rows=""></textarea>
							  </div>
							   <div class="col-md-6">Repairs Undertaken
							    <textarea class="form-control input-md" name="Repairs" cols="" rows=""></textarea>
							  </div>
							</div>
					<div class="form-group">
							  <div class="col-md-6">Special Tools for Intervention/Modification
							  <textarea class="form-control input-md" name="Tools" cols="" rows=""></textarea>
							  </div>
							  <div class="col-md-6">Spares Used
							  <textarea class="form-control input-md" name="Spares" cols="" rows=""></textarea>
							  </div>
							</div>
					<div class="form-group">
							  <div class="col-md-6">Cost
							  <textarea class="form-control input-md" name="Cost" cols="" rows=""></textarea>
							  </div>
							  <div class="col-md-6">Report/Suggestions
							  <textarea class="form-control input-md" name="Report" cols="" rows=""></textarea>
							  </div>
							</div>
							
					<div class="form-group">
							  <div class="col-md-8">
								<button id="submit" name="SubmitComplete" onclick="return confirm('Are you sure you want to submit and complete this Task?');" class="btn btn-primary">Complete Task</button>
							  </div>
							</div>
							</form>
				</div>
		</div>
			<?php 
			}elseif($rowsq[2]==2){
			echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert</h4>
                This Task was rejected by '.$managers.' on '.date('d, M Y H:i A',strtotime($rowsq[5])).'<br />
				Reason: '.$rowsq[3].'
              </div>';
			}
			}
			 ?>
			
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
	function myFunction() {
  var person = prompt("Please Give Reason For your Decline:", "");
  if(person==""){
	  document.getElementById("SubmitReject").value='Not';
	  }else{
	  document.getElementById("reason").value=person;
	  document.getElementById("SubmitReject").value='Rejected';
	  }
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