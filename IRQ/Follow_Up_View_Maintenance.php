<style type="text/css">

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
th.rows {
	font-weight:bold;
	color:#2C2C2C;
	text-align:right;
	border-bottom:thin solid #B3B3B3;
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
.table {background:#FFFFFF;}
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
b.st {
    border-radius: 2px;
	padding-right:5px;
	background-color:#FF0033;
	font-size:9px;
	padding-left:5px;
	padding-bottom:2px;
	color:#FFFFFF;
	font-weight:bold;
	font-family:"Times New Roman", Times, serif;
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
-->
</style>

<?php 
session_start();
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
}

$query="SELECT * FROM irq_request z 
							INNER JOIN irq_maintenance a on a.maintenanceid = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							WHERE z.requestid='" . $id . "'";
	$result=mysqli_query($conn,$query);
	$row = mysqli_fetch_array($result);
	
	if($row['doc_id'] == 5){
		$message='Problem Observed&nbsp; :';
		$date ='Breakdown Date&nbsp; :';
		}else{
		$message='Service Due&nbsp; :';
		$date ='Service Date&nbsp; :';
		}
		
		echo '<br /><div style="width:620px">';
		echo '<table width="600px" border="0">';
		echo '<tr>
		<th class="rows" width="200px">Request No.&nbsp; :</th>
		<td align="left" width="190px;">&nbsp;<strong>'.$row['requestid'].'</strong></td>
		</tr><tr>
		<th class="rows">Department&nbsp; :</th>
		<td align="left">&nbsp;'.strtoupper($row['description']).'</td>
		<th class="rows" width="130px">Job Card No.&nbsp; :</th>
		<td align="left">&nbsp;'.$row['cardno'].'</td>		
		</tr><tr>
		<th class="rows">Section&nbsp; :</th>
		<td align="left">'.$row['section'].'</td>
		<th class="rows">'.$date.'</th>
		<td>&nbsp;'. date("d, M Y h:i A",strtotime($row['breakdowndate'])) .'</td>
		</tr><tr>
		<th class="rows">M/C Type&nbsp; :</th>
		<td align="left">&nbsp;'.$row['mctype'].'</td>
		<th class="rows">M/C No.&nbsp; :</th>
		<td align="left" >&nbsp;'.$row['mcno'].'</td>
		</tr><tr>
		<th class="rows">Requesting Officer&nbsp; :</th>
		<td align="left" >&nbsp;'.$row['requesting_officer'].'</td>
		<th class="rows">Urgency&nbsp; :</th>
		<td align="left">&nbsp;'.$row['urgency'].'</td>
		</tr><tr>
		<th class="rows">'.$message.'</th>
		<td align="left" colspan="3">&nbsp;<textarea name="" disabled="true" cols="58" rows="2">'.$row['problem'].'</textarea></td>
		</tr>';
		echo '</table>';
		
		$comments="SELECT approver_name, approver, approvaldate, approver_comment,Unread,Sent FROM irq_levels a
	 			INNER JOIN irq_approvers b ON a.approver_id=b.approver_id
				LEFT JOIN irq_authorize_state c ON a.level_id  = c.level and requisitionid='".$id."'
				WHERE a.doc_id=".$row['doc_id']."
				ORDER BY a.level_id ASC";
				
		$titles="SELECT approver_name FROM irq_levels a
	 			INNER JOIN irq_approvers b ON a.approver_id=b.approver_id
				LEFT JOIN irq_authorize_state c ON a.level_id  = c.level and requisitionid='".$id."'
				WHERE a.doc_id=".$row['doc_id']."
				GROUP BY c.level ORDER BY a.level_id ASC";
$first=1;
$tit = array();
$comm = array();
$commentresults= mysqli_query($conn,$comments) or die (mysqli_error($conn));
$titleresults= mysqli_query($conn,$titles) or die (mysqli_error($conn));
while($comment=mysqli_fetch_array($commentresults)){
$comm[] = $comment;
}
$tit[] ='REQUESTING OFFICER';
while($title=mysqli_fetch_array($titleresults)){
$tit[] = $title['approver_name'];
}	
		echo '<table width="100%" border="0">';
		//while($comment=mysqli_fetch_array($commentresults)){
		for($i=0; $i < count($comm)+1 and $i < count($tit); $i++) {
		echo' <tr>
				<td align="center"  width="10%"><img class="image" src="images/image.jpg"  /></td>
				<td><div class="bubble">
			<span style="color: #999999; font-size:10px;">From: <a href="#">'.$tit[$i].' ('.$comm[$i]['approver'].')</a></span> <br />';
			if($comm[$i-1]['Unread']==0 && $i !=0){
			echo "<b style=background-color:#3399FF; font-size:9px;>Waiting</b>";
			}else{
			echo ($comm[$i]['approver_comment'] == "" ? "<b style=background-color:#CC0000; font-size:9px;>Pending</b>" : $comm[$i]['approver_comment']);
			}
		echo '<br />
				<span style="color: #999999; font-size:10px;">'. ($comm[$i]['approvaldate'] =="" ? "<br />" : date("d, M Y",strtotime($comm[$i]['approvaldate'])).' '. date("h:i:s A",strtotime($comm[$i]['approvaldate']))) .'</span>
				
				</div>
			</td>
			  </tr>';
			  $first++;
			  }
		echo '</table>';
		
		echo '</div>';
   
   ?>
