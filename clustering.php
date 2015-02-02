<?php
include_once('init.php');
if(isset($_GET['keywords'])) {
	?>
		<link rel="stylesheet" type="text/css" href="css/clustering.css">
	<?php
	$set = 'mathematician';
	if($_GET['keywords'] == 'software') {$set = 'software';}
	if($_GET['keywords'] == 'normal') {$set = 'normal';}
?>
	<select onchange="window.location='clustering.php?keywords='+this.value;" onkeyup="this.onchange();">
		<option value="mathematician" <?php echo (($set=='mathematician')?'selected':''); ?>>Mathematician set</option>
		<option value="software" <?php echo (($set=='software')?'selected':''); ?>>Software set</option>
		<option value="normal" <?php echo (($set=='normal')?'selected':''); ?>>Math set</option>
	</select><br />
<table><tr>
<?php
$set = apc_fetch($set.'TrainSet');
$i = 0;
foreach ($set as $word => $score) {
	echo '<td><u>'.$word.':</u> '.(floor(100*$score)/100)."</td>";
	$i ++; if($i%3 == 0) {echo '</tr><tr>';}
}
?>
</tr></table>

<?php
}
elseif(isset($_GET['pages'])) {
	$page = 1; $perPage = 60;
	if(isset($_GET['page'])) {$page = mysql_real_escape_string($_GET['page']);}
	?>
		<link rel="stylesheet" type="text/css" href="css/clustering.css">
		<table><tr>
	<?php
	$set = 'mathematician';
	if($_GET['pages'] == 'software') {$set = 'software';}
?>
	<select onchange="window.location='clustering.php?pages='+this.value;" onkeyup="this.onchange();">
		<option value="mathematician" <?php echo (($set=='mathematician')?'selected':''); ?>>Mathematician pages</option>
		<option value="software" <?php echo (($set=='software')?'selected':''); ?>>Software pages</option>
	</select><br />
<?php
	$r = mysql_query("SELECT * FROM wg_page ORDER BY ".$set." DESC LIMIT ".(($page-1)*$perPage).",".$perPage);
	$i = 0;
	while($re = mysql_fetch_array($r)) {
		echo "<td width='33%'><a class='pageLink' href='viewPage.php?id=".$re['id']."' target='_new'>".$re['name'].":</a> ".$re[$set]."</td>";
		$i ++; if($i%3 == 0) {echo '</tr><tr>';}
	}
	?>
	</tr>
	<tr>
	<td colspan="3" align="center">
	<?php
		if($page > 1) {
			echo '<a href="clustering.php?pages='.$set.'&page='.($page-1).'">&larr;</a>';
		}

		echo ' Page '.$page.' <a href="clustering.php?pages='.$set.'&page='.($page+1).'">&rarr;</a>';
	?>
	</td>
	</tr>

	</table>
	<?php
}
else {
topMenu();
?>	
	<body>
	<link rel="stylesheet" type="text/css" href="css/clustering.css" />
		<iframe src="clustering.php?keywords" id="firstIframe"></iframe>
		<iframe src="clustering.php?pages"></iframe>
	</body>
<?php
}
?>