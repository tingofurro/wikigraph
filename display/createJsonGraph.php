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
		$f = mysql_query("SELECT * FROM wg_field WHERE id='$field'"); $fi = mysql_fetch_array($f);
		// $n = mysql_query("SELECT id, pagerank, name, cleanField FROM wg_page WHERE id IN (85, 121, 154, 205, 310, 318, 365, 563, 568, 575, 586, 1144, 1149, 2355, 2374, 3125, 3183, 3258, 3281, 3282, 3332, 3368, 3672, 3710, 3744, 3764, 4118, 4584, 4626, 4628, 5193, 5225, 5325, 5862, 8995, 13072, 13540, 13543, 13821, 13831, 14142, 14411, 14448, 15035, 15585, 16124, 16136, 16173, 16240, 16744, 16754, 16757, 16762, 16765, 16767, 16782, 16840, 16853, 16859, 16868, 16903, 16911, 19722, 22535, 22599, 22603, 22643, 22725, 23640, 24529, 26050, 27934, 27943, 27951, 27961, 27972, 27982, 31856)");
		// $n = mysql_query("SELECT id, pagerank, name, cleanField FROM wg_page WHERE id IN (53, 62, 85, 318, 563, 568, 575, 585, 586, 808, 1006, 1172, 1718, 2012, 2066, 2077, 2114, 2292, 2355, 2752, 4090, 5065, 5075, 5862, 6738, 7018, 7494, 7505, 7517, 7532, 8364, 8995, 9559, 9868, 10704, 10910, 11075, 12278, 12285, 12286, 12291, 12293, 12308, 12315, 12340, 12342, 12345, 12351, 12526, 12815, 12822, 12835, 12836, 12847, 12868, 12873, 12876, 12894, 12895, 12900, 12901, 13029, 13071, 13072, 13073, 13112, 13113, 13176, 13218, 13361, 13543, 13831, 14448, 15248, 19176, 19711, 19712, 19722, 19725, 19956, 24757, 24766, 25414, 25426, 25480, 25495, 25501, 25503, 25659, 25665, 28290, 28291, 28292, 28293, 28294, 28295, 28296, 28297, 28298, 28299, 28304, 28305, 28306, 28308, 28309, 28310, 28311, 28312, 28313)");
		// $n = mysql_query("SELECT id, pagerank, name, cleanField FROM wg_page WHERE id IN (85, 369, 563, 568, 575, 586, 916, 953, 999, 1006, 1014, 4336, 5623, 5862, 8563, 8995, 10043, 12879, 12894, 17601, 17604, 17607, 19027, 19301, 19332, 19333, 19337, 19342, 19344, 19345, 19367, 19374, 19402, 19712, 19722, 26564, 26570, 26576, 26578, 26583, 26584, 26588, 26601, 26605, 26610, 26611, 26612, 26654, 26661, 26687, 26690, 26692, 27625, 27629, 27633, 27634)");
		$n = mysql_query("SELECT id, PR, name, field FROM wg_page WHERE (field=$field AND ".$fi['sname'].">$thresh1) OR ".$fi['sname'].">$thresh2");
		$nodes = array();
		while($no = mysql_fetch_array($n)) {
			array_push($nodes, $sp.$sp."{\"id\": ".$no['id'].", \"name\": \"".$no['name']."\", \"group\": ".$no['field']." }");
			array_push($listNode, $no['id']);
		}
		$txt .= implode(", \n", $nodes);
	$txt .= "\n], \n";
	$txt .= "\"links\": [\n";
		$listNodeTxt = implode(", ", $listNode);
		$e = mysql_query("SELECT * FROM wg_link WHERE (`to` IN(".$listNodeTxt.") AND `from` IN(".$listNodeTxt.")) ORDER BY id");
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