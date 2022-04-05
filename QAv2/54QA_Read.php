<script type="text/javascript" src="js/qsearch.js"></script>
<script type="text/javascript" src="js/webcam.js"></script>
<?php
if(!is_numeric($_GET['ID'])){
ob_start();
die('<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                Invalid Request, Please try Again
              </div>');
}

$SelectedUser = $VID = $_GET['ID'];
					
if (isset($_POST['Submit'])) {
$sqlz = "SELECT process_level FROM qadailyreport a
										INNER JOIN qa_approval_levels c ON c.type=3
										WHERE a.id='".$SelectedUser."' AND process_level =c.levelcheck AND 
										(CASE WHEN c.authoriser='QAT' THEN a.technicianid='".$_SESSION['UserID']."' ELSE c.authoriser='".$_SESSION['UserID']."' END)";

	$rez = DB_query($sqlz);
	$myz = DB_fetch_array($rez);
$Level = ($myz['process_level']+1);
$resl = DB_query("SELECT accept_msg,reject_msg FROM qa_approval_levels
						WHERE type = 3 AND levelcheck=".$myz['process_level']."",	$db);
$myl = DB_fetch_row($resl);	

$sql = "UPDATE qadailyreport SET process_level='" . $Level . "', rejected=0
					WHERE id = '". $SelectedUser . "'";
$result = DB_query($sql);
DB_query("DELETE FROM qadailyreportremarks WHERE approver='".$Level."' AND refid='". $SelectedUser ."'");
$sql = "INSERT INTO qadailyreportremarks (refid,remarks,approver,approvertitle,approvername,action)
					VALUE('". $SelectedUser ."','". $_POST['remarks'] ."','".$Level."','".$myl[0]."','". $_SESSION['UsersRealName'] ."','')";

		$result = DB_query($sql);
		$_SESSION['msg'] =_('The selected Report has been forwaded for processing');	

}
if (isset($_POST['Reject'])) {
$sqlz = "SELECT process_level FROM qadailyreport a
										INNER JOIN qa_approval_levels c ON c.type=3
										WHERE a.id='".$SelectedUser."' AND process_level =c.levelcheck AND 
										(CASE WHEN c.authoriser='QAT' THEN a.technicianid='".$_SESSION['UserID']."' ELSE c.authoriser='".$_SESSION['UserID']."' END)";

	$rez = DB_query($sqlz);
	$myz = DB_fetch_array($rez);
$Level = ($myz['process_level']+1);
$resl = DB_query("SELECT accept_msg,reject_msg FROM qa_approval_levels
						WHERE type = 3 AND levelcheck=".$myz['process_level']."",	$db);
	$myl = DB_fetch_row($resl);	
	
$sql = "UPDATE qadailyreport SET process_level=0, rejected=1
					WHERE id = '". $SelectedUser . "'";
$result = DB_query($sql);
DB_query("DELETE FROM qadailyreportremarks WHERE approver='".$Level."' AND refid='". $SelectedUser ."'");
$sql = "INSERT INTO qadailyreportremarks (refid,remarks,approver,approvertitle,approvername,action)
					VALUE('". $SelectedUser ."','". $_POST['remarks'] ."','".$Level."','".$myl[1]."','". $_SESSION['UsersRealName'] ."','')";

		$result = DB_query($sql);
		$_SESSION['msg'] =_('The selected Report has been Rejected back to the Technician');
}

if(isset($_GET['New']) && $_GET['New']=='Yes'){
	$sqlx = "SELECT *,a.id FROM qadailyreport a
										INNER JOIN qa_approval_levels c ON c.type=3
										WHERE a.id='".$SelectedUser."' AND process_level =c.levelcheck AND 
										(CASE WHEN c.authoriser='QAT' THEN (a.technicianid='".$_SESSION['UserID']."' OR a.technicianid='0') ELSE c.authoriser='".$_SESSION['UserID']."' END)";
	}else{
	$sqlx = "SELECT *,a.id FROM qadailyreport a
										INNER JOIN qa_approval_levels c ON c.type=3
										WHERE a.id='".$SelectedUser."' AND process_level >c.levelcheck AND 
										(CASE WHEN c.authoriser='QAT' THEN (a.technicianid='".$_SESSION['UserID']."' OR a.technicianid='0') ELSE c.authoriser='".$_SESSION['UserID']."' END)";
	}

	$resultx = DB_query($sqlx);
	$myrow = DB_fetch_array($resultx);
				
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
			  
			  

echo '<table style="border:none; width:100%" class="table">
      <tr><td colspan="4">';

	echo '<table style="border:none; width:100%" class="table">
			<tr height="30px">
				<th width="100px">' . _('Calibre Tested') . '</th>
				<th width="100px">' . _('Cart. Lot No.') . '</th>
				<th width="100px">' . _('Det. Charge') . '</th>
				<th width="100px">' . _('Powder Lot No.') . '</th>
				<th width="100px">' . _('Bullet Mass') . '</th>
			</tr>';

	echo '<tr height="30px">
			<td>'.$myrow['calibre'].'</td>
			<td>'.$myrow['cartlotno'].'</td>
			<td>'.$myrow['detcharge'].'</td>
			<td>'.$myrow['powderlotno'].'</td>
			<td>'.$myrow['bulletmass'].'</td>
		</tr>';
echo '</td>
	</tr>
	</table>';
	echo '</td>	<td>';	
	echo '<tr><th colspan="4">EPVAT TEST</th></tr>';
	if($myrow['calibre']=='7.62x51mm Ball'){
	$velocity = '833.5 &plusmn; 15 m/s';
	$pressure = '< 3600 Bars';
	$pressure1 = '> 550 Bars';
	$accuracy = '< 150 mm';
	$force = '&ge; 27.21 Kgf';
	$rate = '650 to 750 Rds/Min';
	$at = 'At 3" 100% Misfire<br />At 16" 100% Fire';
	$v = 'V25m';
	}elseif($myrow['calibre']=='5.56x45mm Ball'){
	$velocity = '915 &plusmn; 12 m/s';
	$pressure = '< 3800 Bars';
	$pressure1 = '> 880 Bars';
	$accuracy = '< 221 mm';
	$force = '&ge; 20.4 Kgf';
	$rate = '700 to 1000 Rds/Min';
	$at = 'At 3" 100% Misfire<br />At 14" 100% Fire';
	$v = 'V25m';
	}elseif($myrow['calibre']=='9x19mm Para'){
	$velocity = '370 &plusmn; 10 m/s';
	$pressure = '< 2300 Bars';
	$pressure1 = 'No Measurement'; //< 880 Bars
	$accuracy = '< 200 mm';
	$force = '&ge; 20.4 Kgf';
	$rate = '550 to 650 Rds/Min';
	$at = 'At 3" 100% Misfire<br />At 12" 100% Fire';
	$v = 'V16m';
	}elseif($myrow['calibre']=='7.62x51mm Tracer'){
	$velocity = '833 &plusmn; 15 m/s';
	$pressure = '< 3600 Bars';
	$pressure1 = '> 550 Bars';
	$accuracy = '< 300 mm';
	$force = '&ge; 27.21 Kgf';
	$rate = '650 to 750 Rds/Min';
	$at = 'At 3" 100% Misfire<br />At 16" 100% Fire';
	$v = 'V25m';
	}
	echo '<tr><td colspan="2"></td><th>Required Velocity</th><th>Obtained Velocity</th></tr>';
	echo '<tr><td>Mean Velocity</td><td>'.$v.'</td><td><center>'.$velocity.'</center></td><td><center>'.$myrow['velocity'].'</center></td></tr>';
	echo '<tr><td colspan="2"></td><th>Required Pressure</th><th>Obtained Pressure</th></tr>';
	echo '<tr><td>Mean Mouth Pressure</td><td></td><td><center>'.$pressure.'</center></td><td><center>'.$myrow['mouthpressure'].'</center></td></tr>';
	echo '<tr><td>Mean Port Pressure</td><td></td><td><center>'.$pressure1.'</center></td><td><center>'.$myrow['portpressure'].'</center></td></tr>';
	echo '<tr><th colspan="4">Mean Accuracy (H + L)</th></tr>';
	echo '<tr><td colspan="2"></td><th>Required Accuracy</th><th>Obtained Accuracy</th></tr>';
	echo '<tr><td>Bullet Production</td><td>Lot No : '.$myrow['bulletproductionlot'].'</td><td><center>'.$accuracy.'</center></td><td><center>'.$myrow['bulletproduction'].'</center></td></tr>';
	echo '<tr><td colspan="2"></td><th>Required Accuracy</th><th>Obtained Accuracy</th></tr>';
	echo '<tr><td>Loading (PC 530)</td><td>Lot No : '.$myrow['loadinglot'].'</td><td><center>'.$accuracy.'</center></td><td><center>'.$myrow['loading'].'</center></td></tr>';
	echo '<tr><td colspan="2"></td><th>Required Force</th><th>Obtained Force</th></tr>';
	echo '<tr><td>Bullet Extraction Force</td><td>Lot No : '.$myrow['bextractionlot'].'</td><td><center>'.$force.'</center></td><td><center>'.$myrow['bextraction'].'</center></td></tr>';
	echo '<tr><td colspan="2"></td><th>Required Standard</th><th>Remarks</th></tr>';
	echo '<tr><td>Mercurous Nitrate Test</td><td></td><td><center>No Cracks(0)</center></td><td><center>'.$myrow['mercurous'].'</center></td></tr>';
	echo '<tr><td>Water Tightness Test</td><td></td><td><center>&le; 3 Out of 20 Leaks</center></td><td><center>'.$myrow['watertightness'].'</center></td></tr>';
	echo '<tr><td colspan="2"></td><th>Required Rate/Minute</th><th>Remarks</th></tr>';
	echo '<tr><td>Rate of Fire</td><td>Lot No : '.$myrow['ratefirelot'].'</td><td><center>'.$rate.'</center></td><td><center>'.$myrow['ratefire'].'</center></td></tr>';
	echo '<tr><td colspan="2"></td><th>Required Rate/Minute</th><th>Remarks</th></tr>';
	echo '<tr><td>Primer Sensitivity</td><td>Lot No : '.$myrow['sensitivitylot'].'</td><td><center>'.$at.'</center></td><td><center>'.$myrow['sensitivityat3'].'</br />'.$myrow['sensitivity'].'</center></td></tr>';

echo '</table>';
?>             
<!-- /.mailbox-controls -->
			
			<form enctype="multipart/form-data" action="index.php?Application=QA&amp;Link=54QARead&ID=<?php echo $VID; ?>&amp;New=No" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
			
					<?php
			$sql = "SELECT *
		FROM qadailyreportremarks
		WHERE refid='" . $VID . "' ORDER BY approver ASC";
	$results = DB_query($sql);
$TITLE = "";
echo '<table class="table" style="border:none; width:100%" >';
while($myro = DB_fetch_array($results)){
echo' <tr>
		<td align="center" width="70"><img class="image" src="images/image.jpg"  /></td>
		<td><div class="bubble"><span class="time">' .  strtoupper($myro['approvertitle'])  . '<br />From: <a href="#">'.$myro['approvername'].'</a></span> <br /> '.$myro['remarks'].'
				'.($myro['action'] !="" ? '<br />ACTION : '.$myro['action'] : '').'
				<br /><span class="time">'. calculate_time_span($myro['remarkdate']) .'</span>
				
				</div>
			</td>
			  </tr>';
}

if($myrow['process_level']==$myrow['levelcheck']){
echo '<tr><td width="70">Remarks</td><td  colspan="2"><textarea name="remarks" style="width:100%" rows="2"></textarea></td></tr>';
}
echo '</table>';
			?>
			 
            <!-- /.box-footer -->
            <div class="box-footer">
			 <div class="pull-right">
			 <?php 
			 if($myrow['process_level']==$myrow['levelcheck']){
			 if($myrow['process_level']!=0){
			 ?>
			 <button type="submit" name="Reject" onclick="return confirm('Are you sure you want to Reject this Request?')" class="btn btn-danger"><i class="fa fa-reply"></i> Reject</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 <?php } ?>
			<button type="submit" name="Submit" onclick="return confirm('Are you sure you want to Forward this Request?')" class="btn btn-success"><i class="fa fa-share"></i> Forward</button>
			<?php } ?>
			</div>
			  <a href="<?php echo $RootPath . '/PDFQADailyReport.php?id=' . $SelectedUser; ?>" target="_blank"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>
            </div>
            <!-- /.box-footer -->
          </div>
	</form>		  
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
		$(".user").click(function() {
    if($(this).is(":checked")) {
		 $(".setterfield").show(200);
    } else {
		$(".setterfield").hide(200);
    }
});
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