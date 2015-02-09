<?php
	include_once('init.php');
	include_once('createJsonGraph.php');
	$field =  5;
	if(isset($_GET['graphCat'])) {
		$field = mysql_real_escape_string($_GET['graphCat']);
		generateCatGraph($field);
	}
	elseif(isset($_GET['graphArt']) AND isset($_GET['threshhold'])) {
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
		<a href="<?php echo $root;?>graphArt/<?php echo $field;?>/0.3"><div class="optionItem">View Main Pages</div></a>
		<a href="<?php echo $root;?>graphArt/<?php echo $field;?>/0"><div class="optionItem">View All Pages</div></a>
	</div>


</body>

<script type="text/javascript">var webroot = '<?php echo $root; ?>';</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script src="<?php echo $root; ?>JS/graphCat.js"></script>