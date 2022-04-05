<style type="text/css">
.box{
 background-color:white;
    width:330px;
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
$key = addslashes($key);
$sql = DB_query("select * from employee WHERE  CONCAT_WS(' ', emp_fname, emp_lname) LIKE '%$key%'");

    while($row = DB_fetch_array($sql)) {
	$count++;
	$fname= $row['emp_fname'];
	$lname=$row['emp_lname'];
	$idno=$row['emp_id'];

	if($count<= 10){
?>
<div id="return">
<a href="#" style="font-size:11px;" onClick="document.getElementById('key').value='<?php echo $fname.' '.$lname; ?>'; document.getElementById('result').style.display='none';"><?php echo $fname.' '.$lname; ?></a><br />
</div>		
<?php }}
if($count==""){
echo "no match found";
}else{
 ?> 
 
 <?php } ?>
 		
   </div>	
   </span>