<?php

/* $Id: PDFChequeListing.php 6943 2014-10-27 07:06:42Z daintree $*/

include('includes/SQL_CommonFunctions.inc');
include ('includes/session.inc');
$ViewTopic= 'GeneralLedger';
$BookMark = 'ChequePaymentListing';

$InputError=0;
if (isset($_POST['FromDate']) AND !Is_Date($_POST['FromDate'])){
	$msg = _('The date from must be specified in the format') . ' ' . $_SESSION['DefaultDateFormat'];
	$InputError=1;
	unset($_POST['FromDate']);
}
if (isset($_POST['ToDate']) and !Is_Date($_POST['ToDate'])){
	$msg = _('The date to must be specified in the format') . ' ' . $_SESSION['DefaultDateFormat'];
	$InputError=1;
	unset($_POST['ToDate']);
}

if (!isset($_POST['FromDate']) OR !isset($_POST['ToDate'])){


	 $Title = _('Payment Listing');
	 include ('includes/header.inc');

	echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/money_add.png" title="' .
		 $Title . '" alt="" />' . ' ' . $Title . '</p>';

	if ($InputError==1){
		prnMsg($msg,'error');
	}

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';

	echo '<div><input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" /></div>';
	echo '<table class="selection">
	 		<tr>
				<td>' . _('Enter the date from which cheques are to be listed') . ':</td>
				<td><input type="text" name="FromDate" maxlength="10" size="10" class="date" alt="' . $_SESSION['DefaultDateFormat'] . '"  value="' . Date($_SESSION['DefaultDateFormat']) . '" /></td>
			</tr>';
	 echo '<tr><td>' . _('Enter the date to which cheques are to be listed') . ':</td>
	 		<td><input type="text" name="ToDate" maxlength="10" size="10"  class="date" alt="' . $_SESSION['DefaultDateFormat'] . '"  value="' . Date($_SESSION['DefaultDateFormat']) . '" /></td>
	</tr>';
	 echo '<tr><td>' . _('Bank Account') . '</td><td>';

	 $sql = "SELECT bankaccountname, accountcode FROM bankaccounts";
	 $result = DB_query($sql);


	 echo '<select name="BankAccount">';

	 while ($myrow=DB_fetch_array($result)){
		echo '<option value="' . $myrow['accountcode'] . '">' . $myrow['bankaccountname'] . '</option>';
	 }


	 echo '</select></td></tr>';

	 echo '<tr>
				<td>' . _('Email the report off') . ':</td>
				<td><select name="Email">
					<option selected="selected" value="No">' . _('No') . '</option>
					<option value="Yes">' . _('Yes') . '</option>
				</select></td>
			</tr>
			</table>
			<div class="centre">
                <br />
				<input type="submit" name="Go" value="' . _('Create PDF') . '" />
			</div>
            </form>';

	 include('includes/footer.inc');
	 exit;
} else {

	include('includes/ConnectDB.inc');
}

$sql = "SELECT bankaccountname,
				decimalplaces AS bankcurrdecimalplaces
		FROM bankaccounts INNER JOIN currencies
		ON bankaccounts.currcode=currencies.currabrev
		WHERE accountcode = '" .$_POST['BankAccount'] . "'";
$BankActResult = DB_query($sql);
$myrow = DB_fetch_array($BankActResult);
$BankAccountName = $myrow['bankaccountname'];
$BankCurrDecimalPlaces = $myrow['bankcurrdecimalplaces'];

$sql= "SELECT amount,
			ref,
			transdate,
			banktranstype,
			type,
			transno
		FROM banktrans
		WHERE banktrans.bankact='" . $_POST['BankAccount'] . "'
		AND (banktrans.type=1 or banktrans.type=22)
		AND transdate >='" . FormatDateForSQL($_POST['FromDate']) . "'
		AND transdate <='" . FormatDateForSQL($_POST['ToDate']) . "'";
	
$Result=DB_query($sql,'','',false,false);
if (DB_error_no()!=0){
	$Title = _('Payment Listing');
	include('includes/header.inc');
	prnMsg(_('An error occurred getting the payments'),'error');
	if ($debug==1){
		prnMsg(_('The SQL used to get the receipt header information that failed was') . ':<br />' . $sql,'error');
	}
	include('includes/footer.inc');
  	exit;
} elseif (DB_num_rows($Result) == 0){
	$Title = _('Payment Listing');
	include('includes/header.inc');
  	prnMsg (_('There were no bank transactions found in the database within the period from') . ' ' . $_POST['FromDate'] . ' ' . _('to') . ' ' . $_POST['ToDate'] . '. ' ._('Please try again selecting a different date range or account'), 'error');
	include('includes/footer.inc');
  	exit;
}

include('includes/PDFStarter.php');

/*PDFStarter.php has all the variables for page size and width set up depending on the users default preferences for paper size */

$pdf->addInfo('Title',_('Cheque Listing'));
$pdf->addInfo('Subject',_('Cheque listing from') . '  ' . $_POST['FromDate'] . ' ' . _('to') . ' ' . $_POST['ToDate']);
$line_height=12;
$PageNumber = 1;
$TotalCheques = 0;

include ('includes/PDFChequeListingPageHeader.inc');

while ($myrow=DB_fetch_array($Result)){
    $LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,42,$FontSize,$myrow['transdate'], 'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,95,$FontSize,locale_number_format(-$myrow['amount'],$BankCurrDecimalPlaces), 'right');
	$LeftOvers = $pdf->addTextWrap($Left_Margin+105,$YPos,90,$FontSize,$myrow['ref'], 'left');

	$sql = "SELECT accountname,
					amount,
					narrative
			FROM gltrans INNER JOIN chartmaster
			ON gltrans.account=chartmaster.accountcode
			WHERE gltrans.typeno ='" . $myrow['transno'] . "'
			AND gltrans.type='" . $myrow['type'] . "'";

	$GLTransResult = DB_query($sql,'','',false,false);
	if (DB_error_no()!=0){
		$Title = _('Payment Listing');
		include('includes/header.inc');
   		prnMsg(_('An error occurred getting the GL transactions'),'error');
		if ($debug==1){
				prnMsg( _('The SQL used to get the receipt header information that failed was') . ':<br />' . $sql, 'error');
		}
		include('includes/footer.inc');
  		exit;
	}
	while ($GLRow=DB_fetch_array($GLTransResult)){
		$LeftOvers = $pdf->addTextWrap($Left_Margin+210,$YPos,90,$FontSize,$GLRow['accountname'], 'left');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+290,$YPos,60,$FontSize,locale_number_format($GLRow['amount'],$_SESSION['CompanyRecord']['decimalplaces']), 'right');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+355,$YPos,200,$FontSize,$GLRow['narrative'], 'left');
		$YPos -= ($line_height);
		if ($YPos - (2 *$line_height) < $Bottom_Margin){
		  		/*Then set up a new page */
			  		$PageNumber++;
		  		include ('includes/PDFChequeListingPageHeader.inc');
	  		} /*end of new page header  */
	}
	DB_free_result($GLTransResult);

	$YPos -= ($line_height);
	$TotalCheques = $TotalCheques - $myrow['amount'];

	if ($YPos - (2 *$line_height) < $Bottom_Margin){
		  /*Then set up a new page */
		  $PageNumber++;
		  include ('includes/PDFChequeListingPageHeader.inc');
	  } /*end of new page header  */
} /* end of while there are customer receipts in the batch to print */


$YPos-=$line_height;
$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize,locale_number_format($TotalCheques,2), 'right');
$LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,300,$FontSize,_('TOTAL') . ' ' . $Currency . ' ' . _('CHEQUES'), 'left');

$ReportFileName = $_SESSION['DatabaseName'] . '_ChequeListing_' . date('Y-m-d').'.pdf';
$pdf->Output($_SESSION['reports_dir'].'/'.$ReportFileName,'F');
$pdf->OutputI($ReportFileName);
$pdf->__destruct();
if ($_POST['Email']=='Yes'){

	include('includes/htmlMimeMail.php');
	$mail = new htmlMimeMail();
	$attachment = $mail->getFile($_SESSION['reports_dir'] . '/'.$ReportFileName);
	$mail->setSubject(_('Payments check list'));
	$mail->setText(_('Please find herewith payments listing from') . ' ' . $_POST['FromDate'] . ' ' . _('to') . ' ' . $_POST['ToDate']);
	$mail->addAttachment($attachment, 'PaymentListing.pdf', 'application/pdf');
	$ChkListingRecipients = GetMailList('ChkListingRecipients');
	if(sizeOf($ChkListingRecipients) == 0){
		prnMsg(_('There are no member in Check Listing Recipients email group,  no mail will be sent'),'error');
		include('includes/footer.inc');
		exit;
	}

	if($_SESSION['SmtpSetting']==0){
		$mail->setFrom(array('"' . $_SESSION['CompanyRecord']['coyname'] . '" <' . $_SESSION['CompanyRecord']['email'] . '>'));
		$result = $mail->send($ChkListingRecipients);
	}else{
		$result = SendmailBySmtp($mail,$ChkListingRecipients);
	}
}



?>
