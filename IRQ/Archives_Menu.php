<?php
session_start();
include 'inc/db_config.php';
include_once('Menu_Links.php');
if(!isset($_SESSION['UserID']) && !isset($_SESSION['AccessLevel']) && !isset($_SESSION['UsersRealName']) && !isset($_SESSION['DatabaseName'])){
header('location:../index.php');
}
?>
<style type="text/css">
<!--
.title {
	font-size: x-large;
	font-family: "Times New Roman", Times, serif;
	font-weight: bold;
}
.subject {
	font-size:11px;
	font-family: "Times New Roman", Times, serif;
	font-weight: bold;
}
.time {color: #999999; font-size:10px;}
.hov:hover {
    background-color:#CCCCCC;
}
b {
    border-radius: 2px;
	padding-right:5px;
	padding-left:5px;
	padding-bottom:2px;
	color:#FFFFFF;
	font-weight:bold;
    width: 35px;
	font-family:"Times New Roman", Times, serif;
}
.cat{
	background:#333333;
	color:#FFFFFF;
	font-weight:bold;
	font-family:"Times New Roman", Times, serif;
	}
#header {
    position: fixed;
    top: 0px;
    height: 65px;
    width: 100%;
    background:#FFFFFF;
}
-->
</style>
<script language="JavaScript">
function setVisibility(id) {
document.getElementById(id).style.display = 'none';

}

function displaySubmit(){
			document.form.action="Archives_Menu.php"; 
			document.form.submit();
	}

</script>
<div id="header">
<form id="stream" name="form" method="post">
<table width="100%" border="0" style="height:60px;">
  <tr >
    <td colspan="2"><span class="title">Archives</span></td>
  </tr>
  <tr>
    <td>
      <input placeholder="Search Request Number..." style="width:100%;" value="<?php echo (isset($_POST['Searchfield']) ? $_POST['Searchfield'] : ""); ?>" type="text" name="Searchfield"></td><td width="30" align="left"><img src="images/search_button.png" style="cursor:pointer;" onclick="displaySubmit();" />
    </td>
  </tr>
</table>
</form>
</div>
<div style="border-bottom:solid; width:100%; padding-top:65px;"></div>
<?php

if(isset($_POST['Searchfield']) && $_POST['Searchfield'] !=""){
$search=" AND requestid LIKE '%".$_POST['Searchfield']."%' ";
}else{
$search="";
}

if($_SESSION['Document_id'] ==1){
if(isset($_SESSION['Doc_Store'])){
$query="SELECT * FROM irq_request z 
							INNER JOIN irq_stockrequest a  on a.dispatchid = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id AND final_approver=1
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN locations g ON a.loccode = g.loccode
							WHERE closed=1 AND z.doc_id='".$_SESSION['Doc_Store']."' AND CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' WHEN d.userid ='ISSUE' THEN g.authoriser='".$_SESSION['UserID']."' WHEN d.userid ='PROCURE' THEN g.purchasing_officer='".$_SESSION['UserID']."' ELSE d.userid='".$_SESSION['UserID']."' END ".$search." GROUP BY z.requestid ORDER BY Requesteddate DESC";
}else{
$query="SELECT * FROM irq_request z 
							INNER JOIN irq_stockrequest a  on a.dispatchid = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id AND final_approver=1
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN locations g ON a.loccode = g.loccode
							WHERE closed=1 AND z.doc_id='".$_SESSION['Document_id']."' AND CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' WHEN d.userid ='ISSUE' THEN g.authoriser='".$_SESSION['UserID']."' WHEN d.userid ='PROCURE' THEN g.purchasing_officer='".$_SESSION['UserID']."' ELSE d.userid='".$_SESSION['UserID']."' END ".$search." GROUP BY z.requestid ORDER BY Requesteddate DESC";
}
}elseif($_SESSION['Document_id'] ==2){
$query="SELECT * FROM irq_request z 
							INNER JOIN irq_transport a on a.TransportID = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id AND final_approver=1
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							WHERE closed=1 AND CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' ELSE d.userid='".$_SESSION['UserID']."' END ".$search." GROUP BY z.requestid ORDER BY Requesteddate DESC";
}elseif($_SESSION['Document_id'] ==5){
$query="SELECT * FROM irq_request z 
							INNER JOIN irq_maintenance a on a.maintenanceid = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id AND final_approver=1
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							WHERE closed=1 AND CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' ELSE d.userid='".$_SESSION['UserID']."' END ".$search." GROUP BY z.requestid ORDER BY Requesteddate DESC";
}elseif($_SESSION['Document_id'] ==7){
$query="SELECT * FROM irq_request z 
							INNER JOIN irq_gatepass a on a.gatepassid = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id AND final_approver=1
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							WHERE closed=1 AND CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' ELSE d.userid='".$_SESSION['UserID']."' END ".$search." GROUP BY z.requestid ORDER BY Requesteddate DESC";
}		
							
$results= mysqli_query($conn,$query) or die (mysqli_error($conn));
$num=mysqli_num_rows($results);
if($num >0){
	$current_cat = null;
	while($row=mysqli_fetch_array($results)){
	if (date("d, M Y",strtotime($row['Requesteddate'])) != $current_cat) 
                    {
                        $current_cat = date("d, M Y",strtotime($row['Requesteddate']));
                        echo "<div align=center class=cat>$current_cat</div>";
                    }

					?>
		<div class="hov" style="border-bottom:inset; width:100%"><a href="<?php echo $complete.'?id='. $row['requestid']; ?>" target="Content" onclick="setVisibility('<?php echo $row['requestid'].''.$row['level']; ?>');">
		<?php
		echo '<table width="100%" border="0"><tr><td><span class="subject">'.strtoupper(substr($row['requestid'].' : '.$row['doc_name'], 0, 28)).'</span></td><td align="right"><span class="time">'. date("h:i:s A",strtotime($row['Requesteddate'])).'</span></td></tr>';
		echo '<tr><td><span class="subject">'.substr('From Dept:'.strtoupper($row['description']), 0, 30).'</span></td><td align="right">';
		if($row['closed']==1){echo '<b style="background-color:#3399FF; font-size:9px;">Completed</b>';}
		echo '</td></tr></table>';
		echo '</a></div>';
	}
}
else{
		echo '<table width="100%"><tr><td><span class="subject">No Requests in Archives</span></td></tr></table>';
	}
?>

