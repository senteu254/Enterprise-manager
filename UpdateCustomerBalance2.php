<?php
/* $Id: CustomerAccount.php 7004 2014-11-24 15:56:19Z rchacon $*/
/* Shows customer account/statement on screen rather than PDF. */

include('includes/session.inc');
$Title = _('Customer Account');// Screen identification.
$ViewTopic = 'CustomerARInquiries';// Filename in ManualContents.php's TOC.
$BookMark = 'CustomerAccount';// Anchor's id in the manual's html document.
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
if (!isset($_GET['DebtorNo']) and !isset($_SESSION['CustomerID'])) {
	prnMsg(_('To display the account a Supplier must first be selected from the customer selection screen'), 'info');
	echo '<br /><div class="centre"><a href="', $RootPath, '/SelectSupplier.php">', _('Select a Supplier Account to Display'), '</a></div>';
	include('includes/footer.inc');
	exit;
} else {
	if (isset($_GET['DebtorNo'])) {
		$_SESSION['CustomerID'] = stripslashes($_GET['DebtorNo']);
	}
	$DebtorNo = $_SESSION['CustomerID'];
}

if (isset($_POST['update'])) {
		 $balance=$_POST['balance'];
		 if ($balance>0){
		 $type=580;
		 $sql = "UPDATE debtortrans SET ovamount='".$balance."', type='".$type."' WHERE debtorno = '". $DebtorNo . "' AND id='" . $_POST['id']. "'";
				$result = DB_query($sql);
				prnMsg( _('Balance has been sucessfully Updated'), 'success' );			  
		}elseif ($balance<0){
		$type=590;
		 $sql = "UPDATE debtortrans SET ovamount='".$balance."', type='".$type."' WHERE debtorno = '". $DebtorNo . "' AND id='" . $_POST['id']. "'";
				$result = DB_query($sql);
				prnMsg( _('Balance has been sucessfully Updated'), 'success' );		
		}
		}
	if (isset($_POST['save'])) {
		 $balance=$_POST['newbalance'];
		 if ($balance>0){
		 $type=490;
		 $typeno=GetNextTransNo($type,$db);
		  $sql = "INSERT INTO debtortrans (transno,
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
										'".$CustomerID."',
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
										'".$CustomerID."',
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
$ErrMsg = _('There was a problem retrieving the transactions that were settled over the course of the last month for'). ' ' . $DebtorNo . ' ' . _('from the database');
/*now get all the outstanding transaction ie Settled=0 */
$ErrMsg =  _('There was a problem retrieving the outstanding transactions for') . ' ' .	$DebtorNo . ' '. _('from the database') . '.';
		
		$sql = "SELECT  debtortrans.id ,
		              debtortrans.type,
					  debtortrans.alloc,
					 debtortrans.debtorno,	
					  debtortrans.ovamount,
					  debtorsmaster.debtorno,
					  debtorsmaster.name,
					  debtorsmaster.address1,
					  debtorsmaster.address5,
					  debtorsmaster.address6,
					  debtorsmaster.currcode,
					  currencies.currency,
					  systypes.typeid,
					  systypes.typename
				FROM debtortrans INNER JOIN systypes
					ON debtortrans.type=systypes.typeid
				INNER JOIN debtorsmaster 
				    ON debtortrans.debtorno=debtorsmaster.debtorno
				INNER JOIN currencies
		            ON debtorsmaster.currcode = currencies.currabrev
				WHERE(debtortrans.type=580 OR debtortrans.type=590)
				AND debtortrans.debtorno='" . $DebtorNo . "'
				AND status=0";
							
$Result = DB_query($sql, $ErrMsg);
if (DB_num_rows($Result) == 0) {
prnMsg( _('There are no Balance Carried foward for the selected Customer'));	
	//echo '<div class="centre">', _('There are no Balance Carried foward for the selected Supply'), ' ', '</div>';
	echo '<div class="noprint toplink">';
	echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
				<a href="', $RootPath, '/SelectCustomer.php">', _('Back to Customer Screen'), '</a>
			</div>
			<br />
					<div class="centre">';
					$sql6 = "SELECT * FROM debtorsmaster WHERE debtorno='" . $DebtorNo . "'";
					     $Ost=DB_query($sql6, $ErrMsg);
                     while ($row1=DB_fetch_array($Ost)) {
						echo'<input type="submit" name="ADD" value="' . _('Add Balance '. $row1['name'] .'') . '" /><br><br><br>';
						if(isset($_POST['ADD'])){
						 unset($_POST['Search']);
			
						echo '<table style="width:580px;" class="selection">
				<tr><th colspan="4">', _('BALANCE BROUGHT FORWARD FOR'), ': ', stripslashes($DebtorNo), ' - ', $row1['name'], '</th></tr>
				<br>';
		
		echo '
				<tr><th>', _('Telephone No.'), '</th><td>', $row1['address1'], '</td><th>', _('Email Address'), '</th><td>', $row1['address6'], '</td></tr>
				<tr><th>', _('Address 1'), '</th><td>', $row1['address1'], '</td><th>', _('All amounts stated in'), '</th><td>', $row1['currency'], '</td></tr>
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
				<a href="', $RootPath, '/SelectCustomer.php">', _('Back to Customer Screen'), '</a>
			</div>';
		while ($row=DB_fetch_array($Result)) {
		echo '<table style="width:580px;" class="selection">
				<tr><th colspan="4">', _('BALANCE BROUGHT FORWARD FOR'), ': ', stripslashes($DebtorNo), ' - ', $row['name'], '</th></tr>
				<br>';
		
		echo '
				<tr><th>', _('Telephone No.'), '</th><td>', $row['telephone'], '</td><th>', _('Email Address'), '</th><td>', $row['email'], '</td></tr>
				<tr><th>', _('Address'), '</th><td>', $row['address1'], '</td><th>', _('All amounts stated in'), '</th><td>', $row['currency'], '</td></tr>
				<tr><th>', _('Address'), '</th><td>', $row['address1'], '</td><th>', _('Country'), '</th><td colspan="2">', $row['address6'], ' ', $row['address6'], '</td></tr>
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