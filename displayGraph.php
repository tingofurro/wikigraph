<?php
	include_once('init.php');
	include_once('createJsonGraph.php');
	$field =  5; $colorScheme = 1;
	if(isset($_GET['graphCat'])) {
		$field = mysql_real_escape_string($_GET['graphCat']);
		generateCatGraph($field);
	}
	elseif(isset($_GET['graphArt']) AND isset($_GET['threshhold'])) {
		$colorScheme = 2;
		$field = mysql_real_escape_string($_GET['graphArt']);
		$threshhold = mysql_real_escape_string($_GET['threshhold']);
		generateArticleGraph($field, $threshhold);
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
		<a href="<?php echo $root;?>graphArt/<?php echo $field;?>/1"><div class="optionItem">View Most Important Pages</div></a>
		<a href="<?php echo $root;?>graphArt/<?php echo $field;?>/0.5"><div class="optionItem">View Main Pages</div></a>
		<a href="<?php echo $root;?>graphArt/<?php echo $field;?>/0"><div class="optionItem">View All Pages</div></a>
	</div>

<?php if ($colorScheme == 1) {?>
<script type="text/javascript">var color = ['#FF0000', '#FF3333', '#FF6666', '#FF9999', '#FFCCCC', '#FFFFFF'];</script>
<?php } else { ?>
<script type="text/javascript">var color = ['#FFFFFF', '#E5E5FF', '#CCCCFF', '#B2B2FF', '#9999FF', '#7F7FFF', '#6666FF', '#4C4CFF', '#3333FF', '#1919FF', '#0000FF'];</script>
<?php } ?>
</body>
<script type="text/javascript">var webroot = '<?php echo $root; ?>';</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script src="<?php echo $root; ?>JS/graphCat.js"></script>