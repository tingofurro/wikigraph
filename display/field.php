<?php
topMenu($root, $realRoot);
$totFields = 23;
?>
<html>
<head>
	<title>Number of categories and pages per field</title>
	<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/displayField.css" />
</head>
<body>
	<div id="leftMenu">
	<?php
	$f = mysql_query("SELECT * FROM wg_field ORDER BY id");
	while($fi = mysql_fetch_array($f)) {
		$pag = mysql_query("SELECT COUNT(*) AS count FROM wg_page WHERE field=".($fi['id'])); $page = mysql_fetch_array($pag);

		echo "<a class='fieldClick' target='graphIframe' href='".$realRoot."graphCat/".$fi['id']."'>";
		echo "<div class='oneField'>".$fi['name'];
			echo "<div class='icons'>";
				echo "<img src='".$realRoot."images/icons/articles.png' class='icon' /> ".$page['count'];
			echo "</div>";
		echo "</div>";
		echo "</a>";
	}
	?>
	</div>
	<iframe src="<?php echo $realRoot; ?>graphCat/1" name="graphIframe" id="graphIframe"></iframe>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $realRoot; ?>JS/displayField.js"></script>
</body>
</html>