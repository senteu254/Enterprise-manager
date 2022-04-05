
<div class="container-fluid">
<?php	
	if (isset($_POST['submit'])){
		
		$a = addslashes("$_POST[id_emp]");
		$b = addslashes("$_POST[emp_fname]");
		$c = addslashes("$_POST[emp_lname]");
		$d = addslashes("$_POST[emp_mname]");
		
		$aa = strtotime($_POST['date']);
		$dob = date("Y-m-d",$aa);
		
		$g = addslashes("$_POST[emp_gen]");
		$i = addslashes("$_POST[emp_stat]");
		$j = addslashes("$_POST[acc]");
		$bank = addslashes("$_POST[bank]");
		$branch = addslashes("$_POST[branch]");
		$id = addslashes("$_POST[id]");
		$pin = addslashes("$_POST[pin]");
		$lic = addslashes("$_POST[licence]");
		$nhif = addslashes("$_POST[nhif]");
		$nssf = addslashes("$_POST[nssf]");
		
		
		$dat = strtotime("now");
		$da = date("Y-m-d",$dat);
		
		function ageCalculator($dob){
    if(!empty($dob)){
        $birthdate = new DateTime($dob);
        $today   = new DateTime($da);
        $age = $birthdate->diff($today)->y;
        return $age;
    }else{
        return 0;
    }
}
//$date=FormatDateForSQL(date("y/m/d"));
//$dobtest=FormatDateForSQL($dob);
$select = "SELECT * FROM employee WHERE emp_id='".$a."' OR id_number='".$id."'";
$qry=DB_query($select);
$num=DB_num_rows($qry);
 if($num>0){
 prnMsg(_("Please Verify the information Entered and try Again.Duplicate entry for personal No,ID No or Pin No!"),'warn');
$error=1;
 }
 elseif(ageCalculator($dob)<18 && ageCalculator($dob)>0 || ageCalculator($dob)>65 ||ageCalculator($dob)<1){
  prnMsg(_("Date of Birth is Invalid!Employee is either underage(below 18 yrs), overage(above 65 yrs) or future date is entered!"),'warn');
 }
else{
		
		$ErrMsg = _('The employee details cannot be inserted because');
		$sql = "INSERT INTO employee
	(`emp_id`,`emp_fname`,`emp_lname`,`emp_mname`,`emp_bday`,`emp_gen`,`bank_name`,`branch`,`account_no`,`emp_stat`,`id_number`,`pin`,`stat`,`dlicence_no`,addedby,personnel,nhif,nssf)
						values('$a','$b','$c','$d','$dob','$g','$bank','$branch','$j','$i','$id','$pin','1','$lic','".$_SESSION['UsersRealName']."','".$_POST['personnel']."','$nhif','$nssf')";
		$qry = DB_query($sql,$ErrMsg);
			if ($qry){
			 prnMsg(_("Employee Information added"),'success');
				}
			else {
			prnMsg(_("Not Added Please Try Again"),'error');
			 }
		
	}
	}
?>

<div class="row">
				<div class="col-md-11">
						<h3>Add New Employee</h3>
				</div>
			</div>
<div class="container-fluid">
	<div class = "row">
		<div class="panel panel-default">
			<div class="panel-body">
					<form enctype="multipart/form-data" autocomplete="off" method="post" class="form-horizontal">
					<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			?>
					<fieldset>
						<!-- Text input-->
							<div class="form-group">
							  <div class="col-md-4">Personal Number
							  <input id="id_emp" name="id_emp" type="text" value = "<?php echo $a;?>" placeholder="Employee ID" class="form-control input-md" required=""/>
							  </div>
							  <div class="col-md-4">ID Number
							  <input id="id" name="id" value="<?php echo $id;?>" type="text" placeholder="National id" maxlength="8" size="8" class="form-control input-md" required=""/>
							  </div>
							   <div class="col-md-4">Personnel
								 <select name="personnel" class="form-control input-md">
								 <?php
								 $arr= array('Civilian','Military');
								 foreach($arr as $val){
								 echo '<option value="'.$val.'">'.$val.'</option>';
								 }
								 ?>
								 </select>
							  </div>
							</div>

							<!-- Text input-->
							<div class="form-group" >
							  <div class="col-md-4">First Name
							  <input id="emp_fname" name="emp_fname" value="<?php echo $b?>" type="text" placeholder="First Name" class="form-control input-md" required=""/>
							  </div>
							  <div class="col-md-4">Middle Name
							  <input id="emp_mname" name="emp_mname" value="<?php echo $d;?>" type="text" placeholder="Middle Name" class="form-control input-md" />
							  </div>
							  <div class="col-md-4">Last Name
								<input id="emp_lname" name="emp_lname" value="<?php echo $c?>" type="text" placeholder="Last Name" class="form-control input-md" required=""/>
							  </div>
							</div>

							<!-- Select Basic -->
							<div class="form-group"> 
							  <div class="col-md-4">Date Of Birth
							  <input id="date" name="date" type="text" value="<?php echo $dob;?>" placeholder="D.O.B" class="form-control input-md" required=""/>
							  </div>
							  <div class="col-md-4">Gender
								<select id="emp_gen" name="emp_gen" required="true" class="form-control">
								<?php
								if(isset($g) && $g=="Female"){
								echo '<option selected="selected">Female</option>';
								echo '<option>Male</option>';
								}elseif(isset($g) && $g=="Male"){
								echo '<option selected="selected">Male</option>';
								echo '<option>Male</option>';
								}
								else{
								?>
								  <option value="">Gender</option>
								  <option>Female</option>
								  <option>Male</option>
								    <?php } ?>
								 
								</select>
								
							  </div>
							  <div class="col-md-4">Marital Status
								<select id="emp_stat" name="emp_stat" class="form-control">
								<?php
								if(isset($i) && $i=="Single"){
								echo '<option selected="selected">Single</option>';
								echo '<option>Married</option>';
								echo '<option>Widow</option>';
								echo '<option>Widower</option>';
								}elseif(isset($i) && $i=="Married"){
								echo '<option selected="selected">Married</option>';
								echo '<option>Single</option>';
								echo '<option>Widow</option>';
								echo '<option>Widower</option>';
								}elseif(isset($i) && $i=="Widow"){
								echo '<option selected="selected">Widow</option>';
								echo '<option>Single</option>';
								echo '<option>Married</option>';
								echo '<option>Widower</option>';
								}elseif(isset($i) && $i=="Widower"){
								echo '<option selected="selected">Widower</option>';
								echo '<option>Single</option>';
								echo '<option>Married</option>';
								echo '<option>Widow</option>';
								}
								else{
								?>
								  <option value="">Marital Status</option>
								  <option value="Single">Single</option>
								  <option value="Married">Married</option>
								  <option value="Widow">Widow</option>
								  <option value="Widower">Widower</option>
								  <?php } ?>
								</select>
							  </div>
							</div>
							<div class="form-group">
							  <div class="col-md-4">Name of Bank
							  <input id="bank" name="bank" value="<?php echo $bank;?>" type="text" placeholder="Bank Name" class="form-control input-md" />
								
							  </div>
					
							  <div class="col-md-4">Branch
							  <input id="branch" name="branch" value="<?php echo $branch;?>" type="text" placeholder="Branch" class="form-control input-md" />
								
							  </div>
							
							  <div class="col-md-4">Account
							  <input id="acc" name="acc" value="<?php echo $j;?>" type="text" placeholder="Account No" class="form-control input-md" />
								
							  </div>
							</div>							
							
							<div class="form-group">
							  <div class="col-md-3">P.I.N
							  <input id="pin" name="pin" value="<?php echo $pin;?>" maxlength="11" size="11" type="text" placeholder="P.I.N" class="form-control input-md" />
								
							  </div>
							  <div class="col-md-3">License No
								 <input id="licence" value="<?php echo $lic;?>" name="licence" type="text" placeholder="Driving licence number" class="form-control input-md"  />
							  </div>
							  <div class="col-md-3">NHIF No
								 <input id="nhif" value="" name="nhif" type="text" placeholder="NHIF Number" class="form-control input-md"  />
							  </div>
							  <div class="col-md-3">NSSF No
								 <input id="nssf" value="" name="nssf" type="text" placeholder="NSSF Number" class="form-control input-md"  />
							  </div>
							</div>
							
								<!-- Button (Double) -->
							<div class="form-group">
							  <label class="col-md- control-label" for="submit"></label>
							  <div class="col-md-8">
								<button id="submit" name="submit" class="btn btn-primary">Update</button>
							  </div>
							</div>

						</fieldset>
					</form>
					
		</div>
			</div>
				</div>
					</div>
<link rel="stylesheet" type="text/css" href="HR_Head/css/datepickr.css" />
			
	 <link rel="stylesheet" href="js/smoothness/jquery-ui.css" />
    
    <!-- Load jQuery JS -->
    <script src="js/jquery-1.9.1.js"></script>
    <!-- Load jQuery UI Main JS  -->
    <script src="js/jquery-ui.js"></script>
