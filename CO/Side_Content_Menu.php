<?php
ob_start();
    
    if(!isset($_SESSION['Username'])){
         header("Location: login.php");
    }
?>
<div id="sidebar">
				
				<!-- Box -->
				<div class="box">
					
					<!-- Box Head -->
					<div class="box-head">
						<h2>Management</h2>
					</div>
					<!-- End Box Head-->
					<?php
					if(isset($_GET['Page']) && $_GET['Page'] == 'History'){
					?>
					<div class="box-content">
						<a href="#" onClick="PrintDoc()" class="add-button"><span style="width:140px;">Print History</span></a>
						<div class="cl">&nbsp;</div></div>
						<?php
						}else{
						
					if(isset($_GET['Page']) && $_GET['Page'] == 'Apply'){
					?>
					<div class="box-content">
						<a href="dashboard.php?Page=History&id=<?=mysqli_result($objQuery,$i,"LC_No");?>" class="add-button"><span style="width:140px;">Allottment History</span></a>
						<div class="cl">&nbsp;</div></div>
						<?php
						}
						?>
					
					<div class="box-content">
						<a href="dashboard.php?Page=Bank" class="add-button"><span style="width:140px;">Add Bank</span></a>
						<div class="cl">&nbsp;</div></div>
					<div class="box-content">
						<a href="dashboard.php?Page=Management" class="add-button"><span style="width:140px;">LC Management</span></a>
						<div class="cl">&nbsp;</div></div>
					
					<div class="box-content">
						<a href="dashboard.php?Page=Allocation" class="add-button"><span style="width:140px;">Payment Allocation</span></a>
						<div class="cl">&nbsp;</div></div>

						<?php }?>
					</div>
				</div>
				<!-- End Box -->
			</div>

