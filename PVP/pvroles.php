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
	</head>
	<body>
		<div class="container-fluid">
<?php
if(isset($_GET['id']) && is_numeric($_GET['id'])){
if(isset($_GET['Edit']) && $_GET['Edit']=='Yes'){

$sql="select * from pvroles where id='".$_GET['id']."'  ";
$qry1 = DB_query($sql);
$rec = DB_fetch_array($qry1);
$_POST['rlevel'] = $rec['level'];
$_POST['rpvrole'] = $rec['pvrole'];

}elseif(isset($_GET['Delete']) && $_GET['Delete']=='Yes'){

$sql="DELETE FROM pvroles where id='".$_GET['id']."'  ";
$qry1 = DB_query($sql);

if ($qry1){
prnMsg( _('Pvrole Succesfully Deleted'), 'success');
}

}else{
prnMsg( _('Pvrole Not Added'), 'error');
}
}
	if (isset($_POST['submit'])){
		if (($_POST['level'] == '')or($_POST['pvrole'] == ''))
		{
			echo "You must fill those fields";
		}	
	else{ 
		
		$id = $_POST['level'];
	   $app = $_POST['pvrole'];
			
		if(isset($_GET['Edit']) && $_GET['Edit']=='Yes' && isset($_GET['id']) && is_numeric($_GET['id'])){
	$sql="select * from pvroles where level='$id' and pvrole='$app'  ";
$qry1 = DB_query($sql);
$rec = DB_fetch_array($qry1);
if($rec>0 ){
prnMsg( _(' Not Updated! Role Already Exists'), 'warn');
}
else{
	$sql = "UPDATE pvroles SET level='".$id."', pvrole='".$app."' WHERE id=".$_GET['id']." ";
$qry = DB_query($sql);
	if ($qry){
	prnMsg( _('Role Succesfully Updated'), 'success');
						}
		else{
			prnMsg( _('Role Not  Updated'), 'error');
						}
				unset($_GET['Edit']);
				unset($_GET['id']);
				}
			}
				else{			
			
$sql="select * from pvroles where level='$id' and pvrole='$app'  ";
$qry1 = DB_query($sql);
$rec = DB_fetch_array($qry1);
if($rec>0 ){
prnMsg( _('Role Already Exists'), 'warn');
}
else
					{
					$insert = "INSERT INTO pvroles
									(`id`,`level`,`pvrole`)
										values('','$id','$app')";
					$qry = DB_query($insert);
					if ($qry){
						prnMsg( _('Role Succesfully Added'), 'success');
						}
					else
					prnMsg( _('Role Not Added'), 'error');
				}
		}
	}
}
?>
<div class="col-md-8 col-md-offset-2">
	<div class="panel panel-default">
		<div class="panel-heading"><center>Add Payment Voucher Roles</center></div>
		<div class="panel-body">
	<form method="POST" class="form-horizontal">
			<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			?>
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="band_id">Payment Voucher level</label>  
			  <div class="col-md-4">
			 <?php
echo'<select required="required" name="level">';	
$sql = "SELECT * FROM pvlevel  ";
	$result = DB_query($sql);

	while ($Row = DB_fetch_array($result)) {
		
	echo '<option ';
	echo ($Row['level'] == $_POST['rlevel']) ? 'selected="true"' : '';
	echo 'value="' . $Row['levelcode'] . '">' . $Row['level'] . '</option>';	

}
echo '</select>';
			?>
			  
			  </div>
			</div>
	
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="pvrole">Payment Voucher Roles</label>  
			  <div class="col-md-4">
			 <input id="pvrole" name="pvrole" type="text" value="<?php if(isset($_POST['rpvrole'])){ echo $_POST['rpvrole']; } ?>" placeholder="PV Role" class="form-control input-md" required=""> 
			  </div>
			</div>
						

			<!-- Button -->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="submit"></label>
			  <div class="col-md-8">
				<button id="submit" name="submit" class="btn btn-primary">Submit</button>
				<a href="index.php?Application=PVP&Ref=Dashboard"><input type="button" value="Cancel" class="btn btn-default"></a>
			  </div>
			</div>
	</form>
	<style type="text/css">
	.even{
	background-color:#CCCCCC;
	}
	.odd{
	background-color:#FFFFFF;
	}
	</style>
	<table border="0">
	<tr height="20"><th colspan="5"><strong><center>Current Payment Voucher Roles & Levels</center></strong></th></tr>
  <tr height="30">
    <th width="50" scope="col">S/No.</th>
	<th width="100" scope="col">Pv Level No</th>
    <th width="250" scope="col">PV Roles</th>
    <th width="90" scope="col" colspan="2">Actions</th>
  </tr>
  <?php
  $i=1;
  $num_rec_per_page=10;
  if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; }; 
  $start_from = ($page-1) * $num_rec_per_page; 
  $sql="select * from pvroles ORDER BY level ASC LIMIT $start_from, $num_rec_per_page";
	$qry1 = DB_query($sql);
	while($rec = DB_fetch_array($qry1)){
	$no = $start_from+$i++;
	if($i%2){
	 $class='even';
	}else{
	$class='odd';
	}
	
  echo '<tr class="'.$class.'" height="30">
    <td>'.$no.'</td>
	 <td>'.$rec['level'].'</td>
    <td>'.$rec['pvrole'].'</td>
    <td><a href="index.php?Application=PVM&Ref=pvsetup&id='.$rec['id'].'&Edit=Yes" title="Edit">Edit</a></td>';
	?>
    <td><a onClick="return confirm('Are you sure You want to Delete this Pv Role?');" href="index.php?Application=PVM&Ref=pvsetup&id=<?php echo $rec['id']; ?>&Delete=Yes" title="Delete">Delete</a></td>
  </tr>
  <?php
  }
  ?>
</table>
<?php 
$sql = "SELECT * FROM pvroles"; 
$rs_result = DB_query($sql); //run the query
$total_records = DB_num_rows($rs_result);  //count number of records
$total_pages = ceil($total_records / $num_rec_per_page); 

echo "<a href='index.php?Application=PVP&Ref=pvsetup&page=1'>".'|<'."</a> "; // Goto 1st page  

for ($i=1; $i<=$total_pages; $i++) { 
            echo "<a href='index.php?Application=PVM&Ref=pvsetup&page=".$i."'>".$i."</a> "; 
}; 
echo "<a href='index.php?Application=PVM&Ref=pvsetup&page=$total_pages'>".'>|'."</a> "; // Goto last page
?>
		</div>
	</div>
</div>
		</div>