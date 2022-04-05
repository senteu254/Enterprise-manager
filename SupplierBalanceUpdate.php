<?php
/* $Id: CustomerAccount.php 7004 2014-11-24 15:56:19Z rchacon $*/
/* Shows customer account/statement on screen rather than PDF. */

include('includes/session.inc');
$Title = _('Supplier Account');// Screen identification.
$ViewTopic = 'SupplierARInquiries';// Filename in ManualContents.php's TOC.
$BookMark = 'SupplierAccount';// Anchor's id in the manual's html document.
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
if (!isset($_GET['SupplierID']) and !isset($_SESSION['SupplierID'])) {
	prnMsg(_('To display the account a Supplier must first be selected from the customer selection screen'), 'info');
	echo '<br /><div class="centre"><a href="', $RootPath, '/SelectSupplier.php">', _('Select a Supplier Account to Display'), '</a></div>';
	include('includes/footer.inc');
	exit;
} else {
	if (isset($_GET['SupplierID'])) {
		$_SESSION['SupplierID'] = stripslashes($_GET['SupplierID']);
	}
	$SupplierID = $_SESSION['SupplierID'];
}

if (isset($_POST['update'])) {
		 $balance=$_POST['balance'];
		 if ($balance>=0){
		 $type=490;
		 $sql = "UPDATE supptrans SET ovamount='".$balance."', type='".$type."' WHERE supplierno = '". $SupplierID . "' AND id='" . $_POST['id']. "'";
				$result = DB_query($sql);
				prnMsg( _('Balance has been sucessfully Updated'), 'success' );			  
		}elseif ($balance<=0){
		$type=480;
		 $sql = "UPDATE supptrans SET ovamount='".$balance."', type='".$type."' WHERE supplierno = '". $SupplierID . "' AND id='" . $_POST['id']. "'";
				$result = DB_query($sql);
				prnMsg( _('Balance has been sucessfully Updated'), 'success' );		
		}
		}
	if (isset($_POST['save'])) {
		 $balance=$_POST['newbalance'];
		 if ($balance>0){
		 $type=490;
		 $typeno=GetNextTransNo($type,$db);
		  $sql = "INSERT INTO supptrans (transno,
		                                type,
										supplierno,
										OrderNo,
										suppreference,
										trandate,
										duedate,
										ovamount,
										ovgst,
										rate,
										transtext,
										inputdate) 
							  VALUES('".$typeno."',
										'".$type."',
										'".$SupplierID."',
										'',
										'',
										'" . date('Y-m-d') . "',
										'" . date('Y-m-d') . "',
										'".$balance."',
										'',
										1,
										'',
										'".date('Y-m-d H:m:s')."')";
				$result = DB_query($sql);
				prnMsg( _('Balance has been sucessfully Updated'), 'success' );			  
		}elseif ($balance<0){
		$type=480;
		$typeno=GetNextTransNo($type,$db);
		 $sql = "INSERT INTO supptrans (transno,
		                                type,
										supplierno,
										OrderNo,
										suppreference,
										trandate,
										duedate,
										ovamount,
										ovgst,
										rate,
										transtext,
										inputdate) 
							  VALUES('".$typeno."',
										'".$type."',
										'".$SupplierID."',
										'',
										'',
										'" . date('Y-m-d') . "',
										'" . date('Y-m-d') . "',
										'".$balance."',
										'',
										1,
										'',
										'".date('Y-m-d H:m:s')."')";
				$result = DB_query($sql);
				prnMsg( _('Balance has been sucessfully Updated'), 'success' );		
		}
		}
/*now get all the settled transactions which were allocated this month */
$ErrMsg = _('There was a problem retrieving the transactions that were settled over the course of the last month for'). ' ' . $SupplierID . ' ' . _('from the database');
/*now get all the outstanding transaction ie Settled=0 */
$ErrMsg =  _('There was a problem retrieving the outstanding transactions for') . ' ' .	$SupplierID . ' '. _('from the database') . '.';
		
		$sql56 = "SELECT  supptrans.id,
		              supptrans.type,
					  supptrans.status,
					  supptrans.alloc,
					  supptrans.supplierno,	
					  supptrans.ovamount,
					  suppliers.supplierid,
					  suppliers.suppname,
					  suppliers.telephone,
					  suppliers.address1,
					  suppliers.address5,
					  suppliers.address6,
					  suppliers.email,
					  suppliers.currcode,
					currencies.currency,
					  systypes.typeid,
					  systypes.typename
				FROM supptrans INNER JOIN systypes
					ON supptrans.type=systypes.typeid
				INNER JOIN suppliers 
				    ON supptrans.supplierno=suppliers.supplierid
				INNER JOIN currencies
		            ON suppliers.currcode = currencies.currabrev				
				WHERE (supptrans.type=480 OR supptrans.type=490)
				AND supptrans.supplierno=" . $SupplierID . " 
				AND supptrans.status=0
				AND supptrans.transtate=0";
				
$supplierResult = DB_query($sql56, $ErrMsg);
if (DB_num_rows($supplierResult) == 0) {
prnMsg( _('There are no Balance Carried foward for the selected Supplier'));	
	//echo '<div class="centre">', _('There are no Balance Carried foward for the selected Supply'), ' ', '</div>';
	echo '<div class="noprint toplink">';
	echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
				<a href="', $RootPath, '/SelectSupplier.php">', _('Back to Supplier Screen'), '</a>
			</div>
			<br />
					<div class="centre">';
					$sql6 = "SELECT * FROM suppliers WHERE supplierid='" . $SupplierID . "'";
					     $Ost=DB_query($sql6, $ErrMsg);
                     while ($row1=DB_fetch_array($Ost)) {
						echo'<input type="submit" name="ADD" value="' . _('Add Balance '. $row1['suppname'] .'') . '" /><br><br><br>';
						if(isset($_POST['ADD'])){
						 unset($_POST['Search']);
			
						echo '<table style="width:580px;" class="selection">
				<tr><th colspan="4">', _('BALANCE BROUGHT FORWARD FOR'), ': ', stripslashes($SupplierID), ' - ', $row1['suppname'], '</th></tr>
				<br>';
		
		echo '
				<tr><th>', _('Telephone No.'), '</th><td>', $row1['telephone'], '</td><th>', _('Email Address'), '</th><td>', $row1['email'], '</td></tr>
				<tr><th>', _('Supplier Group'), '</th><td>', $row1['groupname'], '</td><th>', _('All amounts stated in'), '</th><td>', $row1['currency'], '</td></tr>
				<tr><th>', _('Address'), '</th><td>', $row1['address1'], '</td><th>', _('Country'), '</th><td colspan="2">', $row1['address6'], ' ', $row1['address6'], '</td></tr>
			</table>';		
						echo'<table class="selection">
				<tr>
				<th>Total Balance</th>
				</tr>
				<tr>';
				echo'<td>'.'<input type="number" maxlength="20" size="15" name="newbalance" value="" />'.'</td>
				</tr>';				
			echo'</table>			
			<br />
					<div class="centre">
						<input type="submit" name="save" value="' . _('Save Balance') . '" /><br><br><br>
					</div>';
						
						echo'</form>';
						}
						}
					echo'</form>';
					echo'</div>';
	include('includes/footer.inc');
	exit;
}
		echo '<div class="noprint toplink">
				<a href="', $RootPath, '/SelectSupplier.php">', _('Back to Supplier Screen'), '</a>
			</div>';
		while ($row=DB_fetch_array($supplierResult)) {
		echo '<table style="width:580px;" class="selection">
				<tr><th colspan="4">', _('BALANCE BROUGHT FORWARD FOR'), ': ', stripslashes($SupplierID), ' - ', $row['suppname'], '</th></tr>
				<br>';
		
		echo '
				<tr><th>', _('Telephone No.'), '</th><td>', $row['telephone'], '</td><th>', _('Email Address'), '</th><td>', $row['email'], '</td></tr>
				<tr><th>', _('Supplier Group'), '</th><td>', $row['groupname'], '</td><th>', _('All amounts stated in'), '</th><td>', $row['currency'], '</td></tr>
				<tr><th>', _('Address'), '</th><td>', $row['address1'], '</td><th>', _('Country'), '</th><td colspan="2">', $row['address6'], ' ', $supplierRecord['address6'], '</td></tr>
			</table>';			
		if ($row['alloc']>0){
		echo'Cannot edit the balance since transaction has been done on the '.$row['typename'].' ,consult system admin';		
		}else{
		echo '<br /><form onSubmit="return VerifyForm(this);" action="', htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'), '" method="post" class="centre noprint">
		<input name="FormID" type="hidden" value="'. $_SESSION['FormID']. '" />
		<input name="id" type="hidden" value="' .$row['id']. '" />';
		
		echo'<table class="selection">
				<tr>
				<th>Type</th>
				<th>Total Balance</th>
				</tr>
				<tr>';
				echo'<td>'.$row['typename'].'</td>';
				echo'<td>'.'<input type="text" maxlength="20" size="15" name="balance" value="'.locale_number_format($row['ovamount'],2).'" />'.'</td>
				</tr>';				
			echo'</table>			
			<br />
					<div class="centre">
						<input type="submit" name="update" value="' . _('Update Balance') . '" /><br><br><br>
					</div>
			</form>';
		}
		/* Show a table of the invoices returned by the SQL. */
		}
		include('includes/footer.inc');
		?>