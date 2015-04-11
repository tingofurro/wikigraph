<?php
	include_once('createJsonGraph.php');
	$graphType = 'art';
	if(isset($_GET['graphArt'])) {$field = mysql_real_escape_string($_GET['graphArt']);}else {$field = 1;}
	$uncleanField = $field;
	$f = mysql_query("SELECT * FROM wg_field WHERE id=$field");
	if(!$fi = mysql_fetch_array($f)) {
		$f = mysql_query("SELECT * FROM wg_field ORDER BY id LIMIT 1"); $fi = mysql_fetch_array($f);
		$field = $fi['id'];
	}

	$fileUrl = "display/json/field/".$field.".json";
	$fileExists = file_exists(getDocumentRoot().'/'.$fileUrl);

	if(!$fileExists) {
		if($graphType == 'art') {generateArticleGraph($field);}
	}
?>
<!DOCTYPE html>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/graphCat.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="<?php echo $realRoot; ?>JS/lib/d3.js"></script>
<body>
	<div id="choiceMenu">
		<div id="viewCategories" class="optionItem"><?php echo $fi['name']; ?></div>
	</div>
</body>
<script src="<?php echo $realRoot; ?>JS/graph.js"></script>
<script type="text/javascript">
	var webroot = '<?php echo $realRoot; ?>';
	<?php if ($graphType == 'cat') { ?> var color = d3.scale.category10(); <?php }
	 else {?>var color = d3.scale.category20(); <?php }
		if($fileExists) {
			?> plotGraph('<?php echo $root.$fileUrl; ?>', false, '', <?php echo $uncleanField; ?>); <?php
		}
		else {
			?> plotGraph('<?php echo $realRoot."json/catGraph.json"; ?>', true, '<?php echo getDocumentRoot()."/".$fileUrl; ?>', <?php echo $uncleanField; ?>); <?php
		}
	?>
</script>