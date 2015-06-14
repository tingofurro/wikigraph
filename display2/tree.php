<?php
	include_once('createJsonTree.php');
	topMenu($root, $realRoot);
	$source = 1;
	if(isset($_GET['sourceName']) OR isset($_GET['source'])) {
		if(isset($_GET['sourceName'])) {
			$name = mysql_real_escape_string($_GET['sourceName']);
			$name = strToWiki(urldecode($name)); $where = "cat.name='$name'";
		}
		else {
			$source = mysql_real_escape_string($_GET['source']); $where = "cat.id='$source'";
		}
		echo "SELECT cat.*, par.name AS parentName FROM wg_category AS cat INNER JOIN wg_category AS par ON par.id=cat.parent WHERE ".$where." LIMIT 1";
		$r = mysql_query("SELECT cat.*, par.name AS parentName FROM wg_category AS cat INNER JOIN wg_category AS par ON par.id=cat.parent WHERE ".$where." LIMIT 1");
		if($re = mysql_fetch_array($r)) {
			$source = $re['id']; $name = $re['name'];
			if($re['parent'] != 0) {
				?>
					<a href="<?php echo $root."category/".$re['parent']; ?>">
						<div id="parentDiv">
							&#65513; <?php echo wikiToName($re['parentName']); ?>
						</div>
					</a>
				<?php
			}
		}
	}
	$r = mysql_query("SELECT * FROM wg_category WHERE id='$source'"); $re = mysql_fetch_array($r);
	generateTree($source, 2);
?>
<!DOCTYPE html>
<html>
	<title><?php echo wikiToName($re['name']); ?></title>
	<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/tree.css" />
	<body>
		<script type="text/javascript">
		var webroot = '<?php echo $realRoot; ?>';
		var linkroot = '<?php echo $root; ?>';
		</script>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
		<script src="http://d3js.org/d3.v3.min.js"></script>
		<script src="<?php echo $realRoot; ?>JS/treeCat.js"></script>
	</body>
</html>