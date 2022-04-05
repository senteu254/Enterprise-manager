<style type="text/css">

/* For pagination function. */
ul.pagination {
    text-align:center;
    color:#829994;
}
ul.pagination li {
    display:inline;
    padding:0 3px;
}
ul.pagination a {
    color:#0d7963;
    display:inline-block;
    padding:5px 10px;
    border:1px solid #cde0dc;
    text-decoration:none;
}
ul.pagination a:hover,
ul.pagination a.current {
    background:#0d7963;
    color:#fff;
}
</style>
<link rel="stylesheet" href="HR_Head/font-awesome/css/font-awesome.min.css">
<div class="container-fluid">			
	<div class="col-md-12">
			<div class = "col-md-10">
<?php
			  
						//this will get the data in database		
						$welcome= "SELECT COUNT(emp_id) as num FROM employee WHERE stat=1";
						$welcome_viewed = DB_query($welcome);
						$num = DB_fetch_row($welcome_viewed);
						echo "<h3> $num[0] Employees </h3>";
					?>
			</div>
			
		<div class="col-md-2">
			<p class="text-right"><a href = "index.php?Application=HR&Ref=AddEmp"><button type="button" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Add Employee">Add New Employee</button></a></p>
		</div>
	</div>
	
<form action="index.php?Application=HR&Ref=List" method="post">
	<div class="container-fluid" style="margin-top:50px;">
	<div class = "row">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="table-responsive">
<?php
if(isset($_GET['Status'])){
$_POST['status'] = $_GET['Status'];
}

if (isset($_GET['Enable'])){
$qry =DB_query("UPDATE employee SET stat=1
					WHERE employee.emp_id='".$_GET['Enable']."'");
$qry =DB_query("UPDATE www_users SET blocked=0
					WHERE emp_id='".$_GET['Enable']."'");
$_SESSION['msg'] = _('Employee Has been Enabled Successfully');

}

echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
$search = '';
if(isset($_POST['Search'])){
if(isset($_POST['first']) && $_POST['first'] !=""){
$search = "AND emp_fname LIKE '%$_POST[first]%'";
}elseif(isset($_POST['last']) && $_POST['last'] !=""){
$search = "AND emp_lname LIKE '%$_POST[last]%'";
}elseif(isset($_POST['id']) && $_POST['id'] !=""){
$search = "AND emp_id LIKE '%".$_POST['id']."%'";
}else{
$search = '';
}

}
$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
 
$per_page = 10; // Set how many records do you want to display per page.
 
$startpoint = ($page * $per_page) - $per_page;
if(isset($_POST['status']) && ($_POST['status'] ==1 or $_POST['status'] ==2)){
$statement = "employee WHERE id_pos = ".$_POST['status']." AND stat=1 ".$search." ORDER BY employee.emp_id ASC"; // Change `records` according to your table name.  
$results = mysqli_query($db,"SELECT * ,employee.emp_id as id FROM {$statement} LIMIT {$startpoint} , {$per_page}");

}elseif(isset($_POST['status']) && $_POST['status'] ==3){
$statement = "employee WHERE stat=2 ".$search." ORDER BY employee.emp_id ASC"; // Change `records` according to your table name.  
$results = mysqli_query($db,"SELECT * ,employee.emp_id as id FROM {$statement} LIMIT {$startpoint} , {$per_page}");

}else{
$statement = "employee WHERE employee.stat=1 ".$search." ORDER BY employee.emp_id ASC"; // Change `records` according to your table name.
  
$results = mysqli_query($db,"SELECT * ,employee.emp_id as id FROM {$statement} LIMIT {$startpoint} , {$per_page}");
}

$array =array('1'=>'Permanent','2'=>'Provisionary','3'=>'Past');

echo '<div class="container-fluid">
						<div class="box-body">
              <div class="form-group">
			  <div class="col-md-3">';
echo 'Employee Status:</label>';
echo '<select name="status" class="form-control input-md">';
echo '<option value="">All Employees</option>';
foreach($array as $key=> $data){
echo '<option '.($_POST['status']==$key ? 'selected="selected"' : '').' value="'.$key.'">'.$data.'</option>';
}
echo '</select></div>';
?>
			<div class="col-md-3">First Name:
			<input id="first"  name ="first" type="text" class="form-control input-md" placeholder="Search by First Name">
			 </div>
			 <div class="col-md-3">Last Name:
			 <input id="last"  name ="last" class="form-control input-md" type="text" placeholder="Search by Last Name">
			 </div>
			 <div class="col-md-3">Employee No.
			 <input id="id" class="form-control input-md"  name ="id" type="text" placeholder="Search by Employee No">
			 </div>
				</div>
            </div>
			</div>
			
			</div>
			<p></p>
			<div class="box-footer">
			<div class="pull-right">
                <button type="submit" name="Search" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
              </div>
			  <a href="PDFEmployeeReport.php" target="_blank"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>
            </div>

	</div>
			</div>
		</div>
	</div>
</div>
</form>
<?php
error_reporting( error_reporting() & ~E_NOTICE ); if(!empty($_SESSION['msg'])) echo '<div id="div3" class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success</h4>
                ' . ucwords($_SESSION['msg']). '
              </div>'; unset($_SESSION['msg']); 
			 if(!empty($_SESSION['errmsg'])) echo '<div id="div3" class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>
                ' . ucwords($_SESSION['errmsg']). '
              </div>'; unset($_SESSION['errmsg']); 
?>	
	
<div class="container-fluid">
	<div class = "row" >
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="table-responsive">

					<table class="table table-hover" style="width:100%">
						<thead>
							<tr>
								<th>Personal Number</th>
								<th>First Name</th>
								<th>Last Name</th>
								<th>Gender</th>
								<th>ID Number</th>
								<?php
								if($_POST['status'] ==3){
								echo '<th>Date of Exit</th>
								<th>Reason</th>';
								}
								?>
								
								<th style="text-align:center; width:50px;">Actions</th>
							</tr>
						</thead>
						<?php
						while($rec=DB_fetch_array($results))
						{
						?>
						<tbody>
							<tr>
									<td width="140">
										<?php echo $rec['emp_id'] ?>
									</td>
									<td>
										<?php echo $rec['emp_fname'] ?>
									</td>
									<td>
										<?php echo $rec['emp_lname'] ?>
									</td>
									<td>
										<?php echo $rec['emp_gen'] ?>
									</td>
									<td>
										<?php echo $rec['id_number'] ?>
									</td>
									<?php
									if($_POST['status'] ==3){
									echo '<td>'.$rec['exitdate'].'</td>
									<td>'.$rec['reasonforexit'].'</td>';
									}
									?>
									<td>
										<a href='index.php?Application=HR&Ref=Profile&id=<?php echo $rec['id']?>' data-toggle="tooltip" data-placement="top" title="Profile"><span class="glyphicon glyphicon-list-alt" style="font-size:20px"></span></a>
										<?php if($_POST['status'] ==3){ ?>
										<a href='index.php?Application=HR&Ref=List&Enable=<?php echo $rec['id']?>' data-toggle="tooltip" data-placement="top" title="Enable Account"   onclick="return confirm('Are you sure you wish to Enable this employee account?')" ><span class="glyphicon glyphicon-check" style="font-size:20px; color:green"></span></a>
										<?php }else{ ?>
										<a href='index.php?Application=HR&Ref=Disable&id=<?php echo $rec['id']?>' data-toggle="tooltip" data-placement="top" title="Disable Account"   onclick="return confirm('Are you sure you wish to disable this employee account?')" ><span class="glyphicon glyphicon-trash" style="font-size:20px; color:red"></span></a>
										<?php } ?>
									</td>
					<?php
						}
					?>
					
					
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
 
 // displaying paginaiton.
echo pagination($statement,$per_page,$page,$url='?Application=HR&Ref=List&Status='.$_POST['status'].'&');
?>
</div>
<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
	