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
	$fieldList = cleanFieldList();
	$fieldNames = cleanFieldListName();
	foreach ($fieldList as $i => $field) {

		$pag = mysql_query("SELECT COUNT(*) AS count, COUNT(CASE WHEN mathematician>=200 THEN 1 ELSE NULL END) AS people FROM wg_page WHERE cleanField=".($i+1));
		$page = mysql_fetch_array($pag);

		echo "<a class='fieldClick' target='graphIframe' href='".$root."graphCat/".$field."'>";
		echo "<div class='oneField'>".$fieldNames[$i];
			echo "<div class='icons'>";
				echo "<img src='".$root."images/icons/articles.png' class='icon' /> ".$page['count'];
				echo "&nbsp;&nbsp;&nbsp;";
				echo "<img src='".$root."images/icons/people.png' class='icon' /> ".$page['people'];
			echo "</div>";
		echo "</div>";
		echo "</a>";
	}
	?>
	</div>
	<iframe src="<?php echo $root; ?>graphCat/1" name="graphIframe" id="graphIframe"></iframe>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $root; ?>JS/displayField.js"></script>
</body>
</html>