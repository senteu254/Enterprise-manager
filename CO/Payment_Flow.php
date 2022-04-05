<?php
ob_start();
 ?>
<div id="sidebar">
				
				<!-- Box -->
				<div class="box" style="margin-top:-17%;">
					
					<!-- Box Head -->
					<div class="box-head" style="background:#337ab7; color: white; border-radius: 7px; no-repeat 0 0; padding:0 0 0 15px;">
						<h2>Payment Flow</h2>
					</div>
					<!-- End Box Head-->
					<br>
					<?php
					if(isset($Querytrack)){
					$Rows = DB_num_rows($Querytrack);
					if($Rows>0){
					echo '<div class="table">';
				  	echo '<table border="0">
				  		<tr>
						<th style=font-size:9px>ID</th>
						<th>Date Paid</th>
						<th style=font-size:9px>Amount</th>
					  </tr>';
					while($data = DB_fetch_row($Querytrack)){
					$truncated = (strlen($data[2]) > 15) ? substr($data[2], 0, 15) . '...' : $data[2];
					
		 		 	echo("<tr><td style=font-size:9px>$data[0]</td><td style=font-size:9px>$truncated</td><td style=font-size:9px; align=right><strong>".number_format($data[1], 0)."</strong></td></tr>");
		  
					}
					echo "</table>";
					echo '<div align="right">';
					echo "<table>";
					$qry = DB_query(" SELECT SUM(Amount_LC) AS total_paid FROM contract_payment WHERE ContractID=$id");
	$row = DB_fetch_assoc($qry);
					echo '<tr><td><strong>Sub Total: '.number_format($row['total_paid'], 0).'</strong></td></tr>';
					echo "</table>";
					echo '</div>';
					echo '<div class="box-content">
						<a href="PDFReport_Contract_Payment.php?id='.$id.'" class="add-button"><span>Print Report</span></a>
						<div class="cl">&nbsp;</div></div>';
					echo '</div>';
					}else{
					$r='<div style="font-size:13px; font-weight:bold; color:#FF3366">No Payment Record Found</div>';
					echo '<table border="0">
					  <tr>
						<td align="center">'.$r.'</td>
					  </tr>
					  </table>';
					}
					}else{
					echo '<div class="box-content">
						<a href="CO/PDFReport_General_Payment.php" class="add-button"><span>General Payment Report</span></a>
						<div class="cl">&nbsp;</div></div>';
					echo "</div>";
					}
					?>
					
					</div>
				</div>
				<!-- End Box -->
			</div>
