    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<div class="container-fluid">
			
<?php
if(isset($_GET['id']) && is_numeric($_GET['id'])){
if(isset($_GET['Edit']) && $_GET['Edit']=='Yes'){

$sql="select pvrole,levelcode from pvlevel where id='".$_GET['id']."'  ";
$qry1 = DB_query($sql);
$rec = DB_fetch_array($qry1);
$_POST['rlevel'] = $rec['pvrole'];
$_POST['rlevelcode'] = $rec['levelcode'];

}elseif(isset($_GET['Delete']) && $_GET['Delete']=='Yes'){

$sql="DELETE FROM pvlevel where id='".$_GET['id']."'  ";
echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.confirm('Are you sure you wish to delete ?)
    </SCRIPT>");
$qry1 = DB_query($sql);

if ($qry1){
prnMsg( _('level Information Deleted'), 'success');
}
}
else{
prnMsg( _('level Information  Not Deleted'), 'error');
}
}
	if (isset($_POST['submit'])){
		if (($_POST['level'] == ''))
		{
			echo "You must fill that field";
		}	
	else{  
		
		$id = addslashes("$_POST[level]");
		 $lcode = $_POST['levelcode'];
	
	if(isset($_GET['Edit']) && $_GET['Edit']=='Yes' && isset($_GET['id']) && is_numeric($_GET['id'])){
	$sql = "UPDATE pvlevel SET pvrole='".$id."' WHERE id=".$_GET['id']." ";
$qry = DB_query($sql);
	if ($qry  ){
	prnMsg( _('level Succesfully Updated'), 'success');
	}
	else{
	prnMsg( _('level Not Updated'), 'error');
		}
				unset($_GET['Edit']);
				unset($_GET['id']);

				}
else{
					$insert = "INSERT INTO pvlevel
									(`id`,`levelcode`,`pvrole`)
										values('','$lcode','$id')";
					$qry = DB_query($insert);
					if ($qry){
					prnMsg( _('level Succesfully Added'), 'success');
						}
					else
					prnMsg( _('level Succesfully Not Added'), 'error');
		}
	}
}
?>
<div class="col-md-6 col-md-offset-3">
	<div class="panel panel-default">
		<div class="panel-heading"><strong><center>Add payment voucher approval level</center></strong></div>
		<div class="panel-body">
	<form method="POST" class="form-horizontal">
			
			<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			?>
			<!--role code-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="band_id"> Level Code:</label>  
			  <div class="col-md-6">
			  <input id="level" name="levelcode" type="text" value="<?php if(isset($_POST['rlevelcode'])){ echo $_POST['rlevelcode']; } ?>" placeholder="level code" class="form-control input-md" required=""> 
			  			  </div>
			</div>
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="band_id"> PV level:</label>  
			  <div class="col-md-6">
			  <input id="level" name="level" type="text" value="<?php if(isset($_POST['rlevel'])){ echo $_POST['rlevel']; } ?>" placeholder="pv level" class="form-control input-md" required="">
	</div>
	</div>
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
	<tr height="20"><th colspan="4"><strong>Current  PV Approval Levels</strong></th></tr>
  <tr height="30">
    <th width="150" scope="col">Levelcode</th>
    <th width="150" scope="col">pv level</th>
    <th width="90" scope="col" colspan="2">Actions</th>
  </tr>
  <?php
  error_reporting(E_ALL & ~E_NOTICE);					
						
  $i=0;
  $sql="select * from pvlevel";
	$query = DB_query($sql);
	while($rec = DB_fetch_array($query)){
	$i++;
	if($i%2){
	 $class='even';
	}else{
	$class='odd';
	}
	
  echo '<tr class="'.$class.'" height="30">
    <td>'.$rec['levelcode'].'</td>
    <td>'.$rec['pvrole'].'</td>
	 <td><a href="index.php?Application=PVM&Ref=pvapprlevels&id='.$rec['id'].'&Edit=Yes" title="Edit">Edit</a></td>';
	?>
    <td><a onclick="return confirm('Are you sure You want to Delete this band?');" href="index.php?Application=PVM&Ref=pvapprlevels&id=<?php echo $rec['id']; ?>&Delete=Yes" title="Delete">Delete</a></td>
  </tr>
  <?php
  }
  ?>
</table>
		
			
</div>
		
<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
