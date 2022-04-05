<?php

include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
include('config.php');

$sql = "select emp_id from employee";
    $result = DB_query($sql);

    $list = array();
    while($row = DB_fetch_array($result))
    {
        $list[] = $row['emp_id'];
    }
    echo json_encode($list);
	
	?>