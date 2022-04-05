<?php 

include('includes/SQL_CommonFunctions.inc');
	$mainlink = "index.php?Application=QA&Link=";
function calculate_time_span($date){
    $seconds  = strtotime(date('Y-m-d H:i:s')) - strtotime($date);

        $months = floor($seconds / (3600*24*30));
        $day = floor($seconds / (3600*24));
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds - ($hours*3600)) / 60);
        $secs = floor($seconds % 60);

        if($seconds < 60)
            $time = $secs." seconds";
        else if($seconds < 60*60 )
            $time = $mins." min";
        else if($seconds < 24*60*60)
            $time = $hours." hours";
        else if($seconds < 24*60*60*30)
            $time = $day." day";
        else
            $time = $months." month";

        return $time;
}
				$path='QAv2/';
				$view = (isset($_GET['Link']) && $_GET['Link'] != '') ? $_GET['Link'] : '';
								switch ($view) {
								case 'AQL' :
									$content=$path.'AQL_Listing.php';	
									$head='New AQL Sample Results/Remarks';
									break;
									case 'AQLRead' :
										$content=$path.'AQL_Read.php';	
										$head='Results Information';
										break;
									case 'AQLReadTest' :
										$content=$path.'AQL_ReadTest.php';	
										$head='Results Information';
										break;
										
								case 'Non-Conformance' :
									$content=$path.'NonConformance_Listing.php';	
									$head='New Non-Conforming Products';
									break;
									case 'Non-ConformanceRead' :
										$content=$path.'NonConformance_Read.php';	
										$head='Non-Conforming Products Information';
										break;
										
								case '54QA' :
									$content=$path.'54QA_Listing.php';	
									$head='New 54 QA Daily Report';
									break;
									case '54QARead' :
										$content=$path.'54QA_Read.php';	
										$head='54 QA Daily Report Information';
										break;
										
								case 'QAHardness' :
									$content=$path.'QAHardness_Listing.php';	
									$head='New QA Hardness Annealing Graph';
									break;
									case 'QAHardnessRead' :
										$content=$path.'QAHardness_Read.php';	
										$head=' QA Hardness Annealing Information';
										break;
										
								case 'PrimerSensitivity' :
									$content=$path.'PrimerSensitivity_Listing.php';	
									$head='New Primer Sensitivity Curve';
									break;
									case 'PrimerSensitivityRead' :
										$content=$path.'PrimerSensitivity_Read.php';	
										$head='Primer Sensitivity Curve Information';
										break;
										
								case 'RawMatAcc' :
									$content=$path.'RawMaterial_Listing.php';	
									$head='New Raw Material Acceptance Forms';
									break;
									case 'RawMatAccRead' :
										$content=$path.'RawMaterial_Read.php';	
										$head='Raw Material Acceptance Information';
										break;
										
								case 'PropAcc' :
									$content=$path.'PropellantAcceptance_Listing.php';	
									$head='New Propellant Acceptance Forms';
									break;
									case 'PropAccRead' :
										$content=$path.'PropellantAcceptance_Read.php';	
										$head='Propellant Acceptance Information';
										break;
										
								case 'NewAQL' :
									$content=$path.'AQL_New.php';	
									$head='Add New AQL Sample Results';
									break;
									
								case 'NewNonConformance' :
									$content=$path.'NonConformance_New.php';	
									$head='Add New Non-Conforming Products';
									break;
									
								case 'New54QA' :
									$content=$path.'54QA_New.php';	
									$head='Add New 54 QA Daily Report';
									break;
									
								case 'NewQAHardness' :
									$content=$path.'QAHardness_New.php';	
									$head='Add New QA Hardness Annealing Graph';
									break;
									
								case 'NewPrimerSen' :
									$content=$path.'PrimerSensitivity_New.php';	
									$head='Add New Primer Sensitivity Curve';
									break;
									
								case 'NewRawMat' :
									$content=$path.'RawMaterial_New.php';	
									$head='Add New Raw Material Acceptance Form';
									break;
									
								case 'NewPropAcc' :
									$content=$path.'PropellantAcceptance_New.php';	
									$head='Add New Propellant Acceptance Form';
									break;
									
								case 'Settings' :
									$content=$path.'Setting_Levels.php';	
									$head='Approval Levels';
									break;
									
									default :
									$content =$path.'AQL_Listing.php';
									$head='New AQL Sample Results/Remarks';
									$_GET['Link']="AQL";
									break;
							}
							
function pagination_inbox($query,$per_page=10,$page=1,$url='?'){   
    global $db; 
    $query = "SELECT COUNT(*) as `num` FROM {$query}";
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

$statement = " SELECT COUNT(a.sampleid) FROM qasamples a
									INNER JOIN qa_approval_levels c ON c.type=1
									WHERE a.process_level=c.levelcheck AND 
									(CASE WHEN c.authoriser='QAT' THEN (a.createdby='".$_SESSION['UserID']."' OR '".$_SESSION['UserID']."' IN(SELECT serviceno FROM qat)) ELSE c.authoriser='".$_SESSION['UserID']."' END)"; //LEFT OUTER JOIN qasampletechnicians x on x.sampleidno=a.sampleid OR x.technician='".$_SESSION['UserID']."'
$welcome1 = DB_query($statement);
$aql =DB_fetch_row($welcome1);
$statement = " SELECT COUNT(a.id) FROM qanonconformingproducts a
										INNER JOIN qa_approval_levels c ON c.type=2
										WHERE process_level =c.levelcheck AND 
										(CASE WHEN c.authoriser='MS' THEN a.mc_setter='".$_SESSION['UserID']."' 
										 	  WHEN c.authoriser='QAT' THEN a.technicianid='".$_SESSION['UserID']."' 
											  ELSE c.authoriser='".$_SESSION['UserID']."' END)";
$welcome1 = DB_query($statement);
$non =DB_fetch_row($welcome1);
$statement = " SELECT COUNT(a.id) FROM qadailyreport a
										INNER JOIN qa_approval_levels c ON c.type=3
										WHERE process_level =c.levelcheck AND 
										(CASE WHEN c.authoriser='QAT' THEN a.technicianid='".$_SESSION['UserID']."' ELSE c.authoriser='".$_SESSION['UserID']."' END)";
$welcome1 = DB_query($statement);
$aqd =DB_fetch_row($welcome1);
$statement = " SELECT COUNT(a.id) FROM qaannealinghardness a
										INNER JOIN qa_approval_levels c ON c.type=4
										WHERE process_level =c.levelcheck AND 
										(CASE WHEN c.authoriser='QAT' THEN a.technicianid='".$_SESSION['UserID']."' ELSE c.authoriser='".$_SESSION['UserID']."' END)";
$welcome1 = DB_query($statement);
$ha =DB_fetch_row($welcome1);
$statement = " SELECT COUNT(a.testno) FROM qaprimersensitivity a
										INNER JOIN qa_approval_levels c ON c.type=5
										WHERE process_level =c.levelcheck AND 
										(CASE WHEN c.authoriser='QAT' THEN a.technicianid='".$_SESSION['UserID']."' ELSE c.authoriser='".$_SESSION['UserID']."' END)";
$welcome1 = DB_query($statement);
$psc =DB_fetch_row($welcome1);
$statement = " SELECT COUNT(a.id) FROM qarawmatacceptance a
										INNER JOIN qa_approval_levels c ON c.type=6
										WHERE process_level =c.levelcheck AND 
										(CASE WHEN c.authoriser='QAT' THEN FIND_IN_SET('".$_SESSION['UserID']."',a.inspectors) ELSE c.authoriser='".$_SESSION['UserID']."' END)";
$welcome1 = DB_query($statement);
$rma =DB_fetch_row($welcome1);
$statement = " SELECT COUNT(a.id) FROM qapropellantacceptance a
										INNER JOIN qa_approval_levels c ON c.type=7
										WHERE process_level =c.levelcheck AND 
										(CASE WHEN c.authoriser='QAT' THEN a.technicianid='".$_SESSION['UserID']."' ELSE c.authoriser='".$_SESSION['UserID']."' END)";
$welcome1 = DB_query($statement);
$prop =DB_fetch_row($welcome1);

echo '<link href="' . $RootPath . '/css/'. $_SESSION['Theme'] .'/default.css" rel="stylesheet" type="text/css" />';
echo '<script type="text/javascript" src = "js/jquery-1.9.1.js"></script>';
?>	
<link rel="stylesheet" href="QAv2/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="QAv2/iCheck/flat/blue.css">
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
	<a href="#" class="dropdown-toggle" data-toggle="dropdown"><button style="width:100%;" class="btn btn-primary"><i class="fa fa-pencil-square-o"></i> Document Register <span class="caret"></span></button></a>
		<ul class="dropdown-menu" role="menu">
			<?php 
			echo '<li style="width:240px"><a href="'.$mainlink.'NewAQL">Add New AQL Sample Results</a></li>';
			echo '<li><a href="'.$mainlink.'NewNonConformance">New Non-Conformance Form</a></li>';
			echo '<li><a href="'.$mainlink.'New54QA">New 54 QA Daily Report</a></li>';
			echo '<li><a href="'.$mainlink.'NewQAHardness">New Hardness Annealing Graph</a></li>';
			echo '<li><a href="'.$mainlink.'NewPrimerSen">New Primer Sensitivity Curve</a></li>';
			echo '<li><a href="'.$mainlink.'NewRawMat">Raw Material Acceptance Form</a></li>';
			echo '<li><a href="'.$mainlink.'NewPropAcc">Propellant Acceptance Form</a></li>';
			?>
		</ul>
</li>
</ul>
<br></br><p></p>
	<div class="panel panel-default">
		<div class="panel-heading">Folders</div>
			<div class="panel-body">
			
			<ul class="nav nav-pills nav-stacked" style="font-size:11px;">
                <li <?php echo (($_GET['Link']=="AQL" || $_GET['Link']=="AQLRead" || $_GET['Link']=="AQLReadTest") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>AQL"><i class="fa fa-file-text"></i> AQL Sample Results </a><span class="label-count"><?php echo ($aql[0]>0 ? $aql[0]:''); ?></span></li>
				<li <?php echo (($_GET['Link']=="Non-Conformance" || $_GET['Link']=="Non-ConformanceRead") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>Non-Conformance"><i class="fa fa-language"></i> Non-Conformance Form</a><span class="label-count"><?php echo ($non[0]>0 ? $non[0]:''); ?></span></li>
				 <li <?php echo (($_GET['Link']=="54QA" || $_GET['Link']=="54QARead") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>54QA"><i class="fa fa-bar-chart-o"></i> 54 QA Daily Report </a><span class="label-count"><?php echo ($aqd[0]>0 ? $aqd[0]:''); ?></span></li>
                <li <?php echo (($_GET['Link']=="QAHardness" || $_GET['Link']=="QAHardnessRead") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>QAHardness"><i class="fa fa-line-chart"></i> Hardness Annealing Graph</a><span class="label-count"><?php echo ($ha[0]>0 ? $ha[0]:''); ?></span></li>
				<li <?php echo (($_GET['Link']=="PrimerSensitivity"  || $_GET['Link']=="PrimerSensitivityRead") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>PrimerSensitivity"><i class="fa fa-snowflake-o"></i> Primer Sensitivity Curve </a><span class="label-count"><?php echo ($psc[0]>0 ? $psc[0]:''); ?></span></li>
				<li <?php echo (($_GET['Link']=="RawMatAcc"  || $_GET['Link']=="RawMatAccRead") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>RawMatAcc"><i class="fa fa-bullseye"></i> Raw Material Acceptance </a><span class="label-count"><?php echo ($rma[0]>0 ? $rma[0]:''); ?></span></li>
				<li <?php echo (($_GET['Link']=="PropAcc"  || $_GET['Link']=="PropAccRead") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>PropAcc"><i class="fa fa-bullseye"></i> Propellant Acceptance </a><span class="label-count"><?php echo ($prop[0]>0 ? $prop[0]:''); ?></span></li>
				<li <?php echo (($_GET['Link']=="Settings"  || $_GET['Link']=="SettingRead") ? 'class="active"' : ''); ?>><a href="<?php echo $mainlink; ?>Settings"><i class="fa fa-gear"></i> Settings <span class="pull-right"><i class="fa fa-star text-primary"></i></span></a></li>
              </ul>
			</div>
		</div>
	</div>	
	<div id="printarea">
	<div class="col-md-9 col-md-offset-">
	<div class="panel panel-success">
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

.label-count {
  position: absolute;
  top: 2px;
  right: 6px;
  font-size: 10px;
  color:#FFFFFF;
  line-height: 17px;
  background-color:#CC0000;
  padding: 0 4px;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  -ms-border-radius: 3px;
  border-radius: 5px; }
</style>

