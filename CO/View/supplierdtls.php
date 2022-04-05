<?php
	ob_start();
    session_start();
    if(!isset($_SESSION['Username'])){
         header("Location: login.php");
    }
	if (is_numeric($_GET['id'])){
	$id=$_GET['id'];
	}else{
		session_start();
	$_SESSION['err_msg']='
					<p><strong>Invalid ID Please check and try Again!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:dashboard.php");
	}
	require_once('../inc/config.php');
	
	$strSQL1 = "SELECT * FROM suppliers WHERE SupplierID=$id";
	$objQuery1 = mysqli_query($conn,$strSQL1) or die ("Error Query [".$strSQL1."]");
	$Num_Rows1 = mysqli_num_rows($objQuery1);
	while($row=mysqli_fetch_array($objQuery1)){
	$add=$row['Postal_Address'];
	$name=$row['Name'];
	$phn=$row['Phone_No'];
	$email=$row['Email_Add'];
	$cper=$row['Contact_Person'];
	$cphn=$row['Contact_No'];
	$on=$row['Date_Added'];
	$by=$row['Added_By'];
	}
	?>
	
					<div class="box">
					<!-- Box Head -->
					<div class="box-head">
						<h2 class="left">Supplier Details</h2>
						
					</div>
						<div class="table">
						<table width="553" border="0">
  <tr>
    <th width="160">Name:</th>
    <td width="168"><?php echo $name; ?></td>
    <th width="211">Added By: </th>
  </tr>
  <tr>
    <th>Postal Add</th>
    <td><?php echo $add; ?></td>
    <td><?php echo $by; ?></td>
  </tr>
   <tr>
    <th>Contact Person:</th>
    <td><?php echo $cper; ?></td>
    <th>Added On: </th>
  </tr>
   <tr>
    <th>Phone No.</th>
    <td><?php echo $phn; ?></td>
    <td><?php echo $on; ?></td>
  </tr>
   <tr>
    <th>Email Add</th>
    <td><?php echo $email; ?></td>
    <td></td>
  </tr>
   <tr>
    <th>Contact No.</th>
    <td><?php echo $cphn; ?></td>
    <td></td>
  </tr>
</table>
						
				</div>	
				</div>
				

