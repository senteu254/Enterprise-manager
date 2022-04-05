
	<div class="container-fluid">
				<div class = "row" style = "margin-top:10px">	
					<div class = "col-md-8">
						<h1>Half Day Permission</h1>
					</div>
				</div>
	<ul class="nav nav-tabs">
	  <li role="presentation"><a href="<?php echo $mainlink; ?>HdTrack">Recently Approve</a></li>
	  <li role="presentation" class="active"><a href="<?php echo $mainlink; ?>HdAppAll">Done Approve</a></li>
	</ul>
	<br>
<div class="container-fluid">
	<div class = "row">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="table-responsive">
					<?php
				$sql =  "SELECT *,employee.emp_id as id FROM employee, hd_log, departments,section, section_head,program_head,approve_hr,www_users
									WHERE employee.emp_id = hd_log.emp_id 
									AND employee.id_dept = departments.departmentid
									AND approve_hr.hr_approve= hd_log.hr_approve
									AND section.id_sec = employee.id_sec 
									AND section_head.sec_head = hd_log.section_head
									AND program_head.prog_head = hd_log.prog_head
									AND www_users.emp_id = employee.emp_id
									AND www_users.userid='".$_SESSION['UserID']."'
									AND section_head.sec_head ='2'
									AND program_head.prog_head ='2'
									AND approve_hr.hr_approve='2'
								    AND employee.stat='1' 
									ORDER BY employee.emp_id";
						$qry=DB_query($sql);
						
					if($qry){
					?>
						<table class='table table-hover' style='margin-top:10px;'>
									<thead>
										<tr>
											<th>Personal Number</th>
											<th>Department</th>
											<th>Section</th>
											<th>First Name</th>
											<th>Last Name</th>
											<th>Filed Date</th>
											<th>Status</th>
										</tr>
									</thead>	
			
					<tbody>
					<?php
						while($rec=DB_fetch_array($qry))
						{
					?>
					<tbody>
						<tr>
								<td>
									<?php echo $rec['id']; ?>
								</td>
								<td>
									<?php echo $rec['description']; ?>
								</td>
								<td>
									<?php echo $rec['section_name']; ?>
								</td>
								<td>
									<?php echo $rec['emp_fname']; ?>
								</td>
								<td>
									<?php echo $rec['emp_lname']; ?>
								</td>
								<td>
									<?php echo $rec['date']; ?>
								</td>
						
								<td>
									<?php echo $rec['name_stat_hr']; ?>
								</td>
								</td>
								
								
							</tr>
					</tbody>
				
				<?php
					}
					echo"</table>";
					}
				?>
						
				
				
					
				</div>
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
