<?php

/* $Id: ReverseGRN.php 7021 2014-12-14 02:04:44Z tehonu $*/

include('includes/DefineSerialItems.php');
include('includes/SQL_CommonFunctions.inc');
include('includes/session.inc');

$Title = _('Reverse Customer Payment');

include('includes/header.inc');

if ((isset($_SESSION['CustomerID']) AND $_SESSION['CustomerID']!='')
	OR (!isset($_POST['CustomerID']) OR $_POST['CustomerID'])==''){

	$_POST['CustomerID']=$_SESSION['CustomerID'];

}

if (!isset($_POST['CustomerID']) OR $_POST['CustomerID']==""){
	echo '<br />' . _('This page is expected to be called after a customer has been selected');
	echo '<meta http-equiv="Refresh" content="0; url=' . $RootPath . '/SelectCustomer.php">';
	exit;
} elseif (!isset($_POST['name']) or $_POST['name']=="") {
	$sql = "SELECT name FROM debtorsmaster WHERE debtorno='" . $_SESSION['CustomerID'] . "'";
	$SuppResult = DB_query($sql, _('Could not retrieve the customer name for') . ' ' . $_SESSION['CustomerID']);
	$SuppRow = DB_fetch_row($SuppResult);
	$_POST['name'] = $SuppRow[0];
}

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/supplier.png" title="' . _('Sales') . '" alt="" />' . ' ' . _('Reverse Payment from') . ' ' . $_POST['name'] .  '</p> ';
############################################################Reverse payment######################################################################################
if (isset($_GET['TRANSno']) AND isset($_POST['CustomerID'])){
$QtyToReverse=2;
	
$SQL5 = "SELECT * FROM custallocns
            INNER JOIN debtortrans ON debtortrans.id=custallocns.transid_allocfrom
			WHERE custallocns.transid_allocfrom=".$_GET['TRANSno']."";
	$Result5=DB_query($SQL5);
	$TRANS5 = DB_fetch_array($Result5);
	$transid = $TRANS5['transid_allocto'];
	$amnt = $TRANS5['amt'];
/*Need to update or delete the existing GRN item */
	 $SQLr = "SELECT *,id FROM debtortrans 
			WHERE id='" . $transid . "' AND transtate=0";
	$Resultr=DB_query($SQLr,$ErrMsg,$DbgMsg);
	$TRANS = DB_fetch_array($Resultr);
	
	$allocto = $TRANS['alloc'];	
	
	$balance=$allocto-$amnt;
	$SQL3 = "UPDATE debtortrans SET transtate = '" .$QtyToReverse. "' WHERE id=".$_GET['TRANSno']." AND type=12";
	$Result3=DB_query($SQL3,$ErrMsg,$DbgMsg,true);	
	
	$SQL4 = "UPDATE custallocns SET status = '" .$QtyToReverse. "' WHERE transid_allocfrom=".$_GET['TRANSno']."";
	$Result4=DB_query($SQL4,$ErrMsg,$DbgMsg,true);	
	/*If the GRN being reversed is an asset - reverse the fixedassettrans record */
	$SQL = "UPDATE debtortrans SET alloc = '". $balance . "'  WHERE id = '" . $transid . "'";	
	$Result=DB_query($SQL,$ErrMsg,$DbgMsg,true);
	prnMsg( _('The selected Payment Has been Reversed'), 'success' );
	echo '<a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">' . _('Select Another Payment to Reverse') . '</a>';

##################################################################################################################################################
} else {
	echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

if (!isset($_POST['TransAfterDate']))  {
	$_POST['TransAfterDate'] = Date($_SESSION['DefaultDateFormat'], Mktime(0, 0, 0, Date('m') - $_SESSION['NumberOfMonthMustBeShown'], Date('d'), Date('Y')) );
}
if (!isset($_POST['TransToDate']))  {
	$_POST['TransToDate'] = Date($_SESSION['DefaultDateFormat'], Mktime(0, 0, 0, Date('m')) );
}
    echo '<input type="hidden" name="CustomerID" value="' . $_POST['CustomerID'] . '" />';
    echo '<input type="hidden" name="name" value="' . $_POST['name'] . '" />';
	echo '<table class="selection"><tr>';
	echo '<td>' . _('Show all transactions From'), ':<input alt="', $_SESSION['DefaultDateFormat'], '" class="date" id="datepicker" maxlength="10" minlength="0" name="TransAfterDate" required="required" size="12" tabindex="1" type="text" value="', $_POST['TransAfterDate'], '" />',_('To'), ':<input alt="', $_SESSION['DefaultDateFormat'], '" class="date" id="datepicker" maxlength="10" minlength="0" name="TransToDate" required="required" size="12" tabindex="1" type="text" value="', $_POST['TransToDate'], '" /></td>
		</tr>
		</table>
		<br />
		<div class="centre">
			<input type="submit" name="ShowPayment" value="' . _('Show All Payment') . '" />
		</div>';
    echo '</div>
          </form>';

	if (isset($_POST['ShowPayment'])){

		$sql = "SELECT *,a.id as custid FROM debtortrans a
		        LEFT JOIN custallocns b ON a.id=b.transid_allocfrom
				WHERE debtorno = '" . $_POST['CustomerID'] . "'				
				AND trandate>='" . FormatDateForSQL($_POST['TransAfterDate']) . "'
				AND trandate<='" . FormatDateForSQL($_POST['TransToDate']) . "'
				AND type=12
				AND transtate=0";

		$ErrMsg = _('An error occurred in the attempt to get the outstanding GRNs for') . ' ' . $_POST['SuppName'] . '. ' . _('The message was') . ':';
  		$DbgMsg = _('The SQL that failed was') . ':';
		$result = DB_query($sql,$ErrMsg,$DbgMsg);

		if (DB_num_rows($result) ==0){
			prnMsg(_('There are no payment for') . ' ' . $_POST['SuppName'] . '.<br />' . _('To reverse'),'warn');
		} else { //there are GRNs to show

			echo '<br /><table cellpadding="2" class="selection">';
			$TableHeader = '<tr>
								<th>' . _('Payment Type') . '</th>
								<th>' . _('Total Amount') . '</th>
								<th>' . _('Date Paid') . '</th>
								<th>' . _('Action') . '</th>
							</tr>';

			echo $TableHeader;

			/* show the GRNs outstanding to be invoiced that could be reversed */
			$RowCounter =0;
			$k=0;
			while ($myrow=DB_fetch_array($result)) {
				if ($k==1){
					echo '<tr class="EvenTableRows">';
					$k=0;
				} else {
					echo '<tr class="OddTableRows">';
					$k=1;
				}
                 $total=$myrow['ovamount']+$myrow['ovgst'];
				$DisplayQtyRecd = locale_number_format($myrow['qtyrecd'],'Variable');
				$DisplayTotal = locale_number_format($total);
				$DisplayQtyRev = locale_number_format($myrow['qtytoreverse'],'Variable');
				$DisplayDateDel = ConvertSQLDate($myrow['trandate']);
				$LinkToRevGRN = '<a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?TRANSno=' . $myrow['custid'] . '"onclick="return confirmDelete(this);">' . _('Reverse') . '</a>';

				printf('<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						</tr>',
						$myrow['reference'],
						$DisplayTotal,
						$DisplayDateDel,
						$LinkToRevGRN);

				$RowCounter++;
				if ($RowCounter >20){
					$RowCounter =0;
					echo $TableHeader;
				}
			}

			echo '</table>';

		}
	}
}
include ('includes/footer.inc');
?>
<script>
    function confirmDelete(link) {
        if (confirm("Are you sure you want to reverse this Payment?")) {
            doAjax(link.href, "POST"); // doAjax needs to send the "confirm" field
        }
        return false;
    }
</script>