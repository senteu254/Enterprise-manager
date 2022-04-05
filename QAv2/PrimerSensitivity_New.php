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
$sql = "UPDATE qaprimersensitivity SET depthrange='" . $_POST['depthrange'] . "',
						test='" . $_POST['test'] ."',
						calibre='" . $_POST['calibre'] ."',
						primerlot='" . $_POST['primerlot'] ."',
						date='" . FormatDateForSQL($_POST['date']) ."'
					WHERE testno = '". $SelectedUser . "'";
					
$sqldel = "DELETE FROM qaprimersensitivitydata WHERE testno = '". $SelectedUser . "'";
$ErrMsg = _('The user alterations could not be processed because');
$DbgMsg = _('The SQL that was used to update the user and failed was');
$Rest = DB_query($sqldel,$ErrMsg,$DbgMsg,true);

	for($i=0;$i<count($_POST['height']);$i++){
	$sno= $_POST['height'][$i];
	$qty = $_POST['fired'][$i];
	$desc = $_POST['misfired'][$i];
	$no = $i+1;	
	$SQL="INSERT INTO qaprimersensitivitydata (num,
									testno,
									height,
									fired,
									misfired) 
							VALUES('". $no ."',
									'". $SelectedUser ."',
									'". $sno ."',
									'". $qty ."',
									'". $desc ."')";
					$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		}
					
	$_SESSION['msg'] = _('The selected record has been updated successfully');
	$redirect = htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&Link=PrimerSensitivity';	
	} elseif ($InputError !=1) {
	//initialise no input errors assumed initially before we test
		$RequestNo = GetNextTransNo(81, $db);
		$sql = "INSERT INTO qaprimersensitivity (`testno`, 
										`test`,
										`depthrange`,
										`calibre`, 
										`primerlot`,
										`date`,
										technicianid,
										process_level,
										remarks,
										remarker,
										remarkstime)
					VALUES (" . $RequestNo . ",
						'" . $_POST['test'] ."',
						'" . $_POST['depthrange'] ."',
						'" . $_POST['calibre'] ."',
						'" . $_POST['primerlot'] ."',
						'" . FormatDateForSQL($_POST['date']) ."',
						'" . $_SESSION['UserID'] ."',
						0,
						'',
						'".$_SESSION['UsersRealName']."',
						'".date('Y-m-d H:i:s')."')";
	
	for($i=0;$i<count($_POST['height']);$i++){
	$sno= $_POST['height'][$i];
	$qty = $_POST['fired'][$i];
	$desc = $_POST['misfired'][$i];
	$no = $i+1;	
	$SQL="INSERT INTO qaprimersensitivitydata (num,
									testno,
									height,
									fired,
									misfired) 
							VALUES('". $no ."',
									'". $RequestNo ."',
									'". $sno ."',
									'". $qty ."',
									'". $desc ."')";
					$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		}
$_SESSION['msg'] = _('A new record has been inserted Successfully');
$redirect = htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&Link=PrimerSensitivityRead&ID='.$RequestNo.'&New=Yes';
	}

	if ($InputError!=1){
		//run the SQL from either of the above possibilites
		$ErrMsg = _('The user alterations could not be processed because');
		$DbgMsg = _('The SQL that was used to update the user and failed was');
		$result = DB_query($sql,$ErrMsg,$DbgMsg);
  

		unset($_POST['depthrange']);
		unset($_POST['calibre']);
		unset($_POST['primerlot']);
		unset($_POST['date']);
		unset($_POST['test']);
		unset($SelectedUser);
		echo "<script type=\"text/javascript\">
				window.location.href = '".$redirect."';
            </script>";
	}
	
}
			
echo '<div id="loadingbackground">';
		echo '<div id="progressBar" ><br />
			 <i class="fa fa-spinner fa-pulse fa-4x fa-fw"></i><br>PROCESSING. PLEASE WAIT...
			</div>';
		echo '</div>';	  

echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&amp;Link=NewPrimerSen" id="form">
	<div>
	<br />
	<table class="table">
		<tr>';
		
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

#############################################################################################

if (isset($SelectedUser)) {
	//editing an existing User

	$sql = "SELECT *
		FROM qaprimersensitivity
		WHERE testno='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['id'] = $myrow['testno'];
	$_POST['date'] = $myrow['date'];
	$_POST['calibre'] = $myrow['calibre'];
	$_POST['primerlot'] = $myrow['primerlot'];
	$_POST['depthrange'] = $myrow['depthrange'];
	$_POST['test'] = $myrow['test'];


	echo '<input type="hidden" name="SelectedUser" value="' . $SelectedUser . '" />';
	echo '<input type="hidden" name="UserID" value="' . $_POST['id'] . '" />';

	echo '<tr>
				<td>' . _('Record No') . ':</td>
				<td>' . $_POST['id'] . '</td>
			</tr>';
echo '<center><a class="btn btn-default" href="' . $RootPath . '/index.php?Application=QA&Link=PrimerSensitivity">' . _('Back to Main Menu') . '</a></center>';
}
#############################################################################################

	echo '<td>' . _('Test No') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" name="test" value="'.$_POST['test'].'" /></td>
		</tr>
	<tr>
	<td>' . _('Calibre') . '</td>
			<td><select name="calibre" required="required">';
     $SQL = "SELECT calibre	FROM wocalibre";
				 $result=DB_query($SQL);
  echo '<option selected="selected" value="">--Select Calibre--</option>';
  while ($myrow4=DB_fetch_array($result)){
	if (isset($_POST['calibre']) AND  $myrow4['calibre']==$_POST['calibre']){
		echo '<option selected="selected" value="'. $myrow4['calibre'] . '">' . $myrow4['calibre'] . '</option>';
	} else {
		echo '<option value="'. $myrow4['calibre'] . '">' . $myrow4['calibre'] . '</option>';
	}
}
  echo '</select></td>
		</tr>
		<tr>
		<td>' .  _('Primer Lot No.') . '</td>';
	echo '<td><input type="text" autofocus="autofocus" name="primerlot" value="'.$_POST['primerlot'].'" /></td>';

echo '</td></tr><tr>
<td>' . _('Primer Depth Range') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" name="depthrange" value="'.$_POST['depthrange'].'" /></td>
		</tr>
		<tr>
<td>' . _('Date') . '</td>
			<td><input type="text" autofocus="autofocus" alt="'.$_SESSION['DefaultDateFormat'].'" required="required" class="date" name="date" value="'.Date($_SESSION['DefaultDateFormat']).'" /></td>
		</tr>
	</tr>
	</table>
	<br/>';

echo '</div>';

if (isset($SelectedUser)) {
	//editing an existing User

	$sql = "SELECT *
		FROM qaprimersensitivitydata
		WHERE testno='" . $SelectedUser . "'";

	$result3 = DB_query($sql);
echo '<table id="dataTable" class="table">';
echo '<tr>
		<th>' .  _('Height')  . '</th>
		<th>' .  _('Fired'). '</th>
		<th>' .  _('MisFired'). '</th>
	</tr>';
while($myro = DB_fetch_array($result3)){
	echo '<tr>
			
			<td><input type="text" size="10" maxlength="20" autofocus="autofocus" required name="height[]" class="integer" value="'.$myro['height'].'" /></td>
			<td><input type="text" size="10" maxlength="10" autofocus="autofocus" required  class="integer" name="fired[]" value="'.$myro['fired'].'" /></td>
			<td><input type="text" name="misfired[]" size="10" maxlength="10" required autofocus="autofocus" class="integer" value="'.$myro['misfired'].'" /></td>
			<td><a href="#" onClick="Javacsript:deleteRow(this)"><img src="'.$RootPath.'/css/trash.png" title="' ._('Delete Row') . '" alt="" /></a></a></td>
		</tr>';
	}
echo '</table>';
echo '<center><input name="" type="button" class="btn btn-default" onclick=addRow("dataTable") title="' ._('Add New Row') . '" value="Add New Row" /></center>';	
}else{
echo '<table id="dataTable" class="table">';
echo '<tr>
		<th>' .  _('Height')  . '</th>
		<th>' .  _('Fired'). '</th>
		<th>' .  _('MisFired'). '</th>
	</tr>';

	echo '<tr>
			
			<td><input type="text" size="10" maxlength="20" required autofocus="autofocus" name="height[]" autocomplete="off" class="integer" value="" /></td>
			<td><input type="text" size="10" maxlength="10" required autofocus="autofocus"  class="integer" autocomplete="off" name="fired[]" value="" /></td>
			<td><input type="text" name="misfired[]" size="10" required maxlength="10" autofocus="autofocus" autocomplete="off" class="integer" /></td>
			<td><a href="#" onClick="Javacsript:deleteRow(this)"><img src="'.$RootPath.'/css/trash.png" title="' ._('Delete Row') . '" alt="" /></a></a></td>
		</tr>';

echo '</table>';	
	echo '<center><input name="" type="button" class="btn btn-default" onclick=addRow("dataTable") title="' ._('Add New Row') . '" value="Add New Row" /></center>';		
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
			element2.size = "10";
            element2.name = "height[]";
            cell2.appendChild(element2);

            var cell3 = row.insertCell(1);
            var element3 = document.createElement("input");
            element3.type = "text";
			element3.required = "required";
			element3.autocomplete = "off";
			element3.size = "10";
            element3.name = "fired[]";
            cell3.appendChild(element3);
			
			var cell4 = row.insertCell(2);
            var element4 = document.createElement("input");
			element4.type = "text";
			element4.required = "required";
			element4.autocomplete="off";
			element4.size = "10";
            element4.name = "misfired[]";
            cell4.appendChild(element4);
			
			row.insertCell(3).innerHTML= '<a href="#" onClick="Javacsript:deleteRow(this)"><?php echo '<img src="'.$RootPath.'/css/trash.png" title="' ._('Delete Row') . '" alt="" /></a>';?></a>';


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