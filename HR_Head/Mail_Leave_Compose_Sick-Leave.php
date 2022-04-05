<?php
if(isset($_POST['Save'])){
$userid = $_POST['empid'];
$from= date('Y-m-d',strtotime($_POST['from']));
$to = date('Y-m-d',strtotime($_POST['to']));
$days = $_POST['days'];
$duties = $_POST['reason'];
$contact= $_POST['contact'];
$name= $_POST['em_name'];
$add= $_POST['em_add'];
$phone= $_POST['em_phone'];
if(isset($_POST['Draft'])){
$send = 0;
}else{
$send = 1;
}

$LeaveID = GetNextTransNo(111, $db);

$sql1= "SELECT COUNT(leave_id) as nums FROM leave_annual
							WHERE emp_id='" . $userid . "'
							AND YEAR(date_added)='". date('Y') ."'
							AND (from_date <= '".$to."' AND '".$from."' <= to_date)
							AND rejected =0";
$sql2= "SELECT COUNT(off_id) as nums FROM leave_off_duty
							WHERE emp_id='" . $userid . "'
							AND YEAR(date_added)='". date('Y') ."'
							AND (from_date <= '".$to."' AND '".$from."' <= to_date)
							AND rejected =0";

$result=DB_query($sql1,'','',false,false);
$check = DB_fetch_row($result);
$result2=DB_query($sql2,'','',false,false);
$check2 = DB_fetch_row($result2);

$sqlz= "SELECT grade FROM employee WHERE emp_id='" . $userid . "'";
$res=DB_query($sqlz,'','',false,false);
$grade = DB_fetch_row($res);
if($grade[0]=="MANAGER"){
$leaveid=4;
}elseif($grade[0]=="I-CHIEF OFFICER"){
$leaveid=5;
}else{
$leaveid=3;
}
if($days ==0){
echo _('<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-window-close"></i></button>
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>
               Invalid Dates! Please review and try again
              </div>');
}elseif($from < date('Y-m-d')){
echo _('<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-window-close"></i></button>
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>
              Date From cannot be Earlier than today! Please review and try again.
              </div>');
}elseif($to < date('Y-m-d')){
echo _('<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-window-close"></i></button>
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>
              Date To cannot be Earlier than today! Please review and try again.
              </div>');
}elseif($check[0] >0){
echo _('<div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-window-close"></i></button>
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>
               Leave Overlaps with another leave you file earlier! Please review and try again.
              </div>');
}elseif($check2[0] >0){
echo _('<div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-window-close"></i></button>
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>
               Leave Overlaps with another leave you file earlier! Please review and try again.
              </div>');
}else{
$narrative = 'Application of <b>Sick Leave</b> from <b>'.date('d/m/Y',strtotime($_POST['from'])).'</b> to <b>'.date('d/m/Y',strtotime($_POST['to'])).'</b>. Number of days applied for <b>'.$days.'</b> days. While I will be on leave, my duties will be performed by <b>'.$duties.'</b>. My contact address while on leave will be <b>'.$contact.'</b>.';
$ErrMsg = _('The Information cannot be inserted because');
$sql = "INSERT INTO `leave_annual`(`leave_id`, `emp_id`, `leave_type`,`type`, `from_date`, `to_date`, `days`, `assigned_duties`, `contact`,`em_name`, `em_address`, `em_phone`, `narrative`, `send`, `added_by`) 
									VALUES (".$LeaveID.",'".$userid."',".$leaveid.",4,'".$from."','".$to."','".$days."','".$duties."','".$contact."','".$name."','".$add."','".$phone."','".$narrative."','".$send."','".$_SESSION['UserID']."')";
$qry = DB_query($sql,$ErrMsg);
if ($qry){
if($send==0){
	echo _('<div class="alert alert-info alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-window-close"></i></button>
                <h4><i class="icon fa fa-info"></i> Alert!</h4>
               Leave Application has been saved in your draft
              </div>');
	}else{
	echo _('<div class="alert alert-success alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-window-close"></i></button>
                <h4><i class="icon fa fa-check"></i> Alert!</h4>
               Leave Application has been Sent Successfully
              </div>');
	}
unset($_POST);
}else {
	echo _('<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-window-close"></i></button>
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>
               Not Added Please Try Again
              </div>');
	}
}
	
}
?>
<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			
			?>
					<div class="container-fluid">
						<div class="box-body">

			<div class="form-group">
			<?php
				$ErrMsg = _('An error occurred in retrieving the records');
						$DbgMsg = _('The SQL that was used to retrieve the information and that failed in the process was');
						if($_SESSION['CanEditHR'] == 1){
						$results = "SELECT * FROM employee a
											INNER JOIN www_users c ON a.emp_id=c.emp_id";
						$option ='<option selected value="">--Please Select Applicant--</option>';
						}else{	
						$results = "SELECT * FROM employee a
											INNER JOIN www_users c ON a.emp_id=c.emp_id
											WHERE c.userid='".$_SESSION['UserID']."'";
						$option ='';
							}
						$welcome_viewed = DB_query($results,$ErrMsg,$DbgMsg);
					if(DB_num_rows($welcome_viewed) >=1){
					echo '<div class="col-md-5">Applicant';
					echo '<select name="empid" class="form-control input-md" required>';
					echo $option;
						while($rows = DB_fetch_array($welcome_viewed)){
						echo '<option '.(($_SESSION['UserID']== $rows['emp_id'] or $_POST['empid']== $rows['emp_id'])? 'selected="selected"' : '').' value="'.$rows['emp_id'].'">'.$rows['emp_id'].' - '.$rows['emp_lname'].' '.$rows['emp_fname'].' '.$rows['emp_mname'].'</option>';
						}
					echo '</select>';
					echo '</div>';
					}else{
					echo '<div class="alert alert-warning alert-dismissible">
						<h4><i class="icon fa fa-warning"></i> Alert!</h4>
						No Leaves Days Assigned to You. Please contact Human Resource Department or System Administrator to assist you accordingly.
					  </div>';
					die;
					}
				?>
				<div class="col-md-3">Include Weekends
			    <div class="onoffswitch">
        <input type="checkbox" onclick="findDateDiff()" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch">
        <label class="onoffswitch-label" for="myonoffswitch">
            <span class="onoffswitch-inner"></span>
            <span class="onoffswitch-switch"></span>
        </label>
    </div>
			  </div>
				</div>
				
			<div class="form-group">
				<div class="col-md-5">From: (Format: mm/dd/yyyy)
			<?php
			echo '<input type="text" class="date" onfocus="findDateDiff()" alt="m/d/Y" name="from" value="'.(isset($_POST['from']) ? $_POST['from'] : date('m/d/Y')).'" id="form-control" required="" />';
			?>
			  </div>
			<div class="col-md-5">To: (Format: mm/dd/yyyy)
			<?php
			echo '<input type="text" class="date" onfocus="findDateDiff()" alt="m/d/Y" name="to" value="'.(isset($_POST['to']) ? $_POST['to'] : date('m/d/Y')).'" id="form-control" required="" />';
			?>
			 </div>
			 <div class="col-md-2">No of Days
				<input id="days" onmouseover="findDateDiff()" value="<?php echo $_POST['days']; ?>" name="days" readonly="" type="text" class="form-control input-md " />
			 </div>
				</div>
			  <div class="form-group">
				<div class="col-md-5">While on Leave, My duties goes to:
				<textarea name="reason" class="form-control input-md" required="" ><?php echo $_POST['reason']; ?></textarea>
			 </div>
			<div class="col-md-7">Contact Address while on leave:
				<textarea name="contact" class="form-control input-md" ><?php echo $_POST['contact']; ?></textarea>
			 </div>
				</div>
				
			<fieldset><legend>Incase I cannot be reached please contact</legend>
			  <div class="form-group">
				<div class="col-md-4">Name:
				<input id="id_emp" name="em_name" value="<?php echo $_POST['em_name']; ?>" type="text" class="form-control input-md" required=""/>
			  </div>
			<div class="col-md-4">Address:
				<input id="id" name="em_add" value="<?php echo $_POST['em_add']; ?>" type="text"  class="form-control input-md" required=""/>
			 </div>
			 <div class="col-md-4">Phone No:
				<input id="id" name="em_phone" value="<?php echo $_POST['em_phone']; ?>" type="text"  class="form-control input-md" required=""/>
			 </div>
				</div>
			  </fieldset>
				
            </div>
			<div class="box-footer">
              <div class="pull-right">
			  <input type="hidden" name="Save" value="Save" />
                <button type="submit" name="Draft" onclick="findDateDiff()" class="btn btn-default"><i class="fa fa-pencil"></i> Draft</button>
                <button type="submit" name="Send" onclick="findDateDiff()" class="btn btn-primary"><i class="fa fa-envelope-o"></i> Send</button>
              </div>
              <button type="reset" class="btn btn-default"><i class="fa fa-times"></i> Discard</button>
            </div>

						</div>
					</form>
<style type="text/css">
    .onoffswitch {
        position: relative; width: 90px;
        -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
    }
    .onoffswitch-checkbox {
        display: none;
    }
    .onoffswitch-label {
        display: block; overflow: hidden; cursor: pointer;
        border: 2px solid #999999; border-radius: 20px;
    }
    .onoffswitch-inner {
        display: block; width: 200%; margin-left: -100%;
        transition: margin 0.3s ease-in 0s;
    }
    .onoffswitch-inner:before, .onoffswitch-inner:after {
        display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
        font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
        box-sizing: border-box;
    }
    .onoffswitch-inner:before {
        content: "YES";
        padding-left: 10px;
        background-color: #27ae60; color: #FFFFFF;
    }
    .onoffswitch-inner:after {
        content: "NO";
        padding-right: 10px;
        background-color: #EEEEEE; color: #999999;
        text-align: right;
    }
    .onoffswitch-switch {
        display: block; width: 18px; margin: 6px;
        background: #FFFFFF;
        position: absolute; top: 0; bottom: 0;
        right: 56px;
        border: 2px solid #999999; border-radius: 20px;
        transition: all 0.3s ease-in 0s; 
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
        margin-left: 0;
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
        right: 0px; 
    }

</style>
<?php
function js_str($s)
{
    return '"' . addcslashes($s, "\0..\37\"\\") . '"';
}

function js_array($array)
{
    $temp = array_map('js_str', $array);
    return '[' . implode(',', $temp) . ']';
}
    //bind to $name
     $qry = 'SELECT DATE_FORMAT(date, "%m-%d") as d FROM leave_holidays WHERE repeat_annually=1';
	$rest=DB_query($qry);
    //put all of the resulting names into a PHP array
    $result_array = Array();
    while($row = DB_fetch_array($rest)){
        $result_array[] = date('Y').'-'.$row['d'];
    }
	$qry = 'SELECT DATE_FORMAT(date, "%m-%d") as d FROM leave_holidays WHERE repeat_annually=0 AND YEAR(date)= YEAR(CURDATE())';
	$rest=DB_query($qry);
    while($row = DB_fetch_array($rest)){
        $result_array[] = date('Y').'-'.$row['d'];
    }
    //convert the PHP array into JSON format, so it works with javascript
	
     //$json_array = json_encode($result_array);
?>

<script type="text/javascript">
function findDateDiff() {
var check = document.getElementById("myonoffswitch").checked;
if(check == true){
findDateDiff2();
}else{
findDateDiff3();
}

}


function findDateDiff1() {
var from = document.getElementsByName('from')[0].value;
var to = document.getElementsByName('to')[0].value;
var startDate = new Date(from);
var endDate = new Date(to);
    var millisecondsPerDay = 86400 * 1000; 
    startDate.setHours(0,0,0,1);  
    endDate.setHours(23,59,59,999);  
    var diff = endDate - startDate;     
    var days = Math.ceil(diff / millisecondsPerDay);
    
    // Subtract two weekend days for every week in between
    var weeks = Math.floor(days / 7);
    days = days - (weeks * 2);

    // Handle special cases
    var startDay = startDate.getDay();
    var endDay = endDate.getDay();
    
    // Remove weekend not previously removed.   
    if (startDay - endDay > 1)         
        days = days - 2;      
    
    // Remove start day if span starts on Sunday but ends before Saturday
    if (startDay === 0 && endDay != 6)
        days = days - 1 ; 
            
    // Remove end day if span ends on Saturday but starts after Sunday
    if (endDay === 6 && startDay !== 0)
        days = days - 1  ;
	
	var FDay = startDate.getDate();
	var FMonth = startDate.getMonth() + 1;
	var FDM = FDay+'/'+FMonth;
	
	var TDay = endDate.getDate();
	var TMonth = endDate.getMonth() + 1;
	var TDM = TDay+'/'+TMonth;
	
	//var holidays = ['1/1','1/5','1/6','20/10','12/12','25/12','26/12'];
	var  holidays = <?php echo js_array($result_array); ?>;
	for (var i in holidays) {
      if ((holidays[i] >= FDM) && (holidays[i] <= TDM)) {
      	days--;
      }
    }
		
    if(days < 1){
	 document.getElementById('days').value = 0;
	}else{
    document.getElementById('days').value =  days;
	}
}
function findDateDiff2() {
var from = document.getElementsByName('from')[0].value;
var to = document.getElementsByName('to')[0].value;
var startDate = new Date(from);
var endDate = new Date(to);
    var millisecondsPerDay = 86400 * 1000; 
    startDate.setHours(0,0,0,1);  
    endDate.setHours(23,59,59,999);  
    var diff = endDate - startDate;     
    var days = Math.ceil(diff / millisecondsPerDay);
		
    if(days < 1){
	 document.getElementById('days').value = 0;
	}else{
    document.getElementById('days').value = days;
	}
}

var gon = {};
//gon["holiday"] = "2018-12-12,2015-09-25,2016-08-31,2016-08-07,2015-08-13,2016-08-29,2016-01-07,2015-09-08".split(",");
gon["holiday"] = <?php echo js_array($result_array); ?>;

// 2 helper functions - moment.js is 35K minified so overkill in my opinion
function pad(num) { return ("0" + num).slice(-2); }
function formatDate(date) { var d = new Date(date), dArr = [d.getFullYear(), pad(d.getMonth() + 1), pad(d.getDate())];return dArr.join('-');}

function calculateDays(first,last) {
  var aDay = 24 * 60 * 60 * 1000,
  daysDiff = parseInt((last.getTime()-first.getTime())/aDay,10)+1;

  if (daysDiff>0) {  
    for (var i = first.getTime(), lst = last.getTime(); i <= lst; i += aDay) {
      var d = new Date(i);
      if (d.getDay() == 6 || d.getDay() == 0 // weekend
      || gon.holiday.indexOf(formatDate(d)) != -1) {
          daysDiff--;
      }
    }
  }
  return daysDiff;
}

// ONLY using jQuery here because OP already used it. I use 1.11 so IE8+

function findDateDiff3() {
var from = document.getElementsByName('from')[0].value;
var to = document.getElementsByName('to')[0].value;
var startDate = new Date(from);
var endDate = new Date(to);
    var days = calculateDays(startDate,endDate);
    if (days <= 0) {
       document.getElementById('days').value = 0;
    }
    else {
      //alert(days +" working days found");
	   document.getElementById('days').value = days;
    }
}

</script>
<script>
function LeaveDays(sel) {
	var state_id = sel.options[sel.selectedIndex].value;  
	if (state_id.length > 0 ) { 
	 $.ajax({
			type: "GET",
			url: "Ajax_LeaveDays.php",
			data: "id="+state_id,
			cache: false,
			beforeSend: function () { 
				$('#output').html('Available Leave Days:<br /><i class="fa fa-spinner fa-pulse fa-2x fa-fw">');
			},
			success: function(html) {    
				$("#output").html( html );
			}
		});
	}
}

</script>