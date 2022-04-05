<?php
if(!is_numeric($_GET['LID'])){
ob_start();
die('<b style="color:#FF0000">Invalid Request, Please try Again</b>');
}
$LID = $_GET['LID'];
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
				if($_GET['L_Type']==1){	
						$results = "SELECT *, b.emp_id as em_num FROM leave_off_duty a
							INNER JOIN employee b ON a.emp_id=b.emp_id
							INNER JOIN www_users c ON a.added_by=c.userid
							INNER JOIN leave_types d ON a.leave_type=d.id
							LEFT JOIN departments e ON e.departmentid= b.id_dept
							LEFT JOIN section f ON b.id_sec=f.id_sec
							WHERE send = 1 AND off_id=".$LID."";
				}elseif($_GET['L_Type']==2){	
						$results = "SELECT *, b.emp_id as em_num FROM leave_half_day a
							INNER JOIN employee b ON a.emp_id=b.emp_id
							INNER JOIN www_users c ON a.added_by=c.userid
							INNER JOIN leave_types d ON a.leave_type=d.id
							LEFT JOIN departments e ON e.departmentid= b.id_dept
							LEFT JOIN section f ON b.id_sec=f.id_sec
							WHERE send = 1 AND half_id=".$LID."";
				}elseif($_GET['L_Type']>=3){	
						$results = "SELECT *, b.emp_id as em_num,z.type_name FROM leave_annual a
							INNER JOIN employee b ON a.emp_id=b.emp_id
							INNER JOIN www_users c ON a.added_by=c.userid
							INNER JOIN leave_types d ON a.leave_type=d.id
							INNER JOIN leave_all_types z ON a.type=z.id
							LEFT JOIN departments e ON e.departmentid= b.id_dept
							LEFT JOIN section f ON b.id_sec=f.id_sec
							WHERE send = 1 AND leave_id=".$LID."";
				}else{
				die('<b style="color:#FF0000">Invalid Request, Please try Again</b>');
				}
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
						$rows = DB_fetch_array($welcome_viewed);
		

?>
              
			  <div class="mailbox-read-info">
                <h4><?php echo $rows['type_name']; ?></h4>
                <h5>From: <?php echo ucwords(strtolower($rows['realname'])); ?>
                  <span class="mailbox-read-time pull-right"><?php echo  date("d M. Y",strtotime($rows['date_added'])).' '. date("h:i A",strtotime($rows['date_added'])); ?></span></h5>
              </div>
			  <legend></legend>
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message">
<span class="pull-right">
<?php  

$rest = "SELECT *,levelcheck as lc FROM leave_approval_levels a
				LEFT JOIN leave_approval b ON a.levelcheck=b.approver AND b.leave_id='".$LID."'
				WHERE a.leave_type='".$rows['id']."'";
$welcom = DB_query($rest,$ErrMsg,$DbgMsg);
while($rowb = DB_fetch_array($welcom)){
if($rowb['lc'] ==1 && $rows['approver1'] !=NULL && $rowb['status']==1){
$color = 'color:green;';
$check ='<i class="fa fa-check-circle-o fa-2x" style="color:green;" title="'.$rowb['accept_msg'].' By '.$rowb['approver_name'].'" aria-hidden="true"></i>';
}elseif($rowb['lc'] ==1 && $rows['approver1'] !=NULL && $rowb['status']==2){
$color = 'color:#FF0000;';
$check ='<i class="fa fa-times-circle-o fa-2x" style="color:#FF0000;" title="'.$rowb['accept_msg'].' By '.$rowb['approver_name'].'" saria-hidden="true"></i>';
}elseif($rowb['lc'] ==2 && $rows['approver2'] !=NULL && $rowb['status']==1){
$color = 'color:green;';
$check ='<i class="fa fa-check-circle-o fa-2x" style="color:green; " title="'.$rowb['accept_msg'].' By '.$rowb['approver_name'].'" aria-hidden="true"></i>';
}elseif($rowb['lc'] ==2 && $rows['approver2'] !=NULL && $rowb['status']==2){
$color = 'color:#FF0000;';
$check ='<i class="fa fa-times-circle-o fa-2x" style="color:#FF0000;" title="'.$rowb['accept_msg'].' By '.$rowb['approver_name'].'" saria-hidden="true"></i>';
}elseif($rowb['lc'] ==3 && $rows['approver3'] !=NULL && $rowb['status']==1){
$color = 'color:green;';
$check ='<i class="fa fa-check-circle-o fa-2x" style="color:green;" title="'.$rowb['accept_msg'].' By '.$rowb['approver_name'].'" aria-hidden="true"></i>';
}elseif($rowb['lc'] ==3 && $rows['approver3'] !=NULL && $rowb['status']==2){
$color = 'color:#FF0000;';
$check ='<i class="fa fa-times-circle-o fa-2x" style="color:#FF0000;" title="'.$rowb['accept_msg'].' By '.$rowb['approver_name'].'" saria-hidden="true"></i>';
}elseif($rowb['lc'] ==4 && $rows['approver4'] !=NULL && $rowb['status']==1){
$color = 'color:green;';
$check ='<i class="fa fa-check-circle-o fa-2x" style="color:green; " title="'.$rowb['accept_msg'].' By '.$rowb['approver_name'].'" aria-hidden="true"></i>';
}elseif($rowb['lc'] ==4 && $rows['approver4'] !=NULL && $rowb['status']==2){
$color = 'color:#FF0000;';
$check ='<i class="fa fa-times-circle-o fa-2x" style="color:#FF0000;" title="'.$rowb['accept_msg'].' By '.$rowb['approver_name'].'" saria-hidden="true"></i>';
}else{
$color = 'color:#CCCCCC;';
$check ='<i class="fa fa-circle-o fa-2x" style="color:#CCCCCC;" aria-hidden="true"></i>';
}
if($rowb['lc'] !=1){
echo '&nbsp;<i class="fa fa-arrow-right" style="'.$color.'"></i>';
}
echo '<span class="fa-stack fa-2x" style="font-size:17px;">';
echo $check; 
echo '</span>';
echo '<span style="font-size:10px;'.$color.'">'.$rowb['level_position'].'</span>';; 
}
?>
</span>
                <p>Hello Sir/Madam,</p>
				<table style="width:100%">
				<tr><td width="120" height="25">Name:</td><td><?php echo ' <b>'.strtoupper($rows['emp_lname'].' '.$rows['emp_fname'].' '.$rows['emp_mname']).'</b>'; ?></td></tr>
				<tr><td height="25">Personal No.:</td><td><?php echo '<b>'.$rows['em_num'].'</b>'; ?></td></tr>
				<tr><td height="25">Department: </td><td><?php echo '<b>'.strtoupper($rows['description']).'</b>'; ?></td></tr>
				<tr><td height="25">Section: </td><td><?php echo '<b>'.strtoupper($rows['section_name']).'</b>'; ?></td></tr>
				</table><br>
				<p><?php echo $rows['narrative']; ?></p>
				<?php if($_GET['L_Type']>=3){ ?>
				<p>Incase I cannot be reached Please contact</p>
				<p><?php echo '<b>'.strtoupper($rows['em_name']).'</b>'; ?></p>
				<p><?php echo '<b>'.strtoupper($rows['em_address']).'</b>'; ?></p>
				<p><?php echo '<b>'.strtoupper($rows['em_phone']).'</b>'; ?></p>
				<?php } ?>
				<br />
                <p>Thanks,<br><?php echo ucwords(strtolower($rows['realname'])); ?></p>
				
				<br />
				
				<?php
				if($rows['rejected']==1){
				echo 'Reason For Rejection:<p></p><b>'.$rows['reason'].'</b>';
				echo '<br></br>';
				}
				?>
				
              </div>
			  <form enctype="multipart/form-data" method="post" class="form-horizontal">
			<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			?>
            <!-- /.box-footer -->
            <div class="box-footer">
<!--              <div class="pull-right">
                <button type="submit" name="Reject" onClick="return confirm('Are you absolutely sure you want to Reject?')" class="btn btn-default"><i class="fa fa-reply"></i> Reject</button>
                <button type="submit" name="Forward" onClick="return confirm('Are you absolutely sure you want to Send?')" class="btn btn-default"><i class="fa fa-share"></i> Forward</button>
              </div>-->
			  <a href="PDFLeave.php?<?php echo 'LID='.$LID.'&L_Type='.$_GET['L_Type']; ?>" target="_blank"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>
            </div>
			</form>
            <!-- /.box-footer -->
          </div>