<?php
include('includes/DefineCartClass.php');
require_once('includes/session.inc');
include('includes/GetPrice.inc');
include('includes/SQL_CommonFunctions.inc');
include('includes/GetSalesTransGLCodes.inc');
$i = 1;

$DefaultDeliveryDate = DateAdd(Date($_SESSION['DefaultDateFormat']),'d',$_SESSION['Items'.$identifier]->DeliveryDays);
echo '<input type="hidden" class="date" name="ItemDue_' . $i . '" value="' . $DefaultDeliveryDate . '" />
		<input type="hidden" class="number" name="qty_' . $i . '" size="6" value="1" maxlength="6" />
		<input type="hidden" name="part_' . $i . '" value="" data-type="no-illegal-chars" size="21" maxlength="20" />
		<input type="hidden" name="QuickEntry" value="' . _('Quick Entry') . '" />
		<input type="hidden" name="ItemSelection" value="Yes" />';
function SearchItem(){
$_POST['Keywords'] = mb_strtoupper($_GET['Keywords']);
if(!isset($_POST['Keywords']) or $_POST['Keywords'] ==""){
$optionsapp="No items to display";
}else{
$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';
$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.barcode,
						stockmaster.decimalplaces,
						stockmaster.controlled
					FROM stockmaster INNER JOIN stockcategory
					ON stockmaster.categoryid=stockcategory.categoryid
					WHERE (stockcategory.stocktype='F' OR stockcategory.stocktype='D' OR stockcategory.stocktype='L')
					AND stockmaster.mbflag <>'G'
					
					AND stockmaster.description " . LIKE . " '" . $SearchString . "'
					AND stockmaster.discontinued=0
					ORDER BY stockmaster.stockid LIMIT 10"; //AND stockmaster.controlled <> 1
$result=DB_query($SQL);
$optionsapp="";
$optionsapp.='<ul>';
while ($row=DB_fetch_array($result)) {
    $name=$row["description"];
    $stockid=$row["stockid"];
	$barcode=$row["barcode"];
    $optionsapp.= "<a href=# onclick=edit('".$stockid."');><li>".$stockid." - ".$name."</li></a>";
	//$optionsapp.= '<input type="text" name="part_' . $i . '" value="'.$stockid.'" data-type="no-illegal-chars" size="21" maxlength="20" />';
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
min-width:376px;
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