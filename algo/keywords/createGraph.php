<?php
include_once('../../dbco.php');
include_once('../../mainFunc.php');

$sp = str_repeat(' ', 3);
$txt = "{\n";
$txt .= $sp."\"nodes\": [\n";
	$listNode = array();
	$n = mysql_query("SELECT * FROM wg_page WHERE keywords!=''");
	$nodes = array(); $keywords = array();
	while($no = mysql_fetch_array($n)) {
		array_push($nodes, $sp.$sp."{\"id\": ".$no['id'].", \"name\": \"".wikiToName($no['name'])."\", \"group\": ".$no['field']." }");
		array_push($listNode, $no['id']);
		$keywords[$no['id']] = explode("[]", $no['keywords']);
	}
	$txt .= implode(", \n", $nodes);
$txt .= "\n], \n";
$txt .= "\"links\": [\n";
	$edges = array(); $keywordKeys = array_keys($keywords);
	for($i = 0; $i < count($listNode)-1; $i ++) {
		for($j = $i+1; $j < count($listNode); $j ++) {
			$kw1 = $keywords[$keywordKeys[$i]];
			$kw2 = $keywords[$keywordKeys[$j]];
			$intersect = array_intersect($kw1, $kw2);
			if(count($intersect) > 1) {
				array_push($edges, $sp.$sp."{\"source\": ".$i.", \"target\": ".$j.", \"value\": 2 }");
			}
		}		
	}
	$txt .= implode(", \n", $edges);
$txt .= "\n]\n";
$txt .= "}";

$src = "graph.json";
$fh = fopen($src, 'w'); fwrite($fh, $txt);

?>