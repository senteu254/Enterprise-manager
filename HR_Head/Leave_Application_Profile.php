<link rel="stylesheet" href="HR_Head/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="HR_Head/iCheck/flat/blue.css">
<?php	
echo '<link href="' . $RootPath . '/css/'. $_SESSION['Theme'] .'/default.css" rel="stylesheet" type="text/css" />';
echo '<script type="text/javascript" src = "js/jquery-1.9.1.js"></script>';
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
				$path='HR_Head/';
				$view = (isset($_GET['Link']) && $_GET['Link'] != '') ? $_GET['Link'] : '';
								switch ($view) {
								case 'LeaveInbox' :
									$content=$path.'Mail_Leave_Inbox.php';	
									$head='Inbox';
									break;
									case 'LeaveInboxRead' :
										$content=$path.'Mail_Leave_Inbox_Read.php';	
										$head='Read Inbox';
										break;
									
								case 'LeaveTrash' :
									$content=$path.'Mail_Leave_Trash.php';	
									$head='Rejected';
									break;
									case 'LeaveTrashRead' :
										$content=$path.'Mail_Leave_Trash_Read.php';	
										$head='Read Rejected';
										break;
								
								case 'LeaveApproved' :
									$content=$path.'Mail_Leave_Approved.php';	
									$head='Approved';
									break;
									case 'LeaveApprovedRead' :
										$content=$path.'Mail_Leave_Approved_Read.php';	
										$head='Read Approved';
										break;
									
								case 'LeaveDraft' :
									$content=$path.'Mail_Leave_Draft.php';	
									$head='Draft';
									break;
									case 'LeaveDraftRead' :
										$content=$path.'Mail_Leave_Draft_Read.php';	
										$head='Read Draft';
										break;
								
								case 'LeaveSent' :
									$content=$path.'Mail_Leave_Sent.php';	
									$head='Sent';
									break;	
									case 'LeaveSentRead' :
										$content=$path.'Mail_Leave_Sent_Read.php';	
										$head='Read Sent';
										break;
									
								case 'LeaveForwarded' :
									$content=$path.'Mail_Leave_Forwarded.php';	
									$head='Forwarded';
									break;
									case 'LeaveForwardedRead' :
										$content=$path.'Mail_Leave_Forwarded_Read.php';	
										$head='Read Forwarded';
										break;
									
								case 'LeaveCompose' :
									$content=$path.'Mail_Leave_Compose_Annual.php';	
									$head='Compose New Annual Leave';
									break;
									
								case 'Off-Duty' :
									$content=$path.'Mail_Leave_Compose_Off-Duty.php';	
									$head='Compose New Off-Duty Leave';
									break;
									
								case 'Half-Day' :
									$content=$path.'Mail_Leave_Compose_Half-Day.php';	
									$head='Compose New Half-Day Permission';
									break;
									
								case 'Sick-Leave' :
									$content=$path.'Mail_Leave_Compose_Sick-Leave.php';	
									$head='Compose New Sick Leave';
									break;
									
								case 'Maternity-Paternity' :
									$content=$path.'Mail_Leave_Compose_Maternity-Paternity.php';	
									$head='Compose New Maternity/Paternity Leave';
									break;
									
								case 'Compassionate' :
									$content=$path.'Mail_Leave_Compose_Compassionate.php';	
									$head='Compose New Compassionate Leave';
									break;
									
									default :
									$content =$path.'Mail_Leave_Inbox.php';
									$head='Inbox';
									$_GET['Link']="LeaveInbox";
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
						$statement = " leave_annual,www_users,leave_types,leave_approval_levels,employee,departments,section,chiefofficer
										WHERE leave_annual.added_by=www_users.userid 
										AND leave_annual.leave_type=leave_types.id 
										AND leave_approval_levels.leave_type=leave_types.id 
										AND leave_approval_levels.levelcheck=leave_annual.levelcheck
										AND leave_annual.emp_id=employee.emp_id
										AND employee.id_dept=departments.departmentid
										AND employee.id_sec=section.id_sec
										AND departments.departmentid=chiefofficer.id_dept
										AND send = 1 AND rejected=0 AND
										CASE WHEN leave_approval_levels.authoriser ='HOD' THEN departments.authoriser='".$_SESSION['UserID']."' 
										WHEN leave_approval_levels.authoriser ='SH' THEN section.emp_id='".$_SESSION['UserID']."' 
										WHEN leave_approval_levels.authoriser ='CO' THEN chiefofficer.emp_id='".$_SESSION['UserID']."' 
										ELSE leave_approval_levels.authoriser ='".$_SESSION['UserID']."' END";
						$statement2 = " leave_off_duty,www_users,leave_types,leave_approval_levels,employee,departments,section,chiefofficer
										WHERE leave_off_duty.added_by=www_users.userid 
										AND leave_off_duty.leave_type=leave_types.id 
										AND leave_approval_levels.leave_type=leave_types.id 
										AND leave_approval_levels.levelcheck=leave_off_duty.levelcheck
										AND leave_off_duty.emp_id=employee.emp_id
										AND employee.id_dept=departments.departmentid
										AND employee.id_sec=section.id_sec
										AND departments.departmentid=chiefofficer.id_dept
										AND send = 1 AND rejected=0 AND
										CASE WHEN leave_approval_levels.authoriser ='HOD' THEN departments.authoriser='".$_SESSION['UserID']."' 
										WHEN leave_approval_levels.authoriser ='SH' THEN section.emp_id='".$_SESSION['UserID']."' 
										WHEN leave_approval_levels.authoriser ='CO' THEN chiefofficer.emp_id='".$_SESSION['UserID']."' 
										ELSE leave_approval_levels.authoriser ='".$_SESSION['UserID']."' END";
						$statement3 = " leave_half_day,www_users,leave_types,leave_approval_levels,employee,departments,section,chiefofficer
										WHERE leave_half_day.added_by=www_users.userid 
										AND leave_half_day.leave_type=leave_types.id 
										AND leave_approval_levels.leave_type=leave_types.id 
										AND leave_approval_levels.levelcheck=leave_half_day.levelcheck
										AND leave_half_day.emp_id=employee.emp_id
										AND employee.id_dept=departments.departmentid
										AND employee.id_sec=section.id_sec
										AND departments.departmentid=chiefofficer.id_dept
										AND send = 1 AND rejected=0 AND
										CASE WHEN leave_approval_levels.authoriser ='HOD' THEN departments.authoriser='".$_SESSION['UserID']."' 
										WHEN leave_approval_levels.authoriser ='SH' THEN section.emp_id='".$_SESSION['UserID']."' 
										WHEN leave_approval_levels.authoriser ='CO' THEN chiefofficer.emp_id='".$_SESSION['UserID']."' 
										ELSE leave_approval_levels.authoriser ='".$_SESSION['UserID']."' END";
  
						$results = "SELECT leave_id FROM {$statement}";
						$results2 = "SELECT off_id FROM {$statement2}";
						$results3 = "SELECT half_id FROM {$statement3}";
						$sqlquery = $results." UNION ALL ".$results2." UNION ALL ".$results3."";
						$query = "SELECT COUNT(*) as `num` FROM ({$sqlquery}) t";
						$welc = DB_query($query,$ErrMsg,$DbgMsg);
						$num = DB_fetch_array($welc);
						if($num['num']>0){
						$inbox = '<span class="label2 label-danger pull-right">'.$num['num'].'</span>';
						}else{
						$inbox ="";
						}

				?>	
<div class="container-fluid">
<div class = "row">
<div class="col-md-3 col-md-offset-">
<ul class="navbar-nav" style="width:100%;" >
<li class="dropdown" style="width:100%;" >
	<a href="#" class="dropdown-toggle" data-toggle="dropdown"><button style="width:100%;" class="btn btn-primary"><i class="fa fa-pencil-square-o"></i> Compose Leave <span class="caret"></span></button></a>
		<ul class="dropdown-menu" role="menu">
			<li><a href="index.php?Application=HR&Ref=LeaveApp&Link=LeaveCompose">Annual Leave Application</a></li>
			<li><a href="index.php?Application=HR&Ref=LeaveApp&Link=Off-Duty">Off-Duty Leave Application</a></li>
			<li><a href="index.php?Application=HR&Ref=LeaveApp&Link=Half-Day">Half-Day Permission Application &nbsp;&nbsp;&nbsp;&nbsp;</a></li>
			<li><a href="index.php?Application=HR&Ref=LeaveApp&Link=Sick-Leave">Sick Leave Application</a></li>
			<li><a href="index.php?Application=HR&Ref=LeaveApp&Link=Maternity-Paternity">Maternity/Paternity Leave</a></li>
			<li><a href="index.php?Application=HR&Ref=LeaveApp&Link=Compassionate">Compassionate Leave</a></li>
		</ul>
</li>
</ul>
<br></br><p></p>
	<div class="panel panel-default">
		<div class="panel-heading">Folders</div>
			<div class="panel-body">
			
			<ul class="nav nav-pills nav-stacked">
                <li <?php echo (($_GET['Link']=="LeaveInbox" || $_GET['Link']=="LeaveInboxRead") ? 'class="active"' : ''); ?>><a href="index.php?Application=HR&Ref=LeaveApp&Link=LeaveInbox"><i class="fa fa-inbox"></i> Inbox <?php echo $inbox; ?></a></li>
				<li <?php echo (($_GET['Link']=="LeaveSent" || $_GET['Link']=="LeaveSentRead") ? 'class="active"' : ''); ?>><a href="index.php?Application=HR&Ref=LeaveApp&Link=LeaveSent"><i class="fa fa-envelope-o"></i> Sent </a></li>
                <li <?php echo (($_GET['Link']=="LeaveDraft" || $_GET['Link']=="LeaveDraftRead") ? 'class="active"' : ''); ?>><a href="index.php?Application=HR&Ref=LeaveApp&Link=LeaveDraft"><i class="fa fa-file-text-o"></i> Drafts </a></li>
                <li <?php echo (($_GET['Link']=="LeaveApproved" || $_GET['Link']=="LeaveApprovedRead") ? 'class="active"' : ''); ?>><a href="index.php?Application=HR&Ref=LeaveApp&Link=LeaveApproved"><i class="fa fa-filter"></i> Approved <span class="pull-right"><i class="fa fa-star text-warning"></i></span></a></li>
				<li <?php echo (($_GET['Link']=="LeaveForwarded" || $_GET['Link']=="LeaveForwardedRead") ? 'class="active"' : ''); ?>><a href="index.php?Application=HR&Ref=LeaveApp&Link=LeaveForwarded"><i class="fa fa-share"></i> Forwarded </a></li>
				<li <?php echo ($_GET['Link']=="LeaveTrash" ? 'class="active"' : ''); ?>><a href="index.php?Application=HR&Ref=LeaveApp&Link=LeaveTrash"><i class="fa fa-trash-o"></i> Rejected</a></li>
              </ul>
			</div>
		</div>
	</div>

	<div id="printarea">
	<div class="col-md-9 col-md-offset-">
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

