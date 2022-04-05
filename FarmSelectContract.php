<?php

/* $Id: SelectContract.php 3692 2010-08-15 09:22:08Z daintree $*/

include('includes/session.inc');
$Title = _('Select Contract');

$ViewTopic= 'Farm Contracts';
$BookMark = 'SelectContract';

include('includes/header.inc');

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/contract.png" title="' . _('Contracts') . '" alt="" />' . ' ' . _('Select A Contract') . '</p> ';

echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

echo '<br /><div class="centre">';

if (isset($_GET['ContractRef'])){
	$_POST['ContractRef']=$_GET['ContractRef'];
}
    if (isset($_GET['SelectedCustomer'])){
	    $_POST['SelectedCustomer']=$_GET['SelectedCustomer'];
}


if (isset($_POST['ContractRef']) AND $_POST['ContractRef']!='') {
	$_POST['ContractRef'] = trim($_POST['ContractRef']);
	echo _('Contract Reference') . ' - ' . $_POST['ContractRef'];
   } else {
	   if (isset($_POST['SelectedCustomer'])) {
  		  echo _('For customer') . ': ' . $_POST['SelectedCustomer'] . ' ' . _('and') . ' ';
		  echo '<input type="hidden" name="SelectedCustomer" value="' . $_POST['SelectedCustomer'] . '" />';
	}
}

if (!isset($_POST['ContractRef']) or $_POST['ContractRef']==''){

	echo _('Contract Reference') . ': <input type="text" name="ContractRef" maxlength="20" size="20" />&nbsp;&nbsp;';
	echo '<select name="Status">';

	      if (isset($_GET['Status'])){
	     	$_POST['Status']=$_GET['Status'];
	}
	if (!isset($_POST['Status'])){
		 $_POST['Status']=4;
	}

	$statuses[] = _('Not Yet Quoted');
	$statuses[] = _('Quoted - No Order Placed');
	$statuses[] = _('Order Placed');
	$statuses[] = _('Completed');
	$statuses[] = _('All Contracts');

	$status_count = count($statuses);

	for ( $i = 0; $i < $status_count; $i++ ) {
		if ( $i == $_POST['Status'] ) {
			echo '<option selected="selected" value="' . $i . '">' . $statuses[$i] . '</option>';
		    } else {
		     echo '<option value="' . $i . '">' . $statuses[$i] . '</option>';
		      }
	}

	echo '</select> &nbsp;&nbsp;';
}
echo '<input type="submit" name="SearchContracts" value="' . _('Search') . '" />';
echo '&nbsp;&nbsp;<a href="' . $RootPath . '/FarmContract.php">' . _('New Contract') . '</a></div><br />';


//figure out the SQL required from the inputs available

if (isset($_POST['ContractRef']) AND $_POST['ContractRef'] !='') {
		$SQL = "SELECT contractref,
		               contractdescription, SUBSTRING(contractdescription,1,30),
					   categoryid,
					   farmcontracts.debtorno,
					   contractNo,
					   wo,
					   customerref,
					   requireddate
				FROM farmcontracts
				WHERE contractref " . LIKE . " '%" .  $_POST['ContractRef'] ."%'";

} else { //contractref not selected
	if (isset($_POST['SelectedCustomer'])) {

		$SQL = "SELECT contractref,
		               contractdescription, SUBSTRING(contractdescription,1,30),
					   categoryid,
					   farmcontracts.debtorno,
					   contractNo,
					   wo,
					   customerref,
					   requireddate
				FROM farmcontracts 
				WHERE debtorno='". $_POST['SelectedCustomer'] ."'";
		if ($_POST['Status']!=4){
			$SQL .= " AND status='" . $_POST['Status'] . "'";
		}
	} else { //no customer selected
		$SQL = "SELECT contractref,
		               contractdescription, SUBSTRING(contractdescription,1,30),
					   categoryid,
					   farmcontracts.debtorno,
					   contractNo,
					   wo,
					   customerref,
					   requireddate
				FROM farmcontracts";
		if ($_POST['Status']!=4){
			$SQL .= " AND status='" . $_POST['Status'] . "'";
		}
	}
} //end not contract ref selected

$ErrMsg = _('No contracts were returned by the SQL because');
$ContractsResult = DB_query($SQL,$ErrMsg);

/*show a table of the contracts returned by the SQL */

echo '<table align="center" style="width:55%">';

$TableHeader = '<tr>
					<th>' . _('Modify') . '</th>
					<th>' . _('Order') . '</th>
					<th>' . _('Costing') . '</th>
					<th>' . _('Contract Ref') . '</th>
					<th>' . _('Customer No') . '</th>
					<th>' . _('Required Date') . '</th>
				</tr>';

echo $TableHeader;

$j = 1;
$k=0; //row colour counter
while ($myrow=DB_fetch_array($ContractsResult)) {
	if ($k==1){
		echo '<tr class="EvenTableRows">';
		$k=0;
	} else {
		echo '<tr class="OddTableRows">';
		$k++;
	}

	$ModifyPage = $RootPath . '/FarmContract.php?ModifyContractRef=' . $myrow['contractref'];
	$OrderModifyPage = $RootPath . '/SelectOrderItems.php?ModifyOrderNumber=' . $myrow['contractNo'];
	$IssueToWOPage = $RootPath . '/WorkOrderIssue.php?WO=' . $myrow['wo'] . '&amp;description_Id=' . $myrow['contractref'];
	$CostingPage = $RootPath . '/FarmContractsCosting.php?SelectedContract=' . $myrow['contractref'];
	$FormatedRequiredDate = ConvertSQLDate($myrow['requireddate']);

	if ($myrow['status']==0 OR $myrow['status']==0){ //still setting up the contract
		echo '<td><a href="' . $ModifyPage . '">' . _('Modify') . '</a></td>';
	} else {
		echo '<td>' . _('n/a') . '</td>';
	}
	if ($myrow['status']==0 OR $myrow['status']==0){ // quoted or ordered
		echo '<td><a href="' . $OrderModifyPage . '">' . $myrow['contractNo'] . '</a></td>';
	} else {
		echo '<td>' . _('n/a') . '</td>';
	}
	if ($myrow['status']==0 OR $myrow['status']==0){
			echo '<td><a href="' . $CostingPage . '">' . _('View') . '</a></td>';
		} else {
			echo '<td>' . _('n/a') . '</td>';
	}
	$suppname= DB_query("SELECT * FROM suppliers a,farmcontracts b
								         WHERE b.debtorno=a.supplierid");
							$supp = DB_fetch_array($suppname);
	echo '<td>' . $myrow['contractref'] . '</td>';
		  // echo '<td>' . $myrow['SUBSTRING(contractdescription,1,30)'] . '</td> ';
		  echo '<td>' . $supp['suppname'] . '</td>
		  <td>' . $FormatedRequiredDate . '</td></tr>';

	$j++;
	if ($j == 12){
		$j=1;
		echo $TableHeader;
	}
//end of page full new headings if
}
//end of while loop

echo '</table>
      </div>
      </form>
      <br />';
include('includes/footer.inc');
?>