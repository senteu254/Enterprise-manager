<?php
require_once('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');


function Prop(){
$sql="SELECT * FROM stockcategory,stockmaster  Where stockmaster.categoryid=stockcategory.categoryid AND stockcategory.categoryid='".$_GET['prop']."'";
$result=DB_query($sql);
$optionstype="";
while ($row=DB_fetch_array($result)){
    $type=$row["longdescription"];
    $type=$row["longdescription"];
    $optionstype.="<OPTION VALUE=\"$type\">".$type;
	
}
return $optionstype;
}



?>
								 <div class="col-md-4">Property Type
								 <select id='type' name='type'  class='form-control' >
							<OPTION VALUE="">Choose</OPTION>
									   <?php echo Prop();?>
							</select>
							  </div>