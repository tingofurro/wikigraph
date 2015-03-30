<?php
include_once('algo/func.php');
include_once('display/graphFunctions.php');
$root = getRoot();
$realRoot = getRealRoot();
$subfield = 18;
if(isset($_GET['sf'])) {$subfield = $_GET['sf'];}
$thresh1 = 0.3; // if in my category, it has to be somewhat relevant
$thresh2 = 80; // if not in my category, it should be highly relevant

$subf = mysql_query("SELECT * FROM wg_subfield WHERE id=$subfield"); $subfi = mysql_fetch_array($subf);
$field = $subfi['field'];
$f = mysql_query("SELECT * FROM wg_field WHERE id=$field"); $fi = mysql_fetch_array($f);

$n = mysql_query("SELECT * FROM wg_page WHERE (field=".$subfi['field']." AND ".$fi['sname'].">$thresh1) OR ".$fi['sname'].">$thresh2");
$nodes = array();
while ($node = mysql_fetch_array($n)) array_push($nodes, $node['id']);

// we're going to remove the other potential subfields
$otherSubf = mysql_query("SELECT * FROM wg_subfield WHERE field=$field AND id!=$subfield");
$toRemove = array();
while($otherSubfi = mysql_fetch_array($otherSubf)) array_push($toRemove, $otherSubfi['id']);
$nodes = array_diff($nodes, $toRemove);
$n = mysql_query("SELECT * FROM wg_page WHERE id IN(".implode(",", $nodes).")");

while($node = mysql_fetch_array($n)) {$adja[$node['id']] = array();}



$edg = mysql_query("SELECT * FROM wg_link WHERE `to` IN (".implode(",", array_keys($adja)).") AND `from` IN (".implode(",", array_keys($adja)).") ORDER BY id");
while($edge = mysql_fetch_array($edg)) array_push($adja[$edge['to']], $edge['from']);

$diffusion = computeDiffusion($adja, $subfi['page'], 0.7); // diffusion centered around our new math field

$nodes = array();
foreach ($diffusion as $nb => $p) if($p>1) array_push($nodes, $nb);


$src = getDocumentRoot()."/display/json/subfield.json";
nodes2Graph($nodes, $src);
?>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/graphCat.css">
<body>
<select onchange="window.location='<?php echo $root;?>/subfield/'+this.value;" style="font-size: 16px;">
<?php
$s = mysql_query("SELECT * FROM wg_subfield ORDER BY name");
while($su = mysql_fetch_array($s)) {
	echo "<option value=".$su['id']." ".(($su['id']==$subfield)?"selected":"").">".$su['name']."</option>";
}
?>	
</select>
</body>
<script type="text/javascript">
	var color = [];
	var fieldId = '<?php echo $field; ?>';
	<?php
	$colArray = array('#FF0000', '#FF0074', '#FF00E8', '#A200FF', '#2D00FF', '#0046FF', '#00BAFF', '#00FFD0', '#00FF5B', '#18FF00', '#8CFF00', '#FFFD00', '#FF8900');
	$f = mysql_query("SELECT * FROM wg_field ORDER BY id");
	while($fi = mysql_fetch_array($f)) {
		echo "color[".$fi['id']."] = '".array_shift($colArray)."';\n";
	}
	?>
	var fileFrom = '<?php echo $realRoot."json/subfield.json"; ?>';
	var alphaI = 0.1; var saveGraph = false;
</script>
<script type="text/javascript">var webroot = '<?php echo $realRoot; ?>';</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="<?php echo $realRoot; ?>JS/lib/d3.js"></script>
<script src="<?php echo $realRoot; ?>JS/graphCat.js"></script>