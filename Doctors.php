<link rel="stylesheet" type="text/css" href="datepickr.css" />
			
	 <link rel="stylesheet" href="js/smoothness/jquery-ui.css" />
    
    <!-- Load jQuery JS -->
    <script src="js/jquery-1.9.1.js"></script>
    <!-- Load jQuery UI Main JS  -->
    <script src="js/jquery-ui.js"></script>
    
    <!-- Load SCRIPT.JS which will create datepicker for input field  -->
    <script src="script.js"></script>
    
    <link rel="stylesheet" href="runnable.css" />
<?php

/* $Id: MaintenanceTasks.php 5231 2012-04-07 18:10:09Z daitnree $*/

include('includes/session.inc');

$Title = _('Doctors Details');

$ViewTopic = 'Doctors Details';
$BookMark = 'DoctorsDetails';

include('includes/header.inc');

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/group_add.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p><br />';


if (isset($_POST['Submit'])) {
	    $date = date('Y-m-d', strtotime($_POST['date']));
		$sql="INSERT INTO doctor(doc_id,
											doc_name,
											doc_gender,
											date,
											specialization)
						VALUES( '',
								'" . $_POST['name'] . "',
								'" . $_POST['gender'] . "',
								'" . $date . "',
								'" . $_POST['spec'] . "'
								)";
		$ErrMsg = _('The doctor details cannot be inserted because');
		$Result=DB_query($sql,$ErrMsg);
		if($Result){
		prnMsg(_('Doctors details successfully Inserted'),'success');
		}
		else{
		prnMsg(_('Doctors details  NOT successfully Inserted'),'error');
		}
		unset($_POST['no']);
		unset($_POST['name']);
		unset($_POST['gender']);
		unset($_POST['date']);
		unset($_POST['spec']);
	}
	
	


echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post" id="form1">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<table class="selection">';

if (!isset($_POST['name'])){
	$_POST['name']='';
}
if (!isset($_POST['gender'])){
	 $_POST['gender']= '';
}
if (!isset($_POST['date'])){
	$_POST['date']='';
}
if (!isset($_POST['spec'])){
	$_POST['spec']='';
}


echo '<tr>
		<td>' . _('Doctor Name').':</td>
		<td><input type="text"  required="required" name="name"  /></td>
	</tr>';

	
	
echo '<tr>
		<td>' . _('Gender') . ':</td>
		<td>
		<select id="emp_gen" name="gender" required="true" class="form-control">
								
								  <option value="">Gender</option>
								  <option>Female</option>
								  <option>Male</option>
								  
								</select>
	</td>
	</tr>';

echo '<tr>
		<td>' . _('Date of appointment').':</td>
		<td><input type="text"   name="date" required="required"  id="date" value="" /></td>
	</tr>';

echo '<tr>
		<td>' . _('Area Of Specialization').':</td>
		<td><textarea name="spec" required="required" cols="40" rows="3"></textarea></td>
	</tr>';

	echo'</table>';


	echo '<br />
		<div class="centre">
			<input type="submit" name="Submit" value="'._('Insert').'" />
		</div>';


echo '</div>
        </form>';
include('includes/footer.inc');

?>
