<?php
ob_start();
session_start();
date_default_timezone_set('Africa/Nairobi');
if(!isset($_SESSION['UserID']) && !isset($_SESSION['AccessLevel']) && !isset($_SESSION['UsersRealName']) && !isset($_SESSION['DatabaseName'])){
header('location:../index.php');
}
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
include 'inc/db_config.php';
if(is_numeric($_GET['id'])){
$id=$_GET['id'];
}else{
die ('Invalid Request Content Please Try Again!');
}	

$query="SELECT * FROM irq_request z 
							INNER JOIN irq_stockrequest a on a.dispatchid = z.requestid
							INNER JOIN irq_authorize_state b on a.dispatchid = b.requisitionid
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN locations g ON a.loccode=g.loccode
							WHERE a.dispatchid='" . $id . "' ORDER BY Requesteddate DESC limit 1";
$results= mysqli_query($conn,$query) or die (mysqli_error($conn));
	while($row=mysqli_fetch_array($results)){
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
					$loccode=$row['loccode'];


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
		
		echo '<br />';				
		echo '<div align="center" class="content">';
		echo '<table class="content" width="50%" border="0">';
		echo '<tr><th align="right">Request No</th><th align="right">Department</th><th>Stock Location</th><th align="right">Requested Date</th><th align="right">Date when required</th><th align="right">Forwarded By</th></tr>';
		echo '<tr><td align="center">'.$row['dispatchid'].'</td>';
		echo '<td align="center">'.$row['description'].'</td>';
		echo '<td align="center">'.$row['locationname'].'</td>';
		echo '<td align="center">'.date("d, M Y",strtotime($row['Requesteddate'])).'</td>';
		echo '<td align="center">'.date("d, M Y",strtotime($row['despatchdate'])).'</td>';
		echo '<td align="center">'.$row['approver'].'</td>';
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
						<th>' . _('Product') . '</th>
						<th>' . _('Quantity') . '<br />' . _('Required') . '</th>
						<th>' . _('Quantity') . '<br />' . _('Delivered') . '</th>
						<th>' . _('Units') . '</th>
						<th>' . _('Completed') . '</th>
						<th>' . _('Tag') . '</th>
					</tr>';				

				$query='SELECT * FROM irq_stockrequestitems a
							INNER JOIN stockmaster b ON b.stockid=a.stockid
							WHERE dispatchid='.$id.'';
					$results= mysqli_query($conn,$query) or die (mysqli_error($conn));
					while($row=mysqli_fetch_array($results)){
					if($row['cancelled'] ==1){
		echo '<tr  class="strikeout">
					<td>' . $row['description'] . '</td>
					<td align="center">'.$row['quantity'].'</td>
					<td align="center">' .$row['qtydelivered']. '</td>
					<td align="center">' . $row['uom'] . '</td>
					<td align="center"><b>Cancelled By '. $row['cancelled_by'] . '</b></td>
					<td align="center">None</td>';
					}else{
					echo '<tr>
					<td>' . $row['description'] . '</td>
					<td align="center">'.$row['quantity'].'</td>
					<td align="center">' .$row['qtydelivered']. '</td>
					<td align="center">' . $row['uom'] . '</td>
					<td align="center">Completed</td>
					<td align="center">None</td>';
					}
					}
		
		echo '</table>';
		echo '</div>';
		
		echo '<br />';
		echo '<div class="line"> ';
		echo '<span class="bg">Attachments</span>';
		echo '</div><br />';
		echo '<a style="width:20%" target="_top" href="../PrintReq_Item_Service.php?id='.$id.'" ><img src="images/pdf.gif" /> Download File</a>';
		echo '<br /><br />';
		echo '<div class="line"> ';
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
				<br /><span class="time">'. calculate_time_span($myrows['approvaldate']) .'</span>
				
				</div>
			</td>
			  </tr>';
			  }
		echo '</table>';
		
		echo '</form>';
		echo '<br /><br />';
		
		echo '</div>';
	

?>