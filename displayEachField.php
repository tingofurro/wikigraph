<?php
include('init.php');
topMenu($root);
$r = mysql_query("SELECT * FROM wg_page ORDER BY id");
$fieldList = array('algebra', 'analysis', 'arithmetic', 'calculus', 'combinatorics', 'game_theory', 'geometry', 'graph_theory', 'logic', 'number_theory', 'order_theory', 'prob_stats', 'topology'); 
$perField = array();

while($re = mysql_fetch_array($r)) {
	$fields = array();
	foreach ($fieldList as $f) {$fields[$f] = $re[$f];}
	arsort($fields);
	$keys = array_keys($fields);
	if($fields[$keys[0]] > 0.02) {
		if(!isset($perField[$keys[0]])) {$perField[$keys[0]] = 0;}
		$perField[$keys[0]] ++;
	}
}
echo "<br /><Br />";
foreach ($perField as $field => $count) {
	echo "<b>$field</b>: ".$count." articles<br />";
}
echo "<b>TOTAL: </b>".array_sum($perField)."<br />";

?>
