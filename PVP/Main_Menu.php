<link rel="stylesheet" href="PVP/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="PVP/iCheck/flat/blue.css">

<?php	

echo '<link href="' . $RootPath . '/css/'. $_SESSION['Theme'] .'/default.css" rel="stylesheet" type="text/css" />';
echo '<script type="text/javascript" src = "js/jquery-1.9.1.js"></script>';
				$path='PVP/';
				$view = (isset($_GET['Link']) && $_GET['Link'] != '') ? $_GET['Link'] : '';
								switch ($view) {
								case 'Home' :
									$content=$path.'main.php';	
									$head='Dashboard';
									break;
								case 'Initiate_PV_Contract' :
									$content=$path.'Create_PV_Contract.php';	
									$head='Initiate Payment Voucher(Contract)';
									break;
									
								case 'Initiate_PV' :
									$content=$path.'Create_PV.php';	
									$head='Initiate Payment Voucher';
									break;
								case 'Initiate_PV_Ovesea' :
									$content=$path.'Create_PV_Ovesea.php';	
									$head='Initiate Payment Voucher (Memo)';
									break;
								case 'Initiate_PV_Salary' :
									$content=$path.'Create_PV_Salary.php';	
									$head='Initiate Payment Voucher (Salary)';
									break;
								case 'inbox' :
									$content=$path.'inbox.php';	
									$head='New Payment Voucher';
									break;
								case 'cheque' :
									$content=$path.'inbox_cheque.php';	
									$head='New Payment Voucher';
									break;
								case 'inbox_Accountant' :
									$content=$path.'Rejected_PV.php';	
									$head='Rejected Payment Voucher';
									break;
								case 'Actioned_PV' :
									$content=$path.'Actioned.php';	
									$head='Actioned Payment Voucher';
									break;
								case 'Rejected_PV' :
									$content=$path.'Rejected.php';	
									$head='Rejected Payment Voucher for Amendments';
									break;	
								case 'PV_Report' :
									$content=$path.'PV_Report.php';	
									$head='Payment Voucher Reports';
									break;
								case 'PV_Detailed_Report' :
									$content=$path.'PV_Detailed_Report.php';	
									$head='PV Voucher Detailed Reports';
									break;
								case 'Paid_PV' :
									$content=$path.'PaidPV.php';	
									$head='Paid Payment Voucher';
									break;
								case 'drafted_PV' :
									$content=$path.'draft_PV.php';	
									$head='Drafts';
									break;
                     			case 'Amended_PV' :
									$content=$path.'amended_pv.php';	
									$head='Amended Payment Voucher';
									break;							
					            default :
									$content =$path.'main.php';
									$head='Home';
									break;
							}
/*	function pagination($query,$per_page=10,$page=1,$url='?'){   
    global $db; 
    $query = "SELECT COUNT(*) as `num` FROM {$query}";
    $row = mysqli_fetch_array(mysqli_query($db,$query));
    $total = $row['num'];
    $adjacents = "2"; 
      
    $prevlabel = "&lsaquo; Prev";
    $nextlabel = "Next &rsaquo;";
    $lastlabel = "Last &rsaquo;&rsaquo;";
      
    $page = ($page == 0 ? 1 : $page);  
    $start = ($page - 1) * $per_page;                               
      
    $prev = $page - 1;                          
    $next = $page + 1;
      
    $lastpage = ceil($total/$per_page);
      
    $lpm1 = $lastpage - 1; // //last page minus 1
      
    $pagination = "";
    if($lastpage > 1){   
        $pagination .= "<ul class='pagination'>";
        $pagination .= "<li class='page_info'>Page {$page} of {$lastpage}</li>";
              
            if ($page > 1) $pagination.= "<li><a href='{$url}page={$prev}'>{$prevlabel}</a></li>";
              
        if ($lastpage < 7 + ($adjacents * 2)){   
            for ($counter = 1; $counter <= $lastpage; $counter++){
                if ($counter == $page)
                    $pagination.= "<li><a class='current'>{$counter}</a></li>";
                else
                    $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";                    
            }
          
        } elseif($lastpage > 5 + ($adjacents * 2)){
              
            if($page < 1 + ($adjacents * 2)) {
                  
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
                    if ($counter == $page)
                        $pagination.= "<li><a class='current' style='color:blue'>{$counter}</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";                    
                }
                //$pagination.= "<li class='dot'>...</li>";
                $pagination.= "<li><a href='{$url}page={$lpm1}'>{$lpm1}</a></li>";
                $pagination.= "<li><a href='{$url}page={$lastpage}'>{$lastpage}</a></li>";  
                      
            } elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                  
                $pagination.= "<li><a href='{$url}page=1'>1</a></li>";
                $pagination.= "<li><a href='{$url}page=2'>2</a></li>";
                //$pagination.= "<li class='dot'>...</li>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<li><a class='current'>{$counter}</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";                    
                }
                //$pagination.= "<li class='dot'>..</li>";
                $pagination.= "<li><a href='{$url}page={$lpm1}'>{$lpm1}</a></li>";
                $pagination.= "<li><a href='{$url}page={$lastpage}'>{$lastpage}</a></li>";      
                  
            } else {
                  
                $pagination.= "<li><a href='{$url}page=1'>1</a></li>";
                $pagination.= "<li><a href='{$url}page=2'>2</a></li>";
                $pagination.= "<li class='dot'>..</li>";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<li><a class='current'>{$counter}</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";                    
                }
            }
        }
          
            if ($page < $counter - 1) {
                $pagination.= "<li><a href='{$url}page={$next}'>{$nextlabel}</a></li>";
                $pagination.= "<li><a href='{$url}page=$lastpage'>{$lastlabel}</a></li>";
            }
          
        $pagination.= "</ul>";        
    }
      
    return $pagination;
}*/						
/*function pagination_inbox($query,$per_page=10,$page=1,$url='?'){   
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
}*/
	$sql9="SELECT  *,CONCAT(1,payment_voucher.process_level) AS rejected FROM payment_voucher,pvroles,pvlevel 
				WHERE payment_voucher.process_level=pvlevel.levelcode 
				AND pvlevel.levelcode=pvroles.level
				AND payment_voucher.process_level<6 
				AND payment_voucher.state=0";
				$result9 = DB_query($sql9);
   $myrow9 = DB_fetch_array($result9);
   $sql9="SELECT  * FROM  pvroles
				WHERE authoriser='" . $_SESSION['UserID'] ."'";
				$result9 = DB_query($sql9);
   $myrow9 = DB_fetch_array($result9);
			 
   if($myrow9['level'] ==2 or $myrow9['level'] ==7){
 $query2 = "SELECT  * FROM payment_voucher,pvroles,pvlevel 
				WHERE payment_voucher.process_level=12 or payment_voucher.process_level=17
				AND pvlevel.levelcode=pvroles.level
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				AND payment_voucher.state=0
				GROUP BY payment_voucher.voucherid";
 }else{
 $query2 = "SELECT  * FROM payment_voucher,pvroles,pvlevel 
				WHERE payment_voucher.process_level=1".$myrow9['level']." 
				AND pvlevel.levelcode=pvroles.level
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				AND payment_voucher.state=0
				GROUP BY payment_voucher.voucherid";
 }			
				
   $count2 = DB_num_rows(DB_query($query2));
   if($myrow9['level'] ==2 or $myrow9['level'] ==7){
   $query1 = "SELECT  * FROM payment_voucher,pvroles,pvlevel 
				WHERE payment_voucher.process_level=pvlevel.levelcode 
				AND pvlevel.levelcode=pvroles.level
				AND pvroles.level=2
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				AND payment_voucher.state=0";
   }else{
   $query1 = "SELECT  * FROM payment_voucher,pvroles,pvlevel 
				WHERE payment_voucher.process_level=pvlevel.levelcode 
				AND pvlevel.levelcode=pvroles.level
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				AND payment_voucher.state=0";
   }
   $count = DB_num_rows(DB_query($query1));
   $amended1 = "SELECT  * FROM payment_voucher,pvroles,pvlevel 
				WHERE payment_voucher.process_level=pvlevel.levelcode 
				AND pvlevel.levelcode=pvroles.level
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				AND payment_voucher.state=0
				AND review_rejected=2";
	$amended2 = DB_num_rows(DB_query($amended1));
   $query33 = "SELECT  * FROM payment_voucher,pvroles,pvlevel 
				WHERE payment_voucher.process_level=pvroles.level
				AND pvroles.level=pvroles.level
				AND pvlevel.levelcode=0
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				AND payment_voucher.state=0";
  $count33 = DB_num_rows(DB_query($query33));
	
  $query3 = "SELECT  * FROM payment_voucher,pvroles,pvlevel 
				WHERE payment_voucher.process_level>pvlevel.levelcode 
				AND pvlevel.levelcode=pvroles.level
				AND pvroles.authoriser='" . $_SESSION['UserID'] ."'
				AND payment_voucher.state=0";
  $count3 = DB_num_rows(DB_query($query3));
  $query5 = "SELECT  * FROM payment_voucher 
	             WHERE process_level>10
				 AND process_level <19";
  $count5 = DB_num_rows(DB_query($query5));
	?>
<div class="container-fluid">
<div class = "row">
<div class="col-md-3 col-md-offset-">
	<?php
	$sql9="SELECT *  FROM pvroles  
				WHERE authoriser='" . $_SESSION['UserID'] ."'  ";
	$result9 = DB_query($sql9);
    $myrow9 = DB_fetch_array($result9);
	if($myrow9['level']==0){ ?>
				<!--<li><a href="index.php?Application=PVM&Ref=default&Link=Initiate_PV"><i class="fa fa-file-text-o"></i>Initiate PV &nbsp;&nbsp;</a></li>-->
				
<ul style="width:100%;" >
<li class="dropdown" style="width:100%;" >
	<a href="#" class="dropdown-toggle" data-toggle="dropdown"><button style="width:100%;" class="btn btn-primary"><i class="fa fa-pencil-square-o"></i> Initiate Payment Voucher <span class="caret"></span></button></a>
		<ul class="dropdown-menu" role="menu">
<?php 
/*echo '<li><a href="index.php?Application=PVM&Ref=default&Link=Initiate_PV"><i class="fa fa-file-text-o"></i>Initiate PV (With LPO/LSO) &nbsp;&nbsp;</a></li>';			
echo '<li><a href="index.php?Application=PVM&Ref=default&Link=Initiate_PV_Ovesea"><i class="fa fa-file-text-o"></i>Initiate PV (Contract/Memo) &nbsp;&nbsp;</a></li>';
echo '<li><a href="index.php?Application=PVM&Ref=default&Link=Initiate_PV_Salary"><i class="fa fa-file-text-o"></i>Initiate PV (Salary) &nbsp;&nbsp;</a></li>';*/
echo '<li><a href="index.php?Application=PVM&Ref=default&Link=Initiate_PV"><i class="fa fa-file-text-o"></i>Initiate PV (With LPO/LSO) &nbsp;&nbsp;</a></li>';			
echo '<li><a href="index.php?Application=PVM&Ref=default&Link=Initiate_PV_Ovesea"><i class="fa fa-file-text-o"></i>Initiate PV (Memo) &nbsp;&nbsp;</a></li>';
echo '<li><a href="index.php?Application=PVM&Ref=default&Link=Initiate_PV_Salary"><i class="fa fa-file-text-o"></i>Initiate PV (Salary) &nbsp;&nbsp;</a></li>';
echo '<li><a href="index.php?Application=PVM&Ref=default&Link=Initiate_PV_Contract"><i class="fa fa-file-text-o"></i>Initiate PV (Contract) &nbsp;&nbsp;</a></li>';
?>
		</ul>
</li>
</ul>
				 <?php }else echo'';
				 ?>
	<div class="panel panel-default">
		<div class="panel-heading">PV Folders</div>
			<div class="panel-body">
			
			<ul class="nav nav-pills nav-stacked">
                <!--<li><a href="index.php?Application=PVM&Ref=default&Link=Home"><i class="fa fa-inbox"></i> Dashboard </a></li>-->
				
<?php

				$sql9="SELECT *  FROM pvroles  
				WHERE authoriser='" . $_SESSION['UserID'] ."'";
				$result9 = DB_query($sql9);
$myrow9 = DB_fetch_array($result9);
	if($myrow9['level']==0){ ?>
				 <li <?php echo ''.($_GET['Link']=='drafted_PV'? 'class="active"':'').''; ?>><a href="index.php?Application=PVM&Ref=default&Link=drafted_PV"><i class="fa fa-envelope-o"></i> Draft <?php echo '<span class="num" id="num"><span class="label2 label-warning pull-right">'.$count33.'</span></span>'; ?></a></li>
				 <?php }else echo'';?>
				 <?php
				$sql9="SELECT *  FROM pvroles  
				WHERE authoriser='" . $_SESSION['UserID'] ."'  ";
				$result9 = DB_query($sql9);
$myrow9 = DB_fetch_array($result9);
	if($myrow9['level']==0){
                echo'<li '.($_GET['Link']=='inbox_Accountant'? 'class="active"':'').'><a href="index.php?Application=PVM&Ref=default&Link=inbox_Accountant"><i class="fa fa-envelope-o"></i>Rejected PV <span class="label2 label-danger pull-right">'.$count5.'</span></a></li>';
				 }else echo'<li '.($_GET['Link']=='inbox'? 'class="active"':'').'><a href="index.php?Application=PVM&Ref=default&Link=inbox"><i class="fa fa-envelope-o"></i> Inbox <span class="label2 label-primary pull-right">'.$count.'</span></a></li>
				 <li '.($_GET['Link']=='Amended_PV'? 'class="active"':'').'><a href="index.php?Application=PVM&Ref=default&Link=Amended_PV"><i class="fa fa-envelope-o"></i> Amended PV <span class="label2 label-primary pull-right">'.$amended2.'</span></a></li>';?>
                <li <?php echo ''.($_GET['Link']=='Actioned_PV'? 'class="active"':'').''; ?>><a href="index.php?Application=PVM&Ref=default&Link=Actioned_PV"><i class="fa fa-filter"></i> Actioned PV  <?php echo '<span class="num" id="num"><span class="label2 label-success pull-right">'.$count3.'</span></span>'; ?></a></li>
				<?php
				$sql9="SELECT *  FROM pvroles  
				WHERE authoriser='" . $_SESSION['UserID'] ."'  ";
				$result9 = DB_query($sql9);
$myrow9 = DB_fetch_array($result9);
	if($myrow9['level']>0){ ?>
				<li <?php echo ''.($_GET['Link']=='Rejected_PV'? 'class="active"':'').''; ?>><a href="index.php?Application=PVM&Ref=default&Link=Rejected_PV"><i class="fa fa-trash-o"></i> Rejected PV <?php echo '<span class="num" id="num"><span class="label2 label-danger pull-right">'.$count2.'</span></span>'; ?></a></li> 
				<?php }else{
				echo'';
				}
				?>
	<li <?php echo ''.($_GET['Link']=='PV_Report'? 'class="active"':'').''; ?>><a href="index.php?Application=PVM&Ref=default&Link=PV_Report"><i class="fa fa-file-text-o"></i> PV Reports&nbsp;&nbsp; </a></li>
	
	<li <?php echo ''.($_GET['Link']=='PV_Detailed_Report'? 'class="active"':'').''; ?>><a href="index.php?Application=PVM&Ref=default&Link=PV_Detailed_Report"><i class="fa fa-file-text-o"></i>PV Detailed Report&nbsp;&nbsp; </a></li>
	<li <?php echo ''.($_GET['Link']=='Paid_PV'? 'class="active"':'').''; ?>><a href="index.php?Application=PVM&Ref=default&Link=Paid_PV"><i class="fa fa-file-text-o"></i>Paid PV'S&nbsp;&nbsp; </a></li>				
              </ul>
			</div>
		</div>
	</div>

	<div id="printarea">
	<div class="col-md-9 col-md-offset-">
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo $head; ?></div>
			<div class="panel-body">
			<?php include $content; ?>
</div>
		</div>
	</div>
	</div> <!--end of printable area-->
</div>

