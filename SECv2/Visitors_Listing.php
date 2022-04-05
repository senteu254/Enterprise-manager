
<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php
if (isset($_POST['CheckOutID']) && $_POST['CheckOutID'] !="") {

		$sql = "UPDATE visitor_timein SET check_out=1, remarks='".$_POST['remarks']."', time_out='".date('Y-m-d H:i:s')."', sec_officer_checkout='".$_SESSION['UsersRealName']."'
		       WHERE CheckID=".$_POST['CheckOutID']."";
		$result = DB_query($sql);
		echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success!</h4>Visitor record Number ' . $_POST['VID'] . ' has been Checked Out</div>';
}

$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
 
$per_page = 30; // Set how many records do you want to display per page.
$startpoint = ($page * $per_page) - $per_page;

if(isset($_POST['Searchfield']) && $_POST['Searchfield'] !=""){
$search=" AND (v_idno LIKE '%".$_POST['Searchfield']."%' OR v_name LIKE '%".$_POST['Searchfield']."%' OR v_phoneno LIKE '%".$_POST['Searchfield']."%') ";
}else{
$search="";
}


						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$statement = " SELECT visitor_register.VisitorNo,
										v_name,
										v_idno,
										v_phoneno,
										v_from,
										date,
										IFNULL((SELECT check_out FROM visitor_timein WHERE visitor_timein.VisitorNo = visitor_register.VisitorNo AND check_out=0 GROUP BY visitor_timein.VisitorNo),1) as status
									FROM visitor_register
									INNER JOIN visitor_timein ON visitor_timein.VisitorNo = visitor_register.VisitorNo
									WHERE DATE(time_in) = DATE(NOW()) ".$search." ";

						$sqlforPages = $statement;
						$sql = $statement." GROUP BY visitor_register.VisitorNo ORDER BY  visitor_timein.time_in DESC LIMIT {$startpoint} , {$per_page}";
						$welcome_viewed = DB_query($sql,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($welcome_viewed);
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	?>
					<fieldset>
						<div class="box-body no-padding">
              <div class="mailbox-controls">
                <!-- Check all button -->
				<div class="row">
                <div class="col-xs-1">
                <!-- /.btn-group -->
                <a href="index.php?Application=SEC2&Link=Visitors"><button type="button" title="Refresh" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button></a>
				</div>
				<div class="col-xs-4">
				<form enctype="multipart/form-data" method="post">
				<div class="input-group input-group-sm">
                <input type="text" placeholder="Search Visitor Name..." id="myInput" onkeyup="myFunction()" name="Searchfield" value="<?php echo isset($_POST['Searchfield']) ? $_POST['Searchfield'] : ""; ?>" class="form-control">
                    <span class="input-group-btn">
                      <button type="submit" class="btn btn-info btn-flat"><i class="fa fa-search"></i></button>
                    </span>
              </div> 
			  </form>
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
				<th>Status</th><th>Visitor Name</th><th>ID Number</th><th>Phone Number</th><th>Residence</th><th>Action</th>
				</thead>
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
					
                  echo '<tr>
                    <td width="35" class="mailbox-star">'.($row['status']==0 ? '<a style="color:green;" href="#"><i class="fa fa-star text-yellow">Active</i> </a>':'<a style="color:red;" href="#"><i class="fa fa-star-o text-yellow">Left</i> </a>').'</td>
                    <td class="mailbox-name"><a href="index.php?Application=SEC2&Link=VisitorRead&VID='.$row['VisitorNo'].'">'.ucwords(strtolower($row['v_name'])).'</a></td>
                    <td class="mailbox-subject">'.$row['v_idno'].'</td>
                    <td class="mailbox-attachment">'.$row['v_phoneno'].'</td>
                    <td class="mailbox-date">'.$row['v_from'].'</td>
					<td width="100"><a href="Visitor_CheckOut.php?VID='.$row['VisitorNo'].'" rel="facebox"><button type="button" class="btn btn-danger btn-sm"><i class="fa fa-sign-out" aria-hidden="true"></i> CheckOut</button></a></td>
                  </tr>';
				  }
				  }else{
				  echo '<tr><td class="alert-danger" colspan="6"><center><b style="color:#FF0000">No Active Visitor</b></center></td></tr>';
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
						$statements = " SELECT visitor_register.VisitorNo,
										v_name,
										v_idno,
										v_phoneno,
										v_from,
										date,
										IFNULL((SELECT check_out FROM visitor_timein WHERE visitor_timein.VisitorNo = visitor_register.VisitorNo AND check_out=0 GROUP BY visitor_timein.VisitorNo),1) as status,
										time_in
									FROM visitor_register
									INNER JOIN visitor_timein ON visitor_timein.VisitorNo = visitor_register.VisitorNo
									WHERE DATE(time_in) < DATE(NOW()) AND check_out=0 ";

						$sql = $statements." GROUP BY visitor_register.VisitorNo ";
						$welcome_viewed = DB_query($sql,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($welcome_viewed);
	if($num_rows >0){					
	?>				
	
					
		</div>
		</div>
	<div class="panel panel-danger">
		<div class="panel-heading">Visitors with Pending Check Out</div>
			<div class="panel-body">
			
			<div class="table-responsive mailbox-messages">
			  
                <table style="width:100%;" class="table table-hover table-striped ">
				<thead>
				<th>Status</th><th>Visitor Name</th><th>ID Number</th><th>Phone Number</th><th>Residence</th><th>Action</th>
				</thead>
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
					
                  echo '<tr>
                    <td  class="mailbox-star">'.($row['status']==0 ? '<a style="color:red;" href="#"><i class="fa fa-star text-yellow">Active for '.calculate_time_span($row['time_in']).'</i> </a>':'<a style="color:red;" href="#"><i class="fa fa-star-o text-yellow">Left</i> </a>').'</td>
                    <td class="mailbox-name"><a href="index.php?Application=SEC2&Link=VisitorRead&VID='.$row['VisitorNo'].'">'.ucwords(strtolower($row['v_name'])).'</a></td>
                    <td class="mailbox-subject">'.$row['v_idno'].'</td>
                    <td class="mailbox-attachment">'.$row['v_phoneno'].'</td>
                    <td class="mailbox-date">'.$row['v_from'].'</td>
					<td width="100"><a href="Visitor_CheckOut.php?VID='.$row['VisitorNo'].'" rel="facebox"><button type="button" class="btn btn-danger btn-sm"><i class="fa fa-sign-out" aria-hidden="true"></i> CheckOut</button></a></td>';
                  echo '</tr>';
				  }
				  }else{
				  echo '<tr><td class="alert-danger" colspan="7"><center><b style="color:#FF0000">No Active Visitor</b></center></td></tr>';
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