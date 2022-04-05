
<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php

						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$maintenance = " fixedassets z 
							INNER JOIN fixedassetlocations a on a.locationid = z.assetlocation 
							INNER JOIN fixedassetplanning e ON z.assetid = e.assetid
							INNER JOIN fixedassetplantask f ON e.planid = f.planid
							WHERE (SELECT caneditMF FROM www_users WHERE userid='".$_SESSION['UserID']."')=1 AND e.planid NOT IN(SELECT requestid FROM fixedassettasks)";
						$sql = "SELECT * FROM {$maintenance}";
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
				<th>Status</th><th>Asset Name</th><th>Location</th><th>Status</th><th>Month</th><th>Planned By</th><th>Planned Date</th>
				</thead>
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
                  echo '<tr>
                    <td width="35" class="mailbox-star"><a style="color:green;" href="#"><i class="fa fa-star text-yellow">New</i> </a></td>
                    <td class="mailbox-name"><a href="index.php?Application=FA&Link=PreventiveRead&VID='.$row['planid'].'">'.strtoupper($row['longdescription']).'</a></td>
                    <td class="mailbox-subject">'.$row['locationdescription'].'</td>
                    <td class="mailbox-attachment">'.$row['servicestatus'].'</td>
					<td class="mailbox-attachment">'.$row['month'].'</td>
					<td class="mailbox-attachment">'.$row['planningofficer'].'</td>
                    <td class="mailbox-date">'.ConvertSQLDate($row['inputdate']).'</td>
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
						$maintenance = " fixedassets z 
							INNER JOIN fixedassetlocations a on a.locationid = z.assetlocation 
							INNER JOIN fixedassetplanning e ON z.assetid = e.assetid
							INNER JOIN irq_documents f ON e.docid = f.doc_id
							INNER JOIN fixedassetplantask s ON e.planid = s.planid
							INNER JOIN fixedassettasks ft ON e.planid = ft.requestid
							WHERE completed !=2 AND (SELECT caneditMF FROM www_users WHERE userid='".$_SESSION['UserID']."')=1";
						$sql = "SELECT * FROM {$maintenance}";
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
				<th>Status</th><th>Asset Name</th><th>Location</th><th>Status</th><th>Month</th><th>Planned By</th><th>Planned Date</th>
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
                    <td class="mailbox-name"><a href="index.php?Application=FA&Link=PreventiveRead&VID='.$row['planid'].'">'.strtoupper($row['longdescription']).'</a></td>
                    <td class="mailbox-subject">'.$row['locationdescription'].'</td>
                    <td class="mailbox-attachment">'.$row['servicestatus'].'</td>
					<td class="mailbox-attachment">'.$row['month'].'</td>
					<td class="mailbox-attachment">'.$row['planningofficer'].'</td>
                    <td class="mailbox-date">'.ConvertSQLDate($row['inputdate']).'</td>
                  </tr>';
				  }
				  }
				  
				  ?>
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
			  <?php //} ?>
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