<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php

$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
 
$per_page = 30; // Set how many records do you want to display per page.
$startpoint = ($page * $per_page) - $per_page;

if(isset($_POST['Searchfield']) && $_POST['Searchfield'] !=""){
$search=" (requestid LIKE '%".$_POST['Searchfield']."%' OR y.realname LIKE '%".$_POST['Searchfield']."%' OR e.description LIKE '%".$_POST['Searchfield']."%') AND ";
}else{
$search="";
}

						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$statement = " irq_request z 
							INNER JOIN irq_stockrequest a on z.requestid = a.dispatchid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN locations g ON a.loccode = g.loccode
							INNER JOIN www_users y ON z.initiator = y.userid
							WHERE closed=2 AND ".$search." (z.initiator='".$_SESSION['UserID']."' OR
							CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' 
							WHEN d.userid ='ISSUE' THEN g.authoriser='".$_SESSION['UserID']."' 
							WHEN d.userid ='PROCURE' THEN g.purchasing_officer='".$_SESSION['UserID']."' 
							ELSE d.userid='".$_SESSION['UserID']."' END)";
						$transport = " irq_request z 
							INNER JOIN irq_transport a on a.TransportID = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN www_users y ON z.initiator = y.userid
							WHERE closed=2 AND ".$search." (z.initiator='".$_SESSION['UserID']."' OR
							CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' 
							ELSE d.userid='".$_SESSION['UserID']."' END)";
						$maintenance = " irq_request z 
							INNER JOIN irq_maintenance a on a.maintenanceid = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN www_users y ON z.initiator = y.userid
							WHERE closed=2 AND ".$search." (z.initiator='".$_SESSION['UserID']."' OR
							CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' 
							ELSE d.userid='".$_SESSION['UserID']."' END)";
						$gatepass = " irq_request z 
							INNER JOIN irq_gatepass a on a.gatepassid = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN www_users y ON z.initiator = y.userid
							WHERE closed=2 AND ".$search." (z.initiator='".$_SESSION['UserID']."' OR
							CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' 
							ELSE d.userid='".$_SESSION['UserID']."' END)";
  
						$results = "SELECT requestid as id, e.description, doc_name, Requesteddate, y.realname,z.doc_id FROM {$statement} GROUP BY requestid";
						$results2 = "SELECT requestid as id, e.description, doc_name, Requesteddate, y.realname,z.doc_id FROM {$transport} GROUP BY requestid";
						$results3 = "SELECT requestid as id, e.description, doc_name, Requesteddate, y.realname,z.doc_id FROM {$maintenance} GROUP BY requestid";
						$results4 = "SELECT requestid as id, e.description, doc_name, Requesteddate, y.realname,z.doc_id FROM {$gatepass} GROUP BY requestid";
						$sqlforPages = $results." UNION ALL ".$results2." UNION ALL ".$results3." UNION ALL ".$results4."";
						$sql = $results." UNION ALL ".$results2." UNION ALL ".$results3." UNION ALL ".$results4." ORDER BY Requesteddate DESC LIMIT {$startpoint} , {$per_page}";
						$welcome_viewed = DB_query($sql,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($welcome_viewed);
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	?>
					<fieldset>
						<div class="box-body no-padding">
              <div class="mailbox-controls">
                <!-- Check all button -->
				<?php if($num_rows>0){ ?>
				<div class="row">
                <div class="col-xs-8">
                <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                </button>
                <!-- /.btn-group -->
                <a href="index.php?Application=IRQ2&Link=Trash"><button type="button" title="Refresh" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button></a>
				</div>
				<div class="col-xs-4">
				<form enctype="multipart/form-data" method="post">
				<div class="input-group input-group-sm">
                <input type="text" placeholder="Search..." name="Searchfield" value="<?php echo isset($_POST['Searchfield']) ? $_POST['Searchfield'] : ""; ?>" class="form-control">
                    <span class="input-group-btn">
                      <button type="submit" class="btn btn-info btn-flat"><i class="fa fa-search"></i></button>
                    </span>
              </div>
			  </form>
			  </div>
			  </div>
				<?php  } ?>
                <div class="pull-right">
				<div class="btn-group">
				<?php
				echo pagination_inbox($sqlforPages,$per_page,$page,$url='?Application=IRQ2&Link=Trash&');
				?>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.pull-right -->
              </div>
              <div class="table-responsive mailbox-messages">
			  
                <table style="width:100%" class="table table-hover table-striped">
                  <tbody>
				  <?php
				 if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
					$words = '<b>'.$row['doc_name'].'</b> - '.$row['description'];
					$truncated = (strlen($words) > 73) ? substr($words, 0, 73).'...' : $words;
					$realname = (strlen($row['realname']) > 21) ? substr($row['realname'], 0, 21).'...' : $row['realname'];
					if($row['doc_id']==1 || $row['doc_id']==4){
					$RType = 'TrashRead';
					}elseif($row['doc_id']==2 || $row['doc_id']==3){
					$RType = 'TrashReadTransport';
					}elseif($row['doc_id']==5 || $row['doc_id']==6){
					$RType = 'TrashReadMaintenance';
					}elseif($row['doc_id']==7 || $row['doc_id']==8){
					$RType = 'TrashReadGatePass';
					}
                  echo '<tr>
                    <td width="35"><input type="checkbox" name="ids[]" value="'.$row['id'].'"></td>
                    <td width="35" class="mailbox-star"><a href="#"><i class="fa fa-star text-yellow"></i></a></td>
                    <td width="170" class="mailbox-name"><a href="index.php?Application=IRQ2&Link='.$RType.'&LID='.$row['id'].'">'.ucwords(strtolower($realname)).'</a></td>
                    <td class="mailbox-subject">'.$truncated.'
                    </td>
                    <td class="mailbox-attachment"></td>
                    <td width="110" class="mailbox-date">'.calculate_time_span($row['Requesteddate']).'</td>
                  </tr>';
				  }
				  }else{
				  echo '<tr><td class="alert-danger" colspan="6"><center><b style="color:#FF0000">No Record to Display</b></center></td></tr>';
				  }
				  
				  ?>
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>

						</fieldset>
					</form>