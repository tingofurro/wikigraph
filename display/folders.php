<?php
include('../dbco.php');
$dash = '-';
$space = ' '; //str_repeat(input, multiplier)
echo 'hey';
$currentLevel = 0; $parent = 0;
echo "Mathematics<br />";
echo "-----------<br />";

dfsDisplay($currentLevel, $parent);


function dfsDisplay($currentLevel, $parent) {
	$dash = '-';
	$space = '&nbsp;'; //str_repeat(input, multiplier)
	$s = mysql_query("SELECT * FROM wg_cluster WHERE level=".($currentLevel+1)." AND parent=".$parent." ORDER BY score DESC");
	while($se = mysql_fetch_array($s)) {
		$c = mysql_query("SELECT COUNT(*) AS count FROM wg_page WHERE cluster".($currentLevel+1)."=".$se['id']); $co = mysql_fetch_array($c);
		echo str_repeat($space, 5*($currentLevel+1)).$se['level']."|".$se['name']." (".$co['count'].")<br />";
		echo str_repeat($space, 2+5*($currentLevel+1)).str_repeat($dash, strlen($se['name'])).'<br />';
		if($currentLevel<=0) {dfsDisplay($currentLevel+1, $se['id']);}
	}
}
?>