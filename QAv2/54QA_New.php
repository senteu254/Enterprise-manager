<script type="text/javascript" src="js/qsearch.js"></script>
<script type="text/javascript" src="js/webcam.js"></script>
<?php
if (isset($_GET['SelectedUser'])){
	$SelectedUser = $_GET['SelectedUser'];
} elseif (isset($_POST['SelectedUser'])){
	$SelectedUser = $_POST['SelectedUser'];
}
$resl = DB_query("SELECT accept_msg,reject_msg FROM qa_approval_levels
						WHERE type = 3 AND levelcheck=0",	$db);
	$myl = DB_fetch_row($resl);	
if (isset($_POST['Submit'])) {
$InputError = 0;

if (isset($SelectedUser)) {
$sql = "UPDATE qadailyreport SET calibre='" . $_POST['calibre'] ."',
		cartlotno='" . $_POST['cartlotno'] ."',
		detcharge='" . $_POST['detcharge'] ."',
		powderlotno='" . $_POST['powderlotno'] ."',
		bulletmass='" . $_POST['bulletmass'] ."',
		velocity='" . $_POST['velocity'] ."',
		mouthpressure='" . $_POST['mouthpressure'] ."',
		portpressure='" . $_POST['portpressure'] ."',
		mercurous='" . $_POST['mercurous'] ."',
		watertightness='" . $_POST['watertightness'] ."',
		bulletproduction='" . $_POST['bproduction'] ."',
		bulletproductionlot='" . $_POST['bproductionlot'] ."',
		loading='" . $_POST['loading'] ."',
		loadinglot='" . $_POST['loadinglot'] ."',
		bextraction='" . $_POST['bextraction'] ."',
		bextractionlot='" . $_POST['bextractionlot'] ."',
		ratefire='" . $_POST['ratefire'] ."',
		ratefirelot='" . $_POST['ratefirelot'] ."',
		sensitivity='" . $_POST['sensitivity'] ."',
		sensitivityat3='" . $_POST['sensitivity3'] ."',
		sensitivitylot='" . $_POST['sensitivitylot'] ."'
					WHERE id = '". $SelectedUser . "'";
					
	$_SESSION['msg'] = _('The selected report has been updated successfully');
	$redirect = htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&Link=54QA';
		
	} elseif ($InputError !=1) {
	//initialise no input errors assumed initially before we test
		$RequestNo = GetNextTransNo(82, $db);
		$sql = "INSERT INTO `qadailyreport`(`id`,`calibre`, `cartlotno`, `detcharge`, `powderlotno`, `bulletmass`, `velocity`, `mouthpressure`, `portpressure`, `mercurous`, `watertightness`, `bulletproduction`, `bulletproductionlot`, `loading`, `loadinglot`, `bextraction`, `bextractionlot`, `ratefire`, `ratefirelot`, `sensitivity`, `sensitivityat3`, `sensitivitylot`,technicianid) 
		VALUES ('" . $RequestNo ."', 
		'" . $_POST['calibre'] ."',
		'" . $_POST['cartlotno'] ."',
		'" . $_POST['detcharge'] ."',
		'" . $_POST['powderlotno'] ."',
		'" . $_POST['bulletmass'] ."',
		'" . $_POST['velocity'] ."',
		'" . $_POST['mouthpressure'] ."',
		'" . $_POST['portpressure'] ."',
		'" . $_POST['mercurous'] .' '. $_POST['mcheck'] ."',
		'" . $_POST['watertightness'] .' '. $_POST['wcheck'] ."',
		'" . $_POST['bproduction'] .' '. $_POST['bcheck'] ."',
		'" . $_POST['bproductionlot'] ."',
		'" . $_POST['loading'] .' '. $_POST['lcheck'] ."',
		'" . $_POST['loadinglot'] ."',
		'" . $_POST['bextraction'] ."',
		'" . $_POST['bextractionlot'] ."',
		'" . $_POST['ratefire'] ."',
		'" . $_POST['ratefirelot'] ."',
		'" . $_POST['sensitivity'] ."',
		'" . $_POST['sensitivity3'] ."',
		'" . $_POST['sensitivitylot'] ."',
		'" . $_SESSION['UserID'] ."'
		)";
		
		/*$sql2 = "INSERT INTO qadailyreportremarks (`refid`, `remarks`,approver, approvertitle, `approvername`,action)
					VALUES (" . $RequestNo . ",
						'" . $_POST['remarks'] ."',
						1,
						'".$myl[0]."',
						'" . $_SESSION['UsersRealName'] ."','')";*/
		$_SESSION['msg'] = _('A new record has been inserted Successfully');
		$redirect = htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&Link=54QARead&ID='.$RequestNo.'&New=Yes';
	}

	if ($InputError!=1){
		//run the SQL from either of the above possibilites
		$ErrMsg = _('The user alterations could not be processed because');
		$DbgMsg = _('The SQL that was used to update the user and failed was');
		$result = DB_query($sql,$ErrMsg,$DbgMsg);

		unset( $_POST['calibre']);
		unset( $_POST['cartlotno']);
		unset($_POST['detcharge']);
		unset( $_POST['powderlotno'] );
		unset($_POST['bulletmass']);
		unset( $_POST['velocity']);
		unset($_POST['mouthpressure'] );
		unset($_POST['portpressure'] );
		unset($_POST['mercurous'] );
		unset($_POST['watertightness']);
		unset($_POST['bproduction']);
		unset($_POST['bproductionlot']);
		unset( $_POST['loading']);
		unset($_POST['loadinglot']);
		unset($_POST['bextraction']);
		unset( $_POST['bextractionlot']);
		unset($_POST['ratefire']);
		unset($_POST['ratefirelot'] );
		unset($_POST['sensitivity']);
		unset($_POST['sensitivity3']);
		unset($_POST['sensitivitylot']);
		unset($_POST['remarks']);
		unset($SelectedUser);
		
	echo "<script type=\"text/javascript\">
				window.location.href = '".$redirect."';
            </script>
        ";
	}
	
}
		
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


echo '<form method="post" action="" id="form">
	<div>
	<br />
	<table class="table">
		<tr>';
		
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

#############################################################################################

if (isset($SelectedUser)) {
	//editing an existing User

	$sql = "SELECT *
		FROM qadailyreport a
		WHERE a.id='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['id'] = $myrow['id'];
	$_POST['calibre']  = $myrow['calibre'];
	$_POST['cartlotno']  = $myrow['cartlotno'];
	$_POST['detcharge']  = $myrow['detcharge'];
	$_POST['powderlotno']   = $myrow['powderlotno'];
	$_POST['bulletmass']  = $myrow['bulletmass'];
	$_POST['velocity'] = $myrow['velocity'];
	$_POST['mouthpressure'] = $myrow['mouthpressure'];
	$_POST['portpressure'] = $myrow['portpressure'];
	$_POST['mercurous'] = $myrow['mercurous'];
	$_POST['watertightness'] = $myrow['watertightness'];
	$_POST['bproduction'] = $myrow['bulletproduction'];
	$_POST['bproductionlot'] = $myrow['bulletproductionlot'];
	$_POST['loading'] = $myrow['loading'];
	$_POST['loadinglot'] = $myrow['loadinglot'];
	$_POST['bextraction'] = $myrow['bextraction'];
	$_POST['bextractionlot'] = $myrow['bextractionlot'];
	$_POST['ratefire'] = $myrow['ratefire'];
	$_POST['ratefirelot'] = $myrow['ratefirelot'];
	$_POST['sensitivity'] = $myrow['sensitivity'];
	$_POST['sensitivity3'] = $myrow['sensitivityat3'];
	$_POST['sensitivitylot'] = $myrow['sensitivitylot'];
	$_POST['remarks'] = $myrow['remarks'];

	echo '<input type="hidden" name="SelectedUser" value="' . $SelectedUser . '" />';
	echo '<input type="hidden" name="UserID" value="' . $_POST['id'] . '" />';

	echo '<tr>
				<td>' . _('Record ID') . ':</td>
				<td>' . $_POST['id'] . '</td>
			</tr>';
echo '<center><a class="btn btn-default" href="' . $RootPath . '/index.php?Application=QA&amp;Link=54QA">' . _('Back to Main Menu') . '</a></center>';
}
#############################################################################################

	echo '<td>' . _('Calibre') . '</td>
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
		</tr><tr>
		<td>' . _('Cart. Lot No') . '</td>
			<td><input type="text" autofocus="autofocus" id="first" required="required" name="cartlotno" value="'.$_POST['cartlotno'].'" /></td>
		</tr>
		<td>' . _('Det Charge') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" name="detcharge" value="'.$_POST['detcharge'].'" /></td>
		</tr>
		<td>' . _('Powder Lot No') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" name="powderlotno" value="'.$_POST['powderlotno'].'" /></td>
		</tr>
		<tr>
		<td>' .  _('Bullet Mass') . '</td>';
	echo '<td><input type="text" autofocus="autofocus" name="bulletmass" value="'.$_POST['bulletmass'].'" /></td>';
		

echo '</td>
	</tr>
	</table>
	<br/>';

echo '</div>';

echo '<table id="dataTable" class="table" style="width:100%">';

echo '<tr>
	<td>' . _('Mean Velocity ') . '</td>
	<td><input type="text" autofocus="autofocus" required="required" name="velocity" value="'.$_POST['velocity'].'" /></td><td></td><td></td>
		</tr>';
echo '<tr>
	<td>' . _('Mean Mouth Pressure ') . '</td>
	<td><input type="text" autofocus="autofocus" required="required" name="mouthpressure" value="'.$_POST['mouthpressure'].'" /></td><td></td><td></td>
		</tr>';
echo '<tr>
	<td>' . _('Mean Port Pressure') . '</td>
	<td><input type="text" autofocus="autofocus" required="required" name="portpressure" value="'.$_POST['portpressure'].'" /></td><td></td><td></td>
		</tr>';
echo '<tr>
	<th colspan="4">' . _('Mean Accuracy (H+L)') . '</th>
		</tr>';
echo '<tr>
	<td>' . _('Bullet Production') . '</td>
	<td>
	'.(isset($SelectedUser) ? '<input type="text" autofocus="autofocus" required="required" name="bproduction" value="'.$_POST['bproduction'].'" />' :
	'<input type="text" autofocus="autofocus" size="8" required="required" name="bproduction" value="'.$_POST['bproduction'].'" />
	<select name="bcheck">
	<option value="Pass">Pass</option>
	<option value="Fail">Fail</option>
	</select>' ).
	'</td>
	<td>' . _('Lot No') . '</td>
	<td><input type="text" autofocus="autofocus" id="second" name="bproductionlot" value="'.$_POST['bproductionlot'].'" /></td>
		</tr>';
echo '<tr>
	<td>' . _('Loading (PC 530)') . '</td>
	<td>'
	.(isset($SelectedUser) ? '<input type="text" autofocus="autofocus" required="required" name="loading" value="'.$_POST['loading'].'" />' :
	'<input type="text" autofocus="autofocus" size="8" required="required" name="loading" value="'.$_POST['loading'].'" />
	<select name="lcheck">
	<option value="Pass">Pass</option>
	<option value="Fail">Fail</option>
	</select>'
	).
	'</td>
	<td>' . _('Lot No') . '</td>
	<td><input type="text" autofocus="autofocus" id="second1" name="loadinglot" value="'.$_POST['loadinglot'].'" /></td>
		</tr>';
echo '<tr>
	<td>' . _('Bullet Extraction Force') . '</td>
	<td><input type="text" autofocus="autofocus" required="required" name="bextraction" value="'.$_POST['bextraction'].'" /></td>
	<td>' . _('Lot No') . '</td>
	<td><input type="text" autofocus="autofocus" id="second2" name="bextractionlot" value="'.$_POST['bextractionlot'].'" /></td>
		</tr>';
echo '<tr>
	<td>' . _('Mercurous Nitrate Test') . '</td>
	<td colspan="3">'
	.(isset($SelectedUser) ? '<input type="text" autofocus="autofocus" required="required" name="mercurous" value="'.$_POST['mercurous'].'" />' :
	'<input type="text" autofocus="autofocus" size="8" required="required" name="mercurous" value="'.$_POST['mercurous'].'" />
	<select name="mcheck">
	<option value="Pass">Pass</option>
	<option value="Fail">Fail</option>
	</select>'
	).
	'</td>
		</tr>';
echo '<tr>
	<td>' . _('Water Tightness Test') . '</td>
	<td colspan="3">'
	.(isset($SelectedUser) ? '<input type="text" autofocus="autofocus" required="required" name="watertightness" value="'.$_POST['watertightness'].'" />' :
	'<input type="text" autofocus="autofocus" size="8" required="required" name="watertightness" value="'.$_POST['watertightness'].'" />
	<select name="wcheck">
	<option value="Pass">Pass</option>
	<option value="Fail">Fail</option>
	</select>'
	).
	'</td>
		</tr>';
echo '<tr>
	<td>' . _('Rate of Fire') . '</td>
	<td><input type="text" autofocus="autofocus" required="required" name="ratefire" value="'.$_POST['ratefire'].'" /></td>
	<td>' . _('Lot No') . '</td>
	<td><input type="text" autofocus="autofocus" id="second3" name="ratefirelot" value="'.$_POST['ratefirelot'].'" /></td>
		</tr>';
echo '<tr>
	<td>' . _('Primer Sensitivity') . ' At 3"</td>
	<td><input type="text" autofocus="autofocus" required="required" name="sensitivity3" value="'.$_POST['sensitivity3'].'" /></td>
	<td rowspan="2">' . _('Primer Lot No') . '</td>
	<td rowspan="2"><input type="text" autofocus="autofocus" name="sensitivitylot" value="'.$_POST['sensitivitylot'].'" /></td>
		</tr>';
echo '<tr>
	<td>' . _('Primer Sensitivity At 16"/14"/12"') . '</td>
	<td colspan="3"><input type="text" autofocus="autofocus" required="required" name="sensitivity" value="'.$_POST['sensitivity'].'" /></td>
		</tr>';
/*echo '<tr>
	<td>' . _('Final Remarks') . '</td>
	<td colspan="3"><textarea name="remarks" required="required" cols="43" rows="3">'.$_POST['remarks'].'</textarea></td>
		</tr>';*/
echo '</table>';

			?>
			 
            <!-- /.box-footer -->
            <div class="box-footer">
			 <div class="pull-right">

			<button type="submit" name="Submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Draft</button>
			</div>
            </div>
            <!-- /.box-footer -->
          </div>
	</form>		  

		<style type="text/css">
		.jj {
    border-radius: 2px;
	padding-right:5px;
	padding-left:5px;
	padding-bottom:2px;
	color:#FFFFFF;
	font-weight:bold;
    width: 35px;
	font-family:"Times New Roman", Times, serif;
}
		</style>
		
		<script type="text/javascript">
		$('#first').keyup(function(){
    $('#second').val(this.value);
	$('#second1').val(this.value);
	$('#second2').val(this.value);
	$('#second3').val(this.value);
});
$('#first').blur(function(){
    $('#second').val(this.value);
	$('#second1').val(this.value);
	$('#second2').val(this.value);
	$('#second3').val(this.value);
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