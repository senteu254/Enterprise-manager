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
<?php
$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
 
$per_page = 10; // Set how many records do you want to display per page.
 
$startpoint = ($page * $per_page) - $per_page;
?>
<link rel="stylesheet" href="HR_Head/font-awesome/css/font-awesome.min.css">

<div class="container-fluid">
	<div class = "row" >
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="table-responsive">

					<table class="table table-hover" style="width:100%">
					<tr><th colspan="6"><center>KOFC ESTABLISHMENT STATUS</center></th></tr>
						
							<tr>
								<th>DESIGNATION</th>
								<th>ESTB</th>
								<th>CIV</th>
								<th>MIL</th>
								<th>VACANT</th>
								<th>SURPLUS</th>
							</tr>
						
						<?php
						$group ='';
						$results = mysqli_query($db,"SELECT a.appointment_name,
															a.establishment,
															a.category,
															b.description,
															(SELECT COUNT(emp_id) FROM employee e WHERE e.appointment_name=a.appointment_name AND personnel='Civilian') AS CIV,
															(SELECT COUNT(emp_id) FROM employee e WHERE e.appointment_name=a.appointment_name AND personnel='Military') AS MIL,
															(a.establishment-(SELECT COUNT(emp_id) FROM employee e WHERE e.appointment_name=a.appointment_name)) AS BAL
													FROM appointment a
													INNER JOIN appointment_category b ON a.category=b.id");
						while($rec=DB_fetch_array($results)){
						?>
						<tbody>
						<?php
						if($group!=$rec['category']){
						echo '<tr><td colspan="6"><center><strong>'.strtoupper($rec['description']).'</strong></center></td></tr>';
						$group=$rec['category'];
						}
						?>
							<tr>
									<td>
										<?php echo $rec['appointment_name']; ?>
									</td>
									<td style="color:#0000FF;">
										<?php echo $rec['establishment']; ?>
									</td>
									<td>
										<?php echo $rec['CIV']; ?>
									</td>
									<td>
										<?php echo $rec['MIL']; ?>
									</td>
									<td style="color:#009933;">
										<?php echo ($rec['BAL']>0? $rec['BAL']:''); ?>
									</td>
									<td style="color:#FF0000;">
										<?php echo ($rec['BAL']<0? $rec['BAL']:''); ?>
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
	