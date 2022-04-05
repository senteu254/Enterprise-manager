
<?php
include('includes/session.inc');

$Title = _('Individual Medical History');

$ViewTopic = 'Individual Medical History';
$BookMark = 'IndividualMedicalHistory';

include('includes/header.inc');

echo '<p class="page_title_text"><img src="'.$RootPath.'/css/'.$Theme.'/images/group_add.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p><br />';



$ErrMsg = _('An error occurred in retrieving the records');
$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');	
$sql = "SELECT  *,employee.emp_id as id FROM employee,medical
WHERE  employee.emp_id=medical.personal_no
";$result = DB_query($sql,$ErrMsg,$DbgMsg);

while($myrow=DB_fetch_array($result)){
$allList[$myrow['id']][] = $myrow;
}				
?>

<table border="0">
	<tr height="20">
	<th colspan="2" align="center"><strong>Medical History</strong></th></tr>
  <tr height="30">
    <th width="300">Employee Details</th>
    <th width="300">Action</th>
  </tr>			<!-- Text input-->
			<?php 
			foreach ($allList as $all => $List)
{
	 foreach($List as $itemInfo)
    { 
	 echo '<tr  height="30">
    <td>'. $itemInfo['id'].'-'.$itemInfo['emp_fname'].' '.$itemInfo['emp_lname'].'</td>
	';
	?>
    <td><a href="PDFMEDReportPortrait.php?id=<?php echo $itemInfo['id'];?>">Click Here to Print</a></td>
  </tr>
  <?php
			
	}
}
?>
	</table>
	<?php
	include('includes/footer.inc');
	?>