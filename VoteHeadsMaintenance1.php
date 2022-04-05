<?php

/* $Id: Areas.php 6941 2014-10-26 23:18:08Z daintree $*/

include('includes/session.inc');

$Title = _('Vote Heads Maintenance');
$ViewTopic= 'CreatingNewVotehead';
$BookMark = 'Votebook';
include('includes/header.inc');

if (isset($_GET['SelectedVotehead'])){
	$SelectedVotehead = mb_strtoupper($_GET['SelectedVotehead']);
} elseif (isset($_POST['SelectedVotehead'])){
	$SelectedVotehead = mb_strtoupper($_POST['SelectedVotehead']);
}
if (isset($Errors)) {
	unset($Errors);
}
$Errors = array();

if (isset($_POST['submit'])) {
 $Selectedbook = $_POST['book'];
	//initialise no input errors assumed initially before we test
	$InputError = 0;
	$i=1;
	$_POST['voteCode'] = mb_strtoupper($_POST['voteCode']);
	$sql = "SELECT Votecode FROM voteheadmaintenance WHERE Votecode='".$_POST['voteCode']."'";
	$result = DB_query($sql);
	if (mb_strlen($_POST['voteCode']) <4) {
		$InputError = 1;
		prnMsg(_('The vote code must be more than 4 characters long'),'error');
		$Errors[$i] = 'voteCode';
		$i++;
	} elseif (DB_num_rows($result)>0 AND !isset($SelectedVotehead)){
		$InputError = 1;
		prnMsg(_('The vote code entered already exists'),'error');
		$Errors[$i] = 'voteCode';
		$i++;
	}elseif ( trim($_POST['voteCode']) == '' ) {
		$InputError = 1;
		prnMsg(_('The vote code may not be empty'),'error');
		$Errors[$i] = 'voteCode';
		$i++;
	} elseif ( trim($_POST['voteHead']) == '' ) {
		$InputError = 1;
		prnMsg(_('The vote head description may not be empty'),'error');
		$Errors[$i] = 'voteHead';
		$i++;
	}
	if (isset($SelectedVotehead) AND $InputError !=1) {

		$sql = "UPDATE voteheadmaintenance SET Votehead='" . $_POST['voteHead'] . "'
								WHERE Votecode = '" . $SelectedVotehead . "'";

		$msg = _(' Vote code') . ' ' . $SelectedVotehead  . ' ' . _('has been updated');

	} elseif ($InputError !=1) {
		$sql = "INSERT INTO voteheadmaintenance (Votecode,
									Votehead,
									Vbook
								) VALUES (
									'" . $_POST['voteCode'] . "',
									'" . $_POST['voteHead'] . "',
									'" .$_POST['book']. "'
								)";

		$SelectedVotehead = $_POST['voteHead'];
		$msg = _('New vote code') . ' ' . $_POST['voteCode'] . ' ' . _('has been added to Votebook');
	} else {
		$msg = '';
	}
	//run the SQL from either of the above possibilites
	if ($InputError !=1) {
		$ErrMsg = _('The Vote Head could not be added or updated because');
		$DbgMsg = _('The SQL that failed was');
		$result = DB_query($sql, $ErrMsg, $DbgMsg);
		unset($SelectedVotehead);
		unset($_POST['voteCode']);
		unset($_POST['voteHead']);
		prnMsg($msg,'success');
	}

} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button
	$CancelDelete = 0;
// PREVENT DELETES IF DEPENDENT RECORDS IN 'voteheadsmaintenance'
	$sql= "SELECT COUNT(voted_Item) AS voted_Item FROM  commitment WHERE  commitment.voted_Item='$SelectedVotehead'";
	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);
	if ($myrow['voted_Item']>0) {
		$CancelDelete = 1;
		prnMsg( _('Cannot delete this Vote head because Commitment has been entered using this Votehead'),'warn');
		echo '<br />' . _('There are') . ' ' . $myrow['voted_Item'] . ' ' . _('voted_Item using this vote code');
	} 
	if ($CancelDelete==0) {
		$sql="DELETE FROM voteheadmaintenance WHERE Votecode='" . $SelectedVotehead . "'";
		$result = DB_query($sql);
		prnMsg(_('Vote Code') . ' ' . $SelectedVotehead . ' ' . _('has been deleted') .' !','success');
	} //end if Delete voteheads
	unset($SelectedVotehead);
	unset($_GET['delete']);
}
if (!isset($SelectedVotehead)) {
	$sql = "SELECT a.Votecode,
					a.Votehead,
					a.Vbook,
					b.votecode,
					b.allocated_Fund,
					b.suppliementary	
				FROM voteheadmaintenance a
				LEFT JOIN funds_allocations b ON a.Votecode=b.votecode";
	$result = DB_query($sql);
	echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/maintenance.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p><br />';
	echo'<table cellpadding="2" class="selection">';
	echo '<tr>
				<th class="ascending">' . _('Vote Code') . '</th>
				<th class="ascending">' . _('VoteHead Name') . '</th>
				<th class="ascending">' . _('Book') . '</th>
				<th>' . _('Allocation in FY').'&nbsp;'.Date('Y',YearEndDate($_SESSION['YearEnd'],-1)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd'],0)).'</th>
				<th>' . _('Action') . '</th>
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
		//sql9="SELECT bookName FROM votebookmaintenance where Vbook=";
		$totalallocation=$myrow['allocated_Fund']+$myrow['suppliementary'];
		echo '<td>' . $myrow['Votecode'] . '</td>
				<td>' . $myrow['Votehead'] . '</td>
				<td>' .$myrow['Vbook']. '</td>
				<td>' . locale_number_format($totalallocation,2) . '</td>
				<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?SelectedVotehead=' . $myrow['Votecode'] . '">' . _('Edit') . '</a></td>
				<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?SelectedVotehead=' . $myrow['Votecode'] . '&amp;delete=yes">' . _('Delete') . '</a></td>
			</tr>';
	}
	//END WHILE LIST LOOP
	echo '</table>';
}
//end of ifs and buts!
if (isset($SelectedVotehead)) {
	echo '<div class="centre"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">' . _('Review Books Defined in the Votebook') . '</a></div>';
}
if (!isset($_GET['delete'])) {

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">';
    echo '<div><br />';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	if (isset($SelectedVotehead)) {
		//editing an existing voteheads
		$sql = "SELECT Votecode,
						Votehead,
						Vbook
					FROM voteheadmaintenance
					WHERE Votecode='" . $SelectedVotehead . "'";
		$result = DB_query($sql);
		$myrow = DB_fetch_array($result);
		$_POST['voteCode'] = $myrow['Votecode'];
		$_POST['voteHead']  = $myrow['Votehead'];
		$_POST['vbook'] = $myrow['Vbook'];

		echo '<input type="hidden" name="SelectedVotehead" value="' . $SelectedVotehead . '" />';
		echo '<input type="hidden" name="voteCode" value="' .$_POST['voteCode'] . '" />';
		echo '<table class="selection">
				<tr>
					<td>' . _('Vote Code') . ':</td>
					<td>' . $_POST['voteCode'] . '</td>
				</tr>';
	} else {
		if (!isset($_POST['voteCode'])) {
			$_POST['voteCode'] = '';
		}
		if (!isset($_POST['voteHead'])) {
			$_POST['voteHead'] = '';
		}else{
		if (!isset($_POST['Vbook'])) {
			$_POST['Vbook'] = '';
		}
		if (!isset($_POST['Vbook'])) {
			$_POST['Vbook'] = '';
		
		}
		}
		echo '<table class="selection">
		
		<tr>
	<td>Select a Book</td>
	<td><select name="book">';
	echo '<option  selected="selected" value="">--Select book--</option>';
    $sql = "SELECT * FROM  votebookmaintenance ORDER BY bookid";
	$result1 = DB_query($sql);
	while ($myrow=DB_fetch_array($result1)){
	
			$Account = $myrow['bookid'] . ' - ' . htmlspecialchars($myrow['bookName'],ENT_QUOTES,'UTF-8',false);
			if (isset($Selectedbook) AND $Selectedbook==$myrow['bookName']){
				echo '<option selected="selected" value="' . $myrow['bookName'] . '">' . $Account . '</option>';					
			} else {			
				echo '<option value="' . $myrow['bookName'] . '">' . $Account . '</option>';
			}
	     }
	echo '</select></td>
		</tr>';		
		
		
			echo'<tr>
				<td>' . _('Vote Code') . ':</td>
				<td><input tabindex="1" ' . (in_array('voteCode',$Errors) ? 'class="inputerror"' : '' ) .' type="text" name="voteCode" required="required" autofocus="autofocus" value="' . $_POST['voteCode'] . '" size="10" maxlength="15" title="' . _('Enter the vote code - up to 4 characters are allowed') . '" /></td>
			</tr>';
	}
	echo '<tr><td>' . _('Vote Head') . ':</td>
		<td><input tabindex="2" ' . (in_array('voteHead',$Errors) ?  'class="inputerror"' : '' ) .'  type="text" required="required" name="voteHead" value="' . $_POST['voteHead'] .'" size="45" maxlength="55" title="' . _('Enter the Vote head descriptions') . '" /></td>
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