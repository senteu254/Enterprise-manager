<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HR</title>
	<link rel="shortcut icon" href="../hrlogo.png">
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<!-- JQuery DataTable Css -->
<link href="plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">
<style type="text/css">
.dt-buttons {
    float: none !important;
    margin-bottom: 15px; }
.dataTables_wrapper .dt-buttons {
    float: left; }
    .dataTables_wrapper .dt-buttons a.dt-button {
      background-color:#000000;
      color: #fff;
      padding: 7px 12px;
      margin-right: 5px;
      text-decoration: none;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.16), 0 2px 10px rgba(0, 0, 0, 0.12);
      -webkit-border-radius: 2px;
      -moz-border-radius: 2px;
      -ms-border-radius: 2px;
      border-radius: 2px;
      border: none;
      font-size: 13px;
      outline: none; }
      .dataTables_wrapper .dt-buttons a.dt-button:active {
        opacity: 0.8;color: #fff; }
</style>
	</head>
	<body>
		<div class="container-fluid">
<?php
if(isset($_GET['id']) && is_numeric($_GET['id'])){
if(isset($_GET['Edit']) && $_GET['Edit']=='Yes'){

$sql="select band_id,appointment_name,establishment from appointment where id='".$_GET['id']."'  ";
$qry1 = DB_query($sql);
$rec = DB_fetch_array($qry1);
$_POST['rband_id'] = $rec['band_id'];
$_POST['rappointment_name'] = $rec['appointment_name'];
$_POST['estbedit'] = $rec['establishment'];
$_POST['appointment_categoryedit'] = $rec['category'];

}elseif(isset($_GET['Delete']) && $_GET['Delete']=='Yes'){

$sql="DELETE FROM appointment where id='".$_GET['id']."'  ";
$qry1 = DB_query($sql);

if ($qry1){
echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Appointment Succesfully Deleted
              </div>';
}else{
echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                Appointment Not Deleted
              </div>';
}
}
}
	if (isset($_POST['submit'])){
		//dri mah vl.an kung mai onud ang txtbox then ang mga "category or stu_id is  name sa entity sa database
		if (($_POST['appointment_name'] == ''))
		{
			echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                You must fill those fields
              </div>';
		}else{ //dri namn is ang mga "name=stu_id" nga ara sa mga input type. 
		
		//$id = addslashes($_POST['band_id']);
	   $app = $_POST['appointment_name'];
	   $estb = $_POST['estb'];
	   $cat = $_POST['appointment_category'];
			
		if(isset($_GET['Edit']) && $_GET['Edit']=='Yes' && isset($_GET['id']) && is_numeric($_GET['id'])){
$sql = "UPDATE appointment SET appointment_name='".$app."',establishment='".$estb."',category='".$cat."' WHERE id=".$_GET['id']." ";
$qry = DB_query($sql);
	if ($qry){
	echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Appointment Succesfully Updated
              </div>';
		}else{
			echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
               Appointment Not  Updated
              </div>';
						}
				unset($_GET['Edit']);
				unset($_GET['id']);
			}
				else{			
			
$sql="select * from appointment where band_id='$id' and appointment_name='$app' and category='$cat'  ";
$qry1 = DB_query($sql);
$rec = DB_fetch_array($qry1);
if($rec>0 ){
echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
               Appointment Already Exists
              </div>';
}
else
					{
					$insert = "INSERT INTO appointment
									(`appointment_name`,establishment,category)
										values('$app','$estb','$cat')";
					$qry = DB_query($insert);
					if ($qry){
			echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Appointment Succesfully Added
              </div>';
						}
					else
					echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
               Appointment Not Added
              </div>';
				}
		}
	}
}
?>
<div class="col-md-8 col-md-offset-2">
	<div class="panel panel-default">
		<div class="panel-heading">Add Appointment Name</div>
		<div class="panel-body">
	<form method="POST" class="form-horizontal">
			<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			?>
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="band_id">Appointment Category</label>  
			  <div class="col-md-4">
			  <?php
echo'<select required="required" style="width:400px" name="appointment_category">';	
$sql = "SELECT id,description FROM appointment_category  ";
	$result = DB_query($sql);
	while ($Row = DB_fetch_array($result)) {	
	echo '<option ';
	echo ($Row['id'] == $_POST['appointment_categoryedit']) ? 'selected="true"' : '';
	echo 'value="' . $Row['id'] . '">' . $Row['description'] . '</option>';	
	}
echo '</select>';
			?>
			  
			  </div>
			</div>
			
			<!--<div class="form-group">
			  <label class="col-md-4 control-label" for="band_id">Band Id</label>  
			  <div class="col-md-4">
			  <?php
/*echo'<select required="required" name="band_id">';	
$sql = "SELECT id,band_id FROM band  ";
	$result = DB_query($sql);

	while ($Row = DB_fetch_array($result)) {
		
	echo '<option ';
	echo ($Row['band_id'] == $_POST['rband_id']) ? 'selected="true"' : '';
	echo 'value="' . $Row['band_id'] . '">' . $Row['band_id'] . '</option>';	

}
echo '</select>';*/
			?>
			  
			  </div>
			</div>-->
			
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="appointment_name">Appointment Name</label>  
			  <div class="col-md-4">
			 <input id="appointment_name" name="appointment_name" type="text" value="<?php if(isset($_POST['rappointment_name'])){ echo $_POST['rappointment_name']; } ?>" placeholder="Appointment Name" class="form-control input-md" required=""> 

		  
			  </div>
			</div>
			<div class="form-group">
			  <label class="col-md-4 control-label" for="appointment_name">Establishment</label>  
			  <div class="col-md-4">
			 <input id="appointment_name" name="estb" type="text" value="<?php if(isset($_POST['estbedit'])){ echo $_POST['estbedit']; } ?>" placeholder="Establishment" class="form-control input-md" required=""> 

		  
			  </div>
			</div>			

			<!-- Button -->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="submit"></label>
			  <div class="col-md-8">
				<button id="submit" name="submit" class="btn btn-primary">Submit Appointment</button>
				<a href="index.php?Application=HR&Ref=Dashboard"><input type="button" value="Cancel" class="btn btn-default"></a>
			  </div>
			</div>
	</form>
	</div>
	</div>
	</div>
<div class="body">
<div class="col-md-10 col-md-offset-1">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Registered Appointments</strong></div>
		<div class="panel-body">
	<div class="table-responsive">
	 <table class="table table-bordered table-striped table-hover dataTable js-exportable">
	 <thead>
  <tr>
    <th>S/No.</th>
    <th>Appointment Name</th>
	<th>Category</th>
	<th>Establishment</th>
    <th>Actions</th>
  </tr>
  </thead>
  <tbody>
  <?php
  $no=1;
  $sql="select *,a.id as id from appointment a
  LEFT JOIN appointment_category b ON b.id=a.category
  ORDER BY band_id ASC"; 
	$qry1 = DB_query($sql);
	while($rec = DB_fetch_array($qry1)){
	
  echo '<tr>
    <td>'.$no.'</td>
    <td>'.$rec['appointment_name'].'</td>
	<td>'.$rec['description'].'</td>
	<td>'.$rec['establishment'].'</td>
    <td><a href="index.php?Application=HR&Ref=Appointment&id='.$rec['id'].'&Edit=Yes" title="Edit">Edit</a>';
	?>||<a onClick="return confirm('Are you sure You want to Delete this Appointment?');" href="index.php?Application=HR&Ref=Appointment&id=<?php echo $rec['id']; ?>&Delete=Yes" title="Delete">Delete</a></td>
  </tr>
  <?php
  $no++;
  }
  ?>
  </tbody>
</table>
		</div>
	</div>
</div>
		</div>
		</div>
		
		<!-- Jquery DataTable Plugin Js -->
    <script src="plugins/jquery-datatable/jquery.dataTables.js"></script>
    <script src="plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/buttons.flash.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/jszip.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/pdfmake.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/vfs_fonts.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/buttons.html5.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/buttons.print.min.js"></script>
	<script src="js/jquery-datatable.js"></script>
