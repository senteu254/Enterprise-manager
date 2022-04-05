<link rel="stylesheet" href="FA2/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="FA2/iCheck/flat/blue.css">
<?php
//$description_Id =  $_GET['description_Id'];
if (isset($_POST['Submit'])) {	
	$description_Id_save = $_POST['newcode'];
	$description_save = $_POST['newDescription'];
	$units_save = $_POST['units'];
	$cost_save = $_POST['newcost'];

	$sql32 = "UPDATE farmdescriptions SET description ='$description_save',
	                                units ='$units_save',
									cost ='$cost_save'
								   WHERE description_Id = '" .$_GET['description_Id']. "'"; 
	//$sql = "UPDATE payment_voucher SET chequeNo='" .$cheque. "',tax='" .$tax. "' WHERE voucherid = '". $pv . "'";
		//$result = DB_query($sql32);
	//header("Location: index.php?Application=FA2&Ref=default&Link=View_Farm_Description");			
}
echo '<table class="selection table table-hover">
<tr>
</td>';
//echo'<a href= "' . $RootPath . '/Farm_DescriptionView.php">' . _('Back to production Items') . '</a>';
//$description_code=$_GET['description_Id'];

$SQL = "SELECT * FROM farmdescriptions WHERE description_Id  = '" .$_GET['description_Id']. "'"; 
$SearchResult = DB_query($SQL, $ErrMsg, $DbgMsg);
while (($myrow = DB_fetch_array($SearchResult))) {
$description=$myrow['description'];
$description_Id=$myrow['description_Id'];
$cost=$myrow['cost'];

}

//$id =$_REQUEST['description_Id'];

 echo'<table align="center" style="width:35%" class="selection table table-hover">';
 //echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=FA2&Ref=default&Link=Edit_Farm_Description_Item" method="post">';
 echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=FA2&Ref=default&Link=Edit_Farm_Description_Item" id="form">';
	echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';
//echo'<input type="hidden" size="10" maxlength="60" value="'.$StockID.'" name="stockid" />';
	echo'<tr>
	<td>Item Code</td>
	<td><input type="text" size="25"  disabled="disabled" maxlength="60" value="'.$description_Id.'" name="newcode" /></td>
	</tr>';
  echo' <tr><td style="font-size:10pt">Descriptions</td><td><input type="text"  size="40" maxlength="70" name="newDescription" value="'.$description.'"  /><td><td><center></center></td></tr>';	
      
 echo' <tr>
 <td>' . _('Units') . ':</td>
		<td><select ' . (in_array('Description',$Errors) ?  'class="selecterror"' : '' ) .'  name="Units">';

$sql = "SELECT unitname FROM unitsofmeasure ORDER by unitname";
$UOMResult = DB_query($sql);

if (!isset($_POST['Units'])) {
	$UOMrow['unitname']=_('each');
}
while( $UOMrow = DB_fetch_array($UOMResult) ) {
	 if (isset($_POST['Units']) AND $_POST['Units']==$UOMrow['unitname']){
		echo '<option selected="selected" value="' . $UOMrow['unitname'] . '">' . $UOMrow['unitname'] . '</option>';
	 } else {
		echo '<option value="' . $UOMrow['unitname'] . '">' . $UOMrow['unitname']  . '</option>';
	 }
}

	echo '</select></td>
	</tr>';
	echo' <tr><td style="font-size:10pt">Cost</td><td><input type="text" size="25" maxlength="60"name="newcost" value="'.$cost.'"  /><td></tr>'; 
	echo'<tr>
	<td>&nbsp;</td><td>
	<input type="submit" name="Submit" value="' . _('Update Descriptions') . '" />
	</tr>
	</tr>
	</tr>';
	echo'</form>';
	
	echo'</table>';
 ?>