<?php
require_once('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$i = 1;
echo '<input name="SelectedCustomer" type="hidden" value="" />';
function SearchItem(){
$_POST['Keywords'] = mb_strtoupper($_GET['Keywords']);
if(!isset($_POST['Keywords']) or $_POST['Keywords'] ==""){
$optionsapp="No items to display";
}else{
$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';

$SQL = "SELECT  * FROM suppliers
				WHERE suppname " . LIKE . " '" . $SearchString . "'
				ORDER BY supplierid LIMIT 10 ";

	$ErrMsg = _('The searched supplier records requested cannot be retrieved because');
	$result = DB_query($SQL,$ErrMsg);
	$NUMS = DB_num_rows($result);
$optionsapp="";
$optionsapp.='<ul>';
if($NUMS >0){
while ($row=DB_fetch_array($result)) {
    $name =$row["suppname"];
    $stockid=$row["supplierid"];
    $optionsapp.= "<a href=# onclick=supplier('".$stockid."');><li>".$stockid." - ".$name."</li></a>";
	//$optionsapp.= '<input type="text" name="part_' . $i . '" value="'.$stockid.'" data-type="no-illegal-chars" size="21" maxlength="20" />';
}
}else{
$optionsapp.='<span style="color:red">The supplier you entered does not exist please verify and try again!</span>';
}
$optionsapp.='</ul></div>';
}
return $optionsapp;
}
echo '<div class="divs">';
echo SearchItem();
echo '</div>';
?>
<style type="text/css">
.divs {
background:#FFFFFF;
position: absolute;
z-index: 99;
min-width:300px;
}
ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
}
 
li {
  border-bottom: 1px solid #ccc;
  text-align:left;
}
 
li:last-child {
  border: none;
}
 
li a {
  text-decoration: none;
  color: #000;
  display: block;
 
  -webkit-transition: font-size 0.3s ease, background-color 0.3s ease;
  -moz-transition: font-size 0.3s ease, background-color 0.3s ease;
  -o-transition: font-size 0.3s ease, background-color 0.3s ease;
  -ms-transition: font-size 0.3s ease, background-color 0.3s ease;
  transition: font-size 0.3s ease, background-color 0.3s ease;
}
 
li a:hover {
  background: #f6f6f6;
}


</style>