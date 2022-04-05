<?php
					$select = "SELECT * FROM employee WHERE employee.emp_id='".$_GET['id']."'";
					
					           $qry=DB_query($select);
					           $rec = DB_fetch_array($qry);
								$idemp = "$rec[emp_id]";
								$fname = "$rec[emp_fname]";
								$lname = "$rec[emp_lname]";
								$mname = "$rec[emp_mname]";
								$bday = "$rec[emp_bday]";
								$age = "$rec[emp_age]";
								$gender = "$rec[emp_gen]";
								$address = "$rec[emp_add]";
								$cont = "$rec[emp_cont]";
								$idno= "$rec[id_number]";
								$pin= "$rec[pin]";
								$lic= "$rec[dlicence_no]";
								$email = "$rec[email]";
								$status = "$rec[emp_stat]";
								$pos1 = "$rec[pos_stat]";
								$pos = "$rec[emp_pos]";
								$statpos = "$rec[pos_stat]";
								$depart = "$rec[id_dept]";
								$added = "$rec[addedby]";
								$date = "$rec[emp_date]";
								$band = "$rec[band]";
								$appointment = "$rec[appointment_name]";
								$grade= "$rec[grade]";
								$id_pos= "$rec[id_pos]";
								$section= "$rec[id_sec]";
								$bank= "$rec[bank_name]";
								$branch= "$rec[branch]";
								$acc= "$rec[account_no]";
								$nhif= "$rec[nhif]";
								$nssf= "$rec[nssf]";
								$personnel= "$rec[personnel]";
								$docapp = "$rec[datecurrentapp]";
								$ethnicity = "$rec[ethnicity]";
								$pwd = "$rec[pwd]";
								$disability ="$rec[disability]";
								$category ="$rec[appointment_category]";
								
								$filename = 'HR_Head/prof_pics/'.$idemp.'.jpg';
								if (file_exists($filename)) {
								$image = $filename;
								}else{
								$image = 'HR_Head/prof_pics/Profile.png';
								}
	
				
				$path='HR_Head/';
				$view = (isset($_GET['Link']) && $_GET['Link'] != '') ? $_GET['Link'] : '';
								switch ($view) {
								case 'Contact' :
									$content=$path.'Prof_Contact_Details.php';
									$head='Contact Information';
									break;
								
								case 'Qualifications' :
									$content=$path.'Prof_Qualifications.php';
									$head='Academic Qualifications (Starting with the Highest)';
									break;
							
								case 'Kin' :
									$content=$path.'Prof_NextofKin.php';		
									$head='Next-of-Kin Information';
									break;
									
								case 'Dependent' :
									$content=$path.'Prof_Dependent.php';
									$head='Dependent Information';
									break;
							
								case 'Insuarance' :
									$content=$path.'Prof_Insuarance.php';	
									$head='Insurance Information';
									break;
								
								case 'Employment' :
									$content=$path.'Prof_Employment_Status.php';	
									$head='Employment Status';
									break;
								case 'Occurences' :
									$content=$path.'Prof_Work_Occurences.php';	
									$head='Work Occurences';
									break;
								case 'Property' :
									$content=$path.'Prof_Company_Property.php';	
									$head='Company Property';
									break;
								case 'Photo' :
									$content=$path.'Prof_Photo.php';	
									$head='Profile Photo';
									break;
									default :
									$content =$path.'Prof_Personal_Details.php';
									$head='Personal Information';
									
							}
				?>	
<div class="container-fluid">
<div class = "row">
<div class="col-md-3 col-md-offset-">
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo $fname?> <?php echo $lname?> <?php echo $mname;?></div>
			<div class="panel-body">
			<center>
			<a href="index.php?Application=HR&Ref=Profile&Link=Photo&id=<?php echo $_GET['id']; ?>" title="Profile Photo"><img height="200" width="200" src="<?php echo $image; ?>" alt="Profile Image" /></a>
			<br><br />
			
			<ul class="nav nav-pills nav-stacked">
			<li role="presentation" <?php echo ($view=='' ? 'class="active"':'') ?> ><a href="index.php?Application=HR&Ref=Profile&id=<?php echo $_GET['id']; ?>">Personal Details</a></li>
			<li role="presentation" <?php echo ($view=='Qualifications' ? 'class="active"':'') ?>><a href="index.php?Application=HR&Ref=Profile&Link=Qualifications&id=<?php echo $_GET['id']; ?>">Academic Qualifications</a></li>
			<li role="presentation" <?php echo ($view=='Contact' ? 'class="active"':'') ?>><a href="index.php?Application=HR&Ref=Profile&Link=Contact&id=<?php echo $_GET['id']; ?>">Contact Information</a></li>
			<li role="presentation" <?php echo ($view=='Dependent' ? 'class="active"':'') ?>><a href="index.php?Application=HR&Ref=Profile&Link=Dependent&id=<?php echo $_GET['id']; ?>">Dependants</a></li>
			<li role="presentation" <?php echo ($view=='Kin' ? 'class="active"':'') ?>><a href="index.php?Application=HR&Ref=Profile&Link=Kin&id=<?php echo $_GET['id']; ?>">Next-of-Kin Information</a></li>
			<li role="presentation" <?php echo ($view=='Insuarance' ? 'class="active"':'') ?>><a href="index.php?Application=HR&Ref=Profile&Link=Insuarance&id=<?php echo $_GET['id']; ?>">Insurance Information</a></li>
			<li role="presentation" <?php echo ($view=='Employment' ? 'class="active"':'') ?>><a href="index.php?Application=HR&Ref=Profile&Link=Employment&id=<?php echo $_GET['id']; ?>">Employment Status</a></li>
			<li role="presentation" <?php echo ($view=='Occurences' ? 'class="active"':'') ?>><a href="index.php?Application=HR&Ref=Profile&Link=Occurences&id=<?php echo $_GET['id']; ?>">Work Occurences</a></li>	
			<li role="presentation" <?php echo ($view=='Property' ? 'class="active"':'') ?>><a href="index.php?Application=HR&Ref=Profile&Link=Property&id=<?php echo $_GET['id']; ?>">Company Property</a></li>				
			</ul>
			</center>
			</div>
		</div>
	</div>

	<div class="col-md-9 col-md-offset-">
	<div class="col-md-12 col-md-offset-">
	<div class="panel panel-primary">
		<div class="panel-heading"><?php echo $head; ?></div>
			<div class="panel-body">
				
			<!--/*----------------------------------------------------------------------------*/-->
			
			<?php include $content; ?>

			<!-------------------------------------------------------------------------------------->

			</div>
		</div>
	</div>
</div>
</div>

