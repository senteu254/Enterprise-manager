<?php
 function getUsers() {
 
    $messages = array();
    $query = "SELECT 
          userid, 
          realname
        FROM `www_users`";
    $resultObj = DB_query($query);
    // Fetch all the rows at once.
    while ($row = DB_fetch_array($resultObj)) {
      $messages[] = $row;
    }
    
    return $messages;
  }
  $users = getUsers();
?>
  <link rel="stylesheet" href="HR_Head/dist/css/select2.min.css">
<div class="bodys">

  <div class="chat_box">
	<div class="chat_head"> Chat Box
	
	<div class="box-tools pull-right">
                    <span class="onlineid"></span>
                     <span class="msgsid"></span>
                    <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="Contacts" data-widget="chat-pane-toggle">
                      <i class="fa fa-comments"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                  </div>
	
	</div>
	<div class="chat_body"> 
	
<!--Chat Users come here-->
	</div>
  </div>
<form id="data" method="post" enctype="multipart/form-data">
<div class="msg_box" style="right:310px">
	<div class="msg_head"><span data-toggle="tooltip" class="UserName" id="UserName" style="font-size:11px;"></span> 
	<div class="box-tools pull-right">
	<ul class="navbar-nav">
					<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cogs"></i> <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu" style="font-size:10px; font-family:'Times New Roman', Times, serif;">
									<li><a onclick="return confirm('Once you delete your copy of this conversation, it cannot be undone.');" href="index.php?Application=HR">Delete Conversation</a></li>
									<li><a class="AddUser" href="#">Add a User to Chat</a></li>
								</ul>
						</li>
						</ul>
					<button type="button" class="btn btn-box-tool closes" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
	</div>
	<div class="msg_wrap">
				<div class="input-group" id="NewMembers" style="display:none;">
                      <select class="form-control select2" id="Re" name="Receipient[]" multiple="multiple" data-placeholder="Add User to Chat" style="width:100%">
					  <?php
					  if (!empty($users)) {
					  foreach ($users as $users) {
					  echo '<option value="'.$users['userid'].'">'.ucwords(strtolower($users['realname'])).'</option>';
					  }
					  }
					  ?>
                </select>
                          <span class="input-group-btn">
                            <button type="button" onclick="GetMembers();" style="height:32px;" class="btn btn-info btn-flat">Done</button>
                          </span>
                    </div>
				
		<div class="msg_body">
			
		</div>
                <span id="Preview" class="Preview"></span>
		
	<div class="msg_footer" ><div class="input-group">
					<!--<input id="Re" name="Receipient[]" type="hidden" />-->
					<?php echo '<input type="hidden" id="FormID" name="FormID" value="' . $_SESSION['FormID'] . '" />'; ?>
                      <input type="text" id="msg" name="message" placeholder="Type Message ..." class="form-control" style="background:url(HR_Head/dist/img/chat_bg.png); border:none; color:#2ecc71;">
                          <span class="input-group-btn">		  
                            <button type="button" onclick="SendMsg();" class="btn btn-success btn-flat"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
                          </span>
                    </div>
					<input id="file-input" type="file" name="file-input" style="display:none;" onchange="showname();" />
					<button type="button" data-toggle="tooltip" id="FileUpload" title="Add Files" class="btn btn-default btn-flat"><i class="fa fa-paperclip" aria-hidden="true"></i></button>
					<button type="button" data-toggle="tooltip" id="PhotoUpload" title="Add Photos" class="btn btn-default btn-flat"><i class="fa fa-file-image-o" aria-hidden="true"></i></button>	
					<button type="button" data-toggle="tooltip" title="Choose an emoji" class="btn btn-default btn-flat"><i class="fa fa-smile-o" aria-hidden="true"></i></button>
					<button type="button" data-toggle="tooltip" title="Send a Like" class="btn btn-default btn-flat"><i style="color:#0099FF" class="fa fa-thumbs-o-up" aria-hidden="true"></i></button>
					</div>
</div>
</div>	
</form>
</div>	
<audio id="chatAudio"><source src="HR_Head/Audio/notify.ogg" type="audio/ogg"><source src="HR_Head/Audio/notify.mp3" type="audio/mpeg"><source src="HR_Head/Audio/notify.wav" type="audio/wav"></audio>
	<script src="HR_Head/dist/js/app.min.js"></script>
	<script src="HR_Head/dist/js/select2.full.min.js"></script>
	

<script>
$(document).ready(function(){
$(".select2").select2();
GetNewMsg();
setInterval(GetNewMsg, 5000);
$('.form-control').keypress(function(e){
if (e.keyCode == 13) {
e.preventDefault();
SendMsg();
}
});

$('.chat_head').click(function(){
		$('.chat_body').slideToggle('slow');
	});
	$('.UserName').click(function(){
		$('.msg_wrap').slideToggle('slow');
	});
	
	$('.closes').click(function(){
		$('.msg_box').hide();
	});
	
	$('#Preview').click(function(){
		clearForm("file-input");
		$('.Preview').html('');
	});
	
	$('.AddUser').click(function(){
		$('#NewMembers').show();
	});
	
	$('.user').click(function(){

		$('.msg_wrap').show();
		$('.msg_box').show();
	});
	
	$('#FileUpload').on('click', function() {
    $('#file-input').trigger('click');
	});
	$('#PhotoUpload').on('click', function() {
    $('#file-input').trigger('click');
	});

});

	function Download(path) {
	 window.location = path;
	}

	function GetNewMsg() {
			$.ajax({
			type: "GET",
			url: "Ajax_Chat.php",
			data: "GetUsers=1",
			cache: false,
			success: function(html) { 
				getUsersOnline();
				getUsersMessages(); 
				$(".chat_body").html( html );
			}
		});
    }
	
	function getUsersOnline() {
			$.ajax({
			type: "GET",
			url: "Ajax_Chat.php",
			data: "getUsersOnline=1",
			cache: false,
			success: function(html) {    
				$(".onlineid").html( html );
			}
		});
    }
	function getUsersMessages() {
			$.ajax({
			type: "GET",
			url: "Ajax_Chat.php",
			data: "getUsersMsgs=1",
			cache: false,
			success: function(html) {    
				$(".msgsid").html( html );
			}
		});
    }
	
	function SendMsg() {
            var msg = $("#msg").val();
			var FormID = $("#FormID").val();
			var Re = $("#Re").val();
			var jsonString = JSON.stringify(Re);
			//var FileName = $("#file-input").val();
			var file_data = $('#file-input').prop('files')[0];
            var form_data = new FormData();
            form_data.append('file-input', file_data);
			form_data.append('Re', jsonString);
			form_data.append('msg', msg);
			form_data.append('FormID', FormID);
			
			$("#msg").val('');
			if(msg!=''){
			$.ajax({
			type: "POST",
			url: "Ajax_Chat.php",
			data: form_data,
			cache: false,
			contentType: false,
            processData: false,
			beforeSend: function () { 
				$('#msg_ajax').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
			},
			success: function(html) {  
				clearForm("file-input");
				$('.Preview').html(''); 
				//$("#msg_ajax").html( html ).insertBefore('.msg_push');
				$( html ).insertBefore('.msg_push');
				$('.msg_body').scrollTop($('.msg_body')[0].scrollHeight);
			}
		});
        }
    }
	
	$(document).ready(function (e) {
                $('#upload').on('click', function () {
                    var file_data = $('#file-input').prop('files')[0];
                    var form_data = new FormData();
                    form_data.append('file-input', file_data);
                    $.ajax({
                        url: 'upload.php', // point to server-side PHP script 
                        dataType: 'text', // what to expect back from the PHP script
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (response) {
                            $('#msg').html(response); // display success response from the PHP script
                        },
                        error: function (response) {
                            $('#msg').html(response); // display error response from the PHP script
                        }
                    });
                });
            });

	function GetMsg(sel) {
            //var receipient = $(this).val();
			$('.msg_wrap').show();
			$('.msg_box').show();
			$("#Re").val('');
			var receipient = sel;
			var UserID = "<?php echo $_SESSION['UserID']; ?>";
			if(receipient!=''){
			$.ajax({
			type: "GET",
			url: "Ajax_Chat.php",
			data: "GetMsgs=1&receipient="+receipient+"& UserID="+ UserID,
			//data: "GetMsgs=1",
			//data: ({GetMsgs:GetMsgs, receipient:receipient, UserID: UserID}),
			cache: false,
			beforeSend: function () { 
				$('.msg_body').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
			},
			success: function(html) {    
				$(".msg_body").html( html );
				$('.msg_body').scrollTop($('.msg_body')[0].scrollHeight);
			}
		});
        }
    }
	

function GetChat(userid, name){
		//document.form.Receipient.value= userid;
		document.getElementById("Re").value = userid;
		$('.UserName').html( name.replace(/@/g,' ') );
	}
function GetMembers(){
		var member = $("#Re").val();
		var text = $("#Re :selected").text();
		var members = member.slice(0,6);
		var quizArray = text.split('~');
		var finalString = quizArray.join('<br/>');
		document.getElementById("UserName").innerHTML = members;
		document.getElementById("UserName").title = finalString;
		$(".UserName").attr("data-toggle", "tooltip");
		$('#NewMembers').hide();
	}
function showname() {
      var name = document.getElementById('file-input'); 
	  var type = FileType(name.files.item(0).name);
	  var filename = name.files.item(0).name;
	  var file_name = filename.substring(0,29);
	  $('.Preview').html('<div class="input-group"><span style="text-align:left" class="btn-default  form-control btn-block btn-sm">'+ type +' '+ file_name +'</span><span class="input-group-btn"><button type="button" class="btn btn-danger btn-flat"><i class="fa fa-times" aria-hidden="true"></i></button></span></div>');
      //alert('Selected file: ' + name.files.item(0).name);
     // alert('Selected file: ' + name.files.item(0).type);
    };
function FileType(file) {
   // var ext = getExtension(filename);
	var ext = file.substr( (file.lastIndexOf('.') +1) );
    switch (ext) {
    case 'jpg':
    case 'gif':
    case 'bmp':
    case 'png':
	var n = '<i class="fa fa-file-image-o" aria-hidden="true"></i>';
	break;
	case 'doc':
	case 'docx':
	var n = '<i class="fa fa-file-word-o" style="color:blue" aria-hidden="true"></i>';
	break;
	case 'pdf':
	var n = '<i class="fa fa-file-pdf-o" style="color:red" aria-hidden="true"></i>';
	break;
	case 'xls':
	case 'csv':
	var n = '<i class="fa fa-file-excel-o" style="color:green" aria-hidden="true"></i>';
	break;
	case 'ppt':
	var n = '<i class="fa fa-file-powerpoint-o" style="color:red" aria-hidden="true"></i>';
	break;
	default:
	var n = '<i class="fa fa-file-o" aria-hidden="true"></i>';
    }
	return n;
}
function clearForm(ctrlId) {
   var old = document.getElementById(ctrlId);
   var newElm = document.createElement('input');
   newElm.type = "file";
   newElm.id = ctrlId;
   newElm.setAttribute("style", "display:none;");
   newElm.setAttribute("onchange", "showname();");
   newElm.name = old.name;
   newElm.className = old.className;
   // Put code to copy other attributes as well
   old.parentNode.replaceChild(newElm, old);
}

function PlaySound(){
      $('#chatAudio')[0].play();
  }
</script>
<style>

.direct-chat-timestamp {
    color:#bdc3c7;
}
.chat_box{
	position:fixed;
	right:20px;
	bottom:0px;
	width:270px;
}
.chat_body{
	background:white;
	height:400px;
	display:none;
	padding:5px 0px;
	overflow:auto;
	overflow-x: hidden;
	
}

.chat_head,.msg_head{
	background:#f39c12;
	color:white;
	padding:15px;
	padding-top:5px;
	font-weight:bold;
	cursor:pointer;
	border-radius:5px 5px 0px 0px;
}

.msg_box{
	position:fixed;
	display:none;
	bottom:-5px;
	width:250px;
	background:white;
	border-radius:5px 5px 0px 0px;
}

.msg_head{
	background:#3498db;
}

.msg_body{
	background:url(HR_Head/dist/img/chat_bg.png);
	height:250px;
	font-size:12px;
	padding:15px;
	overflow:auto;
	overflow-x: hidden;
}
.msg_input{
	width:100%;
	border: 1px solid white;
	border-top:1px solid #DDDDDD;
	-webkit-box-sizing: border-box; /* Safari/Chrome, other WebKit */
	-moz-box-sizing: border-box;    /* Firefox, other Gecko */
	box-sizing: border-box;  
}

.minimize{
	float:right;
	cursor:pointer;
	padding-right:5px;
	
}

.user{
	position:relative;
	padding:13px 30px;
}
.user:hover{
	background:#f8f8f8;
	cursor:pointer;

}
.online{
	content:'';
	position:absolute;
	background:#2ecc71;
	height:10px;
	width:10px;
	left:10px;
	top:15px;
	border-radius:6px;
}

.offline{
	content:'';
	position:absolute;
	background:#bdc3c7;
	height:10px;
	width:10px;
	left:10px;
	top:15px;
	border-radius:6px;
}

.msg_a{
	position:relative;
	border:solid thin #3498db;
	color:#3498db;
	padding:10px;
	padding-bottom:15px;
	min-height:10px;
	margin-bottom:5px;
	margin-right:10px;
	border-radius:5px;
}

.msg_a:after{
	content:"";
	position:absolute;
	width:0px;
	height:0px;
	border: 10px solid;
	border-color: transparent #000030 transparent transparent;
	left:-18px;
	top:7px;
}
.msg_a:before{
	content:"";
	position:absolute;
	width:0px;
	height:0px;
	border: 10px solid;
	border-color: transparent #3498db transparent transparent;
	left:-20px;
	top:7px;
}

.msg_b{
	border:solid thin #2ecc71;
	color:#2ecc71;
	padding:10px;
	padding-bottom:15px;
	min-height:15px;
	margin-bottom:5px;
	position:relative;
	margin-left:10px;
	border-radius:5px;
	word-wrap: break-word;
}
.msg_b:after{
	content:"";
	position:absolute;
	display:inline-block;
	width:0px;
	height:0px;
	top:6px;
	border-bottom:8px solid transparent;
	border-left:8px solid #000030;
	border-top: 8px solid transparent;
	right:-7px;
	top:7px;
}
.msg_b:before{
	content:"";
	position:absolute;
	display:inline-block;
	width:0px;
	height:0px;
	top:6px;
	border-bottom:8px solid transparent;
	border-left:8px solid #2ecc71;
	border-top: 8px solid transparent;
	right:-9px;
	top:7px;
}
textarea {
    resize: none;
}
.msg_footer{
padding-bottom:7px;
}

.active{
background:#FF9933;
}
/* Let's get this party started */
::-webkit-scrollbar {
    width: 5px;
}
 
/* Track */
::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
    -webkit-border-radius: 10px;
    border-radius: 10px;
}
 
/* Handle */
::-webkit-scrollbar-thumb {
    -webkit-border-radius: 10px;
    border-radius: 10px;
    background: rgba(255,0,0,0.8); 
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5); 
}
::-webkit-scrollbar-thumb:window-inactive {
	background: rgba(255,0,0,0.4); 
}
</style>