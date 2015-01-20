<?php
	include_once('init.php');
	$root = 1; if(isset($_GET['source'])) {$root = mysql_real_escape_string($_GET['source']);}
	$r = mysql_query("SELECT * FROM wg_category WHERE id='$root'");
	$totalChildren = array();
	if($ro = mysql_fetch_array($r)) {
		$i = $ro['distance'];
		$layerParents = array($ro['id']);
		for ($d=$i; $d < 6; $d++) { 
			$tempChildren = array();
			foreach ($layerParents as $p) {
				$r = mysql_query("SELECT cat.* FROM wg_catlink AS link INNER JOIN wg_category AS cat ON link.catto=cat.id WHERE link.catfrom=".$p." AND cat.distance=".($d+1)."");
				while($re = mysql_fetch_array($r)) {
					if(!in_array($re['id'], $totalChildren)) {array_push($totalChildren, $re['id']); array_push($tempChildren, $re['id']);}
				}
			}
			echo "Children in distance <b>".($d+1)."</b>: ".count($tempChildren)."<br />";
			$layerParents = $tempChildren;
		}
		echo "<br /> -- <b>TOTAL Children: ".count($totalChildren)."</b>";
	}
?>