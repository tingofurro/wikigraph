<?php
function generateCatGraph($field) {
	$field = cat2OldCat($field-1);
	$sp = str_repeat(' ', 3);
	$txt = "{\n";
	$txt .= $sp."\"nodes\": [\n";
		$listNode = array();
		$n = mysql_query("SELECT * FROM wg_category WHERE ".whereField($field)." ORDER BY id");
		$nodes = array();
		while($no = mysql_fetch_array($n)) {
			array_push($nodes, $sp.$sp."{\"id\": ".$no['id'].", \"name\": \"".wikiToName($no['name'])."\", \"group\": ".$no['distance']." }");
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

	$src = getDocumentRoot()."/display/json/catGraph.json";
	$fh = fopen($src, 'w'); fwrite($fh, $txt);
}
function generateArticleGraph($field) {
	$sp = str_repeat(' ', 3);
	$txt = "{\n";
	$txt .= $sp."\"nodes\": [\n";
		$listNode = array();
		$thresh1 = 1; // if in my category, it has to be somewhat relevant
		$thresh2 = 30; // if not in my category, it should be highly relevant
		$cleanField = cleanFieldList();
		$myName = $cleanField[($field-1)];
		$n = mysql_query("SELECT id, pagerank, name, cleanField FROM wg_page WHERE (cleanField=$field AND ".$myName.">$thresh1) OR ".$myName.">$thresh2");
		$n = mysql_query("SELECT id, pagerank, name, cleanField FROM wg_page WHERE id IN (85, 121, 154, 205, 310, 318, 365, 563, 568, 575, 586, 1144, 1149, 2355, 2374, 3125, 3183, 3258, 3281, 3282, 3332, 3368, 3672, 3710, 3744, 3764, 4118, 4584, 4626, 4628, 5193, 5225, 5325, 5862, 8995, 13072, 13540, 13543, 13821, 13831, 14142, 14411, 14448, 15035, 15585, 16124, 16136, 16173, 16240, 16744, 16754, 16757, 16762, 16765, 16767, 16782, 16840, 16853, 16859, 16868, 16903, 16911, 19722, 22535, 22599, 22603, 22643, 22725, 23640, 24529, 26050, 27934, 27943, 27951, 27961, 27972, 27982, 31856)");
		$nodes = array();
		while($no = mysql_fetch_array($n)) {
			array_push($nodes, $sp.$sp."{\"id\": ".$no['id'].", \"name\": \"".$no['name']."\", \"group\": ".$no['cleanField']." }");
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
	$src = getDocumentRoot()."/display/json/catGraph.json";
	$fh = fopen($src, 'w'); fwrite($fh, $txt);
}
?>