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

if(is_numeric($_GET['id'])){
$id=$_GET['id'];
}

$query="SELECT * FROM irq_request z 
							INNER JOIN irq_stockrequest a on a.dispatchid = z.requestid 
							INNER JOIN irq_authorize_state b on a.dispatchid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN locations g ON a.loccode=g.loccode
							WHERE a.dispatchid='" . $id . "'";
	$result=mysqli_query($conn,$query);

$myrow = mysqli_fetch_array($result);
   echo '<br /><div style="width:750px">';
   echo '<table width="100%" border="0">';
		echo '<tr><th align="right">Request No</th><th align="right">Department</th><th>Stock Location</th><th align="right">Requested Date</th><th align="right">Date when required</th><th align="right">Forwarded By</th></tr>';
		echo '<tr><td align="center">'.$myrow['dispatchid'].'</td>';
		echo '<td align="center">'.$myrow['description'].'</td>';
		echo '<td align="center">'.$myrow['locationname'].'</td>';
		echo '<td align="center">'.date("d, M Y",strtotime($myrow['Requesteddate'])).'</td>';
		echo '<td align="center">'.date("d, M Y",strtotime($myrow['despatchdate'])).'</td>';
		echo '<td align="center">'.$myrow['approver'].'</td>';
		echo '</tr>';
		echo '<td colspan="6"><textarea style="width:97%" name="" disabled="true" rows="1">Description: '.$myrow['narrative'].'</textarea></td></tr>';
		echo '<tr><td colspan="6">';
		echo '<div align="center">';
		echo '<div style="width:500px">';
		echo '<table class="table">';
		echo '<tr>
					<th>' . _('Item Code') . '</th>
					<th>' . _('Product') . '</th>
					<th>' . _('Quantity') . '<br />' . _('Required') . '</th>
					<th>' . _('UOM') . '</th>
					</tr>';
			$query='SELECT * FROM irq_stockrequestitems a
							INNER JOIN stockmaster b ON b.stockid=a.stockid
							WHERE dispatchid='.$id.'';
		$results= mysqli_query($conn,$query) or die (mysqli_error($conn));
			while($row=mysqli_fetch_array($results)){
			if($row['cancelled'] ==1){
			echo '<tr class="strikeout">
					<td align="center">' . $row['stockid'] . '</td>
					<td  width="35%" align="center">' . $row['description'] . '</td>
					<td align="center">' . $row['quantity'].'</td>
					<td align="center">' . $row['uom'] . '</td>
					<td><b class="st">Cancelled By '. $row['cancelled_by'] . '</b></td>';
			echo '</tr>';
			
					}else{
		echo '<tr>
					<td align="center">' . $row['stockid'] . '</td>
					<td align="center">' . $row['description'] . '</td>
					<td align="center">' . $row['quantity'].'</td>
					<td align="center">' . $row['uom'] . '</td>
					</tr>';
					}
				}
			
		echo '</table>';
		echo '</div>';
		echo '</div>';
		echo '</td></tr>';
		echo '</table>';
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
            //$time = $months." month ago";
			$time = date("d, M Y",strtotime($date)).' '. date("h:i:s A",strtotime($date));

        return $time;
}		
		$comments="SELECT approver_name, approver, approvaldate, approver_comment,Unread,Sent,closed FROM irq_levels a
	 			INNER JOIN irq_approvers b ON a.approver_id=b.approver_id
				LEFT JOIN irq_authorize_state c ON a.level_id  = c.level and requisitionid='".$id."'
				LEFT JOIN irq_request z ON c.requisitionid=z.requestid
				WHERE a.doc_id=".$myrow['doc_id']."
				ORDER BY a.level_id ASC";
				
		$titles="SELECT approver_name FROM irq_levels a
	 			INNER JOIN irq_approvers b ON a.approver_id=b.approver_id
				LEFT JOIN irq_authorize_state c ON a.level_id  = c.level and requisitionid='".$id."'
				WHERE a.doc_id=".$myrow['doc_id']."
				ORDER BY a.level_id ASC";
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
		if($comm[$i]['Unread']==1){
		$comm[]['approver_comment'] ='Completed';
		}
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
