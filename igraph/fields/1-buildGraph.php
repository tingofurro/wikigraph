<?php
include_once('../dbco.php');
include_once('../mainFunc.php');


$thresh1 = 1; // if in my category, it has to be somewhat relevant
$thresh2 = 30; // if not in my category, it should be highly relevant

$f = mysql_query("SELECT * FROM wg_field");
while($fi = mysql_fetch_array($f)) {
	$field = $fi['id'];
	$n = mysql_query("SELECT id, PR, name, field FROM wg_page WHERE (field=$field AND ".$fi['sname'].">$thresh1) OR ".$fi['sname'].">$thresh2");
	$listNode = array();
	while($no = mysql_fetch_array($n)) array_push($listNode, $no['id']);

	$listNodeTxt = implode(", ", $listNode);
	$e = mysql_query("SELECT * FROM wg_link WHERE (`to` IN(".$listNodeTxt.") AND `from` IN(".$listNodeTxt.")) ORDER BY id");
	$edges = array();
	while($ed = mysql_fetch_array($e)) {
		array_push($edges, $ed['from']." ".$ed['to']);
	}
	$src = getDocumentRoot()."/igraph/graphs/".$fi['sname'].".json";
	$fh = fopen($src, 'w');
	fwrite($fh, implode("\n", $edges));
	fclose($fh);
}
?>