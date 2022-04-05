<?php
ob_start();
session_start();
date_default_timezone_set('Africa/Nairobi');
if(!isset($_SESSION['UserID']) && !isset($_SESSION['AccessLevel']) && !isset($_SESSION['UsersRealName']) && !isset($_SESSION['DatabaseName'])){
header('location:../index.php');
}
include 'inc/db_config.php';
if(is_numeric($_GET['id'])){
$id=$_GET['id'];
}else{
die ('Invalid Request Content Please Try Again!');
}	

$query='SELECT * FROM irq_request b
						INNER JOIN irq_stockrequest a ON b.requestid = a.dispatchid
						INNER JOIN departments e ON a.departmentid = e.departmentid
						INNER JOIN irq_documents f ON b.doc_id = f.doc_id
						INNER JOIN locations g ON a.loccode=g.loccode
						WHERE b.requestid='.$id.'';

$results= mysqli_query($conn,$query) or die (mysqli_error($conn));
	while($row=mysqli_fetch_array($results)){
				$doc = $row['doc_id'];
				$loccode = $row['loccode'];
				$dept = $row['departmentid'];

if(isset($_POST['Approve'])){
if(isset($_POST['lineitem'])){
	$Count = count($_POST["lineitem"]);
	for($i=0;$i<$Count;$i++) {
	$qry="DELETE FROM irq_stockrequestitems WHERE itemid='".$_POST['lineitem'][$i]."'";
	mysqli_query($conn,$qry) or die('Could not run Query: ' . mysqli_error($conn));
	}
	}
	//check if all the items are closed
	$q='SELECT * FROM irq_stockrequestitems a
			INNER JOIN stockmaster b ON b.stockid=a.stockid
			WHERE cancelled=0 AND completed=0 AND dispatchid='.$id.'';
	$re= mysqli_query($conn,$q) or die (mysqli_error($conn));
	$count = mysqli_num_rows($re);
	if($count ==0){
$insert = "DELETE FROM irq_request WHERE requestid=".$id."";	
mysqli_query($conn,$insert) or die('Could not run Query: ' . mysqli_error($conn));
$_SESSION['msg'] = '<ul class="states"><li class="warning">' . _('Warning: Requisition No. '). $id . ' ' . _('has been deleted because all the items for this request has been cancelled.'). '</li></ul>';
header('location:../IRQ_PurchaseOrService.php?Ref=Draft');
exit;
	}else{
	//end of check if all the items are closed
$insert = "UPDATE irq_request SET draft='0', Requesteddate='" . date('Y-m-d H:i:s') . "' WHERE requestid=".$id."";
mysqli_query($conn,$insert) or die('Could not run Query: ' . mysqli_error($conn));
		
		$levelid=1 .$doc;
		$comment=($row['narrative'] =="" ? "Process Initiator" : $row['narrative']);
$HSQL="INSERT INTO irq_authorize_state (requisitionid,
											level,
											approvaldate,
											approver,
											approver_comment)
										VALUES(
											'" . $id . "',
											'" . $levelid . "',
											'" . date('Y-m-d H:i:s') . "',
											'" . $_SESSION['UsersRealName']. "',
											'". $comment ."')";
	$insert=mysqli_query($conn,$HSQL);
	/******************************************************************************************/
	header('location:../IRQ_SentMail.php?doc='.$doc.'&level='.$levelid.'&loc='.$loccode.'&dept='.$dept.'&Re=Ref=Draft');
/*---------------------------------------------------------------------------------------*/
		
	} //end of check else if all the items are closed
}

ob_end_flush();
					
?>
<style type="text/css">
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
.table {
    background-color:#FFFFCC;
    margin:0 auto;
    width:70%;
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
th {
	font-weight:bold;
	color:#2C2C2C;
	text-align:center;
	border-bottom:thin solid #B3B3B3;
}
input[type='submit'] {
    background-color:#34a7e8;
    border:thin outset #1992DA;
    padding:6px 24px;
    vertical-align:middle;
    font-weight:bold;
    color:#FFFFFF;
    cursor: pointer;
    
    -webkit-border-radius:  4px;
    -moz-border-radius:     4px;
    border-radius:          4px;
    -moz-box-shadow:    1px 1px 1px #64BEF1 inset;
	-webkit-box-shadow: 1px 1px 1px #64BEF1 inset;
	box-shadow:         1px 1px 1px #64BEF1 inset;
}
a{
text-decoration:none;
}
.time {color: #999999; font-size:10px;}
.image{
		 border-radius:25px;
		 width:50px;
		 height:50px;
}
/*bubble*/
.bubble
{
position: relative;
width: 90%;

min-height: 10px;
padding-left:8px;
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
td { position: relative; }
tr.strikeout td:before {
  content: " ";
  position: absolute;
  top: 50%;
  left: 0;
  border-bottom: 2px solid #FF0033;
  width: 100%;
}

tr.strikeout td:after {
  content: "\00B7";
  font-size: 1px;
}
b {
    border-radius: 2px;
	padding-right:5px;
	background-color:#FF0033;
	font-size:9px;
	padding-left:5px;
	padding-bottom:2px;
	color:#FFFFFF;
	font-weight:bold;
    width: 35px;
	font-family:"Times New Roman", Times, serif;
}
-->
</style>
<?php

?>
<table width="100%" border="0" style="height:60px;">
  <tr>
    <td><span class="title"><?php echo strtoupper($row['doc_name']); ?></span></td>
  </tr>
  <tr>
    <td><?php echo date("d, M Y",strtotime($row['Requesteddate'])); ?></td>
	<td align="right"><?php echo date("h:i:s A",strtotime($row['Requesteddate'])); ?></td>
  </tr>
</table>
<div style="border-bottom:solid; width:100%"></div>
<?php

		echo '<div align="right" >';
		?>
		<a href="Draft_Delete.php?id=<?php echo $row['requestid']; ?>" target="_top" onclick="return confirm('Are you sure you want to delete this item?');" title="Edit" target="_self"><img src="images/trash.png" />Delete</a>
		<?php
		echo '</div>';
		echo '<div align="center" class="content">';
		echo '<table class="content" width="50%" border="0">';
		echo '<tr><th align="right">Request No</th><th align="right">Department</th><th>Stock Location</th><th align="right">Requested Date</th><th align="right">Date when required</th><th align="right">Forwarded By</th></tr>';
		echo '<tr><td align="center">'.$row['dispatchid'].'</td>';
		echo '<td align="center">'.$row['description'].'</td>';
		echo '<td align="center">'.$row['locationname'].'</td>';
		echo '<td align="center">'.date("d, M Y",strtotime($row['Requesteddate'])).'</td>';
		echo '<td align="center">'.date("d, M Y",strtotime($row['despatchdate'])).'</td>';
		echo '<td align="center" >'.$_SESSION['UserID'].'</td>';
		echo '</tr>';
		echo '<tr><td colspan="6"><textarea style="width:100%" name="" disabled="true" rows="1">Description: '.$row['narrative'].'</textarea></td></tr>';
		echo '</table>';		
		echo '<br />';
		}
		echo '<div class="line"> ';
		echo '<span class="bg">Items Requested</span>';
		echo '</div><br />';
		echo '<div class="table">';
		echo '<table width="100%" border="0">';
		echo '<tr>
						<th>' . _('Item Code') . '</th>
						<th>' . _('Product') . '</th>
						<th>' . _('Quantity') . '<br />' . _('Required') . '</th>
						<th>' . _('UOM') . '</th>
						<th>' . _('Cancel Line') . '</th>
					</tr>';
					
				$query='SELECT * FROM irq_stockrequestitems a
							INNER JOIN stockmaster b ON b.stockid=a.stockid
							WHERE dispatchid='.$id.'';
					$results= mysqli_query($conn,$query) or die (mysqli_error($conn));
					while($row=mysqli_fetch_array($results)){
					
		?>
		<form action="" method="post" enctype="multipart/form-data" onsubmit="return confirm('Are you sure you want to submit this Request?');" target="_top">
		<?php
		echo '<tr><td align="center">'.$row['stockid'].'</td>';
		echo '<td align="center">'.$row['description'].'</td>';
		echo '<td align="center">'.$row['quantity'].'</td>';
		echo '<td align="center">'.$row['uom'].'</td>';
		echo '<td align="center"><input name="lineitem[]" type="checkbox" value="'.$row['itemid'].'" /></td></tr>';
		}// end closed
		echo '</table>';
		echo '</div>';
		
		echo '<br />';
		echo '<div class="line"> ';
		echo '</div>';
		
		echo '<br />';
		echo '<input name="Approve" type="submit" value="Continue >>" />';
		echo '</div><br />';
		
		echo '</form>';
		echo '<br /><br />';
		
		echo '</div>';
	

?>

