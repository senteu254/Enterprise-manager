<?php
if(!is_numeric($_GET['LID'])){
ob_start();
die('<b style="color:#FF0000">Invalid Request, Please try Again</b>');
}
$LID = $_GET['LID'];
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$results = "SELECT *, a.loccode, z.doc_id, a.departmentid,z.closed FROM irq_request z 
							INNER JOIN irq_stockrequest a on z.requestid = a.dispatchid
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN locations g ON a.loccode = g.loccode
							INNER JOIN www_users y ON z.initiator = y.userid
							WHERE draft=0 AND z.requestid='" . $LID . "' GROUP BY requestid";

						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
						$rows = DB_fetch_array($welcome_viewed);
						$loccode = $rows['loccode'];
						$doc = $rows['doc_id'];
						$closed = $rows['closed'];
						
$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
		$redirect = htmlspecialchars($protocol.''.$_SERVER['HTTP_HOST'].''.$_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8').'?Application=IRQ2&Link=Trash';						
if(isset($_GET['Delete']) && $_GET['id'] !=""){
$qry="DELETE FROM irq_stockrequestitems WHERE itemid='".$_GET['id']."'";
DB_query($qry);
	$q='SELECT * FROM irq_stockrequestitems a
			INNER JOIN stockmaster b ON b.stockid=a.stockid
			WHERE cancelled=0 AND completed=0 AND dispatchid='.$LID.'';
	$re= DB_query($q);
	$count = DB_num_rows($re);
	if($count ==0){
$insert = "DELETE FROM irq_request WHERE requestid=".$LID."";	
DB_query($insert);
$_SESSION['msg'] = '<ul class="states"><li class="warning">' . _('Requisition No. '). $LID . ' ' . _('has been deleted because all the items for this request has been cancelled.'). '</li></ul>';
?>
<script>
        window.location.href = "<?php echo $redirect; ?>";
	</script>
<?php
	}
 prnMsg( _('Success: Item No. '). $_GET['id'] . ' ' . _('has been deleted successfully.'), 'success');
}

if(isset($_POST['Forward'])){
$qry="UPDATE irq_stockrequestitems SET cancelled=0, completed=0 WHERE dispatchid='". $LID ."'";
DB_query($qry);
$qry2="UPDATE irq_request SET closed='0' WHERE requestid=".$LID."";	
DB_query($qry2);
$_SESSION['msg'] = '<ul class="states"><li class="warning">' . _('Requisition No. '). $LID . ' ' . _('has been re-send for approval successfully.'). '</li></ul>';
?>
<script>
        window.location.href = "<?php echo $redirect; ?>";
	</script>
<?php
}

if(isset($_POST['ChangeQty']) && $_POST['ItemCode'] !=""){
$qry="UPDATE irq_stockrequestitems SET quantity='". $_POST['Qty']."' WHERE itemid='". $_POST['ItemCode']."'";
DB_query($qry);
 prnMsg( _('Success: Quantity for Item No. '). $_POST['ItemCode'] . ' ' . _('has been updated successfully.'), 'success');
}

echo '<div id="popDiv" style="z-index: 999;
									width: 100%;
									height: 100%;
									top: 0;
									left: 0;
									display: none;
									position: absolute;				
									background-color: #fff;
									background-color: rgba(255,255,255,0.9);
									filter: alpha(opacity = 50);">';
	
	
	echo '<table style="width: 200px;
						height: 100px;
						position: absolute;
						color: #000000;
						background:#bdc3c7;
						border:none;
						/* To align popup window at the center of screen*/
						top: 50%;
						left: 50%;
						margin-top: -100px;
						margin-left: -150px;">
			<tr>
				<th><center>' . _('Update Quantity Requested') . ':</center></th>
			</tr>';
			echo '<form method="post" action="" id="form">';
			echo '<tr>
				<td><center>
				<input type="number" id="Qty" name="Qty" value="" />
				<input type="hidden" id="ItemCode" name="ItemCode" value="" />
				<input type="hidden" name="RequestNo" value="' . $LID . '" />
				<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" /></center>
				</td>
			</tr>
			<tr>
				<td><center><input type="submit" name="ChangeQty" value="' . _('Submit') . '" />
				</form>';
	echo "<input type=submit onclick=hide('popDiv') value=" . _('Cancel') . " /></center>";
	echo '</td>
			</tr>
			</table>';
	
	echo '</div>';	
?>
              
			  <div class="mailbox-read-info">
                <h4><?php echo strtoupper($rows['doc_name']); ?></h4>
                <h5>From: <?php echo ucwords(strtolower($rows['realname'])); ?>
                  <span class="mailbox-read-time pull-right"><?php echo  date("d M. Y",strtotime($rows['Requesteddate'])).' '. date("h:i A",strtotime($rows['Requesteddate'])); ?></span></h5>
              </div>
			  <legend></legend>
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message">
<span class="pull-right">
<?php  
		$comments="SELECT approver_name, approver, approvaldate, approver_comment,Unread,Sent FROM irq_levels a
	 			INNER JOIN irq_approvers b ON a.approver_id=b.approver_id
				LEFT JOIN irq_authorize_state c ON a.level_id  = c.level and requisitionid='".$LID."'
				WHERE a.doc_id=".$doc." GROUP BY a.level_id
				ORDER BY a.level_id ASC";
				
		$titles="SELECT approver_name FROM irq_levels a
	 			INNER JOIN irq_approvers b ON a.approver_id=b.approver_id
				WHERE a.doc_id=".$doc."
				GROUP BY a.level_id ORDER BY a.level_id ASC";
$first=1;
$tit = array();
$comm = array();
$commentresults= DB_query($comments);
$titleresults= DB_query($titles);
while($comment= DB_fetch_array($commentresults)){
$comm[] = $comment;
}
$tit[] ='USER';
while($title=DB_fetch_array($titleresults)){
$tit[] = $title['approver_name'];
}

for($i=0; $i < count($comm)+1 and $i < count($tit); $i++) {
if($comm[$i]['approver_comment'] !="" && $closed !=2){
$color = 'color:green;';
$check ='<i class="fa fa-check-circle-o fa-2x" style="color:green;" title="'.($i ==0 ? 'Created':'Recommended').' By '.$comm[$i]['approver'].'" aria-hidden="true"></i>';
}elseif($comm[$i]['approver_comment'] !="" && $closed==2){
$color = 'color:#FF0000;';
$check ='<i class="fa fa-times-circle-o fa-2x" style="color:#FF0000;" title="Rejected By '.$comm[$i]['approver'].'" saria-hidden="true"></i>';
}else{
$color = 'color:#CCCCCC;';
$check ='<i class="fa fa-circle-o fa-2x" style="color:#CCCCCC;" aria-hidden="true"></i>';
}
if($i !=0){
echo '&nbsp;<i class="fa fa-arrow-right" style="'.$color.'"></i>';
}
echo '<span class="fa-stack fa-2x" style="font-size:17px;">';
echo $check; 
echo '</span>';
echo '<span style="font-size:10px;'.$color.'">'.strtoupper($tit[$i]).'</span>';; 
}
?>
</span>
                <p>Hello Sir/Madam,</p>
				<table align="left" style="width:100%">
				<tr><td height="25">Request No.:</td><td><?php echo '<b>'.$rows['requestid'].'</b>'; ?></td>
				<td width="120" height="25">Department:</td><td><?php echo ' <b>'.strtoupper($rows['description']).'</b>'; ?></td></tr>
				<tr><td height="25">Store Location: </td><td><?php echo '<b>'.strtoupper($rows['locationname']).'</b>'; ?></td>
				<td height="25">Required Date: </td><td><?php echo '<b>'.date("d, M Y",strtotime($rows['despatchdate'])).'</b>'; ?></td></tr>
				<tr><td height="25">Reason: </td><td colspan="3"><?php echo '<b>'.$rows['narrative'].'</b>'; ?></td></tr>
				</table><br>
				
				<?php
				echo '<table style="width:90%;" class="table table-hover table-striped"><tbody>';
				echo '<tr>
						<th width="15%">' . _('Item Code') . '</th>
						<th width="50%">' . _('Product') . '</th>
						<th width="15%">' . _('Stock Bal') . '</th>
						<th width="10%">' . _('Qty Req') . '</th>
						<th width="10%">' . _('UOM') . '</th>
						<th width="10%"></th>
					</tr>';

			$query = "SELECT *, locstock.quantity as stkbal, irq_stockrequestitems.quantity as qty FROM irq_stockrequestitems,stockmaster,locstock
							WHERE irq_stockrequestitems.stockid = stockmaster.stockid AND locstock.stockid = stockmaster.stockid AND irq_stockrequestitems.dispatchid='".$LID."' AND locstock.loccode='". $loccode ."'";
					$results= DB_query($query);
					while($row=DB_fetch_array($results)){
				echo '<tr>
					<tr><td><center>'.$row['stockid'].'</center></td>
					<td>' . $row['longdescription'] . '</td>
					<td><center>' . $row['stkbal'] . '</center></td>';
				echo "<td><center><a href=# title='Update Quantity' onclick=popqty('popDiv',".$row['itemid'].",'".$row['qty']."')>" . $row['qty']. "</a></center></td>";
				echo '<td><center>' . $row['uom'] . '</center></td>';
					?>
				<td><center><a onclick="return confirm('Are you sure you want to Delete this Item?')" style="color:#FF0000;" href=<?php echo "index.php?Application=IRQ2&Link=TrashRead&LID=".$LID."&RType=".$_GET['RType']."&id=".$row['itemid']."&Delete=1 title='Delete'><i class='fa fa-trash'></i></a></center></td>";
			echo '</tr>';
			}
			echo '<tbody></table>';
			?>
			<br />
                <p>Kind Regards,<br><?php echo ucwords(strtolower($rows['realname'])); ?></p>
				
				<br />
				
              </div>
			  <form enctype="multipart/form-data" method="post" class="form-horizontal">
			<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			?>
            <!-- /.box-footer -->
            <div class="box-footer">
             <div class="pull-right">
                <button type="submit" name="Forward" onClick="return confirm('Are you absolutely sure you want to Resend this Request?')" class="btn btn-default"><i class="fa fa-share"></i> Re-Send</button>
              </div>
			  <a href="PrintReq_Item_Service.php?<?php echo 'id='.$LID; ?>" target="_blank"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>
            </div>
			</form>
            <!-- /.box-footer -->
			<br></br>
			<?php
			$comment="SELECT * FROM irq_request a 
							INNER JOIN irq_authorize_state b on a.requestid = b.requisitionid 
							WHERE a.requestid='" . $LID . "' ORDER BY Requesteddate DESC";

$commentresults= DB_query($comment);
	
		echo '<table border="0" style="border:none; width:100%;">';
		while($myrows=DB_fetch_array($commentresults)){
		echo' <tr>
				<td align="center" width="70"><img class="image" src="images/image.jpg"  /></td>
				<td><div class="bubble"><span class="time">From: <a href="#">'.$myrows['approver'].'</a></span> <br /> '.$myrows['approver_comment'].'
				<br /><span class="time">'. calculate_time_span($myrows['approvaldate']) .'</span>
				
				</div>
			</td>
			  </tr>';
			  }
		echo '</table>';
			?>
          </div>
		  
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


a{
text-decoration:none;
}
.time {color: #999999; font-size:10px;}
.image{
		 border-radius:25px;
		 width:50px;
		 height:50px;
		 padding:20px,20px,20px,20px;
}
/*bubble*/
.bubble
{
position: relative;
width: 90%;

min-height: 10px;
padding-left:18px;
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

.link{font-size:9px;}
-->
</style>
		<script type="text/javascript">
			function popqty(div,id,qty) {
				document.getElementById(div).style.display = 'block';
				document.getElementById('Qty').value = qty;
				document.getElementById('ItemCode').value = id;
			}
			function hide(div) {
				document.getElementById(div).style.display = 'none';
				document.getElementById('Qty').value = "";
				document.getElementById('ItemCode').value = "";
			}
			//To detect escape button
			document.onkeydown = function(evt) {
				evt = evt || window.event;
				if (evt.keyCode == 27) {
					hide('popDiv');
					document.getElementById('Qty').value = "";
					document.getElementById('ItemCode').value = "";
				}
			};
		</script>