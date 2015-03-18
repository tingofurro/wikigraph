<?php
function generateTree($source, $depth) {
	$r = mysql_query("SELECT cat.*, (SELECT COUNT(*) FROM wg_category AS temp WHERE temp.parent=cat.id) AS children FROM wg_category AS cat WHERE cat.id=".$source." LIMIT 1");
	if($re = mysql_fetch_array($r)) {
		$maxDistance = $re['distance']+$depth;
		$txt = writeNode($re, $maxDistance);
		$src = getDocumentRoot()."/display/json/catTree.json";
		$fh = fopen($src, 'w'); fwrite($fh, $txt);
	}
	else {
		echo 'Error occurred: DB not found';
	}
}

function getChildren($id, $maxDistance) {
	$r =  mysql_query("SELECT cat.*, (SELECT COUNT(*) FROM wg_category AS temp WHERE temp.parent=cat.id AND distance<=".$maxDistance.") AS children FROM wg_category AS cat WHERE parent='".$id."'");
	$chil = array();
	while($re = mysql_fetch_array($r)) {
		array_push($chil, str_repeat(' ', 3*$re['distance']).writeNode($re, $maxDistance));
	}

	return implode(",\n", $chil);
}
function writeNode($re, $maxDistance) {
	$sp = str_repeat(' ', 3*$re['distance']);
	$txt = '{"name": "'.str_replace("_", " ", $re['name']).'", "class": "'.($re['killBranch']?'badNode':'node').'"';
	if($re['children'] > 0) {$txt .= ", \n".$sp."\"children\": [\n".$sp.getChildren($re['id'], $maxDistance)."\n".$sp."]\n".$sp;}
	$txt .= "}";
	return $txt;
}
?>