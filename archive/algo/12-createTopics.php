<?php
include('../dbco.php');
include('../mainFunc.php');

$toAdd = array();
$f = mysql_query("SELECT * FROM wg_field");
while($fi = mysql_fetch_array($f)) {
	echo 'Doing: '.$fi['sname']."<br />";
	$src = getDocumentRoot()."/igraph/fields/".$fi['sname'].".txt";
	$txt = file_get_contents($src);
	$txt = preg_split('/\r\n|\n|\r/', trim($txt));
	echo count($txt);
	foreach ($txt as $toks) {
		$toks = explode(" ", $toks);
		if(count($toks) == 2) array_push($toAdd, "(NULL, '".$fi['id']."', '".$toks[1]."', '".$toks[0]."')");
	}
}

mysql_query("INSERT INTO `wg_topic` (`id`, `field`, `topic`, `page`) VALUES ".implode(", ", $toAdd).";")

?>