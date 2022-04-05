
<?php

$tab = (isset($_GET['Tab']) && $_GET['Tab'] != '') ? $_GET['Tab'] : '';

	if (isset($_POST['Submit'])) {
	$InputError=0;
	if ($_POST['level']=='') {
		prnMsg( _('You must input an authoriser'), 'error');
		$InputError=1;
	}
	
if ($InputError !=1) {

		$sql = "INSERT INTO irq_approvers (approver_name)
				VALUES (
						'" . $_POST['level'] . "')";
		$msg = _('The Approver has been added successfully');
	}

	if ($InputError !=1) {
		$result = DB_query($sql);
		prnMsg($msg, 'success');
	}
	}
	
	if (isset($_POST['Update'])) {
	$InputError=0;
	if ($_POST['level']=='') {
		prnMsg( _('You must input an authoriser'), 'error');
		$InputError=1;
	}
	if(isset($_POST['question']) && $_POST['question']=='HOD'){
	$userid='HOD';
	}elseif(isset($_POST['question']) && $_POST['question']=='ISSUE'){
	$userid='ISSUE';
	}elseif(isset($_POST['question']) && $_POST['question']=='PROCURE'){
	$userid='PROCURE';
	}else{
	$userid=$_POST['user'];
	}
	
if ($InputError !=1) {

		$sql = "UPDATE irq_approvers SET approver_name='" . $_POST['level'] . "', userid='" . $userid . "' WHERE approver_id='".$_POST['appvrid']."'";
				
		$msg = _('The Approver has been updated successfully');
	}

	if ($InputError !=1) {
		$result = DB_query($sql);
		echo '<br />';
		prnMsg($msg, 'success');
	}
	}
		
	if (isset($_GET['Edit']) && $_GET['Edit']==1) {
	if (is_numeric($_GET['Appid'])){
	$appid = $_GET['Appid'];
	$InputError=0;
	}else{
	prnMsg( _('Invalid Request'), 'error');
	$InputError=1;
	}
	$sql="SELECT approver_id,
				userid,
				approver_name
			FROM irq_approvers
			WHERE approver_id='".$appid."'";
			$result=DB_query($sql);
			while ($myrow=DB_fetch_array($result)){
			$appname = $myrow['approver_name'];
			$userid = $myrow['userid'];
			$appvrid = $myrow['approver_id'];
			}
	
	}
	
	if(isset($_GET['Del']) && $_GET['Del']==1){
	if (is_numeric($_GET['Appid'])){
	$appid = $_GET['Appid'];
	$InputError=0;
	}else{
	prnMsg( _('Invalid Request'), 'error');
	$InputError=1;
	}
$result=DB_query("DELETE FROM irq_approvers WHERE approver_id='" .$appid. "'");
if($result==true){
prnMsg('<ul class="states"><li class="succes">Success: Level Deleted Successfully from the Workflow</li></ul>', 'success');
	}else{
	prnMsg('<ul class="states"><li class="error">Error: '.DB_error().'</li></ul>', 'error');

	}
}
echo '<link href="' . $RootPath . '/css/'. $_SESSION['Theme'] .'/default.css" rel="stylesheet" type="text/css" />';
echo '<link href="' . $RootPath . '/facebox/src/facebox.css" rel="stylesheet" type="text/css" />';
echo '<script src="' . $RootPath . '/facebox/src/facebox.js" type="text/javascript"></script>';

?>

			<script type="text/javascript">
				jQuery(document).ready(function($) {
					$(" a[rel*=facebox]" ).facebox({
						loadingImage : "facebox/src/loading.gif" ,
						closeImage   : "facebox/src/closelabel.png" 
					})
				})
	</script>

<style type="text/css">
.process {
    background-color:;
	background:linear-gradient( #996699, #0066FF);
	margin:0 auto;    
   text-align:center;
	color:#FFFFFF;
	cursor:move;
	font-weight:bold;
	padding-top:8px;
	padding-bottom:8px;
    width:25%;
	min-height: 15px;
	
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
.start {
	background:linear-gradient( #99CC00, #996666);
	margin:0 auto;
	text-align:center;
	color:#FFFFFF;
	cursor:move;
	font-weight:bold;
	vertical-align: middle;
	padding-top:8px;
	padding-bottom:8px;
    width:90px;
	min-height: 15px;
    /*border-collapse: collapse;*/
    border:thin outset #B3B3B3;
    
    -webkit-border-radius:  15px;
    -moz-border-radius:     15px;
    border-radius:          15px;
    -moz-box-shadow:    1px 1px 2px #C3C3C3;
	-webkit-box-shadow: 1px 1px 2px #C3C3C3;
	box-shadow:         1px 1px 2px #C3C3C3;
	-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')";
	filter: progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')
}
.end {
	background:linear-gradient( #FF0000, #FF6633);
	margin:0 auto;
	text-align:center;
	color:#FFFFFF;
	cursor:move;
	font-weight:bold;
	vertical-align: middle;
	padding-top:8px;
	padding-bottom:8px;
    width:90px;
	min-height: 15px;
    /*border-collapse: collapse;*/
    border:thin outset #B3B3B3;
    
     -webkit-border-radius:  15px;
    -moz-border-radius:     15px;
    border-radius:          15px;
    -moz-box-shadow:    1px 1px 2px #C3C3C3;
	-webkit-box-shadow: 1px 1px 2px #C3C3C3;
	box-shadow:         1px 1px 2px #C3C3C3;
	-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')";
	filter: progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')
}

.arrow{
		background:url(images/arrows.png) no-repeat;
		height:38px;
		width:17px;
		cursor:move;
		margin:0 auto;
}
.decision{
		background:url(images/decision.png) no-repeat;
		
		background-position:right;
		height:113px;
		width:280px;
		cursor:move;
		margin:0 auto;
}

.addapp{
	background-color:#FFFFFF;
	margin:0 auto;
	text-align:center;
	color:#000066;
	cursor:pointer;
	font-weight:bold;
	vertical-align: middle;
	padding-top:8px;
    width:100px;
	min-height: 25px;
    /*border-collapse: collapse;*/
    border:thin outset #B3B3B3;
    
    -webkit-border-radius:  1px;
    -moz-border-radius:     1px;
    border-radius:          1px;
    -moz-box-shadow:    1px 1px 2px #C3C3C3;
	-webkit-box-shadow: 1px 1px 2px #C3C3C3;
	box-shadow:         1px 1px 2px #C3C3C3;
	-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')";
	filter: progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')
}
#tabs {
    margin: 0;
    overflow: hidden;
    padding: 0;
    zoom: 1;
    position:relative;
    top:2px;
    z-index: 1;
}
 
#tabs li {
    display: block;
    list-style: none;
    margin: 0;
    margin-right: 1px;
    padding: 0;
    float: left;
}
 
#tabs li a {
    display: block;
    padding: 2px 10px;
    border: 2px solid #817bfc;
	border-radius:5px 5px 0 0;
    border-bottom: 0 none;
    text-align: center;
    text-decoration: none;
}
 
.tab-section {
    background:#FFFFF;
    padding: 10px;
    border: 2px solid #817bfc;
	border-radius:0 5px 5px 5px;
}

.current2 {
    background:#FFFFFF;
    color: #000;
    border-bottom: 2px solid #d4efff;
}

</style>

<div align="left" style="width:100%">

<br />

<ul id="tabs">
    <li><a <?php echo ($tab==1)? 'class="current2"' : ''; ?> href="<?php echo $mainlink; ?>ManageTasks&Tab=1">Manage Approvers</a></li>
    <li><a <?php echo ($tab==2)? 'class="current2"' : ''; ?> href="<?php echo $mainlink; ?>ManageTasks&Tab=2">Documents</a></li>
</ul>
 <?php
 if($tab==1){
 ?>
<div id="java" class="tab-section">
   <!--start tab-->
    <div style="width:100%" class="centre">
	<form action="" method="post" enctype="multipart/form-data" target="_parent">
	<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
	<table>
  <tr>
    <th colspan="2"><h4>Manage Users/Roles</h4></th>
  </tr>
  <tr>
    <td><div align="center" style="font-weight:bold">Role : </div></td>
    <td><input name="level" value="<?php echo (isset($appname)) ? $appname : ''; ?>" type="text" /></td>
  </tr>
  <script type="text/javascript">
 $().ready(function(){
if($(".coupon_question").is(":checked")) {
		 $(".answer").hide();
    } else {
        $(".answer").show(300);
    }
	
$(".answers").change(function() {
		 //$(".answer").hide(200);
		 $(".issue").hide(200);
		 $(".PROCURE").hide(200);

});
</script>
  <?php
  if(isset($_GET['Edit']) && $_GET['Edit']=1 && $InputError ==0){
  echo '<input type="hidden" name="appvrid" value="' . $appvrid . '" />';
  echo '<tr><td></td><td class="hod"><input class="coupon_question"';
  echo ($userid =='HOD') ? 'checked="true"' : '';
  echo ' type="radio" name="question" value="HOD"/> <strong> Is Role HOD?</strong></td></tr>';
  
  echo '<tr><td></td><td class="issue"><input class="coupon_question"';
  echo ($userid =='ISSUE') ? 'checked="true"' : '';
  echo ' type="radio" name="question" value="ISSUE"/> <strong> Is Role ISSUER?</strong></td></tr>';
  
   echo '<tr><td></td><td class="PROCURE"><input class="coupon_question"';
  echo ($userid =='PROCURE') ? 'checked="true"' : '';
  echo ' type="radio" name="question" value="PROCURE"/> <strong> Is Role PROCURE?</strong></td></tr>';
  
  echo '<tr><td></td><td class="issue"><input class="coupon_question"';
  echo ($userid !='' && $userid !='ISSUE' && $userid !='HOD' && $userid !='PROCURE') ? 'checked="true"' : '';
  echo ' type="radio" name="question" value="USER"/> <strong> Is Role USER?</strong></td></tr>';
  echo '<tr><td>';
				$st = "SELECT * FROM www_users";
				$res=DB_query($st);
			echo '<div class="answer" align="center" style="font-weight:bold">User : </div></td>';
			echo '<td><select class="answers" name="user" >';
			echo '<option selected="selected">--Select User--</option>';
				while($row=DB_fetch_array($res))
				{
			echo '<option value="'.$row['userid'].'"';
			echo ($row['userid'] == $userid) ? ' selected="selected"' : '';
			echo '>'.$row['userid'].'</option>';
				}
			echo '</select>';
			echo '</td></tr>';
			echo '<tr> <td colspan="2"><div class="centre"><input type="submit" value="' . _('Update') . '" name="Update"/></div></td></tr>';
  
  }else{
  ?>
  <tr>
  <td colspan="2"><div class="centre">
		<input type="submit" name="Submit" value="Submit" />
  </div></td>
  </tr>
  <?php } ?>
</table>
</form>
</div>
<br />
<div style="width:100%" class="centre">
	<table style="width:100%" class="table table-hover table-striped">
  <tr>
    <th width="100px">Level ID</th>
	<th>Role</th>
	<th>User</th>
	<th colspan="2"></th>
  <?php
  $sql="SELECT approver_id,
				userid,
				approver_name
			FROM irq_approvers
			ORDER BY approver_id";
			$result=DB_query($sql);
			while ($myrow=DB_fetch_array($result)){
  ?>
    </tr>
    <td><?php echo $myrow['approver_id']; ?></td>
    <td><?php echo $myrow['approver_name']; ?></td>
	<td align="center"><?php echo $myrow['userid']; ?></td>
	<td width="20px"><a href="<?php echo $mainlink.'ManageTasks&Tab=1&Edit=1&Appid='.$myrow['approver_id']; ?>" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></td>
	<td width="30px"><a onclick="return confirm('Are you sure you want to Delete this Approver?');" href="<?php echo $mainlink.'ManageTasks&Tab=1&Del=1&Appid='.$myrow['approver_id']; ?>" title="Delete" style="color:#FF0000;"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td>
  </tr>
  <?php
  }
  ?>
</table>

</div>	
   
	<!--end tab-->
</div>
<?php
}else{
?>
<div id="php" class="tab-section">
    <!--start tab2-->
	<div style="width:100%" class="centre">
	<table style="width:100%" class="table table-hover table-striped">
	<tr>
    <th colspan="3">List of Documents Commonly Used</th>
  </tr>
  <tr>
    <th width="100px">Document ID</th>
	<th>Document Name</th>
	<th></th>
	  <?php
  $sql="SELECT doc_id,
				doc_name
			FROM irq_documents
			ORDER BY doc_id";
			$result=DB_query($sql);
			while ($myrow=DB_fetch_array($result)){
  ?>
    </tr>
    <td><?php echo $myrow['doc_id']; ?></td>
    <td><?php echo $myrow['doc_name']; ?></td>
	<td width="50px"><a href="<?php echo $mainlink; ?>ManageTasks&Tab=2&Doc=<?php echo $myrow['doc_id']; ?>" title="Edit WorkFlow"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></td>
  </tr>
  <?php
  }
  ?>
</table>
</div>
<br />
<?php
if(isset($_GET['Doc']) && $_GET['Doc'] != '' && is_numeric($_GET['Doc'])){
?>
<div style="width:80%" class="centre">
<?php
  $sql="SELECT doc_id,
				doc_name
			FROM irq_documents
			WHERE doc_id='".$_GET['Doc']."'";
			$result=DB_query($sql);
			while ($myrow=DB_fetch_array($result)){
			$name=$myrow['doc_name'];
			$id=$myrow['doc_id'];
			}
  ?>
<table style="width:70%; border:none;">
  <tr>
    <th colspan="2"><h4><?php echo $name; ?></h4></th>
  </tr>
  <tr>
    <td>
	<?php
	$sql="SELECT * FROM irq_levels a INNER JOIN irq_approvers b ON a.approver_id=b.approver_id WHERE a.doc_id='" . $id . "' ORDER BY a.level_id ASC";

	$DbgMsg = _('The SQL that was used to retrieve the information was');
	$ErrMsg = _('Could not check whether the level exists because');
	$result=DB_query($sql,$ErrMsg,$DbgMsg);
	$num=DB_num_rows($result);
	if($num !=''){
	echo '<div class="start">Start (Initiator)</div>';
	echo "<div class=arrow></div>";
	while ($myrow=DB_fetch_array($result)){
	if($myrow['final_approver']==0){
	echo '<a style="text-decoration:none;" href="Add_Level.php?Doc=' .$id .'&AppId='.$myrow['level_id'].'" rel="facebox"><div class="process">'.$myrow['approver_name'].'</div></a>';
	echo "<div class=arrow></div>";
	}
	$decision = $myrow['decision'];
	$end = $myrow['final_approver'];
	if($decision==1 && $end==0){
	echo "<div class=decision></div>";
	}
	$end = $myrow['final_approver']; 
	$appr = $myrow['approver_name'];
	$lev = $myrow['level_id'];
	}
	if($end==1){
	if($decision==1){
	echo '<a style="text-decoration:none;" href="Add_Level.php?Doc=' .$id .'&AppId='.$lev.'" rel="facebox"><div class="process">'.$appr.'</div></a>';
	echo "<div class=arrow></div>";
	echo "<div class=decision></div>";
	echo '<div style="width:50px; border-radius:30px; padding-top:15px; font-size:10px; height:27px;" class="start">Approved</div>';
	}else{
	echo '<a style="text-decoration:none;" href="Add_Level.php?Doc=' .$id .'&AppId='.$lev.'" rel="facebox"><div class="end">'.$appr.'</div></a>';
	}
	}else{
	echo '<a style="text-decoration:none;" href="Add_Level.php?Doc='. $id .'" rel="facebox"><div class="addapp">Add Approver</div></a>';
	}
	}else{	
	echo '<a style="text-decoration:none;" href="Add_Level.php?Doc='. $id .'" rel="facebox"><div class="addapp">start</div></a>';
	}
	?>
	
	</td>
  </tr>
</table>
</div>	
<?php
	}
	?>
	<!--end tab2-->
</div>	
<?php
}
?>
</div>

