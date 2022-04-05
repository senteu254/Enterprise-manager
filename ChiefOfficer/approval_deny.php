
		<div class="container-fluid">
				<div class = "row">	
<div class = "col-md-4">
						<h1>Sick Leave</h1>
					</div>
					</div>
				<div class = "row">	
					<div class = "col-md-5">
					<ul class="nav nav-tabs">  
						  <li role="presentation"><a href="<?php echo $mainlink; ?>SickLeave">Pending</a></li>
						  <li role="presentation"><a href="<?php echo $mainlink; ?>SickLeaveAppv">Approve</a></li>
						  <li role="presentation"  class="active"><a href="<?php echo $mainlink; ?>SickLeaveDeny">Deny</a></li>
						</ul>
					</div>
				</div>
<div class="container-fluid">
	<div class = "row">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="table-responsive">
					<?php
						
						//this codes is that all the approve will go to the approve submenu
						$sql = "SELECT *,employee.emp_id as id FROM employee ,leaves,chiefofficer,section,departments, approve_hr,section_head,chief_officer
						where leaves.hr_approve = approve_hr.hr_approve 
						AND chief_officer.co=leaves.chief_officer
						AND section_head.sec_head=leaves.section_head
						AND employee.id_dept = departments.departmentid
						AND employee.emp_id = leaves.emp_id  
						AND section.id_dept = departments.departmentid
						AND chiefofficer.id_dept=departments.departmentid
						AND approve_hr.hr_approve = '2'
						AND section_head.sec_head='2'
						AND chief_officer.co='3'
						AND employee.grade!='MANAGER'
						AND employee.grade!='I-CHIEF OFFICER'
						AND chiefofficer.id_co = '$_SESSION[LID]'";
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
											<th>Status</th>
											<th></th>
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
									<?php echo $rec['name_stat_co']; ?>
								</td>
								<td>
								<a href="<?php echo $mainlink; ?>SLDenyView&id=<?php echo $rec['leaveid'];?>"><input type='button' value='View' data-toggle="tooltip" data-placement="top" title="View Content" class='btn btn-info'/></a>
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
