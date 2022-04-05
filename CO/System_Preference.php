<?php
	ob_start();
	//$id=DB_real_escape_string($_GET['id']);
	
	$strSQL1 = "SELECT * FROM company_preference";
	$objQuery1 = DB_query($strSQL1);
	$Num_Rows1 = DB_num_rows($objQuery1);
	while($row=DB_fetch_array($objQuery1)){
	$id=$row['Id'];
	$name=$row['Name'];
	$slogan=$row['Slogan'];
	$add=$row['Address'];
	$log=$row['Logo'];
	}
	//insert records
	if(isset($_POST['Submit'])){
	session_start();
	 function getExtension($str) 
	{
		$i = strrpos($str,".");
		if (!$i) { return ""; }
		$l = strlen($str) - $i;
		$ext = substr($str,$i+1,$l);
		return $ext;
	}
		
		$filename = stripslashes($_FILES['uploadedfile']['name']);
		$extension = getExtension($filename);
			$extension = strtolower($extension);

		if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) 
		{	
		die ("<div class='msg msg-error'><p>Unknown Attachment Extension ( ".$extension." )! Please Upload Only png, jpg, jpeg, gif Files.</p></div>");
		}
		else{
			$name = time().".".$extension;
			$type = $_FILES['uploadedfile'] ["type"];
			$size = $_FILES['uploadedfile'] ["size"];
			$temp = $_FILES['uploadedfile'] ["tmp_name"];
			$error = $_FILES['uploadedfile'] ["error"];
			
			
			if ($error > 0){
			switch ($error) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE (100Kb) directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        } 
				die("<div class='msg msg-error'><p>Error uploading file! Code $error. $message.</p></div>");
			}
			else{
				if($size > 100000) //conditions for the file
				{
				die ("<div class='msg msg-error'><p>Format is not allowed or file size is too big!</p></div>");
				}
				else
				{
				$logo = "css/images/".$name;
				unlink($log);
				move_uploaded_file($temp,$logo);
				  }
 				 }
	$nname = $_POST['name'];
	$slo = $_POST['slogan'];
	$addr = $_POST['add'];
	$insert = "UPDATE company_preference SET Name='$nname',Slogan='$slo',Address='$addr',Logo='$logo' WHERE Id=$id";
	
	mysqli_query($insert) or die('Could not run Query: ' . mysqli_error());
	$_SESSION['msg']='
				<p><strong>User was Updated succesifully!</strong></p>
				<a href="#" class="close">close</a>';
				header("Location:".$mainlink."System");
	
	}
	}
	//End insert records

?>
				
				<div class="box-headd">
				<div id="navigation">
			<ul>
			    <!--<li><a <?php //if(!empty($class8)){echo $class8;}?> href="<?php //echo $mainlink; ?>System" ><span>System</span></a></li>-->
				<li><a <?php if(!empty($class1)){echo $class1;}?>href="<?php echo $mainlink; ?>Type"><span>Contract Type</span></a></li>
			    <li><a <?php if(!empty($class9)){echo $class9;}?>href="<?php echo $mainlink; ?>Commitee_Roles"><span>Commitee roles</span></a></li>
			</ul>
		</div>
		<br>
		</div>
				<div class="box" style="color: black; border-radius: 7px;">
					<!-- Box Head -->
					<div class="box-head" style="background:#337ab7; color: white; border-radius: 7px; no-repeat 0 0; padding:0 0 0 15px;">
						<h2 class="left">Edit User Login Details</h2>
						
					</div>
					 <form enctype="multipart/form-data" action="" method="POST">
						
						<!-- Form -->
						<div class="form">
						<p>
									<span class="req">max 100 symbols</span>
									<label>Company Name <span>(Required Field)</span></label>
									<input type="text" class="field size1" required="true" value="<?php echo $name; ?>" name="name" />
						</p>
						  <p>
									
							  <label></label>
								<label>Company Slogan <span>(Required Field)</span></label>
							  <input type="text" class="field size1" required="true" value="<?php echo $slogan; ?>" name="slogan" />
							</p>
								<p>
									
									<label>Address <span>(Required Field)</span></label>
									<input type="text" class="field size1" id="add" required="true" value="<?php echo $add; ?>" name="add" />
								</p>
								<p>
									
									<label>Company Logo <span>(Required Field)</span></label>
									<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
      <input name="uploadedfile" class="field size4" value="<?php echo $log; ?>" type="file" /><?php if($log ==""){echo "<img src='css/images/nologo.jpg' width='50px' height='50px' alt=''>";}else{ echo "<img src='".$log."' width='50px' height='50px' alt=''>";}?>::Logo Preview::
									
								</p>

									
						</div>
						<!-- End Form -->
						
						<!-- Form Buttons -->
						<div class="buttons">
							<input type="submit" on name="Submit" value="Submit" />
						</div>
						<!-- End Form Buttons -->
					</form>
					
				</div>






