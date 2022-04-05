<?php
/* $Id: session.inc 7056 2015-01-02 00:53:14Z rchacon $*/

if (!isset($PathPrefix)) {
	$PathPrefix='';
}

if (!file_exists($PathPrefix . 'config.php')){
	$RootPath = dirname(htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8'));
	if ($RootPath == '/' OR $RootPath == "\\") {
		$RootPath = '';
	}
	header('Location:' . $RootPath . '/install/index.php');
	exit;
}
include($PathPrefix . 'config.php');

if (isset($dbuser)) { //this gets past an upgrade issue where old versions used lower case variable names
	$DBUser=$dbuser;
	$DBPassword=$dbpassword;
	$DBType=$dbType;
}

if (isset($SessionSavePath)){
	session_save_path($SessionSavePath);
}

if (!isset($SysAdminEmail)) {
	$SysAdminEmail='';
}

ini_set('session.gc_maxlifetime',$SessionLifeTime);

if( !ini_get('safe_mode') ){
	set_time_limit($MaximumExecutionTime);
	ini_set('max_execution_time',$MaximumExecutionTime);
}
session_write_close(); //in case a previous session is not closed
session_start();

include($PathPrefix . 'includes/ConnectDB.inc');
include($PathPrefix . 'includes/DateFunctions.inc');

if (!isset($_SESSION['AttemptsCounter']) OR $AllowDemoMode==true){
	$_SESSION['AttemptsCounter'] = 0;
}

/* iterate through all elements of the $_POST array and DB_escape_string them
to limit possibility for SQL injection attacks and cross scripting attacks
*/

if (isset($_SESSION['DatabaseName'])){
	foreach ($_POST as $PostVariableName => $PostVariableValue) {
		if (gettype($PostVariableValue) != 'array') {
			if(get_magic_quotes_gpc()) {
				$_POST['name'] = stripslashes($_POST['name']);
			}
			$_POST[$PostVariableName] = DB_escape_string($PostVariableValue);
		} else {
			foreach ($PostVariableValue as $PostArrayKey => $PostArrayValue) {
				if(get_magic_quotes_gpc()) {
					$PostVariableValue[$PostArrayKey] = stripslashes($value[$PostArrayKey]);
				}
				$PostVariableValue[$PostArrayKey] = DB_escape_string($PostArrayValue);
			}
		}
	}

	/* iterate through all elements of the $_GET array and DB_escape_string them
	to limit possibility for SQL injection attacks and cross scripting attacks
	*/
	foreach ($_GET as $GetKey => $GetValue) {
		if (gettype($GetValue) != 'array') {
			$_GET[$GetKey] = DB_escape_string($GetValue);
		}
	}
} else { //set SESSION['FormID'] before the a user has even logged in
	$_SESSION['FormID'] = sha1(uniqid(mt_rand(), true));
}

include($PathPrefix . 'includes/LanguageSetup.php');
$FirstLogin = False;

if(basename($_SERVER['SCRIPT_NAME'])=='Logout.php'){
	header('Location: index.php');
} elseif (isset($AllowAnyone)){ /* only do security checks if AllowAnyone is not true */
	$_SESSION['AllowedPageSecurityTokens'] = array();
	$_SESSION['DatabaseName'] = $DefaultDatabase;
	$_SESSION['CompanyName'] = $DefaultDatabase;
	include_once ($PathPrefix . 'includes/ConnectDB_' . $DBType . '.inc');
	include($PathPrefix . 'includes/GetConfig.php');
} else {
	include $PathPrefix . 'includes/UserLogin.php';	/* Login checking and setup */

	if (isset($_POST['UserNameEntryField']) AND isset($_POST['Password'])) {
		$rc = userLogin($_POST['UserNameEntryField'], $_POST['Password'], $SysAdminEmail, $db);
		$FirstLogin = true;
	} elseif (empty($_SESSION['DatabaseName'])) {
		$rc = UL_SHOWLOGIN;
	} else {
		$rc = UL_OK;
	}

	/*  Need to set the theme to make login screen nice */
	$Theme = (isset($_SESSION['Theme'])) ? $_SESSION['Theme'] : $DefaultTheme;
	switch ($rc) {
	case  UL_OK; //user logged in successfully
		include($PathPrefix . 'includes/LanguageSetup.php'); //set up the language
		break;

	case UL_SHOWLOGIN:
		include($PathPrefix . 'includes/Login.php');
		exit;

	case UL_BLOCKED:
		die(include($PathPrefix . 'includes/FailedLogin.php'));

	case  UL_CONFIGERR:
		$Title = _('Account Error Report');
		include($PathPrefix . 'includes/header.inc');
		echo '<br /><br /><br />';
		prnMsg(_('Your user role does not have any access defined for webERP. There is an error in the security setup for this user account'),'error');
		include($PathPrefix . 'includes/footer.inc');
			exit;

	case  UL_NOTVALID:
		$demo_text = '<font size="2" color="red"><b>' .  _('Incorrect user/password combination') . '</b><br /><b>You have '.(5-$_SESSION['AttemptsCounter']).' Attempts Remaining</b></font>';
		die(include($PathPrefix . 'includes/Login.php'));

	case  UL_MAINTENANCE:
		$demo_text = '<font size="3" color="red"><b>' .  _('system maintenance') . '</b></font><br /><b>' . _('webERP is not available right now') . '<br />' . _('during maintenance of the system') . '</b>';
		die(include($PathPrefix . 'includes/Login.php'));

	}
}

//////////////////////////////////////////////////////////////////////////////////////
  function logToFile($filename, $msg)
   { 
   // open file
   $fd = fopen($filename, "a");
   date_default_timezone_set("Africa/Nairobi");
   // append date/time to message
   $str = "[FAILED ATTEMPT ON " . strtoupper(date("l", time())).' '. date("F jS, Y") . "] \r\n" . $msg; 
   // write string
   fwrite($fd, $str . "\r\n");
   // close file
   fclose($fd);
   }

 function logit($attempt, $username, $passwrd)
 {
 date_default_timezone_set("Africa/Nairobi");
   $log  = "-----------------------------------------"."\r\n".
   		   "HOST NAME    : ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."\r\n".
   		   "IP ADDRESS   : ".$_SERVER['REMOTE_ADDR']."\r\n".
   		   "TIME         : ".date("g:i a")."\r\n".
           "ATTEMPT      : ".$attempt."\r\n".
           "USERNAME     : ".$username."\r\n".
           "PASSWORD     : ".$passwrd."\r\n".
           "========================================="."\r\n";
	if (!file_exists("/var/log/LoginAttempts")) {
    mkdir("/var/log/LoginAttempts/", 0775, true);
	}
		logToFile("/var/log/LoginAttempts/".date("F jS, Y").".log", $log);
	}	

	
 if (isset($_GET['LogOut']) && $_GET['LogOut']==True) {
    logOut();
  }
	
	function LX($Name, $Password, $db){
global $debug;
global $PathPrefix;
	
$sql = "SELECT *
				FROM www_users
				WHERE www_users.userid='" . $Name . "'";

		$ErrMsg = _('Could not retrieve user details on login because');
		$debug =1;
        $PasswordXVerified = false;
		$Auth_Result = DB_query($sql,$ErrMsg);

		if (DB_num_rows($Auth_Result) > 0) {
			$myrow = DB_fetch_array($Auth_Result);
			if (VerifyPass($Password,$myrow['password'])) {
				$PasswordXVerified = true;
			} elseif (isset($GLOBALS['CryptFunction'])) {
				switch ($GLOBALS['CryptFunction']) {
					case 'sha1':
						if ($myrow['password'] == sha1($Password)) {
							$PasswordXVerified = true;
						}
						break;
					case 'md5':
						if ($myrow['password'] == md5($Password)) {
							$PasswordXVerified = true;
						}
						break;
					default:
						if ($myrow['password'] == $Password) {
							$PasswordXVerified = true;
						}
				}
				}	
			if($PasswordXVerified){
			if ($myrow['blocked']==1){
			//the account is blocked
				return  UL_BLOCKED;
			}
			$sql = "UPDATE www_users SET OnlineStatus=1 WHERE www_users.userid='" . $_SESSION['UserID'] . "'";
			DB_query($sql);
			return UL_XOK;
			}else{
			
			if (!isset($_SESSION['AttemptsCounter'])) {
				$_SESSION['AttemptsCounter'] = 0;
				return UL_XNO;
			} elseif ($_SESSION['AttemptsCounter'] >= 5 AND isset($Name)) {
				/*User blocked from future accesses until sysadmin releases */
				$sql = "UPDATE www_users
							SET blocked=1
							WHERE www_users.userid='" . $Name . "'";
				$Auth_Result = DB_query($sql);

				return  UL_BLOCKED;
				$_SESSION['err']="Too Many Attempts. Account Blocked!";
			}
			$_SESSION['AttemptsCounter']++;
			logit($_SESSION['AttemptsCounter'],  $Name, $Password);
			}
			}
			return UL_XNO;
	}

	if (isset($_POST['UserN']) AND isset($_POST['PassW'])) {
		$LR = LX($_POST['UserN'], $_POST['PassW'], $db);
	}
	
	switch ($LR) {
	case  UL_XOK: //user logged in successfully
		$_SESSION['sessionX'] = strtotime("now");
		$_SESSION['AttemptsCounter']=0;
		break;

	case UL_XNO:
		$_SESSION['err']="Invalid Password. Please try Again!<br />You have ".(5-$_SESSION['AttemptsCounter'])." Attempts Remaining";
		break;
	
	case UL_BLOCKED:
		die(include($PathPrefix . 'includes/FailedLogin.php'));
		break;
	}
	
function isLogged(){
    if(isset($_SESSION['AccessLevel']) && isset($_SESSION['UserID'])){ # When logged in this variable is set to TRUE
        return TRUE;
    }else{
        return FALSE;
    }
} 
# Log a user Out
function logOut(){
global $db;
$sql = "UPDATE www_users SET OnlineStatus=0 WHERE www_users.userid='" . $_SESSION['UserID'] . "'";
DB_query($sql);
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
    }
    session_unset();
	session_destroy();
	header('Location: index.php');
	exit;
	
} 
	# Session Logout after in activity
function sessionX(){

    $logLength = 1800; # time in seconds :: 1800 = 30 minutes
    $ctime = strtotime("now"); # Create a time from a string
    if(isset($_SESSION['sessionX'])){ 
        # Check if they have exceded the time limit of inactivity
        if(((strtotime("now") - $_SESSION['sessionX']) > $logLength) && isLogged()==TRUE){
            # If exceded the time, log the user out
	$sql = "UPDATE www_users SET OnlineStatus=0 WHERE www_users.userid='" . $_SESSION['UserID'] . "'";
DB_query($sql);
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		echo '<form action="'.htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8').'" method="post">';
		echo '<input type="hidden" name="FormID" value="'.$_SESSION['FormID'].'" />';
		echo '<div id="popDiv" style="z-index: 999;
									width: 100%;
									bottom: auto;
  									min-height: 100%;
									top: 0;
									left: 0;
									display: block;
									position: fixed;				
									background-color: #fff;
									background-color: rgba(255,255,255,0.7);
									filter: alpha(opacity = 50);">';
	
	
	echo '<table style="width: 340px;
						background:#FFFFFF;
						height: 150px;
						position: absolute;
						text-align:center;
						color: #000000;
						/* To align popup window at the center of screen*/
						top: 30%;
						left: 50%;
						margin-top: -100px;
						margin-left: -150px;">
			<tr>';
				echo "<th>" . _('<font color="red">Your Previous session has expired!</font><br /> Please Sign in to continue') . " </th></tr>";
			echo '<tr><td><div style="border:medium outset gray;
									border-radius:19px 19px 19px 19px;
									height:84px;
									margin:20px auto;
									padding:12px;
									background:url(css/webERP.gif) no-repeat scroll center center #FFFFFF;
									width:120px;"></div></td></tr>';
			echo '<tr><td><center><b>'.$_SESSION['UsersRealName'].'</b></center></td></tr>';
			echo '<tr><td><center><b>'.$_SESSION['UserEmail'].'</b></center></td></tr>';
			echo '<tr>
				<td>
				<input type="hidden" name="UserN" required="required" value="'.$_SESSION['UserID'].'" placeholder="'._('User name').'" />
				<center><input style="height:35px; width:85%;" type="password" required="required" name="PassW" placeholder="'. _('Password').'" /></center>
				</td>			
			</tr>';
			echo '<tr><td>';
			echo '<div align="center" style="color:red">'.ucwords($_SESSION['err']).'</div>'; if(!empty($_SESSION['err']))unset($_SESSION['err']);
			echo '</td></tr>';

			echo '<tr>
				<td><center><input style="height:35px; width:85%;" type="submit" name="Login" value="' . _('Login') . '" /></center>';
	echo '</td>
			</tr>
			<tr><td><center><a href="'.htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8').'?LogOut=True" title="Sign in with a different account">Sign in with a different account</a></center></td></tr>
			</table>';
	
	echo '</div>';
	echo '</form>';
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		          
        }else{
            # If they have not exceded the time limit of inactivity, keep them logged in
            $_SESSION['sessionX'] = $ctime;
		 
        }
	}
}
# Run Session logout check
sessionX(); 

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/*If the Code $Version - held in ConnectDB.inc is > than the Database VersionNumber held in config table then do upgrades */
if (strcmp($Version,$_SESSION['VersionNumber'])>0 AND (basename($_SERVER['SCRIPT_NAME'])!='UpgradeDatabase.php')) {
	header('Location: UpgradeDatabase.php');
}


If (isset($_POST['Theme']) AND ($_SESSION['UsersRealName'] == $_POST['RealName'])) {
	$_SESSION['Theme'] = $_POST['Theme'];
	$Theme = $_POST['Theme'];
} elseif (isset($_SESSION['Theme'])) {
	$Theme = $_SESSION['Theme'];
} else {
	$Theme = $DefaultTheme;
	$_SESSION['Theme'] = '$DefaultTheme';
}


if ($_SESSION['HTTPS_Only']==1){
	if ($_SERVER['HTTPS']!='on'){
		prnMsg(_('webERP is configured to allow only secure socket connections. Pages must be called with https://') . ' .....','error');
		exit;
	}
}



// Now check that the user as logged in has access to the page being called. $SecurityGroups is an array of
// arrays defining access for each group of users. These definitions can be modified by a system admin under setup


if (!is_array($_SESSION['AllowedPageSecurityTokens']) AND !isset($AllowAnyone)) {
	$Title = _('Account Error Report');
	include($PathPrefix . 'includes/header.inc');
	echo '<br /><br /><br />';
	prnMsg(_('Security settings have not been defined for your user account. Please advise your system administrator. It could also be that there is a session problem with your PHP web server'),'error');
	include($PathPrefix . 'includes/footer.inc');
	exit;
}

/*The page security variable is now retrieved from the database in GetConfig.php and stored in the $SESSION['PageSecurityArray'] array
 * the key for the array is the script name - the script name is retrieved from the basename ($_SERVER['SCRIPT_NAME'])
 */
if (!isset($PageSecurity)){
//only hardcoded in the UpgradeDatabase script - so old versions that don't have the scripts.pagesecurity field do not choke
	$PageSecurity = $_SESSION['PageSecurityArray'][basename($_SERVER['SCRIPT_NAME'])];
}


//Payment Voucher Start
if (in_array("18", $_SESSION['AllowedPageSecurityTokens'])) {
    $sql = "SELECT * FROM payment_voucher WHERE process_level =1";
	$result = DB_query($sql);
	$non = DB_num_rows($result);
	if($non >0){
		$pv = '<div class="bg">Payment Voucher&nbsp;&nbsp;<span class=label>'.$non.'</span></div>';
		$number ='<span class=label>'.$non.'</span>';
	}else{
		$pv = 'Payment Voucher';	
	}
}
if (in_array("19", $_SESSION['AllowedPageSecurityTokens'])) {
    $sql = "SELECT * FROM payment_voucher WHERE process_level =2";
	$result = DB_query($sql);
	$n = DB_num_rows($result);
	if($n >0){
		$pv = '<div class="bg">Payment Voucher&nbsp;&nbsp;<span class=label>'.$n.'</span></div>';
		$nu ='<span class=label>'.$n.'</span>';
	}else{
		$pv = 'Payment Voucher';	
	}
}
if (in_array("20", $_SESSION['AllowedPageSecurityTokens'])) {
    $sql = "SELECT * FROM payment_voucher WHERE process_level =3";
	$result = DB_query($sql);
	$nou = DB_num_rows($result);
	if($nou >0){
		$pv = '<div class="bg">Payment Voucher&nbsp;&nbsp;<span class=label>'.$nou.'</span></div>';
		$num ='<span class=label>'.$nou.'</span>';
	}else{
		$pv = 'Payment Voucher';	
	}
}
if (in_array("21", $_SESSION['AllowedPageSecurityTokens'])) {
    $sql = "SELECT * FROM payment_voucher WHERE process_level =4";
	$result = DB_query($sql);
	$nos = DB_num_rows($result);
	if($nos >0){
		$pv = '<div class="bg">Payment Voucher&nbsp;&nbsp;<span class=label>'.$nos.'</span></div>';
		$numb ='<span class=label>'.$nos.'</span>';
	}else{
		$pv = 'Payment Voucher';	
	}
}
if (in_array("22", $_SESSION['AllowedPageSecurityTokens'])) {
    $sql = "SELECT * FROM payment_voucher WHERE process_level =5";
	$result = DB_query($sql);
	$nop = DB_num_rows($result);
	if($nop >0){
		$pv = '<div class="bg">Payment Voucher&nbsp;&nbsp;<span class=label>'.$nop.'</span></div>';
		$numbe ='<span class=label>'.$nop.'</span>';
	}else{
		$pv = 'Payment Voucher';	
	}
}
//Payment Voucher End


if (!isset($AllowAnyone)){
	if ((!in_array($PageSecurity, $_SESSION['AllowedPageSecurityTokens']) OR !isset($PageSecurity))) {
		$Title = _('Security Permissions Problem');
		include($PathPrefix . 'includes/header.inc');
		echo '<tr>
				<td class="menu_group_items">
					<table width="100%" class="table_index">
						<tr>
							<td class="menu_group_item">
								<b><font style="size:+1; text-align:center;">' . _('The security settings on your account do not permit you to access this function') . '</font></b>
							</td>
						</tr>
					</table>
				</td>
			</tr>';

		include($PathPrefix . 'includes/footer.inc');
		exit;
	}
}

//$PageSecurity = 9 hard coded for supplier access Supplier access must have just 9 and 0 tokens
if (in_array(9,$_SESSION['AllowedPageSecurityTokens']) AND count($_SESSION['AllowedPageSecurityTokens'])==2){
	$SupplierLogin = 1;
} else {
	$SupplierLogin = 0; //false
}
if (in_array(1,$_SESSION['AllowedPageSecurityTokens']) AND count($_SESSION['AllowedPageSecurityTokens'])==2){
	$CustomerLogin = 1;
} else {
	$CustomerLogin = 0;
}
if (in_array($_SESSION['PageSecurityArray']['WWW_Users.php'], $_SESSION['AllowedPageSecurityTokens'])) { /*System administrator login */
	$debug = 1; //allow debug messages
} else {
	$debug = 0; //don't allow debug messages
}

if ($FirstLogin AND !$SupplierLogin AND !$CustomerLogin AND $_SESSION['ShowDashboard']==1) {
	header('Location: ' . $PathPrefix .'Dashboard.php');
}

function CryptPass( $Password ) {
    if (PHP_VERSION_ID < 50500) {
        $Salt = base64_encode(rand(1,999999999999));
        $Salt = str_replace('+', '.', $Salt);
        $Hash = crypt($Password, '$2y$10$' . $Salt . '$');
    } else {
        $Hash = password_hash($Password,PASSWORD_DEFAULT);
    }
    return $Hash;
 }

 function VerifyPass($Password,$Hash) {
     if(PHP_VERSION_ID < 50500) {
         return (crypt($Password,$Hash)==$Hash);
     } else {
         return password_verify($Password,$Hash);
     }
 }


if (sizeof($_POST) > 0 AND !isset($AllowAnyone)) {
	/*Security check to ensure that the form submitted is originally sourced from webERP with the FormID = $_SESSION['FormID'] - which is set before the first login*/
	if (!isset($_POST['FormID']) OR ($_POST['FormID'] != $_SESSION['FormID'])) {
		$Title = _('Error in form verification');
		include('includes/header.inc');
		prnMsg(_('This form was not submitted with a correct ID') , 'error');
		include('includes/footer.inc');
		exit;
	}
}
?>