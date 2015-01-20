<?php
function getKillList() {
	include_once('func.php');
	$file = file_get_contents("killList.txt");
	$killList = explode("\r\n", $file);
	foreach ($killList as $i => $ele) { $killList[$i] = strToWiki($ele);}
	return $killList;
}
?>