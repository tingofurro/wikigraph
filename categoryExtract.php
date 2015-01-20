<?php
// 11252 categories ... over 6 layers => 9.574 => 4.241 => 4.012 => 2.870 => 2.201
include_once('init.php');
include_once('killList.php');
$killList = getKillList();

buildCategories('Mathematics', $killList);
function buildCategories($ROOT_NAME, $killList) {
	set_time_limit(3600);
	mysql_query("TRUNCATE wg_category");
	mysql_query("TRUNCATE wg_catlink");
	mysql_query("INSERT INTO `wg_category` (`id`, `name`, `parent`, `distance`, `killBranch`) VALUES (NULL, '".$ROOT_NAME."', '0', '0', '0');");
	mysql_query("ALTER TABLE wg_category ADD travelled INT DEFAULT 0");
	$maxBranches = 6;
	for ($distance=0; $distance < $maxBranches; $distance++) { // breadth first search 
		$r = mysql_query("SELECT * FROM wg_category WHERE travelled='0' AND killBranch='0' AND distance<".$maxBranches." ORDER BY distance");
		while($re = mysql_fetch_array($r)) {
			echo extractCategories($re['name'], $re['distance'], $re['id'], $killList);
			echo 'Travelled: <b>'.$re['name'].'</b><br />';
		}
	}
	mysql_query("ALTER TABLE wg_category DROP COLUMN travelled"); // clean up the table
}
function extractCategories($parentName, $parentDistance, $parentId, $killList) {
	$dom = new DOMDocument;
	$html = file_get_contents('http://en.wikipedia.org/wiki/Category:'.$parentName);
	@$dom->loadHTML($html);
	$dom = $dom->getElementById('mw-subcategories');
	if(!is_null($dom)) {
		foreach ($dom->getElementsByTagName('a') as $link) {
			$thisClass = $link->getAttribute('class');
			if(!empty($thisClass) AND strpos($thisClass, 'CategoryTreeLabel') !== false) {
				$h = explode(":", $link->getAttribute('href')); $category = $h[1];
				$r = mysql_query("SELECT * FROM wg_category WHERE name='$category'");
				if($re = mysql_fetch_array($r)) {
					$to = $re['id']; // By design, do not update distance or parent of that node, as this is not truly a tree
				}
				else {
					mysql_query("INSERT INTO `wg_category` (`id`, `name`, `parent`, `distance`, `killBranch`, `travelled`) VALUES (NULL, '$category', '".$parentId."', '".($parentDistance+1)."', '".(in_array($category, $killList)?1:0)."', '0');");
					$r = mysql_query("SELECT * FROM wg_category ORDER BY id DESC LIMIT 1"); $re = mysql_fetch_array($r); $to = $re['id'];
				}
				mysql_query("INSERT INTO `wg_catlink` (`id`, `catfrom`, `catto`) VALUES ('', '$parentId', '$to');");
			}
		}
	}
	mysql_query("UPDATE wg_category SET travelled='1' WHERE id='$parentId'");	
}
?>