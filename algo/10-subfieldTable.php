<?php
include_once('../dbco.php');
include_once('func.php');
/*
OBJECTIVE:
Create a list of all possible subfields,
They'll get tested next (through diffusion), and some will get deleted
*/

mysql_query("TRUNCATE wg_subfield");
$f = mysql_query("SELECT * FROM wg_field ORDER BY id");
while($fi = mysql_fetch_array($f)) {
	$p = mysql_query("SELECT * FROM wg_page WHERE field=".$fi['id']." AND SPR>0.35");
	while($pa = mysql_fetch_array($p)) {
		mysql_query("INSERT INTO `wg_subfield` (`id`, `field`, `name`, `sname`, `page`) VALUES (NULL, '".$fi['id']."', '".wikiToName($pa['name'])."', '".strtolower($pa['name'])."', ".$pa['id'].");");
	}
}
?>