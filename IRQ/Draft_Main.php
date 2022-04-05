<?php
session_start();
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
	}-->
</style>
<table width="100%" border="0" style="height:60px;">
  <tr>
    <td><span class="title">Draft</span></td>
  </tr>
  <tr>
    <td>
      <a href="#" ><div style="background:url(images/search_button.png) no-repeat right;"><input placeholder="Search..." style="width:90%" type="text" name="textfield"></div></a>
    </td>
  </tr>
</table>
<div style="border-bottom:solid; width:100%"></div>
<?php

include 'inc/db_config.php';
if($_SESSION['Document_id']==1){
if(isset($_SESSION['Doc_Store'])){ // request for purchase
$query="SELECT * FROM irq_request a
				INNER JOIN irq_stockrequest b ON b.dispatchid = a.requestid
				INNER JOIN irq_documents c ON a.doc_id = c.doc_id
				INNER JOIN departments d ON b.departmentid = d.departmentid
				WHERE draft=1 AND closed=0 AND initiator = '".$_SESSION['UserID']."' AND a.doc_id='". $_SESSION['Doc_Store'] ."' ORDER BY Requesteddate DESC";
}else{
$query="SELECT * FROM irq_request a
				INNER JOIN irq_stockrequest b ON b.dispatchid = a.requestid
				INNER JOIN irq_documents c ON a.doc_id = c.doc_id
				INNER JOIN departments d ON b.departmentid = d.departmentid
				WHERE draft=1 AND closed=0 AND initiator = '".$_SESSION['UserID']."' AND a.doc_id='". $_SESSION['Document_id'] ."' ORDER BY Requesteddate DESC";
}//end session doc_store
}elseif($_SESSION['Document_id']==2){ //transport requisition
$query="SELECT * FROM irq_request a
				INNER JOIN irq_transport b ON a.requestid = b.TransportID
				INNER JOIN irq_documents c ON a.doc_id = c.doc_id
				INNER JOIN departments d ON b.departmentid = d.departmentid
				WHERE draft=1 AND closed=0 AND initiator = '".$_SESSION['UserID']."' ORDER BY Requesteddate DESC";
}elseif($_SESSION['Document_id']==5){ //maintenance requisition
$query="SELECT * FROM irq_request a
				INNER JOIN irq_maintenance b ON a.requestid = b.maintenanceid
				INNER JOIN irq_documents c ON a.doc_id = c.doc_id
				INNER JOIN departments d ON b.departmentid = d.departmentid
				WHERE draft=1 AND closed=0 AND initiator = '".$_SESSION['UserID']."' ORDER BY Requesteddate DESC";
}elseif($_SESSION['Document_id']==7){ // requisition
$query="SELECT * FROM irq_request a
				INNER JOIN irq_gatepass b ON a.requestid = b.gatepassid
				INNER JOIN irq_documents c ON a.doc_id = c.doc_id
				INNER JOIN departments d ON b.departmentid = d.departmentid
				WHERE draft=1 AND closed=0 AND initiator = '".$_SESSION['UserID']."' ORDER BY Requesteddate DESC";
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

		echo '<div class="hov" style="border-bottom:inset; width:100%"><a style="text-decoration:none;" href="'. $draft.'?id='.$row['requestid'].'&draft='.$row['draft'].'" target="Content">';
		echo '<table width="100%" border="0"><tr><td><span class="subject">'.strtoupper(substr($row['doc_name'], 0, 30)).'</span></td><td align="right"><span class="time">'. date("h:i:s A",strtotime($row['Requesteddate'])).'</span></td></tr>';
		echo '<tr><td><span class="subject">'.substr('From Dept:'.strtoupper($row['description']), 0, 28).'</span></td><td align="right">';
		if($row['draft']==1){echo '<b style="background-color:#3399FF; font-size:9px;">Draft</b>';}
		echo '</td></tr></table>';
		echo '</a></div>';
	}
}
else{
		echo '<table width="100%"><tr><td><span class="subject">No Draft Requests</span></td></tr></table>';
	}
?>

