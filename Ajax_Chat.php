<?php
require_once('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
 function addMessage($senderId, $recvId, $message, $path, $name, $extension) {
    $addResult = false;  
    $cUserId = $senderId;
	$crecvId = $recvId;
    $cMessage = $message;
    $query = "INSERT INTO `chat_message`(`senderid`, `receipientid`, `content`, `filename`, `path`, `extension`)
      VALUES ('{$cUserId}','{$crecvId}', '{$cMessage}','{$name}','{$path}','{$extension}')";
    $result = DB_query($query);
    
    if ($result != false) {
	$sent = date('j M Y, g:i a');
      // Get the last inserted row id.
      $addResult = '<div class="msg_b">'.$cMessage.' <br />
	  '.($name=="" ? '' : "<div class='input-group'>
<span style='text-align:left' class='btn-default form-control btn-sm'>".CheckFileType($extension)." ".$name."</span>
<span class='input-group-btn'>		  
<button type='button' onclick=Download('".$path."'); class='btn btn-info btn-flat'><i class='fa fa-download' aria-hidden='true'></i></button>
 </span>
 </div>").'
	  <span class="direct-chat-timestamp pull-left"><i class="fa fa-check" style="font-size:thin;color:blue;"></i>'.$sent.'</span></div>';
    } else {
      $addResult = '<i class="fa fa-times" aria-hidden="true"></i>';
    }
    
    return $addResult;
  }
  
  function CheckFileType($ext){
  switch ($ext) {
    case 'jpg':
    case 'gif':
    case 'bmp':
    case 'png':
	$n = '<i class="fa fa-file-image-o" aria-hidden="true"></i>';
	break;
	case 'doc':
	case 'docx':
	$n = '<i class="fa fa-file-word-o" style="color:blue;" aria-hidden="true"></i>';
	break;
	case 'pdf':
	$n = '<i class="fa fa-file-pdf-o" style="color:red" aria-hidden="true"></i>';
	break;
	case 'xls':
	case 'csv':
	$n = '<i class="fa fa-file-excel-o" style="color:green" aria-hidden="true"></i>';
	break;
	case 'ppt':
	$n = '<i class="fa fa-file-powerpoint-o" style="color:red" aria-hidden="true"></i>';
	break;
	default:
	$n = '<i class="fa fa-file-o" aria-hidden="true"></i>';
    }
	return $n;
  }
  
 function getMessages($sender,$recv) {
    $messages = array();
    $query = "SELECT 
          content, 
          time,
		  senderid,
		  receipientid,
		  status,
		  extension,
		  path,
		  filename
        FROM `chat_message`
		WHERE ((senderid='".$sender."' AND receipientid='".$recv."') OR (senderid='".$recv."' AND receipientid='".$sender."'))
        ORDER BY `time` ASC";

    // Execute the query
    $resultObj = DB_query($query);
    // Fetch all the rows at once.
    while ($row = DB_fetch_array($resultObj)) {
      $messages[] = $row;
    }
    
    return $messages;
  }
  
  function getExtension($str) 
	{
		$i = strrpos($str,".");
		if (!$i) { return ""; }
		$l = strlen($str) - $i;
		$ext = substr($str,$i+1,$l);
		return $ext;
	}
  
  if(isset($_GET['GetMsgs']) && $_GET['GetMsgs']==1){
$messages = getMessages($_GET['UserID'],$_GET['receipient']);
$update = "UPDATE chat_message SET status=1 
			WHERE (senderid='".$_GET['receipient']."' AND receipientid='".$_GET['UserID']."')";
	DB_query($update);
if (!empty($messages)) {
        foreach ($messages as $message) {
          $msg = htmlentities($message['content'], ENT_NOQUOTES);
          $sent = date('j M Y, g:i a', strtotime($message['time']));
		echo '<div class="'.($_SESSION['UserID']==$message['senderid'] ? 'msg_b':'msg_a').'">'.$msg.' <br />
		'.($message['filename']=="" ? '' : "<div class='input-group'>
<span style='text-align:left' class='btn-default form-control btn-sm'>".CheckFileType($message['extension'])." ".$message['filename']."</span>
<span class='input-group-btn'>		  
<button type='button' onclick=Download('".$message['path']."'); class='btn btn-info btn-flat'><i class='fa fa-download' aria-hidden='true'></i></button>
 </span>
 </div>").'
		<span class="direct-chat-timestamp '.($_SESSION['UserID']==$message['senderid'] ? 'pull-left':'pull-right').'">'.($_SESSION['UserID']==$message['senderid']? '<i class="fa fa-check" style="font-size:thin;color:blue;" aria-hidden="true"></i>' : '').' '.$sent.'</span></div>';
        }
		echo '<div id="msg_ajax"></div><div class="msg_push"></div>';
      } else {
		echo '<div id="msg_ajax"><span style="margin-left: 25px; color:white;">No chat messages available!</span></div><div class="msg_push"></div>';
      }
}
  
if (isset($_POST['msg'])) {
  
  $userId = $_SESSION['UserID'];
  $recvr = json_decode(stripslashes($_POST['Re']));
  // Escape the message string
  $msg = htmlentities($_POST['msg'],  ENT_NOQUOTES);
  
    if(isset($_FILES['file-input']["name"])){
			$filename = stripslashes($_FILES["file-input"] ["name"]);
			$extension = getExtension($filename);
			$extension = strtolower($extension);
			$name = time().".".$extension;
			$type = $_FILES["file-input"] ["type"];
			$size = $_FILES["file-input"] ["size"];
			$temp = $_FILES["file-input"] ["tmp_name"];
			$error = $_FILES["file-input"] ["error"];
			$path = "companies/".$_SESSION['DatabaseName']."/Chat_Files/".$name;	
			move_uploaded_file($temp,$path);
  }else{
  $extension ="";
  $name ="";
  $path ="";
  }
  
  foreach($recvr as $d){
  $result = addMessage($userId, $d, $msg, $path, $name, $extension);
  }
  echo $result;
}

function getUsers($user) {
 
    $messages = array();
    $query = "SELECT 
          userid, 
          realname,
		  OnlineStatus,
		  (SELECT COUNT(mid) AS num
				FROM `chat_message`
				WHERE receipientid='".$_SESSION['UserID']."' AND senderid=www_users.userid AND status=0
				GROUP BY senderid) as num
        FROM `www_users` WHERE userid <>'".$_SESSION['UserID']."'
		ORDER BY OnlineStatus DESC,(SELECT COUNT(mid) AS num
				FROM `chat_message`
				WHERE receipientid='".$_SESSION['UserID']."' AND senderid=www_users.userid
				GROUP BY senderid) DESC";
    $resultObj = DB_query($query);
    // Fetch all the rows at once.
    while ($row = DB_fetch_array($resultObj)) {
      $messages[] = $row;
    }
    
    return $messages;
  }
  
  function getUsersOnline() {
 
    $query = "SELECT COUNT(userid) as online
        FROM `www_users` WHERE OnlineStatus=1 AND userid <>'".$_SESSION['UserID']."'";
    $resultObj = DB_query($query);
    $row = DB_fetch_array($resultObj);
    
    return $row['online'];
  }
  
  function getUsersMessages() {
 
    $query = "SELECT (SELECT COUNT(mid) AS num
				FROM `chat_message`
				WHERE receipientid='".$_SESSION['UserID']."' AND senderid=www_users.userid AND status=0
				GROUP BY senderid) as num
        FROM `www_users` WHERE OnlineStatus=1 AND userid <>'".$_SESSION['UserID']."'";
    $resultObj = DB_query($query);
    $row = DB_fetch_array($resultObj);
    
    return $row['num'];
  }
  
  if(isset($_GET['getUsersOnline']) && $_GET['getUsersOnline']==1){
  $online = getUsersOnline();
  echo '<span data-toggle="tooltip" title="'.($online==0 ? 'No User Online' : $online.' Users Online').'" class="badge bg-green">'.$online.'</span>';
  }
  if(isset($_GET['getUsersMsgs']) && $_GET['getUsersMsgs']==1){
  $msgs = getUsersMessages();
  echo '<span data-toggle="tooltip" title="'.($msgs==0 ? 'No New Message' : $msgs.' New Messages').'" class="badge bg-red">'.$msgs.'</span>';
  }
  
if(isset($_GET['GetUsers']) && $_GET['GetUsers']==1){
   $users = getUsers();
   if (!empty($users)) {
        foreach ($users as $users) {		
		echo "<div class='user' style='font-size:10px' onclick=GetMsg('".$users['userid']."');GetChat('".$users['userid']."','".str_replace(' ', '@', strtoupper($users['realname']))."');><span class='".($users['OnlineStatus']==1 ? 'online':'offline')."'></span>".strtoupper($users['realname'])."<span data-toggle='tooltip' title='".$users['num']." New Messages' id='badge' class='badge bg-yellow pull-right'>".$users['num']."</span></div>";
		if($users['num']>0){
	echo '<script type="text/javascript">',
			 'PlaySound();',
			 '</script>';
			 }
        }
      } else {
        echo '<span style="margin-left: 25px;">No User Online Now!</span>';
      }

  }
?>