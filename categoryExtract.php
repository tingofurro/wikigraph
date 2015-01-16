<?php
include_once('dbco.php');
set_time_limit(3600);
$maxBranches = 4;
$r = mysql_query("SELECT * FROM wg_category WHERE travelled='0' AND killBranch='0' AND distance<".$maxBranches." ORDER BY distance");

while($re = mysql_fetch_array($r)) {
	echo extractCategories($re['name'], $re['distance'], $re['id']);
	echo 'Travelled: <b>'.$re['name'].'</b><br />';
}
function extractCategories($parentName, $parentDistance, $parentId) {
	$dom = new DOMDocument;
	$html = file_get_contents('http://en.wikipedia.org/wiki/Category:'.$parentName);
	@$dom->loadHTML($html);
	$dom = $dom->getElementById('mw-subcategories');
	if(!is_null($dom)) {
		foreach ($dom->getElementsByTagName('a') as $link) {
			$thisClass = $link->getAttribute('class');
			if(!empty($thisClass) AND strpos($thisClass, 'CategoryTreeLabel') !== false) {
				$h = explode(":", $link->getAttribute('href')); $category = $h[1];
				mysql_query("INSERT INTO `wg_category` (`id`, `name`, `parent`, `distance`, `killBranch`, `travelled`) VALUES (NULL, '$category', '".$parentId."', '".($parentDistance+1)."', '0', '0');");
			}
		}
	}
	mysql_query("UPDATE wg_category SET travelled='1' WHERE id='$parentId'");	
}
?>