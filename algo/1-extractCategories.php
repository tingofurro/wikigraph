<?php
/*
OBJECTIVE:
Given a root category (from Wikipedia), and a "KillList" (in txt/killList.txt)
This will generate a "tree" of categories arising from the root, save them in the database

CURRENT STATUS:
1800 categories starting at 'Fields_of_mathematics'
*/

$ROOT_NAME = 'Fields_of_mathematics';
include_once('../dbco.php');
include_once('func.php');
include_once('extractor.php');
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
	$r = mysql_query("SELECT * FROM wg_category ORDER BY id DESC LIMIT 1"); $re = mysql_fetch_array($r);
	$lastId = $re['id'];
	$subCategories = extractSubcat($parentName);
	$toRemove = array();
	for ($i=0; $i < count($subCategories); $i++) { 
		$f = mysql_query("SELECT * FROM wg_category WHERE `name`=\"".$subCategories[$i]."\"");
		if($fi = mysql_fetch_array($f)) {
		// By design, do not update distance or parent of that node, as this is not truly a tree
			array_push($catLinkPush, "('', '$parentId', '".$fi['id']."')");
			$nField = array(); if($fi['fields'] != '') {$nField = explode("|", $fi['fields']);}
			$newFields = explode("|", $myField);
			foreach ($newFields as $f) if(!in_array($f, $nField)) array_push($nField, $f);
			mysql_query("UPDATE wg_category SET fields='".implode("|", $nField)."' WHERE id='".$fi['id']."'");
			array_push($toRemove, $fi['name']);
		}
	}
	$subCategories = array_diff($subCategories, $toRemove);
	foreach ($subCategories as $subCat) {
		if($parentDistance == 0) {$myField = $fieldCount; $fieldCount ++;} // you are a direct child of the root, you become a field
		array_push($catPush, "(NULL, '$subCat', '$myField', '".$parentId."', '".($parentDistance+1)."', '".(in_array($subCat, $killList)?1:0)."', '0')");
		$lastId ++;
		array_push($catLinkPush, "('', '$parentId', '".$lastId."')");
	}

	if(count($catPush) > 0) mysql_query("INSERT INTO `wg_category` (`id`, `name`, `fields`, `parent`, `distance`, `killBranch`, `travelled`) VALUES ".implode(", ", $catPush).";");
	if(count($catLinkPush) > 0) mysql_query("INSERT INTO `wg_catlink` (`id`, `catfrom`, `catto`) VALUES ".implode(", ", $catLinkPush).";");

	mysql_query("UPDATE wg_category SET travelled='1' WHERE id='$parentId'");
	ob_flush();
}
?>