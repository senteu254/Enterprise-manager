<?php
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$VID = $_GET['VID'];
?>
			<form enctype="multipart/form-data" name="formless" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
			<input type="hidden" name="CheckOutID" id="CheckOutID" value="" /><input type="hidden" name="Remarks" id="Remarks" value="" />
			<input type="hidden" name="VID" id="VID" value="<?php echo $VID; ?>" />
			<br />
			<?php		
				echo '<table style="width:850px;" class="table table-hover table-striped"><tbody>';
				echo '<tr>
						<th width="10%">' . _('Gate') . '</th>
						<th width="25%">' . _('Host') . '</th>
						<th width="20%">' . _('Department') . '</th>
						<th width="15%">' . _('Purpose') . '</th>
						<th width="15%">' . _('Time In') . '</th>
						<th width="15%"><center>' . _('Time Out') . '</center></th>
					</tr>';
			$sql= "SELECT gates.description as gate, 
							visitor_timein.time_in, 
							visitor_timein.host,
							visitor_timein.purpose,
							visitor_timein.time_out, 
							visitor_timein.sec_officer, 
							visitor_timein.sec_officer_checkout, 
							visitor_timein.remarks, 
							visitor_timein.check_out, 
							departments.description as dept,
							CheckID
							FROM visitor_timein 
							INNER JOIN gates ON gates.GateID=visitor_timein.GateID 
							INNER JOIN departments ON departments.departmentid=visitor_timein.departmentid
							WHERE visitor_timein.VisitorNo='" . $VID . "' AND check_out=0 GROUP BY visitor_timein.CheckID, visitor_timein.GateID ORDER BY visitor_timein.time_in DESC";
			$result = DB_query($sql);
					while($row=DB_fetch_array($result)){
				echo '<tr style="font-size:10px"><td ><center >'.$row['gate'].'</center></td>
					<td>' . $row['host'] . '</td>
					<td>' . $row['dept'] . '</td>
					<td>' . $row['purpose']. '</td>
					<td><center>' . date("d, M Y h:i A",strtotime($row['time_in'])) . '</center></td>
					<td><center>' . ($row['check_out']==0 ? '<button onclick="myFunction('.$row['CheckID'].')" type="button" class="btn btn-danger btn-sm"> CheckOut</button>' : date("d, M Y h:i A",strtotime($row['time_out']))) . '</center></td>';
			echo '</tr>';
			}
			echo '<tbody></table>';
			
			
			echo '<div id="popDiv" style="z-index: 999;
									width: 100%;
									height: 100%;
									top: 0;
									left: 0;
									display: none;
									position: absolute;				
									background-color: #fff;
									background-color: rgba(255,255,255,0.7);
									filter: alpha(opacity = 50);">';
	
	
	echo '<table style="width: 300px;
						background:#FFFFFF;
						height: 150px;
						position: absolute;
						color: #000000;
						/* To align popup window at the center of screen*/
						top: 35%;
						left: 73%;
						margin-top: 180px;
						margin-left: -280px;">
			<tr>';
				echo "<th>" . _('Please Give Us Any Remarks') . ": </th></tr>";
			echo '<tr>
				<td>
				<textarea name="remarks" class="form-control input-md" ></textarea>
				<input name="checkid" id="checkids" type="hidden" />
				</td>			
			</tr>
			<tr>
				<td><button type="submit" name="CheckOut" class="btn btn-primary btn-sm"> ' . _('CheckOut') . '</button> <div class="pull-right"><button onclick="hide(\'popDiv\')" type="button" class="btn btn-danger btn-sm"> Cancel</button></div>';
	echo '</td>
			</tr>
			</table>';
	
	echo '</div>';
			?>
	</form>	
            <!-- /.box-footer -->
          </div>
		  
		  <script>
function myFunction(id) {
    var person = prompt("Please Give Us Any Remarks:", "Thanks and Welcome back");
    if (person ) {
         document.getElementById("CheckOutID").value = id;
		 document.getElementById("Remarks").value = person;
		 document.formless.action='';
		document.formless.submit();
    }
}
</script>