<?php
// 11252 categories ... over 6 layers => 1.753
include_once('init.php');
include_once('killList.php');
$killList = getKillList();

buildCategories('Fields_of_mathematics', $killList);
function buildCategories($ROOT_NAME, $killList) {
	set_time_limit(3600);
	mysql_query("TRUNCATE wg_category");
	mysql_query("TRUNCATE wg_catlink");
	mysql_query("INSERT INTO `wg_category` (`id`, `name`, `fields`, `parent`, `distance`, `killBranch`) VALUES (NULL, '".$ROOT_NAME."', '', '0', '0', '0');");
	mysql_query("ALTER TABLE wg_category ADD travelled INT DEFAULT 0");
	$maxBranches = 6;
	for ($distance=0; $distance < $maxBranches; $distance++) { // breadth first search 
		$r = mysql_query("SELECT * FROM wg_category WHERE travelled='0' AND killBranch='0' AND distance<".$maxBranches." ORDER BY distance");
		while($re = mysql_fetch_array($r)) {
			extractCategories($re['name'], $re['distance'], $re['id'], $killList, $re['fields']);
			echo 'Travelled: <b>'.$re['name'].'</b><br />';
		}
	}
	mysql_query("ALTER TABLE wg_category DROP COLUMN travelled"); // clean up the table
}
function extractCategories($parentName, $parentDistance, $parentId, $killList, $myField) {
	$fieldCount = 1;
	$dom = new DOMDocument;
	$html = file_get_contents('http://en.wikipedia.org/wiki/Category:'.$parentName);
	@$dom->loadHTML($html);
	$dom = $dom->getElementById('mw-subcategories');
	if(!is_null($dom)) {
		foreach ($dom->getElementsByTagName('a') as $link) {
			if($parentDistance == 0) {$myField = $fieldCount; $fieldCount ++;}

			$thisClass = $link->getAttribute('class');
			if(!empty($thisClass) AND strpos($thisClass, 'CategoryTreeLabel') !== false) {
				$h = explode(":", $link->getAttribute('href')); $category = urldecode(utf8_encode($h[1]));
				$r = mysql_query("SELECT * FROM wg_category WHERE name='".mysql_real_escape_string($category)."'");
				if($re = mysql_fetch_array($r)) {
					$to = $re['id']; // By design, do not update distance or parent of that node, as this is not truly a tree
					$nField = array(); if($re['fields'] != '') {$nField = explode("|", $re['fields']);}
					$newFields = explode("|", $myField);
					foreach ($newFields as $f) {
						if(!in_array($f, $nField)) {array_push($nField, $f);}
					}
					mysql_query("UPDATE wg_category SET fields='".implode("|", $nField)."' WHERE id='".$re['id']."'");
				}
				else {
					$toKill = (in_array($category, $killList)?1:0);					
					mysql_query("INSERT INTO `wg_category` (`id`, `name`, `fields`, `parent`, `distance`, `killBranch`, `travelled`) VALUES (NULL, '$category', '$myField', '".$parentId."', '".($parentDistance+1)."', '".$toKill."', '0');");
					$r = mysql_query("SELECT * FROM wg_category ORDER BY id DESC LIMIT 1"); $re = mysql_fetch_array($r); $to = $re['id'];
				}
				mysql_query("INSERT INTO `wg_catlink` (`id`, `catfrom`, `catto`) VALUES ('', '$parentId', '$to');");
			}
		}
	}
	mysql_query("UPDATE wg_category SET travelled='1' WHERE id='$parentId'");	
}
?>