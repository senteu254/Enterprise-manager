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
						<th width="15%">' . _('Gate') . '</th>
						<th>' . _('Purpose') . '</th>
						<th>' . _('Time In') . '</th>
						<th><center>' . _('Time Out') . '</center></th>
					</tr>';
			$sqls= "SELECT gates.description as gate,
							remarks,
							time_in,
							time_out,
							check_out,
							CheckID
							FROM vehicle_timein 
							INNER JOIN gates ON gates.GateID=vehicle_timein.GateID 
							WHERE VehicleNo=".$VID." AND check_out=0
							GROUP BY vehicle_timein.CheckID, vehicle_timein.GateID ORDER BY check_out ASC, vehicle_timein.time_in DESC";
			$result = DB_query($sqls);
					while($row=DB_fetch_array($result)){
				echo '<tr><td><center >'.$row['gate'].'</center></td>
					<td>' . $row['remarks'] . '</td>
					<td width="160px">' . date("d, M Y h:i A",strtotime($row['time_in'])) . '</td>
					<td width="160px"><center>' . ($row['check_out']==0 ? '<button onclick="myFunction('.$row['CheckID'].')" type="button" class="btn btn-danger btn-sm"> CheckOut</button>' : date("d, M Y h:i A",strtotime($row['time_out']))) . '</center></td>';
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