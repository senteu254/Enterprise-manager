<?php 
	if (is_numeric($_GET['id'])){
	$id=$_GET['id'];
	}else{
	$_SESSION['err_msg']='
					<p><strong>Invalid ID Please check and try Again!</strong></p>
					<a href="#" class="close">close</a>';
					echo "<script>window.location.href = 'index.php?Application=CON&Page=Dashboard'</script>";
					//header("Location:dashboard.php");
	}	
	$strSQL1 = "SELECT  * FROM contract_details a inner join contract_type b on a.TypeID=b.TypeID WHERE ContractID=$id";
	$objQuery1 = DB_query($strSQL1);
	$Num_Rows1 = DB_num_rows($objQuery1);
	
	while($row=DB_fetch_array($objQuery1)){
	$no=$row['Contract_Number'];
	$type=$row['Name'];
	$name=$row['Contract_Name'];
	$begin=$row['Begin_Date'];
	$end=$row['End_Date'];
	$amt=$row['Amount'];
	$curr=$row['Currency'];
	$desc1=$row['Description'];
	$typedesc=$row['Type_Description'];
	$on=$row['Date_Added'];
	$by=$row['Added_By'];
	}
	?>
	
	<div class="box">
	<!-- Box Head -->
	<div class="box-head">
		<h2 class="left">Supplier Details</h2>
		
	</div>s
		<div class="table">
		<table width="553" border="0">
  <tr>
    <th width="160">Contract No:</th>
    <td width="168"><?php echo $no; ?></td>
    <th width="211">Added By: </th>
  </tr>
  <tr>
    <th>Contact Name:</th>
    <td><?php echo $name; ?></td>
    <td><?php echo $by; ?></td>
  </tr>
   <tr>
    <th>Amount Allocated:</th>
    <td><?php echo number_format($amt, 2);?></td>
    <th>Added On: </th>
  </tr>
   <tr>
    <th>Currency:</th>
    <td><?php echo $curr; ?></td>
    <td><?php echo $on; ?></td>
  </tr>
   <tr>
    <th>Begin Date:</th>
    <td><?php echo $begin; ?></td>
    <td></td>
  </tr>
   <tr>
    <th>End Date:</th>
    <td><?php echo $end; ?></td>
    <td></td>
  </tr>
  <table width="553">
  <tr>
    <th width="145">Description</th>
    <td><?php echo $desc1; ?></td>
  </tr>
  </table>
</table>						
</div>	
</div>
				

