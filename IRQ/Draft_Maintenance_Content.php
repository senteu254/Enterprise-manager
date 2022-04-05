<?php
ob_start();
session_start();
date_default_timezone_set('Africa/Nairobi');
if(!isset($_SESSION['UserID']) && !isset($_SESSION['AccessLevel']) && !isset($_SESSION['UsersRealName']) && !isset($_SESSION['DatabaseName'])){
header('location:../index.php');
}
include 'inc/db_config.php';
function calculate_time_span($date1,$date2){
    $seconds  = strtotime($date1) - strtotime($date2);

        $months = floor($seconds / (3600*24*30));
        $day = floor($seconds / (3600*24));
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds - ($hours*3600)) / 60);
        $secs = floor($seconds % 60);

        if($seconds < 60)
            $time = $secs." seconds";
        else if($seconds < 60*60 )
            $time = $mins." mins";
        else if($seconds < 24*60*60)
            $time = $hours." hours";
        else if($seconds < 24*60*60)
            $time = $day." day/s";
        else
            $time = $months." month/s";

        return $time;
}
if(is_numeric($_GET['id'])){
$id=$_GET['id'];
}else{
die ('Invalid Request Content Please Try Again!');
}	

$query='SELECT * FROM irq_request a
						INNER JOIN irq_maintenance b ON b.maintenanceid = a.requestid
						INNER JOIN irq_documents c ON a.doc_id = c.doc_id
						INNER JOIN departments d ON d.departmentid = b.departmentid
						WHERE a.requestid='.$id.'';

$results= mysqli_query($conn,$query) or die (mysqli_error($conn));
	while($row=mysqli_fetch_array($results)){
				$doc = $row['doc_id'];
				$dept = $row['departmentid'];

if(isset($_POST['Approve'])){
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
	$insert=mysqli_query($conn,$HSQL) or die(mysqli_error($conn));
	/******************************************************************************************/
	header('location:../IRQ_SentMail.php?doc='.$doc.'&level='.$levelid.'&dept='.$dept.'&Re=Ref=Draft');
/*---------------------------------------------------------------------------------------*/
	
	} //end of if approve

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
	text-align:right;
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
		if($row['doc_id'] == 5){
		$message='Problem Observed&nbsp; :';
		$date ='Breakdown Date&nbsp; :';
		}else{
		$message='Service Due&nbsp; :';
		$date ='Service Date&nbsp; :';
		}
	
		echo '<div align="right" >';
		?>
		<a href="Draft_Delete.php?id=<?php echo $row['requestid']; ?>" target="_top" onclick="return confirm('Are you sure you want to delete this item?');" title="Edit" target="_self"><img src="images/trash.png" />Delete</a>
		<?php
		echo '</div>';
		echo '<div align="center" class="content">';
		echo '<div style="width:70%">';
		echo '<table class="content" border="0">';
		echo '<tr>
		<th width="150px">Request No.&nbsp; :</th>
		<td align="left" width="190px;">&nbsp;<strong>'.$row['requestid'].'</strong></td>
		</tr><tr>
		<th>Department&nbsp; :</th>
		<td colspan="3" align="left">&nbsp;'.strtoupper($row['description']).'</td>	
		</tr><tr>
		<th>Section&nbsp; :</th>
		<td align="left">'.$row['section'].'</td>
		<th >'.$date.'</th>
		<td>&nbsp;'. date("d, M Y h:i A",strtotime($row['breakdowndate'])) .'</td>
		</tr><tr>
		<th>M/C Type&nbsp; :</th>
		<td align="left">&nbsp;'.$row['mctype'].'</td>
		<th >M/C No.&nbsp; :</th>
		<td align="left" >&nbsp;'.$row['mcno'].'</td>
		</tr><tr>
		<th>Requesting Officer&nbsp; :</th>
		<td align="left" >&nbsp;'.$row['requesting_officer'].'</td>
		<th>Urgency&nbsp; :</th>
		<td align="left">&nbsp;'.$row['urgency'].'</td>
		</tr><tr>
		<th>'.$message.'</th>
		<td align="left" colspan="3">&nbsp;<textarea name="" disabled="true" cols="63" rows="2">'.$row['problem'].'</textarea></td>
		</tr>';
		echo '</table>';	
		echo '</div>';	
		echo '<br />';	
		}
		?>
		<form action="" method="post" enctype="multipart/form-data" onsubmit="return confirm('Are you sure you want to submit this Request?');" target="_top">
		<?php
		
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

