<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HR</title>
     <link rel="stylesheet" href="js/smoothness/jquery-ui.css" />
    
    <!-- Load jQuery JS -->
    <script src="js/jquery-1.9.1.js"></script>
    <!-- Load jQuery UI Main JS  -->
    <script src="js/jquery-ui.js"></script>
    
    <!-- Load SCRIPT.JS which will create datepicker for input field  -->
    <script src="Employee/script.js"></script>
    
    <link rel="stylesheet" href="Employee/runnable.css" />
	</head>
	<body>
		<div class="container-fluid">
						
						<?php
			$res=DB_query("SELECT emp_id FROM www_users WHERE userid='".$_SESSION['UserID']."'");
		$row=DB_fetch_array($res);
		$_GET['id']=$row['emp_id'];
		
			$sql="select * From employee where emp_id = '$_GET[id]'";
			$qry3 = DB_query($sql);
			$rec = DB_fetch_array($qry3);
			$grade = $rec ['grade'];
		if (isset($_POST['submit'])){
			
		//dri mah vl.an kung mai onud ang txtbox then ang mga "category or stu_id is  name sa entity sa database
		if (($_POST['edate'] == '')or($_POST['endate'] == ''))
		{
			echo "You must fill those fields";
		}	
	else{ //dri namn is ang mga "name=stu_id" nga ara sa mga input type. 
		
		$b = addslashes("$_POST[leavetype]");
		$aa = strtotime($_POST['edate']);
		$a = date("Y-m-d",$aa);
		$gg = strtotime($_POST['endate']);
		$g = date("Y-m-d",$gg);
		$dat = strtotime("now");
		$da = date("Y-m-d",$dat);
		
		$output = ($gg-$aa) / 86400;
		
	if( $g > $a && $a > $da && $g > $da ){
	if( $grade=='MANAGER'){
	$insert = "INSERT INTO leaves
						(`leaveid`,`emp_id`,`date`,`leavetype`,`edate`,`endate`,`no_days`,`general_manager`,`managing_director`,`hr_approve`)
										values('','$_GET[id]','$da','$b','$a','$g','$output','1','1','1')";
					$qry = DB_query($insert);

		     }else if( $grade=='I-CHIEF OFFICER'){
					$insert = "INSERT INTO leaves
									(`leaveid`,`emp_id`,`date`,`leavetype`,`edate`,`endate`,`no_days`,`prog_head`,`general_manager`,`hr_approve`)
										values('','$_GET[id]','$da','$b','$a','$g','$output','1','1','1')";
					$qry = DB_query($insert);
					
					}else {

		$insert = "INSERT INTO leaves
									(`leaveid`,`emp_id`,`date`,`leavetype`,`edate`,`endate`,`no_days`,`section_head`,`chief_officer`,`prog_head`,`hr_approve`)
										values('','$_GET[id]','$da','$b','$a','$g','$output','1','1','1','1')";
					$qry = DB_query($insert);
					
					}
					if ($qry){
					prnMsg( _('Sick Leave added'), 'success');
						}else 
						prnMsg( _('Not Posted!'), 'error');
							 }
							 if($g < $a){
				prnMsg( _('End date cannot be Less than Start date'), 'warn');
				}
				if($g < $da || $a < $da){
				prnMsg( _('End date or Start date cannot be Less than system current time'), 'warn');
					}
					}
			}


?>
<div class="col-md-6 col-md-offset-3">
<div class="panel panel-default">
  <div class="panel-heading" align="center"> Sick Leave Form</div>
	<div class="panel-body">
			<br/>
	<form method="POST" class="form-horizontal">
	<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			?>
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="edate">Sick leave</label>  
			  <div class="col-md-4">
			 <input id="leavetype" name="leavetype" type="type" value="Sick Leave" class="form-control input-md" readonly> 
				
			  </div>
			</div>
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="edate">Effective Date</label>  
			  <div class="col-md-4">
			  <input id="edate" name="edate" type="date" placeholder="Date Start" class="form-control input-md" required="">
				
			  </div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="endate">Due Date</label>  
			  <div class="col-md-4">
			  <input id="endate" name="endate" type="date" placeholder="Date End" class="form-control input-md" required="">
				
			  </div>
			</div>
						

			<!-- Button -->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="submit"></label>
			  <div class="col-md-8">
				<button id="submit" name="submit" class="btn btn-primary">Submit Leave</button>
							<input type="button" value="Back" name="cancel" 
							onclick="history.back()" class="btn btn-default"/>

			  </div>
			 
			</div>
	</form>
	</div>
</div>
</div>
</div>
