<?php
	$PageSecurity=0;
	
	include('includes/session.inc');
	$Title=_('Field Service  Maintenance');
	include('includes/header.inc');
	
	if ($InputError == 0){
	if (isset($_POST['submit']))
	{}
	}
		   
	$service_Id= $_POST['service_Id'] ;					
	$service_Name=$_POST['service_Name'];
	
	 if (isset($_POST["submit"])) {   
	 $result = DB_query("SELECT service_Id
								FROM farmservicesmaintenance
								WHERE service_Id='" . $service_Id ."'");
	     
	if(empty($service_Id)) {
	prnMsg(_('Service ID cannot be Empty '),		'error');
	$InputError = 1;
	}elseif(empty($service_Name)) {
	prnMsg(_('Service  Name cannot be Empty '),		'error');
	$InputError = 1;
	}else if (DB_num_rows($result)==1){
	prnMsg(_('The Service ID entered is already in the database - duplicate Service ID are prohibited by the system. Try choosing an alternative Service ID'),'error');
	$InputError = 1;
    }else if ($InputError != 1) { 
    /////////////////////////////////////////////////////////////////////////  
	
	
	DB_query("INSERT INTO `farmservicesmaintenance`(service_Id,service_Name) 
					 VALUES ('$service_Id','$service_Name')");
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
	echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Farm Service Maintenance') . '" alt="" />' . ' ' . _(    'Farm Service Maintenance') . '</p>';
    //////////////////////////////////////////////////////////////////////////////////////////
    echo '<form action="" method="post" name="myform" enctype="multipart/form-data" target="_self">';	
	echo '<input name="FormID" type="hidden" value="'.$_SESSION['FormID'].'" />';
	?>		
	<table align="center" style="width:45%">
	<tr>
	<td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Service Id</td><td><input type="text" size="10" maxlength="60" placeholder="Serive Id" name="service_Id" /><td><center></center>
	</td> 
	</tr>		
	<tr>
	 <tr><td style="font-size:10pt">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Service Name</td><td><input type="text" size="40" placeholder="Name of the Service" maxlength="60" name="service_Name" /><td><td><center></center>   </td></tr>	 
    </tr>
  	<tr>
	<td>&nbsp;</td><td><input type="submit" name="submit" value="Submit" /></td></tr>
	</tr>
	</table>
	<?php
   include('includes/footer.inc');
    ?>	
