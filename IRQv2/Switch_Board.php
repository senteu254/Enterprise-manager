<?php 

include('includes/SQL_CommonFunctions.inc');
	$mainlink = "index.php?Application=IRQ2&Link=";
function calculate_time_span($date){
    $seconds  = strtotime(date('Y-m-d H:i:s')) - strtotime($date);

        $months = floor($seconds / (3600*24*30));
        $day = floor($seconds / (3600*24));
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds - ($hours*3600)) / 60);
        $secs = floor($seconds % 60);

        if($seconds < 60)
            $time = $secs." seconds ago";
        else if($seconds < 60*60 )
            $time = $mins." min ago";
        else if($seconds < 24*60*60)
            $time = $hours." hours ago";
        else if($seconds < 24*60*60)
            $time = $day." day ago";
        else
            //$time = $months." month ago";
			$time = date("M d, Y ",strtotime($date));

        return $time;
}
				$path='IRQv2/';
				$view = (isset($_GET['Link']) && $_GET['Link'] != '') ? $_GET['Link'] : '';
								switch ($view) {
								case 'Inbox' :
									$content=$path.'Mail_Inbox.php';	
									$head='Inbox';
									break;
									case 'InboxRead' :
										$content=$path.'Mail_Inbox_Read.php';	
										$head='Read Inbox';
										break;
									case 'InboxReadTransport' :
										$content=$path.'Mail_Inbox_Read_Transport.php';	
										$head='Read Inbox';
										break;
									case 'InboxReadMaintenance' :
										$content=$path.'Mail_Inbox_Read_Maintenance.php';	
										$head='Read Inbox';
										break;
									case 'InboxReadGatePass' :
										$content=$path.'Mail_Inbox_Read_GatePass.php';	
										$head='Read Inbox';
										break;
									
								case 'Trash' :
									$content=$path.'Mail_Trash.php';	
									$head='Rejected';
									break;
									case 'TrashRead' :
										$content=$path.'Mail_Trash_Read.php';	
										$head='Read Rejected';
										break;
									case 'TrashReadTransport' :
										$content=$path.'Mail_Read_Transport.php';	
										$head='Read Rejected';
										break;
									case 'TrashReadMaintenance' :
										$content=$path.'Mail_Read_Maintenance.php';	
										$head='Read Rejected';
										break;
									case 'TrashReadGatePass' :
										$content=$path.'Mail_Read_GatePass.php';	
										$head='Read Rejected';
										break;
								
								case 'Approved' :
									$content=$path.'Mail_Approved.php';	
									$head='Approved';
									break;
									case 'ApprovedRead' :
										$content=$path.'Mail_Approved_Read.php';	
										$head='Read Approved';
										break;
									case 'ApprovedReadTransport' :
										$content=$path.'Mail_Read_Transport.php';	
										$head='Read Approved';
										break;
									case 'ApprovedReadMaintenance' :
										$content=$path.'Mail_Read_Maintenance.php';	
										$head='Read Approved';
										break;
									case 'ApprovedReadGatePass' :
										$content=$path.'Mail_Read_GatePass.php';	
										$head='Read Approved';
										break;
									
								case 'Draft' :
									$content=$path.'Mail_Draft.php';	
									$head='Draft Request';
									break;
									case 'DraftRead' :
										$content=$path.'Mail_Draft_Read.php';	
										$head='Read Draft';
										break;
									case 'DraftReadTransport' :
										$content=$path.'Mail_Draft_Read_Transport.php';	
										$head='Read Draft';
										break;
									case 'DraftReadMaintenance' :
										$content=$path.'Mail_Draft_Read_Maintenance.php';	
										$head='Read Draft';
										break;
									case 'DraftReadGatePass' :
										$content=$path.'Mail_Draft_Read_GatePass.php';	
										$head='Read Draft';
										break;
								
								case 'Sent' :
									$content=$path.'Mail_Sent.php';	
									$head='Sent';
									break;	
									case 'SentRead' :
										$content=$path.'Mail_Sent_Read.php';	
										$head='Read Sent';
										break;
									case 'SentReadTransport' :
										$content=$path.'Mail_Read_Transport.php';	
										$head='Read Sent';
										break;
									case 'SentReadMaintenance' :
										$content=$path.'Mail_Read_Maintenance.php';	
										$head='Read Sent';
										break;
									case 'SentReadGatePass' :
										$content=$path.'Mail_Read_GatePass.php';	
										$head='Read Sent';
										break;
									
								case 'Forwarded' :
									$content=$path.'Mail_Forwarded.php';	
									$head='Forwarded';
									break;
									case 'ForwardedRead' :
										$content=$path.'Mail_Forwarded_Read.php';	
										$head='Read Forwarded';
										break;
									case 'ForwardedReadTransport' :
										$content=$path.'Mail_Read_Transport.php';	
										$head='Read Forwarded';
										break;
									case 'ForwardedReadMaintenance' :
										$content=$path.'Mail_Read_Maintenance.php';	
										$head='Read Forwarded';
										break;
									case 'ForwardedReadGatePass' :
										$content=$path.'Mail_Read_GatePass.php';	
										$head='Read Forwarded';
										break;
									
								case 'PurchaseRequest' :
									$content=$path.'Create_Stock_Request.php';	
									$head='Compose New Purchase Request';
									break;
									
								case 'StoresRequest' :
									$content=$path.'Create_Stock_Request.php';	
									$head='Compose New Stores Request';
									break;
									
								case 'TransportRequest' :
									$content=$path.'Create_Transport_Request.php';	
									$head='Compose New Transport Request';
									break;
									
								case 'MaintenanceRequest' :
									$content=$path.'Create_Maintenance_Request.php';	
									$head='Compose Maintenance Request';
									break;
									
								case 'GatePassRequest' :
									$content=$path.'Create_Gatepass_Request.php';	
									$head='Compose New Gate Pass Request';
									break;
									
								case 'ManageTasks' :
									$content=$path.'LevelManager.php';
									$head="Manage Approval Levels";
									break;
									
									default :
									$content =$path.'Mail_Inbox.php';
									$head='Inbox';
									$_GET['Link']="Inbox";
									break;
							}
							

function pagination_inbox($query,$per_page=10,$page=1,$url='?'){   
    global $db; 
    $query = "SELECT COUNT(*) as `num` FROM ({$query}) t";
    $row = mysqli_fetch_array(mysqli_query($db,$query));
    $total = $row['num'];
    $adjacents = "2"; 
      
    $prevlabel = '<button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>';
    $nextlabel = '<button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>';
    $lastlabel = "Last &rsaquo;&rsaquo;";
      
    $page = ($page == 0 ? 1 : $page);  
    $start = ($page - 1) * $per_page;                               
      
    $prev = $page - 1;                          
    $next = $page + 1;
      
    $lastpage = ceil($total/$per_page);
      
    $lpm1 = $lastpage - 1; // //last page minus 1
      
    $pagination = "";
    if($lastpage > 1){   
        //$pagination .= "<ul class='pagination'>";
        $pagination .= "Page {$page} of {$lastpage}&nbsp;&nbsp;";
              
            if ($page > 1) $pagination.= "<a href='{$url}page={$prev}'>{$prevlabel}</a>";
			if ($page != $lastpage && $page < $lastpage) $pagination.= "<a href='{$url}page={$next}'>{$nextlabel}</a>";
            //$pagination.= "<a href='{$url}page={$next}'>{$nextlabel}</a>"; 
			      
    }
      
    return $pagination;
}

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
							WHERE draft=0 AND closed=0 AND b.Sent=0 AND b.Unread=0 AND 
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
							WHERE draft=0 AND closed=0 AND b.Sent=0 AND b.Unread=0 AND 
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
							WHERE draft=0 AND closed=0 AND b.Sent=0 AND b.Unread=0 AND 
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
							WHERE draft=0 AND closed=0 AND b.Sent=0 AND b.Unread=0 AND
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

echo '<link href="' . $RootPath . '/css/'. $_SESSION['Theme'] .'/default.css" rel="stylesheet" type="text/css" />';
echo '<script type="text/javascript" src = "js/jquery-1.9.1.js"></script>';
?>	
<link rel="stylesheet" href="IRQv2/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="IRQv2/iCheck/flat/blue.css">
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
 <script src="bootstrap/js/bootstrap.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
 <script src="bootstrap/js/jquery.min.js"></script>

<div class="container-fluid">
<div class = "row">
<div class="col-md-3 col-md-offset-">
<ul class="navbar-nav" style="width:100%;" >
<li class="dropdown" style="width:100%;" >
	<a href="#" class="dropdown-toggle" data-toggle="dropdown"><button style="width:100%;" class="btn btn-primary"><i class="fa fa-pencil-square-o"></i> Compose Requisition <span class="caret"></span></button></a>
		<ul class="dropdown-menu" role="menu">
			<?php 
			if ((in_array(86, $_SESSION['AllowedPageSecurityTokens']) OR !isset($PageSecurity))) {
			echo '<li><a href="'.$mainlink.'StoresRequest&New=Yes">Store Requisition and Issue Voucher</a></li>';
			}
			if ((in_array(39, $_SESSION['AllowedPageSecurityTokens']) OR !isset($PageSecurity))) {
			echo '<li><a href="'.$mainlink.'PurchaseRequest&New=Yes">Request for Purchase or Service</a></li>';
			}
			if ((in_array(38, $_SESSION['AllowedPageSecurityTokens']) OR !isset($PageSecurity))) {
			echo '<li><a href="'.$mainlink.'TransportRequest">Transport Requisition</a></li>';
			}
			if ((in_array(40, $_SESSION['AllowedPageSecurityTokens']) OR !isset($PageSecurity))) {
			echo '<li><a href="'.$mainlink.'MaintenanceRequest">Maintenance Requisition</a></li>';
			}
			if ((in_array(41, $_SESSION['AllowedPageSecurityTokens']) OR !isset($PageSecurity))) {
			echo '<li><a href="'.$mainlink.'GatePassRequest">Gate Pass Requisition</a></li>';
			}
			?>
		</ul>
</li>
</ul>
<br></br><p></p>
	<div class="panel panel-default">
		<div class="panel-heading">Folders</div>
			<div class="panel-body">
			
			<ul class="nav nav-pills nav-stacked">
                <li <?php echo (($_GET['Link']=="Inbox" || $_GET['Link']=="InboxRead" || $_GET['Link']=="InboxReadTransport" || $_GET['Link']=="InboxReadMaintenance" || $_GET['Link']=="InboxReadGatePass") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>Inbox"><i class="fa fa-inbox"></i> Inbox <?php echo '<span class="num" id="num">'.$inbox.'</span>'; ?></a></li>
				<li <?php echo (($_GET['Link']=="Sent" || $_GET['Link']=="SentRead" || $_GET['Link']=="SentReadTransport" || $_GET['Link']=="SentReadMaintenance" || $_GET['Link']=="SentReadGatePass") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>Sent"><i class="fa fa-envelope-o"></i> Sent </a></li>
                <li <?php echo (($_GET['Link']=="Draft" || $_GET['Link']=="DraftRead" || $_GET['Link']=="DraftReadTransport" || $_GET['Link']=="DraftReadMaintenance" || $_GET['Link']=="DraftReadGatePass") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>Draft"><i class="fa fa-file-text-o"></i> Drafts </a></li>
                <li <?php echo (($_GET['Link']=="Approved" || $_GET['Link']=="ApprovedRead" || $_GET['Link']=="ApprovedReadTransport" || $_GET['Link']=="ApprovedReadMaintenance" || $_GET['Link']=="ApprovedReadGatePass") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>Approved"><i class="fa fa-filter"></i> Approved <span class="pull-right"><i class="fa fa-star text-warning"></i></span></a></li>
				<li <?php echo (($_GET['Link']=="Forwarded" || $_GET['Link']=="ForwardedRead" || $_GET['Link']=="ForwardedReadTransport" || $_GET['Link']=="ForwardedReadMaintenance" || $_GET['Link']=="ForwardedReadGatePass") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>Forwarded"><i class="fa fa-share"></i> Forwarded </a></li>
				<li <?php echo ($_GET['Link']=="Trash"  || $_GET['Link']=="TrashRead" || $_GET['Link']=="TrashReadTransport" || $_GET['Link']=="TrashReadMaintenance" || $_GET['Link']=="TrashReadGatePass" ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>Trash"><i class="fa fa-trash-o"></i> Rejected</a></li>
				<?php if($_SESSION['CanEditFlow'] == 1){ ?>
				<li <?php echo ($_GET['Link']=="ManageTasks" ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>ManageTasks&Tab=1"><i class="fa fa-gear"></i> Settings</a></li>
				<?php } ?>
              </ul>
			</div>
		</div>
	</div>	
	<div id="printarea">
	<div class="col-md-9 col-md-offset-">
<?php error_reporting( error_reporting() & ~E_NOTICE ); if(!empty($_SESSION['msg'])) echo '<div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Success</h4>
                ' . ucwords($_SESSION['msg']). '
              </div>'; unset($_SESSION['msg']); 
			 if(!empty($_SESSION['errmsg'])) echo '<div class="alert alert-warning alert-dismissible">
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>
                ' . ucwords($_SESSION['errmsg']). '
              </div>'; unset($_SESSION['errmsg']); ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo $head; ?></div>
			<div class="panel-body">
			<!--/*----------------------------------------------------------------------------*/-->
			
			<?php include $content; ?>

			<!-------------------------------------------------------------------------------------->

			</div>
		</div>
	</div>
	</div> <!--end of printable area-->
</div>
<?php
include ('includes/footer.inc');
?>

<script type="text/javascript" src="HR_Head/iCheck/jquery-2.2.3.min.js"></script>
<script src="HR_Head/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    //Enable iCheck plugin for checkboxes
    //iCheck for checkbox and radio inputs
    $('.mailbox-messages input[type="checkbox"]').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      radioClass: 'iradio_flat-blue'
    });

    //Enable check and uncheck all functionality
    $(".checkbox-toggle").click(function () {
      var clicks = $(this).data('clicks');
      if (clicks) {
        //Uncheck all checkboxes
        $(".mailbox-messages input[type='checkbox']").iCheck("uncheck");
        $(".fa", this).removeClass("fa-check-square-o").addClass('fa-square-o');
      } else {
        //Check all checkboxes
        $(".mailbox-messages input[type='checkbox']").iCheck("check");
        $(".fa", this).removeClass("fa-square-o").addClass('fa-check-square-o');
      }
      $(this).data("clicks", !clicks);
    });

    //Handle starring for glyphicon and font awesome
    $(".mailbox-star").click(function (e) {
      e.preventDefault();
      //detect type
      var $this = $(this).find("a > i");
      var glyph = $this.hasClass("glyphicon");
      var fa = $this.hasClass("fa");

      //Switch states
      if (glyph) {
        $this.toggleClass("glyphicon-star");
        $this.toggleClass("glyphicon-star-empty");
      }

      if (fa) {
        $this.toggleClass("fa-star");
        $this.toggleClass("fa-star-o");
      }
    });
  });
</script>
 <script type="text/javascript">

/*--This JavaScript method for Print command--*/

    function PrintDoc() {

        var toPrint = document.getElementById('printarea');

        var popupWin = window.open('', '_blank', 'width=800,height=700,location=no,left=5px');

        popupWin.document.open();

        popupWin.document.write('<html><title>::Print Preview::</title><link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css" media="screen"/><link rel="stylesheet" type="text/css" href="HR_Head/font-awesome/css/font-awesome.min.css" media="screen"/><?php echo '<link href="' . $RootPath . '/css/'. $_SESSION['Theme'] .'/default.css" rel="stylesheet" type="text/css" />'; ?><style type="text/css"> .box-footer{ display:none;}</style></head><body onload="window.print()">')

        popupWin.document.write(toPrint.innerHTML);

        popupWin.document.write('</html>');

        popupWin.document.close();

    }

/*--This JavaScript method for Print Preview command--*/

    function PrintPreview() {

        var toPrint = document.getElementById('printarea');

        var popupWin = window.open('', '_blank', 'width=700,height=500,location=no,left=200px');

        popupWin.document.open();

        popupWin.document.write('<html><title>::Print Preview::</title><link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css" media="screen"/><link rel="stylesheet" type="text/css" href="HR_Head/font-awesome/css/font-awesome.min.css" media="screen"/><?php echo '<link href="' . $RootPath . '/css/'. $_SESSION['Theme'] .'/default.css" rel="stylesheet" type="text/css" />'; ?><style type="text/css"> .box-footer{ display:none;}</style></head><body">')

        popupWin.document.write(toPrint.innerHTML);

        popupWin.document.write('</html>');

        popupWin.document.close();

    }

</script>
		<script type="text/javascript">
			function pop(div) {
				document.getElementById(div).style.display = 'block';
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
<style>
#loadingbackground{
	display:none; 	
    opacity: 0.8;
    position: fixed;
    top: 0;
    left: 0;
    background: #fff;
    width: 100%;
    height: 100%;
}

#progressBar{
    width: 300px;
    height: 150px;
    background-color: #fff;
    border: 5px solid #1468b3;
    text-align: center;
    color: #202020;
    position: absolute;
    left: 50%;
    top: 50%;
    margin-left: -150px;
    margin-top: -100px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    behavior: url("/css/pie/PIE.htc"); /* HANDLES IE */
}
		.navbar-default{
		opacity: .9;
		z-index: 999;
		}
		.dropdown:hover .dropdown-menu {
		display: block;
		}
		.panel-default{
		opacity: .9;
		}
		hr.message-inner-separator
{
    clear: both;
    margin-top: 10px;
    margin-bottom: 13px;
    border: 0;
    height: 1px;
    background-image: -webkit-linear-gradient(left,rgba(0, 0, 0, 0),rgba(0, 0, 0, 0.15),rgba(0, 0, 0, 0));
    background-image: -moz-linear-gradient(left,rgba(0,0,0,0),rgba(0,0,0,0.15),rgba(0,0,0,0));
    background-image: -ms-linear-gradient(left,rgba(0,0,0,0),rgba(0,0,0,0.15),rgba(0,0,0,0));
    background-image: -o-linear-gradient(left,rgba(0,0,0,0),rgba(0,0,0,0.15),rgba(0,0,0,0));
}

</style>
<style type="text/css">

#form-control {
  
  width: 100%;
  height: 34px;
  padding: 6px 12px;
  font-size: 14px;
  line-height: 1.42857143;
  color: #555;
  background-color: #fff;
  background-image: none;
  border: 1px solid #ccc;
  border-radius: 4px;
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
          box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
  -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
       -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
          transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
}
#form-control:focus {
  border-color: #66afe9;
  outline: 0;
  -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
          box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
}
#form-control::-moz-placeholder {
  color: #999;
  opacity: 1;
}
#form-control:-ms-input-placeholder {
  color: #999;
}
#form-control::-webkit-input-placeholder {
  color: #999;
}
#form-control[disabled],
#form-control[readonly],
fieldset[disabled] .form-control {
  cursor: not-allowed;
  background-color: #eee;
  opacity: 1;
}
textarea.form-control {
  height: auto;
}
.label2 {
  display: inline;
  padding: .2em .6em .3em;
  font-size: 75%;
  font-weight: bold;
  line-height: 1;
  color: #fff;
  text-align: center;
  white-space: nowrap;
  vertical-align: baseline;
  border-radius: .25em;
}

</style>
