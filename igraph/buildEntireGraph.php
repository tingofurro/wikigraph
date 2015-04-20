<?php
include_once('../dbco.php');
include_once('../mainFunc.php');


$e = mysql_query("SELECT * FROM wg_link ORDER BY id");
$edges = array();
while($ed = mysql_fetch_array($e)) {
	array_push($edges, $ed['from']." ".$ed['to']);
}
$src = getDocumentRoot()."/igraph/graph.json";
$fh = fopen($src, 'w');
fwrite($fh, implode("\n", $edges));
fclose($fh);

?>