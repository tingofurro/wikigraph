<?php
include_once('init.php');
$totFields = 23;
?>
<html>
<head>
	<title>Number of categories and pages per field</title>
	<link rel="stylesheet" type="text/css" href="css/">
</head>
<body>
	<table>
	<tr>
		<td>Name of field</td>
		<td>Nb Wikipedia Categories</td>
		<td>Nb Wikipedia Articles</td>
	</tr>	
	<?php
	for($field = 1; $field <= $totFields; $field ++) {
		$whereField = whereField($field);
		$fieldNa = mysql_query("SELECT * FROM wg_category WHERE ".$whereField." ORDER BY id LIMIT 1");
		$fieldName = mysql_fetch_array($fieldNa);

		$ca = mysql_query("SELECT COUNT(*) AS count FROM wg_category WHERE ".$whereField);
		$cat = mysql_fetch_array($ca);
		
		$pag = mysql_query("SELECT COUNT(*) AS count FROM wg_page WHERE ".$whereField);
		$page = mysql_fetch_array($pag);
		echo "<tr><td><a href='graph.php?field=".$field."' target='_new'>".wikiToName($fieldName['name'])."</a></td><td>".$cat['count']."</td><td>".$page['count']."</td></tr>";
	}
	?>
	</table>
</body>
</html>