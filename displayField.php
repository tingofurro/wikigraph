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
	<?php
	for($field = 1; $field <= $totFields; $field ++) {
		$whereField = whereField($field);
		$fieldNa = mysql_query("SELECT * FROM wg_category WHERE ".$whereField." ORDER BY id LIMIT 1");
		$fieldName = mysql_fetch_array($fieldNa);

		$ca = mysql_query("SELECT COUNT(*) AS count FROM wg_category WHERE ".$whereField);
		$cat = mysql_fetch_array($ca);
		
		$pag = mysql_query("SELECT COUNT(*) AS count FROM wg_page WHERE ".$whereField);
		$page = mysql_fetch_array($pag);
		echo "<div><a class='fieldClick' href='graph.php?field=".$field."'>".wikiToName($fieldName['name'])."</a> ".$cat['count']." P ".$page['count']."</div>";
	}
	?>
</body>
</html>