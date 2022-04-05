<?php
/* $Id: ReprintGrn.php 4486 2011-02-08 09:20:50Z daintree $*/

include('includes/session.inc');
$Title=_('Reprint a Receipt');
include('includes/header.inc');

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/supplier.png" title="' . $Title . '" alt="" />' . ' ' . $Title . '</p>';

if (!isset($_POST['PONumber'])) {
	$_POST['PONumber']='';
}

echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<table class="selection">
		<tr>
			<th colspan="2"><h3>' . _('Select a Receipt') . '</h3></th>
		</tr>
		<tr>
			<td>' . _('Enter a Receipt Number') . '</td>
			<td>' . '<input type="text" name="PONumber" class="number" size="7" value="'.$_POST['PONumber'].'" /></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: center"><input type="submit" name="Show" value="' . _('Show Receipt') . '" /></td>
		</tr>
	</table>
    <br />
    </div>
	</form>';

if (isset($_POST['Show'])) {
	if ($_POST['PONumber']=='') {
		echo '<br />';
		prnMsg( _('You must enter a Receipt number in the box above'), 'warn');
		include('includes/footer.inc');
		exit;
	}
	$sql="SELECT count(transno)
				FROM debtortrans
				WHERE type=12 AND transno=" . $_POST['PONumber'] ."";
	$result=DB_query($sql);
	$myrow=DB_fetch_row($result);
	if ($myrow[0]==0) {
		echo '<br />';
		prnMsg( _('This Receipt does not exist on the system. Please try again.'), 'warn');
		include('includes/footer.inc');
		exit;
	}

	$sql="SELECT debtortrans.trandate,
							debtortrans.ovamount,
							debtortrans.ovdiscount,
							debtortrans.ovgst,
							debtortrans.transno,
							invtext
						FROM debtortrans WHERE debtortrans.type=12
						AND debtortrans.transno='" . $_POST['PONumber'] . "'";
	$result=DB_query($sql);
	if (DB_num_rows($result)==0) {
		echo '<br />';
		prnMsg( _('There are no receipt that can be reprinted.'), 'warn');
		include('includes/footer.inc');
		exit;
	}
	$k=0;
	echo '<br />
			<table class="selection">
			<tr>
				<th colspan="8"><h3>' . _('Receipt No') .' ' . $_POST['PONumber'] . '</h3></th>
			</tr>
			<tr>
				<th>' . _('Customer') . '</th>
				<th>' . _('Date') . '</th>
				<th>' . _('Receipt Number') . '</th>
				<th>' . _('Amount') . '</th>
				<th>' . _('Action') . '</th>
			</tr>';

	while ($myrow=DB_fetch_array($result)) {
	
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}
		echo '<td>' . $myrow['invtext'] . '</td>
			<td>' . $myrow['trandate'] . '</td>
			<td class="number">' . $_POST['PONumber'] . '</td>
			<td class="number">' . locale_number_format($myrow['ovamount']+$myrow['ovgst'], 2) . '</td>
			<td><a target="_blank" href="' . $RootPath . '/PDFReceipt.php?BatchNumber=' . $myrow['transno']. '&ReceiptNumber=1">' . _('Reprint Receipt ') . '</a></td>
		</tr>';
	}
	echo '</table>';
}

include('includes/footer.inc');

?>