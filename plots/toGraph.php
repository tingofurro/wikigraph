<?php
include('../init.php');
$r = mysql_query("SELECT * FROM wg_page ORDER BY id");
$mathScore = array(); $softwareScore = array(); $listScore = array();
while($re = mysql_fetch_array($r)) {
	array_push($mathScore, $re['mathematician']);
	array_push($softwareScore, $re['software']);
	array_push($listScore, $re['list']);
}
echo 'mathematician = ['.implode(", ", $mathScore).'];<br /><br />';
echo 'software = ['.implode(", ", $softwareScore).'];<br /><br />';
echo 'list = ['.implode(", ", $listScore).'];<br /><br />';

?>