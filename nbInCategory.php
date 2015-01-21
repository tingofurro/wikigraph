<?php
include_once('init.php');
$totCategories = 23;
for($cat = 1; $cat <= $totCategories; $cat ++) {
	$c = mysql_query("SELECT COUNT(*) AS count FROM wg_category WHERE (fields='$cat' OR fields LIKE '%|".$cat."|%' OR fields LIKE '".$cat."|%' OR fields LIKE '%|".$cat."')");
	$co = mysql_fetch_array($c);
	$catNam = mysql_query("SELECT * FROM wg_category WHERE (fields='$cat' OR fields LIKE '%|".$cat."|%' OR fields LIKE '".$cat."|%' OR fields LIKE '%|".$cat."') ORDER BY id LIMIT 1");
	$catName = mysql_fetch_array($catNam);
	echo "<b>".$catName['name']."</b>: ".$co['count']." categories<br />";
}
?>