<?php
session_start();
if(!isset($_SESSION['UserID']) && !isset($_SESSION['AccessLevel']) && !isset($_SESSION['UsersRealName']) && !isset($_SESSION['DatabaseName'])){
header('location:../IRQ.php');
}
?>
<frameset cols="25%,75%">
<frame src="Inbox_Main.php" name="Menu">
<frame src="" name="Content">
</frameset><noframes></noframes>
