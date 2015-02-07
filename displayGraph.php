<?php
	include_once('init.php');
	include_once('graphJsonCreator.php');
	$field =  5;
	if(isset($_GET['field'])) {$field = mysql_real_escape_string($_GET['field']);}
	generateGraph($field);
?>
<!DOCTYPE html>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo $root; ?>css/graphCat.css">
<body></body>
<script type="text/javascript">var webroot = '<?php echo $root; ?>';</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script src="<?php echo $root; ?>JS/graphCat.js"></script>
