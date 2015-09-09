<?php
include_once('dbco.php');
include_once('mainFunc.php');
include_once('createJsonGraph.php');
include_once('graphFunctions.php');
$realRoot = getRealRoot();
$cluster = 0; $level = 0; $limit = 700;
if(isset($_GET['cluster'])) {
	$c = mysql_query("SELECT * FROM cluster WHERE id=".mysql_real_escape_string($_GET['cluster']));
	if($cl = mysql_fetch_array($c)) {$cluster = $cl['id']; $level = $cl['level'];}
}

$fileUrl = "display/cache/".$cluster.".json";
$fileExists = file_exists(getDocumentRoot().'/'.$fileUrl);
if(!$fileExists) generateGraph($level, $cluster, $limit);
$cid = $cluster;
$names = array(); $cidArray = array(); $cnameArray = array();
while($level > 0) {
	$c = mysql_query("SELECT * FROM cluster WHERE id=".$cid); $cl = mysql_fetch_array($c);
	$v = array("name" => shorterName($cl['name']), "id"=> $cl['id']);
	array_unshift($names, $v);
	array_unshift($cidArray, $cl['id']);
	array_unshift($cnameArray, $cl['name']);
	$level --; $cid = $cl['parent'];
}
array_unshift($cidArray,0);
array_unshift($cnameArray, "Mathematics");
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
	<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/pie.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="<?php echo $realRoot; ?>JS/lib/d3.js"></script>
	<script src="<?php echo $realRoot; ?>JS/colors.js"></script>
	<script src="<?php echo $realRoot; ?>JS/lib/noty.js"></script>
</head>
	<?php include_once('header.php'); ?>
<body>
	<div class="hide" id="fileUrl"><?php echo ($fileExists)?$root.$fileUrl:$realRoot."temp.json"; ?></div>
	<div class="hide" id="toRun"><?php echo ($fileExists)?0:1; ?></div>
	<div class="hide" id="whereToSave"><?php echo ($fileExists)?'':getDocumentRoot()."/".$fileUrl;?></div>
	<div class="hide" id="cidList"><?php echo implode(",", $cidArray); ?></div>
	<div class="hide" id="cnameList"><?php echo implode("|", $cnameArray); ?></div>
	<script src="<?php echo $realRoot; ?>JS/graph.js"></script>
	<script src="<?php echo $realRoot; ?>JS/keywords.js"></script>
	<script src="<?php echo $realRoot; ?>JS/pieNew.js"></script>
	<script type="text/javascript">var webroot = '<?php echo $realRoot; ?>';</script>
	<?php
		$cNames = array(); $cNames[0] = ''; $lastParent = 0; $i = 0;
		$c = mysql_query("SELECT * FROM cluster ORDER BY id");
		while($cl = mysql_fetch_array($c)) {
			if($cl['parent'] != $lastParent) {$lastParent = $cl['parent']; $i = 0;}
			if($cl['parent']==0) $cNames[$cl['id']] = $i."";
			else $cNames[$cl['id']] = $cNames[$cl['parent']].",".$i;
			?>
			<div class="hide" id="iList<?php echo $cl['id']; ?>"><?php echo $cNames[$cl['id']]; ?></div>
			<?php
			$i ++;
		}
	?>
</body>
</html>