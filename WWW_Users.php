<?php
/* $Id: WWW_Users.php 7067 2015-01-05 03:41:43Z rchacon $*/

if (isset($_POST['UserID']) AND isset($_POST['ID'])){
	if ($_POST['UserID'] == $_POST['ID']) {
		$_POST['Language'] = $_POST['UserLanguage'];
	}
}
include('includes/session.inc');

$ModuleList = array(_('Commercial Services'),
					_('Accounts Receivables'),
					_('Procurement'),
					_('Accounts Payables'),
					_('Stores & Warehouse'),
					_('Farm'),
					_('Requisition'),
					_('MRS'),
					_('Human Resource'),
					_('Security'),
					_('Manufacturing'),
					_('Finance'),
					_('Maintenance'),
					_('Petty Cash'),
					_('Payment Voucher'),
					_('Setup'),
					_('Utilities'),
					_('Quality Assurance'),
					_('Contract'));

$PDFLanguages = array(_('Latin Western Languages'),
						_('Eastern European Russian Japanese Korean Vietnamese Hebrew Arabic Thai'),
						_('Chinese'),
						_('Free Serif'));

$Title = _('Users Maintenance');// Screen identificator.
$ViewTopic= 'GettingStarted';// Filename's id in ManualContents.php's TOC.
$BookMark = 'UserMaintenance';// Anchor's id in the manual's html document.
include('includes/header.inc');
echo '<p class="page_title_text"><img alt="" src="'.$RootPath.'/css/'.$Theme.
	'/images/group_add.png" title="' .// Title icon.
	_('Search') . '" />' .// Icon title.
	$Title . '</p>';// Page title.

include('includes/SQL_CommonFunctions.inc');

echo '<br />';// Extra line after page_title_text.

// Make an array of the security roles
$sql = "SELECT secroleid,
				secrolename
		FROM securityroles
		ORDER BY secrolename";

$Sec_Result = DB_query($sql);
$SecurityRoles = array();
// Now load it into an a ray using Key/Value pairs
while( $Sec_row = DB_fetch_row($Sec_Result) ) {
	$SecurityRoles[$Sec_row[0]] = $Sec_row[1];
}
DB_free_result($Sec_Result);

if (isset($_GET['SelectedUser'])){
	$SelectedUser = $_GET['SelectedUser'];
	$SelectedEmployee = true;
} elseif (isset($_POST['SelectedUser'])){
	$SelectedUser = $_POST['SelectedUser'];
	$SelectedEmployee = true;
}

if (isset($_GET['NewUser']) && $_GET['NewUser']=='Yes'){
	$_SESSION['NewUser'] = $_GET['NewUser'];
}elseif(isset($_GET['NewUser']) && $_GET['NewUser']!='Yes'){
unset($_SESSION['NewUser']);
}

if (isset($_GET['SelectedEmployee'])){
	$SelectedEmployee = $_GET['SelectedEmployee'];
} elseif (isset($_POST['SelectedEmployee'])){
	$SelectedEmployee = $_POST['SelectedEmployee'];
}


if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible
	if (mb_strlen($_POST['UserID'])<4){
		$InputError = 1;
		prnMsg(_('The user ID entered must be at least 4 characters long'),'error');
	} elseif (ContainsIlLegalCharacters($_POST['UserID'])) {
		$InputError = 1;
		prnMsg(_('User names cannot contain any of the following characters') . " - ' &amp; + \" \\ " . _('or a space'),'error');
	} elseif (mb_strlen($_POST['Password'])<5){
		if (!$SelectedUser){
			$InputError = 1;
			prnMsg(_('The password entered must be at least 5 characters long'),'error');
		}
	} elseif (mb_strstr($_POST['Password'],$_POST['UserID'])!= False){
		$InputError = 1;
		prnMsg(_('The password cannot contain the user id'),'error');
	} elseif ($_POST['Password']!= $_POST['RePassword']){
		$InputError = 1;
		prnMsg(_('The Passwords do not match!'),'error');
	} elseif ((mb_strlen($_POST['Cust'])>0)
				AND (mb_strlen($_POST['BranchCode'])==0)) {
		$InputError = 1;
		prnMsg(_('If you enter a Customer Code you must also enter a Branch Code valid for this Customer'),'error');
	} elseif ($AllowDemoMode AND $_POST['UserID'] == 'admin') {
		prnMsg(_('The demonstration user called demo cannot be modified.'),'error');
		$InputError = 1;
	}

	if (!isset($SelectedUser)){
		/* check to ensure the user id is not already entered */
		$result = DB_query("SELECT userid FROM www_users WHERE userid='" . $_POST['UserID'] . "'");
		if (DB_num_rows($result)==1){
			$InputError =1;
			prnMsg(_('The user ID') . ' ' . $_POST['UserID'] . ' ' . _('already exists and cannot be used again'),'error');
		}
	}

	if ((mb_strlen($_POST['BranchCode'])>0) AND ($InputError !=1)) {
		// check that the entered branch is valid for the customer code
		$sql = "SELECT custbranch.debtorno
				FROM custbranch
				WHERE custbranch.debtorno='" . $_POST['Cust'] . "'
				AND custbranch.branchcode='" . $_POST['BranchCode'] . "'";

		$ErrMsg = _('The check on validity of the customer code and branch failed because');
		$DbgMsg = _('The SQL that was used to check the customer code and branch was');
		$result = DB_query($sql,$ErrMsg,$DbgMsg);

		if (DB_num_rows($result)==0){
			prnMsg(_('The entered Branch Code is not valid for the entered Customer Code'),'error');
			$InputError = 1;
		}
	}

	/* Make a comma separated list of modules allowed ready to update the database*/
	$i=0;
	$ModulesAllowed = '';
	while ($i < count($ModuleList)){
		$FormVbl = 'Module_' . $i;
		$ModulesAllowed .= $_POST[($FormVbl)] . ',';
		$i++;
	}
	$_POST['ModulesAllowed']= $ModulesAllowed;

	if (isset($SelectedUser) AND $InputError !=1) {

/*SelectedUser could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/

		if (!isset($_POST['Cust'])
			OR $_POST['Cust']==NULL
			OR $_POST['Cust']==''){

			$_POST['Cust']='';
			$_POST['BranchCode']='';
		}
		$UpdatePassword = '';
		if ($_POST['Password'] != ''){
			$UpdatePassword = "password='" . CryptPass($_POST['Password']) . "',";
		}

		$sql = "UPDATE www_users SET realname='" . $_POST['RealName'] . "',
						customerid='" . $_POST['Cust'] ."',
						phone='" . $_POST['Phone'] ."',
						email='" . $_POST['Email'] ."',
						" . $UpdatePassword . "
						branchcode='" . $_POST['BranchCode'] . "',
						supplierid='" . $_POST['SupplierID'] . "',
						salesman='" . $_POST['Salesman'] . "',
						pagesize='" . $_POST['PageSize'] . "',
						fullaccess='" . $_POST['Access'] . "',
						cancreatetender='" . $_POST['CanCreateTender'] . "',
						theme='" . $_POST['Theme'] . "',
						language ='" . $_POST['UserLanguage'] . "',
						defaultlocation='" . $_POST['DefaultLocation'] ."',
						modulesallowed='" . $ModulesAllowed . "',
						showdashboard='" . $_POST['ShowDashboard'] . "',
						blocked='" . $_POST['Blocked'] . "',
						pdflanguage='" . $_POST['PDFLanguage'] . "',
						department='" . $_POST['Department'] . "'
					WHERE userid = '". $SelectedUser . "'";

		prnMsg( _('The selected user record has been updated'), 'success' );
	} elseif ($InputError !=1) {

		$sql = "INSERT INTO www_users (userid,
						emp_id,
						realname,
						customerid,
						branchcode,
						supplierid,
						salesman,
						password,
						phone,
						email,
						pagesize,
						fullaccess,
						cancreatetender,
						defaultlocation,
						modulesallowed,
						displayrecordsmax,
						theme,
						language,
						pdflanguage,
						department)
					VALUES ('" . $_POST['UserID'] . "',
						'" . $_POST['SelectedEmployee'] ."',
						'" . $_POST['RealName'] ."',
						'" . $_POST['Cust'] ."',
						'" . $_POST['BranchCode'] ."',
						'" . $_POST['SupplierID'] ."',
						'" . $_POST['Salesman'] . "',
						'" . CryptPass($_POST['Password']) ."',
						'" . $_POST['Phone'] . "',
						'" . $_POST['Email'] ."',
						'" . $_POST['PageSize'] ."',
						'" . $_POST['Access'] . "',
						'" . $_POST['CanCreateTender'] . "',
						'" . $_POST['DefaultLocation'] ."',
						'" . $ModulesAllowed . "',
						'" . $_SESSION['DefaultDisplayRecordsMax'] . "',
						'" . $_POST['Theme'] . "',
						'". $_POST['UserLanguage'] ."',
						'" . $_POST['PDFLanguage'] . "',
						'" . $_POST['Department'] . "')";
		
		$LocationSql = "INSERT INTO locationusers (loccode,
													userid,
													canview,
													canupd
												) VALUES (
													'" . $_POST['DefaultLocation'] . "',
													'" . $_POST['UserID'] . "',
													1,
													1
												)";
		$ErrMsg = _('The default user locations could not be processed because');
		$DbgMsg = _('The SQL that was used to update the user locations and failed was');
		$Result = DB_query($LocationSql, $ErrMsg, $DbgMsg);
		prnMsg( _('A new user record has been inserted'), 'success' );
	}

	if ($InputError!=1){
		//run the SQL from either of the above possibilites
		$ErrMsg = _('The user alterations could not be processed because');
		$DbgMsg = _('The SQL that was used to update the user and failed was');
		$result = DB_query($sql,$ErrMsg,$DbgMsg);

		unset($_POST['UserID']);
		unset($_POST['RealName']);
		unset($_POST['Cust']);
		unset($_POST['BranchCode']);
		unset($_POST['SupplierID']);
		unset($_POST['Salesman']);
		unset($_POST['Phone']);
		unset($_POST['Email']);
		unset($_POST['Password']);
		unset($_POST['PageSize']);
		unset($_POST['Access']);
		unset($_POST['CanCreateTender']);
		unset($_POST['DefaultLocation']);
		unset($_POST['ModulesAllowed']);
		unset($_POST['ShowDashboard']);
		unset($_POST['Blocked']);
		unset($_POST['Theme']);
		unset($_POST['UserLanguage']);
		unset($_POST['PDFLanguage']);
		unset($_POST['Department']);
		unset($SelectedUser);
		unset($SelectedEmployee);
	}

} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button


	if ($AllowDemoMode AND $SelectedUser == 'admin') {
		prnMsg(_('The demonstration user called demo cannot be deleted'),'error');
	} else {
		$sql="SELECT userid FROM audittrail where userid='" . $SelectedUser ."'";
		$result=DB_query($sql);
		if (DB_num_rows($result)!=0) {
			prnMsg(_('Cannot delete user as entries already exist in the audit trail'), 'warn');
		} else {
			$sql="DELETE FROM locationusers WHERE userid='" . $SelectedUser . "'";
			$ErrMsg = _('The Location - User could not be deleted because');;
			$result = DB_query($sql,$ErrMsg);

			$sql="DELETE FROM www_users WHERE userid='" . $SelectedUser . "'";
			$ErrMsg = _('The User could not be deleted because');;
			$result = DB_query($sql,$ErrMsg);
			prnMsg(_('User Deleted'),'info');
		}
		unset($SelectedUser);
	}

}

if (!isset($SelectedUser) && !isset($_SESSION['NewUser'])) {
echo '<a href="WWW_Users.php?NewUser=Yes" title="Add new User">Add new User</a>';
/* If its the first time the page has been displayed with no parameters then none of the above are true and the list of Users will be displayed with links to delete or edit each. These will call the same page again and allow update/input or deletion of the records*/


if (isset($_POST['SearchUser'])
	OR isset($_POST['Go'])
	OR isset($_POST['Next'])
	OR isset($_POST['Previous'])) {

	if (mb_strlen($_POST['Keyword']) > 0 AND mb_strlen($_POST['useid']) > 0) {
		prnMsg( _('Employee name keywords have been used in preference to the User ID extract entered'), 'info' );
	}
	if ($_POST['Keyword'] == '' AND $_POST['useid'] == '') {
		$SQL = "SELECT userid,
					realname,
					phone,
					email,
					customerid,
					branchcode,
					supplierid,
					salesman,
					lastvisitdate,
					fullaccess,
					cancreatetender,
					pagesize,
					theme,
					language
				FROM www_users";
	} else {
		if (mb_strlen($_POST['Keyword']) > 0) {
			$_POST['Keyword'] = mb_strtoupper($_POST['Keyword']);
			//insert wildcard characters in spaces
			$SearchString = '%' . str_replace(' ', '%', $_POST['Keyword']) . '%';
			$SQL = "SELECT userid,
					realname,
					phone,
					email,
					customerid,
					branchcode,
					supplierid,
					salesman,
					lastvisitdate,
					fullaccess,
					cancreatetender,
					pagesize,
					theme,
					language
				FROM www_users
						WHERE realname " . LIKE . " '" . $SearchString . "'
						ORDER BY realname";
		} elseif (mb_strlen($_POST['useid']) > 0) {
			$_POST['useid'] = mb_strtoupper($_POST['useid']);
			$SQL = "SELECT userid,
					realname,
					phone,
					email,
					customerid,
					branchcode,
					supplierid,
					salesman,
					lastvisitdate,
					fullaccess,
					cancreatetender,
					pagesize,
					theme,
					language
				FROM www_users
						WHERE userid " . LIKE . " '%" . $_POST['useid'] . "%'
						ORDER BY userid";
		}
	} //one of keywords or SupplierCode was more than a zero length string
	$result = DB_query($SQL);
} //end of if search
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<table class="selection">
		<tr><th colspan="5"><h3>Search Login Credential for Employees</h3></th><tr>
			<tr>
		<td>' . _('Enter a partial First Name') . ':</td>
		<td>';
if (isset($_POST['Keyword'])) {
	echo '<input type="text" name="Keyword" value="' . $_POST['Keyword'] . '" size="20" maxlength="25" />';
} else {
	echo '<input type="text" name="Keyword" size="20" maxlength="25" />';
}
echo '</td>
		<td><b>' . _('OR') . '</b></td>
		<td>' . _('Enter a partial User ID.') . ':</td>
		<td>';
if (isset($_POST['useid'])) {
	echo '<input type="text" autofocus="autofocus" name="useid" value="' . $_POST['useid'] . '" size="15" maxlength="18" />';
} else {
	echo '<input type="text" autofocus="autofocus" name="useid" size="15" maxlength="18" />';
}
echo '</td></tr>';
			echo '</table>';
			echo '<br /><div class="centre"><input type="submit" name="SearchUser" value="' . _('Search Now') . '" /></div>';

if (isset($_POST['SearchUser'])) {
	$ListCount = DB_num_rows($result);
	$ListPageMax = ceil($ListCount / $_SESSION['DisplayRecordsMax']);
	if (isset($_POST['Next'])) {
		if ($_POST['PageOffset'] < $ListPageMax) {
			$_POST['PageOffset'] = $_POST['PageOffset'] + 1;
		}
	}
	if (isset($_POST['Previous'])) {
		if ($_POST['PageOffset'] > 1) {
			$_POST['PageOffset'] = $_POST['PageOffset'] - 1;
		}
	}
	if ($ListPageMax > 1) {
		echo '<p>&nbsp;&nbsp;' . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': </p>';
		echo '<select name="PageOffset">';
		$ListPage = 1;
		while ($ListPage <= $ListPageMax) {
			if ($ListPage == $_POST['PageOffset']) {
				echo '<option value="' . $ListPage . '" selected="selected">' . $ListPage . '</option>';
			} else {
				echo '<option value="' . $ListPage . '">' . $ListPage . '</option>';
			}
			$ListPage++;
		}
		echo '</select>
			<input type="submit" name="Go" value="' . _('Go') . '" />
			<input type="submit" name="Previous" value="' . _('Previous') . '" />
			<input type="submit" name="Next" value="' . _('Next') . '" />';
		echo '<br />';
	}
	echo '<input type="hidden" name="Search" value="' . _('Search Now') . '" />';
	echo '<br />
		<br />
		<br />
		<table cellpadding="2">';
	echo '<tr>
				<th class="ascending">' . _('User Login') . '</th>
				<th class="ascending">' . _('Full Name') . '</th>
				<th class="ascending">' . _('Telephone') . '</th>
				<th class="ascending">' . _('Email') . '</th>
				<th class="ascending">' . _('Salesperson') . '</th>
				<th class="ascending">' . _('Last Visit') . '</th>
				<th class="ascending">' . _('Security Role') . '</th>
				<th class="ascending">' . _('Report Size') . '</th>
				<th class="ascending">' . _('Theme') . '</th>
				<th class="ascending">' . _('Language') . '</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
		</tr>';
	$k = 0; //row counter to determine background colour
	$RowIndex = 0;
	if (DB_num_rows($result) <> 0) {
		DB_data_seek($result, ($_POST['PageOffset'] - 1) * $_SESSION['DisplayRecordsMax']);
	}
	while (($myrow = DB_fetch_array($result)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {
		if ($k == 1) {
			echo '<tr class="EvenTableRows">';
			$k = 0;
		} else {
			echo '<tr class="OddTableRows">';
			$k = 1;
		}
		printf('<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td><a href="%s&amp;SelectedUser=%s">' . _('Edit') . '</a></td>
					<td><a href="%s&amp;SelectedUser=%s&amp;delete=1" onclick="return confirm(\'' . _('Are you sure you wish to delete this user?') . '\');">' . _('Delete') . '</a></td>
					</tr>',
					$myrow['userid'],
					$myrow['realname'],
					$myrow['phone'],
					$myrow['email'],
					$myrow['salesman'],
					$LastVisitDate,
					$SecurityRoles[($myrow['fullaccess'])],
					$myrow['pagesize'],
					$myrow['theme'],
					$LanguagesArray[$myrow['language']]['LanguageName'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?',
					$myrow['userid'],
					htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '?',
					$myrow['userid']);
		$RowIndex = $RowIndex + 1;
		//end of page full new headings if
	}
	//end of while loop
	echo '</table>';
}
//end if results to show
if (isset($ListPageMax) and $ListPageMax > 1) {
	echo '<p>&nbsp;&nbsp;' . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': </p>';
	echo '<select name="PageOffset">';
	$ListPage = 1;
	while ($ListPage <= $ListPageMax) {
		if ($ListPage == $_POST['PageOffset']) {
			echo '<option value="' . $ListPage . '" selected="selected">' . $ListPage . '</option>';
		} else {
			echo '<option value="' . $ListPage . '">' . $ListPage . '</option>';
		}
		$ListPage++;
	}
	echo '</select>
		<input type="submit" name="Go" value="' . _('Go') . '" />
		<input type="submit" name="Previous" value="' . _('Previous') . '" />
		<input type="submit" name="Next" value="' . _('Next') . '" />';
	echo '<br />';
}
			echo '</form>';
/*------------------------------------------------------------------------------------------------------------------------------------------*/
} //end of ifs and buts!
else{
if (isset($SelectedEmployee)){
		/* check to ensure the user id is not already entered */
		$result = DB_query("SELECT userid FROM www_users WHERE emp_id='" . $SelectedEmployee . "'");
		if (DB_num_rows($result)==1){
			$InputError =1;
			prnMsg(_('The Selected Employee has already an existing Account and cannot have more than one Accounts'),'error');
		}
	}
if (isset($SelectedEmployee) AND ($InputError !=1) AND !isset($_GET['delete'])) {

	echo '<div class="centre"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8')  . '?NewUser=No">' . _('Review Existing Users') . '</a></div><br />';


echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

if (isset($SelectedEmployee)) {
	//editing an existing User
	$sql = "SELECT emp_id,
			emp_fname,
			emp_lname,
			emp_mname,
			email,
			emp_cont
		FROM employee
		WHERE emp_id='" . $SelectedEmployee . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['Emp_ID'] = $myrow['emp_id'];
	$_POST['RealName'] = $myrow['emp_fname'].' '.$myrow['emp_lname'].' '.$myrow['emp_mname'];
	$_POST['Phone'] = $myrow['emp_cont'];
	$_POST['Email'] = $myrow['email'];

	echo '<input type="hidden" name="SelectedEmployee" value="' . $SelectedEmployee . '" />';

}

if (isset($SelectedUser)) {
	//editing an existing User

	$sql = "SELECT userid,
			realname,
			phone,
			email,
			customerid,
			password,
			branchcode,
			supplierid,
			salesman,
			pagesize,
			fullaccess,
			cancreatetender,
			defaultlocation,
			modulesallowed,
			showdashboard,
			blocked,
			theme,
			language,
			pdflanguage,
			department
		FROM www_users
		WHERE userid='" . $SelectedUser . "'";

	$result = DB_query($sql);
	$myrow = DB_fetch_array($result);

	$_POST['UserID'] = $myrow['userid'];
	$_POST['RealName'] = $myrow['realname'];
	$_POST['Phone'] = $myrow['phone'];
	$_POST['Email'] = $myrow['email'];
	$_POST['Cust']	= $myrow['customerid'];
	$_POST['BranchCode']  = $myrow['branchcode'];
	$_POST['SupplierID'] = $myrow['supplierid'];
	$_POST['Salesman'] = $myrow['salesman'];
	$_POST['PageSize'] = $myrow['pagesize'];
	$_POST['Access'] = $myrow['fullaccess'];
	$_POST['CanCreateTender'] = $myrow['cancreatetender'];
	$_POST['DefaultLocation'] = $myrow['defaultlocation'];
	$_POST['ModulesAllowed'] = $myrow['modulesallowed'];
	$_POST['Theme'] = $myrow['theme'];
	$_POST['UserLanguage'] = $myrow['language'];
	$_POST['ShowDashboard'] = $myrow['showdashboard'];
	$_POST['Blocked'] = $myrow['blocked'];
	$_POST['PDFLanguage'] = $myrow['pdflanguage'];
	$_POST['Department'] = $myrow['department'];

	echo '<input type="hidden" name="SelectedUser" value="' . $SelectedUser . '" />';
	echo '<input type="hidden" name="UserID" value="' . $_POST['UserID'] . '" />';
	echo '<input type="hidden" name="ModulesAllowed" value="' . $_POST['ModulesAllowed'] . '" />';

	echo '<table class="selection">
			<tr>
				<td>' . _('User code') . ':</td>
				<td>' . $_POST['UserID'] . '</td>
			</tr>';

} else { //end of if $SelectedUser only do the else when a new record is being entered

	echo '<table class="selection">
			<tr>
				<td>' . _('User Login') . ':</td>
				<td><input pattern="(?!^([aA]{1}[dD]{1}[mM]{1}[iI]{1}[nN]{1})$)[^?+.&\\>< ]{4,}" type="text" required="required" name="UserID" size="22" maxlength="20" placeholder="'._('At least 4 characters').'" title="'._('Please input not less than 4 characters and canot be admin or contains illegal characters').'"  /></td>
			</tr>';

	/*set the default modules to show to all
	this had trapped a few people previously*/
	$i=0;
	if (!isset($_POST['ModulesAllowed'])) {
		$_POST['ModulesAllowed']='';
	}
	foreach($ModuleList as $ModuleName){
		if ($i>0){
			$_POST['ModulesAllowed'] .=',';
		}
		$_POST['ModulesAllowed'] .= '1';
		$i++;
	}
}

if (!isset($_POST['Password'])) {
	$_POST['Password']='';
}
if (!isset($_POST['RealName'])) {
	$_POST['RealName']='';
}
if (!isset($_POST['Phone'])) {
	$_POST['Phone']='';
}
if (!isset($_POST['Email'])) {
	$_POST['Email']='';
}
echo '<tr>
		<td>' . _('Password') . ':</td>
		<td><input type="password" pattern=".{5,}" name="Password" ' . (!isset($SelectedUser) ? 'required="required"' : '') . ' size="22" maxlength="20" value="' . $_POST['Password'] . '" placeholder="'._('At least 5 characters').'" id="password" title="'._('Passwords must be 5 characters or more and cannot same as the users id. A mix of upper and lower case and some non-alphanumeric characters are recommended.').'" /></td>
	</tr>';
	echo '<tr>
		<td>' . _('Re-Enter Password') . ':</td>
		<td><input type="password" pattern=".{5,}" name="RePassword" ' . (!isset($SelectedUser) ? 'required="required"' : '') . ' size="22" maxlength="20" value="' . $_POST['RePassword'] . '" placeholder="'._('At least 5 characters').'" id="confirm_password" title="'._('Passwords must be 5 characters or more and cannot same as the users id. A mix of upper and lower case and some non-alphanumeric characters are recommended.').'" /> <span id="message"></span></td>
	</tr>';
echo '<tr>
		<td>' . _('Full Name') . ':</td>
		<td><input type="text" name="RealName" ' . (isset($SelectedUser) ? 'autofocus="autofocus"' : '') . ' required="required" value="' . $_POST['RealName'] . '" size="36" maxlength="35" /></td>
	</tr>';
echo '<tr>
		<td>' . _('Telephone No') . ':</td>
		<td><input type="tel" name="Phone" pattern="[0-9+()\s-]*" value="' . $_POST['Phone'] . '"  size="32" maxlength="30" /></td>
	</tr>';
echo '<tr>
		<td>' . _('Email Address') .':</td>
		<td><input type="email" name="Email" placeholder="' . _('e.g. user@domain.com') . '" required="required" value="' . $_POST['Email'] .'" size="32" maxlength="55" title="'._('A valid email address is required').'" /></td>
	</tr>';
echo '<tr>
		<td>' . _('Security Role') . ':</td>
		<td><select name="Access">';

foreach ($SecurityRoles as $SecKey => $SecVal) {
	if (isset($_POST['Access']) and $SecKey == $_POST['Access']){
		echo '<option selected="selected" value="' . $SecKey . '">' . $SecVal  . '</option>';
	} else {
		echo '<option value="' . $SecKey . '">' . $SecVal  . '</option>';
	}
}
echo '</select>';
echo '<input type="hidden" name="ID" value="'.$_SESSION['UserID'].'" /></td>

    </tr>';

echo '<tr>
		<td>' . _('User Can Create Tenders') . ':</td>
		<td><select name="CanCreateTender">';

if ($_POST['CanCreateTender']==0){
	echo '<option selected="selected" value="0">' . _('No') . '</option>';
	echo '<option value="1">' . _('Yes') . '</option>';
} else {
 	echo '<option selected="selected" value="1">' . _('Yes') . '</option>';
	echo '<option value="0">' . _('No') . '</option>';
}
echo '</select></td></tr>';

echo '<tr>
		<td>' . _('Default Location') . ':</td>
		<td><select name="DefaultLocation">';

$sql = "SELECT loccode, locationname FROM locations";
$result = DB_query($sql);

while ($myrow=DB_fetch_array($result)){
	if (isset($_POST['DefaultLocation']) AND $myrow['loccode'] == $_POST['DefaultLocation']){
		echo '<option selected="selected" value="' . $myrow['loccode'] . '">' . $myrow['locationname']  . '</option>';
	} else {
		echo '<option value="' . $myrow['loccode'] . '">' . $myrow['locationname']  . '</option>';
	}
}

echo '</select></td>
	</tr>';

if (!isset($_POST['Cust'])) {
	$_POST['Cust']='';
}
if (!isset($_POST['BranchCode'])) {
	$_POST['BranchCode']='';
}
if (!isset($_POST['SupplierID'])) {
	$_POST['SupplierID']='';
}
echo '<tr>
		<td>' . _('Customer Code') . ':</td>
		<td><input type="text" name="Cust" data-type="no-ilLegal-chars" title="' . _('If this user login is to be associated with a customer account, enter the customer account code') . '" size="10" maxlength="10" value="' . $_POST['Cust'] . '" /></td>
	</tr>';

echo '<tr>
		<td>' . _('Branch Code') . ':</td>
		<td><input type="text" name="BranchCode" data-type="no-ilLegal-chars" title="' . _('If this user login is to be associated with a customer account a valid branch for the customer account must be entered.') . '" size="10" maxlength="10" value="' . $_POST['BranchCode'] .'" /></td>
	</tr>';

echo '<tr>
		<td>' . _('Supplier Code') . ':</td>
		<td><input type="text" name="SupplierID" data-type="no-ilLegal-chars" size="10" maxlength="10" value="' . $_POST['SupplierID'] .'" /></td>
	</tr>';

echo '<tr>
		<td>' . _('Restrict to Sales Person') . ':</td>
		<td><select name="Salesman">';

$sql = "SELECT salesmancode, salesmanname FROM salesman WHERE current = 1 ORDER BY salesmanname";
$result = DB_query($sql);
if ((isset($_POST['Salesman']) AND $_POST['Salesman']=='') OR !isset($_POST['Salesman'])){
	echo '<option selected="selected" value="">' .  _('Not a salesperson only login') . '</option>';
} else {
	echo '<option value="">' . _('Not a salesperson only login') . '</option>';
}
while ($myrow=DB_fetch_array($result)){

	if (isset($_POST['Salesman']) AND $myrow['salesmancode'] == $_POST['Salesman']){
		echo '<option selected="selected" value="' . $myrow['salesmancode'] . '">' . $myrow['salesmanname'] . '</option>';
	} else {
		echo '<option value="' . $myrow['salesmancode'] . '">' . $myrow['salesmanname'] . '</option>';
	}

}

echo '</select></td>
	</tr>';

echo '<tr>
		<td>' . _('Reports Page Size') .':</td>
		<td><select name="PageSize">';

if(isset($_POST['PageSize']) AND $_POST['PageSize']=='A4'){
	echo '<option selected="selected" value="A4">' . _('A4')  . '</option>';
} else {
	echo '<option value="A4">' . _('A4') . '</option>';
}

if(isset($_POST['PageSize']) AND $_POST['PageSize']=='A3'){
	echo '<option selected="selected" value="A3">' . _('A3')  . '</option>';
} else {
	echo '<option value="A3">' . _('A3')  . '</option>';
}

if(isset($_POST['PageSize']) AND $_POST['PageSize']=='A3_Landscape'){
	echo '<option selected="selected" value="A3_Landscape">' . _('A3') . ' ' . _('landscape')  . '</option>';
} else {
	echo '<option value="A3_Landscape">' . _('A3') . ' ' . _('landscape')  . '</option>';
}

if(isset($_POST['PageSize']) AND $_POST['PageSize']=='Letter'){
	echo '<option selected="selected" value="Letter">' . _('Letter')  . '</option>';
} else {
	echo '<option value="Letter">' . _('Letter')  . '</option>';
}

if(isset($_POST['PageSize']) AND $_POST['PageSize']=='Letter_Landscape'){
	echo '<option selected="selected" value="Letter_Landscape">' . _('Letter') . ' ' . _('landscape')  . '</option>';
} else {
	echo '<option value="Letter_Landscape">' . _('Letter') . ' ' . _('landscape')  . '</option>';
}

if(isset($_POST['PageSize']) AND $_POST['PageSize']=='Legal'){
	echo '<option selected="selected" value="Legal">' . _('Legal')  . '</option>';
} else {
	echo '<option value="Legal">' . _('Legal')  . '</option>';
}
if(isset($_POST['PageSize']) AND $_POST['PageSize']=='Legal_Landscape'){
	echo '<option selected="selected" value="Legal_Landscape">' . _('Legal') . ' ' . _('landscape')  . '</option>';
} else {
	echo '<option value="Legal_Landscape">' . _('Legal') . ' ' . _('landscape')  . '</option>';
}

echo '</select></td>
	</tr>';

echo '<tr>
		<td>' . _('Theme') . ':</td>
		<td><select required="required" name="Theme">';

$ThemeDirectories = scandir('css/');


foreach ($ThemeDirectories as $ThemeName) {

	if (is_dir('css/' . $ThemeName) AND $ThemeName != '.' AND $ThemeName != '..' AND $ThemeName != '.svn'){

		if (isset($_POST['Theme']) AND $_POST['Theme'] == $ThemeName){
			echo '<option selected="selected" value="' . $ThemeName . '">' . $ThemeName  . '</option>';
		} else if (!isset($_POST['Theme']) AND ($Theme==$ThemeName)) {
			echo '<option selected="selected" value="' . $ThemeName . '">' . $ThemeName  . '</option>';
		} else {
			echo '<option value="' . $ThemeName . '">' . $ThemeName . '</option>';
		}
	}
}

echo '</select></td>
	</tr>';


echo '<tr>
		<td>' . _('Language') . ':</td>
		<td><select required="required" name="UserLanguage">';

foreach ($LanguagesArray as $LanguageEntry => $LanguageName){
	if (isset($_POST['UserLanguage']) AND $_POST['UserLanguage'] == $LanguageEntry){
		echo '<option selected="selected" value="' . $LanguageEntry . '">' . $LanguageName['LanguageName']  . '</option>';
	} elseif (!isset($_POST['UserLanguage']) AND $LanguageEntry == $DefaultLanguage) {
		echo '<option selected="selected" value="' . $LanguageEntry . '">' . $LanguageName['LanguageName']  . '</option>';
	} else {
		echo '<option value="' . $LanguageEntry . '">' . $LanguageName['LanguageName']  . '</option>';
	}
}
echo '</select></td>
	</tr>';

/*Make an array out of the comma separated list of modules allowed*/
$ModulesAllowed = explode(',',$_POST['ModulesAllowed']);

$i=0;
foreach($ModuleList as $ModuleName){

	echo '<tr>
			<td>' . _('Display') . ' ' . $ModuleName . ' ' . _('module') . ': </td>
			<td><select name="Module_' . $i . '">';
	if ($ModulesAllowed[$i]==0){
		echo '<option selected="selected" value="0">' . _('No') . '</option>';
		echo '<option value="1">' . _('Yes') . '</option>';
	} else {
	 	echo '<option selected="selected" value="1">' . _('Yes') . '</option>';
		echo '<option value="0">' . _('No') . '</option>';
	}
	echo '</select></td>
		</tr>';
	$i++;
}

echo '<tr>
		<td>' . _('Display Dashboard after Login') . ': </td>
		<td><select name="ShowDashboard">';
if($_POST['ShowDashboard']==0) {
	echo '<option selected="selected" value="0">' . _('No') . '</option>';
	echo '<option value="1">' . _('Yes') . '</option>';
} else {
 	echo '<option selected="selected" value="1">' . _('Yes') . '</option>';
	echo '<option value="0">' . _('No') . '</option>';
}
echo '</select></td>
	</tr>';

if (!isset($_POST['PDFLanguage'])){
	$_POST['PDFLanguage']=0;
}

echo '<tr>
		<td>' . _('PDF Language Support') . ': </td>
		<td><select name="PDFLanguage">';
for($i=0;$i<count($PDFLanguages);$i++){
	if ($_POST['PDFLanguage']==$i){
		echo '<option selected="selected" value="' . $i .'">' . $PDFLanguages[$i] . '</option>';
	} else {
		echo '<option value="' . $i .'">' . $PDFLanguages[$i]. '</option>';
	}
}
echo '</select></td>
	</tr>';

/* Allowed Department for Internal Requests */

echo '<tr>
		<td>' . _('Allowed Department for Internal Requests') . ':</td>';

$sql="SELECT departmentid,
			description
		FROM departments
		ORDER BY description";

$result=DB_query($sql);
echo '<td><select name="Department">';
if ((isset($_POST['Department']) AND $_POST['Department']=='0') OR !isset($_POST['Department'])){
	echo '<option selected="selected" value="0">' .  _('Any Internal Department') . '</option>';
} else {
	echo '<option value="">' . _('Any Internal Department') . '</option>';
}
while ($myrow=DB_fetch_array($result)){
	if (isset($_POST['Department']) AND $myrow['departmentid'] == $_POST['Department']){
		echo '<option selected="selected" value="' . $myrow['departmentid'] . '">' . $myrow['description'] . '</option>';
	} else {
		echo '<option value="' . $myrow['departmentid'] . '">' . $myrow['description'] . '</option>';
	}
}
echo '</select></td>
	</tr>';

/* Account status */

echo '<tr>
		<td>' . _('Account Status') . ':</td>
		<td><select required="required" name="Blocked">';
if ($_POST['Blocked']==0){
	echo '<option selected="selected" value="0">' . _('Open') . '</option>';
	echo '<option value="1">' . _('Blocked') . '</option>';
} else {
 	echo '<option selected="selected" value="1">' . _('Blocked') . '</option>';
	echo '<option value="0">' . _('Open') . '</option>';
}
echo '</select></td>
	</tr>';

echo '</table>
	<br />
	<div class="centre">
		<input type="submit" name="submit" value="' . _('Enter Information') . '" />
	</div>
    </div>
	</form>';
} //end selected employee
else{

if (isset($_POST['Search'])
	OR isset($_POST['Go'])
	OR isset($_POST['Next'])
	OR isset($_POST['Previous'])) {

	if (mb_strlen($_POST['Keywords']) > 0 AND mb_strlen($_POST['idno']) > 0) {
		prnMsg( _('Employee name keywords have been used in preference to the Employee Number extract entered'), 'info' );
	}
	if ($_POST['Keywords'] == '' AND $_POST['idno'] == '') {
		$SQL = "SELECT *
				FROM employee
				ORDER BY emp_fname";
	} else {
		if (mb_strlen($_POST['Keywords']) > 0) {
			$_POST['Keywords'] = mb_strtoupper($_POST['Keywords']);
			//insert wildcard characters in spaces
			$SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';
			$SQL = "SELECT *
						FROM employee
						WHERE emp_fname " . LIKE . " '" . $SearchString . "'
						ORDER BY emp_fname";
		} elseif (mb_strlen($_POST['idno']) > 0) {
			$_POST['idno'] = mb_strtoupper($_POST['idno']);
			$SQL = "SELECT *
						FROM employee
						WHERE emp_id " . LIKE . " '%" . $_POST['idno'] . "%'
						ORDER BY emp_id";
		}
	} //one of keywords or SupplierCode was more than a zero length string
	$result = DB_query($SQL);
} //end of if search
echo '<a href="WWW_Users.php?NewUser=No" title="Review existing Users">Review Existing Users</a>';
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<table class="selection">
		<tr><th colspan="5"><h3>Search Employee</h3></th><tr>
			<tr>
		<td>' . _('Enter a partial First Name') . ':</td>
		<td>';
if (isset($_POST['Keywords'])) {
	echo '<input type="text" name="Keywords" value="' . $_POST['Keywords'] . '" size="20" maxlength="25" />';
} else {
	echo '<input type="text" name="Keywords" size="20" maxlength="25" />';
}
echo '</td>
		<td><b>' . _('OR') . '</b></td>
		<td>' . _('Enter a partial Employee No.') . ':</td>
		<td>';
if (isset($_POST['idno'])) {
	echo '<input type="text" autofocus="autofocus" name="idno" value="' . $_POST['idno'] . '" size="15" maxlength="18" />';
} else {
	echo '<input type="text" autofocus="autofocus" name="idno" size="15" maxlength="18" />';
}
echo '</td></tr>';
			echo '</table>';
			echo '<br /><div class="centre"><input type="submit" name="Search" value="' . _('Search Now') . '" /></div>';

if (isset($_POST['Search'])) {
	$ListCount = DB_num_rows($result);
	$ListPageMax = ceil($ListCount / $_SESSION['DisplayRecordsMax']);
	if (isset($_POST['Next'])) {
		if ($_POST['PageOffset'] < $ListPageMax) {
			$_POST['PageOffset'] = $_POST['PageOffset'] + 1;
		}
	}
	if (isset($_POST['Previous'])) {
		if ($_POST['PageOffset'] > 1) {
			$_POST['PageOffset'] = $_POST['PageOffset'] - 1;
		}
	}
	if ($ListPageMax > 1) {
		echo '<p>&nbsp;&nbsp;' . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': </p>';
		echo '<select name="PageOffset">';
		$ListPage = 1;
		while ($ListPage <= $ListPageMax) {
			if ($ListPage == $_POST['PageOffset']) {
				echo '<option value="' . $ListPage . '" selected="selected">' . $ListPage . '</option>';
			} else {
				echo '<option value="' . $ListPage . '">' . $ListPage . '</option>';
			}
			$ListPage++;
		}
		echo '</select>
			<input type="submit" name="Go" value="' . _('Go') . '" />
			<input type="submit" name="Previous" value="' . _('Previous') . '" />
			<input type="submit" name="Next" value="' . _('Next') . '" />';
		echo '<br />';
	}
	echo '<input type="hidden" name="Search" value="' . _('Search Now') . '" />';
	echo '<br />
		<br />
		<br />
		<table cellpadding="2">';
	echo '<tr>
	  		<th class="ascending">' . _('Employee No') . '</th>
			<th class="ascending">' . _('Employee Name') . '</th>
			<th class="ascending">' . _('ID Number') . '</th>
			<th class="ascending">' . _('Phone No.') . '</th>
			<th class="ascending">' . _('Grade') . '</th>
		</tr>';
	$k = 0; //row counter to determine background colour
	$RowIndex = 0;
	if (DB_num_rows($result) <> 0) {
		DB_data_seek($result, ($_POST['PageOffset'] - 1) * $_SESSION['DisplayRecordsMax']);
	}
	while (($myrow = DB_fetch_array($result)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {
		if ($k == 1) {
			echo '<tr class="EvenTableRows">';
			$k = 0;
		} else {
			echo '<tr class="OddTableRows">';
			$k = 1;
		}
		echo '<td><input type="submit" name="SelectedEmployee" value="'.$myrow['emp_id'].'" /></td>
				<td>' . $myrow['emp_fname'].' '.$myrow['emp_lname'] . '</td>
				<td>' . $myrow['id_number'] . '</td>
				<td>' . $myrow['emp_cont'] . '</td>
				<td>' . $myrow['grade'] . '</td>
			</tr>';
		$RowIndex = $RowIndex + 1;
		//end of page full new headings if
	}
	//end of while loop
	echo '</table>';
}
//end if results to show
if (isset($ListPageMax) and $ListPageMax > 1) {
	echo '<p>&nbsp;&nbsp;' . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': </p>';
	echo '<select name="PageOffset">';
	$ListPage = 1;
	while ($ListPage <= $ListPageMax) {
		if ($ListPage == $_POST['PageOffset']) {
			echo '<option value="' . $ListPage . '" selected="selected">' . $ListPage . '</option>';
		} else {
			echo '<option value="' . $ListPage . '">' . $ListPage . '</option>';
		}
		$ListPage++;
	}
	echo '</select>
		<input type="submit" name="Go" value="' . _('Go') . '" />
		<input type="submit" name="Previous" value="' . _('Previous') . '" />
		<input type="submit" name="Next" value="' . _('Next') . '" />';
	echo '<br />';
}
			echo '</form>';
}
}
include('includes/footer.inc');
?>
<script src="js/jquery-1.9.1.js" type="text/javascript" ></script>
<script type="text/javascript">
$('#confirm_password').on('keyup', function () {
    if ($(this).val() == $('#password').val()) {
        $('#message').html('Passwords match.').css('color', 'green');
    } else $('#message').html('Passwords do not match!').css('color', 'red');
});
</script>