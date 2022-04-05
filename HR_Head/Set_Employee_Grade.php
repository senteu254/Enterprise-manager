<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
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

$sql="select band_id,grade from grade where id='".$_GET['id']."'  ";
$qry1 = DB_query($sql);
$rec = DB_fetch_array($qry1);
$_POST['rband_id'] = $rec['band_id'];
$_POST['rgrade'] = $rec['grade'];

}elseif(isset($_GET['Delete']) && $_GET['Delete']=='Yes'){

$sql="DELETE FROM grade where id='".$_GET['id']."'  ";
$qry1 = DB_query($sql);

if ($qry1){
prnMsg( _('Grade Succesfully Deleted'), 'success');

}else{
prnMsg( _('Grade Not Deleted'), 'error');
}
}
}
	if (isset($_POST['submit'])){
		//dri mah vl.an kung mai onud ang txtbox then ang mga "category or stu_id is  name sa entity sa database
		if (($_POST['band_id'] == '')or($_POST['grade'] == ''))
		{
			echo "You must fill those fields";
		}	
	else{ //dri namn is ang mga "name=stu_id" nga ara sa mga input type. 
		
		$id = addslashes("$_POST[band_id]");
		$gr = addslashes("$_POST[grade]");
		
	if(isset($_GET['Edit']) && $_GET['Edit']=='Yes' && isset($_GET['id']) && is_numeric($_GET['id'])){
	$sql="select * from grade where band_id='$id' and grade='$gr'  ";
$qry1 = DB_query($sql);
$rec = DB_fetch_array($qry1);
if($rec>0 ){
prnMsg( _(' Not Updated! Grade Already Exists'), 'warn');
}
else{
	$sql = "UPDATE grade SET band_id='".$id."', grade='".$gr."' WHERE id=".$_GET['id']."   ";
$qry = DB_query($sql);
	if ($qry ){
	prnMsg( _('Grade Succesfully Updated'), 'success');
						}else{
						prnMsg( _('Grade Not Updated'), 'error');
						}
				unset($_GET['Edit']);
				unset($_GET['id']);
				}
			}
			else{		
		
$sql="select * from grade where band_id='$id' and grade='$gr'  ";
$qry1 = DB_query($sql);
$rec = DB_fetch_array($qry1);
if($rec>0 ){
prnMsg( _('Grade Already Exists'), 'warn');
}
else{
					$insert = "INSERT INTO grade
									(`id`,`band_id`,`grade`)
										values('','$id','$gr')";
					$qry = DB_query($insert);
					if ($qry){
					prnMsg( _('Grade Succesfully Added'), 'success');
						}
					else
					prnMsg( _('Grade Not Added'), 'error');
				}
		}
	}
}
?>
<div class="col-md-6 col-md-offset-3">
	<div class="panel panel-default">
		<div class="panel-heading">Add Grade</div>
		<div class="panel-body">

	<form method="POST" class="form-horizontal">
			<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			?>
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="band_id">Band Id</label>  
			  <div class="col-md-4">
			 <!--<input id="band_id" name="band_id" type="text" placeholder="Band category" class="form-control input-md" required="">  -->
<?php
echo'<select required="required" name="band_id">';	
$sql = "SELECT id,band_id FROM band  ";
	$result = DB_query($sql);

	while ($Row = DB_fetch_array($result)) {
		
	echo '<option ';
	echo ($Row['band_id'] == $_POST['rband_id']) ? 'selected="true"' : '';
	echo 'value="' . $Row['band_id'] . '">' . $Row['band_id'] . '</option>';		

}
echo '</select>';
			?>
			  
			  </div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="grade">Grade</label>  
			  <div class="col-md-4">
			 <input id="grade" name="grade" type="text" value="<?php if(isset($_POST['rgrade'])){ echo $_POST['rgrade']; } ?>" placeholder="Grade" class="form-control input-md" required=""> 
				
			  </div>
			</div>
						

			<!-- Button -->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="submit"></label>
			  <div class="col-md-8">
				<button id="submit" name="submit" class="btn btn-primary">Submit Grade</button>
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
		<div class="panel-heading"><strong>Registered Grade</strong></div>
		<div class="panel-body">
	<div class="table-responsive">
	 <table class="table table-bordered table-striped table-hover dataTable js-exportable">
	 <thead>
  <tr height="30">
    <th width="50" scope="col">S/No.</th>
	<th width="80" scope="col">Band</th>
    <th width="300" scope="col">Grade</th>
    <th>Actions</th>
	<th></th>
  </tr>
  </thead>
   <tbody>
  <?php
  $no=1;
  $sql="select * from grade ORDER BY band_id ASC";
	$qry1 = DB_query($sql);
	while($rec = DB_fetch_array($qry1)){
	
  echo '<tr>
    <td>'.$no.'</td>
	 <td>'.$rec['band_id'].'</td>
    <td>'.$rec['grade'].'</td>
    <td><a href="index.php?Application=HR&Ref=Grade&id='.$rec['id'].'&Edit=Yes" title="Edit">Edit</a></td>';
	?>
    <td><a onClick="return confirm('Are you sure You want to Delete this Grade?');" href="index.php?Application=HR&Ref=Grade&id=<?php echo $rec['id']; ?>&Delete=Yes" title="Delete">Delete</a></td>
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