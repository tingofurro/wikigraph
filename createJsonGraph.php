<?php
function generateCatGraph($field) {
	$sp = str_repeat(' ', 3);
	$txt = "{\n";
	$txt .= $sp."\"nodes\": [\n";
		$listNode = array();
		$n = mysql_query("SELECT * FROM wg_category WHERE ".whereField($field)." ORDER BY id");
		$nodes = array();
		while($no = mysql_fetch_array($n)) {
			array_push($nodes, $sp.$sp."{\"name\": \"".wikiToName($no['name'])."\", \"group\": ".$no['distance']." }");
			array_push($listNode, $no['id']);
		}
		$txt .= implode(", \n", $nodes);
	$txt .= "\n], \n";
	$txt .= "\"links\": [\n";
		$listNodeTxt = implode(", ", $listNode);
		$e = mysql_query("SELECT * FROM wg_catlink WHERE (catto IN(".$listNodeTxt.") AND catfrom IN(".$listNodeTxt.")) ORDER BY id");
		$edges = array();
		while($ed = mysql_fetch_array($e)) {
			array_push($edges, $sp.$sp."{\"source\": ".array_search($ed['catto'], $listNode).", \"target\": ".array_search($ed['catfrom'], $listNode).", \"value\": 2 }");
		}
		$txt .= implode(", \n", $edges);
	$txt .= "\n]\n";
	$txt .= "}";
	$fh = fopen('json/catGraph.json', 'w'); fwrite($fh, $txt);
}
function generateArticleGraph($field, $threshhold) {
	$sp = str_repeat(' ', 3);
	$txt = "{\n";
	$txt .= $sp."\"nodes\": [\n";
		$listNode = array();
		// $n = mysql_query("SELECT * FROM wg_page WHERE ".whereField($field)." AND pagerank>".$threshhold." ORDER BY id");
		// $n = mysql_query("SELECT * FROM wg_page WHERE pagerank>=0.7");
		$thresh = 15;
		// $n = mysql_query("SELECT id, pagerank, name FROM wg_page WHERE algebra>=".$thresh." OR analysis>=".$thresh." OR arithmetic>=".$thresh." OR calculus>=".$thresh." OR combinatorics>=".$thresh." OR game_theory>=".$thresh." OR geometry>=".$thresh." OR graph_theory>=".$thresh." OR logic>=".$thresh." OR number_theory>=".$thresh." OR order_theory>=".$thresh." OR prob_stats>=".$thresh." OR topology>=".$thresh."");
		$n = mysql_query("SELECT id, pagerank, name FROM wg_page WHERE algebra>=".$thresh."");
		$nodes = array();
		while($no = mysql_fetch_array($n)) {
			array_push($nodes, $sp.$sp."{\"name\": \"".$no['name']."\", \"group\": ".min(9, (floor(10*$no['pagerank'])))." }");
			array_push($listNode, $no['id']);
		}
		$txt .= implode(", \n", $nodes);
	$txt .= "\n], \n";
	$txt .= "\"links\": [\n";
		$listNodeTxt = implode(", ", $listNode);
		$e = mysql_query("SELECT * FROM wg_links WHERE (`to` IN(".$listNodeTxt.") AND `from` IN(".$listNodeTxt.")) ORDER BY id");
		$edges = array();
		while($ed = mysql_fetch_array($e)) {
			array_push($edges, $sp.$sp."{\"source\": ".array_search($ed['to'], $listNode).", \"target\": ".array_search($ed['from'], $listNode).", \"value\": 2 }");
		}
		$txt .= implode(", \n", $edges);
	$txt .= "\n]\n";
	$txt .= "}";
	$fh = fopen('json/catGraph.json', 'w'); fwrite($fh, $txt);	
}
?>