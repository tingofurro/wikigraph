<?php
	include('init.php');
	include_once('treeJsonCreator.php');
	$source = 1;
	if(isset($_GET['sourceName']) OR isset($_GET['source'])) {
		if(isset($_GET['sourceName'])) {
			$name = mysql_real_escape_string($_GET['sourceName']);
			$name = strToWiki($name); $where = "cat.name='$name'";
		}
		else {
			$source = mysql_real_escape_string($_GET['source']); $where = "cat.id='$source'";
		}
		$r = mysql_query("SELECT cat.*, par.name AS parentName FROM wg_category AS cat INNER JOIN wg_category AS par ON par.id=cat.parent WHERE ".$where." LIMIT 1");
		if($re = mysql_fetch_array($r)) {
			$source = $re['id'];
			if($re['parent'] != 0) {
				?>
					<a href="tree.php?source=<?php echo $re['parent']; ?>">
						<div id="parentDiv">
							&#65513; <?php echo wikiToName($re['parentName']); ?>
						</div>
					</a>
				<?php
			}
		}
	}
	generateGraph($source, 2);
?>
<!DOCTYPE html>
<html>
	<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="css/categoryDisplay.css" />
	<body>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
		<script src="http://d3js.org/d3.v3.min.js"></script>
		<script src="JS/category.js"></script>
	</body>
</html>