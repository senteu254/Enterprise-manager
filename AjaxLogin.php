<?php
include ('includes/session.inc');

	$LRx = LX($_POST['user'], $_POST['key'], $db);

	switch ($LRx) {
	case  UL_XOK: //user logged in successfully
		return $_SESSION['sessionX'] = strtotime("now");
		return $_SESSION['AttemptsCounter']=0;
		include ('includes/session.inc');
		break;

	case UL_XNO:
		echo "Invalid Password. Please try Again!<br />You have ".(5-$_SESSION['AttemptsCounter'])." Attempts Remaining";
		break;
	
	case UL_BLOCKED:
		echo 'Too many Attempts. Your Account has been blocked.';
		break;
	}


?>