<?php
require_once('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

$sql="SELECT count(*) FROM purchorder_control WHERE fy = '".Date('Y-m-d',YearEndDate($_SESSION['YearEnd'],0))."' AND type='".$_GET['type']."' AND '".$_GET['id']."' BETWEEN order_from AND order_to";
$result=DB_query($sql);
$myrow = DB_fetch_row($result);
if ( $myrow[0] < 1 ) {
  echo "<script>document.getElementById('PONo').style.backgroundColor = 'red'; 
  alert('The Order Number you entered is not within the Order Number Range set for this Financial Year. Please check the number and try again or contact System Administrator for assistant.');
  document.getElementById('PONo').value = ''; </script>" ; 
}else{
 echo "<script>document.getElementById('PONo').style.backgroundColor = '';</script>" ; 
}
?>