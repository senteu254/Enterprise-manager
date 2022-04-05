<?php	
$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
 
$per_page = 30; // Set how many records do you want to display per page.
$startpoint = ($page * $per_page) - $per_page;

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
$cheque=$_POST['cheque'];
$InputError = 0;
$pv=$_POST['PV'];

$sql = "UPDATE payment_voucher SET process_level=process_level+1,chequeNo='" .$cheque. "' WHERE voucherid = '". $pv . "'";
		$result = DB_query($sql);
$SQL = "SELECT voucherno FROM  payment_voucher_approval  WHERE voucherno='".$pv."' AND process_level=process_level+1";
  if(DB_num_rows($resu) ==0){
$sql2 = "SELECT process_level FROM payment_voucher WHERE voucherid = '". $pv . "'";
		$result2 = DB_query($sql2); 
		$rows = DB_fetch_row($result2);
$sqlq = "INSERT INTO payment_voucher_approval (voucherno,
						process_level,
						approver)
					VALUES ('" . $pv . "',
						'".$rows[0]."',
						'" . $_SESSION['UserID'] ."')";
		$resulta = DB_query($sqlq);
	}
	echo  '<div class="alert alert-success alert-dismissible"><h4><i class="icon fa fa-check"></i> Success</h4>'._('The selected Payment Voucher Has been Certified and Forwarded for Processing').'</div>';
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
			echo '<form method="post" name="DForm" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=inbox" id="DForm">';
			echo '<tr>
				<td>
				<textarea required name="comment" id="comment" cols="30" style="width:100%" rows="4"></textarea>
				<input type="hidden" name="PV" id="pvs" value="' . $_GET['SelectedUser'] . '" />
				<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
				</td>
			</tr>
			<tr>
				<td><input type="hidden" name="Decline" value="' . _('Submit') . '" />
				</form>';
	echo "<input type=submit onclick=hide('popDiv') value=" . _('Cancel') . " />";
	echo '</td>
			</tr>
			</table>';
	
	echo '</div>';	
	
		echo '<div id="popDiv2" style="z-index: 999;
									width: 100%;
									height: 100%;
									top: 0;
									left: 0;
									display: none;
									position: absolute;				
									background-color: #fff;
									background-color: rgba(255,255,255,0.7);
									filter: alpha(opacity = 50);">';
	
	
	echo '<table style="width: 350px;
						height: 100px;
						position: absolute;
						color: #000000;
						/* To align popup window at the center of screen*/
						top: 10%;
						left: 50%;
						margin-top: -100px;
						margin-left: -150px;">
			<tr>
				<th>' . _('Are you sure you wish to approve this Voucher?') . '</th>
			</tr>';
			echo '<form method="post" name="ApproveForm" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=inbox" id="ApproveForm">';
			echo '<tr><td>';
				 //if ($myrow['process_level']>=6){
				 echo '<span id="showcheque" style="display:none;">';
				 echo 'Enter Cheque Number: <textarea name="cheque" id="cheque" cols="20" style="width:100%" rows="2"></textarea>';
				 echo '</span>';
		//}
			echo '<input type="hidden" name="PV" id="pvs2" value="" />
				<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
				</td></tr>
			<tr><td><input type="hidden" name="Submit" value="' . _('Submit') . '" />
				</form>';
	echo "<input type=submit onclick=hide('popDiv2') value=" . _('Cancel') . " />";
	echo '</td></tr></table>';
	
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
		//prnMsg( _('The selected Payment Voucher Has been Declined'), 'success' );
		echo  '<div class="alert alert-success alert-dismissible"><h4><i class="icon fa fa-check"></i> Success</h4>'._('The selected Payment Voucher Has been Declined').'</div>';
	
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
					chequeNo,
					supplierid
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
	
echo '<a href="' . $RootPath . '/index.php?Application=PVM&Ref=default&Link=inbox"><button type="button" name="" class="button"><i class="fa fa-reply"></i> ' . _('Go Back') . '</button></a>';
echo'<br>';
echo'<br>';
echo '<table style="background:none repeat scroll 0% 0% #F1F1F1; border:none; box-shadow:none;">
      <tr><td>';
echo '<table style="width:100%;background-color: white;">
      <tr><td>';

	echo '<table style="width:100%;background-color: white;">
			<tr>
				<td>' . _('Voucher ID') . ':</td>
				<td>' . $_POST['voucherid'] . '</td>
			</tr>';


	echo '<tr>
			<td>' . _('Authority Ref No.') . '</td>
			<td>'.$_POST['arefno'].'</td>
		</tr>
		<tr>
			<td>' . _('Date Raised') . '</td>
			<td>'.$_POST['datereq'].'</td>
		</tr>
		<tr>
			<td>' . _('Label') . '</td>
			<td>'.$_POST['label'].'</td>
		</tr>
		<tr>
			<td>' .  _('Payee Name') . '</td>
			<td>'.$_POST['name'] .'</td>
		</tr>';


echo '</td>
	</tr>
	</table>

	<br />';

echo '</div>';

echo '<table style="width:350px; id="dataTable" class="selection">';
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
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=inbox" id="form">';
if ($myrow['process_level']==6){
echo '<table id="dataTable" class="selection">';
echo'<tr>';
echo '<tr>
	<td>Cheque No.</td>
				<td>
				<textarea required name="cheque" cols="20" style="width:100%" rows="2"></textarea>
				</td>
			</tr>';
			echo'</table>';
		}else{
		echo'';
		}
if ($myrow['process_level'] < ($myrow['process_level']+1)){

			//echo'<input type="submit" name="Submit" value="' . _('Approve') . '" />';
			echo ($myrow['process_level']>=8 ? '<a href="Payments.php'.($myrow['supplierid']=="" ? '?' : '?SupplierID='.$myrow['supplierid']).'&amp;Vid='.$myrow['voucherid'].'"><button type="button" class="btn btn-success"><i class="fa fa-money"></i> ' . _('Make Payment') . '</button></a></center>' : '<input type="submit" name="Submit" value="' . _('Approve') . '" />  <a href=# onclick=pop(\'popDiv\')><img src=\''.$RootPath.'/css/decline.png\'/> </a>' );
		echo'<input type="hidden" name="suppid" value="' . $myrow['supplierid'] . '" />';
		echo'<input type="hidden" name="PV" value="' . $_GET['SelectedUser'] . '" />
			<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
		//echo "<a href=# onclick=pop('popDiv')><img src='".$RootPath."/css/decline.png'/> </a>";
		echo '</form>';
		}else{
		echo '';
		}
echo '</form>';
    include('includes/Level_Tracking.php');

	echo '</td>	</tr></table>';
	echo '</td>
	<td valign="top" class="status">
	<td valign="top" class="status">
	<div style="background:url(css/statuspv.png) left top no-repeat; height:320px; width:220px;">
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

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=inbox" id="form">';
	
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
		 $sort=" AND payment_voucher.authorityref " . LIKE . " '%". $_POST['ref'] ."%' AND authorityref " . LIKE . " '%". $_POST['Yearend'] ."%'";
		 }else{
		 $sort =" AND payment_voucher.authorityref " . LIKE . " '%". $_POST['Yearend'] ."%'";
		 }		
		 }
		  
######################################################################################
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=inbox" id="form">';
if (!isset($SelectedUser)) {
$sql9="SELECT *  FROM pvroles  
				WHERE authoriser='" . $_SESSION['UserID'] ."'  ";
				$result9 = DB_query($sql9);
$myrow9 = DB_fetch_array($result9);
	if($myrow9['level']==0){
		$sql = "SELECT  * FROM payment_voucher,pvroles,pvlevel 
				WHERE payment_voucher.process_level=pvlevel.levelcode  
				AND pvlevel.levelcode=10 OR  pvlevel.levelcode=11 OR  pvlevel.levelcode=12 OR  pvlevel.levelcode=13 OR  pvlevel.levelcode=14 OR  pvlevel.levelcode=15 OR  pvlevel.levelcode=16 OR  pvlevel.levelcode=17 OR  pvlevel.levelcode=18
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				".$sort."";
		$sql2 = " payment_voucher,pvroles,pvlevel 
				WHERE payment_voucher.process_level=pvlevel.levelcode  
				AND pvlevel.levelcode=10 OR  pvlevel.levelcode=11 OR  pvlevel.levelcode=12 OR  pvlevel.levelcode=13 OR  pvlevel.levelcode=14 OR  pvlevel.levelcode=15 OR  pvlevel.levelcode=16 OR  pvlevel.levelcode=17 OR  pvlevel.levelcode=18
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				".$sort."";
			}else{
		$sql = "SELECT  *  FROM payment_voucher,pvroles,pvlevel
				WHERE payment_voucher.process_level=pvlevel.levelcode 
				AND pvlevel.levelcode=pvroles.level
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				".$sort."";
		$sql2 = " payment_voucher,pvroles,pvlevel
				WHERE payment_voucher.process_level=pvlevel.levelcode 
				AND pvlevel.levelcode=pvroles.level
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				".$sort."";
			}
			
	$sqlforPages = $sql2;
	$sql = $sql." ORDER BY uniqueid DESC LIMIT {$startpoint} , {$per_page}";
	$result = DB_query($sql);

	$ListCount = DB_num_rows($result);
	$ListPageMax = ceil($ListCount / ($_SESSION['DisplayRecordsMax']));
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
				?>
				<?php
				echo pagination($sqlforPages,$per_page,$page,$url='?Application=PVM&Ref=default&Link=inbox&');
				?>
                
	<table style="width:100%;font-size:12px;" class="table-hover" >
	<tr style="height:30px;">
				
				<th>Priority</th>
				<th style="width:110px;">RefNo</th>
				<th>Payee Name</th>
				<th>Amnt</th>
				<th width="300px">Action</th>			
			</tr>
<?php

	$k=0; //row colour counter
	DB_data_seek($result, ($_POST['PageOffset'] - 1) * ($_SESSION['DisplayRecordsMax']));
	if (DB_num_rows($result) == 0) {
		echo '<tr><td colspan="8"><center style="color:#FF0000"><strong>No Records Found</strong></center></td></tr>';
	}else{
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
		$LastVisitDate = $myrow['label'];
	}
echo'<input type="hidden" name="suppid" value="' . $myrow['supplierid'] . '" />
	<input type="hidden" name="amts" value="' . $myrow['amount'] . '" />';
		/*The SecurityHeadings array is defined in config.php */
$check='<td><input name="pv[]" type="checkbox" value="'. $myrow['voucherid'] .'" /></th>';
echo '<form method="post" name="form" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
        //$decl="<a href=# onclick=pop('popDiv',".$myrow['voucherid'].")><img src='".$RootPath."/css/decline.png'/></a>";
		$decl ="<button type='button' onclick=popdecline('popDiv',".$myrow['voucherid'].") name='Cancel'  class='btn btn-danger'><i class='fa fa-reply'></i> Decline</button>";
		$app ="<button type='button' onclick=pop2('popDiv2',".$myrow['voucherid'].",".$myrow['process_level'].",'".$myrow['chequeNo']."') name='Approve'  class='btn btn-primary'><i class='fa fa-share'></i> Approve</button>";
	if($myrow['level']==6){
	
		$app2 ="<button type='button' onclick=pop3('popDiv2',".$myrow['voucherid'].",".$myrow['process_level'].",'".$myrow['chequeNo']."') name='Approve'  class='btn btn-primary'><i class='fa fa-share'></i>Cash</button>";
		}else{
		$app2 ="";
		}
		 // $app='<td><a title="Approve"  onclick="return confirm(\'' . _('Are you sure you wish to approve this Voucher?') . '\');" href="'.htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8').'?Application=PVM&amp;Ref=default&amp;Link=inbox&amp;PV='.$myrow['voucherid'].'"><button type="button" class="btn btn-primary" name="Submit"><i class="fa fa-share"></i>' . _('Approve') . ' </button></a>';
		    printf('<td>%s</td>
		            <td><a href="%s&amp;SelectedUser=%s&amp;view=1">' . $myrow['authorityref']. '</a></td>
					<td>%s</td>
					<td>%s</td>				
					<td><center><a href="%s&amp;SelectedUser=%s&amp;view=1">' . _('') . '</a>'.($myrow['process_level']>=8 ? '<a href="%s'.($myrow['supplierid']=="" ? '' : '?SupplierID=%s').'&amp;Vid='.$myrow['voucherid'].'"><button type="button" class="btn btn-success"><i class="fa fa-money"></i> ' . _('Make Payment') . '</button></a></center>' : ''.$app.' '.$decl.' '.$app2.'' ).'</td>
					</tr>',
					'<span class="label2 label-'.(($myrow['label']=="Urgent" or $myrow['label']=="Emergency")? 'danger':'success').'">'.$myrow['label'].'</span>',
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?Application=PVM&Ref=default&Link=inbox',
					$myrow['voucherid'],
					$myrow['payeename'],
					$myrow['total'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?Application=PVM&Ref=default&Link=inbox',
					$myrow['voucherid'],
					($myrow['process_level']>=8 ? 'Payments.php' : ''),
					$myrow['supplierid'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=PVM&Ref=default&Link=inbox',
					$myrow['voucherid'],
					($myrow['process_level']>=8 ? 'Payments.php' : ''),
					$myrow['supplierid']);
	
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
	}
	
echo '</form>';
?>
</div>
		<script type="text/javascript">

			function pop2(div,id, level,cheque) {
			if(level==6){
			 var check = prompt("Enter Cheque No#", cheque);
			 if(check){
			 document.getElementById('pvs2').value = id;
  			 document.getElementById('cheque').value = check;
			 document.getElementsByName('ApproveForm')[0].submit();
			 }
				}else{
				var con = confirm("Are you sure you wish to approve this Voucher?");
				if(con){
				document.getElementById('pvs2').value = id;
  			 	document.getElementById('cheque').value = cheque;
				document.getElementsByName('ApproveForm')[0].submit();
				}
				}
			}
				function pop3(div,id, level,cheque) {
		
				var con = confirm("Are you sure you wish to approve this Voucher?");
				if(con){
				document.getElementById('pvs2').value = id;
  			 	document.getElementById('cheque').value = cheque;
				document.getElementsByName('ApproveForm')[0].submit();
				
				}
			}
		function popdecline(div,ids) {
				var comm = prompt("Please Leave a Reason for Decline:", "");
			 	if(comm){
				document.getElementById('pvs').value = ids;
				document.getElementById('comment').value = comm;
			 	document.getElementsByName('DForm')[0].submit();
				}
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
