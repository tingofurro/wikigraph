<?php
function generateGraph() {
	$sp = str_repeat(' ', 3);
	$txt = "{\n";
	$txt .= $sp."\"nodes\": [\n";
		$listNode = array();
		$n = mysql_query("SELECT * FROM wg_category WHERE distance<=2 ORDER BY id");
		$nodes = array();
		while($no = mysql_fetch_array($n)) {
			array_push($nodes, $sp.$sp."{\"name\": \"".$no['name']."\", \"group\": ".$no['distance']." }");
			array_push($listNode, $no['id']);
		}
		$txt .= implode(", \n", $nodes);
	$txt .= "\n], \n";
	$txt .= "\"links\": [\n";
		$listNode = implode(", ", $listNode);
		$e = mysql_query("SELECT * FROM wg_catlink WHERE (catto IN(".$listNode.") OR catfrom IN(".$listNode.")) ORDER BY id");
		$edges = array();
		while($ed = mysql_fetch_array($e)) {
			array_push($edges, $sp.$sp."{\"source\": ".($ed['catto']-1).", \"target\": ".($ed['catfrom']-1).", \"value\": 2 }");
		}
		$txt .= implode(", \n", $edges);
	$txt .= "\n]\n";
	$txt .= "}";
	$fh = fopen('json/catGraph.json', 'w'); fwrite($fh, $txt);
	// echo nl2br($txt);
}
?>