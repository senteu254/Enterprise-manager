<?php
if(isset($_POST['Save'])){

if(isset($_GET['Edit']) && $_GET['Edit']=='Yes' && isset($_GET['id']) && is_numeric($_GET['id'])){

$sql = "UPDATE leave_holidays SET name='".$_POST['name']."',date='".FormatDateForSQL($_POST['dates'])."',repeat_annually='".$_POST['repeat']."' WHERE id=".$_GET['id']." ";
$qry = DB_query($sql);
	if ($qry  ){
	prnMsg( _('Information Succesfully Updated'), 'success');
	}
	else{
	prnMsg( _('Information Not Updated'), 'error');
		}
				unset($_POST['name']);
				unset($_POST['dates']);
				unset($_POST['repeat']);
				unset($_GET['Edit']);
				unset($_GET['id']);

}else{
		$ErrMsg = _('The employee details cannot be inserted because');
		$sql = "INSERT INTO leave_holidays
	(name,date,repeat_annually,addedby)
						values('".$_POST['name']."','".FormatDateForSQL($_POST['dates'])."','".$_POST['repeat']."','".$_SESSION['UserID']."')";
		$qry = DB_query($sql,$ErrMsg);
			if ($qry){
			 prnMsg(_("Information added Successfully"),'success');
			 echo '<p></p>';
				}
			else {
			prnMsg(_("Not Added Please Try Again"),'error');
			echo '<p></p>';
			 }
	}

}
if(isset($_GET['id']) && is_numeric($_GET['id'])){
if(isset($_GET['Edit']) && $_GET['Edit']=='Yes'){

$sql="select name,date,repeat_annually from leave_holidays where id='".$_GET['id']."'";
$qry1 = DB_query($sql);
$rec = DB_fetch_array($qry1);
$_POST['name'] = $rec['name'];
$_POST['dates'] = date('d/m/Y',strtotime($rec['date']));
$_POST['repeat'] = $rec['repeat_annually'];

}elseif(isset($_GET['Delete']) && $_GET['Delete']=='Yes'){

$sql="DELETE FROM leave_holidays where id='".$_GET['id']."'  ";
echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.confirm('Are you sure you wish to delete this Record ?)
    </SCRIPT>");
$qry1 = DB_query($sql);

if ($qry1){
prnMsg( _('Information Deleted'), 'success');
	
						}
unset($_GET['Edit']);
unset($_GET['id']);
}
else{
prnMsg( _('Information  Not Deleted'), 'error');
}
}
?>
<form enctype="multipart/form-data" method="post" class="form-horizontal">
<?php
echo '<link href="' . $RootPath . '/css/'. $_SESSION['Theme'] .'/default.css" rel="stylesheet" type="text/css" />';
			echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
			?>
					<div class="container-fluid">
						<div class="box-body">
              <div class="form-group">
			<div class="col-md-4">Description
				<input name="name"  type="text"  class="form-control input-md" value="<?php if(isset($_POST['name'])){ echo $_POST['name']; } ?>" required=""/>
			 </div>
			<div class="col-md-4">Date
				<input type="text" class="date" alt="d/m/Y" name="dates" value="<?php if(isset($_POST['dates'])){ echo $_POST['dates']; } ?>" id="form-control" required="" />
			 </div>
			<div class="col-md-4">Repeats Annually
				<select name="repeat" class="form-control input-md">
				<?php
				if(isset($_POST['repeat']) && $_POST['repeat']==1){ 
				echo '<option value="0">No</option>
				<option selected="selected" value="1">Yes</option>'; 
				}else{
				echo '<option value="0">No</option>
				<option value="1">Yes</option>';
				}
				?>
				</select>
			 </div>
				</div>
            </div>
			<div class="box-footer">
              <div class="pull-right">
                <button type="submit" name="Save" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Save</button>
              </div>
              <button type="reset" class="btn btn-default"><i class="fa fa-times"></i> Discard</button>
            </div>
			<p></p>
			<br />
			<table style="width:100%" class="table table-hover table-striped">
                  <tbody>
                  <tr>
                    <th><input type="checkbox"></th>
                    <th class="mailbox-star">No.</th>
                    <th class="mailbox-subject">Description</th>
					<th class="mailbox-subject">Date</th>
					<th class="mailbox-subject">Repeats Annually</th>
                    <th width="50">Actions</th>
                  </tr>
				  <?php
				  $qry = "SELECT * FROM leave_holidays ORDER BY repeat_annually DESC, date ASC";
				  $rest=DB_query($qry);
				  $i =1;
				  while($row = DB_fetch_array($rest)){
                echo  '<tr>
                    <td><input type="checkbox"></td>
                    <td class="mailbox-star">'.$i.'</i></a></td>
                    <td class="mailbox-subject"><center>'.$row['name'].'</center></td>
					<td class="mailbox-subject"><center>'.($row['repeat_annually']==1 ? date('d/m',strtotime($row['date'])) : date('d/m/Y',strtotime($row['date']))).'</center></td>
					<td ><center>'.($row['repeat_annually']==1 ? 'Yes':'No').'</center></td>';
                echo "<td class='mailbox-date'><a href='index.php?Application=HR&Ref=LeaveSetting&LinkHolidays&id=".$row['id']."&Edit=Yes' data-toggle='tooltip' data-placement='top' title='Edit'><span class='glyphicon glyphicon-edit' style='font-size:20px'></span></a>
				<a href='index.php?Application=HR&Ref=LeaveSetting&Link=Holidays&id=".$row['id']."&Delete=Yes' data-toggle='tooltip' data-placement='top' title='Delete'><span class='glyphicon glyphicon-trash' style='font-size:20px'></span></a>
                  </tr>";
				  $i++;
				  }
				  ?>
				  
                  </tbody>
                </table>

						</div>
					</form>
<script type="text/javascript" src="js/jquery-1.9.1.js"></script>						
