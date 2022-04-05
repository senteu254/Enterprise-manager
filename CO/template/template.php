
<head>
	<script>
function toggle(source) {
  checkboxes = document.getElementsByName('users[]');
  for(var i=0, n=checkboxes.length;i<n;i++) {
    checkboxes[i].checked = source.checked;
  }
}
</script>
<script src="CO/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script language="CO/javascript" src="CO/js/users.js" type="text/javascript"></script>
<script type="CO/text/javascript" src="CO/js/jconfirmaction.jquery.js"></script>
<script type="CO/text/javascript">
	
	$(document).ready(function() {
		$('.ask').jConfirmAction();
	});
	
</script>
 <script type="CO/text/javascript">

/*--This JavaScript method for Print command--*/

    function PrintDoc() {

        var toPrint = document.getElementById('printarea');

        var popupWin = window.open('', '_blank', 'width=700,height=800,location=no,left=5px');

        popupWin.document.open();

        popupWin.document.write('<html><link rel="stylesheet" type="text/css" href="css/print.css" /></head><body onload="window.print()">')

        popupWin.document.write(toPrint.innerHTML);

        popupWin.document.write('</html>');

        popupWin.document.close();

    }

/*--This JavaScript method for Print Preview command--*/

</script>
<link href="CO/facebox/src/facebox.css" media="screen" rel="stylesheet" type="text/css" />
			<link href="CO/css/example.css" media="screen" rel="stylesheet" type="text/css" />
			<script src="CO/facebox/src/facebox.js" type="text/javascript"></script>
			<script type="CO/text/javascript">
				jQuery(document).ready(function($) {
					$(" a[rel*=facebox]" ).facebox({
						loadingImage : "facebox/src/loading.gif" ,
						closeImage   : "facebox/src/closelabel.png" 
					})
				})
	</script>
<?
//require_once('inc/config.php');
$strSQL1 = "SELECT * FROM www_users WHERE userid='".$_SESSION['UserID']."'";
$objQuery1 = DB_query($strSQL1);
$Num_Rows1 = DB_num_rows($objQuery1);
while($row=DB_fetch_array($objQuery1)){
$compname=$row['realname'];
	}
?>
<script>
function Confirm() {
    confirm("Are You Sure?");
}
</script>
	<link rel="stylesheet" href="CO/css/style.css" type="text/css" media="all" />

<body>
<!-- Header -->
<div id="header">
	<div class="shell">
		<!-- Logo + Top Nav -->
		<div style="color:blue;">
		<div id="top">
			<h1><a href="#"><?php //echo $compname; ?></a></h1>
			<div align="right";>
				<strong><?php echo $compname; ?></strong>
				<span>|</span>
				<a href="<?php echo $mainlink; ?>Type">Setup</a>
				<span></span>
				<!--<a href="<?php //echo $mainlink; ?>Users">Users Settings</a>-->
				<span>|</span>
			</div>
		</div>
	</div>
		<!-- End Logo + Top Nav -->

		<!-- Main Nav -->
		<div id="navigation" style="color:#FFFFFF;margin-top:3%;">
			<ul>
			    <li><a <?php if(!empty($class)){echo $class;}?> href="<?php echo $mainlink; ?>Dashboard"><span>Dashboard</span></a></li>
			    <!--<li><a <?php //if(!empty($class2)){echo $class2;}?> href="<?php //echo $mainlink; ?>Commitee"><span>Commitee</span></a></li>-->
			    <li><a <?php if(!empty($class3)){echo $class3;}?> href="<?php echo $mainlink; ?>Contract"><span>Contracts</span></a></li>
				<li><a <?php if(!empty($class4)){echo $class4;}?> href="<?php echo $mainlink; ?>Assign"><span>Assign Contracts</span></a></li>
				<li><a <?php if(!empty($class5)){echo $class5;}?> href="<?php echo $mainlink; ?>Payment"><span>Initiate Contract Payment</span></a></li>
				<li><a <?php if(!empty($class6)){echo $class6;}?> href="<?php echo $mainlink; ?>Payment_flow"><span>Contracts on Payment Flow</span></a></li>
			</ul>
		</div>
		<!-- End Main Nav -->
	</div>
</div>

<!-- End Header -->

<!-- Container -->
<div id="container">
	<div class="shell">
		
		<!-- Small Nav -->
		<div class="small-nav" style="margin-top:-1%;">
			<a href="#">Dashboard</a>
			<span>&gt;</span>
			<?php echo $title;?>
		</div>
		<!-- End Small Nav -->
		
		<!-- Message OK -->	
		<?php error_reporting( error_reporting() & ~E_NOTICE ); session_start(); echo '<div class="msg msg-ok">'.ucwords($_SESSION['msg']).'</div>'; if(!empty($_SESSION['msg']))unset($_SESSION['msg']);?>	
		<!-- End Message OK -->	
		
		<!-- Message Error -->	
		<?php error_reporting( error_reporting() & ~E_NOTICE ); session_start(); echo '<div class="msg msg-error">'.ucwords($_SESSION['err_msg']).'</div>'; if(!empty($_SESSION['err_msg']))unset($_SESSION['err_msg']);?>
		<!-- End Message Error -->
		<br />
		<!-- Main -->
		<div id="main" style="margin-left:-40px;">
			<div class="cl">&nbsp;</div>
			
			<!-- Content -->
			<div id="content" style="margin-top:-4%;">
				
				<!-- Box -->
				<?php include $content; ?> 
				<!-- End Box -->
				

			</div>
			<!-- End Content -->
			
			<!-- Sidebar -->
			<?php
			if(isset($sidemenu)){
			include $sidemenu;
			}else{
			echo '';
			}
			?>
			<!-- End Sidebar -->
			
			<div class="cl">&nbsp;</div>			
		</div>
		<!-- Main -->
	</div>
</div>
<!-- End Container -->

<!-- Footer -->
<div id="footer">
	<div class="shell">
		<span class="left"><?php include('includes/footer.inc');?> </span>
		<span class="right">
			
		</span>
	</div>
</div>
<!-- End Footer -->
	
</body>
</html>
