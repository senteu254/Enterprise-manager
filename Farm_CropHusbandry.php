<?php
	$PageSecurity=0;
	
	include('includes/session.inc');
	$Title=_('Crop Husbadry');
	include('includes/header.inc');
	include('includes/SQL_CommonFunctions.inc');
	$FarmID = GetNextTransNo(100, $db);
	
	if (isset($_POST['SubmitForm'])){
	if ($InputError == 0){   
	$SelectedService= $_POST['SelectedService'] ;	
	$SelectedContractor = $_POST['suppname'];
	$SelectedField = $_POST['SelectedField'];	
	$work=$_POST['work'];
	$operation=$_POST['operation'];	
   /////////////////////////////////////////////////////////////////////////
	DB_query("INSERT INTO farmcrophusbandry(hid, service_Id,
										     suppname,
										     field,
										   work_Done,
										   operation) 
		                            VALUES ('$FarmID','$SelectedService',
											'$SelectedContractor',
										    '$SelectedField',
											'$work',
											'$operation')");
								
	 for($i=0; $i<count($_POST['qty']); $i++){
	 $desc=$_POST['description'][$i];
	 $qty=$_POST['qty'][$i];
	 DB_query("INSERT INTO farmcrophusbandryitems(chid, item_Description,quantity) 
		                            VALUES ('$FarmID','$desc',
											'$qty')");						 
	 }
	 }
	 }
	 ///////////////////////////////////////////
  echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Crop Husbandry') . '" alt="" />' . ' ' . _(    'Crop Husbandry') . '</p>';

 echo'<form action="" name="myForm" method="post" enctype="multipart/form-data" target="_parent">';
 echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
 echo '<table cellpadding="1" width="50%" height="30%">';
 echo' <tr>
  <tr>
  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .   _('Service By:').  '</td>
  <td><select name="SelectedService"  onchange="this.myform.submit">';
  $servicesql = "SELECT service_Id,  
                  service_Name
				  FROM  farmservicesmaintenance
				  ORDER BY service_Id";
				 $result=DB_query($servicesql);
  echo '<option selected="selected" value="">--Select type of  Service--</option>';
  while ($myrow=DB_fetch_array($result)){	
  $Service = htmlspecialchars($myrow['service_Name'],ENT_QUOTES,'UTF-8',false);
		if (isset($_POST['SelectedService']) AND $_POST['SelectedService']==$myrow['service_Id']){
		echo '<option selected="selected" value="' . $myrow['service_Id'] . '">' . $Service . '</option>';		
		} else {
	    echo '<option value="' . $myrow['service_Id'] . '">' .$Service. '</option>';
		}
		}
  echo '</select>
  </tr>'; 
 echo'</table>';
 echo' <br />
	<div class="centre">
	<input name="SelectService" type="submit" value="Select" /></center>';
	echo'</br>';
  if(isset($_POST['SelectedService'])){
   /*******************************************************************************************************************************************************/
   if(isset($_POST['Submit'])){
  
   if (isset($_POST['SelectedField'])){
	$SelectedField = $_POST['SelectedField'];
    }
   if (isset($_POST['suppname'])){
	$SelectedContractor = $_POST['suppname'];
    }
    }
    if (isset($_POST['SelectedField'])){
	$SelectedField = $_POST['SelectedField'];
    }
	if (isset($_POST['SupplieridSelected'])){
	$SelectedContractor = $_POST[$_POST['SupplieridSelected'] .'SuppSelected'];
    }
  
  if(isset($_POST['SearchContractors'])){
	$SQL = "SELECT supplierid,
					suppname,
					currcode,
					address1,
					address2,
					address3,
					address4,
					telephone,
					email,
					url
				FROM suppliers
				ORDER BY suppname";
	
		$rest=DB_query($SQL);
  echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Select Contactor to Assign a service') . '" alt="" />' . '  ' .    _('Select Contactor to Assign service'). '</p>';
	 echo '<table>
   <tr style=font-size:10pt>
    <th><b>ID</b></th>
    <th><b>Contractor</b></th>
	<th><b>Address</b></th>
	<th><b>Telephone No</b></th>
	<th><b>Email</b></th>
	<th></th>
   </tr>';
  ?>
    <style> 
  .odd{background-color: white;} 
  .even{background-color:#CCCCCC;} 
   </style>
  <?php
  $i=0;
  while($row=DB_fetch_array($rest)){
  $i++;
  if($i%2 ==0){$class='even';}else{$class='odd';}
  echo '<tr class=' .$class. ' style=font-size:10pt>
  <td><input name="SupplieridSelected" type="submit" value="'. $row['supplierid'] .'" /></td>
  <td>'.$row['suppname'] .'</td>
  <td>'. $row['address1'] .'</td>
  <td>'. $row['telephone'] .'</td>
  <td>'. $row['email'] .'</td>
	<input name="'. $row['supplierid'] .'SuppSelected" type="hidden" value="'. $row['suppname'] .'" />
  <input name="SelectedField" type="hidden" value="'. $_POST['SelectedField'] .'" />
  <input name="SelectedDescription" type="hidden" value="'. $_POST['SelectedDescription'] .'" />
  </tr>';
  }
  echo '</table>';
  exit;
  }
  
  echo'<table align="center" style="width:40%">';
  if(isset($_POST['SelectedService']) && $_POST['SelectedService']=='CO'){
  echo'<tr>
  <td style="font-size:10pt">Contractor.</td><td><center></center><input type="text" disabled="true"  value="'.$SelectedContractor.'" /><input  type="hidden" size="60"  name="suppname" value="'.$SelectedContractor.'"/><input tabindex="4" type="submit" name="SearchContractors" value="' . _('Search') . '" /></td></tr>';
  
  }
  echo'<tr>
  <tr>
  <td>' .   _('Select Farm Field.').  '</td>
  <td><select name="SelectedField"  onchange="this.myform.submit">';
     $SQL = "SELECT code,
					Field_Name,
					acres					
				    FROM farmfield
				    ORDER BY code";
				 $result=DB_query($SQL);
  echo '<option selected="selected" value="">--Select Service Field--</option>';
  while ($myrow=DB_fetch_array($result)){	
  $Field =  htmlspecialchars($myrow['Field_Name'],ENT_QUOTES,'UTF-8',false);
		if (isset($SelectedField) AND $SelectedField==$myrow['code']){
		echo '<option selected="selected" value="' . $myrow['code'] . '">' . $Field . '</option>';		
		} else {
	    echo '<option value="' . $myrow['code'] . '">' .$Field. '</option>';
		}
		}
  echo '</select>
  </tr>';
  echo'<tr><td style="font-size:10pt">Work Done:</td><td><textarea name="work" rows="1" cols="30"></textarea></td></tr>';
   echo'<tr><td style="font-size:10pt">Operation:</td><td><input type="text" size="35" placeholder="Type of Work" name="operation" /><td>  <center></center></td></tr>';
  echo'</table>';
  
   echo '<p class="page_title_text"><img src="' . $RootPath . '/css/' . $Theme . '/images/customer.png" title="' . _('Select Contactor to Assign a service') . '" alt="" />' . '  ' .    _('Items Descriptions'). '</p>';
   echo '<table id="dataTable" class="selection">';
   echo '<tr>
		<th>' .  _('Item Description')  . '</th>
		<th>' .  _('Quantity'). '</th>
	</tr>';
	echo '<tr>
			
			<td><input type="text" size="50"  maxlength="35" autofocus="autofocus"  class="integer" name="description[]" value="" /></td>
			<td><input type="text" size="20"  maxlength="10" autofocus="autofocus" class="integer" name="qty[]" value="" /></td>
			<td><input onClick="Javacsript:deleteRow(this)" name="" type="button" value="Delete Row" /></td>
		</tr>';
echo '</table>';
	echo '<table style="width:667px;">';	
		echo '<tr><td style="width:270px;"><input onclick=addRow("dataTable") name="Add Row" type="button" value="Add Row" /></td></tr>';
echo '</table>';  
	echo'</table>'; 
    echo'<input name="SubmitForm" type="submit" value="Submit" /></center>';
	 include('includes/footer.inc');
	 }
  ?>
  <SCRIPT language="javascript">
        function addRow(tableID) {

            var table = document.getElementById(tableID);

            var rowCount = table.rows.length;
            var row = table.insertRow(rowCount);

			var cell2 = row.insertCell(0);
            var element2 = document.createElement("input");
            element2.type = "text";
			element2.required = "required";
			element2.size = "50";
            element2.name = "description[]";
            cell2.appendChild(element2);

            var cell3 = row.insertCell(1);
            var element3 = document.createElement("input");
            element3.type = "text";
			element3.required = "required";
			element3.size = "20";
			element3.name = "qty[]";
            cell3.appendChild(element3);
			
			row.insertCell(2).innerHTML= '<input onClick="Javacsript:deleteRow(this)" name="" type="button" value="Delete Row" />';
			 }

        function deleteRow(obj) {
    var index = obj.parentNode.parentNode.rowIndex;
    var table = document.getElementById("dataTable");
    table.deleteRow(index);
    
}

    </SCRIPT>

  