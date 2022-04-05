<?php
//set random name for the image, used time() for uniqueness
if(isset($_POST['Submit']) && $_FILES['photo']!=""){
$filename =  $_GET['id'].'.jpg';
$filepath = 'HR_Head/prof_pics/';
if (file_exists($filepath.$filename)) {
unlink($filepath.$filename);
}
move_uploaded_file($_FILES['photo']['tmp_name'], $filepath.$filename);

}
?>

<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
?>
<fieldset>
<div class="form-group">
<label class="col-md-2 control-label" for="emp_cont">Profile Photo</label>  
<div class="col-md-4">
<input name="photo" class="form-control input-md" type="file" />
 </div>
</div>

	<!-- Button (Double) -->
							<div class="form-group">
							  <label class="col-md-2 control-label" for="submit"></label>
							  <div class="col-md-8">
							  <input name="Submit" type="submit" value="Upload Photo" class="btn btn-primary"/>
							  </div>
							</div>

						</fieldset>
					</form>
