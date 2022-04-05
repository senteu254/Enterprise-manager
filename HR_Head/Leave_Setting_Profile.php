<link rel="stylesheet" href="HR_Head/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="HR_Head/iCheck/flat/blue.css">
<?php

				$path='HR_Head/';
				$view = (isset($_GET['Link']) && $_GET['Link'] != '') ? $_GET['Link'] : '';
								switch ($view) {
								case 'LeaveTypes' :
									$content =$path.'Setting_Leave_Types.php';
									$head='Leave Types';
									break;
									
								case 'LeaveAlloc' :
									$content=$path.'Setting_Leave_DaysAllocation.php';	
									$head='Leave Days Allocation';
									break;
									
								case 'LeaveBal' :
									$content=$path.'Setting_Leave_DaysBalances.php';	
									$head='Opening Leave Days Balances';
									break;
								
								case 'LeaveApprovalLevel' :
									$content=$path.'Setting_Leave_Levels.php';	
									$head='Leave Approval Levels';
									break;
									
								case 'Holidays' :
									$content=$path.'Setting_Leave_Holidays.php';	
									$head='Holidays Setup';
									break;
									
									default :
									$content =$path.'Setting_Leave_Levels.php';
									$head='Leave Approval Levels';
									$_GET['Link']="LeaveApprovalLevel";
									break;
							}
				?>	
<div class="container-fluid">
<div class = "row">
<div class="col-md-3 col-md-offset-">
	<div class="panel panel-default">
		<div class="panel-heading">Main Links</div>
			<div class="panel-body">
			
			<ul class="nav nav-pills nav-stacked">
				<li <?php echo ($_GET['Link']=="LeaveApprovalLevel" ? 'style="border-left:solid; border-left-color:#6666FF;"' : ''); ?>><a href="index.php?Application=HR&Ref=LeaveSetting&Link=LeaveApprovalLevel">Approval Levels</a></li>
                 <li <?php echo ($_GET['Link']=="LeaveBal" ? 'style="border-left:solid; border-left-color:#6666FF;"' : ''); ?>><a href="index.php?Application=HR&Ref=LeaveSetting&Link=LeaveBal">Opening Leave Balances</a></li>
				<li <?php echo ($_GET['Link']=="LeaveAlloc" ? 'style="border-left:solid; border-left-color:#6666FF;"' : ''); ?>><a href="index.php?Application=HR&Ref=LeaveSetting&Link=LeaveAlloc">Leave Days Allocation</a></li>
				<li <?php echo ($_GET['Link']=="Holidays" ? 'style="border-left:solid; border-left-color:#6666FF;"' : ''); ?>><a href="index.php?Application=HR&Ref=LeaveSetting&Link=Holidays">Holidays Setup</a></li>
				<li <?php echo ($_GET['Link']=="LeaveTypes" ? 'style="border-left:solid; border-left-color:#6666FF;"' : ''); ?>><a href="index.php?Application=HR&Ref=LeaveSetting&Link=LeaveTypes">Leave Types</a></li>
              </ul>
			</div>
		</div>
	</div>

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
