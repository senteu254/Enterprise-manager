<?php 

include('includes/SQL_CommonFunctions.inc');
	$mainlink = "index.php?Application=FA&Link=";
function secondsToWords($seconds)
{
    $ret = "";

    /*** get the days ***/
    $days = intval(intval($seconds) / (3600*24));
    if($days> 0)
    {
        $ret .= "$days days ";
    }

    /*** get the hours ***/
    $hours = (intval($seconds) / 3600) % 24;
    if($hours > 0)
    {
        $ret .= "$hours hours ";
    }

    /*** get the minutes ***/
    $minutes = (intval($seconds) / 60) % 60;
    if($minutes > 0)
    {
        $ret .= "$minutes minutes ";
    }

    /*** get the seconds ***/
    $seconds = intval($seconds) % 60;
    if ($seconds > 0) {
        $ret .= "$seconds seconds";
    }

    return $ret;
}

				$path='MTNCEv2/';
				$view = (isset($_GET['Link']) && $_GET['Link'] != '') ? $_GET['Link'] : '';
								switch ($view) {
								case 'NewPMPlan' :
									$content=$path.'New_PMPlan.php';	
									$head='New Preventive Maintenance Plan';
									break;
									
								case 'NewAsset' :
									$content=$path.'FixedAssetItems.php';	
									$head='New Fixed Asset Register';
									break;
									
								case 'AssetCategories' :
									$content=$path.'FixedAssetCategories.php';	
									$head='Fixed Asset Category Maintenance';
									break;
									
								case 'AssetLocation' :
									$content=$path.'FixedAssetLocations.php';	
									$head='Fixed Asset Location Maintenance';
									break;
									
								case 'SearchAsset' :
									$content=$path.'Assets_Listing.php';	
									$head='Fixed Asset Register';
									break;
									
								case 'Breakdown' :
									$content=$path.'Breakdown_Listing.php';	
									$head='Breakdown Service Requisition Awaiting Scheduling';
									break;
									case 'BreakdownRead' :
										$content=$path.'Breakdown_Read.php';	
										$head='Breakdown Requisition Information';
										break;
										
								case 'Preventive' :
									$content=$path.'Preventive_Listing.php';	
									$head='Preventive Service Requisition Awaiting Scheduling';
									break;	
									case 'PreventiveRead' :
										$content=$path.'Preventive_Read.php';	
										$head='Preventive Requisition Information';
										break;
										
								case 'ScheduleTask' :
									$content=$path.'ScheduleTask_Listing.php';	
									$head='Pending Breakdown Requisition Tasks';
									break;	
									case 'ScheduleTaskRead' :
										$content=$path.'ScheduleTask_Read.php';	
										$head='Maintenance Task Information';
										break;
									case 'ScheduleTaskReadP' :
										$content=$path.'ScheduleTask_ReadP.php';	
										$head='Maintenance Task Information';
										break;
									
								case 'CompleteTask' :
									$content=$path.'CompleteTask_Listing.php';	
									$head='Complete Breakdown Requisition Reports';
									break;
									case 'CompleteTaskRead' :
										$content=$path.'CompleteTask_Read.php';	
										$head='Complete Task Reports';
										break;
									case 'CompleteTaskReadP' :
										$content=$path.'CompleteTask_ReadP.php';	
										$head='Complete Task Reports';
										break;
								
								case 'Planning' :
									$content=$path.'Planning_Listing.php';	
									$head='Maintenance Planning Reports';
									break;
									case 'PlanningRead' :
										$content=$path.'CompleteTask_Read.php';	
										$head='Complete Task Reports';
										break;
										
								case 'PlanningSchedule' :
									$content=$path.'PlanningSchedule_Listing.php';	
									$head='Machines Schedule for Maintenance this Month';
									break;
									case 'PlanningScheduleRead' :
										$content=$path.'PlanningSchedule_Read.php';	
										$head='Complete Task Reports';
										break;
										
								case 'Journal' :
									$content=$path.'FixedAssetDepreciation.php';	
									$head='Fixed Asset Depreciation Journal';
									break;
									
									default :
									$content =$path.'Breakdown_Listing.php';
									$head='Breakdown Requisition';
									$_GET['Link']="Breakdown";
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

						$maintenance = " irq_request z 
							INNER JOIN irq_maintenance a on a.maintenanceid = z.requestid 
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN www_users y ON z.initiator = y.userid
							WHERE draft=0 AND closed=1 AND (SELECT caneditMF FROM www_users WHERE userid='".$_SESSION['UserID']."')=1 AND requestid NOT IN(SELECT requestid FROM fixedassettasks)";
						$query = "SELECT COUNT(*) as `num` FROM {$maintenance}";
						$welc = DB_query($query,$ErrMsg,$DbgMsg);
						$num = DB_fetch_array($welc);
						if($num['num']>0){
						$inbox = '<span class="label2 label-danger pull-right">'.$num['num'].' New</span>';
						}else{
						$inbox ="";
						}
						
						$maintenance = " fixedassets z 
							INNER JOIN fixedassetlocations a on a.locationid = z.assetlocation 
							INNER JOIN fixedassetplanning e ON z.assetid = e.assetid
							INNER JOIN fixedassetplantask f ON e.planid = f.planid
							WHERE (SELECT caneditMF FROM www_users WHERE userid='".$_SESSION['UserID']."')=1 AND e.planid NOT IN(SELECT requestid FROM fixedassettasks)";
						$query = "SELECT COUNT(*) as `num` FROM {$maintenance}";
						$welc = DB_query($query,$ErrMsg,$DbgMsg);
						$num = DB_fetch_array($welc);
						if($num['num']>0){
						$inbox2 = '<span class="label2 label-danger pull-right">'.$num['num'].' New</span>';
						}else{
						$inbox2 ="";
						}
						
						$maintenance = " irq_request z 
							INNER JOIN irq_maintenance a on a.maintenanceid = z.requestid 
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN www_users y ON z.initiator = y.userid
							INNER JOIN fixedassettasks ft ON z.requestid = ft.requestid
							WHERE draft=0 AND closed=1 AND completed=0 AND (manager='".$_SESSION['UserID']."' OR userresponsible='".$_SESSION['UserID']."')";
						$maintenance2 = " fixedassets z 
							INNER JOIN fixedassetlocations a on a.locationid = z.assetlocation 
							INNER JOIN fixedassetplanning e ON z.assetid = e.assetid
							INNER JOIN irq_documents f ON e.docid = f.doc_id
							INNER JOIN fixedassettasks ft ON e.planid = ft.requestid
							INNER JOIN fixedassetplantask s ON e.planid = s.planid
							WHERE completed=0 AND (manager='".$_SESSION['UserID']."' OR userresponsible='".$_SESSION['UserID']."')";
						$query1 = "SELECT z.requestid FROM {$maintenance}";
						$query2 = "SELECT ft.requestid FROM {$maintenance2}";
						$sqlquery = $query1." UNION ALL ".$query2."";
						$query = "SELECT COUNT(*) as `num` FROM ({$sqlquery}) t";
						$welc = DB_query($query,$ErrMsg,$DbgMsg);
						$num = DB_fetch_array($welc);
						if($num['num']>0){
						$inbox3 = '<span class="label2 label-danger pull-right">'.$num['num'].' New</span>';
						}else{
						$inbox3 ="";
						}
						
						$maintenance = " fixedassets z 
							INNER JOIN fixedassetlocations a on a.locationid = z.assetlocation 
							INNER JOIN fixedassetplanning e ON z.assetid = e.assetid
							WHERE fyend='".FormatDateForSQL(Date($_SESSION['DefaultDateFormat'],YearEndDate($_SESSION['YearEnd'],0)))."' 
							AND FIND_IN_SET(".date('m').",months)>0 AND (SELECT COUNT(planid) FROM fixedassetplantask q WHERE q.planid=e.planid AND month=".date('m').")=0 AND (SELECT caneditMP FROM www_users WHERE userid='".$_SESSION['UserID']."')=1";
						$query = "SELECT COUNT(*) as `num` FROM {$maintenance}";
						$welc = DB_query($query,$ErrMsg,$DbgMsg);
						$num = DB_fetch_array($welc);
						if($num['num']>0){
						$inbox4 = '<span class="label2 label-danger pull-right">'.$num['num'].' New</span>';
						}else{
						$inbox4 ="";
						}

echo '<link href="' . $RootPath . '/css/'. $_SESSION['Theme'] .'/default.css" rel="stylesheet" type="text/css" />';
echo '<script type="text/javascript" src = "js/jquery-1.9.1.js"></script>';
?>	
<link rel="stylesheet" href="IRQv2/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="IRQv2/iCheck/flat/blue.css">
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
 <script src="bootstrap/js/bootstrap.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
 <script src="bootstrap/js/jquery.min.js"></script>
 
  <link href="facebox/src/facebox.css" media="screen" rel="stylesheet" type="text/css" />
  <script src="facebox/lib/jquery.js" type="text/javascript"></script>
  <script src="facebox/src/facebox.js" type="text/javascript"></script>
  <script type="text/javascript">
    jQuery(document).ready(function($) {
      $('a[rel*=facebox]').facebox({
        loadingImage : 'facebox/src/loading.gif',
        closeImage   : 'facebox/src/closelabel.png'
      })
    })
  </script>

<div class="container-fluid">
<div class = "row">
<div class="col-md-3 col-md-offset-">
<ul class="navbar-nav" style="width:100%;" >
<li class="dropdown" style="width:100%;" >
	<a href="#" class="dropdown-toggle" data-toggle="dropdown"><button style="width:100%;" class="btn btn-primary"><i class="fa fa-pencil-square-o"></i> Asset/Planning Register <span class="caret"></span></button></a>
		<ul class="dropdown-menu" role="menu">
			<?php 
			echo '<li style="width:240px"><a href="'.$mainlink.'NewAsset">Add New Asset</a></li>';
			echo '<li><a href="'.$mainlink.'NewPMPlan">Preventive Maintenance Plan</a></li>';
			echo '<li><a href="'.$mainlink.'AssetCategories">Modify Asset Categories</a></li>';
			echo '<li><a href="'.$mainlink.'AssetLocation">Modify Asset Location</a></li>';
			?>
		</ul>
</li>
</ul>
<br></br><p></p>
	<div class="panel panel-default">
		<div class="panel-heading">Folders</div>
			<div class="panel-body">
			
			<ul class="nav nav-pills nav-stacked" style="font-size:11px;">
                <li <?php echo (($_GET['Link']=="Breakdown" || $_GET['Link']=="BreakdownRead") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>Breakdown"><i class="fa fa-file-text"></i> Breakdown Requisition <?php echo $inbox; ?></a></li>
				<li <?php echo (($_GET['Link']=="Preventive" || $_GET['Link']=="PreventiveRead") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>Preventive"><i class="fa fa-envelope-o"></i> Preventive Requisition <?php echo $inbox2; ?></a></li>
				<li <?php echo (($_GET['Link']=="ScheduleTask" || $_GET['Link']=="ScheduleTaskRead" || $_GET['Link']=="ScheduleTaskReadP") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>ScheduleTask"><i class="fa fa-file-text"></i> Scheduled Tasks <?php echo $inbox3; ?></a></li>
				<li <?php echo (($_GET['Link']=="CompleteTask"  || $_GET['Link']=="CompleteTaskRead" || $_GET['Link']=="CompleteTaskReadP") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>CompleteTask"><i class="fa fa-gear"></i> Completed Tasks </a></li>
				<li <?php echo (($_GET['Link']=="Planning"  || $_GET['Link']=="PlanningRead") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>Planning"><i class="fa fa-gear"></i> Planning <span class="pull-right"><i class="fa fa-star text-danger"></i></span></a></li>
				<li <?php echo (($_GET['Link']=="PlanningSchedule"  || $_GET['Link']=="PlanningScheduleRead") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>PlanningSchedule"><i class="fa fa-gear"></i> Planning Schedule <?php echo $inbox4; ?></a></li>
				<li <?php echo (($_GET['Link']=="SearchAsset"  || $_GET['Link']=="SearchAssetRead") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>SearchAsset"><i class="fa fa-gear"></i> Assets <span class="pull-right"><i class="fa fa-star text-danger"></i></span></a></li>
				<li <?php echo (($_GET['Link']=="Journal"  || $_GET['Link']=="JournalRead") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>Journal"><i class="fa fa-gear"></i> Depreciation Journal <span class="pull-right"><i class="fa fa-star text-danger"></i></span></a></li>
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
	$(document).ready(function(){
       		 $("#div3").fadeOut(4000);
			});
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
  <script type="text/javascript"  src="js/jquery.dataTables.min.js"></script>
  <script>
  $(function(){
    $("#myTable").dataTable();
});
$(function(){
    $("#myTable2").dataTable();
});
  </script>		
<style>
#loadingbackground{
	display:none; 	
    opacity: 0.8;
	z-index: 999;
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

