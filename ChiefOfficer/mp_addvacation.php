o<!DOCTYPE html>
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
	if (isset($_POST['submit'])){
	
	$res=DB_query("SELECT emp_id FROM www_users WHERE userid='".$_SESSION['UserID']."'");
		$row=DB_fetch_array($res);
		$_GET['id']=$row['emp_id'];
		
	
		if (($_POST['sdate'] == '')or($_POST['eddate'] == ''))
		{
			echo "You must fill those fields";
		}	
	else{ //dri namn is ang mga "name=stu_id" nga ara sa mga input type. 
		
		$b = addslashes("$_POST[leavetype]");
		$aa = strtotime($_POST['sdate']);
		$a = date("Y-m-d",$aa);
		$gg = strtotime($_POST['eddate']);
		$g = date("Y-m-d",$gg);
		$dat = strtotime("now");
		$da = date("Y-m-d",$dat);
		
		
		$output = ($gg-$aa) / 86400;
		
			$sql="select SUM(credits) AS cred From mp_credits where emp_id = '$_GET[id]'";
			$qry1 = DB_query($sql);
			$rec = DB_fetch_array($qry1);
			$value1 = $rec ['cred'];
			
			$sql="select SUM(nodays) AS nod1 From mp_log where emp_id = '$_GET[id]'";
			$qry2 = DB_query($sql);
			$rec =DB_fetch_array($qry2);
			$value2 = $rec ['nod1'];
			
			$sql="select * From employee where emp_id = '$_GET[id]'";
			$qry3 = DB_query($sql);
			$rec = DB_fetch_array($qry3);
			$grade = $rec ['grade'];
			$marital_status = $rec ['emp_stat'];
			$id_pos = $rec ['id_pos'];
			
			$value3 = $value1 - $value2;
			$value4 = $value3 - $output;
			
			if($value4 > 0 && $g > $a &&$a > $da && $g > $da && $id_pos='1' ){
			if($value4 > 0){
				if($value4 > 0 && $grade=='MANAGER' && $marital_status=='Married' ){
					$insert = "INSERT INTO mp_log
									(`mp_id`,`emp_id`,`date`,`leavetype`,`sdate`,`eddate`,`nodays`,`general_manager`,`managing_director`,`hr_approve`)
										values('','$_GET[id]','$da','$b','$a','$g','$output','1','1','1')";
					$qry =DB_query($insert);
					}
					else if($value4 > 0 && $grade=='I-CHIEF OFFICER' && $marital_status=='Married' ){ 
					$insert = "INSERT INTO mp_log
									(`mp_id`,`emp_id`,`date`,`leavetype`,`sdate`,`eddate`,`nodays`,`prog_head`,`general_manager`,`hr_approve`)
										values('','$_GET[id]','$da','$b','$a','$g','$output','1','1','1')";
					$qry = DB_query($insert);
					}
					else{
					$insert = "INSERT INTO mp_log
									(`mp_id`,`emp_id`,`date`,`leavetype`,`sdate`,`eddate`,`nodays`,`section_head`,`chief_officer`,`prog_head`,`hr_approve`)
										values('','$_GET[id]','$da','$b','$a','$g','$output','1','1','1','1')";
					$qry = DB_query($insert);
					}
					if ($qry){
						prnMsg( _('Paternity/Maternity Leave added'), 'success');
						}
					else
						prnMsg( _('Not Posted!'), 'error');
				}
				}
			if($value4 < 0){
				prnMsg( _('You have only').' '.$value3.' '.('day(s) left'), 'error');
				}
				if($marital_status !=='Married'){
				prnMsg( _('Not allowed! Not Married'), 'warn');
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
<div class="col-md-8 col-md-offset-2">
<div class="panel panel-default">
  <div class="panel-heading" align="center">Paternity/Maternity Leave Form</div>
	<div class="panel-body">
			<br/>
	<form method="POST" class="form-horizontal">
	<?php
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	?>
		<div style = "margin-left:-150px">
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-6 control-label" for="edate">Leave Type</label>  
			  <div class="col-md-3">
			  <input id="leavetype" name="leavetype" type="type" value="P/M Leave" class="form-control input-md" readonly>
				
			  </div>
			</div>
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-6 control-label" for="sdate">Effective Date</label>  
			  <div class="col-md-3">
			  <input id="sdate" name="sdate" type="date" placeholder="Date Start" class="form-control input-md" required="">
				
			  </div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-6 control-label" for="eddate">Due Date</label>  
			  <div class="col-md-3">
			  <input id="eddate" name="eddate" type="date" placeholder="Date End" class="form-control input-md" required="">
				
			  </div>
			</div>
						

			<!-- Button -->
			<div class="form-group">
			  <label class="col-md-6 control-label" for="submit"></label>
			  <div class="col-md-6">
				<button id="submit" name="submit" class="btn btn-primary">Submit Leave</button>
							<input type="button" value="Back" name="cancel" 
							onclick="history.back()" class="btn btn-default"/>
			  </div>
			</div>
		</div>

	</form>
		</div>
		</div>
		</div>
		</div>