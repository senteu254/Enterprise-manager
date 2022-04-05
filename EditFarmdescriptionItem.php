<?php
/* $Id: StockCategories.php 7054 2015-01-01 11:36:36Z exsonqu $*/

include('includes/session.inc');

$Title = _('Production Item');
$ViewTopic= 'Edit Production Item';
$BookMark = 'Farm Production Item';
include('includes/header.inc');

echo '</table></td>';
/********************************************************************************************************************************/
echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/inventory.png" title="' . _('Edit Farm Production Item') . '" alt="" />' . ' ' . _('Edit Farm Production Item') . '</p>';
echo'<a href= "' . $RootPath . '/Farm_DescriptionView.php">' . _('Back to production Items') . '</a>';
echo'</br>';
echo'</br>';


$SQL = "SELECT * FROM farmdescriptions WHERE description_Id  = '$id'"; 
$myrow = DB_query($SQL);

$id =$_REQUEST['description_Id'];
if(isset($_POST['save']))
{	
	$description_Id_save = $_POST['description_Id'];
	$description_save = $_POST['Description'];
	$units_save = $_POST['units'];
	$cost_save = $_POST['cost'];

	$sql = "UPDATE farmdescriptions SET Description ='$description_save',
		 units ='$units_save',cost ='$cost_save' WHERE description_Id = '$id'"; 
	echo "Update!";
	
	header("Location: index.php");			
}


echo'<td>' .$id. '</td>';

 echo'<table align="center" style="width:35%">';
 echo '<form action="" method="post" name="myform" enctype="multipart/form-data" target="_self">';
	echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';
//echo'<input type="hidden" size="10" maxlength="60" value="'.$StockID.'" name="stockid" />';
	
  echo' <tr><td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Descriptions</td><td><input type="text" size="10" maxlength="60" value="'.$description.'" name="Description" /><td><td><center></center></td></tr>';	
      
 echo' <tr><td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Units</td><td><input type="text" size="25" placeholder="Cost of the Service/item" maxlength="60" name="cost" /><td><td><center></center></td></tr>';
 ////////////////////////////////////////////////////////////////////////////////

echo' <tr><td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;cost</td><td><input type="text" size="25" placeholder="Cost of the Service/item" maxlength="60" name="cost" /><td><td><center></center></td></tr>'; 
  ////////////////////////////////////////////////////////////////////////////////
	echo'<tr>
	<td>&nbsp;</td><td><input type="submit" name="Save" value="Save" /></td></tr>
	</tr>';
 echo'</form>';
 

 echo'</table>';
 include ('includes/footer.inc');
 ?>