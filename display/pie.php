<?php
include_once('dbco.php');
include_once('mainFunc.php');

$file = getDocumentRoot()."/display/pies/0.csv";

?>
<html>
<!DOCTYPE html>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/pie.css">
<?php include_once('header.php'); ?>
<body>
	<div id="mainExpli">
		Structure Mathematical subjects using Wikipedia.
	</div>
	<div id="clickExplain">
		(1) Click on a subject to see its subtopics.<br />
		(2) See the Wikipedia Articles in the Subject (click See Graph)
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script type="text/javascript">
		var webroot = '<?php echo $realRoot; ?>';
		var prettyroot = '<?php echo $root; ?>';
	</script>
	<script type="text/javascript" src="<?php echo $realRoot; ?>JS/pie.js"></script>
</body>
</html>