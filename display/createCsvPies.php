<?php
$file = getDocumentRoot()."/display/pies/0.csv";
$c = mysql_query("SELECT cluster1, COUNT(*) AS count FROM page WHERE cluster1!=0 GROUP BY cluster1 ORDER BY COUNT(*) DESC");
$txt = "id,clus,articles\n";
while($cl = mysql_fetch_array($c)) {
	$clu = mysql_query("SELECT * FROM cluster WHERE id=".$cl['cluster1']); $clus = mysql_fetch_array($clu);
	$name = array_unique(explode(" ", str_replace(",", " ", $clus['name'])));
	$name = implode(" ", $name);
	$txt .= $clus['id'].",".$name.",".$cl['count']."\n";
}
$fh = fopen($file, 'w'); fwrite($fh, $txt);

$cLis = mysql_query("SELECT * FROM cluster WHERE id>=2 AND level<=4 ORDER BY id");
while($cList = mysql_fetch_array($cLis)) {
	$parent = $cList['id'];
	$c = mysql_query("SELECT cluster".($cList['level']+1).", COUNT(*) AS count FROM page WHERE cluster".$cList['level']."=".$parent." AND cluster".($cList['level']+1)."!=0 GROUP BY cluster".($cList['level']+1)." ORDER BY COUNT(*) DESC");
	$txt = "id,clus,articles\n";
	$file = getDocumentRoot()."/display/pies/".$parent.".csv";
	while($cl = mysql_fetch_array($c)) {
		$clu = mysql_query("SELECT * FROM cluster WHERE id=".$cl['cluster'.($cList['level']+1)]); $clus = mysql_fetch_array($clu);
		$name = array_unique(explode(" ", str_replace(",", " ", $clus['name'])));
		$name = implode(" ", $name);
		$txt .= $clus['id'].",".$name.",".$cl['count']."\n";
	}
	$fh = fopen($file, 'w'); fwrite($fh, $txt);
}

?>