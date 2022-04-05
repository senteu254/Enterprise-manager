<script type="text/javascript" src="js/qsearch.js"></script>
<script type="text/javascript" src="js/webcam.js"></script>
<?php
		
############################################################################################

if (isset($_GET['SelectedUser'])){
	$SelectedUser = $_GET['SelectedUser'];
} elseif (isset($_POST['SelectedUser'])){
	$SelectedUser = $_POST['SelectedUser'];
}

$resl = DB_query("SELECT accept_msg,reject_msg FROM qa_approval_levels
						WHERE type = 2 AND levelcheck=0",	$db);
	$myl = DB_fetch_row($resl);

if (isset($_POST['Submit'])) {
$InputError = 0;

if (isset($SelectedUser)) {
$sql = "UPDATE qanonconformingproducts SET machine='" . $_POST['machine'] . "',
						calibre='" . $_POST['calibre'] ."',
						lot='" . $_POST['lot'] ."',
						mc_setter='" . $_POST['setter'] ."',
						date='" . FormatDateForSQL($_POST['date']) ."'
					WHERE id = '". $SelectedUser . "'";
					
$sql2 = "UPDATE qanonconformingremarks SET remarks='" . $_POST['remarks'] . "',
						approvername='" . $_SESSION['UsersRealName'] ."'
					WHERE refid = '". $SelectedUser . "'";
					
	$_SESSION['msg'] = _('The selected record has been updated successfully');
		
	} elseif ($InputError !=1) {
	//initialise no input errors assumed initially before we test
		$RequestNo = GetNextTransNo(80, $db);
		$sql = "INSERT INTO qanonconformingproducts (`id`, 
										`machine`,
										`calibre`, 
										`lot`, 
										`mc_setter`,
										`date`,
										process_level,
										technicianid)
					VALUES (" . $RequestNo . ",
						'" . $_POST['machine'] ."',
						'" . $_POST['calibre'] ."',
						'" . $_POST['lot'] ."',
						'" . $_POST['setter'] ."',
						'" . FormatDateForSQL($_POST['date']) ."',
						1,
						'".$_SESSION['UserID']."')";
		$sql2 = "INSERT INTO qanonconformingremarks (`refid`, `remarks`,approver, approvertitle, `approvername`)
					VALUES (" . $RequestNo . ",
						'" . $_POST['remarks'] ."',
						1,
						'".$myl[0]."',
						'" . $_SESSION['UsersRealName'] ."')";
		$_SESSION['msg'] =  _('A new record has been Created Successfully and forwarded for Authoritation');

	}

	if ($InputError!=1){
		//run the SQL from either of the above possibilites
		$ErrMsg = _('The user alterations could not be processed because');
		$DbgMsg = _('The SQL that was used to update the user and failed was');
		$result = DB_query($sql,$ErrMsg,$DbgMsg);
		$result = DB_query($sql2,$ErrMsg,$DbgMsg);


		unset($_POST['machine']);
		unset($_POST['calibre']);
		unset($_POST['lot']);
		unset($_POST['date']);
		unset($_POST['remarks']);
		unset($_POST['setter']);
		unset($SelectedUser);
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
			  

echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&amp;Link=NewNonConformance" id="form">
	<div>
	<br />
	<table class="table" style="width:70%">
		<tr>';
		
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

#############################################################################################
if (isset($SelectedUser)) {
	//editing an existing User

	$sql = "SELECT *
		FROM qanonconformingproducts a
		INNER JOIN qanonconformingremarks b ON a.id=b.refid
		WHERE id='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['id'] = $myrow['id'];
	$_POST['machine'] = $myrow['machine'];
	$_POST['date'] = $myrow['date'];
	$_POST['calibre'] = $myrow['calibre'];
	$_POST['lot'] = $myrow['lot'];
	$_POST['remarks'] = $myrow['remarks'];
	$_POST['setter'] = $myrow['mc_setter'];


	echo '<input type="hidden" name="SelectedUser" value="' . $SelectedUser . '" />';
	echo '<input type="hidden" name="UserID" value="' . $_POST['id'] . '" />';

	echo '<tr>
				<td>' . _('Record ID') . ':</td>
				<td>' . $_POST['id'] . '</td>
			</tr>';
echo '<center><a class="btn btn-default" href="' . $RootPath . '/index.php?Application=QA&amp;Link=Non-Conformance">' . _('Back to Main Menu') . '</a></center>';
}
#############################################################################################

	echo '<td>' . _('Machine.') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" name="machine" value="'.$_POST['machine'].'" /></td>
		</tr>
		<tr>
		<td>' . _('Machine Setter') . '</td>
			<td><select name="setter" required="required">';
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
  echo '</select></td>
		</tr>
		<td>' . _('Date') . '</td>
			<td><input type="text" autofocus="autofocus" required="required" class="date" name="date" value="'.Date($_SESSION['DefaultDateFormat']).'" /></td>
		</tr>
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
		<td>' .  _('Lot No.') . '</td>';
	echo '<td><input type="text" autofocus="autofocus" name="lot" value="'.$_POST['lot'].'" /></td>';
		

echo '</td>
	</tr>
	</table>
	<br/>';

echo '</div>';

echo '<table id="dataTable" class="table" style="width:70%">';
echo '<tr>
		<th>' .  _('QAT Observation(s)/Details of Non-Conformance')  . '</th>
	</tr>';

echo '<tr>
	<td><textarea name="remarks" required="required" style="width:100%" rows="5">'.$_POST['remarks'].'</textarea></td>
		</tr>';
echo '</table>';

?>

			<br /> 
            <!-- /.box-footer -->
            <div class="box-footer">
			 <div class="pull-right">
			 <?php 
			  if (isset($SelectedUser)) {
				 ?>
			<button type="submit" name="Submit" class="btn btn-primary"><i class="fa fa-save"></i> Update Record</button>
			<?php }else{ ?>
			<button type="submit" name="Submit" onclick="return confirm('Are you sure you want to Forward this Request?')" class="btn btn-success"><i class="fa fa-share"></i> Forward</button>
			<?php } ?>
			</div>
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