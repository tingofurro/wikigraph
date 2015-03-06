<?php
	include_once('createJsonGraph.php');
	$field =  1; $graphType = 'cat';
	$fieldList = cleanFieldList();
	$fieldColorList = cleanFieldColor();
	$fieldNameList = cleanFieldListName();
	if(isset($_GET['graphCat'])) {
		$field = mysql_real_escape_string($_GET['graphCat']);
		if(in_array($field, $fieldList)) {
			$field = array_search($_GET['graphCat'], $fieldList)+1;
		}
	}
	elseif(isset($_GET['graphArt'])) {
		$graphType = 'art';
		$field = mysql_real_escape_string($_GET['graphArt']);
	}
	else {$field = 1;}

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
		<div id="viewCategories" style="background-color: <?php echo $fieldColorList[($field-1)]; ?>" class="optionItem"><?php echo $fieldNameList[($field-1)]; ?></div>
		<a href="<?php echo $root;?>graphCat/<?php echo $field;?>"><div id="viewCategories" class="optionItem">View Categories</div></a>
		<a href="<?php echo $root;?>graphArt/<?php echo $field;?>"><div class="optionItem">View Pages</div></a>
	</div>
<?php

?>

<script type="text/javascript">
	var fieldId = '<?php echo $field; ?>';
	var graphType = '<?php echo $graphType; ?>';
<?php if ($graphType == 'cat') { ?>
	var color = ['#FF0000', '#FF3333', '#FF6666', '#FF9999', '#FFCCCC', '#FFFFFF'];
<?php } else { ?>
	var color = ['#FF0000', '#FF0074', '#FF00E8', '#A200FF', '#2D00FF', '#0046FF', '#00BAFF', '#00FFD0', '#00FF5B', '#18FF00', '#8CFF00', '#FFFD00', '#FF8900'];
<?php } ?>
<?php
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