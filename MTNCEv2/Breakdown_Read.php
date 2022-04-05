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
ob_start();
$LID = $_GET['VID'];
$level=$_GET['LV'];
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$results = "SELECT *, z.doc_id, a.departmentid,FF.description as asset,e.description as department FROM irq_request z 
							INNER JOIN irq_maintenance a on a.maintenanceid = z.requestid 
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN www_users y ON z.initiator = y.userid
							LEFT JOIN  fixedassets FF ON FF.mcno=a.mcno
							WHERE draft=0 AND closed=1 AND
							z.requestid='" . $LID . "' 
							GROUP BY requestid";
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
						$rows = DB_fetch_array($welcome_viewed);
						$doc = $rows['doc_id'];
						$loccode = $rows['loccode'];
						$dept = $rows['departmentid'];
						$closed = $rows['closed'];
						$catid = $rows['assetcategoryid'];

if (isset($_POST['Submit'])) {
	if (!is_numeric(filter_number_format($_POST['FrequencyDays'])) OR filter_number_format($_POST['FrequencyDays']) < 0){
		prnMsg(_('The days before a task falls due is expected to be a postive'),'error');
	} else {
	$sqlsx= "SELECT COUNT(requestid) AS num, completed FROM fixedassettasks
							WHERE requestid='" . $LID . "'";
			$resux = DB_query($sqlsx);
			$rowsx=DB_fetch_row($resux);
	if($rowsx[0]==1 && (!isset($_POST['Edit']) or $_POST['Edit']=="")){
	$_SESSION['errmsg'] = 'Task has already been scheduled for this request';
	}else{
			
	$next_date= date('Y-m-d', strtotime(FormatDateForSQL($_POST['StartDate']). ' + '.filter_number_format($_POST['FrequencyDays']).' days'));
	
	if(isset($_POST['Edit']) && $_POST['Edit']!=""){
	$JobCard = explode('/',$_POST['JobCardNo']);
	$JobCardNo = $JobCard[0].'/'.$_POST['problemtype'].'.'.$catid.'/'.$JobCard[2];
	$sqlz = "UPDATE fixedassettasks SET userresponsible='" . $_POST['Responsible'] . "',
										taskdescription='" . $_POST['Description'] . "',
										jobcardno='" . $JobCardNo . "',
										frequencydays='" . filter_number_format($_POST['FrequencyDays']) . "',
										manager='" . $_POST['Manager'] . "',
										expstartdate='" . FormatDateForSQL($_POST['StartDate']) . "',
										expcompletedate='" . $next_date . "',
										accepted=0,
										assignedby='" . $_SESSION['UsersRealName'] . "'
						WHERE taskid='".$_POST['Edit']."'";
	$ErrMsg = _('The fixed asset task details cannot be inserted because');
	$Resultz=DB_query($sqlz,$ErrMsg);
	$_SESSION['msg'] = 'Task Updated successfully';
	}else{
	if(isset($_POST['problemtype']) && $_POST['problemtype']=='M'){
	$CardNo = sprintf('%03d', GetNextTransNo(112,$db));
	$JobCardNo = date('y').'/'.$_POST['problemtype'].'.'.$catid.'/'.$CardNo;
	}elseif(isset($_POST['problemtype']) && $_POST['problemtype']=='E'){
	$CardNo = sprintf('%03d', GetNextTransNo(113,$db));
	$JobCardNo = date('y').'/'.$_POST['problemtype'].'.'.$catid.'/'.$CardNo;
	}
		$sqlz="INSERT INTO fixedassettasks (assetid,
											jobcardno,
											problemtype,
											taskdescription,
											frequencydays,
											userresponsible,
											manager,
											expstartdate,
											expcompletedate,
											doc_id,
											requestid,
											assignedby)
						VALUES( '" . $_POST['AssetID'] . "',
								'" . $JobCardNo . "',
								'" . $_POST['problemtype'] . "',
								'" . $_POST['Description'] . "',
								'" . filter_number_format($_POST['FrequencyDays']) . "',
								'" . $_POST['Responsible'] . "',
								'" . $_POST['Manager'] . "',
								'" . FormatDateForSQL($_POST['StartDate']) . "',
								'" . $next_date . "',
								'5',
								'".$LID."',
								'" . $_SESSION['UsersRealName'] . "')";
		$ErrMsg = _('The fixed asset task details cannot be inserted because');
		$Resultz=DB_query($sqlz,$ErrMsg);
		$_SESSION['msg'] = 'Task Created successfully';
		}
		unset($_POST['AssetID']);
		unset($_POST['Description']);
		unset($_POST['FrequencyDays']);
		unset($_POST['Manager']);
		unset($_POST['Responsible']);
	}
	}

}

if (isset($_POST['SubmitRemarks'])) {
$sqlz = "UPDATE fixedassettasks SET foreman='" . $_SESSION['UserID'].' - '.$_SESSION['UsersRealName'] . "',
										foremanremarks='" . $_POST['remarks'] . "',
										foremanremarksdate='" . date('Y-m-d H:i:s') . "',
										completed=2
						WHERE requestid='".$LID."'";
	$ErrMsg = _('The fixed asset task details cannot be inserted because');
	$Resultz=DB_query($sqlz,$ErrMsg);
	$_SESSION['msg'] = 'Result verified successfully';
}

if (isset($_GET['Delete']) && $_GET['Delete']!="") {
$sql = "DELETE FROM fixedassettasks WHERE taskid='".$_GET['Delete']."'";
	$ErrMsg = _('The fixed asset task details cannot be inserted because');
	$Result=DB_query($sql,$ErrMsg);
	$_SESSION['msg'] = 'Task Deleted successfully';
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
		$message='Problem Observed';
		$date ='Breakdown Date';
		echo '<table class="table" style="width:100%; ">';
		echo '<tr>
		<td>Request No.</td>
		<td><strong>'.$rows['requestid'].'</strong></td>
		<td >'.$date.'</td>
		<td>'. date("d, M Y h:i A",strtotime($rows['breakdowndate'])) .'</td>
		</tr><tr>
		<td>Department</td>
		<td>'.strtoupper($rows['department']).'</td>
		<td>Section</td>
		<td align="left">'.$rows['section'].'</td>
		</tr><tr>
		<td>M/C Type</td>
		<td>'.$rows['mctype'].'</td>
		<td>M/C No.</td>
		<td>'.$rows['mcno'].'</td>
		</tr><tr>
		<td>Asset to Maintain</td>
		<td colspan="3">'.$rows['mcno'] .'-'.  $rows['asset']. '</td>
		</tr><tr>
		<td>Requesting Officer</td>
		<td>'.$rows['requesting_officer'].'</td>
		<td>Urgency</td>
		<td>'.$rows['urgency'].'</td>
		</tr><tr>
		<td>'.$message.'</td>
		<td colspan="3">'.$rows['problem'].'</td>
		</tr>';
		$asset =$rows['assetid'];
		echo '</table>';
			?>
              </div>
				<?php
				$sqls= "SELECT COUNT(requestid) AS num, completed FROM fixedassettasks
							WHERE requestid='" . $LID . "'";
			$resu = DB_query($sqls);
			$rowsx=DB_fetch_row($resu);
			if($rowsx[0]!=0){
				echo '<table style="width:100%;" class="table table-striped"><tbody>';
				
			$sqls= "SELECT *,b.realname as managers,c.realname as responsible,accepted,rejectmessage,rejecteddate FROM fixedassettasks a,www_users b,www_users c
							WHERE a.manager=b.userid AND a.userresponsible=c.userid AND requestid='" . $LID . "'";
			$results = DB_query($sqls);
					$roww=DB_fetch_array($results);
					if($roww['accepted']!=1){
					echo '<span class="pull-right"><a href="index.php?Application=FA&Link=BreakdownRead&VID='.$LID.'&amp;Edit='.$roww['taskid'].'"><i style="color:blue" class="fa fa-edit"></i> </a> || <a href="index.php?Application=FA&Link=BreakdownRead&VID='.$LID.'&amp;Delete='.$roww['taskid'].'"><i style="color:red" class="fa fa-trash"></i></a></span>';
					}
				echo '<tr><td colspan="3">Job Card No. : <strong>' . $roww['jobcardno'] . '</strong></td></tr>';
				echo '<tr>
					<td><strong style="text-decoration:underline;">Responsible Tech:</strong><br />' . $roww['managers'] . '</td>
					<td><strong style="text-decoration:underline;">Assistant Tech:</strong><br />' . $roww['responsible'] . '</strong></td>
					<td><strong style="text-decoration:underline;">Planned By:</strong><br />' . $roww['assignedby']. '</strong></td>
					</tr><tr>
					<td><strong style="text-decoration:underline;">Expected Start Date:</strong><br />' . date("d, M Y",strtotime($roww['expstartdate'])) . '</strong></td>
					<td><strong style="text-decoration:underline;">Expected Completion Date:</strong><br />' . date("d, M Y",strtotime($roww['expcompletedate'])) . '</strong></td>
					<td><strong style="text-decoration:underline;">No of Days:</strong><br />' . $roww['frequencydays'] . '</strong></td>
					</tr><tr>
					<td colspan="3"><strong style="text-decoration:underline;">Description:</strong><br />' . $roww['taskdescription']. '</strong></td>
					';
			echo '</tr>';
			
			echo '<tbody></table>';
			if($roww['accepted']==2){
			echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert</h4>
                This Task was rejected by '.$roww['managers'].' on '.date('d, M Y H:i A',strtotime($roww['rejecteddate'])).'<br />
				Reason: '.$roww['rejectmessage'].'
              </div>';
			}
			if($rowsx[1]!=0){
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
		if($rowsx[1]==1){
			echo '<form enctype="multipart/form-data" action="index.php?Application=FA&Link=BreakdownRead&VID='.$LID.'" onsubmit="return document.getElementById(\'loadingbackground\').style.display = \'block\';" method="post" class="form-horizontal">';
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			echo '<strong style="text-decoration:underline;">VERIFICATION BY FOREMAN</strong>';
			echo '<div class="form-group">
							  <div class="col-md-12">Remarks:
							  <textarea class="form-control input-md" required name="remarks" cols="" rows=""></textarea>
							  </div>
							</div>';
							
			echo '<div class="form-group">
							  <div class="col-md-8">
								<button id="submit" name="SubmitRemarks" class="btn btn-primary">Submit Results</button>
							  </div>
							</div>';
			echo '</form>';
			}else{
			echo '<strong style="text-decoration:underline;">VERIFICATION BY FOREMAN</strong>';
			echo '<div class="form-group">
							  <div class="col-md-12"><strong>Remarks:</strong>
							  '.$rowx['foremanremarks'].'
							  </div>
							</div>';
			echo '<strong>Foreman:</strong>'.$rowx['foreman'].' <span class="pull-right">'.date('d/m/Y H:i:s',strtotime($rowx['foremanremarksdate'])).' </span>';
			}
			}
			}
			?>
				
			<?php
			
			if($rowsx[0]==0 OR (isset($_GET['Edit']) && $_GET['Edit']!="")){
			if(isset($_GET['Edit']) && $_GET['Edit'] !=""){
			$sqls= "SELECT * FROM fixedassettasks
							WHERE taskid='" . $_GET['Edit'] . "'";
			$results = DB_query($sqls);
			$myro=DB_fetch_array($results);
			}
			?>
			<div class="panel panel-default">
		<div class="panel-heading">Task Register</div>
			<div class="panel-body">
			<form enctype="multipart/form-data" action="<?php echo 'index.php?Application=FA&Link=BreakdownRead&VID='.$LID.''; ?>" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
			<?php echo '<input type="hidden" name="AssetID" value="' . $asset . '" />'; ?>
			<?php echo '<input type="hidden" name="JobCardNo" value="' . (isset($myro['jobcardno'])? $myro['jobcardno'] : '') . '" />'; ?>
			<?php echo '<input type="hidden" name="Edit" value="' . ((isset($_GET['Edit']) && $_GET['Edit'] !="")? $_GET['Edit']:'') . '" />'; ?>
			<div class="form-group">
							  <div class="col-md-6">Expected Start Date
							  <input autocomplete="off" type="text" class="date" alt="d/m/Y" name="StartDate" value="<?php echo (isset($myro['expstartdate'])? date("d/m/Y",strtotime($myro['expstartdate'])) : date('d/m/Y')); ?>" id="form-control" required="" />
							  </div>
							  <div class="col-md-6">Days Task Due
							  <input autocomplete="off" pattern="(?!^\s+$)[^<>+]{1,40}" name="FrequencyDays" type="text" value = "<?php echo (isset($myro['frequencydays'])? $myro['frequencydays'] : ''); ?>" placeholder="Days Task Due" class="form-control input-md" required=""/>
							  <span id="result" style="z-index: 99;" class="result"><span class="loading"></span></span>
							  </div>
							  </div>
							  <div class="form-group">
							  <div class="col-md-6">Responsible Technician
							   <?php 
							   $sql = "SELECT userid,realname FROM www_users WHERE 1";
									$result = DB_query($sql);
							   ?>
							  	<select name="Manager" required="true" class="form-control">
								<?php
								echo '<option selected="selected" value="">--Please Select Responsible Technician--</option>';
								while ($myrow = DB_fetch_array($result)) {
								echo '<option value="'. $myrow[0] .'" '.((isset($myro['manager']) && $myro['manager']==$myrow[0])? 'selected' : '').'>'. $myrow[0] .'-'. $myrow[1] .'</option>';
								}
								?>
								</select>
							  </div>
							   <div class="col-md-6">Assistant Technician
							  	<?php 
							   $sql = "SELECT userid,realname FROM www_users WHERE department=10";
									$result = DB_query($sql);
							   ?>
							  	<select name="Responsible" required="true" class="form-control">
								<?php
								echo '<option selected="selected" value="">--Please Select Assistant Technician--</option>';
								while ($myrow = DB_fetch_array($result)) {
								echo '<option value="'. $myrow[0] .'" '.((isset($myro['userresponsible']) && $myro['userresponsible']==$myrow[0])? 'selected' : '').'>'. $myrow[0] .'-'. $myrow[1] .'</option>';
								}
								?>
								</select>
							  </div>
							   
							</div>
					<div class="form-group">
							  <div class="col-md-6">Problem Type
							  <select name="problemtype" required class="form-control">
							  <option value="">--Please Select Problem Type--</option>
							  <?php
							  $arr =array('M'=>'MECHANICAL','E'=>'ELECTRICAL');
							  foreach($arr as $key=>$val){
								echo '<option value="'. $key .'" '.((isset($myro['problemtype']) && $myro['problemtype']==$key)? 'selected' : '').'>'. $key .'-'. $val .'</option>';
								}
							  ?>
							  </select>
							  </div>
							  <div class="col-md-6">Task Description
							  <textarea class="form-control input-md" name="Description" cols="" rows=""><?php echo (isset($myro['taskdescription'])? $myro['taskdescription'] : ''); ?></textarea>
							  </div>
							</div>
							
					<div class="form-group">
							  <div class="col-md-8">
								<button id="submit" name="Submit" class="btn btn-primary">Submit Task</button>
							  </div>
							</div>
			</form>
			</div>
		</div>
			<?php } ?>
			
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