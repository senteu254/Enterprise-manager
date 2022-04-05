<?php
include('../includes/session.inc');
include('../includes/SQL_CommonFunctions.inc');
?>
			<form enctype="multipart/form-data" method="post" class="form-horizontal">
			<?php echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
			<?php
			$VID = $_GET['VID'];
				echo '<table style="width:100%;" class="table table-hover table-striped"><tbody>';
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
							WHERE visitor_timein.VisitorNo='" . $VID . "' GROUP BY visitor_timein.CheckID, visitor_timein.GateID ORDER BY visitor_timein.time_in DESC";
			$result = DB_query($sql);
					while($row=DB_fetch_array($result)){
				echo '<tr style="font-size:10px"><td ><center >'.$row['gate'].'</center></td>
					<td>' . $row['host'] . '</td>
					<td>' . $row['dept'] . '</td>
					<td>' . $row['purpose']. '</td>
					<td><center>' . date("d, M Y h:i A",strtotime($row['time_in'])) . '</center></td>
					<td><center>' . ($row['check_out']==0 ? '<button onclick="popshow(\'popDiv\','.$row['CheckID'].')" type="button" class="btn btn-danger btn-sm"> CheckOut</button>' : date("d, M Y h:i A",strtotime($row['time_out']))) . '</center></td>';
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
		  
		  <script type="text/javascript">
			function popshow(div,id) {
				document.getElementById(div).style.display = 'block';
				document.getElementById('checkids').value = id;
			}
			function hide(div) {
				document.getElementById(div).style.display = 'none';
			}
			//To detect escape button
			document.onkeydown = function(evt) {
				evt = evt || window.event;
				if (evt.keyCode == 27) {
					hide('popDiv');
				}
			};
		</script>

<style type="text/css">
<!--
.title {
	font-size: x-large;
	font-family: "Times New Roman", Times, serif;
	font-weight: bold;
	padding-bottom:2px;
}
.bg{
	background-color:#00CCFF;
	font-family:"Times New Roman", Times, serif;
	font-size:16px;
	border-radius:4px 4px 1px 1px;
	padding-bottom:3px;
	padding:2px;
	color:#FFFFFF;
	font-weight:bold;
}
.line{
	border-bottom:inset;
	width:90%;
	border-bottom-color:#00CCFF;
}
.content {
    background-color:white;
    margin:0 auto;
    width:100%;
    /*border-collapse: collapse;*/
    border:thin outset #B3B3B3;
    
    -webkit-border-radius:  4px;
    -moz-border-radius:     4px;
    border-radius:          4px;
    -moz-box-shadow:    1px 1px 2px #C3C3C3;
	-webkit-box-shadow: 1px 1px 2px #C3C3C3;
	box-shadow:         1px 1px 2px #C3C3C3;
	-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')";
	filter: progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')
}


a{
text-decoration:none;
}
.time {color: #999999; font-size:10px;}
.image{
		 border-radius:25px;
		 width:50px;
		 height:50px;
		 padding:20px,20px,20px,20px;
}
/*bubble*/
.bubble
{
position: relative;
width: 90%;

min-height: 10px;
padding-left:18px;
background: #DEFFFF;
-webkit-border-radius:  4px;
    -moz-border-radius:     4px;
    border-radius:          4px;
    -moz-box-shadow:    1px 1px 2px #C3C3C3;
	-webkit-box-shadow: 1px 1px 2px #C3C3C3;
	box-shadow:         1px 1px 2px #C3C3C3;
	-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')";
	filter: progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3');

}

.bubble:after
{
content: '';
position: absolute;
border-style: solid;
border-width: 9px 15px 9px 0;
border-color: transparent #DEFFFF;
display: block;
width: 0;
z-index: 1;
left: -15px;
top: 7px;
}

.link{font-size:9px;}
-->
</style>