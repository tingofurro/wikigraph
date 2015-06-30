<?php
include_once('../dbco.php');
include_once('../mainFunc.php');
include_once('func.php');
set_time_limit(3600);
header( 'Content-type: text/html; charset=utf-8' );

$hasKeywords = false;
$col = mysql_query("SHOW COLUMNS FROM wg_page;");
while($colu = mysql_fetch_array($col)) {
	if($colu[0] == 'keywords') $hasKeywords = true;
}
if(!$hasKeywords) mysql_query("ALTER TABLE `wg_page` ADD `keywords` TEXT NOT NULL DEFAULT '' AFTER `field`");

$python = wherePython();
$pyscript = '"'.getDocumentRoot().'/algo/11-articleLoader.py"';
$param1 = '"'.getDocumentRoot().'"';
$script = $python.' '.$pyscript.' '.$param1;
//echo $script;
$left = mysql_query("SELECT * FROM wg_page WHERE field!=0 AND keywords=''");
while($left = mysql_fetch_array($left)) {
	$pages = array();
	$f = mysql_query("SELECT * FROM wg_field");
	while($fi = mysql_fetch_array($f)) {
		$p = mysql_query("SELECT * FROM wg_page WHERE field=".$fi['id']." ORDER BY (CASE WHEN CHAR_LENGTH(keywords)=0 THEN 0 ELSE 1 END), PR DESC LIMIT 10");
		while($pa = mysql_fetch_array($p)) {
			$txt = strip_tags(file_get_contents('../data/'.$pa['id'].'.txt'));
			$txt = preg_replace('#(\r\n?|\n){2,}#', '$1$1', $txt);
			$txt = $pa['name']."\n\n".$txt;
			$fh = fopen('11-texts/'.$pa['id'].'.txt', 'w'); fwrite($fh, $txt);
			array_push($pages, $pa['id']);
		}
	}
	exec($script, $output);
	$sql = "UPDATE wg_page SET keywords = CASE id ";
	foreach ($pages as $page) {
		$keywords = preg_split('/\r\n|\n|\r/', file_get_contents('11-results/'.$page.'.txt'));
		$sql .= "WHEN ".$page." THEN \"".implode("[]", $keywords)."\" ";
	}
	$sql .= "END WHERE id IN (".implode(",", $pages).")";
	mysql_query($sql);

	emptyFolder('11-texts'); emptyFolder('11-results');
	echo 'Done with '.count($pages)." pages<br />";
	ob_flush(); flush();
	$left = mysql_query("SELECT * FROM wg_page WHERE field!=0 AND keywords=''");
}
?>