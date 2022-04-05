
<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php
if(isset($_POST['Delete'])){
	$id = $_POST['ids'];
	for($i=0; $i<count($id); $i++){
		$delete = "DELETE FROM irq_request WHERE requestid=".$id[$i]."";
		$welcome_viewed = DB_query($delete,$ErrMsg,$DbgMsg);
	}
	echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>Record(s) Deleted Successfully.</div>';
}
if(isset($_POST['Forward'])){
	$id = $_POST['ids'];
	for($i=0; $i<count($id); $i++){
	
	}
	echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>Record(s) Forwarded Successfully.</div>';
}


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
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN locations g ON a.loccode = g.loccode
							INNER JOIN www_users y ON z.initiator = y.userid
							WHERE draft=1 AND ".$search." z.initiator='".$_SESSION['UserID']."'";
						$transport = " irq_request z 
							INNER JOIN irq_transport a on a.TransportID = z.requestid 
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN www_users y ON z.initiator = y.userid
							WHERE draft=1 AND ".$search." z.initiator='".$_SESSION['UserID']."'";
						$maintenance = " irq_request z 
							INNER JOIN irq_maintenance a on a.maintenanceid = z.requestid 
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN www_users y ON z.initiator = y.userid
							WHERE draft=1 AND ".$search." z.initiator='".$_SESSION['UserID']."'";
						$gatepass = " irq_request z 
							INNER JOIN irq_gatepass a on a.gatepassid = z.requestid 
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN www_users y ON z.initiator = y.userid
							WHERE draft=1 AND ".$search." z.initiator='".$_SESSION['UserID']."'";
  
						$results = "SELECT requestid as id, e.description, doc_name, Requesteddate, y.realname,z.doc_id FROM {$statement}";
						$results2 = "SELECT requestid as id, e.description, doc_name, Requesteddate, y.realname,z.doc_id FROM {$transport}";
						$results3 = "SELECT requestid as id, e.description, doc_name, Requesteddate, y.realname,z.doc_id FROM {$maintenance}";
						$results4 = "SELECT requestid as id, e.description, doc_name, Requesteddate, y.realname,z.doc_id FROM {$gatepass}";
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
                <div class="btn-group">
				<button type="Submit" name="Delete" onClick="return confirm('Are you absolutely sure you want to delete?')" title="Delete" class="btn btn-default btn-sm"><i class="fa fa-trash"></i></button>
                  <button type="Submit" name="Forward" onClick="return confirm('Are you absolutely sure you want to Forward?')" title="Forward" class="btn btn-default btn-sm"><i class="fa fa-share"></i></button>
                </div>
                <!-- /.btn-group -->
                <a href="index.php?Application=IRQ2&Link=Draft"><button type="button" title="Refresh" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button></a>
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
				<?php } ?>
                <div class="pull-right">
				<div class="btn-group">
				<?php
				echo pagination_inbox($sqlforPages,$per_page,$page,$url='?Application=IRQ2&Link=Draft&');
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
					$RType = 'DraftRead';
					}elseif($row['doc_id']==2 || $row['doc_id']==3){
					$RType = 'DraftReadTransport';
					}elseif($row['doc_id']==5 || $row['doc_id']==6){
					$RType = 'DraftReadMaintenance';
					}elseif($row['doc_id']==7 || $row['doc_id']==8){
					$RType = 'DraftReadGatePass';
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