<?php
	$PageSecurity=0;
	
	include('includes/session.inc');
	$Title=_('Field Maintenance');
	include('includes/header.inc');
	
	if ($InputError == 0){
	if (isset($_POST['submit']))
	{}
	}	   
	$code= $_POST['code'] ;					
	$Field_Name=$_POST['Field_Name'];
	$acres=$_POST['acres'] ;
	
	 if (isset($_POST["submit"])) {   
	 $result = DB_query("SELECT code
								FROM farmfield
								WHERE code='" . $code ."'");
    /////////////////////////////////////////////////////////////////////////  
	if(empty($code)) {
	prnMsg(_('Please Field Code cannot be Empty '),		'error');
	$InputError = 1;
	}elseif(empty($Field_Name)) {
	prnMsg(_('Please Field Name cannot be Empty '),		'error');
	$InputError = 1;
	}else if (DB_num_rows($result)==1){
	prnMsg(_('The field code  entered is already in the database - duplicate Service ID are prohibited by the system. Try choosing an alternative Field Code'),'error');
	$InputError = 1;
    }else if ($InputError != 1) { 
	
	DB_query("INSERT INTO `farmfield`(code,Field_Name,acres) 
					 VALUES ('$code','$Field_Name','$acres')");
							  $InsResult = DB_query($sql,$ErrMsg,$DbgMsg,true);
								DB_Txn_Commit();
								if (DB_error_no() ==0) {
					 $InsResult = DB_query($sql,$ErrMsg,$DbgMsg,true);
					DB_Txn_Commit();
					
				echo '<br />';
				prnMsg( _('New Service has been added to the database'),'success'); 
     }
     }
	 }

	 ///////////////////////////////////////////
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Farm Service Descriptions') . '" alt="" />' . ' ' . _(    'Farm Fields') . '</p>';
    //////////////////////////////////////////////////////////////////////////////////////////
    echo '<form action="" method="post" name="myform" enctype="multipart/form-data" target="_self">';	
	echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';
	?>		
	<table align="center" style="width:45%">
	<tr>
	<td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Field Id</td><td><input type="text" size="10" maxlength="60" placeholder="Field Id" name="code" /><td><center></center>
	</td> 
	</tr>		
	<tr>
	 <tr><td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Field Name</td><td><input type="text" size="40" placeholder="Name of the field" maxlength="60" name="Field_Name" /><td><td><center></center>   </td></tr>	 
    </tr>
   <tr><td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Acres</td><td><input type="text" size="40" placeholder="No.Of Acres" maxlength="60" name="acres" /><td><td><center></center>   </td></tr>	
	<tr>
	<td>&nbsp;</td><td><input type="submit" name="submit" value="Submit" /></td></tr>
	</tr>
	</table>
	<?php
   include('includes/footer.inc');
    ?>	
