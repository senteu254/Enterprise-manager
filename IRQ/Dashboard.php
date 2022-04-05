<?php
require_once('includes/session.inc');
echo '<link href="' . $RootPath . '/css/'. $_SESSION['Theme'] .'/default.css" rel="stylesheet" type="text/css" />';
include 'graph.php';
?>
 <head>
        <script src="irq/lib/js/jquery.min.js"></script>
        <script src="irq/lib/js/chartphp.js"></script>
        <link rel="stylesheet" href="irq/lib/js/chartphp.css">
    </head> 
	<style>
    /* white color data labels */
    .jqplot-data-label{color:white;}
    </style> 
	<table style="width:90%">
  <tr>
    <td align="left" ><div id="container" class="margin" role="group"></div></td>
    </tr>

</table>