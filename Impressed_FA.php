<?php

/* $Id: SecurityTokens.php 4424 2010-12-22 16:27:45Z tim_schofield $*/

include('includes/session.inc');
$Title = _('Petty Cash/Cheque Requistion');

include('includes/header.inc');
echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/maintenance.png" title="' .
		_('Print') . '" alt="" />' . ' ' . $Title . '</p>';
		
############################################################################################

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
$sql = "UPDATE impressed SET process_level=7 WHERE voucherid = '". $pv[$i] . "'";

		$result = DB_query($sql);
$SQL = "SELECT voucherno FROM impressed_approval where voucherno='".$pv[$i]."' and process_level=7";
$resu=DB_query($SQL);
  if(DB_num_rows($resu) ==0){
$sqlq = "INSERT INTO impressed_approval (voucherno,
						process_level,
						approver)
					VALUES ('" . $pv[$i] . "',
						'7',
						'" . $_SESSION['UserID'] ."')";
		$resulta = DB_query($sqlq);
	}		
	}
	prnMsg( _('The selected impressed Voucher Has been Certified and Approved'), 'success' );
	unset($_POST['approve']);
	#######################################################333
	}
	if(isset($_POST['btn-upload'])){    
	$file = rand(1000,100000)."-".$_FILES['file']['name'];
    $file_loc = $_FILES['file']['tmp_name'];
	$file_size = $_FILES['file']['size'];
	$file_type = $_FILES['file']['type'];
	$folder="uploads/";
	
	// new file size in KB
	$new_size = $file_size/1024;  
	// new file size in KB
	// make file name in lower case
	$new_file_name = strtolower($file);
	// make file name in lower case
	
	$final_file=str_replace(' ','-',$new_file_name);
	echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	if(move_uploaded_file($file_loc,$folder.$final_file))
	{
		$sql="INSERT INTO tbl_uploads(file,type,size) VALUES('$final_file','$file_type','$new_size')";
		$result = DB_query($sql);
		?>
		<script>
		alert('successfully uploaded');
        </script>
		<?php
	}
	else
	{
		?>
		<script>
		alert('error while uploading file');
        window.location.href='impressed_FA.php?fail';
        </script>
		<?php
	}
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
			<th>' . _('Reason to Decline') . ':</th>
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
$sql = "UPDATE impressed SET process_level=15, comment='$comment' WHERE voucherid = '". $_POST['PV'] . "'";

		$result = DB_query($sql);
		prnMsg( _('The selected impressed  has been Declined'), 'success' );
		
	
	}
	unset($_POST['approve']);
	}elseif (isset($_POST['Decline2'])) {
$InputError = 0;

$documents=$_POST['documents'];
$sqlf = "UPDATE impressed SET process_level=8, documents='$documents' WHERE voucherid = '". $_POST['PV'] . "'";

		$resultf = DB_query($sqlf);
		prnMsg( _('Documents has been saved successfully'), 'success' );
		
	}
	//unset($_POST['approve']);
	//}
/********************************************************************************************************/######################################################################################
if (isset($_GET['clear'])) {
	//editing an existing User
echo '<a href="' . $RootPath . '/Impressed_FA.php">' . _('Petty Cash/Cheque Requistion Form') . '</a>

<table>
			<tr>
				<th>' . _('Supporting Documents for SNo. ' . $SelectedUser . '') . ':</th>
			</tr>';
			echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" id="form">';
			echo '<tr>
				<td>';
				echo'<textarea  name="documents" cols="30" rows="4"></textarea>
				
				</tr>';

				echo'<input type="hidden" name="PV" value="' . $_GET['SelectedUser'] . '" />
				<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
			</td>
			</tr>
			<tr>
		<td><input type="submit" name="Decline2" value="' . _('Submit documents') . '" />';
	
	echo '</td>
			</tr>';
			echo'</form>
			</table>';	

	}elseif(isset($_GET['view'])) {
	//editing an existing User

	$sql = "SELECT voucherid,
	                serialNo,
					datereq,
					label,
					payeename,
					particulars,
					amount,
					total,
					process_level
		FROM impressed
		WHERE voucherid='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['voucherid'] = $myrow['voucherid'];
	$_POST['serialNo'] = $myrow['serialNo'];
	$_POST['datereq'] = $myrow['datereq'];
	$_POST['label'] = $myrow['label'];
	$_POST['name']	= $myrow['payeename'];
	$pat  = unserialize($myrow['particulars']);
	$amount = unserialize($myrow['amount']);
	$_POST['total'] = $myrow['total'];
	
echo '<a href="' . $RootPath . '/Impressed_FA.php">' . _('Petty Cash/Cheque Requistion') . '</a>';
echo '<table style="background:none repeat scroll 0% 0% #F1F1F1; border:none; box-shadow:none;">
      <tr><td>';
echo '<table class="selection" height:280px;>
      <tr><td>';

	echo '<table class="selection">
			<tr>
				<td>' . _('SNo.') . ':</td>
				<th>' . $_POST['voucherid'] . '</th>
			</tr>
		</tr>
		<td>' . _('Date Raised') . '</td>
			<td>'.$_POST['datereq'].'</td>
		</tr>
		<td>' . _('Label') . '</td>
			<td>'.$_POST['label'].'</td>
		</tr>
		<tr>
		<td>' .  _('User Name') . '</td>
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
		<th>' .  _('Amount'). '</th>
	</tr>';
	
for($i=0;$i<=count($pat);$i++)
{
	$particulars = $pat[$i];
	$amnt = $amount[$i];

	echo '<tr>
			
			<td>'.$particulars.'</td>
			<td>'.$amnt.'</td>
		 
		 </tr>';
		}
		echo '<tr align="right"><td></td><td></td><td>' .  _('Total Amount'). '</td><th>' .$_POST['total'].'</th></tr>';

echo '</table>';
if ($myrow['process_level'] ==6) {
		echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" id="form">
			<input type="submit" name="Submit" value="' . _('Approve') . '" />';
		echo'<input type="hidden" name="pv[]" value="' . $_GET['SelectedUser'] . '" />
			<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
		echo "<a href=# onclick=pop('popDiv')><img src='".$RootPath."/css/decline.png'/> </a>";
		echo '</form>';
		}else{
		echo '';
		}

    include('includes/Level_Tracking_Impressed.php');

	echo '</td>	</tr></table>';
	echo '</td>
	<td valign="top" class="status">
	<div style="background:url(css/flow4.png) left top no-repeat; height:280px; width:233px;">
	<div style="padding-left:80px; padding-top:12px; font-weight:bold;">'.$label.' &nbsp;User</div>
	<div style="padding-left:80px; padding-top:12px; font-weight:bold;">'.$label2.' &nbsp;Head of &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Department</div>
	<div style="padding-left:80px; padding-top:12px; font-weight:bold;">'.$label3.' &nbsp;Controlling &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Department</div>
	<div style="padding-left:80px; padding-top:12px; font-weight:bold;">'.$label4.' &nbsp;Procurement &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Department</div>
	<div style="padding-left:80px; padding-top:12px; font-weight:bold;">'.$label5.' &nbsp;AIE Holder</div>
	<div style="padding-left:80px; padding-top:12px; font-weight:bold;">'.$label6.' &nbsp;Finance &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Department</div>
	<div style="padding-left:80px; padding-top:12px; font-weight:bold;">'.$label7.' &nbsp;Cashier</div>
	</div>
	</td>
	</tr>
	</table>';
	######################################################################################################################################################################
	}elseif(isset($_GET['payment'])) {
	//editing an existing User

	$sql = "SELECT voucherid,
	                serialNo,
					datereq,
					label,
					payeename,
					particulars,
					amount,
					total,
					process_level
		FROM impressed
		WHERE voucherid='" . $SelectedUser . "'
		AND process_level=7 ";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['voucherid'] = $myrow['voucherid'];
	$_POST['serialNo'] = $myrow['serialNo'];
	$_POST['datereq'] = $myrow['datereq'];
	$_POST['label'] = $myrow['label'];
	$_POST['name']	= $myrow['payeename'];
	$pat  = unserialize($myrow['particulars']);
	$amount = unserialize($myrow['amount']);
	$_POST['total'] = $myrow['total'];
	
echo '<a href="' . $RootPath . '/Impressed_FA.php">' . _('Petty Cash/Cheque Requistion') . '</a>';
echo '<table style="background:none repeat scroll 0% 0% #F1F1F1; border:none; box-shadow:none;">
      <tr><td>


	<br />';

echo '</div>';

echo '<table id="dataTable" class="selection">';
echo'<tr>
<tr align="right"><td></td><td></td><td>' .  _(''). '</td><th>' .('Balance').'</th></tr>
<td>' .  _('Amount of Balance in Petty Cash Account')  . '</td>
<th>' .  _('')  . '</th>

    </tr>';
echo '<tr>
		<th>' .  _('Particulars')  . '</th>
		<th>' .  _('Amount'). '</th>
	</tr>';
	
for($i=0;$i<=count($pat);$i++)
{
	$particulars = $pat[$i];
	$amnt = $amount[$i];

	echo '<tr>
			
			<td>'.$particulars.'</td>
			<td>'.$amnt.'</td>
		 
		 </tr>';
		}
		echo '<tr align="right"><td></td><td></td><td>' .  _('Total Amount'). '</td><th>' .$_POST['total'].'</th></tr>';

echo '</table>';
if ($myrow['process_level'] ==6) {
		echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" id="form">
			<input type="submit" name="Submit" value="' . _('Approve') . '" />';
		echo'<input type="hidden" name="pv[]" value="' . $_GET['SelectedUser'] . '" />
			<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
		echo "<a href=# onclick=pop('popDiv')><img src='".$RootPath."/css/decline.png'/> </a>";
		echo '</form>';
		}else{
		echo '';
		}

	echo'</tr>
	</table>';
	}
	##################################################################################################################################################################
	elseif(isset($_GET['doc'])) {
	//editing an existing User

	$SQL7 = "SELECT voucherid,
	                serialNo,
					amount,
					total,
					documents,
					process_level
		FROM impressed
		WHERE voucherid='" . $SelectedUser . "'";
	$res = DB_query($SQL7);

	
echo '<a href="' . $RootPath . '/Impressed_FA.php">' . _('Petty Cash/Cheque Requistion Form') . '</a>';
echo '<table style="background:none repeat scroll 0% 0% #F1F1F1; border:none; box-shadow:none;">';

	echo '<table class="selection">
			  <tr>
				<th>' . _('SNo.') . ':</th>
				<th>' . _('Documents') . '</th>
			</tr>';
	while ($myrow7 = DB_fetch_array($res)) {
			echo'<tr>
		    <td>' . $myrow7['voucherid'] . '</td>
			<td>'.$myrow7['documents'].'</td>
		</tr>';
		}
	echo'</table>';
	echo '<a href="PrintImpressedform.php?voucherid='. $SelectedUser . '">Print PDF</a>';


    //include('includes/footer.inc');
	}else{
######################################################################################
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" id="form">';
if (!isset($SelectedUser)) {
	$sql = "SELECT voucherid,
	                serialNo,
					datereq,
					label,
					payeename,
					particulars,
					amount,
					process_level,
					total,
					documents
				FROM impressed WHERE process_level=6 OR process_level=7 OR process_level=8
			    ORDER BY process_level ASC,voucherid DESC";
				################ 6=to be approved,15 rejected at finance officer,,7 =with documents appload########################
	$result = DB_query($sql);
	echo '<table class="selection">';
	echo '<tr><th>' . _('Pid') . '</th>
	           <th>' . _('Serial No') . '</th>
				<th>' . _('Label') . '</th>
				<th>' . _('Date Raised') . '</th>
				<th>' . _('User Name') . '</th>
				<th>' . _('Amount') . '</th>
				<th>' . _('Authorize') . '</th>
				<th></th>
				<th></th>
				<th>' ._('Clearance'). '</th>
				<th>' ._('View documents'). '</th>
				
				<th>&nbsp;</th>
			</tr>';

	$k=0; //row colour counter

	while ($myrow = DB_fetch_array($result)) {
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}
 $print  = $RootPath . '/Supplierdetailsprint.php?TenderID=' . $myrow['voucherid'];
	if ($myrow['datereq']=='') {
		$LastVisitDate = Date($_SESSION['DefaultDateFormat']);
	} else {
		$LastVisitDate = ConvertSQLDate($myrow['datereq']);
	}
	##################################################################
	//if ($myrow['process_level'] ==7) {
	// $payment  = $RootPath . '/PcClaimExpensesFromTab.php?PettycashNo='.$SelectedUser.
		//$payment='<th><a href="PcClaimExpensesFromTab.php">' . _('Make Payment') . '</a></th>';
		//}elseif ($myrow['process_level'] ==6){
		//$payment='<th>waiting Approval</th>';
		//}elseif ($myrow['process_level'] ==7){
		//$payment='<th>Approved</th>';
		//}else{
		//$payment='<th>Paid</th>';
		//}
		
	##################################################################

		/*The SecurityHeadings array is defined in config.php */
		if ($myrow['process_level'] ==6) {
		$check='<th><input name="pv[]" type="checkbox" value="'. $myrow['voucherid'] .'" /></th>';
		}elseif ($myrow['process_level'] ==11){
		$check='<th><img src="'.$RootPath.'/css/red.png"/></th>';
		}else{
		$check='<th><img src="'.$RootPath.'/css/ok.png"/></th>';
		}
		if ($myrow['process_level']==7) {
		$clear='<th><a href="%s&amp;SelectedUser=%s&amp;clear=1">' . _('Clear') . '</a></th>';
		echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" id="form">';
		echo'<input type="hidden" name="pv[]" value="' . $_GET['SelectedUser'] . '" />
			<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';		
		}elseif($myrow['process_level']==6){
		$payment='<th>Wait for approval</th>';
		$clear='<th>Wait for approval</th>';
		}elseif ($myrow['process_level']==8){
		$clear='<th>Clear</th>';
		$payment='<th><a href="PcClaimExpensesFromTab.php">' . _('Make Payment') . '</a></th>';
		}elseif ($myrow['process_level']==8){
		$payment='<th>Paid</th>';
		$clear='<th>cleared</th>';		
		}else{
		echo'';
		}
		if ($myrow['process_level']==8) {
		$doc='<th><a href="%s&amp;SelectedUser=%s&amp;doc=1">' . _('View Documents') . '</a></th>';
		echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" id="form">';
		echo'<input type="hidden" name="pv[]" value="' . $_GET['SelectedUser'] . '" />
			<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';		
		}elseif($myrow['process_level']==8){
		$doc='<th><a href="%s&amp;SelectedUser=%s&amp;doc=1">' . _('View Documents') . '</a></th>';
		
		}else{
		$doc='<th></th>';
		}
		########################################################################################################################################
		

        ########################################################################################################################################
		printf('<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					'.$check.'
					<td><a href="%s&amp;SelectedUser=%s&amp;view=1">' . _('View') . '</a></td>
					'.$payment.'
					'.$clear.'					
					'. $doc . ' 				
					</tr>',
					$myrow['voucherid'],
					$myrow['serialNo'],
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

	} //END WHILE LIST LOOP
	echo '</table><br />';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<input type="submit" name="Submit" value="' . _('Update') . '" />';
	}
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
			
			
			
	$(".user").click(function() {
    if($(this).is(":checked")) {
		 $(".supplierfield").hide(200);
		 $(".userfield").show(200);
    } else {
        $(".supplierfield").show(300);
		$(".userfield").hide(200);
    }
});
		</script>