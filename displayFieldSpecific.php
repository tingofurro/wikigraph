<?php
include('init.php');
topMenu($root);
$field = 'geometry';
$r = mysql_query("SELECT * FROM wg_page ORDER BY $field DESC LIMIT 200");
echo '<br /><br />';

	$fieldList = array('algebra', 'analysis', 'arithmetic', 'calculus', 'combinatorics', 'game_theory', 'geometry', 'graph_theory', 'logic', 'number_theory', 'order_theory', 'prob_stats', 'topology'); 


while($re = mysql_fetch_array($r)) {
	$otherScores = array();
	foreach ($fieldList as $f) {if($f!=$field) {array_push($otherScores, $re[$f]);}}
	rsort($otherScores, -1);
	$subArr = array_splice($otherScores, 1);
	$avgScore = array_sum($subArr)/count($subArr);
	$specificity = $re[$field]/($avgScore+0.3);
	// if($specificity > 5) {
		echo (($specificity>1.7)?"<b>":"")."[".$re['name']."] Score: ".$re[$field]." | specificity: ".$specificity.(($specificity>1.7)?"</b>":"")."<br />";
	// }
}
?>
