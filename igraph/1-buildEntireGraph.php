<?php
include_once('../dbco.php');
include_once('../mainFunc.php');


$nodes = array();
$n = mysql_query("SELECT * FROM wg_page ORDER BY PR DESC LIMIT 1000");
while($no = mysql_fetch_array($n)) array_push($nodes, $no['id']);

$e = mysql_query("SELECT * FROM wg_link WHERE (`to` IN (".implode(", ", $nodes).") AND `from` IN (".implode(", ", $nodes).")) ORDER BY id");
$edges = array();
while($ed = mysql_fetch_array($e)) {
	array_push($edges, $ed['from']." ".$ed['to']);
}
$src = getDocumentRoot()."/igraph/data/graph.json";
$fh = fopen($src, 'w');
fwrite($fh, implode("\n", $edges));
fclose($fh);

?>