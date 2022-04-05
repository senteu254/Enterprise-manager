<?php
	$PageSecurity=0;
	$InputError = 0;
	include('includes/session.inc');
	$Title=_('Supplimentary Allocation');
	include('includes/header.inc');
	include('includes/MainMenuLinksArray.php');
	
	if ($InputError == 0){
	if (isset($_POST['submit']))
	{}
	}	   
	
	/***********************************validations*************************************/
	if (isset($_POST["Submit"])) { 
    $amount=$_POST['amount'] ;
	$funds_allocated=$myrow['funds_allocations'];
	$FromSelectedVotehead = $_POST['FromVotehead'];
	$ToSelectedVotehead = $_POST['ToVotehead'];	
	
    $result = DB_query("SELECT a.voted_Item,
	                           b.allocated_Fund,
							   a.commitments,
							   SUM(d.amt) AS amt,
							   b.suppliementary
							   FROM commitment a
							 INNER JOIN funds_allocations b ON a.voted_Item=b.votecode
							 INNER JOIN supptrans c ON a.lpo_No	=c.OrderNo	
							 INNER JOIN suppallocs d ON c.suppreference=d.invoice
							 WHERE b.votecode=" . $FromSelectedVotehead ."
							 AND b.votecode=". $FromSelectedVotehead ."");
							 
						while($test = DB_fetch_array($result)){
						$sum=($test['commitments']+ $test['amt']);                        
						$totalAlloc=($test['allocated_Fund']+ $test['suppliementary']);
						$Ava_Balance=($totalAlloc-$sum);
				}
	if(empty($amount)) {
	prnMsg(_('Please insert the amount to Re-Allocate'),		'error');
	$InputError = 1;
	}else if(!is_numeric($amount)) {
	prnMsg(_('Amount Funds to be Allocated must be Numeric only '),		'error');
	$InputError = 1;
	}else if ($InputError != 1){ 
	$SQL = "UPDATE funds_allocations SET allocated_Fund = allocated_Fund-".$amount."
			WHERE votecode=".$FromSelectedVotehead."";
		$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The transaction could not be updated because');
		$DbgMsg = _('Transaction could not be made in the system');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		
		$SQLi = "UPDATE funds_allocations SET suppliementary = suppliementary + ".$amount." 
		       WHERE votecode=".$ToSelectedVotehead."";	
	    $InsResult = DB_query($SQLi,$ErrMsg,$DbgMsg,true);
					DB_Txn_Commit();
	    echo '<br />';
	    prnMsg( _('Voted head has been successfully re_allocated '),'success');	
		
		  prnMsg( _('Data has been successfully Added to Votebook allocation tracking records '),'success');	
		
		$SQL3 = "INSERT INTO supplmentarytracking (From_Votecode,
											To_Votecode,
											Amount,
											alloc_Date)
										VALUES (
											'". $FromSelectedVotehead . "',
											'" .$ToSelectedVotehead . "', 
											" . $amount. ",
											'" . Date('Y-m-d') . "')";

	$ErrMsg = _('The quotation cannot be added because');
	$InsertQryResult = DB_query($SQL3,$ErrMsg,true);
	}
	}
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _ 
	('Supplimentry Allocation') . '" alt="" />' . ' ' . _('Supplimentary Allocation') . '</p>';
    //////////////////////////////////////////////////////////////////////////////////////////
    echo '<form action="" method="post" name="myform" enctype="multipart/form-data" target="_self">';	
	echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';		
	echo '<table align="center" style="width:35%">	
		<br />
		<table>';	
		$votehead= DB_query("SELECT Votehead FROM  voteheadmaintenance
								         WHERE Votecode='". $myrow['votecode'] ."'");	
										 $head = DB_fetch_array($votehead);
		echo '<tr>';
		echo'<td>' . _('From Vote Head').':</td>
		<td><select name="FromVotehead">';
	echo '<option  selected="selected" value="">--Select Votehead to obtain funds--</option>';
    $sql = "SELECT *,b.Votehead,
	               b.Votecode
				   FROM funds_allocations a,voteheadmaintenance b
				   WHERE a.voted_Item=b.Votecode
				   ORDER BY b.Votecode";
	$result = DB_query($sql);
	while ($myrow=DB_fetch_array($result)){
			$Account = $myrow['votecode'] . ' - ' . htmlspecialchars($myrow['Votehead'],ENT_QUOTES,'UTF-8',false);
			if (isset($FromSelectedVotehead) AND $FromSelectedVotehead==$myrow['votecode']){
				echo '<option selected="selected" value="' . $myrow['votecode'] . '">' . $Account . '</option>';					
			} else {			
				echo '<option value="' . $myrow['votecode'] . '">' . $Account . '</option>';
			}
	     }
	echo '</select></td>
		</tr>';
	echo '<tr>
		<td>' .  _('To Vote Head').': </td>
		<td><select name="ToVotehead"> ';		
	echo '<option  selected="selected" value="">--Select Votedhead to Re_Allocate--</option>';
	DB_data_seek($result,0);
	while ($myrow=DB_fetch_array($result)){
			$Account = $myrow['votecode'] . ' - ' . htmlspecialchars($myrow['Votehead'],ENT_QUOTES,'UTF-8',false);
			if (isset($ToSelectedVotehead) AND $ToSelectedVotehead==$myrow['votecode']){
				echo '<option selected="selected" value="' . $myrow['votecode'] . '">' . $Account . '</option>';					
			} else {			
				echo '<option value="' . $myrow['votecode'] . '">' . $Account . '</option>';
			}
	     }
	echo'<tr>
	<td style="font-size:10pt">Amount</td><td><input type="text" size="20" maxlength="60" name="amount" /><td><td><center></center></td>    </tr>';
	 echo '</select></td></tr>';
 
	echo '<tr><td colspan="2"><center><input name="Submit" type="submit" value="Enter Funds Allocations" /></center></td></tr>';
	echo'</table>';
	echo'</form>';
	include('includes/footer.inc');
	?>
