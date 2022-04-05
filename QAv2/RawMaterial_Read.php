<script type="text/javascript" src="js/qsearch.js"></script>
<script type="text/javascript" src="js/webcam.js"></script>
<?php
include('includes/phplot/phplot.php');
if(!is_numeric($_GET['ID'])){
ob_start();
die('<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                Invalid Request, Please try Again
              </div>');
}
$SelectedUser = $VID = $_GET['ID'];
if (isset($_POST['AddTech'])) {
$sql = "UPDATE qarawmatacceptance SET inspectors=CONCAT(inspectors,'','".$_POST['userx'].",') WHERE id = ". $SelectedUser . "";
$result = DB_query($sql);
$_SESSION['msg'] =_('Inspector Added Successfully');
}
if(isset($_GET['DeleteTech']) && $_GET['DeleteTech'] !=""){
$sql = "SELECT inspectors FROM qarawmatacceptance WHERE id = ". $SelectedUser . "";
$res = DB_query($sql);
$myr = DB_fetch_row($res);
$arr = explode(',',$myr[0]);
unset($arr[$_GET['DeleteTech']]);

	$sql = "UPDATE qarawmatacceptance SET inspectors='".implode(",", $arr)."' WHERE id = ". $SelectedUser . "";
	$res = DB_query($sql);
	$_SESSION['msg'] = _('Technician') . ' ' . $_GET['DeleteTech'] . _(' has been deleted from the list');
	unset($_GET['DeleteTech']);	
}			

if (isset($_POST['Submit'])) {
$sqlz = "SELECT *,a.id
			FROM qarawmatacceptance a
			INNER JOIN qa_approval_levels e ON e.type=6
			WHERE a.id='" . $SelectedUser . "' AND process_level =e.levelcheck AND 
			(CASE WHEN e.authoriser='QAT' THEN FIND_IN_SET('".$_SESSION['UserID']."',a.inspectors) ELSE e.authoriser='".$_SESSION['UserID']."' END)";
	$rez = DB_query($sqlz);
	$myz = DB_fetch_array($rez);
$Level = ($myz['process_level']+1);
$resl = DB_query("SELECT accept_msg,reject_msg FROM qa_approval_levels
						WHERE type = 6 AND levelcheck='".$myz['process_level']."'",$db);
	$myl = DB_fetch_row($resl);	

$sql = "UPDATE qarawmatacceptance SET process_level='" . $Level . "', rejected=0
					WHERE id = '". $SelectedUser . "'";

$result = DB_query($sql);
DB_query("DELETE FROM qarawmatacceptanceremarks WHERE approver='".$Level."' AND refid='". $SelectedUser ."'");
$sql = "INSERT INTO qarawmatacceptanceremarks (refid,remarks,approver,approvertitle,approvername)
					VALUE('". $SelectedUser ."','". $_POST['remarks'] ."','".$Level."','".$myl[0]."','". $_SESSION['UsersRealName'] ."')";

		$result = DB_query($sql);
		$_SESSION['msg'] =_('The selected Report has been forwaded for processing');
		
}
if (isset($_POST['Reject'])) {
$sqlz = "SELECT *,a.id
			FROM qarawmatacceptance a
			INNER JOIN qa_approval_levels e ON e.type=6
			WHERE a.id='" . $SelectedUser . "' AND process_level =e.levelcheck AND 
			(CASE WHEN e.authoriser='QAT' THEN FIND_IN_SET('".$_SESSION['UserID']."',a.inspectors) ELSE e.authoriser='".$_SESSION['UserID']."' END)";
	$rez = DB_query($sqlz);
	$myz = DB_fetch_array($rez);
$Level = ($myz['process_level']+1);
$resl = DB_query("SELECT accept_msg,reject_msg FROM qa_approval_levels
						WHERE type = 6 AND levelcheck='".$myz['process_level']."'",$db);
	$myl = DB_fetch_row($resl);	

$sql = "UPDATE qarawmatacceptance SET process_level=0, rejected=1
					WHERE id = '". $SelectedUser . "'";

		$result = DB_query($sql);
DB_query("DELETE FROM qarawmatacceptanceremarks WHERE approver='".$Level."' AND refid='". $SelectedUser ."'");
$sql = "INSERT INTO qarawmatacceptanceremarks (refid,remarks,approver,approvertitle,approvername)
					VALUE('". $SelectedUser ."','". $_POST['remarks'] ."','".$Level."','".$myl[1]."','". $_SESSION['UsersRealName'] ."')";

		$result = DB_query($sql);
		$_SESSION['msg'] =_('The selected Report has been Rejected back to the Technician');
		

}

function getExtension($str) 
    {
        $i = strrpos($str,".");
        if (!$i) { return ""; }
        $l = strlen($str) - $i;
        $ext = substr($str,$i+1,$l);
        return $ext;
    }
if (isset($_POST['Upload'])) {
$filename = stripslashes($_FILES['chemical'] ["name"]);
$extension = getExtension($filename);
$extension = strtolower($extension);
if($extension =='pdf'){
$fileupload =  'ChemicalAnalysis_'.$SelectedUser.'_'.time().'.'.$extension;
$filepath = 'QAv2/ChemicalAnalysisFiles/';
if (file_exists($filepath.$fileupload)) {
unlink($filepath.$fileupload);
}
$sql = "UPDATE qarawmatacceptance SET file_path='".$filepath.$fileupload."', user_uploaded='".$_SESSION['UserID'].'-'.$_SESSION['UsersRealName']."', upload_time='".date('Y-m-d H:i:s')."' WHERE id = '". $SelectedUser . "'";
$result = DB_query($sql);					
move_uploaded_file($_FILES['chemical']['tmp_name'], $filepath.$fileupload);
$_SESSION['msg'] =_('Chemical Analysis Report has been uploaded successfully');
}else{
$_SESSION['errmsg'] = 'Invalid File extention. Only .pdf extention is allowed.';
}
}


if(isset($_GET['New']) && $_GET['New']=='Yes'){
	$sql = "SELECT *,a.id
			FROM qarawmatacceptance a
			INNER JOIN qa_approval_levels e ON e.type=6
			WHERE a.id='" . $SelectedUser . "' AND process_level =e.levelcheck AND 
			(CASE WHEN e.authoriser='QAT' THEN FIND_IN_SET('".$_SESSION['UserID']."',a.inspectors) ELSE e.authoriser='".$_SESSION['UserID']."' END)";
	}else{
	$sql = "SELECT *,a.id
			FROM qarawmatacceptance a
			INNER JOIN qa_approval_levels e ON e.type=6
			WHERE a.id='" . $SelectedUser . "' AND process_level >e.levelcheck AND 
			(CASE WHEN e.authoriser='QAT' THEN FIND_IN_SET('".$_SESSION['UserID']."',a.inspectors) ELSE e.authoriser='".$_SESSION['UserID']."' END)";
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

echo '<table style="border:none; width:100%" class="table">
      <tr><td>';

	echo '<table style="border:none; width:100%" class="table">
			<tr>
				<td>' . _('Calibre') . ':</td>
				<td>' . $myrow['calibre'] . '</td>
			</tr>
			<tr>
				<td>' . _('Company') . ':</td>
				<td>' . $myrow['company'] . '</td>
			</tr>
			<tr>
				<td>' . _('Country') . ':</td>
				<td>' . $myrow['country'] . '</td>
			</tr>';

	echo '<tr>
			<td>' . _('Date') . '</td>
			<td>'.ConvertSQLDate($myrow['date']).'</td>
		</tr>
		<tr>
			<td>' . _('Technician') . '</td>
			<td>'.$myrow['technicianid'].'</td>
		</tr>';

echo '</table>';
	echo '</td>	<td style="width:50%">';
echo '<form enctype="multipart/form-data" onsubmit="return document.getElementById(\'loadingbackground\').style.display = \'block\';" method="post" class="form-horizontal">';	
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';		
echo '<table class="table" style="border:none; width:100%">';
if($myrow['process_level']==0 && $myrow['technicianid']==$_SESSION['UserID']){
echo '<tr><td colspan="3"><select name="userx" style="width:250px;">';
echo '<option value="">--Please Select Name of Inspectors--</option>';
$userx = DB_query("SELECT userid,realname FROM www_users",	$db);
while($myus = DB_fetch_array($userx)){
echo '<option value="'.$myus['userid'].'">'.$myus['userid'].' - '.$myus['realname'].'</option>';
}
echo '</select> <input name="AddTech" type="submit" class="btn btn-primary" value="Add" /></td></tr>';
}else{
echo '<tr><td colspan="3"><strong><center>Name of Inspectors</center></strong></td></tr>';
}
if($myrow['inspectors'] !=""){
$arr = explode(',',$myrow['inspectors']);
for($i = 0; $i<count($arr)-1; $i++){
$usern = DB_query("SELECT userid,realname FROM www_users WHERE userid='".$arr[$i]."'");
$myro = DB_fetch_array($usern);
echo '<tr>
		<td >'.$myro['userid'].'</td>
		<td >'.$myro['realname'].'</td>';
		if($myrow['process_level']==0 && $myrow['technicianid']==$_SESSION['UserID']){
		echo '<td width="50"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&Link=RawMatAccRead&amp;ID='.$SelectedUser.'&amp;New=Yes&amp;DeleteTech='.$i.'" onclick="return confirm(\'Are you absolutely sure you want to Delete this User?\')" title="Delete" style="color:red;"><i class="fa fa-trash"></i></a></td>';
		}
	echo '</tr>';
	}
	}

echo '</table>';
echo '</form>';
	echo '</td>
	</tr>
	<tr><td colspan="2">';

				
$sql = "SELECT *
		FROM qarawmatacceptancedata
		WHERE refno='" . $SelectedUser . "' ORDER BY num ASC";
	$results = DB_query($sql);
echo '<table class="table" style="width:100%">';
echo '<tr>
		<th>' .  _('Brass Lot No'). '</th>
		<th colspan="2"><center>' .  _('Strip Dimension'). '</center></th>
		<th colspan="2"><center>' .  _('Strip Dimension'). '</center></th>
		<th>' .  _('Hardness'). '</th>
		<th>' .  _('WT.'). '</th>
	</tr>
	<tr>
		<th></th>
		<th>' .  _('Thickness'). '</th>
		<th>' .  _('Width'). '</th>
		<th>' .  _('Outer'). '</th>
		<th>' .  _('Inner'). '</th>
		<th></th>
		<th></th>
	</tr>';
while($myro = DB_fetch_array($results)){

echo '<tr height="25">
		<td >'.$myro['lot'].'</td>
		<td >'.$myro['thickness'].'</td>
		<td >'.$myro['width'].'</td>
		<td >'.$myro['outerdim'].'</td>
		<td >'.$myro['innerdim'].'</td>
		<td >'.$myro['hardness'].'</td>
		<td >'.$myro['wt'].'</td>
	</tr>';
	}

echo '</table>';
		  
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

echo '</td></tr>';
if($myrow['file_path']!=""){
echo '<tr><td colspan="2">';
echo '<table class="table" style="width:100%">
<th>Document Name</th><th>User Uploaded</th><th>Upload Time</th><th>Action</th>
<tr><td>CHEMICAL ANALYSIS REPORT</td><td>'.$myrow['user_uploaded'].'</td><td>'.date('d M Y H:i:s A',strtotime($myrow['upload_time'])).'</td><td><a target="_plank" href="'.$myrow['file_path'].'"><i class="fa fa-download">Download</a></td></tr>
</table>';
echo '</td></tr>';
}else{
if($myrow['process_level']==0){
echo '<form enctype="multipart/form-data" action="" onsubmit="return document.getElementById(\'loadingbackground\').style.display = \'block\';" method="post" class="form-horizontal">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<tr><td colspan="2">
<strong>Upload Chemical Analysis Report :</strong> <input name="chemical" required class="btn btn-default" type="file" /> 
<br />
<button type="submit" name="Upload" onclick="return confirm(\'Are you sure you want to Upload this Report?\')" class="btn btn-primary"><i class="fa fa-upload"></i> Upload Report</button>
</td></tr>';
echo '</form>';
}
}
echo '</table>';
?>             
<!-- /.mailbox-controls -->
			
			<form enctype="multipart/form-data" action="index.php?Application=QA&Link=RawMatAccRead&ID=<?php echo $SelectedUser; ?>&New=No" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
			
					<?php
			$sql = "SELECT *
		FROM qarawmatacceptanceremarks
		WHERE refid='" . $VID . "' ORDER BY approver ASC";
	$results = DB_query($sql);
$TITLE = "";
echo '<table class="table" style="border:none; width:100%" >';
while($myro = DB_fetch_array($results)){
echo' <tr>
		<td align="center" width="70"><img class="image" src="images/image.jpg"  /></td>
		<td><div class="bubble"><span class="time">' .  strtoupper($myro['approvertitle'])  . '<br />From: <a href="#">'.$myro['approvername'].'</a></span> <br /> '.$myro['remarks'].'<br /><span class="time">'. calculate_time_span($myro['remarkdate']) .'</span>
				
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
			  <a href="<?php echo $RootPath . '/PDFRawMaterialAcceptanceReport.php?id='.$SelectedUser; ?>" target="_blank"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>
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