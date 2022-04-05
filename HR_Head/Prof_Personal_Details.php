<?php
if (isset($_POST['submit']))
	{
		
		$fname = addslashes("$_POST[emp_fname]");
		$lname = addslashes("$_POST[emp_lname]");
		$mname = addslashes("$_POST[emp_mname]");
		//$e = addslashes("$_POST[date]");
		$aa = strtotime($_POST['date']);
		$dob = date("Y-m-d",$aa);
		$gender = addslashes("$_POST[emp_gen]");
		$i = addslashes("$_POST[emp_stat]");
		$j = addslashes("$_POST[acc]");
		$bank = addslashes("$_POST[bank]");
		$branch = addslashes("$_POST[branch]");
		$idno = addslashes("$_POST[id]");
		$pin = addslashes("$_POST[pin]");
		$lic = addslashes("$_POST[licence]");
		$nhif = addslashes("$_POST[nhif]");
		$nssf = addslashes("$_POST[nssf]");
		$personnel = addslashes("$_POST[personnel]");
		$disability = $_POST['pwddescribe'];
		$pwd = $_POST['pwd'];
		$ethnicity = $_POST['ethnicity'];
		
		
	
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

if(ageCalculator($dob)<18 && ageCalculator($dob)>0 || ageCalculator($dob)>65 ||ageCalculator($dob)<1){
  echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
               Date of Birth is Invalid!Employee is either underage(below 18 yrs), overage(above 65 yrs) or future date is entered!
              </div>';
 }
else{
		$qry =DB_query("UPDATE employee SET
					emp_fname = '$fname',
					emp_lname = '$lname',
					emp_mname = '$mname',
					emp_bday = '$dob',
					emp_gen= '$gender',
					emp_stat = '$i',
					id_number ='$idno',
					bank_name = '$bank',
					account_no = '$j',
					pin = '$pin',
					dlicence_no = '$lic',
					personnel = '$personnel',
					nhif = '$nhif',
					nssf = '$nssf',
					ethnicity='$ethnicity',
					pwd='$pwd',
					disability='$disability'
					WHERE employee.emp_id='".$_GET[id]."'");
			if ($qry){
				echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Personal Details Successfully Updated
              </div>';
			}
			else{
			echo '<div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
               Personal Details Not Updated!
              </div>';
				}
	}
}		

	?>

<form enctype="multipart/form-data" autocomplete="off" method="post" class="form-horizontal">
<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			?>
					<fieldset>
						<!-- Text input-->
							<div class="form-group">
							  <div class="col-md-4">Personal Number
							  <button disabled="true" class="form-control input-md"><div align="left"><?php echo $idemp;?></div></button>
							  </div>
							  <div class="col-md-4">ID Number
							  <input id="id" name="id" value="<?php echo $idno;?>" type="text" maxlength="8" placeholder="National id" class="form-control input-md" required=""/>
							  </div>
							  <div class="col-md-4">Personnel
								 <select name="personnel" class="form-control input-md">
								 <?php
								 $arr= array('Civilian','Military');
								 foreach($arr as $val){
								 echo '<option '.($val==$personnel ? 'selected':'').' value="'.$val.'">'.$val.'</option>';
								 }
								 ?>
								 </select>
							  </div>
							</div>

							<!-- Text input-->
							<div class="form-group" >
							  <div class="col-md-4">First Name
							  <input id="emp_fname" name="emp_fname" value="<?php echo $fname?>" type="text" placeholder="First Name" class="form-control input-md" required=""/>
							  </div>
							  <div class="col-md-4">Middle Name
							  <input id="emp_mname" name="emp_mname" value="<?php echo $mname;?>" type="text" placeholder="Middle Name" class="form-control input-md" />
							  </div>
							  <div class="col-md-4">Last Name
								<input id="emp_lname" name="emp_lname" value="<?php echo $lname?>" type="text" placeholder="Last Name" class="form-control input-md" required=""/>
							  </div>
							</div>

							<!-- Select Basic -->
							<div class="form-group"> 
							  <div class="col-md-4">Date Of Birth
							  <input id="date" name="date" type="text" value="<?php echo $bday;?>" placeholder="D.O.B" class="form-control input-md"/>
							  </div>
							  <div class="col-md-4">Gender
								<select id="emp_gen" name="emp_gen" required="true" class="form-control">
								<?php if(isset($gender) && $gender=="Male"){
								echo '<option selected="selected">Male</option>';
								echo '<option>Female</option>';
								}elseif(isset($gender) && $gender=="Female"){
								echo '<option selected="selected">Female</option>';
								echo '<option>Male</option>';
								}else{
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
								if(isset($status) && $status=="Single"){
								echo '<option selected="selected">Single</option>';
								echo '<option>Married</option>
								  <option>Widow</option>
								  <option>Widower</option>';
								}elseif(isset($status) && $status=="Married"){
								echo '<option selected="selected">Married</option>';
								echo '<option>Single</option>
								  <option>Widow</option>
								  <option>Widower</option>';
								}elseif(isset($status) && $status=="Widow"){
								echo '<option selected="selected">Widow</option>';
								echo '<option>Single</option>
								  <option>Married</option>
								  <option>Widower</option>';
								}elseif(isset($status) && $status=="Widower"){
								echo '<option selected="selected">Widower</option>';
								echo '<option>Single</option>
								  <option>Married</option>
								  <option>Widow</option>';
								}else{
								?>
								  <option selected="selected" value="">Marital Status</option>
								  <option>Single</option>
								  <option>Married</option>
								  <option>Widow</option>
								  <option>Widower</option>
								  <?php } ?>
								</select>
							  </div>
							</div>
							
							<div class="form-group">
							  <div class="col-md-4">Ethnicity
							  <select id="ethnicity" name="ethnicity" class="form-control">
							  <option value="">--Please Select Ethnicity--</option>
							  <?php
							  $res = DB_query("SELECT * FROM ethnicity");
							  while($row = DB_fetch_array($res)){
							  echo '<option '.($ethnicity ==$row['id']? 'selected':'').' value="'.$row['id'].'">'.$row['description'].'</option>';
							  }
							  ?>
							 </select>
							  </div>
					
							  <div class="col-md-4">Person With Disability
							 <select id="pwd" name="pwd" class="form-control">
							 <option <?php echo ($pwd ==0? 'selected':''); ?> value="0">No</option>
						     <option <?php echo ($pwd ==1? 'selected':''); ?>  value="1">Yes</option>
							 </select>
								
							  </div>
							
							  <div class="col-md-4">Describe Disability
							  <input id="acc" name="pwddescribe" value="<?php echo $disability;?>" type="text" placeholder="Describe Disability" class="form-control input-md" />
								
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
							  <input id="acc" name="acc" value="<?php echo $acc;?>" type="text" placeholder="Account No" class="form-control input-md" />
								
							  </div>
							</div>							
							
							<div class="form-group">
							  <div class="col-md-3">P.I.N
							  <input id="pin" name="pin" value="<?php echo $pin;?>" type="text"  maxlength="11"placeholder="P.I.N" class="form-control input-md" />
								
							  </div>
							  <div class="col-md-3">License No
								 <input id="licence" value="<?php echo $lic;?>" name="licence" type="text" placeholder="Driving licence number" class="form-control input-md"  />
							  </div>
							  <div class="col-md-3">NHIF No
								 <input id="nhif" name="nhif" value="<?php echo $nhif;?>" type="text" placeholder="NHIF Number" class="form-control input-md"  />
							  </div>
							  <div class="col-md-3">NSSF No
								 <input id="nssf" name="nssf" value="<?php echo $nssf;?>" type="text" placeholder="NSSF Number" class="form-control input-md"  />
							  </div>
							</div>
							
								<!-- Button (Double) -->
							<div class="form-group">
							  <label class="col-md- control-label" for="submit"></label>
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
  $('select').attr({
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
		$('select').each(function() {
            if ($(this).attr('disabled')) {
                $(this).removeAttr('disabled');
            }
         });
    });
});
</script>
 <link rel="stylesheet" href="js/smoothness/jquery-ui.css" />
    
    <!-- Load jQuery JS -->
    <script src="js/jquery-1.9.1.js"></script>
    <!-- Load jQuery UI Main JS  -->
    <script src="js/jquery-ui.js"></script>
    
    <!-- Load SCRIPT.JS which will create datepicker for input field  -->
    <script src="HR_Head/script.js"></script>
    
    <link rel="stylesheet" href="HR_Head/runnable.css" />