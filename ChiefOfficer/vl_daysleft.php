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
			$res=DB_query("SELECT emp_id FROM www_users WHERE userid='".$_SESSION['UserID']."'");
		$row=DB_fetch_array($res);
		$_GET['id']=$row['emp_id'];
		//INITIALIZE DATE RANGE Start
				$nw = strtotime("now");
				$tdy = date("m-d",$nw);
				
				$mjune = strtotime("june 1");
				$djune = date("m-d",$mjune);
				
				$mdec = strtotime("December 31");
				$ddec = date("m-d",$mdec);
				
				$mjan = strtotime("January 1");
				$djan = date("m-d",$mjan);
				
				$mmay = strtotime("May 31");
				$dmay = date("m-d",$mmay);
				//INITIALIZE DATE RANGE End
			
				//START FUNCTION
					if(($djune <= $tdy)&&($ddec >= $tdy)):
					$cyear = strtotime("June 1");
					$cyr = date("m-d",$cyear);
					
					$cyrs = strtotime("now");
					$cyrr = date("Y",$cyrs);
					
					$cyyy = strtotime("May 31");
					$cyy = date("m-d", $cyyy);
					
					$csss = strtotime("nextyear");
					$csx = date("Y",$csss);
					
			$sql1="select *,SUM(credits) AS nod From vacation_credits
					where  date >= '$cyrr-$cyr' 
						AND date <= '$csx-$cyy' AND vacation_credits.emp_id = '$_GET[id]'";
			$qry1 = DB_query($sql1);
			$rec = DB_fetch_array($qry1);
			$cred = $rec['nod'];
			
			
			$sql="select grade From employee where emp_id = '$_GET[id]'";
			$qry3 = DB_query($sql);
			$rec = DB_fetch_array($qry3);
			$grade = $rec ['grade'];
			if( $grade=='MANAGER'){

			$sql="select *,SUM(nodays) AS nod1,employee.emp_id as id, emp_fname, emp_lname, leavetype, nodays, sdate, eddate  From vacation_log, employee,managing_director
					where managing_director.md = vacation_log.managing_director
						  AND managing_director.md = '2'
						  AND vacation_log.emp_id = employee.emp_id 
							 AND vacation_log.emp_id = '$_GET[id]'";
							
			$qry2 = DB_query($sql);
			$recc = DB_fetch_array($qry2);
			}
			else if( $grade=='I-CHIEF OFFICER'){ 
			
			$sql="select *,SUM(nodays) AS nod1,employee.emp_id as id, emp_fname, emp_lname, leavetype, nodays, sdate, eddate  From vacation_log, employee,general_manager
					where general_manager.gm = vacation_log.general_manager
						  AND general_manager.gm = '2'
						  AND vacation_log.emp_id = employee.emp_id 
							 AND vacation_log.emp_id = '$_GET[id]'";
							
			$qry2 = DB_query($sql);
			$recc = DB_fetch_array($qry2);
			}
			else{
			
			$sql="select *,SUM(nodays) AS nod1,employee.emp_id as id, emp_fname, emp_lname, leavetype, nodays, sdate, eddate  From vacation_log, employee,program_head
					where program_head.prog_head = vacation_log.prog_head
						  AND program_head.prog_head = '2'
						  AND vacation_log.emp_id = employee.emp_id 
							 AND vacation_log.emp_id = '$_GET[id]'";
							
			$qry2 = DB_query($sql);
			$recc = DB_fetch_array($qry2);
			}
			
			
			$value2 = $recc['nod1'];
			$value3 = $cred - $value2;
			
					elseif(($djan <= $tdy)&&($dmay >= $tdy)):
					
					$cyear = strtotime("June 1");
					$cyr = date("m-d",$cyear);
					
					$cyrs = strtotime("lastyear");
					$cyrr = date("Y",$cyrs);
					
					$cmayr = strtotime("May 31");
					$cmr = date("m-d",$cmayr);
					
					$cyrx = strtotime("now");
					$cyx = date("Y",$cyrx);
					
					
			$sql1="select *,SUM(credits) AS nod From vacation_credits
					where  date >= '$cyrr-$cyr' 
							AND date <= '$cyx-$cmr' AND vacation_credits.emp_id = '$_GET[id]'";
			$qry1 = DB_query($sql1);
			$rec = DB_fetch_array($qry1);
			$cred = $rec['nod'];
			
			
			$sql="select grade From employee where emp_id = '$_GET[id]'";
			$qry3 = DB_query($sql);
			$rec =DB_fetch_array($qry3);
			$grade = $rec ['grade'];
			if( $grade=='MANAGER'){

			$sql="select *,SUM(nodays) AS nod1,employee.emp_id as id, emp_fname, emp_lname, leavetype, nodays, sdate, eddate  From vacation_log, employee,managing_director
					where managing_director.md = vacation_log.managing_director
						  AND managing_director.md = '2'
						  AND vacation_log.emp_id = employee.emp_id 
							 AND vacation_log.emp_id = '$_GET[id]'";
							
			$qry2 = DB_query($sql);
			$recc =DB_fetch_array($qry2);
			}
			else if( $grade=='I-CHIEF OFFICER'){ 
			
			$sql="select *,SUM(nodays) AS nod1,employee.emp_id as id, emp_fname, emp_lname, leavetype, nodays, sdate, eddate  From vacation_log, employee,general_manager
					where general_manager.gm = vacation_log.general_manager
						  AND general_manager.gm = '2'
						  AND vacation_log.emp_id = employee.emp_id 
							 AND vacation_log.emp_id = '$_GET[id]'";
							
			$qry2 = DB_query($sql);
			$recc = DB_fetch_array($qry2);
			}
			else{
			
			$sql="select *,SUM(nodays) AS nod1,employee.emp_id as id, emp_fname, emp_lname, leavetype, nodays, sdate, eddate  From vacation_log, employee,program_head
					where program_head.prog_head = vacation_log.prog_head
						  AND program_head.prog_head = '2'
						  AND vacation_log.emp_id = employee.emp_id 
							 AND vacation_log.emp_id = '$_GET[id]'";
							
			$qry2 = DB_query($sql);
			$recc = DB_fetch_array($qry2);
			}
			
			
			$value2 = $recc['nod1'];
			$value3 = $cred - $value2;
			
			
					
			
			endif;
		?>
				

		<div class="col-md-6 col-md-offset-3">
			<div class = "row">
					<div class="col-md-12">
						<div class="page-header">
								<h1 align="center">Vacation Leave</h1>
						</div>
					</div>
				</div>
			<br/>
						<div class="panel panel-success">
							<div class="panel-heading">
								<h3 class="panel-title" align="center">Information</h3>
							</div>
							<div class="panel-body">
									Personal Number: &nbsp <strong><?php echo $recc['id'];?></strong><hr/>
									Full Name: &nbsp <strong><?php echo $recc['emp_fname'];?> <style="margin-left: 20px;"/><?php echo $recc['emp_lname'];?></strong><hr/>
									Days Left: &nbsp <strong><?php echo "$value3";?></strong><hr/>
							</div>
						</div>
					<div class = "col-md-4">
							<input type="button" value="Back" name="cancel" 
							onclick="history.back()" class="btn btn-default"/>
					</div>
		</div>
	</div>