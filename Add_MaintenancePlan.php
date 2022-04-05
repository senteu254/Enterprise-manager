<?php
require_once('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
if(!isset($_GET['AID'])){
die ('<div align="center">
<div style="border:groove; width:500px;">
<table width="100%" border="0">
  <tr>
    <td align="center" style="color:#FF0000; font-size:26px; font-weight:bold; text-decoration:underline;">Unauthorized Access!</td>
  </tr>
  <tr>
    <td align="center" style="font-weight:bold; font-size:24px; text-decoration:underline;">You do not Have Permission to access this Page</td>
  </tr>
  <tr>
    <td align="center">All fraudulent Attempts will be investigated and procecuted</td>
  </tr>
  <tr>
    <td align="center">in accordance with applicable law</td>
  </tr>
</table>
</div>
</div>');
exit;
}
if(is_numeric($_GET['AID'])){
$id=$_GET['AID'];
}

 $sql="SELECT * FROM fixedassets,fixedassetlocations WHERE fixedassetlocations.locationid=fixedassets.assetlocation AND assetid='".$id."'";
 $result=DB_query($sql);
$r=DB_fetch_array($result);
	echo '<br /><div style="width:460px;">';
	if(isset($_GET['PID'])){
	echo '<form action="index.php?Application=FA&Link=Planning" method="post" enctype="multipart/form-data" target="_top">';
	echo '<input type="hidden" name="PlanID" value="' . $_GET['PID'] . '" />';
	$sqlx="SELECT * FROM fixedassetplanning WHERE planid='".$_GET['PID']."'";
	$resultx=DB_query($sqlx);
	$rwz=DB_fetch_array($resultx);
	$_POST['Yearend'] =ConvertSQLDate($rwz['fyend']);
	$_POST['state'] =$rwz['servicestatus'];
	$_POST['month'] =explode(',',$rwz['months']);
	}else{
	echo '<form action="index.php?Application=FA&Link=NewPMPlan" method="post" enctype="multipart/form-data" target="_top">';
	}
	echo '<table class="table">
			<tr>
				<th>' . _('Preventive Maintainance Plan for') . ':</th>
			</tr>';
	echo '<tr>
				<th>Serial No.: ' . $r['serialno'] . '&nbsp;&nbsp;&nbsp;&nbsp; Name: ' . $r['longdescription'] . '</th>
			</tr>';
			
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			echo '<input type="hidden" id="assetid" name="assetid" value="' . $id . '" />';
			echo'<tr><td>Financial Year: ';
			if(!isset($_POST['Yearend'])){
			$_POST['Yearend'] = Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],0));
			}
			
			echo'<select name="Yearend"><option '.((isset($_POST['Yearend']) && Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],-1))==$_POST['Yearend'])? 'selected':'').' value="' .  Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],-1)) . '">'.Date('Y',YearEndDate($_SESSION['YearEnd'],-2)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd'],-1)).'</option>';			   
			echo'<option '.((isset($_POST['Yearend']) && Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],0))==$_POST['Yearend'])? 'selected':'').' value="'.  Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],0)) . '">'.Date('Y',YearEndDate($_SESSION['YearEnd'],-1)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd'],0)).'</option>';
			
			echo'<option '.((isset($_POST['Yearend']) && Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],+1))==$_POST['Yearend'])? 'selected':'').' value="'.  Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],+1)) . '">'.Date('Y',YearEndDate($_SESSION['YearEnd'],0)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd'],+1)).'</option>';
	echo '</select></td></tr>';
	
			echo '<tr><td>';
				$arr = array('S'=>'Serviceable','N/S'=>'UnServiceable');
			echo 'Serviceability : <select required="true" name="state" id="state">';
			echo '<option selected="selected" value="">--Select Serviceability Status--</option>';
				foreach($arr as $key=>$val){
			echo '<option '.((isset($_POST['state']) && $key==$_POST['state'])? 'selected':'').' value="'.$key.'">'.$val.'</option>';
				}
			echo '</select>';
			echo '</td></tr>';
			echo '<tr><td> Months:<br />';
			$mts = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
			foreach($mts as $key=>$val){
			echo '<input name="month[]" type="checkbox" '.(in_array($key, $_POST['month'])? 'checked':'').' value="'.$key.'" /> '.$val.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			echo '</td></tr>';

			echo '<td align="centre"><input type="submit" value="' . _('Submit') . '" name="SubmitPlan"/>';
	echo '</td>
			</tr>
			</table>';	
			echo '</form></div>';	

?>
