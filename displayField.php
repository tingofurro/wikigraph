<?php
include_once('init.php');
topMenu($root);
$totFields = 23;
?>
<html>
<head>
	<title>Number of categories and pages per field</title>
	<link rel="stylesheet" type="text/css" href="<?php echo $root; ?>css/displayField.css" />
</head>
<body>
	<div id="leftMenu">
	<?php
	for($field = 1; $field <= $totFields; $field ++) {
		$whereField = whereField($field);
		$fieldNa = mysql_query("SELECT * FROM wg_category WHERE ".$whereField." ORDER BY id LIMIT 1");
		$fieldName = mysql_fetch_array($fieldNa);

		$ca = mysql_query("SELECT COUNT(*) AS count FROM wg_category WHERE ".$whereField);
		$cat = mysql_fetch_array($ca);
		
		$pag = mysql_query("SELECT COUNT(*) AS count FROM wg_page WHERE ".$whereField);
		$page = mysql_fetch_array($pag);
		echo "<a class='fieldClick' target='graphIframe' href='".$root."graph/".$field."'>";
		echo "<div class='oneField'>".wikiToName($fieldName['name'])."";
			echo "<div class='icons'>";
				echo "<img src='".$root."images/icons/categories.png' class='icon' /> ".$cat['count'];
				echo "&nbsp;&nbsp;&nbsp;";
				echo "<img src='".$root."images/icons/articles.png' class='icon' /> ".$page['count'];
			echo "</div>";
		echo "</div>";
		echo "</a>";
	}
	?>
	</div>
	<iframe src="<?php echo $root; ?>graph/1" name="graphIframe" id="graphIframe"></iframe>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $root; ?>JS/displayField.js"></script>
</body>
</html>