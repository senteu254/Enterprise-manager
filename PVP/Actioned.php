	
<?php	
############################################################################################
$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
 
$per_page = 30; // Set how many records do you want to display per page.
$startpoint = ($page * $per_page) - $per_page;


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
$sql = "UPDATE payment_voucher SET process_level=process_level+1 WHERE voucherid = '". $pv[$i] . "'";

		$result = DB_query($sql);
$SQL = "SELECT voucherno FROM  payment_voucher_approval where voucherno='".$pv[$i]."' and process_level=3";
$resu=DB_query($SQL);
  if(DB_num_rows($resu) ==0){
$sqlq = "INSERT INTO payment_voucher_approval (voucherno,
						process_level,
						approver)
					VALUES ('" . $pv[$i] . "',
						'',
						'" . $_SESSION['UserID'] ."')";
		$resulta = DB_query($sqlq);
	}		
	}
	prnMsg( _('The selected Payment Voucher Has been Certified and Forwarded for Processing'), 'success' );
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
			echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=Actioned_PV" id="form">';
			echo '<tr>
				<td>
				<textarea required name="comment" cols="30" rows="4"></textarea>
				<input type="hidden" name="PV" id="pvs" value="' . $_GET['SelectedUser'] . '" />
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

if (isset($_POST['PV']) && $_POST['PV'] !="") {
$sql="SELECT process_level FROM payment_voucher WHERE voucherid='" . $_POST['PV'] . "'";
$result = DB_query($sql);
$myrow = DB_fetch_array($result);

$comment=$_POST['comment'];
$sql = "UPDATE payment_voucher SET process_level=1".$myrow['process_level'].", comment='$comment' WHERE voucherid = '". $_POST['PV'] . "'";

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
					process_level,
					tax,
					chequeNo
		FROM payment_voucher
		WHERE voucherid='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['voucherid'] = $myrow['voucherid'];
	$_POST['arefno'] = $myrow['authorityref'];
	$_POST['datereq'] = $myrow['datereq'];
	$_POST['label'] = $myrow['label'];
	$_POST['chequeNo'] = $myrow['chequeNo'];
	$_POST['name']	= $myrow['payeename'];
	$pat  = unserialize($myrow['particulars']);
	$lpono = unserialize($myrow['lpo_no']);
	$invoiceno = unserialize($myrow['invoice_no']);
	$amount = unserialize($myrow['amount']);
	$_POST['total'] = $myrow['total'];
	$taxed = $myrow['tax'];
	
echo '<a href="' . $RootPath . '/index.php?Application=PVM&Ref=default&Link=Actioned_PV"><button type="button" name="" class="button"><i class="fa fa-reply"></i> ' . _('Go Back') . '</button></a>';

echo'<div align="center">';
echo'</div>';
echo'<br>';
echo '<table style="background-color:rgb(241, 241, 241);">
      <tr><td>';
echo '<table class="selection">
      <tr><td>';

	echo '<table style="width:100%;background-color: white;">
			<tr>
				<td>' . _('Voucher ID') . ':</td>
				<td>' . $_POST['voucherid'] . '</td>
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
		<tr>
		<tr>
		<td>' .  _('Cheque No.') . '</td>
		<td>'.$_POST['chequeNo'] .'</td>
		</tr>';

echo '</td>
	</tr>
	</table>

	<br />';

echo '</div>';

echo '<table id="dataTable" class="selection" style="width:450px;background-color: white;">';
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
		################################################################6% witholding##################################################
		$total = str_replace(',','',$_POST['total']);
		if($taxed ==1){
		$TAX = (6/116*$total);
		echo '<tr align="right"><td colspan="3" style="text-align:right">' .  _('PAYEE 1 ['.$_POST['name'].']'). '</td><th>' .number_format($total-$TAX,2).'</th></tr>';
		echo '<tr align="right"><td colspan="3" style="text-align:right">' .  _('PAYEE 2 [WITHOLDING TAX(6%)]'). '</td><th>' .number_format($TAX,2).'</th></tr>';
		}
		echo '<tr align="right"><td colspan="3" style="text-align:right">' .  _('TOTAL AMOUNT PAYABLE'). '</td><th>' .number_format($total,2).'</th></tr>';
        ################################################################End 6% witholding###############################################
echo '</table>';
   include('includes/Level_Tracking.php');

	echo '</td>	</tr></table>';
	echo '</td>
	<td valign="top" class="status">
	<td valign="top" class="status">
	<div style="background:url(css/statuspv.png) left top no-repeat; height:315px; width:220px;">
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label.' &nbsp;VBC Certificate</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label2.' &nbsp;Procurement &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Certificate</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label3.' &nbsp;AIE Holder</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label4.' &nbsp;Internal Audit</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label5.' &nbsp;Examination</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label6.' &nbsp;Finance</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label7.' &nbsp;Cash Payment</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label8.' &nbsp;MD</div>
	<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label9.' &nbsp;Payment</div>';
	//echo'<div style="padding-left:70px; padding-top:15px; font-weight:bold;">'.$label9.' &nbsp;FM</div>
	
	echo'</div>
	</td>
	</tr>
	</table>';
	}else{
######################################################################################

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=Actioned_PV" id="form">';
	
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
		 $sort=" AND payment_voucher.authorityref " . LIKE . " '%". $_POST['ref'] ."%' AND payment_voucher.authorityref " . LIKE . " '%". $_POST['Yearend'] ."%' GROUP BY payment_voucher.uniqueid";
		 }else{
		 $sort =" AND payment_voucher.authorityref " . LIKE . " '%". $_POST['Yearend'] ."%'  ";
		 }		
		 }
		  
######################################################################################
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=Actioned_PV" id="form">';
if (!isset($SelectedUser)) {
 $sqlforPages = " payment_voucher,pvroles
				WHERE payment_voucher.process_level>pvroles.level 
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				".$sort."";
				
	    $sql = "SELECT  * FROM payment_voucher,pvroles
				WHERE payment_voucher.process_level>pvroles.level 
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				".$sort."
				ORDER BY payment_voucher.uniqueid DESC LIMIT {$startpoint} , {$per_page}";
	$result = DB_query($sql);
	
	echo pagination($sqlforPages,$per_page,$page,$url='?Application=PVM&Ref=default&Link=Actioned_PV&');
		}
		?>
	<table style="width:100%; font-size:12px;" class="selection table table-hover">
	<tr style="height:30px;">
				
				<th>Date Raised</th>
				<th>Priority</th>
				<th style="width:130px;">Book No.</th>
				<th>Payee Name</th>
				<th>Amount</th>
				<th>Action</th>
			</tr>
<?php
	$k=0; //row colour counter
	if (DB_num_rows($result) == 0) {
		echo '<tr><td colspan="8"><center style="color:#FF0000"><strong>No Records Found</strong></center></td></tr>';
	}else{
	DB_data_seek($result, ($_POST['PageOffset'] - 1) * ($_SESSION['DisplayRecordsMax']));
	while (($myrow = DB_fetch_array($result)) AND ($RowIndex <> ($_SESSION['DisplayRecordsMax']))) {
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
$VoucherNo=$myrow['voucherid'];
echo '<form method="post" name="form" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=Actioned_PV" id="form">';
        $decl="<a href=# onclick=pop('popDiv',".$myrow['voucherid'].")><img src='".$RootPath."/css/decline.png'/> </a>";
         $app='<th><input type="submit" name="Submit" value="' . _('Approve') . '" />';
		 $print='<td><a href="' . $RootPath . '/PDFprintPV.php?voucher='.$VoucherNo.'"><span style="color:white;font-size:12px;" class="label label-default">Print</span></a></td>';
		

		printf('<td>%s</td>
		            <td>%s</td>
					<td><a href="%s&amp;SelectedUser=%s&amp;view=1">' . $myrow['authorityref']. '</a></td>
					<td>%s</td>
					<td>%s</td>
					'.$print.'
					'.$payment.'			
					</tr>',
					$LastVisitDate,
					'<span class="label2 label-'.(($myrow['label']=="Urgent" or $myrow['label']=="Emergency")? 'danger':'success').'">'.$myrow['label'].'</span>',
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?Application=PVM&Ref=default&Link=Actioned_PV',
					$myrow['voucherid'],
					$myrow['payeename'],
					$myrow['total'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?Application=PVM&Ref=default&Link=Actioned_PV',
					$myrow['voucherid'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=Actioned_PV',
					$myrow['voucherid']);
	
	$RowIndex++;
	
	} //END WHILE LIST LOOP
	}
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
	//echo '<center><input type="submit" name="Submit" value="' . _('Update') . '" /<center>';
	}
	
	
echo '</form>';
echo '</form>';
?>
</div>
		<script type="text/javascript">
			function pop(div,id) {
				document.getElementById(div).style.display = 'block';
				document.getElementById('pvs').value = id;
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
