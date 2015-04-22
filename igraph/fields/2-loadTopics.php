<?php
include('../dbco.php');
include('../mainFunc.php');
$f = mysql_query("SELECT * FROM wg_field WHERE id=1");
while($fi = mysql_fetch_array($f)) {
	$page = mysql_query("SELECT * FROM wg_page WHERE field=".$fi['id']);
	$pageList = array();
	while($pages = mysql_fetch_array($page)) array_push($pageList, $pages['id']);

	$src = getDocumentRoot()."/igraph/fields/".$fi['sname'].".txt";
	$txt = file_get_contents($src);
	$txt = preg_split('/\r\n|\n|\r/', trim($txt));
	$sql = "UPDATE wg_page SET topic = CASE id ";
	foreach ($txt as $toks) {
		$tok = explode(" ", $toks);
		$sql .= "WHEN ".$tok[0]." THEN ".$tok[1]." ";
	}
	$sql .= "END WHERE id IN (".implode(", ", $pageList).")";
	mysql_query($sql);
}
?>