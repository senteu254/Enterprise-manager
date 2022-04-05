
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<style>
		.navbar-default{
		opacity: .9;
		z-index: 999;
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
<style type="text/css">

#form-control {
  
  width: 100%;
  height: 34px;
  padding: 6px 12px;
  font-size: 14px;
  line-height: 1.42857143;
  color: #555;
  background-color: #fff;
  background-image: none;
  border: 1px solid #ccc;
  border-radius: 4px;
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
          box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
  -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
       -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
          transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
}
#form-control:focus {
  border-color: #66afe9;
  outline: 0;
  -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
          box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
}
#form-control::-moz-placeholder {
  color: #999;
  opacity: 1;
}
#form-control:-ms-input-placeholder {
  color: #999;
}
#form-control::-webkit-input-placeholder {
  color: #999;
}
#form-control[disabled],
#form-control[readonly],
fieldset[disabled] .form-control {
  cursor: not-allowed;
  background-color: #eee;
  opacity: 1;
}
textarea.form-control {
  height: auto;
}
.label2 {
  display: inline;
  padding: .2em .6em .3em;
  font-size: 75%;
  font-weight: bold;
  line-height: 1;
  color: #fff;
  text-align: center;
  white-space: nowrap;
  vertical-align: baseline;
  border-radius: .25em;
}

</style>

<div class="container-fluid" >
	<!--welcome user in menu-->
	<?php
function pagination($query,$per_page=10,$page=1,$url='?'){   
    global $db; 
    $query = "SELECT COUNT(*) as `num` FROM {$query}";
    $row = mysqli_fetch_array(mysqli_query($db,$query));
    $total = $row['num'];
    $adjacents = "2"; 
      
    $prevlabel = "&lsaquo; Prev";
    $nextlabel = "Next &rsaquo;";
    $lastlabel = "Last &rsaquo;&rsaquo;";
      
    $page = ($page == 0 ? 1 : $page);  
    $start = ($page - 1) * $per_page;                               
      
    $prev = $page - 1;                          
    $next = $page + 1;
      
    $lastpage = ceil($total/$per_page);
      
    $lpm1 = $lastpage - 1; // //last page minus 1
      
    $pagination = "";
    if($lastpage > 1){   
        $pagination .= "<ul class='pagination'>";
        $pagination .= "<li class='page_info'>Page {$page} of {$lastpage}</li>";
              
            if ($page > 1) $pagination.= "<li><a href='{$url}page={$prev}'>{$prevlabel}</a></li>";
              
        if ($lastpage < 7 + ($adjacents * 2)){   
            for ($counter = 1; $counter <= $lastpage; $counter++){
                if ($counter == $page)
                    $pagination.= "<li><a class='current'>{$counter}</a></li>";
                else
                    $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";                    
            }
          
        } elseif($lastpage > 5 + ($adjacents * 2)){
              
            if($page < 1 + ($adjacents * 2)) {
                  
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
                    if ($counter == $page)
                        $pagination.= "<li><a class='current'>{$counter}</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";                    
                }
                $pagination.= "<li class='dot'>...</li>";
                $pagination.= "<li><a href='{$url}page={$lpm1}'>{$lpm1}</a></li>";
                $pagination.= "<li><a href='{$url}page={$lastpage}'>{$lastpage}</a></li>";  
                      
            } elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                  
                $pagination.= "<li><a href='{$url}page=1'>1</a></li>";
                $pagination.= "<li><a href='{$url}page=2'>2</a></li>";
                $pagination.= "<li class='dot'>...</li>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<li><a class='current'>{$counter}</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";                    
                }
                $pagination.= "<li class='dot'>..</li>";
                $pagination.= "<li><a href='{$url}page={$lpm1}'>{$lpm1}</a></li>";
                $pagination.= "<li><a href='{$url}page={$lastpage}'>{$lastpage}</a></li>";      
                  
            } else {
                  
                $pagination.= "<li><a href='{$url}page=1'>1</a></li>";
                $pagination.= "<li><a href='{$url}page=2'>2</a></li>";
                $pagination.= "<li class='dot'>..</li>";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<li><a class='current'>{$counter}</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";                    
                }
            }
        }
          
            if ($page < $counter - 1) {
                $pagination.= "<li><a href='{$url}page={$next}'>{$nextlabel}</a></li>";
                $pagination.= "<li><a href='{$url}page=$lastpage'>{$lastlabel}</a></li>";
            }
          
        $pagination.= "</ul>";        
    }
      
    return $pagination;
}
		
		$path='HR_Head/';
		$view = (isset($_GET['Ref']) && $_GET['Ref'] != '') ? $_GET['Ref'] : '';
								switch ($view) {
								case 'Admin' :
									$content=$path.'admin.php';
									break;
								case 'Band' :
									$content=$path.'Set_Employee_Band.php';
									break;
									
								case 'Ethnicity' :
									$content=$path.'Set_Ethnicity.php';
									break;
							
								case 'Appointment' :
									$content=$path.'Set_Appointment.php';		
									break;
									
								case 'Grade' :
									$content=$path.'Set_Employee_Grade.php';
									break;
									
								case 'AppCat' :
									$content=$path.'Set_AppointmentCategory.php';
									break;
							
								case 'List' :
									$content=$path.'View_Employee.php';	
									break;
									
								case 'AddEmp' :
									$content=$path.'Add_Employee.php';
									break;
									
								case 'EmpReport' :
									$content=$path.'EmployeeReport.php';
									break;
									
								case 'Establishment' :
									$content=$path.'Staff_Establishment.php';
									break;
									
								case 'Profile' :
									$content=$path.'Employee_Profile.php';
									break;
								
								case 'LeaveApp' :
									$content=$path.'Leave_Application_Profile.php';
									break;
									
								case 'LeaveSetting' :
									$content=$path.'Leave_Setting_Profile.php';
									break;
									
								case 'LeaveReports' :
									$content=$path.'Leave_Reports_Profile.php';
									break;

								case 'LeaveReportsPerDept' :
									$content=$path.'Leave_Reports_PerDept.php';
									break;	
									
								case 'LeaveDaysReports' :
									$content=$path.'Leave_Reports_Days.php';
									break;
									
								case 'Disable' :
									$content=$path.'Disable_Employee.php';
									break;
														
								default :
									$content =$path.'Dashboard.php';
									break;
									
							}
 

						//this will get the data in database	
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');	
						$welcome_view = "SELECT * FROM leaves, approve_hr where leaves.hr_approve = approve_hr.hr_approve AND approve_hr.hr_approve = '1'";
						$welcome_viewed = DB_query($welcome_view,$ErrMsg,$DbgMsg);
						$num_rows = DB_num_rows($welcome_viewed);
						if($num_rows > 0);

						//this will get the data in database	
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');	
						$welcome_view = "SELECT * FROM vacation_log, approve_hr where vacation_log.hr_approve = approve_hr.hr_approve AND approve_hr.hr_approve = '1'";
						$welcome_viewed = DB_query($welcome_view,$ErrMsg,$DbgMsg);
						$num_rows2 = DB_num_rows($welcome_viewed);
						if($num_rows2 > 0);
						
						//this will get the data in databas
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');		
						$sql = "SELECT * FROM mp_log, approve_hr where mp_log.hr_approve = approve_hr.hr_approve AND approve_hr.hr_approve = '1'";
						$qry = DB_query($sql,$ErrMsg,$DbgMsg);
						$num_rows3 = DB_num_rows($qry);
						if($num_rows3 > 0);
						
						
						
						//this will get the data in databas
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$sql5 = "SELECT *,employee.emp_id as id FROM employee, hd_log, departments,section, section_head,program_head,approve_hr
									WHERE employee.emp_id = hd_log.emp_id 
									AND employee.id_dept = departments.departmentid
									AND section.id_dept = departments.departmentid
									AND approve_hr.hr_approve= hd_log.hr_approve
									AND section.id_sec = employee.id_sec 
									AND section_head.sec_head = hd_log.section_head
									AND program_head.prog_head = hd_log.prog_head
									AND section_head.sec_head ='2'
									AND program_head.prog_head ='2'
									AND approve_hr.hr_approve='1'
								    AND employee.stat='1' 
								    AND hd_stat='0'";
						$qry5 = DB_query($sql5,$ErrMsg,$DbgMsg);
						$num_rows5 = DB_num_rows($qry5);
						if($num_rows5 > 0);
						
						
						$User = "SELECT emp_id FROM www_users WHERE userid ='".$_SESSION['UserID']."'";
						$Users = DB_query($User,$ErrMsg,$DbgMsg);
						$user = DB_fetch_row($Users);
						
						
						//this will get the data in databas
						$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$sql4 = "SELECT * FROM employee, off_log, departments,section, section_head,approve_hr
									WHERE employee.emp_id = off_log.emp_id 
									AND employee.id_dept = departments.departmentid
									AND approve_hr.hr_approve= off_log.hr_approve
									AND section.id_sec = employee.id_sec 
									AND section.id_dept=departments.departmentid
									AND section_head.sec_head =off_log.section_head
									AND section_head.sec_head ='2'
									AND approve_hr.hr_approve='1'
								    AND employee.stat='1' 
								    AND off_stat='0'";
						$qry4 = DB_query($sql4,$ErrMsg,$DbgMsg);
						$num_rows4 = DB_num_rows($qry4);
						if($num_rows4 > 0);
					?>
					
				<nav class="navbar navbar-default" role="navigation">
				  <div class="container-fluid">
					<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header" >
					  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					  </button>
					  <strong><a class="navbar-brand" href="index.php?Application=HR&Ref=Dashboard">Home</a></strong>
					</div>
					
					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
					<?php if($_SESSION['CanEditHR'] == 1){  ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Structure <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
									<li><a href="index.php?Application=HR&Ref=Band">Add Band</a></li>
									<li><a href="index.php?Application=HR&Ref=AppCat">Add Appointment Category</a></li>
									<li><a href="index.php?Application=HR&Ref=Appointment">Add Appointment</a></li>
									<li><a href="index.php?Application=HR&Ref=Grade">Add Grade</a></li>
									<li><a href="index.php?Application=HR&Ref=Ethnicity">Add Ethnicity</a></li>
								</ul>
						</li>
						<?php } ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"> Employee <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
								<?php if($_SESSION['CanEditHR'] == 1){ ?>
									<li><a href="index.php?Application=HR&Ref=List">View Employee</a></li>
									<li><a href="index.php?Application=HR&Ref=AddEmp">Add Employee</a></li>
									<li><a href="index.php?Application=HR&Ref=EmpReport">Employee Report</a></li>
									<li><a href="index.php?Application=HR&Ref=Establishment">Staff Establishment</a></li>
								<?php }else{
								echo '<li><a href="index.php?Application=HR&Ref=Profile&id='.$user[0].'">View Profile</a></li>';
									}
								 ?>
								</ul>
						</li>
						
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"> Leave Manager <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
									<li><a href="index.php?Application=HR&Ref=LeaveApp">Leave Application</a></li>
									<?php if($_SESSION['CanEditHR'] == 1){ ?>
									<li><a href="index.php?Application=HR&Ref=LeaveSetting">Leave Setting</a></li>
									<li><a href="index.php?Application=HR&Ref=LeaveReports">Leave Reports</a></li>
									<li><a href="index.php?Application=HR&Ref=LeaveReportsPerDept">Leave Reports Per Department</a></li>
									<li><a href="index.php?Application=HR&Ref=LeaveDaysReports">Leave Days Reports</a></li>
									<?php } ?>
								</ul>
						</li>
						
					</ul>
					</div><!-- /.navbar-collapse -->
				  </div><!-- /.container-fluid -->
				</nav>
	</div>
