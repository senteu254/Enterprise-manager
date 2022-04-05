<script type="text/javascript" src="js/qsearch.js"></script>
<script type="text/javascript" src="js/webcam.js"></script>
			
			<?php
			if (isset($_GET['SelectedSampleID'])){
	$SelectedSampleID =mb_strtoupper($_GET['SelectedSampleID']);
} elseif(isset($_POST['SelectedSampleID'])){
	$SelectedSampleID =mb_strtoupper($_POST['SelectedSampleID']);
}

if (isset($_GET['SelectedStockItem'])) {
	$SelectedStockItem = $_GET['SelectedStockItem'];
} elseif (isset($_POST['SelectedStockItem'])) {
	$SelectedStockItem = $_POST['SelectedStockItem'];
}
if (isset($_GET['LotNumber'])) {
	$LotNumber = $_GET['LotNumber'];
} elseif (isset($_POST['LotNumber'])) {
	$LotNumber = $_POST['LotNumber'];
}
if (isset($_GET['SampleID'])) {
	$SampleID = $_GET['SampleID'];
} elseif (isset($_POST['SampleID'])) {
	$SampleID = $_POST['SampleID'];
}

if (isset($Errors)) {
	unset($Errors);
}

$Errors = array();


if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */
	$i=1;

	if (isset($SelectedSampleID) AND $InputError !=1) {
		$sql = "UPDATE qasamples SET identifier='" . $_POST['Identifier'] . "',
									comments='" . $_POST['Comments'] . "',
									LotKey='" . $_POST['LotKey'] . "',
									Batch='" . $_POST['Batch'] . "',
									SampleSize='" . $_POST['SampleSize'] . "',
									sampledate='" . FormatDateForSQL($_POST['SampleDate']) . "',
									ProductionDate='" . $_POST['ProductionDate'] . "',
									cert='1'
				WHERE sampleid = '" . $SelectedSampleID . "'";

		$_SESSION['msg'] = _('QA Sample record for') . ' ' . $SelectedSampleID  . ' ' .  _('has been updated');
		$ErrMsg = _('The update of the QA Sample failed because');
		$DbgMsg = _('The SQL that was used and failed was');
		$result = DB_query($sql,$ErrMsg, $DbgMsg);
		//prnMsg($msg , 'success');
		$redirect = htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&Link=AQL';

	} else {
		CreateQASample($_POST['ProdSpecKey'],$_POST['LotKey'], $_POST['Identifier'], $_POST['Comments'], 1, 1,$_POST['Batch'],$_POST['SampleSize'],$_POST['ProductionDate'],$db);
		$SelectedSampleID=DB_Last_Insert_ID($db,'qasamples','sampleid');
		if ($SelectedSampleID > '') {
		$rest = DB_query("SELECT MAX(sampleid) FROM qasamples");
		$rowid = DB_fetch_row($rest);
			$_SESSION['msg'] = _('Created New Sample Sample ID'.$rowid[0]);
			//prnMsg($msg , 'success');
			$redirect = htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&Link=AQLReadTest&SelectedSampleID='.$rowid[0];
		}
	}
	unset($SelectedSampleID);
	unset($_POST['ProdSpecKey']);
	unset($_POST['LotKey']);
	unset($_POST['Identifier']);
	unset($_POST['Comments']);
	unset($_POST['ProductionDate']);
	unset($_POST['Batch']);
	unset($_POST['SampleSize']);
		 echo "
            <script type=\"text/javascript\">
				window.location.href = '".$redirect."';
            </script>
        ";
}

echo '<div id="loadingbackground">';
		echo '<div id="progressBar" ><br />
			 <i class="fa fa-spinner fa-pulse fa-4x fa-fw"></i><br>PROCESSING. PLEASE WAIT...
			</div>';
		echo '</div>';
		


if (isset($SelectedSampleID)) {
	echo '<center><a class="btn btn-default" href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&Link=AQL">' . _('Show All Samples') . '</a></center>';

}

if (! isset($_GET['delete'])) {

	echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?Application=QA&Link=NewAQL">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	if (isset($SelectedSampleID)) {

		$sql = "SELECT prodspeckey,
						lotkey,
						identifier,
						comments,
						cert,
						sampledate,
						ProductionDate,
						Batch,
						SampleSize
				FROM qasamples
				WHERE sampleid='".$SelectedSampleID."'";

		$result = DB_query($sql);
		$myrow = DB_fetch_array($result);

		$_POST['ProdSpecKey'] = $myrow['prodspeckey'];
		$_POST['LotKey'] = $myrow['lotkey'];
		$_POST['Identifier'] = $myrow['identifier'];
		$_POST['Comments'] = $myrow['comments'];
		$_POST['SampleDate'] = ConvertSQLDate($myrow['sampledate']);
		$_POST['SampleSize'] = $myrow['SampleSize'];
		$_POST['Batch'] = $myrow['Batch'];
		$_POST['ProductionDate'] = $myrow['ProductionDate'];

		echo '<input type="hidden" name="SelectedSampleID" value="' . $SelectedSampleID . '" />';
		echo '<table class="table">
				<tr>
					<td>' . _('Sample ID') . ':</td>
					<td>' . str_pad($SelectedSampleID,10,'0',STR_PAD_LEFT)  . '</td>
				</tr>';

		echo '<tr>
				<td>' . _('Specification') . ':</td>
				<td>' . $_POST['ProdSpecKey']. '</td>
			</tr>
			<tr>
				<td>' . _('Lot') . ':</td>
				<td><input type="text" name="LotKey" size="20" maxlength="20" value="' . $_POST['LotKey']. '" /></td>
			</tr>
			<tr>
				<td>' . _('Batch Size') . ':</td>
				<td><input type="text" name="Batch" size="20" maxlength="20" value="' . $_POST['Batch']. '" /></td>
			</tr>
			<tr>
				<td>' . _('Sample Size') . ':</td>
				<td><input type="text" name="SampleSize" size="20" maxlength="20" value="' . $_POST['SampleSize']. '" /></td>
			</tr>
			<tr>
				<td>' . _('Identifier') . ':</td>
				<td><input type="text" name="Identifier" size="15" maxlength="15" value="' . $_POST['Identifier']. '" /></td>
			</tr>
			<tr>
				<td>' . _('Comments') . ':</td>
				<td><input type="text" name="Comments" size="30" maxlength="255" value="' . $_POST['Comments']. '" /></td>
			</tr>
			<tr>
				<td>' . _('Sample Date') . ':</td>
				<td><input class="date" type="text" name="SampleDate" size="10" maxlength="10" value="' . $_POST['SampleDate']. '" /></td>
			</tr>
			<tr>
				<td>' . _('Production Date') . ':</td>
				<td><input type="text" name="ProductionDate" size="30" maxlength="20" value="' . $_POST['ProductionDate']. '" /></td>
			</tr>
			</table>
			<br />
			<div class="centre">
				<input type="submit" name="submit" value="' . _('Enter Information') . '" />
			</div>
			</div>
			</form>';

	} else { //end of if $SelectedSampleID only do the else when a new record is being entered

		echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
		echo '<div>';
		echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
		echo '<table class="table">';
		$SQLSpecSelect="SELECT DISTINCT(keyval),
								description
							FROM prodspecs LEFT OUTER JOIN stockmaster
							ON stockmaster.stockid=prodspecs.keyval";

		$ResultSelection=DB_query($SQLSpecSelect);
		echo '<tr>
				<td>' . _('Specification') . ':</td>';
		echo '<td><select name="ProdSpecKey">';
		while ($MyRowSelection=DB_fetch_array($ResultSelection)){
			echo '<option value="' . $MyRowSelection['keyval'] . '">' . $MyRowSelection['keyval'].' - ' .htmlspecialchars($MyRowSelection['description'], ENT_QUOTES,'UTF-8', false)  . '</option>';
		}
		echo '</select></td>
			</tr>
			<tr>
				<td>' . _('Lot') . ':</td>
				<td><input type="text" required="required" name="LotKey" size="15" maxlength="15" value="' . (isset($_POST['LotKey'])? $_POST['LotKey']:'') . '" /></td>
			</tr>
			<tr>
				<td>' . _('Batch Size') . ':</td>
				<td><input type="text" name="Batch" size="15" maxlength="15" value="' . (isset($_POST['Batch'])? $_POST['Batch']:'') . '" /></td>
			</tr>
			<tr>
				<td>' . _('Sample Size') . ':</td>
				<td><input type="text" name="SampleSize" size="15" maxlength="15" value="' . (isset($_POST['SampleSize'])? $_POST['SampleSize']:'') . '" /></td>
			</tr>
			<tr>
				<td>' . _('Identifier') . ':</td>
				<td><input type="text" name="Identifier" size="15" maxlength="15" value="' . (isset($_POST['Identifier'])? $_POST['Identifier']:'') . '" /></td>
			</tr>
			<tr>
				<td>' . _('Production Date') . ':</td>
				<td><input type="text" name="ProductionDate" size="30" maxlength="20" value="' . (isset($_POST['ProductionDate'])? $_POST['ProductionDate']:date($_SESSION['DefaultDateFormat'])) . '" /></td>
			</tr>
			<tr>
				<td>' . _('Comments') . ':</td>
				<td><input type="text" name="Comments" size="30" maxlength="255" value="' . (isset($_POST['Comments'])? $_POST['Comments']:'') . '" /></td>
			</tr>';
		echo '</table>
			<br />
			<div class="centre">
				<input type="submit" name="submit" value="' . _('Enter Information') . '" />
			</div>
			</div>
			</form>';
	}
} //end if record deleted no point displaying form to add record
			?>
	  
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
		<script language="JavaScript">
		function take_snapshot() {
			// take snapshot and get image data
			Webcam.snap( function(data_uri) {
				// display results in page
			var ids = document.getElementById('vid').value;
			var imgpt = $("#imagepath").val();
				document.getElementById('results').innerHTML = 
					'Processing...';
					
				Webcam.upload( data_uri, 'Sec_updateimage.php?VID='+ids+'&IMGPT='+imgpt, function(code, text) {
					document.getElementById('results').innerHTML = 
					'<img style="border-radius:8px 8px 8px 8px;" height="135px" width="120px" src="SECv2/'+text+'"/>';
					document.getElementById('imagepath').value = text;
				} );	
			} );
			document.getElementById('showweb').style.display = 'none';
		}
	</script>

<style type="text/css">
#results {height:135px; width:120px; border-radius:8px 8px 8px 8px; background:#ccc;}
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