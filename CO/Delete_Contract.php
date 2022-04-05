<?php

if(empty($_GET['id'])){
	$_SESSION['err_msg']='
					<p><strong>No Contract was selected!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:".$mainlink."Contract");
					exit;
}
if(!is_numeric($_GET['id'])){
session_start();
	$_SESSION['err_msg']='
					<p><strong>Invalid ID!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:".$mainlink."Contract");
					exit;
}
$rowCount = count($_GET["id"]);
for($i=0;$i<$rowCount;$i++) {
$result=DB_query("DELETE FROM contract_details WHERE ContractID='" .$_GET["id"][$i]. "'");
}
if($result==NULL){
session_start();
	$_SESSION['err_msg']='
					<p><strong>Contract Cannot deleted because this Contract have been assigned to supplier </strong></p>
					<a href="#" class="close">close</a>';
					header("Location:".$mainlink."Contract");
					exit;
}else{
session_start();
	$_SESSION['msg']='
					<p><strong>Contract Deleted Successfully!</strong></p>
					<a href="#" class="close">close</a>';
					header("Location:".$mainlink."Contract");
					exit;
	}
?>



