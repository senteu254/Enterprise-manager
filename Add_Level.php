<?php
require_once('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
if(!isset($_GET['Doc']) && !isset($_GET['AppId'])){
die ('<div align="center">
<div style="border:groove; width:500px;">
<table width="100%" border="0">
  <tr>
    <td align="center" style="color:#FF0000; font-size:26px; font-weight:bold; text-decoration:underline;">Unauthorized Access!</td>
  </tr>
  <tr>
    <td align="center" style="font-weight:bold; font-size:24px; text-decoration:underline;">You do not Have Permission to access this Page</td>
  </tr>
  <tr>
    <td align="center">All fraudulent Attempts will be investigated and procecuted</td>
  </tr>
  <tr>
    <td align="center">in accordance with applicable law</td>
  </tr>
</table>
</div>
</div>');
exit;
}
if(is_numeric($_GET['Doc'])){
$id=$_GET['Doc'];
}
if(isset($_GET['AppId']) && is_numeric($_GET['AppId'])){
$Appid=$_GET['AppId'];
$sql="SELECT approver_id,decision FROM irq_levels WHERE level_id='$Appid' AND doc_id='$id'";
$result=DB_query($sql);
while($r=DB_fetch_array($result)){
$setid=$r['approver_id'];
$decision=$r['decision'];
}
$sqli="SELECT MAX(level_id) as MAXID FROM irq_levels WHERE doc_id='$id'";
$qw=DB_query($sqli);
while($max=DB_fetch_array($qw)){
$MAXID=$max['MAXID'];
}

}else{
$Appid='';
$setid='';
$decision='';
}

	echo '<div style="width:260px;">';
	echo '<form action="Add_Level_Save.php" method="post" enctype="multipart/form-data" target="_top">';
	echo '<table >
			<tr>
				<th>' . _('Please Select Approver of this level') . ':</th>
			</tr>';
			
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			echo '<input type="hidden" id="doc" name="doc" value="' . $id . '" />';
			echo '<input type="hidden" id="doc" name="Appid" value="' . $Appid . '" />';
			echo '<tr><td>';
				$st = "SELECT * FROM irq_approvers";
				$res=DB_query($st);
			echo 'Approver : <select required="true" name="state" id="state">';
			echo '<option selected="selected">--Select Approver--</option>';
				while($row=DB_fetch_array($res))
				{
			echo '<option value="'.$row['approver_id'].'"';
			echo ($row['approver_id'] == $setid) ? ' selected="selected"' : '';
			echo '>'.$row['approver_name'].'</option>';
				}
			echo '</select>';
			echo '</td></tr>';
			echo '<tr><td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="decision" ';
			echo ($decision == 1) ? ' checked="true"' : '';
			echo 'type="checkbox" value="1" /> Makes Decision</b></td></tr>';
			echo'<tr>';
			
				if($Appid !=''){
				echo '<td align="centre"><input type="submit" value="' . _('Update') . '" name="submit"/>';
				if($MAXID == $Appid){
				?>
		<input type="submit" onclick="return confirm('Are you sure you want to Delete this Level?');" value="Delete" name="Delete"/>
			<?php
			}else{
				echo '';
				}
			}else{
			echo '<td align="centre"><input type="submit" value="' . _('Submit') . '" name="submit"/>';
			echo ' <b><input name="end" type="checkbox" value="1" />Final Approver</b>';
			}
	echo '</td>
			</tr>
			</table>';	
			echo '</form></div>';	

?>
