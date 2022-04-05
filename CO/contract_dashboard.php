<?php
include('includes/SQL_CommonFunctions.inc');
$mainlink = "index.php?Application=CON&Page=";
$path='CO/';
$view = (isset($_GET['Page']) && $_GET['Page'] != '') ? $_GET['Page'] : '';
switch ($view) {
	case 'View' :
        $title="Active Contracts";	
		$content=$path.'Active_Contracts.php';	
		$sidemenu =$path.'Side_Content_Contract.php';
		$class= 'class="active"';	
		break;

	case 'Type' :
	    $title="Contract Type";	
		$content = $path.'New_Contract_Type.php';
		$class1= 'class="active"';	
		break;
		
	case 'Edit_Type' :
	    $title="Edit Type";	
		$content = $path.'Edit_Contract_Type.php';	
		$class1= 'class="active"';	
		break;

	case 'Commitee' :
	    $title=" New Commitee";	
 		$content =$path.'New_Commitee.php';	
		$class2= 'class="active"';	
		break;
	
	case 'Edit_Supplier' :
	    $title="Edit Supplier";	
 		$content =$path.'Edit_Supplier.php';
		$class2= 'class="active"';	
		break;
	
	case 'Contract' :
	    $title="Add New Contract";	
 		$content =$path.'New_Contract.php';	
		$class3= 'class="active"';	
		break;
		
	case 'Assign' :
	    $title="Contract Assignment";	
 		$content =$path.'Assign_Contract.php';
		$class4= 'class="active"';	
		break;
		
	case 'Payment' :
	    $title="Contract Payment";	
 		$content =$path.'Contract_Payment.php';
		$sidemenu =$path.'Payment_Flow.php';
		$class5= 'class="active"';	
		break;
		
	case 'Users' :
	    $title="Add New User";	
 		$content =$path.'New_User.php';
		$class= 'class="active"';	
		break;
		
	case 'Edit_Users' :
	    $title="Edit User";	
 		$content =$path.'Edit_User.php';
		$class= 'class="active"';	
		break;
		
	case 'System' :
	    $title="Edit System";	
 		$content =$path.'System_Preference.php';	
		$class8= 'class="active"';	
		break;
	
	case 'Commitee_Roles' :
	    $title="Commitee Roles";	
 		$content =$path.'Commitee_Roles.php';	
		$class9= 'class="active"';	
		break;
		
	case 'Edit_Contract' :
	    $title="Edit Contract";	
 		$content =$path.'Edit_Contract.php';	
		$class3= 'class="active"';	
		break;
		
	case 'Delete_Contract' :
	    $title="Delete Contract";	
 		$content =$path.'Delete_Contract.php';	
		$class3= 'class="active"';	
		break;
		
	case 'Edit_Currency' :
	    $title="Edit Currency";	
 		$content =$path.'Edit_Currency.php';	
		$class9= 'class="active"';	
		break;
	case 'Payment_flow' :
	    $title="Contracts on Payment Flow";	
		$content=$path.'Payment_on_payment_flow.php';
		$class2= 'class="active"';
		break;
	
	
	default :
	    $title="Dashboard";	
		$content=$path.'Active_Contracts.php';
		$sidemenu =$path.'Side_Content_Contract.php';
		$class= 'class="active"';		
}

include $path.'template/template.php';
?>

