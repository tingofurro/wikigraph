<?php
include('dbco.php');
$r = mysql_query("SELECT cat.*, (SELECT COUNT(*) FROM wg_category AS temp WHERE temp.parent=cat.id) AS children FROM wg_category AS cat WHERE parent='0' LIMIT 1"); $re = mysql_fetch_array($r);
$txt = writeNode($re);
$fh = fopen('catTree.json', 'w'); fwrite($fh, $txt);
echo nl2br($txt);
function getChildren($id) {
	$r =  mysql_query("SELECT cat.*, (SELECT COUNT(*) FROM wg_category AS temp WHERE temp.parent=cat.id AND distance<=2) AS children FROM wg_category AS cat WHERE parent='".$id."'");
	$chil = array();
	while($re = mysql_fetch_array($r)) {
		array_push($chil, str_repeat(' ', 3*$re['distance']).writeNode($re));
	}

	return implode(",\n", $chil);
}
function writeNode($re) {
	$sp = str_repeat(' ', 3*$re['distance']);
	$txt = '{"name": "'.str_replace("_", " ", $re['name']).'"';
	if($re['children'] > 0) {$txt .= ", \n".$sp."\"children\": [\n".$sp.getChildren($re['id'])."\n".$sp."]\n".$sp;}
	$txt .= "}";
	return $txt;
}
?>