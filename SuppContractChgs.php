<?php

/* $Id:  $*/

/*The supplier transaction uses the SuppTrans class to hold the information about the invoice
the SuppTrans class contains an array of Contract objects - containing details of all contract charges
Contract charges are posted to the debit of Work In Progress (based on the account specified in the stock category record of the contract item
This is cleared against the cost of the contract as originally costed - when the contract is closed and any difference is taken to the price variance on the contract */

include('includes/DefineSuppTransClass.php');

/* Session started here for password checking and authorisation level check */
include('includes/session.inc');

$Title = _('Contract Charges or Credits');

include('includes/header.inc');

if (!isset($_SESSION['SuppTrans'])){
	prnMsg(_('Contract charges or credits are entered against supplier invoices or credit notes respectively. To enter supplier transactions the supplier must first be selected from the supplier selection screen, then the link to enter a supplier invoice or credit note must be clicked on'),'info');
	echo '<br />
		<a href="' . $RootPath . '/SelectSupplier.php">' . _('Select A Supplier') . '</a>';
	exit;
	/*It all stops here if there aint no supplier selected and invoice/credit initiated ie $_SESSION['SuppTrans'] started off*/
}

/*If the user hit the Add to transaction button then process this first before showing  all contracts on the invoice otherwise it wouldnt show the latest addition*/

if (isset($_POST['AddContractChgToInvoice'])){
  foreach ($_POST['Select'] as $id){
  	$SQLp = "SELECT * FROM contract_payment a INNER JOIN contract_details b ON a.ContractID=b.ContractID WHERE a.PaymentID='".$id."' AND a.status=0";
				$ErrMsg = _('No records displayed For the selected Contract');
	$resultpay = DB_query($SQLp,$ErrMsg);
	$Row = DB_fetch_array($resultpay);
   $_SESSION['SuppTrans']->Add_Contract_To_Trans($Row['PaymentID'],
                                        $Row['Contract_Number'],
   										filter_number_format($Row['Amount_Paid']),
										$Row['Description'],
										$Row['ContractID']);
										/*
  $_SESSION['SuppTrans']->Add_Contract_To_Trans($_POST['ContractRef'],
														filter_number_format($_POST['Amount']),
														$_POST['Narrative'],
														$_POST['AnticipatedCost']);*/
  }
 // unset($_POST['AddAnotherInvoice']);
 
	/*$InputError = False;
	if ($_POST['ContractRef'] == ''){
		$_POST['ContractRef'] = $_POST['ContractSelection'];
	} else{
		$result = DB_query("SELECT  * FROM contract_payment a
			   INNER JOIN contract_assignment b ON a.ContractID = b.ContractID
				WHERE a.PaymentID='" . $_POST['ContractRef'] . "'");*/
	/*	$sql = "SELECT  * FROM contract_payment a
			   INNER JOIN contract_assignment b ON a.ContractID = b.ContractID
			   INNER JOIN suppliers c ON b.SupplierID = c.SupplierID
			   LEFT JOIN contract_details d ON b.ContractID = d.ContractID";*/
		/*if (DB_num_rows($result)==0){
			prnMsg(_('The contract reference entered does not exist as a customer ordered contract. This contract cannot be charged to'),'error');
			$InputError =true;
		} //end if the contract ref entered is not a valid contract
	}//end if a contract ref was entered manually
	if (!is_numeric(filter_number_format($_POST['Amount']))){
		prnMsg(_('The amount entered is not numeric. This contract charge cannot be added to the invoice'),'error');
		$InputError = True;
	}*/

	//if ($InputError == False){
		/*$_SESSION['SuppTrans']->Add_Contract_To_Trans($_POST['ContractRef'],
														filter_number_format($_POST['Amount']),
														$_POST['Narrative'],
														$_POST['AnticipatedCost']);
		unset($_POST['ContractRef']);
		unset($_POST['Amount']);
		unset($_POST['Narrative']);*/
	/****************************************************************/


/****************************************************************/

	//}
}

if (isset($_GET['Delete'])){
	$_SESSION['SuppTrans']->Remove_Contract_From_Trans($_GET['Delete']);
}

/*Show all the selected ContractRefs so far from the SESSION['SuppInv']->Contracts array */
if ($_SESSION['SuppTrans']->InvoiceOrCredit=='Invoice'){
		echo '<div class="centre">
				<p class="page_title_text">' . _('Contract charges on Invoice') . ' ';
} else {
		echo '<div class="centre">
				<p class="page_title_text">' . _('Contract credits on Credit Note') . ' ';
}

echo  $_SESSION['SuppTrans']->SuppReference . ' ' ._('From') . ' ' . $_SESSION['SuppTrans']->SupplierName;

echo '</p></div>';

echo '<table class="selection">';
$TableHeader = '<tr>
					<th class="ascending">' . _('Contract') . '</th>
					<th class="ascending">' . _('Contract No.') . '</th>
					<th class="ascending">' . _('Amount') . '</th>
					<th width="480px" class="ascending">' . _('Narrative') . '</th>
					<th class="ascending">' . _('Anticipated') . '</th>
				</tr>';
echo $TableHeader;

$TotalContractsValue = 0;

foreach ($_SESSION['SuppTrans']->Contracts as $EnteredContract){

	/*if  ($EnteredContract->AnticipatedCost==true) {
		$AnticipatedCost = _('Yes');
	} else {
		$AnticipatedCost = _('No');
	}*/
	echo '<tr>
			<td>' . $EnteredContract->ContractRef  . '</td>
			<td>' . $EnteredContract->ContractNo  . '</td>
			<td class="number">' . locale_number_format($EnteredContract->Amount,$_SESSION['CompanyRecord']['decimalplaces']) . '</td>
			<td>' . $EnteredContract->Narrative . '</td>
			<td>' . $EnteredContract->AnticipatedCost . '</td>
			<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Delete=' . $EnteredContract->Counter . '">' . _('Delete') . '</a></td>
		</tr>';

	$TotalContractsValue += $EnteredContract->Amount;

}

echo '</table><table class="selection"><tr>
		<td class="number">' . _('Total') . ':</td>
		<td class="number">' . locale_number_format($TotalContractsValue,$_SESSION['CompanyRecord']['decimalplaces']) . '</td>
	</tr>
	</table>';

if ($_SESSION['SuppTrans']->InvoiceOrCredit == 'Invoice'){
	echo '<br />
			<a href="' . $RootPath . '/SupplierInvoice.php">' . _('Back to Invoice Entry') . '</a>
			<hr />';
} else {
	echo '<br />
			<a href="' . $RootPath . '/SupplierCredit.php">' . _('Back to Credit Note Entry') . '</a>
			<hr />';
}

/*Set up a form to allow input of new Contract charges */
echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

if (!isset($_POST['ContractRef'])) {
	$_POST['ContractRef']='';
}
echo '<table class="selection">';
echo '<tr>';
	 echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" id="form">';
	echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo "<td>&nbsp;Select Contract:</td><td>";
	echo '<select class="field small-field" required="true" onchange="this.form.submit();" name="Contract">';
	echo '<option selected="selected" value="">--Please Select A Contract--</option>';
	$SQL = "SELECT * ,b.status FROM contract_details a
	       INNER JOIN contract_payment b ON a.ContractID=b.ContractID
		   WHERE b.status=0
		   GROUP BY a.ContractID";
	$Query = DB_query($SQL);
	$Num_Rows = DB_num_rows($Query);
	while($roows=DB_fetch_array($Query)){
	$Contract_name=$roows['Contract_Name'];
	$Contract_ID=$roows['ContractID'];
	$Contract_number=$roows['Contract_Number'];
	echo "<option value=".$Contract_ID."";?><?=$Contract_ID == ''.$_POST['Contract'].'' ? ' selected="selected"' : '';?><?php echo ">".$Contract_number."-".$Contract_name."</option>";
	}
	echo "</select></td>";
	$contract_id=$_POST['Contract'];
	$qry = DB_query("SELECT * FROM contract_payment WHERE ContractID='".$contract_id."' AND status=0");
	//$myrow2 = DB_fetch_assoc($qry);
	
	echo'<table style="width:70%;" class="selection">';
	echo'<tr>
     	 <th></th>
		 <th>Payment ID</th>
		 <th>Description</th>
		 <th>Amount</th>
		 <th>Date</th>';
	echo'</tr>';
	$k=0; //row colour counter
	while($myrow2=DB_fetch_array($qry)){
	if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k++;
		}	
	/*$PaymentID=;
	$Description=$myrow2['Description'];
	$Amount_Paid=$myrow2['Amount_Paid'];	
	$Description=;	
	$Date_Paid=$myrow2['Date_Paid'];*/
	$total_Amount+=$myrow2['Amount_Paid'];
	
	 echo'<td><input name="Select[]" type="checkbox" value="'. $myrow2['PaymentID'] .'" /></td>
	     <td>'.$myrow2['PaymentID'].'</td>
		 <td>'.$myrow2['Description'].'</td>
		 <td>'.locale_number_format($myrow2['Amount_Paid'],$_SESSION['CompanyRecord']['decimalplaces']).'</td>
		 <td>'.$myrow2['Date_Paid'].'</td>';
	echo'</tr>';	
	    }
   echo'</tr>
		 <td></td>
		 <td></td>
		 <td><b>Total Amount</b></td>
		 <td><b>'.locale_number_format($total_Amount,$_SESSION['CompanyRecord']['decimalplaces']).'</b></td>
		 <td></td>
		 <td></td>';
	echo'</tr>
	</table>';
/*echo'<table>';
		echo'<tr>
			<td>' . _('Contract Reference') . ':</td>
			<td><input type="text" name="ContractRef" size="22" maxlength="20" value="' .  $_POST['ContractRef'] . '" /></td>
		</tr>';


if (!isset($_POST['Amount'])) {
	$_POST['Amount']=0;
}
if (!isset($_POST['Narrative'])) {
	$_POST['Narrative']='';
}
echo '<tr>
		<td>' . _('Amount') . ':</td>
		<td><input type="text" class="number" pattern="(?!^[-]?0[.,]0*$).{1,11}" title="'._('Amount must be numeric').'" placeholder="'._('Non zero amount').'" name="Amount" size="12" maxlength="11" value="' .  locale_number_format($total_Amount,$_SESSION['CompanyRecord']['decimalplaces']) . '" /></td>
	</tr>';
echo '<tr>
		<td>' . _('Narrative') . ':</td>
		<td><input type="text" name="Narrative" size="42" maxlength="40" value="' .  $_POST['Narrative'] . '" /></td>
	</tr>';
echo '<tr>
		<td>' . _('Anticipated Cost') . ':</td>
		<td>';
if (isset($_POST['AnticipatedCost']) AND $_POST['AnticipatedCost']==1){
	echo '<input type="checkbox" name="AnticipatedCost" checked />';
} else {
	echo '<input type="checkbox" name="AnticipatedCost" />';
}

echo '</td>
	</tr>
	
	</table>';*/

echo '<div class="centre"><input type="submit" name="AddContractChgToInvoice" value="' . _('Enter Contract Charge') . '" /></div>';

echo '</div>
      </form>';
include('includes/footer.inc');
?>
