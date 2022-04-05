// JavaScript Document
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js">

$(function() {
$(".comment_button").click(function() {

var test = $("#state").val();
var doc = $("#doc").val();
var dataString = 'content='+ test + '&doc='+ doc;

if(test=='')
{
alert("Please Enter Some Text");
}
if(doc=='')
{
alert("Sorry invalid Requist Please Try Again");
}
else
{
$("#flash").show();
$("#flash").fadeIn(400).html('<img src="irq/images/spinner.gif" align="absmiddle"> <span class="loading">Please Wait...</span>');

$.ajax({
type: "POST",
url: "irq/get_state.php",
data: dataString,
cache: false,
success: function(html){
$("#display").after(html);
document.getElementById('content').value='';
document.getElementById('content').focus();
document.getElementById('popDiv').style.display = 'none';
$("#flash").hide();
}
});
} return false;
});
});