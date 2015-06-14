<?php
include_once('dbco.php');
include_once('mainFunc.php');
include_once('createJsonGraph.php');
$realRoot = getRealRoot();

$cluster1 = 5;
$fileUrl = "display/cache/1-".$cluster1.".json";
$fileExists = file_exists(getDocumentRoot().'/'.$fileUrl);
if(!$fileExists) generateGraph($cluster1);

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
	

	<script src="<?php echo $realRoot; ?>JS/graph.js"></script>
	<script type="text/javascript">
		var webroot = '<?php echo $realRoot; ?>';
		var color = d3.scale.category20();
			<?php if($fileExists) { ?> plotGraph('<?php echo $root.$fileUrl; ?>', false, ''); <?php }
			      else { ?> plotGraph('<?php echo $realRoot."temp.json"; ?>', true, '<?php echo getDocumentRoot()."/".$fileUrl; ?>'); <?php } ?>
	</script>
</body>
</html>