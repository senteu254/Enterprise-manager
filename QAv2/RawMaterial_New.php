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
$sql = "UPDATE qarawmatacceptance SET company='" . $_POST['company'] . "',
						country='" . $_POST['country'] ."',
						calibre='" . $_POST['calibre'] ."',
						date='" . FormatDateForSQL($_POST['date']) ."'
					WHERE id = '". $SelectedUser . "'";
					
$sqldel = "DELETE FROM qarawmatacceptancedata WHERE refno = '". $SelectedUser . "'";
$ErrMsg = _('The user alterations could not be processed because');
$DbgMsg = _('The SQL that was used to update the user and failed was');
$Rest = DB_query($sqldel,$ErrMsg,$DbgMsg,true);

	for($i=0;$i<count($_POST['lot']);$i++){
	$lot = $_POST['lot'][$i];
	$thick= $_POST['thickness'][$i];
	$width = $_POST['width'][$i];
	$outer = $_POST['outer'][$i];
	$inner = $_POST['inner'][$i];
	$hard = $_POST['hardness'][$i];
	if($_POST['wt'][$i]==""){
	$wt =0;
	}else{
	$wt = $_POST['wt'][$i];
	}
	$no = $i+1;	
	$SQL="INSERT INTO qarawmatacceptancedata (`num`, `refno`, `lot`, `thickness`, `width`, `outerdim`, `innerdim`, `hardness`, `wt`) 
							VALUES('". $no ."',
									'". $SelectedUser ."',
									'". $lot ."',
									'". $thick ."',
									'". $width ."',
									'". $outer ."',
									'". $inner ."',
									'". $hard ."',
									'". $wt ."')";
					$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		}
					
	$_SESSION['msg'] = _('The selected record has been updated successfully');
	$redirect = htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&Link=RawMatAcc';	
	} elseif ($InputError !=1) {
	//initialise no input errors assumed initially before we test
		$RequestNo = GetNextTransNo(84, $db);
		$sql = "INSERT INTO qarawmatacceptance (`id`, `calibre`, `country`, `company`, `date`,`technicianid`,inspectors)
					VALUES (" . $RequestNo . ",
						'" . $_POST['calibre'] ."',
						'" . $_POST['country'] ."',
						'" . $_POST['company'] ."',
						'" . FormatDateForSQL($_POST['date']) ."',
						'" . $_SESSION['UserID'] ."',
						'" . $_SESSION['UserID'] .",')";
	
	for($i=0;$i<count($_POST['lot']);$i++){
	$lot = $_POST['lot'][$i];
	$thick= $_POST['thickness'][$i];
	$width = $_POST['width'][$i];
	$outer = $_POST['outer'][$i];
	$inner = $_POST['inner'][$i];
	$hard = $_POST['hardness'][$i];
	if($_POST['wt'][$i]==""){
	$wt =0;
	}else{
	$wt = $_POST['wt'][$i];
	}
	$no = $i+1;	
	$SQL="INSERT INTO qarawmatacceptancedata (`num`, `refno`, `lot`, `thickness`, `width`, `outerdim`, `innerdim`, `hardness`, `wt`) 
							VALUES('". $no ."',
									'". $RequestNo ."',
									'". $lot ."',
									'". $thick ."',
									'". $width ."',
									'". $outer ."',
									'". $inner ."',
									'". $hard ."',
									'". $wt ."')";
					$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		}
$_SESSION['msg'] = _('A new record has been inserted Successfully');
$redirect = htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&Link=RawMatAccRead&ID='.$RequestNo.'&New=Yes';
	}

	if ($InputError!=1){
		//run the SQL from either of the above possibilites
		$ErrMsg = _('The user alterations could not be processed because');
		$DbgMsg = _('The SQL that was used to update the user and failed was');
		$result = DB_query($sql,$ErrMsg,$DbgMsg);
  

		unset($_POST['company']);
		unset($_POST['calibre']);
		unset($_POST['country']);
		unset($_POST['date']);
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

echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&amp;Link=NewRawMat" id="form">
	<div>
	<br />
	<table class="table">
		<tr>';
		
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

#############################################################################################

if (isset($SelectedUser)) {
	//editing an existing User

	$sql = "SELECT *
		FROM qarawmatacceptance
		WHERE id='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['id'] = $myrow['id'];
	$_POST['date'] = $myrow['date'];
	$_POST['calibre'] = $myrow['calibre'];
	$_POST['country'] = $myrow['country'];
	$_POST['company'] = $myrow['company'];


	echo '<input type="hidden" name="SelectedUser" value="' . $SelectedUser . '" />';
	echo '<input type="hidden" name="UserID" value="' . $_POST['id'] . '" />';

	echo '<tr>
				<td>' . _('Record No') . ':</td>
				<td colspan="3">' . $_POST['id'] . '</td>
			</tr>';
echo '<center><a class="btn btn-default" href="' . $RootPath . '/index.php?Application=QA&Link=RawMatAcc">' . _('Back to Main Menu') . '</a></center>';
}
#############################################################################################

	echo '<td>' .  _('Calibre')  . '</td>
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
		<td>' . _('Country of Origin') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" name="country" value="'.$_POST['country'].'" /></td>
		</tr>
		<tr>
		<td>' .  _('Company Name') . '</td>';
	echo '<td><input type="text" autofocus="autofocus" name="company" value="'.$_POST['company'].'" /></td>';

echo '<td>' . _('Date') . '</td>
			<td><input type="text" autofocus="autofocus" alt="'.$_SESSION['DefaultDateFormat'].'" required="required" class="date" name="date" value="'.Date($_SESSION['DefaultDateFormat']).'" /></td>
		</tr>
	</tr>
	</table>
	<br/>';

echo '</div>';

if (isset($SelectedUser)) {
	//editing an existing User

	$sql = "SELECT *
		FROM qarawmatacceptancedata
		WHERE refno='" . $SelectedUser . "'";

	$result3 = DB_query($sql);
echo '<table id="dataTable" class="table">';
echo '<tr>
		<th>' .  _('Brass Lot No'). '</th>
		<th colspan="2"><center>' .  _('Strip Dimension'). '</center></th>
		<th colspan="2"><center>' .  _('Strip Dimension'). '</center></th>
		<th>' .  _('Hardness'). '</th>
		<th>' .  _('WT.'). '</th>
		<th></th>
	</tr>
	<tr>
		<th></th>
		<th>' .  _('Thickness'). '</th>
		<th>' .  _('Width'). '</th>
		<th>' .  _('Outer'). '</th>
		<th>' .  _('Inner'). '</th>
		<th></th>
		<th></th>
		<th></th>
	</tr>';
while($myro = DB_fetch_array($result3)){
	echo '<tr>
			<td><input type="text" size="10" maxlength="10" required autofocus="autofocus"  class="integer" autocomplete="off" name="lot[]" value="'.$myro['lot'].'" /></td>
			<td><input type="text" name="thickness[]" size="4" required maxlength="10" autofocus="autofocus" autocomplete="off" value="'.$myro['thickness'].'" /></td>
			<td><input type="text" name="width[]" size="4" required maxlength="10" autofocus="autofocus" autocomplete="off" value="'.$myro['width'].'" /></td>
			<td><input type="text" name="outer[]" size="4" required maxlength="10" autofocus="autofocus" autocomplete="off" value="'.$myro['outerdim'].'" /></td>
			<td><input type="text" name="inner[]" size="4" required maxlength="10" autofocus="autofocus" autocomplete="off" value="'.$myro['innerdim'].'" /></td>
			<td><input type="text" name="hardness[]" size="4" maxlength="10" autofocus="autofocus" autocomplete="off" value="'.$myro['hardness'].'" /></td>
			<td><input type="text" name="wt[]" size="3" maxlength="10" autofocus="autofocus" autocomplete="off"  value="'.$myro['wt'].'" /></td>
			<td><a href="#" onClick="Javacsript:deleteRow(this)"><img src="'.$RootPath.'/css/trash.png" title="' ._('Delete Row') . '" alt="" /></a></a></td>
		</tr>';
	}
echo '</table>';
echo '<center><input name="" type="button" class="btn btn-default" onclick=addRow("dataTable") title="' ._('Add New Row') . '" value="Add New Row" /></center>';	
}else{
echo '<table id="dataTable" class="table" style="width:100%">';
echo '<tr>
		<th>' .  _('Brass Lot No'). '</th>
		<th colspan="2"><center>' .  _('Strip Dimension'). '</center></th>
		<th colspan="2"><center>' .  _('Strip Dimension'). '</center></th>
		<th>' .  _('Hardness'). '</th>
		<th>' .  _('WT.'). '</th>
		<th></th>
	</tr>
	<tr>
		<th></th>
		<th>' .  _('Thickness'). '</th>
		<th>' .  _('Width'). '</th>
		<th>' .  _('Outer'). '</th>
		<th>' .  _('Inner'). '</th>
		<th></th>
		<th></th>
		<th></th>
	</tr>
	';

	echo '<tr>
			<td><input type="text" size="10" maxlength="10" required autofocus="autofocus"  class="integer" autocomplete="off" name="lot[]" value="" /></td>
			<td><input type="text" name="thickness[]" size="4" required maxlength="10" autofocus="autofocus" autocomplete="off" /></td>
			<td><input type="text" name="width[]" size="4" required maxlength="10" autofocus="autofocus" autocomplete="off" /></td>
			<td><input type="text" name="outer[]" size="4" required maxlength="10" autofocus="autofocus" autocomplete="off" /></td>
			<td><input type="text" name="inner[]" size="4" required maxlength="10" autofocus="autofocus" autocomplete="off" /></td>
			<td><input type="text" name="hardness[]" size="4" maxlength="10" autofocus="autofocus" autocomplete="off" /></td>
			<td><input type="text" name="wt[]" size="3" maxlength="10" autofocus="autofocus" autocomplete="off"  /></td>
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
            element2.name = "lot[]";
            cell2.appendChild(element2);

            var cell3 = row.insertCell(1);
            var element3 = document.createElement("input");
            element3.type = "text";
			element3.required = "required";
			element3.autocomplete = "off";
			element3.size = "4";
            element3.name = "thickness[]";
            cell3.appendChild(element3);
			
			var cell4 = row.insertCell(2);
            var element4 = document.createElement("input");
			element4.type = "text";
			element4.required = "required";
			element4.autocomplete="off";
			element4.size = "4";
            element4.name = "width[]";
            cell4.appendChild(element4);
			
			var cell4 = row.insertCell(3);
            var element4 = document.createElement("input");
			element4.type = "text";
			element4.required = "required";
			element4.autocomplete="off";
			element4.size = "4";
            element4.name = "outer[]";
            cell4.appendChild(element4);
			
			var cell4 = row.insertCell(4);
            var element4 = document.createElement("input");
			element4.type = "text";
			element4.required = "required";
			element4.autocomplete="off";
			element4.size = "4";
            element4.name = "inner[]";
            cell4.appendChild(element4);
			
			var cell4 = row.insertCell(5);
            var element4 = document.createElement("input");
			element4.type = "text";
			element4.autocomplete="off";
			element4.size = "4";
            element4.name = "hardness[]";
            cell4.appendChild(element4);
			
			var cell4 = row.insertCell(6);
            var element4 = document.createElement("input");
			element4.type = "text";
			element4.autocomplete="off";
			element4.size = "3";
            element4.name = "wt[]";
            cell4.appendChild(element4);
			
			row.insertCell(7).innerHTML= '<a href="#" onClick="Javacsript:deleteRow(this)"><?php echo '<img src="'.$RootPath.'/css/trash.png" title="' ._('Delete Row') . '" alt="" /></a>';?></a>';


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