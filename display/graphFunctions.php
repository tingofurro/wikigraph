<?php
function nodes2Graph($nodes, $file, $lvl=1) {
	$sp = str_repeat(' ', 3);
	$nodesTxt = array();
	$listNodeTxt = implode(", ", $nodes);
	$PR = array(); $clust = array(); $names = array(); $keywords = array();
	$n = mysql_query("SELECT id, PR, name, cluster".$lvl.", keywords FROM wg_page WHERE id IN (".implode(",", $nodes).") ORDER BY id");
	while($no = mysql_fetch_array($n)) {
		$PR[$no['id']] = $no['PR']; $clus[$no['id']] = $no['cluster'.$lvl];
		$names[$no['id']] = $no['name'];
		$keywords[$no['id']] = shorterName($no['keywords'],5);
	}
	$minPR = min(array_values($PR));
	$maxPR = max(array_values($PR));
	$minDist = 5;
	$maxDist = 200;
	$goodNodes = array(); $edg = array();
	$e = mysql_query("SELECT * FROM wg_link WHERE (`to` IN(".$listNodeTxt.") AND `from` IN(".$listNodeTxt.")) ORDER BY id");
	while($ed = mysql_fetch_array($e)) {
		$thisPR = min($PR[$ed['to']], $PR[$ed['from']]);
		$dist = ceil(($maxDist-$minDist)*$thisPR/($maxPR-$minPR) + $minDist);
		// $dist = 20; // constan $dist = $maxDist-$dist; // reverse
		array_push($edg, array("from"=> $ed['from'], "to" => $ed['to'], "dist" => $dist));
		array_push($goodNodes, $ed['from']); array_push($goodNodes, $ed['to']);
	}
	$goodNodes = array_unique($goodNodes);
	$gn = array();
	foreach ($goodNodes as $g) array_push($gn, $g);
	sort($gn);
	$edges = array();
	foreach ($gn as $n) array_push($nodesTxt, $sp.$sp."{\"id\": ".$n.", \"name\": \"".$names[$n]."\", \"group\": ".$clus[$n].", \"keywords\": \"".$keywords[$n]."\"}");
	foreach ($edg as $e) array_push($edges, $sp.$sp."{\"source\": ".array_search($e['from'], $gn).", \"target\": ".array_search($e['to'], $gn).", \"value\": ".$e['dist']."}");

	$txt = "{\n";
	$txt .= $sp."\"nodes\": [\n";
		$txt .= implode(", \n", $nodesTxt);
	$txt .= "\n], \n";
	$txt .= "\"links\": [\n";
		$txt .= implode(", \n", $edges);
	$txt .= "\n]\n";
	$txt .= "}";
	$fh = fopen($file, 'w'); fwrite($fh, $txt);
}
function generateMatrix($nodes) {
	$nbNodes = count($nodes); $mat = array();
	for ($r=0; $r < $nbNodes; $r++) { 
		$mat[$r] = array();
		for ($c=0; $c < $nbNodes; $c++) $mat[$r][$c] = 0;
	}
	$e = mysql_query("SELECT * FROM wg_link WHERE `to` IN (".implode(",", $nodes).") AND `from` IN (".implode(",", $nodes).")");
	while($ed = mysql_fetch_array($e)) {
		$r = array_search($ed['from'], $nodes);
		$c = array_search($ed['to'], $nodes);
		$mat[$r][$c] = 1;
	}
	$ret = "adja = [\n";
	for ($r=0; $r < $nbNodes; $r++) { 
		$mat[$r] = implode(", ", $mat[$r]);
	}
	$ret .= implode(";\n", $mat);
	$ret .= '];';
	return $ret;
}
?>