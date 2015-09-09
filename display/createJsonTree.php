<?php
function generateTree($source, $depth) {
	$src = getDocumentRoot()."/display/tree.json";
	if($source == 0) {
		$re = array();
		$maxDistance = $depth;
		$re['id'] = 0; $re['level'] = 0; $re['name'] = 'Mathematics';
		$re['children'] = 1;
	}
	else {
		$r = mysql_query("SELECT clus.*, (SELECT COUNT(*) FROM cluster AS temp WHERE temp.parent=clus.id) AS children FROM cluster AS clus WHERE clus.id=".$source." LIMIT 1");
		if($re = mysql_fetch_array($r)) {
			$maxDistance = $re['level']+$depth;
		}
		else echo 'Error occurred: DB not found';
	}
	$txt = writeNode($re, $maxDistance);
	$fh = fopen($src, 'w'); fwrite($fh, $txt);
}

function getChildren($id, $maxDistance) {
	$r =  mysql_query("SELECT clus.*, (SELECT COUNT(*) FROM cluster AS temp WHERE temp.parent=clus.id AND level<=".$maxDistance.") AS children FROM cluster AS clus WHERE parent=".$id." AND score>1 LIMIT 7");
	$chil = array();
	while($re = mysql_fetch_array($r)) {
		array_push($chil, str_repeat(' ', 3*$re['level']).writeNode($re, $maxDistance));
	}

	return implode(",\n", $chil);
}
function writeNode($re, $maxDistance) {
	$sp = str_repeat(' ', 3*$re['level']);
	$name = shorterName($re['name']);
	$txt = '{"id": '.$re['id'].', "name": "'.$name.'", "class": "'.(($re['children']==0)?'badNode':'node').'"';
	if($re['children'] > 0) {$txt .= ", \n".$sp."\"children\": [\n".$sp.getChildren($re['id'], $maxDistance)."\n".$sp."]\n".$sp;}
	$txt .= "}";
	return $txt;
}
?>