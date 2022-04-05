
<?php
$ViewTopic = 'Inventory';
$BookMark = 'CreateRequest';
$Title = 'Requisition Panel';
session_start();
if(!isset($_SESSION['UserID']) && !isset($_SESSION['AccessLevel']) && !isset($_SESSION['UsersRealName']) && !isset($_SESSION['DatabaseName'])){
header('location:index.php');
}
							if(isset($_GET['New']) && $_GET['New'] == 'Document'){
							require_once('includes/session.inc');
							unset($_SESSION['Document_id']);
							unset($_SESSION['Doc_Store']);
							if(isset($_GET['Doc']) && $_GET['Doc'] == 4){
							$_SESSION['Doc_Store'] = 4;
							}
							$_SESSION['Document_id'] = 1;
							}elseif(!isset($_SESSION['Document_id'])){
							header('location:index.php');
							}
							
							include 'IRQ/inc/db_config.php';
							$view = (isset($_GET['Ref']) && $_GET['Ref'] != '') ? $_GET['Ref'] : '';
								switch ($view) {
								case 'Create-Request' :
									$title="Create an Internal Request";
									$subtitle ='Create New Internal Stock Request';
									$content=$path.'Create_Stock_Request.php';
									//$content=$path.'Create_Transport_Request.php';
									$class1= 'class="active"';	
									break;
							
								case 'Follow-Up' :
									$title="Follow-Up Request";
									$subtitle ='Follow Up Document';
									$content=$path.'Follow_Up.php';	
									$class2= 'class="active"';	
									break;
									
								case 'Manage-Tasks' :
									$title="Manage Approval Levels";
									$subtitle ='Create Work Flow for Each Document';
									$content=$path.'LevelManager.php';		
									$class3= 'class="active"';	
									break;
							
								case 'Review-Tasks' :
									$title="Task Review";
									$subtitle ='Create New Internal Stock Request';
									$content=$path.'Follow_Up.php';		
									$class4= 'class="active"';	
									break;
								
								case 'Inbox' :
									$title="Incoming Requests";
									$subtitle ='List of Requests waiting Your approval';
									$content1='<iframe style="border:none; border-top:solid; background:#f5f5dc;" src="'.$path.'inbox.php" height="610px" width="100%"></iframe>';	
									$class5= 'class="active"';	
									break;
								
								case 'Draft' :
									$title="Saved Request";
									$subtitle ='Created and saved Internal Stock Request';
									$content1='<iframe style="border:none; border-top:solid; background:#f5f5dc;" src="'.$path.'Draft.php" height="610px" width="100%"></iframe>';
									$class6= 'class="active"';	
									break;
									
								case 'Completed' :
									$title="Completed Request";
									$subtitle ='List of completed Internal Stock Request';
									$content1='<iframe style="border:none; border-top:solid; background:#f5f5dc;" src="'.$path.'Completed.php" height="610px" width="100%"></iframe>';
									$class7= 'class="active"';	
									break;
																		
								default :
									$title="Dashboard";	
									$content =$path.'Dashboard.php';
									$subtitle ='This is a quick overview of some features';
									$class= 'class="active"';		
							}
							if(isset($_SESSION['Doc_Store'])){
							$query="SELECT * FROM irq_request z 
							INNER JOIN irq_stockrequest a on z.requestid = a.dispatchid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN locations g ON a.loccode = g.loccode
							WHERE draft=0 AND closed=0 AND Unread=0 AND z.doc_id='". $_SESSION['Doc_Store'] ."' AND CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' WHEN d.userid ='ISSUE' THEN g.authoriser='".$_SESSION['UserID']."' ELSE d.userid='".$_SESSION['UserID']."' END";
							$result= mysqli_query($conn,$query) or die (mysqli_error($conn));
							$inum=mysqli_num_rows($result);
							$query="SELECT * FROM irq_request a
							INNER JOIN irq_stockrequest b ON b.dispatchid = a.requestid
							INNER JOIN irq_documents c ON a.doc_id = c.doc_id
							INNER JOIN departments d ON b.departmentid = d.departmentid
							WHERE draft=1 AND closed=0 AND initiator = '".$_SESSION['UserID']."' AND a.doc_id='". $_SESSION['Doc_Store'] ."'";
							$result= mysqli_query($conn,$query) or die (mysqli_error($conn));
							$dnum=mysqli_num_rows($result);
							$query="SELECT * FROM irq_request z 
							INNER JOIN irq_stockrequest a  on a.dispatchid = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id AND final_approver=1
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN locations g ON a.loccode = g.loccode
							WHERE closed=1 AND z.doc_id='".$_SESSION['Doc_Store']."' AND CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' WHEN d.userid ='ISSUE' THEN g.authoriser='".$_SESSION['UserID']."' ELSE d.userid='".$_SESSION['UserID']."' END";
							$result= mysqli_query($conn,$query) or die (mysqli_error($conn));
							$cnum=mysqli_num_rows($result);
							}else{
							$query="SELECT * FROM irq_request z
							INNER JOIN irq_stockrequest a on z.requestid = a.dispatchid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN locations g ON a.loccode = g.loccode
							WHERE draft=0 AND closed=0 AND Unread=0 AND z.doc_id='". $_SESSION['Document_id'] ."' AND CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' WHEN d.userid ='ISSUE' THEN g.authoriser='".$_SESSION['UserID']."' ELSE d.userid='".$_SESSION['UserID']."' END";
							$result= mysqli_query($conn,$query) or die (mysqli_error($conn));
							$inum=mysqli_num_rows($result);
							$query="SELECT * FROM irq_request a
							INNER JOIN irq_stockrequest b ON b.dispatchid = a.requestid
							INNER JOIN irq_documents c ON a.doc_id = c.doc_id
							INNER JOIN departments d ON b.departmentid = d.departmentid
							WHERE draft=1 AND closed=0 AND initiator = '".$_SESSION['UserID']."' AND a.doc_id='". $_SESSION['Document_id'] ."'";
							$result= mysqli_query($conn,$query) or die (mysqli_error($conn));
							$dnum=mysqli_num_rows($result);
							$query="SELECT * FROM irq_request z 
							INNER JOIN irq_stockrequest a  on a.dispatchid = z.requestid 
							INNER JOIN irq_authorize_state b on z.requestid = b.requisitionid 
							INNER JOIN irq_levels c ON b.level = c.level_id AND final_approver=1
							INNER JOIN irq_approvers d ON c.approver_id = d.approver_id
							INNER JOIN departments e ON a.departmentid = e.departmentid
							INNER JOIN irq_documents f ON z.doc_id = f.doc_id
							INNER JOIN locations g ON a.loccode = g.loccode
							WHERE closed=1 AND z.doc_id='".$_SESSION['Document_id']."' AND CASE WHEN d.userid ='HOD' THEN e.authoriser='".$_SESSION['UserID']."' WHEN d.userid ='ISSUE' THEN g.authoriser='".$_SESSION['UserID']."' ELSE d.userid='".$_SESSION['UserID']."' END GROUP BY z.requestid";
							$result= mysqli_query($conn,$query) or die (mysqli_error($conn));
							$cnum=mysqli_num_rows($result);
							}
							$Lang = $_SESSION['Lang'];
							$Theme = $_SESSION['Theme'];
							include('includes/header.inc');
							?>

<!DOCTYPE html>
<head>
<style type="text/css">
.del{float:right;}
.FB{float:right; margin-top:14px; margin-right:2px; }
.notes_id{position:fixed;margin-left:15px;bottom: 2em;}
.note_box{width:200px; height:80px; background:#e2e7ee;
border:1px solid #9dabc9;
-webkit-border-radius: 4px;
margin-top:2px;
-moz-border-radius: 4px;
border-radius: 4px;}
.note_box img{margin-left:4px; margin-top:-1px;}
.add{width:140px; 
font-family:Tahoma; margin-top:-1px; font-size:14px;
padding:4px; margin-left:48px; }
.com_im{ margin-right:7px;}
.time{color:#7f7f7f;}
.close{float:right; margin-right:5px; cursor:pointer;}

</style>

	<meta charset="utf-8">
	<title>Requisition Panel</title>
	<link media="all" rel="stylesheet" type="text/css" href="<?php echo $path;?>css/all.css" />
	
	<!--[if lt IE 9]><link rel="stylesheet" type="text/css" href="css/ie.css" /><![endif]-->
<script src="<?php echo $path;?>js/ajax.js"></script>
     <script type="text/javascript" src="<?php echo $path;?>js/angular.min.js"></script>
    <script type="text/javascript" src="<?php echo $path;?>js/desktop-notify-min.js"></script>
    <script>
        function NotificationCenter($scope) {
            var permissionLevels = {};
            permissionLevels[notify.PERMISSION_GRANTED] = 0;
            permissionLevels[notify.PERMISSION_DEFAULT] = 1;
            permissionLevels[notify.PERMISSION_DENIED] = 2;

            $scope.isSupported = notify.isSupported;
            $scope.permissionLevel = permissionLevels[notify.permissionLevel()];

            $scope.getClassName = function() {
                if ($scope.permissionLevel === 0) {
                    return "allowed"
                } else if ($scope.permissionLevel === 1) {
                    return "default"
                } else {
                    return "denied"
                }
            }

            $scope.callback = function() {
                console.log("test");
            }

            $scope.requestPermissions = function() {
                notify.requestPermission(function() {
                    $scope.$apply($scope.permissionLevel = permissionLevels[notify.permissionLevel()]);
                })
            }
        }

        function show() {
            notify.createNotification("Notification", {body:'<?php echo $inum; ?> New Document Waiting Your Autoritation', icon: "<?php echo $path;?>/images/alert.ico"})
        }
    </script>
<script>
    $(document).ready(		
					
            function() {
                setInterval(function() {
                    $('#num').load('<?php echo $path;?>InboxCount.php?Do<?php echo $_SESSION['Document_id']; ?>');	
                }, 5000);
            });
			
	$(document).ready(
            function() {
						window.isActive = true;
						$(window).focus(function() { this.isActive = true; });
						$(window).blur(function() { this.isActive = false; });
                setInterval(function() {
				$.ajax({
				  url: '<?php echo $path;?>Ajax_Notify.php',
				  dataType: 'json',
				  success: function(){
					$('<audio  id="sound" ><source src="<?php echo $path;?>sound/notify.ogg" type="audio/ogg"><source src="<?php echo $path;?>sound/notify.mp3" type="audio/mpeg"><source src="<?php echo $path;?>sound/notify.wav" type="audio/wav"></audio>').appendTo('body');
					if (window.isActive) {
						 $('#sound')[0].play();
						 $(".notes_id").prepend('<div class="note_box">'+
						'<img src="<?php echo $path;?>/images/Doc.png" width="40" height="40" class="pro" align="left">'+
						'<img src="<?php echo $path;?>/images/close1.png" width="17" height="17" align="right" class="close">'+
						'<div class="add" ><b>Notification:</b>'+'<br />'+'<span style="color:#3b5998; font-size:11px;">New Document Forwarded for your authoritation<span>'+
						'</div>'+
						'<div class="com_im"><img src="<?php echo $path;?>/images/nW-U1RPGDdA.png" width="20" height="20" align="right">'+
						'<div class="time">a few seconds ago</div></div>'+
						'</div>');
					   }else{
						  $('#sound')[0].play();
					      show();
						  }
					}
				});
					
                }, 8000);
				setInterval(function() {
				 $('.note_box').fadeOut('slow');
				 }, 7000);
            });
			
		$(".close").click(function() 
			{
			$('.note_box').fadeOut('slow');
			});
</script>
</head>
<body>
<div class="notes_id"> </div>
	<div id="wrapper">
		<div id="content">
			<div class="c1">
				<div class="controls">
					<nav class="links">
						<ul>
							<li><a href="<?php echo $_SERVER['PHP_SELF'].'?Ref=Inbox' ?>" class="ico1">Inbox <div id="num"><?php if($inum>0){echo '<span class="num">'. $inum .'</span>';} ?></div></a></li>
							<li><a href="<?php echo $_SERVER['PHP_SELF'].'?Ref=Draft' ?>" class="ico3">Draft <?php if($dnum>0){echo '<span class="num">'. $dnum .'</span>';} ?></a></li>
							<li><a href="<?php echo $_SERVER['PHP_SELF'].'?Ref=Completed' ?>" class="ico2">Completed <?php if($cnum>0){echo '<span class="num">'. $cnum .'</span>';} ?></a></li>
						</ul>
					</nav>
					
				</div>
				<div class="tabs">
					
					<div id="tab-2" class="tab">
						<article>
							<div class="text-section"><div align="left">
								<h1><?php echo $title; ?></h1>
								<p><?php echo $subtitle; ?></p>
								</div>
			<?php error_reporting( error_reporting() & ~E_NOTICE ); echo '<div align="center"><div style="width:70%; text-align:left;">'.ucwords($_SESSION['msg']).'</div></div>'; if(!empty($_SESSION['msg']))unset($_SESSION['msg']);?>
								
							</div>
							<div align="center"><?php if(isset($content1)){ echo $content1;}else{include_once $content;} ?></div>
						</article>
					</div>
					
				</div>
			</div>
		</div>
		<aside id="sidebar">
			<strong class="logo"><a href="index.php">Main Menu</a></strong>
			<ul class="tabset buttons">
				<li <?php if(!empty($class)){echo $class;}?>>
					<a href="<?php echo $_SERVER['PHP_SELF'].'?Ref=Dashboard' ?>" class="ico1"><span>Dashboard</span><em></em></a>
					<span class="tooltip"><span>Dashboard</span></span>
				</li>
				<li <?php if(!empty($class1)){echo $class1;}?>>
					<a href="<?php echo $_SERVER['PHP_SELF'].'?Ref=Create-Request&New=Yes' ?>" class="ico6"><span>Create Request</span><em></em></a>
					<span class="tooltip"><span>Create Request</span></span>
				</li>
				<li <?php if(!empty($class2)){echo $class2;}?>>
					<a href="<?php echo $_SERVER['PHP_SELF'].'?Ref=Follow-Up' ?>" class="ico3"><span>Follow-Up Requests</span><em></em></a>
					<span class="tooltip"><span>Follow-Up Requests</span></span>
				</li>
				<?php if($_SESSION['CanEditFlow'] == 1){ ?>
				<li <?php if(!empty($class3)){echo $class3;}?>>
					<a href="<?php echo $_SERVER['PHP_SELF'].'?Ref=Manage-Tasks&Tab=1' ?>" class="ico4"><span>Manage Tasks</span><em></em></a>
					<span class="tooltip"><span>Manage Tasks</span></span>
				</li>
				<?php } ?>
				
			</ul>
			<span class="shadow"></span>
		</aside>
	</div>
	<div class="notes_id"> </div>
</body>
</html>