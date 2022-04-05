<link rel="stylesheet" type="text/css" href="datepickr.css" />
			
	 <link rel="stylesheet" href="js/smoothness/jquery-ui.css" />
    
    <!-- Load jQuery JS -->
    <script src="js/jquery-1.9.1.js"></script>
    <!-- Load jQuery UI Main JS  -->
    <script src="js/jquery-ui.js"></script>
    	
    <!-- Load SCRIPT.JS which will create datepicker for input field  -->
    <script src="script.js"></script>
    
    <link rel="stylesheet" href="runnable.css" />
       
               <script type="text/javascript">
                $(document).ready(function(){
                    $("#no").autocomplete({
                        source:'autocomplete.php',
                        minLength:1
                    });
                });
        </script>
		 <script type="text/javascript">
                $(document).ready(function(){
                    $("#type").autocomplete({
                        source:'autocompletemed.php',
                        minLength:1
                    });
                });
        </script>

	
<?php

/* $Id: MaintenanceTasks.php 5231 2012-04-07 18:10:09Z daitnree $*/

include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$Title = _('Employees Medical Records');

$ViewTopic = 'Employees Medical Records';
$BookMark = 'EmployeesMedicalRecords';

include('includes/header.inc');

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/group_add.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p><br />';


if (isset($_POST['Submit'])) {
	 $date = date('Y-m-d', strtotime($_POST['date']));
	 $No = GetNextTransNo( 80, $db);
		$sql="INSERT INTO medical(id,
		                                    personal_no,
											emp_name,
											doctor,
											age,
											marital_status,
											date,
											diagnosis)
						VALUES(  '" . $No . "',
						        '" . $_POST['no'] . "',
								'" . $_POST['name'] . "',
								'" . $_POST['doc'] . "',
								'" . $_POST['age'] . "',
								'" . $_POST['stat'] . "',
								'" . $date . "',
								'" . $_POST['diagnosis'] . "'
								)";
		$ErrMsg = _('The medical details cannot be inserted because');
		$Result=DB_query($sql,$ErrMsg);
		
		for($i=0;$i<count($_POST['sno']);$i++)
{
	$sno= $_POST['sno'][$i];
	$qty = $_POST['qty'][$i];
	$desc = $_POST['type'][$i];
	$dos = $_POST['dosage'][$i];
	//$no = $i+1;	
	$SQL="INSERT  INTO prescription (id,
	                                med_id,
									personal_no,
									description,
									qty,
									dosage) 
							VALUES('',
							        '". $No ."',
									'". $_POST['no'] ."',
									'". $desc ."',
									'". $qty ."',
									'". $dos."')";
					$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The request header record could not be inserted because');
		$DbgMsg = _('The following SQL to insert the request header record was used');
		$Result = DB_query($SQL,$ErrMsg,$DbgMsg,true);
		}
		if($Result){
		prnMsg(_('Medical details successfully Inserted'),'success');
		}
		else{
		prnMsg(_('Medical details  NOT successfully Inserted'),'error');
		}
		unset($_POST['no']);
		unset($_POST['name']);
		unset($_POST['doctor']);
		unset($_POST['date']);
		unset($_POST['diagnosis']);
		//unset($_POST['prescription']);
		unset($_POST['age']);
		unset($_POST['stat']);
		unset($_POST['qty']);
		unset($_POST['description']);
		unset($_POST['dosage']);
		
	}
	
	


echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '" method="post" id="form1">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<table class="selection">';

if (!isset($_POST['no'])){
	$_POST['no']='';
}
if (!isset($_POST['name'])){
	$_POST['name']='';
}
if (!isset($_POST['doctor'])){
	 $_POST['doctor']= '';
}
if (!isset($_POST['date'])){
	$_POST['date']='';
}
if (!isset($_POST['diagnosis'])){
	$_POST['diagnosis']='';
}

if (!isset($_POST['age'])){
	$_POST['age']='';
}
if (!isset($_POST['stat'])){
	$_POST['stat']='';
}

if (isset($_POST['submitreq'])) {
echo '<tr>
		<td>' . _('Employee No.').':</td>
		<td>'.$_POST['no'].'<input name="no" id="no" type="hidden" value="'.$_POST['no'].'" /></td>';
		echo'</tr>';
echo '<tr>
		<td>' . _('Employee Name').':</td>
		<td>';


$AssetSQL="SELECT * FROM employee where emp_id = '".$_POST['no']."'" ;
$AssetResult=DB_query($AssetSQL);
$myrow=DB_fetch_array($AssetResult);


echo'<input type="text"  required="required" name="name"  value="'.$myrow['emp_fname'] .' '.  $myrow['emp_lname']. '" readonly/>';

	
	
echo '<tr>
		<td>' . _('Doctor Responsible') . ':</td>
		<td><select required="required" name="doc">';
$UserSQL="SELECT * FROM doctor";
$UserResult=DB_query($UserSQL);
while ($myrow=DB_fetch_array($UserResult)) {
		echo '<option selected="selected" value="'.$myrow['doc_name'].'">' . $myrow['doc_name']. '</option>';
		}
		echo '</select></td>
	</tr>';

echo '<tr>
		<td>' . _('Date of Visit').':</td>
		<td><input type="text"   name="date" id="date" required="required" value="' . $_POST['date'] . '" /></td>
	</tr>';
	echo '<tr>
		<td>' . _('Age').':</td>
		<td><input type="text"   name="age" id="age" required="required"  class="integer" value="' . $_POST['age'] . '" /></td>
	</tr>';
	echo '<tr>
		<td>' . _('Marital Status') . ':</td>
		<td>
		<select id="stat" name="stat" required="true" class="form-control">
								
								  <option value="">Status</option>
								  <option>Single</option>
								  <option>Married</option>
								   <option>Widow</option>
								    <option>Widower</option>
								</select>
	</td>
	</tr>';

echo '<tr>
		<td>' . _('Diagnosis').':</td>
		<td><textarea name="diagnosis" required="required" cols="40" rows="3">' . $_POST['diagnosis'] . '</textarea></td>
	</tr>';


echo' <tr>
  <th colspan="2">
  <div align="center">
      <div style="width:100%;">
		<table id="dataTable" class="selection">';
echo '<tr>
		<th>' .  _('Description of Medicine'). '</th>
		<th>' .  _('Qty'). '</th>
		<th>' .  _('Dosage'). '</th>
	</tr>';



?>
	<tr>
			
			
		  <td><input type="text"  name="type[]" id="type"   required="required" value="" /></td>
			<td><input type="text" size="10" maxlength="10"  class="integer" name="qty[]" value="" /></td>
			<td><input type="text" size="10" maxlength="10" autofocus="autofocus"  name="dosage[]" value="" /></td>
			<td><a href="#" onClick="Javacsript:deleteRow(this)"><img src="/css/trash.png" title="' ._('Delete Row') . '" alt="" /></a></a></td>
		</tr>

</table>	
		<input name="" type="button" onclick=addRow("dataTable") title="' ._('Add New Row') . '" value="New Row" />
      </div>
    </div>
  </th>
  </tr>
  <tr>
  </table>
  <?php
	echo '<br />
		<div class="centre">
			<input type="submit" name="Submit" value="'._('Prescribe').'" />
		</div>
		
		</form>';


echo '</div>
        ';
include('includes/footer.inc');
}else{

echo '<tr>
		<td>' . _('Employee No.').':</td>
		
		<td><input  type="text" name="no" id="no" required="required" value="" /></td>
		<td>';
	
				echo'<input type="submit" name="submitreq" value="'._('Submit').'"  />
			</td>';
		echo'</tr></table>
		</form>';
		include('includes/footer.inc');
}
?>
<SCRIPT language="javascript">


        function addRow(tableID) {

            var table = document.getElementById(tableID);

            var rowCount = table.rows.length;
            var row = table.insertRow(rowCount);
            var colCount = table.rows[0].cells.length;
			
			var cell3 = row.insertCell(0);
            var element3= document.createElement("input");
            element3.type = "text";
			element3.required = "required";
            element3.name = "type[]";
			element3.id = "type";
            cell3.appendChild(element3);
			
		    var cell4 = row.insertCell(1);
            var element4= document.createElement("input");
            element4.type = "text";
			element4.required = "required";
			element4.size = "10";
            element4.name = "qty[]";
            cell4.appendChild(element4);
			
			var cell5 = row.insertCell(2);
            var element5 = document.createElement("input");
            element5.type = "text";
			element5.required = "required";
			element5.size = "10";
            element5.name = "dosage[]";
            cell5.appendChild(element5);
			
			
			row.insertCell(3).innerHTML= '<a href="#" onClick="Javacsript:deleteRow(this)"><? echo '<img src="css/trash.png" title="' ._('Delete Row') . '" alt="" /></a>';?></a>';


        }

        function deleteRow(obj) {
      
    var index = obj.parentNode.parentNode.rowIndex;
    var table = document.getElementById("dataTable");
    table.deleteRow(index);
    
}

function findTotal(){
    var arr = document.getElementsByName('amnt[]');
    var tot=0;
    for(var i=0;i<arr.length;i++){
        if(parseInt(arr[i].value))
            tot += parseInt(arr[i].value);
			total = tot.toString().replace(/([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,');
    }
    document.getElementById('total').value = total;
}

    </SCRIPT>
	<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
<script>
function Prop(sel) {
	var state_id = sel.options[sel.selectedIndex].value;  
	if (state_id.length > 0 ) { 
	 $.ajax({
			type: "GET",
			url: "Ajax_Medical.php",
			data: "prop="+state_id,
			cache: false,
			beforeSend: function () { 
				$('#output').html('<img src="loader.gif" alt="www" width="24" height="24">');
			},
			success: function(html) {    
				$("#output").html( html );
			}
		});
	}
}
</script>		
			
<link rel="stylesheet" href="js/smoothness/jquery-ui.css" />
    
    <!-- Load jQuery JS -->
    <script src="js/jquery-1.9.1.js"></script>
    <!-- Load jQuery UI Main JS  -->
    <script src="js/jquery-ui.js"></script>
    
    <!-- Load SCRIPT.JS which will create datepicker for input field  -->
    <script src="script.js"></script>
    
    <link rel="stylesheet" href="runnable.css" />
	

