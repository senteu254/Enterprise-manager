
<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php

						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$maintenance = " irq_request z 
							INNER JOIN irq_maintenance a on a.maintenanceid = z.requestid 
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN www_users y ON z.initiator = y.userid
							WHERE draft=0 AND closed=1 AND (SELECT caneditMF FROM www_users WHERE userid='".$_SESSION['UserID']."')=1 AND requestid NOT IN(SELECT requestid FROM fixedassettasks)";
						$results3 = "SELECT requestid as id, e.description, doc_name, Requesteddate, y.realname,z.doc_id FROM {$maintenance}";

						$sqlforPages = $results3;
						$sql = $results3." ORDER BY Requesteddate DESC ";
						$welcome_viewed = DB_query($sql,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($welcome_viewed);
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	?>
					<fieldset>
						<div class="box-body no-padding">
              <div class="mailbox-controls">
                <!-- Check all button -->
				
              <div class="table-responsive mailbox-messages">
			  
                <table id="myTable" style="width:100%; font-size:10px;" class="table table-hover table-striped">
				<thead>
				<th>Status</th><th>Document Name</th><th>Department</th><th>User</th><th>Requested Date</th>
				</thead>
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
					
                  echo '<tr>
                    <td width="35" class="mailbox-star"><a style="color:green;" href="#"><i class="fa fa-star text-yellow">New</i> </a></td>
                    <td class="mailbox-name"><a href="index.php?Application=FA&Link=BreakdownRead&VID='.$row['id'].'">'.strtoupper($row['doc_name']).'</a></td>
                    <td class="mailbox-subject">'.$row['description'].'</td>
                    <td class="mailbox-attachment">'.$row['realname'].'</td>
                    <td class="mailbox-date">'.date('d, M, Y H:i A',strtotime($row['Requesteddate'])).'</td>
                  </tr>';
				  }
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
	<?php
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$maintenance = " irq_request z 
							INNER JOIN irq_maintenance a on a.maintenanceid = z.requestid 
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN www_users y ON z.initiator = y.userid
							INNER JOIN fixedassettasks ft ON z.requestid = ft.requestid
							WHERE draft=0 AND closed=1 AND completed !=2 AND (SELECT caneditMF FROM www_users WHERE userid='".$_SESSION['UserID']."')=1";
						$results3 = "SELECT z.requestid as id, e.description, doc_name, Requesteddate, y.realname,z.doc_id,ft.accepted,completed FROM {$maintenance}";

						$sqlforPages = $results3;
						$sql = $results3." ORDER BY Requesteddate DESC";
						$welcome_viewed = DB_query($sql,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($welcome_viewed);
	//if($num_rows >0){					
	?>				
	
					
		</div>
		</div>
	<div class="panel panel-danger">
		<div class="panel-heading">Pending Scheduled Tasks</div>
			<div class="panel-body">
			
			<div class="table-responsive mailbox-messages">
			  
                <table id="myTable2" style="width:100%; font-size:10px;" class="table table-hover table-striped">
				<thead>
				<th>Status</th><th>Document Name</th><th>Department</th><th>User</th><th>Requested Date</th>
				</thead>
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
					if($row['accepted']==2){
					$state = '<a style="color:red;" href="#"><i class="fa fa-star">Rejected</i> </a>';
					}elseif($row['accepted']==1 && $row['completed']==0){
					$state = '<a style="color:green;" href="#"><i class="fa fa-star">Ongoing</i> </a>';
					}elseif($row['accepted']==1 && $row['completed']==1){
					$state = '<a style="color:green;" href="#"><i class="fa fa-star">Work Completed</i> </a>';
					}else{
					$state = '<a style="color:#FF6600;" href="#"><i class="fa fa-star">Active</i> </a>';
					}
                  echo '<tr>
                    <td width="35" class="mailbox-star">'.$state.'</td>
                    <td class="mailbox-name"><a href="index.php?Application=FA&Link=BreakdownRead&VID='.$row['id'].'">'.strtoupper($row['doc_name']).'</a></td>
                    <td class="mailbox-subject">'.$row['description'].'</td>
                    <td class="mailbox-attachment">'.$row['realname'].'</td>
                    <td class="mailbox-date">'.date('d, M, Y H:i A',strtotime($row['Requesteddate'])).'</td>
                  </tr>';
				  }
				  }
				  
				  ?>
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
			  <?php// } ?>
<script>
function myFunction() {
  var input, filter, table, tr, td, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>		