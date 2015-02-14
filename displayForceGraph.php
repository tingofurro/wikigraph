<?php
$colorArray = array('black', '#e50700', '#e77F03', '#e8ba05', '#DEE907', '#A6EA09', '#6EEC0B', '#37ED0D', '#0FEE1E', '#11EF58', '#13F092', '#15F2CC', '#17E1F3', '#1AAAF4', '#1C75F5', '#1E40F6', '#3520F7', '#6E22F9', '#A624FA', '#DD27FB', '#FC29E4', '#FD2BB0', '#FF2E7D', 'pink');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Force directed algorithm</title>
</head>
<style type="text/css">
	body {
		padding: 0px; margin: 0px;
		background: #404040;
	}
	.dot {
		position: absolute;
		width: 1px; height: 5px; width: 5px;
		border-radius: 3px;
		background: black;
		cursor: pointer;
		border: 0.5px solid #d0d0d0;
	}
	.dot .labels {
		display: none; position: absolute;
		top: -20px; left: -105px;
		width: 200px; text-align: center;
		font-size: 12px;
	}
	.dot:hover {
		height: 7px; width: 7px;
	}
	.dot:hover .labels {
		display: block;
	}
</style>
<body>
<?php
	include_once('init.php');
	$file = 'g5000-r4.txt';
	if(isset($_GET['f'])) {$file = mysql_real_escape_string($_GET['f']);}
	$handle = fopen('graphData/'.$file, 'r');
	$nodes = array();
	$labels = array(); $colors = array();
	$X = array(); $Y = array();
	while (($buffer = fgets($handle, 4096)) !== false) {
		$buffer = str_replace("\n", "", $buffer);
		$toks = explode("|", $buffer);
		if(count($toks) >= 3) {
			$node = $toks[0];
			array_push($nodes, $toks[0]);
			$X[$node] = $toks[1]; $Y[$node] = $toks[2];
		}
	}
	$p = mysql_query("SELECT * FROM wg_page ORDER BY id");
	while($pa = mysql_fetch_array($p)) {
		$labels[$pa['id']] = $pa['name'];
		$fields = explode("|", $pa['fields']);
		$col = 0;
		if(count($fields) == 1) {$col = $fields[0];}
		$colors[$pa['id']] = $col;
	}
	$addLabels = true;
	foreach ($nodes as $node) {
		echo "<div class='dot' style='top: ".$Y[$node]."px; left: ".$X[$node]."px; background: ".$colorArray[$colors[$node]].";'>&nbsp;";
		if($addLabels) {
			echo "<div class='labels'>".$labels[$node]."</div>";
		}
		echo "</div>";
	}
?>
</body>
</html>