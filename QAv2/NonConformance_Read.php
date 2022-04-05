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

if (isset($_POST['submit'])) {
	$sqlz = "SELECT process_level
		FROM qanonconformingproducts a
		INNER JOIN qa_approval_levels c ON c.type=2
		WHERE a.id='" . $VID . "' AND process_level =c.levelcheck AND 
		(CASE WHEN c.authoriser='MS' THEN a.mc_setter='".$_SESSION['UserID']."' 
			  WHEN c.authoriser='QAT' THEN a.technicianid='".$_SESSION['UserID']."' 
			ELSE c.authoriser='".$_SESSION['UserID']."' END)";
	$resultz = DB_query($sqlz);
	$myz = DB_fetch_array($resultz);
$Level = ($myz['process_level']+1);
$resl = DB_query("SELECT accept_msg,reject_msg FROM qa_approval_levels
						WHERE type = 2 AND levelcheck=".$myz['process_level']."",	$db);
$myl = DB_fetch_row($resl);	

$error = 0;
if(isset($_POST['user']) && $_POST['user'] !=""){
if(isset($_POST['setter']) && $_POST['setter'] !=""){
$sql = "UPDATE qanonconformingproducts SET process_level=1, mc_setter='". $_POST['setter'] ."', rejected=0
					WHERE id = '". $SelectedUser . "'";
	$result = DB_query($sql);
}else{
$error = 1;
$_SESSION['errmsg'] = _('Machine Setter can not be empty');
}
}else{
$sql = "UPDATE qanonconformingproducts SET process_level='" . $Level . "', rejected=0
					WHERE id = '". $SelectedUser . "'";
	$result = DB_query($sql);
}
if($error == 0){
if(isset($_POST['action'])){
$_POST['action'] = $_POST['action'];
}else{
$_POST['action'] ="";
}
DB_query("DELETE FROM qanonconformingremarks WHERE approver='".$Level."' AND refid='". $SelectedUser ."'");
$sql = "INSERT INTO qanonconformingremarks (refid,remarks,approver,approvertitle,approvername,action)
					VALUE('". $SelectedUser ."','". $_POST['remarks'] ."','".$Level."','".$myl[0]."','". $_SESSION['UsersRealName'] ."','".$_POST['action']."')";

		$result = DB_query($sql);
		$_SESSION['msg'] = _('The selected Record has been forwaded for processing');
		
	
}

}

if (isset($_POST['Reject'])) {
	$sqlz = "SELECT process_level
		FROM qanonconformingproducts a
		INNER JOIN qa_approval_levels c ON c.type=2
		WHERE a.id='" . $VID . "' AND process_level =c.levelcheck AND 
		(CASE WHEN c.authoriser='MS' THEN a.mc_setter='".$_SESSION['UserID']."' 
			  WHEN c.authoriser='QAT' THEN a.technicianid='".$_SESSION['UserID']."' 
			ELSE c.authoriser='".$_SESSION['UserID']."' END)";
	$resultz = DB_query($sqlz);
	$myz = DB_fetch_array($resultz);
$Level = ($myz['process_level']+1);
$resl = DB_query("SELECT accept_msg,reject_msg FROM qa_approval_levels
						WHERE type = 2 AND levelcheck=".$myz['process_level']."",	$db);
$myl = DB_fetch_row($resl);	

$sql = "UPDATE qanonconformingproducts SET process_level=0, rejected=1
					WHERE id = '". $SelectedUser . "'";

		$result = DB_query($sql);
DB_query("DELETE FROM qanonconformingremarks WHERE approver='".$Level."' AND refid='". $SelectedUser ."'");
$sql = "INSERT INTO qanonconformingremarks (refid,remarks,approver,approvertitle,approvername)
					VALUE('". $SelectedUser ."','". $_POST['remarks'] ."','".$Level."','".$myl[1]."','". $_SESSION['UsersRealName'] ."')";

		$result = DB_query($sql);
		$_SESSION['msg'] =_('The selected Report has been Rejected back to the Technician');
		
}

if(isset($_GET['New']) && $_GET['New']=='Yes'){
	$sql = "SELECT *,a.id
		FROM qanonconformingproducts a
		INNER JOIN qa_approval_levels c ON c.type=2
		WHERE a.id='" . $VID . "' AND process_level =c.levelcheck AND 
		(CASE WHEN c.authoriser='MS' THEN a.mc_setter='".$_SESSION['UserID']."' 
			  WHEN c.authoriser='QAT' THEN a.technicianid='".$_SESSION['UserID']."' 
			ELSE c.authoriser='".$_SESSION['UserID']."' END)";
	}else{
	$sql = "SELECT *,a.id
		FROM qanonconformingproducts a
		INNER JOIN qa_approval_levels c ON c.type=2
		WHERE a.id='" . $VID . "' AND process_level >c.levelcheck AND 
		(CASE WHEN c.authoriser='MS' THEN a.mc_setter='".$_SESSION['UserID']."' 
			  WHEN c.authoriser='QAT' THEN a.technicianid='".$_SESSION['UserID']."' 
			ELSE c.authoriser='".$_SESSION['UserID']."' END)";
	}

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);
				
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
			  
			  

	$_POST['id'] = $myrow['id'];
	$_POST['machine'] = $myrow['machine'];
	$_POST['date'] = ConvertSQLDate($myrow['date']);
	$_POST['lot'] = $myrow['lot'];
	$_POST['calibre']	= $myrow['calibre'];
	

echo '<div class="mailbox-read-message"><table class="table" style="border:none; width:100%">
      <tr><td>';

	echo '<table class="table" style="border:none; width:100%">
			<tr >
				<td width="100px">' . _('Record ID') . ':</td>
				<td width="200px"><strong>' . $_POST['id'] . '</strong></td>
			</tr>';

	echo '<tr><td>' . _('Machine.') . '</td>
			<td>'.$_POST['machine'].'</td>
		</tr><tr height="30px">
		<td>' . _('Date') . '</td>
			<td>'.$_POST['date'].'</td>
		</tr><tr>
		<td>' . _('Calibre') . '</td>
			<td>'.$_POST['calibre'].'</td>
		</tr>
		<tr>
		<td>' .  _('Lot No') . '</td>
		<td>'.$_POST['lot'] .'</td>';

echo '</td>
	</tr>
	</table>';
include('includes/Level_Tracking_QA.php');
	echo '</td>	<td>';
	
	
	echo '</td><td>
	<td valign="top" class="status">
	<div style="background:url(css/status1.png) left top no-repeat; height:150px; width:150px;">
	<div style="padding-left:35px; padding-top:2px; font-weight:bold; font-size:12px;">'.$label.' &nbsp;QAT</div>
	<div style="padding-left:35px; padding-top:4px; font-weight:bold; font-size:12px;">'.$label2.' &nbsp;M/C Setter</div>
	<div style="padding-left:35px; padding-top:4px; font-weight:bold; font-size:12px;">'.$label3.' &nbsp;CAPO</div>
	<div style="padding-left:35px; padding-top:4px; font-weight:bold; font-size:12px;">'.$label4.' &nbsp;PM</div>
	<div style="padding-left:35px; padding-top:4px; font-weight:bold; font-size:12px;">'.$label5.' &nbsp;CQAO</div>
	<div style="padding-left:35px; padding-top:4px; font-weight:bold; font-size:12px;">'.$label6.' &nbsp;QARDM</div>
	
	
	</div>
	</td>
	</tr>';	
echo '</table>';
?>             
<!-- /.mailbox-controls -->
			
			<form enctype="multipart/form-data" action="index.php?Application=QA&amp;Link=Non-ConformanceRead&amp;ID=<?php echo $VID; ?>&amp;New=No" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
			
					<?php
			$sql = "SELECT *
		FROM qanonconformingremarks
		WHERE refid='" . $VID . "' ORDER BY approver ASC";
	$results = DB_query($sql);
$TITLE = "";
echo '<table class="table" style="border:none; width:100%" >';
while($myro = DB_fetch_array($results)){
echo' <tr>
		<td align="center" width="70"><img class="image" src="images/image.jpg"  /></td>
		<td><div class="bubble"><span class="time">' .  $myro['approvertitle']  . '<br />From: <a href="#">'.$myro['approvername'].'</a></span> <br /> '.$myro['remarks'].'
				'.($myro['action'] !="" ? '<br />ACTION : '.$myro['action'] : '').'
				<br /><span class="time">'. calculate_time_span($myro['remarkdate']) .'</span>
				
				</div>
			</td>
			  </tr>';
}

if($myrow['process_level']==$myrow['levelcheck']){
if($myrow['process_level']==1){
echo '<tr>
			<td colspan="2"><input class="user" type="checkbox" name="user" /> Forward to another M/C Setter?
			<div class="setterfield" style="display:none"><select name="setter">';
     $SQL = "SELECT userid,
						realname
					FROM www_users
					WHERE blocked='0'";
				 $result=DB_query($SQL);
  echo '<option selected="selected" value="">--Select Machine Setter--</option>';
  while ($myrow4=DB_fetch_array($result)){
	if (isset($_POST['setter']) AND  $myrow4['userid']==$_POST['setter']){
		echo '<option selected="selected" value="'. $myrow4['userid'] . '">' . $myrow4['realname'] . '</option>';
	} else {
		echo '<option value="'. $myrow4['userid'] . '">' . $myrow4['realname'] . '</option>';
	}
}
  echo '</select></div>
			</td>
			</tr>';
			}
echo '<tr><td width="70">Remarks</td><td  colspan="2"><textarea name="remarks" style="width:100%" rows="2"></textarea></td></tr>';
if($myrow['process_level']==4){
echo '<tr>
			<td></td><td><input name="action" type="radio" value="FULLY COMPLETED" /> ACTION FULLY COMPLETED </td>
			</tr>
			<tr>
			<td></td><td><input name="action" type="radio" value="PARTIALLY COMPLETED" /> ACTION PARTIALLY COMPLETED </td>
			</tr>
			<tr>
			<td></td><td> <input name="action" type="radio" value="NO ACTION TAKEN" /> NO ACTION TAKEN</td>
			</tr>';
}
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
			<button type="submit" name="submit" onclick="return confirm('Are you sure you want to Forward this Request?')" class="btn btn-success"><i class="fa fa-share"></i> Forward</button>
			<?php } ?>
			</div>
			  <a href="<?php echo $RootPath . '/PDFConformanceReportPortrait.php?id=' . $VID; ?>" target="_blank"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>
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