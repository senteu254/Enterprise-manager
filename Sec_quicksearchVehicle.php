<style type="text/css">
.box{
 background-color:white;
    width:300px;
    /*border-collapse: collapse;*/
    border:thin outset #B3B3B3;
	z-index: 99;
    
    -webkit-border-radius:  4px;
    -moz-border-radius:     4px;
    border-radius:          4px;
    -moz-box-shadow:    1px 1px 2px #C3C3C3;
	-webkit-box-shadow: 1px 1px 2px #C3C3C3;
	box-shadow:         1px 1px 2px #C3C3C3;
	-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')";
	filter: progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3'
}
</style>
<span align="left">
<div class="box">
	
<?php
require_once('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$count= 0;
$key =  $_GET['key'];
$Type =  $_GET['Type'];
$key = addslashes($key);
if($Type == "Vehicle"){
$sql = DB_query("select * from vehicle_kofc_register WHERE  RegNo LIKE '%$key%'");

    while($row = DB_fetch_array($sql)) {
	$count++;
	$regno= $row['RegNo'];
	$make=$row['Make'];

	if($count<= 10){
?>
<div id="return">
<a href="#" style="font-size:14px;" onClick="document.getElementById('regnos').value='<?php echo $regno; ?>'; document.getElementById('resultvehicle').style.display='none';"><?php echo $regno.' ('.$make.')'; ?></a><br />
</div>		
<?php }}
if($count==""){
echo "No match found";
}else{ } 
}
if($Type == "Visitor"){
$sql = DB_query("select * from visitor_register WHERE  v_idno LIKE '%$key%'");

    while($row = DB_fetch_array($sql)) {
	$count++;
	$regno= $row['v_idno'];
	$make=ucwords(strtoupper($row['v_name']));

	if($count<= 10){
?>
<div id="return">
<a href="#" style="font-size:14px;" onClick="document.getElementById('idnumbers').value='<?php echo $regno; ?>'; document.getElementById('resultvisitor').style.display='none';"><?php echo $regno.' - '.$make.''; ?></a><br />
</div>		
<?php }}
if($count==""){
echo "No match found";
}else{ } 
}

?>
 		
   </div>	
   </span>