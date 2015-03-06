<?php
include('init.php');
$r = mysql_query("SELECT * FROM wg_page ORDER BY id");
$fieldList = cleanFieldList();
$perField = array();
$ids = array();
$cleanField = array();
while($re = mysql_fetch_array($r)) {
	$fields = array();
	foreach ($fieldList as $f) {$fields[$f] = $re[$f];}
	arsort($fields);
	$keys = array_keys($fields);
	if($fields[$keys[0]] > 0.02) {
		if(!isset($perField[$keys[0]])) {$perField[$keys[0]] = 0;}
		$perField[$keys[0]] ++;
		array_push($cleanField, (array_search($keys[0], $fieldList)+1));
		array_push($ids, $re['id']);
	}
}

$sql = "UPDATE wg_page SET cleanField = CASE id ";
foreach ($cleanField as $i => $cF) {$sql .= "WHEN ".$ids[$i]." THEN ".$cF." ";}
$sql .= "END WHERE id IN (".implode(",", $ids).")";
// echo $sql;
mysql_query($sql);

foreach ($perField as $field => $count) {
	echo "<b>$field</b>: ".$count." articles<br />";
}
echo "<b>TOTAL: </b>".array_sum($perField)."<br />";

?>
