<?php
if(!isset($_SESSION['UserID']) && !isset($_SESSION['AccessLevel']) && !isset($_SESSION['UsersRealName']) && !isset($_SESSION['DatabaseName'])){
header('location:../index.php');
}
$docid = (isset($_SESSION['Document_id']) && $_SESSION['Document_id'] != '') ? $_SESSION['Document_id'] : '';
								switch ($docid) {
								case '1' :
									$link='IRQ_PurchaseOrService.php';
									$complete='Completed_Content.php';
									$inbox='Inbox_Content.php';
									$draft='Draft_Content.php';
									break;
							
								case '2' :
									$link='IRQ_TransportRequest.php';
									$complete='Completed_Transport_Content.php';
									$inbox='Inbox_Transport_Content.php';
									$draft='Draft_Transport_Content.php';
									break;
									
								case '5' :
									$link='IRQ_MaintenanceRequest.php';
									$complete='Completed_Maintenance_Content.php';
									$inbox='Inbox_Maintenance_Content.php';
									$draft='Draft_Maintenance_Content.php';
									break;
									
								case '7' :
									$link='IRQ_GatepassRequest.php';
									$complete='Completed_Gatepass_Content.php';
									$inbox='Inbox_Gatepass_Content.php';
									$draft='Draft_Gatepass_Content.php';
									break;
				
							}
							
?>