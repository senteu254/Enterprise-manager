<?php
	$PageSecurity=0;
	$InputError = 0;
	include('includes/session.inc');
	$Title=_('VoteBook Allocations');
	include('includes/header.inc');
	include('includes/MainMenuLinksArray.php');
	
	if ($InputError == 0){
   if (isset($_POST["submit"])) {  
	$voted_Item= $_POST['voted_Item'] ;					
	$allocated_Fund=$_POST['allocated_Fund'] ;
	$votecode=$_POST['accountcode'] ;
	$SelectedVotehead = $_POST['Votecode'];
	$Votehead=$_POST['Votehead'] ;
	//$Yearend=$_POST['Yearend'] ;
    if(isset($_POST['Yearend'])){
    $Yearend=$_POST['Yearend'];
	/***********************************validations*************************************/
		 
	if(empty($allocated_Fund)) {
	prnMsg(_('Please Insert Allocation Fund! '),'error'); 
	$InputError = 1;
	}else if(!is_numeric($allocated_Fund)) {
	prnMsg(_('Allocation Fund must be Numeric only '),'error');
	$InputError = 1;
	}else if ($allocated_Fund >$_POST['budgetbalance']){
	prnMsg(_('Allocated Funds cannot excceed the total budget!'),'error');
	$InputError = 1;
    }else if ($InputError != 1) { 
   /**********************************************************************/
  
	DB_query("INSERT INTO `funds_allocations`(date,accountcode,votecode,voted_Item,allocated_Fund,Financial_Year)VALUES ('". date('Y-m-d') ."','". $_POST['SelectedAccount'] ."','". $_POST['SelectedAccount'] ."','" .$_POST['Votecode']. "','$allocated_Fund','$Yearend')"); 
	
	 $InsResult = DB_query($sql,$ErrMsg,$DbgMsg,true);
					DB_Txn_Commit();
					
				echo '<br />';
				prnMsg( _('Voted Item has been added to the database'),'success'); 
				}
		}
	}
	}
	if (isset($_POST['SelectedAccount'])){
	$SelectedAccount = $_POST['SelectedAccount'];
    }
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Select Order to be Committed') . '" alt="" />' . ' ' . _(      'Funds Allocations') . '</p>';
    //////////////////////////////////////////////////////////////////////////////////////////
    echo '<form action="" method="post" name="myform" enctype="multipart/form-data" target="_self">';	
	echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';		
	echo '<table align="center" style="width:35%">	
	<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
		<br />
		<table>
		<tr>
		<td>' .   _('Select GL Account').  ':</td>
		<td><select name="SelectedAccount" required="required" onchange="this.myform.submit">';

     $SQL = "SELECT accountcode,
				    accountname
			        FROM chartmaster
			        ORDER BY accountcode";

	$result=DB_query($SQL);
	if (DB_num_rows($result)==0){
	echo '</select></td>
	</tr>';
	prnMsg(_('No General ledger accounts have been set up yet') . ' - ' . _('budgets cannot be allocated until the GL accounts are set up'),'warn');
    } else {  
	while ($myrow=DB_fetch_array($result)){
	$glaccnt=$myrow['accountcode'];
		$Account = $myrow['accountcode'] . ' - ' . htmlspecialchars($myrow['accountname'],ENT_QUOTES,'UTF-8',false);
		if (isset($SelectedAccount) AND $SelectedAccount==$myrow['accountcode']){
			echo '<option selected="selected" value="' . $myrow['accountcode'] . '">' . $Account . '</option>';
			} else {
			 echo '<option value="' . $myrow['accountcode'] . '">' . $Account . '</option>';
		   }
	    }
	echo '</select></td>
	</tr>';
	     }
	echo '<tr><td colspan="2"><center><input name="Submit" type="submit" value="Select" /></center></td></tr>';
	if(isset($SelectedAccount) && $SelectedAccount !=''){
	 $SQL="SELECT period,
					budget,
					actual
				    FROM chartdetails
				    WHERE accountcode='". $SelectedAccount ."'";

	$result=DB_query($SQL);
	while ($myrow=DB_fetch_array($result)) {
		$Budget[$myrow['period']]=$myrow['budget'];
		$Actual[$myrow['period']]=$myrow['actual'];
	}
	 for ($i=1; $i<=12; $i++) {
	 $CurrentYearEndPeriod = GetPeriod(Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],0)),$db);
	 $ThisYearBudget=$ThisYearBudget+$Budget[$CurrentYearEndPeriod-(12-$i)];
	 }
	 $YEARENDL=Date('Y-m-d',YearEndDate($_SESSION['YearEnd'],-1));
	 $YEAREND=Date('Y-m-d',YearEndDate($_SESSION['YearEnd'],0));	
	 $SQL="SELECT *,SUM(allocated_Fund) as funds
				FROM funds_allocations 
				WHERE date >'".$YEARENDL."' AND date <= '".$YEAREND."' AND accountcode='". $SelectedAccount ."'";

	$result=DB_query($SQL);
	while ($myrow=DB_fetch_array($result)) {
	$allocatedtot=$myrow['funds'];
	$Votehead_display=$myrow['votecode'];
	}	
	$ThisYearBudgetBal=($ThisYearBudget-$allocatedtot);	
	
    echo'<tr>	 
	<td style="font-size:10pt">Total Budget</td> <td>'. locale_number_format($ThisYearBudgetBal,$_SESSION['CompanyRecord']['decimalplaces']) .'</td>
	</tr>	
	<tr><td>Financial Year End</td><td><select name="Yearend">';
			   
			   echo'<option value="'.  Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],0)) . '">' .Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],0)) . '</option>';			   
	 
	echo '</select></td></tr>
	<tr>
	<td>Select Votehead Account</td>';
	$header="SELECT * FROM  voteheadmaintenance 
				WHERE Votecode ='". $SelectedAccount ."'";

	$result5=DB_query($header);
	if(DB_num_rows($result5)>0){
	while ($myrow5=DB_fetch_array($result5)) {	
	echo'<td><input name="Votecode" type="text" size="60"  disabled value="'.$myrow5['Votecode'] . " - ".$myrow5['Votehead'].'" /></td>';
	}
	}else{
	echo'<td colspan=4 align=center style="color:#FF0000;">Votehead correspond the selected account could be found!,consult system admin</td>';
	}
	/*<td><select name="Votecode">';
	echo '<option  selected="selected" value="">--Select Votehead--</option>';
    $sql = "SELECT *,glaccount FROM voteheadmaintenance  ORDER BY VoteCode";
	$result1 = DB_query($sql);
	while ($myrow=DB_fetch_array($result1)){
	
			$Account = $myrow['Votecode'] . ' - ' . htmlspecialchars($myrow['Votehead'],ENT_QUOTES,'UTF-8',false);
			if (isset($SelectedVotehead) AND $SelectedVotehead==$myrow['Votecode']){
				echo '<option selected="selected" value="' . $myrow['Votecead'] . '">' . $Account . '</option>';					
			} else {			
				echo '<option value="' . $myrow['Votecode'] . '">' . $Account . '</option>';
			}
	     }
	echo '</select></td>*/
		echo'</tr>';	
	
	
	echo'<tr>
	<td style="font-size:10pt">Allocated Fund</td><td><input type="text" size="20" maxlength="60" name="allocated_Fund" value="'.$_POST[$allocated_Fund].'" /><td> <td><center></center></td></tr>	<input name="budgetbalance" type="hidden" value="'.$ThisYearBudgetBal.'" />
	<tr>
	<tr>
	<td>&nbsp;</td><td><input type="submit" name="submit" value="Submit" /></td></tr>
	</tr>';
	echo '</table>';
	    }
    ?>
	<table></table>	
	<?php
	include('includes/footer.inc');
	?>
