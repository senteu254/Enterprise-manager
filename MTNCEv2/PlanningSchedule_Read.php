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
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$results = "SELECT * FROM fixedassets z 
							INNER JOIN fixedassetlocations a on a.locationid = z.assetlocation
							INNER JOIN fixedassetcategories c on c.categoryid = z.assetcategoryid 
							INNER JOIN fixedassetplanning e ON z.assetid = e.assetid
							WHERE planid='" . $LID . "'";
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
						$rows = DB_fetch_array($welcome_viewed);


if (isset($_GET['Delete']) && $_GET['Delete']!="") {
$sql = "DELETE FROM fixedassettasks WHERE taskid='".$_GET['Delete']."'";
	$ErrMsg = _('The fixed asset task details cannot be inserted because');
	$Result=DB_query($sql,$ErrMsg);
	$_SESSION['msg'] = 'Task Deleted successfully';
}
		
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
					$foreman =$row['assignedby'];
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
			if($rowsq[1]>0){
			$sqlx= "SELECT *, TIME_TO_SEC(TIMEDIFF(enddate,startdate)) as diff FROM fixedassettasks
							WHERE requestid='" . $LID . "'";
			$resx = DB_query($sqlx);
			$rowx=DB_fetch_array($resx);
		echo '<table class="table" style="width:100%; ">';
		echo '<tr><th colspan="4">MAINTENANCE REPORT</th></tr>';
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
		<th colspan="3"><strong style="text-decoration:underline;">START DATE & TIME :</strong>'.date("d, M Y h:i A",strtotime($rowx['startdate'])). ' <br /><strong style="text-decoration:underline;">END DATE & TIME &nbsp;&nbsp;&nbsp;&nbsp;:</strong>'.date("d, M Y h:i A",strtotime($rowx['enddate'])).'</th>
		<th><strong style="text-decoration:underline;">TOTAL TIME TAKEN :</strong><br />'.secondsToWords($rowx['diff']).'</th>
		</tr>';
		if($rowx['completed']==2){
		echo '<tr><td colspan="4">';
		echo '<strong style="text-decoration:underline;">VERIFICATION BY FOREMAN</strong>';
			echo '<div class="form-group">
							  <div class="col-md-12"><strong>Remarks:</strong>
							  '.$rowx['foremanremarks'].'
							  </div>
							</div>';
			echo '<strong>Foreman:</strong>'.$rowx['foreman'].' <span class="pull-right">'.date('d/m/Y H:i:s',strtotime($rowx['foremanremarksdate'])).' </span>';
			echo '</td></tr>';
		
		if($rowx['user']=="" && $rowx['userauthorized']==1){
		echo '</table>';
		echo '<form enctype="multipart/form-data" action="" onsubmit="return document.getElementById(\'loadingbackground\').style.display = \'block\';" method="post" class="form-horizontal">';
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			echo '<strong style="text-decoration:underline;">HANDING OVER TO USER</strong>';
			echo '<div class="form-group">
							  <div class="col-md-3">Time:
							 <input required class="form-control input-md" name="timereceived" type="text" />
							  </div>
							  <div class="col-md-3">Machine Down Time:
							 <input required class="form-control input-md" name="downtime" type="text" />
							  </div>
							  <div class="col-md-6">Remarks:
							  <textarea class="form-control input-md" required name="remarks" cols="" rows=""></textarea>
							  </div>
							</div>';
							
			echo '<div class="form-group">
							  <div class="col-md-8">
								<button id="submit" name="SubmitRemarks" class="btn btn-primary">Receive Machine</button>
							  </div>
							</div>';
			echo '</form>';
		}elseif($rowx['user']=="" && $rowx['userauthorized']==0){
		echo '</table>';
		echo '<div id="div" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>
                Machine is not yet handed over to user. Please contact Planning Office to accept this machine.
              </div>';
		}else{
		echo '<tr><td colspan="4">';
		echo '<strong style="text-decoration:underline;">HANDING OVER TO USER</strong>';
			echo '<div class="form-group">
							  <div class="col-md-12">
							  <strong>Time:</strong>
							  '.$rowx['timereceived'].' <br />
							  <strong>MC Down Time:</strong>
							  '.$rowx['mcdowntime'].' <br />
							  <strong>Remarks:</strong>
							  '.$rowx['userremarks'].'
							  </div>
							</div>';
			echo '<strong>Foreman:</strong>'.$rowx['user'].' <span class="pull-right">'.date('d/m/Y H:i:s',strtotime($rowx['userremarksdate'])).' </span>';
			echo '</td></tr>';
		echo '</table>';
		echo ' <div class="box-footer">
			  <a href="PDFFixedAssetCompleteReportPortrait.php?id='.$LID.'" target="_blank"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>
            </div>';
		}
		}else{
		echo '</table>';
		echo '<div id="div" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>
                Machine awaiting verification by foreman. Please contact '.$foreman.' for assistance.
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