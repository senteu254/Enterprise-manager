    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<div class="container-fluid">
			
<?php
if(isset($_GET['id']) && is_numeric($_GET['id'])){
if(isset($_GET['Edit']) && $_GET['Edit']=='Yes'){

$sql="SELECT Field_Name,code,acres FROM farmfield WHERE id='".$_GET['id']."'  ";
$qry1 = DB_query($sql);
$rec = DB_fetch_array($qry1);
$_POST['rlevel'] = $rec['Field_Name'];
$_POST['rlevelcode'] = $rec['code'];
$_POST['racres']= $rec['acres'];

}elseif(isset($_GET['Delete']) && $_GET['Delete']=='Yes'){

$sql="DELETE FROM farmfield where id='".$_GET['id']."'  ";
echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.confirm('Are you sure you wish to delete ?)
    </SCRIPT>");
$qry1 = DB_query($sql);

if ($qry1){
prnMsg( _('Field Information Deleted'), 'success');
}
}
else{
prnMsg( _('Field Information  Not Deleted'), 'error');
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
		  $acres = $_POST['acres'];
	
	if(isset($_GET['Edit']) && $_GET['Edit']=='Yes' && isset($_GET['id']) && is_numeric($_GET['id'])){
	$sql = "UPDATE farmfield SET Field_Name='".$id."',acres='".$acres."' WHERE id=".$_GET['id']." ";
$qry = DB_query($sql);
	if ($qry  ){
	prnMsg( _('Farm Field Succesfully Updated'), 'success');
	}
	else{
	prnMsg( _('Farm Field Not Updated'), 'error');
		}
				unset($_GET['Edit']);
				unset($_GET['id']);

				}
else{
					$insert = "INSERT INTO farmfield
									(`id`,`code`,`Field_Name`,`acres`)
										values('','$lcode','$id','$acres')";
					$qry = DB_query($insert);
					if ($qry){
					prnMsg( _('Farm Field Succesfully Added'), 'success');
						}
					else
					prnMsg( _('Farm Field Succesfully Not Added'), 'error');
		}
	}
}
?>
<div class="col-md-10 col-md-offset-1">
	<div class="panel panel-default">
		<div class="panel-heading"><strong><center>Add Farm Fields</center></strong></div>
		<div class="panel-body">
	<form method="POST" class="form-horizontal">
			
			<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			?>
			<!--role code-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="band_id"> Field Code:</label>  
			  <div class="col-md-6">
			  <input id="level" name="levelcode" type="text" value="<?php if(isset($_POST['rlevelcode'])){ echo $_POST['rlevelcode']; } ?>" placeholder="level code" class="form-control input-md" required=""> 
			  			  </div>
			</div>
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="band_id"> Field Name:</label>  
			  <div class="col-md-6">
			  <input id="level" name="level" type="text" value="<?php if(isset($_POST['rlevel'])){ echo $_POST['rlevel']; } ?>" placeholder="pv level" class="form-control input-md" required="">
	</div>
	</div>
	<div class="form-group">
			  <label class="col-md-4 control-label" for="acres">  Acres:</label>  
			  <div class="col-md-6">
			  <input id="level" name="acres" type="text" value="<?php if(isset($_POST['racres'])){ echo $_POST['racres']; } ?>" placeholder="No.of Acres" class="form-control input-md" required="">
	</div>
	</div>
			<div class="form-group">
			  <label class="col-md-4 control-label" for="submit"></label>
			  <div class="col-md-8">
				<button id="submit" name="submit" class="btn btn-primary">Submit</button>
				<a href="index.php?Application=FA2&Ref=Dashboard"><input type="button" value="Cancel" class="btn btn-default"></a>
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
	<table border="0" style="width:70%;">
	<tr height="20"><th colspan="4"><strong>Current  Farm fields</strong></th></tr>
  <tr height="30">
    <th width="150" scope="col">Code</th>
    <th width="150" scope="col">Farm fields</th>
	 <th width="150" scope="col">No.s Acres</th>
    <th width="90" scope="col" colspan="2">Actions</th>
  </tr>
  <?php
  error_reporting(E_ALL & ~E_NOTICE);					
						
  $i=0;
   $sql="select * from farmfield order by code";
	$query = DB_query($sql);
	$query = DB_query($sql);
	while($rec = DB_fetch_array($query)){
	$i++;
	if($i%2){
	 $class='even';
	}else{
	$class='odd';
	}
	
  echo '<tr class="'.$class.'" height="30">
    <td>'.$rec['code'].'</td>
    <td>'.$rec['Field_Name'].'</td>
	<td>'.$rec['acres'].'</td>
	 <td><a href="index.php?Application=FA2&Ref=Farm_Fields&id='.$rec['id'].'&Edit=Yes" title="Edit">Edit</a></td>';
	?>
    <td><a onclick="return confirm('Are you sure You want to Delete this band?');" href="index.php?Application=FA2&Ref=Farm_Fields&id=<?php echo $rec['id']; ?>&Delete=Yes" title="Delete">Delete</a></td>
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
