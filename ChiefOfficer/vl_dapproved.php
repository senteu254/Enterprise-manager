<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CHIEF OFFICER</title>
	<link rel="shortcut icon" href="../hrlogo.png">
    <!-- Bootstrap -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
<?php
error_reporting(0);
session_start();
DB_query("UPDATE vacation_log SET chief_officer='2' where v_id='$_GET[id]'");
echo'<div class="container-fluid">
		<div class="row">
			<div class="col-md-4 col-md-offset-4">
				<div class="alert alert-success">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">
							×</button>
						<span class="glyphicon glyphicon-ok"></span> <strong>Done!</strong>
						<hr class="message-inner-separator">
					<p><strong>Success!</strong> Vacation Leave Approved.</p><br>
					<div class="col-md-offset-8">
						<a href="'.$mainlink.'VAppv"><button type="button" class="btn btn-success">Continue</button></a>
					</div>
				</div>
			</div>
		</div>
	</div>';
?>