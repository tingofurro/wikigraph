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
	set_time_limit(90);
	$sp = str_repeat(' ', 3);
	$txt = "{\n";
	$txt .= $sp."\"nodes\": [\n";
		$listNode = array();
		$thresh1 = 1; // if in my category, it has to be somewhat relevant
		$thresh2 = 30; // if not in my category, it should be highly relevant
		$f = mysql_query("SELECT * FROM wg_field WHERE id='$field'"); $fi = mysql_fetch_array($f);
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