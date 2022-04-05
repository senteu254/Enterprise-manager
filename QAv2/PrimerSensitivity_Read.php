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

if (isset($_POST['Submit'])) {
$sqlz = "SELECT process_level
			FROM qaprimersensitivity a
			INNER JOIN qa_approval_levels c ON c.type=5
			WHERE a.testno='" . $SelectedUser . "' AND process_level =c.levelcheck AND 
			(CASE WHEN c.authoriser='QAT' THEN a.technicianid='".$_SESSION['UserID']."' ELSE c.authoriser='".$_SESSION['UserID']."' END)";
	$resz = DB_query($sqlz);
	$myz = DB_fetch_array($resz);
$Level = ($myz['process_level']+1);
$resl = DB_query("SELECT accept_msg,reject_msg FROM qa_approval_levels
						WHERE type = 5 AND levelcheck='".$myz['process_level']."'",$db);
	$myl = DB_fetch_row($resl);	

$sql = "UPDATE qaprimersensitivity SET process_level='" . $Level . "', rejected=0
					WHERE testno = '". $SelectedUser . "'";

		$result = DB_query($sql);
DB_query("DELETE FROM qaprimersensitivityremarks WHERE approver='".$Level."' AND refid='". $SelectedUser ."'");
$sql = "INSERT INTO qaprimersensitivityremarks (refid,remarks,approver,approvertitle,approvername)
					VALUE('". $SelectedUser ."','". $_POST['remarks'] ."','".$Level."','".$myl[0]."','". $_SESSION['UsersRealName'] ."')";

		$result = DB_query($sql);
		$_SESSION['msg'] =_('The selected Report has been forwaded for processing');
}
if (isset($_POST['Reject'])) {
$sqlz = "SELECT process_level
			FROM qaprimersensitivity a
			INNER JOIN qa_approval_levels c ON c.type=5
			WHERE a.testno='" . $SelectedUser . "' AND process_level =c.levelcheck AND 
			(CASE WHEN c.authoriser='QAT' THEN a.technicianid='".$_SESSION['UserID']."' ELSE c.authoriser='".$_SESSION['UserID']."' END)";
	$resz = DB_query($sqlz);
	$myz = DB_fetch_array($resz);
$Level = ($myz['process_level']+1);
$resl = DB_query("SELECT accept_msg,reject_msg FROM qa_approval_levels
						WHERE type = 5 AND levelcheck='".$myz['process_level']."'",$db);
	$myl = DB_fetch_row($resl);	

$sql = "UPDATE qaprimersensitivity SET process_level=0, rejected=1
					WHERE testno = '". $SelectedUser . "'";
$result = DB_query($sql);
DB_query("DELETE FROM qaprimersensitivityremarks WHERE approver='".$Level."' AND refid='". $SelectedUser ."'");
$sql = "INSERT INTO qaprimersensitivityremarks (refid,remarks,approver,approvertitle,approvername)
					VALUE('". $SelectedUser ."','". $_POST['remarks'] ."','".$Level."','".$myl[1]."','". $_SESSION['UsersRealName'] ."')";

		$result = DB_query($sql);
		$_SESSION['msg'] =_('The selected Report has been Rejected back to the Technician');
}

if(isset($_GET['New']) && $_GET['New']=='Yes'){
	$sql = "SELECT *, a.testno
			FROM qaprimersensitivity a
			INNER JOIN qa_approval_levels c ON c.type=5
			WHERE a.testno='" . $SelectedUser . "' AND process_level =c.levelcheck AND 
			(CASE WHEN c.authoriser='QAT' THEN a.technicianid='".$_SESSION['UserID']."' ELSE c.authoriser='".$_SESSION['UserID']."' END)";
	}else{
	$sql = "SELECT *, a.testno
			FROM qaprimersensitivity a
			INNER JOIN qa_approval_levels c ON c.type=5
			WHERE a.testno='" . $SelectedUser . "' AND process_level >c.levelcheck ";
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

	$_POST['id'] = $myrow['testno'];
	$_POST['test'] = $myrow['test'];
	$_POST['machine'] = $myrow['depthrange'];
	$_POST['date'] = ConvertSQLDate($myrow['date']);
	$_POST['lot'] = $myrow['primerlot'];
	$_POST['calibre']	= $myrow['calibre'];
switch ($myrow['calibre']) {
    case "5.56x45mm Ball":
        $req1 = "MEAN H.+5s <14Inches";
		$req2 = "MEAN H.-2s >3Inches";
        break;
    case "7.62x51mm Ball":
        $req1 = "MEAN H.+5s <16Inches";
		$req2 = "MEAN H.-2s >3Inches";
        break;
    case "9x19mm Para":
        $req1 = "MEAN H.+5s <12Inches";
		$req2 = "MEAN H.-2s >3Inches";
        break;
    default:
        $req1 = "";
		$req2 = "";
}
	echo '<table style="border:none; width:100%" class="table">
      <tr><td>';

	echo '<table style="border:none; width:100%" class="table">
			<tr height="30px">
				<td width="150px">' . _('Test No') . ':</td>
				<td width="200px" colspan="3"><strong>' . $_POST['test'] . '</strong></td>
			</tr>';

	echo '<tr height="30px"><td>' . _('Date') .'</td>
			<td><b>'.$_POST['date'].'</b></td>
			<td>' . _('Calibre') . '</td>
			<td><strong>'.$_POST['calibre'].'</strong></td>
		</tr><tr height="30px">
		<td>' . _('Primer Depth Range.') . '</td>
			<td><b>'.$_POST['machine'].'</b></td>
			<td><strong>Requirement:</strong></td><td><b> '.$req1.'</br>'.$req2.'</b></td>
		</tr>
		<tr height="30px">
		<td>' .  _('Primer Lot No') . '</td>
		<td colspan="3"><b>'.$_POST['lot'] .'</b></td>';

echo '</td>
	</tr>
	</table>';
	
	echo '</td>
	</tr>
	<tr><td colspan="4">';
		
$sql = "SELECT *
		FROM qaprimersensitivitydata
		WHERE testno='" . $SelectedUser . "' ORDER BY height ASC";
	$results = DB_query($sql);
echo '<table style="width:100%" class="table">';
echo '<tr>
		<th width="100">H(d)</td>
		<th width="100">Height</td>
		<th width="100">No Fire</td>
		<th width="100">% (PI)</td>
		<th width="100">(KI)</td>
		<th width="100">KI &times; PI</td>
	</tr>';
$k1 = array(0,1,3,5,7,9,11,13,15,17,19);
$Sample = 25;
$i = 0;
$sumPI = 0;
$sumKP = 0;
while($myro = DB_fetch_array($results)){
$PI = ($myro['misfired']/$Sample);
$KP = ($PI*$k1[$i]);

if ($k==1){
			echo '<tr>';
			$k=0;
		} else {
			echo '<tr>';
			$k=1;
		}
echo '
		<td ><center>'. ($myro['misfired']== $Sample ? $myro['height'] : '') .'</center></td>
		<td ><center>'.$myro['height'].'</center></td>
		<td ><center>'.$myro['misfired'].'</center></td>
		<td ><center>'. ($PI== 1 ? '..........' : round($PI,2)) .'</center></td>
		<td ><center>'. ($k1[$i]== 0 ? '...........' : $k1[$i]) .'</center></td>
		<td ><center>'. ($KP== 0 ? '...........' : $KP) .'</center></td>
	</tr>';
$i++;

$sumPI +=($PI== 1 ? 0 : $PI);
$sumKP +=$KP;
if($myro['misfired'] == $Sample){
$Hd = $myro['height'];
}

	}
echo '<tr><td colspan="3" style="text-align:right"><strong>Total :</strong></td><td><strong><center>'. $sumPI .'</center></strong></td><td></td><td><strong><center>'. $sumKP .'</strong></center></td></tr>';
$meanH = ($sumPI+$Hd+0.5);
$sqPI = ($sumPI*$sumPI);
$SD = sqrt($sumKP-$sqPI);	
$meanH1 = $meanH + (5*$SD);
$meanH2 = $meanH - (2*$SD);
echo '<tr height="30"><td colspan="6"></td></tr>';
echo '<tr><td colspan="4">H(d) = Max Height Giving 100% Misfire</td><th><center>'.$Hd.'</center></th><td></td></tr>';
echo '<tr><td colspan="4">Y = Height Between Two Heights (linch)</td><th><center>1</center></th><td></td></tr>';
echo '<tr><td colspan="4">MEAN H. = Sum of PI + H(d) + &frac12;Y</td><th><center>'. round($meanH,2) .'</center></th><td></td></tr>';
echo '<tr><td colspan="4">(Sum of PI)&sup2;</td><th><center>'. round($sqPI,2) .'</center></th><td></td></tr>';
echo '<tr><td colspan="4">SD = &radic; { Sum of KI &times; PI - (Sum of PI)&sup2; }</td><th><center>'. round($SD,2) .'</center></th><td></td></tr>';
echo '<tr><td colspan="4">MEAN H. + 5s</td><th><center>'. round($meanH1,2) .'</center></th><td></td></tr>';
echo '<tr><td colspan="4">MEAN H. - 2s</td><th><center>'. round($meanH2,2) .'</center></th><td></td></tr>';
echo '</table>';
		  
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

?>             
<!-- /.mailbox-controls -->
			
			<form enctype="multipart/form-data" action="index.php?Application=QA&Link=PrimerSensitivityRead&ID=<?php echo $SelectedUser; ?>&New=No" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
			
					<?php
			$sql = "SELECT *
		FROM qaprimersensitivityremarks
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
			  <a href="<?php echo $RootPath . '/QAPrimerCurve.php?SelectedUser='.$SelectedUser;?>" target="_blank"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>
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