<?php
include_once('dbco.php');
include_once('mainFunc.php');
include_once('createJsonGraph.php');
include_once('graphFunctions.php');
$realRoot = getRealRoot();
$cluster = 0; $level = 0;
$limit = 700;
if(isset($_GET['cluster'])) {
	$c = mysql_query("SELECT * FROM wg_cluster WHERE id=".mysql_real_escape_string($_GET['cluster']));
	if($cl = mysql_fetch_array($c)) {
		$cluster = $cl['id']; $level = $cl['level'];
	}
}

$where = ''; $nodeList = array(); $nodeNameList = array();
if($level > 0) $where = ' WHERE cluster'.$level."=".$cluster;
$n = mysql_query("SELECT * FROM wg_page".$where." ORDER BY PR DESC LIMIT ". $limit);
while($no = mysql_fetch_array($n)) {array_push($nodeList, $no['id']); array_push($nodeNameList, $no['name']);}

$fileUrl = "display/cache/".$cluster.".json";
$fileExists = file_exists(getDocumentRoot().'/'.$fileUrl);
if(!$fileExists) generateGraph($level, $cluster, $limit);
$cid = $cluster;
$names = array();
while($level > 0) {
	$c = mysql_query("SELECT * FROM wg_cluster WHERE id=".$cid); $cl = mysql_fetch_array($c);
	$v = array("name" => shorterName($cl['name']), "id"=> $cl['id']);
	array_unshift($names, $v);
	$level --; $cid = $cl['parent'];
}
$extraTop = "";
foreach ($names as $i => $n) $extraTop .= "<a href='".$root."graph/".$n['id']."'><span style='font-size:".(40-8*$i)."px;' class='folders'>/".$n['name']."</span></a>";
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
	<script src="<?php echo $realRoot; ?>JS/lib/noty.js"></script>
</head>
	<?php include_once('header.php'); ?>
<body>
	<div id="clusterNameContain">
	<?php
	$any = false;
	$c = mysql_query("SELECT * FROM wg_cluster WHERE good=1 AND parent=".$cluster);
	while($cl = mysql_fetch_array($c)) {
		$any = true;
		?> <a href="<?php echo $root;?>graph/<?php echo $cl['id']; ?>"><div class="clusterName" value="<?php echo $cl['id']; ?>"><?php echo shorterName($cl['name']); ?></div></a><?php
	}
	if($any) {
		?>
			<div id="expliSub">Click above links to zoom-in</div>
		<?php
	}
	?>
	</div>
	<div id="adjMatrix" onclick="$('#adjContainer').show();">Adjacency Matrix</div>
	<div id="artNames" onclick="$('#artNameContainer').show();">Article Names</div>
	<div id="adjContainer">
		<textarea><?php echo generateMatrix($nodeList); ?></textarea>
		<div class="closeAdj" onclick="$('#adjContainer').hide();">X</div>
	</div>
	<div id="artNameContainer">
		<textarea><?php echo implode("\n", $nodeNameList); ?></textarea>
		<div class="closeAdj" onclick="$('#artNameContainer').hide();">X</div>
	</div>
	<script src="<?php echo $realRoot; ?>JS/graph.js"></script>
	<script src="<?php echo $realRoot; ?>JS/keywords.js"></script>
	<script type="text/javascript">
		var webroot = '<?php echo $realRoot; ?>';
		var color = d3.scale.category20();
			<?php if($fileExists) { ?> plotGraph('<?php echo $root.$fileUrl; ?>', false, ''); <?php }
			      else { ?> plotGraph('<?php echo $realRoot."temp.json"; ?>', true, '<?php echo getDocumentRoot()."/".$fileUrl; ?>'); <?php } ?>
	</script>
</body>
</html>