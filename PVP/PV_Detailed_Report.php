	
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
			echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=PV_Detailed_Report" id="form">';
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
	$sql = "SELECT * FROM payment_voucher a
			INNER JOIN voteheadmaintenance b ON a.votehead = b.Votecode
			WHERE a.votehead='" . $SelectedUser . "'";

	$result455 = DB_query($sql);
	$myrow233 = DB_fetch_array($result455);
	  $_POST['Votehead'] = $myrow233['Votehead'];
	/*
    $_POST['votehead'] = $myrow['votehead'];
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
	$taxed = $myrow['tax'];*/
	
echo '<a href="' . $RootPath . '/index.php?Application=PVM&Ref=default&Link=PV_Detailed_Report"><button type="button" name="" class="button"><i class="fa fa-reply"></i> ' . _('Go Back') . '</button></a>
 <p><u><center><button style="width:100%;" class="btn btn-primary">Payment Vouchers paid through  ' . $_POST['Votehead']  . '</center></u></p>';
echo'<div align="center">';
echo'</div>';
echo'<br>';
echo '<table style="width:100%; font-size:12px;" class="selection table table-hover">
<tr>    <th>No.</th>
        <th>' .  _('Voucher ID')  . '</th>
		<th>' .  _('Date Raised')  . '</th>
		<th>' .  _('Ref No.'). '</th>
		<th>' .  _('Payee Name'). '</th>
		<th>' .  _('Amount'). '</th>
</tr>';
if (DB_num_rows($result455) == 0) {
		echo '<tr><td colspan="8"><center style="color:#FF0000"><strong>No Records Found</strong></center></td></tr>';
	}else{
	$i=0;
while ($myrow = DB_fetch_array($result455)) {
$i++;
       if ($k==1){
		echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}
	echo'<td>' . $i . '</td>
	     <td>' . $myrow['voucherid'] . '</td>
	     <td>' .  $myrow['datereq'] . '</td>
		 <td>' .  $myrow['authorityref'] . '</td>
		 <td>' . $myrow['payeename'] . '</td>
		 <td>' . $myrow['total'] . '</td>';
		echo'</tr>';
	}
	}
	echo'</table>';
	}else{
######################################################################################

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=PV_Detailed_Report" id="form">';
	
	echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';
	
		echo '<table><tr>
		<td>' . _('Book Ref No.') . ':</td>
		<td><input name="ref" value="'. $_POST['ref'] .'" type="text" /></td>';       
		echo'	<td></td><td>
			<input name="Search" type="submit" value="Search" />
			<td>';
	   
	    echo' </tr></table>'; 
		//////////////////////////////////////
	 echo '</form>';
		
		 if(isset($_POST['Search'])){
		 
	if(isset($_POST['ref']) && $_POST['ref'] !=""){
		 $sort=" AND voteheadmaintenance.Votehead " . LIKE . " '%". $_POST['ref'] ."%' AND voteheadmaintenance.Votehead " . LIKE . " '%". $_POST['Yearend'] ."%' ";
		 }else{
		 $sort =" AND voteheadmaintenance.Votehead " . LIKE . " '%". $_POST['Yearend'] ."%' ";
		 }		
		 }
		 
######################################################################################
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=PV_Detailed_Report" id="form">';
if (!isset($SelectedUser)) {
/* $sqlforPages = " payment_voucher,pvroles
				WHERE payment_voucher.process_level>pvroles.level 
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				".$sort."";*/
				
$sqlforPages = "voteheadmaintenance a
				LEFT JOIN funds_allocations b ON a.Votecode=b.votecode
				WHERE b.Financial_Year='".Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],+0))."'
				".$sort."";

$sql = "SELECT a.Votecode,
					a.Votehead,
					a.Vbook,
					b.Financial_Year,
					b.votecode,
					b.allocated_Fund,
					b.suppliementary	
				FROM voteheadmaintenance a
				LEFT JOIN funds_allocations b ON a.Votecode=b.votecode
				WHERE b.Financial_Year='".Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],+0))."'
				".$sort."
				ORDER BY b.votecode DESC LIMIT {$startpoint} , {$per_page}";	
							
	   /* $sql = "SELECT  * FROM payment_voucher,pvroles
				WHERE payment_voucher.process_level>pvroles.level 
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				".$sort."
				ORDER BY payment_voucher.uniqueid DESC LIMIT {$startpoint} , {$per_page}";*/
	$result = DB_query($sql);
				
	$result = DB_query($sql);
	
	echo pagination($sqlforPages,$per_page,$page,$url='?Application=PVM&Ref=default&Link=PV_Detailed_Report&');
		}
		?>
	<table style="width:110%; font-size:12px;" class="selection table table-hover">
	<tr style="height:30px;">
				
				<th>VoteCode</th>
				<th>VoteHead.</th>
				<th>Running Balance.</th>
				<th>View</th>
			</tr>
<?php
	$k=0; //row colour counter
	if (DB_num_rows($result) == 0) {
		echo '<tr><td colspan="8"><center style="color:#FF0000"><strong>No Records Found</strong></center></td></tr>';
	}else{
	DB_data_seek($result, ($_POST['PageOffset'] - 1) * ($_SESSION['DisplayRecordsMax']));
	while (($myrow = DB_fetch_array($result)) AND ($RowIndex <> ($_SESSION['DisplayRecordsMax']))) {
	$resultyy=DB_query("SELECT 
							 (SELECT SUM(COALESCE(a.commitments,0)) FROM commitment a WHERE a.voted_Item=b.votecode and b.Financial_Year=a.Fyear) AS commitments,
							 (SELECT SUM(COALESCE(a.decommitment,0)) FROM commitment a WHERE a.voted_Item=b.votecode and b.Financial_Year=a.Fyear) AS decom,
							 b.allocated_Fund,
							 b.votecode,
							 b.suppliementary,
		 					 b.voted_Item,
							 e.Votehead,
							 e.Votecode,
							 (SELECT SUM(COALESCE(c.amount,0)) FROM votepaymenttrans c WHERE c.VoteCode=b.votecode AND c.Fy=b.Financial_Year) AS amt
							 FROM voteheadmaintenance e
							 INNER JOIN funds_allocations b ON b.votecode=e.Votecode
							 WHERE b.votecode = '" .$myrow['Votecode']. "'
							 AND b.Financial_Year='".Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],+0))."'
							 GROUP BY e.Votecode");
  
	 while($myrowy = DB_fetch_array($resultyy)){
     $Cur_Balance=($myrowy['allocated_Fund']-$myrowy['commitments']);
	 $totalAlloc=($myrowy['allocated_Fund']+$myrowy['suppliementary']);
	 $comm=($myrowy['commitments']-$myrowy['decom']);
	 $sum=($comm + $myrow['amt']);
	 $Ava_Balance=($totalAlloc-$sum);
	 }
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
echo '<form method="post" name="form" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=PV_Detailed_Report" id="form">';
        $decl="<a href=# onclick=pop('popDiv',".$myrow['voucherid'].")><img src='".$RootPath."/css/decline.png'/> </a>";
         $app='<th><input type="submit" name="Submit" value="' . _('Approve') . '" />';
		 $print='<td><a href="' . $RootPath . '/PDFprintPV.php?voucher='.$VoucherNo.'"><span style="color:white;font-size:12px;" class="label label-default">Print</span></a></td>';
		

		    printf('<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td><a href="%s&amp;SelectedUser=%s&amp;view=1">View Payment Vouchers</a></td>		
					</tr>',
					$myrow['Votecode'],
					$myrow['Votehead'],
					locale_number_format($Ava_Balance,2),
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?Application=PVM&Ref=default&Link=PV_Detailed_Report',
					$myrow['Votecode']);
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
