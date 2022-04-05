<script type="text/javascript" src="js/qsearch.js"></script>
	<!-- First, include the Webcam.js JavaScript Library -->
<script type="text/javascript" src="js/webcam.js"></script>
<?php		

if (isset($_POST['SubmitPlan'])) {
	$Cancel = 0;
if(isset($_POST['state']) && $_POST['state']==""){
$Cancel = 1;
echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>Please Select the Serviceability Status.</div>';
}elseif(!isset($_POST['month']) or count($_POST['month'])==0){
$Cancel = 1;
echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>Please Select the months in which the machine will be maintained.</div>';
}
$sql="SELECT COUNT(planid) as num FROM fixedassetplanning a WHERE a.assetid='".$_POST['assetid']."' AND a.fyend='".Date('Y-m-d',YearEndDate($_SESSION['YearEnd'],0))."'";
$result = DB_query($sql);
$row = DB_fetch_row($result);
if($row[0]>0 && (!isset($_POST['PlanID']) or $_POST['PlanID']=="")){
$Cancel = 1;
echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>Sorry the selected machine has already been planned for maintainance for the selected financial year.</div>';
}
	if ($Cancel == 0) {
	if(isset($_POST['PlanID']) && $_POST['PlanID']!=""){
	$sql ="UPDATE fixedassetplanning SET fyend='" . FormatDateForSQL($_POST['Yearend']) . "',servicestatus='" . $_POST['state'] . "',months='" . implode(',',$_POST['month']) . "' WHERE planid=".$_POST['PlanID']."";
	$result = DB_query($sql);
	echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Success!</h4>Planning for the selected Machine has been Updated successfully.</div>';
	}else{
	$sql = "INSERT INTO fixedassetplanning (`assetid`, `fyend`, `servicestatus`, `months`, `planningofficer`)
								 VALUES ('" . $_POST['assetid'] . "',
									'" . FormatDateForSQL($_POST['Yearend']) . "',
									'" . $_POST['state'] . "',
									'" . implode(',',$_POST['month']) . "',
									'" . $_SESSION['UserID'].'-'. $_SESSION['UsersRealName'] . "')";
		$result = DB_query($sql);
	echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Success!</h4>Planning for the selected Machine has been Saved successfully.</div>';

	} 
	}
}

?>

<form enctype="multipart/form-data" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
              <!-- /.mailbox-controls -->
		<div class="table-responsive mailbox-messages">
			  
                <table id="myTable" style="width:100%; font-size:12px;" class="table table-hover table-striped">
				<thead>
				<th>Asset Category</th><th width="30%">Description</th><th>Location</th><th>Purchased Date</th><th>Status</th><th>Action</th>
				</thead>
                  <tbody>
				  <?php
				  $sql="SELECT *, (SELECT COUNT(planid) FROM fixedassetplanning a WHERE a.assetid=fixedassets.assetid AND fyend='".Date('Y-m-d',YearEndDate($_SESSION['YearEnd'],0))."') AS status FROM fixedassets,fixedassetlocations,fixedassetcategories WHERE fixedassetlocations.locationid=fixedassets.assetlocation AND fixedassetcategories.categoryid=fixedassets.assetcategoryid";
				  $result=DB_query($sql);
				  $num_rows = DB_num_rows($result);
				  if($num_rows>0){
			  		while($row = DB_fetch_array($result)){
					
                  echo '<tr>
                    <td class="mailbox-name">'.strtoupper($row['categorydescription']).'</td>
                    <td class="mailbox-subject">'.$row['longdescription'].'</td>
                    <td class="mailbox-attachment">'.$row['locationdescription'].'</td>
                    <td class="mailbox-date">'.$row['datepurchased'].'</td>
					 <td width="35" class="mailbox-star">'.($row['status']==1 ? '<a style="color:red;" href="#"><i class="fa fa-star">Planned</i> </a>':'<a style="color:green;" href="#"><i class="fa fa-star-o">Active</i> </a>').'</td>
					<td class="mailbox-date"><a rel="facebox" href="Add_MaintenancePlan.php?AID='.$row['assetid'].'">Start Planning</a></td>
                  </tr>';
				  }
				  }
				  
				  ?>
                  </tbody>
                </table>
                <!-- /.table -->
              </div>

</form>
            <!-- /.box-footer -->
          </div>
