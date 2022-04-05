<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee * List</title>
    <!-- Bootstrap -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
	</head>
	<body>
						<?php
$sql="select SUM(credits) AS nod From mp_credits 
					where mp_credits.emp_id = '$_GET[id]'";
			$qry2 = DB_query($sql);
			$rec = DB_fetch_array($qry2);
			$cred = $rec['nod'];



			$sql="select SUM(nodays) AS nod1, employee.emp_id as id, emp_fname, emp_lname, date, leavetype, nodays, sdate, eddate  From mp_log, employee
					where mp_log.emp_id = employee.emp_id AND mp_log.emp_id = '$_GET[id]'";
			$qry2 = DB_query($sql);
			$recc = DB_fetch_array($qry2);
			
			$value2 = $recc ['nod1'];
			
			$value3 = $cred - $value2;
?>
			<div class="container-fluid">
			<div class="col-md-8 col-md-offset-2">
<div class="panel panel-default">
  <div class="panel-heading" align="center">Paternity/Maternity Leave</div>
	<div class="panel-body">
					<div class="col-md-12">
						<div class="col-md-8 col-md-offset-4">
										Personal Number: &nbsp <strong><?php echo $recc['id'];?></strong><hr/>
										Date: &nbsp <strong><?php echo $recc['date']; ?></strong><hr/>
										Full Name: &nbsp <strong><?php echo $recc['emp_fname'];?> <style="margin-left: 20px;"/><?php echo $recc['emp_lname'];?></strong><hr/>
										Leave Type: &nbsp <strong><?php echo $recc['leavetype'];?></strong><hr/>
										Effective Date: &nbsp <strong><?php echo $recc['sdate'];?></strong> &nbsp to &nbsp <strong><?php echo $recc['eddate'];?></strong><hr/>
										Total Days Leave: &nbsp <strong><?php echo $recc['nod1'];?></strong><hr/>
						</div>
						<div class="col-md-8 col-md-offset-4">
								<input type = "button" value="Back" class="btn btn-default" onClick="window.history.back()"/></a>
						</div>
					</div>
		</div>
		</div>
		</div>
		</div>