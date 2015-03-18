<?php
/*
OBJECTIVE:
Given a root category (from Wikipedia), and a "KillList" (in txt/killList.txt)
This will generate a "tree" of categories arising from the root, save them in the database

CURRENT STATUS:
1766 categories starting at 'Fields_of_mathematics'
*/

$ROOT_NAME = 'Fields_of_mathematics';

include_once('../dbco.php');
include_once('func.php');
$killList = getKillList();

buildCategories($ROOT_NAME, $killList);
function buildCategories($root, $killList) {
	set_time_limit(3600);
	mysql_query("TRUNCATE wg_category");
	mysql_query("TRUNCATE wg_catlink");
	mysql_query("INSERT INTO `wg_category` (`id`, `name`, `fields`, `parent`, `distance`, `killBranch`) VALUES (NULL, '".$root."', '', '0', '0', '0');");
	mysql_query("ALTER TABLE wg_category ADD travelled INT DEFAULT 0");
	$maxBranches = 15;
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
	$catPush = array(); $catLinkPush = array();
	$dom = new DOMDocument;
	$html = file_get_contents('http://en.wikipedia.org/wiki/Category:'.$parentName);
	@$dom->loadHTML($html);
	$dom = $dom->getElementById('mw-subcategories');
	if(!is_null($dom)) {
		foreach ($dom->getElementsByTagName('a') as $link) {
			if($parentDistance == 0) {$myField = $fieldCount; $fieldCount ++;} // this is yoloin it

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
					array_push($catPush, "(NULL, '$category', '$myField', '".$parentId."', '".($parentDistance+1)."', '".$toKill."', '0')");
					$r = mysql_query("SELECT * FROM wg_category ORDER BY id DESC LIMIT 1"); $re = mysql_fetch_array($r); $to = $re['id'];
				}
				array_push($catLinkPush, "('', '$parentId', '$to')");

			}
		}
		if(count($catPush) > 0) {
			mysql_query("INSERT INTO `wg_category` (`id`, `name`, `fields`, `parent`, `distance`, `killBranch`, `travelled`) VALUES ".implode(", ", $catPush).";");
			mysql_query("INSERT INTO `wg_catlink` (`id`, `catfrom`, `catto`) VALUES ".implode(", ", $catLinkPush).";");
		}
	}
	mysql_query("UPDATE wg_category SET travelled='1' WHERE id='$parentId'");
}
?>