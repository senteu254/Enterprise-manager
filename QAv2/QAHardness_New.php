<script type="text/javascript" src="js/qsearch.js"></script>
<script type="text/javascript" src="js/webcam.js"></script>
<?php
if (isset($_GET['SelectedUser'])){
	$SelectedUser = $_GET['SelectedUser'];
} elseif (isset($_POST['SelectedUser'])){
	$SelectedUser = $_POST['SelectedUser'];
}
if (isset($_POST['Submit'])) {
$InputError = 0;

if (isset($SelectedUser)) {
$sql = "UPDATE qaannealinghardness SET `sheetid`='" . $_POST['sheet'] ."', 
										`brasslot`='" . $_POST['brasslot'] ."', 
										`machineno`='" . $_POST['machine'] ."', 
										`shift`='" . $_POST['shift'] ."',
										`caselot`='" . $_POST['caselot'] ."',
										`base_control`='" . serialize($_POST['control']) ."',
										date='" . FormatDateForSQL($_POST['date']) ."',
										technician='".$_SESSION['UsersRealName']."'
					WHERE id = '". $SelectedUser . "'";
					
$sqldel = "DELETE FROM qaannealinghardnessdata WHERE testno = '". $SelectedUser . "'";
$ErrMsg = _('The user alterations could not be processed because');
$DbgMsg = _('The SQL that was used to update the user and failed was');
$Rest = DB_query($sqldel,$ErrMsg,$DbgMsg,true);

	for($i=0;$i<count($_POST['sample']);$i++){
	$sno= $_POST['sample'][$i];
	$qty = $_POST['result'][$i];
	$qty1 = ((isset($_POST['result1'][$i]) && $_POST['result1'][$i] !="")? $_POST['result1'][$i] : "NULL");
	$qty2 = ((isset($_POST['result2'][$i]) && $_POST['result2'][$i] !="")? $_POST['result2'][$i] : "NULL");
	$qty3 = ((isset($_POST['result3'][$i]) && $_POST['result3'][$i] !="")? $_POST['result3'][$i] : "NULL");
	$qty4 = ((isset($_POST['result4'][$i]) && $_POST['result4'][$i] !="")? $_POST['result4'][$i] : "NULL");
	$no = $i+1;	
	$SQL="INSERT INTO qaannealinghardnessdata (num,
									testno,
									sample,
									result,
									result1,
									result2,
									result3,
									result4) 
							VALUES('". $no ."',
									'". $SelectedUser ."',
									'". $sno ."',
									'". $qty ."',
									". $qty1 .",
									". $qty2 .",
									". $qty3 .",
									". $qty4 .")";
					$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		}
					
	$_SESSION['msg'] = _('The selected record has been updated successfully');
	$redirect = htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&Link=QAHardness';	
	} elseif ($InputError !=1) {
	//initialise no input errors assumed initially before we test
		$RequestNo = GetNextTransNo(83, $db);
		$sql = "INSERT INTO qaannealinghardness (`id`, 
												`sheetid`, 
												`brasslot`, 
												`machineno`, 
												`shift`,
												caselot,
												base_control,
												`date`,
												technician,
												technicianid,
												process_level)
											VALUES (" . $RequestNo . ",
												'" . $_POST['sheet'] ."',
												'" . $_POST['brasslot'] ."',
												'" . $_POST['machine'] ."',
												'" . $_POST['shift'] ."',
												'" . $_POST['caselot'] ."',
												'" . serialize($_POST['control']) ."',
												'" . FormatDateForSQL($_POST['date']) ."',
												'".$_SESSION['UsersRealName']."',
												'".$_SESSION['UserID']."',
												0)";
	
	for($i=0;$i<count($_POST['sample']);$i++){
	$sno= $_POST['sample'][$i];
	$qty = $_POST['result'][$i];
	$qty1 = ((isset($_POST['result1'][$i]) && $_POST['result1'][$i] !="")? $_POST['result1'][$i] : "NULL");
	$qty2 = ((isset($_POST['result2'][$i]) && $_POST['result2'][$i] !="")? $_POST['result2'][$i] : "NULL");
	$qty3 = ((isset($_POST['result3'][$i]) && $_POST['result3'][$i] !="")? $_POST['result3'][$i] : "NULL");
	$qty4 = ((isset($_POST['result4'][$i]) && $_POST['result4'][$i] !="")? $_POST['result4'][$i] : "NULL");
	$no = $i+1;	
	$SQL="INSERT INTO qaannealinghardnessdata (num,
									testno,
									sample,
									result,
									result1,
									result2,
									result3,
									result4) 
							VALUES('". $no ."',
									'". $RequestNo ."',
									'". $sno ."',
									'". $qty ."',
									". $qty1 .",
									". $qty2 .",
									". $qty3 .",
									". $qty4 .")";
					$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		}
$_SESSION['msg'] = _('A new record has been inserted Successfully');
$redirect = htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&Link=QAHardnessRead&ID='.$RequestNo.'&New=Yes';
	}

	if ($InputError!=1){
		//run the SQL from either of the above possibilites
		$ErrMsg = _('The user alterations could not be processed because');
		$DbgMsg = _('The SQL that was used to update the user and failed was');
		$result = DB_query($sql,$ErrMsg,$DbgMsg);

		unset($_POST['sheet']);
		unset($_POST['brasslot']);
		unset($_POST['machine']);
		unset($_POST['date']);
		unset($_POST['shift']);
		unset($_POST['type']);
		unset($_POST['operation']);
		unset($SelectedUser);
		echo "<script type=\"text/javascript\">
				window.location.href = '".$redirect."';
            </script>
        ";
	}
	
}


echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&amp;Link=NewQAHardness" id="form" name="form">
	<div>
	<br />
	<table class="table" style="width:100%">
		<tr>';
		
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
#############################################################################################

if (isset($SelectedUser)) {
	//editing an existing User

	$sql = "SELECT *,a.id
		FROM qaannealinghardness a
		INNER JOIN qarecordingsheet b ON a.sheetid=b.id
		WHERE a.id='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['id'] = $myrow['id'];
	$_POST['date'] = $myrow['date'];
	$_POST['shift'] = $myrow['shift'];
	$_POST['brasslot'] = $myrow['brasslot'];
	$_POST['machine'] = $myrow['machineno'];
	$_POST['sheet'] = $myrow['sheetid'];
	$_POST['operation'] = $myrow['operationid'];
	$_POST['type'] = $myrow['typeid'];
	$_POST['caselot'] = $myrow['caselot'];
	$_POST['control'] = $myrow['base_control'];
	
echo '<center><a class="btn btn-default" href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&amp;Link=QAHardness">' . _('Back to Main Menu') . '</a></center>';

	echo '<input type="hidden" name="SelectedUser" value="' . $SelectedUser . '" />';
	echo '<input type="hidden" name="UserID" value="' . $_POST['id'] . '" />';

	echo '<td>' . _('Record No') . ':</td>
			<td>' . $_POST['id'] . '</td>
		</tr>';
}
#############################################################################################
	echo '<tr>
		<td>' . _('Operation') . '</td>
		<td><select name="operation" required="required" onchange="document.form.submit();">';
     $SQL = "SELECT * FROM qaoperation";
				 $result=DB_query($SQL);
  echo '<option selected="selected" value="">--Select Sheet--</option>';
  while ($myrow4=DB_fetch_array($result)){
	if (isset($_POST['operation']) AND  $myrow4['id']==$_POST['operation']){
		echo '<option selected="selected" value="'. $myrow4['id'] . '">' . $myrow4['operation'] . '</option>';
	} else {
		echo '<option value="'. $myrow4['id'] . '">' . $myrow4['operation'] . '</option>';
	}
}
  echo '</select></td>';
		
  echo '<td>' . _('Operation Type') . '</td>
			<td><select name="type" required="required" onchange="document.form.submit();">';
			 echo '<option selected="selected" value="">--Select Sheet--</option>';
	if(isset($_POST['operation']) && $_POST['operation'] !=""){
     $SQL = "SELECT *,a.id FROM qaoperationtype a
	 				INNER JOIN qarecordingsheet b ON b.typeid=a.id
	 				WHERE b.operationid=".$_POST['operation']." GROUP BY a.id";
	$result=DB_query($SQL);
  while ($myrow4=DB_fetch_array($result)){
	if (isset($_POST['type']) AND  $myrow4['id']==$_POST['type']){
		echo '<option selected="selected" value="'. $myrow4['id'] . '">' . $myrow4['typename'] . '</option>';
	} else {
		echo '<option value="'. $myrow4['id'] . '">' . $myrow4['typename'] . '</option>';
	}
}
}
  echo '</select></td>
		</tr>';

  echo '<tr><td>' . _('Data Sheet') . '</td>
			<td><select name="sheet" required="required" onchange="document.form.submit();">';
  echo '<option selected="selected" value="">--Select Sheet--</option>';
  if(isset($_POST['operation']) && $_POST['operation'] !="" && isset($_POST['type']) && $_POST['type'] !=""){
  $SQL = "SELECT * FROM qarecordingsheet WHERE operationid=".$_POST['operation']." AND typeid=".$_POST['type']."";
	$result=DB_query($SQL);
  while ($myrow4=DB_fetch_array($result)){
	if (isset($_POST['sheet']) AND  $myrow4['id']==$_POST['sheet']){
		echo '<option selected="selected" value="'. $myrow4['id'] . '">' . $myrow4['sheetname'] . '</option>';
	} else {
		echo '<option value="'. $myrow4['id'] . '">' . $myrow4['sheetname'] . '</option>';
	}
	}
}
  echo '</select></td>
		';

  echo '
		<td>' .  _('Brass Lot No.') . '</td>';
	echo '<td><input type="text" autofocus="autofocus" name="brasslot" value="'.$_POST['brasslot'].'" /></td></tr>';

 echo '<tr>
		<td>' .  _('Case Lot No.') . '</td>';
	echo '<td><input type="text" autofocus="autofocus" name="caselot" value="'.$_POST['caselot'].'" /></td>';
	
 echo '
		<td>' .  _('Machine No.') . '</td>';
	echo '<td><input type="text" autofocus="autofocus" name="machine" value="'.$_POST['machine'].'" /></td></tr>';
	echo '<tr>
			<td>' . _('Shift') . '</td>
			<td><input type="text" autofocus="autofocus" name="shift" value="'.$_POST['shift'].'" /></td>';
	echo '<td>' . _('Date') . '</td>
			<td><input type="text" autofocus="autofocus" alt="' .$_SESSION['DefaultDateFormat'] .'" required="required" class="date" name="date" value="'.Date($_SESSION['DefaultDateFormat']).'" /></td>
		</tr>';
	echo '</table>';

echo '</div>';
if(isset($_POST['sheet']) && $_POST['sheet'] !=""){
 $SQL = "SELECT * FROM qarecordingsheet WHERE id=".$_POST['sheet']."";
	$resu=DB_query($SQL);
  $myr=DB_fetch_array($resu);
  $line = $myr['graphs'];
  $bcontrol=$myr['basecontrol'];
  $hardness=$myr['hardness'];
echo '<input type="hidden" name="graphno" value="' . $line . '" />';

if (isset($SelectedUser)) {
	//editing an existing User
	
if($hardness !="" or $bcontrol !=""){
echo '<strong>'._('HARDNESS :'.$hardness).'</strong>';
echo '<table align="left">';
echo '<tr><th colspan="2">' .  _('BASE CONTROL')  . '</th></tr>';
$control = unserialize($_POST['control']);
foreach($bcontrol as $key=>$val){
echo '<tr><th width="20" height="30">'.$key.'</th><td><input type="text" size="10" required maxlength="10" autofocus="autofocus" name="control['.$key.']" value="'.$val.'" /></td></tr>';
}
echo '</table>';
}

	$sql = "SELECT *
		FROM qaannealinghardnessdata
		WHERE testno='" . $SelectedUser . "'";

	$result3 = DB_query($sql);
echo '<table id="dataTable" class="table">';
echo '<tr>
		<th>' .  _('Test')  . '</th>
		<th>' .  _('Result'). '</th>';
		if($line >1){
			for($i=1; $i<$line; $i++){
			echo '<th>' .  _('Result '.$i). '</th>';
			}
		}
		echo '<th></th>
	</tr>';
while($myro = DB_fetch_array($result3)){
	echo '<tr>
			
			<td><input type="text" size="6" maxlength="20" autofocus="autofocus" required class="number" name="sample[]" value="'.$myro['sample'].'" /></td>
			<td><input type="text" size="6" maxlength="10" autofocus="autofocus" required class="number" name="result[]" value="'.$myro['result'].'" /></td>';
			if($line >1){
			for($i=1; $i<$line; $i++){
			echo '<td><input type="text" size="6" maxlength="10" autofocus="autofocus" class="number" autocomplete="off" name="result'.$i.'[]" value="'.$myro['result'.$i].'" /></td>';
			}
			}
		echo '<td><a href="#" onClick="Javacsript:deleteRow(this)"><img src="'.$RootPath.'/css/trash.png" title="' ._('Delete Row') . '" alt="" /></a></a></td>
		</tr>';
	}
echo '</table>';
echo '<center><input name="" type="button" class="btn btn-default" onclick=addRow("dataTable") title="' ._('Add New Row') . '" value="Add New Row" /></center>';	
}else{

if($hardness !="" or $bcontrol !=""){
echo '<strong>'._('HARDNESS :'.$hardness).'</strong>';
echo '<table align="left">';
echo '<tr><th colspan="2">' .  _('BASE CONTROL')  . '</th></tr>';
$control = explode(',',$bcontrol);
foreach($control as $val){
echo '<tr><th width="20" height="30">'.$val.'</th><td><input type="text" size="10" required maxlength="10" autofocus="autofocus" name="control['.$val.']" value="" /></td></tr>';
}
echo '</table>';
}
echo '<table id="dataTable" class="table">';
echo '<tr>
		<th>' .  _('Test')  . '</th>
		<th>' .  _('Result'). '</th>';
		if($line >1){
			for($i=1; $i<$line; $i++){
			echo '<th>' .  _('Result '.$i). '</th>';
			}
		}
		echo '<th></th>
	</tr>';

	echo '<tr>
			
			<td><input type="text" size="6" maxlength="20" required autofocus="autofocus" name="sample[]" autocomplete="off" class="number" value="" /></td>
			<td><input type="text" size="6" maxlength="10" required autofocus="autofocus"  class="number" autocomplete="off" name="result[]" value="" /></td>';
			if($line >1){
			for($i=1; $i<$line; $i++){
			echo '<td><input type="text" size="6" maxlength="10" autofocus="autofocus"  class="number" autocomplete="off" name="result'.$i.'[]" value="" /></td>';
			}
			}
			echo '<td><a href="#" onClick="Javacsript:deleteRow(this)"><img src="'.$RootPath.'/css/trash.png" title="' ._('Delete Row') . '" alt="" /></a></a></td>
		</tr>';

echo '</table>';	
	echo '<center><input name="" type="button" class="btn btn-default" onclick=addRow("dataTable") title="' ._('Add New Row') . '" value="Add New Row" /></center>';		
}
}

?>
			 
            <!-- /.box-footer -->
            <div class="box-footer">
			 <div class="pull-right">
			<button type="submit" name="Submit" onclick="return confirm('Are you sure you want to Save this Record?')" class="btn btn-primary"><i class="fa fa-save"></i> Save Record</button>
			</div>
            </div>
            <!-- /.box-footer -->
          </div>
	</form>		  
		  		<SCRIPT language="javascript">
        function addRow(tableID) {

            var table = document.getElementById(tableID);

            var rowCount = table.rows.length;
            var row = table.insertRow(rowCount);

			var cell2 = row.insertCell(0);
            var element2 = document.createElement("input");
            element2.type = "text";
			element2.required = "required";
			element2.autocomplete="off";
			element2.size = "6";
            element2.name = "sample[]";
            cell2.appendChild(element2);
			
			<?php
			$z=1;
			if($line >1){
			for($i=1; $i<$line; $i++){
			?>
			var cell4 = row.insertCell(<?php echo $i; ?>);
            var element4 = document.createElement("input");
            element4.type = "text";
			element4.autocomplete = "off";
			element4.size = "6";
            element4.name = "result<?php echo $i; ?>[]";
            cell4.appendChild(element4);
			<?php
			$z++;
			}
			}
			?>

            var cell3 = row.insertCell(1);
            var element3 = document.createElement("input");
            element3.type = "text";
			element3.required = "required";
			element3.autocomplete = "off";
			element3.size = "6";
            element3.name = "result[]";
            cell3.appendChild(element3);
			
			
			row.insertCell(<?php echo $z+1; ?>).innerHTML= '<a href="#" onClick="Javacsript:deleteRow(this)"><?php echo '<img src="'.$RootPath.'/css/trash.png" title="' ._('Delete Row') . '" alt="" /></a>';?></a>';


        }

        function deleteRow(obj) {
      
    var index = obj.parentNode.parentNode.rowIndex;
    var table = document.getElementById("dataTable");
    table.deleteRow(index);
    
}

    </SCRIPT>

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