<?php
include_once('../../dbco.php');
$fieldPredicting = 1;

foreach(glob('txt/*') as $file) if(is_file($file)) unlink($file);

$f = mysql_query("SELECT * FROM wg_field WHERE id!=$fieldPredicting");
while($fi = mysql_fetch_array($f)) {
	$p = mysql_query("SELECT * FROM wg_page WHERE field=".$fi['id']." ORDER BY RAND() LIMIT 5");
	while($pa = mysql_fetch_array($p)) {
		$fh = fopen('txt/'.$pa['id'].'.txt', 'w');
		$txt = strip_tags(file_get_contents('../../data/'.$pa['id'].'.txt'));
		$txt = str_replace('\n\n', '\n', $txt);
		fwrite($fh, $txt);
	}
}

?>