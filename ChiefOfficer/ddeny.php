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
<?php
session_start();
DB_query("UPDATE leaves SET chief_officer='3' where leaveid='$_GET[id]'");
echo'<div class="container-fluid">
		<div class="row">
			<div class="col-md-4 col-md-offset-4">
				<div class="alert alert-danger">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">
						×</button>
					<span class="glyphicon glyphicon-remove"></span> <strong>Done!</strong>
					<hr class="message-inner-separator">
					<p><strong> Sick Leave Denied!</strong></p>
					<br>
					<div class="col-md-offset-8">
						<a href="'.$mainlink.'SickLeaveDeny"><button type="button" class="btn btn-danger">Continue</button></a>
					</div>
				</p>
				</div>
			</div>
		</div>
	</div>';
?>