<?php
	include_once('createJsonGraph.php');
	$field =  1; $graphType = 'cat';
	if(isset($_GET['graphCat'])) {
		$fieldN = mysql_real_escape_string($_GET['graphCat']);
		$rF = mysql_query("SELECT * FROM wg_field WHERE name='".$fieldN."' OR sname='".$fieldN."'");
		if($reF = mysql_fetch_array($rF)) $field = $reF['id'];
	}
	elseif(isset($_GET['graphArt'])) {
		$graphType = 'art';
		$field = mysql_real_escape_string($_GET['graphArt']);
	}
	else {$field = 1;}

	$f = mysql_query("SELECT * FROM wg_field WHERE id='$field'"); $fi = mysql_fetch_array($f);

	$url = getDocumentRoot()."/display/json/".$graphType."-".$field.".json";
	$fileExists = file_exists($url);

	if(!$fileExists) {
		if($graphType == 'art') {generateArticleGraph($field);}
		else {generateCatGraph($field);}
	}
?>
<!DOCTYPE html>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/graphCat.css">
<body>
	<div id="choiceMenu">
		<div id="viewCategories" style="background-color: <?php echo $fi['color']; ?>;" class="optionItem"><?php echo $fi['name']; ?></div>
		<a href="<?php echo $root;?>graphCat/<?php echo $field;?>"><div id="viewCategories" class="optionItem">View Categories</div></a>
		<a href="<?php echo $root;?>graphArt/<?php echo $field;?>"><div class="optionItem">View Pages</div></a>
	</div>

<script type="text/javascript">
	var fieldId = '<?php echo $field; ?>';
	var graphType = '<?php echo $graphType; ?>';
<?php if ($graphType == 'cat') { ?>
	var color = ['#FF0000', '#FF3333', '#FF6666', '#FF9999', '#FFCCCC', '#FFFFFF'];
<?php }
	else {
		if($graphType == 'art') {
			echo "var color = [];";
			$colArray = array('#FF0000', '#FF0074', '#FF00E8', '#A200FF', '#2D00FF', '#0046FF', '#00BAFF', '#00FFD0', '#00FF5B', '#18FF00', '#8CFF00', '#FFFD00', '#FF8900');
			$f = mysql_query("SELECT * FROM wg_field ORDER BY id");
			while($fi = mysql_fetch_array($f)) {
				echo "color[".$fi['id']."] = '".array_shift($colArray)."';\n";
			}
		}
	}
	if($fileExists) {
		?>  var fileFrom = '<?php echo $realRoot."json/".$graphType."-".$field.".json"; ?>';
			var alphaI = 0.0051;
			var saveGraph = false; <?php
	}
	else {
		?>  var fileFrom = '<?php echo $realRoot."json/catGraph.json"; ?>';
			var alphaI = 0.1;
			var saveGraph = true; <?php
	}
?>
</script>
</body>
<script type="text/javascript">var webroot = '<?php echo $realRoot; ?>';</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="<?php echo $realRoot; ?>JS/lib/d3.js"></script>
<script src="<?php echo $realRoot; ?>JS/graphCat.js"></script>