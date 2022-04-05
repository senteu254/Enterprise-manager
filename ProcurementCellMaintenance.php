<?php

/* $Id: Areas.php 6941 2014-10-26 23:18:08Z daintree $*/

include('includes/session.inc');

$Title = _('Procurement Cell Maintenance');
$ViewTopic= 'Procurement Cell Maintenance';
$BookMark = 'Cell Maintenance';
include('includes/header.inc');

if (isset($_GET['SelectedBook'])){
	$SelectedBook = mb_strtoupper($_GET['SelectedBook']);
} elseif (isset($_POST['SelectedBook'])){
	$SelectedBook = mb_strtoupper($_POST['SelectedBook']);
}
if (isset($Errors)) {
	unset($Errors);
}
$Errors = array();

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;
	$i=1;
	$_POST['bookID'] = mb_strtoupper($_POST['bookID']);
	$sql = "SELECT cell_code FROM cell_maintenance WHERE cell_code='".$_POST['bookID']."'";
	$result = DB_query($sql);
	if ( trim($_POST['bookID']) == '' ) {
		$InputError = 1;
		prnMsg(_('The cell code may not be empty'),'error');
		$Errors[$i] = 'bookID';
		$i++;
	} elseif ( trim($_POST['bookNAME']) == '' ) {
		$InputError = 1;
		prnMsg(_('The cell Name description may not be empty'),'error');
		$Errors[$i] = 'bookNAME';
		$i++;
	}
	if (isset($SelectedBook) AND $InputError !=1) {

		$sql = "UPDATE cell_maintenance SET cell_name='" . $_POST['bookNAME'] . "'
								WHERE cell_name = '" . $SelectedBook . "'";

		$msg = _(' cell code') . ' ' . $SelectedBook  . ' ' . _('has been updated');

	} elseif ($InputError !=1) {
		$sql = "INSERT INTO cell_maintenance (cell_code,
									cell_name
								) VALUES (
									'" . $_POST['bookID'] . "',
									'" . $_POST['bookNAME'] . "'
								)";

		$SelectedBook = $_POST['bookNAME'];
		$msg = _('New  cell ') . ' ' . $_POST['bookNAME'] . ' ' . _('has been added to procurement cell');
	} else {
		$msg = '';
	}
	//run the SQL from either of the above possibilites
	if ($InputError !=1) {
		$ErrMsg = _('The cell could not be added or updated because');
		$DbgMsg = _('The SQL that failed was');
		$result = DB_query($sql, $ErrMsg, $DbgMsg);
		unset($SelectedBook);
		unset($_POST['bookID']);
		unset($_POST['bookNAME']);
		prnMsg($msg,'success');
	}

} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button
	$CancelDelete = 0;
// PREVENT DELETES IF DEPENDENT RECORDS IN 'voteheadsmaintenance'
	$sql= "SELECT COUNT(cell_code) AS cell_code FROM cell_maintenance WHERE  cell_code='$SelectedBook'";
	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);
	if ($myrow['cell_code']>0) {
		$CancelDelete = 1;
		prnMsg( _('Cannot delete this cell head because cell has been enetered using this Book'),'warn');
		echo '<br />' . _('There are') . ' ' . $myrow['cell_code'] . ' ' . _(' using this vote code');
	} 
	if ($CancelDelete==0) {
		$sql="DELETE FROM votebookmaintenance WHERE bookid='" . $SelectedBook . "'";
		$result = DB_query($sql);
		prnMsg(_('Vote Code') . ' ' . $SelectedBook . ' ' . _('has been deleted') .' !','success');
	} //end if Delete voteheads
	unset($SelectedBook);
	unset($_GET['delete']);
}
if (!isset($SelectedBook)) {
	$sql = "SELECT * FROM cell_maintenance";
	$result = DB_query($sql);
	echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/maintenance.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p><br />';
	echo '<table class="selection">
			<tr>
				<th>' . _('Cell Code') . '</th>
				<th>' . _('Cell Name') . '</th>
				<th>'._('Action').'</th>
			</tr>';
	$k=0; //row colour counter
	while ($myrow = DB_fetch_array($result)) {
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k++;
		}
		
		echo '<td>' . $myrow['cell_code'] . '</td>
				<td>' . $myrow['cell_name'] . '</td>
				<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?SelectedBook=' . $myrow['cell_code'] . '">' . _('Edit') . '</a></td>
				<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?SelectedBook=' . $myrow['cell_code'] . '&amp;delete=yes">' . _('Delete') . '</a></td>
			</tr>';
	}
	//END WHILE LIST LOOP
	echo '</table>';
}
//end of ifs and buts!
if (isset($SelectedBook)) {
	echo '<div class="centre"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">' . _('Review Votebook Defined in the Votebook') . '</a></div>';
}
if (!isset($_GET['delete'])) {

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">';
    echo '<div><br />';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	if (isset($SelectedBook)) {
		//editing an existing voteheads
		$sql = "SELECT * FROM cell_maintenance
					WHERE cell_code='" . $SelectedBook . "'";
		$result = DB_query($sql);
		$myrow = DB_fetch_array($result);
		$_POST['bookID'] = $myrow['cell_code'];
		$_POST['bookNAME']  = $myrow['cell_name'];

		echo '<input type="hidden" name="SelectedBook" value="' . $SelectedBook . '" />';
		echo '<input type="hidden" name="bookID" value="' .$_POST['bookID'] . '" />';
		echo '<table class="selection">
				<tr>
					<td>' . _('Cell Code') . ':</td>
					<td>' . $_POST['bookID'] . '</td>
				</tr>';
	} else {
		if (!isset($_POST['bookID'])) {
			$_POST['bookID'] = '';
		}
		if (!isset($_POST['bookNAME'])) {
			$_POST['bookNAME'] = '';
		}
		echo '<table class="selection">
			<tr>
				<td>' . _('Cell Code') . ':</td>
				<td><input tabindex="1" ' . (in_array('bookID',$Errors) ? 'class="inputerror"' : '' ) .' type="text" name="bookID" required="required" autofocus="autofocus" value="' . $_POST['bookID'] . '" size="10" maxlength="15" title="' . _('Enter the Book code - up to 4 characters are allowed') . '" /></td>
			</tr>';
	}
	echo '<tr><td>' . _('Cell Name') . ':</td>
		<td><input tabindex="2" ' . (in_array('bookNAME',$Errors) ?  'class="inputerror"' : '' ) .'  type="text" required="required" name="bookNAME" value="' . $_POST['bookNAME'] .'" size="45" maxlength="55" title="' . _('Enter the boo Name descriptions') . '" /></td>
		</tr>';
	echo '<tr>
			<td colspan="2">
				<div class="centre">
					<input tabindex="3" type="submit" name="submit" value="' . _('Enter Information') .'" />
				</div>
			</td>
		</tr>
		</table>
        </div>
		</form>';
 } //end if record deleted no point displaying form to add record
include('includes/footer.inc');
?>