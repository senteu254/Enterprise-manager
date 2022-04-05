
   	 
<?php
$PageSecurity=0;
	include('includes/session.inc');
	$Title=_('Votebook Commitments');
	include('includes/header.inc');
	
	 echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Voted Commitements') . '" alt="" />' . ' ' . _('Votebook   Details') . '</p>';
	 
	if (isset($_POST['submit'])){
		   
	$payee_Name=$_POST['payee_Name'] ;
	$date=date("j, n, Y");
	$lpo_No= $_POST['lpo_No'] ;					
	$commitments=$_POST['commitments'] ;
	$voteaccount=$_POST['account'] ;
	
	$a=DB_query("SELECT commitments FROM commitment");
	$num=DB_num_rows($a);
	if($num !=''){
	$B=DB_query("SELECT SUM(commitments) as tot FROM commitment");
	while($b=DB_fetch_array($B)){
	$c=$b['tot'];
	}
	}else{
	$c=0;
	}
	$cummulative_Com=$commitments+$c;
	
	

	$c=DB_query("SELECT lpo_No FROM commitment where lpo_No='".$lpo_No."'");
	$row=DB_num_rows($c);
    if(!$row ==0){
	prnMsg(_('Lpo has been already been commited '), 'error');
	}else if(empty($lpo_No)) {
	prnMsg(_('Lpo Number  Cannot be empty!  '),		'error');
	}else if (DB_num_rows($result)==1){
	prnMsg(_('The Lpo Number entered is already in the database - duplicate vote codes are prohibited by the system. Try choosing an alternative Service Number'),		'error')    ;
		$InputError = 1;
		$Errors[$i] = 'lpo_No';
		$i++;
			
} else if (DB_error_no() ==0){ 
	DB_query("INSERT INTO commitment(payee_Name,date,lpo_No,commitments,voted_Item,cummulative_Com,Fyear) 
		 VALUES ('$payee_Name','" . Date('Y-m-d') . "','$lpo_No','$commitments','$voteaccount','$cummulative_Com','".Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],+0))."')"); 
		  $InsResult = DB_query($sql,$ErrMsg,$DbgMsg,true);
					DB_Txn_Commit();
					
				echo '<br />';
						prnMsg( _('Lpo has been succesfully Commited'),'success');			 		 
	}
	}

	 if(isset($_POST['SearchOrders'])){
	 if(isset($_POST['lpo_No']) && $_POST['lpo_No'] !=''){
	 if(isset($_POST['account'])){
	 $SelectedVotedaccount=$_POST['account'];
	 }
	 if(isset($_POST['commit'])&& $_POST['commit'] ==0){
	 $SQL = "SELECT purchorders.realorderno,
							purchorders.orderno,
							suppliers.suppname,
							purchorders.orddate,
							purchorders.deliverydate,
							purchorders.status
						FROM purchorders INNER JOIN suppliers
						ON  purchorders.supplierno = suppliers.supplierid
						WHERE purchorders.orderno='". $_POST['lpo_No'] ."'";	 
				$rest=DB_query($SQL);
				$r=DB_fetch_array($rest);
				$_POST['OrderSelected'] = $r['orderno'];
				$_POST[$r['orderno'].'SuppSelected'] = $r['suppname'];	
	 }else{ // lso query
	 $SQL = "SELECT lsorders.realorderno,
							lsorders.orderno,
							suppliers.suppname,
							lsorders.orddate,
							lsorders.deliverydate,
							lsorders.status
						FROM lsorders INNER JOIN suppliers
						ON  lsorders.supplierno = suppliers.supplierid
						WHERE lsorders.orderno='". $_POST['lpo_No'] ."'";	 
				$rest=DB_query($SQL);
				$r=DB_fetch_array($rest);
				$_POST['OrderSelected'] = $r['orderno'];
				$_POST[$r['orderno'].'SuppSelected'] = $r['suppname'];	
				//end of lso query
	 }
	 }else{
	  if(isset($_POST['commit'])&& $_POST['commit'] ==0){
	 $SQL = "SELECT purchorders.realorderno,
							purchorders.orderno,
							suppliers.suppname,
							purchorders.orddate,
							purchorders.deliverydate,
							purchorders.status
						FROM purchorders INNER JOIN suppliers
						ON  purchorders.supplierno = suppliers.supplierid";
						 
		}else{ //lso query
		 $SQL = "SELECT lsorders.realorderno,
							lsorders.orderno,
							suppliers.suppname,
							lsorders.orddate,
							lsorders.deliverydate,
							lsorders.status
						FROM lsorders INNER JOIN suppliers
						ON  lsorders.supplierno = suppliers.supplierid";		
						//end 
		}
				$rest=DB_query($SQL);
		echo '<form action="" method="post" enctype="multipart/form-data" target="_self">';	
		echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';	
		echo '<input name="account" type="hidden" value="'.$_POST['account'].'" />';
		echo '<input name="commit" type="hidden" value="'.$_POST['commit'].'" />';	
		echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Select Order to be Committed') . '" alt="" />' . ' ' .    _('Select Order to be Committed') . '</p>';
	 echo '<table>
  <tr style=font-size:10pt>
    <th><b>Order #</b></th>
    <th><b>Order Date</b></th>
	<th><b>Supplier</b></th>
	<th></th>
  </tr>';?>
    <style> 

  .odd{background-color: white;} 

  .even{background-color:#CCCCCC;} 
  
   </style>
  <?php
  $i=0;
   while($row=DB_fetch_array($rest)){
   $i++;
 if($i%2 ==0){$class='even';}else{$class='odd';}
  echo '<tr class=' .$class. ' style=font-size:10pt>
    <td><input name="OrderSelected" type="submit" value="'. $row['orderno'] .'" /></td>
    <td>'. ConvertSQLDate($row['orddate']) .'</td>
	<td>'. $row['suppname'] .'</td>
	<input name="'. $row['orderno'] .'SuppSelected" type="hidden" value="'. $row['suppname'] .'" />
    </tr>';
  }
  echo '</table></form>';
 include('includes/footer.inc');
	 exit;
	 }
	 }
 if (isset($_POST['account'])){
 if(isset($_POST['lpo_No']) && $_POST['lpo_No'] !=''){
  $SQL = "SELECT purchorders.realorderno,
							purchorders.orderno,
							suppliers.suppname,
							purchorders.orddate,
							purchorders.deliverydate,
							purchorders.status
						FROM purchorders INNER JOIN suppliers
						ON  purchorders.supplierno = suppliers.supplierid
						WHERE purchorders.orderno='". $_POST['lpo_No'] ."'";	 
				$rest=DB_query($SQL);
				$r=DB_fetch_array($rest);
				$_POST['OrderSelected'] = $r['orderno'];
				$_POST[$r['orderno'].'SuppSelected'] = $r['suppname'];
 }
 $SelectedVotedaccount=$_POST['account'];
 }
	 echo '<form action="" method="POST">';	
	 echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';	
     echo '<table align="center" style="width:60%">
  <tr>
   <tr>
   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .   _('Select Voted Account.').  '</td>
		<td><select name="account" onchange="this.form.submit();">';
	echo '<option  selected="selected" value="">--Please select Voted Account--</option>';
 $SQL = "SELECT a.votecode,
				a.voted_Item,
				a.Financial_Year,
				a.allocated_Fund,
				b.Votecode,
				b.Votehead
			FROM funds_allocations a,voteheadmaintenance b
			WHERE a.votecode=b.Votecode
			AND a.Financial_Year='".Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],+0))."'
			ORDER BY accountcode";

 $result=DB_query($SQL);
 if (DB_num_rows($result)==0){
      echo '</select></td>
			</tr>';
	prnMsg(_('No Voteheads accounts have been set up yet') . ' - ' . _('Allocations cannot be allocated until the Voteheads accounts are set up'),'warn');
 } else {
	while ($myrow=DB_fetch_array($result)){
	
	
	
		$Account = $myrow['votecode'] . ' - ' . htmlspecialchars($myrow['Votehead'],ENT_QUOTES,'UTF-8',false);
		if (isset($SelectedVotedaccount) AND $SelectedVotedaccount==$myrow['votecode']){
		    echo '<option selected="selected" value="' . $myrow['votecode'] . '">' . $Account . '</option>';
					
		} else {
			
			echo '<option value="' . $myrow['votecode'] . '">' . $Account . '</option>';
		}
	}
 //////////////////////////////////////////////////////////////
 echo '</select><input type="submit" name="Select" value="Select" /></td>';
 if (isset( $SelectedVotedaccount)){
$result=DB_query("SELECT a.allocated_Fund,a.Financial_Year,a.suppliementary,
							ifnull((select SUM(b.commitments)
							from commitment b
							where b.voted_Item=a.votecode and a.Financial_Year=b.Fyear), 0) as commitment,
							ifnull((select SUM(b.decommitment)
							from commitment b
							where b.voted_Item=a.votecode and a.Financial_Year=b.Fyear), 0) as decommitment,
							ifnull((select SUM(d.amt)
							from supptrans c, suppallocs d, commitment e
							where c.suppreference=d.invoice and
							e.lpo_No=c.OrderNo and
							e.voted_Item=a.votecode  and a.Financial_Year=e.Fyear), 0) as payments
							FROM funds_allocations a
							WHERE a.votecode='". $SelectedVotedaccount ."'
							AND a.Financial_Year='".Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],+0))."'					 
							GROUP BY a.votecode");
							 $Row=DB_fetch_array($result);
	$Ava_Balance=(($Row['allocated_Fund']+$Row['suppliementary'])-(($Row['commitment']-$Row['decommitment'])+$Row['payments']));
 
 // $all_Fund=($allocated_Fund+$decomm)-$comm-$myrow['amt'];
 }
 echo '<tr>
 <td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Allocated Funds</td><td>'. locale_number_format($Ava_Balance,$_SESSION['CompanyRecord']['decimalplaces']) .'</td> </tr>
 <tr>';
 ?>
 <table>
  <tr>
    <th colspan="4">
	 <div align="center">
      <div style="width:800px;">
        <table>		
          <tr>
            <td rowspan="2">Select type of Item to Commit</td>
            <td><label>
              <input name="commit" type="radio" value="0" <?=0 == ''.$_POST['commit'].'' ? ' checked="checked"' : '';?> />LPO</label></td>
          </tr>
          <tr>
            <td><label>
              <input type="radio" <?=1 == ''.$_POST['commit'].'' ? ' checked="checked"' : '';?> name="commit" value="1" />LSO</label></td>
          </tr>
        </table>
	<?php

	echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';	
 echo'<tr>
 <td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;LPO/LSO No.</td> <td><center></center><input type="text" size="15" maxlength="40" name="lpo_No" value="'. $_POST[ 'OrderSelected'] .'"/><input type="submit" name="SearchOrders" value=" Search " /></td></tr>
 <tr> 
 <td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Payee Name.</td><td><center></center><input  type="text" size="50" disabled="true" maxlength="60" value="'.$_POST[''. $_POST['OrderSelected'] .'SuppSelected'].'" name="" /><input  type="hidden" size="50" maxlength="60" value="'.$_POST[''. $_POST['OrderSelected'] .'SuppSelected'].'" name="payee_Name" /></td></tr>';

if(isset($_POST['OrderSelected'])&& $_POST['commit'] ==0){

	$SQL = "SELECT * FROM purchorderdetails
					WHERE orderno='". $_POST['OrderSelected'] ."'";	 
				    $res=DB_query($SQL);
	
 echo '<tr><td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Descriptions</td><td><textarea disabled name="particulars" cols="30" rows="0">';
 while($row=DB_fetch_array($res)){
 $OrderTotal += ($row['quantityord'] * $row['unitprice']);
 echo $row['itemdescription'].',';
 }
 echo ' </textarea><center></center></td></tr>';
 }elseif(isset($_POST['OrderSelected'])&& $_POST['commit'] ==1){
$SQL = "SELECT * FROM lsorderdetails
					WHERE orderno='". $_POST['OrderSelected'] ."'";	 
				    $res=DB_query($SQL);
	
 echo '<tr><td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Descriptions</td><td><textarea disabled name="particulars" cols="30" rows="0">';
 while($row=DB_fetch_array($res)){
 $OrderTotal += ($row['quantityord'] * $row['unitprice']);
 echo $row['itemdescription'].',';
 }
 echo ' </textarea><center></center></td></tr>';
 
 }else{
 echo '<tr><td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Descriptions</td><td><textarea disabled name="particulars" cols="30" rows="0">';
 echo ' </textarea><center></center></td></tr>';
 }
 
 ?><tr>
 <td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Expenditure and Commitments</td><td><input type="hidden" size="30" value="<?php echo $OrderTotal; ?>" maxlength="60" name="commitments" /><input type="text"  disabled="disabled" size="30" value="<?php echo locale_number_format($OrderTotal,2); ?>" maxlength="60" /></td></tr>
 <tr><td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Balance</td><td><input id="total" value="<?php echo locale_number_format($Ava_Balance-$OrderTotal,2); ?>" type="text" disabled /></td></tr>	
</tr>
<?php
}
echo '</table>';
echo ' </div>
<input type="submit" name="submit" value="Submit" />'; 
 echo '</form>';
 
include('includes/footer.inc');
?>	
