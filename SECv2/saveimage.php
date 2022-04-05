<?php
if(isset($_POST['Delete']) && $_POST['Delete']=="Delete"){
unlink($_POST['key']);
}
//set random name for the image, used time() for uniqueness

$filename =  time() . '.jpg';
$filepath = 'visit_prfl_ic/';

move_uploaded_file($_FILES['webcam']['tmp_name'], $filepath.$filename);

echo $filepath.$filename;
?>
