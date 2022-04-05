<?php
require_once('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$key =  $_GET['VID'];
$key = addslashes($key);
$sql = DB_query("select * FROM visitor_register WHERE  VisitorNo =$key");

    $row = DB_fetch_array($sql);
	$name= $row['v_name'];
	$phone=$row['v_phoneno'];
	$idno=$row['v_idno'];
	$residence=$row['v_from'];

?>
<div class="panel panel-default" style="width:530px;">
		<div class="panel-heading">Update Visitor Information</div>
			<div class="panel-body">
			<form enctype="multipart/form-data" action="index.php?Application=SEC2&Link=VisitorRead&VID=<?php echo $key; ?>" onsubmit="return document.getElementById('loadingbackground').style.display = 'block';" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; 
				  echo '<input type="hidden" name="VIDUP" value="' . $key . '" />'; ?>
			<div class = "row">
      <div class="col-md-12 col-md-offset-">
			<div class="form-group">
							  <div class="col-md-7">Visitor Full Name
							  <input autocomplete="off" pattern="(?!^\s+$)[^<>+]{1,40}" name="name" type="text" value = "<?php echo $name; ?>" placeholder="Visitor Full Name" class="form-control input-md" required=""/>
							  </div>
							   <div class="col-md-5">ID Number
							  	<input  autocomplete="off" name="idno" type="text" value = "<?php echo $idno; ?>" placeholder="ID Number" class="form-control input-md" required=""/>
							  </div>
							   
							</div>
			
			<div class="form-group">
							  <div class="col-md-7">Place of Residence
							   <input  autocomplete="off" pattern="(?!^\s+$)[^<>+]{1,40}" name="from" type="text" value = "<?php echo $residence; ?>" placeholder="Place of Residence" class="form-control input-md" required=""/>
							  </div>
							  <div class="col-md-5">Phone Number
							  	<input  autocomplete="off" name="phoneno" type="text" value = "<?php echo $phone; ?>" placeholder="Phone Number" class="form-control input-md" required=""/>
							  </div>
							</div>
			</div>
		</div>			
					<br />		
					<div class="form-group">
							  <div class="col-md-12">
								<button id="submit" name="UpdateV" class="btn btn-primary">Update</button>
								<div class="pull-right">
								<a href=""><input class="btn btn-warning" name="Cancel" value="Cancel" type="button" /></a>
								</div>
							  </div>
							</div>
						</form>
	
			</div>
		</div>