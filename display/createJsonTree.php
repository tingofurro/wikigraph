<?php
function generateTree($source, $depth, $dbPrefix) {
	$src = getDocumentRoot()."/display/tree.json";
	if($source == 0) {
		$re = array();
		$maxDistance = $depth;
		$re['name'] = '';
		if($dbPrefix == 'ma_') $re['name'] = 'Mathematics';
		if($dbPrefix == 'ee_') $re['name'] = 'Elec. Eng.';
		if($dbPrefix == 'cs_') $re['name'] = 'Comp. Sci.';
		$re['id'] = 0; $re['level'] = 0;
		$re['children'] = 1;
	}
	else {
		$r = mysql_query("SELECT clus.*, (SELECT COUNT(*) FROM ".$dbPrefix."cluster AS temp WHERE temp.parent=clus.id) AS children FROM cluster AS clus WHERE clus.id=".$source." LIMIT 1");
		if($re = mysql_fetch_array($r)) {
			$maxDistance = $re['level']+$depth;
		}
		else echo 'Error occurred: DB not found';
	}
	$txt = writeNode($re, $maxDistance, $dbPrefix, 0);
	$fh = fopen($src, 'w'); fwrite($fh, $txt);
}

function getChildren($id, $maxDistance, $dbPrefix) {
	$r =  mysql_query("SELECT clus.*, (SELECT COUNT(*) FROM ".$dbPrefix."cluster AS temp WHERE temp.parent=clus.id AND level<=".$maxDistance.") AS children FROM ".$dbPrefix."cluster AS clus WHERE parent=".$id." AND score>1 ORDER BY score DESC LIMIT 7");
	$chil = array();
	while($re = mysql_fetch_array($r)) {
		array_push($chil, str_repeat(' ', 3*$re['level']).writeNode($re, $maxDistance, $dbPrefix, $id));
	}

	return implode(",\n", $chil);
}
function writeNode($re, $maxDistance, $dbPrefix, $parent) {
	$sp = str_repeat(' ', 3*$re['level']);
	$name = str_replace("_", " ", $re['name']);
	$txt = '{"id": '.$re['id'].', "name": "'.urldecode($name).'", "level": '.$re['level'].', "parent": '.$parent.', "class": "'.(($re['children']==0)?'badNode':'node').'"';
	if($re['children'] > 0) {$txt .= ", \n".$sp."\"children\": [\n".$sp.getChildren($re['id'], $maxDistance, $dbPrefix)."\n".$sp."]\n".$sp;}
	$txt .= "}";
	return $txt;
}
?>