<?php
include_once('../../dbco.php');
set_time_limit(2600);

$pages = array();
$f = mysql_query("SELECT * FROM wg_field");
while($fi = mysql_fetch_array($f)) {
	$p = mysql_query("SELECT * FROM wg_page WHERE field=".$fi['id']." AND keywords='' ORDER BY PR DESC LIMIT 20");
	while($pa = mysql_fetch_array($p)) {
		$txt = strip_tags(file_get_contents('../../data/'.$pa['id'].'.txt'));
		$txt = preg_replace('#(\r\n?|\n){2,}#', '$1$1', $txt);
		$txt = $pa['name']."\n\n".$txt;
		$fh = fopen('txt/'.$pa['id'].'.txt', 'w'); fwrite($fh, $txt);
		array_push($pages, $pa['id']);
	}
}

$python = 'C:\\Python27\\python.exe';
$pyscript = '"C:\\Program Files (x86)\\EasyPHP-12.1\\www\\Wikigraph\\algo\\keywords\\articleLoader.py"';
exec("$python $pyscript", $output);

foreach ($pages as $page) {
	$keywords = preg_split('/\r\n|\n|\r/', file_get_contents('results/'.$page.'.txt'));
	mysql_query("UPDATE wg_page SET keywords=\"".implode("[]", $keywords)."\" WHERE id=$page");
}

emptyFolder('txt'); emptyFolder('results');

function emptyFolder($folderName) {
	foreach(glob($folderName.'/*') as $file) if(is_file($file)) unlink($file);
}
?>