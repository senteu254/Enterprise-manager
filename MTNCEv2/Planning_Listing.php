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
<form name="form" enctype="multipart/form-data" method="post" class="form-horizontal">
<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
					<fieldset>
					 <div class="box-body no-padding">
              <div class="mailbox-controls">
                <!-- Check all button -->
				<?php
				if(!isset($_POST['Yearend'])){
				$_POST['Yearend'] = Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],0));
				}
				echo'<center>Financial Year: ';
			echo'<select onchange="document.form.submit();" name="Yearend">
			<option '.((Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],-1))==$_POST['Yearend'])? 'selected':'').' value="' .  Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],-1)) . '">'.Date('Y',YearEndDate($_SESSION['YearEnd'],-2)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd'],-1)).'</option>';	
					   
			echo'<option '.((Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],0))==$_POST['Yearend'])? 'selected':'').' value="'.  Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],0)) . '">'.Date('Y',YearEndDate($_SESSION['YearEnd'],-1)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd'],0)).'</option>';
			
			echo'<option '.((Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],+1))==$_POST['Yearend'])? 'selected':'').' value="'.  Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],+1)) . '">'.Date('Y',YearEndDate($_SESSION['YearEnd'],0)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd'],+1)).'</option>';
	echo '</select></center>';
	
	$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$maintenance = " fixedassets z 
							INNER JOIN fixedassetlocations a on a.locationid = z.assetlocation 
							INNER JOIN fixedassetplanning e ON z.assetid = e.assetid
							WHERE fyend='".FormatDateForSQL($_POST['Yearend'])."' AND (SELECT caneditMP FROM www_users WHERE userid='".$_SESSION['UserID']."')=1";
						$sql = "SELECT * FROM {$maintenance}";
						$welcome_viewed = DB_query($sql,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($welcome_viewed);

			
			$months = array(1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
				?>
              <div class="table-responsive mailbox-messages">
			  
                <table id="myTable" style="width:100%; font-size:10px;" class="table table-hover table-striped">
				<thead>
				<th style="width:560px;">Asset Name</th><th>Location</th><th>Serviceability</th>
				<?php foreach($months as $key=>$val){ echo '<th>'.$val.'</th>'; } ?>
				</thead>
                  <tbody>
				  <?php
				  if($num_rows>0){
			  		while($row = DB_fetch_array($welcome_viewed)){
					$sqlz="SELECT a.month FROM fixedassetplantask a
							INNER JOIN fixedassettasks b ON a.planid=b.requestid
							WHERE a.assetid=".$row['assetid']." AND b.completed=2";
			$rest = DB_query($sqlz,$ErrMsg,$DbgMsg);
					$array =array();
					while($rowm = DB_fetch_array($rest)){
					$array[] = $rowm['month'];
					}
                  echo '<tr>
                    <td class="mailbox-name"><a rel="facebox" href="Add_MaintenancePlan.php?AID='.$row['assetid'].'&PID='.$row['planid'].'">'.strtoupper($row['longdescription']).'</a></td>
                    <td class="mailbox-subject">'.$row['locationdescription'].'</td>
                    <td class="mailbox-attachment"><center>'.$row['servicestatus'].'</center></td>';
					$mts = explode(',',$row['months']);
					foreach($months as $key=>$val){ 
					echo '<td class="mailbox-date"><center>'.(in_array($key, $mts)? (in_array($key, $array)? '<i style="color:green" class="fa fa-check"></i>':'<i style="color:red" class="fa fa-times"></i>') :'').'</center></td>';
					}
                    
                  echo '</tr>';
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