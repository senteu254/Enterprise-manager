<?php
require_once('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
function LeaveDays(){
			$sql="SELECT IFNULL((a.opening_bal+a.leave_days),0) as days, 
						IFNULL((SELECT SUM(days) FROM leave_annual b
								WHERE a.emp_id=b.emp_id 
								AND a.leave_type=b.leave_type 
								AND a.year=YEAR(b.date_added)
								AND b.rejected=0
								AND b.send=1 
								AND b.type=3
								GROUP BY b.emp_id),0) as useddays
							FROM leave_days_allocation a
							WHERE a.emp_id='".$_GET['id']."' AND a.year='".date('Y')."' AND a.leave_type=3";
			$result=DB_query($sql);
			$row=DB_fetch_array($result);
			$availabledays = ($row["days"]-$row["useddays"]);
			if($availabledays >0){
			$rest='<div class="col-md-3">Available Leave Days:';
			$rest .= '<button type="button"  class="btn btn-danger">'.$availabledays.' Days</button>
						<input name="AvailableDays" type="hidden" value="'.$availabledays.'" /></div>';
			}else{
			$rest='<div class="col-md-3">Available Leave Days:';
			$rest .= '<button type="button"  class="btn btn-danger">No Available Days</button>
						<input name="AvailableDays" type="hidden" value="0" /></div>';
			}
			return $rest;
			}
echo LeaveDays();
?>