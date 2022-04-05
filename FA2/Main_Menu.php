<link rel="stylesheet" href="FA2/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="FA2/iCheck/flat/blue.css">
<?php	
echo '<link href="' . $RootPath . '/css/'. $_SESSION['Theme'] .'/default.css" rel="stylesheet" type="text/css" />';
echo '<script type="text/javascript" src = "js/jquery-1.9.1.js"></script>';
                $path='FA2/';
				$view = (isset($_GET['Link']) && $_GET['Link'] != '') ? $_GET['Link'] : '';
								switch ($view) {
								case 'Farm Service Records' :
									$content=$path.'main.php';	
									$head='Farm Service Records';
									break;									
								case 'kofc_service' :
									$content=$path.'kofc_service.php';	
									$head='KOFC Farm Service';
									break;
								case 'contract_service' :
									$content=$path.'contract_service.php';	
									$head='Contract Farm Service';
									break;
								case 'View_Farm_Description' :
									$content=$path.'ViewFarmDescription.php';	
									$head='View Farm Description';
									break;
								case 'Edit_Farm_Description_Item' :
									$content=$path.'EditFarmdescriptionItem.php';	
									$head='Edit Farm Description';
									break;
								case 'View_Farm_Fields' :
									$content=$path.'ViewFarmFields.php';	
									$head='View Farm Fields';
									break;					
					            default :
									$content =$path.'main.php';
									$head='Home';
									break;
							}
	function pagination($query,$per_page=10,$page=1,$url='?'){   
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
}
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
<ul style="width:100%;" >
<li class="dropdown" style="width:100%;" >
	<a href="#" class="dropdown-toggle" data-toggle="dropdown"><button style="width:100%;" class="btn btn-primary"><i class="fa fa-pencil-square-o"></i> Crop Production <span class="caret"></span></button></a>
		<ul class="dropdown-menu" role="menu">
		<?php 
		echo '<li><a href="index.php?Application=FA2&Ref=default&Link=kofc_service&New"><i class="fa fa-file-text-o"></i>KOFC Service &nbsp;&nbsp;</a></li>';			
		echo '<li><a href="index.php?Application=FA2&Ref=default&Link=contract_service&New"><i class="fa fa-file-text-o"></i>Contract Services &nbsp;&nbsp;</a></li>';?>
	</ul>
</li>
</ul>
 <?php }else echo'';
 ?>
	<div class="panel panel-default">
		<div class="panel-heading">Folders</div>
			<div class="panel-body">			
			<ul class="nav nav-pills nav-stacked">
		 <li <?php echo ''.($_GET['Link']=='View_Farm_Description'? 'class="active"':'').''; ?>><a href="index.php?Application=FA2&Ref=default&Link=View_Farm_Description"><i class="fa fa-envelope-o"></i> View Farm Description </a></li>
		 <li <?php echo ''.($_GET['Link']=='View_Farm_Fields'? 'class="active"':'').''; ?>><a href="index.php?Application=FA2&Ref=default&Link=View_Farm_Fields"><i class="fa fa-envelope-o"></i> View Farm fields</a></li>
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
<script type="text/javascript"  src="js/jquery.dataTables.min.js"></script>
<script>
$(function(){
$("#myTable").dataTable();
});
$(function(){
$("#myTable2").dataTable();
});
</script>	