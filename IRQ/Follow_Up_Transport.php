<?php 
if(!isset($_SESSION['UserID']) && !isset($_SESSION['AccessLevel']) && !isset($_SESSION['UsersRealName']) && !isset($_SESSION['DatabaseName'])){
header('location:../index.php');
}
require_once('includes/session.inc');
echo '<link href="' . $RootPath . '/css/'. $_SESSION['Theme'] .'/default.css" rel="stylesheet" type="text/css" />';
echo '<link href="' . $RootPath . '/facebox/src/facebox.css" rel="stylesheet" type="text/css" />';
echo '<script src="' . $RootPath . '/facebox/src/facebox.js" type="text/javascript"></script>';
$sql="SELECT * FROM irq_request z 
							INNER JOIN irq_transport a on a.TransportID = z.requestid 
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							WHERE initiator = '".$_SESSION['UserID']."' AND draft=0
							ORDER BY Requesteddate DESC";
	$result=DB_query($sql, '',  '',false, false);

		if (DB_error_no()!=0) {
			$Title = _('Transaction Follow Up');
			
			prnMsg( _('There was a problem retrieving the Requisition details') . ' ' . _('from the database') . '. ','error');
			if ($debug==1) {
				prnMsg (_('The SQL used to get this information that failed was') . '<br />' . $sql,'error');
			}
			
			exit;
		}
		
			
?>
<style type="text/css">
.even{
background:#CCCCCC;
height:40px;
}
.odd{
background:#FFFFFF;
height:40px;
}
.inprogress{
background:#00CCFF;
width:60px;
color:#FFFFFF;
font-size:9px;
font-family:Georgia, "Times New Roman", Times, serif;
font-weight:bold;
border-radius: 3px;
}
.completed{
background:#0033CC;
width:60px;
color:#FFFFFF;
font-size:9px;
font-family:Georgia, "Times New Roman", Times, serif;
font-weight:bold;
border-radius: 3px;
}
.cancelled{
background:#FF0000;
width:60px;
color:#FFFFFF;
font-size:9px;
font-family:Georgia, "Times New Roman", Times, serif;
font-weight:bold;
border-radius: 3px;
}
.outer {
    background-color:white;
    width:100%;
	height:15px;
    -webkit-border-radius:  4px;
    -moz-border-radius:     4px;
    border-radius:          4px;
    -moz-box-shadow:    1px 1px 2px #C3C3C3;
	-webkit-box-shadow: 1px 1px 2px #C3C3C3;
	box-shadow:         1px 1px 2px #C3C3C3;
	-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')";
	filter: progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')
}
.inner {
  background: -webkit-linear-gradient(#009900, #66FF66, #66CCFF); /* For Safari 5.1 to 6.0 */
  background: -o-linear-gradient(#009900, #66FF66, #66CCFF); /* For Opera 11.1 to 12.0 */
  background: -moz-linear-gradient(#009900, #66FF66, #66CCFF); /* For Firefox 3.6 to 15 */
  background: linear-gradient(#009900, #66FF66, #66CCFF); /* Standard syntax */
    height:15px;   
    -webkit-border-radius:  4px;
    -moz-border-radius:     4px;
    border-radius:          4px;
    -moz-box-shadow:    1px 1px 2px #C3C3C3;
	-webkit-box-shadow: 1px 1px 2px #C3C3C3;
	box-shadow:         1px 1px 2px #C3C3C3;
	-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')";
	filter: progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')
}
.innercancelled {
  background: -webkit-linear-gradient( #FF0000, #FFFFFF, #FF6600); /* For Safari 5.1 to 6.0 */
  background: -o-linear-gradient(#FF0000, #FFFFFF, #FF6600); /* For Opera 11.1 to 12.0 */
  background: -moz-linear-gradient(#FF0000, #FFFFFF, #FF6600); /* For Firefox 3.6 to 15 */
  background: linear-gradient(#FF0000, #FFFFFF, #FF6600); /* Standard syntax */
    height:15px;   
    -webkit-border-radius:  4px;
    -moz-border-radius:     4px;
    border-radius:          4px;
    -moz-box-shadow:    1px 1px 2px #C3C3C3;
	-webkit-box-shadow: 1px 1px 2px #C3C3C3;
	box-shadow:         1px 1px 2px #C3C3C3;
	-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')";
	filter: progid:DXImageTransform.Microsoft.Shadow(Strength=1, Direction=135, Color='#C3C3C3')
}
.tbrow{
background:#999999;
height:40px;
}
</style>

			<script type="text/javascript">
				jQuery(document).ready(function($) {
					$(" a[rel*=facebox]" ).facebox({
						loadingImage : "facebox/src/loading.gif" ,
						closeImage   : "facebox/src/closelabel.png" 
					})
				})
	</script>
<div align="center" style="width:80%">
<table>
  <tr>
    <th colspan="7"><h4>Transport Requisition Proformae</h4></th>
  </tr>
  <tr>
    <th width="80px">Process No.</th>
	<th>Dept/Section</th>
	<th>Document Name</th>
	<th>Required From</th>
	<th>Required To</th>
	<th>Status</th>
	<th>Progress</th>
  </tr>
   <?php
   $i=0;
   while($myrow = DB_fetch_array($result)){
   $i++;
   if($i%2 == 0){ $class = 'even'; }else{ $class = 'odd'; }
  
   $sql="SELECT max(level_id) as id FROM irq_levels WHERE doc_id='". $myrow['doc_id'] ."'";
	$result1=DB_query($sql, '',  '',false, false);
	$myrow1 = DB_fetch_array($result1);
	$sql="SELECT max(level) as level FROM irq_authorize_state WHERE requisitionid='". $myrow['requestid'] ."'";
	$result2=DB_query($sql, '',  '',false, false);
	$myrow2 = DB_fetch_array($result2);
    $prog = ($myrow2['level']*90)/$myrow1['id'];
	
   //check if all the items are closed
   if($prog !=100 && $myrow['closed'] ==0){
    $status='<div class="inprogress">In Progress</div>';
	$cla='inner';
	$progress = $prog;
    }elseif($myrow['closed'] ==2){
	$status='<div class="cancelled">Cancelled</div>';
	$cla='innercancelled';
	$progress = 100;
	}else{
	//end of check if all the items are closed
   $status='<div class="completed">Completed</div>';
   $cla='inner';
   $progress = 100;
   }
   
    echo "<tr onMouseOver=this.className='tbrow' onMouseOut=this.className='$class' class=$class>";
	echo '<td><center><a rel="facebox" href="IRQ/Follow_Up_View_Transport.php?id='. $myrow['requestid'] .'" title="View More" target="_parent">'. sprintf("%04d", $i) .'</a></center></td>
	<td>'. $myrow['description'] .'</td>
	<td>'. $myrow['doc_name'] .'</td>
	<td><center>'. ConvertSQLDate($myrow['required_from']).' '. date("h:i:s A",strtotime($myrow['required_from'])) .'</center></td>
	<td><center>'. ConvertSQLDate($myrow['required_to']).' '. date("h:i:s A",strtotime($myrow['required_to'])) .'</center></td>
	<td width="80px"><center>'. $status .'</center></td>
	<td width="140px">';
	echo '<div class="outer"><div class="'. $cla .'" style=" width:'. $progress .'%;"><center><b style="color:#fff;">'. round($progress) .'%</b></center></div></div>';
	//<progress value="'. $progress .'" max="100"></progress> <center><b style="color:#0099FF;">'. round($progress) .'%</b></center>
	echo '</td></tr>';
	
	}
	?>
</table>
</div>
