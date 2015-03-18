<?php
include_once('../dbco.php');
include_once('func.php');
/*
OBJECTIVE:
Create a list of Math fields.
They are the categories at distance 1 from the CAT_ROOT.
You can for now manually clean those after it runs in the wg_field DB
*/

mysql_query("TRUNCATE wg_field");
$c = mysql_query("SELECT * FROM wg_category WHERE distance=1");
while($ca = mysql_fetch_array($c)) {
	$pageId = 0;
	$p = mysql_query("SELECT * FROM wg_page WHERE name='".$ca['name']."'");
	if($pa = mysql_fetch_array($p)) {
		$pageId = $pa['id'];
	}
	else {
		$exp = explode("_", $ca['name']); $where = array();
		foreach ($exp as $tok) array_push($where, "name LIKE '%".$tok."%'");
		$p = mysql_query("SELECT * FROM wg_page WHERE ".implode(" OR ", $where)." ORDER BY id LIMIT 1");
		if($pa = mysql_fetch_array($p)) {$pageId = $pa['id'];}
	}
	if($pageId != 0)
		mysql_query("INSERT INTO `wg_field` (`id`, `name`, `sname`, `page`, `color`) VALUES (NULL, '".wikiToName($ca['name'])."', '".strtolower($ca['name'])."', '".$pageId."', '');");
}
?>