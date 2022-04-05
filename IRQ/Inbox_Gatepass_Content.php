<?php
ob_start();
session_start();
date_default_timezone_set('Africa/Nairobi');
if(!isset($_SESSION['UserID']) && !isset($_SESSION['AccessLevel']) && !isset($_SESSION['UsersRealName']) && !isset($_SESSION['DatabaseName'])){
header('location:../index.php');
}
include 'inc/db_config.php';
function calculate_time_span($date){
    $seconds  = strtotime(date('Y-m-d H:i:s')) - strtotime($date);

        $months = floor($seconds / (3600*24*30));
        $day = floor($seconds / (3600*24));
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds - ($hours*3600)) / 60);
        $secs = floor($seconds % 60);

        if($seconds < 60)
            $time = $secs." seconds ago";
        else if($seconds < 60*60 )
            $time = $mins." min ago";
        else if($seconds < 24*60*60)
            $time = $hours." hours ago";
        else if($seconds < 24*60*60)
            $time = $day." day ago";
        else
            $time = $months." month ago";

        return $time;
}
if(is_numeric($_GET['id'])){
$id=$_GET['id'];
$level=$_GET['LV'];
}else{
die ('Invalid Request Content Please Try Again!');
}	

$q="SELECT * FROM irq_request a 
							INNER JOIN irq_gatepass b on b.gatepassid = a.requestid 
							INNER JOIN irq_authorize_state c on a.requestid = c.requisitionid 
							INNER JOIN irq_levels d ON c.level = d.level_id
							INNER JOIN irq_approvers e ON d.approver_id = e.approver_id
							INNER JOIN departments f ON b.departmentid = f.departmentid
							INNER JOIN irq_documents g ON a.doc_id = g.doc_id
							WHERE a.requestid='" . $id . "' AND c.level='".$level."'";
$resul= mysqli_query($conn,$q) or die (mysqli_error($conn));
	$row=mysqli_fetch_array($resul);
					if(isset($row['decision']) && $row['decision']==1){
					$decision=TRUE;
					}else{
					$decision=FALSE;
					}
					if(isset($row['Sent']) && $row['Sent']==1){
					$sent=TRUE;
					}else{
					$sent=FALSE;
					}
					if(isset($row['closed']) && $row['closed']==1){
					$closed=TRUE;
					}else{
					$closed=FALSE;
					}
					if(isset($row['final_approver']) && $row['final_approver']==1){
					$final=TRUE;
					}else{
					$final=FALSE;
					}
					$doc= $row['doc_id'];
					$subj= $row['doc_name'];
					$dept=$row['description'];
if(isset($_POST['Approve'])){
//submit from inbox
$comm= $_POST['comment'];
$comment= mysqli_real_escape_string($conn,$comm);

		$sql="SELECT level FROM irq_authorize_state WHERE requisitionid='" . $id . "'";
		$result=mysqli_query($conn,$sql);
		$rowcount=mysqli_num_rows($result);
		if($rowcount >0){
		$new = ($rowcount+1);
		$levelid = $new .$doc;
		}else{
		$levelid =1 .$doc;
		}
		if($final==TRUE){
		$HSQL="INSERT INTO irq_authorize_state (requisitionid,
											level,
											approvaldate,
											approver,
											approver_comment)
										VALUES(
											'" . $id . "',
											'" . $rowcount.$doc . "',
											'" . date('Y-m-d H:i:s') . "',
											'" . $_SESSION['UsersRealName']. "',
											'" . $comment . "')";
		$insert=mysqli_query($conn,$HSQL) or die('Could not run Query: ' . mysqli_error($conn));
		$insert = "UPDATE irq_request SET closed='1' WHERE requestid=".$id."";	
		mysqli_query($conn,$insert) or die('Could not run Query: ' . mysqli_error($conn));
		$_SESSION['msg'] = '<ul class="states"><li class="succes">' . _('Success: Transport Requisition No. '). $id . ' ' . _('has been appoved'). '</li></ul>';
		header('location:../IRQ_GatepassRequest.php?Ref=Inbox');
		}else{
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
											'" . $comment . "')";
	$insert=mysqli_query($conn,$HSQL) or die('Could not run Query: ' . mysqli_error($conn));

$insert = "UPDATE irq_authorize_state SET sent='1' WHERE requisitionid=".$id." AND level=".$row['level']."";
mysqli_query($conn,$insert) or die('Could not run Query: ' . mysqli_error($conn));

/******************************************************************************************/
	header('location:../IRQ_SentMail.php?doc='.$doc.'&level='.$levelid.'&dept='.$row['departmentid'].'');
/*--------------------------------------------------------------------------------------------------*/
}
}//end of post submit
if(isset($_POST['Decline'])){
$comm= $_POST['comment'];
$comment= mysqli_real_escape_string($conn,$comm);
		$sql="SELECT level FROM irq_authorize_state WHERE requisitionid='" . $id . "'";
		$result=mysqli_query($conn,$sql);
		$rowcount=mysqli_num_rows($result);
		if($rowcount >0){
		$new = ($rowcount+1);
		$levelid = $new .$doc;
		}else{
		$levelid =1 .$doc;
		}
		
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
											'" . $comment . "')";
	$insert=mysqli_query($conn,$HSQL) or die('Could not run Query: ' . mysqli_error($conn));
$insert = "UPDATE irq_request SET closed='2' WHERE requestid=".$id."";	
mysqli_query($conn,$insert) or die('Could not run Query: ' . mysqli_error($conn));

$_SESSION['msg'] = '<ul class="states"><li class="warning">' . _('Warning: Requisition No. '). $id . ' ' . _('has been cancelled.'). '</li></ul>';
header('location:../IRQ_GatepassRequest.php?Ref=Inbox');
exit;
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
<table width="100%" border="0" style="height:60px;">
  <tr>
    <td><span class="title"><?php echo strtoupper($subj); ?></span></td>
  </tr>
  <tr>
    <td><?php echo date("d, M Y",strtotime($row['Requesteddate'])); ?></td>
	<td align="right"><?php echo date("h:i:s A",strtotime($row['Requesteddate'])); ?></td>
  </tr>
</table>
<div style="border-bottom:solid; width:100%"></div>
<?php
		$insert = "UPDATE irq_authorize_state SET Unread='1'
					WHERE requisitionid=".$id." AND level=".$row['level']."";
					mysqli_query($conn,$insert) or die('Could not run Query: ' . mysqli_error($conn));
		echo '<br />';			
		echo '<div align="center" class="content">';
		if($row['doc_id'] ==7){
		echo '<table class="content" width="50%" border="0">';
		echo '<tr><th>Request No</th><th>Department</th><th>Building</th><th>Delivery No.</th><th>Invoice No.</th><th>Requested Date</th><th>Requested By</th></tr>';
		echo '<tr><td align="center">'.$row['requestid'].'</td>';
		echo '<td align="center">'.$row['description'].'</td>';
		echo '<td align="center">'.$row['building'].'</td>';
		echo '<td align="center">'.$row['deliveryno'].'</td>';
		echo '<td align="center">'.$row['invoiceno'].'</td>';
		echo '<td align="center">'.date("d, M Y",strtotime($row['Requesteddate'])).'</td>';
		echo '<td align="center" >'.$row['initiator'].'</td>';
		echo '</tr>';
		echo '</table>';		
		echo '<br />';
		echo '<div style="width:70%;">';
		echo '<table class="content">';
		echo '<tr><th align="right">Vehicle Reg No.</th>';
		echo '<td>'.$row['vregno'].'</td>';
		echo '<th>Vehicle Type</th>';
		echo '<td>'.$row['vtype'].'</td></tr>';
		echo '<tr><th align="right">Vehicle\'s Company</th>';
		echo '<td colspan="3">'.$row['vcompany'].'</td>';
		echo '<tr><th>Driver Name</th>';
		echo '<td>'.$row['driver_name'].'</td>';
		echo '<th>License No</th>';
		echo '<td>'.$row['licenseno'].'</td></tr>';
		echo '<tr><th>Passenger Name</th>';
		echo '<td>'.$row['passanger_name'].'</td>';
		echo '<th>Passenger ID No</th>';
		echo '<td>'.$row['passanger_idno'].'</td></tr>';
		echo '</table>';
		echo '</div>';
		}else{
		echo '<div style="width:70%;">';
		echo '<table class="content">';
		echo '<tr style="height:40px;"><th align="right">Request No.</th>';
		echo '<td>'.$row['requestid'].'</td>';
		echo '<th>Requested Date</th>';
		echo '<td>'.date("d, M Y",strtotime($row['Requesteddate'])) .' Time:'. date("H:i:s",strtotime($row['Requesteddate'])).'</td></tr>';
		echo '<tr style="height:40px;"><th align="right">Bearer Name</th>';
		echo '<td>'.$row['driver_name'].'</td>';
		echo '<th>Requestion Officer</th>';
		echo '<td>'.$row['requesting_officer'].'</td></tr>';
		echo '<tr style="height:40px;"><th>Vehicle Reg No</th>';
		echo '<td>'.$row['vregno'].'</td>';
		echo '<th>Vehicle Type</th>';
		echo '<td>'.$row['vtype'].'</td></tr>';
		echo '<tr style="height:40px;"><th>Frrom Department</th>';
		echo '<td>'.$row['description'].'</td>';
		echo '<th>Item Destination</th>';
		echo '<td>'.$row['destination'].'</td></tr>';
		echo '</table>';
		echo '</div>';
		}
		echo '<br />';	
		echo '<div class="line"> ';
		echo '<span class="bg">Items Transported</span>';
		echo '</div><br />';
		echo '<div class="table">';
		echo '<table width="100%" border="0">';
		echo '<tr>
						<th width="90px">' . _('S/No') . '</th>
						<th width="90px">' . _('Oty') . '</th>
						<th>' . _('Description of Goods on Board (Lot No.)') . '</th>
					</tr>';
					
				$query='SELECT * FROM irq_gatepass_items
							WHERE gatepassid='.$id.'';
					$results= mysqli_query($conn,$query) or die (mysqli_error($conn));
					while($row=mysqli_fetch_array($results)){

		echo '<tr><td align="center">'.$row['sno'].'</td>';
		echo '<td align="center">'.$row['qty'].'</td>';
		echo '<td>'.$row['description'].'</td>';
		echo '</tr>';
		}// end closed
		echo '</table>';
		echo '</div>';
		echo '<br />';
		
		?>
		<form action="" method="post" enctype="multipart/form-data" onsubmit="return confirm('Are you sure you want to submit this Request?') && mySubmit();" target="_top">
		<?php
		echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
		echo '<div class="line"> ';
		echo '</div>';
		
		if($sent ==FALSE){
		echo '<div style="font-size: large; width:90%" align="left" >Comment: </div>';
		echo '<textarea required="required" name="comment" cols="75" rows="3"></textarea>';
		echo '<br /><br />';
		echo '<div style="font-size: large; width:90%;" align="centre" >';
		if($decision == 1){
		echo '<span id="submitButton">';
		echo '<input style="background:#FF3300; width:130px;" name="Decline" type="submit" value="<< Decline" /> &nbsp;';
		echo '</span>';
		} //end if decision
		if($closed == FALSE){
		echo '<span id="submitButton">';
		echo '<input name="Approve" type="submit" value="Continue >>" />';
		echo '</span>';
		} //end if closed
		} //end if sent
		echo '<div id="loadingbackground">';
		echo '<div id="progressBar" >
			 <img style="height:50px;width:50px;margin:30px;" src="images/pleasewait.gif" alt="Loading.."/><br>PROCESSING. PLEASE WAIT...
			</div>';
		echo '</div>';
		echo '</div><br />';
		$comment="SELECT * FROM irq_request a 
							INNER JOIN irq_authorize_state b on a.requestid = b.requisitionid 
							WHERE a.requestid='" . $id . "' ORDER BY Requesteddate DESC";

$commentresults= mysqli_query($conn,$comment) or die (mysqli_error($conn));
	
		echo '<table width="100%" border="0">';
		while($myrows=mysqli_fetch_array($commentresults)){
		echo' <tr>
				<td align="center"  width="10%"><img class="image" src="images/image.jpg"  /></td>
				<td><div class="bubble"><span class="time">From: <a href="#">'.$myrows['approver'].'</a></span> <br /> '.$myrows['approver_comment'].'
				<br /><span class="time">'. calculate_time_span($myrows['approvaldate']).'</span>
				
				</div>
			</td>
			  </tr>';
			  }
		echo '</table>';
		
		echo '</form>';
		echo '<br /><br />';
		
		echo '</div>';
	

?>
<style>
.info{
border: 1px solid;
margin: 10px 0px;
border-radius:7px;
padding:10px 10px 10px 50px;
background-repeat: no-repeat;
background-position: 10px center;
}
.info {
color: #00529B;
background-color: #BDE5F8;
background-image: url('info.png');
}
#loadingbackground{
	display:none; 	
    opacity: 0.8;
    position: fixed;
    top: 0;
    left: 0;
    background: #fff;
    width: 100%;
    height: 100%;
}

#progressBar{
    width: 300px;
    height: 150px;
    background-color: #fff;
    border: 5px solid #1468b3;
    text-align: center;
    color: #202020;
    position: absolute;
    left: 50%;
    top: 50%;
    margin-left: -150px;
    margin-top: -100px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    behavior: url("/css/pie/PIE.htc"); /* HANDLES IE */
}
</style>
<script language="javascript">
function mySubmit()
{
document.getElementById('submitButton').style.display = "none";
document.getElementById('loadingbackground').style.display = "block";
}
</script>