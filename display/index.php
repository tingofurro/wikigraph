
<?php
set_time_limit(180);
include_once('dbco.php');
include_once('mainFunc.php');
include_once('createJsonGraph.php');
$realRoot = getRealRoot();
$cluster = 0; $level = 0;
if(isset($_GET['cluster'])) {
	$c = mysql_query("SELECT * FROM wg_cluster WHERE id=".mysql_real_escape_string($_GET['cluster']));
	if($cl = mysql_fetch_array($c)) {
		$cluster = $cl['id']; $level = $cl['level'];
	}
}
$fileUrl = "display/cache/".$cluster.".json";
$fileExists = file_exists(getDocumentRoot().'/'.$fileUrl);
if(!$fileExists) generateGraph($level, $cluster);
$c = mysql_query("SELECT * FROM wg_cluster WHERE parent=".$cluster);
?>
<!DOCTYPE html>
<html>
<meta charset="utf-8">
<head>
	<title>Wikigraph</title>
	<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/index.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/graph.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="<?php echo $realRoot; ?>JS/lib/d3.js"></script>
</head>
<body>
	<div id="logo">wikigraph</div>
	<div id="clusterNameContain">
	<?php
	while($cl = mysql_fetch_array($c)) {
		?> <a href="<?php echo $root;?>/graph/<?php echo $cl['id']; ?>"><div class="clusterName" value="<?php echo $cl['id']; ?>"><?php echo $cl['name']; ?></div></a><?php
	}
	?>
		
	</div>
	<script src="<?php echo $realRoot; ?>JS/graph.js"></script>
	<script type="text/javascript">
		var webroot = '<?php echo $realRoot; ?>';
		var color = d3.scale.category20();
			<?php if($fileExists) { ?> plotGraph('<?php echo $root.$fileUrl; ?>', false, ''); <?php }
			      else { ?> plotGraph('<?php echo $realRoot."temp.json"; ?>', true, '<?php echo getDocumentRoot()."/".$fileUrl; ?>'); <?php } ?>
	</script>
</body>
</html>