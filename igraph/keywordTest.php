<?php
include_once('../dbco.php');
include_once('../mainFunc.php');

$field = 1; $topic = 5;

$f = mysql_query("SELECT * FROM wg_field WHERE id=".$field); $fi = mysql_fetch_array($f);
$src = getDocumentRoot()."/igraph/fields/".$fi['sname'].".txt";
$groups = file_get_contents($src); $groups = preg_split('/\r\n|\n|\r/', trim($groups));
$nodes = array();
foreach ($groups as $toks) {
	$tok = explode(" ", $toks);
	if(count($tok) == 2 AND $tok[1] == $topic) array_push($nodes, $tok[0]);
}
$p = mysql_query("SELECT * FROM wg_page WHERE id IN (".implode(", ", $nodes).")");
while($pa = mysql_fetch_array($p)) {
	$txt = strip_tags(file_get_contents('../data/'.$pa['id'].'.txt'));
	$txt = preg_replace('#(\r\n?|\n){2,}#', '$1$1', $txt);
	$txt = $pa['name']."\n\n".$txt;
	$fh = fopen('txt/'.$pa['id'].'.txt', 'w'); fwrite($fh, $txt);
}

?>