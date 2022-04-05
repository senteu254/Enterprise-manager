<?php

include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
include('config.php');

$description=$_GET["term"];

 
 $query=DB_query("SELECT * FROM stockmaster where longdescription like '%".$description."%' order by longdescription ");
 $json=array();
 
    while($row=DB_fetch_array($query)){
         $json[]=$row['longdescription'];
    }
 
 echo json_encode($json);
 
?>