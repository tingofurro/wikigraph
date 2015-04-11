<?php
include('../../dbco.php');
$sp = str_repeat(' ', 3);
if(isset($_POST['nodes']) AND isset($_POST['links']) AND isset($_POST['toFile'])) {
	$toFile = mysql_real_escape_string($_POST['toFile']);
	$nodes = $_POST['nodes'];
	$links = $_POST['links'];
	$txt = "{\n";
	$txt .= $sp."\"nodes\": \n";
	$txt .= $nodes;
	$txt .= "\n, \n";
	$txt .= "\"links\": \n";
	$txt .= $links;
	$txt .= "\n";
	$txt .= "}";

	$fh = fopen($toFile, 'w');
	fwrite($fh, $txt);
	fclose($fh);
}
else {
	echo '0';
}
?>