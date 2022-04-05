function setUpdateAction() {
document.frmUser.action = "update/edit_course.php";
document.frmUser.submit();
}
function setDeleteAction() {
if(confirm("Are you sure want to delete these rows?")) {
document.frmUser.action = "delete/delete_users.php";
document.frmUser.submit();
}
}
function setDeleteCourse() {
if(confirm("Are you sure want to delete these rows?")) {
document.frmUser.action = "delete/delete_course.php";
document.frmUser.submit();
}
}
function setDeleteMsg() {
if(confirm("Are you sure want to delete these rows?")) {
document.frmUser.action = "delete/delete_msg.php";
document.frmUser.submit();
}
}
