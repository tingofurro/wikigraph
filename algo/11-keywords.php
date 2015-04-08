<?php
include_once('../dbco.php');
include_once('../mainFunc.php');
set_time_limit(3600);

$hasKeywords = false;
$col = mysql_query("SHOW COLUMNS FROM wg_page;");
while($colu = mysql_fetch_array($col)) {
	if($colu[0] == 'PR') $hasKeywords = true;
}
if(!$hasKeywords) mysql_query("ALTER TABLE `wg_page` ADD `keywords` TEXT NOT NULL DEFAULT '' AFTER `field`");

$pages = array();
$f = mysql_query("SELECT * FROM wg_field");
while($fi = mysql_fetch_array($f)) {
	$p = mysql_query("SELECT * FROM wg_page WHERE field=".$fi['id']." AND keywords='' ORDER BY PR DESC LIMIT 20");
	while($pa = mysql_fetch_array($p)) {
		$txt = strip_tags(file_get_contents('../data/'.$pa['id'].'.txt'));
		$txt = preg_replace('#(\r\n?|\n){2,}#', '$1$1', $txt);
		$txt = $pa['name']."\n\n".$txt;
		$fh = fopen('11-texts/'.$pa['id'].'.txt', 'w'); fwrite($fh, $txt);
		array_push($pages, $pa['id']);
	}
}

$python = wherePython();

$pyscript = '"'.getDocumentRoot().'/algo/11-articleLoader.py"';
echo "$python $pyscript";
exec("$python $pyscript", $output);
foreach ($pages as $page) {
	$keywords = preg_split('/\r\n|\n|\r/', file_get_contents('11-results/'.$page.'.txt'));
	mysql_query("UPDATE wg_page SET keywords=\"".implode("[]", $keywords)."\" WHERE id=$page");
}

emptyFolder('11-texts'); emptyFolder('11-results');

function emptyFolder($folderName) {
	foreach(glob($folderName.'/*') as $file) if(is_file($file)) unlink($file);
}
?>