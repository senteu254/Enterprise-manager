
<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php
if (isset($_GET['CheckOutID']) && $_GET['CheckOutID'] !="") {
if($_GET['Type']=='Staff'){
	$sql = "UPDATE staff_material_register SET booked_out=1,
										booked_out_time=NOW(),
										security_out='".$_SESSION['UsersRealName']."'
						WHERE id = '".$_GET['CheckOutID']."'";
	}else{
	$sql = "UPDATE visitor_material_register SET booked_out=1,
										booked_out_time=NOW(),
										security_out='".$_SESSION['UsersRealName']."'
						WHERE id = '".$_GET['CheckOutID']."'";
	}

			$ErrMsg = _('The material could not be updated because');
			$DbgMsg = _('The SQL that was used to update the material but failed was');
			$result = DB_query($sql, $ErrMsg, $DbgMsg);
		echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success!</h4>Material record Number ' . $_GET['CheckOutID'] . ' has been Checked Out</div>';
}

$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
 
$per_page = 30; // Set how many records do you want to display per page.
$startpoint = ($page * $per_page) - $per_page;


						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$statement = " SELECT v_name, v_phoneno, b.description as gatename, a.description as name, booked_out as status, booked_in_time, id, 'Visitor' as Type, booked_out_time
										FROM visitor_material_register a
										INNER JOIN gates b ON b.GateID = a.gate
										INNER JOIN visitor_register c ON a.visitorid = c.VisitorNo
										WHERE DATE(booked_in_time) = DATE(NOW()) ".$search." ";
										
						$statement2 = " SELECT CONCAT(emp_fname, ' ', emp_mname, ' ', emp_lname) AS v_name, emp_cont AS v_phoneno, b.description as gatename, a.description as name, booked_out as status, booked_in_time, id, 'Staff' as Type, booked_out_time
										FROM staff_material_register a
										INNER JOIN gates b ON b.GateID = a.gate
										INNER JOIN employee c ON a.staffid = c.emp_id
										WHERE DATE(booked_in_time) = DATE(NOW()) ".$search." ";

						$sqlforPages = $statement." UNION ALL ".$statement2."";
						$sql = $statement."UNION ALL ".$statement2." ORDER BY status ASC, booked_in_time DESC LIMIT {$startpoint} , {$per_page}";
						$welcome_viewed = DB_query($sql,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($welcome_viewed);
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			
			 error_reporting( error_reporting() & ~E_NOTICE ); if(!empty($_SESSION['msgx'])) echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success</h4>
                ' . ucwords($_SESSION['msgx']). '
              </div>'; unset($_SESSION['msgx']); 
			 if(!empty($_SESSION['errmsg'])) echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>
                ' . ucwords($_SESSION['errmsg']). '
              </div>'; unset($_SESSION['errmsg']); 
	?>
					<fieldset>
						<div class="box-body no-padding">
              <div class="mailbox-controls">
                <!-- Check all button -->
				<div class="row">
                <div class="col-xs-1">
                <!-- /.btn-group -->
                <a href=""><button type="button" title="Refresh" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button></a>
				</div>
				<div class="col-xs-4">
				<div class="input-group input-group-sm">
                <input type="text" placeholder="Search Owner Name..." id="myInput" onkeyup="myFunction()" name="Searchfield" value="" class="form-control">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-info btn-flat"><i class="fa fa-search"></i></button>
                    </span>
              </div> 
			  </div>
			  <div class="col-xs-7">
					<div class="pull-right">
				<div class="btn-group">
				<?php
				echo pagination_inbox($sqlforPages,$per_page,$page,$url='?Application=SEC2&Link=Visitors&');
				?>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.pull-right -->
				</div>

              </div>
              <div class="table-responsive mailbox-messages">
			  
                <table id="myTable" style="width:100%;" class="table table-hover table-striped">
				<thead>
				<th>Status</th><th>Description</th><th>Owner</th><th>Phone Number</th><th>Gate</th><th>Time In</th><th>Action</th>
				</thead>
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
					
                  echo '<tr>
                    <td width="35" class="mailbox-star">'.($row['status']==0 ? '<a style="color:green;" href="#"><i class="fa fa-star text-yellow">Active</i> </a>':'<a style="color:red;" href="#"><i class="fa fa-star-o text-yellow">Out</i> </a>').'</td>
                    <td class="mailbox-name"><a href="index.php?Application=SEC2&Link=MaterialRead&VID='.$row['id'].'&Type='.$row['Type'].'">'.ucwords(strtolower($row['name'])).'</a></td>
                    <td class="mailbox-subject">'.ucwords(strtolower($row['v_name'])).'</td>
                    <td class="mailbox-attachment">'.$row['v_phoneno'].'</td>
                    <td class="mailbox-date">'.$row['gatename'].'</td>
					<td class="mailbox-date">'.date("h:i A",strtotime($row['booked_in_time'])).'</td>
					<td width="100">'.($row['status']==0 ? '<a href="index.php?Application=SEC2&Link=Materials&CheckOutID='.$row['id'].'&Type='.$row['Type'].'" onclick="return confirm(\'Are you sure you want to check out this material?\');"><button type="button" class="btn btn-danger btn-sm"><i class="fa fa-sign-out" aria-hidden="true"></i> CheckOut</button></a>':''.date("h:i A",strtotime($row['booked_out_time'])).'').'</td>
                  </tr>';
				  }
				  }else{
				  echo '<tr><td class="alert-danger" colspan="7"><center><b style="color:#FF0000">No Material Registered</b></center></td></tr>';
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
						$sql = " SELECT *, b.description as gatename, a.description as name, booked_out as status
										FROM visitor_material_register a
										INNER JOIN gates b ON b.GateID = a.gate
										INNER JOIN visitor_register c ON a.visitorid = c.VisitorNo
										WHERE DATE(booked_in_time) < DATE(NOW()) AND booked_out=0 
										ORDER BY booked_out ASC, booked_in_time DESC";
										
						$statement = " SELECT v_name, v_phoneno, b.description as gatename, a.description as name, booked_out as status, booked_in_time, id, 'Visitor' as Type, booked_out_time
										FROM visitor_material_register a
										INNER JOIN gates b ON b.GateID = a.gate
										INNER JOIN visitor_register c ON a.visitorid = c.VisitorNo
										WHERE DATE(booked_in_time) < DATE(NOW()) AND booked_out=0";
										
						$statement2 = " SELECT CONCAT(emp_fname, ' ', emp_mname, ' ', emp_lname) AS v_name, emp_cont AS v_phoneno, b.description as gatename, a.description as name, booked_out as status, booked_in_time, id, 'Staff' as Type, booked_out_time
										FROM staff_material_register a
										INNER JOIN gates b ON b.GateID = a.gate
										INNER JOIN employee c ON a.staffid = c.emp_id
										WHERE DATE(booked_in_time) < DATE(NOW()) AND booked_out=0";

						$sql = $statement." UNION ALL ".$statement2." ORDER BY status ASC, booked_in_time DESC";
						$welcome_viewed = DB_query($sql,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($welcome_viewed);
	if($num_rows >0){					
	?>				
	
					
		</div>
		</div>
	<div class="panel panel-danger">
		<div class="panel-heading">Materials with Pending Check Out</div>
			<div class="panel-body">
			
			<div class="table-responsive mailbox-messages">
			
			<table style="width:100%;" class="table table-hover table-striped">
				<thead>
				<th>Status</th><th>Description</th><th>Owner</th><th>Phone Number</th><th>Gate</th><th>Time In</th><th>Action</th>
				</thead>
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
					
                  echo '<tr>
                    <td class="mailbox-star">'.($row['status']==0 ? '<a style="color:red;" href="#"><i class="fa fa-star text-yellow">Active for '.calculate_time_span($row['booked_in_time']).'</i> </a>':'<a style="color:red;" href="#"><i class="fa fa-star-o text-yellow">Out</i> </a>').'</td>
                    <td class="mailbox-name"><a href="index.php?Application=SEC2&Link=MaterialRead&VID='.$row['id'].'&Type='.$row['Type'].'">'.ucwords(strtolower($row['name'])).'</a></td>
                    <td class="mailbox-subject">'.ucwords(strtolower($row['v_name'])).'</td>
                    <td class="mailbox-attachment">'.$row['v_phoneno'].'</td>
                    <td class="mailbox-date">'.$row['gatename'].'</td>
					<td class="mailbox-date">'.date("h:i A",strtotime($row['booked_in_time'])).'</td>
					<td width="100">'.($row['status']==0 ? '<a href="index.php?Application=SEC2&Link=Materials&CheckOutID='.$row['id'].'" onclick="return confirm(\'Are you sure you want to check out this material?\');"><button type="button" class="btn btn-danger btn-sm"><i class="fa fa-sign-out" aria-hidden="true"></i> CheckOut</button></a>':''.date("h:i A",strtotime($row['booked_out_time'])).'').'</td>
                  </tr>';
				  }
				  }else{
				  echo '<tr><td class="alert-danger" colspan="7"><center><b style="color:#FF0000">No Material Registered</b></center></td></tr>';
				  }
				  
				  ?>
                  </tbody>
                </table>
			  
                <!-- /.table -->
              </div>
			  <?php } ?>
<script>
function myFunction() {
  var input, filter, table, tr, td, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[2];
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