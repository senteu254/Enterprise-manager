    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<div class="container-fluid">
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
<?php
if(isset($_GET['id']) && is_numeric($_GET['id'])){
if(isset($_GET['Edit']) && $_GET['Edit']=='Yes'){

$sql="select description from ethnicity where id='".$_GET['id']."'  ";
$qry1 = DB_query($sql);
$rec = DB_fetch_array($qry1);
$_POST['rband_id'] = $rec['description'];

}elseif(isset($_GET['Delete']) && $_GET['Delete']=='Yes'){
$sql="DELETE FROM ethnicity where id='".$_GET['id']."'  ";
echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.confirm('Are you sure you wish to delete this Ethnic Community?)
    </SCRIPT>");
$qry1 = DB_query($sql);
if ($qry1){
echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Ethnic Community Succesfully Deleted
              </div>';
}else{
echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
               Ethnic Community Information  Not Deleted
              </div>';
}
}
}
	if (isset($_POST['submit'])){
		//dri mah vl.an kung mai onud ang txtbox then ang mga "category or stu_id is  name sa entity sa database
		if (($_POST['band_id'] == ''))
		{
			echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
               You must fill that field
              </div>';
		}	
	else{ //dri namn is ang mga "name=stu_id" nga ara sa mga input type. 
		
		$desc = addslashes("$_POST[band_id]");
	
	if(isset($_GET['Edit']) && $_GET['Edit']=='Yes' && isset($_GET['id']) && is_numeric($_GET['id'])){
	$sql = "UPDATE ethnicity SET description='".$desc."' WHERE id=".$_GET['id']." ";
$qry = DB_query($sql);
	if ($qry  ){
	echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Ethnic Community Succesfully Updated
              </div>';
	}
	else{
	echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
               Ethnic Community Not Updated
              </div>';
		}
				unset($_GET['Edit']);
				unset($_GET['id']);
				}
else{

$sql="select * from ethnicity where description='$desc'  ";
$qry1 = DB_query($sql);
$rec = DB_fetch_array($qry1);
if($rec>0 ){
echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
               Ethnic Community Already Exists
              </div>';
}
                else
					{
					$insert = "INSERT INTO ethnicity
									(`description`)
										VALUES('$desc')";
					$qry = DB_query($insert);
					if ($qry){
					echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
              Ethnic Community Succesfully Added
              </div>';
						}
					else
					echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
               Ethnic Community Succesfully Not Added
              </div>';
				}
		}
	}
}
?>
<div class="col-md-6 col-md-offset-3">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Add Ethnic Community</strong></div>
		<div class="panel-body">
	<form method="POST" class="form-horizontal">
			
			<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			?>
			
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="band_id">Ethnic Name:</label>  
			  <div class="col-md-6">
			  <input id="band_id" name="band_id" type="text" value="<?php if(isset($_POST['rband_id'])){ echo $_POST['rband_id']; } ?>" placeholder="Ethnic Name" class="form-control input-md" required=""> 
			  			  </div>
			</div>
						

			<!-- Button -->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="submit"></label>
			  <div class="col-md-8">
				<button id="submit" name="submit" class="btn btn-primary">Submit</button>
				<a href="index.php?Application=HR&Ref=Dashboard"><input type="button" value="Cancel" class="btn btn-default"></a>
			  </div>
			</div>
	</form>
	</div>	
	</div>			
</div>
<?php
if(isset($_GET['Employees']) && $_GET['Employees']=="Yes"){
 $sql="select description,id from ethnicity where id=".$_GET['ethnic'];
	$query = DB_query($sql);
	$rc = DB_fetch_array($query);
?>
<div class="body">
<div class="col-md-10 col-md-offset-1">
<a href="index.php?Application=HR&Ref=Band"><input name="" value="Go Back" type="button" class="btn btn-warning" /></a>
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Employees belonging to <?php echo $rc['description']; ?> Ethnic Community</strong></div>
		<div class="panel-body">
	<div class="table-responsive">
	 <table class="table table-bordered table-striped table-hover dataTable js-exportable">
	
	<thead>
  <tr>
    <th>S/No.</th>
    <th>SrvNo</th>
	<th>Employee Name</th>
	<th>Appointment Name</th>
	<th>Grade</th>
	<th>Personnel</th>
	<th>Ethnicity</th>
  </tr>
  </thead>
  <tfoot>
  <tr>
    <th>S/No.</th>
    <th>Srv No</th>
	<th>Employee Name</th>
	<th>Appointment Name</th>
	<th>Grade</th>
	<th>Personnel</th>
	<th>Ethnicity</th>
  </tr>
  </tfoot>
  <tbody>
  <?php
	$no=1;
  $sql="select emp_id,emp_fname,emp_lname,emp_mname,appointment_name,grade,personnel,description from employee,ethnicity where employee.ethnicity=ethnicity.id AND ethnicity='".$_GET['ethnic']."' AND stat=1";
	$query = DB_query($sql);
	while($rec = DB_fetch_row($query)){
  echo '<tr>
    <td>'.$no.'</td>
    <td><a href="index.php?Application=HR&Ref=Profile&id='.$rec[0].'">'.$rec[0].'</a></td>
	<td>'.$rec[1].' '.$rec[3].' '.$rec[2].'</td>
	<td>'.$rec[4].'</td>
	<td>'.$rec[5].'</td>
	<td>'.$rec[6].'</td>
	<td>'.$rec[7].'</td>';
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
<?php
}else{
?>	
<div class="body">
<div class="col-md-10 col-md-offset-1" >
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Registered Ethnic Communities</strong></div>
		<div class="panel-body">
	<div class="table-responsive">
	 <table class="table table-bordered table-striped table-hover dataTable js-exportable" width="100%">
	
	<thead>
  <tr>
    <th>S/No.</th>
    <th width="250">Ethnic Name</th>
	<th></th>
	<th></th>
  </tr>
  </thead>
  <tfoot>
  <tr>
    <th>S/No.</th>
    <th>Ethnic Name</th>
	 <th></th>
	 <th></th>
  </tr>
  </tfoot>
  <tbody>
  <?php
	$no=1;
  $sql="select description,id from ethnicity";
	$query = DB_query($sql);
	while($rec = DB_fetch_array($query)){
  echo '<tr>
    <td>'.$no.'</td>
    <td>'.$rec['description'].'</td>';
  echo '<td><a href="index.php?Application=HR&Ref=Ethnicity&id='.$rec['id'].'&Edit=Yes" title="Edit">Edit</a> || ';
   echo ' <a onclick="return confirm(\'Are you sure You want to Delete this Ethnici Community?\');" href="index.php?Application=HR&Ref=Ethnicity&id='.$rec['id'].'&Delete=Yes" title="Delete">Delete</a></td>';
  echo '<td><a href="index.php?Application=HR&Ref=Ethnicity&ethnic='.$rec['id'].'&Employees=Yes" title="Employees in this Band">Employees in this Ethnic Community</a></td>';
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
<?php
}
?>
<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
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
