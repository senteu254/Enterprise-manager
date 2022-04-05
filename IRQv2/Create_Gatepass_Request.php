<?php 
if($_POST['Submit']){
if($_POST['document'] ==7){
if($_POST['document'] ==''){
$error = '<br />Document Type can not be blank';
}
if(empty($_POST['building'])){
$error .= '<br />Building can not be blank';
}
if(empty($_POST['Department'])){
$error .= '<br />Department can not be blank';
}
if($_POST['idno'] !=""){
if(!is_numeric($_POST['idno'])){
$error .= '<br />Invalid ID Number Please Check and re-enter it';
}
}

}else{
if(empty($_POST['bearer'])){
$error = '<br />Bearer Name can not be blank';
}
if(empty($_POST['Department'])){
$error .= '<br />Department can not be blank';
}
if(empty($_POST['destination'])){
$error .= '<br />Item Destination can not be blank';
}
}

if(isset($error)){
echo prnMsg ( _($error), 'warn' );
}else{
if(isset($_POST['draft']) && $_POST['draft']==1){
$draft=1;
}else{
$draft=0;
}
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
	$SQL="INSERT INTO irq_request (requestid,
									doc_id,
									draft,
									closed,
									initiator,
									Requesteddate) 
							VALUES('". $RequestNo ."',
									'". $_POST['document'] ."',
									'". $draft ."',
									'0',
									'". $_SESSION['UserID'] ."',
									'". date('Y-m-d H:i:s') ."')";
					$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
	
	if($_POST['document'] ==7){	
			$SQL="INSERT INTO irq_gatepass (gatepassid,
									departmentid,
									building,
									vregno,
									vtype,
									vcompany,
									driver_name,
									licenseno,
									passanger_name,
									passanger_idno,
									deliveryno,
									invoiceno,
									destination,
									requesting_officer) 
							VALUES('". $RequestNo ."',
									'". $_POST['Department'] ."',
									'". $_POST['building'] ."',
									'". $_POST['regno'] ."',
									'". $_POST['type'] ."',
									'". $_POST['company'] ."',
									'". $_POST['driver'] ."',
									'". $_POST['license'] ."',
									'". $_POST['passenger'] ."',
									'". $_POST['idno'] ."',
									'". $_POST['DNo'] ."',
									'". $_POST['invoiceno'] ."',
									'". $_POST['destination'] ."',
									'". $_SESSION['UsersRealName'] ."')";
					$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);	
		}//end of external gatepass entry
		else{
		$SQL="INSERT INTO irq_gatepass (gatepassid,
									driver_name,
									vregno,
									vtype,
									departmentid,
									destination,
									requesting_officer) 
							VALUES('". $RequestNo ."',
									'". $_POST['bearer'] ."',
									'". $_POST['regno'] ."',
									'". $_POST['type'] ."',
									'". $_POST['Department'] ."',
									'". $_POST['destination'] ."',
									'". addslashes($_SESSION['UsersRealName']) ."')";
					$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);	
		}//end of internal gate pass entry

for($i=0;$i<count($_POST['sno']);$i++)
{
	$sno= $_POST['sno'][$i];
	$qty = $_POST['qty'][$i];
	$desc = $_POST['description'][$i];
	$no = $i+1;	
	$SQL="INSERT INTO irq_gatepass_items (id,
									gatepassid,
									sno,
									qty,
									description) 
							VALUES('". $no ."',
									'". $RequestNo ."',
									'". $sno ."',
									'". $qty ."',
									'". $desc ."')";
					$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		}
		
		if($draft==0){
		
				$levelid=1 .$_POST['document'];
			$HSQL="INSERT INTO irq_authorize_state (requisitionid,
														level,
														approvaldate,
														approver,
														approver_comment)
													VALUES(
														'" . $RequestNo . "',
														'" . $levelid . "',
														'" . date('Y-m-d H:i:s') . "',
														'" . addslashes($_SESSION['UsersRealName']). "',
														'".$_POST['comment']."')";
		$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($HSQL,$ErrMsg,$DbgMsg,true);
		
		/******************************************************************************************/
		/*$EmailSQL="SELECT www_users.email, www_users.realname
					FROM www_users, irq_approvers, irq_levels, departments, locations
					WHERE irq_approvers.approver_id = irq_levels.approver_id AND 
						irq_levels.level_id = '" . $levelid ."' AND
						irq_levels.doc_id = '" . $_POST['document'] ."' AND
						CASE WHEN irq_approvers.userid ='HOD' THEN departments.authoriser = www_users.userid and departments.departmentid ='". $_POST['Department'] ."' WHEN irq_approvers.userid ='ISSUE' THEN locations.authoriser=www_users.userid ELSE irq_approvers.userid = www_users.userid END
						LIMIT 1";
		$EmailResult =DB_query($EmailSQL);
		$nums = DB_num_rows($EmailResult);
		if ($nums>0){
		$myEmail=DB_fetch_array($EmailResult);
		 	//header('location:IRQ_MaintenanceRequest.php?id='. $RequestNo .'&Email='. $myEmail['email'] .'&User='. $myEmail['realname'] .'&Ref=Create-Request&New=Yes');
			
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
		echo _('<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success</h4>Success: Requisition No. '). $RequestNo . ' ' . _('has been forwarded to'). ' ' . $myEmail['realname'] . ' ' . _('and emailed to') . ' ' . $myEmail['email'].'</div>';		
		}else{
			echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success</h4>Request No. '.$RequestNo.' has been created and forwarded for authoritation</div>';
			}*/
		echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success</h4>Request No. '.$RequestNo.' has been created and forwarded for authoritation</div>';
		/*--------------------------------------------------------------------------------------------------*/
		}else{
		echo _('<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success</h4>Gate Pass Request No. '.$RequestNo.' has been saved as a Draft.</div>');
		}
		unset($_POST);
}
}
		
			
?>
<style type="text/css">
input[type='button'], button {
    background-color:#34a7e8;
    border:thin outset #1992DA;
    padding:6px 24px;
    vertical-align:middle;
    font-weight:bold;
    color:#FFFFFF;
    cursor: pointer;
    
    -webkit-border-radius:  4px;
    -moz-border-radius:     4px;
    border-radius:          4px;
    -moz-box-shadow:    1px 1px 1px #64BEF1 inset;
	-webkit-box-shadow: 1px 1px 1px #64BEF1 inset;
	box-shadow:         1px 1px 1px #64BEF1 inset;
}
</style>
<span class="pull-right">
<?php  
if(!isset($_POST['document'])){
$doc =7;
}else{
$doc = $_POST['document'];
}			
		$titles="SELECT approver_name FROM irq_levels a
	 			INNER JOIN irq_approvers b ON a.approver_id=b.approver_id
				WHERE a.doc_id=".$doc."
				GROUP BY a.level_id ORDER BY a.level_id ASC";
$first=1;
$tit = array();
$titleresults= DB_query($titles);
$tit[] ='USER';
while($title=DB_fetch_array($titleresults)){
$tit[] = $title['approver_name'];
}

for($i=0; $i < count($tit); $i++) {
$color = 'color:#CCCCCC;';
$check ='<i class="fa fa-circle-o fa-2x" style="color:#CCCCCC;" aria-hidden="true"></i>';
if($i !=0){
echo '&nbsp;<i class="fa fa-arrow-right" style="'.$color.'"></i>';
}
echo '<span class="fa-stack fa-2x" style="font-size:17px;">';
echo $check; 
echo '</span>';
echo '<span style="font-size:10px;'.$color.'">'.strtoupper($tit[$i]).'</span>';; 
}
?>
</span>
<div align="center">
<form action="" name="form" method="post" enctype="multipart/form-data" >
<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
<table class="selection table" style="width:90%">
  <tr>
    <th colspan="4"><center><h4>Gate Pass  Requisition Proformae</h4></center></th>
  </tr>
  <tr>
    <th colspan="4"> <div align="center">
        <table style="width:50%">
          <tr>
            <td rowspan="2">Document</td>
            <td><label>
              <input name="document" type="radio" value="8" <?=8 == ''.$_POST['document'].'' ? ' checked="checked"' : '';?> />
              Internal Gate Pass </label></td>
          </tr>
          <tr>
            <td><label>
              <input type="radio" <?=7 == ''.$_POST['document'].'' ? ' checked="checked"' : '';?> name="document" value="7" />
              External Gate Pass </label></td>
          </tr>
		   <tr>
		   <td></td>
            <td><select name="option" required onchange="this.form.submit();">
			<?php if($_POST['option']==1){
				echo '<option value="">--Choose Option--</option>
			<option value="1" selected="selected">With Delivery Note</option>
			<option value="2">Without Delivery Note</option>';
			}elseif($_POST['option']==2){
				echo '<option value="">--Choose Option--</option>
			<option value="1">With Delivery Note</option>
			<option value="2" selected="selected">Without Delivery Note</option>';
			}else{
			echo '<option value="" selected="selected">--Choose Option--</option>
			<option value="1">With Delivery Note</option>
			<option value="2">Without Delivery Note</option>';
			}
			  ?>
			</select></td>
          </tr>
		  <tr><td colspan="2"><center><input name="SelectDocument" type="submit" value="Select" /></center></td></tr>
        </table>
    </div></th>
  </tr>
  <tr>
    <th colspan="4">
	<!------------------------------------------------------------------------------------------------------->
	<?php
	if(isset($_POST['document']) && $_POST['document']==7){
	?>
	<div align="center">
      <div style="width:90%;">
        <table>
  <tr>
    <td width="30%">From Department</td>
    <?php
						
	// any internal department allowed
	// any internal department allowed
	$sql="SELECT departmentid,
				description
			FROM departments
			ORDER BY description";
$result=DB_query($sql);
echo '<td><select style="width:240px;" name="Department" >';
//echo '<option selected="selected" value="">--Select Requesting Dept--</option>';
while ($myrow=DB_fetch_array($result)){
	if (isset($_POST['Department']) AND $_POST['Department']==$myrow['departmentid']){
		echo '<option selected="True" value="' . $myrow['departmentid'] . '">' . htmlspecialchars($myrow['description'], ENT_QUOTES,'UTF-8') . '</option>';
	} else {
		echo '<option value="' . $myrow['departmentid'] . '">' . htmlspecialchars($myrow['description'], ENT_QUOTES,'UTF-8') . '</option>';
	}
}
echo '</select>';
echo '</td>';
?>
    <td width="30%">From Building</td>
    <td ><input style="width:140px;" value="<?php echo $_POST['building']; ?>" name="building" type="text" /></td>
  </tr>
  <tr>
    <td>Vehicle Reg. No</td>
    <td><input  style="width:170px;" value="<?php echo $_POST['regno']; ?>" name="regno" type="text" /></td>
    <td>Make/Type</td>
    <td><input  style="width:140px;" value="<?php echo $_POST['type']; ?>" name="type" type="text" /></td>
  </tr>
  <tr>
    <td>Owner/Company of Vehicle</td>
    <td colspan="3"><input  style="width:300px;" value="<?php echo $_POST['company']; ?>" name="company" type="text" /></td>
  </tr>
  <tr>
    <td>Driver's Name</td>
    <td><input  style="width:170px;" value="<?php echo $_POST['driver']; ?>" name="driver" type="text" /></td>
    <td>License No.</td>
    <td><input  style="width:140px;" value="<?php echo $_POST['license']; ?>" name="license" type="text" /></td>
  </tr>
  <tr>
    <td>Senior Passenger's Name</td>
    <td><input  style="width:170px;" value="<?php echo $_POST['passenger']; ?>" name="passenger" type="text" /></td>
    <td>ID No.</td>
    <td><input  style="width:140px;" value="<?php echo $_POST['idno']; ?>" name="idno" type="text" /></td>
  </tr>
  <tr>
    <td>Destination:</td>
    <td><input  style="width:170px;" value="<?php echo $_POST['destination']; ?>" name="destination" type="text" /></td>
	<td>Invoice No.</td>
    <td><input  style="width:140px;" value="<?php echo $_POST['invoiceno']; ?>" name="invoiceno" type="text" /></td>
  </tr>
   </table>
      </div>
    </div>
<!------------------------------------------------------------------------------------------------------->
	<?php
	echo '<center><textarea name="comment" placeholder="Write your Comment here..." style="width:80%" rows="2">'.$_POST['comment'].'</textarea></center>';
	}elseif(isset($_POST['document']) && $_POST['document']==8){
	?>
	<div align="center">
      <div style="width:80%;">
        <table>
  <tr>
    <th>Name of the Bearer </th>
    <td colspan="3"><input style="width:350px;" value="<?php echo $_POST['bearer']; ?>" name="bearer" type="text" /></td>
  </tr>
  <tr>
    <th>From Department</th>
	<?php
						
	// any internal department allowed
	$sql="SELECT departmentid,
				description
			FROM departments
			ORDER BY description";

$result=DB_query($sql);
echo '<td colspan="3"><select name="Department">';
echo '<option selected="selected" value="">--Please Select Requesting Department--</option>';
while ($myrow=DB_fetch_array($result)){
	if (isset($_POST['Department']) AND $_POST['Department']==$myrow['departmentid']){
		echo '<option selected="True" value="' . $myrow['departmentid'] . '">' . htmlspecialchars($myrow['description'], ENT_QUOTES,'UTF-8') . '</option>';
	} else {
		echo '<option value="' . $myrow['departmentid'] . '">' . htmlspecialchars($myrow['description'], ENT_QUOTES,'UTF-8') . '</option>';
	}
}
echo '</select>';
echo '</td>';
?>
	</tr>
	<tr>
	 <th>Item(s) Destination</th>
    <td colspan="3"><input  style="width:350px;" value="<?php echo $_POST['destination']; ?>" name="destination" type="text" /></td>
  </tr>
  <tr>
    <th>Vehicle Reg. No</th>
    <td><input  style="width:160px;" value="<?php echo $_POST['regno']; ?>" name="regno" type="text" /></td>
    <th>Make/Type</th>
    <td><input  style="width:160px;" value="<?php echo $_POST['type']; ?>" name="type" type="text" /></td>
  </tr>
   </table>
      </div>
    </div>
	<?php
	echo '<center><textarea name="comment" placeholder="Write your Comment here..." style="width:80%" rows="2">'.$_POST['comment'].'</textarea></center>';
	}
	if(isset($_POST['document'])){
	?>
<!-------------------------------------------------------------------------------------------------->	
	</th>
  </tr>
  <tr>
  <th colspan="4">
  <div align="center">
      <div style="width:90%;">
        <?php
		if($_POST['option']==2){
		echo '<table id="dataTable" class="selection">';
echo '<tr>
		<th>' .  _('Serialized')  . '</th>
		<th>' .  _('Qty'). '</th>
		<th>' .  _('Description of Goods on Board (Lot No.)'). '</th>
	</tr>';

	echo '<tr>
			
			<td><input type="text" size="10" maxlength="20" autofocus="autofocus" name="sno[]" value="" /></td>
			<td><input type="text" size="10" maxlength="10" autofocus="autofocus"  class="integer" name="qty[]" value="" /></td>
			<td><textarea name="description[]" cols="45" rows="1"></textarea></td>
			<td><a href="#" onClick="Javacsript:deleteRow(this)"><img src="'.$RootPath.'/css/trash.png" title="' ._('Delete Row') . '" alt="" /></a></a></td>
		</tr>';

echo '</table>';	
		echo '<input name="" type="button" onclick=addRow("dataTable") title="' ._('Add New Row') . '" value="New Row" />';
		}elseif($_POST['option']==1){
		echo '<table id="dataTable">';
echo '<tr>
		<th colspan="2">' .  _('Enter Delivery Note No : ')  . '</th>
		<td><input name="DNo" type="text" style="width:330px;" value="'.(isset($_POST['DNo']) ? $_POST['DNo'] : '').'" /> <br /><small>For Many deliveries separate with comma(,) (e.g 1234,5678,321)</small></td>
	</tr>';
	$showsubmit =0;
	if(isset($_POST['Delivery'])){
	if($_POST['DNo']==""){
	echo '<tr><td colspan="3" style="color:red">No Delivery Note Number Entered. Please Try Again!</td></tr>';
	}else{
	$sql = "SELECT salesorderdetails.stkcode,
					CONCAT_WS('',stockmaster.description,' (DN No - ',deliverynotes.deliverynotenumber,')') AS description,
					stockmaster.units,
					deliverynotes.qtydelivered,
					salesorderdetails.unitprice,
					salesorderdetails.narrative,
					stockmaster.mbflag,
					stockmaster.decimalplaces
				FROM salesorderdetails
				INNER JOIN stockmaster
				ON salesorderdetails.stkcode=stockmaster.stockid
				INNER JOIN deliverynotes
				ON deliverynotes.salesorderlineno = salesorderdetails.orderlineno AND salesorderdetails.orderno = deliverynotes.salesorderno
				WHERE deliverynotes.deliverynotenumber IN (".$_POST['DNo'].")
				ORDER BY deliverynotes.deliverynotenumber ASC";
	$ErrMsg = _('There was a problem retrieving the Delivery Number from the database');
	$result=DB_query($sql, $ErrMsg);

	if (DB_num_rows($result)>0){
	echo '</table><table style="width:100%">';
	echo '<tr>
		<th>' .  _('Item Code')  . '</th>
		<th>' .  _('Qty'). '</th>
		<th>' .  _('Description of Goods on Board'). '</th>
		</tr>';
	while ($myrow2=DB_fetch_array($result)){
	echo '<tr><td>'.$myrow2['stkcode'].'<input type="hidden" name="sno[]" value="'.$myrow2['stkcode'].'" /></td>
			<td>'.$myrow2['qtydelivered'].'<input type="hidden" name="qty[]" value="'.number_format($myrow2['qtydelivered']).'" /></td>
			<td>'.$myrow2['description'].'<input type="hidden" name="description[]" value="'.$myrow2['description'].'" /></td></tr>';
		}
		$showsubmit =1;
	}else{
	echo '<tr><td colspan="3" style="color:red">Invalid Delivery Note Number Entered. Please Try Again!</td></tr>';
	}
	}
	}

echo '</table>';
echo '<input name="Delivery" type="submit" title="' ._('Select') . '" value="Select" />';
		}else{
		
		}
		?>
      </div>
    </div>
  </th>
  </tr>
  <tr>
    <td></td>
<?php
if($showsubmit ==0 && $_POST['option']==1){
echo '<td></td>';
}else{
    echo '<td colspan="3"><input name="Submit" type="submit" value="Submit" />
        <strong>
          <input name="draft" type="checkbox" value="1" />
          Save as a Draft</strong></td>';
		  
	}
		  
}
?>
  </tr>
</table>

</form>
</div>
<SCRIPT language="javascript">
        function addRow(tableID) {

            var table = document.getElementById(tableID);

            var rowCount = table.rows.length;
            var row = table.insertRow(rowCount);

			var cell2 = row.insertCell(0);
            var element2 = document.createElement("input");
            element2.type = "text";
			element2.required = "required";
			element2.size = "10";
            element2.name = "sno[]";
            cell2.appendChild(element2);

            var cell3 = row.insertCell(1);
            var element3 = document.createElement("input");
            element3.type = "text";
			element3.required = "required";
			element3.size = "10";
            element3.name = "qty[]";
            cell3.appendChild(element3);
			
			var cell4 = row.insertCell(2);
            var element4 = document.createElement("textarea");
			element4.required = "required";
			element4.cols = "45";
			element4.rows = "1";
            element4.name = "description[]";
            cell4.appendChild(element4);
			
			row.insertCell(3).innerHTML= '<a href="#" onClick="Javacsript:deleteRow(this)"><?php echo '<img src="'.$RootPath.'/css/trash.png" title="' ._('Delete Row') . '" alt="" /></a>';?></a>';


        }

        function deleteRow(obj) {
      
    var index = obj.parentNode.parentNode.rowIndex;
    var table = document.getElementById("dataTable");
    table.deleteRow(index);
    
}

function findTotal(){
    var arr = document.getElementsByName('amnt[]');
    var tot=0;
    for(var i=0;i<arr.length;i++){
        if(parseInt(arr[i].value))
            tot += parseInt(arr[i].value);
			total = tot.toString().replace(/([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,');
    }
    document.getElementById('total').value = total;
}

    </SCRIPT>