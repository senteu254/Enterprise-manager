
	<div class="container-fluid">
				<div class = "row">	
					<div class = "col-md-8">
						<h1>Paternity/Maternity Leave</h1>
					</div>
				</div>
	<ul class="nav nav-tabs">
	   <li role="presentation"><a href="<?php echo $mainlink; ?>MPTrack">Recently Approve</a></li>
	  <li role="presentation" class="active"><a href="<?php echo $mainlink; ?>MPAll">Done Approve</a></li>
	</ul>
	<br>
<div class="container-fluid">
	<div class = "row">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="table-responsive">
					<?php
				//this codes is that all the approve will go to the approve submenu
						
						$sql = "SELECT *,employee.emp_id as id FROM employee, mp_log, departments,section, general_manager,managing_director,approve_hr,www_users
									WHERE employee.emp_id = mp_log.emp_id 
									AND employee.id_dept = departments.departmentid
									AND section.id_sec = employee.id_sec
									AND approve_hr.hr_approve = mp_log.hr_approve 
									AND managing_director.md = mp_log.managing_director
									AND general_manager.gm = mp_log.general_manager
									AND www_users.emp_id = employee.emp_id
									AND www_users.userid='".$_SESSION['UserID']."'
								    AND employee.stat='1'
									AND employee.grade ='MANAGER'
									AND approve_hr.hr_approve ='2'
									AND general_manager.gm ='2'
									AND managing_director.md = '2'
									 AND mp_stat='0' ORDER BY employee.emp_id";
						$qry=DB_query($sql);
						
						
					
					
			$sql1 = "SELECT *,employee.emp_id as id FROM employee, mp_log, departments,section,  program_head, general_manager,approve_hr,www_users
									WHERE employee.emp_id = mp_log.emp_id 
									AND employee.id_dept = departments.departmentid
									AND section.id_sec = employee.id_sec
									AND approve_hr.hr_approve = mp_log.hr_approve 
									AND program_head.prog_head = mp_log.prog_head
									AND general_manager.gm = mp_log.general_manager
									AND www_users.emp_id = employee.emp_id
									AND www_users.userid='".$_SESSION['UserID']."'
								    AND employee.stat='1'
									AND employee.grade ='I-CHIEF OFFICER'
									AND approve_hr.hr_approve ='2'
									AND program_head.prog_head ='2'
									AND general_manager.gm  = '4' AND mp_stat='0' ORDER BY employee.emp_id";
						$qry1=DB_query($sql1);
						
				
								
						$sql2 = "SELECT *,employee.emp_id as id FROM employee, mp_log, departments,section, section_head, program_head, chief_officer,approve_hr,www_users
									WHERE employee.emp_id = mp_log.emp_id 
									AND employee.id_dept = departments.departmentid
									AND section.id_sec = employee.id_sec 
									AND approve_hr.hr_approve = mp_log.hr_approve
									AND program_head.prog_head = mp_log.prog_head 
									AND section_head.sec_head = mp_log.section_head
									AND chief_officer.co = mp_log.chief_officer
									AND www_users.emp_id = employee.emp_id
									AND www_users.userid='".$_SESSION['UserID']."'
									AND employee.stat='1'
									 AND employee.grade !='I-CHIEF OFFICER'
									AND employee.grade !='MANAGER'
									AND approve_hr.hr_approve ='2'
									AND section_head.sec_head ='2'
									AND chief_officer.co ='2'   
									AND  program_head.prog_head = '4' AND mp_stat='0' ORDER BY employee.emp_id";
						$qry2=DB_query($sql2);
						
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
									<?php echo $rec['date']; ?>
								</td>
						
								<td>
									<?php echo $rec['name_stat_md']; ?>
								</td>
								</td>
								
								
							</tr>
					</tbody>
				<?php
					}
					
				?>
				
					<?php
						while($rec=DB_fetch_array($qry1))
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
									<?php echo $rec['name_stat_gm']; ?>
								</td>
						
							</tr>
					</tbody>
				<?php
					}
					
				?>
				
					<?php
						while($rec=DB_fetch_array($qry2))
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
									<?php echo $rec['name_stat_prog']; ?>
								</td>
								
								
								
							</tr>
					</tbody>
				<?php
					}
					echo"</table>";
					
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