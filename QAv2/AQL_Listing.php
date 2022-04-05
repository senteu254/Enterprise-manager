
<?php

if (isset($_GET['SelectedSampleID'])){
	$SelectedSampleID =mb_strtoupper($_GET['SelectedSampleID']);
} elseif(isset($_POST['SelectedSampleID'])){
	$SelectedSampleID =mb_strtoupper($_POST['SelectedSampleID']);
}
if (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

// PREVENT DELETES IF DEPENDENT RECORDS

	$sql= "SELECT COUNT(*) FROM sampleresults WHERE sampleresults.sampleid='".$SelectedSampleID."'
											AND sampleresults.testvalue > ''";
	$result = DB_query($sql);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		$_SESSION['errmsg'] =_('Cannot delete this Sample ID because there are test results tied to it');
	} else {
		$sql="DELETE FROM sampleresults WHERE sampleid='" . $SelectedSampleID . "'";
		$ErrMsg = _('The sample results could not be deleted because');
		$result = DB_query($sql,$ErrMsg);
		$sql="DELETE FROM qasamples WHERE sampleid='" . $SelectedSampleID ."'";
		$ErrMsg = _('The QA Sample could not be deleted because');
		$result = DB_query($sql,$ErrMsg);
		//echo $sql;
		$_SESSION['msg'] = _('QA Sample') . ' ' . $SelectedSampleID . _(' has been deleted from the database');
		unset ($SelectedSampleID);
		unset($delete);
		unset ($_GET['delete']);
		unset ($_GET['SelectedSampleID']);
	}
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
$search=" AND (a.sampleid LIKE '%".$_POST['Searchfield']."%' OR lotkey LIKE '%".$_POST['Searchfield']."%' OR description LIKE '%".$_POST['Searchfield']."%' OR prodspeckey LIKE '%".$_POST['Searchfield']."%') ";
}else{
$search="";
}
//(SELECT MAX(approver_level) FROM qrsampleremarks WHERE qrsampleremarks.sampleid=qasamples.sampleid) AS approver_level,


						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$statement = " qasamples a
									LEFT OUTER JOIN stockmaster on stockmaster.stockid=a.prodspeckey
									
									INNER JOIN qa_approval_levels c ON c.type=1
									WHERE a.process_level=c.levelcheck AND 
									(CASE WHEN c.authoriser='QAT' THEN (a.createdby='".$_SESSION['UserID']."' OR '".$_SESSION['UserID']."' IN(SELECT serviceno FROM qat)) ELSE c.authoriser='".$_SESSION['UserID']."' END)
									 {$search}"; //LEFT OUTER JOIN qasampletechnicians x on x.sampleidno=a.sampleid    OR x.technician='".$_SESSION['UserID']."'

						$sqlforPages = $statement;
						$sql = 'SELECT *, a.sampleid FROM '.$statement." ORDER BY process_level ASC, sampledate DESC LIMIT {$startpoint} , {$per_page}";
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
                <a href="index.php?Application=QA&Link=AQL"><button type="button" title="Refresh" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button></a>
				</div>
				<div class="col-xs-4">
				<form enctype="multipart/form-data" method="post">
				<?php  echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
				<div class="input-group input-group-sm">
                <input type="text" placeholder="Search Specification/Lot/Serial..." id="myInput" onkeyup="myFunction()" name="Searchfield" value="<?php echo isset($_POST['Searchfield']) ? $_POST['Searchfield'] : ""; ?>" class="form-control">
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
				echo pagination_inbox($sqlforPages,$per_page,$page,$url='?Application=QA&Link=AQL&');
				?>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.pull-right -->
				</div>

              </div>
              <div class="table-responsive mailbox-messages">
			  
                <table id="myTable" style="width:100%; font-size:11px;" class="table table-hover table-striped">
				<thead>
				<th>Status</th><th>Sample No</th><th>Specification</th><th>Description</th><th>Lot / Serial</th><th>SampleDate</th><th>CreatedBy</th>
				<?php if($row['process_level']==0){ ?>
				<th width="50">Action</th>
				<?php }else{ ?>
				<th width="50">Cert</th>
				<?php } ?>
				</thead>
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
					$CertAllowed='<a target="_blank" href="'. $RootPath . '/PDFCOA.php?LotKey=' .$row['lotkey'] .'&ProdSpec=' .$row['prodspeckey'] .'&QASampleID=' .$row['sampleid']. '">' . _('Print') . '</a>';
                  echo '<tr>
				  	<td width="60" class="mailbox-star">'.($row['rejected']==0? '<i class="fa fa-star text-success">New</i>':'<i class="fa fa-star text-danger">Rejected</i>').'</td>';
					if($row['process_level']==0){
                   echo '<td class="mailbox-name"><a href="index.php?Application=QA&Link=AQLReadTest&SelectedSampleID='.$row['sampleid'].'">'.str_pad($row['sampleid'],10,'0',STR_PAD_LEFT).'</a></td>';
				   }else{
				   echo '<td class="mailbox-name"><a href="index.php?Application=QA&Link=AQLRead&ID='.$row['sampleid'].'&amp;New=Yes">'.str_pad($row['sampleid'],10,'0',STR_PAD_LEFT).'</a></td>';
				   }
                    echo '<td class="mailbox-subject">'.$row['prodspeckey'].'</td>
                    <td class="mailbox-attachment">'.$row['description'].'</td>
                    <td class="mailbox-date">'.$row['lotkey'].'</td>
					<td class="mailbox-date">'.ConvertSQLDate($row['sampledate']).'</td>
					<td class="mailbox-date">'.$row['createdby'].'</td>';
					if($row['process_level']==0){
				echo '<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&amp;Link=NewAQL&amp;SelectedSampleID=' . $row['sampleid'] .'" title="Edit"><i class="fa fa-edit"></i></a> || <a href="' .htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  .'?Application=QA&amp;Link=AQL&amp;delete=yes&amp;SelectedSampleID=' . $row['sampleid'].'" onclick="return confirm(\'' . _('Are you sure you wish to delete this Sample ID ?') . '\');" title="Delete" style="color:red;"><i class="fa fa-trash"></i></a></td>';
					}else{
				echo '<td>'.$CertAllowed.'</td>';
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
		$search1=" AND (a.sampleid LIKE '%".$_POST['Searchfield1']."%' OR lotkey LIKE '%".$_POST['Searchfield1']."%' OR description LIKE '%".$_POST['Searchfield1']."%' OR prodspeckey LIKE '%".$_POST['Searchfield1']."%') ";
		}else{
		$search1="";
		}
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$statement = " qasamples a
									LEFT OUTER JOIN stockmaster on stockmaster.stockid=a.prodspeckey
									INNER JOIN qasampletechnicians x on x.sampleidno=a.sampleid
									INNER JOIN qa_approval_levels c ON c.type=1
									WHERE a.process_level>c.levelcheck AND 
									(CASE WHEN c.authoriser='QAT' THEN (a.createdby='".$_SESSION['UserID']."' OR x.technician='".$_SESSION['UserID']."') ELSE c.authoriser='".$_SESSION['UserID']."' END)
									 {$search1}";

						$sqlforPages = $statement;
						$sql = 'SELECT *, a.sampleid,(SELECT MAX(levelcheck) FROM qa_approval_levels WHERE type=1) AS LastLevel FROM '.$statement." GROUP BY a.sampleid ORDER BY process_level ASC, sampledate DESC LIMIT {$startpoint} , {$per_page}";
						$welcome_viewed = DB_query($sql,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($welcome_viewed);
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	?>
	</div>
		</div>
	<div class="panel panel-primary">
		<div class="panel-heading">Forwarded AQL Sample Results/Remarks</div>
			<div class="panel-body">
					<fieldset>
						<div class="box-body no-padding">
              <div class="mailbox-controls">
                <!-- Check all button -->
				<div class="row">
                <div class="col-xs-1">
                <!-- /.btn-group -->
                <a href="index.php?Application=QA&Link=AQL"><button type="button" title="Refresh" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button></a>
				</div>
				<div class="col-xs-4">
				<form enctype="multipart/form-data" method="post">
				<?php  echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
				<div class="input-group input-group-sm">
                <input type="text" placeholder="Search Specification/Lot/Serial..." id="myInput1" onkeyup="myFunction1()" name="Searchfield1" value="<?php echo isset($_POST['Searchfield1']) ? $_POST['Searchfield1'] : ""; ?>" class="form-control">
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
				echo pagination_inbox($sqlforPages,$per_page,$page,$url='?Application=QA&Link=AQL&');
				?>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.pull-right -->
				</div>

              </div>
              <div class="table-responsive mailbox-messages">
			  
                <table id="myTable1" style="width:100%; font-size:11px;" class="table table-hover table-striped">
				<thead>
				<th>Status</th><th>Sample No</th><th>Specification</th><th>Description</th><th>Lot / Serial</th><th>SampleDate</th><th>CreatedBy</th><th>Cert</th>
				</thead>
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
					$CertAllowed='<a target="_blank" href="'. $RootPath . '/PDFCOA.php?LotKey=' .$row['lotkey'] .'&ProdSpec=' .$row['prodspeckey'] .'&QASampleID=' .$row['sampleid']. '">' . _('Print') . '</a>';
				
                  echo '<tr>
				  	<td width="60" class="mailbox-star">'.($row['process_level']>$row['LastLevel']? '<i class="fa fa-star text-success">Approved</i>':'<i class="fa fa-star text-warning">Sent</i>').'</td>
                    <td class="mailbox-name"><a href="index.php?Application=QA&Link=AQLRead&ID='.$row['sampleid'].'&amp;New=No">'.str_pad($row['sampleid'],10,'0',STR_PAD_LEFT).'</a></td>
                    <td class="mailbox-subject">'.$row['prodspeckey'].'</td>
                    <td class="mailbox-attachment">'.$row['description'].'</td>
                    <td class="mailbox-date">'.$row['lotkey'].'</td>
					<td class="mailbox-date">'.ConvertSQLDate($row['sampledate']).'</td>
					<td class="mailbox-date">'.$row['createdby'].'</td>
					<td width="50">'.$CertAllowed.'</td>
                  </tr>';
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

function myFunction1() {
  var input, filter, table, tr, td, i;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable1");
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