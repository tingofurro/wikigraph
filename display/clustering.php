<?php
if(isset($_GET['keywords'])) {
	?>
		<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/displayClustering.css">
	<?php
	$setName = 'mathematician';
	if($_GET['keywords'] == 'software') {$setName = 'software';}
	if($_GET['keywords'] == 'normal') {$setName = 'normal';}
?>
	<select onchange="window.location='<?php echo $root; ?>clustering/keywords/'+this.value;" onkeyup="this.onchange();">
		<option value="mathematician" <?php echo (($setName=='mathematician')?'selected':''); ?>>Mathematician set</option>
		<option value="software" <?php echo (($setName=='software')?'selected':''); ?>>Software set</option>
		<option value="normal" <?php echo (($setName=='normal')?'selected':''); ?>>Math set</option>
	</select><br />
<table><tr>
<?php
$set = apc_fetch($setName.'TrainSet', $exist);
if(!$exist) {
	include_once(getDocumentRoot()."/algo/nlp/nlp.php");
	wordScores($setName, getDocumentRoot());
	$set = apc_fetch($setName.'TrainSet');
}
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
		<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/displayClustering.css">
		<table><tr>
	<?php
	$set = 'mathematician';
	if($_GET['pages'] == 'software') {$set = 'software';}
?>
	<select onchange="window.location='<?php echo $root; ?>clustering/pages/'+this.value;" onkeyup="this.onchange();">
		<option value="mathematician" <?php echo (($set=='mathematician')?'selected':''); ?>>Mathematician pages</option>
		<option value="software" <?php echo (($set=='software')?'selected':''); ?>>Software pages</option>
	</select><br />
<?php
	$r = mysql_query("SELECT * FROM wg_page ORDER BY ".$set." DESC LIMIT ".(($page-1)*$perPage).",".$perPage);
	$i = 0;
	while($re = mysql_fetch_array($r)) {
		echo "<td width='33%'><a class='pageLink' href='".$root."explore/".$re['id']."' target='_new'>".$re['name'].":</a> ".$re[$set]."</td>";
		$i ++; if($i%3 == 0) {echo '</tr><tr>';}
	}
	?>
	</tr>
	<tr>
	<td colspan="3" align="center">
	<?php
		if($page > 1) {
			echo '<a href="'.$root.'clustering/pages/'.$set.'/'.($page-1).'">&larr;</a>';
		}

		echo ' Page '.$page.' <a href="'.$root.'clustering/pages/'.$set.'/'.($page+1).'">&rarr;</a>';
	?>
	</td>
	</tr>

	</table>
	<?php
}
else {
topMenu($root, $realRoot);
?>	
	<body>
	<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/displayClustering.css" />
		<iframe src="<?php echo $root; ?>clustering/keywords" id="firstIframe"></iframe>
		<iframe src="<?php echo $root; ?>clustering/pages"></iframe>
	</body>
<?php
}
?>