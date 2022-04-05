<?php
date_default_timezone_set('Africa/Nairobi');
$Title = _('Edit Procurement Plan');
require_once('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
include('includes/header.inc');
$ViewTopic = 'Procurement';
$BookMark = 'CreatePlan';
if (isset($_POST['SubmitRequest'])) {
$sql="SELECT planid FROM irq_procurementplan
							WHERE departmentid='" . $_POST['dept'] . "' AND year='".$_POST['year']."'";
	$r=DB_query($sql, '',  '',false, false);
	$nums= DB_num_rows($r);
	if($nums >0){
	$w = DB_fetch_array($r);
	$id = $w['planid'];
	$ShowRecords=true;
	}else{
	prnMsg( _('There is no Procurement Plan for the Selected Department for the Financial Year ends '.$_POST['year'].'.'), 'error');
	$ShowRecords=false;
	}
}
if (isset($_POST['update'])) {

foreach ($_POST as $key => $value) {
if (mb_strstr($key,'StockID')) {
		$Index=mb_substr($key, 7);
		$StockID=$value;
		$ErrMsg = _('Cannot update Procurement Plan');
		$DbgMsg = _('The SQL that failed to update the Procurement Plan was');
		$SQL="UPDATE irq_procurementplanitems SET quantity='" . $_POST['qty'.$Index] . "',
													quantity2='" . $_POST['qty2'.$Index] . "',
													quantity3='" . $_POST['qty3'.$Index] . "'
					WHERE planid='" . $_POST['planid'] ."'
					AND  stockid = '" . $StockID. "'";				
		$result=DB_query($SQL,$ErrMsg,$DbgMsg);

}
}
prnMsg( _('Procurement Plan updated Successfully.'), 'success');
}
?>
<form action="" method="post" enctype="multipart/form-data" target="_parent">
<table>
<tr>
<th colspan="2"><h4>Simple Procurement Plan Report</h4></th>
</tr>
<tr>
<td>Department</td>
						<?php
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';			
	// any internal department allowed
	if($_SESSION['AllowedDepartment'] == 0){
	// any internal department allowed
	$sql="SELECT departmentid,
				description
			FROM departments
			ORDER BY description";
}else{
	// just 1 internal department allowed
	$sql="SELECT departmentid,
				description
			FROM departments
			WHERE departmentid = '". $_SESSION['AllowedDepartment'] ."'
			ORDER BY description";
}
$result=DB_query($sql);
echo '<td><select name="dept">';
echo '<option selected="selected" value="">--Please Select Requesting Department--</option>';
while ($myrow=DB_fetch_array($result)){
	if (isset($_POST['dept']) AND $_POST['dept']==$myrow['departmentid']){
		echo '<option selected="True" value="' . $myrow['departmentid'] . '">' . htmlspecialchars($myrow['description'], ENT_QUOTES,'UTF-8') . '</option>';
	} else {
		echo '<option value="' . $myrow['departmentid'] . '">' . htmlspecialchars($myrow['description'], ENT_QUOTES,'UTF-8') . '</option>';
	}
}
echo '</select>';
echo '</td>';
				
?>					
  </tr>
  <tr>
<td>Financial Year Ends </td>
<td>
<?php
$sql3="SELECT year FROM irq_procurementplan GROUP BY year";
$result3=DB_query($sql3);
	 echo '<select name="year">';
	 while ($myrow3=DB_fetch_array($result3)){
	 if (isset($_POST['year']) AND $_POST['year']==$myrow3['year']){
	 echo '<option selected="true">'.str_pad($myrow3['year'],2,'0',STR_PAD_LEFT).'</option>';
	 }else{
        echo '<option>'.str_pad($myrow3['year'],2,'0',STR_PAD_LEFT).'</option>';
		}
		}
	echo '</select>';
					   ?>
</td>
</tr>
<tr>
<td></td>
<td><input name="SubmitRequest" type="submit" value="Submit" /></td>
</tr>
  </table>
</form>

<?php
if (isset($ShowRecords) && $ShowRecords==true) {
echo '<form id="form" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<br />
			<table class="selection">
			
			<tr>';
			echo '<th>' .  _('Item ID'). '</th>
				<th>' .  _('Item Description') . '</th>';
		echo '<th>' .  _('1st Quarter') . '</th> <th>' .  _('2nd Quarter') . '</th> <th>' .  _('3rd Quarter') . '</th>';
	echo '</tr>';

// Main Table
$sql = "SELECT stockmaster.stockid,
								stockmaster.description,
								irq_procurementplanitems.quantity as qty,
								irq_procurementplanitems.quantity2 as qty2,
								irq_procurementplanitems.quantity3 as qty3
							FROM stockmaster INNER JOIN irq_procurementplanitems
							ON irq_procurementplanitems.stockid = stockmaster.stockid
							WHERE irq_procurementplanitems.planid='".$id."'";
							
			$result=DB_query($sql);
			if (DB_error_no()!=0 OR DB_num_rows($result)==0) {

				$Title = _('Transaction Print Error Report');
				include ('includes/header.inc');
				echo '<br />' . _('There was a problem retrieving the Procurement plan details for Request number') . ' ' . $id . ' ' . _('from the database');
				if ($debug==1) {
					echo '<br />' . _('The SQL used to get this information that failed was') . '<br />' . $sql;
				}
				include('includes/footer.inc');
				exit;
			} else {
			$i =0;
			while ($myrow2=DB_fetch_array($result)) {
			
			echo '<tr>';
		echo '<th>' .  $myrow2['stockid'] . '</th>';
		echo '<td style="background-color:d2e5e8" class="number">' . $myrow2['description'] . '</td>';
		echo '<td><input type="text" class="number" size="14" name="qty'.$i.'" value="'.locale_number_format($myrow2['qty'],0) .'" /></td>';
		echo '<td><input type="text" class="number" size="14" name="qty2'.$i.'" value="'. locale_number_format($myrow2['qty2'],0) .'" /></td>';
		echo '<td><input type="text" class="number" size="14" name="qty3'.$i.'" value="'. locale_number_format($myrow2['qty3'],0) .'" /></td>';
		echo '</tr>';
		echo '<input type="hidden" name="StockID'.$i.'" value="'.$myrow2['stockid'].'" />';
			$i++;
			}
			}
	
echo '<input type="hidden" name="planid" value="'.$id.'" />';
// Total Line

		
		echo '</table>';

	echo '<script  type="text/javascript">defaultControl(document.form.1next);</script>';
	echo '<br />
		<div class="centre">
			<input type="submit" name="update" value="' . _('Update') . '" />
		</div>
		</div>
		</form>';
}
include('includes/footer.inc');
?>
