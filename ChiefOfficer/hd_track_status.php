
		<div class="container-fluid">
				<div class = "row" style = "margin-top:50px">
					<div class = "col-md-3 col-md-offset-1">
						<h1>Half Day Permission</h1>
					</div>
				</div>
					<br/>
				
			<div class = "col-md-10 col-md-offset-1">
		<?php
			
						$sql = "SELECT *,employee.emp_id as id FROM employee, hd_log, departments,section, section_head,program_head,approve_hr
									WHERE employee.emp_id = hd_log.emp_id 
									AND employee.id_dept = departments.departmentid
									AND approve_hr.hr_approve= hd_log.hr_approve
									AND section.id_sec = employee.id_sec 
									AND section_head.sec_head = hd_log.section_head
									AND program_head.prog_head = hd_log.prog_head
								    AND employee.stat='1' 
									AND hd_log.hd_id = '$_GET[id]'";
						$qry=DB_query($sql);
						
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
											<th>Section Status</th>
											<th>HOD Status</th>
											<th>HRO  Status</th>
											<th>HRM  Status</th>
											
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
									<?php echo $rec['name_stat_sec']; ?>
								</td>
								<td>
									<?php echo $rec['name_stat_prog']; ?>
								</td>
							<td>
									<?php echo $rec['name_stat_hr']; ?>
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
				
				?>
				
			</div>
			<div class="col-md-4 col-md-offset-1">
					<a href="<?php echo $mainlink; ?>HdTrack"><input type = "button" value="Back" class="btn btn-default"/></a>
			</div>
	</div>
	