<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>

<link rel="stylesheet" type="text/css" href="HR_Head/tree/ltr.css">
<script type="text/javascript" src="HR_Head/tree/jquery.js"></script>
<script type="text/javascript" src="HR_Head/tree/funcs.js"></script>

<div class="container-fluid">
		<div class="col-md-10 col-md-offset-1">
	<div class="panel panel-default">
		<div class="panel-heading"><strong><center>Organization Structure</center></strong></div>
		<div class="panel-body">
		
		<?php		

if(isset($_GET['Delete']) && $_GET['Delete']=='Yes'){

$sql="DELETE FROM organization_chart where id='".$_GET['id']."'  ";
echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.confirm('Are you sure you want to delete?')
    window.location.href='index.php?Application=HR&Ref=OrgChart';
    </SCRIPT>");
$qry = DB_query($sql);

if ($qry){
	prnMsg( _('Organization Information Successfully Deleted'), 'success');
}
else{
prnMsg( _('Organization Information Not Deleted Please try Again'), 'error');
    }
}


	if (isset($_POST['submit'])){
		//dri mah vl.an kung mai onud ang txtbox then ang mga "category or stu_id is  name sa entity sa database
		if (($_POST['title'] == '') || ($_POST['employees'] == ''))
		{
			echo "You must fill those field";
		}	
	else{  
		
		$a = addslashes("$_POST[title]");
		$b = addslashes("$_POST[parentid]");
		$c = addslashes("$_POST[employees]");
		$d = addslashes("$_POST[id_dept]");
		$e = addslashes("$_POST[id_sec]");

$sql="select * FROM organization_chart where title='$a'  ";
$qry = DB_query($sql);
$rec = DB_fetch_array($qry);
if($rec>0 ){
prnMsg( _('Organization Information Already Exists!'), 'warn');
	}
else
					{
					$insert = "INSERT INTO organization_chart
									(`id`,`title`,`parent_id`,`no_of_employees`,`id_dept`,`id_sec`)
										values('','$a','$b','$c','$d','$e')";
					$qry = DB_query($insert);
					
					if ($qry){
						prnMsg( _('Organization Information added'), 'success');

						}
					else
				prnMsg( _('Organization Information added Not Added'), 'error');
				unset($_POST['title']);
		            unset($_POST['parentid']);
		            unset($_POST['employees']);
					  unset($_POST['id_dept']);
					  unset($_POST['id_sec']);
		}
	}
}

if(isset($_GET['id']) && is_numeric($_GET['id'])){
if(isset($_GET['Edit']) && $_GET['Edit']=='Yes'){
	
$sql="select * from organization_chart where id='".$_GET['id']."'  ";
$qry = DB_query($sql);
$rec = DB_fetch_array($qry);
$_POST['rtitle'] = $rec['title'];
$_POST['rparentid'] = $rec['parent_id'];
$_POST['remployees'] = $rec['no_of_employees'];
$_POST['rid_dept'] = $rec['id_dept'];
$_POST['rid_sec'] = $rec['id_sec'];

if (isset($_POST['update'])){
	
	
	$sql1 = "UPDATE organization_chart SET 
	title='".$_POST['title']."', 
	parent_id='".$_POST['parentid']."', 
	no_of_employees='".$_POST['employees']."', 
	id_dept='".$_POST['id_dept']."',
	id_sec='".$_POST['id_sec']."'
	WHERE id='".$_GET['id']."' ";
$qry2 = DB_query($sql1);

	if ($qry2){
	prnMsg( _('Organization Information Updated'), 'success');
						}
		else{
		prnMsg( _('Organization Information Not Updated'), 'error');
		echo "Not Updated";
		}
unset($_POST['title']);
unset($_POST['parentid']);
unset($_POST['employees']);
unset($_POST['id_dept']);
unset($_POST['id_sec']);
}	
			
}
}
?>
	<form method="POST" class="form-horizontal">
			
			<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			
		
?>
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="title">Title:</label>  
			  <div class="col-md-4">
			  <input id="title" name="title" type="text" value="<?php if(isset($_POST['rtitle'])){ echo $_POST['rtitle']; } ?>" placeholder="Title" class="form-control input-md" > 
			  			  </div>
			</div>
		
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="parentid">Parent Id:</label>  
			  <div class="col-md-4">
			  <?php
			  echo'<select  name="parentid">';
			  	echo '<option selected="selected" value="'.$myrow['id'].'">Select</option>';
			  $AssetSQL="SELECT id,title FROM organization_chart  ";
$AssetResult=DB_query($AssetSQL);
while ($myrow=DB_fetch_array($AssetResult)) {
if ($myrow['id']==$_POST['rparentid']) {
		echo '<option selected="selected" value="'.$myrow['id'].'">' . $myrow['id'] . '-'.$myrow['title'].'  </option>';
	} else {
		echo '<option value="'.$myrow['id'].'">' . $myrow['id'] . '-'.$myrow['title'].'  </option>';
	}
}
	
			  			echo'  
						</select>
						</div>
			</div>';
		?>
		<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="employees">No Of Employees:</label>  
			  <div class="col-md-4">
			  <input id="employees" name="employees" type="text" value="<?php if(isset($_POST['remployees'])){ echo $_POST['remployees']; } ?>" placeholder="No of employees" class="form-control input-md" > 
			  			  </div>
			</div>
					
		<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="id_dept">Depart Id:</label>  
			  <div class="col-md-4">
			  <?php
			  echo'<select  name="id_dept">';
			  	echo '<option selected="selected" value="'.$myrow['departmentid'].'">Select id as foreign key from department table</option>';
			  $AssetSQL="SELECT departmentid,description FROM departments  ";
$AssetResult=DB_query($AssetSQL);
while ($myrow=DB_fetch_array($AssetResult)) {
if ($myrow['departmentid']==$_POST['rid_dept']) {
		echo '<option selected="selected" value="'.$myrow['departmentid'].'">' . $myrow['departmentid'] . '-'.$myrow['description'].'  </option>';
	} else {
		echo '<option value="'.$myrow['departmentid'].'">' . $myrow['departmentid'] . '-'.$myrow['description'].'  </option>';
	}
}
	
			  			echo'  
						</select>
						</div>
			</div>';
		?>	
					

<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="id_dept">Section Id:</label>  
			  <div class="col-md-4">
			  <?php
			  echo'<select  name="id_sec">';
			  	echo '<option selected="selected" value="'.$myrow['id_sec'].'">Select id as foreign key from section table</option>';
			  $AssetSQL="SELECT id_sec,section_name FROM section  ";
$AssetResult=DB_query($AssetSQL);
while ($myrow=DB_fetch_array($AssetResult)) {
if ($myrow['id_sec']==$_POST['rid_sec']) {
		echo '<option selected="selected" value="'.$myrow['id_sec'].'">' . $myrow['id_sec'] . '-'.$myrow['section_name'].'  </option>';
	} else {
		echo '<option value="'.$myrow['id_sec'].'">' . $myrow['id_sec'] . '-'.$myrow['section_name'].'  </option>';
	}
}
	
			  			echo'  
						</select>
						</div>
			</div>';
		?>	



  <?php if (isset($_GET['Edit'])) {
	
						echo '<div class="form-group">
							  <label class="col-md-3 control-label" for="submit" class="btn btn-primary"></label>
							  <div class="col-md-8">
							  <button id="submit" name="update class="btn btn-primary">Update</button>
							
				<a href="index.php?Application=HR&Ref=Dashboard"><input type="button" value="Cancel" class="btn btn-default"></a>
							  </div>
							</div>';
							} 
							else {
							echo'<div class="form-group">
							  <label class="col-md-3 control-label" for="submit" class="btn btn-primary"></label>
							  <div class="col-md-8">
							  <button id="submit" name="submit" class="btn btn-primary">Submit</button>
				<a href="index.php?Application=HR&Ref=Dashboard"><input type="button" value="Cancel" class="btn btn-default"></a>
							  </div>
							</div>';
							}
							?>
			
	</form>
	<div class="container-fluid">
	<div class = "row">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="table-responsive">

	<style type="text/css">
	.even{
	background-color:#CCCCCC;
	}
	.odd{
	background-color:#FFFFFF;
	}
	</style>
	<table border="0">
	<tr height="20"><th colspan="4"><strong>Organization Chart</strong></th></tr>
  <tr height="30">
    <th width="150" scope="col">Id.</th>
    <th width="250" scope="col">Title</th>
	<th width="250" scope="col">Parent Id</th>
	<th width="250" scope="col">No Of Employees</th>
	<th width="250" scope="col">Department Id</th>
	<th width="250" scope="col">Section Id</th>
    <th width="90" scope="col" colspan="2">Actions</th>
  </tr>
  <?php
  error_reporting(E_ALL & ~E_NOTICE);
						//paging codes
						if (isset($_GET["page"])) 
											{ 
												$page = $_GET["page"]; 
											} else { 
												$page=1; 
											};
											$endlimit = 5; 
											$start_from = ($page-1) * $endlimit;
										  $sql="select * from organization_chart  ";
	$qry1 = DB_query($sql);	
  $i=0;
  $sql="select * from organization_chart LIMIT $start_from, $endlimit";
	$query = DB_query($sql);
	while($rec = DB_fetch_array($query)){
	$i++;
	if($i%2){
	 $class='even';
	}else{
	$class='odd';
	}
	
  echo '<tr class="'.$class.'" height="30">
    <td>'.$rec['id'].'</td>
    <td>'.$rec['title'].'</td>
	<td>'.$rec['parent_id'].'</td>
	<td>'.$rec['no_of_employees'].'</td>
	<td>'.$rec['id_dept'].'</td>
	<td>'.$rec['id_sec'].'</td>
    <td><a href="index.php?Application=HR&Ref=OrgChart&id='.$rec['id'].'&Edit=Yes" title="Edit">Edit</a></td>';
	?>
    <td><a onClick="return confirm('Are you sure You want to Delete this Organization Chart Value?');" href="index.php?Application=HR&Ref=OrgChart&id=<?php echo $rec['id']; ?>&Delete=Yes" title="Delete">Delete</a></td>
  </tr>
  <?php
 }
  ?>
</table>
					
</div>
	
	</div>
	
</div>

	
	</div>					
					
<?php $rec = DB_num_rows($qry1);
		$total_pages = ceil($rec / $endlimit);
		$j=0;
		echo ' '.$_REQUEST["page"].' ';
				for($j=1; $j<=$total_pages; $j++ )
					{
						 echo"<ul class='pagination '> <li>&nbsp<a href = 'index.php?Application=HR&Ref=OrgChart&page=".$j."'>".$j."</a></li></ul>";
					}
						echo'&nbsp&nbsp';
						
					?>

<div class="container-fluid">
	<div class = "row">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="table-responsive">

 <?php
 
 //include the treeview class
 include 'class.treeview.php';
  include 'config.php';
//create an instant of Treeview Class
 $treeSample = new Treeview('organization_chart', 'id', 'title', 'parent_id','no_of_employees','id_dept','id_sec', 'org');
 //Calling the method to generate tree view and set the queryArray public member for Input Parameter
 $treeSample->generate_tree_list($treeSample->queryArray);
 //echo the public member of object names treeResult (Contain the treeview html and jquery codes)
 echo $treeSample->treeResult;
?>
 
 
</div>
	</div>	
</div>
	</div>
	</div>
	<script>
		$(function () {
		$('[data-toggle="tooltip"]').tooltip()
		})
	</script>					
