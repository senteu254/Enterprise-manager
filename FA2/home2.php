
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
 <script src="../bootstrap/js/jquery.min.js"></script>
		<div class="container-fluid">
			<div class="row">
				<?php 
				require_once('includes/session.inc');				
				include('includes/SQL_CommonFunctions.inc');				
				include ('FA2/PV_Menu.php');
				
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
				  ?>
		            <div>
     	</div>	
<?php
include $content;
?>
</div>
</div>
<?php
include ('includes/footer.inc');
?>
<!-- end -->