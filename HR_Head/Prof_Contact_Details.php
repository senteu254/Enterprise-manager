<?php
if (isset($_POST['submit']))
	{
		$a = addslashes("$_POST[emp_cont]");
		$c = addslashes("$_POST[emp_add]");	
		if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		prnMsg( _('Property Details Successfully Deleted!'), 'success');
		echo '<div style="position:absolute; z-index: 999; left:150px; top:200px; width: 350px">
								<div class="alert alert-danger">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">
										×</button>
								   <span class="glyphicon glyphicon-remove"></span> <strong>Error!</strong>
									<hr class="message-inner-separator">
									This ('.$_POST['email'].') email address is considered invalid.<br />
									<a href="index.php?Application=HR&Ref=Profile&Link=Contact&id='.$_GET['id'].'"><button type="button" class="btn btn-danger">Continue</button></a>
									</p>
								</div>
							</div>';
		}else{	
		$b = addslashes("$_POST[email]");
		$qry =DB_query("UPDATE employee SET
					emp_cont = '$a',
					email = '$b',
					emp_add = '$c'
					WHERE employee.emp_id='".$_GET[id]."' LIMIT 1");
			if ($qry){
				prnMsg( _('Contact Information Successfully Updated'), 'success');
			}
			else{
			prnMsg( _('Not Updated!'), 'error');
				}
		}
	}
	?>

<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
?>
<fieldset>
<div class="form-group">
<label class="col-md-2 control-label" for="emp_cont">Contact Number</label>  
<div class="col-md-4">
<input id="emp_cont" name="emp_cont" type="text" value="<?php echo $cont;?>" placeholder="Contact Number" class="form-control input-md" required=""/>
 </div>
</div>
<div class="form-group">
<label class="col-md-2 control-label" for="email">Email Address</label>  
<div class="col-md-4">
<input id="email" name="email" type="text" value="<?php echo $email;?>" placeholder="Email Address" class="form-control input-md" required=""/>
</div>
</div>
<div class="form-group">
<label class="col-md-2 control-label" for="emp_add"> Permanent Home Address</label>
 <div class="col-md-7">                     
<textarea class="form-control" id="emp_add" name="emp_add" placeholder="Address" required=""><?php echo $address;?></textarea>
</div>
</div>

	<!-- Button (Double) -->
							<div class="form-group">
							  <label class="col-md-2 control-label" for="submit"></label>
							  <div class="col-md-8">
								<div id="btn"></div>
							  </div>
							</div>

						</fieldset>
					</form>
<script type="text/javascript" src="js/jquery-1.9.1.js"></script>						
<script type="text/javascript">
$().ready(function() {
 $('input').attr({
                    'disabled': 'disabled'
                });
  $('textarea').attr({
                    'disabled': 'disabled'
                });
 $('#btn').html('<button id="clicker" class="btn btn-primary">Edit</button>');
    $('#clicker').click(function() {
        $('input').each(function() {
            if ($(this).attr('disabled')) {
                $(this).removeAttr('disabled');
				 $('#btn').html('<button id="submit" name="submit" class="btn btn-primary">Update</button>');
            }
            
        });
		$('textarea').each(function() {
            if ($(this).attr('disabled')) {
                $(this).removeAttr('disabled');
            }
         });
    });
});
</script>