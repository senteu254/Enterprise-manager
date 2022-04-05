<?php

/* $Id: SecurityTokens.php 4424 2010-12-22 16:27:45Z tim_schofield $*/

include('includes/session.inc');
$Title = _('Payment Voucher Processing');

include('includes/header.inc');
echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/maintenance.png" title="' .
		_('Print') . '" alt="" />' . ' ' . $Title . '</p>';
		
############################################################################################
if (isset($_POST['Go1']) OR isset($_POST['Go2'])) {
	$_POST['PageOffset'] = (isset($_POST['Go1']) ? $_POST['PageOffset1'] : $_POST['PageOffset2']);
	$_POST['Go'] = '';
}
if (!isset($_POST['PageOffset'])) {
	$_POST['PageOffset'] = 1;
} else {
	if ($_POST['PageOffset'] == 0) {
		$_POST['PageOffset'] = 1;
	}
}

if (isset($_GET['SelectedUser'])){
	$SelectedUser = $_GET['SelectedUser'];
} elseif (isset($_POST['SelectedUser'])){
	$SelectedUser = $_POST['SelectedUser'];
}
if (isset($_POST['Submit'])) {
$InputError = 0;
$pv=$_POST['pv'];
$N = count($pv);
for($i=0; $i < $N; $i++)
{
$sql = "UPDATE payment_voucher SET process_level=3 WHERE voucherid = '". $pv[$i] . "'";

		$result = DB_query($sql);
$SQL = "SELECT voucherno FROM  payment_voucher_approval where voucherno='".$pv[$i]."' and process_level=3";
$resu=DB_query($SQL);
  if(DB_num_rows($resu) ==0){
$sqlq = "INSERT INTO payment_voucher_approval (voucherno,
						process_level,
						approver)
					VALUES ('" . $pv[$i] . "',
						'3',
						'" . $_SESSION['UserID'] ."')";
		$resulta = DB_query($sqlq);
	}		
	}
	prnMsg( _('The selected Payment Voucher Has been Certified and Forwarded for Authoritation'), 'success' );
	unset($_POST['approve']);
	}
	echo '<div id="popDiv" style="z-index: 999;
									width: 100%;
									height: 100%;
									top: 0;
									left: 0;
									display: none;
									position: absolute;				
									background-color: #fff;
									background-color: rgba(255,255,255,0.7);
									filter: alpha(opacity = 50);">';
	
	
	echo '<table style="width: 300px;
						height: 200px;
						position: absolute;
						color: #000000;
						/* To align popup window at the center of screen*/
						top: 50%;
						left: 50%;
						margin-top: -100px;
						margin-left: -150px;">
			<tr>
				<th>' . _('Please Leave a Reason for Decline') . ':</th>
			</tr>';
			echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" id="form">';
			echo '<tr>
				<td>
				<textarea required name="comment" cols="30" rows="4"></textarea>
				<input type="hidden" name="PV" value="' . $_GET['SelectedUser'] . '" />
				<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
				</td>
			</tr>
			<tr>
				<td><input type="submit" name="Decline" value="' . _('Submit') . '" />
				</form>';
	echo "<input type=submit onclick=hide('popDiv') value=" . _('Cancel') . " />";
	echo '</td>
			</tr>
			</table>';
	
	echo '</div>';		
	
if (isset($_POST['Decline'])) {
$InputError = 0;

if (isset($_POST['PV'])) {
$comment=$_POST['comment'];
$sql = "UPDATE payment_voucher SET process_level=11, comment='$comment' WHERE voucherid = '". $_POST['PV'] . "'";

		$result = DB_query($sql);
		prnMsg( _('The selected Payment Voucher Has been Declined'), 'success' );
		
	
	}
	unset($_POST['approve']);
	}

######################################################################################
if (isset($_GET['view'])) {
	//editing an existing User

	$sql = "SELECT voucherid,
					authorityref,
					datereq,
					label,
					payeename,
					particulars,
					lpo_no,
					invoice_no,
					amount,
					total,
					process_level
		FROM payment_voucher
		WHERE voucherid='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['voucherid'] = $myrow['voucherid'];
	$_POST['arefno'] = $myrow['authorityref'];
	$_POST['datereq'] = $myrow['datereq'];
	$_POST['label'] = $myrow['label'];
	$_POST['name']	= $myrow['payeename'];
	$pat  = unserialize($myrow['particulars']);
	$lpono = unserialize($myrow['lpo_no']);
	$invoiceno = unserialize($myrow['invoice_no']);
	$amount = unserialize($myrow['amount']);
	$_POST['total'] = $myrow['total'];
	
echo '<a href="' . $RootPath . '/Process_VB_PV.php">' . _('Back to Payment Voucher') . '</a>';
echo '<table style="background:none repeat scroll 0% 0% #F1F1F1; border:none; box-shadow:none;">
      <tr><td>';
echo '<table class="selection">
      <tr><td>';

	echo '<table class="selection">
			<tr>
				<td>' . _('Voucher ID') . ':</td>
				<th>' . $_POST['voucherid'] . '</th>
			</tr>';


	echo '<td>' . _('Authority Ref No.') . '</td>
			<td>'.$_POST['arefno'].'</td>
		</tr>
		<td>' . _('Date Raised') . '</td>
			<td>'.$_POST['datereq'].'</td>
		</tr>
		<td>' . _('Label') . '</td>
			<td>'.$_POST['label'].'</td>
		</tr>
		<tr>
		<td>' .  _('Payee Name') . '</td>
		<td>'.$_POST['name'] .'</td>
		<td>';

echo '</td>
	</tr>
	</table>

	<br />';

echo '</div>';

echo '<table id="dataTable" class="selection">';
echo '<tr>
		<th>' .  _('Particulars')  . '</th>
		<th>' .  _('LPO No'). '</th>
		<th>' .  _('Invoice No'). '</th>
		<th>' .  _('Amount'). '</th>
	</tr>';
	
for($i=0;$i<=count($lpono);$i++)
{
	$particulars = $pat[$i];
	$lpo = $lpono[$i];
	$invoice = $invoiceno[$i];
	$amnt = $amount[$i];

	echo '<tr>
			
			<td>'.$particulars.'</td>
			<td>'.$lpo.'</td>
			<td>'.$invoice.'</td>
			<td>'.$amnt.'</td>
		 
		 </tr>';
		}
		echo '<tr align="right"><td></td><td></td><td>' .  _('Total Amount'). '</td><th>' .$_POST['total'].'</th></tr>';

echo '</table>';
if ($myrow['process_level'] ==2) {
		echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" id="form">
			<input type="submit" name="Submit" value="' . _('Approve') . '" />';
		echo'<input type="hidden" name="pv[]" value="' . $_GET['SelectedUser'] . '" />
			<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
		echo "<a href=# onclick=pop('popDiv')><img src='".$RootPath."/css/decline.png'/> </a>";
		echo '</form>';
		}else{
		echo '';
		}

    include('includes/Level_Tracking.php');

	echo '</td>	</tr></table>';
	echo '</td>
	<td valign="top" class="status">
	<div style="background:url(css/status.png) left top no-repeat; height:230px; width:220px;">
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label.' &nbsp;Bills</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label2.' &nbsp;Procurement &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Certificate</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label3.' &nbsp;AIE Holder</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label4.' &nbsp;Internal Audit</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label5.' &nbsp;Examination</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label6.' &nbsp;Cash Payment</div>
	
	
	</div>
	</td>
	</tr>
	</table>';
	}else{
######################################################################################

	echo '<form action="" method="post">';
	
	echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';
	
	echo '<table><tr>
		<td>' . _('Book Ref No.') . ':</td>
		<td><input name="ref" value="'. $_POST['ref'] .'" type="text" /></td>
         <td>Financial Year End</td><td><select name="Yearend">';
		 if(isset($_POST['Yearend']) && $_POST['Yearend']==Date('y',YearEndDate($_SESSION['YearEnd'],-2)).'-'.Date('y',YearEndDate($_SESSION['YearEnd'],-1))){
		echo'<option selected value="' .  Date('y',YearEndDate($_SESSION['YearEnd'],-2)).'-'.Date('y',YearEndDate($_SESSION['YearEnd'],-1)) . '">' .  Date('y',YearEndDate($_SESSION['YearEnd'],-2)).'-'.Date('y',YearEndDate($_SESSION['YearEnd'],-1)) . '</option>'; 
		echo'<option value="'.  Date('y',YearEndDate($_SESSION['YearEnd'],-1)).'-'.Date('y',YearEndDate($_SESSION['YearEnd'],0)) . '">' .Date('y',YearEndDate($_SESSION['YearEnd'],-1)).'-'.Date('y',YearEndDate($_SESSION['YearEnd'],0)) . '</option>';
		 }else{
		  echo'<option value="' .  Date('y',YearEndDate($_SESSION['YearEnd'],-2)).'-'.Date('y',YearEndDate($_SESSION['YearEnd'],-1)) . '">' .  Date('y',YearEndDate($_SESSION['YearEnd'],-2)).'-'.Date('y',YearEndDate($_SESSION['YearEnd'],-1)) . '</option>';
		  echo'<option selected value="'.  Date('y',YearEndDate($_SESSION['YearEnd'],-1)).'-'.Date('y',YearEndDate($_SESSION['YearEnd'],0)) . '">' .Date('y',YearEndDate($_SESSION['YearEnd'],-1)).'-'.Date('y',YearEndDate($_SESSION['YearEnd'],0)) . '</option>';
			}

	echo '</select></td>';
		echo'	<td></td><td>
			<input name="Search" type="submit" value="Search" />
			<td>';
	   
	    echo' </tr></table>'; 
		//////////////////////////////////////
		 echo '</form>';
		
		 if(isset($_POST['Search'])){
		 
		if(isset($_POST['ref']) && $_POST['ref'] !=""){
		 $sort=" AND authorityref " . LIKE . " '%". $_POST['ref'] ."%' AND authorityref " . LIKE . " '%". $_POST['Yearend'] ."%'";
		 }else{
		 $sort =" AND authorityref " . LIKE . " '%". $_POST['Yearend'] ."%'";
		 }		
		 }
		  
######################################################################################
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" id="form">';
if (!isset($SelectedUser)) {
	$sql = "SELECT voucherid,
					authorityref,
					datereq,
					label,
					payeename,
					particulars,
					lpo_no,
					invoice_no,
					amount,
					process_level,
					total
				FROM payment_voucher WHERE process_level >=2 AND process_level<14 
				".$sort."
				ORDER BY process_level ASC, voucherid DESC";
	$result = DB_query($sql);
	
	$ListCount = DB_num_rows($result);
	$ListPageMax = ceil($ListCount / ($_SESSION['DisplayRecordsMax']*2));
	if (isset($_POST['Next'])) {
			if ($_POST['PageOffset'] < $ListPageMax) {
				$_POST['PageOffset'] = $_POST['PageOffset'] + 1;
			}
		}
		if (isset($_POST['Previous'])) {
			if ($_POST['PageOffset'] > 1) {
				$_POST['PageOffset'] = $_POST['PageOffset'] - 1;
			}
		}
		echo '<input type="hidden" name="PageOffset" value="' . $_POST['PageOffset'] . '" />';
		if ($ListPageMax > 1) {
			echo '<br /><div class="centre">&nbsp;&nbsp;' . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';
			echo '<select name="PageOffset1">';
			$ListPage = 1;
			while ($ListPage <= $ListPageMax) {
				if ($ListPage == $_POST['PageOffset']) {
					echo '<option value="' . $ListPage . '" selected="selected">' . $ListPage . '</option>';
				} else {
					echo '<option value="' . $ListPage . '">' . $ListPage . '</option>';
				}
				$ListPage++;
			}
			echo '</select>
				<input type="submit" name="Go1" value="' . _('Go') . '" />
				<input type="submit" name="Previous" value="' . _('Previous') . '" />
				<input type="submit" name="Next" value="' . _('Next') . '" />';
			echo '</div>';
		}
		}
	echo '<table class="selection">';
	echo '<tr><th>' . _('Voucher ID') . '</th>
				<th>' . _('Ref No') . '</th>
				<th>' . _('Label') . '</th>
				<th>' . _('Date Raised') . '</th>
				<th>' . _('Payee Name') . '</th>
				<th>' . _('Amount') . '</th>
				<th>' . _('Authorize') . '</th>
				<th>&nbsp;</th>
			</tr>';

	$k=0; //row colour counter
	DB_data_seek($result, ($_POST['PageOffset'] - 1) * ($_SESSION['DisplayRecordsMax']*2));
	while (($myrow = DB_fetch_array($result)) AND ($RowIndex <> ($_SESSION['DisplayRecordsMax']*2))) {
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}

	if ($myrow['datereq']=='') {
		$LastVisitDate = Date($_SESSION['DefaultDateFormat']);
	} else {
		$LastVisitDate = $myrow['datereq'];
	}

		/*The SecurityHeadings array is defined in config.php */
		if ($myrow['process_level'] ==2) {
		$check='<th><input name="pv[]" type="checkbox" value="'. $myrow['voucherid'] .'" /></th>';
		}elseif ($myrow['process_level'] ==11){
		$check='<th><img src="'.$RootPath.'/css/red.png"/></th>';
		}else{
		$check='<th><img src="'.$RootPath.'/css/ok.png"/></th>';
		}

		printf('<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					'.$check.'
					<td><a href="%s&amp;SelectedUser=%s&amp;view=1">' . _('View') . '</a></td>
					</tr>',
					$myrow['voucherid'],
					$myrow['authorityref'],
					$myrow['label'],
					$LastVisitDate,
					$myrow['payeename'],
					$myrow['total'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?',
					$myrow['voucherid'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?',
					$myrow['voucherid'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?',
					$myrow['voucherid']);
	
	$RowIndex++;
	
	} //END WHILE LIST LOOP
	echo '</table>';
	if (isset($ListPageMax) AND $ListPageMax > 1) {
		echo '<br /><div class="centre">&nbsp;&nbsp;' . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';
		echo '<select name="PageOffset2">';
		$ListPage = 1;
		while ($ListPage <= $ListPageMax) {
			if ($ListPage == $_POST['PageOffset']) {
				echo '<option value="' . $ListPage . '" selected="selected">' . $ListPage . '</option>';
			} //$ListPage == $_POST['PageOffset']
			else {
				echo '<option value="' . $ListPage . '">' . $ListPage . '</option>';
			}
			$ListPage++;
		} //$ListPage <= $ListPageMax
		echo '</select>
			<input type="submit" name="Go2" value="' . _('Go') . '" />
			<input type="submit" name="Previous" value="' . _('Previous') . '" />
			<input type="submit" name="Next" value="' . _('Next') . '" />';
		echo '</div>';
	}//end if results to show
	echo '<br /><input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<input type="submit" name="Submit" value="' . _('Update') . '" />';
	}
	
	
echo '</form>';
include('includes/footer.inc');
?>
		<script type="text/javascript">
			function pop(div) {
				document.getElementById(div).style.display = 'block';
			}
			function hide(div) {
				document.getElementById(div).style.display = 'none';
			}
			//To detect escape button
			document.onkeydown = function(evt) {
				evt = evt || window.event;
				if (evt.keyCode == 27) {
					hide('popDiv');
				}
			};
		</script>