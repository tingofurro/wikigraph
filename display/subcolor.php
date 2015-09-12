<?php
include_once('createJsonTree.php');
generateTree(0,3);
?>
<html>
<head>
	<title>Colors and subcolors</title>
</head>
<body>
	<?php
		$cNames = array(); $cNames[0] = ''; $lastParent = 0; $i = 0;
		$c = mysql_query("SELECT * FROM cluster ORDER BY id");
		while($cl = mysql_fetch_array($c)) {
			if($cl['parent'] != $lastParent) {$lastParent = $cl['parent']; $i = 0;}
			if($cl['parent']==0) $cNames[$cl['id']] = $i."";
			else $cNames[$cl['id']] = $cNames[$cl['parent']].",".$i;
			?>
			<div class="hide" id="iList<?php echo $cl['id']; ?>"><?php echo $cNames[$cl['id']]; ?></div>
			<?php
			$i ++;
		}
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/subcolor.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/graph.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="<?php echo $realRoot; ?>JS/lib/d3.js"></script>
	<script type="text/javascript">var webroot = '<?php echo $realRoot; ?>';</script>
	<script type="text/javascript" src="<?php echo $realRoot; ?>JS/colors.js"></script>
	<script type="text/javascript" src="<?php echo $realRoot; ?>JS/subcolor.js"></script>
	<script src="<?php echo $realRoot; ?>JS/graph2.js"></script>
	<script src="<?php echo $realRoot; ?>JS/keywords.js"></script>
	<script src="<?php echo $realRoot; ?>JS/lib/noty.js"></script>
</body>
</html>