
		<div class="container-fluid">
			<div class="row">
				<?php 
		$path='ChiefOfficer/';
		$mainlink='index.php?Application=HRC&Ref=';
		$view = (isset($_GET['Ref']) && $_GET['Ref'] != '') ? $_GET['Ref'] : '';
								switch ($view) {
								case 'SickLeave' :
									$content=$path.'view-emp-pending.php';
									break;
							
								case 'Vacation' :
									$content=$path.'vl_pending.php';		
									break;
									
								case 'PMLeave' :
									$content=$path.'mp_pending.php';
									break;
									
								case 'PMApp' :
									$content=$path.'mp_approval_approve.php';
									break;
									
								case 'PMDeny' :
									$content=$path.'mp_approval_deny.php';
									break;
									
								case 'PMLeftView' :
									$content=$path.'mp_leftvacation.php';
									break;
									
								case 'PMDenyView' :
									$content=$path.'mp_approval_deny_approve.php';
									break;
									
								case 'PMDenyViewApp' :
									$content=$path.'mp_dapproved.php';
									break;
							   case 'PMDViewDeny' :
									$content=$path.'mp_ddeny.php';
									break;
							
								case 'SickLeaveAppv' :
									$content=$path.'approval_approve.php';	
									break;
								
								case 'SickLeaveDeny' :
									$content=$path.'approval_deny.php';	
									break;
									
								case 'SLPendingView' :
									$content=$path.'approval.php';	
									break;
									
								case 'SLAppvView' :
									$content=$path.'leave_view.php';	
									break;
									
								case 'SLDenyView' :
									$content=$path.'leave_deny_approve.php';	
									break;
									
								case 'SLPendingAppv' :
									$content=$path.'dapproved.php';	
									break;
								case 'SLPendingDeny' :
									$content=$path.'ddeny.php';	
									break;
									
								case 'VAppv' :
									$content=$path.'vl_approval_approve.php';	
									break;
									
								case 'VDeny' :
									$content=$path.'vl_approval_deny.php';	
									break;
									
								case 'VAppView' :
									$content=$path.'vl_approval.php';	
									break;
									case 'PMAppView' :
									$content=$path.'mp_approval.php';	
									break;
									
								case 'VAppViewAppv' :
									$content=$path.'vl_dapproved.php';	
									break;
									
								case 'VAppViewDeny' :
									$content=$path.'vl_ddeny.php';	
									break;
									
								case 'VLeftView' :
									$content=$path.'vl_leftvacation.php';	
									break;
									
								case 'VDenyView' :
									$content=$path.'vl_approval_deny_approve.php';	
									break;
									
									
								case 'OffAdd' :
									$content=$path.'off_addvacation.php';	
									break;
								case 'HdAdd' :
									$content=$path.'hd_addvacation.php';	
									break;
								case 'MPAdd' :
									$content=$path.'mp_addvacation.php';	
									break;
								case 'VLAdd' :
									$content=$path.'vl_addvacation.php';
									break;
								case 'SLAdd' :
									$content=$path.'sl_add.php';
									break;
								case 'SLStatus' :
									$content=$path.'leave_track.php';		
									break;
								case 'SLTrack' :
									$content=$path.'leave_track_status.php';
									break;
								case 'SLAll' :
									$content=$path.'all_approve.php';
									break;
								case 'VacationStatus' :
									$content=$path.'vl_leave_track.php';
									break;
								case 'VStatus' :
									$content=$path.'vl_track_status.php';	
									break;
								case 'VAll' :
									$content=$path.'vl_all_approve.php';	
									break;
								case 'MPTrack' :
									$content=$path.'mp_leave_track.php';	
									break;
								case 'MPStatus' :
									$content=$path.'mp_track_status.php';	
									break;
								case 'MPAll' :
									$content=$path.'mp_all_approve.php';	
									break;
								case 'HdTrack' :
									$content=$path.'hd_leave_track.php';	
									break;
								case 'HdStatus' :
									$content=$path.'hd_track_status.php';	
									break;
								case 'HdAppAll' :
									$content=$path.'hd_all_approve.php';	
									break;
								case 'OffTrack' :
									$content=$path.'off_leave_track.php';	
									break;
								case 'OffStatus' :
									$content=$path.'off_track_status.php';	
									break;
								case 'OffAppAll' :
									$content=$path.'off_all_approve.php';	
									break;
								case 'VacationLeft' :
									$content=$path.'vl_daysleft.php';
									break;
								case 'MPLeft' :
									$content=$path.'mp_daysleft.php';	
									break;
											
									
								default :
									$content =$path.'Dashboard.php';

								}
							include ($path.'menu.php');
							?>
			</div>
		</div>
			<?php
			include $content;
			?>
		</div>		
