<script type="text/javascript" src="js/qsearch.js"></script>
	<!-- First, include the Webcam.js JavaScript Library -->
<script type="text/javascript" src="js/webcam.js"></script>
<?php	
if (isset($_GET['AssetID'])){
	$AssetID =$_GET['AssetID'];
} elseif (isset($_POST['AssetID'])){
	$AssetID =$_POST['AssetID'];
} elseif (isset($_POST['Select'])){
	$AssetID =$_POST['Select'];
} else {
	$AssetID = '';
}	
if (isset($_GET['delete']) AND $_GET['delete']==1 ) {
//the button to delete a selected record was clicked instead of the submit button

	$CancelDelete = 0;
	//what validation is required before allowing deletion of assets ....  maybe there should be no deletion option?
	$result = DB_query("SELECT cost,
								accumdepn,
								accumdepnact,
								costact
						FROM fixedassets INNER JOIN fixedassetcategories
						ON fixedassets.assetcategoryid=fixedassetcategories.categoryid
						WHERE assetid='" . $AssetID . "'");
	$AssetRow = DB_fetch_array($result);
	$NBV = $AssetRow['cost'] -$AssetRow['accumdepn'];
	if ($NBV!=0) {
		$CancelDelete =1; //cannot delete assets where NBV is not 0
		prnMsg(_('The asset still has a net book value - only assets with a zero net book value can be deleted'),'error');
		echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>The asset still has a net book value - only assets with a zero net book value can be deleted.</div>';
	}
	$result = DB_query("SELECT * FROM fixedassettrans WHERE assetid='" . $AssetID . "'");
	if (DB_num_rows($result) > 0){
		$CancelDelete =1; /*cannot delete assets with transactions */
		echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>The asset has transactions associated with it. The asset can only be deleted when the fixed asset transactions are purged, otherwise the integrity of fixed asset reports may be compromised.</div>';
	}
	$result = DB_query("SELECT * FROM purchorderdetails WHERE assetid='" . $AssetID . "'");
	if (DB_num_rows($result) > 0){
		$CancelDelete =1; /*cannot delete assets where there is a purchase order set up for it */
		echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>There is a purchase order set up for this asset. The purchase order line must be deleted first.</div>';
	}
	if ($CancelDelete==0) {
		$result = DB_Txn_Begin();

		/*Need to remove cost and accumulate depreciation from cost and accumdepn accounts */
		$PeriodNo = GetPeriod(Date($_SESSION['DefaultDateFormat']),$db);
		$TransNo = GetNextTransNo( 43, $db); /* transaction type is asset deletion - (and remove cost/acc5umdepn from GL) */
		if ($AssetRow['cost'] > 0){
			//credit cost for the asset deleted
			$SQL = "INSERT INTO gltrans (type,
										typeno,
										trandate,
										periodno,
										account,
										narrative,
										amount)
						VALUES ('43',
							'" . $TransNo . "',
							'" . Date('Y-m-d') . "',
							'" . $PeriodNo . "',
							'" . $AssetRow['costact'] . "',
							'" . _('Delete asset') . ' ' . $AssetID . "',
							'" . -$AssetRow['cost']. "'
							)";
			$ErrMsg = _('Cannot insert a GL entry for the deletion of the asset because');
			$DbgMsg = _('The SQL that failed to insert the cost GL Trans record was');
			$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

			//debit accumdepn for the depreciation removed on deletion of this asset
			$SQL = "INSERT INTO gltrans (type,
										typeno,
										trandate,
										periodno,
										account,
										narrative,
										amount)
						VALUES ('43',
							'" . $TransNo . "',
							'" . Date('Y-m-d') . "',
							'" . $PeriodNo . "',
							'" . $AssetRow['accumdepnact'] . "',
							'" . _('Delete asset') . ' ' . $AssetID . "',
							'" . $Asset['accumdepn']. "'
							)";
			$ErrMsg = _('Cannot insert a GL entry for the reversal of accumulated depreciation on deletion of the asset because');
			$DbgMsg = _('The SQL that failed to insert the cost GL Trans record was');
			$result = DB_query($SQL,$ErrMsg,$DbgMsg,true);

		} //end if cost > 0

		$sql="DELETE FROM fixedassets WHERE assetid='" . $AssetID . "'";
		$result=DB_query($sql, _('Could not delete the asset record'),'',true);

		$result = DB_Txn_Commit();

		echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success!</h4>'._('Deleted the asset  record for asset number' ) . ' ' . $AssetID.'</div>';
		unset($_POST['LongDescription']);
		unset($_POST['Description']);
		unset($_POST['AssetCategoryID']);
		unset($_POST['AssetLocation']);
		unset($_POST['DepnType']);
		unset($_POST['DepnRate']);
		unset($_POST['SerialNo']);
		unset($AssetID);
		unset($_SESSION['SelectedAsset']);

	} //end if OK Delete Asset
	}
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
				<th>Asset Category</th><th width="30%">Description</th><th>Location</th><th>Purchased Date</th><th>Action</th>
				</thead>
                  <tbody>
				  <?php
				  $sql="SELECT * FROM fixedassets,fixedassetlocations,fixedassetcategories WHERE fixedassetlocations.locationid=fixedassets.assetlocation AND fixedassetcategories.categoryid=fixedassets.assetcategoryid";
				  $result=DB_query($sql);
				  $num_rows = DB_num_rows($result);
				  if($num_rows>0){
			  		while($row = DB_fetch_array($result)){
					
                  echo '<tr>
                    <td class="mailbox-name">'.strtoupper($row['categorydescription']).'</td>
                    <td class="mailbox-subject">'.$row['longdescription'].'</td>
                    <td class="mailbox-attachment">'.$row['locationdescription'].'</td>
                    <td class="mailbox-date">'.ConvertSQLDate($row['datepurchased']).'</td>
					<td class="mailbox-date pull-right"><a href="index.php?Application=FA&Link=NewAsset&AssetID='.$row['assetid'].'"><i class="fa fa-edit"></i> Edit</a> || <a style="color:red" onclick="return confirm(\'Only click the Delete button if you are sure you wish to delete the asset. Only assets with a zero book value can be deleted\');" href="index.php?Application=FA&Link=SearchAsset&AssetID='.$row['assetid'].'&delete=1"><i class="fa fa-trash"></i> Delete</a></td>
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
