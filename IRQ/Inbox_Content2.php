<?php
ob_start();
session_start();
date_default_timezone_set('Africa/Nairobi');
if(!isset($_SESSION['UserID']) && !isset($_SESSION['AccessLevel']) && !isset($_SESSION['UsersRealName']) && !isset($_SESSION['DatabaseName'])){
header('location:../IRQ_PurchaseOrService.php');
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
$level=$_GET['LV'];
}else{
die ('Invalid Request Content Please Try Again!');
}	

$query="SELECT * FROM irq_request z 
							INNER JOIN irq_stockrequest a on a.dispatchid = z.requestid
							INNER JOIN irq_authorize_state b on a.dispatchid = b.requisitionid  
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN locations g ON a.loccode=g.loccode
							WHERE a.dispatchid='" . $id . "' AND level='".$level."'";
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
					$dept=$row['departmentid'];
if(isset($_POST['Approve'])){
//submit from inbox
$comm= $_POST['comment'];
$comment= mysqli_real_escape_string($conn,$comm);

if(isset($_POST['lineitem'])){
	$Count = count($_POST["lineitem"]);
	for($i=0;$i<$Count;$i++) {
	$qry="UPDATE irq_stockrequestitems SET cancelled=1, completed=1, cancelled_by='". $_SESSION['UserID'] ."' WHERE dispatchid='". $id ."' AND itemid='".$_POST['lineitem'][$i]."'";
	mysqli_query($conn,$qry) or die('Could not run Query: ' . mysqli_error($conn));
	}
	}
	
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

	//check if all the items are closed
	$q='SELECT * FROM irq_stockrequestitems a
			INNER JOIN stockmaster b ON b.stockid=a.stockid
			WHERE cancelled=0 AND completed=0 AND dispatchid='.$id.'';
	$re= mysqli_query($conn,$q) or die (mysqli_error($conn));
	$count = mysqli_num_rows($re);
	if($count ==0){
$insert = "UPDATE irq_request SET closed='1' WHERE requestid=".$id."";	
mysqli_query($conn,$insert) or die('Could not run Query: ' . mysqli_error($conn));
$_SESSION['msg'] = '<ul class="states"><li class="warning">' . _('Warning: Requisition No. '). $id . ' ' . _('has been closed because all the items for this request has been cancelled.'). '</li></ul>';
header('location:../IRQ_PurchaseOrService.php?Ref=Inbox');
exit;
	}else{
	//end of check if all the items are closed
$insert = "UPDATE irq_authorize_state SET sent='1' WHERE requisitionid=".$id." AND level=".$row['level']."";
mysqli_query($conn,$insert) or die('Could not run Query: ' . mysqli_error($conn));

/******************************************************************************************/
	header('location:../IRQ_SentMail.php?doc='.$doc.'&level='.$levelid.'&dept='.$dept.'&loc='.$loccode.'');
/*---------------------------------------------------------------------------------------*/
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
	
$qry="UPDATE irq_stockrequestitems SET cancelled=1, completed=1, cancelled_by='". $_SESSION['UserID'] ."' WHERE dispatchid='". $id ."'";
	mysqli_query($conn,$qry) or die('Could not run Query: ' . mysqli_error($conn));
$insert = "UPDATE irq_request SET closed='2' WHERE requestid=".$id."";	
mysqli_query($conn,$insert) or die('Could not run Query: ' . mysqli_error($conn));
$_SESSION['msg'] = '<ul class="states"><li class="warning">' . _('Warning: Requisition No. '). $id . ' ' . _('has been closed because all the items for this request has been cancelled.'). '</li></ul>';
header('location:../IRQ_PurchaseOrService.php?Ref=Inbox');
exit;
}
unset($_SESSION['RequestStockID']);
unset($_SESSION['Requisition_No']);
unset($_SESSION['RFQStockID']);
unset($_SESSION['RFQReq_No']);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(isset($_POST['SubmitType']) && $_POST['PurchaseItems']){
if (isset($_POST['RequestType']) && $_POST['RequestType']=='LPO') {
$_SESSION['Requisition_No'] = $id;
$query='SELECT stockid,quantity FROM irq_stockrequestitems
							WHERE dispatchid='.$id.'';
					$RequestStockID = array();
					$results= mysqli_query($conn,$query) or die (mysqli_error($conn));
					while($row=mysqli_fetch_array($results)){
					$RequestStockID = array(
											 'SID'=> $row['stockid'],
											 'QTY'=>  $row['quantity']
											);
					$_SESSION['RequestStockID'][]=$RequestStockID;
					}
	header('location:../PO_Header.php?NewOrder=Yes');

	}elseif(isset($_POST['RequestType']) && $_POST['RequestType']=='RFQ'){
	$_SESSION['RFQReq_No'] = $id;
	$query='SELECT stockid,quantity FROM irq_stockrequestitems
							WHERE dispatchid='.$id.'';
					$RequestStockID = array();
					$results= mysqli_query($conn,$query) or die (mysqli_error($conn));
					while($row=mysqli_fetch_array($results)){
					$RequestStockID = array(
											 'SID'=> $row['stockid'],
											 'QTY'=>  $row['quantity']
											);
					$_SESSION['RFQStockID'][]=$RequestStockID;
					}
	header('location:../SupplierTenderCreate.php?New=Yes');
	}
}
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
ob_end_flush();
$Sn = "SELECT * FROM purchorders WHERE  requisitionno='". $id ."'";
		$sultn=mysqli_query($conn,$Sn);
		$Ordernums=mysqli_num_rows($sultn);					
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
    width:80%;
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
	z-index: 999px;
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
.link{font-size:9px;}
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
		$insert = "UPDATE irq_authorize_state SET Unread='1'
					WHERE requisitionid=".$id." AND level=".$row['level']."";
					mysqli_query($conn,$insert) or die('Could not run Query: ' . mysqli_error($conn));
					
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
		if($final == TRUE or $closed == TRUE){
			if(isset($_SESSION['Doc_Store'])){
		echo '<tr>
						<th>' . _('Product') . '</th>
						<th>' . _('Stock') . '<br />' . _('Balance') . '</th>
						<th>' . _('Quantity') . '<br />' . _('Required') . '</th>
						<th>' . _('Quantity') . '<br />' . _('Delivered') . '</th>
						<th>' . _('Units') . '</th>
						<th>' . _('Completed') . '</th>
						<th>' . _('Tag') . '</th>
						<th>' . _('Request for') . '<br />' . _('Purchase') . '</th>
					</tr>';
					}else{
						echo '<tr>
						<th>' . _('Item Code') . '</th>
						<th>' . _('Product') . '</th>
						<th>' . _('Quantity') . '<br />' . _('Required') . '</th>
						<th>' . _('UOM') . '</th>
					</tr>';
					}
					}else{
		echo '<tr>
						<th>' . _('Item Code') . '</th>
						<th>' . _('Product') . '</th>
						<th>' . _('Quantity') . '<br />' . _('Required') . '</th>
						<th>' . _('UOM') . '</th>
						<th>' . _('Cancel Line') . '</th>
					</tr>';
					}

		if($closed == TRUE){
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
		}else{
				$query = "SELECT *, locstock.quantity as stkbal, irq_stockrequestitems.quantity as quantity FROM stockmaster INNER JOIN irq_stockrequestitems
							ON irq_stockrequestitems.stockid = stockmaster.stockid
							INNER JOIN locstock
							ON locstock.stockid = stockmaster.stockid
							WHERE irq_stockrequestitems.dispatchid='".$id."' AND locstock.loccode='". $loccode ."' AND irq_stockrequestitems.quantity-irq_stockrequestitems.qtydelivered >0 AND irq_stockrequestitems.completed=0";
					$results= mysqli_query($conn,$query) or die (mysqli_error($conn));
					while($row=mysqli_fetch_array($results)){
					
		if($final == TRUE){ //start final approver to do
		if($row['cancelled'] ==1){ //start if cancelled
		echo '<tr class="strikeout">
					<td>' . $row['description'] . '</td>
					<td>' . $row['stkbal'] . '</td>
					<td align="center">' . ($row['quantity']-$row['qtydelivered']). '</td>
					<td align="center"><b>Cancelled By '. $row['cancelled_by'] . '</b></td>
					<td align="center">' . $row['uom'] . '</td>
					<td align="center"><input type="checkbox" disabled="true" name="'. $row['dispatchid'] . 'Completed'. $row['dispatchitemsid'] . '" /></td>
					<td align="center"><select disabled="true" name="'. $row['dispatchid'] . 'Tag'. $row['dispatchitemsid'] . '">';

			$SQL = "SELECT tagref,
							tagdescription
						FROM tags
						ORDER BY tagref";

			$TagResult=mysqli_query($conn,$SQL);
			echo '<option value=0>0 - None</option>';
			while ($mytagrow=mysqli_fetch_array($TagResult)){
				if (isset($_SESSION['Adjustment']->tag) and $_SESSION['Adjustment']->tag==$mytagrow['tagref']){
					echo '<option selected="selected" value="' . $mytagrow['tagref'] . '">' . $mytagrow['tagref'].' - ' .$myrow['tagdescription'] . '</option>';
				} else {
					echo '<option value="' . $mytagrow['tagref'] . '">' . $mytagrow['tagref'].' - ' .$mytagrow['tagdescription'] . '</option>';
				}
			}
			echo '</select></td>';
// End select tag
			echo '</tr>';
		 
		}else{ //if cancelled else
		if(isset($_SESSION['Doc_Store'])){ //start if session doc_store
		?>
				<form action="../IRQ_InternalStockRequestFulfill.php" method="post" enctype="multipart/form-data" onsubmit="return confirm('Are you sure you want to submit this Request?') && mySubmit();" target="_top">
		<?php
		echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
		echo '<tr>
					<td>' . $row['description'] . '</td>
					<td>' . $row['stkbal'] . '</td>
					<td align="center">' . ($row['quantity']-$row['qtydelivered']). '</td>
					<td align="center">';
			if( $row['stkbal']>0){		
		echo '<input type="text" class="number" style="width:80px" name="'. $row['dispatchid'] . 'Qty'. $row['dispatchitemsid'] . '" value="'.($row['quantity']-$row['qtydelivered']).'" />';
			}else{
			if($row['on_order']==1){
			echo '<input type="text" disabled="true" class="number" style="width:80px" name="'. $row['dispatchid'] . 'Qty'. $row['dispatchitemsid'] . '" value="'.($row['quantity']-$row['qtydelivered']).'" />';
			}else{
			?>
		<a class="link" onclick="return confirm('Are you sure you want to submit this Request?') && mySubmit();" href="../IRQ_InternalStockRequestFulfill.php?StockIT=<?php echo $row['stockid'].'&dispatch='.$row['dispatchid']; ?>" target="_top">Request for Purchase</a>
		<?php
				}
			}
		echo '</td>
					<td align="center">' . $row['uom'] . '</td>
					<td align="center"><input type="checkbox" name="'. $row['dispatchid'] . 'Completed'. $row['dispatchitemsid'] . '" /></td>
					<td align="center"><select name="'. $row['dispatchid'] . 'Tag'. $row['dispatchitemsid'] . '">';

			$SQL = "SELECT tagref,
							tagdescription
						FROM tags
						ORDER BY tagref";

			$TagResult=mysqli_query($conn,$SQL);
			echo '<option value=0>0 - None</option>';
			while ($mytagrow=mysqli_fetch_array($TagResult)){
				if (isset($_SESSION['Adjustment']->tag) and $_SESSION['Adjustment']->tag==$mytagrow['tagref']){
					echo '<option selected="selected" value="' . $mytagrow['tagref'] . '">' . $mytagrow['tagref'].' - ' .$myrow['tagdescription'] . '</option>';
				} else {
					echo '<option value="' . $mytagrow['tagref'] . '">' . $mytagrow['tagref'].' - ' .$mytagrow['tagdescription'] . '</option>';
				}
			}
			echo '</select></td>';
// End select tag
			echo '<td align="center">';
			if($row['on_order']==1){
			echo 'On Order';
			}else{
			echo '<input type="checkbox" name="StockIT[]" value="'.$row['stockid'].'" />';
			}
			echo '</td>';
			echo '</tr>';
			echo '<input type="hidden" class="number" name="dispatch" value="'.$row['dispatchid'].'" />';
			echo '<input type="hidden" class="number" name="'. $row['dispatchid'] . 'StockID'. $row['dispatchitemsid'] . '" value="'.$row['stockid'].'" />';
			echo '<input type="hidden" class="number" name="'. $row['dispatchid'] . 'Location'. $row['dispatchitemsid'] . '" value="'.$loccode.'" />';
			echo '<input type="hidden" class="number" name="'. $row['dispatchid'] . 'RequestedQuantity'. $row['dispatchitemsid'] . '" value="'.($row['quantity']-$row['qtydelivered']).'" />';
			echo '<input type="hidden" class="number" name="'. $row['dispatchid'] . 'Department'. $row['dispatchitemsid'] . '" value="'.$dept.'" />';
			echo '<input type="hidden" class="number" name="'. $row['dispatchid'] . 'StockBal'. $row['dispatchitemsid'] . '" value="'.$row['stkbal'].'" />';
	
		}else{ //start else session doc_store
		?>
		<form action="" method="post" enctype="multipart/form-data" onsubmit="return confirm('Are you sure you want to submit this Request?') && mySubmit();" target="_top">
		<?php
		echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
		echo '<tr>
					<td align="center">'.$row['stockid'].'</td>
					<td>' . $row['description'] . '</td>
					<td align="center">' . ($row['quantity']-$row['qtydelivered']). '</td>
					<td align="center">' . $row['uom'] . '</td>';
		
		}//end if session doc_store
		} //end if cancelled
		}	//end final approver to do
		else{
		if($row['cancelled']==1){
		echo '<tr class="strikeout"><td align="center">'.$row['stockid'].'</td>';
		echo '<td align="center">'.$row['description'].'</td>';
		echo '<td align="center">'.$row['quantity'].'</td>';
		echo '<td align="center">'.$row['uom'].'</td>';
		echo '<td align="center"><b>Cancelled By '. $row['cancelled_by'] . '</b></td></tr>'; 
		}else{
		?>
		<form action="" method="post" enctype="multipart/form-data" onsubmit="return confirm('Are you sure you want to submit this Request?') && mySubmit();" target="_top">
		<?php
		echo '<tr><td align="center">'.$row['stockid'].'</td>';
		echo '<td align="center">'.$row['description'].'</td>';
		echo '<td align="center">'.$row['quantity'].'</td>';
		echo '<td align="center">'.$row['uom'].'</td>';
		echo '<td align="center"><input name="lineitem[]" type="checkbox" value="'.$row['itemid'].'" /></td></tr>';
		}
		}
		}
		}// end closed
		echo '</table>';
		echo '</div>';
		$SQL = "SELECT COUNT(on_order) FROM irq_stockrequestitems WHERE dispatchid='".$id."' AND on_order=0";
		$Count=mysqli_query($conn,$SQL);
		$mycount=mysqli_fetch_row($Count);
		if(isset($_SESSION['Doc_Store']) && $final == TRUE && $mycount[0]>0){
		echo '<div style="width:81%" align="right"><input name="Request" type="submit" value="Request" /></div>';
		}
		echo '<br />';
		echo '<br />';
		echo '<div class="line"> ';
		echo '<span class="bg">Attachments</span>';
		echo '</div><br />';
		echo '<a style="width:20%" href="../PrintReq_Item_Service.php?id='.$id.'" ><img src="images/pdf.gif" /> Download File</a>';
		echo '<br /><br />';
		echo '<div class="line"> ';
		echo '</div>';
		
		if($sent ==FALSE){
		if($final == FALSE){
		echo '<div style="font-size: large; width:90%" align="left" >Comment: </div>';
		echo '<textarea required="required" name="comment" cols="75" rows="3"></textarea>';
		echo '<br /><br />';
		echo '<div style="font-size: large; width:90%;" align="centre" >';
		if($decision == 1){
		echo '<span id="submitButton">';
		echo '<input style="background:#FF3300; width:130px;" name="Decline" type="submit" value="<< Decline" /> &nbsp;';
		echo '</span>';
		} //end if decision
		}else{
		//echo '<br />';
		}//end if final
		if($closed == FALSE){
		if($final == TRUE && !isset($_SESSION['Doc_Store'])){
		if($Ordernums >0){
		echo '<div class="info">' . _('<strong>Info!</strong>  Purchase Order for Requisition No. '). $id . ' ' . _('has been Placed. Please be patient as we follow up Supplier for supply of those Items.'). '</div>';
		}else{
		echo '
				<fieldset style="width:30%; border-radius:6px;"><legend><strong>Select Request Type</strong></legend>
				<table><tr><td>
					<input type="radio" name="RequestType" value="LPO" />
					Create a Purchase Order</label>
				  </td></tr><tr><td>
				  <label>
					<input type="radio" name="RequestType" value="RFQ" />
					Request For Quotation</label>
				  </td></tr></table>
				  </fieldset>
		';
		echo '<br />';
		echo '<input type="hidden" name="PurchaseItems" value="PurchaseItems" />';
		echo '<span id="submitButton">';
		echo '<input name="SubmitType" type="submit" value="Continue >>" />';
		echo '</span>';
		}
		}else{
		if($final == TRUE && isset($_SESSION['Doc_Store'])){
		echo '<table><tr><td>';
		echo '<label>
		    <input type="radio" name="fulfil" value="Issue" />
		    Issue Stock Request?</label>';
		echo '</td></tr><tr><td>';
		echo ' <label>
		    <input type="radio" name="fulfil" value="Transfer" />
		    Make Location Transfer?</label>';
		echo '</td></tr><tr><td>';
		echo '<span id="submitButton">';
		echo '<input name="Approve" type="submit"  value="Continue >>" />';
		echo '</span>';
		echo '</td></tr></table>';
		}else{
		echo '<span id="submitButton">';
		echo '<input name="Approve" type="submit"  value="Continue >>" />';
		echo '</span>';
		}
		}
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