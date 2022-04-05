<?php
if(!isset($_SESSION['UserID']) && !isset($_SESSION['AccessLevel']) && !isset($_SESSION['UsersRealName']) && !isset($_SESSION['DatabaseName'])){
header('location:../index.php');
}
/* $Id: InternalStockRequest.php 4576 2011-05-27 10:59:20Z daintree $*/
date_default_timezone_set('Africa/Nairobi');
include('includes/DefineStockRequestClass.php');
require('includes/session.inc');


/* $Id: header.inc 6644 2014-03-27 20:04:34Z rchacon $ */

	if (!isset($RootPath)){
		$RootPath = dirname(htmlspecialchars($_SERVER['PHP_SELF']));
		if ($RootPath == '/' OR $RootPath == "\\") {
			$RootPath = '';
		}
	}

	$ViewTopic = isset($ViewTopic)?'?ViewTopic=' . $ViewTopic : '';
	$BookMark = isset($BookMark)? '#' . $BookMark : '';
	$StrictXHTML=False;

	if (!headers_sent()){
		if ($StrictXHTML) {
			header('Content-type: application/xhtml+xml; charset=utf-8');
		} else {
			header('Content-type: text/html; charset=utf-8');
		}
	}
	if($Title == _('Copy a BOM to New Item Code')){//solve the cannot modify heaer information in CopyBOM.php scritps
		ob_start();
	}
	if ($StrictXHTML) {
		echo '<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />';
	} else {
		echo '<meta http-equiv="Content-Type" content="application/html; charset=utf-8" />';
	}
	echo '<link href="' . $RootPath . '/css/'. $_SESSION['Theme'] .'/default.css" rel="stylesheet" type="text/css" />';
	echo '<script type="text/javascript" src = "'.$RootPath.'/javascripts/MiscFunctions.js"></script>';
	echo '</head>';
	echo '<body>';

	echo '<div id="CanvasDiv">';
	echo '<div id="BodyDiv">';
	echo '<div id="BodyWrapDiv">';

//header.inc

include('includes/SQL_CommonFunctions.inc');
$ViewTopic = 'Inventory';
$BookMark = 'CreateRequest';
if(isset($_SESSION['Doc_Store'])){
$doc=$_SESSION['Doc_Store'];
}else{
$doc=$_SESSION['Document_id'];
}

if (isset($_GET['New'])) {
	unset($_SESSION['Transfer']);
	$_SESSION['Request'] = new StockRequest();
}

if (isset($_POST['Update'])) {
	$InputError=0;
	if ($_POST['Department']=='') {
		prnMsg( _('You must select a Department for the request'), 'error');
		$InputError=1;
	}
	$from = FormatDateForSQL($_POST['DispatchDate']);
	$now = FormatDateForSQL(date($_SESSION['DefaultDateFormat']));
	if($from < $now){
	prnMsg( _('Date When Required Can not be Earlier than Now'), 'error');
	$InputError=1;
	}
	if ($_POST['Location']=='') {
		prnMsg( _('You must select a Location to request the items from'), 'error');
		$InputError=1;
	}
	if ($InputError==0) {
		$_SESSION['Request']->Department=$_POST['Department'];
		$_SESSION['Request']->Location=$_POST['Location'];
		$_SESSION['Request']->DispatchDate=$_POST['DispatchDate'];
		$_SESSION['Request']->Narrative=$_POST['Narrative'];
	}
}

if (isset($_POST['Edit'])) {
	$_SESSION['Request']->LineItems[$_POST['LineNumber']]->Quantity=filter_number_format($_POST['Quantity']);
}

if (isset($_GET['Delete'])) {
	unset($_SESSION['Request']->LineItems[$_GET['Delete']]);
	echo '<br />';
	prnMsg( _('The line was successfully deleted'), 'success');
	echo '<br />';
}

foreach ($_POST as $key => $value) {
	if (mb_strstr($key,'StockID')) {
		$Index=mb_substr($key, 7);
		if (filter_number_format($_POST['Quantity'.$Index])>0) {
			$StockID=$value;
			$ItemDescription=$_POST['ItemDescription'.$Index];
			//$DecimalPlaces=$_POST['DecimalPlaces'.$Index];
			$DecimalPlaces=2;
			$NewItem_array[$StockID] = filter_number_format($_POST['Quantity'.$Index]);
			$NewItem_array1[$StockID]=$_POST['Units'.$Index];
			$_SESSION['Request']->AddLine($StockID, $ItemDescription, $NewItem_array[$StockID], $NewItem_array1[$StockID], $DecimalPlaces);
		}
	}
}

if (isset($_POST['Submit'])) {
	DB_Txn_Begin();
	$InputError=0;
	if ($_SESSION['Request']->Department=='') {
		prnMsg( _('You must select a Department for the request'), 'error');
		$InputError=1;
	}
	if ($_SESSION['Request']->Location=='') {
		prnMsg( _('You must select a Location to request the items from'), 'error');
		$InputError=1;
	}

	if ($InputError==0) {
		$RequestNo = GetNextTransNo(51, $db);
		/* $q='SELECT MAX(requestid) as id FROM irq_request';
			$Result = DB_query($q);
			$num= DB_num_rows($Result);
			if($num >0){
			$myrow = DB_fetch_array($Result);
			$RequestNo = $myrow['id']+1;
			}else{
			$RequestNo =1;
			} */
		
		if(isset($_POST['draft']) && $_POST['draft']==1){
			$state=1;
			}else{
			$state=0;
			$process=1;
			}
		$HeaderSQL="INSERT INTO irq_request (requestid,
											doc_id,
											Requesteddate,
											initiator,
											draft)
										VALUES(
											'" . $RequestNo . "',
											'" . $doc . "',
											'" . date('Y-m-d H:m:s') . "',
											'". $_SESSION['UserID'] ."',
											'" . $state . "')";
		$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($HeaderSQL,$ErrMsg,$DbgMsg,true);
		
		$HeaderSQL="INSERT INTO irq_stockrequest (dispatchid,
											loccode,
											departmentid,
											despatchdate,
											narrative)
										VALUES(
											'" . $RequestNo . "',
											'" . $_SESSION['Request']->Location . "',
											'" . $_SESSION['Request']->Department . "',
											'" . FormatDateForSQL($_SESSION['Request']->DispatchDate) . "',
											'" . $_SESSION['Request']->Narrative . "')";
		$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($HeaderSQL,$ErrMsg,$DbgMsg,true);

		foreach ($_SESSION['Request']->LineItems as $LineItems) {
			$LineSQL="INSERT INTO irq_stockrequestitems (dispatchitemsid,
													dispatchid,
													stockid,
													quantity,
													decimalplaces,
													uom)
												VALUES(
													'".$LineItems->LineNumber."',
													'".$RequestNo."',
													'".$LineItems->StockID."',
													'".$LineItems->Quantity."',
													'".$LineItems->DecimalPlaces."',
													'".$LineItems->UOM."')";
			$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request line record could not be inserted because');
			$DbgMsg = _('The following SQL to insert the request header record was used');
			$Result = DB_query($LineSQL,$ErrMsg,$DbgMsg,true);
		}
		
		if($state==0){
		$levelid =1 .$doc;
		$comment=($_SESSION['Request']->Narrative =="" ? "Process Initiator" : $_SESSION['Request']->Narrative);
	$HSQL="INSERT INTO irq_authorize_state (requisitionid,
											level,
											approvaldate,
											approver,
											approver_comment)
										VALUES(
											'" . $RequestNo . "',
											'" . $levelid . "',
											'" . date('Y-m-d H:i:s') . "',
											'" .$_SESSION['UsersRealName'] . "',
											'".$comment."')";
	$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($HSQL,$ErrMsg,$DbgMsg,true);
	
/******************************************************************************************/
		$EmailSQL="SELECT www_users.email, www_users.realname
					FROM www_users, irq_approvers, irq_levels, departments, locations
					WHERE irq_approvers.approver_id = irq_levels.approver_id AND 
						irq_levels.level_id = '" . $levelid ."' AND
						irq_levels.doc_id = '" . $doc ."' AND
						CASE WHEN irq_approvers.userid ='HOD' THEN departments.authoriser = www_users.userid and departments.departmentid ='". $_SESSION['Request']->Department ."' WHEN irq_approvers.userid ='ISSUE' THEN locations.authoriser=www_users.userid and locations.loccode='".$_SESSION['Request']->Location."' ELSE irq_approvers.userid = www_users.userid END
						LIMIT 1";
		$EmailResult =DB_query($EmailSQL);
		$nums = DB_num_rows($EmailResult);
		if ($nums>0){
		$myEmail=DB_fetch_array($EmailResult);
		 	//header('location:../IRQ_PDFGatepassPortrait.php?id='. $id .'&Email='. $myEmail['email'] .'&User='. $myEmail['realname'] .'&Ref=Inbox');
			
		include ('includes/htmlMimeMail.php');
		$mail = new htmlMimeMail();
		$mail->setText(_('Dear '.$myEmail['realname'].', Requisition Number '.$RequestNo.' has been created and is waiting for your authoritation. Please Login to the System for details.'));
		$mail->SetSubject('REQUISITION NEEDS YOUR AUTHORITATION');
		if($_SESSION['SmtpSetting'] == 0){
			$mail->setFrom($_SESSION['CompanyRecord']['coyname'] . ' <' . $_SESSION['CompanyRecord']['email'] . '>');
			$result = $mail->send(array($myEmail['email']));
		}else{
			$result = SendmailBySmtp($mail,array($myEmail['email']));
		}

		prnMsg( _('Success: Requisition No. '). $RequestNo . ' ' . _('has been forwarded to'). ' ' . $myEmail['realname'] . ' ' . _('and emailed to') . ' ' . $myEmail['email'], 'success');

		}else{
			prnMsg(  _('Success: Requisition No. '). $RequestNo. ' ' . _('has been forwarded for authoritation'), 'success');
			}
/*--------------------------------------------------------------------------------------------------*/
			
	}
	DB_Txn_Commit();
	if($state==1){
	prnMsg( _('The internal stock request has been saved as a Draft.'), 'success');
		
		}
	}
	unset($_SESSION['Request']);
	echo '<br /><div class="centre"><a href="'. htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Ref=Create-Request&New=Yes">' . _('Create another request') . '</a></div>';
			exit;
}


if (isset($_GET['Edit'])) {
	echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Ref=Create-Request" method="post">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table class="selection">';
	echo '<tr>
			<th colspan="2"><h4>' . _('Edit the Request Line') . '</h4></th>
		</tr>';
	echo '<tr>
			<td>' . _('Line number') . '</td>
			<td>' . $_SESSION['Request']->LineItems[$_GET['Edit']]->LineNumber . '</td>
		</tr>
		<tr>
			<td>' . _('Stock Code') . '</td>
			<td>' . $_SESSION['Request']->LineItems[$_GET['Edit']]->StockID . '</td>
		</tr>
		<tr>
			<td>' . _('Item Description') . '</td>
			<td>' . $_SESSION['Request']->LineItems[$_GET['Edit']]->ItemDescription . '</td>
		</tr>
		<tr>
			<td>' . _('Unit of Measure') . '</td>
			<td>' . $_SESSION['Request']->LineItems[$_GET['Edit']]->UOM . '</td>
		</tr>
		<tr>
			<td>' . _('Quantity Requested') . '</td>
			<td><input type="text" class="number" name="Quantity" value="' . locale_number_format($_SESSION['Request']->LineItems[$_GET['Edit']]->Quantity, $_SESSION['Request']->LineItems[$_GET['Edit']]->DecimalPlaces) . '" /></td>
		</tr>';
	echo '<input type="hidden" name="LineNumber" value="' . $_SESSION['Request']->LineItems[$_GET['Edit']]->LineNumber . '" />';
	echo '</table>
		<br />';
	echo '<div class="centre">
			<input type="submit" name="Edit" value="' . _('Update Line') . '" />
		</div>
        </div>
		</form>';

}

echo '<form action="'. htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Ref=Create-Request" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

?>
<div style="width:80%" class="centre">
	<table>
  <tr>
    <th colspan="2"><h4><?php if(isset($_SESSION['Doc_Store'])){ echo 'Store Requisition and Issue Voucher'; }else{ echo 'Request For Purchase or Services'; }?></h4></th>
  </tr>
  <tr>
    <td>Department</td>
						<?php
						
	// any internal department allowed
	if($_SESSION['AllowedDepartment'] == 0){
	// any internal department allowed
	$sql="SELECT departmentid,
				description
			FROM departments
			ORDER BY description";
}else{
	// just 1 internal department allowed
	$sql="SELECT departmentid,
				description
			FROM departments
			WHERE departmentid = '". $_SESSION['AllowedDepartment'] ."'
			ORDER BY description";
}
$result=DB_query($sql);
echo '<td><select name="Department">';
echo '<option selected="selected" value="">--Please Select Requesting Department--</option>';
while ($myrow=DB_fetch_array($result)){
	if (isset($_SESSION['Request']->Department) AND $_SESSION['Request']->Department==$myrow['departmentid']){
		echo '<option selected="True" value="' . $myrow['departmentid'] . '">' . htmlspecialchars($myrow['description'], ENT_QUOTES,'UTF-8') . '</option>';
	} else {
		echo '<option value="' . $myrow['departmentid'] . '">' . htmlspecialchars($myrow['description'], ENT_QUOTES,'UTF-8') . '</option>';
	}
}
echo '</select>';
echo '</td>';

echo '<tr>
		<td>' . _('Location from which to request stock') . ':</td>';
$sql="SELECT locations.loccode,
			locationname
		FROM locations
		INNER JOIN locationusers ON locationusers.loccode=locations.loccode AND locationusers.userid='" .  $_SESSION['UserID'] . "' AND locationusers.canupd=1
		WHERE internalrequest = 1
		ORDER BY locationname";

$result=DB_query($sql);
echo '<td><select name="Location">
		<option value="">' . _('Select a Location') . '</option>';
while ($myrow=DB_fetch_array($result)){
	if (isset($_SESSION['Request']->Location) AND $_SESSION['Request']->Location==$myrow['loccode']){
		echo '<option selected="True" value="' . $myrow['loccode'] . '">' . $myrow['loccode'].' - ' .htmlspecialchars($myrow['locationname'], ENT_QUOTES,'UTF-8') . '</option>';
	} else {
		echo '<option value="' . $myrow['loccode'] . '">' . $myrow['loccode'].' - ' .htmlspecialchars($myrow['locationname'], ENT_QUOTES,'UTF-8') . '</option>';
	}
}
echo '</select></td>
	</tr>';					
?>
					
  </tr>
  <tr>
    <td>Date When Required</td>
    <td><?php echo '<input type="text" class="date" alt="'.$_SESSION['DefaultDateFormat'].'" name="DispatchDate" maxlength="10" size="11" value="' . $_SESSION['Request']->DispatchDate . '" />'; ?></td>
  </tr>
  <tr>
    <td>Reason for Requirement</td>
    <td><?php echo '<textarea name="Narrative" cols="40" rows="5">' . $_SESSION['Request']->Narrative . '</textarea>'; ?></td>
  </tr>
	
</table>
</div>
 <div class="centre">
		<input type="submit" name="Update" value="Update" />
  </div>	
		<br />
<?php
	echo '</form>';
	
if (isset($_SESSION['Request']->Location)) {


$i = 0; //Line Item Array pointer
echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Ref=Create-Request" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<br />
	<table class="selection">
	<tr>
		<th colspan="7"><h4>' . _('Details of Items Requested') . '</h4></th>
	</tr>
	<tr>
		<th>' .  _('Line Number') . '</th>
		<th class="ascending">' .  _('Item Code') . '</th>
		<th class="ascending">' .  _('Item Description'). '</th>
		<th class="ascending">' .  _('Quantity Required'). '</th>
		<th>' .  _('UOM'). '</th>
	</tr>';

$k=0;

foreach ($_SESSION['Request']->LineItems as $LineItems) {

	if ($k==1){
		echo '<tr class="EvenTableRows">';
		$k=0;
	} else {
		echo '<tr class="OddTableRows">';
		$k++;
	}
	echo '<td>' . $LineItems->LineNumber . '</td>
			<td>' . $LineItems->StockID . '</td>
			<td>' . $LineItems->ItemDescription . '</td>
			<td class="number">' . locale_number_format($LineItems->Quantity, $LineItems->DecimalPlaces) . '</td>
			<td>' . $LineItems->UOM . '</td>
			<td><a href="'. htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Ref=Create-Request&Edit='.$LineItems->LineNumber.'">' . _('Edit') . '</a></td>
			<td><a href="'. htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Ref=Create-Request&Delete='.$LineItems->LineNumber.'">' . _('Delete') . '</a></td>
		</tr>';
}

echo '</table>';
if(count($_SESSION['Request']->LineItems) >0){
echo '<br />
	<div class="centre">
		<strong>Save as Draft: 
		<input name="draft" type="checkbox" value="1" />
	    </strong><br />
	<br />
	<div class="centre">
		<input type="submit" name="Submit" value="' . _('Submit') . '" />
	</div>
	<br />
    </div>';
	}
echo '</form>';

echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Ref=Create-Request" method="post">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/magnifier.png" title="' . _('Search') . '" alt="" />' . ' ' . _('Search for Inventory Items'). '</p>';
$SQL = "SELECT stockcategory.categoryid,
				stockcategory.categorydescription
			FROM stockcategory, internalstockcatrole
			WHERE stockcategory.categoryid = internalstockcatrole.categoryid
				AND internalstockcatrole.secroleid= " . $_SESSION['AccessLevel'] . "
			ORDER BY stockcategory.categorydescription";
$result1 = DB_query($SQL);
if (DB_num_rows($result1) == 0) {
	echo '<p class="bad">' . _('Problem Report') . ':<br />' . _('There are no stock categories currently defined please use the link below to set them up') . '</p>';
	echo '<br />
		<a href="' . $RootPath . '/StockCategories.php">' . _('Define Stock Categories') . '</a>';
	exit;
}
echo '<table class="selection">
	<tr>
		<td>' . _('In Stock Category') . ':<select name="StockCat">';

if (!isset($_POST['StockCat'])) {
	$_POST['StockCat'] = '';
}
if ($_POST['StockCat'] == 'All') {
	echo '<option selected="True" value="All">' . _('All Authorized') . '</option>';
} else {
	echo '<option value="All">' . _('All Authorized') . '</option>';
}
while ($myrow1 = DB_fetch_array($result1)) {
	if ($myrow1['categoryid'] == $_POST['StockCat']) {
		echo '<option selected="True" value="' . $myrow1['categoryid'] . '">' . $myrow1['categorydescription'] . '</option>';
	} else {
		echo '<option value="' . $myrow1['categoryid'] . '">' . $myrow1['categorydescription'] . '</option>';
	}
}
echo '</select></td>
	<td>' . _('Enter partial') . '<b> ' . _('Description') . '</b>:</td>';
if (isset($_POST['Keywords'])) {
	echo '<td><input type="text" name="Keywords" value="' . $_POST['Keywords'] . '" size="20" maxlength="25" /></td>';
} else {
	echo '<td><input type="text" name="Keywords" size="20" maxlength="25" /></td>';
}
echo '</tr>
		<tr>
			<td></td>
			<td><h3>' . _('OR') . ' ' . '</h3>' . _('Enter partial') . ' <b>' . _('Stock Code') . '</b>:</td>';

if (isset($_POST['StockCode'])) {
	echo '<td><input type="text" autofocus="autofocus" name="StockCode" value="' . $_POST['StockCode'] . '" size="15" maxlength="18" /></td>';
} else {
	echo '<td><input type="text" name="StockCode" size="15" maxlength="18" /></td>';
}
echo '</tr>
	</table>
	<br />
	<div class="centre">
		<input type="submit" name="Search" value="' . _('Search Now') . '" />
	</div>
	<br />
	</div>
	</form>';

if (isset($_POST['Search']) or isset($_POST['Next']) or isset($_POST['Prev'])){

	if ($_POST['Keywords']!='' AND $_POST['StockCode']=='') {
		prnMsg ( _('Order Item description has been used in search'), 'warn' );
	} elseif ($_POST['StockCode']!='' AND $_POST['Keywords']=='') {
		prnMsg ( _('Stock Code has been used in search'), 'warn' );
	} elseif ($_POST['Keywords']=='' AND $_POST['StockCode']=='') {
		prnMsg ( _('Stock Category has been used in search'), 'warn' );
	}
	if (isset($_POST['Keywords']) AND mb_strlen($_POST['Keywords'])>0) {
		//insert wildcard characters in spaces
		$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
		$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';

		if ($_POST['StockCat']=='All'){
			$SQL = "SELECT stockmaster.stockid,
							stockmaster.description,
							stockmaster.units as stockunits,
							stockmaster.decimalplaces
					FROM stockmaster,
						stockcategory,
						internalstockcatrole
					WHERE stockmaster.categoryid=stockcategory.categoryid
						AND stockcategory.categoryid = internalstockcatrole.categoryid
						AND internalstockcatrole.secroleid= " . $_SESSION['AccessLevel'] . "
						AND stockmaster.description " . LIKE . " '" . $SearchString . "'
						AND stockmaster.discontinued=0
					ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
							stockmaster.description,
							stockmaster.units as stockunits,
							stockmaster.decimalplaces
					FROM stockmaster,
						stockcategory,
						internalstockcatrole
					WHERE stockmaster.categoryid=stockcategory.categoryid
						AND stockcategory.categoryid = internalstockcatrole.categoryid
						AND internalstockcatrole.secroleid= " . $_SESSION['AccessLevel'] . "
						AND stockmaster.discontinued=0
						AND stockmaster.description " . LIKE . " '" . $SearchString . "'
						AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					ORDER BY stockmaster.stockid";
		}

	} elseif (mb_strlen($_POST['StockCode'])>0){

		$_POST['StockCode'] = mb_strtoupper($_POST['StockCode']);
		$SearchString = '%' . $_POST['StockCode'] . '%';

		if ($_POST['StockCat']=='All'){
			$SQL = "SELECT stockmaster.stockid,
							stockmaster.description,
							stockmaster.units as stockunits,
							stockmaster.decimalplaces
					FROM stockmaster,
						stockcategory,
						internalstockcatrole
					WHERE stockmaster.categoryid=stockcategory.categoryid
						AND stockcategory.categoryid = internalstockcatrole.categoryid
						AND internalstockcatrole.secroleid= " . $_SESSION['AccessLevel'] . "
						AND stockmaster.stockid " . LIKE . " '" . $SearchString . "'
						AND stockmaster.discontinued=0
					ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
							stockmaster.description,
							stockmaster.units as stockunits,
							stockmaster.decimalplaces
					FROM stockmaster,
						stockcategory,
						internalstockcatrole
					WHERE stockmaster.categoryid=stockcategory.categoryid
						AND stockcategory.categoryid = internalstockcatrole.categoryid
						AND internalstockcatrole.secroleid= " . $_SESSION['AccessLevel'] . "
						AND stockmaster.stockid " . LIKE . " '" . $SearchString . "'
						AND stockmaster.discontinued=0
						AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					ORDER BY stockmaster.stockid";
		}

	} else {
		if ($_POST['StockCat']=='All'){
			$SQL = "SELECT stockmaster.stockid,
							stockmaster.description,
							stockmaster.units as stockunits,
							stockmaster.decimalplaces
					FROM stockmaster,
						stockcategory,
						internalstockcatrole
					WHERE stockmaster.categoryid=stockcategory.categoryid
						AND stockcategory.categoryid = internalstockcatrole.categoryid
						AND internalstockcatrole.secroleid= " . $_SESSION['AccessLevel'] . "
						AND stockmaster.discontinued=0
					ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
							stockmaster.description,
							stockmaster.units as stockunits,
							stockmaster.decimalplaces
					FROM stockmaster,
						stockcategory,
						internalstockcatrole
					WHERE stockmaster.categoryid=stockcategory.categoryid
						AND stockcategory.categoryid = internalstockcatrole.categoryid
						AND internalstockcatrole.secroleid= " . $_SESSION['AccessLevel'] . "
						AND stockmaster.discontinued=0
						AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					ORDER BY stockmaster.stockid";
		}
	}

	if (isset($_POST['Next'])) {
		$Offset = $_POST['NextList'];
	}
	if (isset($_POST['Prev'])) {
		$Offset = $_POST['Previous'];
	}
	if (!isset($Offset) or $Offset<0) {
		$Offset=0;
	}
	$SQL = $SQL . ' LIMIT ' . $_SESSION['DefaultDisplayRecordsMax'] . ' OFFSET ' . ($_SESSION['DefaultDisplayRecordsMax']*$Offset);

	$ErrMsg = _('There is a problem selecting the part records to display because');
	$DbgMsg = _('The SQL used to get the part selection was');
	$SearchResult = DB_query($SQL,$ErrMsg, $DbgMsg);

	if (DB_num_rows($SearchResult)==0 ){
		prnMsg (_('There are no products available meeting the criteria specified'),'info');
	}
	if (DB_num_rows($SearchResult)<$_SESSION['DisplayRecordsMax']){
		$Offset=0;
	}

} //end of if search
/* display list if there is more than one record */
if (isset($searchresult) AND !isset($_POST['Select'])) {
	echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Ref=Create-Request" method="post">';
    echo '<div>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	$ListCount = DB_num_rows($searchresult);
	if ($ListCount > 0) {
		// If the user hit the search button and there is more than one item to show
		$ListPageMax = ceil($ListCount / $_SESSION['DisplayRecordsMax']);
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
		if ($_POST['PageOffset'] > $ListPageMax) {
			$_POST['PageOffset'] = $ListPageMax;
		}
		if ($ListPageMax > 1) {
			echo '<div class="centre"><br />&nbsp;&nbsp;' . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';
			echo '<select name="PageOffset">';
			$ListPage = 1;
			while ($ListPage <= $ListPageMax) {
				if ($ListPage == $_POST['PageOffset']) {
					echo '<option value=' . $ListPage . ' selected>' . $ListPage . '</option>';
				} else {
					echo '<option value=' . $ListPage . '>' . $ListPage . '</option>';
				}
				$ListPage++;
			}
			echo '</select>
				<input type="submit" name="Go" value="' . _('Go') . '" />
				<input type="submit" name="Previous" value="' . _('Previous') . '" />
				<input type="submit" name="Next" value="' . _('Next') . '" />
				<input type="hidden" name=Keywords value="'.$_POST['Keywords'].'" />
				<input type="hidden" name=StockCat value="'.$_POST['StockCat'].'" />
				<input type="hidden" name=StockCode value="'.$_POST['StockCode'].'" />
				<br />
				</div>';
		}
		echo '<table cellpadding="2">';
		echo '<tr>
				<th>' . _('Code') . '</th>
				<th>' . _('Description') . '</th>
				<th>' . _('Total Qty On Hand') . '</th>
				<th>' . _('Units') . '</th>
				<th>' . _('Stock Status') . '</th>
			</tr>';
		$j = 1;
		$k = 0; //row counter to determine background colour
		$RowIndex = 0;
		if (DB_num_rows($searchresult) <> 0) {
			DB_data_seek($searchresult, ($_POST['PageOffset'] - 1) * $_SESSION['DisplayRecordsMax']);
		}
		while (($myrow = DB_fetch_array($searchresult)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {
			if ($k == 1) {
				echo '<tr class="EvenTableRows">';
				$k = 0;
			} else {
				echo '<tr class="OddTableRows">';
				$k++;
			}
			if ($myrow['mbflag'] == 'D') {
				$qoh = _('N/A');
			} else {
				$qoh = locale_number_format($myrow['qoh'], $myrow['decimalplaces']);
			}
			if ($myrow['discontinued']==1){
				$ItemStatus = '<p class="bad">' . _('Obsolete') . '</p>';
			} else {
				$ItemStatus ='';
			}

			echo '<td><input type="submit" name="Select" value="' . $myrow['stockid'] . '" /></td>
					<td>' . $myrow['description'] . '</td>
					<td class="number">' . $qoh . '</td>
					<td>' . $myrow['units'] . '</td>
					<td><a target="_blank" href="' . $RootPath . '/StockStatus.php?StockID=' . $myrow['stockid'].'">' . _('View') . '</a></td>
					<td>' . $ItemStatus . '</td>
				</tr>';
			//end of page full new headings if
		}
		//end of while loop
		echo '</table>
              </div>
              </form>
              <br />';
	}
}
/* end display list if there is more than one record */

if (isset($SearchResult)) {
	$j = 1;
	echo '<br />
		<div class="page_help_text">' . _('Select an item by entering the quantity required.  Click Order when ready.') . '</div>
		<br />
		<form action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?Ref=Create-Request" method="post" id="orderform">
		<div>
		<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
		<table class="table1">
		<tr>
			<td>
				<input type="hidden" name="Previous" value="'.($Offset-1).'" />
				<input tabindex="'.($j+8).'" type="submit" name="Prev" value="'._('Prev').'" /></td>
				<td style="text-align:center" colspan="6">
				<input type="hidden" name="order_items" value="1" />
				<input tabindex="'.($j+9).'" type="submit" value="'._('Add to Requisition').'" /></td>
			<td>
				<input type="hidden" name="NextList" value="'.($Offset+1).'" />
				<input tabindex="'.($j+10).'" type="submit" name="Next" value="'._('Next').'" /></td>
			</tr>
			<tr>
				<th class="ascending">' . _('Code') . '</th>
				<th class="ascending">' . _('Description') . '</th>
				<th>' . _('Units') . '</th>
				<th class="ascending">' . _('On Hand') . '</th>
				<th class="ascending">' . _('On Demand') . '</th>
				<th class="ascending">' . _('On Order') . '</th>
				<th class="ascending">' . _('Available') . '</th>
				<th class="ascending">' . _('Quantity') . '</th>
			</tr>';
	$ImageSource = _('No Image');

	$k=0; //row colour counter
	$i=0;
	while ($myrow=DB_fetch_array($SearchResult)) {
		if ($myrow['decimalplaces']=='') {
			$DecimalPlacesSQL="SELECT decimalplaces
								FROM stockmaster
								WHERE stockid='" .$myrow['stockid'] . "'";
			$DecimalPlacesResult = DB_query($DecimalPlacesSQL);
			$DecimalPlacesRow = DB_fetch_array($DecimalPlacesResult);
			$DecimalPlaces = $DecimalPlacesRow['decimalplaces'];
		} else {
			$DecimalPlaces=$myrow['decimalplaces'];
		}

		$QOHSQL = "SELECT sum(locstock.quantity) AS qoh
							   FROM locstock
							   WHERE locstock.stockid='" .$myrow['stockid'] . "' AND
							   loccode = '" . $_SESSION['Request']->Location . "'";
		$QOHResult =  DB_query($QOHSQL);
		$QOHRow = DB_fetch_array($QOHResult);
		$QOH = $QOHRow['qoh'];

		// Find the quantity on outstanding sales orders
		$sql = "SELECT SUM(salesorderdetails.quantity-salesorderdetails.qtyinvoiced) AS dem
				 FROM salesorderdetails INNER JOIN salesorders
				 ON salesorders.orderno = salesorderdetails.orderno
				 WHERE salesorders.fromstkloc='" . $_SESSION['Request']->Location . "'
				 AND salesorderdetails.completed=0
				 AND salesorders.quotation=0
				 AND salesorderdetails.stkcode='" . $myrow['stockid'] . "'";
		$ErrMsg = _('The demand for this product from') . ' ' . $_SESSION['Request']->Location . ' ' . _('cannot be retrieved because');
		$DemandResult = DB_query($sql,$ErrMsg);

		$DemandRow = DB_fetch_row($DemandResult);
		if ($DemandRow[0] != null){
			$DemandQty =  $DemandRow[0];
		} else {
		  $DemandQty = 0;
		}

		// Find the quantity on purchase orders
		$sql = "SELECT SUM(purchorderdetails.quantityord-purchorderdetails.quantityrecd)*purchorderdetails.conversionfactor AS dem
				 FROM purchorderdetails LEFT JOIN purchorders
					ON purchorderdetails.orderno=purchorders.orderno
				 WHERE purchorderdetails.completed=0
				 AND purchorders.status<>'Cancelled'
				 AND purchorders.status<>'Rejected'
				 AND purchorders.status<>'Completed'
				AND purchorderdetails.itemcode='" . $myrow['stockid'] . "'";

		$ErrMsg = _('The order details for this product cannot be retrieved because');
		$PurchResult = DB_query($sql,$ErrMsg);

		$PurchRow = DB_fetch_row($PurchResult);
		if ($PurchRow[0]!=null){
			$PurchQty =  $PurchRow[0];
		} else {
			$PurchQty = 0;
		}

		// Find the quantity on works orders
		$sql = "SELECT SUM(woitems.qtyreqd - woitems.qtyrecd) AS dedm
			   FROM woitems
			   WHERE stockid='" . $myrow['stockid'] ."'";
		$ErrMsg = _('The order details for this product cannot be retrieved because');
		$WoResult = DB_query($sql,$ErrMsg);

		$WoRow = DB_fetch_row($WoResult);
		if ($WoRow[0]!=null){
			$WoQty =  $WoRow[0];
		} else {
			$WoQty = 0;
		}

		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}
		$OnOrder = $PurchQty + $WoQty;
		$Available = $QOH - $DemandQty + $OnOrder;
		echo '<td>' . $myrow['stockid'] . '</td>
				<td>' . $myrow['description'] . '</td>
				<td>' . $myrow['stockunits'] . '</td>
				<td class="number">' . locale_number_format($QOH,$DecimalPlaces) . '</td>
				<td class="number">' . locale_number_format($DemandQty,$DecimalPlaces) . '</td>
				<td class="number">' . locale_number_format($OnOrder, $DecimalPlaces) . '</td>
				<td class="number">' . locale_number_format($Available,$DecimalPlaces) . '</td>
				<td><input class="number" ' . ($i==0 ? 'autofocus="autofocus"':'') . ' tabindex="'.($j+7).'" type="text" size="6" name="Quantity'.$i.'" value="0" />
				<input type="hidden" name="StockID'.$i.'" value="'.$myrow['stockid'].'" />
				</td>
			</tr>';
		echo '<input type="hidden" name="DecimalPlaces'.$i.'" value="' . $myrow['decimalplaces'] . '" />';
		echo '<input type="hidden" name="ItemDescription'.$i.'" value="' . $myrow['description'] . '" />';
		echo '<input type="hidden" name="Units'.$i.'" value="' . $myrow['stockunits'] . '" />';
		$i++;
	}
#end of while loop
	echo '<tr>
			<td><input type="hidden" name="Previous" value="'.($Offset-1).'" />
				<input tabindex="'.($j+7).'" type="submit" name="Prev" value="'._('Prev').'" /></td>
			<td style="text-align:center" colspan="6"><input type="hidden" name="order_items" value="1" />
				<input tabindex="'.($j+8).'" type="submit" value="'._('Add to Requisition').'" /></td>
			<td><input type="hidden" name="NextList" value="'.($Offset+1).'" />
				<input tabindex="'.($j+9).'" type="submit" name="Next" value="'._('Next').'" /></td>
		<tr/>
		</table>
       </div>
       </form>';
}#end if SearchResults to show
}
//*********************************************************************************************************
include('includes/footer.inc');

?>
