  <style> 

  .odd{background-color: white;} 

  .even{background-color:#CCCCCC;} 
  
   </style>
<?php
	$PageSecurity=0;
	include('includes/session.inc');
	$Title=_('Votebook Decommitments');
	include('includes/header.inc');
	
	 echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Voted Commitements') . '" alt="" />' . ' ' . _('Votebook     Decommitment') . '</p>';
	if($value ==1){
	$value = 1;
	}else{
	$value = 0;
	}
	$id =$_REQUEST['voucherID'];
	$SQL =DB_query("SELECT * FROM commitment WHERE voucherID  = '$voucherID'");
	$test = DB_fetch_array($SQL);
	if (!$SQL) 
	{
	die("Error: Data not found..");
	}
    echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';
	//$voucherID= $test['voucherID'] ;					
	$payee_Name=$test['suppname'] ;
	$lpo_No= $test['orderno'] ;
	$commitments= $test['commitments'] ;
	$decommitment_save= $test['decommitment'];	
	if(isset($_POST['save']))
	{
	$lpo_No = $_POST['orderno'];	
	$deco= filter_number_format($_POST['deco']);
	 if(isset($_POST['decommit'])&& $_POST['decommit'] ==0){
	$c=DB_query("SELECT a.orderno,
	                    a.podetailitem,
	                    b.podetailitem
					    FROM purchorderdetails a,grns b
					    WHERE a.podetailitem=b.podetailitem
						AND a.orderno='".$lpo_No."'");
		}else{
		$c=DB_query("SELECT a.orderno,
	                    a.podetailitem,
	                    b.podetailitem
					    FROM lsorderdetails a,lsogrns b
					    WHERE a.podetailitem=b.podetailitem
						AND a.orderno='".$lpo_No."'");		
		}
	$row=DB_num_rows($c);
    if(empty($lpo_No)) {
	prnMsg(_('Lpo cannot be blank!  '),		'error');
	}else if(!$row ==1){
	prnMsg(_('Selected Lpo has been already decommited '), 'error');
	}else{ 
  	DB_query("UPDATE commitment SET decommitment =decommitment+'".$deco."' WHERE lpo_No = '".$lpo_No."'");
		  $InsResult = DB_query($sql,$ErrMsg,$DbgMsg,true);
	
	 $grns= count($_POST['GRNNo']);
	 for($r=0; $r<$grns; $r++){
	DB_query("INSERT INTO decommitment(grnno,amount) VALUES('".$_POST['GRNNo'][$r]."','".$_POST[$_POST['GRNNo'][$r].'Amount']."')");
		  $InsResult = DB_query($sql,$ErrMsg,$DbgMsg,true);
	}
					DB_Txn_Commit();
				echo '<br />';
						prnMsg( _('Lpo has been succesfully Decommited'),'success');		
	}	
	}
	echo'<form method="post">
	<body>';
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	 if(isset($_POST['SearchLpo'])){
	 if(isset($_POST['orderno']) && $_POST['orderno'] !=''){
	 if(isset($_POST['account'])){
	 $SelectedVotedaccount=$_POST['account'];
	 }
	 if(isset($_POST['decommit'])&& $_POST['decommit'] ==0){
	 $i=0; 	 
	 $SQL = "SELECT b.orderno,
	                a.podetailitem,
					a.itemcode,
					a.qtyrecd,
					a.deliverydate,
					b.podetailitem,
					c.supplierno,
					d.suppname,
					e.materialcost,
					e.stockid,
					(b.unitprice* a.qtyrecd) as totcost
				    FROM grns a
					INNER JOIN purchorderdetails b ON a.podetailitem = b.podetailitem
					INNER JOIN purchorders c ON b.orderno = c.orderno
					INNER JOIN suppliers d ON c.supplierno=d.supplierid
					INNER JOIN stockmaster e ON a.itemcode=e.stockid 
					WHERE b.orderno='". $_POST['orderno'] ."'
					GROUP BY b.orderno"; 
				$rest=DB_query($SQL);
				$r=DB_fetch_array($rest);
				$_POST['LpoSelected'] = $r['orderno'];
				$_POST[$r['orderno'].'payeeNameSelected'] = $r['suppname'];
				$_POST[$r['orderno'].'voted_Item'] = $r['voted_Item'];
				$_POST[$r['orderno'].'Comm'] = $r['totcost'];
				
	 }else{	 
	 $SQL = "SELECT b.orderno,
	                a.podetailitem,
					a.itemcode,
					a.qtyrecd,
					a.deliverydate,
					b.podetailitem,
					c.supplierno,
					d.suppname,
					e.materialcost,
					e.stockid,
					(b.unitprice* a.qtyrecd) as totcost
				    FROM lsogrns a
					INNER JOIN lsorderdetails b ON a.podetailitem = b.podetailitem
					INNER JOIN lsorders c ON b.orderno = c.orderno
					INNER JOIN suppliers d ON c.supplierno=d.supplierid
					INNER JOIN stockmaster e ON a.itemcode=e.stockid 
					WHERE b.orderno='". $_POST['orderno'] ."'
					GROUP BY b.orderno"; 
				$rest=DB_query($SQL);
				$r=DB_fetch_array($rest);
				$_POST['LpoSelected'] = $r['orderno'];
				$_POST[$r['orderno'].'payeeNameSelected'] = $r['suppname'];
				$_POST[$r['orderno'].'voted_Item'] = $r['voted_Item'];
				$_POST[$r['orderno'].'Comm'] = $r['totcost'];
	 
	 }
	 }else{  
	  if(isset($_POST['decommit'])&& $_POST['decommit'] ==0){
	  $SQL = "SELECT b.orderno,
	                a.podetailitem,
					a.itemcode,
					a.qtyrecd,
					a.deliverydate,
					b.podetailitem,
					c.supplierno,
					d.suppname,
					e.materialcost,
					e.stockid,
					(b.unitprice* a.qtyrecd) as totcost
				    FROM grns a
					INNER JOIN purchorderdetails b ON a.podetailitem=b.podetailitem
					INNER JOIN purchorders c ON b.orderno=c.orderno
					INNER JOIN suppliers d ON c.supplierno=d.supplierid
					INNER JOIN stockmaster e ON a.itemcode=e.stockid
					GROUP BY b.orderno"; 
			}else{	 
			 $SQL = "SELECT b.orderno,
	                a.podetailitem,
					a.itemcode,
					a.qtyrecd,
					a.deliverydate,
					b.podetailitem,
					c.supplierno,
					d.suppname,
					e.materialcost,
					e.stockid,
					(b.unitprice* a.qtyrecd) as totcost
				    FROM lsogrns a
					INNER JOIN lsorderdetails b ON a.podetailitem=b.podetailitem
					INNER JOIN lsorders c ON b.orderno=c.orderno
					INNER JOIN suppliers d ON c.supplierno=d.supplierid
					INNER JOIN stockmaster e ON a.itemcode=e.stockid
					GROUP BY b.orderno";
				}  
				$rest=DB_query($SQL);
		echo '<form action="" method="post" enctype="multipart/form-data" target="_self">';	
		echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';	
		echo '<input name="account" type="hidden" value="'.$_POST['account'].'" />';	
		echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Select Order to be Decommitted') . '" alt="" />' . ' ' .    _('Select Order to be Decommitted') . '</p>';
	 echo '<table>
   <tr style=font-size:10pt>
   <td><b></b></td>
    <td><b>Order #</b></td>
    <td><b>Receaved Date</b></td>
	<td><b>Supplier Name</b></td>
	<td></td>
  </tr>';
  while($row=DB_fetch_array($rest)){
  $i++;
  if($i%2 ==0){$class='even';}else{$class='odd';}
   echo "<tr class=".$class." align='center' style=font-size:10pt>";
    echo' <td>'. $i .'</td>
    <td><input name="LpoSelected" type="submit" value="'. $row['orderno'] .'" /></td>
    <td>'. ConvertSQLDate($row['deliverydate']) .'</td>
	<td>'. $row['suppname'] .'</td>
	<input name="'. $row['orderno'] .'payeeNameSelected" type="hidden" value="'. $row['suppname'] .'" />
	<input name="'. $row['orderno'] .'voted_Item" type="hidden" value="'. $row['voted_Item'] .'" />
	<input name="'. $row['orderno'] .'Comm" type="hidden" value="'. $row['totcost'] .'" />
	<input name="decommit" type="hidden" value="'. $_POST['decommit'] .'" />
    </tr>';
  }
  echo '</table></form>';
   include('includes/footer.inc');
	 exit;
	 }
	 }
	 //-------------------------------------------------------------------------------
	 if(isset($_POST['LpoSelected'])){ 
	 if(isset($_POST['decommit'])&& $_POST['decommit'] ==0){ 
	  $SQL = "SELECT b.orderno,
	 				 a.grnno,
	                a.podetailitem,
					a.itemcode,
					a.qtyrecd,
					a.deliverydate,
					b.unitprice,
					(b.unitprice* a.qtyrecd) as totcost
				    FROM grns a
					INNER JOIN purchorderdetails b ON a.podetailitem=b.podetailitem
					LEFT JOIN decommitment d ON a.grnno = d.grnno
					where b.orderno='". $_POST['LpoSelected'] ."' AND d.grnno IS NULL"; 
				}else{				
				$SQL = "SELECT b.orderno,
								 a.grnno,
								a.podetailitem,
								a.itemcode,
								a.qtyrecd,
								a.deliverydate,
								b.unitprice,
								(b.unitprice* a.qtyrecd) as totcost
								FROM lsogrns a
								INNER JOIN lsorderdetails b ON a.podetailitem=b.podetailitem
								LEFT JOIN decommitment d ON a.grnno = d.grnno
								where b.orderno='". $_POST['LpoSelected'] ."' AND d.grnno IS NULL"; 	 
						}
						$rest=DB_query($SQL);
		echo '<form action="" method="post" enctype="multipart/form-data" target="_self">';	
		echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';	
		echo '<input name="account" type="hidden" value="'.$_POST['account'].'" />';	
		
		echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Select Order to be Decommitted') . '" alt="" />' . ' ' .    _('Select Order to be Decommitted') . '</p>';
	 echo '<table>
   <tr style=font-size:10pt>
   <td><b></b></td>
    <td></td>
	<td><b>GRN/SDN#</b></td>
    <td><b>Received Date</b></td>
	<td><b>Qty Received</b></td>
	<td><b>Unit Price</b></td>
	<td><b>Total</b></td>
	<td></td>
  </tr>';
  while($row=DB_fetch_array($rest)){
  $i++;
  if($i%2 ==0){$class='even';}else{$class='odd';}
   echo "<tr class=".$class." align='center' style=font-size:10pt>";
    echo' <td>'. $i .'</td>
    <td><input name="GRN[]" type="checkbox" value="'. $row['grnno'] .'" /></td><input name="'. $row['grnno'] .'DecomAmt" type="hidden" value="'. $row['totcost'] .'" />
	<td>'. $row['grnno'] .'</td>
    <td>'. ConvertSQLDate($row['deliverydate']) .'</td>
	<td>'. $row['qtyrecd'] .'</td>
	<td>'. $row['unitprice'] .'</td>
	<td>'. $row['totcost'] .'</td>
	<input name="GRNSelected" type="hidden" value="'.$_POST['LpoSelected'] .'" />
	<input name="'. $_POST['LpoSelected'] .'payeeNameSelected" type="hidden" value="'. $_POST[''.$_POST['LpoSelected'].'payeeNameSelected'] .'" />
	<input name="'. $_POST['LpoSelected'] .'voted_Item" type="hidden" value="'. $_POST[''.$_POST['LpoSelected'].'voted_Item'] .'" />
	<input name="'. $_POST['LpoSelected'] .'Comm" type="hidden" value="'. $_POST[''.$_POST['LpoSelected'].'Comm'] .'" />
    </tr>';
  }
  echo '<input name="decommit" type="hidden" value="'.$_POST['decommit'].'" />';
  echo '</table><input name="GRNSUBMIT" type="submit" value="Submit" /></form>';
   include('includes/footer.inc');
	 exit;	 
	 }
	 if(isset($_POST['GRNSUBMIT'])){
	 $grn= count($_POST['GRN']);
	 for($z=0; $z<$grn; $z++){
	 $amounttot += $_POST[$_POST['GRN'][$z].'DecomAmt'];
	 echo '<input name="GRNNo[]" type="hidden" value="'.$_POST['GRN'][$z].'" />
	 		<input name="'.$_POST['GRN'][$z].'Amount" type="hidden" value="'.$_POST[$_POST['GRN'][$z].'DecomAmt'] .'" />';	 
	 }
	 }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />'; 	

echo'<table align="center" style="width:40%">';
 ?>
 
 <table>
  <tr>
    <th colspan="4">
	 <div align="center">
        <table>		
          <tr>
            <td rowspan="2">Select type of Item to decommit</td>
            <td><label>
              <input name="decommit" type="radio" value="0" <?php if(isset($_POST['decommit']) && $_POST['decommit']==0){ echo 'checked="checked"'; } ?> />LPO</label></td>          </tr>
		 
          <tr>
            <td><label>
              <input type="radio" <?php if(isset($_POST['decommit']) && $_POST['decommit']==1){ echo 'checked="checked"'; } ?>  name="decommit" value="1" />LSO</label></td>
          </tr>
        </table>
 
 <?php 
  echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';	
  echo'<tr>
 <td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;LPO/LSO No.</td><td><center></center><input type="text" size="15" maxlength="40" name="orderno" value="'. $_POST[ 'GRNSelected'] .'"/>&nbsp;<input type="submit" name="SearchLpo" value=" Select " /></td></tr>
 <tr>
 <td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Payee Name.</td><td><center></center><input  type="text" size="50" disabled="true" maxlength="60" value="'.$_POST[''. $_POST['GRNSelected'] .'payeeNameSelected'].'" name="" /><input  type="hidden" size="50" maxlength="60" value="'.$_POST[''. $_POST['GRNSelected'] .'payeeNameSelected'].'" name="suppname" /></td></tr>';
 /*
 if(isset($_POST['LpoSelected'])){
 $SQL = "SELECT *, SUM(commitments) as sumcom FROM commitment a, funds_allocations b
   WHERE a.voted_Item = b.votecode AND b.votecode='". $_POST[''.$_POST['LpoSelected'].'voted_Item'] ."'";	
 $result=DB_query($SQL);
 $myrow=DB_fetch_array($result);
 $total_balance= ($myrow['allocated_Fund']-$myrow['sumcom'])+ $_POST[''.$_POST['LpoSelected'].'Comm'];
 } 
 */
echo'<tr><td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Decommitments</td><td><input type="text" readonly="true" size="15" name="deco" value="'.locale_number_format($amounttot,2) .'"/></td></tr>';

echo'<tr><td>&nbsp;</td>
		<tr style="font-size:10pt">
		<td>&nbsp;</td>
		<td><input type="submit" name="save" onClick="show_alert()" value="save" /></td>
	    </tr>';
 echo '</table>';
 echo '</form>';
 include('includes/footer.inc');
?>