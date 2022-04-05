<?php
require_once('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
function Sec(){
$sql="SELECT * FROM section Where id_dept='".$_GET['de']."'";
$result=DB_query($sql);
$optionsec="";
while ($row=DB_fetch_array($result)) {
    $section_name=$row["section_name"];
    $id_sec=$row["id_sec"];
    $optionsec.="<OPTION VALUE=\"$id_sec\">".$section_name;
}
return $optionsec;
}

?>

						<select id='id_sec' name='id_sec' class='form-control'>
									<OPTION VALUE=0>Choose<?php echo Sec();?></OPTION>
									   
							</select>
							  