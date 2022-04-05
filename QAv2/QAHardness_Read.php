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
$sqlx = "SELECT process_level
			FROM qaannealinghardness d
			INNER JOIN qa_approval_levels e ON e.type=4
			WHERE d.id='" . $SelectedUser . "' AND process_level =e.levelcheck AND 
			(CASE WHEN e.authoriser='QAT' THEN d.technicianid='".$_SESSION['UserID']."' ELSE e.authoriser='".$_SESSION['UserID']."' END)";
	$resz = DB_query($sqlx);
	$myz = DB_fetch_array($resz);
$Level = ($myz['process_level']+1);
$resl = DB_query("SELECT accept_msg,reject_msg FROM qa_approval_levels
						WHERE type = 4 AND levelcheck='".$myz['process_level']."'",$db);
	$myl = DB_fetch_row($resl);	

$sql = "UPDATE qaannealinghardness SET process_level='" . $Level . "', rejected=0
					WHERE id = '". $SelectedUser . "'";

		$result = DB_query($sql);
DB_query("DELETE FROM qaannealingremarks WHERE approver='".$Level."' AND refid='". $SelectedUser ."'");
$sql = "INSERT INTO qaannealingremarks (refid,remarks,approver,approvertitle,approvername)
					VALUE('". $SelectedUser ."','". $_POST['remarks'] ."','".$Level."','".$myl[0]."','". $_SESSION['UsersRealName'] ."')";

		$result = DB_query($sql);
		$_SESSION['msg'] =_('The selected Report has been forwaded for processing');
}
if (isset($_POST['Reject'])) {
$sqlx = "SELECT process_level
			FROM qaannealinghardness d
			INNER JOIN qa_approval_levels e ON e.type=4
			WHERE d.id='" . $SelectedUser . "' AND process_level =e.levelcheck AND 
			(CASE WHEN e.authoriser='QAT' THEN d.technicianid='".$_SESSION['UserID']."' ELSE e.authoriser='".$_SESSION['UserID']."' END)";
	$resz = DB_query($sqlx);
	$myz = DB_fetch_array($resz);
$Level = ($myz['process_level']+1);
$resl = DB_query("SELECT accept_msg,reject_msg FROM qa_approval_levels
						WHERE type = 4 AND levelcheck='".$myz['process_level']."'",$db);
	$myl = DB_fetch_row($resl);	

$sql = "UPDATE qaannealinghardness SET process_level=0, rejected=1
					WHERE id = '". $SelectedUser . "'";

		$result = DB_query($sql);
DB_query("DELETE FROM qaannealingremarks WHERE approver='".$Level."' AND refid='". $SelectedUser ."'");
$sql = "INSERT INTO qaannealingremarks (refid,remarks,approver,approvertitle,approvername)
					VALUE('". $SelectedUser ."','". $_POST['remarks'] ."','".$Level."','".$myl[1]."','". $_SESSION['UsersRealName'] ."')";

		$result = DB_query($sql);
		$_SESSION['msg'] =_('The selected Report has been Rejected back to the Technician');
}

if(isset($_GET['New']) && $_GET['New']=='Yes'){
	$sql = "SELECT *,d.id
			FROM qarecordingsheet a
			INNER JOIN qaoperationtype b ON b.id=a.typeid
			INNER JOIN qaoperation c ON c.id=a.operationid
			INNER JOIN qaannealinghardness d ON d.sheetid=a.id
			INNER JOIN qa_approval_levels e ON e.type=4
			WHERE d.id='" . $SelectedUser . "' AND process_level =e.levelcheck AND 
			(CASE WHEN e.authoriser='QAT' THEN d.technicianid='".$_SESSION['UserID']."' ELSE e.authoriser='".$_SESSION['UserID']."' END)";
	}else{
	$sql = "SELECT *,d.id
			FROM qarecordingsheet a
			INNER JOIN qaoperationtype b ON b.id=a.typeid
			INNER JOIN qaoperation c ON c.id=a.operationid
			INNER JOIN qaannealinghardness d ON d.sheetid=a.id
			INNER JOIN qa_approval_levels e ON e.type=4
			WHERE d.id='" . $SelectedUser . "' AND process_level >e.levelcheck AND 
			(CASE WHEN e.authoriser='QAT' THEN d.technicianid='".$_SESSION['UserID']."' ELSE e.authoriser='".$_SESSION['UserID']."' END)";
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
				<td>' . _('Operation') . ':</td>
				<td>' . $myrow['operation'] . '</td>
			</tr>
			<tr>
				<td>' . _('Operation Type') . ':</td>
				<td>' . $myrow['typename'] . '</td>
			</tr>
			<tr>
				<td>' . _('Sheet') . ':</td>
				<td>' . $myrow['sheetname'] . '</td>
			</tr>';

	echo '<tr >
			<td>' . _('Machine No.') . '</td>
			<td>'.$myrow['machineno'].'</td>
		</tr>
		<tr>
			<td>' .  _('Brass Lot No') . '</td>
			<td>'.$myrow['brasslot'] .'</td>
		</tr>
		<tr>
			<td>' .  _('Case Lot No') . '</td>
			<td>'.$myrow['caselot'] .'</td>
		</tr>
		<tr>
			<td>' . _('Date') . '</td>
			<td>'.ConvertSQLDate($myrow['date']).'</td>
		</tr>
		<tr>
			<td>' . _('Shift') . '</td>
			<td>'.$myrow['shift'].'</td>
		</tr>
		<tr>
			<td>' . _('Technician') . '</td>
			<td>'.$myrow['technician'].'</td>
		</tr>';

echo '</table>';
	echo '</td>	<td>';
			
$sql = "SELECT *
		FROM qaannealinghardnessdata
		WHERE testno='" . $SelectedUser . "' ORDER BY num ASC";
	$results = DB_query($sql);
echo '<table class="selection" >';
echo '<tr height="30">
		<th width="100">Test</td>
		<th colspan="5">Result</td>
	</tr>';
while($myro = DB_fetch_array($results)){

echo '<tr height="25">
		<td style="border:solid;">'.$myro['sample'].'</td>
		<td style="border:solid">'.$myro['result'].'</td>
		'.($myro['result1']!=""? '<td style="border:solid">'.$myro['result1'].'</td>':'').'
		'.($myro['result2']!=""? '<td style="border:solid">'.$myro['result2'].'</td>':'').'
		'.($myro['result3']!=""? '<td style="border:solid">'.$myro['result3'].'</td>':'').'
		'.($myro['result4']!=""? '<td style="border:solid">'.$myro['result4'].'</td>':'').'
	</tr>';
	}

echo '</table>';
if($myrow['hardness'] !="" && $myrow['basecontrol'] !=""){
echo '<strong>'._('HARDNESS : '.$myrow['hardness']).'</strong><br />';
echo '<table class="selection" >';
echo '<tr><th colspan="2" height="30">' .  _('BASE CONTROL')  . '</th></tr>';
$control = unserialize($myrow['base_control']);
foreach($control as $val=>$key){
echo '<tr><th style="border:solid;" height="20" width="70">'.$val.'</th><th style="border:solid; " width="70">'.$key.'</th></tr>';
}
echo '</table>';
}
	echo '</td>
	</tr>
	<tr><td colspan="4">';
if($myrow['sheets']!="" && $myrow['sheets']>1){
$graphs =$myrow['sheets'];
$max1 = explode('-',$myrow['max_limit']);
$min1 = explode('-',$myrow['min_limit']);
$description = explode(',',$myrow['description']);
$SQLx = "SELECT *
		FROM qaannealinghardnessdata
		WHERE testno='" . $SelectedUser . "' ORDER BY num ASC";
for($x=0; $x<$graphs; $x++){
$graph = new PHPlot(350,400);

	$GraphTitle = ' ' . $description[$x] . "\n\r";

	$graph->SetTitle($GraphTitle);
	$graph->SetTitleColor('blue');
	$graph->SetOutputFile('companies/' .$_SESSION['DatabaseName'] .  '/reports/hardnessgraph_' . $SelectedUser . '_'.$x.'.png');
	$graph->SetXTitle(_('Sample'));
	$graph->SetYTitle(_('Hardness'));
	$graph->SetXTickPos('none');
	$graph->SetXTickLabelPos('none');
	$graph->SetXLabelAngle(0);
	$graph->SetBackgroundColor('white');
	$graph->SetTitleColor('blue');
	$graph->SetPlotType('linepoints');
	$graph->SetFileFormat('png');
	$graph->SetIsInline('1');
	$graph->SetShading(5);
	$graph->SetDrawYGrid(TRUE);
	$graph->SetDrawXGrid(TRUE);
	$graph->SetDataType('text-data');
	$graph->SetLineStyles('solid');
	$graph->SetYTickIncrement(5);

	//$graph->SetNumberFormat($DecimalPoint, $ThousandsSeparator);
	//$graph->SetPrecisionY($_SESSION['CompanyRecord']['decimalplaces']);

	$SalesResult = DB_query($SQLx);
	if (DB_error_no() !=0) {

		prnMsg(_('The graph data for the selected criteria could not be retrieved because') . ' - ' . DB_error_msg(),'error');
		include('includes/footer.inc');
		exit;
	}
	if (DB_num_rows($SalesResult)==0){
		prnMsg(_('There is not data for the criteria entered to graph'),'info');
		include('includes/footer.inc');
		exit;
	}

	$max = explode(',',$max1[$x]);
	$min = explode(',',$min1[$x]);

	$GraphArray = array();
	$i = 0;
	if($x==0){ $y=''; }else{ $y=$x; }
	while ($myro = DB_fetch_array($SalesResult)){
		if(count($max)>1){ $maxx = $max[$i]; }else{ $maxx = $max[0];}
		if(count($min)>1){ $minx = $min[$i]; }else{ $minx = $min[0];}
		$GraphArray[$i] = array($myro['sample'], $myro['result'.$y], $minx, $maxx);
		$i++;
		
	}

	$graph->SetDataValues($GraphArray);
	$graph->SetDataColors(
		array('green','red','red'),  //Data Colors
		array('black')	//Border Colors
	);
	$graph->SetLegend(array(_('Actual'),_('Lower ('.$min[0].')'),_('Upper ('.$max[0].')')));

	//Draw it
	$graph->DrawGraph();
	
}

echo '<table class="selection">
			<tr>
				<td><p><img src="companies/' .$_SESSION['DatabaseName'] .  '/reports/hardnessgraph_'.$SelectedUser.'_0.png" alt="Hardness Report Graph" width="100%"></img></p></td>
			
			<td><p><img src="companies/' .$_SESSION['DatabaseName'] .  '/reports/hardnessgraph_'.$SelectedUser.'_1.png" alt="Hardness Report Graph" width="100%"></img></p></td>
			
			<td><p><img src="companies/' .$_SESSION['DatabaseName'] .  '/reports/hardnessgraph_'.$SelectedUser.'_2.png" alt="Hardness Report Graph" width="100%"></img></p></td>
			</tr>
		  </table>';
//=================================================================================================
}else{
$graph = new PHPlot(750,450);

	$GraphTitle = ' ' . $myrow['description'] . "\n\r";

	$SQLx = "SELECT *
		FROM qaannealinghardnessdata
		WHERE testno='" . $SelectedUser . "' ORDER BY num ASC";


	$graph->SetTitle($GraphTitle);
	$graph->SetTitleColor('blue');
	$graph->SetOutputFile('companies/' .$_SESSION['DatabaseName'] .  '/reports/hardnessgraph_' . $SelectedUser . '.png');
	$graph->SetXTitle(_('Sample'));
	$graph->SetYTitle(_('Hardness'));
	$graph->SetXTickPos('none');
	$graph->SetXTickLabelPos('none');
	$graph->SetXLabelAngle(0);
	$graph->SetBackgroundColor('white');
	$graph->SetTitleColor('blue');
	$graph->SetPlotType('linepoints');
	$graph->SetFileFormat('png');
	$graph->SetIsInline('1');
	$graph->SetShading(5);
	$graph->SetDrawYGrid(TRUE);
	$graph->SetDrawXGrid(TRUE);
	$graph->SetDataType('text-data');
	$graph->SetLineStyles('solid');

	//$graph->SetNumberFormat($DecimalPoint, $ThousandsSeparator);
	//$graph->SetPrecisionY($_SESSION['CompanyRecord']['decimalplaces']);

	$SalesResult = DB_query($SQLx);
	if (DB_error_no() !=0) {

		prnMsg(_('The graph data for the selected criteria could not be retrieved because') . ' - ' . DB_error_msg(),'error');
		include('includes/footer.inc');
		exit;
	}
	if (DB_num_rows($SalesResult)==0){
		prnMsg(_('There is not data for the criteria entered to graph'),'info');
		include('includes/footer.inc');
		exit;
	}

	$max = explode(',',$myrow['max_limit']);
	$min = explode(',',$myrow['min_limit']);

	$GraphArray = array();
	$i = 0;
	while ($myro = DB_fetch_array($SalesResult)){
		if(count($max)>1){ $maxx = $max[$i]; }else{ $maxx = $max[0];}
		if(count($min)>1){ $minx = $min[$i]; }else{ $minx = $min[0];}
		$GraphArray[$i] = array($myro['sample'], $myro['result'], $myro['result1'], $myro['result2'], $myro['result3'], '', $myro['result4'], $minx, $maxx);
		$i++;
		
	}

	$graph->SetDataValues($GraphArray);
	$graph->SetDataColors(
		array('grey','green','orange','pink','','blue','red','red'),  //Data Colors
		array('black')	//Border Colors
	);
	//$graph->SetLegend(array(_('Actual'),'','','','',_('Lower Limit '),_('Upper Limit')));

	//Draw it
	$graph->DrawGraph();
	//===============================================================================================================================	
	
	echo '<table class="selection">
			<tr>
				<td><p><img src="companies/' .$_SESSION['DatabaseName'] .  '/reports/hardnessgraph_'.$SelectedUser.'.png" alt="Hardness Report Graph" width="100%"></img></p></td>
			</tr>
		  </table>';
		  
	}
		  
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

echo '</td></tr></table>';
?>             
<!-- /.mailbox-controls -->
			
			<form enctype="multipart/form-data" action="index.php?Application=QA&Link=QAHardnessRead&ID=<?php echo $VID; ?>&New=No" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
			
					<?php
			$sql = "SELECT *
		FROM qaannealingremarks
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
			  <a href="<?php echo $RootPath . '/PDFQAHardnessAnnealingGraph.php?id='.$SelectedUser; ?>" target="_blank"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>
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