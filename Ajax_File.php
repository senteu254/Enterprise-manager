<?php
require_once('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

if($_GET['Request']=='Appointment'){
function App(){
$sql="SELECT appointment_name FROM appointment Where category='".$_GET['id']."'";
$result=DB_query($sql);
$optionsapp="";
while ($row=DB_fetch_array($result)) {
    $appointment_name=$row["appointment_name"];
    $optionsapp.="<OPTION VALUE=\"$appointment_name\">".$appointment_name;
}
return $optionsapp;
}
?>
							  <div class="col-md-4">Appointment
						
						<select id='appointment' name='appointment' class='form-control'>
									<OPTION VALUE="">Choose</OPTION>
									 <?=App();?> 
							</select>
							  </div>
<?php
}
if($_GET['Request']=='Grade'){
function Grade(){
$sql="SELECT grade FROM grade Where band_id='".$_GET['id']."'";
$result=DB_query($sql);
$optionsgrade="";
while ($row=DB_fetch_array($result)){
    $grade=$row["grade"];
    $grade=$row["grade"];
    $optionsgrade.="<OPTION VALUE=\"$grade\">".$grade;
	
}
return $optionsgrade;
}
?>
							
							  <div class="col-md-4">Grade

								<select id='grade' name='grade' class='form-control'>
									 <OPTION VALUE=0>Choose</OPTION>
									   <?php echo Grade();?>
							</select>
							  </div>
<?php
}
?>