<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HR</title>
    <!-- Bootstrap -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
	</head>
	<body>
		<div class="container-fluid">
<?php
	if (isset($_POST['submit'])){
		//dri mah vl.an kung mai onud ang txtbox then ang mga "category or stu_id is  name sa entity sa database
		if (($_POST['stime'] == '')or($_POST['etime'] == ''))
		{
			echo "You must fill those fields";
		}	
	else{ //dri namn is ang mga "name=stu_id" nga ara sa mga input type. 
		
		$b = addslashes("$_POST[leavetype]");
		$x = $_POST['stime'];
		$y = $_POST['etime'];
		$a=str_pad($x,2,'0',STR_PAD_LEFT);
		$g=str_pad($y,2,'0',STR_PAD_LEFT);
		$dat = strtotime("now");
		$da = date("Y-m-d",$dat);
		
		$dat = strtotime("now");
		$d = date("h:i",$dat);
	
			$res=DB_query("SELECT emp_id FROM www_users WHERE userid='".$_SESSION['UserID']."'");
		$row=DB_fetch_array($res);
		$_GET['id']=$row['emp_id'];
			
	if( $g > $a && $a > $d && $g > $d ){
		
					$insert = "INSERT INTO hd_log
									(`hd_id`,`emp_id`,`date`,`leavetype`,`stime`,`etime`,`section_head`,`prog_head`,`hr_approve`,`hrm_approve`,`hd_stat`)
										values('','$_GET[id]','$da','$b','$a','$g','1','1','1','1','0')";
					$qry = DB_query($insert);
					if ($qry){
					prnMsg( _('Half Day Permission added'), 'success');
						}
					else
					prnMsg( _('Not Posted!'), 'error');
				
		}
		if($g < $a){
				prnMsg( _('End time cannot be Less than Start time'), 'warn');
				}
				if($g < $d || $a < $d){
				prnMsg( _('End date or Start date cannot be Less than system current time'), 'warn');
				}
				
	}
}
	
?>
<div class="col-md-8 col-md-offset-2">
<div class="panel panel-default">
  <div class="panel-heading" align="center">Half Day Permission Form</div>
	<div class="panel-body">
				
			<br/>
	<form method="POST" class="form-horizontal">
	<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			?>
		<div style = "margin-left:-130px">
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-6 control-label" for="edate">Leave Type</label>  
			  <div class="col-md-3">
			  <input id="leavetype" name="leavetype" type="type" value="Hd Permission" class="form-control input-md" readonly>
				
			  </div>
			</div>
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-6 control-label" for="sdate">Effective Time</label>  
			  <div class="col-md-3">
			  
				<?php
	 echo '<select id="stime" name="stime" class="form-control input-md" required="">';
	 for($hours=0; $hours<24; $hours++) // the interval for hours is '1'
    for($mins=0; $mins<60; $mins+=15) // the interval for mins is '30'
        echo '<option>'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
                       .str_pad($mins,2,'0',STR_PAD_LEFT).'</option>';
	echo '</select>';
					   ?>
			  </div>
			</div>
			
			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-6 control-label" for="eddate">Due Time</label>  
			  <div class="col-md-3">
			 
				<?php
	 echo '<select id="etime" name="etime"  class="form-control input-md" required="">';
	 for($hours=0; $hours<24; $hours++) // the interval for hours is '1'
    for($mins=0; $mins<60; $mins+=15) // the interval for mins is '30'
        echo '<option>'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
                       .str_pad($mins,2,'0',STR_PAD_LEFT).'</option>';
	echo '</select>';
					   ?>
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
