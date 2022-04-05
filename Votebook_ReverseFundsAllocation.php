  <style> 

  .odd{background-color: white;} 

  .even{background-color:#CCCCCC;} 
  
   </style>
<?php
	$PageSecurity=0;
	$InputError = 0;
	include('includes/session.inc');
	$Title=_('Reverse Votebook Commitments');
	include('includes/header.inc');
	
	 echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Votebook Transactions') . '" alt="" />' . ' 
	 ' . _('Votebook Transactions') . '</p>';
	
	$id =$_REQUEST['voucherID'];
	$SQL =DB_query("SELECT * FROM commitment WHERE voucherID  = '$voucherID'");
	$test = DB_fetch_array($SQL);
	if (!$SQL) 
	{
	die("Error: Data not found..");
	}
    echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';
	$voucherID= $test['voucherID'] ;	
	$lpo_No= $test['votecode'] ;
	if ($InputError == 0){
	if (isset($_POST['save']))
	{}
	}
	$lpo_No = $_POST['votecode'];	
	if(isset($_POST['save'])){
	$result = DB_query("SELECT votecode
								FROM funds_allocations
								WHERE votecode='" . $lpo_No ."'");
	if(empty($lpo_No)) {
	prnMsg(_('Please select LPO No to reverse Transaction '),		'error');
	$InputError = 1;
	}else if ($InputError != 1) { 
  	DB_query("DELETE FROM funds_allocations WHERE votecode = '".$lpo_No."'");
	 $InsResult = DB_query($sql,$ErrMsg,$DbgMsg,true);
					DB_Txn_Commit();
					
				echo '<br />';
				prnMsg( _('LPO No. has been succesfully Reversed from Votebook Commitment'),'success'); 	
	//header("Location: Votebook_payment.php");			
	}
	}

	echo'<form method="post">
	<body>';
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	 if(isset($_POST['SearchLpo'])){
	 if(isset($_POST['votecode']) && $_POST['votecode'] !=''){
	 if(isset($_POST['account'])){
	 $SelectedVotedaccount=$_POST['account'];
	 }
	 $i=0;   
	 $SQL = "SELECT *,b.Votehead,
	                   b.Votecode
	                  FROM funds_allocations a,voteheadmaintenance b
					  WHERE a.votecode='". $_POST['votecode'] ."'
					  AND a.votecode=b.Votecode";
     				$rest=DB_query($SQL);
				$r=DB_fetch_array($rest);
				$_POST['LpoSelected'] = $r['votecode'];
	 }else{
	  $SQL = "SELECT *,b.Votehead,
	                   b.Votecode
	                  FROM funds_allocations a,voteheadmaintenance b
					  WHERE a.votecode=b.Votecode"; 	 
				$rest=DB_query($SQL);
		echo '<form action="" method="post" enctype="multipart/form-data" target="_self">';	
		echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';	
		echo '<input name="account" type="hidden" value="'.$_POST['account'].'" />';	
		echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Select Order to be Decommitted') . '" alt="" />' . ' ' .    _('Select Votecode to Reverse Funds') . '</p>';
	 echo '<table>
  <tr style=font-size:10pt>
    <td><b>#</b></td>
    <td><b>Votecode #</b></td>
	<td><b>Votehead</b></td>
	<td><b>Amount Allocated</b></td>
	<td></td>
  </tr>';
  while($row=DB_fetch_array($rest)){
   $i++;
	 if($i%2 ==0){$class='even';}else{$class='odd';}
  // echo "<tr class=".$class." align='center' style=font-size:10pt>";
  echo "<tr class=".$class." align='center' style=font-size:10pt>";
  echo' <td>'. $i .'</td>
    <td><input name="LpoSelected" type="submit" value="'. $row['votecode'] .'" /></td>
	<td>'. $row['Votehead'] .'</td>
	<td>'. locale_number_format($row['allocated_Fund'],2) .'</td>
    </tr>';
  }
  echo '</table></form>';
   include('includes/footer.inc');
	 exit;
	 }
	 }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />'; 	
  echo'<table align="center" style="width:60%">';
  
  $SQL9 = "SELECT Votehead FROM voteheadmaintenance WHERE Votecode ='" . $_POST[ 'LpoSelected']. "'";
         $votename=DB_Query($SQL9);
		 $r=DB_fetch_array($votename);

 echo'<tr>
 <td style="font-size:10pt">Select Votehead to Reverse</td><td><center></center><input type="text" size="45" maxlength="60" name="votecode" value="'. $_POST[ 'LpoSelected'] .'"/>&nbsp;<input type="submit" name="SearchLpo" value=" Select " /></td></tr>';
 if(isset($_POST['LpoSelected'])){
 $SQL = "SELECT	allocated_Fund FROM funds_allocations	
          WHERE votecode='". $_POST['LpoSelected'] ."'";	
		 $result=DB_query($SQL);
		 $myrow=DB_fetch_array($result);
 } 
echo'<tr><td style="font-size:10pt">Amount to Reverse</td><td><input type="text" size="15" name="deco" value="'. locale_number_format($myrow['allocated_Fund'],2) .'"/></td></tr>';
		
		echo'<tr><td>&nbsp;</td>
		<tr style="font-size:10pt">
		<td>&nbsp;</td>
		<td><input type="submit" name="save" onClick="show_alert()" value="Reverse Allocated Fund" /></td>
	    </tr>';
 echo '</table>';
 echo '</form>';
 include('includes/footer.inc');
?>