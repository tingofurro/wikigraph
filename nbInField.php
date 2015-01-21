<?php
include_once('init.php');
$totFields = 23;
?>
<html>
<head>
	<title>Number of categories and pages per field</title>
	<style type="text/css">
	body {
		margin: 0px; padding: 0px;
		font-family: sans-serif;
	}
	table {
	margin: auto; margin-top: 50px;
     border-collapse: collapse;
 	}
	td {
		border: 1px solid black;
		text-align: center;
	}
	table tr:first-child {
		font-weight: bold; background: #f0f0f0;
	}
	table tr:first-child td {
		padding: 0px 10px;
	}
	</style>
</head>
<body>
	<table>
	<tr>
		<td>Name of field</td>
		<td>Nb of categories</td>
		<td>Nb of pages</td>
	</tr>	
	<?php
	for($field = 1; $field <= $totFields; $field ++) {
		$fieldNa = mysql_query("SELECT * FROM wg_category WHERE (fields='$field' OR fields LIKE '%|".$field."|%' OR fields LIKE '".$field."|%' OR fields LIKE '%|".$field."') ORDER BY id LIMIT 1");
		$fieldName = mysql_fetch_array($fieldNa);

		$ca = mysql_query("SELECT COUNT(*) AS count FROM wg_category WHERE (fields='$field' OR fields LIKE '%|".$field."|%' OR fields LIKE '".$field."|%' OR fields LIKE '%|".$field."')");
		$cat = mysql_fetch_array($ca);
		
		$pag = mysql_query("SELECT COUNT(*) AS count FROM wg_page WHERE (fields='$field' OR fields LIKE '%|".$field."|%' OR fields LIKE '".$field."|%' OR fields LIKE '%|".$field."')");
		$page = mysql_fetch_array($pag);
		echo "<tr><td>".wikiToName($fieldName['name'])."</td><td>".$cat['count']."</td><td>".$page['count']."</td></tr>";
	}
	?>
	</table>
</body>
</html>