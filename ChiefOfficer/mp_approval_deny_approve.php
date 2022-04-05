<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CHIEF OFFICER</title>
    <!-- Bootstrap -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
	</head>
	<body>
		<div class="container-fluid">
			<div class="col-md-8 col-md-offset-2">
<div class="panel panel-default">
  <div class="panel-heading" align="center">Paternity/Maternity Leave</div>
	<div class="panel-body">
				<?php
					$select = "SELECT *,employee.emp_id as id  FROM mp_log, employee
									WHERE mp_log.emp_id = employee.emp_id AND mp_log.mp_id='$_GET[id]'";
					$qry=DB_query($select);
				?>
				<?php
					while($rec =DB_fetch_array($qry))
					{
				?>
				
					<div class="col-md-12" style="margin-top:40px">
						<div class="col-md-8 col-md-offset-4">
										Personal Number: &nbsp <strong><?php echo $rec['id'];?></strong><hr/>
										Date: &nbsp <strong><?php echo $rec['date']; ?></strong><hr/>
										Full Name: &nbsp <strong><?php echo $rec['emp_fname'];?> <style="margin-left: 20px;"/><?php echo $rec['emp_lname'];?></strong><hr/>
										Leave Type: &nbsp <strong><?php echo $rec['leavetype'];?></strong><hr/>
										Effective Date: &nbsp <strong><?php echo $rec['sdate'];?></strong> &nbsp to &nbsp <strong><?php echo $rec['eddate'];?></strong><hr/>
										Total Days Leave: &nbsp <strong><?php echo $rec['nodays'];?></strong><hr/>
						</div>
						<div class="col-md-8 col-md-offset-4">
								<div class="col-md-10">
									<a href="<?php echo $mainlink; ?>PMDenyViewApp&id=<?php echo $rec['mp_id'];?>"><input type='button' value='Approve' class='btn btn-info'/></a>
								<input type = "button" value="Back" class="btn btn-default" onClick="window.history.back()"/></a>
						</div>
					</div>
				<?php
					}
				?>
		</div>
		</div>
		</div>
		</div>