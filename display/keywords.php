<?php
	// include_once('createJsonGraph.php');

	$fileUrl = "display/json/keywords.json";
	$fileExists = file_exists(getDocumentRoot().'/'.$fileUrl);

	// if(!$fileExists) {
	// 	if($graphType == 'art') {generateArticleGraph($field);}
	// }
?>
<!DOCTYPE html>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/graphCat.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="<?php echo $realRoot; ?>JS/lib/d3.js"></script>
<body></body>
<script src="<?php echo $realRoot; ?>JS/graph.js"></script>
<script type="text/javascript">
	var webroot = '<?php echo $realRoot; ?>';
	var color = d3.scale.category20();
	<?php	 if($fileExists) { ?> plotGraph('<?php echo $root.$fileUrl; ?>', false, ''); <?php }
	 else {
	   ?> plotGraph('<?php echo $realRoot."json/keywordGraph.json"; ?>', true, '<?php echo getDocumentRoot()."/".$fileUrl; ?>'); <?php
	 }
	?>
</script>