<?php
require_once('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						$statement = " irq_request z 
							INNER JOIN irq_stockrequest a on z.requestid = a.dispatchid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN locations g ON a.loccode = g.loccode
							INNER JOIN www_users y ON z.initiator = y.userid
							WHERE draft=0 AND closed=0 AND b.Sent=0 AND 
							CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' 
							WHEN d.userid ='ISSUE' THEN g.authoriser='".$_SESSION['UserID']."' 
							WHEN d.userid ='PROCURE' THEN g.purchasing_officer='".$_SESSION['UserID']."' 
							ELSE d.userid='".$_SESSION['UserID']."' END";
						$transport = " irq_request z 
							INNER JOIN irq_transport a on a.TransportID = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN www_users y ON z.initiator = y.userid
							WHERE draft=0 AND closed=0 AND b.Sent=0 AND 
							CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' 
							ELSE d.userid='".$_SESSION['UserID']."' END";
						$maintenance = " irq_request z 
							INNER JOIN irq_maintenance a on a.maintenanceid = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN www_users y ON z.initiator = y.userid
							WHERE draft=0 AND closed=0 AND b.Sent=0 AND 
							CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' 
							ELSE d.userid='".$_SESSION['UserID']."' END";
						$gatepass = " irq_request z 
							INNER JOIN irq_gatepass a on a.gatepassid = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN www_users y ON z.initiator = y.userid
							WHERE draft=0 AND closed=0 AND b.Sent=0 AND
							CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' 
							ELSE d.userid='".$_SESSION['UserID']."' END";
  
						$results = "SELECT requestid FROM {$statement} GROUP BY requestid";
						$results2 = "SELECT requestid FROM {$transport} GROUP BY requestid";
						$results3 = "SELECT requestid FROM {$maintenance} GROUP BY requestid";
						$results4 = "SELECT requestid FROM {$gatepass} GROUP BY requestid";
						$sqlquery = $results." UNION ALL ".$results2." UNION ALL ".$results3." UNION ALL ".$results4."";
						$query = "SELECT COUNT(*) as `num` FROM ({$sqlquery}) t";
						$welc = DB_query($query,$ErrMsg,$DbgMsg);
						$num = DB_fetch_array($welc);
						if($num['num']>0){
						$inbox = '<span class="label2 label-danger pull-right">'.$num['num'].' New</span>';
						}else{
						$inbox ="";
						}
		echo $inbox;

?>
