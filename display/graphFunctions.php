<?php
function nodes2Graph($nodes, $file) {
	$sp = str_repeat(' ', 3);
	$txt = "{\n";
	$txt .= $sp."\"nodes\": [\n";
	$nodesTxt = array();
	$n = mysql_query("SELECT id, name, field FROM wg_page WHERE id IN (".implode(",", $nodes).")");
	while($no = mysql_fetch_array($n)) {
		array_push($nodesTxt, $sp.$sp."{\"id\": ".$no['id'].", \"name\": \"".$no['name']."\", \"group\": ".$no['field']." }");
	}
	$txt .= implode(", \n", $nodesTxt);
	$txt .= "\n], \n";
	$txt .= "\"links\": [\n";
		$listNodeTxt = implode(", ", $nodes);
		$e = mysql_query("SELECT * FROM wg_link WHERE (`to` IN(".$listNodeTxt.") AND `from` IN(".$listNodeTxt.")) ORDER BY id");
		$edges = array();
		while($ed = mysql_fetch_array($e)) {
			array_push($edges, $sp.$sp."{\"source\": ".array_search($ed['to'], $nodes).", \"target\": ".array_search($ed['from'], $nodes).", \"value\": 2 }");
		}
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