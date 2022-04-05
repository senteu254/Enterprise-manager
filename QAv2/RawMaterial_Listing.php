
<?php
if (isset($_GET['SelectedUser'])){
	$SelectedUser = $_GET['SelectedUser'];
} elseif (isset($_POST['SelectedUser'])){
	$SelectedUser = $_POST['SelectedUser'];
}
if (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button		

			$sql="DELETE FROM qarawmatacceptance WHERE id='" . $SelectedUser . "'";
			$ErrMsg = _('The Record could not be deleted because');
			$result = DB_query($sql,$ErrMsg);
			$sql2="DELETE FROM qarawmatacceptancedata WHERE testno='" . $SelectedUser . "'";
			$result = DB_query($sql2,$ErrMsg);
			$_SESSION['msg'] =_('Record Deleted Successfully');

		unset($SelectedUser);
	}
echo '<div id="loadingbackground">';
		echo '<div id="progressBar" ><br />
			 <i class="fa fa-spinner fa-pulse fa-4x fa-fw"></i><br>PROCESSING. PLEASE WAIT...
			</div>';
		echo '</div>';
		
 error_reporting( error_reporting() & ~E_NOTICE ); if(!empty($_SESSION['msg'])) echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success</h4>
                ' . ucwords($_SESSION['msg']). '
              </div>'; unset($_SESSION['msg']); 
			 if(!empty($_SESSION['errmsg'])) echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>
                ' . ucwords($_SESSION['errmsg']). '
              </div>'; unset($_SESSION['errmsg']); 
$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
 
$per_page = 20; // Set how many records do you want to display per page.
$startpoint = ($page * $per_page) - $per_page;

if(isset($_POST['Searchfield']) && $_POST['Searchfield'] !=""){
$search=" AND (a.id LIKE '%".$_POST['Searchfield']."%' OR calibre LIKE '%".$_POST['Searchfield']."%' OR country LIKE '%".$_POST['Searchfield']."%' OR company LIKE '%".$_POST['Searchfield']."%') ";
}else{
$search="";
}

						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$statement = " qarawmatacceptance a
										INNER JOIN qa_approval_levels c ON c.type=6
										WHERE process_level =c.levelcheck AND 
										(CASE WHEN c.authoriser='QAT' THEN FIND_IN_SET('".$_SESSION['UserID']."',a.inspectors) ELSE c.authoriser='".$_SESSION['UserID']."' END)
										 {$search} ";

						$sqlforPages = $statement;
						$sql = "SELECT *, a.id FROM ".$statement." ORDER BY a.id DESC LIMIT {$startpoint} , {$per_page}";
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
                <a href="index.php?Application=QA&Link=QAHardness"><button type="button" title="Refresh" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button></a>
				</div>
				<div class="col-xs-4">
				<form enctype="multipart/form-data" method="post">
				<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
				<div class="input-group input-group-sm">
                <input type="text" placeholder="Search Calibre/Country..." id="myInput" onkeyup="myFunction()" name="Searchfield" value="<?php echo isset($_POST['Searchfield']) ? $_POST['Searchfield'] : ""; ?>" class="form-control">
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
				echo pagination_inbox($sqlforPages,$per_page,$page,$url='?Application=QA&Link=RawMatAcc&');
				?>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.pull-right -->
				</div>

              </div>
              <div class="table-responsive mailbox-messages">
			  
                <table id="myTable" style="width:100%;font-size:11px;" class="table table-hover table-striped">
				<thead>
				<tr><th>Status</th>
				<th>Record No</th>
				<th>Calibre</th>
				<th>Country</th>
				<th>Company</th>
				<th>Date</th>
				<th>CreatedBy</th>
				<th></th>
			</tr>
				</thead>
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
					if ($myrow['date']=='') {
						$LastVisitDate = Date($_SESSION['DefaultDateFormat']);
					} else {
						$LastVisitDate = ConvertSQLDate($row['date']);
					}
                  echo '<tr>
				  	<td width="60" class="mailbox-star">'.($row['process_level']==$row['levelcheck']? '<i class="fa fa-star text-success">New</i>':'<i class="fa fa-star text-warning">Sent</i>').'</td>
                    <td class="mailbox-name"><a href="index.php?Application=QA&Link=RawMatAccRead&ID='.$row['id'].'&amp;New=Yes">'.str_pad($row['id'],10,'0',STR_PAD_LEFT).'</a></td>
                    <td class="mailbox-subject">'.$row['calibre'].'</td>
                    <td class="mailbox-attachment">'.$row['country'].'</td>
					<td class="mailbox-date">'.$row['company'].'</td>
					<td class="mailbox-date">'.$LastVisitDate.'</td>
					<td class="mailbox-date">'.$row['technicianid'].'</td>';
					if($row['process_level']==0){
				echo '<td width="60"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&amp;Link=NewRawMat&amp;SelectedUser=' . $row['id'] .'" title="Edit"><i class="fa fa-edit"></i></a> || <a href="' .htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  .'?Application=QA&amp;Link=RawMatAcc&amp;delete=yes&amp;SelectedUser=' . $row['id'].'" onclick="return confirm(\'' . _('Are you sure you wish to delete this Record ?') . '\');" title="Delete" style="color:red;"><i class="fa fa-trash"></i></a></td>';
					}else{
					echo '<td></td>';
					}
                  echo '</tr>';
				  }
				  }else{
				  echo '<tr><td class="alert-danger" colspan="8"><center><b style="color:#FF0000">No Record Found</b></center></td></tr>';
				  }
				  
				  ?>
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>
	 	</fieldset>
		
		
		<?php
		if(isset($_POST['Searchfield1']) && $_POST['Searchfield1'] !=""){
		$search1=" AND (a.id LIKE '%".$_POST['Searchfield1']."%' OR calibre LIKE '%".$_POST['Searchfield1']."%' OR company LIKE '%".$_POST['Searchfield1']."%' OR country LIKE '%".$_POST['Searchfield1']."%') ";
		}else{
		$search1="";
		}
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$statement = " qarawmatacceptance a
										INNER JOIN qa_approval_levels c ON c.type=6
										WHERE process_level >c.levelcheck AND 
										(CASE WHEN c.authoriser='QAT' THEN a.technicianid='".$_SESSION['UserID']."' ELSE c.authoriser='".$_SESSION['UserID']."' END)
										 {$search1} ";

						$sqlforPages = $statement;
						$sql = "SELECT *, a.id, (SELECT MAX(levelcheck) FROM qa_approval_levels WHERE type=6) AS LastLevel FROM ".$statement." GROUP BY a.id ORDER BY a.id DESC  LIMIT {$startpoint} , {$per_page}";
						$welcome_viewed = DB_query($sql,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($welcome_viewed);
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	?>
	</div>
		</div>
	<div class="panel panel-primary">
		<div class="panel-heading">Forwarded Raw Material Acceptance Forms</div>
			<div class="panel-body">
					<fieldset>
						<div class="box-body no-padding">
              <div class="mailbox-controls">
                <!-- Check all button -->
				<div class="row">
                <div class="col-xs-1">
                <!-- /.btn-group -->
                <a href="index.php?Application=QA&Link=QAHardness"><button type="button" title="Refresh" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button></a>
				</div>
				<div class="col-xs-4">
				<form enctype="multipart/form-data" method="post">
				<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
				<div class="input-group input-group-sm">
                <input type="text" placeholder="Search Calibre/Country..." id="myInput1" onkeyup="myFunction1()" name="Searchfield1" value="<?php echo isset($_POST['Searchfield1']) ? $_POST['Searchfield1'] : ""; ?>" class="form-control">
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
				echo pagination_inbox($sqlforPages,$per_page,$page,$url='?Application=QA&Link=RawMatAcc&');
				?>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.pull-right -->
				</div>

              </div>
              <div class="table-responsive mailbox-messages">
			  
                <table id="myTable1" style="width:100%;font-size:11px;" class="table table-hover table-striped">
				<thead>
				<tr><th>Status</th>
				<th>Record No</th>
				<th>Calibre</th>
				<th>Country</th>
				<th>Company</th>
				<th>Date</th>
				<th>CreatedBy</th></tr>
				</thead>
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
					if ($myrow['date']=='') {
						$LastVisitDate = Date($_SESSION['DefaultDateFormat']);
					} else {
						$LastVisitDate = ConvertSQLDate($row['date']);
					}
                  echo '<tr>
				  	<td width="60" class="mailbox-star">'.($row['process_level']>$row['LastLevel']? '<i class="fa fa-star text-success">Approved</i>':'<i class="fa fa-star text-warning">Sent</i>').'</td>
                    <td class="mailbox-name"><a href="index.php?Application=QA&Link=RawMatAccRead&ID='.$row['id'].'&amp;New=No">'.str_pad($row['id'],10,'0',STR_PAD_LEFT).'</a></td>
                    <td class="mailbox-subject">'.$row['calibre'].'</td>
                    <td class="mailbox-attachment">'.$row['country'].'</td>
					<td class="mailbox-date">'.$row['company'].'</td>
					<td class="mailbox-date">'.$LastVisitDate.'</td>
					<td class="mailbox-date">'.$row['technicianid'].'</td>';
                  echo '</tr>';
				  }
				  }else{
				  echo '<tr><td class="alert-danger" colspan="7"><center><b style="color:#FF0000">No Record Found</b></center></td></tr>';
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

function myFunction1() {
  var input, filter, table, tr, td, i;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable1");
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