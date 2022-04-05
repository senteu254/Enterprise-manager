
<?php

/* $Id: Areas.php 6941 2014-10-26 23:18:08Z daintree $*/

include('includes/session.inc');

$Title = _('Tax Authorities and Rates Maintenance');
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
	$_POST['percentage'] = mb_strtoupper($_POST['percentage']);
	$sql = "SELECT percentage FROM pv_tax WHERE percentage='".$_POST['percentage']."'";
	$result = DB_query($sql);
 if ( trim($_POST['voteHead']) == '' ) {
		$InputError = 1;
		prnMsg(_('The vote head description may not be empty'),'error');
		$Errors[$i] = 'voteHead';
		$i++;
	}
	if (isset($SelectedVotehead) AND $InputError !=1) {
		$sql = "UPDATE pv_tax SET tax_name='" . $_POST['voteHead'] . "',percentage='".$_POST['percentage']."' WHERE ptid = '" . $SelectedVotehead . "'";

		$msg = _('Authorities and Rates') . ' ' . $SelectedVotehead  . ' ' . _('has been updated');

	} elseif ($InputError !=1) {
		$sql = "INSERT INTO pv_tax (tax_name,
									percentage
								) VALUES (
									'" . $_POST['voteHead'] . "',
									'" .$_POST['percentage']. "'
								)";

		$SelectedVotehead = $_POST['voteHead'];
		$msg = _('New Tax Authorities and Rates') . ' ' . $_POST['voteCode'] . ' ' . _('has been added to System');
	} else {
		$msg = '';
	}
	//run the SQL from either of the above possibilites
	if ($InputError !=1) {
		$ErrMsg = _('The Tax Name could not be added or updated because');
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
	/*$sql= "SELECT COUNT(voted_Item) AS voted_Item FROM  commitment WHERE  commitment.voted_Item='$SelectedVotehead'";
	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);
	if ($myrow['voted_Item']>0) {
		$CancelDelete = 1;
		prnMsg( _('Cannot delete this Vote head because Commitment has been entered using this Votehead'),'warn');
		echo '<br />' . _('There are') . ' ' . $myrow['voted_Item'] . ' ' . _('voted_Item using this vote code');
	} 
	if ($CancelDelete==0) {*/
		$sql="DELETE FROM pv_tax WHERE ptid='" . $SelectedVotehead . "'";
		$result = DB_query($sql);
		prnMsg(_('Code') . ' ' . $SelectedVotehead . ' ' . _('has been deleted') .' !','success');
	//} //end if Delete voteheads
	unset($SelectedVotehead);
	unset($_GET['delete']);
}
if (!isset($SelectedVotehead)) {
	$sql = "SELECT * FROM pv_tax";
	$result = DB_query($sql);
	echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/maintenance.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p><br />';
	echo'<table cellpadding="2" class="selection">';
	echo '<tr>
				<th class="ascending">' . _('Code') . '</th>
				<th class="ascending">' . _('Tax Name') . '</th>
				<th>' . _('Percentage Rates in').'&nbsp;'.Date('Y',YearEndDate($_SESSION['YearEnd'],-1)).'/'.Date('Y',YearEndDate($_SESSION['YearEnd'],0)).'</th>
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
		echo '<td>' . $myrow['ptid'] . '</td>
				<td>' . $myrow['tax_name'] . '</td>
				<td>' .$myrow['percentage']. '</td>
				<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?SelectedVotehead=' . $myrow['ptid'] . '">' . _('Edit') . '</a></td>
				<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?SelectedVotehead=' . $myrow['ptid'] . '&amp;delete=yes">' . _('Delete') . '</a></td>
			</tr>';
	}
	//END WHILE LIST LOOP
	echo '</table>';
}
//end of ifs and buts!
if (isset($SelectedVotehead)) {
	echo '<div class="centre"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">' . _('Tax Authorities and Rates Maintenance') . '</a></div>';
}
if (!isset($_GET['delete'])) {

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">';
    echo '<div><br />';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	if (isset($SelectedVotehead)) {
		//editing an existing voteheads
		$sql = "SELECT * FROM pv_tax
					WHERE ptid='" . $SelectedVotehead . "'";
		$result = DB_query($sql);
		$myrow = DB_fetch_array($result);
		$_POST['voteCode'] = $myrow['ptid'];
		$_POST['voteHead']  = $myrow['tax_name'];
		$_POST['percentage'] = $myrow['percentage'];

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
		echo '<table class="selection">';
		
	}
	echo '<tr><td>' . _('Tax Name') . ':</td>
		<td><input tabindex="2" ' . (in_array('voteHead',$Errors) ?  'class="inputerror"' : '' ) .'  type="text" required="required" name="voteHead" value="' . $_POST['voteHead'] .'" size="45" maxlength="55" title="' . _('Enter the Vote head descriptions') . '" /></td>
		</tr>';
		echo '<tr><td>' . _('Percentange') . ':</td>
		<td><input tabindex="2" ' . (in_array('percentage',$Errors) ?  'class="inputerror"' : '' ) .'  type="text" required="required" name="percentage" value="' . $_POST['percentage'] .'" size="45" maxlength="55" title="' . _('Enter the percentage') . '" /></td>
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
 ////////////////////////////////////////////////////////////////////////////////////////////////////////////
 ?>
 <style type="text/css">
  table.blueTable {
  border: 1px solid #1C6EA4;
  background-color: #EEEEEE;
  width: 100%;
  text-align: left;
  border-collapse: collapse;
}
table.blueTable td, table.blueTable th {
  border: 1px solid #AAAAAA;
  padding: 3px 2px;
}
table.blueTable tbody td {
  font-size: 13px;
}
table.blueTable thead {
  background: #1C6EA4;
  background: -moz-linear-gradient(top, #5592bb 0%, #327cad 66%, #1C6EA4 100%);
  background: -webkit-linear-gradient(top, #5592bb 0%, #327cad 66%, #1C6EA4 100%);
  background: linear-gradient(to bottom, #5592bb 0%, #327cad 66%, #1C6EA4 100%);
  border-bottom: 2px solid #444444;
}
table.blueTable thead th {
  font-size: 21px;
  font-weight: bold;
  color: #FFFFFF;
  border-left: 2px solid #D0E4F5;
}
table.blueTable thead th:first-child {
  border-left: none;
}
table.blueTable tbody tr.child {
  display: none;
  background: #D0E4F5;
}
</style>

<script language="JavaScript">
function display_detail(id){  

  var sid='s'+id;
  var sidbuttons = document.getElementsByClassName('sidbutton');
  for(i = 0; i < sidbuttons.length; i++) {
    if(sidbuttons[i].id == sid){
      if(sidbuttons[i].classList.contains('bopen')){
        sidbuttons[i].innerHTML = '+';
        sidbuttons[i].classList.remove('bopen');
      }else{
        sidbuttons[i].innerHTML = '-';
        sidbuttons[i].classList.add('bopen');    
      }      
    }else{
      if(!sidbuttons[i].classList.contains('bopen'))
        sidbuttons[i].innerHTML = '+';      
    }
  }

  var childrows = document.getElementsByClassName('child');
  for(i = 0; i < childrows.length; i++) {
    if(childrows[i].classList.contains(sid+'child')){
      if( childrows[i].classList.contains('copen') ){
        childrows[i].style.display = 'none';
        childrows[i].classList.remove('copen');
      }
      else{
        childrows[i].style.display = 'table-row';
        childrows[i].classList.add('copen'); 
      }
    }else{
      if(!childrows[i].classList.contains('copen'))  
        childrows[i].style.display = 'none';      
    }  
  }  

}
</script>
<?
include('includes/footer.inc');
?>