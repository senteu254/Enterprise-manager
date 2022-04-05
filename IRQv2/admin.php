
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
 <script src="../bootstrap/js/jquery.min.js"></script>
		<div class="container-fluid">
			<div class="row">
				<?php 
				require_once('includes/session.inc');
				include('includes/SQL_CommonFunctions.inc');
				 ?>
			</div>
		</div>

<?php
include ('IRQv2/Switch_Board.php');

include ('includes/footer.inc');
?>
