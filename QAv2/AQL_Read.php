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
$ErrMsg = _('An error occurred in retrieving the records');
$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');

if (isset($_POST['submit'])) {
$resx = "SELECT process_level
			FROM qasamples a
			INNER JOIN qa_approval_levels c ON c.type=1
			WHERE a.sampleid='".$VID."' AND a.process_level=c.levelcheck AND 
			(CASE WHEN c.authoriser='QAT' THEN a.createdby='".$_SESSION['UserID']."' ELSE c.authoriser='".$_SESSION['UserID']."' END)";
$welc = DB_query($resx,$ErrMsg,$DbgMsg);
$rowx = DB_fetch_array($welc);
$Level = ($rowx['process_level']+1);
$resl = DB_query("SELECT accept_msg,reject_msg FROM qa_approval_levels
						WHERE type = 1 AND levelcheck=".$rowx['process_level']."",	$db);
$myl = DB_fetch_row($resl);

	$sql = "UPDATE qasamples SET process_level='" . $Level . "', rejected=0
					WHERE sampleid = '". $SelectedUser . "'";
		$result = DB_query($sql);
//DB_query("DELETE FROM qrsampleremarks WHERE approver_level='".$Level."' AND sampleid='". $SelectedUser ."'");
$sql = "INSERT INTO qrsampleremarks (sampleid,remark,approver_level,approver_title,approver_name)
					VALUE('". $SelectedUser ."','". $_POST['remarks'] ."','".$Level."','".$myl[0]."','". $_SESSION['UsersRealName'] ."')";
		$result = DB_query($sql);
		$_SESSION['msg'] = '' . _('Success: AQL No. '). $VID . ' ' . _('has been forwarded for authoritation'). '';	
}

if (isset($_POST['Reject'])) {
$resx = "SELECT process_level
			FROM qasamples a
			INNER JOIN qa_approval_levels c ON c.type=1
			WHERE a.sampleid='".$VID."' AND a.process_level=c.levelcheck AND 
			(CASE WHEN c.authoriser='QAT' THEN a.createdby='".$_SESSION['UserID']."' ELSE c.authoriser='".$_SESSION['UserID']."' END)";
$welc = DB_query($resx,$ErrMsg,$DbgMsg);
$rowx = DB_fetch_array($welc);
$Level = ($rowx['process_level']+1);
$resl = DB_query("SELECT accept_msg,reject_msg FROM qa_approval_levels
						WHERE type = 1 AND levelcheck=".$rowx['process_level']."",	$db);
$myl = DB_fetch_row($resl);

$sql = "UPDATE qasamples SET process_level=0, rejected=1, level_rejected='".$Level."'
					WHERE sampleid = '". $SelectedUser . "'";
		$result = DB_query($sql);
//DB_query("DELETE FROM qrsampleremarks WHERE approver_level='".$Level."' AND sampleid='". $SelectedUser ."'");
$sql = "INSERT INTO qrsampleremarks (sampleid,remark,approver_level,approver_title,approver_name)
					VALUE('". $SelectedUser ."','". $_POST['remarks'] ."','".$Level."','".$myl[1]."','". $_SESSION['UsersRealName'] ."')";
		$result = DB_query($sql);
		$_SESSION['msg'] =_('The selected Report has been Rejected back to the Technician');	
}

if(isset($_GET['New']) && $_GET['New']=='Yes'){
						$results = "SELECT *,a.sampleid
									FROM qasamples a
									LEFT OUTER JOIN stockmaster on stockmaster.stockid=a.prodspeckey
									INNER JOIN qa_approval_levels c ON c.type=1
									WHERE a.sampleid='".$VID."' AND a.process_level=c.levelcheck AND 
									(CASE WHEN c.authoriser='QAT' THEN (a.createdby='".$_SESSION['UserID']."' OR 1) ELSE c.authoriser='".$_SESSION['UserID']."' END)";
					}else{
					$results = "SELECT *,a.sampleid
									FROM qasamples a
									LEFT OUTER JOIN stockmaster on stockmaster.stockid=a.prodspeckey
									INNER JOIN qa_approval_levels c ON c.type=1
									WHERE a.sampleid='".$VID."' AND a.process_level>c.levelcheck AND 
									(CASE WHEN c.authoriser='QAT' THEN (a.createdby='".$_SESSION['UserID']."' OR 1) ELSE c.authoriser='".$_SESSION['UserID']."' END)";
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
                <div style="font-weight:bold; font-size:20px;"><?php echo strtoupper($rows['prodspeckey'] . ' - ' . $rows['description'] ); ?></div>
				
              </td></tr>
				<tr><td height="25">Lot / Serial:</td><td><?php echo '<b>'.$rows['lotkey'].'</b>'; ?></td>
				<td width="150" height="25">Batch Size	:</td><td><?php echo ' <b>'.$rows['Batch'].'</b>'; ?></td></tr>
				<tr><td height="25">Sample Size	: </td><td><?php echo '<b>'.$rows['SampleSize'].'</b>'; ?></td>
				<td height="25">Sample Date: </td><td><?php echo '<b>'.date("d, M Y",strtotime($rows['sampledate'])).'</b>'; ?></td></tr>
				</table>
				
				<div class="panel panel-default">
		<div class="panel-heading">Result Information</div>
			<div class="panel-body">
			
			<form enctype="multipart/form-data" action="index.php?Application=QA&amp;Link=AQLRead&amp;ID=<?php echo $VID; ?>&amp;New=No" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
			<?php
				echo '<table style="width:100%;" class="table table-hover table-striped"><tbody>';
				echo '<tr>
						<th>' . _('Test Name') . '</th>
						<th>' . _('Method') . '</th>
						<th>' . _('Test Result') . '</th>	
					</tr>';
			$sql = "SELECT sampleid,
				resultid,
				sampleresults.testid,
				qatests.name,
				qatests.method,
				qatests.units,
				qatests.type,
				qatests.numericvalue,
				sampleresults.defaultvalue,
				sampleresults.targetvalue,
				sampleresults.rangemin,
				sampleresults.rangemax,
				sampleresults.testvalue,
				sampleresults.testdate,
				sampleresults.testedby,
				sampleresults.showoncert,
				isinspec,
				sampleresults.manuallyadded,
				groupby
		FROM sampleresults 
		INNER JOIN qatests ON qatests.testid=sampleresults.testid
		WHERE sampleresults.sampleid='".$VID."'
		AND sampleresults.showontestplan='1'
		ORDER BY groupby, name";

		$result = DB_query($sql, $db);
while ($myrow = DB_fetch_array($result)) {

if($groupby != $myrow['groupby']){
echo '<tr><th colspan="3">'.strtoupper($myrow['groupby']).'</th></tr>';
$groupby = $myrow['groupby'];
$title =1;
}

	if ($k == 1) { /*alternate bgcolour of row for highlighting */
		echo '<tr class="even">';
		$k = 0;
	} else {
		echo '<tr class="odd">';
		$k++;
	}
	$x++;
	$CompareVal='yes';
	$CompareRange='no';
	if ($myrow['targetvalue']=='') {
		$CompareVal='no';
	}

	$BGColor='';
	if ($myrow['testvalue']=='') {
		$BGColor=' style="background-color:yellow;" ';
	} else {
		if ($myrow['isinspec']==0) {
		$BGColor=' style="background-color:orange;" ';		
		}
	}
	
	$Class='';	
	$TypeDisp='Text Box';
	if($rows['prodspeckey']=="KOFC54030601" && $groupby =='Hardness Test'){
$hardness = array(_('SAMPLE'),_('38mm'),_('47mm'),_('57mm'),_('67mm'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="12" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
foreach($hard as $hardness){
echo '<input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $hardness . '"/>';
}
echo '</td>';

}elseif($rows['prodspeckey']=="KOFC54030601" && $groupby =='Wall Thickness Test'){
$hardness = array(_('SAMPLE'),_('6.7mm'),_('37.7mm'),_('Neck'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="12" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
foreach($hard as $hardness){
echo '<input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $hardness . '"/>';
}
echo '</td>';

}elseif($rows['prodspeckey']=="KOFC54030602" && $groupby =='Hardness Test'){
$hardness = array(_('SAMPLE'),_('8mm'),_('29mm'),_('35mm'),_('38mm'),_('47mm'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="12" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
foreach($hard as $hardness){
echo '<input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $hardness . '"/>';
}
echo '</td>';

}elseif($rows['prodspeckey']=="KOFC54030602" && $groupby =='Wall Thickness Test'){
$hardness = array(_('SAMPLE'),_('6.7mm'),_('12.7mm'),_('20.7mm'),_('37.7mm'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="12" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
foreach($hard as $hardness){
echo '<input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $hardness . '"/>';
}
echo '</td>';

}elseif($rows['prodspeckey']=="KOFC54030603" && $groupby =='Hardness Test'){
$hardness = array(_('SAMPLE'),_('6mm'),_('10mm'),_('14mm'),_('18mm'),_('22mm'),_('27mm'),_('35mm'),_('42mm'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="12" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
foreach($hard as $hardness){
echo '<input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $hardness . '"/>';
}
echo '</td>';

}elseif($rows['prodspeckey']=="KOFC54030603" && $groupby =='Wall Thickness Test'){
$hardness = array(_('SAMPLE'),_('7mm'),_('14mm'),_('25mm'),_('35mm'),_('Mouth'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="12" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
foreach($hard as $hardness){
echo '<input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $hardness . '"/>';
}
echo '</td>';

}elseif($rows['prodspeckey']=="KOFC54030604" && $groupby =='Hardness Test'){
$hardness = array(_('SAMPLE'),_('6mm'),_('10mm'),_('14mm'),_('18mm'),_('22mm'),_('27mm'),_('35mm'),_('42mm'),_('46mm'),_('50mm'),_('54mm'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="12" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
foreach($hard as $hardness){
echo '<input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $hardness . '"/>';
}
echo '</td>';

}elseif($rows['prodspeckey']=="KOFC54030604" && $groupby =='Wall Thickness Test'){
$hardness = array(_('SAMPLE'),_('7mm'),_('14mm'),_('25mm'),_('35mm'),_('Mouth'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="12" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
foreach($hard as $hardness){
echo '<input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $hardness . '"/>';
}
echo '</td>';

}elseif($rows['prodspeckey']=="KOFC54030605" && $groupby =='Hardness Test'){
$hardness = array(_('SAMPLE'),_('6mm'),_('10mm'),_('17mm'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="12" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
foreach($hard as $hardness){
echo '<input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $hardness . '"/>';
}
echo '</td>';

}elseif($rows['prodspeckey']=="KOFC54030605" && $groupby =='Wall Thickness Test'){
$hardness = array(_('SAMPLE'),_('10mm'),_('18.64mm'));
if($title ==1){
echo '<tr><td colspan="3">';
foreach($hardness as $tit){
echo '<input  disabled="true" size="12" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $tit . '"/>';
}
echo '</td></tr>';
$title =0;
}
$hard = explode(',',$myrow['testvalue']);
$TestResult ='';
echo '<td colspan="3"><input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $myrow['name'] . '"/>';
foreach($hard as $hardness){
echo '<input disabled="true" size="12" maxlength="80" value="&nbsp;&nbsp;' . $hardness . '"/>';
}
echo '</td>';

}else{
	$TestResult='<input type="text" disabled="true" size="25" maxlength="80" class="' . $Class . '" name="TestValue' .$x .'" value="' . $myrow['testvalue'] . '"' . $BGColor . '/>';
	echo '<td>' . $myrow['name'] . '</td>
			<td>' . $myrow['method'] . '</td>';
	echo '<td>' . $TestResult . '</td>';
	}
	
	echo '</tr>';
}
			echo '<tbody></table>';
			?>
			
					<?php
			$result = DB_query("SELECT remark,approver_title,approver_name,remarkdate FROM qrsampleremarks
						WHERE sampleid = '".$VID."' ORDER BY remarkdate ASC",	$db);
echo '<table style="border:none; width:100%" class="table">';
$tech = DB_query("SELECT technician,realname FROM qasampletechnicians
						INNER JOIN www_users ON qasampletechnicians.technician = www_users.userid
						WHERE sampleidno = '".$VID."'",	$db);
if(DB_num_rows($tech) >0){
echo '<tr><td colspan="3"><table style="border:none; width:100%" class="table"><tr><th colspan="2">Quality Assurance Technicians</th></tr>';
$no=1;
while($mya = DB_fetch_array($tech)){
echo '<tr><td>'.$no.'</td><td>'.$mya['technician'].' - '.$mya['realname'].'</td></tr>';
$no++;
}
echo '</table></td></tr>';
}
while($my = DB_fetch_array($result)){
echo' <tr>
				<td align="center" width="70"><img class="image" src="images/image.jpg"  /></td>
			<td><div class="bubble"><span class="time">' .  strtoupper($my['approver_title'])  . '<br />From: <a href="#">'.$my['approver_name'].'</a></span> 
			<br /> '.$my['remark'].'<br /><span class="time">'. calculate_time_span($my['remarkdate']) .'</span>
				</div>
			</td>
			  </tr>';
}

if($rows['process_level'] ==$rows['levelcheck']){
echo '<tr><td width="70">Remarks</td><td  colspan="2"><textarea name="remarks" style="width:100%" rows="2"></textarea></td></tr>';
}
echo '</table>';
			?>
		
			</div>
		</div>
              </div>

			 
            <!-- /.box-footer -->
            <div class="box-footer">
			 <div class="pull-right">
			 <?php
			 
			  if($rows['process_level']==$rows['levelcheck']){
			 if($rows['process_level']!=0){
			 ?>
			 <button type="submit" name="Reject" onclick="return confirm('Are you sure you want to Reject this Request?')" class="btn btn-danger"><i class="fa fa-reply"></i> Reject</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 <?php } ?>
			<button type="submit" name="submit" onclick="return confirm('Are you sure you want to Forward this Request?')" class="btn btn-success"><i class="fa fa-share"></i> Forward</button>
			<?php } ?>
			</div>
			  <a href="<?php echo $RootPath . '/PDFCOA.php?LotKey=' .$rows['lotkey'] .'&ProdSpec=' .$rows['prodspeckey'] .'&QASampleID=' .$rows['sampleid']; ?>" target="_blank"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>
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