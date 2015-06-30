<?php
/*
OBJECTIVE:
Determine one clean field for each page
For now: Choose max(diffusion) as it shows to what field page you are the "closest"
Additional requirement: have a minimal PR of 0.02, otherwise you don't get a field
*/

include('../dbco.php');

$hasVisited = false; $col = mysql_query("SHOW COLUMNS FROM wg_page;");
while($colu = mysql_fetch_array($col)) {if($colu[0] == 'field') {$hasVisited = true;}}
if (!$hasVisited) mysql_query("ALTER TABLE `wg_page` ADD `field` INT NOT NULL DEFAULT '0' AFTER `category`");

$fieldList = array();
$fieldIds = array();
$f = mysql_query("SELECT * FROM wg_field ORDER BY id");
while($fi = mysql_fetch_array($f)) {array_push($fieldList, $fi['sname']); array_push($fieldIds, $fi['id']);}

$perField = array();
$ids = array();
$p = mysql_query("SELECT * FROM wg_page ORDER BY id");
$cleanField = array();
while($pa = mysql_fetch_array($p)) {
	$fields = array();
	foreach ($fieldList as $f) {$fields[$f] = $pa[$f];}
	arsort($fields); // reverse sort, keeping keys the way they are
	$keys = array_keys($fields);
	if($fields[$keys[0]] > 0.02) {
		if(!isset($perField[$keys[0]])) {$perField[$keys[0]] = 0;}
		$perField[$keys[0]] ++;
		array_push($cleanField, $fieldIds[array_search($keys[0], $fieldList)]);
		array_push($ids, $pa['id']);
	}
}

$sql = "UPDATE wg_page SET field = CASE id ";
foreach ($cleanField as $i => $cF) {$sql .= "WHEN ".$ids[$i]." THEN ".$cF." ";}
$sql .= "END WHERE id IN (".implode(",", $ids).")";
mysql_query($sql);

foreach ($perField as $field => $count) echo "<b>$field</b>: ".$count." articles<br />";
echo "<b>TOTAL: </b>".array_sum($perField)."<br />";

?>
