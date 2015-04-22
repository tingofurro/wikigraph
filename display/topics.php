<?php
	include_once('createJsonGraph.php');
	include_once('graphFunctions.php');

	topMenu($root, $realRoot);

	$field = $_GET['field']; $topic = $_GET['topic'];
	$fileUrl = "display/json/topics/".$field."-".$topic.".json";
	$fileExists = file_exists(getDocumentRoot().'/'.$fileUrl);
	if(!$fileExists) generateTopicGraph($field, $topic);
	$nodes = array();
	$top = mysql_query("SELECT * FROM wg_topic WHERE field=".$field." AND topic=".$topic);
	while($topi = mysql_fetch_array($top)) array_push($nodes, $topi['page']);
?>
<!DOCTYPE html>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/topics.css">
<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/graph.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="<?php echo $realRoot; ?>JS/lib/d3.js"></script>
<body>
	<br /><br />
	<select onchange="window.location='<?php echo $root; ?>topics/'+this.value;">
		<?php $t = mysql_query("SELECT * FROM wg_topic GROUP BY field, topic ORDER BY field, topic"); while($to = mysql_fetch_array($t)) {?>
			<?php $f = mysql_query("SELECT * FROM wg_field WHERE id=".$to['field']); $fi = mysql_fetch_array($f); ?>
			<option value="<?php echo $to['field'].'/'.$to['topic']; ?>" <?php echo (($field==$to['field']AND$topic==$to['topic'])?"selected":""); ?>><?php echo $fi['sname']." - ".$to['topic']; ?></option>
		<?php } ?>
	</select>
	<input type="submit" value="Show Adjacency Matrix" onclick="$('#showMatrix').show();" />
	<div id="showMatrix">
		<textarea><?php echo generateMatrix($nodes); ?></textarea>
		<div id="closeMenu" onclick="$('#showMatrix').hide();">×</div>
	</div>

</body>
<script src="<?php echo $realRoot; ?>JS/graph.js"></script>
<script type="text/javascript">
	var webroot = '<?php echo $realRoot; ?>';
	var color = d3.scale.category20();
		<?php if($fileExists) { ?> plotGraph('<?php echo $root.$fileUrl; ?>', false, ''); <?php }
		else { ?> plotGraph('<?php echo $realRoot."json/catGraph.json"; ?>', true, '<?php echo getDocumentRoot()."/".$fileUrl; ?>'); <?php } ?>
</script>