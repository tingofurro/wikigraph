<?php
	include_once('init.php');
	include_once('createJsonGraph.php');
	$field =  5; $colorScheme = 1;
	$fieldList = cleanFieldList();
	if(isset($_GET['graphCat'])) {
		$field = mysql_real_escape_string($_GET['graphCat']);
		if(in_array($field, $fieldList)) {
			$field = array_search($_GET['graphCat'], $fieldList)+1;
		}
		generateCatGraph($field);
	}
	elseif(isset($_GET['graphArt'])) {
		$colorScheme = 2;
		$field = mysql_real_escape_string($_GET['graphArt']);
		generateArticleGraph($field);
	}
	else {
		$field = 1;
		generateCatGraph($field);
	}
?>
<!DOCTYPE html>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo $root; ?>css/graphCat.css">
<body>
	<div id="choiceMenu">
		<a href="<?php echo $root;?>graphCat/<?php echo $field;?>"><div id="viewCategories" class="optionItem">View Categories</div></a>
		<a href="<?php echo $root;?>graphArt/<?php echo $field;?>"><div class="optionItem">View Pages</div></a>
	</div>

<?php if ($colorScheme == 1) {?>
<script type="text/javascript">var color = ['#FF0000', '#FF3333', '#FF6666', '#FF9999', '#FFCCCC', '#FFFFFF'];</script>
<?php } else { ?>
<script type="text/javascript">var color = ['#FF0000', '#FF0074', '#FF00E8', '#A200FF', '#2D00FF', '#0046FF', '#00BAFF', '#00FFD0', '#00FF5B', '#18FF00', '#8CFF00', '#FFFD00', '#FF8900'];</script>
<?php } ?>
</body>
<script type="text/javascript">var webroot = '<?php echo $root; ?>';</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="<?php echo $root; ?>JS/lib/d3.js"></script>
<script src="<?php echo $root; ?>JS/graphCat.js"></script>