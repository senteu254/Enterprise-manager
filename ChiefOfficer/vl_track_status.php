
		<div class="container-fluid">
				<div class = "row">
					<div class = "col-md-3 col-md-offset-1">
						<h1>Vacation Leave</h1>
					</div>
				</div>
					<br/>
				
			<div class = "col-md-10 col-md-offset-1">
		<?php
			$sql="select * From employee,vacation_log where employee.emp_id = vacation_log.emp_id AND v_id = '$_GET[id]'";
			$qry3 = DB_query($sql);
			$rec =DB_fetch_array($qry3);
			$grade = $rec ['grade'];
						//this codes is that all the approve will go to the approve submenu
						if($grade=='MANAGER'){
						$sql = "SELECT *,employee.emp_id as id FROM employee, vacation_log, departments,section, general_manager,managing_director,approve_hr
									WHERE employee.emp_id = vacation_log.emp_id 
									AND employee.id_dept = departments.departmentid
									AND approve_hr.hr_approve= vacation_log.hr_approve
									AND section.id_sec = employee.id_sec 
									AND managing_director.md = vacation_log.managing_director
									AND general_manager.gm = vacation_log.general_manager
								    AND employee.stat='1' 
									AND vacation_log.v_id = '$_GET[id]'";
						$qry=DB_query($sql);
						}
						
					else if($grade=='I-CHIEF OFFICER'){
					
			$sql1 = "SELECT *,employee.emp_id as id FROM employee, vacation_log, departments,section, general_manager,program_head,approve_hr
									WHERE employee.emp_id = vacation_log.emp_id 
									AND employee.id_dept = departments.departmentid
									AND approve_hr.hr_approve= vacation_log.hr_approve
									AND section.id_sec = employee.id_sec
									AND program_head.prog_head = vacation_log.prog_head 
									AND general_manager.gm = vacation_log.general_manager
									AND approve_hr.hr_approve = vacation_log.hr_approve
								    AND employee.stat='1' 
									AND vacation_log.v_id = '$_GET[id]'";
						$qry1=DB_query($sql1);
						}
				
						else {			
						$sql2 = "SELECT *,employee.emp_id as id FROM employee, vacation_log, departments,section, section_head, program_head, chief_officer,approve_hr
									WHERE employee.emp_id = vacation_log.emp_id 
									AND employee.id_dept = departments.departmentid
									AND approve_hr.hr_approve= vacation_log.hr_approve
									AND section.id_sec = employee.id_sec 
									AND program_head.prog_head = vacation_log.prog_head 
									AND section_head.sec_head = vacation_log.section_head
									AND chief_officer.co = vacation_log.chief_officer
									AND employee.stat='1' 
									AND vacation_log.v_id = '$_GET[id]'";
						$qry2=DB_query($sql2);
						}
					?>
					
					<?php
					if($grade=='MANAGER' && $qry){
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
											<th>HR Status</th>
											<th>General Manager Status</th>
											<th>Managing Director Status</th>
											
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
									<?php echo $rec['name_stat_hr']; ?>
								</td>
							<td>
									<?php echo $rec['name_stat_gm']; ?>
								</td>
						
								<td>
									<?php echo $rec['name_stat_md']; ?>
								</td>
								</td>
								
								
							</tr>
					</tbody>
				<?php
					}
					echo"</table>";
					}
				
				else if($grade=='I-CHIEF OFFICER' && $qry1){
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
											<th>HR Status</th>
											<th>Head Status</th>
											<th>General Manager Status</th>
											
											
										</tr>
									</thead>
					
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
									<?php echo $rec['vdate']; ?>
								</td>
								<td>
									<?php echo $rec['name_stat_hr']; ?>
								</td>
								<td>
									<?php echo $rec['name_stat_prog']; ?>
									</td>
									<td>
									<?php echo $rec['name_stat_gm']; ?>
								</td>
						
								
								
								
								
							</tr>
					</tbody>
				<?php
					}
					echo"</table>";
					}
				
				else {
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
											<th>HR Status</th>
											<th>Section Status</th>
											<th>Chief Officer Status</th>
											<th>Head Status</th>
											
											
										</tr>
									</thead>
					
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
									<?php echo $rec['vdate']; ?>
								</td>
								<td>
									<?php echo $rec['name_stat_hr']; ?>
								</td>
								<td>
									<?php echo $rec['name_stat_sec']; ?>
								</td>
								<td>
									<?php echo $rec['name_stat_co']; ?>
								</td>
								<td>
									<?php echo $rec['name_stat_prog']; ?>
								</td>
								
								
								
							</tr>
					</tbody>
				<?php
					}
					echo"</table>";
					}
				?>
						
			</div>
			<div class="col-md-4 col-md-offset-1">
					<a href="<?php echo $mainlink; ?>VacationStatus"><input type = "button" value="Back" class="btn btn-default"/></a>
			</div>
	</div>
	