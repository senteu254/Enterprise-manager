    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<div class="container-fluid">
			
<?php
if (isset($_POST['submit'])){
$date = date('Y-m-d',strtotime($_POST['date']));
$reason = addslashes($_POST['reason']);
$qry =DB_query("UPDATE employee SET exitdate = '".$date."', reasonforexit = '".$reason."', stat=2
					WHERE employee.emp_id='".$_POST['emp_id']."'");
$qry =DB_query("UPDATE www_users SET blocked=1
					WHERE emp_id='".$_POST['emp_id']."'");
$_SESSION['msg'] = _('Employee Has been Disabled Successfully');
$redirect = htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=HR&Ref=List';
echo "<script type=\"text/javascript\">
				window.location.href = '".$redirect."';
            </script>
        ";
}
$welcome= "SELECT emp_id,emp_fname,emp_lname FROM employee WHERE emp_id='".$_GET['id']."'";
	$welcome_viewed = DB_query($welcome);
	$num = DB_fetch_row($welcome_viewed);
?>
<div class="col-md-6 col-md-offset-3">
	<div class="panel panel-default">
		<div class="panel-heading"><strong><?php echo '<h4>You are about to Disable '.$num[1].' '.$num[2].'</h4>'; ?></strong></div>
		<div class="panel-body">
	<form method="POST" class="form-horizontal">
			
			<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
				<input type="hidden" name="emp_id" value="' . $_GET['id'] . '" />';
			?>
			
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-3 control-label" for="band_id">Date of Exit:</label>  
			  <div class="col-md-9">
			  <input id="date" name="date" type="text" value="<?php echo date('d/m/Y'); ?>" class="form-control input-md" autocomplete="off" required=""> 
			  			  </div>
			</div>
			<div class="form-group">
			  <label class="col-md-3 control-label" for="band_id">Reason For Exit:</label>  
			  <div class="col-md-9">
			  <textarea name="reason" cols="" style="width:100%;" rows="3" ></textarea>
			  			  </div>
			</div>	

			<!-- Button -->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="submit"></label>
			  <div class="col-md-8">
				<button id="submit" name="submit" class="btn btn-primary">Disable Employee</button>
				<a href="index.php?Application=HR&Ref=List"><input type="button" value="Cancel" class="btn btn-default"></a>
			  </div>
			</div>
	</form>

		</div>
	</div>
			
</div>
		
<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
