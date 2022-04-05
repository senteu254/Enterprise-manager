<?php
require_once('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
if(isset($_GET['IMGPT']) && $_GET['IMGPT']!=""){
unlink('SECv2/'.$_GET['IMGPT']);
}
//set random name for the image, used time() for uniqueness

$filename =  time() . '.jpg';
$filepath = 'visit_prfl_ic/';

move_uploaded_file($_FILES['webcam']['tmp_name'], 'SECv2/'.$filepath.$filename);

$sql = DB_query("UPDATE visitor_register SET imagepath='".$filepath.$filename."' WHERE VisitorNo=".$_GET['VID']);

echo $filepath.$filename;
?>
