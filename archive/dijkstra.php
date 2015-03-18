<?php
include_once('init.php');
$n = mysql_query("SELECT * FROM wg_page ORDER BY id");
$nodes = array();
$edgesFrom = array(); $edgesTo = array();
$distance = array();
while ($no = mysql_fetch_array($n)) {
	array_push($nodes, $no['id']);
	$edgesFrom[$no['id']] = array();
	$edgesTo[$no['id']] = array();
	$distance[$no['id']] = 10000;
}
$e = mysql_query("SELECT * FROM wg_link ORDER BY id");
while($ed = mysql_fetch_array($e)) {
	array_push($edgesFrom[$ed['from']], $ed['to']);
	array_push($edgesTo[$ed['to']], $ed['from']);
}
$countFrom = 0; $countTo = 0; $countSad = 0;
foreach ($edgesFrom as $i => $edges) {
	if(count($edgesTo[$i]) == 0) {$countTo ++;}
	if(count($edges) == 0) {$countFrom ++;}
	if(count($edgesTo[$i]) == 0 AND count($edges) == 0) {$countSad ++;}
}
echo "No outgoing: ".$countFrom."<br />";
echo "No incoming: ".$countTo."<br />";
echo "None of both: ".$countSad."<br />";
// $center = 272; // we're going to calculate the ditance from all nodes to this guy :)
// $distance[$center] = 0;
// $visited = array();
// $toVisit = array($center);
// while(count($toVisit) > 0) {
// 	$elemAt = array_shift($toVisit); $myDist = $distance[$elemAt];
// 	foreach ($edgesFrom[$elemAt] as $edgeTo) {
// 		if(($myDist+1) < $distance[$edgeTo]) { // yay :)
// 			$distance[$edgeTo] = $myDist+1;
// 			if(!in_array($edgeTo, $visited)) {
// 				array_push($toVisit, $edgeTo);
// 			}
// 		}
// 	}
// 	array_push($visited, $elemAt);
// }
// echo 'dists = ['.implode(", ", $distance).']';
?>