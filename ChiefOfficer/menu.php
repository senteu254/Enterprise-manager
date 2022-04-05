<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CHIEF OFFICER</title>
	<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
		<style>
		
		.navbar-default{
		opacity: .9;
		z-index:999;
		}
		.dropdown:hover .dropdown-menu {
		display: block;
		}
		.panel-default{
		opacity: .9;
		}
		hr.message-inner-separator
{
    clear: both;
    margin-top: 10px;
    margin-bottom: 13px;
    border: 0;
    height: 1px;
    background-image: -webkit-linear-gradient(left,rgba(0, 0, 0, 0),rgba(0, 0, 0, 0.15),rgba(0, 0, 0, 0));
    background-image: -moz-linear-gradient(left,rgba(0,0,0,0),rgba(0,0,0,0.15),rgba(0,0,0,0));
    background-image: -ms-linear-gradient(left,rgba(0,0,0,0),rgba(0,0,0,0.15),rgba(0,0,0,0));
    background-image: -o-linear-gradient(left,rgba(0,0,0,0),rgba(0,0,0,0.15),rgba(0,0,0,0));
}

		</style>
  </head>
<body>
<div class="container-fluid">
	<!--welcome user in menu-->


	<?php
						error_reporting(E_ALL & ~E_NOTICE);
						//this will get the data in database
						
						//vacation		
						$welcome_view = "SELECT *,employee.emp_id as id FROM employee ,vacation_log,chiefofficer,section,departments, approve_hr,section_head,chief_officer
						WHERE vacation_log.hr_approve = approve_hr.hr_approve 
						AND chief_officer.co = vacation_log.chief_officer
						AND section_head.sec_head = vacation_log.section_head
						AND employee.id_dept = departments.departmentid
						AND employee.emp_id = vacation_log.emp_id  
						AND section.id_dept = departments.departmentid
						AND chiefofficer.id_dept = departments.departmentid
						AND approve_hr.hr_approve = '2'
						AND section_head.sec_head='2'
						AND chief_officer.co='1'
						AND employee.grade!='MANAGER'
						AND employee.grade!='I-CHIEF OFFICER'
						AND chiefofficer.id_co = '$_SESSION[LID]'";
						
						
						$welcome_viewed = DB_query($welcome_view);
						
						$num_rows1 = DB_num_rows($welcome_viewed);
					    if($num_rows1 == 1);
						
						//sick
						$sql = "SELECT *,employee.emp_id as id FROM employee ,leaves,chiefofficer,section,departments, approve_hr,section_head,chief_officer
						WHERE leaves.hr_approve = approve_hr.hr_approve 
						AND chief_officer.co = leaves.chief_officer
						AND section_head.sec_head=leaves.section_head
						AND employee.id_dept = departments.departmentid
						AND employee.emp_id = leaves.emp_id  
						AND section.id_dept = departments.departmentid
						AND chiefofficer.id_dept = departments.departmentid
						AND approve_hr.hr_approve = '2'
						AND section_head.sec_head='2'
						AND chief_officer.co='1'
						AND employee.grade!='MANAGER'
						AND employee.grade!='I-CHIEF OFFICER'
						AND chiefofficer.id_co = '$_SESSION[LID]'";
						
						$qry=DB_query($sql);
						$num_rows2 = DB_num_rows($qry);
						if($num_rows2 == 1);
						
						//Paternity/maternity		
								               	
						$sql1 = "SELECT *,employee.emp_id as id FROM employee ,mp_log,chiefofficer,section,departments, approve_hr,section_head,chief_officer
						WHERE mp_log.hr_approve = approve_hr.hr_approve 
						AND chief_officer.co=mp_log.chief_officer
						AND section_head.sec_head=mp_log.section_head
						AND employee.id_dept = departments.departmentid
						AND employee.emp_id = mp_log.emp_id  
						AND section.id_dept = departments.departmentid
						AND chiefofficer.id_dept = departments.departmentid
						AND approve_hr.hr_approve = '2'
						AND section_head.sec_head='2'
						AND chief_officer.co='1'
						AND employee.grade!='MANAGER'
						AND employee.grade!='I-CHIEF OFFICER'
						AND chiefofficer.id_co = '$_SESSION[LID]'";
						
						$qry1 = DB_query($sql1);
						
						$num_rows3 = DB_num_rows($qry1);
						if($num_rows3 == 1);	
								
					?>
					
				

				<nav class="navbar navbar-default" role="navigation">
				  <div class="container-fluid">
					<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header">
					  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					  </button>
					  <strong><a class="navbar-brand" href="<?php echo $mainlink; ?>Dashboard">Home</a></strong>
					</div>
					
					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-slide-dropdown">
					<ul class="nav navbar-nav">
	
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown"> Sick Leave <span class="badge"> <?php echo $num_rows2 ;?></span><span class="caret"></span></a>
		<ul class="dropdown-menu" role="menu">
									<li><a href="index.php?Application=HRC&Ref=SickLeave">View Leave</a></li>
									<li><a href="index.php?Application=HRC&Ref=SLAdd">File Sick Leave</a></li>
									<li><a href="index.php?Application=HRC&Ref=SLStatus">Sick Leave Status</a></li>
		</ul>
	</li>
	
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">Vacation Leave <span class="badge"> <?php echo $num_rows1 ;?></span><span class="caret"></span></a>
		<ul class="dropdown-menu" role="menu">
		                            <li><a href="index.php?Application=HRC&Ref=Vacation">View Leave</a></li>
									<li><a href="index.php?Application=HRC&Ref=VLAdd">File Vacation Leave</a></li>
									<li><a href="index.php?Application=HRC&Ref=VacationLeft">Days Left</a></li>
									<li><a href="index.php?Application=HRC&Ref=VacationStatus">Vacation Leave Status</a></li>
		</ul>
	</li>
					
	<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Paternity/Maternity leave <span class="badge"> <?php echo $num_rows3 ;?></span><span class="caret"></span></a>
	<ul class="dropdown-menu" role="menu">
		                            <li><a href="index.php?Application=HRC&Ref=PMLeave">View Leave</a></li>
									<li><a href="index.php?Application=HRC&Ref=MPAdd">File paternity/maternity Leave</a></li>
									<li><a href="index.php?Application=HRC&Ref=MPLeft">Days Left</a></li>
									<li><a href="index.php?Application=HRC&Ref=MPTrack">Paternity/Maternity leave Status</a></li>
		</ul>
	</li>
	
<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Half-Day Permission <span class="badge"></span><span class="caret"></span></a>
	<ul class="dropdown-menu" role="menu">
									<li><a href="index.php?Application=HRC&Ref=HdAdd">File Half-Day Permission</a></li>
									<li><a href="index.php?Application=HRC&Ref=HdTrack">Half-Day Permission Status</a></li>
		</ul>
	</li>
	
<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Civilian Off-Duty <span class="badge"></span><span class="caret"></span></a>
	<ul class="dropdown-menu" role="menu">
									<li><a href="index.php?Application=HRC&Ref=OffAdd"> File Civilian Off-Duty</a></li>
									<li><a href="index.php?Application=HRC&Ref=OffTrack"> Civilian Off-Duty  Status </a></li>
		</ul>
	</li>
		
						

					  </ul>
						
					
					
					</div><!-- /.navbar-collapse -->
				  </div><!-- /.container-fluid -->
				</nav>
	</div>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../bootstrap/js/bootstrap.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../bootstrap/js/jquery.min.js"></script>
  </body>
</html>