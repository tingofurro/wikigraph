<?php
include_once('dbco.php');
include_once('mainFunc.php');

$file = getDocumentRoot()."/display/pies/0.csv";

$clus1 = array();
$c = mysql_query("SELECT cluster1, COUNT(*) AS count FROM wg_page WHERE cluster1!=0 GROUP BY cluster1 ORDER BY COUNT(*) DESC");
$txt = "id,clus,articles\n";
while($cl = mysql_fetch_array($c)) {
	array_push($clus1, $cl['cluster1']);
	$clu = mysql_query("SELECT * FROM wg_cluster WHERE id=".$cl['cluster1']); $clus = mysql_fetch_array($clu);
	$name = array_unique(explode(" ", str_replace(",", " ", $clus['name'])));
	$name = implode(" ", $name);
	$txt .= $clus['id'].",".$name.",".$cl['count']."\n";
}
$fh = fopen($file, 'w'); fwrite($fh, $txt);
$cLis = mysql_query("SELECT * FROM wg_cluster ORDER BY id");
while($cList = mysql_fetch_array($cLis)) {
	$parent = $cList['id'];
	$c = mysql_query("SELECT cluster".($cList['level']+1).", COUNT(*) AS count FROM wg_page WHERE cluster".$cList['level']."=".$parent." AND cluster".($cList['level']+1)."!=0 GROUP BY cluster".($cList['level']+1)." ORDER BY COUNT(*) DESC");
	$txt = "id,clus,articles\n";
	$file = getDocumentRoot()."/display/pies/".$parent.".csv";
	while($cl = mysql_fetch_array($c)) {
		$clu = mysql_query("SELECT * FROM wg_cluster WHERE id=".$cl['cluster'.($cList['level']+1)]); $clus = mysql_fetch_array($clu);
		$name = array_unique(explode(" ", str_replace(",", " ", $clus['name'])));
		$name = implode(" ", $name);
		$txt .= $clus['id'].",".$name.",".$cl['count']."\n";
	}
	$fh = fopen($file, 'w'); fwrite($fh, $txt);
}

?>
<html>
<!DOCTYPE html>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/pie.css">
<?php include_once('header.php'); ?>
<body>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script type="text/javascript">
		var webroot = '<?php echo $realRoot; ?>';
	</script>
	<script type="text/javascript" src="<?php echo $realRoot; ?>JS/pie.js"></script>
</body>
</html>