<?php
require_once('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');


function GradeDes(){
$sql="SELECT * FROM grade,appointment Where grade.band_id=appointment.band_id AND appointment.id='".$_GET['des']."'";
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
									   <?php echo GradeDes();?>
							</select>
							  </div>