
<?php
	if (isset($_POST['Property']))
	{
		$a = addslashes("$_POST[type]");
		$b = addslashes("$_POST[serial]");
		$c = addslashes("$_POST[dserial]");	
		$da = strtotime("$_POST[issuedate]");
		$d=date('Y/m/d',$da);
		$ee = strtotime("$_POST[returndate]");
		$e=date('Y/m/d',$ee);
		$f = addslashes("$_POST[description]");
		$g = addslashes("$_POST[issuer]");
		$h = addslashes("$_POST[receiver]");
		$i = addslashes("$_POST[value]");
		$qry ="insert into company_property (personal_no,type,serial_no,digital_serial,issue_date,return_date,description,issuer,receiver,cost) values('".$_GET[id]."','$a','$b','$c','$d','$e','$f','$g','$h','$i')";
		$ErrMsg = _('The Property details cannot be inserted because');
		$Result=DB_query($qry,$ErrMsg);
		if ($Result){
				prnMsg( _('Property Successfully Inserted'), 'success');
			}
			else{
			prnMsg( _('Not Inserted !'), 'error');
				}
		unset($_POST['type']);
		unset($_POST['serial']);
		unset($_POST['dserial']);
		unset($_POST['issuedate']);
		unset($_POST['returndate']);
		unset($_POST['description']);
		unset($_POST['issuer']);
		unset($_POST['receiver']);
		unset($_POST['value']);
			
	}
	
	if (isset($_POST['Update'])) {
		$sql="UPDATE company_property SET
				type = '" . $_POST['type'] . "',
				serial_no='".$_POST['serial'] ."',
				digital_serial='" . $_POST['dserial']."',
				issue_date='" . date('Y/m/d',strtotime("$_POST[issuedate]"))."',
				return_date='" .date('Y/m/d',strtotime("$_POST[returndate]"))."',
				description='" . $_POST['description']."',
				issuer='" . $_POST['issuer']."',
				receiver='" . $_POST['receiver']."',
				cost='" . $_POST['value']."'
				WHERE pid='".$_POST['cpid']."'
				AND personal_no='".$_GET['id']."'";

		$ErrMsg = _('The company property cannot be updated because');
		$Result=DB_query($sql,$ErrMsg);
		if ($Result){
		prnMsg( _('Property  Details Successfully Updated'), 'success');
			}
			else{
			prnMsg( _('Not Updated!'), 'error');
				}

		unset($_POST['type']);
		unset($_POST['serial']);
		unset($_POST['dserial']);
		unset($_POST['issuedate']);
		unset($_POST['returndate']);
		unset($_POST['description']);
		unset($_POST['issuer']);
		unset($_POST['value']);
		unset($_POST['receiver']);
}

if (isset($_GET['Delete'])) {

	$sql="DELETE FROM company_property
		WHERE pid='".$_GET['cpid']."'";

	$ErrMsg = _('The company property cannot be deleted because');
	$Result=DB_query($sql,$ErrMsg);
	if ($Result){
				prnMsg( _('Property Details Successfully Deleted!'), 'success');
			}
			else{
			prnMsg( _('Not Deleted!'), 'error');
				}

}
	?>
<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php
echo'<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
 if (isset($_GET['Edit'])) {
	
	echo '<input type="hidden" name="cpid" value="'.$_GET['cpid'].'" />';
	$sql="SELECT * FROM company_property
			WHERE pid='".$_GET['cpid']."'";
	$ErrMsg = _('The company property details cannot be retrieved because');
	$result=DB_query($sql,$ErrMsg);
	$myrow=DB_fetch_array($result);
	    $type = $myrow['type'];
		$_POST['rserial']=$myrow['serial_no'];
		$_POST['rdserial']=$myrow['digital_serial'];
		$_POST['rissuedate']=$myrow['issue_date'];
		$_POST['rreturndate']=$myrow['return_date'];
		$_POST['rdescription']=$myrow['description'];
		$_POST['rissuer']=$myrow['issuer'];
		$_POST['rreceiver']=$myrow['receiver'];
		$_POST['rvalue']=$myrow['cost'];
	
}
function Proper(){
$sql="SELECT * FROM stockcategory";
$result=DB_query($sql);
while ($row=DB_fetch_array($result)) {
    $category=$row["categorydescription"];
	$cat=$row["categoryid"];
    echo "<option value=". $cat."";?><?=$category == ''.$categ.'' ? ' selected="selected"' : '';?><?php echo ">".$cat.'-'.$category."</option>";
}
}
?>
			<div class="panel panel-default" id="addinfo" <?php echo ($_GET['Edit']=='Yes'? '':'style="display:none;"') ?>>
		<div class="panel-heading">Add Information</div>
			<div class="panel-body">				<!-- Text input-->
							<div class="form-group" >
							  <div class="col-md-4">Property Category:
							 <select id='StockCat' name='StockCat' onChange="Prop(this);" class='form-control' >
							<OPTION VALUE="">Choose</OPTION>
							 <?php
							$sql="SELECT * FROM stockcategory";
							$result=DB_query($sql);
							while ($row=DB_fetch_array($result)) {
								 $category=$row["categorydescription"];
	                             $cat=$row["categoryid"];
								echo "<option value=".$row["categoryid"]."";?><?=$categ == ''.$category.'' ? ' selected="selected"' : '';?><?php echo ">".$cat.'-'.$category."</option>";
							}
								
								?>	   
							</select>
							  </div>
							  <div id="output">
							  <div class="col-md-4">Property Type
								 <select id='type' name='type'  class='form-control' >
							<OPTION VALUE="">Choose</OPTION>
							 <?php if(isset($type)){
									echo '<OPTION selected="selected" value="'.$type.'">'.$type.'</OPTION>';
									}
							?>
							</select>
							  </div>
							</div>

							 
							</div>


							 
							<!-- Text input-->
							<div class="form-group" >
							
							  <div class="col-md-4">Serial Number
							 <input id="serial" name="serial" value="<?php  echo $_POST['rserial']; ?>" class="form-control" />
							  </div>
							 <div class="col-md-4">Digital Serial
							  <input id="type" name="dserial"  value="<?php  echo $_POST['rdserial']; ?>" class="form-control"/ >
							  </div>
							  <div class="col-md-4">Issued Date
							  <input type="text" id="date" name="issuedate" value="<?php  echo $_POST['rissuedate']; ?>" class="form-control"  />
							  </div>
							 
							</div>

							 
							<!-- Text input-->
							<div class="form-group">
							 <div class="col-md-4">Sheduled Return
							  <input type="text" id="dates" name="returndate" value="<?php  echo $_POST['rreturndate']; ?>" class="form-control " />
							  </div> 
							  <div class="col-md-4">Issued BY
							  <input type="text" id="issuer" name="issuer" value="<?php  echo $_POST['rissuer']; ?>" class="form-control" />
							  </div>
							  <div class="col-md-4">Received By
							 <input type="text" id="receiver" name="receiver" value="<?php  echo $_POST['rreceiver']; ?>" class="form-control"  />
							  </div>
					
							  
							  </div>
							  
							<div class="form-group">
							<div class="col-md-4">Estimate Monetary Value
							  <input type="text" id="value" name="value" value="<?php  echo $_POST['rvalue']; ?>" class="form-control "  />
								 </div>
							  <div class="col-md-4">Property Description
							 <textarea class="form-control" id="description" name="description" placeholder="Property Description" ><?php  echo $_POST['rdescription']; ?></textarea>
							 </div>
							  </div>
							  
							  <?php if (isset($_GET['Edit'])) {
						echo '<div class="form-group">
							  <div class="col-md-12">
							  <button id="submit" name="Update" class="btn btn-primary">Update</button>
							   <a href="index.php?Application=HR&Ref=Profile&Link=Property&id='.$_GET['id'].'"><input name="" class="btn btn-default" type="button" value="Cancel" /></a>
							</div>
							</div>';
							} else {
							echo'<div class="form-group">
							  <div class="col-md-12">
							  <button id="submit" name="Property" class="btn btn-primary">Enter New</button>
							  <input name="" class="btn btn-default" onclick="myCheck2();" type="button" value="Cancel" />
							  </div>
							</div>';
						}
		echo '</div></div>';
echo '<div class="col-md-4">
						<div class="form-group">
						<input name="" class="btn btn-success" id="addbtn" onclick="myCheck();" type="button" value="Add New" />
						</div>
						</div>';
echo '<table class="table table-hover" style="margin-top:10px; width:100%;">
     <tr >
		<th width="50" scope="col">' . _('S/No') . '</th>
		<th width="80" scope="col">' . _('Type') . '</th>
		<th width="350" scope="col">' . _('Description') . '</th>
		<th width="120" scope="col">' . _('Issue Date') . '</th>
		<th width="120" scope="col">' . _('Return Date') . '</th>
		<th width="100">' . _('Actions') . '</th>
    </tr>';

$select = "SELECT * FROM company_property where personal_no='".$_GET['id']."'";
$i=0;	
$ErrMsg = _('The company property details cannot be retrieved because');
$qry1=DB_query($select,$ErrMsg);
if(DB_num_rows($qry1)>0){
while($rec = DB_fetch_array($qry1)){
$i++;
	echo '<tr class="'.$class.'">';
	
			echo'<td>' . $i . '</td>
			<td>' . $rec['type'] . '</td>
			<td>' . $rec['description'] . '</td>
			<td>' . $rec['issue_date'] . '</td>
			<td>' . $rec['return_date'] . '</td>
			<td><a href="index.php?Application=HR&Ref=Profile&Link=Property&Edit=Yes&id='. $_GET['id'] .'&cpid=' . $rec['pid'] .'">' . _('Edit') . '</a> || <a href="index.php?Application=HR&Ref=Profile&Link=Property&Delete=Yes&id='. $_GET['id'] .'&cpid=' . $rec['pid'] .'" onclick="return confirm(\'' . _('Are you sure you wish to delete this Property Details ?') . '\');">' . _('Delete') . '</a></td>
		</tr>';

}
}else{
echo '<td colspan="6" class="alert-danger"><center>No Records Found</center></td>';
}
echo '</table>';
?>
		</form>
	<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
<script>
function Prop(sel) {
	var state_id = sel.options[sel.selectedIndex].value;  
	if (state_id.length > 0 ) { 
	 $.ajax({
			type: "GET",
			url: "Ajax_Property.php",
			data: "prop="+state_id,
			cache: false,
			beforeSend: function () { 
				$('#output').html('<img src="loader.gif" alt="Loding..." width="24" height="24">');
			},
			success: function(html) {    
				$("#output").html( html );
			}
		});
	}
}
</script>		
			
 <link rel="stylesheet" href="js/smoothness/jquery-ui.css" />
    
    <!-- Load jQuery UI Main JS  -->
    <script src="js/jquery-ui.js"></script>
    
    <!-- Load SCRIPT.JS which will create datepicker for input field  -->
    <script src="HR_Head/script.js"></script>
    
    <link rel="stylesheet" href="HR_Head/runnable.css" />
<script type="text/javascript">
	  function myCheck() {
  var addbtn = document.getElementById("addbtn");
  var text = document.getElementById("addinfo");
    text.style.display = "block";
	addbtn.style.display = "none";
}
function myCheck2() {
  var addbtn = document.getElementById("addbtn");
  var text = document.getElementById("addinfo");
    text.style.display = "none";
	addbtn.style.display = "block";
}
</script>	

