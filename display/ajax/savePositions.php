<?php
include('../../dbco.php');
if(isset($_POST['cleanField']) AND isset($_POST['d']) AND isset($_POST['graphType'])) {
	$cf = mysql_real_escape_string($_POST['cleanField']); $graphType = mysql_real_escape_string($_POST['graphType']); 
	$data = mysql_real_escape_string($_POST['d']);
	$data = explode("[]", $data);
	$idList = array();
	foreach ($data as $i => $d) {
		$data[$i] = explode("|", $d);
		array_push($idList, $data[$i][0]);
	}

	$sp = str_repeat(' ', 3);
	$txt = "{\n";
	$txt .= $sp."\"nodes\": [\n";
	$nodes = array();
	foreach ($data as $i => $d) {
		array_push($nodes, $sp.$sp."{\"id\": ".$d[0].", \"x\": ".$d[1].", \"y\": ".$d[2].",  \"name\": \"".str_replace("\'", "'", $d[3])."\", \"group\": ".$d[4]." }");
	}
	$txt .= implode(", \n", $nodes);
	$txt .= "\n], \n";
	$txt .= "\"links\": [\n";
	if($graphType == 'art') {
		$e = mysql_query("SELECT `to`, `from` FROM wg_links WHERE (`to` IN(".implode(", ", $idList).") AND `from` IN(".implode(", ", $idList).")) ORDER BY id");
	}
	else {
		$e = mysql_query("SELECT catto AS `to`, catfrom AS `from` FROM wg_catlink WHERE (catto IN(".implode(", ", $idList).") AND catfrom IN(".implode(", ", $idList).")) ORDER BY id");
	}
	$edges = array();
	while($ed = mysql_fetch_array($e)) {
		array_push($edges, $sp.$sp."{\"source\": ".array_search($ed['to'], $idList).", \"target\": ".array_search($ed['from'], $idList).", \"value\": 2 }");
	}

	$txt .= implode(", \n", $edges);
	$txt .= "\n]\n";
	$txt .= "}";


	$fh = fopen('../json/'.$graphType.'-'.$cf.'.json', 'w');
	fwrite($fh, $txt);
	echo '1';
}
else {
	echo '0';
}
?>