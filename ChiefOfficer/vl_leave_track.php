
	<div class="container-fluid">
				<div class = "row" >	
					<div class = "col-md-5">
						<h1>Vacation Leave</h1>
					</div>
				</div>
	<ul class="nav nav-tabs">
	  <li role="presentation" class="active"><a href="<?php echo $mainlink; ?>VacationStatus">Recently Approve</a></li>
	  <li role="presentation"><a href="<?php echo $mainlink; ?>VAll">Done Approve</a></li>
	</ul>
	<br>
<div class="container-fluid">
	<div class = "row">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="table-responsive">
					<?php
				$sql="select * From employee,vacation_log where employee.emp_id = vacation_log.emp_id ";
			$qry3 = DB_query($sql);
			$rec = DB_fetch_array($qry3);
			$grade = $rec ['grade'];
						
						if($grade=='MANAGER'){
						//this codes is that all the approve will go to the approve submenu
						$sql = "SELECT *,employee.emp_id as id FROM employee, departments,section, vacation_log,managing_director,www_users
									WHERE employee.emp_id = vacation_log.emp_id 
									AND departments.departmentid = employee.id_dept
									AND section.id_sec = employee.id_sec
									AND www_users.emp_id = employee.emp_id
									AND www_users.userid='".$_SESSION['UserID']."'
									AND employee.stat = '1'
									AND managing_director.md  = vacation_log.managing_director
									AND managing_director.md = '1'"; 
		
						$qry=DB_query($sql);
						}
						else if($grade=='I-CHIEF OFFICER'){
						$sql = "SELECT *,employee.emp_id as id FROM employee, departments,section, vacation_log,general_manager,www_users
									WHERE employee.emp_id = vacation_log.emp_id 
									AND departments.departmentid = employee.id_dept
									AND section.id_sec = employee.id_sec
									AND www_users.emp_id = employee.emp_id
									AND www_users.userid='".$_SESSION['UserID']."'
									AND general_manager.gm  = vacation_log.general_manager
									AND employee.stat = '1'
									AND general_manager.gm='1'"; 
		
						$qry=DB_query($sql);
						}
						else{
						$sql = "SELECT *,employee.emp_id as id FROM employee, departments,section, vacation_log,program_head,www_users
									WHERE employee.emp_id = vacation_log.emp_id 
									AND departments.departmentid = employee.id_dept
									AND section.id_sec = employee.id_sec
									AND www_users.emp_id = employee.emp_id
									AND www_users.userid='".$_SESSION['UserID']."'
									AND program_head.prog_head  = vacation_log.prog_head
									AND program_head.prog_head='1'
									AND employee.stat = '1'"; 
		
						$qry=DB_query($sql);
						}
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
									<?php echo $rec['vdate']; ?>
								</td>
								<td>
									<a href="<?php echo $mainlink; ?>VStatus&id=<?php echo $rec['v_id'];?>"><input type='button' value='View' data-toggle="tooltip" data-placement="top" title="View Status" class='btn btn-info'/></a>
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
	